<?php
/**
 * AdminController Controller
 *
 */
if (!isset($_SESSION)) { session_start(); }

class AdminController extends CController
{
	public $layout='admin_tpl';	
	public $crumbsTitle='';
	
	/*public function accessRules()
	{				
	}
	
	public function filters()
	{		
	}
	*/
	public function beforeAction($action)
    {    	    	
    	$action_name= $action->id ;
    	$accept_controller=array('login','ajax','noaccess');
	    if(!Yii::app()->functions->isAdminLogin() )
	    {
	    	if (!in_array($action_name,$accept_controller)){	    		
	    	   if ( Yii::app()->functions->has_session){
	    	   	    $message_out=t("You were logout because someone login with your account");
	    	   	    $this->redirect(array('admin/login/?message='.urlencode($message_out)));
	    	   } else $this->redirect(array('admin/login'));	           
	    	}
	    }	
	    
	    $aa_access=Yii::app()->functions->AAccess();
	    $menu_list=Yii::app()->functions->AAmenuList();	    
	    
	    /*dump($action_name); 
	    dump($menu_list);*/
	    
	    switch ($action_name) {
	    	case "viewemail":
	    		$action_name='emailogs';
	    		break;
	    
	    	case "merchantAdd":
	    	case "merchantadd":
	    		$action_name='merchant';
	    		break;
	    	default:
	    		break;
	    }
	    
	    if (in_array($action_name,(array)$menu_list)){
	    	if (!in_array($action_name,(array)$aa_access)){	    			    		
	    		$this->redirect(Yii::app()->createUrl('/admin/noaccess'));
	    	}
	    } 	    
	    	    	    
	    //echo Yii::app()->language;
	    return true;	    
    }
	
    public function init()
	{				
			
		 $name=Yii::app()->functions->getOptionAdmin('website_title');
		 if (!empty($name)){		 	
		 	 Yii::app()->name = $name;
		 }	
		 		 
		 // set website timezone
		 $website_timezone=Yii::app()->functions->getOptionAdmin("website_timezone");		 
		 if (!empty($website_timezone)){		 	
		 	Yii::app()->timeZone=$website_timezone;
		 }		 		

		 $ajax_admin=Yii::app()->createUrl('/ajaxadmin');
		 
		 $cs = Yii::app()->getClientScript();
		 $cs->registerScript(
		  'ajax_admin',
		  "var ajax_admin='$ajax_admin';",
		  CClientScript::POS_HEAD
		);
		
		FunctionsV3::handleLanguage();
		$lang=Yii::app()->language;		
		$cs->registerScript(
		  'lang',
		  "var lang='$lang';",
		  CClientScript::POS_HEAD
		);
		
		$order_notification=getOptionA('admin_disabled_order_notification');
		$cs->registerScript(
		  'order_notification',
		  "var order_notification='$order_notification';",
		  CClientScript::POS_HEAD
		);
		
		$order_notification_sounds=getOptionA('admin_disabled_order_notification_sounds');
		$cs->registerScript(
		  'order_notification_sounds',
		  "var order_notification_sounds='$order_notification_sounds';",
		  CClientScript::POS_HEAD
		);
		
		/*ADD SECURITY VALIDATION*/
		$yii_session_token=session_id();		
		$cs->registerScript(
		  'yii_session_token',
		 "var yii_session_token='$yii_session_token';",
		  CClientScript::POS_HEAD
		);				
		$csrfTokenName = Yii::app()->request->csrfTokenName;
        $csrfToken = Yii::app()->request->csrfToken;
		$cs->registerScript(
		  "$csrfTokenName",
		 "var $csrfTokenName='$csrfToken';",
		  CClientScript::POS_HEAD
		);
		
	}
	
	public function actionDashboard()
	{
		if ( !Yii::app()->functions->isAdminLogin()){			
			$this->layout='login_tpl';
			$this->render('login');
		} else {						
			
			$this->crumbsTitle=Yii::t("default","Dashboard");		
			$this->render('dashboard');			
		}		
	}
				  
	public function actionIndex()
	{					
		if ( !Yii::app()->functions->isAdminLogin()){			
			$this->layout='login_tpl';
			$this->render('login');
		} else {						
			$aa_access=Yii::app()->functions->AAccess();
			if (in_array('dashboard',(array)$aa_access)){
								
				$this->crumbsTitle=Yii::t("default","Dashboard");		
				$this->render('dashboard');			
			} else $this->render('error',array('msg'=>t("Sorry but you don't have access to dashboard")));			
		}		
	}	
	
