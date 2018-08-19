<?php
class CronController extends CController
{
	
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
	
	
	public function actionGetNewOrder()
	{
		
		
		$status=Yii::app()->functions->getOptionAdmin('merchant_app_new_order_status');
		if(empty($status)){
			$status='pending';
		}			
				
		$DbExt=new DbExt; 
		/*$stmt="
		SELECT a.order_id FROM
		{{order}} a
		WHERE
		STATUS IN ('$status','paid')
		AND
		viewed='1'
		AND
		order_id NOT IN (
		  select order_id from
		  {{mobile_merchant_pushlogs}}
		  where
		  order_id =a.order_id
		)
		ORDER BY date_created DESC
		LIMIT 0,5
		";*/
		
		$stmt="
		SELECT a.order_id FROM
		{{order}} a
		WHERE
		STATUS IN ('$status','paid')		
		AND
		date_created like '".date("Y-m-d")."%'
		AND
		order_id NOT IN (
		  select order_id from
		  {{mobile_merchant_pushlogs}}
		  where
		  order_id =a.order_id
		)
		ORDER BY date_created DESC
		LIMIT 0,10
		";		
				
		if(isset($_GET['debug'])){
			dump($stmt);
		}
		if ( $res=$DbExt->rst($stmt)){
			foreach ($res as $val) {
				dump($val);
				merchantApp::pushNewOrder($val['order_id']);
			}			
		} else {
			if(isset($_GET['debug'])){
			   echo "no records to process";
			}
		}				
	}
	
	public function actionProcesspush()
	{
		$iOSPush=new iOSPush;
		$DbExt=new DbExt; 

		$ios_push_mode=Yii::app()->functions->getOptionAdmin('mt_ios_push_mode');			
		$ios_passphrase=Yii::app()->functions->getOptionAdmin('mt_ios_passphrase');
		$ios_push_dev_cer=Yii::app()->functions->getOptionAdmin('mt_ios_push_dev_cer');
		$ios_push_prod_cer=Yii::app()->functions->getOptionAdmin('mt_ios_push_prod_cer');
		
		$ios_push_mode=$ios_push_mode=="development"?false:true;
		$iOSPush->pass_prase=$ios_passphrase;
   	    $iOSPush->dev_certificate=$ios_push_dev_cer;
   	    $iOSPush->prod_certificate=$ios_push_prod_cer;
							
		$api_key=Yii::app()->functions->getOptionAdmin('merchant_android_api_key');		
		$msg_count=1;		
				
		$stmt="SELECT * FROM
		{{mobile_merchant_pushlogs}}
		WHERE
		status='pending'
		ORDER BY id ASC
		LIMIT 0,10
		";
		if(isset($_GET['debug'])){
			dump($stmt);
		}
		if($res=$DbExt->rst($stmt)){		   
		   foreach ($res as $val) {		
		   	
		   	  if(isset($_GET['debug'])){
		   	     dump($val);
		   	  }
		   	  
		   	  $status='';
		   	  $record_id=$val['id'];		   	  
		   	  
		   	  $id_order_book=''; $resp='';  
		   	  		   	 
		   	  $message=array(		 
				 'title'=>$val['push_title'],
				 'message'=>$val['push_message'],
				 'soundname'=>'beep',
				 'count'=>$msg_count,
				 'push_type'=>$val['push_type'],
				 'order_id'=>$val['order_id'],
				 'booking_id'=>$val['booking_id']
			   );
			   			   			   
			   if ( strtolower($val['device_platform'])=="ios"){			   	   
			   	   /*send push using ios*/			   	   			   	   			   	  
			   	   $aps_body['aps'] = array(
					    'alert' => $val['push_message'],
					    'sound' => "www/beep.wav",
					    'badge'=>(integer) 1,
					    'push_type'=>$val['push_type'],
					    'order_id'=>$val['order_id'],
					    'booking_id'=>$val['booking_id'],
				   );  	   						   	      	 
			   	   if ($resp=$iOSPush->push($val['push_message'],$val['device_id'],$ios_push_mode,$aps_body)){
			   	   	   $status="process";
			   	   } else $status=$iOSPush->get_msg();
			   	   
			   } else {
			   	   /*send push using android*/
			   	   if(isset($_GET['debug'])){
			   	      dump($message);
			   	   }
				   if (!empty($api_key)){
			   	       $resp=merchantApp::sendPush($val['device_platform'], 
			   	       $api_key,$val['device_id'],$message);
			   	       
			   	       if (merchantApp::isArray($resp)){
			   	       	   dump($resp);
			   	       	   if( $resp['success']>0){			   	       	   	   
			   	       	   	   $status="process";
			   	       	   } else {		   	       	   	   
			   	       	   	   $status=$resp['results'][0]['error'];
			   	       	   }
			   	       } else $status="uknown push response";
				   } else $status="Invalid API Key";
			   }
			   			   
			   $params_update=array(
			     'status'=>empty($status)?"uknown status":$status,
			     'date_process'=>FunctionsV3::dateNow(),
			     'json_response'=>json_encode($resp)
			    );
			   dump($params_update);
			   $DbExt->updateData('{{mobile_merchant_pushlogs}}',$params_update,'id',$record_id);			   			   
		   }
		}  else echo "No records to process<br/>";
	} 						
	
}/* end class*/