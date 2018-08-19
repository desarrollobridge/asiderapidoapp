<?php 
class Driver
{
	
	public static function assetsUrl()
	{
		return Yii::app()->baseUrl.'/protected/modules/driver/assets';
	}
	
	public static function jsLang()
	{
		return array(
		  'are_your_sure'=>self::t("Are you sure"),
		  'create_agent'=>self::t("Create Agent"),
		  'update_agent'=>self::t("Update Agent"),
		  'create_team'=>self::t("Create Team"),
		  'update_team'=>self::t("Update Team"),
		  'add_driver'=>self::t("Add Driver"),
		  'update_driver'=>self::t("Update Driver"),
		  'pickup_before'=>self::t("Pickup before"),
		  'delivery_before'=>self::t("Delivery before"),
		  'delivery_address'=>self::t("Delivery Address"),
		  'pickup_address'=>self::t("Pickup Address"),
		  'location_on_map'=>self::t("Location on Map"),
		  'no_history'=>self::t("No history"),
		  'reason'=>self::t("Reason"),
		  'assign_agent'=>self::t("Assign Agent"),
		  're_assign_agent'=>self::t("Re-assign Agent"),
		  'details'=>self::t("Details"),
		  'name'=>self::t("Name"),
		  'task_id'=>self::t("Task ID"),
		  'undefine_result'=>self::t("Undefined Result"),
		  'connection_lost'=>self::t("Connection Lost"),
		  'task'=>Driver::t("Task"),
		  'online'=>Driver::t("Online"),
		  'offline'=>Driver::t("Offline"),
		  'not_available'=>Driver::t("Not available"),
		  'no_notification'=>self::t("No notifications for today"),
		  'currentlocation'=>self::t("Current Location"),
		  'autoassigning'=>self::t("Auto assigning"),
		  'on'=>t("ON"),
		  'off'=>t("OFF"),
		  'notes'=>t("Notes"),
		  'profile_photo'=>t("Profile Photo"),
		  'send_push'=>self::t("Send Push"),
		  'on_duty'=>self::t("On-Duty"),
		  'receive_by'=>self::t("Receive By"),
		  'lat'=>t("Lat"),
		  'lng'=>t("Lng"),
		  'driver_required'=>self::t("Driver name is required")
		);
	}
	
	public static function jsLang2()
	{
		return array(
		  "tablet_1"=>self::t("No data available in table"),
    	  "tablet_2"=>self::t("Showing _START_ to _END_ of _TOTAL_ entries"),
    	  "tablet_3"=>self::t("Showing 0 to 0 of 0 entries"),
    	  "tablet_4"=>self::t("(filtered from _MAX_ total entries)"),
    	  "tablet_5"=>self::t("Show _MENU_ entries"),
    	  "tablet_6"=>self::t("Loading..."),
    	  "tablet_7"=>self::t("Processing..."),
    	  "tablet_8"=>self::t("Search:"),
    	  "tablet_9"=>self::t("No matching records found"),
    	  "tablet_10"=>self::t("First"),
    	  "tablet_11"=>self::t("Last"),
    	  "tablet_12"=>self::t("Next"),
    	  "tablet_13"=>self::t("Previous"),
    	  "tablet_14"=>self::t(": activate to sort column ascending"),
    	  "tablet_15"=>self::t(": activate to sort column descending"),
    	  "trans_33"=>self::t("No results match"),
    	  "trans_32"=>self::t("Select Some Options"),
    	  "trans_33"=>self::t("No results match"),
		);
	}
	
	public static function jsLanguageValidator()
    {
    	$js_lang=array(
		  'requiredFields'=>self::t("You have not answered all required fields"),
		  'groupCheckedTooFewStart'=>self::t("default","Please choose at least"),
		  'badEmail'=>self::t("You have not given a correct e-mail address"),
		);
		return $js_lang;
    }

	public static function driverStatus()
	{
		return array(		  
		 'active'=>self::t('active'),	 
		 'pending'=>self::t('pending for approval'),
		 'suspended'=>self::t('suspended'),
		 'blocked'=>self::t('blocked'),
		 'expired'=>self::t('expired'),
		 'denied'=>self::t('denied'),
		);
	}	
	
	public static function parseValidatorError($error='')
	{
		$error_string='';
		if (is_array($error) && count($error)>=1){
			foreach ($error as $val) {
				$error_string.="$val\n";
			}
		}
		return $error_string;		
	}			
	
	public static function deliveryTimeOption()
	{
		$data[]=self::t("Please select");
		for ($i = 1; $i <= 5; $i++) {
            //$data[$i]=self::t("after". " ". $i ." " ."hour of purchase" );
            $data[$i]=self::t("after")." ". $i ." ". t("hour of purchase");
        }
        return $data;
	}
	
	public static function t($message='')
	{
		//return Yii::t("default",$message);
		return Yii::t("driver",$message);
	}
	
	public static function q($data)
	{
		return Yii::app()->db->quoteValue($data);
	}
	
	public static function transportType()
	{
		return array(
		  'truck'=>self::t("Truck"),
		  'car'=>self::t("Car"),
		  'bike'=>self::t("Bike"),
		  'bicycle'=>self::t("Bicycle"),
		  'scooter'=>self::t("Scooter"),
		  'walk'=>self::t("Walk"),
		);
	}
	
    public static function prettyPrice($amount='')
	{
		if(!empty($amount)){
			return displayPrice(getCurrencyCode(),prettyFormat($amount));
		}
		return 0;
	}	
			
	public static function islogin()
	{
		if(isset($_SESSION['driver'])){
			if(!empty($_SESSION['driver']['user_type'])){
				return true;
			}
		}
		return false;
	}
	
	public static function getLoginType()
	{
		if(isset($_SESSION['driver']['user_type'])){
			return $_SESSION['driver']['user_type'];
		}
		return false;	
	}
	
	public static function getUserType()
	{
		if(isset($_SESSION['driver']['user_type'])){
		   return $_SESSION['driver']['user_type'];
		}
		return false;
	}
	
	public static function getUserId()
	{
		if (self::islogin()){
			switch (self::getUserType()) {
				case "admin":
					return $_SESSION['driver']['info']['admin_id'];
					break;
			
				case "merchant":	
				    return $_SESSION['driver']['info']['merchant_id'];
				    break;
				   
				default:
					break;
			}
		}
		return false;
	}
	
	public static function uploadURL()
	{
	    return Yii::app()->getBaseUrl(true)."/upload";
    }
    
    public static function uploadPath()
    {
    	return Yii::getPathOfAlias('webroot')."/upload";  
    }
	
    public static function moduleUrl()
	{
	    return Yii::app()->getBaseUrl(true)."/protected/modules/driver";
    }
    
	public static function adminLogin($username='',$password='')
	{
		$db=new DbExt;
		$stmt="
		SELECT * FROM
		{{admin_user}}
		WHERE
		username=".self::q($username)."
		AND
		password=".self::q(md5($password))."
		LIMIT 0,1
		";
		if($res=$db->rst($stmt)){
			return $res[0];
		}
		return false;
	}
	
	public static function merchantLogin($username='',$password='')
	{
		$db=new DbExt;
		$stmt="
		SELECT * FROM
		{{merchant}}
		WHERE
		username=".self::q($username)."
		AND
		password=".self::q(md5($password))."
		LIMIT 0,1
		";
		if($res=$db->rst($stmt)){
			return $res[0];
		}
		return false;
	}	
	
	public static function cleanText($text='')
	{
		return stripslashes($text);
	}
	
	public static function getTeam($team_id='')
	{
		$db=new DbExt;
		$stmt="
		SELECT * FROM
		{{driver_team}}
		WHERE
		team_id=".self::q($team_id)."
		LIMIT 0,1
		";
		if($res=$db->rst($stmt)){
			return $res[0];
		}
		return false;
	}
	
	public static function teamList($user_type='', $user_id='' , $status='publish')
	{
		
		$and='';
		if(!empty($user_type)){			
			$user_type=$user_type;
		} else $user_type=self::getUserType();		
		
		//if ( self::getUserType()=="admin"){
		if($user_type=="admin"){
			$and=" AND user_type=".self::q($user_type)."  ";
		} else {
			$and =" AND user_type=".self::q($user_type)."  ";
			$and.=" AND user_id=".self::q($user_id)."  ";
		}
				
		if ($user_type=="merchant"){
			$driver_allowed_team_to_merchant=getOptionA('driver_allowed_team_to_merchant');
			//dump($driver_allowed_team_to_merchant);
			if($driver_allowed_team_to_merchant>0){
				if($driver_allowed_team_to_merchant==1){
					$and.=" OR user_type ='admin' ";
				} elseif ($driver_allowed_team_to_merchant==2){
					$driver_allowed_merchant_list=getOptionA('driver_allowed_merchant_list');
					if(!empty($driver_allowed_merchant_list)){
						$driver_allowed_merchant_list=json_decode($driver_allowed_merchant_list,true);
						if(in_array($user_id,$driver_allowed_merchant_list)){
							$and.=" OR user_type ='admin' ";
						}
					}
				} else {
					
				}
			}
		}
		
		$db=new DbExt;
		$stmt="
		SELECT * FROM
		{{driver_team}}		
		WHERE 1
		$and
		AND status ='$status'
		ORDER BY team_name ASC
		";		
		//dump($stmt);
		if($res=$db->rst($stmt)){
			return $res;
		}
		return false;
	}
	
	public static function getTeamAll()
	{
		$db=new DbExt;
		$stmt="
		SELECT * FROM
		{{driver_team}}		
		WHERE
		status='publish'		
		ORDER BY team_name ASC	
		";
		if($res=$db->rst($stmt)){
			return $res;
		}
		return false;
	}	
	
	public static function toList($data='',$key='',$value='',$default_value='')
	{
		$list='';
		if(is_array($data) && count($data)>=1){
			if(!empty($default_value)){
				$list[]=$default_value;
			}
			foreach ($data as $val) {
				$list[$val[$key]]=$val[$value];
			}
			return $list;
		}		
		return false;
	}
	
	public static function driverInfo($driver_id='')
	{
		$db=new DbExt;
		$stmt="
		SELECT a.*,
		b.team_name
		FROM
		{{driver}} a
		LEFT JOIN {{driver_team}} b
		On
        a.team_id = b.team_id 		
		WHERE
		driver_id=".self::q($driver_id)."
		LIMIT 0,1
		";
		if($res=$db->rst($stmt)){
			return $res[0];
		}
		return false;
	}	
	
	public static function driverList($user_type='', $user_id='')
	{		
        $and='';
		if ( $user_type=="admin"){
			$and=" AND user_type=".self::q($user_type)."  ";
		} else {
			$and =" AND user_type=".self::q($user_type)."  ";
			$and.=" AND user_id=".self::q($user_id)."  ";
		}		
		$db=new DbExt;
		$stmt="
		SELECT * FROM
		{{driver}}
		WHERE
		user_type=".self::q($user_type)."
		AND
		user_id =".self::q($user_id)."
		ORDER BY first_name ASC
		";		
		if($res=$db->rst($stmt)){
			return $res;
		}
		return false;
	}	
	
	public static function getAllDriver($user_type='', $user_id='')
	{
		$and='';
		if ( self::getUserType()=="admin"){
			$and=" AND user_type=".self::q($user_type)."  ";
		} else {
			$and =" AND user_type=".self::q($user_type)."  ";
			$and.=" AND user_id=".self::q($user_id)."  ";
			
			$driver_allowed_team_to_merchant=getOptionA('driver_allowed_team_to_merchant');
			if($driver_allowed_team_to_merchant==1){
				$and.=" OR user_type='admin' ";
			} elseif ($driver_allowed_team_to_merchant==2){
				$driver_allowed_merchant_list=getOptionA('driver_allowed_merchant_list');
				if(!empty($driver_allowed_merchant_list)){
					$driver_allowed_merchant_list=json_decode($driver_allowed_merchant_list,true);					
					if ( in_array($user_id,(array)$driver_allowed_merchant_list)){
						$and.=" OR user_type='admin' ";
					}
				}
			}
			
		}
		$db=new DbExt;
		$stmt="
		SELECT * FROM				
		{{driver}}		
		WHERE 1
		$and
		AND status='active'
		ORDER BY first_name ASC
		";		
		//dump($stmt);
		if($res=$db->rst($stmt)){
			return $res;
		}
		return false;
	}		
	
	public static function updateDriverTeam($driver='',$team_id='')
	{
		$db=new DbExt;
		if(!empty($driver)){
			$driver=json_decode($driver,true);
			if(is_array($driver) && count($driver)>=1){
				foreach ($driver as $driver_id) {
					$params['team_id']=$team_id;
					$db->updateData("{{driver}}",$params,'driver_id',$driver_id);
					unset($params);
				}
			}
		}
	}
	