	public function actionLogin()
	{		
		if (isset($_GET['logout'])){
			//Yii::app()->request->cookies['kr_user'] = new CHttpCookie('kr_user', ""); 			
			//Yii::app()->request->cookies['kr_admin_lang_id'] = new CHttpCookie('kr_admin_lang_id', ""); 			
			unset($_SESSION['kr_user']);
			unset($_SESSION['kr_user_session']);
		}		
		$this->layout='login_tpl';
	    $this->render('login');
	}
	
	public function actionAjax()
	{			
		if (isset($_REQUEST['tbl'])){
		   $data=$_REQUEST;	
		} else $data=$_POST;
				
		if (isset($data['debug'])){
			dump($data);
		}
		
			
		/*ADD SECURITY VALIDATION TO ALL REQUEST*/
		$validate_request_session = Yii::app()->params->validate_request_session;
		$validate_request_csrf = Yii::app()->params->validate_request_csrf;
				
		$class_validate=new AjaxAdmin;
		
		if($validate_request_session){
			$session_id=session_id();		
			if($data['yii_session_token']!=$session_id){			
				$class_validate->msg = t("Session token not valid");
				echo $class_validate->output();
				Yii::app()->end();
			}		
		}
						
		if($validate_request_csrf){
			if ( $data[Yii::app()->request->csrfTokenName] != Yii::app()->getRequest()->getCsrfToken()){
				$class_validate->msg = t("Request token not valid");
				echo $class_validate->output();
				Yii::app()->end();
			}		
		}
		/*ADD SECURITY VALIDATION TO ALL REQUEST*/	
		
		/**add ons */     
		if (isset($data['addon'])){
			Yii::import('application.extension.AddonExport.*');
			$class=new $data['addon'];
			$class->data=$data;
			if (method_exists($class,$data['action'])){
				$class->$data['action']();	    
	            echo $class->output();
			}
			yii::app()->end();
		}
		/**add ons */     
		
		$class=new AjaxAdmin;
	    $class->data=$data;	 	    
	    if (method_exists($class,$data['action'])){
	    	 $action_name=$data['action'];	    	 
	    	 $class->$action_name();
	         echo $class->output();
	    } else {
	    	 $class=new Ajax;
	    	 $class->data=$data;	    	 
	    	 $action_name=$data['action'];	    	 
	    	 $class->$action_name();
	         echo $class->output();
	    }
	    yii::app()->end();
	}	
	
	public function actionMerchant()
	{
		$this->crumbsTitle=Yii::t("default","Merchant");
		$this->render('merchant-list');
	}
	
	public function actionMerchantAdd()
	{
		$this->crumbsTitle=Yii::t("default","Merchant Add");
		$this->render('merchant-add');
	}
	
	public function actionPackages()
	{		
		if (isset($_GET['Do'])){
		   $this->crumbsTitle=Yii::t("default","Packages Sort");
		   $this->render('packages-sort');
		} else {
		   $this->crumbsTitle=Yii::t("default","Packages");
		   $this->render('packages-list');
		}
	}
	
	public function actionPackagesAdd()
	{
		$this->crumbsTitle=Yii::t("default","Packages add");
		$this->render('packages-add');
	}
	
	public function actionPaypalSettings()
	{
		$this->crumbsTitle=Yii::t("default","Paypal Settings");
		$this->render('paypal-settings');
	}
	
	public function actionSettings()
	{
		$this->crumbsTitle=Yii::t("default","Settings");
		$this->render('settings');
	}
	
	public function actionRptMerchantReg()
	{
		$this->crumbsTitle=Yii::t("default","Merchant Registration");
		$this->render('rpt-merchant-reg');
	}
	
	public function actionRptMerchantPayment()
	{
		$this->crumbsTitle=Yii::t("default","Merchant Payment");
		$this->render('rpt-merchant-payment');
	}
	
	public function actionSponsoredMerchantList()
	{
		if (isset($_GET['Do'])){
			$this->crumbsTitle=Yii::t("default","Sponsored Merchant Add");
		    $this->render('sponsored-merchant-add');
		} else {
		    $this->crumbsTitle=Yii::t("default","Sponsored Merchant List");
		    $this->render('sponsored-merchant');	
		}		
	}	
	
