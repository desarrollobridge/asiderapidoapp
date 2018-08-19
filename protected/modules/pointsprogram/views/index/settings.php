
<?php echo CHtml::beginForm('','post',array(
'class'=>"form-horizontal"
)); ?> 

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo t("Enabled Points System")?></label>
  <div class="col-sm-8">
  <?php 
  echo CHtml::checkBox('points_enabled',
  getOptionA('points_enabled')==1?true:false
  ,array(
    'class'=>"",
    'value'=>1
  ));
  ?>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo t("Disabled Points in Merchant Settings")?></label>
  <div class="col-sm-8">
  <?php 
  echo CHtml::checkBox('points_disabled_merchant_settings',
  getOptionA('points_disabled_merchant_settings')==1?true:false
  ,array(
    'class'=>"",
    'value'=>1
  ));
  ?>
  </div>
</div>


<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Earn points by order status")?></label>
  <div class="col-sm-8">
    <?php 
    unset($status_list[0]);    
    $points_status = getOptionA('pts_earn_points_status');
    if(!empty($points_status)){
    	$points_status = json_decode($points_status);
    }
    echo CHtml::dropDownList('pts_earn_points_status[]',
    (array)$points_status,
    (array)$status_list,array(
    'class'=>'chosen',
    'multiple'=>true,
    'style'=>"width:300px;"
  ));
  ?>
  </div>
</div>


<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Points label Redeem placeholder")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textField('pts_label_input',getOptionA('pts_label_input'),array(
      'class'=>'form-control'      
    ));
    ?>   
  </div>
</div>

<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Points label Earn")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textField('pts_label_earn',getOptionA('pts_label_earn'),array(
      'class'=>'form-control'      
    ));
    ?>   
    <p><?php echo t("Available Tags")?>: <span class="bg-danger">{points}</span></p>
  </div>
</div>

<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Points label Template")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textField('pts_label',getOptionA('pts_label'),array(
      'class'=>'form-control'      
    ));
    ?>
    <p><?php echo t("Available Tags")?>: <span class="bg-danger">{points}</span></p>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo t("Maximum Earning Points for customer")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textField('pts_maximum_points',getOptionA('pts_maximum_points'),array(
      'class'=>'numeric_only amount_value form-control'      
    ));
    ?>    
  </div>
</div>

<?php 
$pts_payment_option=getOptionA('pts_payment_option');
if (!empty($pts_payment_option)){
	$pts_payment_option=json_decode($pts_payment_option,true);
}
?>

<!--<div class="form-group" id="chosen-field">
  <label class="col-sm-3 control-label"><?php echo t("Points earn apply only on the following payment option")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::dropDownList('pts_payment_option[]',$pts_payment_option,PointsProgram::paymentOptionsList(),array(
      'class'=>"chosen form-control",
      'multiple'=>true
    ));
    ?>    
  </div>
</div>-->

<hr/>

<h4><?php echo t("Earning Points Settings")?></h4>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo t("Based points earnings")?></label>
  <div class="col-sm-8">
   <?php echo CHtml::dropDownList('points_based_earn',getOptionA('points_based_earn'),array(
     1=>t("Food item (default)"),
     2=>t("Order Sub total"),
   ),array(
     'class'=>"form-control",
     'style'=>"width:300px;"
   ))?>
  </div>  
</div>


<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Earning Point")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textField('pts_earning_points',getOptionA('pts_earning_points'),array(
      'class'=>'numeric_only amount_value form-control'      
    ));
    ?>
  </div>
</div>

<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Earning Point Value in")." ".getCurrencyCode()?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textField('pts_earning_points_value',getOptionA('pts_earning_points_value'),array(
      'class'=>'numeric_only amount_value form-control'      
    ));
    ?>
  </div>
</div>

<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Earn points above order")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textField('pts_earn_above_amount',getOptionA('pts_earn_above_amount'),array(
      'class'=>'numeric_only amount_value form-control'      
    ));
    ?>
  </div>
</div>

<hr/>

<h4><?php echo t("Redeeming Points Settings")?></h4>


<div class="form-group">
  <label class="col-sm-3 control-labe"><?php echo t("Customer can redeeming with the following conditions")?></label>
  <div class="col-sm-8">
  <?php 
  echo CHtml::dropDownList('pts_redeem_condition',
  getOptionA('pts_redeem_condition')
  ,array(
    2=>t("Can redeem with all points they earn in all merchant"),
    1=>t("Can redeem only to merchant they earn points"),    
    3=>t("Can redeem only to merchant they earn points + global points"),
  ),array(
    'class'=>"form-control",
    'style'=>"width:300px;"
  ));
  ?>
  </div>  
</div>  

<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Redeeming Point")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textField('pts_redeeming_point',getOptionA('pts_redeeming_point'),array(
      'class'=>'numeric_only amount_value form-control'      
    ));
    ?>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo t("Redeeming Point Value in")." ".getCurrencyCode()?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textField('pts_redeeming_point_value',getOptionA('pts_redeeming_point_value'),array(
      'class'=>'numeric_only amount_value form-control'      
    ));
    ?>
  </div>
