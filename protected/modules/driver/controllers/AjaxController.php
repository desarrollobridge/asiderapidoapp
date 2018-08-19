<?php
if (!isset($_SESSION)) { session_start(); }

class AjaxController extends CController
{
	public $code=2;
	public $msg;
	public $details;
	public $data;
	static $db;
	
	public function __construct()
	{
		$this->data=$_POST;	
		self::$db=new DbExt;
	}
	
	public function init()
	{			
		 // set website timezone
		 $website_timezone=Yii::app()->functions->getOptionAdmin("website_timezone");	 		 		 
		 if (!empty($website_timezone)){		 			 	
		 	Yii::app()->timeZone=$website_timezone;
		 }		 				 
		 		 
		 Driver::handleLanguage();
		 //Yii::app()->language="jp";
	}
	
	private function jsonResponse()
	{
		$resp=array('code'=>$this->code,'msg'=>$this->msg,'details'=>$this->details);
		echo CJSON::encode($resp);
		Yii::app()->end();
	}
	
	private function otableNodata()
	{
		if (isset($_GET['sEcho'])){
			$feed_data['sEcho']=$_GET['sEcho'];
		} else $feed_data['sEcho']=1;	   
		     
        $feed_data['iTotalRecords']=0;
        $feed_data['iTotalDisplayRecords']=0;
        $feed_data['aaData']=array();		
        echo json_encode($feed_data);
    	die();
	}

	private function otableOutput($feed_data='')
	{
	  echo json_encode($feed_data);
	  die();
    }    
    
	public function actionLogin()
	{		
		$req=array(
		  'username'=>Driver::t("username is required"),
		  'password'=>Driver::t("password is required"),
		);
		$Validator=new Validator;
		$Validator->required($req,$this->data);
		if($Validator->validate()){
			switch ($this->data['user_type']) {
				case 1:
					//admin
					if($res=Driver::adminLogin($this->data['username'],$this->data['password'])){						
						$this->code=1;
						$_SESSION['driver']['user_type']="admin";
						$_SESSION['driver']['info']=$res;	
						$this->msg=Driver::t("Login ok");
					} else $this->msg=Driver::t("Login failed");
					break;
			
				default:
					//merchant
					if($res=Driver::merchantLogin($this->data['username'],$this->data['password'])){						
						$mtid=$res['merchant_id'];
						
						$driver_merchant_block=getOptionA('driver_merchant_block');
						if(!empty($driver_merchant_block)){
							$driver_merchant_block=json_decode($driver_merchant_block,true);
							if (in_array($mtid,$driver_merchant_block)){
								$this->msg=Driver::t("your account is not allowed to access driver panel");
								$this->jsonResponse();
								Yii::app()->end();
							}
						}
						
						$this->code=1;
						$_SESSION['driver']['user_type']="merchant";
						$_SESSION['driver']['info']=$res;	
						$this->msg=Driver::t("Login ok");
					} else $this->msg=Driver::t("Login failed");
					break;
			}
		} else $this->msg=$Validator->getErrorAsHTML();
		$this->jsonResponse();
	}
	
	public function actionCreateTeam()
	{
		$params=array(
		  'team_name'=>$this->data['team_name'],
		  'location_accuracy'=>$this->data['location_accuracy'],
		  //'team_member'=>isset($this->data['team_member'])?json_encode($this->data['team_member']):'',
		  'status'=>$this->data['status'],
		  'date_created'=>date('c'),
		  'ip_address'=>$_SERVER['REMOTE_ADDR']
		);		
		if(!isset($this->data['id'])){
			$this->data['id']='';
		}
		
		$team_member=isset($this->data['team_member'])?json_encode($this->data['team_member']):'';
		
		$params['user_type']=Driver::getUserType();
		$params['user_id']=Driver::getUserId();
		
		if(!Driver::islogin()){
			$this->msg=Driver::t("Sorry but your session has expired");
			$this->jsonResponse();
			Yii::app()->end();
		}
						
		$db=new DbExt;
		if(!empty($this->data['id'])){
			unset($params['date_created']);
			$params['date_modified']=date('c');			
			if ( $db->updateData("{{driver_team}}",$params,'team_id',$this->data['id'])){
				$this->code=1;
		   	    $this->msg=Driver::t("Successfully updated");
		   	    $this->details='create-team';
		   	    
		   	    // update driver team
		   	    if(!empty($team_member)){
			       Driver::updateDriverTeam($team_member,$this->data['id']);
		        } else {
		           $sql_update="UPDATE {{driver}} SET team_id='0' WHERE team_id=".Driver::q($this->data['id'])." ";
		           $db->qry($sql_update);
		        }
		   	    
			} else $this->msg=Driver::t("failed cannot update record");
		} else {
		   if($db->insertData("{{driver_team}}",$params)){
		   	  $team_id=Yii::app()->db->getLastInsertID();
		   	  $this->code=1;
		   	  $this->msg=Driver::t("Successful");
		   	  $this->details='create-team';
		   	  
		   	  // update driver team
		   	  if(!empty($team_member)){
			     Driver::updateDriverTeam($team_member,$team_id);
		      }
		   	  
		   } else $this->msg=Driver::t("failed cannot insert record");
		}
		$this->jsonResponse();
	}

	public function actionTeamList()
	{
		$aColumns = array(
		  'a.team_id','a.team_name','a.team_name','a.status','a.date_created'
		);
		$t=AjaxDataTables::AjaxData($aColumns);		
		if (isset($_GET['debug'])){
		    dump($t);
		}
		
		if (is_array($t) && count($t)>=1){
			$sWhere=$t['sWhere'];
			$sOrder=$t['sOrder'];
			$sLimit=$t['sLimit'];
		}	
		
		$and='';				
		if ( Driver::getUserType()=="admin"){			
		   $and =" AND user_type=".Driver::q(Driver::getUserType())."";
		} else {
		   $and =" AND user_type=".Driver::q(Driver::getUserType())."";
		   $and.=" AND user_id=".Driver::q(Driver::getUserId())."  ";		
		}
				
		$stmt="SELECT SQL_CALC_FOUND_ROWS a.*,
			(
			select count(*)
			from
			{{driver}}
			where			
			team_id=a.team_id
			) as total_driver
		FROM
		{{driver_team}} a
		WHERE 1
		$and		
		$sWhere
		$sOrder
		$sLimit
		";
		if (isset($_GET['debug'])){
		   dump($stmt);
		}
				
		$DbExt=new DbExt; 
		if ( $res=$DbExt->rst($stmt)){
			
			$iTotalRecords=0;						
			$stmtc="SELECT FOUND_ROWS() as total_records";
			if ( $resc=$DbExt->rst($stmtc)){									
				$iTotalRecords=$resc[0]['total_records'];
			}
			
			$feed_data['sEcho']=intval($_GET['sEcho']);
			$feed_data['iTotalRecords']=$iTotalRecords;
			$feed_data['iTotalDisplayRecords']=$iTotalRecords;										
			
			foreach ($res as $val) {
				$date_created=Yii::app()->functions->prettyDate($val['date_created'],true);
			    $date_created=Yii::app()->functions->translateDate($date_created);			
			    
			    $id=$val['team_id'];
			    $p="id=$id"."&tbl=driver_team&whereid=team_id";

			    $actions="<div class=\"table-action\">";
			    $actions.="<a data-modal=\".create-team\" data-id=\"$id\" 
			    data-action=\"getTeam\"
			    class=\"table-edit\" href=\"javascript:;\">".Driver::t("Edit")."</a>";    
			    
			    $actions.="&nbsp;|&nbsp;";
			    
			    $actions.="<a data-data=\"$p\" class=\"table-delete\" href=\"javascript:;\">".Driver::t("Delete")."</a>";
			    $actions.="</div>";
			    
			    $feed_data['aaData'][]=array(
			      $val['team_id'],
			      $val['team_name'].$actions,
			      $val['total_driver'],
			      '<span class="btn btn-default">'.Driver::t($val['status'])."</span>",
			      $date_created,
			    );			    
			}
			if (isset($_GET['debug'])){
			   dump($feed_data);
			}
			$this->otableOutput($feed_data);	
		}
		$this->otableNodata();
	}	
	
	public function actiongetTeam()
	{		
		if($res=Driver::getTeam($this->data['id'])){			
			$this->code=1; 
			$this->msg=Driver::t("Successful");			
			/*if(!empty($res['team_member'])){
				$res['team_member']=json_decode($res['team_member'],true);
			}*/
			//dump($res);
			if ($driver=Driver::getDriverByTeam($res['team_id'])){
				foreach ($driver as $val) {					
					$res['team_member'][]=$val['driver_id'];
				}
			} else $res['team_member']='';
			//dump($res);
			$this->details=$res;
		} else $this->msg=Driver::t("Record not found");
		$this->jsonResponse();
	}
	
	public function actionDeleteRecords()
	{		
		if(isset($this->data['tbl']) && isset($this->data['whereid']) ){
			$wherefield=$this->data['whereid'];
			$tbl=$this->data['tbl'];
			$stmt="
			DELETE FROM
			{{{$tbl}}}
			WHERE
			$wherefield=".Driver::q($this->data['id'])."
			";
			//dump($stmt);
			$DbExt=new DbExt; 
			$DbExt->qry($stmt);
			$this->code=1;
			$this->msg=Driver::t("Successful");
		} else $this->msg=Driver::t("Missing parameters");
		$this->jsonResponse();
	}
	
	public function actiondriverList()
	{
		$aColumns = array(
		  'driver_id','username','first_name','email','phone',
		  'team_id','status'
		);
		$t=AjaxDataTables::AjaxData($aColumns);		
		if (isset($_GET['debug'])){
		    dump($t);
		}
		
		if (is_array($t) && count($t)>=1){
			$sWhere=$t['sWhere'];
			$sOrder=$t['sOrder'];
			$sLimit=$t['sLimit'];
		}	
				
        $and='';		
        if ( Driver::getUserType()=="admin"){
           $and=" AND user_type=".Driver::q(Driver::getUserType())."  ";
        } else {
		   $and=" AND user_type=".Driver::q(Driver::getUserType())."";
		   $and.=" AND user_id=".Driver::q(Driver::getUserId())."  ";		
        }
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS a.*,
		(
		select team_name
		from
		{{driver_team}}
		where
		team_id=a.team_id
		limit 0,1
		) as team_name
		FROM
		{{driver}} a
		WHERE 1		
		$and
		$sWhere
		$sOrder
		$sLimit
		";
		if (isset($_GET['debug'])){
		   dump($stmt);
		}
				
		$DbExt=new DbExt; 
		if ( $res=$DbExt->rst($stmt)){
			
			$iTotalRecords=0;						
			$stmtc="SELECT FOUND_ROWS() as total_records";
			if ( $resc=$DbExt->rst($stmtc)){									
				$iTotalRecords=$resc[0]['total_records'];
			}
			
			$feed_data['sEcho']=intval($_GET['sEcho']);
			$feed_data['iTotalRecords']=$iTotalRecords;
			$feed_data['iTotalDisplayRecords']=$iTotalRecords;										
			
			foreach ($res as $val) {
				$date_created=Yii::app()->functions->prettyDate($val['date_created'],true);
			    $date_created=Yii::app()->functions->translateDate($date_created);			
			    
			    $id=$val['driver_id'];
			    $p="id=$id"."&tbl=driver&whereid=driver_id";

			    $actions="<div class=\"table-action\">";
			    $actions.="<a data-modal=\".new-agent\" data-id=\"$id\" 
			    data-action=\"getDriverInfo\"
			    class=\"table-edit\" href=\"javascript:;\">".Driver::t("Edit")."</a>";    
			    
			    $actions.="&nbsp;|&nbsp;";
			    
			    $actions.="<a data-data=\"$p\" class=\"table-delete\" href=\"javascript:;\">".Driver::t("Delete")."</a>";
			    $actions.="</div>";
			    
			    $actions_2="<a data-id=\"$id\" data-fname=\"".$val['first_name']."\" class=\"send-push btn btn-primary\" href=\"javascript:;\">".Driver::t("Send Push")."</a>";
			    
			    $class="tag driver_status ".$val['status'];
			    
			    $driver_id=$val['driver_id'];
			    			    
			    $actions_3='';
			    
			    if ( $val['status']=="active" || $val['status']=="denied"){			    	
			    } else {	
				    if($val['is_signup']==1){
				    	$actions_3="<a href=\"javascript:;\" class=\"driver_approved btn btn-default\" style=\"margin-right:5px;\" data-id=\"$driver_id\" >".Driver::t("Approved")."</a>";
				    	$actions_3.="<a href=\"javascript:;\" class=\"driver_denied btn btn-danger\" data-id=\"$driver_id\" >".t("Denied")."</a>";
				    }
			    }
			    
			    $feed_data['aaData'][]=array(
			      $val['driver_id'],
			      $val['username'].$actions,
			      $val['first_name'],
			      $val['email'],
			      $val['phone'],
			      $val['team_name'],
			      $val['device_platform']."<br><span class=\"concat-text\">".$val['device_id']."</span>".$actions_3,
			      $date_created."<br>". "<span class=\"$class\">".Driver::t($val['status'])."</span>" ,
			      $actions_2
			    );			    
			}
			if (isset($_GET['debug'])){
			   dump($feed_data);
			}
			$this->otableOutput($feed_data);	
		}
		$this->otableNodata();
	}
	
