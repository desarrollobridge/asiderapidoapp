<?php
class AndroidPush
{
	
    public static function sendPush($api_key='',$device_id='',$message='')
    {    	
    	if (empty($api_key)){
    		return array(
    		  'success'=>0,
    		  'results'=>array(
    		     array(
    		       'error'=>'missing api key'
    		     )
    		  )
    		);
    	}
    	if (empty($device_id)){
    		return array(
    		  'success'=>0,
    		  'results'=>array(
    		     array(
    		       'error'=>'missing device id'
    		     )
    		  )
    		);
    	}
    	    	
    	$url = 'https://android.googleapis.com/gcm/send';
		$fields = array(
           'registration_ids' => array($device_id),
           'data' => $message,
        );
        //dump($fields);
        
        $headers = array(
		  'Authorization: key=' . $api_key,
		  'Content-Type: application/json'
        );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));		
		$result = curl_exec($ch);
		if ($result === FALSE) {		    
		   return array(
    		  'success'=>0,
    		  'results'=>array(
    		     array(
    		       'error'=>'Curl failed: '. curl_error($ch)
    		     )
    		  )
    		);
		}
		
        curl_close($ch);
        //echo $result; 
        $result=!empty($result)?json_decode($result,true):false;
        //dump($result);
        if ($result==false){
        	return array(
    		  'success'=>0,
    		  'results'=>array(
    		     array(
    		       'error'=>'invalid response from push service'
    		     )
    		  )
    		);
        }
        return $result;   
    }
    
} /*end class*/