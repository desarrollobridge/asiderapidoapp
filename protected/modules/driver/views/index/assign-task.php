<div class="modal fade assign-task" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
    
      <div class="modal-header">
         <button aria-label="Close" data-dismiss="modal" class="close" type="button">
           <span aria-hidden="true"><i class="ion-android-close"></i></span>
         </button> 
        <h4 id="mySmallModalLabel" class="modal-title">
        <?php echo Driver::t("Task ID")?> : <span class="task-id"></span>
        </h4> 
      </div>  
      
      <div class="modal-body">
      
      <form id="frm" class="frm" method="POST" onsubmit="return false;">
       <?php echo CHtml::hiddenField('action','assignTask')?>
       <?php echo CHtml::hiddenField('task_id','',array(
         'class'=>"task_id"
       ))?>
       
      <?php 
      $team_list=Driver::teamList( Driver::getUserType(),Driver::getUserId());
      if($team_list){
      	 $team_list=Driver::toList($team_list,'team_id','team_name',
      	   Driver::t("Select a team")
      	 );
      }
      $all_driver=Driver::getAllDriver(
        Driver::getUserType(),Driver::getUserId()
      );   
      
      //dump($all_driver);
      
      ?>      
      <h5 class="top20"><?php echo Driver::t("Select Team")?></h5>          
      <div class="top10 row">
      <div class="col-md-12 ">
      <?php 
      echo CHtml::dropDownList('team_id','', (array)$team_list,array(
        'class'=>"task_team_id"
      ))
      ?>
      </div>
      </div>
      
      <div class="assign-agent-wrap">
        <h5 class="top20"><?php echo Driver::t("Assign Agent")?></h5>
        <div class="top10 row">
        <div class="col-md-12 ">
		 <select name="driver_id" id="driver_id" class="driver_id">
		  <?php if(is_array($all_driver) && count($all_driver)>=1):?>
		    <option value=""><?php echo Driver::t("Select driver")?></option>
		    <?php foreach ($all_driver as $val):?>
		    <option class="<?php echo "team_opion option_".$val['team_id']?>" value="<?php echo $val['driver_id']?>">
		      <?php echo $val['first_name']." ".$val['last_name']?>
		    </option>
		    <?php endforeach;?>
		  <?php endif;?>
		  </select>
		</div> <!--col-->  
        </div> <!--row-->
      </div><!-- assign-agent-wrap-->
      
       
      <div class="panel-footer top20">       
         <button type="submit" class="orange-button medium rounded">
         <?php echo Driver::t("Submit")?>
         </button>
         
         <button type="button" data-id=".assign-task" 
            class="close-modal green-button medium rounded"><?php echo Driver::t("Cancel")?></button>
        </div>
                
       </div> <!--panel-footer-->
        
      </form>
      
      </div> <!--body-->
    
    </div> <!--modal-content-->
  </div> <!--modal-dialog-->
</div> <!--modal-->      