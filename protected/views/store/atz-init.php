<?php
$error='';
$db_ext=new DbExt;
$data_get=$_GET;
$data_post=$_POST;

$amount_to_pay=0;

$my_token=isset($_GET['token'])?$_GET['token']:'';
$back_url=Yii::app()->request->baseUrl."/store/merchantSignup/Do/step3/token/".$my_token;

$payment_description='';
$payment_ref=Yii::app()->functions->generateCode()."TT".Yii::app()->functions->getLastIncrement('{{package_trans}}');

$my_token=isset($_GET['token'])?$_GET['token']:'';
$package_id=isset($_GET['package_id'])?$_GET['package_id']:'';	


$mode_autho=Yii::app()->functions->getOptionAdmin('admin_mode_autho');
$autho_api_id=Yii::app()->functions->getOptionAdmin('admin_autho_api_id');
$autho_key=Yii::app()->functions->getOptionAdmin('admin_autho_key');

if ( empty($mode_autho) && empty($autho_api_id) && empty($autho_key)){
	$error=t("Authorize.net is not properly configured");
}

$extra_params='';
if (isset($_GET['renew'])){
	$extra_params="/renew/1/package_id/".$package_id;
}

if ( $res=Yii::app()->functions->getMerchantByToken($my_token)){ 
		
	if (isset($_GET['renew'])){ 		
		if ($new_info=Yii::app()->functions->getPackagesById($package_id)){	    					
			$res['package_name']=$new_info['title'];
			$res['package_price']=$new_info['price'];
			if ($new_info['promo_price']>0){
				$res['package_price']=$new_info['promo_price'];
			}			
		}
	}
		
	$merchant_id=$res['merchant_id'];
	$payment_description="Membership Package - ".$res['package_name'];
	$amount_to_pay=standardPrettyFormat($res['package_price']);
	
	if (isset($_POST['x_card_num'])){
							
		define("AUTHORIZENET_API_LOGIN_ID",$autho_api_id); 
        define("AUTHORIZENET_TRANSACTION_KEY",$autho_key);
        define("AUTHORIZENET_SANDBOX",$mode_autho=="sandbox"?true:false);       
        //define("TEST_REQUEST", $mode_autho=="sandbox"?"FALSE":"TRUE"); 
        		
        require_once 'anet_php_sdk/AuthorizeNet.php';
        $transaction = new AuthorizeNetAIM;
        $transaction->setSandbox(AUTHORIZENET_SANDBOX);
                
        $params= array(
        'company'     => $res['restaurant_name'],
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
        //die();
        
        $transaction->setFields($params);        
        $response = $transaction->authorizeAndCapture();
                
        if ($response->approved) {
        	$resp_transaction = $response->transaction_id;
        	//dump("Transaction ID :".$resp_transaction);
        	        	
        	if (isset($_GET['renew'])){ 
        		
        		if ($new_info=Yii::app()->functions->getPackagesById($package_id)){	    					
					$res['package_name']=$new_info['title'];
					$res['package_price']=$new_info['price'];
					if ($new_info['promo_price']>0){
						$res['package_price']=$new_info['promo_price'];
					}			
				}
																													
				$membership_info=Yii::app()->functions->upgradeMembership($res['merchant_id'],$package_id);
				
				$params=array(
		          'package_id'=>$package_id,	          
		          'merchant_id'=>$res['merchant_id'],
		          'price'=>$res['package_price'],
		          'payment_type'=>Yii::app()->functions->paymentCode('authorize'),
		          'membership_expired'=>$membership_info['membership_expired'],
		          'date_created'=>FunctionsV3::dateNow(),
		          'ip_address'=>$_SERVER['REMOTE_ADDR'],
		          'PAYPALFULLRESPONSE'=>json_encode($response),
		           'TRANSACTIONID'=>$resp_transaction,
		           'TOKEN'=>$my_token
		        );		
        		
		        /*dump("Update Params:");
		        dump($params);*/
		        
        	} else {
	        	$params=array(
		           'package_id'=>$res['package_id'],	          
		           'merchant_id'=>$res['merchant_id'],
		           'price'=>$res['package_price'],
		           'payment_type'=>Yii::app()->functions->paymentCode('authorize'),
		           'membership_expired'=>$res['membership_expired'],
		           'date_created'=>FunctionsV3::dateNow(),
		           'ip_address'=>$_SERVER['REMOTE_ADDR'],
		           'PAYPALFULLRESPONSE'=>json_encode($response),
		           'TRANSACTIONID'=>$resp_transaction,
		           'TOKEN'=>$my_token
		         );	    
        	}     
	         $db_ext->insertData("{{package_trans}}",$params);	
	         
	         $db_ext->updateData("{{merchant}}",
								  array(
								    'payment_steps'=>3,
								    'membership_purchase_date'=>FunctionsV3::dateNow()
								  ),'merchant_id',$res['merchant_id']);
											  		         		    
             $okmsg=Yii::t("default","transacción fue exitosa");		         
        	             
             if (isset($_GET['renew'])){
             	$redirect= Yii::app()->createUrl('store/renewsuccesful');
             } else {
             	
             	/*SEND EMAIL*/
             	FunctionsV3::sendWelcomeEmailMerchant($res);
             	FunctionsV3::sendMerchantActivation($res, isset($res['activation_key'])?$res['activation_key']:'' );
             	$redirect=Yii::app()->createUrl('store/merchantSignup',array(
                  'Do'=>"step4",
                  'token'=>$my_token
                ));
             }
             header("Location: $redirect");
		     Yii::app()->end();
		     
        } else $error=$response->response_reason_text;
	}	
} else $error=Yii::t("default","Algo falló. No se pudo procesar el pago");  

$merchant_default_country=Yii::app()->functions->getOptionAdmin('merchant_default_country');  

$this->renderPartial('/front/banner-receipt',array(
   'h1'=>t("Payment"),
   'sub_text'=>t("step 3 of 4")
));

/*PROGRESS ORDER BAR*/
$this->renderPartial('/front/progress-merchantsignup',array(
   'step'=>3,
   'show_bar'=>true
));

?>


<div class="sections section-grey2 section-orangeform">
  <div class="container">  
    <div class="row top30">
       <div class="inner">
          <h1><?php echo t("Pay using Authorize.net")?></h1>
          <div class="box-grey rounded">	     
          
           <?php if ( !empty($error)):?>
              <p class="text-danger"><?php echo $error;?></p>  
           <?php else :?>
               <form id="forms-normal" class="forms"  method="POST" >
                              
				<div class="row top10">
				  <div class="col-md-3"><?php echo t("Amount")?></div>
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
				  <div class="col-md-3"><?php echo t("Credit Card Number")?></div>
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
				  <div class="col-md-3"><?php echo t("Exp. month")?></div>
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
				  <div class="col-md-3"><?php echo t("Exp. year")?></div>
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
				  <div class="col-md-3"><?php echo t("First Name")?></div>
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
				  <div class="col-md-3"><?php echo t("Last Name")?></div>
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
				  <div class="col-md-3"><?php echo t("Address")?></div>
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
				  <div class="col-md-3"><?php echo t("City")?></div>
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
				  <div class="col-md-3"><?php echo t("State")?></div>
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
				  <div class="col-md-3"><?php echo t("Zip Code")?></div>
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
				  <div class="col-md-3"><?php echo t("Country")?></div>
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
                             
           <?php endif;?>
           
             <div class="top25">
			 <a href="<?php 
			 if (isset($_GET['renew'])) {		 
			    	echo Yii::app()->createUrl('store/merchantsignup',array(
				     'do'=>"step3",
				     'token'=>$my_token,
				     'renew'=>1,
				     'package_id'=>$package_id
				   ));
			    } else {
	 			    echo Yii::app()->createUrl('store/merchantsignup',array(
				      'do'=>"step3",
				      'token'=>$my_token));
			    }
			 ?>">
	         <i class="ion-ios-arrow-thin-left"></i> <?php echo Yii::t("default","Click aqui para cambiar el metodo de pago")?></a>
	         </div>
          
          </div> <!--box-->
       </div> <!--inner-->
    </div> <!--row-->
  </div> <!--container-->
</div><!-- sections-->