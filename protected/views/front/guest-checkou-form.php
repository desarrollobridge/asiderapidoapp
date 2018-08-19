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

<?php if ($transaction_type=="delivery"):?>

<div class="row top10">
<div class="col-md-10">
<?php echo CHtml::textField('street',
isset($client_info['street'])?$client_info['street']:''
,array(
   'class'=>'grey-fields full-width',
   'placeholder'=>Yii::t("default","Calle"),
   'data-validation'=>"required"
  ))?> 
</div> 
</div>

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
   'placeholder'=>Yii::t("default","Codigo Postal")
  ))?> 
</div> 
</div>

<div class="row top10">
<div class="col-md-10">
<?php echo CHtml::textField('location_name',
isset($client_info['location_name'])?$client_info['location_name']:''
,array(
   'class'=>'grey-fields full-width',
   'placeholder'=>Yii::t("default","Apartment suite, unit number, or company name"),   
  ))?>
</div> 
</div>

<div class="row top10">
<div class="col-md-10">
<?php echo CHtml::textField('contact_phone',
isset($client_info['contact_phone'])?$client_info['contact_phone']:''
,array(
   'class'=>'grey-fields mobile_inputs',
   'placeholder'=>Yii::t("default","Numero de Telefono"),
   'data-validation'=>"required"  
  ))?> 
</div> 
</div>

<div class="row top10">
<div class="col-md-10">
<?php echo CHtml::textField('delivery_instruction','',array(
  'class'=>'grey-fields full-width',
  'placeholder'=>Yii::t("default","Instrucciones de envio")   
))?> 
</div> 
</div>

<div class="row top10">
<div class="col-md-10">
<?php echo CHtml::textField('email_address','',array(
   'class'=>'grey-fields full-width',
   'placeholder'=>Yii::t("default","Correo electronico"),   
  ))?> 
</div> 
</div>

<?php else :?>

<?php 
echo CHtml::hiddenField('street','');
echo CHtml::hiddenField('city','');
echo CHtml::hiddenField('state','');
echo CHtml::hiddenField('zipcode','');
echo CHtml::hiddenField('location_name','');
echo CHtml::hiddenField('delivery_instruction','');
?>

<div class="row top10">
<div class="col-md-10">
<?php echo CHtml::textField('contact_phone',
isset($client_info['contact_phone'])?$client_info['contact_phone']:''
,array(
   'class'=>'grey-fields mobile_inputs',
   'placeholder'=>Yii::t("default","Número de telefono"),
   'data-validation'=>"required"  
  ))?> 
</div> 
</div>

<div class="row top10">
<div class="col-md-10">
<?php echo CHtml::textField('email_address','',array(
   'class'=>'grey-fields full-width',
   'placeholder'=>Yii::t("default","Correo electronico"),   
  ))?> 
</div> 
</div>

<?php endif;?>


<?php FunctionsV3::sectionHeader('Create Account')?>		  
<p class="text-muted text-small">***<?php echo t("Opcional")?></p>

<div class="row top10">
<div class="col-md-10">
   <?php echo CHtml::passwordField('password','',array(
   'class'=>'grey-fields full-width',
   'placeholder'=>Yii::t("default","contraseña"),   
  ))?>
 </div> 
</div>          