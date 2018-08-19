<?php
if (!isset($_SESSION)) { session_start(); }

class IndexController extends CController
{
	public $layout='layout';	
	public $body_class='';
	public $is_newupdate=false;
	
	public function init()
	{			
		 // set website timezone
		 $website_timezone=Yii::app()->functions->getOptionAdmin("website_timezone");		 		 
		 if (!empty($website_timezone)){		 	
		 	Yii::app()->timeZone=$website_timezone;
		 }		 				 
		 				 
		 Driver::handleLanguage();	
	}
	
	public function beforeAction($action)
	{			
		/*if (Yii::app()->controller->module->require_login){
			if(! DriverModule::islogin() ){
			   $this->redirect(Yii::app()->createUrl('/admin/noaccess'));
			   Yii::app()->end();		
			}
		}*/
		$action_name= $action->id ;
		$accept_controller=array('login','ajax');
		if(!Driver::islogin()){			
			if(!in_array($action_name,$accept_controller)){
				$this->redirect(Yii::app()->createUrl('/driver/index/login'));
			}
		}
		
		$cs = Yii::app()->getClientScript();
		$jslang=json_encode(Driver::jsLang());
		$cs->registerScript(
		  'jslang',
		 "var jslang=$jslang",
		  CClientScript::POS_HEAD
		);
				
		
		//$js_lang_validator=Yii::app()->functions->jsLanguageValidator();
		//$js_lang=Yii::app()->functions->jsLanguageAdmin();
		
		$js_lang_validator=Driver::jsLanguageValidator();
		$js_lang=Driver::jsLang2();
		
		$cs->registerScript(
		  'jsLanguageValidator',
		  'var jsLanguageValidator = '.json_encode($js_lang_validator).'
		  ',
		  CClientScript::POS_HEAD
		);				
		$cs->registerScript(
		  'js_lang',
		  'var js_lang = '.json_encode($js_lang).'
		  ',
		  CClientScript::POS_HEAD
		);
				
		$website_title=getOptionA('website_title');
		$website_title_1=getOptionA('driver_website_title');
		if(!empty($website_title)){
		   $this->setPageTitle("$website_title -" .ucfirst($action->getId()));
		}
		if(!empty($website_title_1)){
		   $this->setPageTitle("$website_title_1 -" .ucfirst($action->getId()));
		}
		
		// 
		$driver_enabled_auto_assign=getOptionA('driver_enabled_auto_assign');
		if($driver_enabled_auto_assign>0){
			$cs->registerScript(
			  'driver_enabled_auto_assign',
			 "var driver_enabled_auto_assign=$driver_enabled_auto_assign",
			  CClientScript::POS_HEAD
			);
		}
		
		$map_hide_pickup= getOptionA('driver_hide_pickup_task');
		$cs->registerScript(
		  'map_hide_pickup',
		 "var map_hide_pickup='$map_hide_pickup';",
		  CClientScript::POS_HEAD
		);
		
		$map_hide_delivery_task= getOptionA('driver_hide_delivery_task');
		$cs->registerScript(
		  'map_hide_delivery_task',
		 "var map_hide_delivery_task='$map_hide_delivery_task';",
		  CClientScript::POS_HEAD
		);
		
		$map_hide_successful_task= getOptionA('driver_hide_successful_task');
		$cs->registerScript(
		  'map_hide_successful_task',
		 "var map_hide_successful_task='$map_hide_successful_task';",
		  CClientScript::POS_HEAD
		);
		
		$calendar_language=getOptionA('driver_calendar_language');
		$cs->registerScript(
		  'calendar_language',
		 "var calendar_language='$calendar_language';",
		  CClientScript::POS_HEAD
		);
		
		$merchant_calendar_language=Driver::getOption('merchant_driver_calendar_language');
		$cs->registerScript(
		  'merchant_calendar_language',
		 "var merchant_calendar_language='$merchant_calendar_language';",
		  CClientScript::POS_HEAD
		);
		
		 /*check for new update tables*/
        if(Driver::checkNewVersion()){
        	$this->is_newupdate=true;
        }
		
		return true;				
	}
	
	public function actionLogin()
	{
		$this->body_class='login-body';
		$this->render('login');
	}
	
	public function actionLogout()
	{
		unset($_SESSION['driver']);
		$this->redirect(Yii::app()->createUrl('/driver/index/login'));
	}
	
	public function actionIndex(){
				
		$this->body_class="dashboard";		
		$this->render('dashboard');
	}	

	public function actionAgents()
	{
		$this->render('agents-list');
	}
	
	public function actionTasks()
	{
		$this->render('task-list');
	}
	
