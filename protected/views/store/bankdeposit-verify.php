<?php
$this->renderPartial('/front/default-header',array(
   'h1'=>t("Bank Deposit"),
   'sub_text'=>t("verify bank deposit")
));
$ref=isset($_GET['ref'])?$_GET['ref']:'';
?>

<div class="sections section-grey2 section-orangeform">
  <div class="container">  
  <div class="inner">
  
  <h1><?php echo t("Verificación de deposito bancario")?></h1>
  <div class="box-grey rounded">
  <?php if ($res=Yii::app()->functions->getMerchantByToken($ref)):?>
  
   <p class="text-muted">
  <?php echo Yii::t("default","Ingrese los detalles del pago de su depósito bancario a continuación.")?><br/>
  <?php echo Yii::t("default","La falta de información precisa puede causar retrasos en el procesamiento o la invalidación de su pago.")?>
  </p>
    
  <form class="forms-normal forms" id="forms">
  <?php echo CHtml::hiddenField('action','bankDepositVerification')?>
  <?php echo CHtml::hiddenField('ref',$ref)?>
  
  <div class="row top10">
	  <div class="col-md-3"><?php echo t("Codigo")?></div>
	  <div class="col-md-8">
	    <?php
	    echo CHtml::textField('branch_code','',array('class'=>"grey-fields full-width",'data-validation'=>"required"))
	    ?>
	  </div>
  </div>
  
  <div class="row top10">
	  <div class="col-md-3"><?php echo t("Fecha")?></div>
	  <div class="col-md-8">
	   <?php echo CHtml::hiddenField('date_of_deposit')?>
		  <?php echo CHtml::textField('date_of_deposit1','',
		  array('class'=>"j_date2 grey-fields full-width",
		  'data-validation'=>"required",
		  'data-id'=>'date_of_deposit'
		  ));
		  ?>
	  </div>
  </div> 
  
   <div class="row top10">
	  <div class="col-md-3"><?php echo t("Hora")?></div>
	  <div class="col-md-8">
	    <?php echo CHtml::textField('time_of_deposit','',array('class'=>"timepick grey-fields full-width",'data-validation'=>"required"))?>
	  </div>
  </div> 
  
 <div class="row top10">
	  <div class="col-md-3"><?php echo t("Cantidad")?></div>
	  <div class="col-md-8">
	      <?php echo CHtml::textField('amount','',array('data-validation'=>"required",'class'=>'numeric_only grey-fields full-width'))?>
	  </div>
  </div>  
  
 <p class="text-muted" style="margin-top:10px;"><?php echo Yii::t("default","or upload your scan bank deposit")?></p> 

 <div class="row top10">
	  <div class="col-md-3"><?php echo t("Scan Bank deposit slip")?></div>
	  <div class="col-md-8">
	  
	   <div style="display:inline-table;margin-left:1px;" class="btn btn-default" id="photo"><?php echo Yii::t('default',"Browse")?></div>	  
  <DIV  style="display:none;" class="photo_chart_status" >
	<div id="percent_bar" class="photo_percent_bar"></div>
	<div id="progress_bar" class="photo_progress_bar">
	  <div id="status_bar" class="photo_status_bar"></div>
	</div>
  </DIV>	 
	  
	  </div>
  </div>  
  
  <div class="row top10">
	  <div class="col-md-3"></div>
	  <div class="col-md-8">
	      <input type="submit" value="<?php echo Yii::t("default","Submit")?>" class="btn btn-success">
	  </div>
  </div>  
 
  </form>
  
  <?php else :?>
  <p class="text-danger"><?php echo Yii::t("default","¡Ups! no podemos encontrar lo que estas buscando")?></p>
  <?php endif;?>
  
  </div>
  </div>
  </div>
</div>  