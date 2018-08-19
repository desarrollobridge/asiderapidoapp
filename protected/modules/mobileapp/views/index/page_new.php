

<div class="pad10">


<a href="<?php echo Yii::app()->createUrl('/mobileapp/index/pages')?>" class="pad5 block">
<i class="fa fa-long-arrow-left"></i> <?php echo AddonMobileApp::t("Back")?></a>

<?php echo CHtml::beginForm(); ?> 
<?php 
if(isset($data['page_id'])){
	echo CHtml::hiddenField('id',$data['page_id']);
}
?>

<h3><?php echo AddonMobileApp::t("Page")?></h3>
<hr/>

<?php if ( Yii::app()->functions->multipleField()==2):?>

<ul class="nav nav-tabs" role="tablist">
     <li class="active"><a href="#default"><?php echo t("default")?></a></li>    
    <?php if ( $fields=FunctionsV3::getLanguageList(false)):?>  
    <?php foreach ($fields as $f_val): ?>
     <li><a href="#<?php echo $f_val;?>"><?php echo $f_val;?></a></li>
    <?php endforeach;?>
    <?php endif;?>
</ul>

<div class="tab-content">
  <div class="tab-pane active" id="default">
  
    
    <div style="padding:10px;">
    
       <div class="form-group">
	    <label ><?php echo AddonMobileApp::t("Title")?></label>
	    <?php 
	    echo CHtml::textField('title',
	    isset($data['title'])?stripslashes($data['title']):''
	    ,array(
	      'class'=>'form-control',
	      'maxlength'=>200,
	      'required'=>"true"
	    ));
	    ?>
	  </div>
	   
	  <div class="form-group">
	    <label ><?php echo AddonMobileApp::t("Content")?></label>
	    <?php 
	    echo CHtml::textArea('content',
	    isset($data['content'])?stripslashes($data['content']):''
	    ,array(
	      'class'=>'form-control', 
	      'required'=>true,
	      'style'=>"height:200px;"
	    ));
	    ?>
	  </div>  
    
    </div> <!--padding10-->
   
  </div> <!--default-->
  
  <?php foreach ($fields as $f_val): ?>
  <div class="tab-pane" id="<?php echo $f_val?>">
  
  
    <div style="padding:10px;">
   
      <div class="form-group">
	    <label ><?php echo AddonMobileApp::t("Title")?></label>
	    <?php 
	    echo CHtml::textField("lang_title_$f_val",
	    isset($data["lang_title_$f_val"])?stripslashes($data["lang_title_$f_val"]):''
	    ,array(
	      'class'=>'form-control',
	      'maxlength'=>200,
	      'required'=>"true"
	    ));
	    ?>
	  </div>
	   
	  <div class="form-group">
	    <label ><?php echo AddonMobileApp::t("Content")?></label>
	    <?php 
	    echo CHtml::textArea("lang_content_$f_val",
	    isset($data["lang_content_$f_val"])?stripslashes($data["lang_content_$f_val"]):''
	    ,array(
	      'class'=>'form-control', 
	      'required'=>true,
	      'style'=>"height:200px;"
	    ));
	    ?>
	  </div>  
    
    </div> <!--padding10-->
  
  </div>
  <?php endforeach;?>
</div> <!--tab-content-->


<?php else :?>

   <div class="form-group">
    <label ><?php echo AddonMobileApp::t("Title")?></label>
    <?php 
    echo CHtml::textField('title',
    isset($data['title'])?stripslashes($data['title']):''
    ,array(
      'class'=>'form-control',
      'maxlength'=>200,
      'required'=>"true"
    ));
    ?>
  </div>
   
  <div class="form-group">
    <label ><?php echo AddonMobileApp::t("Content")?></label>
    <?php 
    echo CHtml::textArea('content',
    isset($data['content'])?stripslashes($data['content']):''
    ,array(
      'class'=>'form-control', 
      'required'=>true,
      'style'=>"height:200px;"
    ));
    ?>
  </div>  
  
 <?php endif;?>
  
  <div class="form-group">
    <label ><?php echo AddonMobileApp::t("Icon")?></label>
    <?php 
    echo CHtml::textField('icon',
    isset($data['icon'])?stripslashes($data['icon']):''
    ,array(
      'class'=>'form-control', 
      'placeholder'=>AddonMobileApp::t("example")." ion-help-circled"
    ));
    ?>
  </div>
  <p><?php echo AddonMobileApp::t("Get your icon in")?> 
  <a target="_blank" href="http://ionicons.com/">http://ionicons.com/</a> </p>
  
  <div class="form-group">
    <label><?php echo AddonMobileApp::t("HTML format")?> </label>
    <?php 
    if(!isset($data['use_html'])){
    	$data['use_html']=1;
    }
    echo CHtml::checkBox('use_html',
    $data['use_html']==2?true:false
    ,array(
      'value'=>2,
    ));
    ?>
  </div>
  
  <div class="form-group">
    <label ><?php echo AddonMobileApp::t("Sequence")?></label>
    <?php 
    echo CHtml::textField('sequence',
    isset($data['sequence'])?stripslashes($data['sequence']):''
    ,array(
      'class'=>'form-control numeric_only',      
    ));
    ?>
  </div>
  
  
  <div class="form-group">
    <label ><?php echo AddonMobileApp::t("Status")?></label>
    <?php 
    echo CHtml::dropDownList('status',
    isset($data['status'])?stripslashes($data['status']):''
    , statusList() ,array(
      'class'=>'form-control',      
    ))
    ?>
  </div>
  
<div class="form-group">  
  <?php
echo CHtml::ajaxSubmitButton(
	AddonMobileApp::t("Save"),
	array('ajax/savePages'),
	array(
		'type'=>'POST',
		'dataType'=>'json',
		'beforeSend'=>'js:function(){
		                 busy(true); 	
		                 $("#submit").val("'.AddonMobileApp::t("Processing").'");
		                 $("#submit").css({ "pointer-events" : "none" });	                 
		              }
		',
		'complete'=>'js:function(){
		                 busy(false); 		 
		                 $("#submit").val("'.AddonMobileApp::t("Save").'");                
		                 $("#submit").css({ "pointer-events" : "auto" });
		              }',
		'success'=>'js:function(data){	
		               if(data.code==1){		               
		                 nAlert(data.msg,"success");
		                 
		                 if(!empty(data.details)){
		                    window.location.href = data.details;
		                 }
		                 
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