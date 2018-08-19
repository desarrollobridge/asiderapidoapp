<?php
class ApiController extends CController
{	
	public $data;
	public $code=2;
	public $msg='';
	public $details='';
	
	public function __construct()
	{
		$this->data=$_GET;
		
		$website_timezone=Yii::app()->functions->getOptionAdmin("website_timezone");		 
	    if (!empty($website_timezone)){
	 	   Yii::app()->timeZone=$website_timezone;
	    }		 
	    
	    Driver::handleLanguage();
	    
	}
	
	public function beforeAction($action)
	{				
		/*check if there is api has key*/		
		$action=Yii::app()->controller->action->id;				
		if(isset($this->data['api_key'])){
			if(!empty($this->data['api_key'])){			   
			   $continue=true;
			   $action=strtolower($action);
			   //if($action=="getLanguageSettings" || $action=="GetAppSettings" ){
			   if($action=="getlanguagesettings" || $action=="getappsettings" || $action=="uploadprofile" || $action=="uploadtaskphoto"){
			   	  $continue=false;
			   }
			   if($continue){
			   	   $key=getOptionA('driver_api_hash_key');			   	   
				   if(trim($key)!=trim($this->data['api_key'])){
				   	 $this->msg=$this->t("api hash key is not valid");
			         $this->output();
			         Yii::app()->end();
				   }
			   }			
			}
		}		
		return true;
	}	
	
	public function actionIndex(){
		echo 'Api is working';
	}		
	
	private function q($data='')
	{
		return Yii::app()->db->quoteValue($data);
	}
	
	private function t($message='')
	{
		//return Yii::t("default",$message);
		return Yii::t("driver",$message);
	}
		
    private function output()
    {
	   $resp=array(
	     'code'=>$this->code,
	     'msg'=>$this->msg,
	     'details'=>$this->details,
	     'request'=>json_encode($this->data)		  
	   );		   
	   if (isset($this->data['debug'])){
	   	   dump($resp);
	   }
	   
	   if (!isset($_GET['callback'])){
  	   	   $_GET['callback']='';
	   }    
	   
	   if (isset($_GET['json']) && $_GET['json']==TRUE){
	   	   echo CJSON::encode($resp);
	   } else echo $_GET['callback'] . '('.CJSON::encode($resp).')';		    	   	   	  
	   Yii::app()->end();
    }		
    
    public function actionLogin()
    {
    	if(!empty($this->data['username']) && !empty($this->data['password'])){
	    	if ( $res=Driver::driverAppLoginNew($this->data['username'],$this->data['password'])){	
	    		
	    		if($res['status']!="active"){
	    			switch ($res['status']) {
	    				case "pending":
	    					$this->msg=self::t("Login failed. your account is still"." ".t($res['status'])).".";
	    					break;	    			        
	    					
	    				case "suspended":
	    				case "blocked":	
	    				case "expired":	
	    				case "denied":	
	    					$this->msg=self::t("Login failed. your account is"." ".t($res['status'])).".";
	    					break;	    			        
	    						
	    				default:
	    					$this->msg=self::t("Login failed. either username or password is incorrect").".";
	    					break;
	    			}
	    			$this->output();
	    			Yii::app()->end();
	    		}
	    		
	    		$token=md5(Driver::generateRandomNumber(5) . $this->data['username']);
	    		$params=array(
	    		  'last_login'=>date('c'),
	    		  'last_online'=>strtotime("now"),
	    		  'ip_address'=>$_SERVER['REMOTE_ADDR'],
	    		  'token'=>$token,
	    		  'device_id'=>isset($this->data['device_id'])?$this->data['device_id']:'',
	    		  'device_platform'=>isset($this->data['device_platform'])?$this->data['device_platform']:'Android'
	    		);	    		
	    		if(!empty($res['token'])){
	    			unset($params['token']);
	    			$token=$res['token'];
	    		}
	    		$db=new DbExt;
	    		if ( $db->updateData("{{driver}}",$params,'driver_id',$res['driver_id'])){	    			
	    			$this->code=1;
	    			$this->msg=self::t("Login Successful");
	    			
	    			//get location accuracy
	    			$location_accuracy=2;
	    			if ( $team=Driver::getTeam($res['team_id'])){
	    				if($team['location_accuracy']=="high"){
	    					$location_accuracy=1;
	    				}
	    			}
	    			
	    			$this->details=array(
	    			  'username'=>$this->data['username'],
	    			  'password'=>$this->data['password'],
	    			  'remember'=>isset($this->data['remember'])?$this->data['remember']:'',
	    			  'todays_date'=>Yii::app()->functions->translateDate(date("M, d")),
	    			  'todays_date_raw'=>date("Y-m-d"),
	    			  'on_duty'=>$res['on_duty'],
	    			  'token'=>$token,
	    			  'duty_status'=>$res['on_duty'],
	    			  'location_accuracy'=>$location_accuracy
	    			);
	    		} else $this->msg=self::t("Login failed. please try again later");
	    	} else $this->msg=self::t("Login failed. either username or password is incorrect");
    	} else $this->msg=self::t("Please fill in your username and password");
    	$this->output();
    }
    
    public function actionForgotPassword()
    {
    	if (empty($this->data['email'])){
    		$this->msg=self::t("Email address is required");
    		$this->output();
    		Yii::app()->end();
    	}
    	$db=new DbExt;    	
    	if ( $res=Driver::driverForgotPassword($this->data['email'])){
    		$driver_id=$res['driver_id'];    		
    		$code=Driver::generateRandomNumber(5);
    		$params=array('forgot_pass_code'=>$code);
    		if($db->updateData('{{driver}}',$params,'driver_id',$driver_id)){
    			$this->code=1;
    			$this->msg=self::t("We have send the a password change code to your email");
    			
    			$tpl=EmailTemplate::forgotPasswordRequest();
    			$tpl=smarty('first_name',$res['first_name'],$tpl);
    			$tpl=smarty('code',$code,$tpl);
    			$subject=t('Forgot Password');
    			
    			//dump($tpl); dump($subject);
    			if ( sendEmail($res['email'],'',$subject,$tpl)){
    				$this->details="send email ok";
    			} else $this->msg="send email failed";
    			
    		} else $this->msg=self::t("Something went wrong please try again later");
    	} else $this->msg=self::t("Email address not found");
    	$this->output();
    }
    
    public function actionChangePassword()
    {    	
    	$Validator=new Validator;
    	$req=array(
    	  'email_address'=>self::t("Email address is required"),
    	  'code'=>self::t("Code is required"),
    	  'newpass'=>self::t("New Password is required")
    	);
    	$Validator->required($req,$this->data);
    	if ( $Validator->validate()){
    		if ( $res=Driver::driverForgotPassword($this->data['email_address'])){    			
    			if ( $res['forgot_pass_code']==$this->data['code']){
    				$params=array( 
    				  'password'=>md5($this->data['newpass']),
    				  'date_modified'=>date('c'),
    				  'forgot_pass_code'=>Driver::generateRandomNumber(5)
    				 );
    				$db=new DbExt;    				
    				if ( $db->updateData("{{driver}}",$params,'driver_id',$res['driver_id'])){
    				    $this->code=1;
    				    $this->msg=self::t("Password successfully changed");
    				} else $this->msg=self::t("Something went wrong please try again later");    				
    			} else $this->msg=self::t("Invalid password code");
    		} else $this->msg=self::t("Email address not found");
    	} else $this->msg=Driver::parseValidatorError($Validator->getError());		
    	$this->output();
    }
    
