
<div class="pad10">

<?php 
$lang=merchantApp::availableLanguages();
$dictionary=require_once('MobileTranslation.php');

$mobile_dictionary=getOptionA('merchant_mobile_dictionary');
if (!empty($mobile_dictionary)){
	$mobile_dictionary=json_decode($mobile_dictionary,true);
} else $mobile_dictionary=false;
?>
<?php echo CHtml::beginForm('','post',array('class'=>"form-horizontal")); ?> 

<div id="translation-save-wrap"> 
  <a href="javascript:;" class="export-language btn btn-success"><?php echo merchantApp::t("Export")?></a>
  <a href="javascript:;" id="import-language" class="import-language btn btn-info"><?php echo merchantApp::t("Import")?></a>
  <a href="#main-wrapper" class="backtop btn btn-default"><?php echo merchantApp::t("Back to top")?></a>
  <a href="#bottom-trans" class="backtop btn btn-default"><?php echo merchantApp::t("Go to bottom")?></a>
  <?php
echo CHtml::ajaxSubmitButton(
	t("Save"),
	array('ajax/saveTranslation'),
	array(
		'type'=>'POST',
		'dataType'=>'json',
		'beforeSend'=>'js:function(){
		                 busy(true); 	
		                 $("#save-settings").val("'.t("Processing").'");
		                 $("#save-settings").css({ "pointer-events" : "none" });	                 	                 
		              }
		',
		'complete'=>'js:function(){
		                 busy(false); 		                 
		                 $("#save-settings").val("'.t("Save").'");
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

  <ul class="nav nav-tabs" role="tablist" id="nav-tabs">
  <?php $x=1;?>
  <?php foreach ($lang as $lang_code=>$val_lang):?>
   <li class="<?php echo $x==1?"active":''?>">
   <a href="#tab-<?php echo $lang_code?>" role="tab" data-toggle="tab"><?php echo $val_lang?></a>
   </li>
  <?php $x++;?>
  <?php endforeach;?>
 </ul>
 
<div class="tab-content">
  <?php $x=1;?>
  <?php foreach ($lang as $lang_code=>$val_lang):?>
   <div role="tabpanel" class="tab-pane <?php echo $x==1?"active":''?>" id="tab-<?php echo $lang_code;?>">
     
     <div class="pad10">     
     <?php foreach ($dictionary as $key=>$val):?>     
     <?php 
       $value='';
       $field_name=$key."[$lang_code]";
       if ( $lang_code=="en"  && !is_array($mobile_dictionary)){
       	  $value=$val;
       } else {       	  
       	  $value=$mobile_dictionary[$key][$lang_code];
       	  if (empty($value)){
       	  	  $value=$val;
       	  }
       }
     ?>
     <div class="form-group">
       <label class="col-sm-2 control-label"><?php echo $key?></label>
       <div class="col-sm-10">
       <?php 
       echo CHtml::textField($field_name,$value,array(
         'class'=>"form-control"
       ));
       ?>
       </div>
     </div>
     <?php endforeach;?>
     </div>
     
   </div>
  <?php $x++;?>
  <?php endforeach;?>
</div>
     
  

<?php echo CHtml::endForm(); ?>

</div>
<div id="bottom-trans"></div>