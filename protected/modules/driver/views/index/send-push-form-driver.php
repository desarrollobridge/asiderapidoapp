<div class="modal modal-push-form-driver" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
    
      <div class="modal-header">
         <button aria-label="Close" data-dismiss="modal" class="close" type="button">
           <span aria-hidden="true"><i class="ion-android-close"></i></span>
         </button>         
         
           
         <h4 id="mySmallModalLabel" class="modal-title">
        <?php echo Driver::t("Send Push Notification")?> : <span class="push_form_driver_name"></span>
        </h4>         
         
      </div>  
      
      <div class="modal-body">
      <form id="frm_send_push_driver" method="POST" onsubmit="return false;">
      <?php echo CHtml::hiddenField('action','sendPushToDriver')?>
       <?php echo CHtml::hiddenField('push_form_driver_id','',array(
         'class'=>"push_form_driver_id"
       ))?>
       
       <p>
       <?php echo CHtml::textField('push_title','',array(
        'class'=>"form-control",
        'placeholder'=>Driver::t("Push Title"),
        'data-validation'=>"required"
       ))?>
       </p>
       
       <p>
       <?php echo CHtml::textArea('push_message','',array(
        'class'=>"form-control",
        'placeholder'=>Driver::t("Push Message"),
        'data-validation'=>"required"
       ))?>
       </p>
    
       
       <div class="panel-footer top20">       
         <button type="submit" class="orange-button medium rounded">
         <?php echo Driver::t("Submit")?>
         </button>
         
         <button type="button" data-id=".modal-push-form-driver" 
            class="close-modal green-button medium rounded"><?php echo Driver::t("Cancel")?></button>
        </div>
                
       </div> <!--panel-footer-->
       
      </form>
      </div> <!--body-->
    
    </div> <!--modal-content-->
  </div> <!--modal-dialog-->
</div> <!--modal-->            