	public function actionaddAgent()
	{
		$DbExt=new DbExt; 	
		$params=array(		  
		  'first_name'=>isset($this->data['first_name'])?$this->data['first_name']:'',
		  'last_name'=>isset($this->data['last_name'])?$this->data['last_name']:'',
		  'email'=>isset($this->data['email'])?$this->data['email']:'',
		  'phone'=>isset($this->data['phone'])?$this->data['phone']:'',
		  'username'=>isset($this->data['username'])?$this->data['username']:'',
		  'password'=>isset($this->data['password'])?md5($this->data['password']):'',
		  'team_id'=>isset($this->data['team_id_driver_new'])?$this->data['team_id_driver_new']:'',
		  'transport_type_id'=>isset($this->data['transport_type_id'])?$this->data['transport_type_id']:'',
		  'transport_description'=>isset($this->data['transport_description'])?$this->data['transport_description']:'',
		  'licence_plate'=>isset($this->data['licence_plate'])?$this->data['licence_plate']:'',
		  'color'=>isset($this->data['color'])?$this->data['color']:'',
		  'status'=>isset($this->data['status'])?$this->data['status']:'',
		  'date_created'=>date('c'),
		  'ip_address'=>$_SERVER['REMOTE_ADDR'],
		  'profile_photo'=>isset($this->data['profile_photo'])?$this->data['profile_photo']:''
		);		
		
		$params['user_type']=Driver::getUserType();
		$params['user_id']=Driver::getUserId();
		
		if(!isset($this->data['id'])){
			$this->data['id']='';
		}
				
		if(is_numeric($this->data['id'])){
			unset($params['date_created']);
			$params['date_modified']=date('c');
			
			if(empty($this->data['password'])){
			   unset($params['password']);
			}
			
			if ( Driver::getDriverByUsername($this->data['username'],$this->data['id'])){
				$this->msg=Driver::t("Username already exist");
				$this->jsonResponse();
				Yii::app()->end();
			}			
			if ( Driver::getDriverByEmail($this->data['email'],$this->data['id'])){
				$this->msg=Driver::t("Email already exist");
				$this->jsonResponse();
				Yii::app()->end();
			}			
			
			if ( $DbExt->updateData("{{driver}}",$params,'driver_id',$this->data['id'])){
				$this->code=1;
			    $this->msg=Driver::t("Successfully updated");
			    $this->details='new-agent';
			    
			    /*update team*/
			    //Driver::updateTeamDriver($this->data['id'],$params['team_id']);
			    
			} else $this->msg=Driver::t("failed cannot update record");
		} else {			
			
			if ( Driver::getDriverByUsername($this->data['username'])){
				$this->msg=Driver::t("Username already exist");
				$this->jsonResponse();
				Yii::app()->end();
			}			
			if ( Driver::getDriverByEmail($this->data['email'])){
				$this->msg=Driver::t("Email already exist");
				$this->jsonResponse();
				Yii::app()->end();
			}			
			
			if ( $DbExt->insertData('{{driver}}',$params)){
				$this->code=1;
				$this->msg=Driver::t("Successful");
				$this->details='new-agent';
			} else $this->msg=Driver::t("failed cannot insert record");
		}
		$this->jsonResponse();
	}
	
	public function actiongetDriverInfo()
	{		
		if(isset($this->data['id'])){
			if ( $res=Driver::driverInfo($this->data['id'])){
				 $this->code=1;
				 $this->msg=Driver::t("Successful");
				 $this->details=$res;
			} else $this->msg=Driver::t("Record not found");
		} else $this->msg=Driver::t("Missing parameters");
		$this->jsonResponse();
	}
	
	public function actionAddTask()
	{
						
		
		$DbExt=new DbExt; 		
		$req=array(
		  'trans_type'=>Driver::t("Transaction type is required"),
		  'customer_name'=>Driver::t("Customer name is required")
		);
				
		$Validator=new Validator;
		$Validator->required($req,$this->data);
		if($Validator->validate()){
			
			$params=array(
			  'task_description'=>isset($this->data['task_description'])?$this->data['task_description']:'',
			  'trans_type'=>isset($this->data['trans_type'])?$this->data['trans_type']:'',
			  'contact_number'=>isset($this->data['contact_number'])?$this->data['contact_number']:'',
			  'email_address'=>isset($this->data['email_address'])?$this->data['email_address']:'',
			  'customer_name'=>isset($this->data['customer_name'])?$this->data['customer_name']:'',
			  'delivery_date'=>isset($this->data['delivery_date'])?$this->data['delivery_date']:'',
			  'delivery_address'=>isset($this->data['delivery_address'])?$this->data['delivery_address']:'',
			  'team_id'=>isset($this->data['team_id'])?$this->data['team_id']:'',
			  'driver_id'=>isset($this->data['driver_id'])?$this->data['driver_id']:'',
			  'task_lat'=>isset($this->data['task_lat'])?$this->data['task_lat']:'',
			  'task_lng'=>isset($this->data['task_lng'])?$this->data['task_lng']:'',
			  'date_created'=>date('c'),
			  'ip_address'=>$_SERVER['REMOTE_ADDR'],
			  'user_type'=>Driver::getUserType(),
			  'user_id'=>Driver::getUserId(),
			  'dropoff_merchant'=>isset($this->data['dropoff_merchant'])?$this->data['dropoff_merchant']:'',
			  'dropoff_contact_name'=>isset($this->data['dropoff_contact_name'])?$this->data['dropoff_contact_name']:'',
			  'dropoff_contact_number'=>isset($this->data['dropoff_contact_number'])?$this->data['dropoff_contact_number']:'',
			  'drop_address'=>isset($this->data['drop_address'])?$this->data['drop_address']:'',
			  'dropoff_lat'=>isset($this->data['dropoff_lat'])?$this->data['dropoff_lat']:'',
			  'dropoff_lng'=>isset($this->data['dropoff_lng'])?$this->data['dropoff_lng']:'',
			);			
						
			if(!empty($params['delivery_date'])){
				$params['delivery_date']= date("Y-m-d G:i",strtotime($params['delivery_date']));
			}
			if($params['driver_id']>0){
				$params['status']='assigned';
			}
			/*dump($params);
			die();*/
			if(is_numeric($this->data['task_id'])){
				
				unset($params['date_created']);
				unset($params['user_type']);
				unset($params['user_id']);
				$params['date_modified']=date('c');				
				
				$task_info=Driver::getTaskId($this->data['task_id']);
				if( $task_info['status']!="unassigned"){
					unset($params['status']);
				}
								
				if ( $DbExt->updateData("{{driver_task}}",$params,'task_id',$this->data['task_id'])){
					$this->code=1;
					$this->msg=Driver::t("Successfully updated");
										
					if (isset($params['status'])){
						if ($params['status']=="assigned"){
							/*add to history*/
							$assigned_task=$params['status'];
							//if ( $res=Driver::getTaskId($this->data['task_id'])){
							if($task_info){
								$status_pretty = Driver::prettyStatus($task_info['status'],$assigned_task);
								$params_history=array(
								  'order_id'=>isset($task_info['order_id'])?$task_info['order_id']:'',
								  'remarks'=>$status_pretty,
								  'status'=>$assigned_task,
								  'date_created'=>date('c'),
								  'ip_address'=>$_SERVER['REMOTE_ADDR'],
								  'task_id'=>$this->data['task_id']
								);		
								$DbExt->insertData('{{order_history}}',$params_history);	
								
								// send notification to driver								
							    Driver::sendDriverNotification('ASSIGN_TASK',$res);
							    
							}				
						} 
					} else {						
				        Driver::sendDriverNotification('UPDATE_TASK',$task_info);
					}
					
				} else $this->msg=Driver::t("failed cannot update record");
			} else {
				if($DbExt->insertData("{{driver_task}}",$params)){
					$task_id=Yii::app()->db->getLastInsertID();
					$this->code=1;
					$this->msg=Driver::t("Successful");
					
					// send notification to driver
					if ( $info=Driver::getTaskId($task_id)){				
				       Driver::sendDriverNotification('ASSIGN_TASK',$info);
			        }			
					
				} else $this->msg=Driver::t("failed cannot insert record");
			}
		} else $this->msg=$Validator->getErrorAsHTML();
		$this->jsonResponse();
	}
	
	public function actiongetDashboardTask()
	{
		if (isset($this->data['status'])){
			//$status=$this->data['status'];
			$date='';
			if ( isset($this->data['date'])){
				$date=$this->data['date'];
			}
			
			$enabled_critical_task = getOptionA('enabled_critical_task');
			$critical_minutes = getOptionA('critical_minutes');
			if($critical_minutes<=0){
				$critical_minutes=5;
			}
			
			$data=''; $coordinates='';
			$status_list=array('unassigned','assigned','completed');
			foreach ($status_list as $status) {
				if ( $res = Driver::getTaskByStatus($this->userType(),$this->userId(),$status,$date)){
					$total=count($res);
					$html='';
					foreach ($res as $val) {			
						//dump($val);		
						if(!empty($val['task_lat']) && !empty($val['task_lng']) ){
							$coordinates[]=array(
							  'lat'=>$val['task_lat'],
							  'lng'=>$val['task_lng'],
							  'trans_type'=>$val['trans_type'],		
							  'customer_name'=>$val['customer_name'],
							  'address'=>$val['delivery_address'],
							  'task_id'=>$val['task_id'],
							  'status_raw'=>$val['status'],
							  'status'=>Driver::t($val['status']),		
							  'trans_type_raw'=>$val['trans_type'],
							  'trans_type'=>Driver::t($val['trans_type']),
							  'map_type'=>'restaurant'
							);
						} else {
							if ( $res_location=Driver::addressToLatLong($val['delivery_address'])){
								//dump($res_location);
								$val['task_lat']=$res_location['lat'];
								$val['task_lng']=$res_location['long'];
								
								$coordinates[]=array(
							      'lat'=>$res_location['lat'],
							      'lng'=>$res_location['long'],
							      'trans_type'=>$val['trans_type'],
							      'customer_name'=>$val['customer_name'],
							      'address'=>$val['delivery_address'],
							      'task_id'=>$val['task_id'],
							      'status_raw'=>$val['status'],
							      'status'=>Driver::t($val['status']),
							      'trans_type_raw'=>$val['trans_type'],
							      'trans_type'=>Driver::t($val['trans_type']),
							      'map_type'=>'restaurant'
							    );
							}
						}
						$html.=Driver::formatTask($val,$enabled_critical_task, $critical_minutes);
					}
					
										
					$data[$status]=array(
					  'total'=>$total,
					  'html'=>$html					  
					);								
					$this->details=$data;
				} else {
					$data[$status]='';
					$this->details=$data;
				}
			}
			
			/*get the driver online coordinates*/
			$agent_stats=array('active');			
			$include_offline=getOptionA('driver_include_offline_driver_map');
			if($include_offline==1){
			   $agent_stats=array('active','offline');
			}
			//dump($agent_stats);
			
			$driver_allowed_merchant_list=getOptionA('driver_allowed_merchant_list');
						
			$online_agent='';
			foreach ($agent_stats as $agent_stat) {
				$res_agent=Driver::getDriverByStats(
				  Driver::getUserType(),
				  Driver::getUserId(),
				  $agent_stat,
				  isset($this->data['date'])?$this->data['date']:date("Y-m-d"),
				  'active'
				);				

							
				$res_agent_admin='';
				
				if ( Driver::getUserType()=="merchant"){					
					$driver_allowed_team_to_merchant=getOptionA('driver_allowed_team_to_merchant');				
					if($driver_allowed_team_to_merchant==1){
						$res_agent_admin=Driver::getDriverByStats(
						  'admin',
						  '',
						  $agent_stat,
						  isset($this->data['date'])?$this->data['date']:date("Y-m-d"),
						  'active'					  
						);
					} elseif ( $driver_allowed_team_to_merchant == 2 ){
						$mtid = Driver::getUserId();								
						if(!empty($driver_allowed_merchant_list)){
							$_driver_allowed_merchant_list=json_decode($driver_allowed_merchant_list,true);
							if(in_array($mtid,(array)$_driver_allowed_merchant_list)){
								$res_agent_admin=Driver::getDriverByStats(
								  'admin',
								  '',
								  $agent_stat,
								  isset($this->data['date'])?$this->data['date']:date("Y-m-d"),
								  'active'					  
								);
							}
						}
					}
				}
				
				//dump($res_agent_admin);
				
				if (is_array($res_agent) && is_array($res_agent_admin)){
				   $res_agent=array_merge((array)$res_agent,(array)$res_agent_admin);
				} else if ( is_array($res_agent_admin)) {
					$res_agent=$res_agent_admin;
				}				
								
				if (is_array($res_agent) && count($res_agent)>=1){
				   foreach ($res_agent as $agent_val) {
				   	  $coordinates[]=array(
					   'driver_id'=>$agent_val['driver_id'],
					   'first_name'=>$agent_val['first_name'],
					   'last_name'=>$agent_val['last_name'],
					   'email'=>$agent_val['email'],
					   'phone'=>$agent_val['phone'],
					   'lat'=>$agent_val['location_lat'],
					   'lng'=>$agent_val['location_lng'],
					   'map_type'=>'driver',
					   'is_online'=>$agent_val['is_online']
					  );
				   }
				}
			}
			
		    //dump($coordinates);
			
			$this->code=1;	
			$this->msg=$coordinates;
			
		} else $this->msg=Driver::t("parameter status is missing");
		$this->jsonResponse();
	}
	
