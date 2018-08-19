<?php

class AjaxController extends CController
{
	public $code=2;
	public $msg;
	public $details;
	public $data;
	
	public function __construct()
	{
		$this->data=$_POST;	
		
		FunctionsV3::handleLanguage();
	    $lang=Yii::app()->language;	    	   
	    if(isset($_GET['debug'])){
	       dump($lang);
	    }
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
    	
	public function actionSaveSettings()
	{		
				
		Yii::app()->functions->updateOptionAdmin('merchant_android_api_key',
				isset($this->data['merchant_android_api_key'])?$this->data['merchant_android_api_key']:''
		);
		
		Yii::app()->functions->updateOptionAdmin('tpl_order_accept_title',
				isset($this->data['tpl_order_accept_title'])?$this->data['tpl_order_accept_title']:''
		);
		
		Yii::app()->functions->updateOptionAdmin('tpl_order_accept_content',
				isset($this->data['tpl_order_accept_content'])?$this->data['tpl_order_accept_content']:''
		);
		
		Yii::app()->functions->updateOptionAdmin('tpl_order_denied_title',
				isset($this->data['tpl_order_denied_title'])?$this->data['tpl_order_denied_title']:''
		);
		Yii::app()->functions->updateOptionAdmin('tpl_order_denied_content',
				isset($this->data['tpl_order_denied_content'])?$this->data['tpl_order_denied_content']:''
		);
		
		Yii::app()->functions->updateOptionAdmin('tpl_order_change_title',
				isset($this->data['tpl_order_change_title'])?$this->data['tpl_order_change_title']:''
		);
		Yii::app()->functions->updateOptionAdmin('tpl_order_change_content',
				isset($this->data['tpl_order_change_content'])?$this->data['tpl_order_change_content']:''
		);
		
		Yii::app()->functions->updateOptionAdmin('sms_tpl_order_accept_content',
				isset($this->data['sms_tpl_order_accept_content'])?$this->data['sms_tpl_order_accept_content']:''
		);
		Yii::app()->functions->updateOptionAdmin('sms_tpl_order_denied_content',
				isset($this->data['sms_tpl_order_denied_content'])?$this->data['sms_tpl_order_denied_content']:''
		);
		Yii::app()->functions->updateOptionAdmin('sms_tpl_order_change_content',
				isset($this->data['sms_tpl_order_change_content'])?$this->data['sms_tpl_order_change_content']:''
		);
		
		Yii::app()->functions->updateOptionAdmin('push_tpl_new_order_title',
				isset($this->data['push_tpl_new_order_title'])?$this->data['push_tpl_new_order_title']:''
		);
		Yii::app()->functions->updateOptionAdmin('push_tpl_new_order_content',
				isset($this->data['push_tpl_new_order_content'])?$this->data['push_tpl_new_order_content']:''
		);
				
		Yii::app()->functions->updateOptionAdmin('mt_ios_push_dev_cer',
				isset($this->data['mt_ios_push_dev_cer'])?$this->data['mt_ios_push_dev_cer']:''
		);
		Yii::app()->functions->updateOptionAdmin('mt_ios_push_prod_cer',
				isset($this->data['mt_ios_push_prod_cer'])?$this->data['mt_ios_push_prod_cer']:''
		);
		
		Yii::app()->functions->updateOptionAdmin('mt_ios_push_mode',
				isset($this->data['mt_ios_push_mode'])?$this->data['mt_ios_push_mode']:''
		);
		Yii::app()->functions->updateOptionAdmin('mt_ios_passphrase',
				isset($this->data['mt_ios_passphrase'])?$this->data['mt_ios_passphrase']:''
		);
		
		Yii::app()->functions->updateOptionAdmin('push_tpl_booking_title',
				isset($this->data['push_tpl_booking_title'])?$this->data['push_tpl_booking_title']:''
		);
		Yii::app()->functions->updateOptionAdmin('push_tpl_booking_content',
				isset($this->data['push_tpl_booking_content'])?$this->data['push_tpl_booking_content']:''
		);
		
		Yii::app()->functions->updateOptionAdmin('tpl_booking_approved_title',
				isset($this->data['tpl_booking_approved_title'])?$this->data['tpl_booking_approved_title']:''
		);
		Yii::app()->functions->updateOptionAdmin('tpl_booking_approved_content',
				isset($this->data['tpl_booking_approved_content'])?$this->data['tpl_booking_approved_content']:''
		);
		
		Yii::app()->functions->updateOptionAdmin('tpl_booking_denied_title',
				isset($this->data['tpl_booking_denied_title'])?$this->data['tpl_booking_denied_title']:''
		);
		Yii::app()->functions->updateOptionAdmin('tpl_booking_denied_content',
				isset($this->data['tpl_booking_denied_content'])?$this->data['tpl_booking_denied_content']:''
		);
		Yii::app()->functions->updateOptionAdmin('merchant_app_pending_tabs',
				isset($this->data['merchant_app_pending_tabs'])?json_encode($this->data['merchant_app_pending_tabs']):''
		);
		Yii::app()->functions->updateOptionAdmin('merchant_app_new_order_status',
				isset($this->data['merchant_app_new_order_status'])?$this->data['merchant_app_new_order_status']:''
		);
		Yii::app()->functions->updateOptionAdmin('merchant_app_hash_key',
				isset($this->data['merchant_app_hash_key'])?$this->data['merchant_app_hash_key']:''
		);
		
		Yii::app()->functions->updateOptionAdmin('drv_order_status',
				isset($this->data['drv_order_status'])?$this->data['drv_order_status']:''
		);
		
		Yii::app()->functions->updateOptionAdmin('push_booking_accepted_title',
				isset($this->data['push_booking_accepted_title'])?$this->data['push_booking_accepted_title']:''
		);
		Yii::app()->functions->updateOptionAdmin('push_booking_accepted_content',
				isset($this->data['push_booking_accepted_content'])?$this->data['push_booking_accepted_content']:''
		);
		
		Yii::app()->functions->updateOptionAdmin('push_booking_decline_title',
				isset($this->data['push_booking_decline_title'])?$this->data['push_booking_decline_title']:''
		);
		Yii::app()->functions->updateOptionAdmin('push_booking_decline_content',
				isset($this->data['push_booking_decline_content'])?$this->data['push_booking_decline_content']:''
		);
		
		Yii::app()->functions->updateOptionAdmin('merchant_app_force_lang',
				isset($this->data['merchant_app_force_lang'])?$this->data['merchant_app_force_lang']:''
		);
		
		$this->code=1;
	    $this->msg=merchantApp::t("settings saved");
		$this->jsonResponse();				
	}
	
	public function actionSaveTranslation()
	{		
		$mobile_dictionary='';
		if (is_array($this->data) && count($this->data)>=1){
			$version=str_replace(".",'',phpversion());		
			//533		
			/*if ($version<5329){	
				$mobile_dictionary=MobileUnicode::jsonUnicode1($this->data);
				$unicode=1;
			} elseif ( $version>=540) {	
			    $mobile_dictionary=json_encode($this->data,JSON_UNESCAPED_UNICODE);
			    $unicode=2;
			} else {			   
				$mobile_dictionary=json_encode($this->data);			
				$unicode=3;
			}	*/		
			$mobile_dictionary=json_encode($this->data);			
			$unicode=3;
		}				
		Yii::app()->functions->updateOptionAdmin('merchant_mobile_dictionary',$mobile_dictionary);
		$this->code=1;
		$this->msg=merchantApp::t("translation saved");
		$this->details=$unicode;
		$this->jsonResponse();
	}	
	
    public function actionExportLang()
	{
		$content=Yii::app()->functions->getOptionAdmin('merchant_mobile_dictionary');
		header('Content-disposition: attachment; filename=merchant_mobile_dictionary.json');
        header('Content-type: application/json');
        echo $content;										
		yii::app()->end();		
	}	

	public function actionimportLang()
	{
		require_once('Uploader.php');
		$path_to_upload=Yii::getPathOfAlias('webroot')."/upload";
        $valid_extensions = array('json'); 
        if(!file_exists($path_to_upload)) {	
           if (!@mkdir($path_to_upload,0777)){           	               	
           	    $this->msg=merchantApp::t("Error has occured cannot create upload directory");
                $this->jsonResponse();
           }		    
	    }
	    
        $Upload = new FileUpload('uploadfile');
        $ext = $Upload->getExtension();         
        $result = $Upload->handleUpload($path_to_upload, $valid_extensions);                
        if (!$result) {                    	
            $this->msg=$Upload->getErrorMsg();            
        } else {         	
        	$this->code=1;
        	$this->msg=merchantApp::t("upload done. kindly refresh your browser to see the changes affect"); 
			$this->details=Yii::app()->getBaseUrl(true)."/upload/".$_GET['uploadfile'];	
			
			$content = @file_get_contents($path_to_upload ."/".$_GET['uploadfile']);
			Yii::app()->functions->updateOptionAdmin('merchant_mobile_dictionary',$content);
        }
        $this->jsonResponse();
	}	

	public function actionRegisteredDeviceList()
	{
				 
		$aColumns = array(
		  'id','merchant_id',
		  'device_platform','user_type',
		  'device_id','enabled_push','date_created','id'
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
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS a.*,
		(
		select username from
		{{merchant}}
		where
		merchant_id =a.merchant_id 
		limit 0,1
		) as merchant_username,
		
		(
		select username from
		{{merchant_user}}
		where
		merchant_user_id =a.merchant_user_id 
		limit 0,1
		) as user_username,
		
		(
		select restaurant_name 
		from 
		{{merchant}}
		where 
		merchant_id=a.merchant_id
		) as merchant_name
		
		FROM
		{{mobile_device_merchant}} a
		WHERE 				
		status='active'
		AND
		( merchant_id > 0 OR merchant_user_id  > 0 )
		
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
			    
			    $link=Yii::app()->createUrl('merchantapp/index/push',array(
			      'id'=>$val['id']
			    ));
			    $psh=merchantApp::t("Send a push");
			    $action="<a class=\"send-a-push\" data-id=\"$val[id]\" href=\"$link\" title=\"$psh\">
			    <i class=\"fa fa-commenting\" ></i>
			    </a>";
			    
			    $username=$val['merchant_username'];
			    if ( $val['user_type']=="user"){
			    	$username=$val['user_username'];
			    }
			    
				$feed_data['aaData'][]=array(
				  $val['id'],
				  !empty($val['merchant_name'])?stripslashes($val['merchant_name']):merchantApp::t("No name"),
				  $val['device_platform'],
				  $username,
				  $val['user_type'],
				  "<p class=\"concat-text\">".$val['device_id']."..."."</p>",
				  $val['enabled_push']==1?merchantApp::t("Yes"):'',				  
				  $date_created,
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
	
	public function actionUploadCertificate()
	{
		require_once('Uploader.php');
		$path_to_upload=Yii::getPathOfAlias('webroot')."/upload/mt_certificate";
        $valid_extensions = array('pem'); 
        if(!file_exists($path_to_upload)) {	
           if (!@mkdir($path_to_upload,0777)){           	               	
           	    $this->msg=merchantApp::t("Error has occured cannot create upload directory");
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
        	$this->msg=merchantApp::t("upload done");        	        
			$this->details=Yii::app()->getBaseUrl(true)."/upload/".$_GET['uploadfile'];			
        }
        $this->jsonResponse();
	}	
	
	public function actionpushLogs()
	{
		$aColumns = array(
		  'id',
		  'merchant_id',
		  'merchant_id',
		  'device_platform',
		  'device_id',
		  'push_title',
		  'push_message',
		  'push_type',
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
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS a.*,
		(
		select username from
		{{merchant}}
		where
		merchant_id =a.merchant_id 
		limit 0,1
		) as merchant_username,
		
		(
		select username from
		{{merchant_user}}
		where
		merchant_user_id =a.merchant_user_id 
		limit 0,1
		) as user_username,
		
		(
		select restaurant_name 
		from
		{{merchant}}
		where
		merchant_id =a.merchant_id 
		limit 0,1
		)as merchant_name
		
		FROM
		{{mobile_merchant_pushlogs}} a
		WHERE 1		
		
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
			    
			    
			    $username=$val['merchant_username'];
			    if ( $val['user_type']=="user"){
			    	$username=$val['user_username'];
			    }
			    
				$feed_data['aaData'][]=array(
				  $val['id'],
				  stripslashes($val['merchant_name']),
				  $username,
				  $val['device_platform'],
				  '<span class="concat-text">'.$val['device_id'].'</span>',
				  $val['push_title'],
				  $val['push_message'],
				  merchantApp::t($val['push_type']),				  
				  '<span class="tag '.$val['status'].'">'.merchantApp::t($val['status']).'</span>',
				  $date_created
				);
			}
			if (isset($_GET['debug'])){
			   dump($feed_data);
			}
			$this->otableOutput($feed_data);	
		}
		$this->otableNodata();		
	}
	
	public function actionSendPush()
	{		
		$validator=new Validator();
		$req=array( 
		  'device_id'=>merchantApp::t("device id is missing"),
		  'push_title'=>merchantApp::t("push title is required"),
		  'push_message'=>merchantApp::t("push message is required"),
		);
		$validator->required($req,$this->data);
		if ( $validator->validate()){
			
			$params=array(			 
			  'merchant_id'=>isset($this->data['merchant_id'])?$this->data['merchant_id']:'',
			  'user_type'=>isset($this->data['user_type'])?$this->data['user_type']:'',
			  'merchant_user_id'=>isset($this->data['merchant_user_id'])?$this->data['merchant_user_id']:'',
			  'device_platform'=>isset($this->data['device_platform'])?$this->data['device_platform']:'',
			  'device_id'=>isset($this->data['device_id'])?$this->data['device_id']:'',
			  'push_title'=>isset($this->data['push_title'])?$this->data['push_title']:'',
			  'push_message'=>isset($this->data['push_message'])?$this->data['push_message']:'',
			  'date_created'=>FunctionsV3::dateNow(),
			  'ip_address'=>$_SERVER['REMOTE_ADDR'],
			  'push_type'=>"campaign"
			);			
			$DbExt=new DbExt; 
			if ($DbExt->insertData("{{mobile_merchant_pushlogs}}",$params)){
				$record_id=Yii::app()->db->getLastInsertID();
				$this->code=1;
				$this->msg=merchantApp::t("push has been saved. you can check the status on push notification logs section");
				
				/*PROCESS THE PUSH*/
				merchantApp::processPush($record_id);
				
			} else $this->msg=merchantApp::t("something went wrong during processing your request");
		} else $this->msg= $validator->getErrorAsHTML();
		$this->jsonResponse();
	}
	
} /*end class*/