	public function actionManageCurrency()
	{
		if (isset($_GET['Do'])){
			$this->crumbsTitle=Yii::t("default","Currency Add");
		    $this->render('currency-list-add');
		} else {
		    $this->crumbsTitle=Yii::t("default","Currency List");
		    $this->render('currency-list');	
		}		
	}
	
	public function actionCuisine()
	{
	   if (isset($_GET['Do'])){
	   	   if ($_GET['Do']=="Add"){
			   $this->crumbsTitle=Yii::t("default","Cuisine Add");
		       $this->render('cuisine-add');
	   	   } else {
	   	   	   $this->crumbsTitle=Yii::t("default","Cuisine Sort");
		       $this->render('cuisine-sort');
	   	   }
		} else {
		    $this->crumbsTitle=Yii::t("default","Cuisine List");
		    $this->render('cuisine-list');	
		}		
	}
	
	public function actionOrderStatus()
	{
		if (isset($_GET['Do'])){
	   	   if ($_GET['Do']=="Add"){
			   $this->crumbsTitle=Yii::t("default","Order Status Add");
		       $this->render('order-status-add');
	   	   }   	   	   
		} else {
		    $this->crumbsTitle=Yii::t("default","Order Status List");
		    $this->render('order-status-list');	
		}		
	}
	
	public function actionContactSettings()
	{
		$this->crumbsTitle=Yii::t("default","Contact Settings");
		$this->render('contact-settings');
	}
	
	public function actionSocialSettings()
	{
		$this->crumbsTitle=Yii::t("default","Social Settings");
		$this->render('social-settings');
	}
	
	public function actionRatings()
	{
		if (isset($_GET['Do'])){
			$this->crumbsTitle=Yii::t("default","Ratings Add");
		   $this->render('ratings-add');
		} else {
	       $this->crumbsTitle=Yii::t("default","Ratings");
		   $this->render('ratings');
		}
	}
	
	public function actionProfile()
	{
		$this->crumbsTitle=Yii::t("default","Profile Settings");
		$this->render('profile');
	}
	
	public function actionUserList()
	{
		
		if (isset($_GET['Do'])){
			$this->crumbsTitle=Yii::t("default","User Add");
		    $this->render('user-add');
		} else {
		    $this->crumbsTitle=Yii::t("default","User List");
		    $this->render('user-list');
		}
	}
	
	public function actionCustomPage()
	{
		if (isset($_GET['Do'])){
			if ($_GET['Do']=="Add"){
			   $this->crumbsTitle=Yii::t("default","Custom page Add");
		       $this->render('custom-add');
			} elseif ($_GET['Do']=="AddCustom"){
				$this->crumbsTitle=Yii::t("default","Add New Custom Link");
		        $this->render('custom-add-link');
			} else {
			   $this->crumbsTitle=Yii::t("default","Assign Page");
		       $this->render('custom-assign-page');
			}
		} else {
		    $this->crumbsTitle=Yii::t("default","Custom page List");
		    $this->render('custom-list');
		}
	}
	
	public function actionManageLanguage()
	{
		
		$set_lang_id=getOptionA("set_lang_id");
		if ( !empty($set_lang_id)){
			$set_lang_id=json_decode($set_lang_id);
		}
		
		$this->crumbsTitle=t("Manage Language Settings");
		$this->render('manage-language-new',array(
		  'langauge_list'=>FunctionsV3::getLanguageList(false),
		  'set_lang_id'=>$set_lang_id
		));
	}
	
	public function actionSeo()
	{
		$this->crumbsTitle=Yii::t("default","SEO");
		$this->render('seo-settings');
	}
	
	public function actionStripeSettings()
	{
		$this->crumbsTitle=Yii::t("default","Stripe");
		$this->render('stripe-settings');
	}
	
	public function actionSmsSettings()
	{
		$this->crumbsTitle=Yii::t("default","SMS Settings");
		$this->render('sms-settings',array(
		 'provider_selected'=>Yii::app()->functions->getOptionAdmin('sms_provider')
		));
	}	
	