	public static function updateTeamDriver($driver_id='',$team_id='')
	{
		dump($driver_id);
		dump($team_id);
		if ($res=self::getTeam($team_id)){
			dump($res);
			if(!empty($res['team_member'])){
				$team_member=json_decode($res['team_member'],true);
				$team_member=array_flip($team_member);
				dump($team_member);
			}
		}
	}
	
	public static function getDriverByTeam($team_id='')
	{
		$db=new DbExt;
		if(!empty($team_id)){
			$stmt="SELECT * FROM
			{{driver}}
			WHERE
			team_id=".self::q($team_id)."
			";
			//dump($stmt);
			if($res=$db->rst($stmt)){
			   return $res;
			}
		}
		return false;
	}
	
	public static function getTask($user_type='',$user_id='')
	{
		$and='';
		if ( self::getUserType()=="merchant" ){
			$and=" AND user_type=".self::q($user_type)." ";
			$and=" AND user_id=".self::q($user_id)." ";
		}
		$db=new DbExt;		
		$stmt="SELECT * FROM
		{{driver_task}}
		WHERE 1		
		$and
		ORDER BY date_created ASC
		";
		if($res=$db->rst($stmt)){
		   return $res;
		}	
		return false;
	}	
	
	public static function getTaskByDriverID($driver_id='',$delivery_date='')
	{
		$db=new DbExt;		
		$db->qry("SET SQL_BIG_SELECTS=1");
			
		$stmt="SELECT * FROM
		{{driver_task_view}}
		WHERE
		driver_id=".self::q($driver_id)."
		AND
		delivery_date LIKE '".$delivery_date."%'		
		ORDER BY delivery_date ASC
		";			
		if($res=$db->rst($stmt)){
		   return $res;
		}	
		return false;
	}		
	
	public static function getTaskId($task_id='')
	{
		$db=new DbExt;		
		$db->qry("SET SQL_BIG_SELECTS=1");
		/*$stmt="SELECT a.*,
        concat(b.first_name,' ',b.last_name) as driver_name,
		b.on_duty,
		c.team_name
		FROM
		{{driver_task}} a
		LEFT JOIN {{driver}} b
		On
        a.driver_id = b.driver_id 
        LEFT JOIN {{driver_team}} c
        ON 
        c.team_id=a.team_id
		WHERE
		task_id=".self::q($task_id)."		
		LIMIT 0,1
		";*/
		$stmt="
		SELECT * FROM
		{{driver_task_view}}
		WHERE
		task_id=".self::q($task_id)."		
		LIMIT 0,1
		";
		if($res=$db->rst($stmt)){
		   return $res[0];
		}	
		return false;
	}		
	
	public static function deleteTask($task_id='')
	{
		$db=new DbExt;	
		$stmt="
		DELETE FROM
		{{driver_task}}
		WHERE
		task_id=".self::q($task_id)."
		";
		if($db->qry($stmt)){
			
			//delete all history
			$stmt2="
			DELETE FROM
			{{order_history}}
			WHERE
			task_id=".self::q($task_id)."
			";
			$db->qry($stmt2);
			
			return true;
		}
		return false;
	}
	
	public static function getTaskByStatus($user_type='',$merchant_id='',$status='',$date='')
	{

		if ($user_type=="admin"){
		   $where="WHERE 1";
		   
		   $driver_show_admin_only_task=getOptionA('driver_show_admin_only_task');
		   if($driver_show_admin_only_task==1){
		   	  $where="WHERE user_type=".self::q($user_type)."";
		   }
		   
		} else {
		   $where="WHERE user_type=".self::q($user_type)."";
		   if($merchant_id>0){
		   	  $where.=" AND user_id =".self::q($merchant_id)." ";
		   }
		}
		
		$where_status='';	
		
		$order_by="ORDER BY task_id DESC ";	
						
		$and_date='';
		if (!empty($date)){			
			$and_date=" AND delivery_date LIKE '".$date."%' ";
		}
		
		switch ($status) {
			case "unassigned":								
				$where_status="AND status IN ('declined','unassigned')";
				$order_by="ORDER BY critical DESC,task_id DESC";
				break;
				
			case "assigned":	
			    $where_status="AND status IN ('assigned','started','inprogress','acknowledged')";
		        break;
			
			case "completed":	
			    $where_status="AND status IN ('successful','failed','cancelled','canceled')";
			    break;
			    
			default:
				$where_status="AND status =".self::q($status)."";
				break;
		}
		
		$db=new DbExt;		
		$db->qry("SET SQL_BIG_SELECTS=1");
		$stmt="
		SELECT * FROM
		{{driver_task_view}}		
		$where		
		$and_date
		$where_status
		$order_by
		";
		//dump($stmt);
		if($res=$db->rst($stmt)){
		   //dump($res);
		   return $res;
		}	
		return false;
	}		
	
	public static function formatTask($data='' , $enabled_critical_task='', $critical_minutes='' )
	{
		if (is_array($data) && count($data)>=1){			
			//dump($data);
			
			$is_critical=false;
			
			if($enabled_critical_task==1){
				if ($data['critical']==1){				
					
					$time_1=date('Y-m-d g:i:s a');			
				    $time_2=date("Y-m-d g:i:s a",strtotime($data['date_created']));
								
					$time_diff=self::dateDifference($time_2,$time_1);
					//dump($time_diff);				
					if(is_array($time_diff)){
						if($time_diff['days']>0){
							$is_critical=true;
						}
						if($time_diff['hours']>0){
							$is_critical=true;
						}
						if($time_diff['minutes']>$critical_minutes){
							$is_critical=true;
						}					
					}
					
					if($is_critical){
						$db=new DbExt;						
						$db->updateData("{{driver_task}}",array(
						  'critical'=>2
						),'task_id',$data['task_id']);
						unset($db);
					}
				} else $is_critical=true;
			} else $is_critical=true;
			
			$trans_type=self::t("D");
			if ( $data['trans_type']=="pickup"){
				$trans_type= self::t("P");
			}
			
			/*remove critical if the status is no unassigned*/
			if ( $data['critical']==2){
				if ( $data['status']!="unassigned"){
					$data['critical']=1;
				}
			}
			
			ob_start();
			?>
			<div class="row box task-map <?php echo $data['critical']==2?"task_critical":"";?>" 
			data-lat="<?php echo $data['task_lat']?>"
			data-lng="<?php echo $data['task_lng']?>"
			data-id="<?php echo $data['task_id']?>" >
						
		      <div class="col-xs-2 center">
		       <div class="tag rounded <?php echo $data['trans_type'];?>"><?php echo $trans_type;?></div>
		       <div class="top10"><i class="ion-ios-location"></i></div>
		       <div class="top10"><i class="ion-ios-time-outline"></i></div>
		       <?php if ($data['driver_id']>0):?>
		       <div class="top10"><i class="ion-android-person"></i></div>
		       <?php endif;?>
		      </div> <!--row-->
		      
		      <div class="col-xs-10">      
		      
		        <div class="row ">
		          <?php if ( $data['task_id']>0 ):?>
		          <div class="col-md-6 small">
		          <?php echo Driver::t("Task ID")?>. <b><?php echo $data['task_id']?></b></div>
		          <?php endif;?>
		          <?php if ( $data['order_id']>0 ):?>
		          <div class="col-md-6 small">
		          <?php echo Driver::t("Order No")?>. <b><?php echo $data['order_id']?></b></div>
		          <?php endif;?>		          
		        </div>
		        
		        <?php if ( Driver::getUserType()=="admin"):?>
		        <?php if (!empty($data['merchant_name'])): ?>
		        <div class="row top10">
		         <div class="col-md-12"> 
		           <?php 
		           echo Driver::t("Merchant name").": <span class=\"text-primary\">".
		           self::cleanString($data['merchant_name'])."</span>";
		           ?>
		         </div>
		        </div>
		        <?php endif;?>
		        <?php endif;?>
		        
		        <div class="row top10">
		          <div class="col-md-5"><?php echo $data['customer_name']?></div>
		          <div class="col-md-7 text-right">
		          
		          <?php if ($data['status']=="unassigned"):?>
		           <a href="javascript:;" class="assign-agent inline orange-button-small rounded"
		           data-id="<?php echo $data['task_id']?>" >
		           <?php echo self::t("Assign Driver")?>
		           </a>
		          <?php else :?>
		          <p class="rounded tag <?php echo $data['status']?>">
		             <?php echo Driver::t($data['status'])?>
		          </p>
		          <?php endif;?> 
		           
		          </div> <!--col-->
		        </div> <!--row-->
		        
		        <div class="row top5">
		         <div class="col-md-8">
		          <p class="task_address top10 concat-text"><?php echo $data['delivery_address']?></p>
		          <p class="task_time">
		          <?php echo date('g:i a',strtotime($data['delivery_date']))?>
		          </p>        
		         </div>
		         <div class="col-md-4">
		           <a href="javascript:;" class="task-details" data-id="<?php echo $data['task_id']?>" >
		           <?php echo Driver::t("Details")?>
		           </a>
		         </div>
		        </div> <!--row-->
		        
		        <?php if ($data['driver_id']>0):?>
		        <p class="concat-text top10">
		        <?php echo $data['driver_name']?>
		        </p>
		        <?php endif;?>
		        
		      </div> <!--row-->
		      
		      <?php if(!empty($data['assignment_status']) && $data['status']=="unassigned"):?>
		      <?php if ($data['assignment_status']=="unable to auto assign"):?>
		      
		           <div class="col-md-7 top5 center autoassign-col-1-<?php echo $data['task_id']?>">
		           <p class="small-font text-danger"><?php echo Driver::t($data['assignment_status'])?></p>
		           </div>
		           		           
		           <div class="col-md-5 top5 text-right autoassign-col-2-<?php echo $data['task_id']?>">
		           <a href="javascript:retryAutoAssign('<?php echo $data['task_id']?>');"  class="small-font">
		              <?php echo Driver::t("Retry")?>
		           </a>
		           </div>
		          <?php else :?>
		          <div class="col-md-12 top5 center">		        		        
		          <p class="small-font text-primary">
		            <?php echo Driver::t($data['assignment_status'])?>... 
		            <i class="small-font fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
		          </p>
		        <?php endif;?>
		      </div>
		      <?php endif;?>
		      
		    </div> <!--row-->    
			<?php
			$forms = ob_get_contents();
            ob_end_clean();
            return $forms;
		} 
	}
	
	public static function statusList()
	{
		//acknowledged
		return array(
		  ''=>self::t("Please select status"),
		  'unassigned'=>self::t("Unassigned"),
		  'assigned'=>self::t("Assigned"),
		  'started'=>self::t("Started"),
		  'inprogress'=>self::t("Inprogress"),
		  'successful'=>self::t("Successful"),
		  'failed'=>self::t("Failed"),
		  'declined'=>self::t("Declined"),
		  'cancelled'=>self::t("Cancelled"),
		);
	}
	
	public static function prettyStatus($from='', $to='')
	{
		if(!empty($from) && !empty($to)){
			/*$prety= self::t("Status updated from");
			$prety.=" $from ". self::t("to") ." $to";
			return $prety;*/
			$prety= "Status updated from";
			$prety.=" $from ". "to" ." $to";
			return $prety;
		}
		return Driver::t("Status changed");
	}
			
	public static function getTaskHistory($task_id='',$order_id='')
	{
		/*dump($task_id);
		dump($order_id);*/
		
		$db=new DbExt;		
		$and='';
		$or='';
		if ( $order_id>0){
			$or="order_id=".self::q($order_id)." ";
		}
		if ( $task_id>0){
			if (!empty($or)){
			   $or.=" OR task_id=".self::q($task_id)." ";
			} else {
			   $or="task_id=".self::q($task_id)." ";
			}
		}
				
		if(!empty($or)){
			$and=" AND ( $or ) ";
		}
		
		$stmt="SELECT * FROM
		{{order_history}}
		WHERE
		1
		$and
		ORDER BY id ASC
		";
		//dump($stmt);
		if ( $res=$db->rst($stmt)){
			return $res;
		}
		return false;
	}
	
	public static function hasModuleAddon($modulename='')
	{
		if (Yii::app()->hasModule($modulename)){
		   $path_to_upload=Yii::getPathOfAlias('webroot')."/protected/modules/$modulename";	
		   if(file_exists($path_to_upload)){
		   	   return true;
		   }
		}
		return false;
	}
	
