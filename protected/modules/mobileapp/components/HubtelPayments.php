<?php
class HubtelPayments
{
	
	static $err;
	
	public static function getPaymentCredentials($merchant_id='')
	{
		$enabled=false; $mode='';
		$client_id=''; $client_secret=''; $account_number=''; $channel='';
    	if (FunctionsV3::isMerchantPaymentToUseAdmin($merchant_id)){
    		// USER ADMIN SETTINGS
    		$enabled=getOptionA('admin_hubtel_enabled');
    		$client_id=getOptionA('admin_hubtel_client_id');
    		$client_secret = getOptionA('admin_hubtel_client_secret');
    		$account_number = getOptionA('admin_hubtel_accountno');    		
    		$channel = getOptionA('admin_hubtel_channel');    		
    	} else {    		
    		// USE MERCHANT SETTINGS    		
    		$enabled=getOption($merchant_id,'merchant_hubtel_enabled');
    		$client_id=getOption($merchant_id,'merchant_hubtel_client_id');
    		$client_secret = getOption($merchant_id,'merchant_hubtel_client_secret');
    		$account_number = getOption($merchant_id,'merchant_hubtel_accountno');  		
    		$channel = getOption($merchant_id,'merchant_hubtel_channel');  
    	}    	    	
    	if($enabled==2 && !empty($client_id) && !empty($client_secret) && !empty($account_number) ){
    		return array(
    		   'enabled'=>$enabled,
    		   'client_id'=>$client_id,
    		   'client_secret'=>$client_secret,
    		   'account_number'=>$account_number,    	
    		   'channel'=>$channel
    		);
    	}
    	return false;
	}
	
	public static function channelList()
	{
		return array(
		  'mtn-gh'=>"MTN Ghana",
		  'vodafone-gh'=>"Vodafone Ghana",
		  'tigo-gh'=>"Tigo Ghana",
		  'airtel-gh'=>"Airtel Ghana",
		);
	}
	
	public static function receiveMoney($credentials=array(), $params=array())
	{		
		$basic_auth_key =  'Basic ' . base64_encode($credentials['client_id'] . ':' . $credentials['client_secret']);
        $request_url = 'https://api.hubtel.com/v1/merchantaccount/merchants/'.$credentials['account_number'].'/receive/mobilemoney';
        $receive_momo_request = json_encode($params);
        
        //dump($params);
                
		$ch =  curl_init($request_url);  
				curl_setopt( $ch, CURLOPT_POST, true );  
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $receive_momo_request);  
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );  
				curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
				    'Authorization: '.$basic_auth_key,
				    'Cache-Control: no-cache',
				    'Content-Type: application/json',
				  ));
		
		$result = curl_exec($ch); 
		$err = curl_error($ch);
		curl_close($ch);
		
		if($err){
			self::$$err=$err;
		}else{			
			$resp=json_decode($result,true);			
			if(is_array($resp) && count($resp)>=1){
				//dump($resp);
				if ($resp['ResponseCode']=="0001"){
					return $resp;
				} else {
					if(isset($resp['Errors'])){
					   if(is_array($resp['Errors']) && count($resp['Errors'])>=1){
					   	  foreach ($resp['Errors'] as $errors) {
					   	  	  self::$err.=$errors['Messages'][0];
					   	  	  self::$err.="\n";
					   	  }
					   } else $err = AddonMobileApp::t("Undefined error");
					} elseif ( isset($resp['Message'])){
						self::$err = $resp['Message'];
					} else $err = AddonMobileApp::t("Undefined error");
				}
			} else self::$err = AddonMobileApp::t("Invalid response from api");
		}
		return false;
	}
	
	
} /*end class*/