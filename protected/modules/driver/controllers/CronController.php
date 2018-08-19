<?php
class CronController extends CController
{
	static $db;
	
	public function __construct()
	{		
		self::$db=new DbExt;
	}
	
	public function init()
	{			
		 // set website timezone
		 $website_timezone=Yii::app()->functions->getOptionAdmin("website_timezone");	 		 
		 if (!empty($website_timezone)){		 	
		 	Yii::app()->timeZone=$website_timezone;
		 }		 				 
	}
	
	public function actionIndex()
	{		
		
	}
	
	public function actionProcessPush()
	{
		$db=new DbExt;
		$status='';
		
		$ring_tone_filename = 'beep';
		$api_key=Yii::app()->functions->getOptionAdmin('driver_push_api_key');		
		
		$driver_ios_push_mode=getOptionA('driver_ios_push_mode');		
		$driver_ios_pass_phrase=getOptionA('driver_ios_pass_phrase');		
		$driver_ios_push_dev_cer=getOptionA('driver_ios_push_dev_cer');
		$driver_ios_push_prod_cer=getOptionA('driver_ios_push_prod_cer');	
		
		$DriverIOSPush=new DriverIOSPush;
		$DriverIOSPush->pass_prase=$driver_ios_pass_phrase;
		$DriverIOSPush->dev_certificate=$driver_ios_push_dev_cer;
		$DriverIOSPush->prod_certificate=$driver_ios_push_prod_cer;
		
		$production=$driver_ios_push_mode=="production"?true:false;		
		
		$stmt="
		SELECT * FROM
		{{driver_pushlog}}
		WHERE
		status='pending'
		ORDER BY date_created ASC
		LIMIT 0,10
		";
		if ( $res=$db->rst($stmt)){
			foreach ($res as $val) {
				if (isset($_GET['debug'])){
				   dump($val);
				}
				$push_id=$val['push_id'];
				if (!empty($val['device_id'])){
					if(!empty($api_key)){
						$message=array(		 
						 'title'=>$val['push_title'],
						 'message'=>$val['push_message'],
						 'soundname'=>$ring_tone_filename,
						 'count'=>1,
						 'additionalData'=>array(
						   'push_type'=>$val['push_type'],
						   'order_id'=>$val['order_id'],
						   'actions'=>$val['actions'],
						 )
					   );		
					   
					   if (isset($_GET['debug'])){
					      dump($message);
					   }
					   
					   if ( strtolower($val['device_platform']) =="android"){
						   $resp=AndroidPush::sendPush($api_key,$val['device_id'],$message);
						   if(is_array($resp) && count($resp)>=1){
						   	  
						   	   if (isset($_GET['debug'])){ dump($resp); }
						   	   
				   	       	   if( $resp['success']>0){			   	       	   	   
				   	       	   	   $status="process";
				   	       	   } else {		   	       	   	   
				   	       	   	   $status=$resp['results'][0]['error'];
				   	       	   }
						   }  else $status="uknown push response";						
						      
					   } elseif ( strtolower($val['device_platform']) =="ios"  ) {
					   	
					   	   $additional_data=array(
					   	     'push_type'=>$val['push_type'],
						     'order_id'=>$val['order_id'],
						     'actions'=>$val['actions'],
					   	   );					   	   
					   	   if ( $DriverIOSPush->push($val['push_message'],$val['device_id'],$production,$additional_data) ){
					   	   	    $status="process";
					   	   } else $status=$DriverIOSPush->get_msg();
					   	   
					   } else {
					   	   $status="Uknown device";
					   }
					   				
					} else $status= "API key is empty";
				} else $status= "Device id is empty";
				
				$params=array(
				  'status'=>$status,
				  'date_process'=>date('c'),
				  'json_response'=>isset($resp)?json_encode($resp):'',
				  'ip_address'=>$_SERVER['REMOTE_ADDR']
				);
				
				if (isset($_GET['debug'])){
				   dump($params);
				}
				
				$db->updateData("{{driver_pushlog}}",$params,'push_id',$push_id);
				
			}
		} else {
			if (isset($_GET['debug'])){
				echo 'no record to process';
			}
		}
	}
	
