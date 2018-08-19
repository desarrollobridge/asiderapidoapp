<?php
$mtid=Yii::app()->functions->getMerchantID();
$paymode=getOption($mtid,'merchant_moneris_mode');
?>

<div id="error-message-wrapper"></div>

<form class="uk-form uk-form-horizontal forms" id="forms">
<?php echo CHtml::hiddenField('action','merchantMonerisSettings')?>

<div class="uk-form-row">
  <label class="uk-form-label"><?php echo t("Enabled")." ".t("moneris")?>?</label>
  <?php 
  echo CHtml::checkBox('merchant_moneris_enabled',
  getOption($mtid,'merchant_moneris_enabled')==2?true:false
  ,array(
    'value'=>2,
    'class'=>"icheck"
  ))
  ?> 
</div>

<div class="uk-form-row">
  <label class="uk-form-label"><?php echo Yii::t("default","Mode")?></label>
  <?php 
  echo CHtml::radioButton('merchant_moneris_mode',
  $paymode=="sandbox"?true:false
  ,array(
    'value'=>"sandbox",
    'class'=>"icheck"
  ))
  ?>
  <?php echo Yii::t("default","Sandbox")?>
  <?php 
  echo CHtml::radioButton('merchant_moneris_mode',
  $paymode=="live"?true:false
  ,array(
    'value'=>"live",
    'class'=>"icheck"
  ))
  ?>	
  <?php echo Yii::t("default","live")?> 
</div>



<h3><?php echo Yii::t("default","Connection Settings")?></h3>

<div class="uk-form-row">
  <label class="uk-form-label"><?php echo t("Store ID")?></label>
  <?php 
  echo CHtml::textField('merchant_moneris_storeid',
  getOption($mtid,'merchant_moneris_storeid')
  ,array(
    'class'=>"uk-form-width-large"
  ))
  ?>
</div>

<div class="uk-form-row">
  <label class="uk-form-label"><?php echo t("API Token")?></label>
  <?php 
  echo CHtml::textField('merchant_moneris_token',
  getOption($mtid,"merchant_moneris_token")
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