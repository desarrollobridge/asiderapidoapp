<?php
$db_ext=new DbExt;
$payment_code=Yii::app()->functions->paymentCode("authorize");

$error='';
$success='';
$amount_to_pay=0;
$payment_description='';
$payment_ref=Yii::app()->functions->generateCode()."TT".Yii::app()->functions->getLastIncrement('{{sms_package_trans}}');
$data_get=$_GET;

$data_post=$_POST;

$merchant_default_country=Yii::app()->functions->getOptionAdmin('merchant_default_country');  

$package_id=isset($_GET['package_id'])?$_GET['package_id']:'';

$mode_autho=Yii::app()->functions->getOptionAdmin('admin_mode_autho');
$autho_api_id=Yii::app()->functions->getOptionAdmin('admin_autho_api_id');
$autho_key=Yii::app()->functions->getOptionAdmin('admin_autho_key');

if ( empty($mode_autho) && empty($autho_api_id) && empty($autho_key)){
	$error=t("Authorize.net is not properly configured");
}

if ( $res=Yii::app()->functions->getSMSPackagesById($package_id) ){
	$amount_to_pay=$res['price'];
	if ( $res['promo_price']>0){
		$amount_to_pay=$res['promo_price'];
	}	    										
	$amount_to_pay=is_numeric($amount_to_pay)?normalPrettyPrice($amount_to_pay):'';
	$amount_to_pay=unPrettyPrice($amount_to_pay);	
	$payment_description.=isset($res['title'])?$res['title']:'';	
	
	/*dump($amount_to_pay);
	dump($payment_description);*/
	
	if (isset($_POST['x_card_num'])){
		
		define("AUTHORIZENET_API_LOGIN_ID",$autho_api_id); 
        define("AUTHORIZENET_TRANSACTION_KEY",$autho_key);
        define("AUTHORIZENET_SANDBOX",$mode_autho=="sandbox"?true:false);       
        //define("TEST_REQUEST", $mode_autho=="sandbox"?"FALSE":"TRUE"); 
        
		require_once 'anet_php_sdk/AuthorizeNet.php';
        $transaction = new AuthorizeNetAIM;
        $transaction->setSandbox(AUTHORIZENET_SANDBOX);
                
        $params= array(        
        'description' => $payment_description,
        'amount'     => $amount_to_pay, 
        'card_num'   => $_POST['x_card_num'], 
        'exp_date'   => $_POST['expiration_month']."/".$_POST['expiration_yr'],
        'first_name' => $_POST['x_first_name'],
        'last_name'  => $_POST['x_last_name'],
        'address'    => $_POST['x_address'],
        'city'       => $_POST['x_city'],
        'state'      => $_POST['x_state'],
        'country'    => $_POST['x_country'],
        'zip'        => $_POST['x_zip'],        
        'card_code'  => $_POST['cvv'],
        );
        
        $transaction->setFields($params);        
        $response = $transaction->authorizeAndCapture();
        if ($response->approved) {
        	$resp_transaction = $response->transaction_id;
        	        	
        	$params=array(
			  'merchant_id'=>Yii::app()->functions->getMerchantID(),
			  'sms_package_id'=>$package_id,
			  'payment_type'=>$payment_code,
			  'package_price'=>$amount_to_pay,
			  'sms_limit'=>isset($res['sms_limit'])?$res['sms_limit']:'',
			  'date_created'=>FunctionsV3::dateNow(),
			  'ip_address'=>$_SERVER['REMOTE_ADDR'],
			  'payment_gateway_response'=>json_encode($response),
			  'status'=>"paid",
			  'payment_reference'=>$resp_transaction
			);	    										
			if ( $db_ext->insertData("{{sms_package_trans}}",$params)){										
                 header('Location: '.Yii::app()->request->baseUrl."/merchant/smsReceipt/id/".Yii::app()->db->getLastInsertID());
            } else $error=Yii::t("default","ERROR: Cannot insert record.");
        	
        } else $error=$response->response_reason_text;    		
	}		  
} else $error=Yii::t("default","Sorry but we cannot find what your are looking for.");
?>
<div class="page-right-sidebar payment-option-page">
  <div class="main">  
  
  
  <!--<h2><?php echo Yii::t("default","Pay using Authorize.net")?></h2>-->
  
  <?php if ( !empty($error)):?>
  <p class="uk-text-danger"><?php echo $error;?></p>  
  <?php endif;?>
  
  <form id="forms-normal" class="uk-form uk-form-horizontal forms"  method="POST" >
    
  <div class="uk-form-row">
  <label class="uk-form-label"><?php echo Yii::t("default","Amount")?></label>
  <?php echo CHtml::textField('amount',
  $amount_to_pay
  ,array(
  'class'=>'uk-form-width-large',
  'disabled'=>true
  ))?>
  </div>    
  
  <div class="uk-form-row">
  <label class="uk-form-label"><?php echo Yii::t("default","Credit Card Number")?></label>
  <?php echo CHtml::textField('x_card_num',
  isset($data_post['x_card_num'])?$data_post['x_card_num']:''
  ,array(
  'class'=>'uk-form-width-large numeric_only' ,
  'data-validation'=>"required"  
  ))?>
  </div>    
   
  <div class="uk-form-row">           
      <label class="uk-form-label"><?php echo Yii::t("default","Exp. month")?></label>       
      <?php echo CHtml::dropDownList('expiration_month',
      isset($data_post['expiration_month'])?$data_post['expiration_month']:''
      ,
      Yii::app()->functions->ccExpirationMonth()
      ,array(
       'class'=>'uk-form-width-large',
       'placeholder'=>Yii::t("default","Exp. month"),
       'data-validation'=>"required"  
      ))?>
   </div>             
   
   <div class="uk-form-row">                  
      <label class="uk-form-label"><?php echo Yii::t("default","Exp. year")?></label>       
      <?php echo CHtml::dropDownList('expiration_yr',
      isset($data_post['expiration_yr'])?$data_post['expiration_yr']:''
      ,
      Yii::app()->functions->ccExpirationYear()
      ,array(
       'class'=>'uk-form-width-large',
       'placeholder'=>Yii::t("default","Exp. year") ,
       'data-validation'=>"required"  
      ))?>
   </div>             
   
   <div class="uk-form-row">                  
      <label class="uk-form-label"><?php echo Yii::t("default","CCV")?></label>       
      <?php echo CHtml::textField('cvv',
      isset($data_post['cvv'])?$data_post['cvv']:''
      ,array(
       'class'=>'uk-form-width-large numeric_only',       
       'data-validation'=>"required",
       'maxlength'=>4
      ))?>
   </div>   
   
   
 <div class="uk-form-row">
  <label class="uk-form-label"><?php echo Yii::t("default","First Name")?></label>
  <?php echo CHtml::textField('x_first_name',
  isset($data_post['x_first_name'])?$data_post['x_first_name']:''
  ,array(
  'class'=>'uk-form-width-large ',
  'data-validation'=>"required"  
  ))?>
  </div>       
  
  <div class="uk-form-row">
  <label class="uk-form-label"><?php echo Yii::t("default","Last Name")?></label>
  <?php echo CHtml::textField('x_last_name',
  isset($data_post['x_last_name'])?$data_post['x_last_name']:''
  ,array(
  'class'=>'uk-form-width-large',
  'data-validation'=>"required"  
  ))?>
  </div>       
  
  <div class="uk-form-row">
  <label class="uk-form-label"><?php echo Yii::t("default","Address")?></label>
  <?php echo CHtml::textField('x_address',
  isset($data_post['x_address'])?$data_post['x_address']:''
  ,array(
  'class'=>'uk-form-width-large',
  'data-validation'=>"required"  
  ))?>
  </div>       
  
  <div class="uk-form-row">
  <label class="uk-form-label"><?php echo Yii::t("default","City")?></label>
  <?php echo CHtml::textField('x_city',
  isset($data_post['x_city'])?$data_post['x_city']:'' 
  ,array(
  'class'=>'uk-form-width-large',
  'data-validation'=>"required"   
  ))?>
  </div>       
  
  <div class="uk-form-row">
  <label class="uk-form-label"><?php echo Yii::t("default","State")?></label>
  <?php echo CHtml::textField('x_state',
  isset($data_post['x_state'])?$data_post['x_state']:''
  ,array(
  'class'=>'uk-form-width-large',
  'data-validation'=>"required"    
  ))?>
  </div>       
  
  <div class="uk-form-row">
  <label class="uk-form-label"><?php echo Yii::t("default","Zip Code")?></label>
  <?php echo CHtml::textField('x_zip',
  isset($data_post['x_zip'])?$data_post['x_zip']:''
  ,array(
  'class'=>'uk-form-width-large',
  'data-validation'=>"required"  
  ))?>
  </div>       
  
  <div class="uk-form-row">
  <label class="uk-form-label"><?php echo Yii::t("default","Country")?></label>
  <?php echo CHtml::dropDownList('x_country',
  isset($data_post['country_code'])?$data_post['country_code']:$merchant_default_country,
  (array)Yii::app()->functions->CountryListMerchant(),          
  array(
  'class'=>'uk-form-width-large',
  'data-validation'=>"required"
  ))?>
  </div>       
  
  <div class="uk-form-row">
  <label class="uk-form-label"></label>
  <input type="submit" value="<?php echo Yii::t("default","Pay Now")?>" class="uk-button uk-form-width-medium uk-button-success">
  </div>   

  </form>
    
  
  </div>
</div>