	public function actionSmsPackage()
	{
		if (isset($_GET['Do'])){
			if ($_GET['Do']=="Add"){
				$this->crumbsTitle=Yii::t("default","SMS Package Add");
		        $this->render('sms-package-add');
			} else {
				$this->crumbsTitle=Yii::t("default","SMS Package Sort");
		        $this->render('sms-package-sort');
			}		
		} else {
		   $this->crumbsTitle=Yii::t("default","SMS Package");
		   $this->render('sms-package-list');
		}
	}	

	public function actionSetlanguage()
	{				
		$redirect='';
		$referrer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
		if(isset($_GET['lang'])){
			if (!empty($referrer)){
				$redirect=$referrer;
			} else $redirect=Yii::app()->createUrl('admin/dashboard',array(
			  'lang'=>$_GET['lang']
			));
		} else {
			if (!empty($referrer)){
				$redirect=$referrer;
			} else $redirect=Yii::app()->createUrl('admin/dashboard');
		}
		
		$this->redirect($redirect);
	}	
	
	public function actionViewFile()
	{
		$data=$_GET;	
		$fileName=isset($data['fileName'])?$data['fileName']:'';
		$path_to_upload=Yii::getPathOfAlias('webroot')."/upload";	
		if (file_exists($path_to_upload."/".$fileName)){						
			show_source($path_to_upload."/".$fileName);
		} else echo Yii::t("default","File not found");
	}
	
	public function actionmercadopagoSettings()
	{
		$this->crumbsTitle=Yii::t("default","Mercadopago");
		$this->render('mercadopago-settings');
	}
	
	public function actionShowLanguage()
	{
		//header("Content-type: text/plain");
		$file=Yii::getPathOfAlias('webroot')."/mt_language_file.php";
		//show_source($file);		
		header("Content-disposition: attachment; filename=mt_language_file.php");
        header("Content-type: text/plain");
        readfile($file);
	}
	
	public function actionSMStransaction()
	{
		if (isset($_GET['do']) || isset($_GET['Do']) ){	
			$this->crumbsTitle=Yii::t("default","SMS Transaction Update");
		    $this->render('sms-transaction-add');
		} else {	
			$this->crumbsTitle=Yii::t("default","SMS Transaction");
			$this->render('sms-transaction');
		}
	}
	
	public function actionPayLineSettings()
	{
		$this->crumbsTitle=Yii::t("default","Payline Settings");
		$this->render('payline-settings');
	}
	
	public function actionSisowSettings()
	{
		$this->crumbsTitle=Yii::t("default","Sisow Settings");		
		$this->render('sisow-settings');
	}
	
	public function actionPayuMonenySettings()
	{
		$this->crumbsTitle=Yii::t("default","PayUMoney Settings");		
		$this->render('payumoney-settings');
	}
	
	public function actionAnalytics()
	{
		$this->crumbsTitle=Yii::t("default","Custom Code & Google Analytics");		
		$this->render('analytics-settings');
	}
	
	public function actionCustomerlist()
	{
		if (isset($_GET['do'])){	
			$this->crumbsTitle=Yii::t("default","Customer List");		
		    $this->render('customer-add');
		} else {	
			$this->crumbsTitle=Yii::t("default","Customer List");		
			$this->render('customer-list');
		}
	}
	
	public function actionrptMerchanteSales()
	{
		$this->crumbsTitle=Yii::t("default","Merchant Sales Report");		
		$this->render('rpt-merchant-sales');
	}
	
	public function actionOBDsettings()
	{
		$this->crumbsTitle=Yii::t("default","Offline Bank Deposit");		
		$this->render('offlinebank-settings');
	}
	
	public function actionBankdeposit()
	{
		$this->crumbsTitle=Yii::t("default","Receive Bank Deposit");		
		$this->render('bankdeposit-list');
	}
	
	public function actionPayseraSettings()
	{
		$this->crumbsTitle=Yii::t("default","paysera settings");		
		$this->render('paysera-settings');
	}
	
	public function actionEmailSettings()
	{
		$this->crumbsTitle=Yii::t("default","Mail & SMTP Settings");		
		$this->render('email-settings');
	}
	
