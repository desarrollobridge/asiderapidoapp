<?php
if (!isset($_SESSION)) { session_start(); }

class IndexController extends CController
{
	public $layout='layout';	
	
	public function init()
	{
		FunctionsV3::handleLanguage();
		$lang=Yii::app()->language;				
		$cs = Yii::app()->getClientScript();
		$cs->registerScript(
		  'lang',
		  "var lang='$lang';",
		  CClientScript::POS_HEAD
		);
		
	   $table_translation=array(
	      "tablet_1"=>AddonMobileApp::t("No data available in table"),
    	  "tablet_2"=>AddonMobileApp::t("Showing _START_ to _END_ of _TOTAL_ entries"),
    	  "tablet_3"=>AddonMobileApp::t("Showing 0 to 0 of 0 entries"),
    	  "tablet_4"=>AddonMobileApp::t("(filtered from _MAX_ total entries)"),
    	  "tablet_5"=>AddonMobileApp::t("Show _MENU_ entries"),
    	  "tablet_6"=>AddonMobileApp::t("Loading..."),
    	  "tablet_7"=>AddonMobileApp::t("Processing..."),
    	  "tablet_8"=>AddonMobileApp::t("Search:"),
    	  "tablet_9"=>AddonMobileApp::t("No matching records found"),
    	  "tablet_10"=>AddonMobileApp::t("First"),
    	  "tablet_11"=>AddonMobileApp::t("Last"),
    	  "tablet_12"=>AddonMobileApp::t("Next"),
    	  "tablet_13"=>AddonMobileApp::t("Previous"),
    	  "tablet_14"=>AddonMobileApp::t(": activate to sort column ascending"),
    	  "tablet_15"=>AddonMobileApp::t(": activate to sort column descending"),
    	  'are_you_sure'=>AddonMobileApp::t("Are you sure")
	   );	
	   $js_translation=json_encode($table_translation);
		
	   $cs->registerScript(
		  'js_translation',
		  "var js_translation=$js_translation;",
		  CClientScript::POS_HEAD
		);	
	   	
	}
	
	public function beforeAction($action)
	{		
		if (Yii::app()->controller->module->require_login){
			if(!Yii::app()->functions->isAdminLogin()){
			   $this->redirect(Yii::app()->createUrl('/admin/noaccess'));
			   Yii::app()->end();		
			}
		}
		
		$action_name = "mobileapp";	
		$aa_access=Yii::app()->functions->AAccess();
	    $menu_list=Yii::app()->functions->AAmenuList();		    
	    if (in_array($action_name,(array)$menu_list)){
	    	if (!in_array($action_name,(array)$aa_access)){	   	    		
	    		$this->redirect(Yii::app()->createUrl('/admin/noaccess'));
	    	}
	    }	    
		
		return true;
	}
	
	public function actionIndex(){
		$this->redirect(Yii::app()->createUrl('/mobileapp/index/settings'));
	}		
	
	public function actionSettings()
	{
				
		$country_list=require_once('CountryCode.php');
		$mobile_country_list=getOptionA('mobile_country_list');
		if (!empty($mobile_country_list)){
			$mobile_country_list=json_decode($mobile_country_list);
		} else $mobile_country_list=array();
				
		$default_image=AddonMobileApp::getImage(getOptionA('mobile_default_image_not_available'));
		
		$this->render('settings',array(
		  'country_list'=>$country_list,
		  'default_image_url'=>$default_image,
		  'mobile_country_list'=>$mobile_country_list,
		  'upload_push_icon'=>getOptionA('upload_push_icon'),
		  'upload_push_picture'=>getOptionA('upload_push_picture')
		));
	}
	
	public function actionPushLogs()
	{		
		$this->render('pushlogs',array(		  
		));
	}
	
	public function actionregistereddevice()
	{
		$this->render('registered_device',array(		  
		));
	}
	
	public function actionPushHelp()
	{
		$this->render('pushhelp',array(		  
		));
	}
	
	public function actionPush()
	{		
		if ( $res=AddonMobileApp::getRegisteredDeviceByClientID($_GET['id'])){
	    $this->render('push_form',array(		  
	      'data'=>$res
		));
		} else $this->render('error',array(
		  'msg'=> AddonMobileApp::t("cannot find records")
		));
	}
	
	public function actionBroadcast()
	{
		$this->render('broadcast-list',array(		  
		));
	}
	
	public function actionBroadcastNew()
	{
		$this->render('broadcast-new',array(		  
		));
	}
	
	public function actionbroadcastdetails()
	{		
		if ( AddonMobileApp::getBroadcast($_GET['id'])){
			$this->render('broadcast-details',array(		  
		    ));
		} else  $this->render('error',array(
		  'msg'=> AddonMobileApp::t("cannot find records")
		));
	}
	
	public function actiontranslation()
	{
		$this->render('translation',array(		  
		));
	}
	
	public function actionpages()
	{
		$enabled_multiple_translation = getOptionA('enabled_multiple_translation');
		if ($enabled_multiple_translation==2){
			DBTableManager::alterTablePages();
		}
		$this->render('pages',array(		  
		));
	}
	
	public function actionpagenew()
	{
		$data='';
		if (isset($_GET['id'])){
			$data = AddonMobileApp::getPagesByID($_GET['id']);
		}
		
		$this->render('page_new',array(
		  'data'=>$data		
		));
	}
	
} /*end class*/