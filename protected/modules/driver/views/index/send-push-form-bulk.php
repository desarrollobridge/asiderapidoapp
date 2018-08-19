<div class="modal send-bulk-push-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
    
      <div class="modal-header">
         <button aria-label="Close" data-dismiss="modal" class="close" type="button">
           <span aria-hidden="true"><i class="ion-android-close"></i></span>
         </button>         
         
         <h4 id="mySmallModalLabel" class="modal-title">
        <?php echo Driver::t("Send Bulk Push")?>
        </h4> 
        
        <!--<p class="text-muted">-->
        <?php //echo t("Note : These push notification will send to all drivers")?>
        <!--</p>-->
        
      </div>  
      
      <div class="modal-body">
      <form id="frm_send_push_bulk" method="POST" onsubmit="return false;">
      <?php echo CHtml::hiddenField('action','sendPushBulk')?>       
       
      
      <p>
      <?php       
       echo CHtml::dropDownList('team_id2','',
       (array)Driver::getTeamList(  Driver::getUserType(), Driver::getUserId())
       ,array(
        'class'=>"form-control"
      ))?>
      </p>
      
       <p>
       <?php echo CHtml::textField('push_title2','',array(
        'class'=>"form-control",
        'placeholder'=>Driver::t("Push Title"),
        'data-validation'=>"required"
       ))?>
       </p>
       
       <p>
       <?php echo CHtml::textArea('push_message2','',array(
        'class'=>"form-control",
        'placeholder'=>Driver::t("Push Message"),
        'data-validation'=>"required"
       ))?>
       </p>
    
       
       <div class="panel-footer top20">       
         <button type="submit" class="orange-button medium rounded">
         <?php echo Driver::t("Submit")?>
         </button>
         
         <button type="button" data-id=".send-bulk-push-modal" 
            class="close-modal green-button medium rounded"><?php echo Driver::t("Cancel")?></button>
        </div>
                
       </div> <!--panel-footer-->
       
      </form>
      </div> <!--body-->
    
    </div> <!--modal-content-->
  </div> <!--modal-dialog-->
</div> <!--modal-->            