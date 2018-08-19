<h4><?php echo t("Approve Booking Email Template")?></h4>

<p class="bg-success inlineblock"><?php echo t("Available tags")?>: {merchant_name} {booking_name} {booking_date} {booking_time} {number_of_guest} {booking_id} {remarks} </p>

<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Subject")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textField('tpl_booking_approved_title',getOptionA('tpl_booking_approved_title'),array(
      'class'=>'form-control'      
    ));
    ?>   
  </div>
</div>

<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Content")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textArea('tpl_booking_approved_content',getOptionA('tpl_booking_approved_content'),array(
      'class'=>'form-control editor'      
    ));
    ?>   
  </div>
</div>

<hr>

<h4><?php echo t("Denied Booking Email Template")?></h4>
<p class="bg-success inlineblock"><?php echo t("Available tags")?>: {merchant_name} {booking_name} {booking_date} {booking_time} {number_of_guest} {booking_id} {remarks} </p>

<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Subject")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textField('tpl_booking_denied_title',getOptionA('tpl_booking_denied_title'),array(
      'class'=>'form-control'      
    ));
    ?>   
  </div>
</div>

<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Content")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textArea('tpl_booking_denied_content',getOptionA('tpl_booking_denied_content'),array(
      'class'=>'form-control editor'      
    ));
    ?>   
  </div>
</div>

<hr>

