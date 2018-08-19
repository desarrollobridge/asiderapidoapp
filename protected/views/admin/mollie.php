<?php
$paymode=Yii::app()->functions->getOptionAdmin('admin_mol_mode');
?>

<div id="error-message-wrapper"></div>

<form class="uk-form uk-form-horizontal forms" id="forms">
<?php echo CHtml::hiddenField('action','adminMollieSettings')?>

<div class="uk-form-row">
  <label class="uk-form-label"><?php echo t("Enabled")." ".t("Mollie")?>?</label>
  <?php 
  echo CHtml::checkBox('admin_mol_enabled',
  getOptionA('admin_mol_enabled')==2?true:false
  ,array(
    'value'=>2,
    'class'=>"icheck"
  ))
  ?> 
</div>

<div class="uk-form-row">
  <label class="uk-form-label"><?php echo Yii::t("default","Mode")?></label>
  <?php 
  echo CHtml::radioButton('admin_mol_mode',
  $paymode=="sandbox"?true:false
  ,array(
    'value'=>"sandbox",
    'class'=>"icheck"
  ))
  ?>
  <?php echo Yii::t("default","Sandbox")?>
  <?php 
  echo CHtml::radioButton('admin_mol_mode',
  $paymode=="production"?true:false
  ,array(
    'value'=>"production",
    'class'=>"icheck"
  ))
  ?>	
  <?php echo Yii::t("default","live")?> 
</div>


<div class="uk-form-row">
  <label class="uk-form-label"><?php echo t("Locale")?></label>
  <?php 
  echo CHtml::dropDownList('admin_mol_locale',getOptionA('admin_mol_locale'),array(
    'en_US'=>"en_US",
    'de_DE'=>"de_DE",
    'es_ES'=>"es_ES",
    'fr_FR'=>"fr_FR",
    'nl_BE'=>"nl_BE",
    'fr_BE'=>"fr_BE",
    'nl_NL'=>"nl_NL",
  ));
  ?> 
</div>


<h3><?php echo Yii::t("default","Sandbox")?></h3>

<div class="uk-form-row">
  <label class="uk-form-label"><?php echo t("API KEY")?></label>
  <?php 
  echo CHtml::textField('admin_mollie_apikey_sanbox',
  Yii::app()->functions->getOptionAdmin('admin_mollie_apikey_sanbox')
  ,array(
    'class'=>"uk-form-width-large"
  ))
  ?>
</div>


<h3><?php echo Yii::t("default","Live")?></h3>


<div class="uk-form-row">
  <label class="uk-form-label"><?php echo t("API KEY")?></label>
  <?php 
  echo CHtml::textField('admin_mollie_apikey_live',
  Yii::app()->functions->getOptionAdmin('admin_mollie_apikey_live')
  ,array(
    'class'=>"uk-form-width-large"
  ))
  ?>
</div>


<div class="uk-form-row">
  <label class="uk-form-label"><?php echo t("Card fee")?></label>
  <?php 
  echo CHtml::textField('admin_mollie_card_fee',
  Yii::app()->functions->getOptionAdmin('admin_mollie_card_fee')
  ,array(
    'class'=>"uk-form numeric_only"
  ))
  ?>
</div>

<div class="uk-form-row">
  <label class="uk-form-label"><?php echo t("WEBHOOK URL")?></label>
  <?php 
  echo websiteUrl()."/molliewebhook/"
  ?>
</div>


<div class="uk-form-row">
<label class="uk-form-label"></label>
<input type="submit" value="<?php echo Yii::t("default","Save")?>" class="uk-button uk-form-width-medium uk-button-success">
</div>

</form>