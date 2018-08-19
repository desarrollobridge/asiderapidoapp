
<?php 
if($team_list= Driver::teamList( Driver::getUserType(),Driver::getUserId() ) ){
   $team_list=Driver::toList($team_list,'team_id','team_name',
   Driver::t("Please select a team from a list") );
}
?>

<div class="modal fade new-agent" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
    
      <div class="modal-header">
         <button aria-label="Close" data-dismiss="modal" class="close" type="button">
           <span aria-hidden="true"><i class="ion-android-close"></i></span>
         </button> 
        <h4 id="mySmallModalLabel" class="modal-title">
        <?php echo t("Add Driver")?>
        </h4> 
      </div>  
      
      <div class="modal-body">
      
      <form id="frm" class="frm" method="POST" onsubmit="return false;">
      <?php echo CHtml::hiddenField('action','addAgent')?>
      <?php echo CHtml::hiddenField('id','')?>
      <div class="inner">
      
        <!--<div class="row">
            <div class="col-md-6"></div>
            <div class="col-md-6" style="text-align: right;">
               <p class="small"><?php echo t("Upload profile photo")?></p>
               <a id="upload-driver-photo" href="javascript:;" class="btn btn-default"><?php echo t("Browse")?></a>
            </div>
        </div>-->
      
        <div class="row top10">
          <div class="col-md-6 ">
            <?php echo CHtml::textField('first_name','',array(
              'placeholder'=>t("First Name"),
              'required'=>true
            ))?>
          </div>
          <div class="col-md-6 ">
            <?php echo CHtml::textField('last_name','',array(
              'placeholder'=>t("Last Name"),
               'required'=>true
            ))?>
          </div>
        </div> <!--row-->        
        
        <div class="row top10">
          <div class="col-md-6 ">
            <?php echo CHtml::textField('email','',array(
              'placeholder'=>t("Email"),
              //'data-validation'=>'email'
             // 'required'=>true
            ))?>
          </div>
          <div class="col-md-6 ">
            <?php echo CHtml::textField('phone','',array(
              //'placeholder'=>t("Phone"),
              'class'=>"mobile_inputs",
              'required'=>true,
              'maxlength'=>15
            ))?>
          </div>
        </div> <!--row-->        
        
         <div class="row top10">
          <div class="col-md-6 ">
            <?php echo CHtml::textField('username','',array(
              'placeholder'=>t("Username"),
              'required'=>true
            ))?>
          </div>
          <div class="col-md-6 ">
            <?php echo CHtml::passwordField('password','',array(
              'placeholder'=>t("Password"),
              'required'=>true
            ))?>
          </div>
        </div> <!--row-->   
        
        <div class="row top10">
        <div class="col-md-12">
        <p><?php echo t("Assign to Team")?></p>
        <?php 
        echo CHtml::dropDownList('team_id_driver_new','',(array)$team_list,array(
         'class'=>'team_id_driver_new',
          //'required'=>true
        ));
        ?>
        </div>
        </div>
        
        <div class="row top10">
        <div class="col-md-12">
        <p><?php echo t("Transport Type")?></p>
        <?php 
        echo CHtml::dropDownList('transport_type_id','',
        Driver::transportType()
        ,array(
        ));
        ?>
        </div>
        </div>
              
        <div class="transport_option">
        
        <div class="row top10">
         <div class="col-md-12"> 
          <p class="description"><?php echo t("Transport Description (Year,Model)")?></p>
          <?php echo CHtml::textField('transport_description')?>
         </div> 
        </div> <!--row-->
        
        <div class="row top10">
          <div class="col-md-6 ">
            <?php echo CHtml::textField('licence_plate','',array(
              'placeholder'=>t("Licence Plate")
            ))?>
          </div>
          <div class="col-md-6 ">
            <?php echo CHtml::textField('color','',array(
              'placeholder'=>t("Color")
            ))?>
          </div>
        </div> <!--row-->          
        </div> <!--transport_option_1--> 
        
        <div class="row top20">
        <div class="col-md-12">
        <p><?php echo t("Status")?></p>
        <?php 
        echo CHtml::dropDownList('status','',Driver::driverStatus(),array(
         'required'=>true
        ));
        ?>
        </div>
        </div>
         
        
        <div class="row top20">
        <div class="col-md-5 col-md-offset-7">
        <button type="submit" class="orange-button medium rounded"><?php echo t("Submit")?></button>
        <button type="button" data-id=".new-agent" 
            class="close-modal green-button medium rounded"><?php echo t("Cancel")?></button>
        </div>
        </div>        
        
        
      </div> <!--inner-->  
      </form>  
      
      </div> <!--body-->
    
    </div>
  </div>
</div>