
<?php 
//if($team_list= Driver::teamList( Driver::getUserType(),Driver::getUserId() ) ){
if($team_list= Driver::teamListNormal( Driver::getUserType(),Driver::getUserId() ) ){
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
        <?php echo Driver::t("Add Driver")?>
        </h4> 
      </div>  
      
      <div class="modal-body">
      
      <form id="frm" class="frm" method="POST" onsubmit="return false;">
      <?php echo CHtml::hiddenField('action','addAgent')?>
      <?php echo CHtml::hiddenField('id','')?>
      <?php echo CHtml::hiddenField('profile_photo','')?>
      <div class="inner">
      
      
        <div class="row top10">
          <div class="col-sm-9">
            <?php echo CHtml::textField('first_name','',array(
              'placeholder'=>Driver::t("First Name"),
              'required'=>true
            ))?>
          </div>
          <div class="col-sm-3">
          
             
             <div class="profile-photo" id="upload-driver-photo">
               <p><?php echo Driver::t("Profile Photo")?></p>
             </div>               
          
          </div>
        </div> <!--row-->        
        
        <div class="row top10">
          <div class="col-sm-9">
          <?php echo CHtml::textField('last_name','',array(
              'placeholder'=>Driver::t("Last Name"),
               'required'=>true
            ))?>
          </div>  
          
          <div class="col-sm-3" style="text-align: right;">
            <!--<a id="upload-driver-photo" href="javascript:;" class="btn btn-default">
            <?php echo Driver::t("Profile Photo")?>
            </a>-->
          </div>
        </div>
        
        <div class="row top10">
          <div class="col-sm-6 ">
            <?php echo CHtml::textField('email','',array(
              'placeholder'=>Driver::t("Email"),
              //'data-validation'=>'email'
             // 'required'=>true
            ))?>
          </div>
          <div class="col-sm-6 ">
            <?php echo CHtml::textField('phone','',array(
              //'placeholder'=>t("Phone"),
              'class'=>"mobile_inputs",
              'required'=>true,
              'maxlength'=>15
            ))?>
          </div>
        </div> <!--row-->        
        
         <div class="row top10">
          <div class="col-sm-6 ">
            <?php echo CHtml::textField('username','',array(
              'placeholder'=>Driver::t("Username"),
              'required'=>true
            ))?>
          </div>
          <div class="col-sm-6 ">
            <?php echo CHtml::passwordField('password','',array(
              'placeholder'=>Driver::t("Password"),
              'required'=>true
            ))?>
          </div>
        </div> <!--row-->   
        
        <div class="row top10">
        <div class="col-sm-12">
        <p><?php echo Driver::t("Assign to Team")?></p>
        <?php 
        echo CHtml::dropDownList('team_id_driver_new','',(array)$team_list,array(
         'class'=>'team_id_driver_new',
          //'required'=>true
        ));
        ?>
        </div>
        </div>
        
        <div class="row top10">
        <div class="col-sm-12">
        <p><?php echo Driver::t("Transport Type")?></p>
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
         <div class="col-sm-12"> 
          <p class="description"><?php echo Driver::t("Transport Description (Year,Model)")?></p>
          <?php echo CHtml::textField('transport_description')?>
         </div> 
        </div> <!--row-->
        
        <div class="row top10">
          <div class="col-sm-6 ">
            <?php echo CHtml::textField('licence_plate','',array(
              'placeholder'=>Driver::t("Licence Plate")
            ))?>
          </div>
          <div class="col-sm-6 ">
            <?php echo CHtml::textField('color','',array(
              'placeholder'=>Driver::t("Color")
            ))?>
          </div>
        </div> <!--row-->          
        </div> <!--transport_option_1--> 
        
        <div class="row top20">
        <div class="col-sm-12">
        <p><?php echo Driver::t("Status")?></p>
        <?php 
        echo CHtml::dropDownList('status','',Driver::driverStatus(),array(
         'required'=>true
        ));
        ?>
        </div>
        </div>
         
        
        <div class="row top20">
        <div class="col-sm-5 col-sm-offset-7">
        <button type="submit" class="orange-button medium rounded"><?php echo Driver::t("Submit")?></button>
        <button type="button" data-id=".new-agent" 
            class="close-modal green-button medium rounded"><?php echo Driver::t("Cancel")?></button>
        </div>
        </div>        
        
        
      </div> <!--inner-->  
      </form>  
      
      </div> <!--body-->
    
    </div>
  </div>
</div>