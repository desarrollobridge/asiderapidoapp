<?php
$this->renderPartial('/front/default-header',array(
   'h1'=>t("Payment Option"),
   'sub_text'=>t("Elige tu forma de pago")
));?>

<?php 
$this->renderPartial('/front/order-progress-bar',array(
   'step'=>isset($step)?$step:4,
   'show_bar'=>true,
   'guestcheckout'=>isset($guestcheckout)?$guestcheckout:false
));

$s=$_SESSION;
$continue=false;

$merchant_address='';		
if ($merchant_info=Yii::app()->functions->getMerchant($s['kr_merchant_id'])){	
	$merchant_address=$merchant_info['street']." ".$merchant_info['city']." ".$merchant_info['state'];
	$merchant_address.=" "	. $merchant_info['post_code'];
}

$client_info='';

if (isset($is_guest_checkout)){
	$continue=true;	
} else {	
	$client_info = Yii::app()->functions->getClientInfo(Yii::app()->functions->getClientId());
	if (isset($s['kr_search_address'])){	
		$temp=explode(",",$s['kr_search_address']);		
		if (is_array($temp) && count($temp)>=2){
			$street=isset($temp[0])?$temp[0]:'';
			$city=isset($temp[1])?$temp[1]:'';
			$state=isset($temp[2])?$temp[2]:'';
		}
		if ( isset($client_info['street'])){
			if ( empty($client_info['street']) ){
				$client_info['street']=$street;
			}
		}
		if ( isset($client_info['city'])){
			if ( empty($client_info['city']) ){
				$client_info['city']=$city;
			}
		}
		if ( isset($client_info['state'])){
			if ( empty($client_info['state']) ){
				$client_info['state']=$state;
			}
		}
	}	
	
	if (isset($s['kr_merchant_id']) && Yii::app()->functions->isClientLogin() && is_array($merchant_info) ){
		$continue=true;
	}
}
echo CHtml::hiddenField('mobile_country_code',Yii::app()->functions->getAdminCountrySet(true));

echo CHtml::hiddenField('admin_currency_set',getCurrencyCode());

echo CHtml::hiddenField('admin_currency_position',
Yii::app()->functions->getOptionAdmin("admin_currency_position"));

?>