	private function userType()
	{
		return Driver::getUserType();
	}
	
	private function userId()
	{
		return Driver::getUserId();
	}
	
	public function actionassignTask()
	{
		$DbExt=new DbExt; 		
		$req=array(
		  'task_id'=>Driver::t("Task id is required"),
		  'team_id'=>Driver::t("Team id is required"),
		  'driver_id'=>Driver::t("Driver id is required"),
		);
		
		$assigned_task='assigned';
				
		
		$Validator=new Validator;
		$Validator->required($req,$this->data);
		if($Validator->validate()){
			$params=array(
			  'team_id'=>$this->data['team_id'],
			  'driver_id'=>$this->data['driver_id'],
			  'status'=>$assigned_task,
			  'date_modified'=>date('c'),
			  'ip_address'=>$_SERVER['REMOTE_ADDR']
			);
			if ( $DbExt->updateData("{{driver_task}}",$params,'task_id',$this->data['task_id'])){
				$this->code=1;
				$this->msg=Driver::t("Successfully updated");
				$this->details='assign-task';
				
				/*add to history*/
				if ( $res=Driver::getTaskId($this->data['task_id'])){
					$status_pretty = Driver::prettyStatus($res['status'],$assigned_task);
					
					$remarks_args=array(
					  '{from}'=>$res['status'],
					  '{to}'=>$assigned_task
					);
					$params_history=array(
					  'order_id'=>$res['order_id'],
					  'remarks'=>$status_pretty,
					  'status'=>$assigned_task,
					  'date_created'=>date('c'),
					  'ip_address'=>$_SERVER['REMOTE_ADDR'],
					  'task_id'=>$this->data['task_id'],
					  'remarks2'=>"Status updated from {from} to {to}",
					  'remarks_args'=>json_encode($remarks_args)
					);		
					$DbExt->insertData('{{order_history}}',$params_history);
				}				
				
				/*send notification to driver*/
		         Driver::sendDriverNotification('ASSIGN_TASK',$res=Driver::getTaskId($this->data['task_id']));
		         
		         if($res['order_id']>0){
			         if (FunctionsV3::hasModuleAddon("mobileapp")){
						/** Mobile save logs for push notification */
						Yii::app()->setImport(array(			
						  'application.modules.mobileapp.components.*',
						));
						AddonMobileApp::savedOrderPushNotification(array(
						  'order_id'=>$res['order_id'],
						  'status'=>$res['status'],
						));
					 }
		         }
				
			} else $this->msg=Driver::t("failed cannot update record");
		} else $this->msg=$Validator->getErrorAsHTML();
		$this->jsonResponse();
	}
	
	public function actionGetTaskDetails()
	{		
		
		if (isset($this->data['id'])){
			if ( $res=Driver::getTaskId($this->data['id'])){
				$res['status_raw']=!empty($res['status'])?$res['status']:'';
				$res['status']=!empty($res['status'])?Driver::t($res['status']):'';				
				$res['driver_name']=!empty($res['driver_name'])?$res['driver_name']:'';
				$res['team_name']=!empty($res['team_name'])?$res['team_name']:'';
				$res['customer_name']=!empty($res['customer_name'])?$res['customer_name']:'';
				$res['contact_number']=!empty($res['contact_number'])?$res['contact_number']:'';
				$res['email_address']=!empty($res['email_address'])?$res['email_address']:'';
				$res['delivery_date']=!empty($res['delivery_date'])?date("Y-m-d g:i a",strtotime($res['delivery_date'])):'-';
				$res['trans_type_raw']=$res['trans_type'];
				$res['trans_type']=!empty($res['trans_type'])?Driver::t($res['trans_type']):'';				
																		
				/*get task history*/				
				$history_details=''; $history_data='';
				//if ( $info=Driver::getTaskId($this->data['id'])){								
				if($info=$res){
					if($history_details = Driver::getTaskHistory($this->data['id'],$info['order_id'])){
						foreach ($history_details as $valh) {				
														
							$valh['status_raw']=$valh['status'];
							$valh['status']=Driver::t($valh['status']);
							
							if(!empty($valh['remarks2'])){							
								$args=json_decode($valh['remarks_args'],true);								
								if(is_array($args) && count($args)>=1){
									foreach ($args as $args_key=>$args_val) {
										$args[$args_key]=Driver::t($args_val);
									}
								}								
								$new_remarks=$valh['remarks2'];								
								$new_remarks=Yii::t("driver",$new_remarks,$args);								
								$valh['remarks']=$new_remarks;
							}
							
							$valh['date_created']=Yii::app()->functions->FormatDateTime($valh['date_created']);
							
							if (!empty($valh['customer_signature'])){
					            $valh['customer_signature_url']=Driver::uploadURL()."/".$valh['customer_signature'];
					            if (!file_exists(Driver::uploadPath()."/".$valh['customer_signature'])){
    					            $valh['customer_signature_url']='';
    				            }
				            }
				            
				            /*photo*/
				            if(!isset($valh['photo_task_id'])){
				            	$valh['photo_task_id']='';
				            }						
				            if ( $valh['photo_task_id']>0){
				            	if ( $photo_details=Driver::getPhotoDetails($valh['photo_task_id'])){				            	
				            		$photo='';
									if(!empty($photo_details['photo_name'])){
										$photo=Driver::driverUploadPath()."/".$photo_details['photo_name'];
							    		if(file_exists($photo)){
							    			$photo=websiteUrl()."/upload/driver/".$photo_details['photo_name'];
							    		}
									}
									$valh['photo_url']=$photo;	
				            	} else {
				            		$valh['photo_url']=3;
				            		$valh['photo_delete_msg']=Driver::t("This photo has been deleted");
				            	}
				            } else $valh['photo_url']=2;
				            
							$history_data[]=$valh;
						}
					} else {
						$history_data='';
					}
				}
												
				$res['history_data']=$history_data;
				
				// get the order details
				$order_details='';  $order_details_head='';
				if($res['order_id']>0){
					$order_id=$res['order_id'];					
					$_GET['backend']='true';
					if ( $data=Yii::app()->functions->getOrder2($order_id)){						
						$json_details=!empty($data['json_details'])?json_decode($data['json_details'],true):false;
						if ( $json_details !=false){
						    Yii::app()->functions->displayOrderHTML(array(
						       'merchant_id'=>$data['merchant_id'],
						       'order_id'=>$order_id,
						       'delivery_type'=>$data['trans_type'],
						       'delivery_charge'=>$data['delivery_charge'],
						       'packaging'=>$data['packaging'],
						       'cart_tip_value'=>$data['cart_tip_value'],
							   //'cart_tip_percentage'=>$data['cart_tip_percentage'],
							   'cart_tip_percentage'=>$data['cart_tip_percentage']/100,
							   'card_fee'=>$data['card_fee'],
							   'donot_apply_tax_delivery'=>$data['donot_apply_tax_delivery'],
							   'points_discount'=>isset($data['points_discount'])?$data['points_discount']:'' /*POINTS PROGRAM*/
						     ),$json_details,true , $order_id);	
						     $data2=Yii::app()->functions->details;
						     $order_details=$data2['html'];
						     
						     $merchant_info=Yii::app()->functions->getMerchant($data['merchant_id']);
                             $full_merchant_address=$merchant_info['street']." ".$merchant_info['city']. " ".$merchant_info['state']." ".$merchant_info['post_code'];
						     
						     $order_details_head.="<table class=\"table table-striped\">";
						      $order_details_head.="<tbody>";
						      $order_details_head.=Driver::receiptRow("Customer Name",$data['full_name']);
						      $order_details_head.=Driver::receiptRow("Merchant Name",$data['merchant_name']);
						      $order_details_head.=Driver::receiptRow("Telephone",$data['merchant_contact_phone']);
						      $order_details_head.=Driver::receiptRow("Address",$full_merchant_address);
						      $order_details_head.=Driver::receiptRow("TRN Type",$data['trans_type']);
						      $order_details_head.=Driver::receiptRow("Payment Type",strtoupper(t($data['payment_type'])));
						      if ( $data['payment_provider_name']){
						        $order_details_head.=Driver::receiptRow("Card#",$data['payment_provider_name']);
						      }
						      if ( $data['payment_type'] =="pyp"){
						      	$paypal_info=Yii::app()->functions->getPaypalOrderPayment($data['order_id']);	       
						      	$order_details_head.=Driver::receiptRow("Paypal Transaction ID",
						      	isset($paypal_info['TRANSACTIONID'])?$paypal_info['TRANSACTIONID']:'');
						      }
						      
						      $order_details_head.=Driver::receiptRow("Reference #",Yii::app()->functions->formatOrderNumber($data['order_id']));
						      if ( !empty($data['payment_reference'])){
						         $order_details_head.=Driver::receiptRow("Payment Ref",$data['payment_reference']);
						      }
						      
						      if ( $data['payment_type']=="ccr" || $data['payment_type']=="ocr"){
						      	  $order_details_head.=Driver::receiptRow("Card #",
						      	  Yii::app()->functions->maskCardnumber($data['credit_card_number'])
						      	  );
						      }
						      
						      $trn_date=date('M d,Y G:i:s',strtotime($data['date_created']));	                          
						      $order_details_head.=Driver::receiptRow("TRN Date",
						      Yii::app()->functions->translateDate($trn_date));
						      
						      						      
						      if (isset($data['delivery_date'])){
						      	  if(!empty($data['delivery_date'])){
						      	  $delivery_date=prettyDate($data['delivery_date']);
						      	  $delivery_date=Yii::app()->functions->translateDate($delivery_date);
						      	  $order_details_head.=Driver::receiptRow(
						      	  $data['trans_type']=="delivery"?"Delivery Date":"Pickup Date"
						      	  ,$delivery_date);
						      	  }
						      }
						      if (isset($data['delivery_time'])){
						      	  if(!empty($data['delivery_time'])){
						      	  	  $delivery_time=Yii::app()->functions->timeFormat($data['delivery_time'],true);
						      	  	  $order_details_head.=Driver::receiptRow(
						      	        $data['trans_type']=="delivery"?"Delivery Time":"Pickup Time"
						      	      ,$delivery_date);
						      	  }
						      }
						      
						      if(isset($data['delivery_asap'])){
						      	 if(!empty($data['delivery_asap'])){
						      	 	 $order_details_head.=Driver::receiptRow("Deliver ASAP",
						      	 	 $data['delivery_asap']==1?Driver::t("Yes"):""
						      	 	 );
						      	 }
						      }
						      
						      if (!empty($data['client_full_address'])){
		         	             $delivery_address=$data['client_full_address'];
		                      } else $delivery_address=$data['full_address'];	
						      
		                      $order_details_head.=Driver::receiptRow("Deliver to",$delivery_address);
		                      $order_details_head.=Driver::receiptRow("Delivery Instruction",$data['delivery_instruction']);
		                      
		                      if (!empty($data['location_name1'])){
		                      	 $location_name=$data['location_name1'];
		                      } else $location_name=$data['location_name'];
		                      
		                      $order_details_head.=Driver::receiptRow("Location Name",$location_name);
		                      $order_details_head.=Driver::receiptRow("Contact Number",
		                        !empty($data['contact_phone1'])?$data['contact_phone1']:$data['contact_phone']
		                      );
		                      
		                      if($data['order_change']>0.1){
		                      	 $order_details_head.=Driver::receiptRow("Change", 
		                      	  displayPrice( baseCurrency(), normalPrettyPrice($data['order_change']))
		                      	 );
		                      }
		                      
						      $order_details_head.="</tbody>";
						     $order_details_head.="</table>";
						     
						}
					} 
				}
								
				$res['order_details']=$order_details_head.$order_details;
				if(isset($res['merchant_name'])){
				   $res['merchant_name']=Driver::cleanText($res['merchant_name']);
				}
				//dump($res);
								
				if($res['dropoff_merchant']>0){				
					if($drop_merchant=Yii::app()->functions->getMerchant($res['dropoff_merchant'])){
						$res['dropoff_merchant_name']=stripslashes($drop_merchant['restaurant_name']);
					}
				}
				
				$this->code=1;
				$this->msg="OK";
				$this->details=$res;
				//dump($this->details);
				
			} else $this->msg=Driver::t("Cannot find records");
		} else $this->msg=Driver::t("missing parameter id");
		$this->jsonResponse();
	}
	
