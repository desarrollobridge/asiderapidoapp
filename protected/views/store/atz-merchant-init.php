<?php
$this->renderPartial('/front/banner-receipt',array(
   'h1'=>t("Payment"),
   'sub_text'=>t("")
));

$this->renderPartial('/front/order-progress-bar',array(
   'step'=>4,
   'show_bar'=>true
));

$db_ext=new DbExt;
$error='';
$success='';
$amount_to_pay=0;
$payment_description=Yii::t("default",'Payment to merchant');
$payment_ref=Yii::app()->functions->generateCode()."TT".Yii::app()->functions->getLastIncrement('{{order}}');

$data_get=$_GET;
$data_post=$_POST;

$merchant_default_country=Yii::app()->functions->getOptionAdmin('merchant_default_country');  

if ( $data=Yii::app()->functions->getOrder($_GET['id'])){	
	$merchant_id=isset($data['merchant_id'])?$data['merchant_id']:'';
	
	$mode_autho=Yii::app()->functions->getOption('merchant_mode_autho',$merchant_id);
    $autho_api_id=Yii::app()->functions->getOption('merchant_autho_api_id',$merchant_id);
    $autho_key=Yii::app()->functions->getOption('merchant_autho_key',$merchant_id);
		
	$payment_description.=isset($data['merchant_name'])?" ".$data['merchant_name']:'';	
		
    $mtid=Yii::app()->functions->getOption('merchant_sanbox_sisow_secret_key',$merchant_id);
    $mtkey=Yii::app()->functions->getOption('merchant_sandbox_sisow_pub_key',$merchant_id);
    $mtshopid=Yii::app()->functions->getOption('merchant_sandbox_sisow_shopid',$merchant_id);
    $mode=Yii::app()->functions->getOption('merchant_sisow_mode',$merchant_id);
    
    /*COMMISSION*/
	//if ( Yii::app()->functions->isMerchantCommission($merchant_id)){			
	if (FunctionsV3::isMerchantPaymentToUseAdmin($merchant_id)){
		$mode_autho=Yii::app()->functions->getOptionAdmin('admin_mode_autho');
        $autho_api_id=Yii::app()->functions->getOptionAdmin('admin_autho_api_id');
        $autho_key=Yii::app()->functions->getOptionAdmin('admin_autho_key');        
	}
    
	define("AUTHORIZENET_API_LOGIN_ID",$autho_api_id); 
    define("AUTHORIZENET_TRANSACTION_KEY",$autho_key);
    define("AUTHORIZENET_SANDBOX",$mode_autho=="sandbox"?true:false);       
    //define("TEST_REQUEST", $mode_autho=="sandbox"?"FALSE":"TRUE"); 
	
    $amount_to_pay=isset($data['total_w_tax'])?Yii::app()->functions->standardPrettyFormat($data['total_w_tax']):'';
    $amount_to_pay=is_numeric($amount_to_pay)?unPrettyPrice($amount_to_pay):'';
        
    /*dump($payment_description);
    dump($amount_to_pay);*/    
    
    if (isset($_POST['x_card_num'])){
    	
    	require_once 'anet_php_sdk/AuthorizeNet.php';
        $transaction = new AuthorizeNetAIM;
        $transaction->setSandbox(AUTHORIZENET_SANDBOX);
                
        $params= array(
        //'company'     => $res['restaurant_name'],
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
        //'email'      => $_POST['x_email'],
        'card_code'  => $_POST['cvv'],
        );
        //dump($params);
        
        $transaction->setFields($params);        
        $response = $transaction->authorizeAndCapture();
        if ($response->approved) {
        	$resp_transaction = $response->transaction_id;
        	
        	$params_logs=array(
	          'order_id'=>$data_get['id'],
	          'payment_type'=>Yii::app()->functions->paymentCode('authorize'),
	          'raw_response'=>json_encode($response),
	          'date_created'=>FunctionsV3::dateNow(),
	          'ip_address'=>$_SERVER['REMOTE_ADDR'],
	          'payment_reference'=>$resp_transaction
	        );
	        $db_ext->insertData("{{payment_order}}",$params_logs);
	        	       
	        $params_update=array('status'=>'paid');	        
            $db_ext->updateData("{{order}}",$params_update,'order_id',$data_get['id']);
            
            /*POINTS PROGRAM*/ 
            if (FunctionsV3::hasModuleAddon("pointsprogram")){
	           PointsProgram::updatePoints($data_get['id']);
            }
            
            /*Driver app*/
			if (FunctionsV3::hasModuleAddon("driver")){
			   Yii::app()->setImport(array(			
				  'application.modules.driver.components.*',
			   ));
			   Driver::addToTask($data_get['id']);
			}
	        	        
	        $this->redirect( Yii::app()->createUrl('/store/receipt',array(
	          'id'=>$_GET['id']
	        )) );
	        die();
        	
        } else $error=$response->response_reason_text;    	
    }        
} else  $error=Yii::t("default","¡Ups! no podemos encontrar lo que estás buscando.");	
?>