<div class="sections section-grey2 section-payment-option">
   <div class="container">
           
     <?php if ( $continue==TRUE):?>
     <?php 
     $merchant_id=$s['kr_merchant_id'];
     echo CHtml::hiddenField('merchant_id',$merchant_id);
     ?>
     <div class="col-md-7 border">
          
     <div class="box-grey rounded">
     <form id="frm-delivery" class="frm-delivery" method="POST" onsubmit="return false;">
     <?php 
     //echo CHtml::hiddenField('action','placeOrder');
     echo CHtml::hiddenField('action','InitPlaceOrder');
     echo CHtml::hiddenField('country_code',$merchant_info['country_code']);
     echo CHtml::hiddenField('currentController','store');
     echo CHtml::hiddenField('delivery_type',$s['kr_delivery_options']['delivery_type']);
     echo CHtml::hiddenField('cart_tip_percentage','');
     echo CHtml::hiddenField('cart_tip_value','');
     echo CHtml::hiddenField('client_order_sms_code');
     echo CHtml::hiddenField('client_order_session');
     
     echo CHtml::hiddenField('cart_tip_cash_percentage','');
     
     if (isset($is_guest_checkout)){
     	echo CHtml::hiddenField('is_guest_checkout',2);
     }          
     
     $transaction_type=$s['kr_delivery_options']['delivery_type'];     
     ?>
     
     <?php if ( $transaction_type=="pickup" ||  $transaction_type=="dinein"):?> 
               
          <h3>
          <?php 
          if($transaction_type=="pickup"){
             echo t("Información de recogida");
          } else echo t("Dine in information");
          ?>
          </h3>
          <p class="uk-text-bold"><?php echo $merchant_address;?></p>
                   
          <?php if (!isset($is_guest_checkout)):?> 
          <?php //if ( getOptionA('mechant_sms_enabled')==""):?>
          <?php //if ( getOption($merchant_id,'order_verification')==2):?>
          <?php //$sms_balance=Yii::app()->functions->getMerchantSMSCredit($merchant_id);?>
          <?php //if ( $sms_balance>=1):?>
                    
            <div class="row top10">
                <div class="col-md-10">
              <?php echo CHtml::textField('contact_phone',
              isset($client_info['contact_phone'])?$client_info['contact_phone']:''
              ,array(
               'class'=>'mobile_inputs grey-fields',
               'placeholder'=>Yii::t("default","Número de telefono"),
               'data-validation'=>"required",
               'maxlength'=>15
              ))?>
             </div>             
            </div>  
          
		  <?php //endif;?>
          <?php //endif;?>
          <?php //endif;?>
          <?php endif;?>
          
          
          <?php if (isset($is_guest_checkout)):?> <!--PICKUP GUEST-->
          <?php 
           $this->renderPartial('/front/guest-checkou-form',array(
		     'merchant_id'=>$merchant_id,
		     'transaction_type'=>$transaction_type
		   ));
          ?>                     
          <?php endif;?>  <!--PICKUP GUEST-->
          
          
     <?php else :?> <!-- DELIVERY-->                          	       	      
          
		  <?php FunctionsV3::sectionHeader('Información de Envio')?>		  
		  <p>
	        <?php echo clearString(ucwords($merchant_info['restaurant_name']))?> <?php echo Yii::t("default","Restaurante")?> 
	        <?php echo "<span class='bold'>".Yii::t("default",ucwords($s['kr_delivery_options']['delivery_type'])) . "</span> ";
	        if ($s['kr_delivery_options']['delivery_asap']==1){
	        	$s['kr_delivery_options']['delivery_date']." ".Yii::t("default","ASAP");
	        } else {	          
	          echo '<span class="bold">'.Yii::app()->functions->translateDate(date("M d Y",strtotime($s['kr_delivery_options']['delivery_date']))).
	          " ".t("at"). " ". $s['kr_delivery_options']['delivery_time']."</span> ".t("to");
	        }
	        ?>
	       </p>	       
	       	      	     
	       <div class="top10">
	       
	        <?php FunctionsV3::sectionHeader('Dirección')?> 
	        	       
	        <?php if (isset($is_guest_checkout)):?>	         	        
	         <div class="row top10">
                <div class="col-md-10">
                 <?php echo CHtml::textField('first_name','',array(
	               'class'=>'grey-fields full-width',
	               'placeholder'=>Yii::t("default","Nombre"),
	               'data-validation'=>"required"
	              ))?>
	             </div> 
              </div>
              
              <div class="row top10">
                <div class="col-md-10">
                 <?php echo CHtml::textField('last_name','',array(
	               'class'=>'grey-fields full-width',
	               'placeholder'=>Yii::t("default","Apellido"),
	               'data-validation'=>"required"
	              ))?>
	             </div> 
              </div>
	        <?php endif;?> <!--$is_guest_checkout-->
	        
	        <?php if (!$search_by_location):?>
	        <?php if ( $website_enabled_map_address==2 ):?>	        
	        <div class="top10">
            <?php Widgets::AddressByMap()?>
            </div>
            <?php endif;?>
                        
            <?php $address_list=Yii::app()->functions->addressBook(Yii::app()->functions->getClientId());?>
            <?php if(is_array($address_list) && count($address_list)>=1):?>
            <div class="address_book_wrap">
            <div class="row top10">
                <div class="col-md-10">
               <?php                
               echo CHtml::dropDownList('address_book_id',$address_book['id'],
               (array)$address_list,array(
                  'class'=>"grey-fields full-width"
               ));
               ?>
               <a href="javascript:;" class="edit_address_book block top10">
                 <i class="ion-compose"></i> <?php echo t("Editar")?>
               </a>
               </div> 
              </div>   
            </div> <!--address_book_wrap-->
            <?php endif;?>
            <?php endif;?>
            
            <div class="address-block">
              <div class="row top10">
                <div class="col-md-10">
                 <?php echo CHtml::textField('street', isset($client_info['street'])?$client_info['street']:'' ,array(
	               'class'=>'grey-fields full-width',
	               'placeholder'=>Yii::t("default","Calle"),
	               'data-validation'=>"required"
	              ))?>
	             </div> 
              </div>
              
           <?php if (!$search_by_location):?>    
              <div class="row top10">
                <div class="col-md-10">
	             <?php echo CHtml::textField('city',
	             isset($client_info['city'])?$client_info['city']:''
	             ,array(
	               'class'=>'grey-fields full-width',
	               'placeholder'=>Yii::t("default","Ciudad"),
	               'data-validation'=>"required"
	              ))?>
	             </div> 
              </div>
                         
            <div class="row top10">
                <div class="col-md-10">
                 <?php echo CHtml::textField('state',
                 isset($client_info['state'])?$client_info['state']:''
                 ,array(
                 'class'=>'grey-fields full-width',
	               'placeholder'=>Yii::t("default","Estado"),
	               'data-validation'=>"required"
	              ))?>
	             </div> 
              </div>  
              
             <div class="row top10">
                <div class="col-md-10">
                  <?php echo CHtml::textField('zipcode',
                  isset($client_info['zipcode'])?$client_info['zipcode']:''
                  ,array(
	               'class'=>'grey-fields full-width',
	               'placeholder'=>Yii::t("default","Codigo postal")
	              ))?>
	             </div> 
              </div> 
           
           <?php else :?>      
            <!--ADDRESS BY LOCATION -->
            <?php
             echo CHtml::hiddenField('city');
             echo CHtml::hiddenField('state');             
             echo CHtml::hiddenField('area_name');             
             $country_id=getOptionA('location_default_country'); $state_ids='';
             $location_search_data=FunctionsV3::getSearchByLocationData();
             //dump($location_search_data);
             ?>
             <div class="row top10">
                <div class="col-md-10">
                 <?php
                 echo CHtml::dropDownList('state_id','',
                 (array)FunctionsV3::ListLocationState($country_id)
                 ,array(
                   'class'=>'grey-fields full-width',
                   'data-validation'=>"required"
                 ));
                 ?>
	             </div> 
              </div>  
              
             <div class="row top10">
                <div class="col-md-10">
                 <?php
                 echo CHtml::dropDownList('city_id','',
                 array(
                  ''=>t("Select City")
                 )
                 ,array(
                   'class'=>'grey-fields full-width',
                   'data-validation'=>"required"
                 ));
                 ?>
	             </div> 
              </div>   
              
             <div class="row top10">
                <div class="col-md-10">
                 <?php
                 echo CHtml::dropDownList('area_id','',
                 array(
                  ''=>t("Select Distric/Area/neighborhood")
                 )
                 ,array(
                   'class'=>'grey-fields full-width',
                   'data-validation'=>"required"
                 ));
                 ?>
	             </div> 
              </div>    
              
             <div class="row top10">
                <div class="col-md-10">
                  <?php echo CHtml::textField('zipcode',
                  isset($client_info['zipcode'])?$client_info['zipcode']:''
                  ,array(
	               'class'=>'grey-fields full-width',
	               'placeholder'=>Yii::t("default","Codigo postal")
	              ))?>
	             </div> 
              </div>  
              
           <?php endif;?>
              
              
             <div class="row top10">
                <div class="col-md-10">
                 <?php echo CHtml::textField('location_name',
                 isset($client_info['location_name'])?$client_info['location_name']:''
                 ,array(
	               'class'=>'grey-fields full-width',
	               'placeholder'=>Yii::t("default","Dirección o nombre de la compañia")	               
	              ))?>
	             </div> 
              </div> 
              
            </div> <!--address-block-->  
              
              <div class="row top10">
                <div class="col-md-10">
                 <?php echo CHtml::textField('contact_phone',
                 isset($client_info['contact_phone'])?$client_info['contact_phone']:''
                 ,array(
	               'class'=>'grey-fields mobile_inputs full-width',
	               'placeholder'=>Yii::t("default","Número de telefono"),
	               'data-validation'=>"required",
	               'maxlength'=>15
	              ))?>
	             </div> 
              </div>  
              
             <div class="row top10">
                <div class="col-md-10">
                  <?php echo CHtml::textField('delivery_instruction','',array(
	               'class'=>'grey-fields full-width',
	               'placeholder'=>Yii::t("default","instrucciones de envio")   
	              ))?>
	             </div> 
              </div> 
              
             <div class="row top10">
                <div class="col-md-10">
                  <?php
	              echo CHtml::checkBox('saved_address',false,array('class'=>"icheck",'value'=>2));
	              echo " ".t("Guardar en mi libreta de direcciones");
	              ?>
	             </div> 
              </div> 
              
             <?php if (isset($is_guest_checkout)):?>
             <div class="row top10">
                <div class="col-md-10">
                 <?php echo CHtml::textField('email_address','',array(
	               'class'=>'grey-fields full-width',
	               'placeholder'=>Yii::t("default","Correo electronico"),              
	              ))?>
	             </div> 
              </div>
                                          
             <?php endif;?> 
                                      
            
             <?php if (isset($is_guest_checkout)):?>
             <?php FunctionsV3::sectionHeader('Opcional')?>		  
             <div class="row top10">
                <div class="col-md-10">
                 <?php echo CHtml::passwordField('password','',array(
	               'class'=>'grey-fields full-width',
	               'placeholder'=>Yii::t("default","contraseña"),               
	              ))?>
	             </div> 
              </div>
             <?php endif;?>
             
	       </div> <!--top10--> 
	        	        	               
     <?php endif;?> <!-- ENDIF DELIVERY-->
     
     
     <?php if($transaction_type=="dinein"):?>
     <div class="top30"></div>
     <?php FunctionsV3::sectionHeader('Table Information')?>
     
     <div class="row top10">	
        <div class="col-md-10">
         <?php echo CHtml::textField('dinein_number_of_guest','',array(
           'class'=>'grey-fields numeric_only',
           'placeholder'=>Yii::t("default","Numero de invitado"),
           'data-validation'=>"required",
          ))?>
         </div> 
      </div>
      
      <div class="row top10">	
        <div class="col-md-10">
         <?php echo CHtml::textArea('dinein_special_instruction','',array(
           'class'=>'grey-fields full-width',
           'placeholder'=>Yii::t("default","instrucciones especiales"),              
          ))?>
         </div> 
      </div>
     
     <?php endif;?>
     
     <div class="top25">
     <?php 
	 $this->renderPartial('/front/payment-list',array(
	   'merchant_id'=>$merchant_id,
	   'payment_list'=>FunctionsV3::getMerchantPaymentListNew($merchant_id),
	   'transaction_type'=>$s['kr_delivery_options']['delivery_type']	   
	 ));
	 ?>
	 </div>
     
     <!--TIPS-->
     <?php if ( Yii::app()->functions->getOption("merchant_enabled_tip",$merchant_id)==2):?>
     <?php 
     $merchant_tip_default=Yii::app()->functions->getOption("merchant_tip_default",$merchant_id);
     if ( !empty($merchant_tip_default)){
    	echo CHtml::hiddenField('default_tip',$merchant_tip_default);
     }        
     $FunctionsK=new FunctionsK();
     $tips=$FunctionsK->tipsList();        
     ?>	   
	   <div class="section-label top25">
	    <a class="section-label-a">
	      <span class="bold">
	        <?php echo t("Tip Amount")?> (<span class="tip_percentage">0%</span>)
	      </span>
	      <b></b>
	    </a>     
	   </div>          
	   
	    <div class="uk-panel uk-panel-box">
	     <ul class="tip-wrapper">
	       <?php foreach ($tips as $tip_key=>$tip_val):?>           
	       <li>
	       <a class="tips" href="javascript:;" data-type="tip" data-tip="<?php echo $tip_key?>">
	       <?php echo $tip_val?>
	       </a>
	
	       </li>
	       <?php endforeach;?>           
	       <li><a class="tips tip_cash" href="javascript:;" data-type="cash" data-tip="0"><?php echo t("Tip cash")?></a></li>
	       <li><?php echo CHtml::textField('tip_value','',array(
	         'class'=>"numeric_only grey-fields",
	         'style'=>"width:70px;"
	       ));?>
	       </li>
	       <li>           
           <button type="button" class="apply_tip green-button"><?php echo t("Aplicar")?></button>
           </li> 
	     </ul>
	    </div>	       
     <?php endif;?>
     <!--END TIPS-->
     
     </form>    
     
     <!--CREDIT CART-->
     <?php 
     $this->renderPartial('/front/credit-card',array(
	   'merchant_id'=>$merchant_id	   
	 ));
	 ?>     
     <!--END CREDIT CART-->
     
     </div> <!--box rounded-->
     
     </div> <!--left content-->
     
     <div class="col-md-5 border sticky-div"><!-- RIGHT CONTENT STARTS HERE-->
     
       <div class="box-grey rounded  relative top-line-green">
       
       <i class="order-icon your-order-icon"></i>
       
	       <div class="order-list-wrap">   
	       
	         <p class="bold center"><?php echo t("Su pedido")?></p>
	         <div class="item-order-wrap"></div>
	       
	         <!--VOUCHER STARTS HERE-->
            <?php Widgets::applyVoucher($merchant_id);?>
            <!--VOUCHER STARTS HERE-->
            
            <?php 
            if (FunctionsV3::hasModuleAddon("pointsprogram")){
            	/*POINTS PROGRAM*/
                PointsProgram::redeemForm($merchant_id);
            }
            ?>
	         
	         <?php 	         
	         $minimum_order=Yii::app()->functions->getOption('merchant_minimum_order',$merchant_id);
	         $maximum_order=getOption($merchant_id,'merchant_maximum_order');	         
	         if ( $s['kr_delivery_options']['delivery_type']=="pickup"){
	         	
	          	  $minimum_order=Yii::app()->functions->getOption('merchant_minimum_order_pickup',$merchant_id);
	          	  $maximum_order=getOption($merchant_id,'merchant_maximum_order_pickup');	     
	          	      
	         } elseif ( $s['kr_delivery_options']['delivery_type']=="dinein"){
	         	  $minimum_order=getOption($merchant_id,'merchant_minimum_order_dinein');
	         	  $maximum_order=getOption($merchant_id,'merchant_maximum_order_dinein');
	         }	         	         
	         ?>
	         
	         <?php 
	         if (!empty($minimum_order)){
	         	echo CHtml::hiddenField('minimum_order',unPrettyPrice($minimum_order));
	            echo CHtml::hiddenField('minimum_order_pretty',baseCurrency().prettyFormat($minimum_order));
	            ?>
	            <p class="small center"><?php echo t("Subtotal must exceed")?> 
                 <?php echo baseCurrency().prettyFormat($minimum_order,$merchant_id)?>
                </p>      
	            <?php
	         }
	         if($maximum_order>0){
	         	echo CHtml::hiddenField('maximum_order',unPrettyPrice($maximum_order));
	         	echo CHtml::hiddenField('maximum_order_pretty',baseCurrency().prettyFormat($maximum_order));
	         }
	         ?>
	         
	         <?php //if ( getOptionA('captcha_order')==2 || getOptionA('captcha_customer_signup')==2):?>             
	         <?php if ( getOptionA('captcha_order')==2):?>             
             <div class="top10 capcha-wrapper">
             <?php //GoogleCaptcha::displayCaptcha()?>
             <div id="kapcha-1"></div>
             </div>
             <?php endif;?>          
             
              <!--SMS Order verification-->
	          <?php if ( getOptionA('mechant_sms_enabled')==""):?>
	          <?php if ( getOption($merchant_id,'order_verification')==2):?>
	          <?php $sms_balance=Yii::app()->functions->getMerchantSMSCredit($merchant_id);?>
	          <?php if ( $sms_balance>=1):?>
	          <?php $sms_order_session=Yii::app()->functions->generateCode(50);?>
	          <p class="top20 center">
	          <?php echo t("Este comercio ha requerido la verificación de SMS")?><br/>
	          <?php echo t("antes de que puedas hacer tu pedido")?>.<br/>
	          <?php echo t("Click")?> <a href="javascript:;" class="send-order-sms-code" data-session="<?php echo $sms_order_session;?>">
	             <?php echo t("aquí")?></a>
	          <?php echo t("recibe el codigo de la orden por sms")?>
	          </p>
	          <div class="top10 text-center">
	          <?php 
	          echo CHtml::textField('order_sms_code','',array(	            
	            'placeholder'=>t("Codigo SMS"),
	            'maxlength'=>8,
	            'class'=>'grey-fields text-center'
	          ));
	          ?>
	          </div>
	          <?php endif;?>
	          <?php endif;?>
	          <?php endif;?>
	          <!--END SMS Order verification-->
           
	          <div class="text-center top25">
	          <a href="javascript:;" class="place_order green-button medium inline block">
	          <?php echo t("Realizar pedido")?>
	          </a>
	          </div>
	         
	       </div> <!-- order-list-wrap-->       
	   </div> <!--box-grey-->    
     
     </div> <!--right content-->
     
     <?php else :?>      
       <div class="box-grey rounded">
      <p class="text-danger">
      <?php echo t("Algo salió mal, recarga la página  o puede que su sesión haya expirado")?></p>
      </div>
     <?php endif;?>
   
   </div>  <!--container-->
</div> <!--section-payment-option-->