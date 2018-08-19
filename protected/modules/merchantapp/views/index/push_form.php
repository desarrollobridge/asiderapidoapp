

<div class="pad10">

<?php echo CHtml::beginForm(); ?> 
<?php 
echo CHtml::hiddenField('device_id',$data['device_id']);
echo CHtml::hiddenField('device_platform',$data['device_platform']);
echo CHtml::hiddenField('merchant_id',$data['merchant_id']);
echo CHtml::hiddenField('user_type',$data['user_type']);
echo CHtml::hiddenField('merchant_user_id',$data['merchant_user_id']);
?>

<div class="form-group">
    <label ><?php echo merchantApp::t("Push Title")?></label>
    <?php 
    echo CHtml::textField('push_title','',array(
      'class'=>'form-control',
      'maxlength'=>200,
      'required'=>true
    ));
    ?>
  </div>
   
  <div class="form-group">
    <label ><?php echo merchantApp::t("Push Message")?></label>
    <?php 
    echo CHtml::textArea('push_message','',array(
      'class'=>'form-control',  
      'required'=>true
    ));
    ?>
  </div>
  
<div class="form-group">  
  <?php
echo CHtml::ajaxSubmitButton(
	merchantApp::t("Send Push notification"),
	array('ajax/sendpush'),
	array(
		'type'=>'POST',
		'dataType'=>'json',
		'beforeSend'=>'js:function(){
		                 busy(true); 	
		                 $("#submit").val("'.merchantApp::t("Processing...").'");
		                 $("#submit").css({ "pointer-events" : "none" });	                 
		              }
		',
		'complete'=>'js:function(){
		                 busy(false); 		 
		                 $("#submit").val("'.merchantApp::t("Send Push notification").'");                
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