	public function actiongetTaskInfo()
	{
		$this->actionGetTaskDetails();
	}
	
	public function actiondeleteTask()
	{		
		
		$user_type=Driver::getUserType();		
		
		if ( $user_type=="merchant"){
			
			$driver_donot_allow_delete_task=getOptionA('driver_donot_allow_delete_task');		
			if($driver_donot_allow_delete_task==1){
				$this->msg=Driver::t("Sorry but you are not allowed to delete a task");
				$this->jsonResponse();
				Yii::app()->end();
			}
					
			$driver_allowed_days_delete_task=getOptionA('driver_allowed_days_delete_task');
			if($driver_allowed_days_delete_task>0){
				if ( $task_info=Driver::getTaskId($this->data['task_id'])){							
					$time_1=date('Y-m-d g:i:s a');			
				    $time_2=date("Y-m-d g:i:s a",strtotime($task_info['date_created']));			    
					$time_diff=Yii::app()->functions->dateDifference($time_2,$time_1);				
					if (is_array($time_diff) && count($time_diff)>=1){
						if ( $time_diff['days']>$driver_allowed_days_delete_task){
							$this->msg=Driver::t("Sorry but you are not allowed to delete this task");
				            $this->jsonResponse();
				            Yii::app()->end();
						}
					}
				}
			}
		}
				
		if(isset($this->data['task_id'])){		

			$task_id=$this->data['task_id'];			
			if ( $res2 = Driver::getUnAssignedDriver3($task_id)){				    		
				foreach ($res2 as $val2) {	  
	    		   $task_info=Driver::getTaskByDriverNTask($val2['task_id'], $val2['driver_id'] );
	    		   Driver::sendDriverNotification('CANCEL_TASK',$task_info);
	    		}
			} else {			
				if ( $info=Driver::getTaskId($this->data['task_id'])){				
					Driver::sendDriverNotification('CANCEL_TASK',$info);
				}		
			}	
			if( Driver::deleteTask($this->data['task_id'])){
				$this->code=1;
				$this->msg="OK";
			} else $this->msg=Driver::t("Failed deleting records");
		} else $this->msg=Driver::t("missing parameter id");
		$this->jsonResponse();
	}
	
	public function actionchangeStatus()
	{
		$req=array(
		  'task_id'=>Driver::t("Task ID is required"),
		  'status'=>Driver::t("Status is required"),
		);
		$Validator=new Validator;
		$Validator->required($req,$this->data);
		if($Validator->validate()){
			if ( $res=Driver::getTaskId($this->data['task_id'])){				
				$status_pretty = Driver::prettyStatus($res['status'],$this->data['status']);
				$params=array(
				  'order_id'=>$res['order_id'],
				  'remarks'=>$status_pretty,
				  'status'=>$this->data['status'],
				  'date_created'=>date('c'),
				  'ip_address'=>$_SERVER['REMOTE_ADDR'],
				  'task_id'=>$this->data['task_id'],
				  'reason'=>isset($this->data['reason'])?$this->data['reason']:''
				);										
				$DbExt=new DbExt; 
				if ( $DbExt->insertData("{{order_history}}",$params)){
					$this->code=1;
					$this->msg= Driver::t("Task Status Changed Successfully");
					$this->details='task-change-status-modal';
					
					/*update the status*/
					$DbExt->updateData("{{driver_task}}",array(
					 'status'=>$this->data['status']
					),'task_id',$this->data['task_id']);
					
					/*update assigment*/
					$sql_assign="
					UPDATE {{driver_assignment}}
					SET task_status=".Driver::q($this->data['status'])."
					WHERE
					task_id=".Driver::q($this->data['task_id'])."
					";
					$DbExt->qry($sql_assign);
										
					/*send push if status is cancel*/
					$drv_order_cancel=getOptionA('drv_order_cancel');					
					if ( $drv_order_cancel==$this->data['status']){
						Driver::sendDriverNotification('CANCEL_TASK',$res);
					}
										
	                if($res['order_id']>0){
	                	
	                	 Driver::updateOrderStatus( $res['order_id'] , $this->data['status'] );
	                	
				         if (FunctionsV3::hasModuleAddon("mobileapp")){
							/** Mobile save logs for push notification */
							Yii::app()->setImport(array(			
							  'application.modules.mobileapp.components.*',
							));
							AddonMobileApp::savedOrderPushNotification(array(
							  'order_id'=>$res['order_id'],
							  'status'=>$this->data['status'],
							));
						 }
			        }
					
				} else $this->msg=Driver::t("failed cannot update record");
			} else $this->msg=Driver::t("Task id not found");
		} else $this->msg=$Validator->getErrorAsHTML();
		$this->jsonResponse();
	}
	
	public function actionloadAgentDashboard()
	{		
		$data='';
		$agent_stats=array(
		  'active','offline','total'
		);
		
		$driver_allowed_merchant_list=getOptionA('driver_allowed_merchant_list');
		
		foreach ($agent_stats as $agent_stat) {
			$res=Driver::getDriverByStats(
			  Driver::getUserType(),
			  Driver::getUserId(),
			  $agent_stat,
			  isset($this->data['date'])?$this->data['date']:date("Y-m-d"),
			  'active',
			  isset($this->data['team_id'])?$this->data['team_id']:'',			  
			  isset($this->data['agent_name'])?$this->data['agent_name']:''
			);
			
			$res_agent_admin='';
			
			if ( Driver::getUserType()=="merchant"){
				$driver_allowed_team_to_merchant=getOptionA('driver_allowed_team_to_merchant');
				if($driver_allowed_team_to_merchant==1){
					$res_agent_admin=Driver::getDriverByStats(
						'admin',
						'',
						$agent_stat,
						isset($this->data['date'])?$this->data['date']:date("Y-m-d"),
						'active',
						isset($this->data['team_id'])?$this->data['team_id']:'',
						isset($this->data['agent_name'])?$this->data['agent_name']:''
					);
				} elseif ($driver_allowed_team_to_merchant==2){					
				    $mtid = Driver::getUserId();							
					if(!empty($driver_allowed_merchant_list)){
						$_driver_allowed_merchant_list=json_decode($driver_allowed_merchant_list,true);						
						if(in_array($mtid,(array)$_driver_allowed_merchant_list)){
							$res_agent_admin=Driver::getDriverByStats(
								'admin',
								'',
								$agent_stat,
								isset($this->data['date'])?$this->data['date']:date("Y-m-d"),
								'active',
								isset($this->data['team_id'])?$this->data['team_id']:'',
								isset($this->data['agent_name'])?$this->data['agent_name']:''
							);
						}
					}					
				}
			}

			if(is_array($res) && is_array($res_agent_admin)){		
		 	   $res=array_merge((array)$res,(array)$res_agent_admin);
			} elseif ( is_array($res_agent_admin) ){
			   $res=$res_agent_admin;	
			}
			
			/*for ($i=0; $i<count($res); $i++){
		      if (empty($res[$i])) unset($res[$i]);
		    }*/
			//dump($res);
			
			if($res){
				$data[$agent_stat]=$res;
			} else $data[$agent_stat]='';
		}
		
		//dump($data);
		
		$this->code=1;
		$this->msg="OK";
		$this->details=$data;
		$this->jsonResponse();
	}
	
	public function actiongetDriverDetails()
	{
		if ( isset($this->data['driver_id'])){
			if ( $res= Driver::driverInfo($this->data['driver_id'])){
				$data['driver_id']=$res['driver_id'];
				$data['user_id']=$res['user_id'];
				$data['name']=$res['first_name']." ".$res['last_name'];
				$data['email']=$res['email'];
				$data['phone']=$res['phone'];
				$data['transport_type_id']=Driver::t(ucwords($res['transport_type_id']));
				$data['licence_plate']=$res['licence_plate'];
				$data['team_name']=$res['team_name'];
				
				$data['app_version']=$res['app_version'];
				$data['device_platform']=$res['device_platform'];
								
				$order_details='';
				
				$transaction_date=isset($this->data['date'])?$this->data['date']:date("Y-m-d");
				if ( !$order=Driver::getTaskByDriverID($this->data['driver_id'],$transaction_date)){
					$order_details='';
				} else {
					foreach ($order as $order_val) {		
						$order_val['status_raw']=$order_val['status'];
						$order_val['status']=Driver::t($order_val['status']);						
						$order_details[]=$order_val;
					}
				}
				
				//dump($order_details);
								
				$this->code=1;
				$this->msg="OK";
				$this->details=array(
				  'info'=>$data,
				  'task'=>$order_details
				);				
				
			} else $this->msg=Driver::t("Driver details not found");
		} else $this->msg=Driver::t("Missing parameters");
		$this->jsonResponse();
	}
	
	public function actiontaskList()
	{
		$aColumns = array(
		  'task_id','order_id','trans_type','task_description',
		  'driver_name','customer_name','delivery_address','delivery_date'
		);
		$t=AjaxDataTables::AjaxData($aColumns);		
		if (isset($_GET['debug'])){
		    dump($t);
		}
		
		if (is_array($t) && count($t)>=1){
			$sWhere=$t['sWhere'];
			$sOrder=$t['sOrder'];
			$sLimit=$t['sLimit'];
		}	
				
        $and='';		
        if ( Driver::getUserType()=="admin"){
           //$and=" AND user_type=".Driver::q(Driver::getUserType())."  ";
        } else {
		   $and=" AND user_type=".Driver::q(Driver::getUserType())."";
		   $and.=" AND user_id=".Driver::q(Driver::getUserId())."  ";		
        }
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS *
		FROM
		{{driver_task_view}}
		WHERE 1		
		$and
		$sWhere
		$sOrder
		$sLimit
		";
		if (isset($_GET['debug'])){
		   dump($stmt);
		}
				
		$DbExt=new DbExt; 
		$DbExt->qry("SET SQL_BIG_SELECTS=1");
		
		if ( $res=$DbExt->rst($stmt)){
			
			$iTotalRecords=0;						
			$stmtc="SELECT FOUND_ROWS() as total_records";
			if ( $resc=$DbExt->rst($stmtc)){									
				$iTotalRecords=$resc[0]['total_records'];
			}
			
			$feed_data['sEcho']=intval($_GET['sEcho']);
			$feed_data['iTotalRecords']=$iTotalRecords;
			$feed_data['iTotalDisplayRecords']=$iTotalRecords;										
			
			foreach ($res as $val) {
				$date_created=Yii::app()->functions->prettyDate($val['date_created'],true);
			    $date_created=Yii::app()->functions->translateDate($date_created);		
			    
			    $status="<span class=\"tag ".$val['status']." \">".Driver::t($val['status'])."</span>";	
			    
			    $action="<a class=\"btn btn-primary task-details\"
			    	data-id=\"".$val['task_id']."\" href=\"javascript:;\">".Driver::t("Details")."</a>";
			    
			    if ( $val['status']=="unassigned"){
			    	$action="<a class=\"btn btn-default assign-agent\"
			    	data-id=\"".$val['task_id']."\" href=\"javascript:;\">".Driver::t("Assigned")."</a>";
			    }
			    
			    $feed_data['aaData'][]=array(
			      $val['task_id'],
			      $val['order_id']>0?$val['order_id']:'',
			      Driver::t($val['trans_type']),
			      $val['task_description'],
			      $val['driver_name'],
			      $val['customer_name'],
			      $val['delivery_address'],
			      $date_created,
			      $status,
			      $action
			    );			    
			}
			if (isset($_GET['debug'])){
			   dump($feed_data);
			}
			$this->otableOutput($feed_data);	
		}
		$this->otableNodata();
	}
	
