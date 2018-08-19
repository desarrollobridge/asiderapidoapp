<div class="page-right-sidebar">
  <div class="main">
  <div class="inner">
  <form class="uk-form uk-form-horizontal forms" id="forms" onsubmit="return false;">
  <?php echo CHtml::hiddenField('action','addCreditCard')?>
  <?php echo CHtml::hiddenField('currentController','store')?>

  <h2><?php echo Yii::t("default","Add Cards");?></h2>
  
  <a class="uk-button right" href="<?php echo Yii::app()->request->baseUrl; ?>/store/Cards/"><i class="fa fa-list"></i> <?php echo Yii::t("default","Lista")?></a>
  <div class="clear"></div>
  
  <div class="spacer"></div>
        
    <?php if (Yii::app()->functions->isClientLogin()):?>
    <div class="uk-form-row">                  
          <?php echo CHtml::textField('card_name','',array(
           'class'=>'uk-width-1-1',
           'placeholder'=>Yii::t("default","Nomnbre en tarjeta"),
           'data-validation'=>"required"  
          ))?>
       </div>             
       
       <div class="uk-form-row">                  
          <?php echo CHtml::textField('credit_card_number','',array(
           'class'=>'uk-width-1-1 numeric_only',
           'placeholder'=>Yii::t("default","Número de Tarjeta"),
           'data-validation'=>"required",
           'maxlength'=>20
          ))?>
       </div>             
       
       <div class="uk-form-row">                  
          <?php echo CHtml::dropDownList('expiration_month','',
          Yii::app()->functions->ccExpirationMonth()
          ,array(
           'class'=>'uk-width-1-1',
           'placeholder'=>Yii::t("default","Exp. moes"),
           'data-validation'=>"required"  
          ))?>
       </div>             
       
       <div class="uk-form-row">                  
          <?php echo CHtml::dropDownList('expiration_yr','',
          Yii::app()->functions->ccExpirationYear()
          ,array(
           'class'=>'uk-width-1-1',
           'placeholder'=>Yii::t("default","Exp. año") ,
           'data-validation'=>"required"  
          ))?>
       </div>             
       
       <div class="uk-form-row">                  
          <?php echo CHtml::textField('cvv','',array(
           'class'=>'uk-width-1-1',
           'placeholder'=>Yii::t("default","CVV"),
           'data-validation'=>"required",
           'maxlength'=>4
          ))?>
       </div>             
       
       <div class="uk-form-row">                  
          <?php echo CHtml::textField('billing_address','',array(
           'class'=>'uk-width-1-1',
           'placeholder'=>Yii::t("default","Dirección de Envío") ,
           'data-validation'=>"required"  
          ))?>
       </div>             
       
       <div class="uk-form-row">   
          <input type="submit" value="<?php echo Yii::t("default","Agregar Tarjeta")?>" class="uk-button uk-button-success uk-width-1-4">
       </div>
     </div> 
    <?php else :?> 
    <p class="uk-text-danger"><?php echo Yii::t("default","lo sentimos, debes registrarte primero.")?></p>
    <?php endif;?>
    
  </form>
    </div>
  </div> <!--main-->
</div> <!--page-right-sidebar--> 