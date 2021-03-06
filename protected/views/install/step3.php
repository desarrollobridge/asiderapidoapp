
<h3>Site information</h3>

<form method="POST" onsubmit="return checkform();" action="<?php echo Yii::app()->createUrl('index.php/install/finish')?>">

<div class="form-group">
   <label>Website name</label>
   <?php echo CHtml::textField('website_title','',array(
    'class'=>"form-control",
    'required'=>true
   ))?>
</div>

<div class="form-group">
   <label>Address</label>
   <?php echo CHtml::textField('website_address','',array(
    'class'=>"form-control",
    'required'=>true
   ))?>
</div>

<div class="form-group">
   <label>Country</label>
   <?php echo CHtml::dropDownList('admin_country_set','US',
   (array)$country_list
   ,array(
    'class'=>"form-control",
    'required'=>true
   ))?>
</div>

<div class="form-group">
   <label>Phone</label>
   <?php echo CHtml::textField('website_contact_phone','',array(
    'class'=>"form-control",
    'required'=>true
   ))?>
</div>

<div class="form-group">
   <label>Email</label>
   <?php echo CHtml::textField('website_contact_email','',array(
    'class'=>"form-control",
    'required'=>true
   ))?>
</div>

<div class="form-group">
   <label>Currency</label>
   <?php echo CHtml::dropDownList('admin_currency_set','',
   (array)$currency_list
   ,array(
    'class'=>"form-control",
    'required'=>true
   ))?>
</div>

<h3 style="margin-top:20px;margin-bottom:20px;">Admin user</h3>

<div class="form-group">
   <label>Username</label>
   <?php echo CHtml::textField('username','',array(
    'class'=>"form-control",
    'required'=>true
   ))?>
</div>
<div class="form-group">
   <label>Password</label>
   <?php echo CHtml::passwordField('password','',array(
    'class'=>"form-control",
    'required'=>true
   ))?>
</div>
<div class="form-group">
   <label>Confirm Password</label>
   <?php echo CHtml::passwordField('cpassword','',array(
    'class'=>"form-control",
    'required'=>true
   ))?>
</div>

<div class="panel panel-default">
<div class="panel-body">
 <button class="btn btn-success" type="submit" name="action">
   Next
 </button>
</div> 
</div>

</form>