	public function actionEmailTPL()
	{
		$this->crumbsTitle=Yii::t("default","Email Template");		
		
		$order_stats = FunctionsV3::orderStatusTPL();
		
		$data=array(
		  'general_template'=>array(
		     'contact_us'=>array(
		        'email'=>true,
		        'sms'=>false,		        
		        'email_tag'=>'name,email,country,phone,message,sitename,siteurl',
		        'sms_tag'=>''
		      ),
		     'customer_welcome_email'=>array(
		         'email'=>true,
		         'sms'=>false,		         
		         'email_tag'=>'firstname,lastname,sitename,siteurl',
		         'sms_tag'=>''
		      ),		       		     
		     'customer_verification_code_email'=>array(
		        'email'=>true,
		        'sms'=>false,
		        'email_tag'=>'firstname,lastname,code,sitename,siteurl',		        
		      ),
		      'customer_verification_code_sms'=>array(
		        'email'=>false,
		        'sms'=>true,		        
		        //'email_tag'=>'firstname,lastname,code,sitename,siteurl',
		        'sms_tag'=>'code,sitename,siteurl',
		      ),
		      'customer_forgot_password'=>
		       array(
		         'email'=>true,
		         'sms'=>false,		        
		         'email_tag'=>'firstname,lastname,change_pass_link,sitename,siteurl',
		      ),
		      'merchant_welcome_signup'=>array(
		        'email'=>true,
		        'sms'=>false,		  
		        'email_tag'=>'restaurant_name,login_url,sitename,siteurl',      
		      ),
		     'merchant_verification_code'=>array(
		        'email'=>true,
		        'sms'=>false,		  
		        'email_tag'=>'restaurant_name,code,login_url,sitename,siteurl',      
		      ),
		     'merchant_forgot_password'=>array(
		        'email'=>true,
		        'sms'=>false,		     
		        'email_tag'=>'restaurant_name,code,sitename,siteurl',         
		      ),		      
		     'merchant_new_signup'=>array(
		        'email'=>true,
		        'sms'=>true,		     
		        'email_tag'=>'restaurant_name,package_name,merchant_type,sitename,siteurl',         
		        'sms_tag'=>'restaurant_name,package_name,merchant_type,sitename,siteurl',         
		      ),
		     'admin_forgot_password'=>array(
		        'email'=>true,
		        'sms'=>false,		        
		        'email_tag'=>'newpassword,login_url,sitename,siteurl',      
		      ),
		      'merchant_near_expiration'=>array(
		        'email'=>true,
		        'sms'=>true,		        
		        'email_tag'=>'restaurant_name,expiration_date,sitename,siteurl',      
		        'sms_tag'=>'restaurant_name,expiration_date,sitename,siteurl',      
		      ),
		      'merchant_change_status'=>array(
		        'email'=>true,
		        'sms'=>true,		        
		        'email_tag'=>'restaurant_name,status,sitename,siteurl', 
		        'sms_tag'=>'restaurant_name,status,sitename,siteurl', 
		      ),
		      'merchant_invoice'=>array(
		        'email'=>true,
		        'sms'=>false,
		        'email_tag'=>'restaurant_name,invoice_number,period,terms,invoice_link,sitename,siteurl', 
		        'sms_tag'=>'', 
		      ),
		  ),
		  
		  'order_template'=>array(
		     'receipt_template'=>array(
		       'email'=>true,
		       'sms'=>true,		        
		       'push'=>false,
		       'email_tag'=>'order_id,customer_name,restaurant_name,total_amount,receipt,sitename,siteurl', 
		       'sms_tag'=>'order_id,customer_name,restaurant_name,total_amount,order_details,sitename,siteurl',
		     ),
		     'receipt_send_to_merchant'=>array(
		       'email'=>true,
		       'sms'=>true,		        
		       'push'=>true,
		       'email_tag'=>'order_id,customer_name,restaurant_name,total_amount,receipt,accept_link,decline_link,sitename,siteurl', 
		       'sms_tag'=>'order_id,customer_name,restaurant_name,total_amount,order_details,sitename,siteurl', 
		       'push_tag'=>'order_id,customer_name,restaurant_name,total_amount,sitename,siteurl', 
		     ),
		     'receipt_send_to_admin'=>array(
		       'email'=>true,
		       'sms'=>true,		     
		       'push'=>false,   
		       'email_tag'=>'order_id,customer_name,restaurant_name,total_amount,receipt,sitename,siteurl', 
		       'sms_tag'=>'order_id,customer_name,restaurant_name,total_amount,order_details,sitename,siteurl', 
		     ),
		     /*'order_idle_to_merchant'=>array(
		       'email'=>true,
		       'sms'=>false,		        
		       'email_tag'=>'order_id,restaurant_name,idle_time,sitename,siteurl', 
		       'sms_tag'=>'order_id,restaurant_name,idle_time,sitename,siteurl', 
		     ),*/
		     'order_idle_to_admin'=>array(
		       'email'=>true,
		       'sms'=>true,		       
		       'push'=>false, 
		       'email_tag'=>'order_id,restaurant_name,idle_time,sitename,siteurl', 
		       'sms_tag'=>'order_id,restaurant_name,idle_time,sitename,siteurl', 
		     )
		  ),
		  		  
		  'booking_template'=>array(
		     'customer_booked'=>array(
		       'email'=>true,
		       'sms'=>false,		
		       'push'=>false,              
		       'email_tag'=>'booking_id,restaurant_name,number_guest,date_booking,time,customer_name,email,mobile,instruction,status,sitename,siteurl',	       
		     ),
		     'booked_notify_admin'=>array(
		       'email'=>true,
		       'sms'=>false,		
		       'push'=>false,                      
		       'email_tag'=>'booking_id,restaurant_name,number_guest,date_booking,time,customer_name,email,mobile,instruction,status,sitename,siteurl',	       
		     ),
		     'booked_notify_merchant'=>array(
		       'email'=>true,
		       'sms'=>false,		   
		       'push'=>true,                   
		       'email_tag'=>'booking_id,restaurant_name,number_guest,date_booking,time,customer_name,email,mobile,instruction,status,sitename,siteurl',	       
		       'push_tag'=>'booking_id,restaurant_name,number_guest,date_booking,time,customer_name,email,mobile,instruction,status,sitename,siteurl',	       
		     ),
		     'booking_update_status'=>array(
		       'email'=>true,
		       'sms'=>false,		       
		       'push'=>true,               
		       'email_tag'=>'booking_id,restaurant_name,number_guest,date_booking,time,customer_name,email,mobile,instruction,status,merchant_remarks,sitename,siteurl',	       
		       'push_tag'=>'booking_id,restaurant_name,number_guest,date_booking,time,customer_name,email,mobile,instruction,status,merchant_remarks,sitename,siteurl', 
		     )
		  ),
		  
		  'payment_template'=>array(
		     'offline_bank_deposit_signup_merchant'=>array(
		       'email'=>true,
		       'sms'=>false,		        
		       'email_tag'=>'restaurant_name,amount,verify_payment_link,sitename,siteurl',	       
		     ),
		     'offline_bank_deposit_purchase'=>array(
		       'email'=>true,
		       'sms'=>false,		        
		       'email_tag'=>'customer_name,amount,verify_payment_link,sitename,siteurl',	       
		     )
		  ),
		  
		  
		  'order_status_template'=>$order_stats
		);
			
		//dump($data); die();
		$this->render('email-template',$data);
	}
	
