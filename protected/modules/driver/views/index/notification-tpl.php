


<div class="modal fade notification-pop" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    
      <div class="modal-header">
         <button aria-label="Close" data-dismiss="modal" class="close" type="button">
           <span aria-hidden="true"><i class="ion-android-close"></i></span>
         </button> 
        <h4 id="mySmallModalLabel" class="modal-title">
        <?php echo Driver::t("Notifications")?> - <span class="option-name"></span>
        </h4> 
      </div>  
      
      <div class="modal-body">
      
      <form id="frm_notification_tpl" class="frm" method="POST" onsubmit="return false;">
         <?php echo CHtml::hiddenField('action','SaveNotificationTemplate')?>
         <?php echo CHtml::hiddenField('option_name','')?>         
         
        <div class="row">
          <div class="col-md-6" >
          
          <div class="panel panel-default">
	         <div class="panel-body">
	            <div class="row">
			    <div class="col-md-6"><?php echo Driver::t("Mobile Push")?></div>
			    <div class="col-md-6"><?php echo CHtml::dropDownList('tag_PUSH','',
			    Driver::tagAvailableList()
			    )?></div>
			    </div>
			    
			    <div class="top20"></div>
			    <?php 
			    echo CHtml::textArea('PUSH','',array(
			      'class'=>'form-control'			      
			    ));
			    ?>
			    
		     </div> <!--panel-body-->
	      </div> <!--panel-->
	      
	      
	      <div class="panel panel-default">
	         <div class="panel-body">
	            <div class="row">
			    <div class="col-md-6"><?php echo Driver::t("SMS")?></div>
			    <div class="col-md-6"><?php echo CHtml::dropDownList('tag_SMS','',
			    Driver::tagAvailableList()
			    )?></div>
			    </div>
			    
			    <div class="top20"></div>
			    <?php 
			    echo CHtml::textArea('SMS','',array(
			      'class'=>'form-control'
			    ));
			    ?>
			    
		     </div> <!--panel-body-->
	      </div> <!--panel-->
          
          </div> <!--col-->
          <div class="col-md-6">
          
           <div class="panel panel-default">
	         <div class="panel-body">
	            <div class="row">
			    <div class="col-md-6"><?php echo Driver::t("Email")?></div>
			    <div class="col-md-6"><?php echo CHtml::dropDownList('tag_EMAIL','',
			    Driver::tagAvailableList()
			    )?></div>
			    </div>
			    
			    <div class="top20"></div>
			    <?php 
			    echo CHtml::textArea('EMAIL','',array(
			      'class'=>'form-control'
			    ));
			    ?>
			    
		     </div> <!--panel-body-->
	      </div> <!--panel-->
          
          </div>
        </div> <!--row-->
        
        <p><?php echo Driver::t("Example on how to use the available tags")?>:<br/>
         <span class="text-danger"><?php echo Driver::t("Hi [CustomerName] your order no: [OrderNo]")?></span>
        </p> 
        
        <div class="row top20">
        <div class="col-md-5 col-md-offset-7">
        <button type="submit" class="orange-button medium rounded"><?php echo Driver::t("Submit")?></button>
        <button type="button" data-id=".notification-pop" 
            class="close-modal green-button medium rounded"><?php echo Driver::t("Cancel")?></button>
        </div>
        </div>
              
      </form>

      </div> <!--body-->
    
    </div>
  </div>
</div>      