	public function actiongeneralSettings()
	{		
				
		Yii::app()->functions->updateOptionAdmin("drv_google_api",
	    isset($this->data['drv_google_api'])?$this->data['drv_google_api']:'');
	    
	    Yii::app()->functions->updateOptionAdmin("drv_default_location",
	    isset($this->data['drv_default_location'])?$this->data['drv_default_location']:'');
	    
	    Yii::app()->functions->updateOptionAdmin("drv_order_status",
	    isset($this->data['drv_order_status'])?$this->data['drv_order_status']:'');
	    
	    Yii::app()->functions->updateOptionAdmin("drv_map_style",
	    isset($this->data['drv_map_style'])?$this->data['drv_map_style']:'');
	    
	    Yii::app()->functions->updateOptionAdmin("drv_delivery_time",
	    isset($this->data['drv_delivery_time'])?$this->data['drv_delivery_time']:'');
	    
	    //dump($this->data);
	    if(!empty($this->data['drv_default_location'])){
	       $country_list=require_once('CountryCode.php');	
	       $country_name='';
	       if(array_key_exists($this->data['drv_default_location'],(array)$country_list)){
	           $country_name=$country_list[$this->data['drv_default_location']];	   
	       } else $country_name=$this->data['drv_default_location'];	       
	       if ( $res=Driver::addressToLatLong($country_name))	{	       	
	       	   Yii::app()->functions->updateOptionAdmin("drv_default_location_lat",$res['lat']); 
	       	   Yii::app()->functions->updateOptionAdmin("drv_default_location_lng",$res['long']); 	       	
	       } 
	    }
	    
	    /*Yii::app()->functions->updateOptionAdmin("ORDER_AUTO_ADD_TASK",
	    isset($this->data['ORDER_AUTO_ADD_TASK'])?$this->data['ORDER_AUTO_ADD_TASK']:'');*/
	    
	    Yii::app()->functions->updateOptionAdmin("driver_api_hash_key",
	    isset($this->data['driver_api_hash_key'])?$this->data['driver_api_hash_key']:'');
	    
	    Yii::app()->functions->updateOptionAdmin("driver_push_api_key",
	    isset($this->data['driver_push_api_key'])?$this->data['driver_push_api_key']:'');
		
	    Yii::app()->functions->updateOptionAdmin("driver_website_title",
	    isset($this->data['driver_website_title'])?$this->data['driver_website_title']:'');
	    
	    Yii::app()->functions->updateOptionAdmin("driver_send_push_to_online",
	    isset($this->data['driver_send_push_to_online'])?$this->data['driver_send_push_to_online']:'');
	    
	    Yii::app()->functions->updateOptionAdmin("driver_owner_task",
	    isset($this->data['driver_owner_task'])?$this->data['driver_owner_task']:'');
	    
	    Yii::app()->functions->updateOptionAdmin("driver_include_offline_driver_map",
	    isset($this->data['driver_include_offline_driver_map'])?$this->data['driver_include_offline_driver_map']:'');
	    
	    Yii::app()->functions->updateOptionAdmin("driver_disabled_auto_refresh",
	    isset($this->data['driver_disabled_auto_refresh'])?$this->data['driver_disabled_auto_refresh']:'');
	    
	    Yii::app()->functions->updateOptionAdmin("drv_order_cancel",
	    isset($this->data['drv_order_cancel'])?$this->data['drv_order_cancel']:'');
	    
	    Yii::app()->functions->updateOptionAdmin("driver_allowed_team_to_merchant",
	    isset($this->data['driver_allowed_team_to_merchant'])?$this->data['driver_allowed_team_to_merchant']:'');
	    
	    Yii::app()->functions->updateOptionAdmin("driver_allowed_merchant_list",
	    isset($this->data['driver_allowed_merchant_list'])?json_encode($this->data['driver_allowed_merchant_list']):'');
	    
	    Yii::app()->functions->updateOptionAdmin("driver_donot_allow_delete_task",
	    isset($this->data['driver_donot_allow_delete_task'])?$this->data['driver_donot_allow_delete_task']:'');
	    
	    Yii::app()->functions->updateOptionAdmin("driver_allowed_days_delete_task",
	    isset($this->data['driver_allowed_days_delete_task'])?$this->data['driver_allowed_days_delete_task']:'');
	    
	    Yii::app()->functions->updateOptionAdmin("driver_merchant_task_to_admin",
	    isset($this->data['driver_merchant_task_to_admin'])?json_encode($this->data['driver_merchant_task_to_admin']):'');
	    
	    Yii::app()->functions->updateOptionAdmin("driver_show_admin_only_task",
	    isset($this->data['driver_show_admin_only_task'])?$this->data['driver_show_admin_only_task']:'');
	    
	    Yii::app()->functions->updateOptionAdmin("driver_merchant_block",
	    isset($this->data['driver_merchant_block'])?json_encode($this->data['driver_merchant_block']):'');
	    
	    Yii::app()->functions->updateOptionAdmin("driver_enabled_notes",
	    isset($this->data['driver_enabled_notes'])?$this->data['driver_enabled_notes']:'');
	    
	    Yii::app()->functions->updateOptionAdmin("driver_enabled_signature",
	    isset($this->data['driver_enabled_signature'])?$this->data['driver_enabled_signature']:'');
	    
	    Yii::app()->functions->updateOptionAdmin("driver_enabled_signup",
	    isset($this->data['driver_enabled_signup'])?$this->data['driver_enabled_signup']:'');
	    
	    Yii::app()->functions->updateOptionAdmin("driver_send_admin_notification_email",
	    isset($this->data['driver_send_admin_notification_email'])?$this->data['driver_send_admin_notification_email']:'');
	    
	    Yii::app()->functions->updateOptionAdmin("driver_signup_status",
	    isset($this->data['driver_signup_status'])?$this->data['driver_signup_status']:'');
	    	    
	    Yii::app()->functions->updateOptionAdmin("vibrate_interval",
	    isset($this->data['vibrate_interval'])?$this->data['vibrate_interval']:'');	    
	    
	    Yii::app()->functions->updateOptionAdmin("driver_enabled_addphoto",
	    isset($this->data['driver_enabled_addphoto'])?$this->data['driver_enabled_addphoto']:'');	    
	    
	    
	    Yii::app()->functions->updateOptionAdmin("driver_hide_pickup_task",
	    isset($this->data['driver_hide_pickup_task'])?$this->data['driver_hide_pickup_task']:'');	    
	    
	    Yii::app()->functions->updateOptionAdmin("driver_hide_delivery_task",
	    isset($this->data['driver_hide_delivery_task'])?$this->data['driver_hide_delivery_task']:'');	    
	    
	    Yii::app()->functions->updateOptionAdmin("driver_hide_successful_task",
	    isset($this->data['driver_hide_successful_task'])?$this->data['driver_hide_successful_task']:'');	    
	    
	    Yii::app()->functions->updateOptionAdmin("enabled_critical_task",
	    isset($this->data['enabled_critical_task'])?$this->data['enabled_critical_task']:'');	    
	    
	    Yii::app()->functions->updateOptionAdmin("critical_minutes",
	    isset($this->data['critical_minutes'])?$this->data['critical_minutes']:'');	    
	    
	    Yii::app()->functions->updateOptionAdmin("driver_tracking_options",
	    isset($this->data['driver_tracking_options'])?$this->data['driver_tracking_options']:'');	    
	    
	    Yii::app()->functions->updateOptionAdmin("driver_record_track_Location",
	    isset($this->data['driver_record_track_Location'])?$this->data['driver_record_track_Location']:'');	    
	    
	    Yii::app()->functions->updateOptionAdmin("driver_enabled_resize_photo",
	    isset($this->data['driver_enabled_resize_photo'])?$this->data['driver_enabled_resize_photo']:'');	    
	    
	    Yii::app()->functions->updateOptionAdmin("photo_resize_width",
	    isset($this->data['photo_resize_width'])?$this->data['photo_resize_width']:'');	    
	    
	    Yii::app()->functions->updateOptionAdmin("photo_resize_height",
	    isset($this->data['photo_resize_height'])?$this->data['photo_resize_height']:'');	    
	    
	    Yii::app()->functions->updateOptionAdmin("driver_calendar_language",
	    isset($this->data['driver_calendar_language'])?$this->data['driver_calendar_language']:'');	    
	    
	    Yii::app()->functions->updateOptionAdmin("driver_disabled_tracking_bg",
	    isset($this->data['driver_disabled_tracking_bg'])?$this->data['driver_disabled_tracking_bg']:'');	    
	    
	    Yii::app()->functions->updateOptionAdmin("driver_track_interval",
	    isset($this->data['driver_track_interval'])?$this->data['driver_track_interval']:'');	    
	    
	    Yii::app()->functions->updateOptionAdmin("driver_mandatory_signature",
	    isset($this->data['driver_mandatory_signature'])?$this->data['driver_mandatory_signature']:'');	    
	    
	    Yii::app()->functions->updateOptionAdmin("app_default_language",
	    isset($this->data['app_default_language'])?$this->data['app_default_language']:'');	    
	    
	    Yii::app()->functions->updateOptionAdmin("driver_app_name",
	    isset($this->data['driver_app_name'])?$this->data['driver_app_name']:'');	    
	    
	    $this->code=1;
	    $this->msg=Driver::t("Setting saved");	
	    $this->jsonResponse();
	}
	
	public function actionSaveTranslation()
	{		
		$mobile_dictionary='';
		if (is_array($this->data) && count($this->data)>=1){
			//$version=str_replace(".",'',phpversion());					
			$mobile_dictionary=json_encode($this->data);			
			$unicode=3;
		}				
		Yii::app()->functions->updateOptionAdmin('driver_mobile_dictionary',$mobile_dictionary);
		$this->code=1;
		$this->msg=Driver::t("translation saved");
		$this->details=$unicode;
		$this->jsonResponse();
	}		
	
