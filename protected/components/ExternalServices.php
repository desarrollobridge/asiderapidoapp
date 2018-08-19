<?php
if (!isset($_SESSION)) { session_start(); }

class ExternalServices extends DbExt
{
	public $data;
	public $code=2;
	public $msg;
	public $details;
		
	public function output($debug=FALSE)
	{
	    $resp=array('code'=>$this->code,'msg'=>$this->msg,'details'=>$this->details);
	    if ($debug){
		    dump($resp);
	    }
	    return json_encode($resp);    	    
	}
	
	public function fillCategory()
	{		
		if (isset($this->data['cat_id'])){
			if ( $res=Yii::app()->functions->getItemByCategory($this->data['cat_id']) ){				
				$cat_info=Yii::app()->functions->getCategory($this->data['cat_id']);				
				$this->code=1;				
				$this->msg=array(
				  'currency'=>getCurrencyCode(),
				  'category_name'=>$cat_info['category_name']
				);
				$this->details=$res;
			} else $this->msg=Yii::t("default","Category is empty");
		} else $this->msg=Yii::t("default","Category not found");
	}
	
	public function viewFoodItem()
	{
		if (isset($this->data['item_id'])){
    		require_once 'food-item.php';
    	} else {
    		?>
    		<p class="uk-alert uk-alert-danger"><?php echo Yii::t("default","Sorry but we cannot find what you are looking for.")?></p>
    		<?php
    	}
    	die();
	}
	
     public function addToCart()
	 {	    		    		    	
    	$this->msg="Food Item added to cart";	    	
    	if (isset($this->data['item_id'])){
    		$item=$this->data;
    		unset($item['action']);
    		if (is_numeric($this->data['row'])){
    			$row=$this->data['row']-1;
    			$_SESSION['kr_item'][$row]=$item;
    			$this->msg="Cart updated";
    		} else $_SESSION['kr_item'][]=$item;
    			    		
    		$this->code=1;	    		
    	} else $this->msg=Yii::t("default","Item id is required");	    
	 }	
	 
	 public function getCart()
	 {
	 	$merchant_id=isset($this->data['mtid'])?$this->data['mtid']:'';
	 	if (!is_numeric($merchant_id)){
	 		echo Yii::t("default","Merchant ID is missing");
	 		return ;
	 	}	 	
	 	
	 	$is_merchant_open = Yii::app()->functions->isMerchantOpen($merchant_id);  
		$is_merchant_open1 = $is_merchant_open;
		$merchant_preorder= Yii::app()->functions->getOption("merchant_preorder",$merchant_id);  
		$disbabled_table_booking=Yii::app()->functions->getOption("merchant_table_booking",$merchant_id);  
		if ( $merchant_preorder==1){
		  	  $is_merchant_open=true;
		}
	    echo CHtml::hiddenField('is_merchant_open',$is_merchant_open==true?1:2);
		$close_msg=Yii::app()->functions->getOption("merchant_close_msg",$merchant_id);
		if (empty($close_msg)){
		  	$close_msg=Yii::t("default","This restaurant is closed now. Please check the opening times.");
		}
	 	?>
	 	<div class="order-list-wrap">
      <h5><?php echo Yii::t("default","Your Order")?></h5>
         
      <div class="item-order-wrap"></div> <!--END item-order-wrap-->           
      
      <!--VOUCHER STARTS HERE-->
      <?php //Widgets::applyVoucher($merchant_id);?>
      <!--VOUCHER STARTS HERE-->
      
      <?php $minimum_order=Yii::app()->functions->getOption('merchant_minimum_order',$merchant_id);?>
      <?php if (!empty($minimum_order)):?>
      <?php 
            echo CHtml::hiddenField('minimum_order',unPrettyPrice($minimum_order));
            //echo CHtml::hiddenField('minimum_order_pretty',baseCurrency().prettyFormat($minimum_order))
            echo CHtml::hiddenField('minimum_order_pretty',prettyFormat($minimum_order))
       ?>
      <p class="uk-text-muted"><?php echo Yii::t("default","Subtotal must exceed")?> 
         <?php echo baseCurrency().prettyFormat($minimum_order,$merchant_id)?>
      </p>      
      <?php endif;?>
      
      <?php $merchant_maximum_order=Yii::app()->functions->getOption("merchant_maximum_order",$merchant_id);?>
      <?php if (is_numeric($merchant_maximum_order)):?>
      <?php 
      echo CHtml::hiddenField('merchant_maximum_order',unPrettyPrice($merchant_maximum_order));
      echo CHtml::hiddenField('merchant_maximum_order_pretty',baseCurrency().prettyFormat($merchant_maximum_order));
      ?>
      <p class="uk-text-muted"><?php echo Yii::t("default","Maximum Order is")?> 
         <?php echo baseCurrency().prettyFormat($minimum_order,$merchant_id)?>
      </p>      
      <?php endif;?>
      
      <div class="delivery_options uk-form" style="margin-top:10px;">
       <h5><?php echo Yii::t("default","Delivery Options")?></h5>
       <?php echo CHtml::dropDownList('delivery_type',$now,(array)Yii::app()->functions->DeliveryOptions($merchant_id))?>
       <?php echo CHtml::textField('delivery_date',$now,array('class'=>"j_date"))?>
       <?php echo CHtml::textField('delivery_time',$now_time,
       array('class'=>"timepick",'placeholder'=>Yii::t("default","Hora de delivery")))?>
       <span class="uk-text-small uk-text-muted"><?php echo Yii::t("default","Delivery ASAP?")?></span>
       <?php echo CHtml::checkBox('delivery_asap',false,array('class'=>"icheck"))?>
      </div>      
            
      <?php if (yii::app()->functions->validateSellLimit($merchant_id) ):?>
         <?php if ( $is_merchant_open1):?>         
         <a href="javascript:;" class="uk-button checkout"><?php echo Yii::t("default","Chequear")?></a>
         <?php else :?>
            <?php if ($merchant_preorder==1):?>
            <a href="javascript:;" class="uk-button checkout"><?php echo Yii::t("default","Pre-Orden")?></a>
            <?php else :?>
            <p class="uk-alert uk-alert-warning"><?php echo Yii::t("default","Disculpe, Restaurante cerrado.")?></p>
            <p><?php echo prettyDate(date('c'),true);?></p>
            <?php endif;?>
         <?php endif;?>
      <?php else :?>
      <?php $msg=Yii::t("default","This merchant is not currently accepting orders.");?>
      <p class="uk-text-danger"><?php echo $msg;?></p>      
      <?php endif;?>      
      
       </div> <!--order-list-wrap-->
	 	<?php
	 	die();
	 }
	 