	public function actionPaymentGatewaySettings()
	{
		$this->crumbsTitle=Yii::t("default","Payment Gateway Settings");		
		$this->render('paymentgatewa-settings');
	}	
	
	public function actionPayOnDelivery()
	{
		 $this->crumbsTitle=Yii::t("default","Pay On Delivery settings");				
		if (isset($_GET['Do'])){
			if ($_GET['Do']=="Add"){
				$this->render('payondelivery-settings-add');
			} else $this->render('payondelivery-settings-sort');			
		}  else $this->render('payondelivery-settings');		
	}
	
	public function actionsubscriberlist()
	{
		$this->crumbsTitle=Yii::t("default","Subscriber List");		
		$this->render('subscriber-list');
	}
	
	public function actionMerchantAddBulk()
	{
		$this->crumbsTitle=Yii::t("default","Upload Bulk CSV");		
		$this->render('merchant-bulk');
	}
	
	public function actionSMSlogs()
	{
		$this->crumbsTitle=Yii::t("default","SMS Logs");
		$this->render('sms-logs');
	}
	
	public function actionBarclay()
	{	
		$this->crumbsTitle=Yii::t("default","Barclay Settings");
		$this->render('barclay');
	}
	
	public function actionEpayBg()
	{	
		$this->crumbsTitle=Yii::t("default","EpayBg Settings");
		$this->render('epaybg');
	}	
	
	public function actionCommisionSettings()
	{	
		$this->crumbsTitle=Yii::t("default","Commission Settings");
		$this->render('commision-settings');
	}	
	
