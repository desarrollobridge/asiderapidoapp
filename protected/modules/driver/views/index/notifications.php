
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
         <b><?php echo Driver::t("Notifications")?></b>
        </div> <!--col-->
        <div class="col-md-6  text-right">
            
         <!--  <a class="green-button left rounded" href="javascript:;"><?php echo Driver::t("Add Task")?></a>
           <a class="orange-button left rounded" href="javascript:;"><?php echo Driver::t("Refresh")?></a>-->
         
        </div> <!--col-->
      </div> <!--row-->
   </div> <!--nav_option-->
  
   <div class="inner">
   
   <h4><?php echo Driver::t("Pickup Notifications")?></h4>
   
 
  <form id="frm" class="frm form-horizontal">
	 <?php echo CHtml::hiddenField('action','SaveNotification')?>

  <?php 
  $list=Driver::notificationListPickup();  
  ?>  
	 
   <table class="table table-striped">
   <thead>
    <tr>
     <th><?php echo Driver::t("Triggers")?></th>
     <th><?php echo Driver::t("Mobile Push")?></th>
     <th><?php echo Driver::t("SMS")?></th>
     <th><?php echo Driver::t("Email")?></th>
     <th><?php echo Driver::t("Actions")?></th>
    </tr>
   </thead>
   <tbody>
   
   <?php foreach ($list['PICKUP'] as $key=>$val):?>
    <tr>
     <td><?php echo Driver::t($key);?></td>
     <td><?php 
     //echo "PICKUP_".$val[0]."<br/>";
     echo CHtml::checkBox("PICKUP_".$val[0],
     Driver::getOption("PICKUP_".$val[0])==1?true:false
     ,array('class'=>"switch-boostrap"))?></td>
     
     <td><?php echo CHtml::checkBox("PICKUP_".$val[1],
     Driver::getOption("PICKUP_".$val[1])==1?true:false
     ,array('class'=>"switch-boostrap"))?></td>
     
     <td><?php echo CHtml::checkBox("PICKUP_".$val[2],
     Driver::getOption("PICKUP_".$val[2])==1?true:false
     ,array('class'=>"switch-boostrap"))?></td>
     
     <td><a href="javascript:;" class="notification_tpl" data-id="<?php echo "PICKUP_".$key?>">
       <i class="ion-edit"></i>
     </a></td>
     
    </tr>
    <?php endforeach;?>
       
   
   </tbody>
   </table>
      
   
   <?php 
   $list=Driver::notificationListDelivery();  
   ?> 
  
   <div class="top20">&nbsp;</div>
   
   <h4><?php echo Driver::t("Delivery Notifications")?></h4>
   
   <table class="table table-striped">
   <thead>
    <tr>
     <th><?php echo Driver::t("Triggers")?></th>
     <th><?php echo Driver::t("Mobile Push")?></th>
     <th><?php echo Driver::t("SMS")?></th>
     <th><?php echo Driver::t("Email")?></th>
     <th><?php echo Driver::t("Actions")?></th>
    </tr>
   </thead>
   <tbody>
   
   <?php foreach ($list['DELIVERY'] as $key=>$val):?>   
    <tr>
     <td><?php echo Driver::t($key);?></td>
     <td><?php 
     //echo "DELIVERY_".$val[0]."<br/>";
     echo CHtml::checkBox("DELIVERY_".$val[0], 
     Driver::getOption("DELIVERY_".$val[0])==1?true:false
      ,array('class'=>"switch-boostrap"))?></td>
      
     <td><?php echo CHtml::checkBox("DELIVERY_".$val[1],
     Driver::getOption("DELIVERY_".$val[1])==1?true:false
     ,array('class'=>"switch-boostrap"))?></td>
     
     <td><?php echo CHtml::checkBox("DELIVERY_".$val[2],
     Driver::getOption("DELIVERY_".$val[2])==1?true:false
     ,array('class'=>"switch-boostrap"))?></td>
     
     <td><a href="javascript:;" class="notification_tpl" data-id="<?php echo "DELIVERY_".$key?>">
       <i class="ion-edit"></i>
     </a></td>
    </tr>
    <?php endforeach;?>
       
   
   </tbody>
   </table>
   
   <div class="top20">&nbsp;</div>
   
   <h4><?php echo Driver::t("Driver Notifications")?></h4>
   
    <table class="table table-striped">
   <thead>
    <tr>
     <th><?php echo Driver::t("Triggers")?></th>
     <th><?php echo Driver::t("Mobile Push")?></th>
     <th><?php echo Driver::t("SMS")?></th>
     <th><?php echo Driver::t("Email")?></th>
     <th><?php echo Driver::t("Actions")?></th>
    </tr>
   </thead>
   <tbody>
     <tr>
     <td><?php echo Driver::t("ASSIGN_TASK")?></td>
     <td>
     <?php echo CHtml::checkBox('ASSIGN_TASK_PUSH', 
     Driver::getOption("ASSIGN_TASK_PUSH")==1?true:false
      ,array('class'=>"switch-boostrap"))?>
     </td>
     <td>
     <?php echo CHtml::checkBox('ASSIGN_TASK_SMS', 
     Driver::getOption("ASSIGN_TASK_SMS")==1?true:false
      ,array('class'=>"switch-boostrap"))?>
     </td>
     <td>
     <?php echo CHtml::checkBox('ASSIGN_TASK_EMAIL', 
     Driver::getOption("ASSIGN_TASK_EMAIL")==1?true:false
      ,array('class'=>"switch-boostrap"))?>
     </td>
     
     <td><a href="javascript:;" class="notification_tpl" data-id="ASSIGN_TASK"><i class="ion-edit"></i></a></td>
     
     </tr>
     
     <tr>
     <td><?php echo Driver::t("UPDATE_TASK")?></td>
     <td>
     <?php echo CHtml::checkBox('UPDATE_TASK_PUSH', 
     Driver::getOption("UPDATE_TASK_PUSH")==1?true:false
      ,array('class'=>"switch-boostrap"))?>
     </td>
     <td>
     <?php echo CHtml::checkBox('UPDATE_TASK_SMS', 
     Driver::getOption("UPDATE_TASK_SMS")==1?true:false
      ,array('class'=>"switch-boostrap"      
      ))?>
     </td>
     <td>
     <?php echo CHtml::checkBox('UPDATE_TASK_EMAIL', 
     Driver::getOption("UPDATE_TASK_EMAIL")==1?true:false
      ,array('class'=>"switch-boostrap",'disabled'=>true))?>
     </td>
     
     <td><a href="javascript:;" class="notification_tpl" data-id="UPDATE_TASK"><i class="ion-edit"></i></a></td>
     
     </tr>

     <tr>
     <td><?php echo Driver::t("DELETED_TASK")?></td>
     <td>
     <?php echo CHtml::checkBox('CANCEL_TASK_PUSH', 
     Driver::getOption("CANCEL_TASK_PUSH")==1?true:false
      ,array('class'=>"switch-boostrap"))?>
     </td>
     <td>
     <?php echo CHtml::checkBox('CANCEL_TASK_SMS', 
     Driver::getOption("CANCEL_TASK_SMS")==1?true:false
      ,array('class'=>"switch-boostrap"      
      ))?>
     </td>
     <td>
     <?php echo CHtml::checkBox('CANCEL_TASK_EMAIL', 
     Driver::getOption("CANCEL_TASK_EMAIL")==1?true:false
      ,array('class'=>"switch-boostrap",'disabled'=>true))?>
     </td>     
     <td><a href="javascript:;" class="notification_tpl" data-id="CANCEL_TASK"><i class="ion-edit"></i></a></td>     
     </tr>
     
     
     <tr>
     <td><?php echo Driver::t("NEW_ADDED_DRIVER")?></td>
     <td>
     <?php echo CHtml::checkBox('NEW_DRIVER_PUSH', 
     Driver::getOption("NEW_DRIVER_PUSH")==1?true:false
      ,array('class'=>"switch-boostrap",'disabled'=>true))?>
     </td>
     <td>
     <?php echo CHtml::checkBox('NEW_DRIVER_SMS', 
     Driver::getOption("NEW_DRIVER_SMS")==1?true:false
      ,array('class'=>"switch-boostrap"      
      ))?>
     </td>
     <td>
     <?php echo CHtml::checkBox('NEW_DRIVER_EMAIL', 
     Driver::getOption("NEW_DRIVER_EMAIL")==1?true:false
      ,array('class'=>"switch-boostrap"))?>
     </td>     
     <td><a href="javascript:;" class="notification_tpl" data-id="NEW_DRIVER"><i class="ion-edit"></i></a></td>     
     </tr>
     
     <tr>
     <td><?php echo Driver::t("NEW_SIGNUP_WELCOME")?></td>
     <td>
     <?php echo CHtml::checkBox('DRIVER_NEW_SIGNUP_PUSH', 
     Driver::getOption("DRIVER_NEW_SIGNUP_PUSH")==1?true:false
      ,array('class'=>"switch-boostrap",'disabled'=>true))?>
     </td>
     <td>
     <?php echo CHtml::checkBox('DRIVER_NEW_SIGNUP_SMS', 
     Driver::getOption("DRIVER_NEW_SIGNUP_SMS")==1?true:false
      ,array('class'=>"switch-boostrap",'disabled'=>true      
      ))?>
     </td>
     <td>
     <?php echo CHtml::checkBox('DRIVER_NEW_SIGNUP_EMAIL', 
     Driver::getOption("DRIVER_NEW_SIGNUP_EMAIL")==1?true:false
      ,array('class'=>"switch-boostrap"))?>
     </td>     
     <td><a href="javascript:;" class="notification_tpl" data-id="DRIVER_NEW_SIGNUP"><i class="ion-edit"></i></a></td>
     </tr>
     
     <tr>
     <td><?php echo Driver::t("SIGNUP_APPROVED")?></td>
     <td>
     <?php echo CHtml::checkBox('SIGNUP_APPROVED_PUSH', 
     Driver::getOption("SIGNUP_APPROVED_PUSH")==1?true:false
      ,array('class'=>"switch-boostrap",'disabled'=>true))?>
     </td>
     <td>
     <?php echo CHtml::checkBox('SIGNUP_APPROVED_SMS', 
     Driver::getOption("SIGNUP_APPROVED_SMS")==1?true:false
      ,array('class'=>"switch-boostrap"
      ))?>
     </td>
     <td>
     <?php echo CHtml::checkBox('SIGNUP_APPROVED_EMAIL', 
     Driver::getOption("SIGNUP_APPROVED_EMAIL")==1?true:false
      ,array('class'=>"switch-boostrap"))?>
     </td>     
     <td><a href="javascript:;" class="notification_tpl" data-id="SIGNUP_APPROVED"><i class="ion-edit"></i></a></td>
     </tr>
     
      <tr>
     <td><?php echo Driver::t("SIGNUP_DENIED")?></td>
     <td>
     <?php echo CHtml::checkBox('SIGNUP_DENIED_PUSH', 
     Driver::getOption("SIGNUP_DENIED_PUSH")==1?true:false
      ,array('class'=>"switch-boostrap",'disabled'=>true))?>
     </td>
     <td>
     <?php echo CHtml::checkBox('SIGNUP_DENIED_SMS', 
     Driver::getOption("SIGNUP_DENIED_SMS")==1?true:false
      ,array('class'=>"switch-boostrap"
      ))?>
     </td>
     <td>
     <?php echo CHtml::checkBox('SIGNUP_DENIED_EMAIL', 
     Driver::getOption("SIGNUP_DENIED_EMAIL")==1?true:false
      ,array('class'=>"switch-boostrap"))?>
     </td>     
     <td><a href="javascript:;" class="notification_tpl" data-id="SIGNUP_DENIED"><i class="ion-edit"></i></a></td>
     </tr>
     
   </tbody>
   </table>
   
    <div class="top20">&nbsp;</div>
   
   <h4><?php echo Driver::t("Auto assign Notifications")?></h4>
   
     <table class="table table-striped">
   <thead>
    <tr>
     <th><?php echo Driver::t("Triggers")?></th>
     <th><?php echo Driver::t("Mobile Push")?></th>
     <th><?php echo Driver::t("SMS")?></th>
     <th><?php echo Driver::t("Email")?></th>
     <th><?php echo Driver::t("Actions")?></th>
    </tr>
   </thead>
   <tbody>
     <tr>
     <td><?php echo Driver::t("FAILED_AUTO_ASSIGN")?></td>
     <td>
     <?php echo CHtml::checkBox('FAILED_AUTO_ASSIGN_PUSH', 
     Driver::getOption("FAILED_AUTO_ASSIGN_PUSH")==1?true:false
      ,array('class'=>"switch-boostrap",'disabled'=>true))?>
     </td>
     <td>
     <?php echo CHtml::checkBox('FAILED_AUTO_ASSIGN_SMS', 
     Driver::getOption("FAILED_AUTO_ASSIGN_SMS")==1?true:false
      ,array('class'=>"switch-boostrap",'disabled'=>true))?>
     </td>
     <td>
     <?php echo CHtml::checkBox('FAILED_AUTO_ASSIGN_EMAIL', 
     Driver::getOption("FAILED_AUTO_ASSIGN_EMAIL")==1?true:false
      ,array('class'=>"switch-boostrap"))?>
     </td>
     
     <td>
     <a href="javascript:;" class="notification_tpl" data-id="FAILED_AUTO_ASSIGN"><i class="ion-edit"></i></a>
     </td>
     
     </tr>
     
      <tr>
     <td><?php echo Driver::t("AUTO_ASSIGN_ACCEPTED")?></td>
     <td>
     <?php echo CHtml::checkBox('AUTO_ASSIGN_ACCEPTED_PUSH', 
     Driver::getOption("AUTO_ASSIGN_ACCEPTED_PUSH")==1?true:false
      ,array('class'=>"switch-boostrap",'disabled'=>false))?>
     </td>
     <td>
     <?php echo CHtml::checkBox('AUTO_ASSIGN_ACCEPTED_SMS', 
     Driver::getOption("AUTO_ASSIGN_ACCEPTED_SMS")==1?true:false
      ,array('class'=>"switch-boostrap",'disabled'=>true))?>
     </td>
     <td>
     <?php 
     echo CHtml::checkBox('AUTO_ASSIGN_ACCEPTED_EMAIL', 
     Driver::getOption("AUTO_ASSIGN_ACCEPTED_EMAIL")==1?true:false
      ,array('class'=>"switch-boostrap",'disabled'=>true))?>
     </td>
     
     <td><a href="javascript:;" class="notification_tpl" data-id="AUTO_ASSIGN_ACCEPTED"><i class="ion-edit"></i></a></td>
     
     </tr>
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