	public function actionSettings()
	{		
		
		if(!$order_status_list=Yii::app()->functions->orderStatusList()){           
			
        }   
        $country_list=require_once('CountryCode.php');
                
        if(is_array($order_status_list) && count($order_status_list)>=1){
        	foreach ($order_status_list as $key=>$val) {        		
        		$order_status_list[$key]=t($val);
        	}
        }
        
        $selected_merchant=getOptionA('driver_allowed_merchant_list');
        if(!empty($selected_merchant)){
        	$selected_merchant=json_decode($selected_merchant,true);
        }
        
        $selected_merchant_to_admin=getOptionA('driver_merchant_task_to_admin');
        if(!empty($selected_merchant_to_admin)){
        	$selected_merchant_to_admin=json_decode($selected_merchant_to_admin,true);
        }
        
        $driver_merchant_block=getOptionA('driver_merchant_block');
        if(!empty($driver_merchant_block)){
        	$driver_merchant_block=json_decode($driver_merchant_block,true);
        }
        
                    
        if ( Driver::getUserType()=="merchant"){
        	$this->render('settings-merchant',array(
        	  'country_list'=>$country_list,
        	  'language_list'=>Driver::getLanguageList()
        	));        
        } else {        	        	
			$this->render('settings',array(
			  'order_status_list'=>$order_status_list,
			  'country_list'=>$country_list,
			  'ios_push_dev_cer'=>getOptionA('driver_ios_push_dev_cer'),
			  'ios_push_prod_cer'=>getOptionA('driver_ios_push_prod_cer'),
			  'selected_merchant'=>$selected_merchant,
			  'selected_merchant_to_admin'=>$selected_merchant_to_admin,
			  'driver_merchant_block'=>$driver_merchant_block,
			  'language_list'=>Driver::getLanguageList()
			));
        }
	}
	
	public function actionTeams()
	{
		$this->render('teams');
	}
	
	public function actionlanguage()
	{
		/*$lang=Driver::availableLanguages();
		$dictionary=require_once('MobileTranslation.php');		
		
		$mobile_dictionary=getOptionA('driver_mobile_dictionary');
        if (!empty($mobile_dictionary)){
	       $mobile_dictionary=json_decode($mobile_dictionary,true);
        } else $mobile_dictionary=false;
		
		$this->render('language',array(
		  'lang'=>$lang,
		  'dictionary'=>$dictionary,
		  'mobile_dictionary'=>$mobile_dictionary
		));*/
		
		$this->render('language-settings');
	}
	
	public function actionNotifications()
	{
		$user_type=Driver::getUserType();
		if ($user_type=="admin"){
			$this->render('notifications');
		} else $this->render('error',array(
		  'msg'=>Driver::t("Sorry but you don't have access to this page")
		));
	}
	
	public function actionPushlogs()
	{
		$this->render('push-logs');
	}
	
	public function actionReports()
	{
		$cs = Yii::app()->getClientScript(); 
		
		Yii::app()->clientScript->registerScriptFile(
        "//amcharts.com/lib/3/amcharts.js",CClientScript::POS_END);		
        
        Yii::app()->clientScript->registerScriptFile(
        "//amcharts.com/lib/3/serial.js",CClientScript::POS_END);		
        
        Yii::app()->clientScript->registerScriptFile(
        "//amcharts.com/lib/3/themes/light.js",CClientScript::POS_END);		
		
        $team_list=Driver::teamList( Driver::getUserType(),Driver::getUserId());
		if($team_list){
			 $team_list=Driver::toList($team_list,'team_id','team_name',
			   Driver::t("All Team")
			 );
		}
		
		$all_driver=Driver::getAllDriver(
           Driver::getUserType(),Driver::getUserId()
        );   

        $start= date('Y-m-d', strtotime("-7 day") );
	    $end=date("Y-m-d", strtotime("+1 day")); 
        
		$this->render('reports',array(
		  'team_list'=>$team_list,
		  'all_driver'=>$all_driver,
		  'start_date'=>$start,
		  'end_date'=>$end
		));
	}
	
	public function actionAssignment()
	{
		$this->render('assignment');
	}
	
	public function actionAgentsTrackback()
	{
		
		$this->render('agents-trackback',array(
		   'driver_list'=>Driver::driverDropDownList( Driver::getUserType(), Driver::getUserId() ),
		   'track_list'=>Driver::backTrackList( Driver::getUserType(), Driver::getUserId()  )
		));
	}
	
	public function actionBulkLogs()
	{
		$this->render('bulklogs');
	}
	
	public function actionSMSlogs()
	{
		$this->render('sms-logs');
	}
	
	public function actionEmailLogs()
	{
		$this->render('email-logs');
	}
	
	public function actionsetlanguage()
	{				
		if (isset($_GET['lang'])){			
			$redirect = Yii::app()->createUrl('/driver/index/settings',array(
			  'lang'=>$_GET['lang']
			));
		} else {
			$redirect = Yii::app()->createUrl('/driver/index/settings');
		}
		/*echo $redirect;
		die();*/
		$this->redirect($redirect);
	}
		
}/* end class*/