	public function actionAutoAssign()
	{
		
		$db=new DbExt;		
		$distance_exp=3959;  $radius=3000;			
		
		$date_now=date('Y-m-d');
			
		
		$stmt="SELECT * FROM
		{{driver_task_view}}
		WHERE 1
		AND status IN ('unassigned')  
		AND auto_assign_type=''
		AND delivery_date like '$date_now%'
		AND delivery_address!=''
		ORDER BY task_id ASC
		LIMIT 0,10
		";
		
		if (isset($_GET['debug'])){dump($stmt);}		
		
		if ( $res=$db->rst($stmt)){			
			foreach ($res as $val) {
				
				if (isset($_GET['debug'])){
				   dump($val);
				}
				
				$user_type=$val['user_type'];
				$user_id=$val['user_id'];				
				
				$driver_enabled_auto_assign = Driver::getOption('driver_enabled_auto_assign',$user_type,$user_id);
				$driver_include_offline_driver = Driver::getOption('driver_include_offline_driver',$user_type,$user_id);
				$driver_auto_assign_type = Driver::getOption('driver_auto_assign_type',$user_type,$user_id);
				$driver_assign_request_expire = Driver::getOption('driver_assign_request_expire',$user_type,$user_id);
				$assign_type = $driver_auto_assign_type;
				
				$notify_email = Driver::getOption('driver_autoassign_notify_email',$user_type,$user_id);
				
				if (isset($_GET['debug'])){
					dump("driver_enabled_auto_assign=>".$driver_enabled_auto_assign);
					dump("driver_include_offline_driver->".$driver_include_offline_driver);
				}
				
				if(empty($driver_enabled_auto_assign)){
					if (isset($_GET['debug'])){echo "auto assign is disabled";}		
					$db->updateData("{{driver_task}}",array(
					  'auto_assign_type'=>"none"
					),'task_id',$val['task_id']);
					continue;
				}
				
				$lat=''; $lng='';

				if($val['merchant_id']>0){
					if(!empty($val['dropoff_lat'])){
						$lat=$val['dropoff_lat'];
				        $lng=$val['dropoff_lng'];				
					} else {
						$lat=getOption($val['merchant_id'],'merchant_latitude');
				        $lng=getOption($val['merchant_id'],'merchant_longtitude');
					}
				} else {
					$lat=$val['task_lat'];
				    $lng=$val['task_lng'];				
				}										
				
				if(empty($lat)){					
					$lat=$val['task_lat'];
				    $lng=$val['task_lng'];
				}
				
				$task_id=$val['task_id'];	
				
				if (isset($_GET['debug'])){			
				   dump($lat); dump($lng);
				}
				
				$and='';
				$todays_date=date('Y-m-d');			
		        //$time_now = time() - 600;
		        $time_now=strtotime("-10 minutes");
		        
		        $assignment_status=t("waiting for driver acknowledgement");
				
				if ( $driver_include_offline_driver==""){
					$and.=" AND a.on_duty ='1' ";
                    $and.=" AND a.last_online >='$time_now' ";
                    $and.=" AND a.last_login like '".$todays_date."%'";
				}
				
				$limit="LIMIT 0,100";
				if ( $driver_auto_assign_type=="one_by_one"){
					
					$and.=" AND a.user_type=".Driver::q($user_type)."";
					
				    if ($user_type=="merchant"){
					  $and.=" AND a.user_id=".Driver::q($user_id)."";
					  
					  $radius=getOption($val['user_id'],'driver_within_radius');
					  $driver_within_radius_unit=getOption($val['user_id'],'driver_within_radius_unit');					  
					  if ($driver_within_radius_unit=="km"){
						  $distance_exp=6371;
					  }	
					  
					  if($radius<=0){
					  	$radius=3000;
					  }
					  
					  /*check if can use driver admin*/
					  $driver_allowed_team_to_merchant=getOptionA('driver_allowed_team_to_merchant');
					  
					  if (isset($_GET['debug'])){
					     dump("driver_allowed_team_to_merchant=>".$driver_allowed_team_to_merchant);					  
					  }
					  if($driver_allowed_team_to_merchant>0){
					  	  if($driver_allowed_team_to_merchant==1){
					  	  	 $and.=" OR user_type='admin' ";
					  	  }  elseif ($driver_allowed_team_to_merchant==2){
					  	  	 $driver_allowed_merchant_list=getOptionA('driver_allowed_merchant_list');
					  	  	 if(!empty($driver_allowed_merchant_list)){
					  	  	 	$driver_allowed_merchant_list=json_decode($driver_allowed_merchant_list,true);
					  	  	 	if($val['user_id']>0){
					  	  	 		if(in_array($val['user_id'],(array)$driver_allowed_merchant_list)){
					  	  	 			$and.=" OR user_type='admin' ";
					  	  	 		}
					  	  	 	}
					  	  	 }
					  	  }
					  }
					  
				    } else {
				    	$radius=getOptionA('driver_within_radius');
				    	if($radius<=0){
					  	  $radius=3000;
					    }
					    
					    $driver_within_radius_unit=getOptionA('driver_within_radius_unit');
					    if ($driver_within_radius_unit=="km"){
						    $distance_exp=6371;
					    }	
				    }
					
					$and.=" AND a.driver_id NOT IN (
					  select driver_id
					  from
					  {{driver_assignment}}
					  where
					  driver_id=a.driver_id
					  and
					  task_id=".Driver::q($task_id)."
					) ";
										
					$stmt2="
					SELECT a.driver_id, a.first_name,a.last_name,a.location_lat,a.location_lng,
					a.user_type,a.user_id,
					a.on_duty, a.last_online, a.last_login
					, 
					( $distance_exp * acos( cos( radians($lat) ) * cos( radians( location_lat ) ) 
			        * cos( radians( location_lng ) - radians($lng) ) 
			        + sin( radians($lat) ) * sin( radians( location_lat ) ) ) ) 
			        AS distance
			        FROM {{driver}} a
			        HAVING distance < $radius
					$and
					ORDER BY distance ASC
					$limit
					";
				} else {
					
					if (isset($_GET['debug'])){
					   dump('send to all qry');
					}
					$and.=" AND user_type=".Driver::q($user_type)."";
					if ($user_type=="merchant"){
						$and.=" AND user_id=".Driver::q($user_id)."";
						
						 /*check if can use driver admin*/
						  $driver_allowed_team_to_merchant=getOptionA('driver_allowed_team_to_merchant');
						  
						  if (isset($_GET['debug'])){
						    dump("driver_allowed_team_to_merchant=>".$driver_allowed_team_to_merchant);					  
						  }
						  
						  if($driver_allowed_team_to_merchant>0){
						  	  if($driver_allowed_team_to_merchant==1){
						  	  	 $and.=" OR user_type='admin' ";
						  	  }  elseif ($driver_allowed_team_to_merchant==2){
						  	  	 $driver_allowed_merchant_list=getOptionA('driver_allowed_merchant_list');
						  	  	 if(!empty($driver_allowed_merchant_list)){
						  	  	 	$driver_allowed_merchant_list=json_decode($driver_allowed_merchant_list,true);
						  	  	 	if($val['user_id']>0){
						  	  	 		if(in_array($val['user_id'],(array)$driver_allowed_merchant_list)){
						  	  	 			$and.=" OR user_type='admin' ";
						  	  	 		}
						  	  	 	}
						  	  	 }
						  	  }
						  }
						
					}
					
					$and.=" AND a.driver_id NOT IN (
					  select driver_id
					  from
					  {{driver_assignment}}
					  where
					  driver_id=a.driver_id
					  and
					  task_id=".Driver::q($task_id)."
					) ";
					
					$stmt2="SELECT a.* FROM {{driver}} a		
					WHERE 1
					$and			
					";					
				}
				
				if (isset($_GET['debug'])){
				    dump($stmt2);		
				}
				//die();
				if ( $res2=$db->rst($stmt2)){					
					//dump($res2); die();
					foreach ($res2 as $val2) {
						$params=array(
						  'auto_assign_type'=>$assign_type,
						  'task_id'=>$val['task_id'],
						  'driver_id'=>$val2['driver_id'],
						  'first_name'=>$val2['first_name'],
						  'last_name'=>$val2['last_name'],
						  'date_created'=>date('c'),
						  'ip_address'=>$_SERVER['REMOTE_ADDR']
						);
						if (isset($_GET['debug'])){
							dump($params);
							echo "<h3>driver_assignment</h3>";
						}
						if ( !Driver::validateAssigment($val['task_id'],$val2['driver_id']) ){
						   $db->insertData("{{driver_assignment}}",$params);
						}
					}
				} else {
					//send email
					if(!empty($notify_email)){
						
						if (isset($_GET['debug'])){dump($notify_email);}
						$email_enabled=getOptionA('FAILED_AUTO_ASSIGN_EMAIL');
						if($email_enabled){
						   $tpl=getOptionA('FAILED_AUTO_ASSIGN_EMAIL_TPL');
						   $tpl=Driver::smarty('TaskID',$task_id,$tpl);
						   $tpl=Driver::smarty('CompanyName',getOptionA('website_title'),$tpl);
						   if (isset($_GET['debug'])){dump($tpl);}
						   sendEmail($notify_email,"","Unable to auto assign Task $task_id",$tpl);
						}
					}   	
					$assignment_status = "unable to auto assign";
				}
			} /*end foreach*/
			