	public static function AdminStatusTpl()
	{
		//$team_list=Driver::teamList( 'merchant',  Yii::app()->functions->getMerchantID() );
		$team_list=Driver::teamList( 'merchant',  self::getUserId() );
        if($team_list){
      	  $team_list=Driver::toList($team_list,'team_id','team_name',
      	    Driver::t("Select a team")
      	  );
        }
        dump($team_list);
        
        $all_driver=Driver::getAllDriver();  
		?>
		<div class="uk-form-row">
    	  <label class="uk-form-label"><?php echo t('Select Team')?></label>
    	  <?php 
           echo CHtml::dropDownList('team_id','', (array)$team_list,array(
            'class'=>"task_team_id"
           ))
          ?>
    	 </div>
    	 
    	 <div class="uk-form-row">
    	   <label class="uk-form-label"><?php echo t('Assign Agent')?></label>
    	   <select name="driver_id" id="driver_id" class="driver_id">
		   <?php if(is_array($all_driver) && count($all_driver)>=1):?>
		    <option value=""><?php echo Driver::t("Select driver")?></option>
		    <?php foreach ($all_driver as $val):?>
		    <option class="<?php echo "team_opion option_".$val['team_id']?>" value="<?php echo $val['driver_id']?>">
		      <?php echo $val['first_name']." ".$val['last_name']?>
		    </option>
		    <?php endforeach;?>
		   <?php endif;?>
		  </select>
    	 </div>
		<?php
	}
	
	public static function addToTask($order_id='')
	{		
		$db=new DbExt;
		
		if($order_id<=0){
			return ;
		}
		$order_status=Yii::app()->functions->getOptionAdmin('drv_order_status');	
		if(empty($order_status)){
			$order_status='accepted';
		}
		
		$plus_hour=Yii::app()->functions->getOptionAdmin('drv_delivery_time');
		if(empty($plus_hour)){
			$plus_hour=0;
		}
		
		//check if the order has been cancel
		$drv_order_cancel=Yii::app()->functions->getOptionAdmin('drv_order_cancel');
		if(empty($drv_order_cancel)){
			$drv_order_cancel='cancelled';
		}		
		
		if(isset($_POST['status'])){
			if($_POST['status']==$drv_order_cancel){
				if ( $ras=Driver::getTaskByOrderID($order_id)){					
					$params=array(
					    'status'=>'cancelled',
					    'date_modified'=>date('c'),
					    'ip_address'=>$_SERVER['REMOTE_ADDR']
					);				
					$db->updateData("{{driver_task}}",$params,'order_id',$order_id);
					
					/*update assigment*/
					$sql_assign="
					UPDATE {{driver_assignment}}
					SET task_status='cancelled'
					WHERE
					task_id =".self::q($ras['task_id'])."
					";							
					$db->qry($sql_assign);
					Driver::sendDriverNotification('CANCEL_TASK',$ras);					
					return ;
				}
			}
		}
				
		$date_now=date('Y-m-d');
		$stmt="
		SELECT a.*,
		concat(b.first_name,' ' ,b.last_name) as customer_name,
		b.email_address,
		concat( c.street,' ', c.city, ' ', c.state,' ',c.zipcode ,' ', c.country ) as delivery_address,
		c.contact_phone	as contact_number,
		c.formatted_address,
		c.google_lat,
		c.google_lng
		
		FROM
		{{order}} a
		
		left join {{client}} b
        ON
        b.client_id=a.client_id
        
        left join {{order_delivery_address}} c
        ON
        c.order_id=a.order_id
		
		WHERE
		a.order_id = '".$order_id."'
		AND
		a.status in ('$order_status','paid')
		AND
		a.trans_type in ('delivery')
		AND
		a.order_id NOT IN (
		  select order_id
		  from
		  {{driver_task}}
		  WHERE
		  order_id=a.order_id		  
		)
		
		LIMIT 0,1
		";		
		//dump($stmt);
		if ( $res=$db->rst($stmt)){
			foreach ($res as $val) {
											
				$merchant_id=$val['merchant_id'];
				
				$lat=0;
				$long=0;			
				
				$delivery_date=!empty($val['delivery_date'])?$val['delivery_date']:date("Y-m-d");
				if(!empty($val['delivery_time'])){
					//$delivery_date.=" ".$val['delivery_time'];					
					$delivery_date=" ".date("Y-m-d G:i",strtotime($delivery_date." ".$val['delivery_time']." +$plus_hour hour" ));	
				} else {
					//$delivery_date.=" 23:00";					
					$delivery_date.= " ".date("G:i",strtotime("+$plus_hour hour"));
				}
							
				$driver_owner_task=getOptionA('driver_owner_task');				
				if($driver_owner_task=="default"){
					
					$driver_owner_task='merchant';
					
					/*check task owner*/
					$driver_merchant_task_to_admin=getOptionA('driver_merchant_task_to_admin');					
					if(!empty($driver_merchant_task_to_admin)){
						$driver_merchant_task_to_admin=json_decode($driver_merchant_task_to_admin,true);
						if (in_array($merchant_id,(array)$driver_merchant_task_to_admin)){
							$driver_owner_task='admin';	
						}
					}
					
				} 
				if(empty($driver_owner_task)){
				   $driver_owner_task='admin';	
				}				
				
				$params=array(
				  'order_id'=>$val['order_id'],
				  //'user_type'=>'merchant',
				  'user_type'=>$driver_owner_task,
				  'user_id'=>$val['merchant_id'],
				  'trans_type'=>$val['trans_type'],				  
				  'email_address'=>isset($val['email_address'])?$val['email_address']:'',
				  'customer_name'=>isset($val['customer_name'])?$val['customer_name']:'',
				  'contact_number'=>isset($val['contact_number'])?$val['contact_number']:'',
				  'delivery_date'=>$delivery_date,
				  'delivery_address'=>isset($val['delivery_address'])?$val['delivery_address']:'' ,				  
				  'date_created'=>date('c'),
				  'ip_address'=>$_SERVER['REMOTE_ADDR'],
				);
								
				if (!empty($val['google_lat']) && !empty($val['google_lng'])){													
					$params['task_lat']=$val['google_lat'];
					$params['task_lng']=$val['google_lng'];
				} else {					
					if ( $location=Driver::addressToLatLong($params['delivery_address'])){							
						$params['task_lat']=$location['lat'];
						$params['task_lng']=$location['long'];
					}
				}
				
				if(isset($val['formatted_address'])){
					if(!empty($val['formatted_address'])){
						$params['delivery_address']=addslashes($val['formatted_address']);
					}
				}
											
				if ( $merchant_info=self::getMerchantByID($merchant_id)){
					
					$drop_address=$merchant_info['street'];
					if(!empty($merchant_info['city'])){
					   $drop_address.=" ".$merchant_info['city'];
					}
					if(!empty($merchant_info['state'])){
					   $drop_address.=" ".$merchant_info['state'];
					}
					if(!empty($merchant_info['post_code'])){
					   $drop_address.=" ".$merchant_info['post_code'];
					}
					$params['dropoff_merchant']=$merchant_id;
					$params['dropoff_contact_name']=$merchant_info['contact_name'];
					$params['dropoff_contact_number']=$merchant_info['contact_phone'];
					$params['drop_address']=$drop_address;
					$params['dropoff_lat']=$merchant_info['latitude'];
					$params['dropoff_lng']=$merchant_info['lontitude'];
				}
								
				$db->insertData("{{driver_task}}",$params);
			}
		} //else echo 'no records';
	}	
	
	public static function addressToLatLong($address='')
	{		
		$protocol = isset($_SERVER["https"]) ? 'https' : 'http';
		if ($protocol=="http"){
			$api="http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address);
		} else $api="https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address);
		
		/*check if has provide api key*/
		$key=Yii::app()->functions->getOptionAdmin('drv_google_api');		
		if ( !empty($key)){
			$api="https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address)."&key=".urlencode($key);
		}	
					
		if (!$json=@file_get_contents($api)){
			//$json=$this->Curl($api,'');					
			$json=Yii::app()->functions->Curl($api,'');	
		}
			
		/*dump($api);
		dump($json);*/		
			
		if (!empty($json)){
			$json = json_decode($json);	
			if (isset($json->error_message)){
				return false;
			} else {				
				if($json->status=="OK"){					
					$lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
		            $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
				} else {
					$lat=''; $long='';
				}
	            return array(
	              'lat'=>$lat,
	              'long'=>$long
	            );
			}
		}			
		return false;
	}	
	
	public static function getDriverByStats($user_type='',$user_id='',$stats='',$transaction_date='',
	   $driver_status='active' , $team_id='', $driver_name='')
	{
				
		$db=new DbExt;
		
		$tracking_type=getOptionA("driver_tracking_options");
		
		$todays_date=date('Y-m-d');				
		$time_now=strtotime("-10 minutes");
		$and='';
		
		$time_30min=strtotime("+30 minutes");
		$time_30less=strtotime("-30 minutes");
		
		if ( $user_type=="merchant"){
			$and =" AND user_type=".self::q($user_type)." ";
			$and.=" AND user_id=".self::q($user_id)." ";									
		} else {
			$and =" AND user_type=".self::q($user_type)." ";
		}
				
		switch ($stats) {
			case "active":
				/*$and.=" AND on_duty ='1' ";
				$and.=" AND last_online >='$time_now' ";
				$and.=" AND last_login like '".$todays_date."%'";*/
				
				if ( $tracking_type==2){										
					$and.=" AND on_duty ='1' AND last_online >='$time_30less' AND  last_online<='$time_30min' ";
					$and.=" AND last_login like '".$todays_date."%'";
				} else {
					$and.=" AND on_duty ='1' ";
					$and.=" AND last_online >='$time_now' ";
					$and.=" AND last_login like '".$todays_date."%'";
				}
					
				
				break;
		
			case "offline":				
				/*$date_now=date("now",strtotime('-6 minutes'));
				$and.=" AND last_online <='$time_now' ";*/					
				if ( $tracking_type==2){
					$and.=" AND last_online <='$time_30less' ";
				} else {
					$and.=" AND last_online <='$time_now' ";
				}
			default:
				
				break;
		}
		
		$and.=" AND status=".self::q($driver_status)."";		
		
		if ($team_id>0){
			$and.=" AND team_id=".self::q($team_id)." ";
		}
		if (!empty($driver_name)){
			$and.=" AND first_name LIKE '%".$driver_name."%' ";
		}
		
		
		$stmt="
		SELECT a.*,
		(
		  select count(*)
		  from
		  {{driver_task}}
		  where
		  driver_id=a.driver_id
		  and 
		  delivery_date like '".$transaction_date."%'
		) as total_task
		FROM
		{{driver}} a
		WHERE 1
		$and
		ORDER BY first_name ASC
		";		
		/*dump($stats);
		dump($stmt);*/
		if ( $res = $db->rst($stmt)){	
			//dump($res);
			$data='';
			foreach ($res as $val) {		
						
				/*$val['is_online']=2;
				$last_login=date('Y-m-d',strtotime($val['last_login']));
				if ( $last_login==$todays_date && $val['on_duty']==1){
					if ( $val['last_online']>=$time_now){
					   $val['is_online']=1;
					}
				} */			
				
				$val['is_online']=2;
				$last_login=date('Y-m-d',strtotime($val['last_login']));
				if ( $last_login==$todays_date && $val['on_duty']==1){
					if ( $val['last_online']>=$time_now){
					   $val['is_online']=1;
					}
				} 			
				
				if($tracking_type==2){
					if ( $val['on_duty']==1){
					     $val['is_online']=1;
					}
				}
				
				if ($val['last_login']=="0000-00-00 00:00:00" || $val['last_login']=="" ){
					$last_seen='';
				} else $last_seen=PrettyDateTime::parse(new DateTime($val['last_login']));				
				$val['last_seen']=$last_seen;				
				$val['online_status']="online";

				$time_1=date('Y-m-d g:i:s a');			
				$time_2=date("Y-m-d g:i:s a",strtotime($val['last_login']));
				
				$time_diff=self::dateDifference($time_2,$time_1);		
				//dump($time_diff); dump($tracking_type);
				if ($time_diff){
					if ( $time_diff['days']>0){
						 $val['online_status']="lost_connection";
					}					
					if ( $time_diff['hours']>1){
						 $val['online_status']="lost_connection";
					}
					if ($tracking_type==1){
						if ( $time_diff['minutes']>5){
							 $val['online_status']="lost_connection";
						}		
					} else {
						if ( $time_diff['minutes']>15){
							 $val['online_status']="lost_connection";
						}		
					}			
				}
				
				$data[]=$val;
			}
			return $data;
		}
		return false;
	}
	
	public static function driverAppLogin($username='', $password='',$status='active')
	{
		$db=new DbExt;
		
		$stmt="SELECT * FROM
		{{driver}}
		WHERE
		username=".self::q($username)."
		AND
		password=".self::q(md5($password))."
		AND
		status='$status'
		LIMIT 0,1
		";		
		//dump($stmt);
		if ( $res=$db->rst($stmt)){
			return $res[0];
		}
		return false;
	}
	
