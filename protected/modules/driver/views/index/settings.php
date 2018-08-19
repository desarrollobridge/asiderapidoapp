
<div id="layout_1">
<?php 
$this->renderPartial('/tpl/layout1_top',array(   
));
?> 
</div> <!--layout_1-->

<div class="parent-wrapper">

 <div class="content_1 white">   
   <?php 
   $this->renderPartial('/tpl/menu',array(   
   ));
   ?>
 </div> <!--content_1-->
 
 <div class="content_main settings-page">

   <div class="nav_option">
      <div class="row">
        <div class="col-md-6 ">
         <b><?php echo Driver::t("Settings")?></b>
        </div> <!--col-->
        <div class="col-md-6  text-right">
            
         <!--  <a class="green-button left rounded" href="javascript:;"><?php echo Driver::t("Add Task")?></a>
           <a class="orange-button left rounded" href="javascript:;"><?php echo Driver::t("Refresh")?></a>-->
         
        </div> <!--col-->
      </div> <!--row-->
   </div> <!--nav_option-->
  
   <div class="inner">
   
   <ul id="tabs">
	 <li class="active"><?php echo Driver::t("General Settings")?></li>
	 <li><?php echo Driver::t("iOS Settings")?></li>	 
	 <li><?php echo Driver::t("Cron Jobs")?></li>	 
	 <li><?php echo Driver::t("Update Database")?></li>	 
	</ul>
	
   <ul id="tab">  	
	
   <li class="active top30">
   
    <form id="frm" class="frm form-horizontal">
	 <?php echo CHtml::hiddenField('action','generalSettings')?>
	 
	 
	 <h4 style="font-weight:600;"><?php echo Driver::t("General Settings")?></h4>
	 
	 <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Website title")?></label>
	    <div class="col-sm-6">
	     <?php echo CHtml::textField('driver_website_title',
	      getOptionA('driver_website_title')
	      ,array(
	       'class'=>"form-control"	       
	      ))?>	    
	    </div>
	  </div>
	 
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Your mobile API URL")?></label>
	    <div class="col-sm-6">
	     <span class="tag rounded"><?php echo websiteUrl()."/driver/api" ?></span>
	     <p class="text-muted">
	     <?php echo Driver::t("Set this url on your mobile app config files on")?> www/js/config.js
	     </p>
	    </div>
	  </div>
	 
	  <div class="form-group">	    
	  
	   <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("API Hash Key")?></label>
	    <div class="col-sm-6">
	      <?php echo CHtml::textField('driver_api_hash_key',
	      getOptionA('driver_api_hash_key')
	      ,array(
	       'class'=>"form-control"	       
	      ))?>	    
	    
	    <p class="top5 text-muted">
	    <?php echo Driver::t("Make your mobile api secure by putting hash key it can be a unique string without space")?>.<br/>
	    <?php echo Driver::t("Make sure you put the same key in your www/js/config.js")?>
	    </p>
	    </div>
	  </div>
	  	  
	  <label class="col-sm-2 control-label"><?php echo Driver::t("Google Api Key")?></label>
	    <div class="col-sm-6">
	      <?php echo CHtml::textField('drv_google_api',
	      getOptionA('drv_google_api')
	      ,array(
	       'class'=>"form-control"	       
	      ))?>
	    <p class="top5 text-muted"><?php echo Driver::t("Enabled Google Maps Distance Matrix API, Google Maps Geocoding API and Google Maps JavaScript API in your google developer account")?>.</p>
	    <p class="top5 text-muted">
	     <?php echo Driver::t("When creating api key make sure its server key")?>.
	    </p>
	    </div>
	  </div>
	  
	  
	  <hr/>
	  
	  <h4 style="font-weight:600;"><?php echo Driver::t("Language Settings")?></h4>
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Language")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::dropDownList('applanguage',Yii::app()->language,
	      (array)$language_list
	      ,array(
	        'class'=>"applanguage"
	      ));
	      ?>	      
	    </div>
	  </div>	  
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("App Default Language")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::dropDownList('app_default_language',getOptionA('app_default_language'),
	      (array)$language_list
	      ,array(
	        'class'=>"app_default_language"
	      ));
	      ?>	      
	      <p class="top10 text-muted"><?php echo Driver::t("Force default language")?></p>
	    </div>
	  </div>	  
	  
	  
	  <hr/>
	  
	  <h4 style="font-weight:600;"><?php echo Driver::t("Android Settings")?></h4>
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Push Android Key")?></label>
	    <div class="col-sm-6">
	      <?php	    
	      echo CHtml::textField('driver_push_api_key',getOptionA('driver_push_api_key'),array(
	        'class'=>"form-control",
	      ))
	      ?>	      
	    </div>
	  </div>	  	 
	  
	  
	  <hr/>  
	  
	  <h4 style="font-weight:600;"><?php echo Driver::t("Team Management")?></h4>
	  
	 <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Allow all Admin team to use by merchant")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::checkBox('driver_allowed_team_to_merchant',
	      getOptionA('driver_allowed_team_to_merchant')==1?true:false,array(
	        'class'=>"switch-boostrap driver_allowed_team_to_merchant driver_allowed_team_to_merchant1",
	        'value'=>1,
	      ))
	      ?>	      
	    </div>
	  </div>	  
	  	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Set Certain Merchant to use admin Team")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::checkBox('driver_allowed_team_to_merchant',
	      getOptionA('driver_allowed_team_to_merchant')==2?true:false,array(
	        'class'=>"switch-boostrap driver_allowed_team_to_merchant driver_allowed_team_to_merchant2",
	        'value'=>2
	      ))
	      ?>	     	      
	    </div>
	  </div>	  
	  <p class="small-font" style="margin-bottom:10px;">
	  <?php echo Driver::t("If this is enabled Allow all Admin team to use by merchant will be ignored")?>
	  </p> 
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Choose merchant")?></label>
	    <div class="col-sm-6">
	    <?php echo CHtml::dropDownList('driver_allowed_merchant_list',
	    (array)$selected_merchant,
	    (array) Driver::merchantList()
	    ,array(
	      'class'=>"chosen driver_allowed_merchant_list",
          'multiple'=>true,
          'disabled'=>true
	    ))?>
	    </div>
	  </div>  
	  
	  <hr/>  
	  
	  <h4 style="font-weight:600;"><?php echo Driver::t("Task Management")?></h4>	  
	  
	   <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Task Owner")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::dropDownList('driver_owner_task',
	      getOptionA('driver_owner_task')
	      ,array(
	        'default'=>Driver::t("Respective owner of task - default"),
	        'admin'=>Driver::t("admin"),
	        //'merchant'=>Driver::t("merchant"),
	      ),array(
	        'class'=>"form-control"
	      ))
	      ?>
	      <p class="text-muted top5">
	      <?php echo Driver::t("The owner of the task when merchant accept the order")?>
	      </p>
	    </div>
	  </div>	  
	  
	  	 
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Set Merchant task owner to admin")?></label>
	    <div class="col-sm-6">
	    
	    <?php echo CHtml::dropDownList('driver_merchant_task_to_admin',
	    (array)$selected_merchant_to_admin,
	    (array) Driver::merchantList()
	    ,array(
	      'class'=>"chosen driver_merchant_task_to_admin",
          'multiple'=>true,          
	    ))?>
	    
	    <p class="text-muted top5">
	      <?php echo Driver::t("Merchant list that admin will receive the task")?>
	      </p>
	    
	    </div>	    
	  </div>  
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Admin user show only admin task")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::checkBox('driver_show_admin_only_task',
	      getOptionA('driver_show_admin_only_task')==1?true:false,array(
	        'class'=>"switch-boostrap",
	        'value'=>1,
	      ))
	      ?>	
	      <p class="text-muted top5">
	      <?php echo Driver::t("Show only task that belongs to admin user")?>
	      </p>      
	    </div>
	  </div>	  
	  
	   <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Do Not allow merchant to delete the task")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::checkBox('driver_donot_allow_delete_task',
	      getOptionA('driver_donot_allow_delete_task')==1?true:false,array(
	        'class'=>"switch-boostrap",
	        'value'=>1,
	      ))
	      ?>	      
	    </div>
	  </div>	  
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("No.s of days allowed for merchant to delete the task")?></label>
	    <div class="col-sm-2">
	      <?php
	      echo CHtml::textField('driver_allowed_days_delete_task',
	      getOptionA('driver_allowed_days_delete_task')
	      ,array(
	       'class'=>"form-control numeric_only",	       
	      ))
	      ?>	  
	      <p class="text-muted top5">
	      <?php echo Driver::t("Nos. of days After task was created")?>
	      </p>    
	    </div>
	  </div>	  	  
	  
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Block merchant")?></label>
	    <div class="col-sm-6">
	    
	    <?php echo CHtml::dropDownList('driver_merchant_block',
	    (array)$driver_merchant_block,
	    (array) Driver::merchantList()
	    ,array(
	      'class'=>"chosen",
          'multiple'=>true,          
	    ))?>
	    
	    <p class="text-muted top5">
	      <?php echo Driver::t("List of merchant that cannot access driver panel")?>
	      </p>
	    
	    </div>	    
	  </div>  
	  
	  <hr/>
	  	  
	  <h4 style="font-weight:600;"><?php echo Driver::t("Order Settings")?></h4>	
	  
	    <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Order Status Accepted")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::dropDownList('drv_order_status',getOptionA('drv_order_status'),
	      (array)$order_status_list,array(
	        'class'=>"form-control"
	      ))
	      ?>
	      <p class="text-muted top5">
	      <?php echo Driver::t("The order status that will based to insert the order as task")?>
	      </p>
	    </div>
	  </div>	  
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Order Status Cancel")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::dropDownList('drv_order_cancel',getOptionA('drv_order_cancel'),
	      (array)$order_status_list,array(
	        'class'=>"form-control"
	      ))
	      ?>
	      <p class="text-muted top5">
	      <?php echo Driver::t("The order status when merchant cancel the order")?>
	      </p>
	    </div>
	  </div>	  
	  
 <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Delivery Time")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::dropDownList('drv_delivery_time',
	      getOptionA('drv_delivery_time'),	      
	      Driver::deliveryTimeOption()
	      ,array(
	        'class'=>"form-control"
	      ))
	      ?>
	      <p class="text-muted top5">	      
	      </p>
	    </div>
	  </div>	  	    
	  
	  
	  <hr/>
	  	  
	  <h4 style="font-weight:600;"><?php echo Driver::t("App Settings")?></h4>	  
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("App Name")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::textField('driver_app_name',getOptionA('driver_app_name'),array(
	        'class'=>"form-control"
	      ));
	      ?>		      
	    </div>
	  </div>	  	 
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Send Push only to online driver")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::checkBox('driver_send_push_to_online',
	      getOptionA('driver_send_push_to_online')==1?true:false,array(
	        'class'=>"switch-boostrap"
	      ))
	      ?>	
	      <p class="text-muted top5">
	      <?php echo Driver::t("Send push notification only to online drivers when assigning task")?>.
	      </p>      
	    </div>
	  </div>	  	 
	  
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Enabled Notes")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::checkBox('driver_enabled_notes',
	      getOptionA('driver_enabled_notes')==1?true:false,array(
	        'class'=>"switch-boostrap",
	        'value'=>1,
	      ))
	      ?>	      
	    </div>
	  </div>	  
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Enabled Signature")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::checkBox('driver_enabled_signature',
	      getOptionA('driver_enabled_signature')==1?true:false,array(
	        'class'=>"switch-boostrap",
	        'value'=>1,
	      ))
	      ?>	      
	    </div>
	  </div>	  
	  
	   <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Mandatory Signature")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::checkBox('driver_mandatory_signature',
	      getOptionA('driver_mandatory_signature')==1?true:false,array(
	        'class'=>"switch-boostrap",
	        'value'=>1,
	      ))
	      ?>	      
	    </div>
	  </div>	  
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Enabled Signup")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::checkBox('driver_enabled_signup',
	      getOptionA('driver_enabled_signup')==1?true:false,array(
	        'class'=>"switch-boostrap",
	        'value'=>1,
	      ))
	      ?>	      
	    </div>
	  </div>	  
	  
	   <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Enabled Add Photo/Take Picture")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::checkBox('driver_enabled_addphoto',
	      getOptionA('driver_enabled_addphoto')==1?true:false,array(
	        'class'=>"switch-boostrap",
	        'value'=>1,
	      ))
	      ?>	      
	    </div>
	  </div>	  
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Enabled Resize Picture")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::checkBox('driver_enabled_resize_photo',
	      getOptionA('driver_enabled_resize_photo')==1?true:false,array(
	        'class'=>"switch-boostrap",
	        'value'=>1,
	      ))
	      ?>	      
	    </div>
	  </div>	   
	  
	   <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Resize picture")?></label>
	    <div class="col-sm-2">
	      <?php
	      echo CHtml::textField('photo_resize_width',
	       getOptionA('photo_resize_width'),array(
	         'placeholder'=>Driver::t("Width"),
	         'class'=>"numeric_only"
	       ))?>	
	      <p class="text-muted top5"><?php echo Driver::t("resize picture during taking picture in the app")?></p>      
	    </div>
	    <div class="col-sm-2">
	      <?php
	      echo CHtml::textField('photo_resize_height',
	       getOptionA('photo_resize_height'),array(
	         'placeholder'=>Driver::t("Height"),
	         'class'=>"numeric_only"
	       ))?>	      
	    </div>	    
	  </div>	
	  
	  
	   <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Device Vibration")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::textField('vibrate_interval',
	      getOptionA('vibrate_interval'),array(
	        'class'=>"form-control numeric_only",
	        'placeholder'=>t("Milliseconds to vibrate the device")
	      ))
	      ?>	
	      <p class="text-muted top10"><?php echo Driver::t("Default is 3000 Vibrate for 3 seconds")?></p>      	      
	    </div>
	  </div>	 
	  
	  	  
	  <hr/>
	  
	  <h4 style="font-weight:600;"><?php echo Driver::t("Driver Signup Settings")?></h4>	  
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Set Signup Status")?></label>
	    <div class="col-sm-6">
	      <?php 
	        echo CHtml::dropDownList('driver_signup_status',
	        getOptionA('driver_signup_status')
	        ,(array)Driver::driverStatus(),array(	         
	        ));
	        ?>
	      <p class="text-muted top5">
	      <?php echo Driver::t("Set the default status of the driver after signup")?>
	      </p>
	    </div>
	  </div>	 
	  
	 <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Signup - Send Notification Email To")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::textField('driver_send_admin_notification_email',
	      getOptionA('driver_send_admin_notification_email'),array(
	        'class'=>"form-control",
	        'placeholder'=>t("Email address that will receive email once there is new signup")
	      ))
	      ?>	
	      <p class="text-muted top10"><?php echo Driver::t("Multiple email must separated by comma")?></p>      
	    </div>
	  </div>	  
	  
	  <hr/>
	  
	  <h4 style="font-weight:600;"><?php echo Driver::t("Localize Calendar")?></h4>	  
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Language")?></label>
	    <div class="col-sm-6">
	     <?php
	     echo CHtml::dropDownList('driver_calendar_language',getOptionA('driver_calendar_language'),
	     Driver::calendarLocalLang(),array(
	      'class'=>"form-control"
	     ))
	     ?>	    
	    </div>
	  </div>	  
	  
	  
	  <hr/>
	  
	  <h4 style="font-weight:600;"><?php echo Driver::t("Driver Tracking Options")?></h4>	  
	  <p class="text-muted top5" style="margin-bottom:15px;">
	  <?php echo Driver::t("Determine the driver online and offline status")?>
	  </p>
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Tracking Options 1")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::radioButton('driver_tracking_options',
	      getOptionA('driver_tracking_options')==1?true:false
	      ,array(
	        'value'=>1,
	      ))
	      ?>	      
	      <p class="text-muted top5">
	      <?php echo Driver::t("This options will set the driver online when the device sents location to server")?>
	      </p>
	    </div>
	  </div>	   
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Tracking Options 2")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::radioButton('driver_tracking_options',
	      getOptionA('driver_tracking_options')==2?true:false
	      ,array(
	        'value'=>2,
	      ))
	      ?>	      
	      <p class="text-muted top5">
	      <?php echo Driver::t("This options will set the driver only offline when they logout to the app or set to off duty and idle for more than 30min")?>
	      </p>
	    </div>
	  </div>	    
	  
	 <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Records Driver Location")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::checkBox('driver_record_track_Location',
	      getOptionA('driver_record_track_Location')==1?true:false
	      ,array(
	        'value'=>1,
	        'class'=>"switch-boostrap"
	      ))
	      ?>	      
	      <p class="text-muted top5">
	      <?php echo Driver::t("this will save driver locations for later review")?>
	      </p>
	    </div>
	  </div>	  
	  
	   <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Disabled Background Tracking")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::checkBox('driver_disabled_tracking_bg',
	      getOptionA('driver_disabled_tracking_bg')==1?true:false
	      ,array(
	        'value'=>1,
	        'class'=>"switch-boostrap"
	      ))
	      ?>	      
	      <p class="text-muted top5">
	      <?php echo Driver::t("this options will not track your agents when the app is running in background")?>
	      </p>
	    </div>
	  </div>	  	  
	   
	 <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Track Interval")?></label>
	    <div class="col-sm-2">
	      <?php
	      echo CHtml::textField('driver_track_interval',
	      getOptionA('driver_track_interval')
	      ,array(
	       'class'=>"form-control numeric_only"
	      ))
	      ?>	
	      <p class="text-muted top5"><?php echo Driver::t("in seconds, Default is 8 seconds")?></p>	            
	    </div>
	  </div>	     
	 
	   <hr/>
	  
	  <h4 style="font-weight:600;"><?php echo Driver::t("Task Critical Options")?></h4>	  
	  <p class="text-muted top5" style="margin-bottom:15px;">
	  <?php echo Driver::t("Set critical background color to the task when its unassigned after a set of minutes")?>
	  </p>
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Enabled")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::checkBox('enabled_critical_task',
	      getOptionA('enabled_critical_task')==1?true:false,array(
	        'class'=>"switch-boostrap"
	      ))
	      ?>	      
	    </div>
	  </div>	    
	 
	  
	 <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Minutes")?></label>
	    <div class="col-sm-2">
	      <?php
	      echo CHtml::textField('critical_minutes',
	      getOptionA('critical_minutes')
	      ,array(
	       'class'=>"form-control numeric_only"
	      ))
	      ?>
	      <p class="text-muted top5"><?php echo Driver::t("Default is 5 minutes")?></p>	      
	    </div>
	  </div>	     
	  
	  
	  <hr/>
	  
	  <h4 style="font-weight:600;"><?php echo Driver::t("Map Settings")?></h4>	  
	  
	   <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Default Map Country")?></label>
	    <div class="col-sm-6">	      
	      <?php
	      $drv_default_location=getOptionA('drv_default_location');
	      echo CHtml::dropDownList('drv_default_location',
	      !empty($drv_default_location)?$drv_default_location:"US",
	      (array)$country_list,array(
	        'class'=>"form-control"
	      ))
	      ?>
	      <p class="text-muted top5">
	      <?php echo Driver::t("Set the default country to your map")?>
	      </p>
	    </div>
	  </div>	  
	  
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Include offline driver on map")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::checkBox('driver_include_offline_driver_map',
	      getOptionA('driver_include_offline_driver_map')==1?true:false,array(
	        'class'=>"switch-boostrap"
	      ))
	      ?>	      
	    </div>
	  </div>	  
	
 
	   <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Hide Pickup Task")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::checkBox('driver_hide_pickup_task',
	      getOptionA('driver_hide_pickup_task')==1?true:false,array(
	        'class'=>"switch-boostrap"
	      ))
	      ?>	      
	    </div>
	  </div>	  
	  
	   <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Hide Delivery Task")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::checkBox('driver_hide_delivery_task',
	      getOptionA('driver_hide_delivery_task')==1?true:false,array(
	        'class'=>"switch-boostrap"
	      ))
	      ?>	      
	    </div>
	  </div>	  	  
	  
	   <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Hide Successful Task")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::checkBox('driver_hide_successful_task',
	      getOptionA('driver_hide_successful_task')==1?true:false,array(
	        'class'=>"switch-boostrap"
	      ))
	      ?>	      
	    </div>
	  </div>	 
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Disabled Map Auto Refresh")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::checkBox('driver_disabled_auto_refresh',
	      getOptionA('driver_disabled_auto_refresh')==1?true:false,array(
	        'class'=>"switch-boostrap"
	      ))
	      ?>	      
	    </div>
	  </div>	  	  	  	
	  	  
	    <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Map Style")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::textArea('drv_map_style',getOptionA('drv_map_style'),array(
	         'class'=>"form-control",
	         'style'=>"height:250px;"
	      ))
	      ?>
	      <p class="text-muted top5">
	      <?php echo Driver::t("Set the style of your map")?>.
	      <?php echo Driver::t("get it on")?> <a target="_blank" href="https://snazzymaps.com">https://snazzymaps.com</a>
	      <br/>
	      <?php echo Driver::t("leave it empty if if you are unsure")?>.
	      </p>
	    </div>
	  </div>	  	  
  	
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"></label>
	    <div class="col-sm-6">
		  <button type="submit" class="orange-button medium rounded">
		  <?php echo Driver::t("Save")?>
		  </button>
	    </div>	 
	  </div>
	  
     </form>		 
    </li> 
    
    <li> <!--START IOS-->
      <div class="inner">
      
      <form id="frm-ios" class="frm-ios form-horizontal" onsubmit="return false;">
	 <?php echo CHtml::hiddenField('action','saveIOSSettings')?>
	 <?php echo CHtml::hiddenField('driver_ios_push_dev_cer',$ios_push_dev_cer)?>
	 <?php echo CHtml::hiddenField('driver_ios_push_prod_cer',$ios_push_prod_cer)?>
      
      <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("IOS Push Mode")?></label>
	    <div class="col-sm-6">
	     <?php
	     echo CHtml::dropDownList('driver_ios_push_mode',getOptionA('driver_ios_push_mode'),array(
	       'development'=>Driver::t("Development"),
	       'production'=>Driver::t("Production"),
	     ),array(
	      'class'=>"form-control"
	     ))
	     ?>	    
	    </div>
	  </div>	  
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("IOS Push Certificate PassPhrase")?></label>
	    <div class="col-sm-6">
	     <?php
	     echo CHtml::textField('driver_ios_pass_phrase', getOptionA('driver_ios_pass_phrase'),array(
	       'class'=>"form-control",
	       'data-validation'=>"required"
	     ))
	     ?>	    
	    </div>
	  </div>
	  
	  <div class="form-group">
	    <label  class="col-sm-3 control-label" ><?php echo Driver::t("IOS Push Development Certificate")?></label>
	    <a id="upload-certificate-dev" href="javascript:;" class="btn btn-default"><?php echo Driver::t("Browse")?></a>        
	    <?php if (!empty($ios_push_dev_cer)):?>
	    <span><?php echo $ios_push_dev_cer?>...</span>
	    <?php endif;?>
	  </div>
	  
	   <div class="form-group">
	    <label  class="col-sm-3 control-label" ><?php echo Driver::t("IOS Push Production Certificate")?></label>
	    <a id="upload-certificate-prod" href="javascript:;" class="btn btn-default"><?php echo Driver::t("Browse")?></a> 
	    <?php if (!empty($ios_push_prod_cer)):?>
	    <span><?php echo $ios_push_prod_cer?>...</span>
	    <?php endif;?>
	  </div>
	  
	  
	   <div class="form-group">
	    <label class="col-sm-2 control-label"></label>
	    <div class="col-sm-6">
		  <button type="submit" class="orange-button medium rounded">
		  <?php echo Driver::t("Save")?>
		  </button>
	    </div>	 
	  </div>
	  
	  </form>  
      
      </div> <!--inner-->
    </li> <!--END IOS-->
    
    <li>
     <div class="inner">
     <h4><?php echo Driver::t("Run the following cron jobs link in your cpanel")?></h4>     
     <p>
     <a href="<?php echo Yii::app()->getBaseUrl(true)."/driver/cron/processpush"?>" target="_blank">
     <?php echo Yii::app()->getBaseUrl(true)."/driver/cron/processpush"?>
     </a>
     </p>
     
     <p>
     <a href="<?php echo Yii::app()->getBaseUrl(true)."/driver/cron/autoassign"?>" target="_blank">
     <?php echo Yii::app()->getBaseUrl(true)."/driver/cron/autoassign"?>
     </a>
     </p>
     
     <p>
     <a href="<?php echo Yii::app()->getBaseUrl(true)."/driver/cron/processautoassign"?>" target="_blank">
     <?php echo Yii::app()->getBaseUrl(true)."/driver/cron/processautoassign"?>
     </a>
     </p>
     
     <p>
     <a href="<?php echo Yii::app()->getBaseUrl(true)."/driver/cron/checkautoassign"?>" target="_blank">
     <?php echo Yii::app()->getBaseUrl(true)."/driver/cron/checkautoassign"?>
     </a>
     </p>
     
     <p>
     <a href="<?php echo Yii::app()->getBaseUrl(true)."/driver/cron/processbulk"?>" target="_blank">
     <?php echo Yii::app()->getBaseUrl(true)."/driver/cron/processbulk"?>
     </a>
     </p>
     
     <p>
     <a href="<?php echo Yii::app()->getBaseUrl(true)."/driver/cron/clearagenttracking"?>" target="_blank">
     <?php echo Yii::app()->getBaseUrl(true)."/driver/cron/clearagenttracking"?>
     </a>
     </p>
     
     <p>
      <b><?php echo Driver::t("example")?>: curl <?php echo Yii::app()->getBaseUrl(true)."/driver/cron/processpush"?></b>
     </p>
     
     <p>
     <?php echo Driver::t("Video tutorial")?>
     <a href="https://youtu.be/WjndBGKXF7A" target="_blank">https://youtu.be/WjndBGKXF7A</a>
     </p>
     
     </div>
    </li>
    
    <li>
    <div class="inner">
    <h4><?php echo Driver::t("Click below to update your database")?></h4>     
    
    <a href="<?php echo Yii::app()->getBaseUrl(true)."/driver/update"?>" target="_blank">
    <?php echo Yii::app()->getBaseUrl(true)."/driver/update"?>
    </a>
    
    </div>
    </li>
   
   </div> <!--inner-->
 
 </div> <!--content_2-->

</div> <!--parent-wrapper-->