	public function actionmerchantcommission()
	{
		$this->crumbsTitle=Yii::t("default","Merchant Commission");
		$this->render('merchant-commision');
	}
	
	public function actionMerchantCommissiondetails()
	{
		$this->crumbsTitle=Yii::t("default","Merchant Commission details");
		$this->render('merchant-commision-details');
	}
	
	public function actionWithdrawalSettings()
	{
		$this->crumbsTitle=Yii::t("default","Withdrawal settings");
		$this->render('withdrawal-settings');
	}
	
	public function actionincomingwithdrawal()
	{
		$this->crumbsTitle=Yii::t("default","Incoming Withdrawal");
		$this->render('withdrawal');
	}
	
	public function actionRptMerchantSalesummary()
	{
		$this->crumbsTitle=Yii::t("default","Merchant Sales Summary Report");
		$this->render('rpt-merchant-sales-summary');
	}
	
	public function actionFaxSettings()
	{
		$this->crumbsTitle=Yii::t("default","Fax Settings");
		$this->render('fax-settings');
	}
	
	public function actionFaxPackage()
	{
		if (isset($_GET['Do'])){
			if ($_GET['Do']=="Add"){
				$this->crumbsTitle=Yii::t("default","Fax Package Add");
		        $this->render('fax-package-add');
			} else {
				$this->crumbsTitle=Yii::t("default","Fax Package Sort");
		        $this->render('fax-package-sort');
			}		
		} else {
		   $this->crumbsTitle=Yii::t("default","Fax Package");
		   $this->render('fax-package-list');
		}	
	}
	
	public function actionFaxTransaction()
	{
		if (isset($_GET['Do'])){	
			$this->crumbsTitle=Yii::t("default","Fax Transaction Add/Update");
		    $this->render('fax-transaction-add');
		} else {	
		   $this->crumbsTitle=Yii::t("default","Fax Payment Transaction");
		   $this->render('fax-transaction');
		}
	}
	
	public function actionRptBookingSummary()
	{
		$this->crumbsTitle=Yii::t("default","Booking Summary Report");
		$this->render('rpt-booking-summary');
	}
	
	public function actionfaxlogs()
	{
		$this->crumbsTitle=Yii::t("default","Fax Logs");
		$this->render('fax-logs');
	}
	
	public function actionAuthorize()
	{
		$this->crumbsTitle=Yii::t("default","Authorize.net");
		$this->render('authorize-settings');
	}
	
	public function actionNoAccess()
	{
		$this->render('error',array('msg'=>t("Sorry but you don't have permission to access this page")));
	}
	
	public function actionReviews()
	{		
		$this->crumbsTitle=t("Reviews");
		if (isset($_GET['Do'])){
			$this->render('review-add');
		} else $this->render('reviews');		
	}
	
	public function actionDishes()
	{
		$this->crumbsTitle=t("Dishes");
		if (isset($_GET['Do'])){
			$this->render('dishes-add');
		} else $this->render('dishes-list');
	}
	
	public function actionVoucher()
	{
		$this->crumbsTitle=Yii::t("default","Voucher List");
		if (isset($_GET['Do'])){
			$this->crumbsTitle=Yii::t("default","Voucher Add/Update");
			$this->render('voucher-add');		
		} else $this->render('voucher-list');		
	}
	
	public function actionCardPaymentSettings()
	{
		$this->crumbsTitle=t("Offline Credit Card Payment");
		$this->render('card-payment-settings');
	}
	
	public function actionOrderTemplate()
	{
		$this->crumbsTitle=t("Order Email Template");
		$this->render('order-email-tpl');
	}
	
	public function actionZipCode()
	{
		//if (getOptionA('home_search_mode')=="postcode"){
			$this->crumbsTitle=t("Zip Code");
			if (isset($_GET['Do'])){
				$data=FunctionsK::getZipCode($_GET['id']);
				$this->render('zipcode-add',array(
				  'data'=>$data
				));			
			} else $this->render('zipcode');		
		//} else $this->render('error',array('msg'=>t("Zip code only be use if you enabled the searching to post code on settings")));
	}
	
	public function actionThemeSettings()
	{
		$this->render('theme-settings');
	}
	
	public function actionBraintree()
	{
		$this->render('braint-tree-settings');
	}
	
