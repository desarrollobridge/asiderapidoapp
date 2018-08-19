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
	}
	
	public function init()
	{
		FunctionsV3::handleLanguage();
		$lang=Yii::app()->language;				
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
    
	public function actionIndex()
	{				
		$this->page_title="Custom Search Trades";
		$this->render('home');
	}
	
	public function actionSaveSettings()
	{		
		if (isset($this->data['mobile_country_list'])){
			if (AddonMobileApp::isArray($this->data['mobile_country_list'])){
				
				Yii::app()->functions->updateOptionAdmin('mobile_default_image_not_available',
				isset($this->data['mobile_default_image_not_available'])?$this->data['mobile_default_image_not_available']:''
				);
												
				Yii::app()->functions->updateOptionAdmin('mobile_android_push_key',
				isset($this->data['mobile_android_push_key'])?$this->data['mobile_android_push_key']:''
				);
				
				$params=json_encode($this->data['mobile_country_list']);				
				Yii::app()->functions->updateOptionAdmin('mobile_country_list',$params);
				
				Yii::app()->functions->updateOptionAdmin('mobile_push_order_title',
				isset($this->data['mobile_push_order_title'])?$this->data['mobile_push_order_title']:''
				);
				
				Yii::app()->functions->updateOptionAdmin('mobile_push_order_message',
				isset($this->data['mobile_push_order_message'])?$this->data['mobile_push_order_message']:''
				);
				
				Yii::app()->functions->updateOptionAdmin('ios_passphrase',
				isset($this->data['ios_passphrase'])?$this->data['ios_passphrase']:''
				);
				
				Yii::app()->functions->updateOptionAdmin('ios_push_dev_cer',
				isset($this->data['ios_push_dev_cer'])?$this->data['ios_push_dev_cer']:''
				);
				
				Yii::app()->functions->updateOptionAdmin('ios_push_prod_cer',
				isset($this->data['ios_push_prod_cer'])?$this->data['ios_push_prod_cer']:''
				);
								
				Yii::app()->functions->updateOptionAdmin('ios_push_mode',
				isset($this->data['ios_push_mode'])?$this->data['ios_push_mode']:''
				);
				
				Yii::app()->functions->updateOptionAdmin('mobileapp_api_has_key',
				isset($this->data['mobileapp_api_has_key'])?trim($this->data['mobileapp_api_has_key']):''
				);
				
				Yii::app()->functions->updateOptionAdmin('show_addon_description',
				isset($this->data['show_addon_description'])?trim($this->data['show_addon_description']):''
				);
				
				Yii::app()->functions->updateOptionAdmin('mobile_menu',
				isset($this->data['mobile_menu'])?trim($this->data['mobile_menu']):''
				);
				
				Yii::app()->functions->updateOptionAdmin('mobile_save_cart_db',
				isset($this->data['mobile_save_cart_db'])?trim($this->data['mobile_save_cart_db']):''
				);
				
				Yii::app()->functions->updateOptionAdmin('force_app_default_lang',
				isset($this->data['force_app_default_lang'])?trim($this->data['force_app_default_lang']):''
				);
				
				Yii::app()->functions->updateOptionAdmin('app_current_location_results',
				isset($this->data['app_current_location_results'])?trim($this->data['app_current_location_results']):''
				);
				
				Yii::app()->functions->updateOptionAdmin('mobileapp_location_accuracy',
				isset($this->data['mobileapp_location_accuracy'])?trim($this->data['mobileapp_location_accuracy']):''
				);
				
				Yii::app()->functions->updateOptionAdmin('mobile_facebookid',
				isset($this->data['mobile_facebookid'])?trim($this->data['mobile_facebookid']):''
				);
				
				Yii::app()->functions->updateOptionAdmin('mobile_enabled_googlogin',
				isset($this->data['mobile_enabled_googlogin'])?trim($this->data['mobile_enabled_googlogin']):''
				);
				
				Yii::app()->functions->updateOptionAdmin('upload_push_icon',
				isset($this->data['upload_push_icon'])?trim($this->data['upload_push_icon']):''
				);
				
				Yii::app()->functions->updateOptionAdmin('upload_push_picture',
				isset($this->data['upload_push_picture'])?trim($this->data['upload_push_picture']):''
				);
				
				Yii::app()->functions->updateOptionAdmin('mobileapp_enabled_push_picture',
				isset($this->data['mobileapp_enabled_push_picture'])?trim($this->data['mobileapp_enabled_push_picture']):''
				);
				
				Yii::app()->functions->updateOptionAdmin('mobile_analytics_id',
				isset($this->data['mobile_analytics_id'])?trim($this->data['mobile_analytics_id']):''
				);
				
				Yii::app()->functions->updateOptionAdmin('mobile_analytics_enabled',
				isset($this->data['mobile_analytics_enabled'])?trim($this->data['mobile_analytics_enabled']):''
				);
				
				Yii::app()->functions->updateOptionAdmin('mobile_show_category_image',
				isset($this->data['mobile_show_category_image'])?trim($this->data['mobile_show_category_image']):''
				);
				
				$this->code=1;
				$this->msg=AddonMobileApp::t("settings saved");
				
			} else $this->msg=AddonMobileApp::t("location list is required");
		} else $this->msg=AddonMobileApp::t("location list is required");
		$this->jsonResponse();
	}
	
	public function actionUpload()
	{
		require_once('Uploader.php');
		$path_to_upload=Yii::getPathOfAlias('webroot')."/upload";
        $valid_extensions = array('jpeg', 'png' ,'jpg'); 
        if(!file_exists($path_to_upload)) {	
           if (!@mkdir($path_to_upload,0777)){           	               	
           	    $this->msg=AddonMobileApp::t("Error has occured cannot create upload directory");
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
        	$this->msg=AddonMobileApp::t("upload done");        	        
			$this->details=Yii::app()->getBaseUrl(true)."/upload/".$_GET['uploadfile'];			
        }
        $this->jsonResponse();
	}
	
	public function actionRegisteredDeviceList()
	{
		
		/*$aColumns = array(
		  'client_id','mobile_device_platform','first_name',
		  'mobile_device_id','mobile_enabled_push','mobile_country_code_set',
		  'date_created','client_id'
		);*/
		
		$aColumns = array(
		  'client_id','device_platform','client_name',
		  'device_id','enabled_push','country_code_set','date_created','client_id'
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
		
		$and=" AND status in ('active')";
		$and.=" AND device_id !='' ";
		
		$and.=" AND EXISTS (
		   select client_id from {{client}}
		   where
		   client_id=a.client_id
		) ";
				
		$stmt="SELECT SQL_CALC_FOUND_ROWS a.*
		FROM
		{{mobile_registered_view}} a
		WHERE 1		
		$sWhere
		$and
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
			    
			    $link=Yii::app()->createUrl('mobileapp/index/push',array(
			      'id'=>$val['client_id']
			    ));
			    $psh=AddonMobileApp::t("Send a push");
			    $action="<a class=\"send-a-push\" data-id=\"$val[client_id]\" href=\"$link\" title=\"$psh\">
			    <i class=\"fa fa-commenting\" ></i>
			    </a>";
			    
				$feed_data['aaData'][]=array(
				  $val['client_id'],
				  AddonMobileApp::t($val['device_platform']),
				  $val['client_name'],
				  "<p class=\"concat-text\">".$val['device_id']."..."."</p>",
				  $val['enabled_push']==1?AddonMobileApp::t("Yes"):'',
				  $val['country_code_set'],
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
	
	public function actionpushLogs()
	{
   	    $aColumns = array(
		  'id','push_type','client_name','device_platform',
		  'push_title','push_message','date_created'
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
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS *
		FROM
		{{mobile_push_logs}}
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
			    					    			    			    
			    switch ($val['status'])
			    {
			    	case "process":
			    	  $class="bg-success";
			    	  break;
			    	case "pending":
			    	   $class="bg-danger";
			    	   break;	
			    	default:  
			    	  $class="bg-warning";
			    	   break;	
			    }
			    
			    $date_created.="<br><span class='$class'>".AddonMobileApp::t($val['status'])."</span>";
			    			    
				$feed_data['aaData'][]=array(
				  $val['id'],
				  AddonMobileApp::t($val['push_type']),
				  $val['client_name'],
				  AddonMobileApp::t($val['device_platform']),
				  $val['push_title'],
				  $val['push_message'],
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
		  'device_id'=>AddonMobileApp::t("device id is missing"),
		  'push_title'=>AddonMobileApp::t("push title is required"),
		  'push_message'=>AddonMobileApp::t("push message is required"),
		);
		$validator->required($req,$this->data);
		if ( $validator->validate()){
			$params=array(
			  'client_id'=>isset($this->data['client_id'])?$this->data['client_id']:'',
			  'client_name'=>isset($this->data['client_name'])?$this->data['client_name']:'',
			  'device_platform'=>isset($this->data['device_platform'])?$this->data['device_platform']:'',
			  'device_id'=>isset($this->data['device_id'])?$this->data['device_id']:'',
			  'push_title'=>isset($this->data['push_title'])?$this->data['push_title']:'',
			  'push_message'=>isset($this->data['push_message'])?$this->data['push_message']:'',
			  'date_created'=>AddonMobileApp::dateNow(),
			  'ip_address'=>$_SERVER['REMOTE_ADDR'],
			  'push_type'=>"campaign"
			);
			$DbExt=new DbExt; 
			if ($DbExt->insertData("{{mobile_push_logs}}",$params)){
				$this->code=1;
				$this->msg=AddonMobileApp::t("push has been saved. you can check the status on push notification logs section");
			} else $this->msg=AddonMobileApp::t("something went wrong during processing your request");
		} else $this->msg= $validator->getErrorAsHTML();
		$this->jsonResponse();
	}
	
    private function parseValidatorError($error='')
	{
		$error_string='';
		if (is_array($error) && count($error)>=1){
			foreach ($error as $val) {
				$error_string.="$val\n";
			}
		}
		return $error_string;		
	}		
	
	public function actionUploadCertificate()
	{
		require_once('Uploader.php');
		$path_to_upload=Yii::getPathOfAlias('webroot')."/upload/certificate";
        $valid_extensions = array('pem'); 
        if(!file_exists($path_to_upload)) {	
           if (!@mkdir($path_to_upload,0777)){           	               	
           	    $this->msg=AddonMobileApp::t("Error has occured cannot create upload directory");
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
        	$this->msg=AddonMobileApp::t("upload done");        	        
			$this->details=Yii::app()->getBaseUrl(true)."/upload/".$_GET['uploadfile'];			
        }
        $this->jsonResponse();
	}
	
	public function actionBroadCastList()
	{
		$aColumns = array(
		  'broadcast_id','push_title','push_message','send_to',
		  'status','date_created','push_title'
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
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS *
		FROM
		{{mobile_broadcast}}
		WHERE 1		
		$sWhere
		$sOrder
		$sLimit
		";
		if (isset($_GET['debug'])){
		   dump($stmt);
		}
		
		$platform=AddonMobileApp::platFormList();
		
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
			    
			    $action_link=Yii::app()->createUrl('mobileapp/index/broadcastdetails',array(
			      'id'=>$val['broadcast_id']
			    ));			    
			    $actions="<a href=\"$action_link\">".AddonMobileApp::t("View details")."</a>";
			    					    			    			    
			    switch ($val['status'])
			    {
			    	case "process":
			    	  $class="bg-success";
			    	  break;
			    	case "pending":
			    	   $class="bg-danger";
			    	   break;	
			    	default:  
			    	  $class="bg-warning";
			    	   break;	
			    }
			    
			    $date_created.="<br><span class='$class'>".AddonMobileApp::t($val['status'])."</span>";
			    
				$feed_data['aaData'][]=array(
				  $val['broadcast_id'],
				  $val['push_title'],
				  $val['push_message'],
				  AddonMobileApp::t($platform[$val['device_platform']]),	
				  $date_created,
				  $actions	  
				);
			}
			if (isset($_GET['debug'])){
			   dump($feed_data);
			}
			$this->otableOutput($feed_data);	
		}
		$this->otableNodata();
	}
	
	public function actionSaveBroadcast()
	{		
		$validator=new Validator();
		$req=array( 
		  'push_title'=>AddonMobileApp::t("push title is required"),		  
		  'push_message'=>AddonMobileApp::t("push message is required"),		  
		);
		$validator->required($req,$this->data);
		if ( $validator->validate()){
			
			$params=array(
			 'push_title'=>$this->data['push_title'],
			 'push_message'=>$this->data['push_message'],
			 'device_platform'=>$this->data['device_platform'],
			 'date_created'=>AddonMobileApp::dateNow(),
			 'ip_address'=>$_SERVER['REMOTE_ADDR']
			);		
			$DbExt=new DbExt; 
			if ($DbExt->insertData("{{mobile_broadcast}}",$params)){
				$this->code=1;
				$this->msg=AddonMobileApp::t("broadcast saved");
			} else $this->msg=AddonMobileApp::t("something went wrong during processing your request");
			
		} else $this->msg=$validator->getErrorAsHTML();
		$this->jsonResponse();
	}
	
	public function actionBroadCastDetails()
	{
		$aColumns = array(
		  'id','push_type','client_name','device_platform',
		  'push_title','push_message','date_created'
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
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS *
		FROM
		{{mobile_push_logs}}
		WHERE 
		broadcast_id=".AddonMobileApp::q($_GET['broadcast_id'])."
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
			    					    			    			    
			    switch ($val['status'])
			    {
			    	case "process":
			    	  $class="bg-success";
			    	  break;
			    	case "pending":
			    	   $class="bg-danger";
			    	   break;	
			    	default:  
			    	  $class="bg-warning";
			    	   break;	
			    }
			    
			    $date_created.="<br><span class='$class'>".AddonMobileApp::t($val['status'])."</span>";
			    			    
				$feed_data['aaData'][]=array(
				  $val['id'],
				  $val['push_type'],
				  $val['client_name'],
				  $val['device_platform'],
				  $val['push_title'],
				  $val['push_message'],
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
			}*/			
			$mobile_dictionary=json_encode($this->data);			
			$unicode=3;
		}				
		Yii::app()->functions->updateOptionAdmin('mobile_dictionary',$mobile_dictionary);
		$this->code=1;
		$this->msg=AddonMobileApp::t("translation saved");
		$this->details=$unicode;
		$this->jsonResponse();
	}
	
    public function actionExportLang()
	{
		$content=Yii::app()->functions->getOptionAdmin('mobile_dictionary');
		header('Content-disposition: attachment; filename=mobile_dictionary.json');
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
           	    $this->msg=AddonMobileApp::t("Error has occured cannot create upload directory");
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
        	$this->msg=AddonMobileApp::t("upload done. kindly refresh your browser to see the changes affect"); 
			$this->details=Yii::app()->getBaseUrl(true)."/upload/".$_GET['uploadfile'];	
			
			$content = @file_get_contents($path_to_upload ."/".$_GET['uploadfile']);
			Yii::app()->functions->updateOptionAdmin('mobile_dictionary',$content);
        }
        $this->jsonResponse();
	}	
	
	public function actionpagesList()
	{
		$aColumns = array(
		  'page_id','title',
		  'content','sequence',
		  'date_created','page_id'
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
				
		$stmt="SELECT SQL_CALC_FOUND_ROWS a.*
		FROM
		{{mobile_pages}} a
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
			    
			    $link=Yii::app()->createUrl('mobileapp/index/pagenew',array(
			      'id'=>$val['page_id']
			    ));
			    $psh=AddonMobileApp::t("Edit");
			    
			    $action="<a href=\"$link\" title=\"$psh\">
			    <i class=\"fa fa-pencil-square-o\" ></i>
			    </a>";
			    
			    
			    $action.='<a href="javascript:;" class="delete-pages" data-id="'.$val['page_id'].'">
			    <i class="fa fa-trash"></i></a>';
			    
			    $status='<span class="tag '.$val['status'].'">'.AddonMobileApp::t($val['status']).'</span>';
			    
			    $use_html='';
			    if ($val['use_html']==2){
			    	$use_html = '<i class="fa fa-check"></i>';
			    }
			    
				$feed_data['aaData'][]=array(
				  $val['page_id'],
				  stripslashes($val['title']),
				  "<p class=\"concat-text\">".stripslashes($val['content'])."..."."</p>",				  
				  $val['icon'],
				  $use_html,
				  $val['sequence'],  
				  $status.'<br/>'.$date_created,
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
	
	public function actionsavePages()
	{		
		$validator=new Validator();
		$req=array( 
		  'title'=>AddonMobileApp::t("title is required"),
		  'content'=>AddonMobileApp::t("content is required"),		  
		);
		$validator->required($req,$this->data);
		if ( $validator->validate()){
			$params=array(
			  'title'=>$this->data['title'],
			  'content'=>$this->data['content'],
			  'icon'=>isset($this->data['icon'])?$this->data['icon']:'',
			  'sequence'=>isset($this->data['sequence'])?$this->data['sequence']:0,
			  'status'=>$this->data['status'],
			  'date_created'=>FunctionsV3::dateNow(),
			  'ip_address'=>$_SERVER['REMOTE_ADDR'],
			  'use_html'=>isset($this->data['use_html'])?$this->data['use_html']:1
			);
			
			if ( Yii::app()->functions->multipleField()==2){				
				if ( $fields=FunctionsV3::getLanguageList(false)){
					foreach ($fields as $f_val){
						$params["lang_title_$f_val"] = isset($this->data["lang_title_$f_val"])?$this->data["lang_title_$f_val"]:'';
						$params["lang_content_$f_val"] = isset($this->data["lang_content_$f_val"])?$this->data["lang_content_$f_val"]:'';
					}
				}				
			}
			
			$DbExt=new DbExt; 
			
			if (isset($this->data['id'])){
				unset($params['date_created']);
				$params['date_modified']=FunctionsV3::dateNow();
				if ( $DbExt->updateData("{{mobile_pages}}",$params,'page_id',$this->data['id'])){
					$this->code = 1;
					$this->msg = AddonMobileApp::t("successfully updated");
					$this->details='';
				} else $this->msg = AddonMobileApp::t("Failed cannot saved records");
			} else {
				if ( $DbExt->insertData("{{mobile_pages}}",$params)){
					$this->details=Yii::app()->createUrl('mobileapp/index/pagenew',array(
					  'id'=>Yii::app()->db->getLastInsertID()
					));
					$this->code = 1;
					$this->msg = AddonMobileApp::t("Successful");
				} else $this->msg = AddonMobileApp::t("Failed cannot saved records");
			}
			
		} else $this->msg= $validator->getErrorAsHTML();
		$this->jsonResponse();
	}
	
	public function actiondeletePages()
	{
		$DbExt=new DbExt; 
		$DbExt->qry("DELETE FROM
		{{mobile_pages}}
		WHERE
		page_id=".AddonMobileApp::q($this->data['page_id'])."
		");
		$this->code=1;
		$this->msg="OK";
		$this->details='';		
		$this->jsonResponse();
	}
	
} /*end class*/