    public static function generateRandomNumber($range=10) 
    {
	    $chars = "0123456789";	
	    srand((double)microtime()*1000000);	
	    $i = 0;	
	    $pass = '' ;	
	    while ($i <= $range) {
	        $num = rand() % $range;	
	        $tmp = substr($chars, $num, 1);	
	        $pass = $pass . $tmp;	
	        $i++;	
	    }
	    return $pass;
    }	
	
	public static function driverForgotPassword($email_address='')
	{
		$db=new DbExt;	
		$stmt="SELECT * FROM
		{{driver}}
		WHERE
		email=".self::q($email_address)."		
		LIMIT 0,1
		";		
		if ( $res=$db->rst($stmt)){
			return $res[0];
		}
		return false;
	}
	
	public static function getDriverByToken($token='')
	{
		if (empty($token)){
			return false;
		}
		$db=new DbExt;	
		$stmt="SELECT * FROM
		{{driver}}
		WHERE
		token=".self::q($token)."		
		LIMIT 0,1
		";		
		if ( $res=$db->rst($stmt)){
			return $res[0];
		}
		return false;
	}
	
	public static function driverStatusPretty($driver_name='',$status='')
	{		
		$msg='';		
		switch ($status) {
			
			case "sign":
			case "signature":
				$msg=$driver_name." ".("added a signature");
				break;
				break;
				
			case "failed":
				$msg=$driver_name." ".("marked the task as failed");
				break;
				
			case "cancelled":
				$msg=$driver_name." ".("marked the task as cancelled");
				break;
				
			case "declined":
				$msg=$driver_name." ".("declined the task");
				break;
				
			case "acknowledged":
				$msg=$driver_name." ".("accepted the task");
				break;
		
			case "started":	
			    $msg= $driver_name." ".("started this task");
			    break;
			    
			case "inprogress":    
			    $msg= $driver_name." ".("reached the destination");
			    break;
			    
			case "successful":    
			    $msg= $driver_name." ".("Completed the task successfully");
			    break;    
			    
			default:
				$msg=self::t("Status changed");
				break;
		}
		return $msg;
	}
	
	public static function getDriverTaskHistory($task_id='')
	{
		$db=new DbExt;	
		$stmt="SELECT * FROM
		{{order_history}}
		WHERE
		task_id=".self::q($task_id)."		
		AND status NOT IN ('assigned')
		ORDER BY id ASC
		";		
		if ( $res=$db->rst($stmt)){
			$data='';
			foreach ($res as $val) {
				$val['status']=self::t($val['status']);
				$val['status_raw']=$val['status'];
				$val['time']=Yii::app()->functions->timeFormat($val['date_created'],true);
				$val['date']=Yii::app()->functions->FormatDateTime($val['date_created'],false);
				
				if(!empty($val['remarks2'])){							
					$args=json_decode($val['remarks_args'],true);								
					if(is_array($args) && count($args)>=1){
						foreach ($args as $args_key=>$args_val) {
							$args[$args_key]=t($args_val);
						}
					}								
					$new_remarks=$val['remarks2'];								
					$new_remarks=Yii::t("default",$new_remarks,$args);								
					$val['remarks']=$new_remarks;
				}
				
				$data[]=$val;
			}
			return $data;
		}
		return false;
	}
	
	public static function getDriverTaskCalendar($driver_id='', $start='', $end='')
	{
		$db=new DbExt;	
		$stmt="SELECT 
		DISTINCT DATE_FORMAT(a.delivery_date,'%Y-%m-%d') as delivery_date		
		FROM
		{{driver_task}} a
		WHERE
		driver_id=".self::q($driver_id)."		
		AND
		delivery_date BETWEEN '$start' AND '$end'
		";				
		$db->qry("SET SQL_BIG_SELECTS=1");
		if ( $res=$db->rst($stmt)){
			return $res;
		}
		return false;
	}
	
	public static function getTotalTaskByDate($driver_id='',$date='')
	{
		$db=new DbExt;	
		$stmt="
		  SELECT count(*) as total
		  FROM
		  {{driver_task}}
		  WHERE
		  delivery_date LIKE '".$date."%'
		  AND
		  driver_id=".self::q($driver_id)."		
		";
		if ( $res=$db->rst($stmt)){
			return $res[0]['total'];
		}
		return 0;
	}
	
   public static function availableLanguages()
    {
    	$lang['en']='English';
    	$stmt="
    	SELECT * FROM
    	{{languages}}
    	WHERE
    	status in ('publish','published')
    	";
    	$db_ext=new DbExt; 
    	if ($res=$db_ext->rst($stmt)){
    		foreach ($res as $val) {
    			$lang[$val['lang_id']]=$val['language_code'];
    		}    		
    	}
    	return $lang;
    }   
    
    public static function notificationListPickup()
    {
    	$data['PICKUP']['REQUEST_RECEIVED']=array(
    	  'REQUEST_RECEIVED_PUSH',
    	  'REQUEST_RECEIVED_SMS',
    	  'REQUEST_RECEIVED_EMAIL'
    	);
    	$data['PICKUP']['DRIVER_STARTED']=array(
    	  'DRIVER_STARTED_PUSH',
    	  'DRIVER_STARTED_SMS',
    	  'DRIVER_STARTED_EMAIL'
    	);
    	$data['PICKUP']['DRIVER_ARRIVED']=array(
    	  'DRIVER_ARRIVED_PUSH',
    	  'DRIVER_ARRIVED_SMS',
    	  'DRIVER_ARRIVED_EMAIL'
    	);
    	$data['PICKUP']['SUCCESSFUL']=array(
    	  'SUCCESSFUL_PUSH',
    	  'SUCCESSFUL_SMS',
    	  'SUCCESSFUL_EMAIL'
    	);
    	$data['PICKUP']['FAILED']=array(
    	  'FAILED_PUSH',
    	  'FAILED_SMS',
    	  'FAILED_EMAIL'
    	);
    	$data['PICKUP']['NOTES']=array(
    	  'NOTES_PUSH',
    	  'NOTES_SMS',
    	  'NOTES_EMAIL'
    	);
    	$data['PICKUP']['PHOTO']=array(
    	  'PHOTO_PUSH',
    	  'PHOTO_SMS',
    	  'PHOTO_EMAIL'
    	);
    	return $data;
    } 	
    
    public static function notificationListDelivery()
    {
    	$data['DELIVERY']['REQUEST_RECEIVED']=array(
    	  'REQUEST_RECEIVED_PUSH',
    	  'REQUEST_RECEIVED_SMS',
    	  'REQUEST_RECEIVED_EMAIL'
    	);
    	$data['DELIVERY']['DRIVER_STARTED']=array(
    	  'DRIVER_STARTED_PUSH',
    	  'DRIVER_STARTED_SMS',
    	  'DRIVER_STARTED_EMAIL'
    	);
    	$data['DELIVERY']['DRIVER_ARRIVED']=array(
    	  'DRIVER_ARRIVED_PUSH',
    	  'DRIVER_ARRIVED_SMS',
    	  'DRIVER_ARRIVED_EMAIL'
    	);
    	$data['DELIVERY']['SUCCESSFUL']=array(
    	  'SUCCESSFUL_PUSH',
    	  'SUCCESSFUL_SMS',
    	  'SUCCESSFUL_EMAIL'
    	);
    	$data['DELIVERY']['FAILED']=array(
    	  'FAILED_PUSH',
    	  'FAILED_SMS',
    	  'FAILED_EMAIL'
    	);
    	$data['DELIVERY']['NOTES']=array(
    	  'NOTES_PUSH',
    	  'NOTES_SMS',
    	  'NOTES_EMAIL'
    	);
    	$data['DELIVERY']['PHOTO']=array(
    	  'PHOTO_PUSH',
    	  'PHOTO_SMS',
    	  'PHOTO_EMAIL'
    	);
    	return $data;
    } 	

    public static function tagAvailableList()
    {
    	return array(
    	  Driver::t('Available Tags'),
    	  'TaskID','CustomerName',
    	  'CustomerAddress','DeliveryDateTime',
    	  'PickUpDateTime','DriverName',
    	  'OrderNo','CompanyName','CompletedTime',
    	  'DriverUsername','DriverEmail'
    	);
    }   
    
    public static function getNotifications($user_type='',$user_id='' ,$viewed=2)
    {
    	$date_now=date("Y-m-d");
    	if ( $user_type=="admin"){			
			$and='';
		} else {
			$and =" AND user_type=".self::q($user_type)."  ";
			$and.=" AND user_id=".self::q($user_id)."  ";
		}
    	$db_ext=new DbExt; 
    	$stmt="
    	SELECT a.* FROM
    	{{order_history}} a
    	WHERE
    	notification_viewed='$viewed'
    	AND 
    	driver_id > 0
    	AND
    	date_created LIKE '".$date_now."%'
    	AND
    	task_id = (
    	  select task_id 
    	  from
    	  {{driver_task}}
    	  where 
    	  task_id=a.task_id
    	  $and    	  
    	  limit 0,1
    	)    	
    	LIMIT 0,3
    	";    	
    	//dump($stmt);
    	if ($res=$db_ext->rst($stmt)){
    		return $res;
    	}
    	return false;
    }
    
    public static function base30_to_jpeg($base30_string, $output_file) {
	
	    $data = str_replace('image/jsignature;base30,', '', $base30_string);
	    $converter = new jSignature_Tools_Base30();
	    $raw = $converter->Base64ToNative($data);
	//Calculate dimensions
		$width = 0;
		$height = 0;
		foreach($raw as $line)
		{
		    if (max($line['x'])>$width)$width=max($line['x']);
		    if (max($line['y'])>$height)$height=max($line['y']);
		}
		
		// Create an image
		    $im = imagecreatetruecolor($width+20,$height+20);
				
		// Save transparency for PNG
		    imagesavealpha($im, true);
		// Fill background with transparency
		    $trans_colour = imagecolorallocatealpha($im, 255, 255, 255, 127);
		    imagefill($im, 0, 0, $trans_colour);
		// Set pen thickness
		    imagesetthickness($im, 2);
		// Set pen color to black
		    $black = imagecolorallocate($im, 0, 0, 0);   
		// Loop through array pairs from each signature word
		    for ($i = 0; $i < count($raw); $i++)
		    {
		        // Loop through each pair in a word
		        for ($j = 0; $j < count($raw[$i]['x']); $j++)
		        {
		            // Make sure we are not on the last coordinate in the array
		            if ( ! isset($raw[$i]['x'][$j])) 
		                break;
		            if ( ! isset($raw[$i]['x'][$j+1])) 
		            // Draw the dot for the coordinate
		                imagesetpixel ( $im, $raw[$i]['x'][$j], $raw[$i]['y'][$j], $black); 
		            else
		            // Draw the line for the coordinate pair
		            imageline($im, $raw[$i]['x'][$j], $raw[$i]['y'][$j], $raw[$i]['x'][$j+1], $raw[$i]['y'][$j+1], $black);
		        }
		    } 
	
	    //Create Image
	    $ifp = fopen($output_file, "wb"); 
	    imagepng($im, $output_file);
	    fclose($ifp);  
	    imagedestroy($im);
	    return $output_file; 
	}    
	
	public static function priceSettings()
	{
		 $admin_decimal_separator=getOptionA('admin_decimal_separator');
         $admin_decimal_place=getOptionA('admin_decimal_place');
         $admin_currency_position=getOptionA('admin_currency_position');
         $admin_thousand_separator=getOptionA('admin_thousand_separator');
         
         return array(
	        'decimal_place'=> strlen($admin_decimal_place)>0?$admin_decimal_place:2,
		    'currency_position'=>!empty($admin_currency_position)?$admin_currency_position:'left',
		    'currency_set'=>getCurrencyCode(),
		    'thousand_separator'=>!empty($admin_thousand_separator)?$admin_thousand_separator:'',
		    'decimal_separator'=>!empty($admin_decimal_separator)?$admin_decimal_separator:'.',
	     );
	}
	
	public static function getDriverNotifications($driver_id='')
	{
		$db_ext=new DbExt; 
		$stmt="SELECT * FROM
		{{driver_pushlog}}
		WHERE
		driver_id=".self::q($driver_id)."
		AND
		status='process'
		AND
		is_read='2'
		ORDER BY date_created DESC
		LIMIT 0,10
		";
		if($res=$db_ext->rst($stmt)){
		   return $res;	
		}
		return false;
	}
	
	public static function prettyDate($date='',$show_time=true)
	{
		if(!empty($date)){
			return Yii::app()->functions->translateDate(Yii::app()->functions->FormatDateTime($date,$show_time));
		}		
		return '';	
	}
	
