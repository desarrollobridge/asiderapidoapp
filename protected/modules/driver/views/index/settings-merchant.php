
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
         <b><?php echo t("Settings")?></b>
        </div> <!--col-->
        <div class="col-md-6  text-right">
            
         
        </div> <!--col-->
      </div> <!--row-->
   </div> <!--nav_option-->
  
   <div class="inner">
   
   
   <form id="frm" class="frm form-horizontal">
	 <?php echo CHtml::hiddenField('action','generalSettingsMerchant')?>	 	
	 
	   <h4 style="font-weight:600;"><?php echo Driver::t("Language Settings")?></h4>
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Language")?></label>
	    <div class="col-sm-6">
	      <?php
	      echo CHtml::dropDownList('merchant_applanguage',Yii::app()->language,
	      (array)$language_list
	      ,array(
	        'class'=>"applanguage"
	      ));
	      ?>	      
	    </div>
	  </div>	  
	  
	  <hr>
	  
 <h4 style="font-weight:600;"><?php echo Driver::t("Localize Calendar")?></h4>	  
	  
	  <div class="form-group">
	    <label class="col-sm-2 control-label"><?php echo Driver::t("Language")?></label>
	    <div class="col-sm-6">
	     <?php
	     echo CHtml::dropDownList('merchant_driver_calendar_language',Driver::getOption('merchant_driver_calendar_language'),
	     Driver::calendarLocalLang(),array(
	      'class'=>"form-control"
	     ))
	     ?>	    
	    </div>
	  </div>	  	  
	  
	  <hr>
	  
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
 
 </div> <!--content_2-->

</div> <!--parent-wrapper-->