
<div class="modal fade create-team" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
    
      <div class="modal-header">
         <button aria-label="Close" data-dismiss="modal" class="close" type="button">
           <span aria-hidden="true"><i class="ion-android-close"></i></span>
         </button> 
        <h4 id="mySmallModalLabel" class="modal-title">
        <?php echo t("Create Team")?>
        </h4> 
      </div>  
      
      <div class="modal-body">
      
      <form id="frm" class="frm" method="POST" onsubmit="return false;">
      <?php echo CHtml::hiddenField('action','createTeam')?>
      <?php echo CHtml::hiddenField('id','')?>
      <div class="inner">
      
        <div class="row">
          <div class="col-md-12">
            <?php echo CHtml::textField('team_name','',array(
              'placeholder'=>Driver::t("Team Name"),
              'required'=>true
            ))?>
          </div>          
        </div> <!--row-->        
           
        <div class="row top20">
        <div class="col-md-12">
        <p><?php echo Driver::t("Set Location Accuracy for Driver app")?></p>
        <?php 
        echo CHtml::dropDownList('location_accuracy','',array(
          ""=>Driver::t("Please select"),
          'low'=>Driver::t("Low"),
          'medium'=>Driver::t("Medium"),
          'high'=>Driver::t("High"),
        ),array(
          'required'=>true
        ));
        ?>
        </div>
        </div>
      
        <?php 
        if($driver_list=Driver::driverList(Driver::getUserType(),Driver::getUserId())){
        	$driver_list=Driver::toList(
        	    $driver_list,
        	    'driver_id',
        	    'first_name'
        	);
        }        
        ?>
        <div class="row top20">
        <div class="col-md-12">
        <p><?php echo Driver::t("Team Members")?></p>
        <?php 
        echo CHtml::dropDownList('team_member[]','',
        (array)$driver_list
        ,array(
          'class'=>"chosen",
          'multiple'=>true
        ));
        ?>
        </div>
        </div>
                
         <div class="row top20">
        <div class="col-md-12">
        <p><?php echo Driver::t("Status")?></p>
        <?php 
        echo CHtml::dropDownList('status','',Driver::statusListPost(),array(
         'required'=>true
        ));
        ?>
        </div>
        </div>
        
        <div class="row top20">
        <div class="col-md-5 col-md-offset-7">
        <button type="submit" class="orange-button medium rounded"><?php echo Driver::t("Submit")?></button>
        <button type="button" data-id=".create-team" 
            class="close-modal green-button medium rounded"><?php echo Driver::t("Cancel")?></button>
        </div>
        </div>        
        
        
      </div> <!--inner-->  
      </form>  
      
      </div> <!--body-->
    
    </div>
  </div>
</div>