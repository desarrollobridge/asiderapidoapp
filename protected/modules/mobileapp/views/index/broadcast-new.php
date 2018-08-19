

<a href="<?php echo Yii::app()->createUrl('/mobileapp/index/broadcast')?>" class="pad5 block">
<i class="fa fa-long-arrow-left"></i> <?php echo AddonMobileApp::t("Back")?></a>

<div class="pad10">

<?php echo CHtml::beginForm(); ?> 
<?php 

?>

<h3><?php echo AddonMobileApp::t("Create new broadcast push notification")?></h3>
<hr/>

<div class="form-group">
    <label ><?php echo AddonMobileApp::t("Push Title")?></label>
    <?php 
    echo CHtml::textField('push_title','',array(
      'class'=>'form-control',
      'maxlength'=>200,
      'required'=>"true"
    ));
    ?>
  </div>
   
  <div class="form-group">
    <label ><?php echo AddonMobileApp::t("Push Message")?></label>
    <?php 
    echo CHtml::textArea('push_message','',array(
      'class'=>'form-control', 
      'required'=>true
    ));
    ?>
  </div>
  
  <div class="form-group">
    <label ><?php echo AddonMobileApp::t("Send to Device Platform")?></label>
    <?php 
    echo CHtml::dropDownList('device_platform','',AddonMobileApp::platFormList(),array(
      'class'=>'form-control',      
    ))
    ?>
  </div>
  
<div class="form-group">  
  <?php
echo CHtml::ajaxSubmitButton(
	AddonMobileApp::t("Save"),
	array('ajax/saveBroadcast'),
	array(
		'type'=>'POST',
		'dataType'=>'json',
		'beforeSend'=>'js:function(){
		                 busy(true); 	
		                 $("#submit").val("Processing");
		                 $("#submit").css({ "pointer-events" : "none" });	                 
		              }
		',
		'complete'=>'js:function(){
		                 busy(false); 		 
		                 $("#submit").val("Save");                
		                 $("#submit").css({ "pointer-events" : "auto" });
		              }',
		'success'=>'js:function(data){	
		               if(data.code==1){		               
		                 nAlert(data.msg,"success");
		                 $("#push_message").val("");
		                 $("#push_title").val("");
		               } else {
		                  nAlert(data.msg,"warning");
		               }
		            }
		'
	),array(
	  'class'=>'btn btn-primary',
	  'id'=>'submit'
	)
);
?>
  </div>
    
<?php echo CHtml::endForm(); ?>

</div>