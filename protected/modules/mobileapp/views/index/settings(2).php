
<div class="pad10">

 <?php echo CHtml::beginForm(); ?> 
 <?php 
 
 $ios_push_dev_cer=getOptionA('ios_push_dev_cer');
 $ios_push_prod_cer=getOptionA('ios_push_prod_cer');
 
 echo CHtml::hiddenField('mobile_default_image_not_available',
 getOptionA('mobile_default_image_not_available')
 ,array(
   'class'=>'mobile_default_image_not_available'
 ));
 
 echo CHtml::hiddenField('ios_push_dev_cer',$ios_push_dev_cer,array(
  'class'=>'ios_push_dev_cer'
 ));
 echo CHtml::hiddenField('ios_push_prod_cer',$ios_push_prod_cer,array(
  'class'=>'ios_push_prod_cer'
 ));
 ?>
 
 <div class="form-group" id="chosen-field">
  <label ><?php echo AddonMobileApp::t("Your mobile API URL")?></label><br/>
  <p class="bg-success inlineblock"><?php echo websiteUrl()."/mobileapp/api" ?></p>
  <p class="text-muted"><?php echo AddonMobileApp::t("Set this url on your mobile app config files on")?> www/js/config.js</p>
 </div>
 
 
  <div class="form-group">
    <label ><?php echo AddonMobileApp::t("API hash key")?></label>
    <?php 
    echo CHtml::textField('mobileapp_api_has_key',getOptionA('mobileapp_api_has_key'),array(
      'class'=>'form-control',
    ));
    ?>
  </div>
  <P class="text-small text-muted">
  <?php echo AddonMobileApp::t("api hash key is optional this features make your api secure. make sure you put same api hash key on your")?> www/js/config.js <br/>
  <?php echo AddonMobileApp::t("Sample api hash key").": <b>".md5(Yii::app()->functions->generateCode(50))."</b>"?>
  </P>
 
 <div class="form-group" id="chosen-field">
    <label ><?php echo AddonMobileApp::t("Location")?></label>
    <?php echo CHtml::dropDownList('mobile_country_list[]',
    $mobile_country_list,
   (array)$country_list,
   array(
    'class'=>'form-control chosen',
    'multiple'=>true
  ))?>  
  </div>
      
  
  <div class="row">
     <div class="col-md-6">
     
      <div class="form-group">
	    <label ><?php echo AddonMobileApp::t("Default Image")?></label>
	    <a id="upload-file" href="javascript:;" class="btn btn-default"><?php echo AddonMobileApp::t("Browse")?></a>
	    <?php if (!empty($default_image_url)):?>
	    <img src="<?php echo $default_image_url?>" alt="" class="my-thumb img-thumbnail">       
	    <?php endif;?>
	  </div>
     
     </div> <!--col-->
     <div class="col-md-6">          
     </div>
  </div> <!--row-->
  
  
   <div class="row">
     <div class="col-md-6">
     
      <?php echo CHtml::hiddenField('upload_push_icon',$upload_push_icon)?>
      <?php echo CHtml::hiddenField('upload_push_picture',$upload_push_picture)?>
     
      <div class="form-group">
	    <label ><?php echo AddonMobileApp::t("Android Push Icon")?></label>
	    <a id="upload-push-icon" href="javascript:;" class="btn btn-default"><?php echo AddonMobileApp::t("Browse")?></a>	    
	    <img src="<?php echo !empty($upload_push_icon)?AddonMobileApp::getImage($upload_push_icon):'';?>" 
       alt="" class="upload_push_icon thumb <?php echo empty($upload_push_icon)?"hide":''?>" > 
	  </div>
     
     </div> <!--col-->
     <div class="col-md-6">          
     
     
       <div class="form-group">
	    <label ><?php echo AddonMobileApp::t("Android Push Picture")?></label>
	    <a id="upload-push-picture" href="javascript:;" class="btn btn-default"><?php echo AddonMobileApp::t("Browse")?></a>
	    
	    <img src="<?php echo !empty($upload_push_picture)?AddonMobileApp::getImage($upload_push_picture):'';?>" 
        alt="" class="upload_push_picture thumb <?php echo empty($upload_push_picture)?"hide":''?>" > 
	    
	  </div>
     
     </div> <!--col-->
  </div> <!--row-->
  
  
  <div class="form-group">
    <label ><?php echo AddonMobileApp::t("Android Push API Key")?></label>
    <?php 
    echo CHtml::textField('mobile_android_push_key',getOptionA('mobile_android_push_key'),array(
      'class'=>'form-control',
    ));
    ?>
  </div>
 
  
  <div class="row">
    <div class="col-md-6">
    
       
  <div class="form-group" style="margin-top: 30px">
    <label style="padding-right:10px;" ><?php echo AddonMobileApp::t("Show Description on addon item")?></label>
    <?php 
    echo CHtml::checkBox('show_addon_description',
    getOptionA('show_addon_description')==1?true:false
    ,array(
      'value'=>1
    ))
    ?>
  </div>  
    
	
    
    
    </div> <!--col-->
    <div class="col-md-6">
    
    <div class="form-group" style="margin-top: 30px">
	<label style="padding-right:10px;" ><?php echo AddonMobileApp::t("Activate Menu 1")?></label>
	<?php 
	echo CHtml::checkBox('mobile_menu',
	getOptionA('mobile_menu')==1?true:false
	,array(
	'value'=>1
	))
	?>
	<p class="text-muted"><?php echo AddonMobileApp::t("this menu options display only food name and price")?></p>
	</div>
	
    
    </div> <!--col-->
  </div> <!--row-->
  
  
  <div class="row">
    <div class="col-md-6">

    <div class="form-group" style="margin-top: 30px">
    <label style="padding-right:10px;"><?php echo AddonMobileApp::t("Save cart to database")?></label>
    <?php 
    echo CHtml::checkBox('mobile_save_cart_db',
    getOptionA('mobile_save_cart_db')==1?true:false
    ,array(
      'value'=>1
    ))
    ?>    
    <p class="text-muted"><?php echo AddonMobileApp::t("this options will save the cart on database instead on device")?></p>
  </div>
  
    </div> <!--col-->
    <div class="col-md-6">

      <div class="form-group">
	<label ><?php echo AddonMobileApp::t("Facebook APP ID")?></label>
	<?php 
	echo CHtml::textField('mobile_facebookid',getOptionA('mobile_facebookid'),array(
	'class'=>'form-control',
	));
	?>
	</div>
	
    </div> <!--col-->
 </div> <!--row-->
  
 <div class="row">
    <div class="col-md-6">
    
     <div class="form-group" style="margin-top: 30px">
    <label style="padding-right:10px;"><?php echo AddonMobileApp::t("Enabled Google Login")?></label>
    <?php 
    echo CHtml::checkBox('mobile_enabled_googlogin',
    getOptionA('mobile_enabled_googlogin')==1?true:false
    ,array(
      'value'=>1
    ))
    ?>        
  </div>	
    
    </div> <!--col-->
    <div class="col-md-6">

      <div class="form-group" >
	    <label style="padding-right:10px;"><?php echo AddonMobileApp::t("App Default Language")?></label>
	    <?php 
	    echo CHtml::dropDownList('force_app_default_lang',getOptionA('force_app_default_lang'),
	    (array)FunctionsV3::getLanguageList(true),array(
	      'class'=>"form-control"
	    ));
	    ?>    
	    <p class="text-muted"><?php echo AddonMobileApp::t("Force default language")?></p>
	  </div>  

	       
    </div> <!--col-->
 </div> <!--row-->
 
 <div class="row">
    <div class="col-md-6">

    <div class="form-group" >
    <label style="padding-right:10px;"><?php echo AddonMobileApp::t("Location Accuracy")?></label>
    <?php 
    echo CHtml::dropDownList('mobileapp_location_accuracy',getOptionA('mobileapp_location_accuracy'),
    array(
      'true'=>AddonMobileApp::t("true"),
      'false'=>AddonMobileApp::t("false"),
    ),array(
      'class'=>"form-control"
    ));
    ?>    
    <p class="text-muted"><?php echo AddonMobileApp::t("Force default language")?></p>
  </div>   
    
    </div> <!--col-->
    <div class="col-md-6">
    
     
  <div class="form-group" >
    <label style="padding-right:10px;"><?php echo AddonMobileApp::t("Get Current location results")?></label>
    <?php 
    echo CHtml::dropDownList('app_current_location_results',getOptionA('app_current_location_results'),
    array(
       'formatted_address'=>AddonMobileApp::t("formatted address"),
       'address'=>AddonMobileApp::t("address"),
       'city'=>AddonMobileApp::t("city"),
       'state'=>AddonMobileApp::t("state")
    )
    ,array(
      'class'=>"form-control"
    ));
    ?>        
  </div> 
    
    </div> <!--col-->
 </div> <!--row-->
       
 
  <hr/>
  
  
  
  <p style="font-size:12px;color:red;">
  <?php echo AddonMobileApp::t("Note: for ios push notification to work make sure your server port 2195 is open")?>.
  </p>
  
 <div class="form-group">
    <label ><?php echo AddonMobileApp::t("IOS Push Mode")?></label>
    <?php 
    echo CHtml::dropDownList('ios_push_mode',getOptionA('ios_push_mode'),array(
      "development"=>AddonMobileApp::t("Development"),
      "production"=>AddonMobileApp::t("Production")
    ),array(
      'class'=>"form-control"
    ));
    ?>
  </div>
      
  <div class="form-group">
    <label ><?php echo AddonMobileApp::t("IOS Push Certificate PassPhrase")?></label>
    <?php 
    echo CHtml::textField('ios_passphrase',getOptionA('ios_passphrase'),array(
      'class'=>'form-control',
    ));
    ?>
  </div>
  
  <div class="form-group">
    <label ><?php echo AddonMobileApp::t("IOS Push Development Certificate")?></label>
    <a id="upload-certificate-dev" href="javascript:;" class="btn btn-default"><?php echo AddonMobileApp::t("Browse")?></a>        
    <?php if (!empty($ios_push_dev_cer)):?>
    <span><?php echo $ios_push_dev_cer?>...</span>
    <?php endif;?>
  </div>
  
  <div class="form-group">
    <label ><?php echo AddonMobileApp::t("IOS Push Production Certificate")?></label>
    <a id="upload-certificate-prod" href="javascript:;" class="btn btn-default"><?php echo AddonMobileApp::t("Browse")?></a> 
    <?php if (!empty($ios_push_prod_cer)):?>
    <span><?php echo $ios_push_prod_cer?>...</span>
    <?php endif;?>
  </div>
  
  <hr/>
        
  <div class="form-group">  
  <?php
echo CHtml::ajaxSubmitButton(
	AddonMobileApp::t('Save Settings'),
	array('ajax/savesettings'),
	array(
		'type'=>'POST',
		'dataType'=>'json',
		'beforeSend'=>'js:function(){
		                 busy(true); 	
		                 $("#save-settings").val("'.AddonMobileApp::t('Processing').'");
		                 $("#save-settings").css({ "pointer-events" : "none" });	                 	                 
		              }
		',
		'complete'=>'js:function(){
		                 busy(false); 		                 
		                 $("#save-settings").val("'.AddonMobileApp::t("Save Settings").'");
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
  
 <?php echo CHtml::endForm(); ?>

</div>