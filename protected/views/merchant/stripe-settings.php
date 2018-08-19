<?php
$merchant_id=Yii::app()->functions->getMerchantID();
$enabled=Yii::app()->functions->getOption('stripe_enabled',$merchant_id);
$paymode=Yii::app()->functions->getOption('stripe_mode',$merchant_id);
?>

<div id="error-message-wrapper"></div>

<form class="uk-form uk-form-horizontal forms" id="forms">
<?php echo CHtml::hiddenField('action','stripeSettings')?>

<div class="uk-form-row">
  <label class="uk-form-label"><?php echo Yii::t("default","Enabled Stripe")?>?</label>
  <?php 
  echo CHtml::checkBox('stripe_enabled',
  $enabled=="yes"?true:false
  ,array(
    'value'=>"yes",
    'class'=>"icheck"
  ))
  ?> 
</div>

<div class="uk-form-row">
  <label class="uk-form-label"><?php echo Yii::t("default","Mode")?></label>
  <?php 
  echo CHtml::radioButton('stripe_mode',
  $paymode=="Sandbox"?true:false
  ,array(
    'value'=>Yii::t("default","Sandbox"),
    'class'=>"icheck"
  ))
  ?>
  <?php echo Yii::t("default","Sandbox")?> 
  <?php 
  echo CHtml::radioButton('stripe_mode',
  $paymode=="live"?true:false
  ,array(
    'value'=>Yii::t("default","live"),
    'class'=>"icheck"
  ))
  ?>	
  <?php echo Yii::t("defau;t","live")?> 
</div>

<h3><?php echo Yii::t("default","Sandbox")?></h3>
<div class="uk-form-row">
  <label class="uk-form-label"><?php echo Yii::t("default","Test Secret key")?></label>
  <?php 
  echo CHtml::textField('sanbox_stripe_secret_key',
  Yii::app()->functions->getOption('sanbox_stripe_secret_key',$merchant_id)
  ,array(
    'class'=>"uk-form-width-large"
  ))
  ?>
</div>

<div class="uk-form-row">
  <label class="uk-form-label"><?php echo Yii::t("default","Test Publishable Key")?></label>
  <?php 
  echo CHtml::textField('sandbox_stripe_pub_key',
  Yii::app()->functions->getOption('sandbox_stripe_pub_key',$merchant_id)
  ,array(
    'class'=>"uk-form-width-large"
  ))
  ?>
</div>

<h3><?php echo Yii::t("default","live")?></h3>
<div class="uk-form-row">
  <label class="uk-form-label"><?php echo Yii::t("default","Live Secret key")?></label>
  <?php 
  echo CHtml::textField('live_stripe_secret_key',
  Yii::app()->functions->getOption('live_stripe_secret_key',$merchant_id)
  ,array(
    'class'=>"uk-form-width-large"
  ))
  ?>
</div>

<div class="uk-form-row">
  <label class="uk-form-label"><?php echo Yii::t("default","Live Publishable Key")?></label>
  <?php 
  echo CHtml::textField('live_stripe_pub_key',
  Yii::app()->functions->getOption('live_stripe_pub_key',$merchant_id)
  ,array(
    'class'=>"uk-form-width-large"
  ))
  ?>
</div>


<h3><?php echo t("Stripe Ideal")?></h3>

<div class="uk-form-row">
  <label class="uk-form-label"><?php echo Yii::t("default","Enabled Stripe Ideal")?></label>
  <?php 
  $merchant_stripe_ideal_enabled=getOption($merchant_id,'merchant_stripe_ideal_enabled');
  echo CHtml::checkBox('merchant_stripe_ideal_enabled',
  $merchant_stripe_ideal_enabled==1?true:false
  ,array(
    'value'=>1,
    'class'=>"icheck"
  ))
  ?> 
</div>

<div class="uk-form-row">
  <label class="uk-form-label"><?php echo Yii::t("default","Card Fee")?></label>
  <?php 
  echo CHtml::textField('merchant_stripe_ideal_fee',
  getOption($merchant_id,'merchant_stripe_ideal_fee')
  ,array(
    'class'=>"uk-form-width-large"
  ))
  ?>
</div>

<div class="uk-form-row">
<label class="uk-form-label"></label>
<input type="submit" value="<?php echo Yii::t("default","Save")?>" class="uk-button uk-form-width-medium uk-button-success">
</div>

</form>