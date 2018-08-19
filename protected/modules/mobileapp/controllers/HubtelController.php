<?php
class HubtelController extends CController
{
	
	public function actionIndex()
	{
		$callback_obj='';
		$inputs = file_get_contents("php://input");
	    $callback_obj.="$inputs\n";
	    
	    $path_to_upload=Yii::getPathOfAlias('webroot')."/upload/logs";
	    if(!file_exists($path_to_upload)) {	
           if (!@mkdir($path_to_upload,0777)){           	    
           	    return ;
           }		    
	    }	   
	    
	    /*$inputs='{"ResponseCode":"2001","Data":{"AmountAfterCharges":0.0,"TransactionId":"5ca516eed3bf4ea58b363e936790d7c4","ClientReference":"2-3010303","Description":"Transaction failed due to an error with the upstream provider. Please check and try again.","ExternalTransactionId":"","Amount":19.04,"Charges":0.0}}';*/
	    	    
	    if(!empty($inputs)){
	    	$resp=json_decode($inputs,true);	    	
	    	if(is_array($resp) && count($resp>=1)){
	    		
	    		$DbExt=new DbExt;
	    		
	    		//dump($resp);
	    		
	    		$payment_ref=$resp['Data']['ClientReference'];
	    		$t=explode("-",$payment_ref);
	    		if(is_array($t) && count($t)>=1){
	    			$order_id = $t[0];
	    		} else {
	    			$order_id = '';
	    			$callback_obj.="Order id is empty \n";
	    		}
	    		
	    		if ( $resp['ResponseCode']=="0000"){
	    			$params_update=array(
	    			    'status'=>"paid",
	    			   'date_modified'=>FunctionsV3::dateNow()
	    			);
	    			$DbExt->updateData("{{order}}",$params_update,'order_id',$order_id);
	    			$callback_obj.="Success \n";
	    		} else {	    	    			
	    			$params_update=array(
	    			  'status'=>$resp['Data']['Description'],
	    			  'date_modified'=>FunctionsV3::dateNow()
	    			);	    			
	    			$DbExt->updateData("{{order}}",$params_update,'order_id',$order_id);
	    			$callback_obj.="Failed \n";
	    		}
	    	} else $callback_obj.="post back is not array \n";
	    } else $callback_obj.="Inputs is empty \n";	    
	    
	    $callback_obj.="\n";	    
	    	    	     	
	    // log the callback response to file on your server
	    $filename = "hubtel-".date("Y-m-d")."-callback.txt";
	    $log_file = fopen($path_to_upload."/$filename", "a") or die("Unable to open file!");
	    fwrite($log_file,"$callback_obj"); 
	    fclose($log_file); 
	}
	
}/* end class*/