
<div id="layout_1">
<?php 
$this->renderPartial('/tpl/layout1_top',array(   
));
?> 
</div> <!--layout_1-->

<div class="parent-wrapper">

 <div class="content_1 white">   
   <?php 
   $this->renderPartial('/tpl/menu',array(   
   ));
   ?>
 </div> <!--content_1-->
 
 <div class="content_main">

   <div class="nav_option">
      <div class="row">
        <div class="col-md-6 ">
         <b><?php echo t("Notifications")?></b>
        </div> <!--col-->
        <div class="col-md-6  text-right">
            
         <!--  <a class="green-button left rounded" href="javascript:;"><?php echo t("Add Task")?></a>
           <a class="orange-button left rounded" href="javascript:;"><?php echo t("Refresh")?></a>-->
         
        </div> <!--col-->
      </div> <!--row-->
   </div> <!--nav_option-->
  
   <div class="inner">
   
   <h4><?php echo t("Pickup Notifications")?></h4>
   
 
    <form id="frm" class="frm form-horizontal">
	 <?php echo CHtml::hiddenField('action','SaveNotification')?>
	    
   <table class="table table-striped">
   <thead>
    <tr>
     <th>Triggers</th>
     <th>MOBILE PUSH</th>
     <th>SMS</th>
     <th>Email</th>
     <th>Actions</th>
    </tr>
   </thead>
   <tbody>
    <tr>
     <td>REQUEST_RECEIVED</td>
     <td><?php echo CHtml::checkBox('pickup_request_receive_push',false,array('class'=>"switch-boostrap"))?></td>
     <td><?php echo CHtml::checkBox('pickup_request_receive_sms',false,array('class'=>"switch-boostrap"))?></td>
     <td><?php echo CHtml::checkBox('pickup_request_receive_email',false,array('class'=>"switch-boostrap"))?></td>
     <td><a href="javascript:;"><i class="ion-edit"></i></a></td>
    </tr>
    
    <tr>
     <td>DRIVER_STARTED</td>
     <td><?php echo CHtml::checkBox('pickup_driver_started_push',false,array('class'=>"switch-boostrap"))?></td>
     <td><?php echo CHtml::checkBox('pickup_driver_started_sms',false,array('class'=>"switch-boostrap"))?></td>
     <td><?php echo CHtml::checkBox('pickup_driver_started_email',false,array('class'=>"switch-boostrap"))?></td>
     <td><a href="javascript:;"><i class="ion-edit"></i></a></td>
    </tr>
    
    <tr>
     <td>DRIVER_ARRIVED</td>
     <td><?php echo CHtml::checkBox('pickup_driver_arrived_push',false,array('class'=>"switch-boostrap"))?></td>
     <td><?php echo CHtml::checkBox('pickup_driver_arrived_sms',false,array('class'=>"switch-boostrap"))?></td>
     <td><?php echo CHtml::checkBox('pickup_driver_arrived_email',false,array('class'=>"switch-boostrap"))?></td>
     <td><a href="javascript:;"><i class="ion-edit"></i></a></td>
    </tr>
    
    <tr>
     <td>SUCCESSFUL</td>
     <td><?php echo CHtml::checkBox('pickup_driver_successful_push',false,array('class'=>"switch-boostrap"))?></td>
     <td><?php echo CHtml::checkBox('pickup_driver_successful_sms',false,array('class'=>"switch-boostrap"))?></td>
     <td><?php echo CHtml::checkBox('pickup_driver_successful_email',false,array('class'=>"switch-boostrap"))?></td>
     <td><a href="javascript:;"><i class="ion-edit"></i></a></td>
    </tr>
    
    <tr>
     <td>FAILED</td>
     <td><?php echo CHtml::checkBox('pickup_driver_failed_push',false,array('class'=>"switch-boostrap"))?></td>
     <td><?php echo CHtml::checkBox('pickup_driver_failed_sms',false,array('class'=>"switch-boostrap"))?></td>
     <td><?php echo CHtml::checkBox('pickup_driver_failed_email',false,array('class'=>"switch-boostrap"))?></td>
     <td><a href="javascript:;"><i class="ion-edit"></i></a></td>
    </tr>
    
   </tbody>
   </table>
      
   
   <div class="top20">&nbsp;</div>
   
   <h4><?php echo t("Delivery Notifications")?></h4>
   
   
 <table class="table table-striped">
   <thead>
    <tr>
     <th>Triggers</th>
     <th>MOBILE PUSH</th>
     <th>SMS</th>
     <th>Email</th>
     <th>Actions</th>
    </tr>
   </thead>
   <tbody>
    <tr>
     <td>REQUEST_RECEIVED</td>
     <td><?php echo CHtml::checkBox('delivery_request_receive_push',false,array('class'=>"switch-boostrap"))?></td>
     <td><?php echo CHtml::checkBox('delivery_request_receive_sms',false,array('class'=>"switch-boostrap"))?></td>
     <td><?php echo CHtml::checkBox('delivery_request_receive_email',false,array('class'=>"switch-boostrap"))?></td>
     <td><a href="javascript:;"><i class="ion-edit"></i></a></td>
    </tr>
    
    <tr>
     <td>DRIVER_STARTED</td>
     <td><?php echo CHtml::checkBox('delivery_driver_started_push',false,array('class'=>"switch-boostrap"))?></td>
     <td><?php echo CHtml::checkBox('delivery_driver_started_sms',false,array('class'=>"switch-boostrap"))?></td>
     <td><?php echo CHtml::checkBox('delivery_driver_started_email',false,array('class'=>"switch-boostrap"))?></td>
     <td><a href="javascript:;"><i class="ion-edit"></i></a></td>
    </tr>
    
    <tr>
     <td>DRIVER_ARRIVED</td>
     <td><?php echo CHtml::checkBox('delivery_driver_arrived_push',false,array('class'=>"switch-boostrap"))?></td>
     <td><?php echo CHtml::checkBox('delivery_driver_arrived_sms',false,array('class'=>"switch-boostrap"))?></td>
     <td><?php echo CHtml::checkBox('delivery_driver_arrived_email',false,array('class'=>"switch-boostrap"))?></td>
     <td><a href="javascript:;"><i class="ion-edit"></i></a></td>
    </tr>
    
    <tr>
     <td>SUCCESSFUL</td>
     <td><?php echo CHtml::checkBox('delivery_driver_successful_push',false,array('class'=>"switch-boostrap"))?></td>
     <td><?php echo CHtml::checkBox('delivery_driver_successful_sms',false,array('class'=>"switch-boostrap"))?></td>
     <td><?php echo CHtml::checkBox('delivery_driver_successful_email',false,array('class'=>"switch-boostrap"))?></td>
     <td><a href="javascript:;"><i class="ion-edit"></i></a></td>
    </tr>
    
    <tr>
     <td>FAILED</td>
     <td><?php echo CHtml::checkBox('delivery_driver_failed_push',false,array('class'=>"switch-boostrap"))?></td>
     <td><?php echo CHtml::checkBox('delivery_driver_failed_sms',false,array('class'=>"switch-boostrap"))?></td>
     <td><?php echo CHtml::checkBox('delivery_driver_failed_email',false,array('class'=>"switch-boostrap"))?></td>
     <td><a href="javascript:;"><i class="ion-edit"></i></a></td>
    </tr>
    
   </tbody>
   </table>   
   
    <div class="top20">&nbsp;</div>
   
     <div class="form-group">
	    <label class="col-sm-2 control-label"></label>
	    <div class="col-sm-6">
		  <button type="submit" class="orange-button medium rounded">
		  <?php echo Driver::t("Save")?>
		  </button>
	    </div>	 
	  </div>
	  
   </form>
    
   </div> <!--inner-->
 
 </div> <!--content_2-->

</div> <!--parent-wrapper-->