	public static function sendDriverNotification($key='',$info='',$run_push=true)
	{				

		if(!is_array($info) && count($info)<=0){
			return false;
		}
		
		if($info['driver_id']<=0){
			self::sendPushToAssigmentDriver($key,$info['task_id']);
			return false;
		}
				
		/*check if driver is online */
		$driver_send_push_to_online=getOptionA('driver_send_push_to_online');		
		if ( $driver_send_push_to_online==1){						
			if ( !$driver_inf=self::isDriverOnline($info['driver_id'])){
				//echo 'not online';
				return ;
			} 
		}
					
		$db_ext=new DbExt; 			
		
		$key_value=getOptionA($key."_PUSH");				
		if ($key_value==1 && $info['enabled_push']==1){
			$push_message=getOptionA($key."_PUSH_TPL");
			$push_message=self::smarty('TaskID',$info['task_id'],$push_message);
			$push_message=self::smarty('CustomerName',$info['customer_name'],$push_message);
			$push_message=self::smarty('CustomerAddress',$info['delivery_address'],$push_message);
			$push_message=self::smarty('DeliveryDateTime',self::prettyDate($info['delivery_date']),$push_message);
			$push_message=self::smarty('PickUpDateTime',self::prettyDate($info['delivery_date']),$push_message);
			$push_message=self::smarty('DriverName',$info['driver_name'],$push_message);
			$push_message=self::smarty('OrderNo',$info['order_id'],$push_message);
			$push_message=self::smarty('CompanyName',getOptionA('website_title'),$push_message);
			//$push_message=self::smarty('CompletedTime',$info[''],$push_message);				
			$params=array(
			  'device_platform'=>isset($info['device_platform'])?$info['device_platform']:'',
			  'device_id'=>isset($info['device_id'])?$info['device_id']:'',
			  'push_title'=>self::t(str_replace("_",' ',$key)),			  
			  'push_message'=>$push_message,
			  'actions'=>$key,
			  'order_id'=>isset($info['order_id'])?$info['order_id']:'',
			  'driver_id'=>isset($info['driver_id'])?$info['driver_id']:'',
			  'task_id'=>isset($info['task_id'])?$info['task_id']:'',
			  'date_created'=>date('c'),
			  'ip_address'=>$_SERVER['REMOTE_ADDR'],
			  'user_type'=>$info['user_type'],
			  'user_id'=>$info['user_id']
			);					
			$db_ext->insertData("{{driver_pushlog}}",$params);			
			$push_id=Yii::app()->db->getLastInsertID();
			if($run_push){
			   self::RunPush( $push_id );
			}
		}
		
		//EMAIL
		$key_value=getOptionA($key."_SMS");		
		if ($key_value==1 && $info['driver_phone']!=""){
		   $sms_message=getOptionA($key."_SMS_TPL");		   
		   $sms_message=self::smarty('TaskID',$info['task_id'],$sms_message);
		   $sms_message=self::smarty('CustomerName',$info['customer_name'],$sms_message);
		   $sms_message=self::smarty('CustomerAddress',$info['delivery_address'],$sms_message);
		   $sms_message=self::smarty('DeliveryDateTime',self::prettyDate($info['delivery_date']),$sms_message);
		   $sms_message=self::smarty('PickUpDateTime',self::prettyDate($info['delivery_date']),$sms_message);
		   $sms_message=self::smarty('DriverName',$info['driver_name'],$sms_message);
		   $sms_message=self::smarty('OrderNo',$info['order_id'],$sms_message);
		   $sms_message=self::smarty('CompanyName',getOptionA('website_title'),$sms_message);
		   /*dump($sms_message);
		   dump($info['driver_phone']);*/
		   if ( $send_sms= Yii::app()->functions->sendSMS($info['driver_phone'],$sms_message)){		   	    
		   	    $params=array(
		   	      'broadcast_id'=>"999999999",
				  'contact_phone'=>$info['driver_phone'],
				  'sms_message'=>$sms_message,
				  'status'=>isset($send_sms['msg'])?$send_sms['msg']:'',
				  'gateway_response'=>isset($send_sms['raw'])?$send_sms['raw']:'',
				  'gateway'=>$send_sms['sms_provider'],
				  'date_created'=>date('c'),
				  'ip_address'=>$_SERVER['REMOTE_ADDR']
				);				
				$db_ext->insertData("{{sms_broadcast_details}}",$params);
				
				unset($params['broadcast_id']);
				$params['user_type']=$info['user_type'];
				$params['user_id']=$info['user_id'];
				$db_ext->insertData("{{driver_sms_logs}}",$params);
		   }
		}
		
		//EMAIL
		$key_value=getOptionA($key."_EMAIL");		
		if ($key_value==1 && $info['driver_email']!=""){
		   $email_message=Driver::t(getOptionA($key."_EMAIL_TPL"));		   
		   $email_message=self::smarty('TaskID',$info['task_id'],$email_message);
		   $email_message=self::smarty('CustomerName',$info['customer_name'],$email_message);
		   $email_message=self::smarty('CustomerAddress',$info['delivery_address'],$email_message);
		   $email_message=self::smarty('DeliveryDateTime',self::prettyDate($info['delivery_date']),$email_message);
		   $email_message=self::smarty('PickUpDateTime',self::prettyDate($info['delivery_date']),$email_message);
		   $email_message=self::smarty('DriverName',$info['driver_name'],$email_message);
		   $email_message=self::smarty('OrderNo',$info['order_id'],$email_message);
		   $email_message=self::smarty('CompanyName',getOptionA('website_title'),$email_message);		   
		   $stats=sendEmail($info['driver_email'],'', self::t(str_replace("_",' ',$key)) ,$email_message);
		   
		   /*logs email*/		   
		   $db_ext->insertData("{{email_logs}}",array(
		      'email_address'=>$info['driver_email'],
		      'sender'=>getOptionA('global_admin_sender_email'),
		      'subject'=>str_replace("_",' ',$key),
		      'content'=>$email_message,
		      'status'=>$stats==true?"send":"failed",
		      'date_created'=>self::dateNow(),
		      'ip_address'=>$_SERVER['REMOTE_ADDR'],
		      'module_type'=>"driver",
		      'user_type'=>$info['user_type'],
		      'user_id'=>$info['user_id']
		   ));		   
		}
		
	}
	
	public static function smarty($search='',$value='',$subject='')
	{
		return str_replace("[".$search."]",$value,$subject);
	}
	
	public static function getCustomerInformationByOrderID($order_id='')
	{
		$db=new DbExt; 
		$db->qry("SET SQL_BIG_SELECTS=1");
		$stmt="
		SELECT * FROM
		{{driver_order_view}}
		WHERE
		order_id=".self::q($order_id)."
		AND
		status='active'
		LIMIT 0,1
		";
		if ($res=$db->rst($stmt)){
			return $res[0];
		}
		return false;
	}
	
	public static function sendNotificationCustomer($key='',$info='')
	{
		
		/*dump($key);
		dump($info);*/
		
		$db_ext=new DbExt; 
		
		$key_is_enabled=getOptionA($key."_PUSH");			
		//dump($key_is_enabled);
		if ( $key_is_enabled==1 && $info['order_id']>0){			
			$message=getOptionA($key."_PUSH_TPL");			
			$message=self::smarty('TaskID',$info['task_id'],$message);
			$message=self::smarty('CustomerName',$info['customer_name'],$message);
			$message=self::smarty('CustomerAddress',$info['delivery_address'],$message);
			$message=self::smarty('DeliveryDateTime',self::prettyDate($info['delivery_date']),$message);
			$message=self::smarty('PickUpDateTime',self::prettyDate($info['delivery_date']),$message);
			$message=self::smarty('DriverName',$info['driver_name'],$message);
			$message=self::smarty('OrderNo',$info['order_id'],$message);
			$message=self::smarty('CompanyName',getOptionA('website_title'),$message);						
			if ( self::hasModuleAddon('mobileapp')){								
				if ( $client=self::getCustomerInformationByOrderID($info['order_id'])){		
					
					$push_title =  str_replace("_"," ",$key);					
					$push_title = self::t($push_title);
								
					$params=array(
					  'client_id'=>$client['client_id'],
					  'client_name'=>$client['client_name'],
					  'device_platform'=>$client['device_platform'],
					  'device_id'=>$client['device_id'],
					  'push_title'=>$push_title,
					  'push_message'=>$message,
					  'date_created'=>date('c'),
					  'ip_address'=>$_SERVER['REMOTE_ADDR']
					);					
					$db_ext->insertData("{{mobile_push_logs}}",$params);
				}
			}			
		}
				
		$key_is_enabled=getOptionA($key."_EMAIL");					
		if ( $key_is_enabled==1 && !empty($info['email_address'])){
			$message=getOptionA($key."_EMAIL_TPL");		
			$message=self::smarty('TaskID',$info['task_id'],$message);
			$message=self::smarty('CustomerName',$info['customer_name'],$message);
			$message=self::smarty('CustomerAddress',$info['delivery_address'],$message);
			$message=self::smarty('DeliveryDateTime',self::prettyDate($info['delivery_date']),$message);
			$message=self::smarty('PickUpDateTime',self::prettyDate($info['delivery_date']),$message);
			$message=self::smarty('DriverName',$info['driver_name'],$message);
			$message=self::smarty('OrderNo',$info['order_id'],$message);
			$message=self::smarty('CompanyName',getOptionA('website_title'),$message);	
			//dump($message);	

			$email_subject=str_replace("_"," ",$key);
			$email_subject=self::t($email_subject);
					
			$stats = sendEmail($info['email_address'],'',
			$email_subject
			,$message);
			
			/*logs email*/		   
		   $db_ext->insertData("{{email_logs}}",array(
		      'email_address'=>$info['email_address'],
		      'sender'=>getOptionA('global_admin_sender_email'),
		      'subject'=>str_replace("_"," ",$key),
		      'content'=>$message,
		      'status'=>$stats==true?"send":"failed",
		      'date_created'=>self::dateNow(),
		      'ip_address'=>$_SERVER['REMOTE_ADDR'],
		      'module_type'=>"driver",
		      'user_type'=>$info['user_type'],
		      'user_id'=>$info['user_id']
		   ));		   
			
		}
		
		$key_is_enabled=getOptionA($key."_SMS");		
		if ( $key_is_enabled==1 && $info['contact_number']!=""){
			$message=getOptionA($key."_SMS_TPL");		
			$message=self::smarty('TaskID',$info['task_id'],$message);
			$message=self::smarty('CustomerName',$info['customer_name'],$message);
			$message=self::smarty('CustomerAddress',$info['delivery_address'],$message);
			$message=self::smarty('DeliveryDateTime',self::prettyDate($info['delivery_date']),$message);
			$message=self::smarty('PickUpDateTime',self::prettyDate($info['delivery_date']),$message);
			$message=self::smarty('DriverName',$info['driver_name'],$message);
			$message=self::smarty('OrderNo',$info['order_id'],$message);
			$message=self::smarty('CompanyName',getOptionA('website_title'),$message);								
			if ( $send_sms= Yii::app()->functions->sendSMS($info['contact_number'],$message)){		   	    
		   	    $params=array(
		   	      'broadcast_id'=>"999999999",
				  'contact_phone'=>!empty($info['driver_phone'])?$info['driver_phone']:'',
				  'sms_message'=>$message,
				  'status'=>isset($send_sms['msg'])?$send_sms['msg']:'',
				  'gateway_response'=>isset($send_sms['raw'])?$send_sms['raw']:'',
				  'gateway'=>$send_sms['sms_provider'],
				  'date_created'=>date('c'),
				  'ip_address'=>$_SERVER['REMOTE_ADDR']
				);				
				$db_ext->insertData("{{sms_broadcast_details}}",$params);
				
				unset($params['broadcast_id']);
				$params['user_type']=$info['user_type'];
				$params['user_id']=$info['user_id'];
				$db_ext->insertData("{{driver_sms_logs}}",$params);
		   }
		}		
	}
	
	public static function RunPush( $push_id='')
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
		
		$and='';
		if(!empty($push_id)){
			$and=" AND push_id=".self::q($push_id)." ";
		}
				
