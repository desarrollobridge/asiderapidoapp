
<h4><?php echo merchantApp::t("Booking Accept Template")?></h4>

<p class="bg-success inlineblock"><?php echo merchantApp::t("Available tags")?>:
{booking_id} {number_guest} {date_booking} {booking_time} {booking_name}
{mobile} {booking_notes} {remarks}
 </p>

<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo merchantApp::t("Push Title")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textField('push_booking_accepted_title',getOptionA('push_booking_accepted_title'),array(
      'class'=>'form-control',      
    ));
    ?>   
  </div>
</div>

<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo merchantApp::t("Push Content")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textArea('push_booking_accepted_content',getOptionA('push_booking_accepted_content'),array(
      'class'=>'form-control',
      'style'=>"height:200px;"
    ));
    ?>   
  </div>
</div>

<h4><?php echo merchantApp::t("Booking Decline Template")?></h4>


<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo merchantApp::t("Push Title")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textField('push_booking_decline_title',getOptionA('push_booking_decline_title'),array(
      'class'=>'form-control'      
    ));
    ?>   
  </div>
</div>

<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo merchantApp::t("Push Content")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textArea('push_booking_decline_content',getOptionA('push_booking_decline_content'),array(
      'class'=>'form-control',
      'style'=>"height:200px;"   
    ));
    ?>   
  </div>
</div>