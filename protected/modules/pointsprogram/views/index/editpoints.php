
<div>

<a href="<?php echo Yii::app()->createUrl('pointsprogram/index/rewardpoints');?>">
<i class="fa fa-long-arrow-left"></i> <?php echo t("Back")?></a>

<?php echo CHtml::beginForm('','post',array(
'class'=>"form-horizontal"
)); ?> 
<?php echo CHtml::hiddenField('client_id',$client_id)?>

<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Update User Reward Points")?></label>
  <div class="col-sm-8">
  <?php 
  echo CHtml::textField('user_points',$user_points,array(
      'class'=>'numeric_only form-control'      
    ));
  ?>
  </div>
</div>

 <div class="form-group">
    <div class="col-sm-offset-3 col-sm-9">
  <?php
echo CHtml::ajaxSubmitButton(
	PointsProgram::t('Update'),
	array('ajax/savepoints'),
	array(
		'type'=>'POST',
		'dataType'=>'json',
		'beforeSend'=>'js:function(){
		                 busy(true); 	
		                 $("#save-settings").val("'.PointsProgram::t("processing").'");
		                 $("#save-settings").css({ "pointer-events" : "none" });	                 	                 
		              }
		',
		'complete'=>'js:function(){
		                 busy(false); 		                 
		                 $("#save-settings").val("Update");
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

</div>