	 public function loadItemCart()
     {	    		    		    	
    	Yii::app()->functions->displayOrderHTML($this->data, isset($_SESSION['kr_item'])?$_SESSION['kr_item']:'' );
    	$this->code=Yii::app()->functions->code;
    	$this->msg=Yii::app()->functions->msg;
    	$this->details=Yii::app()->functions->details;
     }	
     
     public function setDeliveryOptions()
     {       
       $_SESSION['kr_delivery_options']['delivery_type']=$this->data['delivery_type'];
       $_SESSION['kr_delivery_options']['delivery_date']=$this->data['delivery_date'];
       $_SESSION['kr_delivery_options']['delivery_time']=$this->data['delivery_time'];
       $_SESSION['kr_delivery_options']['delivery_asap']=$this->data['delivery_asap']=="undefined"?"":1;
       
       $this->details=1;
       if ( Yii::app()->functions->isClientLogin()){
       	   $this->details=2;
       }        
       $this->code=1;$this->msg=Yii::t("default","OK");	       
     }     
     
     public function checkOut()
     {
     	
     ?>     
	<?php  $js_lang=Yii::app()->functions->jsLanguageAdmin(); ?>
	<?php $js_lang_validator=Yii::app()->functions->jsLanguageValidator();?>
	<script type="text/javascript">
	var js_lang=<?php echo json_encode($js_lang)?>;
	var jsLanguageValidator=<?php echo json_encode($js_lang_validator)?>;
	</script>
<script src="<?php echo Yii::app()->request->baseUrl;?>/assets/vendor/JQV/form-validator/jquery.form-validator.min.js" type="text/javascript"></script>
     	
<div class="page-right-sidebar checkout-page">
  <div class="main">
  <div class="inner">
     <div class="uk-grid">
       <div class="uk-width-1-2">  
          <form id="forms" class="forms uk-panel uk-panel-box uk-form" method="POST" onsubmit="return false;">
             <?php echo CHtml::hiddenField('action','clientLogin')?>
             <?php echo CHtml::hiddenField('currentController','store')?>
             <?php echo CHtml::hiddenField('redirect',Yii::app()->request->baseUrl."/store/paymentOption")?>
             <h3><?php echo Yii::t("default","Log in to your account")?></h3>
             <div class="uk-form-row">
               <?php echo CHtml::textField('username','',
                array('class'=>'uk-width-1-1','placeholder'=>Yii::t("default","Email"),
               'data-validation'=>"required"))?>
             </div>
             <div class="uk-form-row">
             <?php echo CHtml::passwordField('password','',
             array('class'=>'uk-width-1-1','placeholder'=>Yii::t("default","Password"),'data-validation'=>"required"))?>
             </div>
             <div class="uk-form-row">
             <input type="submit" value="<?php echo Yii::t("default","Login")?>" class="uk-button uk-width-1-1 uk-button-success">
             </div>
             
          </form>
       </div>
       <div class="uk-width-1-2">
          <form id="form-signup" class="form-signup uk-panel uk-panel-box uk-form" method="POST" onsubmit="return false;">
            <?php echo CHtml::hiddenField('action','clientRegistration')?>
            <?php echo CHtml::hiddenField('currentController','store')?>
            <?php echo CHtml::hiddenField('redirect',Yii::app()->request->baseUrl."/store/paymentOption")?>
             <h3><?php echo Yii::t("default","Sign up")?></h3>
             <div class="uk-form-row">
              <?php echo CHtml::textField('first_name','',array(
               'class'=>'uk-width-1-1',
               'placeholder'=>Yii::t("default","First Name"),
               'data-validation'=>"required"
              ))?>
             </div>
             <div class="uk-form-row">
              <?php echo CHtml::textField('last_name','',array(
               'class'=>'uk-width-1-1',
               'placeholder'=>Yii::t("default","Last Name"),
               'data-validation'=>"required"
              ))?>
             </div>
             
             <div class="uk-form-row">
		      <?php echo CHtml::textField('contact_phone','',array(
		       'class'=>'uk-width-1-1',
		       'placeholder'=>yii::t("default","Mobile"),
		       'data-validation'=>"required"
		      ))?>
		     </div>
             
             <div class="uk-form-row">
              <?php echo CHtml::textField('email_address','',array(
               'class'=>'uk-width-1-1',
               'placeholder'=>Yii::t("default","Email address"),
               'data-validation'=>"email"
              ))?>
             </div>
             <div class="uk-form-row">
              <?php echo CHtml::passwordField('password','',array(
               'class'=>'uk-width-1-1',
               'placeholder'=>Yii::t("default","Password"),
               'data-validation'=>"required"
              ))?>
             </div>
             <p class="uk-text-muted" style="text-align: left;">
             <?php echo Yii::t("default","By creating an account, you agree to receive sms from vendor.")?>
             </p>
             <div class="uk-form-row">
             <input type="submit" value="<?php echo Yii::t("default","Create Account")?>" class="uk-button uk-width-1-1 uk-button-primary">
             </div>
          </form>
       </div>
     </div>
     </div>
  </div> <!--main-->
</div> <!--menu-wrapper-->
<script type="text/javascript">
$.validate({ 	
	language : jsLanguageValidator,
    form : '#forms',    
    onError : function() {      
    },
    onSuccess : function() {     
      form_submit();
      return false;
    }  
});
</script>
     	<?php
     	die();
     }
     