			$less="-1";
			if($driver_assign_request_expire>0){
				$less="-$driver_assign_request_expire";
			}
			
			$params_task=array(
			 'auto_assign_type'=>$assign_type,
			 'assign_started'=>date('c',strtotime("$less min")),
			 'assignment_status'=> $assignment_status
			);			
			if (isset($_GET['debug'])){dump($params_task);}
			$db->updateData("{{driver_task}}",$params_task,'task_id',$task_id);
			
		} else {
			if (isset($_GET['debug'])){
				echo 'no record to process';
			}
		}		
		
		/*sleep(1);
		$url=Yii::app()->getBaseUrl(true)."/driver/cron/processautoassign";
		echo @file_get_contents($url);*/
	}

	public function actionProcessAutoAssign()
	{				
		$and='';		
		
		$date_now=date("Y-m-d");
				
		$and.="AND task_id IN (
		  select task_id from {{driver_assignment}}
		  where
		  task_id=a.task_id
		  and
		  status='pending'		  
		)";
		
		$db=new DbExt;
		$stmt="SELECT a.* FROM
		{{driver_task}} a
		WHERE 1
		AND status IN ('unassigned') 
		AND delivery_date like '".$date_now."%'
		$and				
		ORDER BY task_id ASC
		LIMIT 0,5
		";
		
		if (isset($_GET['debug'])){
		   dump($stmt);
		}
		if ( $res=$db->rst($stmt)){
			foreach ($res as $val) {		
				
				if (isset($_GET['debug'])){
				    dump($val);
				}

				$user_type=$val['user_type'];
				$user_id=$val['user_id'];
				
				$enabled_auto_assign=Driver::getOption('driver_enabled_auto_assign',$user_type,$user_id);
				$exclude_offline_driver=Driver::getOption('driver_exclude_offline_driver',$user_type,$user_id);		
				$request_expire=Driver::getOption('driver_assign_request_expire',$user_type,$user_id);		
				if (!is_numeric($request_expire)){
					$request_expire=1;
				}
						
				if (isset($_GET['debug'])){
				    dump($enabled_auto_assign);
				}
				
				if(empty($enabled_auto_assign)){			
					echo "auto assign is disabled";		
					return ;
				}
							
				$task_id=$val['task_id'];
				$assign_type=$val['auto_assign_type'];
				$assign_started=date("Y-m-d g:i:s a",strtotime($val['assign_started']));
								
				$date_now=date('Y-m-d g:i:s a');
				
				if (isset($_GET['debug'])){
					dump($task_id);				
					dump($assign_type);
					dump($assign_started);
					dump($date_now);
				}
				
							
				if ( $assign_type=="one_by_one"){
					$time_diff=Yii::app()->functions->dateDifference($assign_started,$date_now);
					
					if (isset($_GET['debug'])){
				       dump($time_diff);
					}
					if (is_array($time_diff) && count($time_diff)>=1){
					   if ( $time_diff['hours']>0 || $time_diff['minutes']>=$request_expire){					   	    
					   	  if ( $driver=Driver::getUnAssignedDriver($task_id)){					   	      
					   	      $params['assignment_status']="waiting for"." ".$driver['first_name'].
					   	      " ".$driver['last_name']." "."to acknowledge"; 
					   	      					   	      					   	     					   	      
					   	      $assigment_id=$driver['assignment_id'];
					   	      $params_driver=array('status'=>'process','date_process'=>date('c'));
					   	      
					   	      if (isset($_GET['debug'])){
					   	         dump($params_driver);
					   	      }
					   	      $db->updateData('{{driver_assignment}}',$params_driver,'assignment_id',$assigment_id);
					   	      					   	      
					   	      $task_info=Driver::getTaskByDriverNTask($task_id, $driver['driver_id']);
					   	      Driver::sendDriverNotification('ASSIGN_TASK',$task_info);	
					   	      
					   	  }
					   	  $params['assign_started']=date('c');
					   	  if (isset($_GET['debug'])){dump($params);}
					   	  $db->updateData("{{driver_task}}",$params,'task_id',$task_id);
					   } else echo "Not request $request_expire a";
					} else echo "Not request $request_expire b";
				} else {
					if (isset($_GET['debug'])){echo 'all driver';}
					if ( $res= Driver::getUnAssignedDriver2($task_id,'0,10')){
						foreach ($res as $val) {
							
							if (isset($_GET['debug'])){
							    dump($val);
							}
							$assigment_id=$val['assignment_id'];
					   	    $params_driver=array('status'=>'process','date_process'=>date('c'));
					   	    
					   	    if (isset($_GET['debug'])){
					   	        dump($params_driver);
					   	    }
					   	    $db->updateData('{{driver_assignment}}',$params_driver,'assignment_id',$assigment_id);
					   	      
					   	    $task_info=Driver::getTaskByDriverNTask($val['task_id'], $val['driver_id'] );
					   	    Driver::sendDriverNotification('ASSIGN_TASK',$task_info);	
						}
					}
				}
			}			
		} else {
			if (isset($_GET['debug'])){echo 'No results';}
		}
	}
	
	public function actionCheckAutoAssign()
	{
		
		$db=new DbExt;
				
		$stmt="SELECT a.* FROM
		{{driver_task}} a
		WHERE 1
		AND status IN ('unassigned') 	
		AND auto_assign_type IN ('one_by_one','send_to_all')	
		AND assignment_status NOT IN ('','unable to auto assign')
		AND task_id NOT IN (
		  select task_id from {{driver_assignment}}
		  where
		  task_id=a.task_id
		  and
		  status='pending'  
		)
		ORDER BY task_id ASC
		LIMIT 0,5
		";
		if (isset($_GET['debug'])){dump($stmt);}
		if ( $res=$db->rst($stmt)){
			foreach ($res as $val) {	
				
				
			    if (isset($_GET['debug'])){dump($val);}
			    
			    $user_type=$val['user_type'];
				$user_id=$val['user_id'];
				
				$notify_email = Driver::getOption('driver_autoassign_notify_email',$user_type,$user_id);
							    			    
			    $task_id=$val['task_id'];
			    		
				$task_id=$val['task_id'];
				$assign_type=$val['auto_assign_type'];
				$assign_started=date("Y-m-d g:i:s a",strtotime($val['assign_started']));
				
				/*$request_expire=Driver::getOption('driver_assign_request_expire',$user_type,$user_id);		
				dump($request_expire);
				if (!is_numeric($request_expire)){
					$request_expire=1;
				}*/
								
				$request_expire=Driver::getOption('driver_request_expire',$user_type,$user_id);				
				if(!is_numeric($request_expire)){
			        $request_expire=1;
				}			    				
			    
				$date_now=date('Y-m-d g:i:s a');
				if (isset($_GET['debug'])){
					dump($task_id);
					dump("expire in :".$request_expire);
					dump($assign_type);
					dump($assign_started);
					dump($date_now);
				}
				//die();
				
				$time_diff=Yii::app()->functions->dateDifference($assign_started,$date_now);
				if (is_array($time_diff) && count($time_diff)>=1){
					
					if (isset($_GET['debug'])){dump($time_diff);}
					
				    if ( $time_diff['hours']>0 || $time_diff['minutes']>=$request_expire){				    	
				    	$params=array('assignment_status'=>"unable to auto assign");
				    	dump($params);
				    	$db->updateData("{{driver_task}}",$params,'task_id',$task_id);
				    	
				    	
				    	$stmt_assign="
				    	UPDATE {{driver_assignment}}
				    	SET task_status='unable to auto assign'
				    	WHERE
				    	task_id=".Driver::q($task_id)."
				    	";
				    	$db->qry($stmt_assign);
				    					    	
				    	Driver::sendDriverNotification('CANCEL_TASK',$val);
				    	
				    	//send email
				    	if(!empty($notify_email)){
				    		if (isset($_GET['debug'])){dump($notify_email);}
				    		$email_enabled=getOptionA('FAILED_AUTO_ASSIGN_EMAIL');
				    		if($email_enabled){
							   $tpl=getOptionA('FAILED_AUTO_ASSIGN_EMAIL_TPL');
							   $tpl=Driver::smarty('TaskID',$task_id,$tpl);
							   $tpl=Driver::smarty('CompanyName',getOptionA('website_title'),$tpl);
							   if (isset($_GET['debug'])){dump($tpl);}
				    		   sendEmail($notify_email,"","Unable to auto assign Task $task_id",$tpl);
				    		}
				    	}   	
				    	
				    	/*retry auto assign*/
				    	$driver_auto_assign_retry=Driver::getOption('driver_auto_assign_retry',$user_type,$user_id);
				    	if ( $driver_auto_assign_retry==1){
				    		Driver::retryAutoAssign($task_id);
				    	}
				    	
				    }
				}
				
			} /*end foreach*/
		}  else {
			if (isset($_GET['debug'])){
				echo "No results";
			}
		}		
	}
	
	public function actionProcessBulkOld()
	{
		$stmt="SELECT * FROM
		{{driver_bulk_push}}
		WHERE
		status='pending'
		ORDER BY bulk_id ASC
		LIMIT 0,1
		";
		if ( $res=self::$db->rst($stmt)){
			foreach ($res as $val) {
				$bulk_id=$val['bulk_id'];
				dump($val);
				$stmt2="SELECT a.* FROM
				{{driver}} a
				WHERE
				device_id !=''
				AND driver_id NOT IN (
				  select driver_id
				  from {{driver_pushlog}}
				  where
				  driver_id=a.driver_id
				  and
				  bulk_id=".Driver::q($bulk_id)."
				)
				ORDER BY driver_id ASC
				LIMIT 0,1000
				";
				dump($stmt2);
				if ( $res2=self::$db->rst($stmt2)){
					foreach ($res2 as $val2) {						
						$params=array(
						  'push_title'=>$val['push_title'],
						  'push_message'=>$val['push_message'],
						  'device_platform'=>$val2['device_platform'],
						  'driver_id'=>$val2['driver_id'],
						  'device_id'=>$val2['device_id'],
						  'push_type'=>"bulk",
						  'actions'=>"bulk",
						  'bulk_id'=>$bulk_id,
						  'date_created'=>date('c'),
						  'ip_address'=>$_SERVER['REMOTE_ADDR']
						);
						dump($params);
						self::$db->insertData("{{driver_pushlog}}",$params);
					}
				} else {
					echo "No records to process";
					self::$db->updateData("{{driver_bulk_push}}",
					   array('status'=>"process",'date_process'=>date('c'))
					   ,'bulk_id',$bulk_id);
				}
			}
		} else echo "No records to process";
	}
	
	public function actionProcessBulk()
	{
		$datenow=date('Y-m-d G:i:s');
		$ipaddress=$_SERVER['REMOTE_ADDR'];
		$stmt="SELECT * FROM
		{{driver_bulk_push}}
		WHERE
		status='pending'
		ORDER BY bulk_id ASC
		LIMIT 0,5		
		";
		if ( $res=self::$db->rst($stmt)){
			foreach ($res as $val) {				
				$team_id=$val['team_id'];
				$push_title=$val['push_title'];
				$push_message=$val['push_message'];
				$bulk_id=$val['bulk_id'];
				$user_type=$val['user_type'];
				$user_id=$val['user_id'];
				
				$stmt_insert="
				INSERT INTO {{driver_pushlog}}(
				   device_platform,
				   device_id,
				   push_title,
				   push_message,
				   push_type,
				   actions,
				   driver_id,
				   date_created,
				   ip_address,
				   bulk_id,
				   user_type,
				   user_id
				)
				
				SELECT
				device_platform,
				device_id,
				'$push_title',
				'$push_message',
				'private',
				'private',
				driver_id,
				'$datenow',
				'$ipaddress',
				'$bulk_id',
				'$user_type',
				'$user_id'
				FROM {{driver}}
				WHERE
				team_id=".Driver::q($val['team_id'])."
				";				
				//dump($stmt_insert);
				self::$db->qry($stmt_insert);
				self::$db->updateData("{{driver_bulk_push}}",array(
				  'status'=>"process"
				),'bulk_id',$bulk_id);
			}
		}
	}
	
	public function actionClearAgentTracking()
    {
    	$date=date("Y-m-d 23:59:00",strtotime("-5 days"));
    	$db=new DbExt;
    	$stmt="
    	DELETE FROM
    	{{driver_track_location}}
    	WHERE 
    	date_created <=".Driver::q($date)."
    	";
    	if (isset($_GET['debug'])){
    	   dump($stmt);
    	}
    	$db->qry($stmt);
    }
    
	public function actionRunAll()
	{
	   Driver::WgetRequest(websiteUrl()."/driver/cron/processpush");
	   Driver::WgetRequest(websiteUrl()."/driver/cron/autoassign");
	   Driver::WgetRequest(websiteUrl()."/driver/cron/processautoassign");
	   Driver::WgetRequest(websiteUrl()."/driver/cron/checkautoassign");
	   Driver::WgetRequest(websiteUrl()."/driver/cron/processbulk");
	}
		
} /*end class*/