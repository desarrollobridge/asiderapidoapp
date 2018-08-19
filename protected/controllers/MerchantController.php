<?php
/**
 * MerchantController Controller
 *
 */
if (!isset($_SESSION)) { session_start(); }

class MerchantController extends CController
{
	public $layout='merchant_tpl';	
	public $crumbsTitle='';
	
	public function accessRules()
	{		
		
	}
	
	public function beforeAction($action)
    {    	
    	    	
    	$action_name= $action->id ;
    	$accept_controller=array('login','ajax','autologin');
	    //if(!Yii::app()->functions->isMerchantLogin() )
	    if(!Yii::app()->functions->validateMerchantSession() )
	    {
	    	if (!in_array($action_name,$accept_controller)){	 	           
	           if ( Yii::app()->functions->has_session){
	    	   	    $message_out=t("You were logout because someone login with your account");
	    	   	    $this->redirect(array('merchant/login/?message='.urlencode($message_out)));
	    	   } else $this->redirect(array('merchant/login'));	           
	    	}
	    }		    
	    
	    if ( $action_name=="autologin"){
	    	return true;
	    }
	    /*echo $this->uniqueid;
	    echo '<br/>';
	    echo $action_name;*/
	    if ( $this->uniqueid=="merchant"){
	    	if ( !Yii::app()->functions->hasMerchantAccess($action_name)){
	    		if ( $action_name!="login"){
	    			if ( $action_name!="index"){
	    				$this->crumbsTitle=Yii::t("default","No Access");		
	    		        $this->render('noaccess');
	    		        return ;
	    			}    
	    		}
	    	}
	    }
	    
	    $cs = Yii::app()->getClientScript();
		$admin_decimal_place=getOptionA('admin_decimal_place');
		if (empty($admin_decimal_place)){
			$admin_decimal_place=2;
		}
		if ( $admin_decimal_place<=0){
			$admin_decimal_place=0;
		}
		$cs->registerScript(
		  'price_decimal_place',
		 "var price_decimal_place='$admin_decimal_place';",
		  CClientScript::POS_HEAD
		);
		
		$admin_decimal_separator=getOptionA('admin_decimal_separator');
		if (empty($admin_decimal_separator)){
			$admin_decimal_separator='.';
		}
		$cs->registerScript(
		  'price_decimal_separator',
		 "var price_decimal_separator='$admin_decimal_separator';",
		  CClientScript::POS_HEAD
		);
		
		$admin_thousand_separator=getOptionA('admin_thousand_separator');
		if (empty($admin_thousand_separator)){
			$admin_thousand_separator=',';
		}
		$cs->registerScript(
		  'price_thousand_separator',
		 "var price_thousand_separator='$admin_thousand_separator';",
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
				
	    return true;	    
    }	
        	
	public function init()
	{		
		
		 $name=Yii::app()->functions->getOptionAdmin('website_title');
		 if (!empty($name)){		 	
		 	 Yii::app()->name = $name;
		 }		 
		 
		 
		 $mtid=Yii::app()->functions->getMerchantID();		 
		 // set website timezone
		 $website_timezone=Yii::app()->functions->getOptionAdmin("website_timezone");		 
		 if (!empty($website_timezone)){		 	
		 	Yii::app()->timeZone=$website_timezone;
		 }		 		 
		 $mt_timezone=Yii::app()->functions->getOption("merchant_timezone",$mtid);	   	   	    	
    	 if (!empty($mt_timezone)){    	 	
    		Yii::app()->timeZone=$mt_timezone;
    	 }		     	 
    	 
    	 FunctionsV3::handleLanguage();
    	 $cs = Yii::app()->getClientScript();
    	 $lang=Yii::app()->language;
		 $cs->registerScript(
		  'lang',
		  "var lang='$lang';",
		  CClientScript::POS_HEAD
		 );
		 
		 $ajax_admin=Yii::app()->createUrl('/ajaxmerchant');
		 
		 $cs = Yii::app()->getClientScript();
		 $cs->registerScript(
		  'ajax_admin',
		  "var ajax_admin='$ajax_admin';",
		  CClientScript::POS_HEAD
		);		
		
	}
				  
	public function actionIndex()
	{					
		if ( !Yii::app()->functions->isMerchantLogin()){						
			$this->layout='login_tpl';
			$this->render('login');
		} else {											
			
			$this->crumbsTitle=Yii::t("default","Dashboard");		
			$this->render('dashboard');			
		}		
	}	
	
	
	public function actionDashBoard()
	{							
		
		$this->crumbsTitle=Yii::t("default","Dashboard");		
		if ( !Yii::app()->functions->isMerchantLogin()){						
			$this->layout='login_tpl';
			$this->render('login');
		} else {									
			$this->crumbsTitle=Yii::t("default","Dashboard");
			$this->render('dashboard');			
		}		
	}	
	
	
	public function actionLogin()
	{		
		if (isset($_GET['logout'])){
			//Yii::app()->request->cookies['kr_merchant_user'] = new CHttpCookie('kr_merchant_user', ""); 			
			unset($_SESSION['kr_merchant_user']);
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
		$class=new AjaxAdmin;
	    $class->data=$data;
	    $class->$data['action']();	    
	    echo $class->output();
	    yii::app()->end();
	}	
	
	public function actionCategoryList()
	{	    
		$this->crumbsTitle=Yii::t("default","Category");
		
	    if (isset($_GET['Do'])){
			if ( $_GET['Do']=="Add"){
				$this->render('category_add');
			} elseif ( $_GET['Do'] =="Sort" ){	
			   $this->render('category_sort');
			} else $this->render('category_list');
		} else $this->render('category_list');
	}
		
	public function actionAddOnCategory()
	{
				
		$this->crumbsTitle=Yii::t("default","Addon Category");
		
		if (isset($_GET['Do'])){
			if ( $_GET['Do']=="Add"){
				$this->render('addon_category_add');
			} elseif ( $_GET['Do'] =="Sort" ){					
			   $this->render('addon_category_sort');		
			} else $this->render('addon_category_list');
		} else $this->render('addon_category_list');
	}		
	
	public function actionAddOnItem()
	{		
		$this->crumbsTitle=Yii::t("default","Addon Item");
		$mtid=Yii::app()->functions->getMerchantID();
		
		if (isset($_GET['Do'])){
			if ( $_GET['Do']=="Add"){
				
				$merchant_tax=getOption($mtid,'merchant_tax');
				if($merchant_tax>0){
				   $merchant_tax=$merchant_tax/100;
				}
				
				$this->render('addon_item_new',array(
				   /*'merchant_apply_tax'=>getOption($mtid,'merchant_apply_tax'),
				   'merchant_tax'=>$merchant_tax>0?$merchant_tax:0,*/
				   'merchant_apply_tax'=>'',
				   'merchant_tax'=>0,
				));		
            } elseif ( $_GET['Do'] =="Sort" ){	
			   $this->render('addon_item_sort');	
			} else $this->render('addon_item_list');		
		} else $this->render('addon_item_list');		
	}

	public function actionSize()
	{
		$this->crumbsTitle=Yii::t("default","Size");
		
		if (isset($_GET['Do'])){
			if ( $_GET['Do']=="Add"){
				$this->render('size_add');			
           } elseif ( $_GET['Do'] =="Sort" ){	
			   $this->render('size_sort');	
			} else $this->render('size');		
		} else $this->render('size');		
	}
	
	public function actionCookingRef()
	{			
		$this->crumbsTitle=Yii::t("default","Cooking Reference");
		
		if (isset($_GET['Do'])){
			if ( $_GET['Do']=="Add"){
				$this->render('cooking-ref-add');			
            } elseif ( $_GET['Do'] =="Sort" ){	
			   $this->render('cooking_ref_sort');	
			} else $this->render('cooking-ref');		
		} else $this->render('cooking-ref');
	}
	
	public function actionFoodItem()
	{
		$this->crumbsTitle=Yii::t("default","Food Item");
		$mtid=Yii::app()->functions->getMerchantID();
				
		if (isset($_GET['Do'])){
			if ( $_GET['Do']=="Add"){
				$merchant_tax=getOption($mtid,'merchant_tax');
				$merchant_tax=$merchant_tax/100;
				
				$this->render('food-item-add',array(
				  /*'merchant_apply_tax'=>getOption($mtid,'merchant_apply_tax'),
				  'merchant_tax'=>$merchant_tax>0?$merchant_tax:0,*/
				  'merchant_apply_tax'=>'',
				  'merchant_tax'=>0,
				));
			} elseif ( $_GET['Do'] =="Sort" ){	
			   $this->render('food_item_sort');	
			} else $this->render('food-item-list');		
		} else $this->render('food-item-list');
	}
	
	public function actionMerchant()
	{
		$this->crumbsTitle=Yii::t("default","Merchant");
		$this->render('merchant-info');
	}
	
	public function actionSettings()
	{
		$this->crumbsTitle=t("Settings");
		$this->render('settings');
	}
	
	public function actionSocialSettings()
	{
		$this->crumbsTitle=t("Social Settings");
		$this->render('social-settings');
	}
	
	public function actionAlertSettings()
	{
		$this->crumbsTitle=Yii::t("default","Alert Settings");
		$this->render('alert-settings');
	}
	
	public function actionSMSSettings()
	{
		$mechant_sms_enabled=Yii::app()->functions->getOptionAdmin('mechant_sms_enabled');
		if ( $mechant_sms_enabled=="yes"){
			$this->render('noaccess');
		} else {		
			
			$ha_sms_credits=Yii::app()->functions->hasSMSCredits();	
			$mechant_sms_purchase_disabled=Yii::app()->functions->getOptionAdmin('mechant_sms_purchase_disabled');		
			if ( $mechant_sms_purchase_disabled=="yes"){
				$ha_sms_credits=true;
			}
			//if (Yii::app()->functions->hasSMSCredits()){
			if ($ha_sms_credits){
			   $this->crumbsTitle=Yii::t("default","SMS Settings");		
			   $this->render('sms-settings');
			} else {
			   $this->crumbsTitle=Yii::t("default","SMS Purchase Credits");
			   $this->render('sms-purchase');
			}
		}
	}
	
	public function actionPaypalSettings()
    {
    	/*$py=Yii::app()->functions->getMerchantListOfPaymentGateway();		
		if (in_array('paypal',(array)$py) || in_array('pyp',(array)$py) ){
	    	$this->crumbsTitle=Yii::t("default","Paypal Settings");
	    	$this->render('paypal-settings');
        } else $this->render('noaccess');*/
    	    	
    	$this->crumbsTitle=Yii::t("default","Paypal Settings");
    	if ( Yii::app()->functions->hasMerchantAccess("pyp")){	    		
	    	$this->render('paypal-settings');
    	} else  $this->render('noaccess');
    }
    
    public function actionSalesReport()
    {
    	
    	$this->crumbsTitle=Yii::t("default","Sales Report");
    	$this->render('sales-report');
    }
    
    public function actionSalesSummaryReport()
    {
    	$this->crumbsTitle=Yii::t("default","Sales Summary Report");
    	$this->render('sales-summary-report');
    }
    
    public function actionOrderStatus()
    {
    	if (isset($_GET['Do'])){
    		if ( getOptionA('merchant_status_disabled')!=2){
	    	   $this->crumbsTitle=Yii::t("default","Order Status");
	    	   $this->render('order-status-add');
    		} else $this->render('error',array('message'=>t("This options is disabled by website owner")));
    	} else {
    	   $this->crumbsTitle=Yii::t("default","Order Status");
    	   $this->render('order-status');
    	}
    }
    
    public function actionMerchantStatus()
    {
    	$mt_id=Yii::app()->functions->getMerchantID();
    	if ( $res=Yii::app()->functions->isMerchantCommission($mt_id)){
    		$this->crumbsTitle=Yii::t("default","404 page");
    		$this->render('error',array('message'=>t("Sorry but your not allowed to access this page")));
    	} else {
	    	$this->crumbsTitle=Yii::t("default","Merchant Status");
	    	$this->render('merchant-status');
    	}
    }
    
    public function actionReceiptSettings()
    {
    	
    	/*$this->crumbsTitle=Yii::t("default","Receipt Settings");
    	$this->render('receipt-settings');*/
    	$this->render('error',array(
    	  'message'=>t("Sorry but we cannot find what you are looking for.")
    	));
    }
    
    public function actionStripeSettings()
    {    	
    	$this->crumbsTitle=Yii::t("default","Stripe Settings");
    	if ( Yii::app()->functions->hasMerchantAccess("stp")){
    		$this->render('stripe-settings');
    	} else $this->render('noaccess');
    }
    
	public function actionSetlanguage()
	{		
		$redirect='';
		$referrer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
		if(isset($_GET['lang'])){
			if (!empty($referrer)){
				$redirect=$referrer;
			} else $redirect=Yii::app()->createUrl('merchant/dashboard',array(
			  'lang'=>$_GET['lang']
			));
		} else {
			if (!empty($referrer)){
				$redirect=$referrer;
			} else $redirect=Yii::app()->createUrl('merchant/dashboard');
		}
		
		$this->redirect($redirect);
	}	    
	
	public function actionCreditCardInit()
	{
		$this->crumbsTitle=Yii::t("default","Purchase using Offline Credit Card");
		$this->render('select-cc');
	}
	
	public function actionSmsReceipt()
	{
		$this->crumbsTitle=Yii::t("default","Receipt");
		$this->render('sms-receipt');
	}
	
	public function actionPaypalInit()
	{		
		if ( $info=Yii::app()->functions->getSMSPackagesById($_GET['package_id']) ){
			
			$price=$info['price'];
    	    if ( $info['promo_price']>0){
                 $price=$info['promo_price'];
    		}	    	
    		
    		$paypal_con=Yii::app()->functions->getPaypalConnectionAdmin();    		
    		
    		$type=isset($_GET['type'])?$_GET['type']:'';
    		$getparams="type/".$type."/package_id/".$_GET['package_id'];
    		
	        $params='';
			$x=1;
			$params['L_NAME'.$x]=isset($info['title'])?$info['title']:Yii::t("default","No description");
	        $params['L_NUMBER'.$x]=$info['package_id'];
	        $params['L_DESC'.$x]=isset($info['title'])?$info['title']:Yii::t("default","No description");
	        $params['L_AMT'.$x]=normalPrettyPrice($price);
	        $params['L_QTY'.$x]=1;					
				        
			$params['AMT']=normalPrettyPrice($price);
		    $params['RETURNURL']="http://".$_SERVER['HTTP_HOST'].Yii::app()->request->baseUrl."/merchant/paypalPurchase/$getparams";
		    $params['CANCELURL']="http://".$_SERVER['HTTP_HOST'].Yii::app()->request->baseUrl."/merchant/smsSettings/";	  	  
		    $params['NOSHIPPING']='1';
	        $params['LANDINGPAGE']='Billing';
	        $params['SOLUTIONTYPE']='Sole';
	        $params['CURRENCYCODE']=adminCurrencyCode();
	        
	        
	        $paypal=new Paypal($paypal_con);
	  	    $paypal->params=$params;
	  	    $paypal->debug=false;
	  	    if ($resp=$paypal->setExpressCheckout()){  	   	  			  	  	  
	  	  	  header("Location: ".$resp['url']);
	  	    } else {
	  	    	$this->render('error',array('message'=>"ERROR: ".$paypal->getError() ));
	  	    }
    		
		} else {
			$this->render('error',array('message'=>Yii::t("default","ERROR: Cannot get package information")));
		}
	}
	
	public function actionPaypalPurchase()
	{
		$this->crumbsTitle=Yii::t("default","Paypal Confirm Purchase");
		$this->render('paypal-confirmation');
	}
	
	public function actionStripeInit()
	{
		$this->crumbsTitle=Yii::t("default","Stripe Payment");
		$this->render('stripe-init');
	}
	
	public function actionSmsBroadcast()
	{
		if (Yii::app()->functions->hasSMSCredits()){
			if (isset($_GET['Do'])){
				if ($_GET['Do']=="view"){
					$this->crumbsTitle=Yii::t("default","SMS BroadCast Details". " ($_GET[bid])");
			        $this->render('sms-broadcast-details');
				} else {
				    $this->crumbsTitle=Yii::t("default","Add SMS BroadCast");
			        $this->render('sms-broadcast');
				}
			} else {		
				$this->crumbsTitle=Yii::t("default","SMS BroadCast");
			    $this->render('sms-broadcast-list');
			}
		} else {
		   $this->crumbsTitle=Yii::t("default","SMS Purchase Credits");
		   $this->render('sms-purchase');
		}
	}
	
	public function actionPurchaseSMS()
	{
		$this->crumbsTitle=Yii::t("default","SMS Purchase Credits");
        $this->render('sms-purchase');
	}
	
	public function actionMercadopagoInit()
	{		
		$this->crumbsTitle=Yii::t("default","Mercadopago Payment");
		$this->render('mercadopago-init');
	}
	
	public function actionmercadopagoSettings()
	{			
		$this->crumbsTitle=Yii::t("default","Mercadopago");
		if ( Yii::app()->functions->hasMerchantAccess("mcd")){
			$this->render('mercadopago-settings');
		} else $this->render('noaccess');
	}	
	
	public function actionUser()
	{
		$this->crumbsTitle=Yii::t("default","User List");
		if (isset($_GET['Do'])){
			$this->crumbsTitle=Yii::t("default","User Add/Update");
			$this->render('user-add');		
		} else $this->render('user-list');		
	}
	
	public function actionVoucher()
	{
		$this->crumbsTitle=Yii::t("default","Voucher List");
		if (isset($_GET['Do'])){
			$this->crumbsTitle=Yii::t("default","Voucher Add/Update");
			$this->render('voucher-add');		
		} else $this->render('voucher-list');		
	}
	
	public function actionReview()
	{
		$this->crumbsTitle=Yii::t("default","Customer reviews");
		if (isset($_GET['Do'])){
			if ( Yii::app()->functions->getOptionAdmin('merchant_can_edit_reviews')=="yes"){			
				$this->render('error',array(
				 'message'=>t("Sorry but you don't have access this page.")
				));
			} else {				
				$this->crumbsTitle=Yii::t("default","Customer reviews Update");
				$this->render('review-add');
			}
		} else $this->render('review-list');				
	}
	
	public function actionPaylineSettings()
	{
		$this->crumbsTitle=Yii::t("default","Payline Settings");
		$this->render('payline-settings');
	}
	
	public function actionPaylineInit()
	{
		$this->crumbsTitle=Yii::t("default","Payline Payment");
		$this->render('payline-init');
	}
	
	public function actionSisowSettings()
	{			    
		$this->crumbsTitle=Yii::t("default","Sisow Settings");
		if ( Yii::app()->functions->hasMerchantAccess("ide")){
			$this->render('sisow-settings');
		} else $this->render('noaccess');
	}	
	
	public function actionSisowInit()
	{
		$this->crumbsTitle=Yii::t("default","SMS Purchase Credits");
		$this->render('sisow-init');
	}
	
	public function actionpayumoneysettings()
	{			    
		$this->crumbsTitle=Yii::t("default","PayUMoney Settings");
		if ( Yii::app()->functions->hasMerchantAccess("payu")){
			$this->render('payumoney-settings');
		} else $this->render('noaccess');
		
	}
	
	public function actionPayuInit()
	{
		$this->crumbsTitle=Yii::t("default","Pay using PayUMoney");		
		$this->render('payuinit');
	}
	
	public function actionTableBooking()
	{
				
		$tbl_booking=getOption( Yii::app()->functions->getMerchantID() ,'merchant_master_table_boooking');
		if($tbl_booking==1){
			$this->crumbsTitle=Yii::t("default","Table Booking");
			$this->render('error',array(
			  'message'=>t("Sorry but you don't have access this page.")
			));
			return ;
		}
		
		if (isset($_GET['Do'])){
			if ($_GET['Do']=="settings"){
				$this->crumbsTitle=Yii::t("default","Table Booking Settings");		
			    $this->render('tablebooking-settings');
			} else {
			   $this->crumbsTitle=Yii::t("default","Table Booking");		
			   $this->render('tablebooking-add');
			}
		} else {
			$this->crumbsTitle=Yii::t("default","Table Booking");		
			$this->render('tablebooking');
		}
	}
	
	public function actionPayseraSettings()
	{		
		$this->crumbsTitle=Yii::t("default","paysera settings");
		if ( Yii::app()->functions->hasMerchantAccess("pys")){
			$this->render('paysera-settings');
		} else $this->render('noaccess');
	}
	
	public function actionPysinit()
	{				
		$db_ext=new DbExt;
				
		$error='';
		$package_id=isset($_GET['package_id'])?$_GET['package_id']:'';	
		$amount_to_pay=0;
		
		$back_url=Yii::app()->request->baseUrl."/merchant/purchasesms";
		$payment_ref=Yii::app()->functions->generateCode()."TT".Yii::app()->functions->getLastIncrement('{{sms_package_trans}}');		
		$package_id=isset($_GET['package_id'])?$_GET['package_id']:'';
		
		$merchant_id=Yii::app()->functions->getMerchantID();		
		
		if ( $res=Yii::app()->functions->getSMSPackagesById($package_id) ){
			$amount_to_pay=$res['price'];
			if ( $res['promo_price']>0){
				$amount_to_pay=$res['promo_price'];
			}	    										
			$amount_to_pay=is_numeric($amount_to_pay)?normalPrettyPrice($amount_to_pay):'';	
			$payment_description.=isset($res['title'])?$res['title']:'';		
			
			/*dump($payment_description);
			dump($amount_to_pay);
			dump($payment_ref);*/
						
			$amount_to_pay=number_format($amount_to_pay,2,'.','');	
			
            $cancel_url=Yii::app()->getBaseUrl(true)."/merchant/purchasesms";
            
            $accepturl=Yii::app()->getBaseUrl(true)."/merchant/pysinit/?type=purchaseSMScredit&package_id=".
            $package_id."&mode=accept&mtid=$merchant_id";	
                                                
            $callback=Yii::app()->getBaseUrl(true)."/paysera/?type=purchaseSMScredit&package_id=".
            $package_id."&mode=callback&mtid=$merchant_id";	
			
			$country=Yii::app()->functions->getOptionAdmin('admin_paysera_country');
		    $mode=Yii::app()->functions->getOptionAdmin('admin_paysera_mode');
		    $lang=Yii::app()->functions->getOptionAdmin('admin_paysera_lang');
		    $currency=Yii::app()->functions->adminCurrencyCode();	  
		    $projectid=Yii::app()->functions->getOptionAdmin('admin_paysera_project_id');		  
		    $password=Yii::app()->functions->getOptionAdmin('admin_paysera_password');
					    
		    if (isset($_GET['mode'])){				    	
		    	
		    	if ($_GET['mode']=="accept"){
		    		
	    		    $payment_code=Yii::app()->functions->paymentCode("paysera");
				  	$params=array(
						  'merchant_id'=>$_GET['mtid'],
						  'sms_package_id'=>$package_id,
						  'payment_type'=>$payment_code,
						  'package_price'=>$amount_to_pay,
						  'sms_limit'=>isset($res['sms_limit'])?$res['sms_limit']:'',
						  'date_created'=>FunctionsV3::dateNow(),
						  'ip_address'=>$_SERVER['REMOTE_ADDR'],
						  'payment_gateway_response'=>json_encode($_GET),						  
						  //'payment_reference'=>$response['orderid']
					 );							 					
					 $db_ext->insertData("{{sms_package_trans}}",$params);		    		
		    		 header('Location: '.Yii::app()->request->baseUrl."/merchant/smsReceipt/id/".Yii::app()->db->getLastInsertID());
		    		 die();		    		 
		    	}
		    			    			    			    			    	   
		    	try {
		    		
		    		$response = WebToPay::checkResponse($_GET, array(
		              'projectid'     => $projectid,
		              'sign_password' => $password,
		            ));      
		            		            
		            if (is_array($response) && count($response)>=1){  
		            	
		            	if ($response['status']==0){
		            		die("payment has no been executed");
		            	}
		            	if ($response['status']==3){
		            		die("additional payment information");
		            	}		    
		            			            			            	 
		            	$stmt="SELECT * FROM
		            	{{sms_package_trans}}
		            	WHERE
		            	merchant_id ='".$_GET['mtid']."'
		            	AND
		            	sms_package_id='".$_GET['package_id']."'
		            	ORDER BY id DESC
		            	LIMIT 0,1
		            	";		            	
		            	if ( $res2=$db_ext->rst($stmt)){		            		
		            		$current_id=$res2[0]['id'];
		            		$params_update=array('status'=>"paid");
		            		$db_ext->updateData("{{sms_package_trans}}",$params_update,'id',$current_id);
		            	}		            
						echo 'OK';
            	        die();
            	         		            	
		            } else $error=t("ERROR: api returns empty");	
		    		
		    	} catch (WebToPayException $e) {
	               $error=t("ERROR: Something went wrong").". ".$e;
	            }    			    	
		    } else {
				try {									
					$params_request=array(
				        'projectid'     => $projectid,
				        'sign_password' => $password,
				        'orderid'       => $payment_ref,
				        'amount'        => $amount_to_pay*100,
				        'currency'      => $currency,
				        'country'       => $country,
				        'accepturl'     => $accepturl,
				        'cancelurl'     => $cancel_url,
				        'callbackurl'   => $callback,
				        'test'          => $mode,
				        'lang'          =>$lang
				       );	
				     if ($mode==2){
				       	unset($params_request['test']);
				     }       
				     				     				     				     				    
				     $request = WebToPay::redirectToPayment($params_request);
					
				} catch (WebToPayException $e) {
		           $error=t("ERROR: Something went wrong").". ".$e;
		        }    			
		    }
		} else $error=Yii::t("default","Failed. Cannot process payment");  
				
		if (!empty($error)){
			$this->render('error',array('message'=>$error));
		}		
	}	
	
	public function actionOBDinitOLD()
	{		
		$db_ext=new DbExt;
		
		$this->crumbsTitle=Yii::t("default","SMS Purchase Credits");
		
		$error='';
		$package_id=isset($_GET['package_id'])?$_GET['package_id']:'';	
		$amount_to_pay=0;
		$merchant_id=Yii::app()->functions->getMerchantID();			
		$payment_description='';
		
		$back_url=Yii::app()->request->baseUrl."/merchant/purchasesms";
		$payment_ref=Yii::app()->functions->generateCode()."TT".Yii::app()->functions->getLastIncrement('{{sms_package_trans}}');			
		if ( $res=Yii::app()->functions->getSMSPackagesById($package_id) ){
			$amount_to_pay=$res['price'];
			if ( $res['promo_price']>0){
				$amount_to_pay=$res['promo_price'];
			}	    										
			$amount_to_pay=is_numeric($amount_to_pay)?normalPrettyPrice($amount_to_pay):'';	
			$payment_description.=isset($res['title'])?$res['title']:'';		
									
			$merchant_info=Yii::app()->functions->getMerchantInfo();			
			$merchant_email=$merchant_info[0]->contact_email;			
			if (!empty($merchant_email)){				
				
				$subject=Yii::app()->functions->getOptionAdmin('admin_deposit_subject');
		    	$from=Yii::app()->functions->getOptionAdmin('admin_deposit_sender');
		    	
		    	if (empty($from)){
		    	    $from='no-reply@'.$_SERVER['HTTP_HOST'];
		    	}
		    	if (empty($subject)){
		    	    $subject=Yii::t("default","Bank Deposit instructions");
		    	}    	
		    			    	
		    	
		    	$link=Yii::app()->getBaseUrl(true)."/merchant/bankdepositverify/?ref=".$payment_ref;
    	        $links="<a href=\"$link\" target=\"_blank\" >".Yii::t("default","Click on this link")."</a>";
    	        $tpl=Yii::app()->functions->getOptionAdmin('admin_deposit_instructions');
		    	if (!empty($tpl)){   
		    		$tpl=Yii::app()->functions->smarty('amount',
    	            Yii::app()->functions->adminCurrencySymbol().Yii::app()->functions->standardPrettyFormat($amount_to_pay),$tpl);
    	            $tpl=Yii::app()->functions->smarty('verify-payment-link',$links,$tpl);    	            
    	            
    	            if (Yii::app()->functions->sendEmail($merchant_email,$from,$subject,$tpl)){
    	            	
    	            	$payment_code=Yii::app()->functions->paymentCode("bankdeposit");
					  	$params=array(
							  'merchant_id'=>$merchant_id,
							  'sms_package_id'=>$package_id,
							  'payment_type'=>$payment_code,
							  'package_price'=>$amount_to_pay,
							  'sms_limit'=>isset($res['sms_limit'])?$res['sms_limit']:'',
							  'date_created'=>FunctionsV3::dateNow(),
							  'ip_address'=>$_SERVER['REMOTE_ADDR'],
							  'payment_gateway_response'=>json_encode($_GET),						  
							  'payment_reference'=>$payment_ref
						 );							
						 		
						 $db_ext->insertData("{{sms_package_trans}}",$params);		    		
			    		 header('Location: '.Yii::app()->request->baseUrl."/merchant/smsReceipt/id/".Yii::app()->db->getLastInsertID());
			    		 die();		    		 
    	            	
    	            } else $error=t("ERROR: cannot send email to")." ".$merchant_email;
		    	} else $error=t("bank deposit instruction not yet available");
    					
			} else $error=t("please correct your email address. we cannot sent bank instruction with empty merchant email address");
		} else $error=Yii::t("default","Failed. Cannot process payment");  	
		
		if (!empty($error)){
			$this->render('error',array('message'=>$error));
		}				
	}
	
	public function actionBankDepositVerify()
	{
		$this->render('bank-deposit-verification');
	}
	
	public function actionAutoLogin()
	{
		$DbExt=new DbExt;
		$data=$_GET;		
		$stmt="SELECT * FROM
		       {{merchant}}
		       WHERE
		       merchant_id=".Yii::app()->db->quoteValue($data['id'])."
		       AND
		       password=".Yii::app()->db->quoteValue($data['token'])."
		       LIMIT 0,1
		";							
		if ( $res=$DbExt->rst($stmt)){										
			$_SESSION['kr_merchant_user']=json_encode($res);
			
			$session_token=Yii::app()->functions->generateRandomKey().md5($_SERVER['REMOTE_ADDR']);				
			 $params=array(
			  'session_token'=>$session_token,
			  //'last_login'=>FunctionsV3::dateNow()
			 );
			 $DbExt->updateData("{{merchant}}",$params,'merchant_id',$res[0]['merchant_id']);
			 
			 $_SESSION['kr_merchant_user_session']=$session_token;
			 $_SESSION['kr_merchant_user_type']='admin';
			
			$this->redirect(baseUrl()."/merchant",true);			
		} else $msg=t("Login Failed. Either username or password is incorrect");
		echo $msg;
	}
		
	public function actionGallerySettings()
	{
		$this->crumbsTitle=Yii::t("default","gallery settings");		
		$this->render('gallery-settings');
	}
	
	public function actionPayOnDelivery()
	{		
		$this->crumbsTitle=Yii::t("default","Pay On Delivery");		
		if ( Yii::app()->functions->hasMerchantAccess("pyr")){
			$this->render('payondelivery');
		} else $this->render('noaccess');
	}
	
	public function actionOffers()
	{
		$this->crumbsTitle=Yii::t("default","Offers");		
		if (isset($_GET['Do'])){
			if ( $_GET['Do']=="Add"){
				$this->crumbsTitle=Yii::t("default","Offers - add");		
				$this->render('offers_add');			
			} else $this->render('category_list');
		} else 	$this->render('offers');		
	}
	
	public function actionBarclay()
	{
		$this->crumbsTitle=Yii::t("default","Barclay settings");		
		if ( Yii::app()->functions->hasMerchantAccess("bcy")){
		   $this->render('barclay-settings');
		} else $this->render('noaccess');
	}
	
	public function actionEpagbg()
	{
		$this->crumbsTitle=Yii::t("default","EpayBg settings");			
		if ( Yii::app()->functions->hasMerchantAccess("epy")){			
		   $this->render('epaybg-settings');
		} else $this->render('noaccess');
	}	
	
	public function actionStatement()
	{
		$this->crumbsTitle=Yii::t("default","Statement");		
		$this->render('statement');
	}
	
	public function actionEarnings()
	{
		$merchant_type = FunctionsV3::getMerchantTypeBySession();
		if($merchant_type==3 || $merchant_type==1){
			$this->crumbsTitle=Yii::t("default","Earnings");		
			$this->render('error',array(
			  'message'=>t("Sorry but you don't have access this page.")
			));
			return ;
		}
		
		$this->crumbsTitle=Yii::t("default","Earnings");		
		$this->render('earnings',array(
		 'merchant_type'=>FunctionsV3::getMerchantTypeBySession()
		));
	}
	
	public function actionIngredients()
	{
		$this->crumbsTitle=Yii::t("default","Ingredients");		
		if (isset($_GET['Do'])){
			if ($_GET['Do']=="Add"){
				$this->crumbsTitle=Yii::t("default","Ingredients Add");		
				$this->render('ingredients-add');		
			} else {
				$this->crumbsTitle=Yii::t("default","Ingredients Sort");		
				$this->render('ingredients-sort');		
			}		
		} else $this->render('ingredients');		
	}
	
	public function actionWithdrawals()
	{
		$merchant_type = FunctionsV3::getMerchantTypeBySession();
		if($merchant_type==3 || $merchant_type==1){
			$this->crumbsTitle=Yii::t("default","Withdrawals");		
			$this->render('error',array(
			  'message'=>t("Sorry but you don't have access this page.")
			));
			return ;
		}
		
		$wd_enabled_paypal=getOptionA('wd_enabled_paypal');
		$wd_bank_deposit=getOptionA('wd_bank_deposit');		
		if ( $wd_enabled_paypal==2 || $wd_bank_deposit==2 ){
			$stats=yii::app()->functions->getOptionAdmin('wd_payout_disabled');		
			if ($stats==2){
				$this->crumbsTitle=Yii::t("default","Withdrawals");		
				$this->render('error',array('message'=>t("Sorry but widthrawal is disabled by the site owner")));
			} else {
				$this->crumbsTitle=Yii::t("default","Withdrawals");		
				$this->render('withdrawals');
			}
		} else {
			$this->render('error',array('message'=>t("Sorry but withdrawals is not available this time. admin has not yet set any payment method")));
		}
	}
	
	public function actionWithdrawalStep2()
	{
		$this->crumbsTitle=Yii::t("default","Withdrawals Complete");		
		$this->render('withdrawals-step2');
	}
	
	public function actionWithdrawalsHistory()
	{
		$this->crumbsTitle=Yii::t("default","Withdrawal History");		
		$this->render('withdrawals-history');
	}
	
	public function actionFaxSettings()
	{
		$this->crumbsTitle=Yii::t("default","Fax Settings");		
		$this->render('fax-settings');
	}
	
	public function actionFaxPurchase()
	{
		$this->crumbsTitle=Yii::t("default","Fax Purchase Credits");		
		$this->render('fax-purchase');
	}
	
	public function actionPay()
	{
		$get=$_GET;
		$raw=base64_decode(isset($_GET['raw'])?$_GET['raw']:'');
		parse_str($raw,$raw_decode);		
		$price='';		
		$description='';
		
		/*dump($get);
		dump($raw_decode);*/
		$package_id=$get['package_id'];
		
		if (is_array($raw_decode) && count($raw_decode)>=1){
			$price=isset($raw_decode['price'])?$raw_decode['price']:'';
			$description=isset($raw_decode['description'])?$raw_decode['description']:'';
		}
		
		$get_params="&method=".$get['method'];
		$get_params.="&purchase=".$get['purchase'];
		$get_params.="&package_id=".$get['package_id'];
		$get_params.="&raw=".$get['raw'];					
		
		if (!empty($price)){
			switch ($get['method']) {
				case "pyp":
					$paypal_con=Yii::app()->functions->getPaypalConnectionAdmin();  
										
					$params='';
					$x=0;
					$params['L_NAME'.$x]=$description;
			        $params['L_NUMBER'.$x]=$get['package_id'];
			        $params['L_DESC'.$x]=$description;
			        $params['L_AMT'.$x]=normalPrettyPrice($price);
			        $params['L_QTY'.$x]=1;					
						        
					$params['AMT']=normalPrettyPrice($price);
$params['RETURNURL']="http://".$_SERVER['HTTP_HOST'].Yii::app()->request->baseUrl."/merchant/paymentconfirm/?$get_params";
				    $params['CANCELURL']=$get['return_url'];	  	  
				    $params['NOSHIPPING']='1';
			        $params['LANDINGPAGE']='Billing';
			        $params['SOLUTIONTYPE']='Sole';
			        $params['CURRENCYCODE']=adminCurrencyCode();			        
			        
			        $paypal=new Paypal($paypal_con);
			  	    $paypal->params=$params;
			  	    $paypal->debug=false;
			  	    if ($resp=$paypal->setExpressCheckout()){  	   	  			  	  	  
			  	  	    header("Location: ".$resp['url']);
			  	    } else {
			  	    	$this->render('error',array('message'=>"ERROR: ".$paypal->getError() ));
			  	    }
																	
					break;
					
				case "stp":				
				    $this->crumbsTitle=Yii::t("default","Fax Purchase Credits");			    
				    $this->render('pay_stripe',array(
				      'package_id'=>$package_id,
				      'price'=>$price,
				      'description'=>$description,
				      'redirect'=>"faxreceipt",
				      'payment_type'=>$get['method']
				    ));
				    break;
			
				default:
					break;
			}
		} else $this->render('error',array('message'=>t("Price is not define")));
	}
	
	public function actionPaymentConfirm()
	{
		$get=$_GET;				
		$raw=base64_decode($_GET['raw']);
		parse_str($raw,$raw_decode);		
		$price='';		
		$description='';
		
		//dump($raw_decode);
		if (is_array($raw_decode) && count($raw_decode)>=1){
			$price=isset($raw_decode['price'])?$raw_decode['price']:'';
			$description=isset($raw_decode['description'])?$raw_decode['description']:'';
		}
		
		//dump($get);
		if (!empty($price)){
			switch ($get['method']) {
				case "pyp":
					$this->crumbsTitle=Yii::t("default","Payment Confirmation");		
					$this->render('payment-paypal');
					break;
			
				default:
					$this->render('error',array(
					'message'=>t("Sorry but we cannot find what you are looking for.")));
					break;
			}
		} else $this->render('error',array('message'=>t("Price is not define")));		
	}
	
	public function actionfaxreceipt()
	{
		$this->crumbsTitle=Yii::t("default","Receipt");		
		$this->render('fax-receipt');
	}
	
	public function actionfaxbankdepositverification()
	{
		$this->crumbsTitle=Yii::t("default","Bank Deposit Verification");		
		$this->render('fax-deposit-verify');
	}
	
	public function actionFaxStats()
	{
		$this->crumbsTitle=Yii::t("default","Fax Stats");		
		$this->render('faxstats');
	}
	
	public function actionProfile()
	{
		$merchant_info=Yii::app()->functions->getMerchantInfo();
		$user_id=$merchant_info[0]->merchant_user_id;
		$data=Yii::app()->functions->getMerchantUserInfo($user_id);
		if (is_array($data) && count($data)>=1){
		    $this->crumbsTitle=Yii::t("default","Profile");		
			$this->render('profile',array('data'=>$data));
		} else {
			$this->crumbsTitle=Yii::t("default","Error");		
			$this->render('error',array('message'=>t("Error session has expired")));
		}
	}
	
	public function actionFaxPurchaseTrans()
	{
		$this->crumbsTitle=Yii::t("default","Purchase Credit Transactions");		
		$this->render('fax-purchasetrans');
	}
	
	public function actionPurchaseSmsTransaction()
	{
		$this->crumbsTitle=Yii::t("default","Purchase Credit Transactions");		
		$this->render('sms-purchasetrans');
	}
	
	public function actionShippingRate()
	{
		$this->crumbsTitle=Yii::t("default","Delivery Charges Rates");		
		if (FunctionsV3::isSearchByLocation()){
			$this->render('location-delivery-rates');
		} else $this->render('shippingrate');		
	}
	
	public function actionBookingReport()
	{
		$this->crumbsTitle=Yii::t("default","Booking Summary Report");		
		$this->render('rpt-bookingreport');
	}
	
	public function actionCashStatement()
	{
		$this->crumbsTitle=Yii::t("default","Cash Statement");
		$this->render('statement-cash');
	}
	
	public function actionAuthorize()
	{		
		$this->crumbsTitle=Yii::t("default","Authorize.net");
		if ( Yii::app()->functions->hasMerchantAccess("atz")){	
		    $this->render('authorize-settings');
		} else $this->render('noaccess');
	}
	
	public function actionAtzinit()
	{		
		$this->crumbsTitle=Yii::t("default","Pay using Authorize.net");
		$this->render('atz-init');
	}
	
	public function actionEpyinit()
	{
		$this->crumbsTitle=Yii::t("default","Pay using EpayBg");
		$this->render('epy-init');
	}
	
	public function actionEpaybg()
	{
		$post=$_POST;
		$get=$_GET;		
		$error='';
		
			
		switch ($get['mode']) {
			case "accept":
				if ( $res=Yii::app()->functions->barclayGetTokenTransaction($get['token'])){
					
					if ( $package_info=Yii::app()->functions->getSMSPackagesById($res['param1']) ){
						
						$amount_to_pay=$package_info['price'];
						if ( $package_info['promo_price']>0){
							$amount_to_pay=$package_info['promo_price'];
						}	    
						
						$db_ext=new DbExt;
						$payment_code=Yii::app()->functions->paymentCode("epaybg");
	        	        
				        $params=array(
						  'merchant_id'=>Yii::app()->functions->getMerchantID(),
						  'sms_package_id'=>$package_info['sms_package_id'],
						  'payment_type'=>$payment_code,
						  'package_price'=>$amount_to_pay,
						  'sms_limit'=>isset($package_info['sms_limit'])?$package_info['sms_limit']:'',
						  'date_created'=>FunctionsV3::dateNow(),
						  'ip_address'=>$_SERVER['REMOTE_ADDR'],
						  'payment_reference'=>$res['orderid']
						  /*'payment_gateway_response'=>json_encode($chargeArray),
						  'status'=>"paid"*/
						);	    	
						
						if ( $db_ext->insertData("{{sms_package_trans}}",$params)){				
header('Location: '.Yii::app()->request->baseUrl."/merchant/smsReceipt/id/".Yii::app()->db->getLastInsertID());
				           die();
			            } else $error=Yii::t("default","ERROR: Cannot insert record.");	
					}
				} else $error=t("Transaction token not found");
				header('Location: '.websiteUrl()."/merchant/purchasesms?error=".$error); 
				break;
				
			case "cancel":
				header('Location: '.websiteUrl()."/merchant/purchasesms"); 
				break;
			default:
				header('Location: '.websiteUrl()."/merchant/purchasesms"); 
				break;
		}
	}
	
	public function actionOBD()
	{
		$this->crumbsTitle=Yii::t("default","Offline Bank Deposit");
		if ( Yii::app()->functions->hasMerchantAccess("obd")){	
		   $this->render('obd-settings');
		} else $this->render('noaccess');
	}
	
	public function actionOBDReceive()
	{
		$this->crumbsTitle=Yii::t("default","Receive Bank Deposit");
		$this->render('obd-deposit-receive');
	}
	
	public function actionBrainTreeSettings()
	{
		$this->crumbsTitle=t("Braintree settings");
		if ( Yii::app()->functions->hasMerchantAccess("btr")){	
		   $this->render('braintree-settings');
		} else $this->render('noaccess');
	}

	public function actionRazor()
	{
		$this->crumbsTitle=t("Razorpay settings");		
		if ( Yii::app()->functions->hasMerchantAccess("rzr")){	
		    $this->render('razor'); 
		} else $this->render('noaccess');
	}
	
	public function actionMollie()
	{
		$this->render('mollie');
	}
	
	public function actionMolinit()
	{
		$amount_to_pay=0;  $error='';
        $payment_description='';
        
        $package_id=isset($_GET['package_id'])?$_GET['package_id']:'';
        if ( $res=Yii::app()->functions->getSMSPackagesById($package_id) ){
        	$amount_to_pay=$res['price'];
			if ( $res['promo_price']>0){
				$amount_to_pay=$res['promo_price'];
			}	    		
					
			$amount_to_pay=Yii::app()->functions->unPrettyPrice($amount_to_pay);
			$payment_description.=isset($res['title'])?$res['title']:'';	
						
			$locale='en_US';
			$apikey=FunctionsV3::getMollieApiKey(true);
			$_locale=getOptionA('admin_mol_locale');
			if(!empty($_locale)){
				$locale=$_locale;
			}
			
			/*dump($amount_to_pay); dump($payment_description);
			dump($locale);
			dump($apikey);
			dump($res);
			die();*/
			
			if(!empty($apikey)){
				
			    spl_autoload_unregister(array('YiiBase','autoload'));
	            require "Mollie/API/Autoloader.php";				   
	            spl_autoload_register(array('YiiBase','autoload'));				   
			    $mollie = new Mollie_API_Client;
                $mollie->setApiKey($apikey);
               
                $redirect_url=websiteUrl()."/mollieprocess/?transaction=sms&package_id=".$package_id;
                $redirect_url.="&mtid=".Yii::app()->functions->getMerchantID();
				
                 try {                   	   
                   $payment = $mollie->payments->create(array(
				        "amount"      => $amount_to_pay,
				        "description" => $payment_description,
				        'locale'      => $locale,
				        "redirectUrl" => $redirect_url,
				   ));					   	
				   $db= new DbExt;
				   $db->insertData("{{sms_package_trans}}",array(
				      'merchant_id'=>Yii::app()->functions->getMerchantID(),
					  'sms_package_id'=>$package_id,
					  'payment_type'=>'mol',
					  'package_price'=>$amount_to_pay,
					  'sms_limit'=>isset($res['sms_limit'])?$res['sms_limit']:'',
					  'date_created'=>FunctionsV3::dateNow(),
					  'ip_address'=>$_SERVER['REMOTE_ADDR'],
					  'payment_gateway_response'=>json_encode($payment),	
					  'payment_reference'=>$payment->id
				   ));					   
				   $this->redirect($payment->links->paymentUrl);					   
				   Yii::app()->end();
               } catch (Exception $e){
               	  $this->render('error',array(
				    'message'=>$e->getMessage()
				  ));
               }             
                
			} else $error=t("API key is not yet set. please try again later");
        } else $error=t("Sorry but we cannot find what your are looking for.");
        
        $this->render('error',array(
        	'message'=>!empty($error)?$error:''
        ));
	}
	
    public function actionipay88()
	{
		$this->render('ipay88');
	}

	public function actionMinTable()
	{
		$this->crumbsTitle=t("Minimum Order Table");
		$mtid=Yii::app()->functions->getMerchantID();
		$this->render('min-table-rates',array(
		  'mtid'=>$mtid,
		  'data'=>FunctionsV3::getMinOrderTable($mtid)
		));
	}
	
	public function actionmoneris()
	{
		$this->render('moneris-merchant-settings');
	}
	
	public function actionMriInit()
	{
		$package_id=isset($_GET['package_id'])?$_GET['package_id']:'';
	    if ( $data=Yii::app()->functions->getSMSPackagesById($package_id) ){		
			$this->render('mri-init',array(
			   'package_id'=>$package_id,
			  'data'=>$data,
			  'merchant_id'=>Yii::app()->functions->getMerchantID()
			));
		} else $this->render('error',array(
		  'message'=>t("Sorry but we cannot find what your are looking for.")
		));
	}
	
	public function actionfaxMriInit()
	{
		
	}
	
	public function actionPrint()
	{		
		$this->layout="printing_layout";
		
		$merchant_id=Yii::app()->functions->getMerchantID();
		$receipt_size=getOption($merchant_id,'printing_receipt_size');
		$receipt_width=getOption($merchant_id,'printing_receipt_width');		
		FunctionsV3::setPrintSize($receipt_size, $receipt_width);
				
		$baseUrl = Yii::app()->baseUrl; 
		$cs = Yii::app()->getClientScript();		
		$cs->registerCssFile($baseUrl.'/assets/css/admin.css?ver=1.0');		
		
		$this->render('print_receipt');
	}
	
	public function actionInvoice()
	{
		$this->crumbsTitle=t("Invoice list");
		$this->render('invoice-list',array(
		  'merchant_id'=>Yii::app()->functions->getMerchantID()
		));
	}
	
	public function actionViewInvoice()
	{		
		if ( $res=FunctionsV3::getInvoiceByToken($_GET['token'])){					
			$db=new DbExt;
			$params=array('viewed'=>1);
			$db->updateData("{{invoice}}",$params,'invoice_number',$res['invoice_number']);
			
			$link=uploadURL()."/invoice/".$res['pdf_filename'];
			$this->redirect($link);
			Yii::app()->end();
			
		} else $this->render('error',array(
		  'message'=>t("Sorry but we cannot find what you are looking for.")
		));
	}
	
	public function actioncodsettings()
	{
		$this->crumbsTitle=t("Cash On delivery");
		if ( Yii::app()->functions->hasMerchantAccess("cod")){			
			$this->render('cod-settings',array(
			  'merchant_id'=>Yii::app()->functions->getMerchantID()
			));
		} else $this->render('error',array(
			  'message'=>t("Sorry but your not allowed to access this page")
			));		
	}
	
	public function actionofflineccsettings()
	{
		$this->crumbsTitle=t("Offline Credit Card Payment");
		if ( Yii::app()->functions->hasMerchantAccess("ocr")){		
			$this->render('offlinecc-settings',array(
			  'merchant_id'=>Yii::app()->functions->getMerchantID()
			));
		} else $this->render('error',array(
			  'message'=>t("Sorry but your not allowed to access this page")
			));
	}
	
	public function actionvoguepay()
	{
		$this->crumbsTitle=t("voguepay");
		
		if ( Yii::app()->functions->hasMerchantAccess("vog")){	
		    $this->render('voguepay-settings',array(
		      'merchant_id'=>Yii::app()->functions->getMerchantID()
		   ));
		} else $this->render('noaccess');
		
	}
	
	public function actionvoginit()
	{
		$this->crumbsTitle=t("voguepay");
		$package_id=isset($_GET['package_id'])?$_GET['package_id']:'';
	    if ( $data=Yii::app()->functions->getSMSPackagesById($package_id) ){		
			$this->render('vog-init',array(
			   'package_id'=>$package_id,
			  'data'=>$data,
			  'merchant_id'=>Yii::app()->functions->getMerchantID(),
			  'credentials'=>FunctionsV3::GetVogueAdminCredentials()
			));
		} else $this->render('error',array(
		  'message'=>t("Sorry but we cannot find what your are looking for.")
		));
	}
	
	public function actionvognotify()
	{
		$DbExt=new DbExt;
		$data_post=$_POST; $data_get=$_GET; $error='';
				
		if (isset($data_post['transaction_id'])){
			$transaction_id=$data_post['transaction_id'];
					
			$credentials=FunctionsV3::GetVogueAdminCredentials();
			$is_demo=false;				    
		    if($credentials['merchant_id']=="demo"){
		    	$is_demo=true;
		    }			    
		    if ( $vog_res=voguepayClass::getTransaction($transaction_id,$is_demo)){
		    
		    	$pakage_info=Yii::app()->functions->getSMSPackagesById($data_get['package_id']);		    
			    $amount_to_pay=$pakage_info['price'];
				if ( $pakage_info['promo_price']>0){
					$amount_to_pay=$pakage_info['promo_price'];
				}	    				
				$params_logs=array(
	    		   'merchant_id'=>Yii::app()->functions->getMerchantID(),
	    		   'sms_package_id'=>$data_get['package_id'],
	    		   'package_price'=>$amount_to_pay,
	    		   'payment_type'=>"vog",
	    		   'sms_limit'=>isset($pakage_info['sms_limit'])?$pakage_info['sms_limit']:'', 
	    		   'payment_reference'=>$transaction_id,
	    		   'payment_gateway_response'=>json_encode($vog_res),
	    		   'date_created'=>FunctionsV3::dateNow(),
	    		   'ip_address'=>$_SERVER['REMOTE_ADDR'],
	    		);	
	    		if ($vog_res['status']=="Approved"){
	    			$params_logs['status']="paid";
	    		} else $params_logs['status']=$vog_res['status'];		    		
	    			    			
	    		if ( !$res=Yii::app()->functions->mercadoGetPayment($transaction_id)){	    			
	    			$DbExt->insertData("{{sms_package_trans}}",$params_logs);
	    		} 
	    		
	    		echo "OK";
									
		    } else {
		    	// FAOLED GETTING TRANSACTION INFORMATION
		    }
		} else {
			// MISSING TRANSACTION ID
		}
	}
	
	public function actionvogsuccess()
	{
		$DbExt=new DbExt;
		$data_post=$_POST; $data_get=$_GET; $error='';
		$credentials=FunctionsV3::GetVogueAdminCredentials();		
		
	    if(isset($data_post['transaction_id'])){
		    $transaction_id=isset($data_post['transaction_id'])?$data_post['transaction_id']:'';
		    $is_demo=false;				    
		    if($credentials['merchant_id']=="demo"){
		    	$is_demo=true;
		    }	    	 
		    
		    $pakage_info=Yii::app()->functions->getSMSPackagesById($data_get['package_id']);		    
		    $amount_to_pay=$pakage_info['price'];
			if ( $pakage_info['promo_price']>0){
				$amount_to_pay=$pakage_info['promo_price'];
			}	    					
		    
		    if ( $res=Yii::app()->functions->mercadoGetPayment($transaction_id)){
		        // HAS ALREADY RECORDS		        
		        if ($res['status']=="paid"){
			        $redirect_url=Yii::app()->createUrl('merchant/smsreceipt',array(
				      'id'=>$res['id']
				    ));
		        } else {
		        	$redirect_url=Yii::app()->createUrl('merchant/smsreceipt',array(
				      'type'=>"purchaseSMScredit",
	                  'package_id'=>$res['sms_package_id'],
	                  'error'=>1
				    ));
		        }
			    header("location: $redirect_url");
			    Yii::app()->end();
		    } else {
		    	// NO RECORDS FROM SMS TRANSACTION	
		    	if ( $vog_res=voguepayClass::getTransaction($transaction_id,$is_demo)){		    		
		    		if(isset($vog_res['ERROR'])){
		    			$link=Yii::app()->createUrl('/merchant/voginit',array(
		    			  'type'=>"purchaseSMScredit",
	                      'package_id'=>$data_get['package_id'],
	                      'error'=>1,
	                      'message'=>$vog_res['ERROR']
		    			));
		    			$this->redirect($link);
		    			Yii::app()->end();
		    		}
		    		$params_logs=array(
		    		   'merchant_id'=>Yii::app()->functions->getMerchantID(),
		    		   'sms_package_id'=>$data_get['package_id'],
		    		   'package_price'=>$amount_to_pay,
		    		   'payment_type'=>"vog",
		    		   'sms_limit'=>isset($pakage_info['sms_limit'])?$pakage_info['sms_limit']:'', 
		    		   'payment_reference'=>$transaction_id,
		    		   'payment_gateway_response'=>json_encode($vog_res),
		    		   'date_created'=>FunctionsV3::dateNow(),
		    		   'ip_address'=>$_SERVER['REMOTE_ADDR'],
		    		);	
		    		if ($vog_res['status']=="Approved"){
		    			$params_logs['status']="paid";
		    		} else $params_logs['status']=$vog_res['status'];		    		
		    		
		    		if ( $DbExt->insertData("{{sms_package_trans}}",$params_logs)){
					    $redirect_url=Yii::app()->createUrl('merchant/smsreceipt',array(
					      'id'=>Yii::app()->db->getLastInsertID()
					    ));
					    header("location: $redirect_url");
					    Yii::app()->end();
				    } else $error=Yii::t("default","ERROR: Cannot insert record.");	
		    		
		    	} else {
		    		// failed 
		    		$error=t("Failed getting transaction information");
		    	}
		    }		    
	    } else $error=t("Missing transaction id");
	    
	    $this->render('error',array(
	      'message'=>$error
	    ));
	}
	
	public function actionrzrinit()
	{
		$this->crumbsTitle=t("razorpay");
		$this->render('error',array(
		  'message'=>t("Not available")
		));
	}
	
	public function actionobdinit()
	{
		$error='';
		$package_id=isset($_GET['package_id'])?$_GET['package_id']:'';	
		$amount_to_pay=0;
		$merchant_id=Yii::app()->functions->getMerchantID();			
		$payment_description='';
		
		$back_url=Yii::app()->createUrl('/merchant/purchasesms');
		$payment_ref=Yii::app()->functions->generateCode()."TT".Yii::app()->functions->getLastIncrement('{{sms_package_trans}}');	
		if ( $res=Yii::app()->functions->getSMSPackagesById($package_id) ){
			
			$amount_to_pay=$res['price'];
			if ( $res['promo_price']>0){
				$amount_to_pay=$res['promo_price'];
			}	    										
			$amount_to_pay=is_numeric($amount_to_pay)?normalPrettyPrice($amount_to_pay):'';	
			$payment_description.=isset($res['title'])?$res['title']:'';		
			
			$merchant_info=Yii::app()->functions->getMerchantInfo();			
			$merchant_email=$merchant_info[0]->contact_email;			
			if (!empty($merchant_email)){			
								
				$lang=Yii::app()->language;   
				$enabled = getOptionA("offline_bank_deposit_signup_merchant_email");
				if($enabled){
					
				   $subject = getOptionA("offline_bank_deposit_tpl_subject_$lang");
	    	       $tpl = getOptionA("offline_bank_deposit_tpl_content_$lang");	
	    	       	    	       
	    	       $verify_link=FunctionsV3::getHostURL().Yii::app()->createUrl('merchant/bankdepositverify',array(
	    		   'ref'=>$payment_ref
	    		   )); 
	    		   
	    		   $tpl = FunctionsV3::smarty("amount", FunctionsV3::prettyPrice($amount_to_pay) , $tpl);
	    		   $tpl = FunctionsV3::smarty("restaurant_name", stripslashes($merchant_info[0]->restaurant_name) , $tpl);
	    		   $tpl = FunctionsV3::smarty("verify_payment_link", $verify_link , $tpl);
	    		   
	    		   $pattern=array(			    	   
			    	   'sitename'=>getOptionA('website_title'),
			    	   'siteurl'=>websiteUrl(),	 			    	   
			       );
			       $tpl=FunctionsV3::replaceTemplateTags($tpl,$pattern,'');	
	    		   
	    		   //dump($subject); dump($tpl);
	    		   $sender=getOptionA("global_admin_sender_email");
	    		   
	    		    $DbExt=new DbExt();
	    		    $params=array(
					    'email_address'=>$merchant_email,
					    'sender'=>$sender,
					    'subject'=>$subject,
					    'content'=>$tpl,
					    'date_created'=>FunctionsV3::dateNow(),
					    'ip_address'=>$_SERVER['REMOTE_ADDR'],
					    'module_type'=>'core'
					);	  
					if ($DbExt->insertData("{{email_logs}}",$params)){						
						
						FunctionsV3::runCronEmail();
						
						$payment_code=Yii::app()->functions->paymentCode("bankdeposit");
						
						$params=array(
							  'merchant_id'=>$merchant_id,
							  'sms_package_id'=>$package_id,
							  'payment_type'=>$payment_code,
							  'package_price'=>$amount_to_pay,
							  'sms_limit'=>isset($res['sms_limit'])?$res['sms_limit']:'',
							  'date_created'=>FunctionsV3::dateNow(),
							  'ip_address'=>$_SERVER['REMOTE_ADDR'],
							  'payment_gateway_response'=>json_encode($_GET),						  
							  'payment_reference'=>$payment_ref
						 );							 
						 $DbExt->insertData("{{sms_package_trans}}",$params);   		
						 
						 $last_id = Yii::app()->db->getLastInsertID();
						 $redirect_link = Yii::app()->createUrl('merchant/smsReceipt',array(
						  'id'=>$last_id
						 ));
						
						 header("Location: $redirect_link");
						 Yii::app()->end();
						
					} else $error=t("Failed cannot insert records");
					
				} else $error=t("Offline bank deposit template is not available");
			} else $error=t("please correct your email address. we cannot sent bank instruction with empty merchant email address");		
		} else $error=Yii::t("default","Failed. Cannot process payment");  	
		
		if (!empty($error)){
			$this->render('error',array('message'=>$error));
		}		
	}
	
	public function actionreviewreply()
	{		
		$merchant_id=Yii::app()->functions->getMerchantID();
		$this->crumbsTitle=Yii::t("default","Customer reviews");
		if ( $data=Yii::app()->functions->getReviewsById2(isset($_GET['id'])?$_GET['id']:'',$merchant_id)){		
			$data2='';
			if(isset($_GET['record_id'])){
			   $data2=Yii::app()->functions->getReviewsById($_GET['record_id']);
			}
			$this->render('review-reply',array(
			  'data'=>$data,
			  'data2'=>$data2
			));
		} else $this->render('error',array('message'=>t("Sorry but we cannot find what you are looking for.")));
	}
	
	public function actionocrinit()
	{
		$this->crumbsTitle=Yii::t("default","Purchase using Offline Credit Card");
		$this->render('select-cc');
	}
	
	
	public function actionipay()
	{
		$this->crumbsTitle=t("Ipay");
		$this->render('ipay',array(
		  'merchant_id'=>Yii::app()->functions->getMerchantID()
		));
	}
	
	public function actionpipay()
	{
		$this->crumbsTitle=t("Pi Pay");
		$this->render('pipay-settings',array(
		  'merchant_id'=>Yii::app()->functions->getMerchantID()
		));
	}
	
	public function actionpipayinit()
	{
		$this->crumbsTitle=t("Pi Pay");
		$this->render('pipayinit',array(
		));
	}
	
	public function actionhubtelsettings()
	{
		$this->crumbsTitle=t("Hubtel Payments");
		$this->render('hubtel-settings',array(
		  'mtid'=>Yii::app()->functions->getMerchantID()
		));
	}
	
	public function actionsofort()
	{
		$this->crumbsTitle=t("Sofort Payments");
		$this->render('sofort-settings',array(
		  'mtid'=>Yii::app()->functions->getMerchantID()
		));
	}
	
	public function actionjampie()
	{
		$this->render('jampie-settings',array(
		  'mtid'=>Yii::app()->functions->getMerchantID()
		));
	}
	
	public function actionpoints_settings()
	{
		
		if (FunctionsV3::hasModuleAddon("pointsprogram")){
		
			$disabled_settings=getOptionA('points_disabled_merchant_settings');		
			$this->crumbsTitle=t("Loyalty Points Settings");
			if($disabled_settings==1){
			   $this->render('error',array(
			     'message'=>t("Sorry but this page is not available")
			   ));
			} else {
				$this->render('points-settings',array(
				  'mtid'=>Yii::app()->functions->getMerchantID(),
				  'status_list'=>Yii::app()->functions->orderStatusList()
				));
			}
		} else {
			$this->render('error',array(
			 'message'=>t("This requires points addon")
			));
		}
	}
	
	public function actionwing()
	{
		$this->crumbsTitle=t("Wing Payment");
		$this->render('wing-settings',array(
		  'mtid'=>Yii::app()->functions->getMerchantID()
		));
	}
	
	public function actionpaymill()
	{
		$this->crumbsTitle=t("Paymill Settings");
		$this->render('paymill-settings',array(
		  'mtid'=>Yii::app()->functions->getMerchantID()
		));
	}	
	
	public function actionipay_africa()
	{
		$this->crumbsTitle=t("Ipay Africa");
		$this->render('ipay-africa',array(
		  'mtid'=>Yii::app()->functions->getMerchantID()
		));
	}		
	
	public function actiondixipay()
	{
		$this->crumbsTitle=t("DIXIPAY");
		$this->render('dixipay-settings-mt',array(
		  'mtid'=>Yii::app()->functions->getMerchantID()
		));
	}
	
	public function actionipay_africainit()
	{
		$error='';
		$amount_to_pay=0; 
		$payment_description='';
		$merchant_id = Yii::app()->functions->getMerchantID();		
		$package_id=isset($_GET['package_id'])?$_GET['package_id']:'';		
		
		if ( $res=Yii::app()->functions->getSMSPackagesById($package_id) ){
			$merchant_info = FunctionsV3::getMerchantInfo($merchant_id);
			
			$payment_description.=isset($res['title'])?$res['title']:'';	
			
			$amount_to_pay=$res['price'];
			if ( $res['promo_price']>0){
				$amount_to_pay=$res['promo_price'];
			}	    
			
			$telephone = $merchant_info['contact_phone'];
			if(!empty($telephone)){
				$telephone=str_replace("+","",$telephone);
			}
			$email_address = $merchant_info['contact_email'];		

			$back_url= Yii::app()->createUrl('/merchant/purchasesms');
			
			if ( $credentials = IpayAfrica::getAdminCredentials()){
				$this->crumbsTitle=t("Ipay Africa"); 
				$this->render('africa_buy_sms',array(
				  'credentials'=>$credentials,
				  'amount_to_pay'=>$amount_to_pay,
				  'payment_description'=>$payment_description,
				  'telephone'=>$telephone,
				  'email_address'=>$email_address,
				  'back_url'=>$back_url,
				  'order_id'=>FunctionsV3::lastID('sms_package_trans')."-".time(),
				  'package_id'=>$package_id
				));
			} else $error= t("Payment settings is not properly configured");
			
		} else $error=Yii::t("default","Sorry but we cannot find what your are looking for.");
		
		if(!empty($error)){
			$this->render('error',array(
			  '$message'=>$error
			));
		}
	}
	
	public function actionwirecard()
	{
		$this->crumbsTitle=t("WireCard");
		$this->render('wirecard-settings-mt',array(
		  'mtid'=>Yii::app()->functions->getMerchantID()
		));
	}
	
	public function actionpayulatam()
	{
		$this->crumbsTitle=t("PayU Latam");
		$this->render('payulatam-settings-mt',array(
		  'mtid'=>Yii::app()->functions->getMerchantID()
		));
	}
	
}
/*END CONTROLLER*/
