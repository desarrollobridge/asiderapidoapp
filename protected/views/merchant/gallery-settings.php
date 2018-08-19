<?php
$merchant_id=Yii::app()->functions->getMerchantID();
$gallery=Yii::app()->functions->getOption("merchant_gallery",$merchant_id);
$gallery=!empty($gallery)?json_decode($gallery):false;

$gallery_disabled=Yii::app()->functions->getOption("gallery_disabled",$merchant_id);
?>

<form class="uk-form uk-form-horizontal forms" id="forms">
<?php echo CHtml::hiddenField('action','gallerySettings')?>


<div class="uk-form-row"> 
 <label class="uk-form-label"><?php echo Yii::t('default',"Disabled Gallery")?>?</label>
 <?php 
 echo CHtml::checkBox('gallery_disabled',
 $gallery_disabled=="yes"?true:false
 ,array(
   'class'=>'icheck',
   'value'=>"yes"
 ));
 ?>
</div>

<div class="uk-form-row"> 
 <label class="uk-form-label"><?php echo Yii::t('default',"Gallery")?></label>
  <div style="display:inline-table;margin-left:1px;" class="button uk-button" id="gallery"><?php echo Yii::t('default',"Browse")?></div>	  
  <DIV  style="display:none;" class="gallery_chart_status" >
	<div id="percent_bar" class="gallery_percent_bar"></div>
	<div id="progress_bar" class="gallery_progress_bar">
	  <div id="status_bar" class="gallery_status_bar"></div>
	</div>
  </DIV>		  
</div>

<div class="image_preview" id="gallery-preview">
<?php $x=0;?>
  <?php if (is_array($gallery) && count($gallery)>=1):?>
  <?php foreach ($gallery as $val):?>
  <?php $id=mktime()+$x++;?>
  <li>
  <img src="<?php echo uploadURL()."/$val"?>" class="uk-thumbnail uk-thumbnail-mini <?php echo $id;?>">
  <p class="<?php echo $id?>">
  <?php echo CHtml::hiddenField('photo[]',$val)?>
  <a href="javascript:rm_gallery('<?php echo $id;?>');"><?php echo t("Remove image")?></a>
  </p>
  </li>
  <?php endforeach;?>
  <?php endif;?>
</div><!-- gallery-preview-->

<div class="spacer"></div>

<div class="uk-form-row">
<label class="uk-form-label"></label>
<input type="submit" value="<?php echo Yii::t("default","Save")?>" class="uk-button uk-form-width-medium uk-button-success">
</div>

</form>