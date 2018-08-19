<div class="modal fade task-change-status-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
    
      <div class="modal-header">
         <button aria-label="Close" data-dismiss="modal" class="close" type="button">
           <span aria-hidden="true"><i class="ion-android-close"></i></span>
         </button> 
        <h4 id="mySmallModalLabel" class="modal-title">
        <?php echo t("Task ID")?> : <span class="task-id"></span>
        </h4> 
      </div>  
      
      <div class="modal-body">
      
       <form id="frm_changes_status" class="frm" method="POST" onsubmit="return false;">
       
       <?php echo CHtml::hiddenField('action','changeStatus')?>
       <?php echo CHtml::hiddenField('task_id','',array(
         'class'=>"task_id"
       ))?>
       
        <h5 class="top20"><?php echo Driver::t("Status")?></h5>          
	      <div class="top10 row">
	      <div class="col-md-12 ">
	      <?php 
	      echo CHtml::dropDownList('status','',(array)Driver::statusList(),array(
	        'class'=>"status status_task_change"
	      ))
	      ?>
	      </div>
	      </div>
	      
	   <div class="reason_wrap">
	   <h5 class="top20"><?php echo Driver::t("Reason")?></h5>          
       <div class="top10 row">
       <div class="col-md-12 ">
       <?php 
       echo CHtml::textArea('reason','',array(
       ));
       ?>
       </div>
       </div>
       </div>   
       
       
       <div class="panel-footer top20">       
         <button type="submit" class="orange-button medium rounded">
         <?php echo t("Submit")?>
         </button>
         
         <button type="button" data-id=".task-change-status-modal" 
            class="close-modal green-button medium rounded"><?php echo t("Cancel")?></button>
        </div>
                
       </div> <!--panel-footer-->
       
       </form>

      </div> <!--body-->
    
    </div> <!--modal-content-->
  </div> <!--modal-dialog-->
</div> <!--modal-->            