<div class="sections section-grey2 section-orangeform">
  <div class="container">  
    <div class="row top30">
       <div class="inner">
          <h1><?php echo t("Pay using Authorize.net")?></h1>
          <div class="box-grey rounded">	     
          
           <?php if ( !empty($error)):?>
              <p class="text-danger"><?php echo $error;?></p>  
           <?php endif;?>
 
               <form id="forms-normal" class="forms"  method="POST" >
                              
				<div class="row top10">
				  <div class="col-md-3"><?php echo t("Cantidad")?></div>
				  <div class="col-md-8">
				    <?php echo CHtml::textField('amount',
					  $amount_to_pay
					  ,array(
					  'class'=>'grey-fields full-width',
					  'disabled'=>true
					  ))?>
				  </div>
				</div>
								

				<div class="row top10">
				  <div class="col-md-3"><?php echo t("Numero de Tarjeta de Credito")?></div>
				  <div class="col-md-8">
				    <?php echo CHtml::textField('x_card_num',
				  isset($data_post['x_card_num'])?$data_post['x_card_num']:''
				  ,array(
				  'class'=>'grey-fields numeric_only full-width' ,
				  'data-validation'=>"required"  
				  ))?>
				  </div>
				</div>
				
				
                <div class="row top10">
				  <div class="col-md-3"><?php echo t("Mes de vencimiento")?></div>
				  <div class="col-md-8">
				      <?php echo CHtml::dropDownList('expiration_month',
				      isset($data_post['expiration_month'])?$data_post['expiration_month']:''
				      ,
				      Yii::app()->functions->ccExpirationMonth()
				      ,array(
				       'class'=>'grey-fields full-width',
				       'placeholder'=>Yii::t("default","Exp. month"),
				       'data-validation'=>"required"  
				      ))?>
				  </div>
				</div>
									
                <div class="row top10">
				  <div class="col-md-3"><?php echo t("Año de Vencimiento")?></div>
				  <div class="col-md-8">
				   <?php echo CHtml::dropDownList('expiration_yr',
				      isset($data_post['expiration_yr'])?$data_post['expiration_yr']:''
				      ,
				      Yii::app()->functions->ccExpirationYear()
				      ,array(
				       'class'=>'grey-fields full-width',
				       'placeholder'=>Yii::t("default","Exp. year") ,
				       'data-validation'=>"required"  
				      ))?>
				  </div>
				</div>
               				
               <div class="row top10">
				  <div class="col-md-3"><?php echo t("CCV")?></div>
				  <div class="col-md-8">
			      <?php echo CHtml::textField('cvv',
			      isset($data_post['cvv'])?$data_post['cvv']:''
			      ,array(
			       'class'=>'grey-fields full-width numeric_only',       
			       'data-validation'=>"required",
			       'maxlength'=>4
			      ))?>							 
				  </div>
				</div>
				

                <div class="row top10">
				  <div class="col-md-3"><?php echo t("Nombre")?></div>
				  <div class="col-md-8">
				    <?php echo CHtml::textField('x_first_name',
  isset($data_post['x_first_name'])?$data_post['x_first_name']:''
  ,array(
  'class'=>'grey-fields full-width',
  'data-validation'=>"required"  
  ))?>
				  </div>
				</div>				
				
				<div class="row top10">
				  <div class="col-md-3"><?php echo t("Apellido")?></div>
				  <div class="col-md-8">
  <?php echo CHtml::textField('x_last_name',
  isset($data_post['x_last_name'])?$data_post['x_last_name']:''
  ,array(
  'class'=>'grey-fields full-width',
  'data-validation'=>"required"  
  ))?>				  
				  </div>
				</div>				
				
				<div class="row top10">
				  <div class="col-md-3"><?php echo t("Dirección")?></div>
				  <div class="col-md-8">
  <?php echo CHtml::textField('x_address',
  isset($data_post['x_address'])?$data_post['x_address']:''
  ,array(
  'class'=>'grey-fields full-width',
  'data-validation'=>"required"  
  ))?>
				  
				  </div>
				</div>				
				
				<div class="row top10">
				  <div class="col-md-3"><?php echo t("Ciudad")?></div>
				  <div class="col-md-8">
  <?php echo CHtml::textField('x_city',
  isset($data_post['x_city'])?$data_post['x_city']:'' 
  ,array(
  'class'=>'grey-fields full-width',
  'data-validation'=>"required"   
  ))?>
				  
				  </div>
				</div>				
				
				<div class="row top10">
				  <div class="col-md-3"><?php echo t("Estado")?></div>
				  <div class="col-md-8">
  <?php echo CHtml::textField('x_state',
  isset($data_post['x_state'])?$data_post['x_state']:''
  ,array(
  'class'=>'grey-fields full-width',
  'data-validation'=>"required"    
  ))?>
				  
				  </div>
				</div>				
				
				<div class="row top10">
				  <div class="col-md-3"><?php echo t("Codigo Postal")?></div>
				  <div class="col-md-8">
  <?php echo CHtml::textField('x_zip',
  isset($data_post['x_zip'])?$data_post['x_zip']:''
  ,array(
  'class'=>'grey-fields full-width',
  'data-validation'=>"required"  
  ))?>
				  
				  </div>
				</div>				
				
				<div class="row top10">
				  <div class="col-md-3"><?php echo t("País")?></div>
				  <div class="col-md-8">
  <?php echo CHtml::dropDownList('x_country',
  isset($data_post['country_code'])?$data_post['country_code']:$merchant_default_country,
  (array)Yii::app()->functions->CountryListMerchant(),          
  array(
  'class'=>'grey-fields full-width',
  'data-validation'=>"required"
  ))?>
				  
				  </div>
				</div>			
				
				
               <div class="row top10">
				  <div class="col-md-3"></div>
				  <div class="col-md-8">
				  <input type="submit" value="<?php echo Yii::t("default","Paga Ahora")?>" class="black-button inline medium">
				  </div>
				</div>	
				
               </form>           
           
           <?php //endif;?>
           
           <div class="top25">
		     <a href="<?php echo Yii::app()->createUrl('/store/paymentoption')?>">
             <i class="ion-ios-arrow-thin-left"></i> <?php echo Yii::t("default","Click aqui para cambiar el metodo de pago")?></a>
            </div>
          
          </div> <!--box-->
       </div> <!--inner-->
    </div> <!--row-->
  </div> <!--container-->
</div><!-- sections-->