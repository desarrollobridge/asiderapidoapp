<?php
$patern="cuisine|signup|signin|merchantsignup|contact|searcharea";
$patern.="|menu|checkout|paymentoption|receipt|logout|paypalinit|paypalverify";
$patern.="|OrderHistory|Profile|howItWork|forgotPassword|PageSetlanguage|stripeInit";
$patern.="|MercadoInit|RenewSuccesful|Browse|PaylineInit|Paylineverify|sisowinit";
$patern.="|PayuInit|BankDepositverify|AutoResto|AutoStreetName|AutoCategory|PayseraInit";
$patern.="|AutoFoodName|Confirmorder|Paymentbcy|Bcyinit|EpayBg|EpyInit";
$patern.="|GuestCheckout|MerchantSignupSelection|MerchantSignupinfo|CancelWithdrawal";
$patern.="|ATZinit|DepositVerify|Verification|Map|GoogleLogin|AddressBook";
$patern.="|AutoZipcode|AutoPostAddress|Item|Ty|EmailVerification|MyPoints|BtrInit|setlanguage";
$patern.="|mollieinit|mollieprocess|home|molliewebhook";
$patern.="|ip8init|ipay88verify|ipay88receiver";
$patern.="|monerisinit|confirmorder|rzrinit|rzrverify|acceptorder|declineorder|cart|restaurant";
$patern.="|voguepaynotify|voguepaysuccess|voguepayfailed|voginit|vognotify|voginit|vogsuccess";
$patern.="|ipayinit|ipay_success_url|ipay_ipn_url|admin_ipay_ipn_url|admin_ipay_success_url";
$patern.="|pipayinit|pipayconfirm";
$patern.="|sofortinit|sofort_success|sofort_notify|sofort_update_order";
$patern.="|success_jampie|cancel_jampie|jampieinit";
$patern.="|winginit|wing_notify";
$patern.="|paymill_transaction";
$patern.="|strip_idealinit|strip_ideal|stripe_ideal_webhook|ideal_mobile|ideal_receipt";
$patern.="|ipay_africainit|ipay_africa_buy|ipay_africa_pay";
$patern.="|dixipayinit|dixipay_process";
$patern.="|mollieprocess_mobile";
$patern.="|wirecardinit|wirecard_confirm|wirecard_process";
$patern.="|payulataminit|payulatam_pay";

$patern=strtolower($patern);

return array(
	'name'=>'Así de Rápido',
	
	'defaultController'=>'store',
		
	'import'=>array(
		'application.models.*',
		'application.models.admin.*',
		'application.components.*',
		'application.vendor.*',
		'application.modules.pointsprogram.components.*',
		'application.modules.mobileapp.components.*',
		'application.modules.merchantapp.components.*',
		'application.modules.driver.components.*',
	),
	
	'language'=>'es',//se cambia la etiqueta del idioma y se coloca por defecto en Español
	
	'params'=>array(	   
	   'validate_request_session'=>true,
	   'validate_request_csrf'=>true,
	),
			
	'modules'=>array(		
		'exportmanager'=>array(
		  'require_login'=>true
		),
		'mobileapp'=>array(
		  'require_login'=>true
		),
		'pointsprogram'=>array(
		  'require_login'=>true
		),
		'merchantapp'=>array(
		  'require_login'=>true
		),
		'driver'=>array(
		  'require_login'=>true
		),
		'singlerestaurant'=>array(),
		'printer'=>array('require_login'=>true),
	),
	
	'components'=>array(		   
	    'request'=>array(
	        //'class' => 'application.components.HttpRequest',
            'enableCsrfValidation'=>false,
        ),
        'session' => array(
	        'timeout' => 86400,
	    ),
	    'urlManager'=>array(
	        'class' => 'UrlManager', 
		    'urlFormat'=>'path',		    
		    'showScriptName'=>false,		    
		    'rules'=>array(		
		        '' => 'store/index',
		        '<action:('.$patern.')>' => 'store/<action>',
                '<controller:\w+>/<id:\d+>'=>'<controller>/view',
		        '<controller:\w+>'=>'<controller>/index',		         
		        '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
		        '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
		        //'<lang:\w+>/<module:\w+>/<controller:\w+>/<action:\w+>/'=>'<module>/<controller>/<action>',		
		    )		    
		),
				
		/*'db'=>array(	        
		    'class'            => 'CDbConnection' ,
			'connectionString' => 'mysql:host=localhost;dbname=root_testkmrs',
			'emulatePrepare'   => true,
			'username'         => 'root_testkmr',
			'password'         => 'fH3XpcOQ',
			'charset'          => 'utf8',
			'tablePrefix'      => 'mt_',
	    ),*/

	    'db'=>array(	        
		    'class'            => 'CDbConnection' ,
			'connectionString' => 'mysql:host=localhost;dbname=root_testkmrs',
			'emulatePrepare'   => true,
			'username'         => 'root',
			'password'         => '',
			'charset'          => 'utf8',
			'tablePrefix'      => 'mt_',
	    ),
		
	    'functions'=> array(
	       'class'=>'Functions'	       
	    ),
	    'validator'=>array(
	       'class'=>'Validator'
	    ),
	    'widgets'=> array(
	       'class'=>'Widgets'
	    ),
	    	    
	    'Smtpmail'=>array(
	        'class'=>'application.extension.smtpmail.PHPMailer',
	        'Host'=>"YOUR HOST",
            'Username'=>'YOUR USERNAME',
            'Password'=>'YOUR PASSWORD',
            'Mailer'=>'smtp',
            'Port'=>587, // change this port according to your mail server
            'SMTPAuth'=>true,   
            'ContentType'=>'UTF-8',
            //'SMTPSecure'=>'ssl'// tls
	    ), 
	    
	    'GoogleApis' => array(
	         'class' => 'application.extension.GoogleApis.GoogleApis',
	         'clientId' => '', 
	         'clientSecret' => '',
	         'redirectUri' => '',
	         'developerKey' => '',
	    ),
	),
);

function statusList()
{
	return array(
	 'publish'=>Yii::t("default",'Publish'),
	 'pending'=>Yii::t("default",'Pending for review'),
	 'draft'=>Yii::t("default",'Draft')
	);
}

function clientStatus()
{
	return array(
	 'pending'=>Yii::t("default",'pendiente por aprobación'),
	 'active'=>Yii::t("default",'activo'),	 
	 'suspended'=>Yii::t("default",'suspendido'),
	 'blocked'=>Yii::t("default",'bloqueado'),
	 'expired'=>Yii::t("default",'expirado')
	);
}

function paymentStatus()
{
	return array(
	 'pending'=>Yii::t("default",'pendiente'),
	 'paid'=>Yii::t("default",'pagado'),
	 'draft'=>Yii::t("default",'borrador')
	);
}

function dump($data=''){
    echo '<pre>';print_r($data);echo '</pre>';
}