	public function actionSaveNotification()
	{		
		
			
		$delivery=Driver::notificationListDelivery();
		$key="DELIVERY_";
		foreach ($delivery['DELIVERY'] as $val){
			foreach ($val as $val2) {
				$_key=$key.$val2;					
				Driver::updateOption(
				   $_key,isset($this->data[$_key])?$this->data[$_key]:''
				);
			}
		}
		
		$delivery=Driver::notificationListPickup();
		$key="PICKUP_";
		foreach ($delivery['PICKUP'] as $val){
			foreach ($val as $val2) {
				$_key=$key.$val2;					
				Driver::updateOption(
				   $_key,isset($this->data[$_key])?$this->data[$_key]:''
				);
			}
		}
		
		Driver::updateOption("ASSIGN_TASK_PUSH",
        isset($this->data['ASSIGN_TASK_PUSH'])?$this->data['ASSIGN_TASK_PUSH']:'');
        
        Driver::updateOption("ASSIGN_TASK_SMS",
        isset($this->data['ASSIGN_TASK_SMS'])?$this->data['ASSIGN_TASK_SMS']:'');
        
        Driver::updateOption("ASSIGN_TASK_EMAIL",
        isset($this->data['ASSIGN_TASK_EMAIL'])?$this->data['ASSIGN_TASK_EMAIL']:'');
        
        Driver::updateOption("CANCEL_TASK_PUSH",
        isset($this->data['CANCEL_TASK_PUSH'])?$this->data['CANCEL_TASK_PUSH']:'');
        
        Driver::updateOption("CANCEL_TASK_SMS",
        isset($this->data['CANCEL_TASK_SMS'])?$this->data['CANCEL_TASK_SMS']:'');
        
        Driver::updateOption("CANCEL_TASK_EMAIL",
        isset($this->data['CANCEL_TASK_EMAIL'])?$this->data['CANCEL_TASK_EMAIL']:'');
        
        Driver::updateOption("UPDATE_TASK_PUSH",
        isset($this->data['UPDATE_TASK_PUSH'])?$this->data['UPDATE_TASK_PUSH']:'');
        
        Driver::updateOption("UPDATE_TASK_SMS",
        isset($this->data['UPDATE_TASK_SMS'])?$this->data['UPDATE_TASK_SMS']:'');
        
        Driver::updateOption("UPDATE_TASK_EMAIL",
        isset($this->data['UPDATE_TASK_EMAIL'])?$this->data['UPDATE_TASK_EMAIL']:'');
        
        Driver::updateOption("FAILED_AUTO_ASSIGN_EMAIL",
        isset($this->data['FAILED_AUTO_ASSIGN_EMAIL'])?$this->data['FAILED_AUTO_ASSIGN_EMAIL']:'');
        
        Driver::updateOption("AUTO_ASSIGN_ACCEPTED_PUSH",
        isset($this->data['AUTO_ASSIGN_ACCEPTED_PUSH'])?$this->data['AUTO_ASSIGN_ACCEPTED_PUSH']:'');
        
        Driver::updateOption("NEW_DRIVER_PUSH",
        isset($this->data['NEW_DRIVER_PUSH'])?$this->data['NEW_DRIVER_PUSH']:'');
        
        Driver::updateOption("NEW_DRIVER_SMS",
        isset($this->data['NEW_DRIVER_SMS'])?$this->data['NEW_DRIVER_SMS']:'');
        
        Driver::updateOption("NEW_DRIVER_EMAIL",
        isset($this->data['NEW_DRIVER_EMAIL'])?$this->data['NEW_DRIVER_EMAIL']:'');
        
        Driver::updateOption("DRIVER_NEW_SIGNUP_EMAIL",
        isset($this->data['DRIVER_NEW_SIGNUP_EMAIL'])?$this->data['DRIVER_NEW_SIGNUP_EMAIL']:'');
        
        Driver::updateOption("SIGNUP_APPROVED_EMAIL",
        isset($this->data['SIGNUP_APPROVED_EMAIL'])?$this->data['SIGNUP_APPROVED_EMAIL']:'');
        
        Driver::updateOption("SIGNUP_APPROVED_SMS",
        isset($this->data['SIGNUP_APPROVED_SMS'])?$this->data['SIGNUP_APPROVED_SMS']:'');
        
        Driver::updateOption("SIGNUP_DENIED_SMS",
        isset($this->data['SIGNUP_DENIED_SMS'])?$this->data['SIGNUP_DENIED_SMS']:'');
        
        Driver::updateOption("SIGNUP_DENIED_EMAIL",
        isset($this->data['SIGNUP_DENIED_EMAIL'])?$this->data['SIGNUP_DENIED_EMAIL']:'');
		
		
		$this->code=1; $this->msg=Driver::t("Setting saved");
		$this->jsonResponse();
	}
	
	public function actionSaveNotificationTemplate()
	{
		//dump($this->data);
		$key=array('PUSH','SMS','EMAIL');
		
		$user_type=Driver::getLoginType();
		if ( $user_type=="admin"){
						
			foreach ($key as $val) {
				$key=$this->data['option_name']."_$val"."_TPL";						
				Yii::app()->functions->updateOptionAdmin($key,
				  isset($this->data[$val])?$this->data[$val]:''
				);
			}
			
		} else {
			
			$merchant_id=Driver::getUserId();				
			foreach ($key as $val) {
				$key=$this->data['option_name']."_$val"."_TPL";						
				Yii::app()->functions->updateOption($key,
				  isset($this->data[$val])?$this->data[$val]:'',
				  $merchant_id
				);
			}
			
		}
		$this->code=1; $this->msg=Driver::t("Template saved");
		$this->jsonResponse();
	}
	
	public function actionGetNotificationTPL()
	{
		$key=array('PUSH','SMS','EMAIL');
		$user_type=Driver::getLoginType();
		if ( $user_type=="admin"){
			
			$data='';			
			foreach ($key as $val) {
				$key=$this->data['option_name']."_$val"."_TPL";						
			    $data[$val]=getOptionA($key);
			}
			
		} else {
			
			$merchant_id=Driver::getUserId();			
			foreach ($key as $val) {
				$key=$this->data['option_name']."_$val"."_TPL";						
			    $data[$val]=getOption($merchant_id,$key);
			}
			
		}		
		$this->details=$data;
		$this->code=1; $this->msg=Driver::t("OK");
		$this->jsonResponse();
	}
	
	public function actionGetNotifications()
	{		
		$data=''; 
		$db_ext=new DbExt; 
		if ( $res=Driver::getNotifications( Driver::getUserType(),Driver::getUserId() ) ){
			foreach ($res as $val) {
								
				if(!empty($val['remarks2'])){							
					$args=json_decode($val['remarks_args'],true);								
					if(is_array($args) && count($args)>=1){
						foreach ($args as $args_key=>$args_val) {
							$args[$args_key]=t($args_val);
						}
					}								
					$new_remarks=$val['remarks2'];								
					$new_remarks=Yii::t("driver",$new_remarks,$args);								
					$val['remarks']=$new_remarks;
				}
				
				$data[]=array(
				  'title'=>$val['status']." ".Driver::t("Task ID").":".$val['task_id'],
				  'message'=>$val['remarks'],
				  'task_id'=>$val['task_id'],
				  'status'=>Driver::t($val['status'])
				);
				$db_ext->updateData('{{order_history}}',array(
				  'notification_viewed'=>1
				),'id',$val['id']);
			}
			$this->code=1;
			$this->details=$data;
		} else $this->msg="No notifications";
		$this->jsonResponse();
	}
	
	public function actiongetInitialNotifications()
	{
		$data=''; 
		$db_ext=new DbExt; 
		if ( $res=Driver::getNotifications( Driver::getUserType(),Driver::getUserId() , 1 ) ){
			foreach ($res as $val) {
				$data[]=array(
				  'title'=>$val['status']." ".Driver::t("Task ID").":".$val['task_id'],
				  'message'=>$val['remarks'],
				  'task_id'=>$val['task_id'],
				  'status'=>Driver::t($val['status'])
				);
				$db_ext->updateData('{{order_history}}',array(
				  'notification_viewed'=>1
				),'id',$val['id']);
			}
			$this->code=1;
			$this->details=$data;
		} else $this->msg="No notifications";
		$this->jsonResponse();
	}
	
	public function actionPushLogList()
	{
		$aColumns = array(
		  'push_id',
		  'push_title',
		  'push_message',
		  'push_type',
		  'device_platform',
		  'status'
		);
		$t=AjaxDataTables::AjaxData($aColumns);		
		if (isset($_GET['debug'])){
		    dump($t);
		}
		
		if (is_array($t) && count($t)>=1){
			$sWhere=$t['sWhere'];
			$sOrder=$t['sOrder'];
			$sLimit=$t['sLimit'];
		}	
		
		$and='';		
		$and.=" AND user_type=".Driver::q(Driver::getUserType())."";
		$and.=" AND user_id=".Driver::q(Driver::getUserId())."";
		if (isset($_GET['bulk_id'])){
			$and.=" AND bulk_id=".Driver::q($_GET['bulk_id'])."";
		}
				
		$stmt="SELECT SQL_CALC_FOUND_ROWS a.*			
		FROM
		{{driver_pushlog}} a
		WHERE 1
		$and		
		$sWhere
		$sOrder
		$sLimit
		";
		if (isset($_GET['debug'])){
		   dump($stmt);
		}
				
		$DbExt=new DbExt; 
		if ( $res=$DbExt->rst($stmt)){
			
			$iTotalRecords=0;						
			$stmtc="SELECT FOUND_ROWS() as total_records";
			if ( $resc=$DbExt->rst($stmtc)){									
				$iTotalRecords=$resc[0]['total_records'];
			}
			
			$feed_data['sEcho']=intval($_GET['sEcho']);
			$feed_data['iTotalRecords']=$iTotalRecords;
			$feed_data['iTotalDisplayRecords']=$iTotalRecords;										
			
			foreach ($res as $val) {
				$date_created=Yii::app()->functions->prettyDate($val['date_created'],true);
			    $date_created=Yii::app()->functions->translateDate($date_created);			
			    			    
			    if ($val['status']=="process"){
			    	$status="<span class=\"btn btn-primary\">".Driver::t($val['status'])."</span>";
			    } elseif ( $val['status']=="pending"){
			    	$status="<span class=\"btn btn-default\">".Driver::t($val['status'])."</span>";
			    } else $status="<span class=\"btn btn-danger\">".Driver::t($val['status'])."</span>";
			    
			    $feed_data['aaData'][]=array(
			      $val['push_id'],
			      $val['driver_id'],
			      Driver::t($val['push_title']),
			      $val['push_message'],
			      Driver::t($val['push_type']),
			      $val['device_platform']."<br><span class=\"concat-text\">".$val['device_id']."</span>",
			      $status."<br>".$date_created,
			    );			    
			}
			if (isset($_GET['debug'])){
			   dump($feed_data);
			}
			$this->otableOutput($feed_data);	
		}
		$this->otableNodata();
	}
	
	public function actionChartReports()
	{	
		//dump($this->data);
		$data='';
		if ( $data=Driver::generateReports($this->data['chart_type'], $this->data['time_selection'],
		   $this->data['team_selection'], $this->data['driver_selection'],
		   $this->data['chart_type_option'],
		   $this->data['start_date'],
		   $this->data['end_date']
		    )){		    	
		}		
						
		$new_data='';
			
		if (is_array($data) && count($data)>=1){
			
			$first_date=date("Y-m-d",strtotime($data[0]['delivery_date']."-1 day"));
				$new_data[]=array(
				   'date'=>$first_date,
				   'successful'=>0,
				   'cancelled'=>0,
				   'failed'=>0
		    );
			
			foreach ($data as $val) {
				//dump($val);
				switch ($val['status']) {
					
					case "successful":	
					$new_data[]=array(
					  'date'=>$val['delivery_date'],
					  'successful'=>$val['total'],
					  'driver_name'=>isset($val['driver_name'])?$val['driver_name']:'',
					  'total_order_amount'=>isset($val['total_order_amount'])?$val['total_order_amount']:'',
					);
					break;
						
					case "cancelled":	
					$new_data[]=array(
					  'date'=>$val['delivery_date'],
					  'cancelled'=>$val['total'],
					  'driver_name'=>isset($val['driver_name'])?$val['driver_name']:'',
					  'total_order_amount'=>isset($val['total_order_amount'])?$val['total_order_amount']:'',
					);
					break;
					
					case "failed":	
					$new_data[]=array(
					  'date'=>$val['delivery_date'],
					  'failed'=>$val['total'],
					  'driver_name'=>isset($val['driver_name'])?$val['driver_name']:'',
					  'total_order_amount'=>isset($val['total_order_amount'])?$val['total_order_amount']:'',
					);
					break;
				
					default:
						break;
				}
			}
		} else {
			/*$new_data[]=array(
			  'date'=>date("Y-m-d"),
			  'failed'=>0,
			  'driver_name'=>''
			);*/
		}
		
		$table='';
		
				
		if ( $this->data['chart_type_option']=="agent"){
		
			ob_start();
			require_once('charts-bar.php');
			$charts = ob_get_contents();
            ob_end_clean();
                        
            ob_start();
            require_once('chart-bar-table.php');
            $table = ob_get_contents();
            ob_end_clean();
            
		} else {						        
            ob_start();
		    require_once('charts.php');		   
		    $charts = ob_get_contents();
            ob_end_clean();
            
            ob_start();
			require_once('chart-table.php');			
			$table = ob_get_contents();
            ob_end_clean();
		}		
		$this->code=1;
		$this->msg="OK";
		$this->details=array(
		  'charts'=>$charts,
		  'table'=>$table
		);
		$this->jsonResponse();
	}
	