		$stmt="
		SELECT * FROM
		{{driver_pushlog}}
		WHERE
		status='pending'
		$and
		ORDER BY date_created ASC
		LIMIT 0,1
		";
		if ( $res=$db->rst($stmt)){
			foreach ($res as $val) {				
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
					   
					   //dump($message);
					   
					   if ( strtolower($val['device_platform']) =="android"){
						   $resp=AndroidPush::sendPush($api_key,$val['device_id'],$message);
						   if(is_array($resp) && count($resp)>=1){
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
				$db->updateData("{{driver_pushlog}}",$params,'push_id',$push_id);
				
			}
		} //else echo 'no record to process';
	}	
	
	public static function cleanString($text='')
	{
		if(!empty($text)){
			return stripslashes($text);
		}
		return ;
			
	}
	
	public static function generateReports($chart_type='',$time='',$team='',$driver='',$chart_option='',
	$start_date='' , $end_date='' )
	{
	
		$db=new DbExt;
		$and='';
		switch ($time) {			
			case "week":
				$start= date('Y-m-d', strtotime("-7 day") );
			    $end=date("Y-m-d", strtotime("+1 day"));
				$and.= " AND delivery_date BETWEEN '$start' AND '$end' ";
				break;
								
			case "month":	
			    $start= date('Y-m-d', strtotime("-30 day") );
			    $end=date("Y-m-d", strtotime("+1 day"));
				$and.= " AND delivery_date BETWEEN '$start' AND '$end' ";
			   break;
			   
			case "custom":		
			   $and.= " AND delivery_date BETWEEN '$start_date' AND '$end_date' ";
			   break;
			   
			default:
				break;
		}
				
		if ($team>0){			
			$and.=" AND team_id=".self::q($team)." ";
		}
		if($driver>0){
			$and.=" AND driver_id=".self::q($driver)." ";
		}
		
		$and.=" AND driver_id <>'' ";
		
		$user_type=self::getUserType();		
		if ( $user_type=="merchant"){
			$user_id=self::getUserId();
			$and.=" AND user_type='merchant' AND user_id=".self::q($user_id)." ";						
		}
		
		
		$group="GROUP BY DATE_FORMAT(delivery_date,'%Y-%m-%d'),status";
		if ( $chart_option=="agent"){
			$group="GROUP BY driver_name,status";
		}
		
		if ( $chart_type=="task_completion"){
			$stmt="
			SELECT sum(total_w_tax)as total_order_amount,
			driver_id,
			DATE_FORMAT(a.delivery_date,'%Y-%m-%d') as delivery_date ,a.status,
			count(*) as total,
			(
			  select concat(first_name,' ',last_name)
			  from
			  {{driver}}
			  where
			  driver_id=a.driver_id
			) as driver_name
			
			
			FROM {{driver_task_view}} a
			WHERE 1
			$and
			$group
			ORDER BY delivery_date ASC
			";
		} else {
			$stmt="
			";
		}
		
		/*dump($chart_type);
		dump($stmt);*/
		
		$db->qry("SET SQL_BIG_SELECTS=1");
		
		if ( $res=$db->rst($stmt)){			
			//dump($res);
			return $res;
		}
		return false;
	}
	
	public static function updateOption($option_name='', $option_value='')
	{
		$user_type = self::getUserType();		
		switch ($user_type) {
			case "admin":
				//Yii::app()->functions->updateOptionAdmin($option_name,$option_value);
				self::updateOptionAdmin($option_name,$option_value);
				break;
		
			default:
				$user_id= self::getUserId();
				Yii::app()->functions->updateOption($option_name,$option_value,$user_id);
				break;
		}
	}
	
	public static function getOption($option_name='', $user_type='' , $user_id='')
	{
		if (empty($user_type)){
		    $user_type = self::getUserType();
		}
		switch ($user_type) {
			case "admin":
				return Yii::app()->functions->getOptionAdmin($option_name);
				break;
		
			default:
				if(empty($user_id)){
				   $user_id= self::getUserId();
				}
				return Yii::app()->functions->getOption($option_name,$user_id);
				break;
		}
	}
	
	public static function certificatePath()
	{
		return $path_to_upload=Yii::getPathOfAlias('webroot')."/upload/driver_certificate";
	}
	
	public static function isDriverOnline($driver_id='')
	{
		$db=new DbExt;
		$todays_date=date('Y-m-d');			
		//$time_now = time() - 200;
		$time_now=strtotime("-10 minutes");
		$and='';
		
		$and.=" AND on_duty ='1' ";
        $and.=" AND last_online >='$time_now' ";
        $and.=" AND last_login like '".$todays_date."%'";
        
        $stmt="SELECT * FROM
        {{driver}}
        WHERE driver_id=".self::q($driver_id)."
        $and
        LIMIT 0,1
        ";        
        if ( $res=$db->rst($stmt)){
        	return $res;
        }
        return false;
	}
	
	public static function receiptRow($label='', $value='')
	{
		$html='';
		$html.="<tr>";
         $html.="<td>".Driver::t($label)."</td>";
         $html.="<td>".stripslashes($value)."</td>";
        $html.="</tr>";
        return $html;
	}
	
	public static function getUnAssignedDriver($task_id='')
	{
		$db=new DbExt;
		$stmt="
		SELECT * FROM
		{{driver_assignment}}
		WHERE
		status='pending'
		AND task_id=".self::q($task_id)."
		ORDER BY assignment_id ASC
		LIMIT 0,1
		";
		if ( $res=$db->rst($stmt)){
			return $res[0];
		}
		return false;
	}
	
    public static function getUnAssignedDriver2($task_id='')
	{
		$db=new DbExt;
		$stmt="
		SELECT * FROM
		{{driver_assignment}}
		WHERE
		status='pending'
		AND task_id=".self::q($task_id)."
		ORDER BY assignment_id ASC		
		";
		//LIMIT 0,5
		if ( $res=$db->rst($stmt)){
			return $res;
		}
		return false;
	}	
	
	public static function getAssignmentByDriverTaskID($driver_id='',$task_id='')
	{
		$db=new DbExt;
		$stmt="
		SELECT * FROM
		{{driver_assignment}}
		WHERE
		driver_id=".self::q($driver_id)."
		AND task_id=".self::q($task_id)."		
		LIMIT 0,1
		";
		if ( $res=$db->rst($stmt)){
			return $res[0];
		}
		return false;
	}	
	
	public static function getTaskByDriverNTask($task_id='', $driver_id='')
	{
		$res=''; $res2='';
		
		$db=new DbExt;
		$stmt="
		SELECT a.*,a.driver_id as driver_id_task
		 FROM
		{{driver_task}} a
		WHERE
		task_id=".self::q($task_id)."
		LIMIT 0,1
		";		
		if ( $res=$db->rst($stmt)){
			$res=$res[0];			
			$stmt2="
			SELECT 
			b.driver_id,
			concat(b.first_name,' ',b.last_name) as driver_name,
			b.device_id,
			b.phone as driver_phone,
			b.email as driver_email,
			b.device_platform,
			b.enabled_push,
			b.location_lat as driver_lat,
			b.location_lng as driver_lng
			FROM {{driver}} b
			WHERE
			driver_id=".self::q($driver_id)."
			LIMIT 0,1
			";
			if($res2=$db->rst($stmt2)){
			  $res2=$res2[0];
			  //dump($res2);
			}			
			$merge_data=array_merge( (array) $res, (array) $res2);
			return $merge_data;
		}
		return false;
	}

	public static function getTaskByDriverIDWithAssigment($driver_id='',$delivery_date='')
	{
		$db=new DbExt;
		$db->qry("SET SQL_BIG_SELECTS=1");
					
		$or="
		OR task_id IN (
		  select task_id 
		  from
		  {{driver_assignment}}
		  where
		  task_id=a.task_id
		  and
		  driver_id=".self::q($driver_id)."
		  and
		  status='process'
		  and
		  task_status='unassigned'
		)
		";
		
		$stmt="SELECT a.* FROM
		{{driver_task_view}} a
		WHERE
		driver_id=".self::q($driver_id)."
		AND
		delivery_date LIKE '".$delivery_date."%'	
		$or
		ORDER BY delivery_date ASC
		";
		
		if(isset($_GET['debug'])){
		   dump($stmt);	
		}
		if($res=$db->rst($stmt)){
		   return $res;
		}	
		return false;
	}			
	
	public static function sendNotificationToUnAssignDriver($driver_id='',$task_id='' ,$user_type='', $user_id='')
	{
		$enabled=Driver::getOption('driver_enabled_auto_assign', $user_type , $user_id);
		$AUTO_ASSIGN_ACCEPTED=getOptionA('AUTO_ASSIGN_ACCEPTED');
		if($enabled===1 && $AUTO_ASSIGN_ACCEPTED==1){
			$db=new DbExt;
			$stmt="SELECT * FROM
			{{driver_assignment}}
			WHERE
			task_id=".self::q($task_id)."
			AND
			driver_id NOT IN ('$driver_id')
			";
			if($res=$db->rst($stmt)){
			   foreach ($res as $val) {
			   	   
			   }
			}
		}
	}
	
	public static function updateOrderStatus($order_id='', $status='')
	{		
		if (is_numeric($order_id) && !empty($status)){
			$db=new DbExt;
			$params=array(
			  'status'=>$status,
			  'date_modified'=>date('c'),
			  'ip_address'=>$_SERVER['REMOTE_ADDR']
			);
			$db->updateData("{{order}}",$params,'order_id',$order_id);
		}
	}
	
	public static function updateLastOnline($driver_id='')
	{
		$params=array(    	 
    	  'last_online'=>strtotime("now"),
    	  'ip_address'=>$_SERVER['REMOTE_ADDR']
    	);
    	$db=new DbExt;
    	$db->updateData("{{driver}}",$params,'driver_id',$driver_id);
	}
	
    public static function getTaskByOrderID($order_id='')
	{
		$db=new DbExt;	
		$stmt="
		  SELECT * FROM
		  {{driver_task_view}}
		  WHERE
		  order_id = ".self::q($order_id)."		  
		";
		if ( $res=$db->rst($stmt)){
			return $res[0];
		}
		return false;
	}	
	
	public static function sendPushToAssigmentDriver($key='',$task_id='')
	{		
		if (empty($task_id) && empty($key)){
			return ;
		}
		if ( $key=="UPDATE_TASK" || $key=="CANCEL_TASK"){
			$db=new DbExt;	
			 $stmt="
			 SELECT * FROM
			 {{driver_assignment}}
			 WHERE
			 task_id=".self::q($task_id)."
			 ORDER BY assignment_id ASC
			 ";
			 if($res=$db->rst($stmt)){
			 	foreach ($res as $val) {
			 		$task_id=$val['task_id'];
			 		$driver_id=$val['driver_id'];
			 		$task_info=Driver::getTaskByDriverNTask($task_id,$driver_id);
			 		Driver::sendDriverNotification($key,$task_info,false);	
			 	}
			 }
		}
	}
	
	public static function driverStatusPretty2($driver_name='',$status='')
	{		
		$msg='';		
		switch ($status) {
			
			case "sign":
			case "signature":
				$msg="{driver_name} added a signature";
				break;
				break;
				
			case "failed":				
				$msg="{driver_name} marked the task as failed";
				break;
				
			case "cancelled":				
				$msg="{driver_name} marked the task as cancelled";
				break;
				
			case "declined":				
				$msg="{driver_name} declined the task";
				break;
				
			case "acknowledged":				
				$msg="{driver_name} accepted the task";
				break;
		
			case "started":				    
			    $msg="{driver_name} started this task";
			    break;
			    
			case "inprogress":    			    
			    $msg="{driver_name} reached the destination";
			    break;
			    
			case "successful":    			    
			    $msg="{driver_name} Completed the task successfully";
			    break;    
			    
			default:
				$msg=self::t("Status changed");
				break;
		}
		return $msg;
	}	
	
    public static function getUnAssignedDriver3($task_id='')
	{
		$db=new DbExt;
		$stmt="
		SELECT * FROM
		{{driver_assignment}}
		WHERE		
		task_id=".self::q($task_id)."
		ORDER BY assignment_id ASC		
		";
		//LIMIT 0,10
		if ( $res=$db->rst($stmt)){
			return $res;
		}
		return false;
	}	

	public static function getDriverByUsername($username='', $driver_id='')
	{
		$and='';
		if (!empty($driver_id)){
			$and.=" AND driver_id!='".$driver_id."' ";
		}
		$db=new DbExt;
		$stmt="
		SELECT * FROM
		{{driver}}
		WHERE		
		username=".self::q($username)."
		$and
		LIMIT 0,1
		";		
		//dump($stmt);
		if ( $res=$db->rst($stmt)){
			return $res;
		}
		return false;
	}
	
	public static function getDriverByEmail($email='', $driver_id='')
	{
		$and='';
		if (!empty($driver_id)){
			$and.=" AND driver_id!='".$driver_id."' ";
		}
		$db=new DbExt;
		$stmt="
		SELECT * FROM
		{{driver}}
		WHERE		
		email=".self::q($email)."
		$and
		LIMIT 0,1
		";		
		//dump($stmt);
		if ( $res=$db->rst($stmt)){
			return $res;
		}
		return false;
	}	
	
    public static function merchantList()
    {
    	$data='';
    	$DbExt=new DbExt;
    	$stmt="SELECT * FROM
    	{{merchant}}
    	WHERE status in ('active')
    	ORDER BY restaurant_name ASC
    	";    	
    	//$data[-9999]=t("Select merchant");    	
    	if ($res=$DbExt->rst($stmt)){    		
    		foreach ($res as $val) {      			
			    $data[$val['merchant_id']]=ucwords(stripslashes($val['restaurant_name']));
			}
			return $data;
    	}
    	return false;
    }	
    
    public static function latToAdress($lat='' , $lng='')
	{
		$lat_lng="$lat,$lng";
		$protocol = isset($_SERVER["https"]) ? 'https' : 'http';
		if ($protocol=="http"){
			$api="http://maps.googleapis.com/maps/api/geocode/json?latlng=".urlencode($lat_lng);
		} else $api="https://maps.googleapis.com/maps/api/geocode/json?latlng=".urlencode($lat_lng);
		
		/*check if has provide api key*/
		$key=Yii::app()->functions->getOptionAdmin('google_geo_api_key');		
		if ( !empty($key)){
			$api="https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($lat_lng)."&key=".urlencode($key);
		}	
		
		//$api.="&language=ar";
					
		if (!$json=@file_get_contents($api)){
			$json=Yii::app()->functions->Curl($api,'');
		}
		
		if (isset($_GET['debug'])){
			dump($api);		
			dump($json);    
		}
		
		$address_out='';
			
		if (!empty($json)){			
			$results = json_decode($json,true);				
			$parts = array(
		      'address'=>array('street_number','route'),
		      //'address'=>array('street_number'),
		      //'city'=>array('locality'),
		      'city'=>array('locality','political','sublocality'),
		      'state'=>array('administrative_area_level_1'),
		      'zip'=>array('postal_code'),
		      'country'=>array('country'),
		    );		    
		    if (!empty($results['results'][0]['address_components'])) {
		      $ac = $results['results'][0]['address_components'];
		      foreach($parts as $need=>$types) {
		        foreach($ac as &$a) {		          
			          /*dump($need);
			          dump($types);
			          dump($a)	;*/
			          /*if (in_array($a['types'][0],$types)) $address_out[$need] = $a['long_name'];
			          elseif (empty($address_out[$need])) $address_out[$need] = '';*/
			          if (in_array($a['types'][0],$types)){
			          	  if (in_array($a['types'][0],$types)){
			          	  	  if($need=="address"){
			          	  	  	  if(isset($address_out[$need])) {
			          	  	  	     $address_out[$need] .= " ".$a['long_name'];
			          	  	  	  } else $address_out[$need]= $a['long_name'];
			          	  	  } else $address_out[$need] = $a['long_name'];			          	  	  
			          	  }
			          } elseif (empty($address_out[$need])) $address_out[$need] = '';	
		        }
		      }
		      
		      if(!empty($results['results'][0]['formatted_address'])){
		         $address_out['formatted_address']=$results['results'][0]['formatted_address'];
		      }
		      
		      return $address_out;
		    } 				
		}			
		return false;
	}    
	
    public static function getMerchantByID($merchant_id='')
	{
		$DbExt=new DbExt;
		$stmt="SELECT *
		 FROM
		{{view_merchant}}
		WHERE
		merchant_id='".$merchant_id."'
		LIMIT 0,1
		";
		if ( $res=$DbExt->rst($stmt)){
			return $res[0];
		}
		return false;
	}		
	
	public static function dateNow()
	{
		return date('Y-m-d G:i:s');
	}
	
	public static function getNotes($task_id='')
	{
		$DbExt=new DbExt;
		$stmt="SELECT *
		 FROM
		{{order_history}}
		WHERE
		task_id='".$task_id."'
		AND
		status='note'
		ORDER BY id ASC		
		";
		if ( $res=$DbExt->rst($stmt)){
			unset($DbExt);
			return $res;
		}
		unset($DbExt);
		return false;
	}
	
	public static function getNotesByID($id='')
	{
		$DbExt=new DbExt;
		$stmt="SELECT *
		 FROM
		{{order_history}}
		WHERE
		id='".$id."'
		LIMIT 0,1		
		";
		if ( $res=$DbExt->rst($stmt)){
			unset($DbExt);
			return $res[0];
		}
		unset($DbExt);
		return false;
	}	
	
	public static function getTotalNotes($task_id='')
	{
		$DbExt=new DbExt;
		$stmt="SELECT count(*) as total
		FROM
		{{order_history}}
		WHERE
		task_id='".$task_id."'
		AND
		status='note'		
		";
		if ( $res=$DbExt->rst($stmt)){
			unset($DbExt);
			if ($res[0]['total']>0){
			    return $res[0];
			} 
		}
		unset($DbExt);
		return false;
	}
	
    public static function getTaskDistance($lat1='',$lon1='', $lat2='',$lon2='',
    $transport_type='')
    {    	 
    	 $use_curl=getOptionA('google_use_curl');    	
    	 $key=Yii::app()->functions->getOptionAdmin('google_geo_api_key');
    	 
    	 $units_params='imperial';    	 
    	 
    	 $home_search_unit_type=getOptionA('home_search_unit_type');    	 
    	 if(!empty($home_search_unit_type)){
    	 	if($home_search_unit_type=="km"){
    	 	   $units_params='metric';
    	 	} 
    	 }
    	 
    	 switch ($transport_type) {
    	 	case "truck":
    	 	case "car":
    	 	case "scooter":
    	 		$method='driving';
    	 		break;
    	 
    	 	case "bicycle":    	 		
    	 		$method='bicycling';
    	 		break;
    	 			
    	    case "walk":    	 		
    	 		$method='walking';
    	 		break;
    	 				
    	 	default:
    	 		$method='driving';
    	 		break;
    	 }
    	     	 
    	 $url="https://maps.googleapis.com/maps/api/distancematrix/json";
	  	 $url.="?origins=".urlencode("$lat1,$lon1");
	  	 $url.="&destinations=".urlencode("$lat2,$lon2");
	  	 $url.="&mode=".urlencode($method);    	  
	  	 $url.="&units=".urlencode($units_params);
	  	 if(!empty($key)){
	  	 	$url.="&key=".urlencode($key);
	  	 }
	  	 
	  	 //$url.="&language=en";
	  	 
	  	 //dump($url);
	  	 
	  	 if ($use_curl==2){
	  	 	$data = Yii::app()->functions->Curl($url);
	  	 } else $data = @file_get_contents($url);
	  	 
	  	 /*if (!$data=@file_get_contents($url)){
			$data=Yii::app()->functions->Curl($url,'');
		 }*/
		
	  	 $data = json_decode($data,true);  
	  	 	  	 
	  	 if(is_array($data) && count($data)>=1){	  	 	
	  	 	if($data['rows'][0]['elements'][0]['status']=="OK"){	  		
	  	 		return array(
	  	 		  'duration'=>$data['rows'][0]['elements'][0]['duration']['text'],
	  	 		  'distance'=>$data['rows'][0]['elements'][0]['distance']['text']
	  	 		);
	  	 	} 
	  	 }
	  	 return false;
    }	
    
    public static function driverUploadPath()
	{
		$upload_path=Yii::getPathOfAlias('webroot')."/upload";
		$path_to_upload=Yii::getPathOfAlias('webroot')."/upload/driver";
		
		if (!file_exists($upload_path)){
			@mkdir($upload_path,0777);
		}
		
		return $path_to_upload;
	}
	
    public static function getAdminID()
	{
		$db=new DbExt;
		$stmt="
		SELECT * FROM
		{{admin_user}}
		ORDER BY admin_id ASC
		LIMIT 0,1
		";
		if($res=$db->rst($stmt)){
			return $res[0];
		}
		return false;
	}	
	
	public static function adminList()
	{
		$db=new DbExt;
		$stmt="
		SELECT admin_id,username FROM
		{{admin_user}}
		ORDER BY admin_id ASC		
		";
		if($res=$db->rst($stmt)){
			$data='';
			foreach ($res as $val) {
				$data[$val['admin_id']]=$val['username'];
			}
			unset($db);
			return $data;
		}
		unset($db);
		return false;
	}
	
	public static function driverAppLoginNew($username='', $password='')
	{
		$db=new DbExt;
		
		$stmt="SELECT * FROM
		{{driver}}
		WHERE
		username=".self::q($username)."
		AND
		password=".self::q(md5($password))."		
		LIMIT 0,1
		";		
		//dump($stmt);
		if ( $res=$db->rst($stmt)){
			return $res[0];
		}
		return false;
	}	
	
	public static function getOrderTotalAmount($order_id='')
	{
		$db=new DbExt;
		
		$stmt="SELECT total_w_tax FROM
		{{order}}
		WHERE
		order_id=".self::q($order_id)."		
		LIMIT 0,1
		";				
		if ( $res=$db->rst($stmt)){
			return $res[0];
		}
		return false;
	}	
	
	public static function updateOptionAdmin($option_name='',$option_value='')
	{
		$stmt="SELECT * FROM
		{{option}}
		WHERE
		option_name='".addslashes($option_name)."'
		AND
		merchant_id='0'
		";
		$connection=Yii::app()->db;
		$rows=$connection->createCommand($stmt)->queryAll(); 		
		
		$params=array(
		'option_name'=> addslashes($option_name),
		'option_value'=> addslashes($option_value)
		);
		$command = Yii::app()->db->createCommand();
		
		if (is_array($rows) && count($rows)>=1){
			$option_id=$rows[0]['id'];						
			//dump($option_id);
			$res = $command->update('{{option}}' , $params , 
				                     'id=:id' , array(':id'=> addslashes($option_id) ));
				                     
		    if ($res){
		    	return TRUE;
		    } 
		} else {			
			if ($command->insert('{{option}}',$params)){
				return TRUE;
			}
		}
		return FALSE;
	}	
	
	public static function validateAssigment($task_id='', $driver_id='')
	{
		$db=new DbExt;
		
		$stmt="
		SELECT * FROM
		{{driver_assignment}}
		WHERE
		task_id=".self::q($task_id)."
		AND
		driver_id=".self::q($driver_id)."
		LIMIT 0,1
		";				
		if ( $res=$db->rst($stmt)){
			unset($db);
			return $res[0];
		}
		unset($db);
		return false;
	}
	
	public static function seoFriendly($string){
	    $string = str_replace(array('[\', \']'), '', $string);
	    $string = preg_replace('/\[.*\]/U', '', $string);
	    $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
	    $string = htmlentities($string, ENT_COMPAT, 'utf-8');
	    $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string );
	    $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $string);
	    return strtolower(trim($string, '-'));
	}
	
	public static function getTaskPhoto($task_id='')
	{
		$db=new DbExt; $data='';
		
		$stmt="
		SELECT * FROM
		{{driver_task_photo}}
		WHERE
		task_id=".self::q($task_id)."		
		ORDER BY id ASC		
		";				
		if ( $res=$db->rst($stmt)){
			unset($db);
			foreach ($res as $val) {
				$photo='';
				if(!empty($val['photo_name'])){
					$photo=Driver::driverUploadPath()."/".$val['photo_name'];
		    		if(file_exists($photo)){
		    			$photo=websiteUrl()."/upload/driver/".$val['photo_name'];
		    		}
				}
				$val['photo_url']=$photo;
				$data[]=$val;
			}
			return $data;
		}
		unset($db);
		return false;
	}
	
	public static function taskPhotoCount($task_id='')
	{
		$db=new DbExt; $data='';
		
		$stmt="
		SELECT count(*) as total FROM
		{{driver_task_photo}}
		WHERE
		task_id=".self::q($task_id)."		
		";				
		if ( $res=$db->rst($stmt)){
			unset($db);		
			if ($res[0]['total']>0){
			    return $res[0];
			}
		}
		unset($db);
		return false;
	}
	
	public static function getTaskByID($task_id='')
	{
		$db=new DbExt;				
		$stmt="
		SELECT * FROM
		{{driver_task}}
		WHERE
		task_id=".self::q($task_id)."		
		LIMIT 0,1
		";
		if($res=$db->rst($stmt)){
			unset($db);
		   return $res[0];
		}	
		unset($db);
		return false;
	}
	
    public static function getPhotoDetails($photo_id='')
	{
		$db=new DbExt;				
		$stmt="
		SELECT * FROM
		{{driver_task_photo}}
		WHERE
		id=".self::q($photo_id)."		
		LIMIT 0,1
		";
		if($res=$db->rst($stmt)){
			unset($db);
		   return $res[0];
		}	
		unset($db);
		return false;
	}
		
	public static function deletePhoto($photo_id='')
	{
		$db=new DbExt;				
		$stmt="
		DELETE FROM
		{{driver_task_photo}}
		WHERE
		id=".self::q($photo_id)."		
		";
		$res=$db->qry($stmt);
		
		
		/*$stmt="
		DELETE FROM
		{{order_history}}
		WHERE
		photo_task_id=".self::q($photo_id)."		
		";
		$res=$db->qry($stmt);*/
			
		unset($db);
		return true;
	}
	
    public static function merchantListByID($merchant_id='')
    {
    	$data='';
    	$DbExt=new DbExt;
    	$stmt="SELECT * FROM
    	{{merchant}}
    	WHERE status in ('active')
    	AND
    	merchant_id=".self::q($merchant_id)."
    	ORDER BY restaurant_name ASC
    	";    	    	
    	if ($res=$DbExt->rst($stmt)){    		
    		foreach ($res as $val) {      			
			    $data[$val['merchant_id']]=ucwords(stripslashes($val['restaurant_name']));
			}
			return $data;
    	}
    	return false;
    }		
    
	public static function teamListNormal($user_type='', $user_id='' , $status='publish')
	{
		
		$and='';
		if(!empty($user_type)){			
			$user_type=$user_type;
		} else $user_type=self::getUserType();		
		
		//if ( self::getUserType()=="admin"){
		if($user_type=="admin"){
			$and=" AND user_type=".self::q($user_type)."  ";
		} else {
			$and =" AND user_type=".self::q($user_type)."  ";
			$and.=" AND user_id=".self::q($user_id)."  ";
		}						
		
		$db=new DbExt;
		$stmt="
		SELECT * FROM
		{{driver_team}}		
		WHERE 1
		$and
		AND status ='$status'
		ORDER BY team_name ASC
		";		
		//dump($stmt);
		if($res=$db->rst($stmt)){
			return $res;
		}
		return false;
	}    
	
    public static function calendarLocalLang()
	{
		return array(
		    ""=>t("Please select"),
		   'ar'=>"Arabic",
		   'az'=>"Azerbaijanian (Azeri) ",
		   'bg'=>"Bulgarian",
		   'bs'=>"Bosanski ",
		   'ca'=>"Catala",
		   'ch'=>"Simplified Chinese",
		   'cs'=>"Cestina",
		   'da'=>"Dansk",
		   'de'=>"German",
		   'el'=>"El",
		   'en'=>"English",
		   'en-GB'=>"English (British)",
		   'es'=>"Spanish",
		   'et'=>"Eesti",
		   'eu'=>"Euskara",
		   'fa'=>" Persian ",
		   'fi'=>" Finnish (Suomi) ",
		   'fr'=>"French",
		   'gl'=>" Galego ",
		   'he'=>" Hebrew",
		   'hr'=>" Hrvatski ",
		   'hu'=>" Hungarian ",
		   'id'=>" Indonesian ",
		   'it'=>" Italian ",
		   'ja'=>" Japanese ",
		   'ko'=>" Korean",
		   'kr'=>" Korean ",
		   'lt'=>" Lithuanian",
		   'lv'=>" Latvian",
		   'mk'=>" Macedonian",
		   'mn'=>" Mongolian",
		   'nl'=>" Dutch ",
		   'no'=>" Norwegian ",
		   'pl'=>" Polish ",
		   'pt'=>" Portuguese ",
		   'pt-BR'=>" Portugues(Brasil) ",
		   'ro'=>" Romanian ",
		   'ru'=>" Russian ",
		   'se'=>" Swedish ",
		   'sk'=>" Sloven?ina ",
		   'sl'=>" Slovenscina ",
		   'sq'=>" Albanian",
		   'sr'=>" Serbian Cyrillic",
		   'sr-YU'=>" Serbian (Srpski) ",
		   'sv'=>" Svenska ",
		   'th'=>" Thai ",
		   'tr'=>" Turkish ",
		   'uk'=>" Ukrainian ",
		   'vi'=>" Vietnamese ",
		   'zh'=>" Simplified Chinese",
		   'zh-TW'=>" Traditional Chinese",		   
		);
	}	
	
	public static function getTeamList($user_type="", $user_id='')
	{		
	   $team_list[]=self::t("Please select");
       if ($res=Driver::teamList($user_type, $user_id )){
       	  foreach ($res as $val) {
       	  	 $team_list[$val['team_id']]=$val['team_name'];
	      }       	
	      return $team_list;
       }
       return false;
	}
	
    public static function dateDifference($start, $end )
    {
        $uts['start']=strtotime( $start );
		$uts['end']=strtotime( $end );
		if( $uts['start']!==-1 && $uts['end']!==-1 )
		{
		if( $uts['end'] >= $uts['start'] )
		{
		$diff    =    $uts['end'] - $uts['start'];
		if( $days=intval((floor($diff/86400))) )
		    $diff = $diff % 86400;
		if( $hours=intval((floor($diff/3600))) )
		    $diff = $diff % 3600;
		if( $minutes=intval((floor($diff/60))) )
		    $diff = $diff % 60;
		$diff    =    intval( $diff );            
		return( array('days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff) );
		}
		else
		{			
		return false;
		}
		}
		else
		{			
		return false;
		}
		return( false );
     }    	
     
	public static function getLastSignature($task_id='')
	{
		$DbExt=new DbExt;
		$stmt="SELECT *
		 FROM
		{{order_history}}
		WHERE
		task_id='".$task_id."'
		AND
		status='sign'		
		order by ID DESC
		LIMIT 0,1
		";
		if ( $res=$DbExt->rst($stmt)){
			unset($DbExt);
			return $res[0];
		}
		unset($DbExt);
		return false;
	}     
     
     public static function retryAutoAssign($task_id='')
     {
     	  $db=new DbExt; 
     	  $less="-1";					
		  $params=array(			  
			 'assignment_status'=>'waiting for driver acknowledgement',			
			 'assign_started'=>date('Y-m-d G:i:s',strtotime("$less min")),
			 'auto_assign_type'=>''
		  );
		  $db->updateData("{{driver_task}}",$params,'task_id',$task_id);
		  
		  $stmt="DELETE FROM
		  {{driver_assignment}}
		  WHERE
		  task_id=".Driver::q($task_id)."
		  ";
		  $db->qry($stmt);
		  return true;
     }
     
    public static function WgetRequest($url, $post_array='', $check_ssl=true) 
	{

		  $cmd = "curl -X POST -H 'Content-Type: application/json'";
		  $cmd.= " -d '" . json_encode($post_array) . "' '" . $url . "'";
		
		  if (!$check_ssl){
		    $cmd.= "'  --insecure"; // this can speed things up, though it's not secure
		  }
		  $cmd .= " > /dev/null 2>&1 &"; //just dismiss the response
		
		  //dump($cmd);
		  exec($cmd, $output, $exit);
		  return $exit == 0;
    } 
    
    public static function checkTableFields($table='',$new_field='')
	{
		$DbExt=new DbExt;
		$prefix=Yii::app()->db->tablePrefix;		
		$existing_field='';
		if ( $res = self::checkTableStructure($table)){
			foreach ($res as $val) {								
				$existing_field[$val['Field']]=$val['Field'];
			}							
			foreach ($new_field as $key_new=>$val_new) {								
				if (in_array($key_new,(array)$existing_field)){	
					return true;
				} 
			}
		}			
		return false;
	}		
	
    public static function checkTableStructure($table_name='')
    {
    	$db_ext=new DbExt;
    	$stmt=" SHOW COLUMNS FROM {{{$table_name}}}";	    	
    	if ($res=$db_ext->rst($stmt)){    		
    		return $res;
    	}
    	return false;    
    }      
        
	public static function checkNewVersion()
	{		
		$new=0;
		// version 1.5
		$new_fields=array('team_id'=>"team_id");
		if ( !self::checkTableFields('driver_bulk_push',$new_fields)){			
			$new++;
		}
		$new_fields=array('user_type'=>"user_type");
		if ( !self::checkTableFields('driver_pushlog',$new_fields)){			
			$new++;
		}		
				
		if ($new>0){
			return true;
		} else return false;
	}    
	
   public static function driverDropDownList($user_type='', $user_id='')
	{
		$data["-1"]=self::t("Please select");
		if ( $res=self::getAllDriver($user_type,$user_id)){
			foreach ($res as $val) {
				$data[$val['driver_id']]=$val['first_name']." ".$val['last_name'];
			}
			return $data;
		}
		return $data;
	}
	
	public static function backTrackList($user_type='', $user_id='')
	{
		$db=new DbExt; 
		$stmt="
		SELECT date_created,driver_id
		FROM {{driver_track_location}}
		WHERE user_type=".self::q($user_type)."		
		AND
		user_id=".self::q($user_id)."
		GROUP BY DATE_FORMAT(date_created,'%Y-%m-%d')
		ORDER BY date_created DESC
		";
		if ( $res=$db->rst($stmt)){
			return $res;
		}
		return false;	
	}	
	
	public static function getBackTrackRecords($driver_id='', $track_date='')
	{
		$db=new DbExt; 
		$stmt="
		SELECT * FROM
		{{driver_track_location}}
		WHERE
		driver_id=".self::q($driver_id)."		
		AND
		date_created LIKE '".$track_date."%'
		ORDER BY date_created ASC
		";
		if ( $res=$db->rst($stmt)){
			return $res;
		}
		return false;	
	}	
	
	public static function logEmail($email_address='',$subject='',$message='',$status='',
	$user_type='',$user_id='')
	{
		$db_ext=new DbExt;
		$db_ext->insertData("{{email_logs}}",array(
	      'email_address'=>$email_address,
	      'sender'=>getOptionA('global_admin_sender_email'),
	      'subject'=>$subject,
	      'content'=>$message,
	      'status'=>$status==true?"send":"failed",
	      'date_created'=>self::dateNow(),
	      'ip_address'=>$_SERVER['REMOTE_ADDR'],
	      'module_type'=>"driver",
	      'user_type'=>$user_type,
	      'user_id'=>$user_id
	   ));		   
	}
	
	public static function getLanguageList()
	{
		$list[0]=self::t("Please select");
    	$path=Yii::getPathOfAlias('webroot')."/protected/messages";    	
    	$res=scandir($path);
    	if(is_array($res) && count($res)>=1){
    		foreach ($res as $val) {    			
    			if($val=="."){    				
    			} elseif ($val==".."){  
    			} elseif ($val=="default"){  
    			} else {
    				$list[$val]=$val;
    			}
    		}    		
    		return $list;
    	}
    	return false;		
	}
	
	public static function handleLanguage()
	{
		$app = Yii::app();
     	$user = $app->user;
     	
		if (isset($_GET['lang'])){
			
			if(!empty($_GET['lang'])){
	     	 	$app->language = $_GET['lang'];
	     	 	$app->user->setState('lang', $_GET['lang']);
	     	    $cookie = new CHttpCookie('_lang', $_GET['lang']);
	            $cookie->expire = time() + (60*60*24*365); // (1 year)
	            Yii::app()->request->cookies['lang'] = $cookie;   
			}

        } elseif ( isset($_POST['lang']) ){     	 	

        	if(!empty($_POST['lang'])){
	        	$app->language = $_POST['lang'];
	     	 	$app->user->setState('lang', $_POST['lang']);
	     	    $cookie = new CHttpCookie('_lang', $_POST['lang']);
	            $cookie->expire = time() + (60*60*24*365); // (1 year)
	            Yii::app()->request->cookies['lang'] = $cookie;           
        	}
        	
     	} elseif ( $app->user->hasState('lang') ){     	 	
     	 	$app->language = $app->user->getState('lang');
     	 	 
     	} elseif ( isset(Yii::app()->request->cookies['lang']) ){     	 	
     	 	$app->language = Yii::app()->request->cookies['lang']->value;
     	}
	}
	
	
	public static function statusListPost()
	{
		return array(
		 'publish'=>Driver::t('Publish'),
		 'pending'=>Driver::t('Pending for review'),
		 'draft'=>Driver::t('Draft')
		);
	}	
	
    public static function getMobileTranslation()
	{		
		$language_list=self::getLanguageList();		
    	$final_lang='';
    	$path=Yii::getPathOfAlias('webroot')."/protected/messages";    	
    	if(is_array($language_list) && count($language_list)>=1){
    		foreach ($language_list as $val) {    			
    			$lang_path=$path."/$val/driverapp.php";    			
    			if(file_exists($lang_path)){    				
    				$temp_lang='';
    				$temp_lang=require_once($lang_path);    				
    				foreach ($temp_lang as $key=>$val_lang) {
    					$final_lang[$key][$val]=$val_lang;
    				}
    			}
    		}    		
    	}        	
    	return $final_lang;
	}	
	        
}/* end class*/