     public function clientLogin()
     {
     	if (!isset($this->data['password_md5'])){
    		$this->data['password_md5']='';
    	}	    
    	if ( Yii::app()->functions->clientAutoLogin($this->data['username'],$this->data['password'],$this->data['password_md5']) ){
    		$this->code=1;
    		$this->msg=Yii::t("default","Login Okay");
    	} else $this->msg=Yii::t("default","Login Failed. Either username or password is incorrect");
     }
     
     public function PaymentOption()
     {
     	
     	$client_info='';
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
				
		$merchant_address='';		
		if ($merchant_info=Yii::app()->functions->getMerchant($this->data['mtid'])){
			$merchant_address=$merchant_info['street']." ".$merchant_info['city']." ".$merchant_info['state'];
			$merchant_address.=" "	. $merchant_info['post_code'];
		}
     	?>    
     	
        <div class="payment-option-wrap">

     	<form id="frm-delivery" class="frm-delivery uk-form" method="POST" onsubmit="return false;">
        
        <?php echo CHtml::hiddenField('action','placeOrder')?>
        <?php echo CHtml::hiddenField('country_code',$merchant_info['country_code'])?>
        <?php echo CHtml::hiddenField('currentController','store')?>
        <?php echo CHtml::hiddenField('mtid',$this->data['mtid'])?>
        
        <?php if ( $s['kr_delivery_options']['delivery_type']=="pickup"):?>
        
        <h3><?php echo Yii::t("default","Pickup information")?></h3>
        <p class="uk-text-bold"><?php echo $merchant_address;?></p>
        <?php else :?>
        
        <h3><?php echo Yii::t("default","Informacion de Envío")?></h3>
        
        <p>
        <?php echo ucwords($merchant_info['restaurant_name'])?> <?php echo Yii::t("default","Restaurant")?> 
        <?php echo "<span class='uk-text-bold'>".Yii::t("default",ucwords($s['kr_delivery_options']['delivery_type'])) . "</span> ";
        if ($s['kr_delivery_options']['delivery_asap']==1){
        	$s['kr_delivery_options']['delivery_date']." ".Yii::t("default","ASAP");
        } else {
          echo '<span class="uk-text-bold">'.date("M d Y",strtotime($s['kr_delivery_options']['delivery_date'])).
          " at ". " ". $s['kr_delivery_options']['delivery_time']."</span> to ";
        }
        ?>
        </p>
        
        <div class="uk-panel uk-panel-box">                                                              
            <div class="uk-form-row">
              <?php echo CHtml::textField('street',isIsset($client_info['street']),array(
               'class'=>'uk-width-1-1',
               'placeholder'=>Yii::t("default","Street"),
               'data-validation'=>"required"
              ))?>
             </div>
             <div class="uk-form-row">
              <?php echo CHtml::textField('city',isIsset($client_info['city']),array(
               'class'=>'uk-width-1-1',
               'placeholder'=>Yii::t("default","City"),
               'data-validation'=>"required"
              ))?>
             </div>
             <div class="uk-form-row">
              <?php echo CHtml::textField('state',isIsset($client_info['state']),array(
               'class'=>'uk-width-1-1',
               'placeholder'=>Yii::t("default","State"),
               'data-validation'=>"required"
              ))?>
             </div>
             <div class="uk-form-row">
              <?php echo CHtml::textField('zipcode',isIsset($client_info['zipcode']),array(
               'class'=>'uk-width-1-1',
               'placeholder'=>Yii::t("default","Zip code")
              ))?>
             </div>             
             
                          
             <div class="uk-form-row">
              <?php echo CHtml::textField('location_name',isIsset($client_info['location_name']),array(
               'class'=>'uk-width-1-1',
               'placeholder'=>Yii::t("default","Apartment suite, unit number, or company name")  
              ))?>
             </div>             
             <div class="uk-form-row">
              <?php echo CHtml::textField('contact_phone',isIsset($client_info['contact_phone']),array(
               'class'=>'uk-width-1-1',
               'placeholder'=>Yii::t("default","Mobile Number"),
               'data-validation'=>"required"  
              ))?>
             </div>             
             <div class="uk-form-row">
              <?php echo CHtml::textField('delivery_instruction','',array(
               'class'=>'uk-width-1-1',
               'placeholder'=>Yii::t("default","Delivery instructions")   
              ))?>
             </div>                                                                    
        
        </div> <!--uk-panel--> 
        <?php endif;?>
        
        <h3><?php echo Yii::t("default","Información de pago")?></h3>
        
        <?php 
        $enabled_paypal=Yii::app()->functions->getOption('enabled_paypal',$merchant_id);
        $enabled_stripe=Yii::app()->functions->getOption('stripe_enabled',$merchant_id);        
        $merchant_mercado_enabled=Yii::app()->functions->getOption('merchant_mercado_enabled',$merchant_id); 
        $merchant_disabled_cod=Yii::app()->functions->getOption('merchant_disabled_cod',$merchant_id); 
        $merchant_disabled_ccr=Yii::app()->functions->getOption('merchant_disabled_ccr',$merchant_id); 
        
        $merchant_payline_enabled=Yii::app()->functions->getOption('merchant_payline_enabled',$merchant_id); 
        $merchant_sisow_enabled=Yii::app()->functions->getOption('merchant_sisow_enabled',$merchant_id); 
        
        $merchant_payu_enabled=Yii::app()->functions->getOption('merchant_payu_enabled',$merchant_id);
        $enabled_stripe='';
        ?>
         
         <div class="uk-panel uk-panel-box">
         
         <?php if ($merchant_disabled_cod!="yes"):?>
         <div class="uk-form-row">
         <?php echo CHtml::radioButton('payment_opt',false,
         array('class'=>"icheck payment_option",'value'=>'cod'))?> <?php echo Yii::t("default","Cash On delivery")?>
         </div>
         <?php endif;?>
                
         
         <?php if ( $enabled_stripe=="yes"):?>
         <div class="uk-form-row">
         <?php echo CHtml::radioButton('payment_opt',false,
         array('class'=>"icheck payment_option",'value'=>'stp'))?> <?php echo Yii::t("default","Stripe")?>
         </div>
         <?php endif;?>
         
        
         
         </div> <!--uk-panel-->         
         
         <div class="spacer2"></div>
          <a href="javascript:;" class="uk-button uk-button-success place_order"><?php echo Yii::t("default","Place Order")?></a>
         
        </form>     	        
        </div>
     	<?php
     	die();
     }
     