</div>


<!--<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo t("Redeem points above order")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textField('pts_redeem_above_amount',getOptionA('pts_redeem_above_amount'),array(
      'class'=>'numeric_only amount_value form-control'      
    ));
    ?>
  </div>
</div>-->
    
    <div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Redeem points above order")?></label>
  <div class="col-sm-8">
  <?php 
  echo CHtml::textField('points_apply_order_amt',getOptionA('points_apply_order_amt'),array(
      'class'=>'numeric_only amount_value form-control'      
    ));
  ?>
  </div>
</div>

<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Minimum points can be used")?></label>
  <div class="col-sm-8">
  <?php 
  echo CHtml::textField('points_minimum',getOptionA('points_minimum'),array(
      'class'=>'numeric_only amount_value form-control'      
    ));
  ?>
  </div>
</div>

<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Maximum points can be used")?></label>
  <div class="col-sm-8">
  <?php 
  echo CHtml::textField('points_max',getOptionA('points_max'),array(
      'class'=>'numeric_only amount_value form-control'      
    ));
  ?>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo t("Disabled Redeeming")?></label>
  <div class="col-sm-8">
  <?php 
  echo CHtml::checkBox('pts_disabled_redeem',
  getOptionA('pts_disabled_redeem')==1?true:false
  ,array(
    'class'=>"",
    'value'=>1
  ));
  ?>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo t("Don't show redeem if points balance is zero")?></label>
  <div class="col-sm-8">
  <?php 
  echo CHtml::checkBox('pts_redeem_balance_zero',
  getOptionA('pts_redeem_balance_zero')==1?true:false
  ,array(
    'class'=>"",
    'value'=>1
  ));
  ?>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo t("Enabled customer can apply voucher even they have point discount")?></label>
  <div class="col-sm-8">
  <?php 
  echo CHtml::checkBox('pts_enabled_add_voucher',
  getOptionA('pts_enabled_add_voucher')==1?true:false
  ,array(
    'class'=>"",
    'value'=>1
  ));
  ?>
  </div>
</div>


<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo t("Customer can have offers+points discount")?></label>
  <div class="col-sm-8">
  <?php 
  echo CHtml::checkBox('pts_enabled_offers_discount',
  getOptionA('pts_enabled_offers_discount')==1?true:false
  ,array(
    'class'=>"",
    'value'=>1
  ));
  ?>
  </div>
</div>

<hr/>

<h4><?php echo t("Global Points")?></h4>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo t("Reward Points for Account Signup")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textField('pts_account_signup',getOptionA('pts_account_signup'),array(
      'class'=>'numeric_only amount_value form-control'      
    ));
    ?>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo t("Reward Points for restaurant review")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textField('pts_merchant_reivew',getOptionA('pts_merchant_reivew'),array(
      'class'=>'numeric_only amount_value form-control'      
    ));
    ?>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo t("Reward Points for first order")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textField('pts_first_order',getOptionA('pts_first_order'),array(
      'class'=>'numeric_only amount_value form-control'      
    ));
    ?>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo t("Reward Points for Booking Table")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textField('pts_book_table',getOptionA('pts_book_table'),array(
      'class'=>'numeric_only amount_value form-control'      
    ));
    ?>
  </div>
</div>

<hr/>

<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Points Expiry")?></label>
  <div class="col-sm-8">
  <?php 
  echo CHtml::dropDownList('pts_expiry',getOptionA('pts_expiry'),PointsProgram::listPointExpiry(),array(
   'class'=>"form-control",
   'style'=>"width:300px;"
  ))
  ?>
  </div>
</div>


<div class="clear"></div>

 <div class="form-group">
    <div class="col-sm-offset-3 col-sm-9">
  <?php
echo CHtml::ajaxSubmitButton(
	t('Save Settings'),
	array('ajax/savesettings'),
	array(
		'type'=>'POST',
		'dataType'=>'json',
		'beforeSend'=>'js:function(){
		                 busy(true); 	
		                 $("#save-settings").val("Processing");
		                 $("#save-settings").css({ "pointer-events" : "none" });	                 	                 
		              }
		',
		'complete'=>'js:function(){
		                 busy(false); 		                 
		                 $("#save-settings").val("Save Settings");
		                 $("#save-settings").css({ "pointer-events" : "auto" });	                 	                 
		              }',
		'success'=>'js:function(data){	
		               if(data.code==1){		               
		                 nAlert(data.msg,"success");
		               } else {
		                  nAlert(data.msg,"warning");
		               }
		            }
		'
	),array(
	  'class'=>'btn btn-primary',
	  'id'=>'save-settings'
	)
);
?>
    </div>
  </div>


<?php echo CHtml::endForm(); ?>