	public function actionsaveAssigmentSettings()
	{		
		$this->code=1;
		Driver::updateOption('driver_auto_assign_type', 
		isset($this->data['driver_auto_assign_type'])?$this->data['driver_auto_assign_type']:'' );
		
		Driver::updateOption('driver_assign_request_expire', 
		isset($this->data['driver_assign_request_expire'])?$this->data['driver_assign_request_expire']:'' );
		
		Driver::updateOption('driver_enabled_auto_assign', 
		isset($this->data['driver_enabled_auto_assign'])?$this->data['driver_enabled_auto_assign']:'' );
		
		Driver::updateOption('driver_include_offline_driver', 
		isset($this->data['driver_include_offline_driver'])?$this->data['driver_include_offline_driver']:'' );
		
		Driver::updateOption('driver_autoassign_notify_email', 
		isset($this->data['driver_autoassign_notify_email'])?$this->data['driver_autoassign_notify_email']:'' );
		
		Driver::updateOption('driver_request_expire', 
		isset($this->data['driver_request_expire'])?$this->data['driver_request_expire']:'' );
		
		Driver::updateOption('driver_within_radius', 
		isset($this->data['driver_within_radius'])?$this->data['driver_within_radius']:'' );
		
		Driver::updateOption('driver_within_radius_unit', 
		isset($this->data['driver_within_radius_unit'])?$this->data['driver_within_radius_unit']:'' );
				
		Driver::updateOption('driver_auto_assign_retry', 
		isset($this->data['driver_auto_assign_retry'])?$this->data['driver_auto_assign_retry']:'' );
		
		$this->msg= Driver::t("Setting saved");
		$this->jsonResponse();
	}
	
	public function actionUploadCertificate()
	{
		require_once('Uploader.php');
		$path_to_upload=Driver::certificatePath();
        $valid_extensions = array('pem'); 
        if(!file_exists($path_to_upload)) {	
           if (!@mkdir($path_to_upload,0777)){           	               	
           	    $this->msg=Driver::t("Error has occured cannot create upload directory");
                $this->jsonResponse();
           }		    
	    }
	    
        $Upload = new FileUpload('uploadfile');
        $ext = $Upload->getExtension(); 
        //$Upload->newFileName = mktime().".".$ext;
        $result = $Upload->handleUpload($path_to_upload, $valid_extensions);                
        if (!$result) {                    	
            $this->msg=$Upload->getErrorMsg();            
        } else {         	
        	$this->code=1;
        	$this->msg=Driver::t("upload done");        	        
			$this->details=Yii::app()->getBaseUrl(true)."/upload/".$_GET['uploadfile'];			
        }
        $this->jsonResponse();
	}	
	
	public function actionsaveIOSSettings()
	{
		
		Yii::app()->functions->updateOptionAdmin("driver_ios_push_dev_cer",
	    isset($this->data['driver_ios_push_dev_cer'])?$this->data['driver_ios_push_dev_cer']:'');
	    
	    Yii::app()->functions->updateOptionAdmin("driver_ios_push_prod_cer",
	    isset($this->data['driver_ios_push_prod_cer'])?$this->data['driver_ios_push_prod_cer']:'');
	    
	    Yii::app()->functions->updateOptionAdmin("driver_ios_push_mode",
	    isset($this->data['driver_ios_push_mode'])?$this->data['driver_ios_push_mode']:'');
	    
	    Yii::app()->functions->updateOptionAdmin("driver_ios_pass_phrase",
	    isset($this->data['driver_ios_pass_phrase'])?$this->data['driver_ios_pass_phrase']:'');
		
	    $this->code=1;
		$this->msg= Driver::t("Setting saved");
		$this->jsonResponse();
	}
	
	public function actionRetryAutoAssign()
	{		
		if ( isset($this->data['task_id'])){
			$task_id=$this->data['task_id'];
			$this->code=1;
			$this->msg="OK";
						
			$less="-1";
						
			$params=array(			  
			  'assignment_status'=>'waiting for driver acknowledgement',
			  'assign_started'=>date('c',strtotime("$less min")),
			  'auto_assign_type'=>''
			);
						
			$db=new DbExt;
			$db->updateData("{{driver_task}}",$params,'task_id',$task_id);
			
			/*$stmt="UPDATE 
			{{driver_assignment}}
			SET status='pending',
			task_status='unassigned'
			WHERE
			task_id=".Driver::q($task_id)."
			";*/			
			$stmt="DELETE FROM
			{{driver_assignment}}
			WHERE
			task_id=".Driver::q($task_id)."
			";
			$db->qry($stmt);
						
			//re process
			//$url=Yii::app()->getBaseUrl(true)."/driver/cron/processautoassign";
			
			$url=Yii::app()->getBaseUrl(true)."/driver/cron/autoassign";
			@file_get_contents($url);
			
		} else $this->msg=Driver::t("Missing task id");
		$this->jsonResponse();
	}
	
	public function actionSendPushToDriver()
	{		
		$driver_id=$this->data['push_form_driver_id'];
		if ($info=Driver::driverInfo($driver_id)){			
			$params=array(
			  'driver_id'=>$this->data['push_form_driver_id'],
			  'push_title'=>$this->data['push_title'],
			  'push_message'=>$this->data['push_message'],
			  'date_created'=>date('c'),
			  'ip_address'=>$_SERVER['REMOTE_ADDR'],
			  'push_type'=>"private",
			  'actions'=>"private",
			  'device_platform'=>$info['device_platform'],
			  'device_id'=>$info['device_id'],
			  'user_type'=>Driver::getUserType(),
			  'user_id'=>Driver::getUserId(),
			);					
			if ( self::$db->insertData("{{driver_pushlog}}", $params)){
				$push_id=Yii::app()->db->getLastInsertID();
				$this->code=1;
				$this->msg=Driver::t("Push has been saved please wait until the cron process the push");				
			    Driver::RunPush( $push_id ); 
			} else $this->msg=Driver::t("failed cannot insert record");
		} else $this->msg=Driver::t("Record not found");
		$this->jsonResponse();
	}
	
	public function actionSendPushBulk()
	{		
		
		if($this->data['team_id2']<=0){
			$this->msg=Driver::t("Please select team");
			$this->jsonResponse();
		}
		
		$params=array(
		  'push_title'=>$this->data['push_title2'],
		  'push_message'=>$this->data['push_message2'],
		  'date_created'=>date('c'),
		  'ip_address'=>$_SERVER['REMOTE_ADDR'],
		  'team_id'=>$this->data['team_id2'],
		  'user_type'=>Driver::getUserType(),
		  'user_id'=>Driver::getUserId()
		);				
		if ( self::$db->insertData("{{driver_bulk_push}}", $params)){
			$push_id=Yii::app()->db->getLastInsertID();
			$this->code=1;
			$this->msg=Driver::t("Push has been saved please wait until the cron process the push");		    
		} else $this->msg=Driver::t("failed cannot insert record");
	    $this->jsonResponse();
	}

	public function actiongetMerchantAdddress()
	{		
		if ( $res=Yii::app()->functions->getMerchant($this->data['mtid'])){			 
						 
			 $mtid=$this->data['mtid'];
			 
			 $address=$res['street'];
			 if(!empty($res['city'])){
			 	$address.=" ".$res['city'];
			 }
			 if(!empty($res['state'])){
			 	$address.=" ".$res['state'];
			 }
			 if(!empty($res['post_code'])){
			 	$address.=" ".$res['post_code'];
			 }
			 			 
			 $this->code=1;
			 $this->msg="OK";
			 $this->details=array(
			   'address'=>$address,
			   'lat'=>getOption($mtid,'merchant_latitude'),
			   'lng'=>getOption($mtid,'merchant_longtitude'),
			   'contact_name'=>!empty($res['contact_name'])?$res['contact_name']:'',
			   'contact_number'=>!empty($res['contact_phone'])?$res['contact_phone']:''
			 );
			 
		} else $this->msg=Driver::t("Not found");
		$this->jsonResponse();
	}
	