    public function actionChangeDutyStatus()
    {    	
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("Token not valid");
    		$this->output();
    		Yii::app()->end();
    	} 
    	$driver_id=$token['driver_id'];
    	$params=array(
    	  'on_duty'=>isset($this->data['onduty'])?$this->data['onduty']:2,
    	  'last_online'=>strtotime("now")
    	);
    	if ( $this->data['onduty']==2){
    		//$params['last_online']=time() - 300;
    		$tracking_type=getOptionA("driver_tracking_options");
    		 if ($tracking_type==2){
    	        $params['last_online']=strtotime("-35 minutes");
    	    } else $params['last_online']=strtotime("-20 minutes");
    	}
    	$db=new DbExt;
    	if ( $db->updateData('{{driver}}',$params,'driver_id',$driver_id)){
    		$this->code=1;
    		$this->msg="OK";
    		$this->details=$this->data['onduty'];
    	} else $this->msg=self::t("Something went wrong please try again later");   
    	$this->output();
    }
    
    public function actionGetTaskByDate()
    {    	
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("Token not valid");
    		$this->output();
    		Yii::app()->end();
    	}     	
    	
    	//dump($this->data);
    	$driver_id=$token['driver_id'];    	
    	if (isset($this->data['onduty'])){
    		if ($this->data['onduty']==1){
    	        Driver::updateLastOnline($driver_id);
    		}
    	}
    	    	    	
    	//if ( $res=Driver::getTaskByDriverID($driver_id,$this->data['date'])){
    	if ( $res=Driver::getTaskByDriverIDWithAssigment($driver_id,$this->data['date'])){
    		$this->code=1;
    		$this->msg="OK";
    		$data=''; $total_order=0;
    		foreach ($res as $val) {
    			if($val['order_id']>0){
    				if($orderinfo=Driver::getOrderTotalAmount($val['order_id'])){    					
    					$val['order_total_amount']=Driver::prettyPrice($orderinfo['total_w_tax']);
    					$total_order+=$orderinfo['total_w_tax'];
    				}
    			}
    			$val['delivery_time']=Yii::app()->functions->timeFormat($val['delivery_date'],true);
    			$val['status_raw']=$val['status'];
    			$val['status']=self::t($val['status']);    		
    			$val['trans_type_raw']=$val['trans_type'];	
    			$val['trans_type']=self::t($val['trans_type']);    			
    			$val['merchant_name']=stripslashes($val['merchant_name']);
    			$val['merchant_address']=stripslashes($val['merchant_address']);
    			//$val['total_order']=Driver::prettyPrice($total_order);
    			$data[]=$val;
    		}
    		
    		$args=array(
    		  'currency'=>Yii::app()->functions->getCurrencyCode()
    		);
    		$_total_order=Yii::t("default","Total currency today",$args);
    		
    		$this->details=$data;
    		$this->msg=Driver::prettyPrice($total_order);
    	} else $this->msg=self::t("No task for the day");
    	$this->output();
    }
    
    public function actionviewTaskDescription()
    {
    	$this->actionTaskDetails();
    }
    public function actionTaskDetails()
    {    	
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("Token not valid");
    		$this->output();
    		Yii::app()->end();
    	}   
    	if (isset($this->data['task_id'])){
    		if ( $res=Driver::getTaskId($this->data['task_id']) ){
    			    	    			    			
    			//check task belong to current driver    			    			
    			if ( $res['status']!="unassigned"){
	    			$driver_id=$token['driver_id'];
	    			if ($driver_id!=$res['driver_id']){
	    				$this->msg=Driver::t("Sorry but this task is already been assigned to others");
	    				$this->output();
	    				Yii::app()->end();
	    			}    			
    			}
    			
    			$this->code=1;
    			$this->msg=self::t("Task").":".$this->data['task_id'];
    			
    			$res['delivery_time']=Yii::app()->functions->timeFormat($res['delivery_date'],true);
    			$res['status_raw']=$res['status'];
    			$res['status']=self::t($res['status']);    		
    			$res['trans_type_raw']=$res['trans_type'];	
    			$res['trans_type']=self::t($res['trans_type']);  
    			$res['merchant_name']=stripslashes($res['merchant_name']);
    			$res['merchant_address']=stripslashes($res['merchant_address']);
    			
    			$res['history']=Driver::getDriverTaskHistory($this->data['task_id']);
    			
    			if($res['merchant_id']>=1){    				
    				$res['merchant_lat']=getOption($res['merchant_id'],'merchant_latitude');
    				$res['merchant_lng']=getOption($res['merchant_id'],'merchant_longtitude');
    			}
    			
    			/*get signature if any*/
    			$res['customer_signature_url']='';
    			if (!empty($res['customer_signature'])){
    				$res['customer_signature_url']=Driver::uploadURL()."/".$res['customer_signature'];
    				if (!file_exists(Driver::uploadPath()."/".$res['customer_signature'])){
    					$res['customer_signature_url']='';
    				}
    			}
    			
    			
    			$res['map_icons']=array(
    			  'driver'=>websiteUrl()."/protected/modules/driver/assets/images/car.png",
				  'customer'=>websiteUrl()."/protected/modules/driver/assets/images/racing-flag.png",
				  'merchant'=>websiteUrl()."/protected/modules/driver/assets/images/restaurant-pin-32.png",
    			);
    			
    			$res['driver_enabled_notes']=getOptionA('driver_enabled_notes');
    			$res['driver_enabled_signature']=getOptionA('driver_enabled_signature');
    			$res['driver_enabled_addphoto']=getOptionA('driver_enabled_addphoto');
    			
    			if ( $task_photo=Driver::taskPhotoCount($this->data['task_id'])){
    				$res['task_photo']=$task_photo;
    			} else $res['task_photo']=2;
    			
    			if ( $total_notes=Driver::getTotalNotes($this->data['task_id'])){
    			    $res['history_notes']=$total_notes;
    			} else $res['history_notes']=2;
    			
    			
    			$enabled_resize_photo=getOptionA('driver_enabled_resize_photo');
    			$photo_resize_width=getOptionA('photo_resize_width');
    			$photo_resize_height=getOptionA('photo_resize_height');
    			
    			if ($enabled_resize_photo==1){
    				if ($photo_resize_width<=0){
    					$enabled_resize_photo=2;
    				}
    				if ($photo_resize_height<=0){
    					$enabled_resize_photo=2;
    				}
    			}
    			
    			$res['enabled_resize_photo']=$enabled_resize_photo;
    			$res['photo_resize_width']=$photo_resize_width;
    			$res['photo_resize_height']=$photo_resize_height;
    			    						
    			$this->details=$res;
    		} else $this->msg=self::t("Task not found");
    	} else $this->msg=self::t("Task id is missing");
    	$this->output();
    }
	
    public function actionChangeTaskStatus()
    {
    	
    	/*if(isset($_GET['debug'])){
    	   dump($this->data);
    	}*/
    	
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("Token not valid");
    		$this->output();
    		Yii::app()->end();
    	}     	
    	$driver_id=$token['driver_id'];    	
    	$team_id=$token['team_id'];    	
    	$driver_name=$token['first_name'] ." " .$token['last_name'];    	
    	
    	$db=new DbExt;	
    	
    	if (isset($this->data['status_raw']) && isset($this->data['task_id'])){
    		
    		$task_id=$this->data['task_id'];
    		$task_info=Driver::getTaskId($task_id);
    		if(!$task_info){
    			$this->msg=self::t("Task not found");
    			$this->output();
    			Yii::app()->end();
    		}    		
    		    		    		
    		if($this->data['status_raw']=="successful"){    			
    			$driver_mandatory_signature=getOptionA('driver_mandatory_signature');
    			if ($driver_mandatory_signature==1){
	    			if ($task_info['customer_signature']==""){    				
	    				$this->msg=self::t("Customer signature is required");
	    		        $this->output(); 
	    		         Yii::app()->end();
	    			}
    			}
    		}
    		    		
    		$params_history='';    		
    		$params_history['ip_address']=$_SERVER['REMOTE_ADDR'];
    	    $params_history['date_created']=date('c');
    	    $params_history['task_id']=$task_id;    	    
    	    $params_history['driver_id']=$driver_id;     	    
    	    $params_history['driver_location_lat']=isset($token['location_lat'])?$token['location_lat']:'';
    	    $params_history['driver_location_lng']=isset($token['location_lng'])?$token['location_lng']:'';
    	    
    		if($task_info['order_id']>0){
    		   $params_history['order_id']=$task_info['order_id'];
    		}
    				
    		
    		switch ($this->data['status_raw']) {
    			
    			case "failed":
    			case "cancelled":    	
    			   $params=array('status'=>$this->data['status_raw']);    				
    				// update task id
    				$db->updateData("{{driver_task}}",$params,'task_id',$task_id);
    				
    				$remarks=Driver::driverStatusPretty($driver_name,$this->data['status_raw']);    				
    				$params_history['status']=$this->data['status_raw'];
    				$params_history['remarks']=$remarks; 			    				
    				$params_history['reason']=isset($this->data['reason'])?$this->data['reason']:'' ; 
    				
    				$params_history['remarks2']=Driver::driverStatusPretty2($driver_name,$this->data['status_raw']);
    				$args=array(
    				  '{driver_name}'=>$driver_name
    				);
    				$params_history['remarks_args']=json_encode($args);
    				
    				// insert history    				
    				$db->insertData("{{order_history}}",$params_history);
    				
    				$this->code=1;
    				$this->msg="OK";
    				$this->details=array(
    				  'task_id'=>$this->data['task_id'],
    				  'status_raw'=>$params['status'],
    				  'reload_functions'=>'getTodayTask'
    				);    			
    				
    				//update the order status
    				if($task_info['order_id']>0){
    					Driver::updateOrderStatus($task_info['order_id'],$this->data['status_raw']);
    				}
    				
    				//send notification to customer
    				if ( $task_info['trans_type']=="delivery"){    					
    				    Driver::sendNotificationCustomer('DELIVERY_FAILED',$task_info);
    				} else {
    					Driver::sendNotificationCustomer('PICKUP_FAILED',$task_info);
    				}
    							
    				break;
    				
    			case "declined":
    				
    				if ( $assigment_info=Driver::getAssignmentByDriverTaskID($driver_id,$task_id)){
    					
    					$stmt_assign="UPDATE 
    					{{driver_assignment}}
    					SET task_status='declined',
    					date_process=".Driver::q(date('c')).",
    					ip_address=".Driver::q($_SERVER['REMOTE_ADDR'])."
    					WHERE
    					task_id=".Driver::q($task_id)."
    					AND
    					driver_id=".Driver::q($driver_id)."
    					";
    					//dump($stmt_assign);
    					$db->qry($stmt_assign);
    					
    					$this->code=1;
	    				$this->msg="OK";
	    				$this->details=array(
	    				  'task_id'=>$this->data['task_id'],
	    				  'status_raw'=>'declined',
	    				  'reload_functions'=>'getTodayTask'
	    				);    				
    					
    				} else {    				
    					
	    				$params=array('status'=>"declined");    				    				
	    				$db->updateData("{{driver_task}}",$params,'task_id',$task_id);
	    				
	    				$remarks=Driver::driverStatusPretty($driver_name,'declined');    				
	    				$params_history['status']='declined';
	    				$params_history['remarks']=$remarks;    				    				
	    				
	    				$params_history['remarks2']=Driver::driverStatusPretty2($driver_name,'declined');
	    				$args=array(
	    				  '{driver_name}'=>$driver_name
	    				);
	    				$params_history['remarks_args']=json_encode($args);
    				
	    				// insert history    				
	    				$db->insertData("{{order_history}}",$params_history);
	    				
	    				//update the order status
	    				if($task_info['order_id']>0){
	    					Driver::updateOrderStatus($task_info['order_id'],$this->data['status_raw']);
	    				}
	    				
	    				$this->code=1;
	    				$this->msg="OK";
	    				$this->details=array(
	    				  'task_id'=>$this->data['task_id'],
	    				  'status_raw'=>$params['status'],
	    				  'reload_functions'=>'getTodayTask'
	    				);    				
    				}
    				
    				//send email to admin or merchant
    				
    				break;
    				
    				
    			case "acknowledged":    	

    			    // double check if someone has already the accept task   			    
    			    if($task_info['status']!="unassigned"){        			    	
    			    	if ( $task_info['driver_id']!=$driver_id){			    	
    			           $this->msg=Driver::t("Sorry but this task is already been assigned to others");
    			           $this->output();
    			    	   Yii::app()->end();
    			    	}
    			    }
    			    
    				$params=array(
    				  'driver_id'=>$driver_id,
    				  'status'=>"acknowledged",
    				  'team_id'=>$team_id
    				);    				
    				// update task id    				
    				$db->updateData("{{driver_task}}",$params,'task_id',$task_id);
    				
    				$remarks=Driver::driverStatusPretty($driver_name,'acknowledged');
    				$params_history['status']='acknowledged';
    				$params_history['remarks']=$remarks;    
    					
    				$params_history['remarks2']=Driver::driverStatusPretty2($driver_name,'acknowledged');
    				$args=array(
    				  '{driver_name}'=>$driver_name
    				);
    				$params_history['remarks_args']=json_encode($args);
    				
    				// insert history     				
    				$db->insertData("{{order_history}}",$params_history);
    				
    				//update the order status
    				if($task_info['order_id']>0){
    					Driver::updateOrderStatus($task_info['order_id'],$this->data['status_raw']);
    				}
    				
    				$this->code=1;
    				$this->msg="OK";
    				$this->details=array(
    				  'task_id'=>$this->data['task_id'],
    				  'status_raw'=>$params['status'],
    				  'reload_functions'=>'TaskDetails'
    				);    			
    				
    				//update driver_assignment
    				$stmt_assign="UPDATE
    				{{driver_assignment}}
    				SET task_status='acknowledged'
    				WHERE task_id=".Driver::q($task_id)."
    				";
    				$db->qry($stmt_assign);
    				
    				//send notification to customer
    				if ( $task_info['trans_type']=="delivery"){  
    				   Driver::sendNotificationCustomer('DELIVERY_REQUEST_RECEIVED',$task_info);
    				} else {
    				   Driver::sendNotificationCustomer('PICKUP_REQUEST_RECEIVED',$task_info);
    				}
    				
    				break;
    				
    			case "started":	
    			    $params=array('status'=>"started");
    			    $db->updateData("{{driver_task}}",$params,'task_id',$task_id);
    				// update task id
    				
    				$remarks=Driver::driverStatusPretty($driver_name,'started');   
    				$params_history['status']='started';
    				$params_history['remarks']=$remarks;    				
    				
    				$params_history['remarks2']=Driver::driverStatusPretty2($driver_name,'started');
    				$args=array(
    				  '{driver_name}'=>$driver_name
    				);
    				$params_history['remarks_args']=json_encode($args);
	    				
    				// insert history
    				$db->insertData("{{order_history}}",$params_history);
    				
    				//update the order status
    				if($task_info['order_id']>0){
    					Driver::updateOrderStatus($task_info['order_id'],$this->data['status_raw']);
    				}
    				
    				$this->code=1;
    				$this->msg="OK";
    				$this->details=array(
    				  'task_id'=>$this->data['task_id'],
    				  'status_raw'=>$params['status'],
    				  'reload_functions'=>'TaskDetails'
    				);    		
    				
    				//send notification to customer
    				if ( $task_info['trans_type']=="delivery"){  
    				    Driver::sendNotificationCustomer('DELIVERY_DRIVER_STARTED',$task_info);
    				} else {
    					Driver::sendNotificationCustomer('PICKUP_DRIVER_STARTED',$task_info);
    				}
    						
    				break;    			   
    		
    			case "inprogress":
    				 $params=array('status'=>"inprogress");
    				 $db->updateData("{{driver_task}}",$params,'task_id',$task_id);
    				// update task id
    				
    				$remarks=Driver::driverStatusPretty($driver_name,'inprogress');    				
    				$params_history['status']='inprogress';
    				$params_history['remarks']=$remarks;    				
    				
    				$params_history['remarks2']=Driver::driverStatusPretty2($driver_name,'inprogress');
    				$args=array(
    				  '{driver_name}'=>$driver_name
    				);
    				$params_history['remarks_args']=json_encode($args);
    				
    				// insert history
    				$db->insertData("{{order_history}}",$params_history);
    				
    				//update the order status
    				if($task_info['order_id']>0){
    					Driver::updateOrderStatus($task_info['order_id'],$this->data['status_raw']);
    				}
    				
    				$this->code=1;
    				$this->msg="OK";
    				$this->details=array(
    				  'task_id'=>$this->data['task_id'],
    				  'status_raw'=>$params['status'],
    				  'reload_functions'=>'TaskDetails'
    				);    			
    				
    				//send notification to customer
    				if ( $task_info['trans_type']=="delivery"){  
    				   Driver::sendNotificationCustomer('DELIVERY_DRIVER_ARRIVED',$task_info);
    				} else {
    				   Driver::sendNotificationCustomer('PICKUP_DRIVER_ARRIVED',$task_info);
    				}
    				
    				break;
    				
    			case "successful":	    			   
    			    $params=array('status'=>"successful");
    			    $db->updateData("{{driver_task}}",$params,'task_id',$task_id);
    				// update task id
    				
    				$remarks=Driver::driverStatusPretty($driver_name,'successful');    				
    				$params_history['status']='successful';
    				$params_history['remarks']=$remarks;    	
    				
    				$params_history['remarks2']=Driver::driverStatusPretty2($driver_name,'successful');
    				$args=array(
    				  '{driver_name}'=>$driver_name
    				);
    				$params_history['remarks_args']=json_encode($args);
    							
    				// insert history
    				$db->insertData("{{order_history}}",$params_history);
    				
    				
    				//update the order status
    				if($task_info['order_id']>0){
    					Driver::updateOrderStatus($task_info['order_id'],$this->data['status_raw']);
    				}
    				
    				$this->code=1;
    				$this->msg="OK";
    				$this->details=array(
    				  'task_id'=>$this->data['task_id'],
    				  'status_raw'=>$params['status'],
    				  'reload_functions'=>'getTodayTask'
    				);    			
    				
    				//send notification to customer
    				if ( $task_info['trans_type']=="delivery"){  
    				    Driver::sendNotificationCustomer('DELIVERY_SUCCESSFUL',$task_info);
    				} else {
    					Driver::sendNotificationCustomer('PICKUP_SUCCESSFUL',$task_info);
    				}
    				
    				break;
    				   
    			default:
    				$this->msg=self::t("Missing status");
    				break;
    		}
    	} else $this->msg=self::t("Missing parameters");
    	
    	$this->output();
    }
    
    public function actionAddSignatureToTask()
    {
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("Token not valid");
    		$this->output();
    		Yii::app()->end();
    	}     	
    	$driver_id=$token['driver_id'];    
    	    	
    	if ( isset($this->data['image'])){
    		    		
    		if ($this->data['image']=="image/jsignature;base30,"){
    			$this->msg=self::t("Signature is required");
    			$this->output();
    		    Yii::app()->end();
    		}
    		
	    	$path_to_upload=Yii::getPathOfAlias('webroot')."/upload";      	
	    	if (!file_exists($path_to_upload)){
	    		if (!@mkdir($path_to_upload,0777)){           	    
	    			$this->msg=self::t("Failed cannot create folder"." ".$path_to_upload);
           	        Yii::app()->end();
                }		    
	    	}
	    	
	    	$filename="signature_".$this->data['task_id'] . "-" . Driver::generateRandomNumber(10) .".png";
	    		    	
	    	$img = $this->data['image'];	   	    	
	    	Driver::base30_to_jpeg($img, $path_to_upload."/$filename");	    	
	        	        
	        $params=array(
	          'customer_signature'=>$filename,
	          'date_modified'=>date('c'),
	          'recipient_name'=>isset($this->data['recipient_name'])?$this->data['recipient_name']:'',
	          'ip_address'=>$_SERVER['REMOTE_ADDR']
	        );
	        
	        $task_id=$this->data['task_id'];	  
	        $driver_name=$token['first_name'] ." " .$token['last_name'];         

	        $db=new DbExt;		        
	        
	        $task_id=$this->data['task_id'];
    		$task_info=Driver::getTaskId($task_id);
    		if(!$task_info){
    			$this->msg=self::t("Task not found");
    			$this->output();
    			Yii::app()->end();
    		}    		
	        
	        if ( $db->updateData("{{driver_task}}",$params,'task_id',$task_id)){
		        $this->code=1;
		        $this->msg="Successful";      
		        $this->details=$this->data['task_id'];	
		        
		        $remarks=Driver::driverStatusPretty($driver_name,'sign'); 
		        
		        $args=array(
		    	  '{driver_name}'=>$driver_name
		    	);
		         
		        $params_history=array(
		           'status'=>'sign',
		           'remarks'=>$remarks,
		           'date_created'=>date('c'),
		           'ip_address'=>$_SERVER['REMOTE_ADDR'],
		           'task_id'=>$task_id,
		           'customer_signature'=>$filename ,
		           'order_id'=>isset($task_info['order_id'])?$task_info['order_id']:'',
		           'driver_id'=>$driver_id,
		           'driver_location_lat'=>isset($token['location_lat'])?$token['location_lat']:'',
		           'driver_location_lng'=>isset($token['location_lng'])?$token['location_lng']:'',
		            'receive_by'=>isset($this->data['recipient_name'])?$this->data['recipient_name']:'',
		           'signature_base30'=>$this->data['image'],
		           'remarks2'=>"{driver_name} added a signature",
		           'remarks_args'=>json_encode($args),
		        );
		        
                //$db->insertData("{{order_history}}",$params_history);
                if ( $this->data['signature_id']>0){
		        	$db->updateData("{{order_history}}",$params_history,'id',$this->data['signature_id']);
		        } else $db->insertData("{{order_history}}",$params_history);     
		        	       
	        } else $this->msg=self::t("Something went wrong please try again later");
	        
    	} else $this->msg=self::t("Signature is required");
    	$this->output();     
    }
    
    public function actionCalendarTask()
    {    	
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("Token not valid");
    		$this->output();
    		Yii::app()->end();
    	}     	
    	$driver_id=$token['driver_id'];    	
    	
    	if (isset($this->data['start']) && isset($this->data['end'])){
    		$start=$this->data['start'] ." 00:00:00";
    		$end=$this->data['end'] ." 23:59:00";    		
    		$data='';
    		if ( $res=Driver::getDriverTaskCalendar($driver_id,$start,$end)){
    			//dump($res);
    			 foreach ($res as $val) {    			 	
    			 	$data[]=array(
    			 	  'title'=> Driver::getTotalTaskByDate($driver_id,$val['delivery_date']),
    			 	  'id'=>$val['delivery_date'],
    			 	  'year'=>date("Y",strtotime($val['delivery_date'])),
    			 	  'month'=>date("m",strtotime($val['delivery_date'] ." -1 months" )),
    			 	  'day'=>date("d",strtotime($val['delivery_date'])),
    			 	);
    			 }
    			 $this->code=1;
    			 $this->msg="OK";
    			 $this->details=$data;
    		}
    	} else $this->msg=self::t("Missing parameters");
    	
    	$this->output();     
    }
    
    public function actionGetProfile()
    {    	
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("Token not valid");
    		$this->output();
    		Yii::app()->end();
    	}     	
    	$driver_id=$token['driver_id'];    	
    	$info=Driver::driverInfo($driver_id);  

    	$profile_photo='';
    	if(!empty($info['profile_photo'])){
    		$profile_photo_path=Driver::driverUploadPath()."/".$info['profile_photo'];
    		if(file_exists($profile_photo_path)){
    			$profile_photo=websiteUrl()."/upload/driver/".$info['profile_photo'];
    		}
    	}
    	    	       
    	$this->code=1;
    	$this->msg="OK";
    	$this->details=array(
    	  'full_name'=>$info['first_name']." ".$info['last_name'],
    	  'team_name'=>$info['team_name'],
    	  'email'=>$info['email'],
    	  'phone'=>$info['phone'],
    	  'transport_type_id'=>$info['transport_type_id'],
    	  'transport_type_id2'=>self::t(ucwords($info['transport_type_id'])),
    	  'transport_description'=>$info['transport_description'],
    	  'licence_plate'=>$info['licence_plate'],
    	  'color'=>$info['color'],
    	  'profile_photo'=>$profile_photo
    	);
    	$this->output();     
    }
    
    public function actionGetTransport()
    {    	
    	$this->code=1;
    	$this->code=1;
    	$this->details=Driver::transportType();
    	$this->output();     
    }
    
    public function actionUpdateProfile()
    {    	
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("Token not valid");
    		$this->output();
    		Yii::app()->end();
    	}     	
    	$driver_id=$token['driver_id']; 
    	
    	$Validator=new Validator;
    	$req=array(
    	  'phone'=>self::t("Phone is required")    	  
    	);
    	$Validator->required($req,$this->data);
    	if ( $Validator->validate()){
    		$params=array(
    		  'phone'=>$this->data['phone'],
    		  'date_modified'=>date('c'),
    		  'ip_address'=>$_SERVER['REMOTE_ADDR']
    		);
    		$db=new DbExt;
    		if ( $db->updateData("{{driver}}",$params,'driver_id',$driver_id)){
    			$this->code=1;
    			$this->msg=self::t("Profile Successfully updated");
    		} else $this->msg=self::t("Something went wrong please try again later");
    	} else $this->msg=Driver::parseValidatorError($Validator->getError());
    	$this->output();     
    }
    
    public function actionUpdateVehicle()
    {    	
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("Token not valid");
    		$this->output();
    		Yii::app()->end();
    	}     	
    	$driver_id=$token['driver_id']; 
    	
    	$Validator=new Validator;
    	$req=array(
    	  'transport_type_id'=>self::t("Transport Type is required"),
    	  'transport_description'=>self::t("Description is required"),
    	  /*'licence_plate'=>self::t("License Plate is required"),
    	  'color'=>self::t("Color is required"),*/
    	);
    	if ( $this->data['transport_type_id']=="truck"){
    		unset($req);
    		$req=array(
    		  'transport_type_id'=>self::t("Transport Type is required")
    		);
    	}
    	$Validator->required($req,$this->data);
    	if ( $Validator->validate()){
    		$params=array(
    		  'transport_type_id'=>$this->data['transport_type_id'],
    		  'transport_description'=>$this->data['transport_description'],
    		  'licence_plate'=>isset($this->data['licence_plate'])?$this->data['licence_plate']:'',
    		  'color'=>isset($this->data['color'])?$this->data['color']:'',
    		  'date_modified'=>date('c'),
    		  'ip_address'=>$_SERVER['REMOTE_ADDR']
    		);
    		$db=new DbExt;
    		if ( $db->updateData("{{driver}}",$params,'driver_id',$driver_id)){
    			$this->code=1;
    			$this->msg=self::t("Vehicle Info updated");
    		} else $this->msg=self::t("Something went wrong please try again later");
    	} else $this->msg=Driver::parseValidatorError($Validator->getError());
    	$this->output();     
    }
    
    public function actionProfileChangePassword()
    {
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("Token not valid");
    		$this->output();
    		Yii::app()->end();
    	}     	
    	$driver_id=$token['driver_id']; 
    	
    	$Validator=new Validator;
    	$req=array(
    	  'current_pass'=>self::t("Current password is required"),
    	  'new_pass'=>self::t("New password is required"),
    	  'confirm_pass'=>self::t("Confirm password is required")    	  
    	);    	
    	if ( $this->data['new_pass']!=$this->data['confirm_pass']){
    		$Validator->msg[]=self::t("Confirm password does not macth with your new password");
    	}
    	
    	$Validator->required($req,$this->data);
    	if ( $Validator->validate()){
    		    		    		
    		if (!Driver::driverAppLogin($token['username'],$this->data['current_pass'])){
    			$this->msg=self::t("Current password is invalid");
    			$this->output();     
    			Yii::app()->end();
    		}    		
    		$params=array(
    		  'password'=>md5($this->data['new_pass']),
    		  'date_modified'=>date('c'),
    		  'ip_address'=>$_SERVER['REMOTE_ADDR']
    		);
    		$db=new DbExt;
    		if ( $db->updateData("{{driver}}",$params,'driver_id',$driver_id)){
    			$this->code=1;
    			$this->msg=self::t("Password Successfully Changed");
    			$this->details=$this->data['new_pass'];
    		} else $this->msg=self::t("Something went wrong please try again later");
    	} else $this->msg=Driver::parseValidatorError($Validator->getError());
    	$this->output();     
    }
    
    public function actionSettingPush()
    {
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("Token not valid");
    		$this->output();
    		Yii::app()->end();
    	}     	
    	$driver_id=$token['driver_id']; 
    	    	
    	$params=array(
    	  'enabled_push'=>$this->data['enabled_push'],
    	  'date_modified'=>date('c'),
    	  'ip_address'=>$_SERVER['REMOTE_ADDR']
    	);
    	$db=new DbExt;
		if ( $db->updateData("{{driver}}",$params,'driver_id',$driver_id)){
			$this->code=1;
			$this->msg=self::t("Setting Saved");			
		} else $this->msg=self::t("Something went wrong please try again later");
		$this->output();     
    }
    
    public function actionGetSettings()
    {
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("Token not valid");
    		$this->output();
    		Yii::app()->end();
    	}     	
    	$driver_id=$token['driver_id']; 
    	
    	$lang=Driver::availableLanguages();
    	
    	$resp=array(
    	  'enabled_push'=>$token['enabled_push'],
    	  'language'=>$lang
    	);
    	$this->code=1;
    	$this->msg="OK";
    	$this->details=$resp;
    	$this->output();     
    }
    
    public function actionLanguageList()
    {
    	/*if ($res=Yii::app()->functions->getLanguageList()){
			$set_lang_id=Yii::app()->functions->getOptionAdmin('set_lang_id');					
				$eng[]=array(
				  'lang_id'=>"en",
				  'country_code'=>"US",
				  'language_code'=>"English"
				);
				$res=array_merge($eng,$res);
			//}						
			$this->code=1;
			$this->msg="OK";
			$this->details=$res;
		} else $this->msg=Driver::t("no language available");*/
    	
    	$language_list=Driver::getLanguageList();
    	unset($language_list[0]);
    	//dump($language_list);
    	$this->code=1;
		$this->msg="OK";
		$this->details=$language_list;
    	
		$this->output();
    }
    
    public function actionGetAppSettings()
    {    	
    	/*$mobile_dictionary=getOptionA('driver_mobile_dictionary');
    	if(!empty($mobile_dictionary)){
    		$mobile_dictionary=json_decode($mobile_dictionary,true);
    	}*/    	
    	
    	$vibrate_interval=getOptionA('vibrate_interval');
    	if(!is_numeric($vibrate_interval)){
    		$vibrate_interval=3000;
    	}
    	
    	$translation=Driver::getMobileTranslation();  
    	
    	$app_track_interval=getOptionA('driver_track_interval');
		if (!is_numeric($app_track_interval)){
			$app_track_interval=8000;
		} else $app_track_interval=$app_track_interval*1000;
		
		if ($app_track_interval<=0){
			$app_track_interval=8000;
		}
		
		$app_default_language=getOptionA('app_default_language');				
		if ($app_default_language=="0"){			
			$app_default_language='';
		}
    	
    	$this->code=1;
    	$this->msg="OK";
    	$this->details=array(
    	  'notification_sound_url'=>Driver::moduleUrl()."/sound/food_song.mp3",
    	  'enabled_signup'=>getOptionA('driver_enabled_signup'),
    	  'vibrate_interval'=>$vibrate_interval,
    	  'admin_country_set'=>getOptionA('admin_country_set'),
    	  'record_track_Location'=>getOptionA('driver_record_track_Location'),
    	  'disabled_tracking_bg'=>getOptionA('driver_disabled_tracking_bg'),
    	  'track_interval'=>$app_track_interval,  
    	  'app_language'=>$app_default_language,
    	  'app_name'=>getOptionA('driver_app_name'),
    	  'translation'=>$translation
    	);
    	$this->output();
    }
    
    public function actionViewOrderDetails()
    {
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("Token not valid");
    		$this->output();
    		Yii::app()->end();
    	}     	
    	$driver_id=$token['driver_id']; 
    	$order_id= $this->data['order_id'];
    	
		$_GET['backend']='true';
		if ( $data=Yii::app()->functions->getOrder2($order_id)){	
			//dump($data);					
			$json_details=!empty($data['json_details'])?json_decode($data['json_details'],true):false;
			if ( $json_details !=false){
			    Yii::app()->functions->displayOrderHTML(array(
			       'merchant_id'=>$data['merchant_id'],
			       'order_id'=>$order_id,
			       'delivery_type'=>$data['trans_type'],
			       'delivery_charge'=>$data['delivery_charge'],
			       'packaging'=>$data['packaging'],
			       'cart_tip_value'=>$data['cart_tip_value'],
				   'cart_tip_percentage'=>$data['cart_tip_percentage']/100,
				   'card_fee'=>$data['card_fee'],
				   'donot_apply_tax_delivery'=>$data['donot_apply_tax_delivery'],
				   'points_discount'=>isset($data['points_discount'])?$data['points_discount']:'' /*POINTS PROGRAM*/
			     ),$json_details,true,$order_id);
			     $data2=Yii::app()->functions->details;
			     //unset($data2['html']);			     
			     $this->code=1;
			     $this->msg="OK";
			     
			     /*dump($data2['html']);
			     //dump($data2); 
			     die();*/
			     
			     $admin_decimal_separator=getOptionA('admin_decimal_separator');
		         $admin_decimal_place=getOptionA('admin_decimal_place');
		         $admin_currency_position=getOptionA('admin_currency_position');
		         $admin_thousand_separator=getOptionA('admin_thousand_separator');
			     
			     $data2['raw']['settings']=Driver::priceSettings();
			     $data2['raw']['order_info']=array(
			       'order_id'=>$data['order_id'],
			       'order_change'=>$data['order_change'],
			     );
			     
			     /*order info*/			     
			     $merchant_info=Yii::app()->functions->getMerchant($data['merchant_id']);
		 $full_merchant_address=$merchant_info['street']." ".$merchant_info['city']. " ".$merchant_info['state']." ".$merchant_info['post_code'];
		         $order_info[]=array(
		           'label'=>Driver::t("Customer Name"),
		           'value'=>$data['full_name']
		         );
		         $order_info[]=array(
		           'label'=>Driver::t("Merchant Name"),
		           'value'=>stripslashes($data['merchant_name'])
		         );
		         $order_info[]=array(
		           'label'=>Driver::t("Telephone"),
		           'value'=>$data['merchant_contact_phone']
		         );
		         $order_info[]=array(
		           'label'=>Driver::t("Address"),
		           'value'=>$full_merchant_address
		         );
		         $order_info[]=array(
		           'label'=>Driver::t("TRN Type"),
		           'value'=>Driver::t($data['trans_type'])
		         );
		         $order_info[]=array(
		           'label'=>Driver::t("Payment Type"),
		           'value'=>strtoupper(Driver::t($data['payment_type']))
		         );
		         if ( $data['payment_provider_name']){
		         	$order_info[]=array(
		             'label'=>Driver::t("Card#"),
		             'value'=>$data['payment_provider_name']
		           );
		         }
		         		         
		         if ( $data['payment_type'] =="pyp"){
		         	$paypal_info=Yii::app()->functions->getPaypalOrderPayment($data['order_id']);
		         	$order_info[]=array(
		             'label'=>Driver::t("Paypal Transaction ID"),
		             'value'=>isset($paypal_info['TRANSACTIONID'])?$paypal_info['TRANSACTIONID']:''
		           );
		         }
		         
		         $order_info[]=array(
		           'label'=>Driver::t("Reference #"),
		           'value'=>Yii::app()->functions->formatOrderNumber($data['order_id'])
		         );
		         
		         if ( !empty($data['payment_reference'])){
                    $order_info[]=array(
		              'label'=>Driver::t("Payment Ref"),
		              'value'=>$data['payment_reference']
		            );
                 }
                 
                 if ( $data['payment_type']=="ccr" || $data['payment_type']=="ocr"){
                 	 $order_info[]=array(
		              'label'=>Driver::t("Card#"),
		              'value'=>Yii::app()->functions->maskCardnumber($data['credit_card_number'])
		            );
                 }
                 
                 $trn_date=date('M d,Y G:i:s',strtotime($data['date_created']));
                 $order_info[]=array(
		            'label'=>Driver::t("TRN Date"),
		            'value'=>Yii::app()->functions->translateDate($trn_date)
		         );
		         
		         if (isset($data['delivery_date'])){
		         	if(!empty($data['delivery_date'])){		         		
		         		$delivery_date=prettyDate($data['delivery_date']);
                        $delivery_date=Yii::app()->functions->translateDate($delivery_date);                        
                         $order_info[]=array(
		                  'label'=>$data['trans_type']=="delivery"?Driver::t("Delivery Date"):Driver::t("Pickup Date"),
		                  'value'=>$delivery_date
		                 );
		         	}
		         }
		         
		         if (isset($data['delivery_time'])){
		         	if(!empty($data['delivery_time'])){
		         		$delivery_time=Yii::app()->functions->timeFormat($data['delivery_time'],true);
		         		$order_info[]=array(
		                  'label'=>$data['trans_type']=="delivery"?Driver::t("Delivery Time"):Driver::t("Pickup Time"),
		                  'value'=>$delivery_time
		                 );
		         	}
		         }
		         
		         if(isset($data['delivery_asap'])){
		         	if(!empty($data['delivery_asap'])){
		         		$order_info[]=array(
		                  'label'=>Driver::t("Deliver ASAP"),
		                  'value'=>$data['delivery_asap']==1?Driver::t("Yes"):""
		                );
		         	}
		         }
		         
		         if (!empty($data['client_full_address'])){
                    $delivery_address=$data['client_full_address'];
                 } else $delivery_address=$data['full_address'];	
                 
                 $order_info[]=array(
		            'label'=>Driver::t("Deliver to"),
		            'value'=>$delivery_address
		         ); 
		         
		         if(!empty($data['delivery_instruction'])){
		           $order_info[]=array(
		              'label'=>Driver::t("Delivery Instruction"),
		              'value'=>$data['delivery_instruction']
		           ); 
		         }
		         
		         if (!empty($data['location_name1'])){
                     $location_name=$data['location_name1'];
                 } else $location_name=$data['location_name'];

                 $order_info[]=array(
		            'label'=>Driver::t("Location Name"),
		            'value'=>$location_name
		         ); 
		         
		         $order_info[]=array(
		            'label'=>Driver::t("Contact Number"),
		            'value'=>!empty($data['contact_phone1'])?$data['contact_phone1']:$data['contact_phone']
		         ); 
		         
		         if($data['order_change']>0.1){
		         	$order_info[]=array(
		               'label'=>Driver::t("Change"),
		               'value'=>displayPrice( baseCurrency(), normalPrettyPrice($data['order_change']))
		            ); 
		         }
		         		         
		         //dump($order_info);
		         $this->msg=$order_info;	
		         $data2['raw']['html']=$data2['html'];
			     $this->details=$data2['raw'];			     
			     //$this->details=$data2['html'];			     
			     
			} else $this->msg = self::t("Record not found");
		} else $this->msg = self::t("Record not found");    	
    	$this->output();
    }
    
    public function actionGetNotifications()
    {
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("Token not valid");
    		$this->output();
    		Yii::app()->end();
    	}     	
    	$driver_id=$token['driver_id'];
    	if ( $res=Driver::getDriverNotifications($driver_id)) {
    		 $data='';
    		 foreach ($res as $val) {
    		 	$val['date_created']=Driver::prettyDate($val['date_created']);
    		 	//$val['date_created']=date("h:i:s",strtotime($val['date_created']));
    		 	$val['push_title']=Driver::t($val['push_title']);    		 	
    		 	$data[]=$val;
    		 }
    		 $this->code=1;
    		 $this->msg="OK";
    		 $this->details=$data;
    	} else $this->msg=self::t("No notifications");
    	$this->output();
    }
    
    public function actionUpdateDriverLocation()
    {    	
    	
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("Token not valid");
    		$this->output();
    		Yii::app()->end();
    	}     	
    	$driver_id=$token['driver_id'];
    	$params=array(
    	  'location_lat'=>$this->data['lat'],
    	  'location_lng'=>$this->data['lng'],
    	  'last_login'=>date('c'),
	      'last_online'=>strtotime("now"),
	      'app_version'=>isset($this->data['app_version'])?$this->data['app_version']:''
    	);
    	
    	if ( $token['on_duty']==2){
    	    unset($params['last_online']);
    	}   
    	
    	$driver_id=$token['driver_id'];    	
    	
    	$db=new DbExt;
    	if ( $db->updateData("{{driver}}",$params,'driver_id',$driver_id)){
    		$this->code=1;
    		$this->msg="Location set";
    		
    		/*log driver location*/    		
    		$is_record=getOption($token['user_id'],'driver_record_track_Location');
    		if ($is_record==1){
	    		$logs=array(	    		  
	    		  'user_type'=>$token['user_type'],
	    		  'user_id'=>$token['user_id'],
	    		  'driver_id'=>$driver_id,
	    		  'latitude'=>$this->data['lat'],
	    	      'longitude'=>$this->data['lng'],	    	      
	    	      'altitude'=>isset($this->data['altitude'])?$this->data['altitude']:'',
	    	      'accuracy'=>isset($this->data['accuracy'])?$this->data['accuracy']:'',
	    	      'altitudeAccuracy'=>isset($this->data['altitudeAccuracy'])?$this->data['altitudeAccuracy']:'',
	    	      'heading'=>isset($this->data['heading'])?$this->data['heading']:'',
	    	      'speed'=>isset($this->data['speed'])?$this->data['speed']:'',
	    	      'track_type'=>isset($this->data['track_type'])?$this->data['track_type']:'',	    	      	      
	    	      'date_created'=>Driver::dateNow(),
	    	      'ip_address'=>$_SERVER['REMOTE_ADDR'],	    	      
	    		);
	    		$db->insertData("{{driver_track_location}}",$logs);
    		} 
    		
    	} else $this->msg="Failed";
    	$this->output();    	
    }
    
    public function actionClearNofications()
    {
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("Token not valid");
    		$this->output();
    		Yii::app()->end();
    	}     	
    	$driver_id=$token['driver_id'];
    	$stmt="UPDATE 
    	{{driver_pushlog}}
    	SET
    	is_read='1'
    	WHERE
    	driver_id=".self::q($driver_id)."
    	AND
    	is_read='2'
    	";
    	$this->code=1;
    	$this->msg="OK";
    	$db=new DbExt;
    	$db->qry($stmt);
    	$this->output(); 
    }
    
    public function actionDeviceConnected()
    {
    	if (!isset($this->data['token'])){
    		$this->data['token']='';
    	}
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg="token not found";
    		$this->output();
    		Yii::app()->end();
    	}     	
    	$driver_id=$token['driver_id'];
    	Driver::updateLastOnline($driver_id);
    	$this->code=1;
    	$this->msg="OK";
    	$this->output(); 
    }
    
    public function actionLogout()
    {
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("Token not valid");
    		$this->output();
    		Yii::app()->end();
    	} 
    	$driver_id=$token['driver_id'];
    	$params=array(    	  
    	  'last_online'=>time() - 300,
    	  'ip_address'=>$_SERVER['REMOTE_ADDR']
    	);
    	
    	$db=new DbExt;
    	$db->updateData('{{driver}}',$params,'driver_id',$driver_id);
    	$this->code=1;
    	$this->msg="OK";
    	$this->output();
    }
    
    public function actionaddNotes()
    {    	
    	if (!isset($this->data['token'])){
    		$this->data['token']='';
    	}
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("token not found");
    		$this->output();
    		Yii::app()->end();
    	}     	
    	$driver_id=$token['driver_id'];    	
    	if (empty($this->data['notes'])){
    		$this->msg=self::t("Notes is required");
    		$this->output();
    		Yii::app()->end();
    	}
    	
    	$task_id=isset($this->data['task_id'])?$this->data['task_id']:'';
    	
    	if ( !$task_info=Driver::getTaskId($task_id)){    		
    		$this->msg=self::t("Task not found");
    		$this->output();
    		Yii::app()->end();
    	}    	

    	$driver_name=$token['first_name'];
    	
    	$args=array(
    	  '{driver_name}'=>$driver_name
    	);
    	
    	$params=array(
    	  'status'=>"note",
    	  'remarks'=> $driver_name." ".self::t("added a note"),
    	  'date_created'=>Driver::dateNow(),
    	  'ip_address'=>$_SERVER['REMOTE_ADDR'],
    	  'task_id'=>$this->data['task_id'],
    	  'driver_id'=>$driver_id,
    	  'driver_location_lat'=>$token['location_lat'],
    	  'driver_location_lng'=>$token['location_lng'],
    	  'remarks2'=>"{driver_name} added a note",
    	  'remarks_args'=>json_encode($args),
    	  'notes'=>$this->data['notes'],
    	  'order_id'=>isset($task_info['order_id'])?$task_info['order_id']:''
    	);
    	
    	$db=new DbExt;
    	$db->insertData("{{order_history}}",$params);
    	unset($db);    	
    	$this->code=1;
    	$this->msg="OK";
    	$this->details=array(
    	  'task_id'=>$this->data['task_id']
    	);
    	      	
    	if ( $task_info['trans_type']=="delivery"){  	
    	    Driver::sendNotificationCustomer('DELIVERY_NOTES',$task_info);
    	} else Driver::sendNotificationCustomer('PICKUP_NOTES',$task_info);
    	
    	$this->output();
    }
    
    public function actionloadNotes()
    {
    	
    	if (!isset($this->data['token'])){
    		$this->data['token']='';
    	}
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=t("token not found");
    		$this->output();
    		Yii::app()->end();
    	}     	
    	
    	$driver_id=$token['driver_id'];    	    	
    	
    	if ($res=Driver::getNotes($this->data['task_id'])){ 
    		
    		$resp=Driver::getTaskId($this->data['task_id']);      		
    		$this->code=1; 
    		$this->msg= $resp['status'];
    		$data='';
    		foreach ($res as $val) {    			    			
    			$data[]=array(
    			  'id'=>$val['id'],
    			  'date_created'=>date("Y-m-d G:i:s",strtotime($val['date_created'])),     			  
    			  'notes'=>$val['notes']
    			);
    		}
    		$this->details=$data;
    	} else $this->msg=self::t("No result");
    	    	    
    	$this->output();
    }
    
    public function actiondeleteNotes()
    {
    	if (!isset($this->data['token'])){
    		$this->data['token']='';
    	}
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("token not found");
    		$this->output();
    		Yii::app()->end();
    	}     	    	
    	$driver_id=$token['driver_id'];  
    	
    	if(isset($this->data['id'])){
    		if ($res=Driver::getNotesByID($this->data['id'])){    		
	    		$DbExt=new DbExt;
	    		$stmt="DELETE FROM
	    		{{order_history}}
	    		WHERE
	    		id=".Driver::q($this->data['id'])."
	    		";
	    		$DbExt->qry($stmt);
	    		unset($DbExt);
	    		$this->code=1; $this->msg="OK";
	    		
	    		$this->details=array(
	    		  'task_id'=>$res['task_id']
	    		);
    		} else $this->msg=self::t("Notes not found");
    	} else $this->msg=self::t("ID is missing");
    	
    	$this->output();
    }
    
    public function actionUpdateNotes()
    {    	
    	if (!isset($this->data['token'])){
    		$this->data['token']='';
    	}
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("token not found");
    		$this->output();
    		Yii::app()->end();
    	}     	    	
    	$driver_id=$token['driver_id'];  
    	$driver_name=$token['first_name'] ." " .$token['last_name'];    	
    	
        $args=array(
    	  '{driver_name}'=>$driver_name
    	);
    	
    	$params=array(
    	   'remarks'=> $driver_name." ".self::t("updated a note"),
    	  'notes'=>$this->data['notes'],
    	  'date_created'=>Driver::dateNow(),
    	  'ip_address'=>$_SERVER['REMOTE_ADDR'],
    	  'remarks2'=>"{driver_name} updated a note",
    	  'remarks_args'=>json_encode($args),
    	);
    	    
    	$DbExt=new DbExt;
    	if ($res=Driver::getNotesByID($this->data['id'])){    		
	    	if ($DbExt->updateData("{{order_history}}",$params,'id',$this->data['id'])){
	    		$this->code=1;
	    		$this->msg="OK";
	    		$this->details=array(
	    		  'task_id'=>$res['task_id']
	    		);
	    	} else $this->msg=self::t("Error cannot update");
    	} else $this->msg=self::t("Notes not found");
    	
    	$this->output();
    }
    
    public function actionTrackDistance()
    {    	    	
    	if (!isset($this->data['token'])){
    		$this->data['token']='';
    	}
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("token not found");
    		$this->output();
    		Yii::app()->end();
    	}     	    	
    	$driver_id=$token['driver_id'];  
    	$trans_type=!empty($token['transport_type_id'])?$token['transport_type_id']:'car';    	
    	
    	//dump($trans_type);
    	//dump($this->data);
    	
    	$res='';
    	
        if ( $this->data['map_action']=="map1"){
        	        	

        	if ( $res['merchant_distance']=Driver::getTaskDistance(
	    	    $this->data['driver_lat'],
	    	    $this->data['driver_lng'],
	    	    $this->data['task_lat'],
	    	    $this->data['task_lng'],
	    	    $trans_type
	    	)){
	    		$this->code=1; $this->msg="OK";
	    	} else {
	    		$this->msg=self::t("Distance not available");
	    		$res['merchant_distance']=2;
	    	}
        } else {
	    	/*get distance between driver and merchant*/
	    	if ( $res['merchant_distance']=Driver::getTaskDistance(
	    	    $this->data['driver_lat'],
	    	    $this->data['driver_lng'],
	    	    $this->data['dropoff_lat'],
	    	    $this->data['dropoff_lng'],
	    	    $trans_type
	    	)){
	    		$this->code=1; $this->msg="OK";
	    	} else {
	    		$res['merchant_distance']=2;
	    		$this->msg=self::t("Distance not available");
	    	}
	    	
	    	/*get distance between merchant to customer address*/
	    	if ( $res['delivery_distance']=Driver::getTaskDistance(
	    	    $this->data['dropoff_lat'],
	    	    $this->data['dropoff_lng'],
	    	    $this->data['task_lat'],
	    	    $this->data['task_lng'],
	    	    $trans_type
	    	)){
	    		$this->code=1; $this->msg="OK";
	    	} else {
	    		$res['delivery_distance']=2;    	
	    		$this->msg=self::t("Distance not available");
	    	}
        }
    	
    	$res['map_action']=isset($this->data['map_action'])?$this->data['map_action']:'';
    	
    	$this->details=$res;
    	
    	$this->output();
    }
    
    public function actionsignup()
    {
    	
    	$Validator=new Validator;
    	$req=array(
    	  'first_name'=>self::t("First name is required"),
    	  'last_name'=>self::t("Last name is required"),
    	  'email'=>self::t("Email is required"),
    	  'phone'=>self::t("Mobile number is required"),
    	  'username'=>self::t("Username is required"),
    	  'password'=>self::t("Password is required"),
    	  'transport_type_id'=>self::t("Transport Type is required"),
    	);
    	    	
    	if ( Driver::getDriverByUsername($this->data['username'])){			
			$Validator->msg[]=self::t("Username already exist");
		}			
		if ( Driver::getDriverByEmail($this->data['email'])){			
			$Validator->msg[]=self::t("Email already exist");
		}			
		
		if (isset($this->data['phone'])){
			if ( strlen($this->data['phone']<10)){
				$Validator->msg[]=self::t("Mobile number is required");
			}
		}
    	
    	$Validator->required($req,$this->data);
    	if ( $Validator->validate()){
    		
    		$admin_id=Driver::getAdminID();
    		$status=getOptionA('driver_signup_status');
    		if(empty($status)){
    			$status='pending';
    		}
    		
    		$params=array(
    		  'first_name'=>$this->data['first_name'],
    		  'last_name'=>$this->data['last_name'],
    		  'email'=>$this->data['email'],
    		  'phone'=>$this->data['phone'],
    		  'username'=>$this->data['username'],
    		  'password'=>md5($this->data['password']),
    		  'transport_type_id'=>$this->data['transport_type_id'],
    		  'transport_description'=>isset($this->data['transport_description'])?$this->data['transport_description']:'',
              'licence_plate'=>isset($this->data['licence_plate'])?$this->data['licence_plate']:'',
              'color'=>isset($this->data['color'])?$this->data['color']:'',
              'date_created'=>Driver::dateNow(),
              'ip_address'=>$_SERVER['REMOTE_ADDR'],
              'status'=>$status,
              'user_type'=>"admin",
              'user_id'=>isset($admin_id['admin_id'])?$admin_id['admin_id']:'',
              'is_signup'=>1
    		);
    		    		    		
    		$db=new DbExt;
    		if ( $db->insertData("{{driver}}",$params)){
    			$this->code=1;
    			
    			if ( $status=="active"){
    			   $this->msg=self::t("Signup successful");
    			} else $this->msg=self::t("Your request has been receive please wait while we validate your application");
    			
    			/*send email to admin*/
	    		$driver_enabled_signup=getOptionA('driver_enabled_signup');
	    		if($driver_enabled_signup==1){
	    			$admin_email=getOptionA('driver_send_admin_notification_email');
	    			if(!empty($admin_email)){
	    				$tpl=EmailTemplate::newDriverSignup();
	    				$tpl=Driver::smarty('full_name',$this->data['first_name']." ".
	    				$this->data['last_name']
	    				,$tpl);
	    				$tpl=Driver::smarty('email',$this->data['email'],$tpl);
	    				$tpl=Driver::smarty('phone',$this->data['phone'],$tpl);
	    				$tpl=Driver::smarty('username',$this->data['username'],$tpl);
	    				$tpl=Driver::smarty('transport_type_id',$this->data['transport_type_id'],$tpl);    				
	    				Yii::app()->functions->sendEmail(
	    				  $admin_email,'',self::t("New driver Signup"),$tpl
	    				);
	    			}
	    		}
	    		
	    		/*send welcome email*/
	    		$DRIVER_NEW_SIGNUP_EMAIL=getOptionA('DRIVER_NEW_SIGNUP_EMAIL');
	    		$DRIVER_NEW_SIGNUP_EMAIL_TPL=getOptionA('DRIVER_NEW_SIGNUP_EMAIL_TPL');    		
	    		if ( $DRIVER_NEW_SIGNUP_EMAIL==1 && !empty($DRIVER_NEW_SIGNUP_EMAIL_TPL) ){
	    			$tpl=$DRIVER_NEW_SIGNUP_EMAIL_TPL;
	    			$company_name=Yii ::app()->functions->getOptionAdmin('website_title');  
	    			$tpl=Driver::smarty('DriverName',$this->data['first_name'],$tpl);
	    			$tpl=Driver::smarty('CompanyName',$company_name,$tpl);
	    			Yii::app()->functions->sendEmail(
					  $this->data['email'],'',self::t("Thank you for signing up"),$tpl
					);
	    		}
	    		
    			
    		} else $this->msg=self::t("Something went wrong please try again later");
    		
    	} else $this->msg=Driver::parseValidatorError($Validator->getError());		
    	$this->output();
    }
    
    public function actionUploadProfile()
    {
    	$this->data=$_REQUEST;
    	
    	$request=json_encode($_REQUEST);
    	
    	if (!isset($this->data['token'])){
    		$this->data['token']='';
    	}
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("token not found");
    		echo "$this->code|$this->msg||".$request;
    		Yii::app()->end();
    	}     	    	
    	
    	$driver_id=$token['driver_id'];  
    	
    	$path_to_upload=Driver::driverUploadPath();
    	if(!file_exists($path_to_upload)) {	
           if (!@mkdir($path_to_upload,0777)){           	               	
           	    $this->msg=Driver::t("Error has occured cannot create upload directory");
                $this->jsonResponse();
           }		    
	    }
	    
	    $profile_photo='';
	    	    	    
	    if(isset($_FILES['file'])){
	    	
	    	header('Access-Control-Allow-Origin: *');
	    	
		    $new_image_name = urldecode($_FILES["file"]["name"]).".jpg";	
		    $new_image_name=str_replace(array('?',':'),'',$new_image_name);
		        
		    @move_uploaded_file($_FILES["file"]["tmp_name"], "$path_to_upload/".$new_image_name);
		    
		    $db=new DbExt;
		    $params=array(
		     'profile_photo'=>$new_image_name,
		     'date_modified'=>Driver::dateNow()
		    );
		    if($db->updateData("{{driver}}",$params,'driver_id',$driver_id)){
			    $this->code=1;
			    $this->msg=self::t("Upload successful");
			    $this->details=$new_image_name;
			    $profile_photo=websiteUrl()."/upload/driver/".$new_image_name;
		    } else $this->msg=self::t("Error cannot update");
		    
	    } else $this->msg=self::t("Image is missing");
	    
    	echo "$this->code|$this->msg|$profile_photo|".$request;
    }
    
    public function actionUploadTaskPhoto()
    {
    	
    	$this->data=$_REQUEST;
    	
    	$request=json_encode($_REQUEST);
    	
    	if (!isset($this->data['token'])){
    		$this->data['token']='';
    	}
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("token not found");
    		echo "$this->code|$this->msg||".$request;
    		Yii::app()->end();
    	}     	    	
    	
    	$driver_id=$token['driver_id'];  
    	$driver_name=$token['first_name'];
    	$task_id=isset($this->data['task_id'])?$this->data['task_id']:'';
    	
    	$args=array(
    	  '{driver_name}'=>$driver_name
    	);
    	
    	if ( !$task_info=Driver::getTaskId($task_id)){    		
    		$this->msg=self::t("Task not found");
    		echo "$this->code|$this->msg||".$request;
    		Yii::app()->end();
    	}    	
    	    
    	$params=array(
    	  'status'=>"photo",
    	  'remarks'=> $driver_name." ".self::t("added a photo"),
    	  'date_created'=>Driver::dateNow(),
    	  'ip_address'=>$_SERVER['REMOTE_ADDR'],
    	  'task_id'=>$task_id,
    	  'driver_id'=>$driver_id,
    	  'driver_location_lat'=>$token['location_lat'],
    	  'driver_location_lng'=>$token['location_lng'],
    	  'remarks2'=>"{driver_name} added a photo",
    	  'remarks_args'=>json_encode($args),
    	  'notes'=>'',
    	  'order_id'=>isset($task_info['order_id'])?$task_info['order_id']:''
    	);
    	    	
    	$path_to_upload=Driver::driverUploadPath();
    	if(!file_exists($path_to_upload)) {	
           if (!@mkdir($path_to_upload,0777)){           	               	
           	    $this->msg=Driver::t("Error has occured cannot create upload directory");
                $this->jsonResponse();
           }		    
	    }
	    
	    $profile_photo='';
	    	    	    
	    if(isset($_FILES['file'])){
	    	
	    	header('Access-Control-Allow-Origin: *');
	    	
		    $new_image_name = urldecode($_FILES["file"]["name"]).".jpg";	
		    $new_image_name=str_replace(array('?',':'),'',$new_image_name);
		        
		    @move_uploaded_file($_FILES["file"]["tmp_name"], "$path_to_upload/".$new_image_name);
		    
		    $db=new DbExt;
		    
		    $params_photo=array(
    		  'task_id'=>$task_id,
    		  'photo_name'=>$new_image_name,
    		  'date_created'=>Driver::dateNow(),
    		  'ip_address'=>$_SERVER['REMOTE_ADDR']
    		);
    		    		
    		$db->insertData("{{driver_task_photo}}",$params_photo);    	    		
    		$photo_task_id=Yii::app()->db->getLastInsertID();    		
    		
    		$params['photo_task_id']=$photo_task_id;    			
    		$db->insertData("{{order_history}}",$params);
    		unset($db);
    		
    		$this->code=1;
			$this->msg=self::t("Upload successful");
    		$this->details=$task_id;
    		
    		if ( $task_info['trans_type']=="delivery"){
    			Driver::sendNotificationCustomer('DELIVERY_PHOTO',$task_info);
    		} else Driver::sendNotificationCustomer('PICKUP_PHOTO',$task_info);    		
		    		        		
	    } else $this->msg=self::t("Image is missing");
	    
    	echo "$this->code|$this->msg|$this->details|".$request;
    }
    
    public function actiongetTaskPhoto()
    {
    	if (!isset($this->data['token'])){
    		$this->data['token']='';
    	}
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("token not found");
    		echo "$this->code|$this->msg||".$request;
    		Yii::app()->end();
    	}     	    	
    	    	
    	$task_id=isset($this->data['task_id'])?$this->data['task_id']:'';
    	
    	if ( !$task_info=Driver::getTaskByID($task_id)){    		
    		$this->msg=self::t("Task not found");
    		$this->output();
    		Yii::app()->end();
    	}
    	    	
    	if ( $res=Driver::getTaskPhoto($task_id)){
    		$this->code=1;
    		$this->msg=$task_info['status'];
    		$this->details=$res;
    	} else $this->msg=self::t("No photo to show");
    	   	
    	$this->output();
    }
    
    public function actiondeletePhoto()
    {
    	if (!isset($this->data['token'])){
    		$this->data['token']='';
    	}
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("token not found");
    		echo "$this->code|$this->msg||".$request;
    		Yii::app()->end();
    	}     	    	
    	
    	
    	$id=isset($this->data['id'])?$this->data['id']:'';
    	if ( $res=Driver::getPhotoDetails($id)){    		
	    	Driver::deletePhoto($id);
	    	$file=Driver::driverUploadPath()."/".$res['photo_name'];
	    	if (file_exists($file)){	  
	    	   @unlink($file);
	    	}
	    	$this->msg="OK"; $this->code=1;
    	} else $this->msg=self::t("Photo not found");
    	
    	$this->output();
    }
    
    public function actionLoadSignature()
    {    
    
    	if ( !$token=Driver::getDriverByToken($this->data['token'])) {
    		$this->msg=self::t("Token not valid");
    		$this->output();
    		Yii::app()->end();
    	} 
    	
    	$driver_id=$token['driver_id'];    	    	
    	    	
    	if ( $res=Driver::getTaskId($this->data['task_id']) ){    		    		
    		$task_id=$res['task_id'];
    		if ( $data=Driver::getLastSignature($task_id)){    			
    			$this->msg="OK";
    			$this->code=1;
    			if (!empty($data['customer_signature'])){
    				$data['customer_signature_url']=Driver::uploadURL()."/".$data['customer_signature'];
    				if (!file_exists(Driver::uploadPath()."/".$data['customer_signature'])){
    					$data['customer_signature_url']='';
    				}
    			}
    			
    			$this->details=array(
    			  'task_id'=>$task_id,
    			  'status'=>$res['status'],
    			  'data'=>$data
    			);
    		} else $this->msg=self::t("no signature found");
    	} else $this->msg=self::t("Task not found");
    	$this->output();
    }    
        
} /*end class*/