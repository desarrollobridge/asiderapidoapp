<h4><?php echo t("Accept Order Email Template")?></h4>
<p class="bg-success inlineblock"><?php echo t("Available tags")?>: {customer_name} {order_id} {order_status} {remarks} {delivery_time}</p>

<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Subject")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textField('tpl_order_accept_title',getOptionA('tpl_order_accept_title'),array(
      'class'=>'form-control'      
    ));
    ?>   
  </div>
</div>

<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Content")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textArea('tpl_order_accept_content',getOptionA('tpl_order_accept_content'),array(
      'class'=>'form-control editor'      
    ));
    ?>   
  </div>
</div>

<hr>

<h4><?php echo t("Denied Order Email Template")?></h4>
<p class="bg-success inlineblock"><?php echo t("Available tags")?>: {customer_name} {order_id} {order_status} {remarks}</p>

<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Subject")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textField('tpl_order_denied_title',getOptionA('tpl_order_denied_title'),array(
      'class'=>'form-control'      
    ));
    ?>   
  </div>
</div>

<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Content")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textArea('tpl_order_denied_content',getOptionA('tpl_order_denied_content'),array(
      'class'=>'form-control editor'      
    ));
    ?>   
  </div>
</div>

<hr>



<h4><?php echo t("Order Change Status Email Template")?></h4>
<p class="bg-success inlineblock"><?php echo t("Available tags")?>: {customer_name} {order_id} {order_status} {remarks}</p>

<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Subject")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textField('tpl_order_change_title',getOptionA('tpl_order_change_title'),array(
      'class'=>'form-control'      
    ));
    ?>   
  </div>
</div>

<div class="form-group ">
  <label class="col-sm-3 control-label"><?php echo t("Content")?></label>
  <div class="col-sm-8">
    <?php 
    echo CHtml::textArea('tpl_order_change_content',getOptionA('tpl_order_change_content'),array(
      'class'=>'form-control editor'      
    ));
    ?>   
  </div>
</div>

<hr>