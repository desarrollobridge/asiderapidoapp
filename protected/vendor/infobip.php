<?php
class infobip
{		
	
	public function __construct()
	{		
	}
	
	public static function okCode()
	{
		/*https://dev.infobip.com/getting-started/response-status-and-error-codes
		3 = PENDING_WAITING_DELIVERY
		7 = PENDING_ENROUTE
		26 = PENDING_ACCEPTED
		*/
		
		return array(
		 0,3,7,26
		);		
	}
	
	public static function sendSMS($params=array())
	{		
		$resp=''; $ouput='';		
		if (empty($params['details']['to'])){
			throw new Exception( t("To is missing") );
		}		
		if (empty($params['details']['message'])){
			throw new Exception( t("Message is missing") );
		}
		if (empty($params['credetials']['username'])){
			throw new Exception( t("username is missing") );
		}
		if (empty($params['credetials']['password'])){
			throw new Exception( t("password is missing") );
		}
		if (empty($params['credetials']['senderid'])){
			throw new Exception( t("senderid is missing") );
		}
				
		$use_unicode = $params['credetials']['use_unicode'];
		
		if ($use_unicode==1){					
			$authorization_code =$params['credetials']['username'].":".$params['credetials']['password'];
			$authorization_code = base64_encode($authorization_code);
						
			$url = "http://api.infobip.com/sms/1/text/single";				
			//$url = "http://api.infobip.com/sms/1/binary/advanced";
			$headers=array(
			  'accept: application/json',
			  "authorization: Basic $authorization_code",
			  'content-type: application/json'
			);
			//dump($headers);
			$query = array(			  
			  'from'=>$params['credetials']['senderid'],
			  'to'=>$params['details']['to'],
			  'text'=>$params['details']['message'],
			);		
			//dump($query);	
						
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => $url ,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_POSTFIELDS => json_encode($query),
			  CURLOPT_HTTPHEADER =>$headers
			));						
			$resp = curl_exec($curl);
			$err = curl_error($curl);			
			curl_close($curl);		
			if ($err) {
			    $resp =  "Curl Error #:" . $err;
			} 				
		} else {						
			$url = "https://api.infobip.com/sms/1/text/query";
			$query = array(
			  'username'=>$params['credetials']['username'],
			  'password'=>$params['credetials']['password'],
			  'from'=>$params['credetials']['senderid'],
			  'to'=>$params['details']['to'],
			  'text'=>$params['details']['message'],
			  /*'notifyUrl'=>"http://bastisapp.com/testcode/",
			  'notifyContentType'=>'application/json'*/
			);			
			//dump($query);
			$url = $url."?".http_build_query($query);			
			$resp = self::getSslPage($url);					
	    }						
	    
		/*dump("RESPONSE");
		dump($resp);*/
		
		if(!empty($resp)){
			$json_resp = json_decode($resp,true);			
			if (is_array($json_resp) && count($json_resp)>=1){			
				if (array_key_exists('messages',(array)$json_resp)){
					$code = $json_resp['messages'][0]['status']['id'];
					$code_attr = $json_resp['messages'][0]['status']['name'];
					$message_id = $json_resp['messages'][0]['messageId'];
					//dump($code); dump($code_attr); dump($message_id);
					$ok_code = self::okCode();				
					if (in_array($code,(array)$ok_code)){
					 	return $message_id;
					} else throw new Exception( $code_attr );	
				} elseif (array_key_exists('requestError',(array)$json_resp)){
				   throw new Exception( t($json_resp['requestError']['serviceException']['text']) );	
				} else {
					echo 'failed';
				}
			} else {
			    throw new Exception( $resp );
			}
		} else {
			throw new Exception( t("empty response from api") );
		}
	}
	
	public static function getSslPage($url) {
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($ch, CURLOPT_HEADER, false);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_REFERER, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    $result = curl_exec($ch);
	    curl_close($ch);
	    return $result;
	}
	
} /*end class*/