     public function placeOrder()
     {
     	$this->data['merchant_id']=$this->data['mtid'];
	    	
    	$default_order_status=Yii::app()->functions->getOption("default_order_status",$_SESSION['kr_merchant_id']);	    	
    	   	
    	$order_item=$_SESSION['kr_item'];
    	if (is_array($order_item) && count($order_item)>=1){
    		Yii::app()->functions->displayOrderHTML($this->data,$_SESSION['kr_item']);
    		if ( Yii::app()->functions->code==1){
    			//dump("<h2>RESP</h2>");
    			$raw=Yii::app()->functions->details['raw'];	    				    			
    			if (is_array($raw) && count($raw)>=1){	    				
    				$params=array(
    				  'merchant_id'=>$this->data['merchant_id'],
    				  'client_id'=>Yii::app()->functions->getClientId(),
    				  'json_details'=>json_encode($order_item),
    				  'trans_type'=>isset($_SESSION['kr_delivery_options']['delivery_type'])?$_SESSION['kr_delivery_options']['delivery_type']:'',
    				  'payment_type'=>isset($this->data['payment_opt'])?$this->data['payment_opt']:'',
    				  'sub_total'=>isset($raw['total']['subtotal'])?$raw['total']['subtotal']:'',
    				  'tax'=>isset($raw['total']['tax'])?$raw['total']['tax']:'',
    				  'taxable_total'=>isset($raw['total']['taxable_total'])?$raw['total']['taxable_total']:'',
    				  'total_w_tax'=>isset($raw['total']['total'])?$raw['total']['total']:'',
    				  'delivery_charge'=>isset($raw['total']['delivery_charges'])?$raw['total']['delivery_charges']:'',
    				  'delivery_date'=>isset($_SESSION['kr_delivery_options']['delivery_date'])?$_SESSION['kr_delivery_options']['delivery_date']:'',
    				  'delivery_time'=>isset($_SESSION['kr_delivery_options']['delivery_time'])?$_SESSION['kr_delivery_options']['delivery_time']:'',
    				  'delivery_asap'=>isset($_SESSION['kr_delivery_options']['delivery_asap'])?$_SESSION['kr_delivery_options']['delivery_asap']:'',
    				  'date_created'=>FunctionsV3::dateNow(),
    				  'ip_address'=>$_SERVER['REMOTE_ADDR'],
    				  'delivery_instruction'=>isset($this->data['delivery_instruction'])?$this->data['delivery_instruction']:'',
    				  'cc_id'=>isset($this->data['cc_id'])?$this->data['cc_id']:''
    				);	    			
    				if (!empty($default_order_status)){
    					$params['status']=$default_order_status;
    				}	
    				
					/*VOUCHER*/
					$has_voucher=false;
                    if (isset($_SESSION['voucher_code'])){		         	
			         	if (is_array($_SESSION['voucher_code'])){				         		
		         			$params['voucher_amount']=$_SESSION['voucher_code']['amount'];
		         			$params['voucher_code']=$_SESSION['voucher_code']['voucher_name'];
		         			$params['voucher_type']=$_SESSION['voucher_code']['voucher_type'];
		         			$has_voucher=true;
			         	}		         
		            }    					
		            				    					    				
    				if ( $this->insertData("{{order}}",$params)){
	    				$order_id=Yii::app()->db->getLastInsertID();			
	    						    				    				
	    			   /*VOUCHER*/
                        if ($has_voucher==TRUE){
                            Yii::app()->functions->updateVoucher($_SESSION['voucher_code']['voucher_code'],
			         			Yii::app()->functions->getClientId(),$order_id);				         
			            }		
			            	
	    				foreach ($raw['item'] as $val) {		    					
	    					$params_order_details=array(
	    					  'order_id'=>$order_id,
	    					  'client_id'=>Yii::app()->functions->getClientId(),
	    					  'item_id'=>isset($val['item_id'])?$val['item_id']:'',
	    					  'item_name'=>isset($val['item_name'])?$val['item_name']:'',
	    					  'order_notes'=>isset($val['order_notes'])?$val['order_notes']:'',
	    					  'normal_price'=>isset($val['normal_price'])?$val['normal_price']:'',
	    					  'discounted_price'=>isset($val['discounted_price'])?$val['discounted_price']:'',
	    					  'size'=>isset($val['size_words'])?$val['size_words']:'',
	    					  'qty'=>isset($val['qty'])?$val['qty']:'',		    					  
	    					  'addon'=>isset($val['sub_item'])?json_encode($val['sub_item']):'',
	    					  'cooking_ref'=>isset($val['cooking_ref'])?$val['cooking_ref']:''
	    					);		    					
	    					$this->insertData("{{order_details}}",$params_order_details);
	    				}
	    				$this->code=1;		    				
	    				
	    				switch ($this->data['payment_opt'])
	    				{
	    					case "cod":
	    					case "ccr":			    					
	    					    $this->msg=Yii::t("default","Your order has been placed.");
	    						break;
	    					default:	
	    					    $this->msg=Yii::t("default","Please wait while we redirect...");
	    					    break;
	    				}
	    					    				
	    				$this->details=array(
	    				  'order_id'=>$order_id,
	    				  'payment_type'=>$this->data['payment_opt']
	    				);
	    				
	    				Yii::app()->functions->updateClient($this->data);
	    				
    				} else $this->msg=Yii::t("default","ERROR: Cannot insert records.");
    			} else $this->msg=Yii::t("default","ERROR: Something went wrong");	    		
    		} else $this->msg=Yii::app()->functions->msg;
    	} else $this->msg=Yii::t("default","Sorry but your order is empty");	    
     }
     