	public function actionRazor()
	{
		$this->render('razor');
	}
	
	public function actionCategory()
	{
		if(isset($_GET['do'])){
			$this->render('category-add');
		} else $this->render('category');		
	}
	
	public function actionCategorySettings()
	{
		$this->render('category-settings');
	}
	
	public function actionMollie()
	{
		$this->render('mollie');
	}
	
	public function actionipay88()
	{
		$this->render('ipay88');
	}
	
	public function actionmoneris()
	{
		$this->render('moneris-settings');
	}
	
	public function actionPrint()
	{		
		$this->layout="printing_layout";
		
		$size=getOptionA('admin_printing_receipt_size');
		$width=getOptionA('admin_printing_receipt_width');
		
		FunctionsV3::setPrintSize($size, $width);
						
		$baseUrl = Yii::app()->baseUrl; 
		$cs = Yii::app()->getClientScript();				
		$cs->registerCssFile($baseUrl.'/assets/css/admin.css?ver=1.0');
				
		$this->render('print_receipt');
	}
	
	public function actionEmaiLogs()
	{
		$this->render('email-logs');
	}
	
	public function actionViewEmail()
	{
		if (isset($_GET['id'])){
			$id=$_GET['id'];			
			if($res=FunctionsV3::getEmailogsByID($id)){
				echo nl2br($res['content']);
			} else $this->render('error',array(
		      'message'=>t("Sorry but we cannot find what you are looking for.")
		   ));
		} else $this->render('error',array(
		  'message'=>t("Sorry but we cannot find what you are looking for.")
		));
	}
	
	public function actionnotisettings()
	{
		$this->crumbsTitle=t("Notification Settings");
		$this->render('noti-settings');
	}
	
	public function actionManageLocation()
	{
		$this->crumbsTitle=t("Manage location");
		$this->render('manage-location');
	}
	
	public function actionDefinelocation()
	{
		$this->crumbsTitle=t("Define location");
		$id=isset($_GET['countryid'])?$_GET['countryid']:'';
		if ( $res=FunctionsV3::locationCountry($id)){
			$this->render('manage-define-location',array(
			  'data'=>$res,
			  'id'=>$id
			));
		} else  $this->render('error',array(
		      'message'=>t("Sorry but we cannot find what you are looking for.")
		   ));
	}
	
	public function actionInvoice()
	{
		$this->crumbsTitle=t("Invoice");
		$this->render('invoice-list');
	}
	
	public function actionIncomingOrders()
	{
		$this->crumbsTitle=t("Incoming Orders");
		$this->render('incoming-orders');
	}
	
	public function actionCronJobs()
	{
		$this->crumbsTitle=t("Cron jobs");
		$this->render('cron-jobs');
	}
	
	public function actionvoguepay()
	{
		$this->crumbsTitle=t("voguepay");
		$this->render('voguepay');
	}

	
	public function actionipay()
	{
		$this->crumbsTitle=t("Ipay");
		$this->render('ipay');
	}
	
	public function actionpipay()
	{
		$this->crumbsTitle=t("Pi Pay");
		$this->render('pipay-settings');
	}
	
	public function actionhubtelpayment()
	{
		$this->crumbsTitle=t("Hubtel Payments");
		$this->render('hubtel-payemnt-settings');
	}
	
	public function actionsofort()
	{
		$this->render('sofort-settings');
	}
	
	public function actionjampie()
	{
		$this->render('jampie-settings');
	}
	
	public function actionwing()
	{
		$this->crumbsTitle=t("Wing Settings");
		$this->render('wing-settings');
	}
	
	public function actionpaymill()
	{
		$this->crumbsTitle=t("Paymill Settings");
		$this->render('paymill-settings');
	}
	
	public function actionipay_africa()
	{
		$this->crumbsTitle=t("Ipay Africa");
		$this->render('ipay-africa');
	}
	
	public function actiondixipay()
	{
		$this->crumbsTitle=t("DIXIPAY");
		$this->render('dixipay-settings');
	}
	
	public function actionwirecard()
	{
		$this->crumbsTitle=t("WireCard");
		$this->render('wirecard-settings');
	}
	
	public function actionpayulatam()
	{
		$this->crumbsTitle=t("PayU Latam");
		$this->render('payulatam-settings');
	}
} 
/*END CONTROLLER*/
