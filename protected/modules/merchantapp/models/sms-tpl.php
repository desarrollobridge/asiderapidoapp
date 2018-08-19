<h4><?php echo t("Accept Order SMS Template")?></h4>
<p class="bg-success inlineblock"><?php echo t("Available tags")?>: {customer_name} {order_id} {order_status} {remarks}</p>


<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Content")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textArea('sms_tpl_order_accept_content',getOptionA('sms_tpl_order_accept_content'),array(
      'class'=>'form-control'      
    ));
    ?>   
  </div>
</div>

<hr>

<h4><?php echo t("Denied Order SMS Template")?></h4>
<p class="bg-success inlineblock"><?php echo t("Available tags")?>: {customer_name} {order_id} {order_status} {remarks}</p>


<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Content")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textArea('sms_tpl_order_denied_content',getOptionA('sms_tpl_order_denied_content'),array(
      'class'=>'form-control'      
    ));
    ?>   
  </div>
</div>

<hr>



<h4><?php echo t("Order Change Status SMS Template")?></h4>
<p class="bg-success inlineblock"><?php echo t("Available tags")?>: {customer_name} {order_id} {order_status} {remarks}</p>


<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Content")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textArea('sms_tpl_order_change_content',getOptionA('sms_tpl_order_change_content'),array(
      'class'=>'form-control'      
    ));
    ?>   
  </div>
</div>

<hr>