     public function receipt()
     {
     	$data='';
		$ok=false;
		if ( $data=Yii::app()->functions->getOrder2($_GET['id'])){
			$merchant_id=$data['merchant_id'];
			$json_details=!empty($data['json_details'])?json_decode($data['json_details'],true):false;
			if ( $json_details !=false){
				Yii::app()->functions->displayOrderHTML(array('merchant_id'=>$data['merchant_id']),$json_details,true);
				if ( Yii::app()->functions->code==1){
					$ok=true;
				}
			}	
		}
		unset($_SESSION['kr_item']);
		unset($_SESSION['kr_merchant_id']);
		unset($_SESSION['voucher_code']);
		unset($_SESSION['less_voucher']);
		
		$print='';
     	?>
<div class="page">
	<div class="main"> 
	<div class="inner">
     <?php if ($ok==TRUE):?>
         <div class="receipt-main-wrap">
         <h3><?php echo Yii::t("default","Thank You")?></h3>
         <p><?php echo Yii::t("default","Your order has been placed.")?></p>
         
	     <div class="receipt-wrap order-list-wrap">
	       <h4><?php echo Yii::t("default","Order Details")?></h4>
	       <div class="input-block">
	         <div class="label"><?php echo Yii::t("default","Name")?> :</div>
	         <div class="value"><?php echo $data['full_name']?></div>
	         <div class="clear"></div>
	       </div>
	       
	       <?php 	       
	       $print[]=array(
	         'label'=>Yii::t("default","Name"),
	         'value'=>$data['full_name']
	       );
	       ?>
	       
	       <div class="input-block">
	         <div class="label"><?php echo Yii::t("default","Merchant Name")?> :</div>
	         <div class="value"><?php echo $data['merchant_name']?></div>
	         <div class="clear"></div>
	       </div>
	       
	       <?php 	       
	       $print[]=array(
	         'label'=>Yii::t("default","Merchant Name"),
	         'value'=>$data['merchant_name']
	       );
	       ?>
	       
	       <div class="input-block">
	         <div class="label"><?php echo Yii::t("default","TRN Type")?> :</div>
	         <div class="value"><?php echo Yii::t("default",$data['trans_type'])?></div>
	         <div class="clear"></div>
	       </div>
	       
	       <?php 	       
	       $print[]=array(
	         'label'=>Yii::t("default","TRN Type"),
	         'value'=>$data['trans_type']
	       );
	       ?>
	       
	       <div class="input-block">
	         <div class="label"><?php echo Yii::t("default","Payment Type")?> :</div>
	         <div class="value"><?php echo strtoupper(Yii::t("default",$data['payment_type']))?></div>
	         <div class="clear"></div>
	       </div>
	       <?php 	       
	       $print[]=array(
	         'label'=>Yii::t("default","Payment Type"),
	         'value'=>strtoupper($data['payment_type'])
	       );
	       ?>
	       	       
	       <?php if ( $data['payment_type'] =="pyp"):?>
	       <?php 
	       $paypal_info=Yii::app()->functions->getPaypalOrderPayment($data['order_id']);	       
	       ?>
	       <div class="input-block">
	         <div class="label"><?php echo Yii::t("default","Paypal Transaction ID")?> :</div>
	         <div class="value"><?php echo isset($paypal_info['TRANSACTIONID'])?$paypal_info['TRANSACTIONID']:'';?></div>
	         <div class="clear"></div>
	       </div>
	       <?php 	       
	       $print[]=array(
	         'label'=>Yii::t("default","Paypal Transaction ID"),
	         'value'=>isset($paypal_info['TRANSACTIONID'])?$paypal_info['TRANSACTIONID']:''
	       );
	       ?>
	       <?php endif;?>
	       
	       <div class="input-block">
	         <div class="label"><?php echo Yii::t("default","Reference #")?> :</div>
	         <div class="value"><?php echo Yii::app()->functions->formatOrderNumber($data['order_id'])?></div>
	         <div class="clear"></div>
	       </div>
	       <?php 	       
	       $print[]=array(
	         'label'=>Yii::t("default","Reference #"),
	         'value'=>Yii::app()->functions->formatOrderNumber($data['order_id'])
	       );
	       ?>
	       
	       <?php if ( !empty($data['payment_reference'])):?>
	       <div class="input-block">
	         <div class="label"><?php echo Yii::t("default","Payment Ref")?> :</div>
	         <div class="value"><?php echo $data['payment_reference']?></div>
	         <div class="clear"></div>
	       </div>
	       <?php
	       $print[]=array(
	         'label'=>Yii::t("default","Payment Ref"),
	         'value'=>Yii::app()->functions->formatOrderNumber($data['order_id'])
	       );
	       ?>
	       <?php endif;?>
	       	       
	       <?php if ($data['payment_type']=="ccr"):?>
	       <div class="input-block">
	         <div class="label"><?php echo Yii::t("default","Card #")?> :</div>
	         <div class="value"><?php echo $card=Yii::app()->functions->maskCardnumber($data['credit_card_number'])?></div>
	         <div class="clear"></div>
	       </div>
	       <?php 	       
	       $print[]=array(
	         'label'=>Yii::t("default","Card #"),
	         'value'=>$card
	       );
	       ?>
	       <?php endif;?>
	       
	       <div class="input-block">
	         <div class="label"><?php echo Yii::t("default","TRN Date")?> :</div>
	         <div class="value"><?php $trn_date=date('M d,Y G:i:s',strtotime($data['date_created']));
	         echo Yii::app()->functions->translateDate($trn_date);
	         ?></div>
	         <div class="clear"></div>
	       </div>
	       <?php 	       
	       $print[]=array(
	         'label'=>Yii::t("default","TRN Date"),
	         'value'=>$trn_date
	       );
	       ?>
	       
	       <?php if ($data['trans_type']=="delivery"):?>
		       	       
		       <?php if (isset($_SESSION['kr_delivery_options']['delivery_date'])):?>
		       <div class="input-block">
		         <div class="label"><?php echo Yii::t("default","Delivery Date")?> :</div>
		         <div class="value"><?php $deliver_date=prettyDate($_SESSION['kr_delivery_options']['delivery_date']);
		         echo Yii::app()->functions->translateDate($deliver_date);
		         ?></div>
		         <div class="clear"></div>
		       </div>
		       <?php 	       
		       $print[]=array(
		         'label'=>Yii::t("default","Delivery Date"),
		         'value'=>$deliver_date
		       );
		       ?>
		       <?php endif;?>
		       
		       <?php if (isset($_SESSION['kr_delivery_options']['delivery_time'])):?>
		       <?php if ( !empty($_SESSION['kr_delivery_options']['delivery_time'])):?>
		       <div class="input-block">
		         <div class="label"><?php echo Yii::t("default","Delivery Time")?> :</div>
		         <div class="value"><?php echo $_SESSION['kr_delivery_options']['delivery_time']?></div>
		         <div class="clear"></div>
		       </div>
		       <?php endif;?>
		       <?php endif;?>
		       
		       <?php if (isset($_SESSION['kr_delivery_options']['delivery_asap'])):?>
		       <?php if ( !empty($_SESSION['kr_delivery_options']['delivery_asap'])):?>
		       <div class="input-block">
		         <div class="label"><?php echo Yii::t("default","Deliver ASAP")?> :</div>
		         <div class="value"><?php echo $delivery_asap=$_SESSION['kr_delivery_options']['delivery_asap']==1?"Yes":'';?></div>
		         <div class="clear"></div>
		       </div>
			   <?php 	       
				$print[]=array(
				 'label'=>Yii::t("default","Deliver ASAP"),
				 'value'=>$delivery_asap
				);
				?>
		       <?php endif;?>
		       <?php endif;?>
		       
		       <div class="input-block">
		         <div class="label"><?php echo Yii::t("default","Deliver to")?> :</div>
		         <div class="value"><?php echo $data['full_address']?></div>
		         <div class="clear"></div>
		       </div>
				<?php 	       
				$print[]=array(
				  'label'=>Yii::t("default","Deliver to"),
				  'value'=>$data['full_address']
				);
				?>
		       
		       <div class="input-block">
		         <div class="label"><?php echo Yii::t("default","Delivery Instruction")?> :</div>
		         <div class="value"><?php echo $data['delivery_instruction']?></div>
		         <div class="clear"></div>
		       </div>
		       <?php 	       
				$print[]=array(
				  'label'=>Yii::t("default","Delivery Instruction"),
				  'value'=>$data['delivery_instruction']
				);
				?>
		       
		       <div class="input-block">
		         <div class="label"><?php echo Yii::t("default","Location Name")?> :</div>
		         <div class="value"><?php echo $data['location_name']?></div>
		         <div class="clear"></div>
		       </div>
		       <?php 	       
				$print[]=array(
				  'label'=>Yii::t("default","Location Name"),
				  'value'=>$data['location_name']
				);
				?>
				
		   <?php else :?>   
		   
		      <?php if (isset($_SESSION['kr_delivery_options']['delivery_date'])):?>
		       <div class="input-block">
		         <div class="label"><?php echo Yii::t("default","Pickup Date")?> :</div>
		         <div class="value"><?php echo $_SESSION['kr_delivery_options']['delivery_date']?></div>
		         <div class="clear"></div>
		       </div>
		       <?php 	       
				$print[]=array(
				  'label'=>Yii::t("default","Pickup Date"),
				  'value'=>$_SESSION['kr_delivery_options']['delivery_date']
				);
				?>
		       <?php endif;?>
		       
		       <?php if (isset($_SESSION['kr_delivery_options']['delivery_time'])):?>
		       <?php if ( !empty($_SESSION['kr_delivery_options']['delivery_time'])):?>
		       <div class="input-block">
		         <div class="label"><?php echo Yii::t("default","Pickup Time")?> :</div>
		         <div class="value"><?php echo $_SESSION['kr_delivery_options']['delivery_time']?></div>
		         <div class="clear"></div>
		       </div>
		       <?php 	       
				$print[]=array(
				 'label'=>Yii::t("default","Pickup Time"),
				 'value'=>$_SESSION['kr_delivery_options']['delivery_time']
				);
				?>
		       <?php endif;?>
		       <?php endif;?>
	       
	       <?php endif;?>
	       
	       <div class="spacer-small"></div>
	       
	       <?php echo $item_details=Yii::app()->functions->details['html'];?>
	     </div> <!--receipt-wrap-->
	     
	     <div class="print_wrap">
          <a class="print_element left" href="javascript:;"><i class="fa fa-print"></i> <?php echo Yii::t("default","Click here to print")?></a>       
          <div class="clear"></div>
        </div>	    
        
        </div>
     <?php else :?>
     <p class="uk-alert uk-alert-warning"><?php echo Yii::t("default","Sorry but we cannot find what you are looking for.")?></p>
     <?php endif;?>
     </div>
    </div> <!--main-->
</div> <!--page-->

<?php 
$receipt=EmailTPL::salesReceipt($print,Yii::app()->functions->details['raw']);
$tpl=Yii::app()->functions->getOption("receipt_content",$merchant_id);
if (empty($tpl)){
	$tpl=EmailTPL::receiptTPL();
}
$tpl=Yii::app()->functions->smarty('receipt',$receipt,$tpl);
$tpl=Yii::app()->functions->smarty('customer-name',$data['full_name'],$tpl);
$tpl=Yii::app()->functions->smarty('receipt-number',Yii::app()->functions->formatOrderNumber($data['order_id']),$tpl);


$receipt_sender=Yii::app()->functions->getOption("receipt_sender",$merchant_id);
$receipt_subject=Yii::app()->functions->getOption("receipt_subject",$merchant_id);
if (empty($receipt_subject)){	
	$receipt_subject="Your Karenderia order is confirmed";
}
if (empty($receipt_sender)){
	$receipt_sender='no-reply@'.$_SERVER['HTTP_HOST'];
}
$to=isset($data['email_address'])?$data['email_address']:'';

if (!in_array($data['order_id'],(array)$_SESSION['kr_receipt'])){	
    sendEmail($to,$receipt_sender,$receipt_subject,$tpl);    
    
    /*send email to merchant address*/
    $merchant_notify_email=Yii::app()->functions->getOption("merchant_notify_email",$merchant_id);    
    $enabled_alert_notification=Yii::app()->functions->getOption("enabled_alert_notification",$merchant_id);    
    /*dump($merchant_notify_email);
    dump($enabled_alert_notification);   */
    if ( $enabled_alert_notification==""){    	
    	sendEmail($merchant_notify_email,$receipt_sender,Yii::t("default","New Order From ").$data['full_name'],$tpl);
    }   
    
    // send SMS
    Yii::app()->functions->SMSnotificationMerchant($merchant_id,$data);  
}
$_SESSION['kr_receipt']=array($data['order_id']);    
     	die();
     }
     
     public function deleteItem()
     {
     	if ( isset($_SESSION['kr_item'][$this->data['row']])){
      	   unset($_SESSION['kr_item'][$this->data['row']]);
    	}
    	$this->code=1;
    	$this->msg="";
     }
     
} /*END CLASS*/