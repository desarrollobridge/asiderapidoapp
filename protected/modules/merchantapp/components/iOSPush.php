<?php
class iOSPush
{		
	public $pass_prase;
	public $dev_certificate;
	public $prod_certificate;
	
	private $msg='';
			
	/*public function push(
	    $msg='',
	    $device_id='',
	    $production=true,	   
	    $sounds='www/beep.wav',
	    $badgecount=1
	    )
    {*/
	public function push($msg='',$device_id='', $production=true , $aps_body=array() )
	{	
	
    	global $mp_db;  
    	$debug=isset($_GET['debug'])?true:false;
    	    	    	    	
    	$deviceToken = $device_id;    	
        $passphrase = $this->pass_prase;
        //$message = $msg;
        
        if ($debug){
        	echo "<h2>Device ID</h2>";
            dump($deviceToken);        
        }
        
        if (empty($msg)){
        	$this->msg="message is empty";
        	return false;
        }
        
        if (empty($passphrase)){
        	$this->msg="passphrase is empty";
        	return false;
        }
        
        if (empty($device_id)){
        	$this->msg="device id is empty";
        	return false;
        }
                       
        $path_certificate=Yii::getPathOfAlias('webroot')."/upload/mt_certificate";
                     
        if ($production==FALSE){
        	if ($debug){
			    echo "<h2>CERTIFICATE SANDBOX</h2>";
			    dump($path_certificate."/".$this->dev_certificate);
			}
			$ctx = stream_context_create();		
			stream_context_set_option($ctx, 'ssl', 'local_cert', $path_certificate."/".$this->dev_certificate);
			stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
						
		    if ( file_exists($path_certificate."/".$this->dev_certificate)){
		    	if ($debug){dump("FOUND CERTIFICATE");}
		    } else {
		    	if ($debug){dump("NOT FOUND CERTIFICATE");}
		    	$this->msg="certificate not found";
		    	return false;
		    }
		    
        } else {
        	if ($debug){echo "<h2>CERTIFICATE PRODUCTION</h2>";}
        	$ctx = stream_context_create();		
		    stream_context_set_option($ctx, 'ssl', 'local_cert', $path_certificate."/".$this->prod_certificate);
		    stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
		    
		    dump($path_certificate."/".$this->prod_certificate);
		    if ( file_exists($path_certificate."/".$this->prod_certificate)){
		    	if ($debug){dump("FOUND CERTIFICATE");}
		    } else {
		    	if ($debug){dump("NOT FOUND CERTIFICATE");}
		    	$this->msg="certificate not found";
		    	return false;
		    }
        }
		
		if ($production==FALSE){
			if ($debug){
			    echo "<h2>SANDBOX</h2>";
			}
		    $fp = stream_socket_client(
		    'ssl://gateway.sandbox.push.apple.com:2195', $err,
		    $errstr, 120, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		} else {
			if ($debug){
			    echo "<h2>PRODUCTION</h2>";
			}			
		    $fp = stream_socket_client(
		    'ssl://gateway.push.apple.com:2195', $err,
		    $errstr, 120, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		}    
		
		if (!$fp){			
			$this->msg="Failed to connect : $err $errstr" . PHP_EOL;
			return false;
		}		    
		    
		if ($debug){
		    echo 'Connected to APNS' . PHP_EOL;
		}
				
		/*$body['aps'] = array(
		    'alert' => $message,
		    'sound' => $sounds,
		    'badge'=>(integer) !is_numeric($badgecount)?1:$badgecount,
		    'push_type'=>$this->push_type,
		    'order_id'=>$this->order_id,
		    'booking_id'=>$this->booking_id,
	    );
	    $payload = json_encode($body);
	    */		    				
				
		$payload = json_encode($aps_body);
		
		if ($debug){
			echo "<h2>Request</h2>";
		    dump($body);
		}		
		
		$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
		
		$result = fwrite($fp, $msg, strlen($msg));
		
		if ($debug){
			echo "<h2>Response</h2>";
		    dump($result);
		}
				
		if (!$result)
		    $this->msg=$result.' Message not delivered' . PHP_EOL;
		else
		    $this->msg=$result.' Message successfully delivered' . PHP_EOL;
		
		fclose($fp);		
		
		if (preg_match("/Message successfully delivered/i", $this->msg)) {			
			return true;			
		} 		
		return false; 
    }    
    
    public function get_msg()
    {
    	return $this->msg;
    } 
        
} /*END CLASS*/