	public function actionassignToAllDrivers()
    {    	
    	$db=new DbExt;
    	if ( $res=Driver::getTaskId($this->data['task_id'])){
    		
    		$task_id=$this->data['task_id'];
    		$assign_type="send_to_all";
    		
    		$and='';   
    		
    		$and.=" AND user_type=".Driver::q($res['user_type'])."";
    		$and.=" AND user_id=".Driver::q($res['user_id'])."";
    		
    		$driver_allowed_team_to_merchant=getOptionA('driver_allowed_team_to_merchant');
			//dump("driver_allowed_team_to_merchant=>".$driver_allowed_team_to_merchant);					  
			if($driver_allowed_team_to_merchant>0){
			  	  if($driver_allowed_team_to_merchant==1){
			  	  	 $and.=" OR user_type='admin' ";
			  	  }  elseif ($driver_allowed_team_to_merchant==2){
			  	  	 $driver_allowed_merchant_list=getOptionA('driver_allowed_merchant_list');
			  	  	 if(!empty($driver_allowed_merchant_list)){
			  	  	 	$driver_allowed_merchant_list=json_decode($driver_allowed_merchant_list,true);
			  	  	 	if($res['merchant_id']>0){
			  	  	 		if(in_array($res['merchant_id'],(array)$driver_allowed_merchant_list)){
			  	  	 			$and.=" OR user_type='admin' ";
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
			
			$stmt="SELECT a.* FROM {{driver}} a		
			WHERE 1
			$and			
			";					
			//dump($stmt);
			if ( $res2=$db->rst($stmt)){				
				$assignment_status=t("waiting for driver acknowledgement");
				foreach ($res2 as $val) {
					$params=array(
					  'auto_assign_type'=>$assign_type,
					  'task_id'=>$res['task_id'],
					  'driver_id'=>$val['driver_id'],
					  'first_name'=>$val['first_name'],
					  'last_name'=>$val['last_name'],
					  'date_created'=>Driver::dateNow(),
					  'ip_address'=>$_SERVER['REMOTE_ADDR']
					);					
					$db->insertData("{{driver_assignment}}",$params);
				}
								
				$driver_assign_request_expire = Driver::getOption('driver_assign_request_expire',
				$res['user_type'],$res['merchant_id']);				
				
				$less="-1";
				if($driver_assign_request_expire>0){
					$less="-$driver_assign_request_expire";
				}
				
				$params_task=array(
				 'auto_assign_type'=>$assign_type,
				 'assign_started'=>date('Y-m-d G:i:s',strtotime("$less min")),
				 'assignment_status'=> $assignment_status
				);
				
				$db->updateData("{{driver_task}}",$params_task,'task_id',$task_id);
				
				$this->code=1;
				$this->msg=Driver::t("Successful");
				
			} else $this->msg=Driver::t("Unable to assign to all drivers");
    		
    	} else $this->msg=Driver::t("Task not found");
    	$this->jsonResponse();
    }    
    
    public function actionuploadprofilephoto()
    {
    	require_once('Uploader.php');
		$path_to_upload=Driver::driverUploadPath();
        $valid_extensions = array('jpeg','jpg','png','gif'); 
        if(!file_exists($path_to_upload)) {	
           if (!@mkdir($path_to_upload,0777)){           	               	
           	    $this->msg=Driver::t("Error has occured cannot create upload directory");
                $this->jsonResponse();
           }		    
	    }
	    
        $Upload = new FileUpload('uploadfile');
        $ext = $Upload->getExtension(); 
        //$Upload->newFileName = mktime().".".$ext;
        $result = $Upload->handleUpload($path_to_upload, $valid_extensions);                
        if (!$result) {                    	
            $this->msg=$Upload->getErrorMsg();            
        } else {         	
        	$this->code=1;
        	$this->msg=Driver::t("upload done");        	        
			$this->details=Yii::app()->getBaseUrl(true)."/upload/".$_GET['uploadfile'];			
        }
        $this->jsonResponse();
    }
    
    public function actiondriverUpdateStatus()
    {    	
    	$db=new DbExt;
    	    	
    	if(isset($this->data['status'])){
    		if ( $this->data['status']==1){
    			$params=array('status'=>"active");    			
    			$db->updateData("{{driver}}",$params,'driver_id',$this->data['driver_id']);
    			$this->code=1;
    			$this->msg=Driver::t("Successful");
    			
    			$driver_email=''; $phone='';
    			if($driver_info=Driver::driverInfo($this->data['driver_id'])){
    			   $driver_email=$driver_info['email'];
    			   $phone=$driver_info['phone'];
    			}
    			//dump($driver_info); die();
    			    			
    			$SIGNUP_APPROVED_SMS=getOptionA('SIGNUP_APPROVED_SMS');
    			$SIGNUP_APPROVED_EMAIL=getOptionA('SIGNUP_APPROVED_EMAIL');
    			
    			$company_name=Yii ::app()->functions->getOptionAdmin('website_title');  
    			
    			if($SIGNUP_APPROVED_SMS==1){
    				$tpl=getOptionA('SIGNUP_APPROVED_SMS_TPL');
    				if(!empty($tpl)){    				  
                      $tpl=Driver::smarty('CompanyName',$company_name,$tpl);
                      $tpl=Driver::smarty('DriverName',$driver_info['first_name'],$tpl);
                      $tpl=Driver::smarty('DriverUsername',$driver_info['username'],$tpl);
                      $tpl=Driver::smarty('DriverEmail',$driver_info['email'],$tpl);                         
			           if ( $send_sms= Yii::app()->functions->sendSMS($phone,$tpl)){		   	    
					   	    $params=array(
					   	      'broadcast_id'=>"999999999",
							  'contact_phone'=>$phone,
							  'sms_message'=>$tpl,
							  'status'=>isset($send_sms['msg'])?$send_sms['msg']:'',
							  'gateway_response'=>isset($send_sms['raw'])?$send_sms['raw']:'',
							  'gateway'=>$send_sms['sms_provider'],
							  'date_created'=>date('c'),
							  'ip_address'=>$_SERVER['REMOTE_ADDR']
							);				
							$db->insertData("{{sms_broadcast_details}}",$params);
					   }
    				}
    			}
    			
    			if($SIGNUP_APPROVED_EMAIL==1){
    				$tpl=getOptionA('SIGNUP_APPROVED_EMAIL_TPL');
    				$tpl=Driver::smarty('CompanyName',$company_name,$tpl);
                    $tpl=Driver::smarty('DriverName',$driver_info['first_name'],$tpl);
                    $tpl=Driver::smarty('DriverUsername',$driver_info['username'],$tpl);
                    $tpl=Driver::smarty('DriverEmail',$driver_info['email'],$tpl);
    				$stats=sendEmail($driver_email,'',Driver::t("SIGNUP_APPROVED"),$tpl);
    				
    				Driver::logEmail($driver_email,"SIGNUP_APPROVED",$tpl,$stats,Driver::getUserType(),Driver::getUserId());
    				    				
    			}
    			
    		} else {
    			$params=array('status'=>"denied");
    			$db->updateData("{{driver}}",$params,'driver_id',$this->data['driver_id']);
    			$this->code=1;
    			$this->msg=Driver::t("Successful");
    			
    			$driver_email=''; $phone='';
    			if($driver_info=Driver::driverInfo($this->data['driver_id'])){
    			   $driver_email=$driver_info['email'];
    			   $phone=$driver_info['phone'];
    			}
    			//dump($driver_info); die();
    			    			
    			$SIGNUP_DENIED_SMS=getOptionA('SIGNUP_DENIED_SMS');
    			$SIGNUP_DENIED_EMAIL=getOptionA('SIGNUP_DENIED_EMAIL');
    			
    			
    			$company_name=Yii ::app()->functions->getOptionAdmin('website_title');  
    			
    			if($SIGNUP_DENIED_SMS==1){
    				$tpl=getOptionA('SIGNUP_DENIED_SMS_TPL');
    				if(!empty($tpl)){    				  
                      $tpl=Driver::smarty('CompanyName',$company_name,$tpl);
                      $tpl=Driver::smarty('DriverName',$driver_info['first_name'],$tpl);
                      $tpl=Driver::smarty('DriverUsername',$driver_info['username'],$tpl);
                      $tpl=Driver::smarty('DriverEmail',$driver_info['email'],$tpl);                        
			           if ( $send_sms= Yii::app()->functions->sendSMS($phone,$tpl)){		   	    
					   	    $params=array(
					   	      'broadcast_id'=>"999999999",
							  'contact_phone'=>$phone,
							  'sms_message'=>$tpl,
							  'status'=>isset($send_sms['msg'])?$send_sms['msg']:'',
							  'gateway_response'=>isset($send_sms['raw'])?$send_sms['raw']:'',
							  'gateway'=>$send_sms['sms_provider'],
							  'date_created'=>date('c'),
							  'ip_address'=>$_SERVER['REMOTE_ADDR']
							);				
							$db->insertData("{{sms_broadcast_details}}",$params);
					   }
    				}
    			}
    			    			
    			if($SIGNUP_DENIED_EMAIL==1){
    				$tpl=getOptionA('SIGNUP_DENIED_EMAIL_TPL');
    				if(!empty($tpl)){
	    				$tpl=Driver::smarty('CompanyName',$company_name,$tpl);
	                    $tpl=Driver::smarty('DriverName',$driver_info['first_name'],$tpl);
	                    $tpl=Driver::smarty('DriverUsername',$driver_info['username'],$tpl);
	                    $tpl=Driver::smarty('DriverEmail',$driver_info['email'],$tpl);                    
	    				$stats = sendEmail($driver_email,'',Driver::t("SIGNUP_DENIED"),$tpl);
	    				
	    				Driver::logEmail($driver_email,"SIGNUP_DENIED",
	    				$tpl,$stats,Driver::getUserType(),Driver::getUserId());
    				}
    			}
    		}
    	} else $this->msg=Driver::t("Missing status");
    	$this->jsonResponse();
    }
    
    public function actionBroadCastLogs()
    {
    	$aColumns = array(
		  'bulk_id',
		  'push_title',
		  'push_message',
		  'date_created',		  
		  'status',
		  'bulk_id'
		);
		$t=AjaxDataTables::AjaxData($aColumns);		
		if (isset($_GET['debug'])){
		    dump($t);
		}
		
		if (is_array($t) && count($t)>=1){
			$sWhere=$t['sWhere'];
			$sOrder=$t['sOrder'];
			$sLimit=$t['sLimit'];
		}	
		
		$and=" AND user_type=".Driver::q(Driver::getUserType())." ";
		$and.=" AND user_id=".Driver::q(Driver::getUserId())." ";
				
		$stmt="SELECT SQL_CALC_FOUND_ROWS a.*			
		FROM
		{{driver_bulk_push}} a
		WHERE 1
		$and		
		$sWhere
		$sOrder
		$sLimit
		";
		if (isset($_GET['debug'])){
		   dump($stmt);
		}
				
		$DbExt=new DbExt; 
		if ( $res=$DbExt->rst($stmt)){
			
			$iTotalRecords=0;						
			$stmtc="SELECT FOUND_ROWS() as total_records";
			if ( $resc=$DbExt->rst($stmtc)){									
				$iTotalRecords=$resc[0]['total_records'];
			}
			
			$feed_data['sEcho']=intval($_GET['sEcho']);
			$feed_data['iTotalRecords']=$iTotalRecords;
			$feed_data['iTotalDisplayRecords']=$iTotalRecords;										
			
			foreach ($res as $val) {
				$date_created=Yii::app()->functions->prettyDate($val['date_created'],true);
			    $date_created=Yii::app()->functions->translateDate($date_created);			
			    
			    $link=Yii::app()->createUrl('driver/index/pushlogs',array(
			      'bulk_id'=>$val['bulk_id']
			    ));
			    $action='<a href="'.$link.'" class="btn btn-info">'.Driver::t("View").'</a>';
			    
			    $feed_data['aaData'][]=array(
			      $val['bulk_id'],			      
			      $val['push_title'],
			      $val['push_message'],			     			      
			      $date_created,
			      "<span class=\"tag ".$val['status']."\">".Driver::t($val['status'])."</span>",
			      $action
			    );			    
			}
			if (isset($_GET['debug'])){
			   dump($feed_data);
			}
			$this->otableOutput($feed_data);	
		}
		$this->otableNodata();
    }
    
    public function actiongeneralSettingsMerchant()
    {
    	    	
        Driver::updateOption("merchant_driver_calendar_language",
	    isset($this->data['merchant_driver_calendar_language'])?$this->data['merchant_driver_calendar_language']:'');
	    
    	$this->code=1;
    	$this->msg=Driver::t("Setting saved");
    	$this->jsonResponse();
    }
    
    public function actionSSMLogs()
    {
    	$aColumns = array(
		  'id',
		  'contact_phone',
		  'sms_message',
		  'gateway',		  
		  'status',
		  'date_created'
		);
		$t=AjaxDataTables::AjaxData($aColumns);		
		if (isset($_GET['debug'])){
		    dump($t);
		}
		
		if (is_array($t) && count($t)>=1){
			$sWhere=$t['sWhere'];
			$sOrder=$t['sOrder'];
			$sLimit=$t['sLimit'];
		}	
		
		$and=" AND user_type=".Driver::q(Driver::getUserType())." ";
		$and.=" AND user_id=".Driver::q(Driver::getUserId())." ";
				
		$stmt="SELECT SQL_CALC_FOUND_ROWS a.*			
		FROM
		{{driver_sms_logs}} a
		WHERE 1
		$and		
		$sWhere
		$sOrder
		$sLimit
		";
		if (isset($_GET['debug'])){
		   dump($stmt);
		}
				
		$DbExt=new DbExt; 
		if ( $res=$DbExt->rst($stmt)){
			
			$iTotalRecords=0;						
			$stmtc="SELECT FOUND_ROWS() as total_records";
			if ( $resc=$DbExt->rst($stmtc)){									
				$iTotalRecords=$resc[0]['total_records'];
			}
			
			$feed_data['sEcho']=intval($_GET['sEcho']);
			$feed_data['iTotalRecords']=$iTotalRecords;
			$feed_data['iTotalDisplayRecords']=$iTotalRecords;										
			
			foreach ($res as $val) {
				$date_created=Yii::app()->functions->prettyDate($val['date_created'],true);
			    $date_created=Yii::app()->functions->translateDate($date_created);			
			    
			    $feed_data['aaData'][]=array(
			      $val['id'],			      
			      $val['contact_phone'],
			      $val['sms_message'],		
			      $val['gateway'],
			      "<span class=\"tag ".$val['status']."\">".Driver::t($val['status'])."</span>",
			      $date_created,
			    );			    
			}
			if (isset($_GET['debug'])){
			   dump($feed_data);
			}
			$this->otableOutput($feed_data);	
		}
		$this->otableNodata();
    }
    
    public function actionEmailLogs()
    {
       $aColumns = array(
		  'id',
		  'sender',
		  'email_address',
		  'subject',	
		  'content',		  
		  'status',
		  'date_created'
		);
		$t=AjaxDataTables::AjaxData($aColumns);		
		if (isset($_GET['debug'])){
		    dump($t);
		}
		
		if (is_array($t) && count($t)>=1){
			$sWhere=$t['sWhere'];
			$sOrder=$t['sOrder'];
			$sLimit=$t['sLimit'];
		}	
		
		$and=" AND user_type=".Driver::q(Driver::getUserType())." ";
		$and.=" AND user_id=".Driver::q(Driver::getUserId())." ";
				
		$stmt="SELECT SQL_CALC_FOUND_ROWS a.*			
		FROM
		{{email_logs}} a
		WHERE 1
		$and		
		$sWhere
		$sOrder
		$sLimit
		";
		if (isset($_GET['debug'])){
		   dump($stmt);
		}
				
		$DbExt=new DbExt; 
		if ( $res=$DbExt->rst($stmt)){
			
			$iTotalRecords=0;						
			$stmtc="SELECT FOUND_ROWS() as total_records";
			if ( $resc=$DbExt->rst($stmtc)){									
				$iTotalRecords=$resc[0]['total_records'];
			}
			
			$feed_data['sEcho']=intval($_GET['sEcho']);
			$feed_data['iTotalRecords']=$iTotalRecords;
			$feed_data['iTotalDisplayRecords']=$iTotalRecords;										
			
			foreach ($res as $val) {
				$date_created=Yii::app()->functions->prettyDate($val['date_created'],true);
			    $date_created=Yii::app()->functions->translateDate($date_created);			
			    
			    $feed_data['aaData'][]=array(
			      $val['id'],			      
			      $val['sender'],
			      $val['email_address'],		
			      $val['subject'],
			      "<span class=\"truncate-text\">".$val['content']."</span>",
			      "<span class=\"tag ".$val['status']."\">".Driver::t($val['status'])."</span>",
			      $date_created,
			    );			    
			}
			if (isset($_GET['debug'])){
			   dump($feed_data);
			}
			$this->otableOutput($feed_data);	
		}
		$this->otableNodata();    	
    }
    
    public function actionloadAgentTrackBack()
	{
		
		if ( $res=Driver::getBackTrackRecords($this->data['track_driver_id'],$this->data['track_date'])){
			
			$this->code=1; $this->msg=Driver::t("Successful");
			$this->details=$res;
		} else $this->msg=Driver::t("Records not found");
		$this->jsonResponse();
	}
	
}/* end class*/