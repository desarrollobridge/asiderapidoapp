
<div class="blue_panel">
  <?php echo Driver::t("Tasks")?>
  
  <?php
  $date_now=date('Y-m-d');    
  $date_now_pretty=date("d M Y");
  echo CHtml::textField('calendar',$date_now_pretty,array(
    'class'=>"lightblue-fields rounded3 medium center"
  ));
  
  echo CHtml::hiddenField('calendar_formated',$date_now,array(
    'class'=>'calendar_formated'
  ))
  ?>
</div>

<ul id="tabs"> 
 <li class="active"><span class="task-total-unassigned">0</span> <?php echo Driver::t("Unassigned")?></li>
 <li><span class="task-total-assigned">0</span> <?php echo Driver::t("Assigned")?></li>
 <li><span class="task-total-completed">0</span> <?php echo Driver::t("Completed")?></li>
</ul>

<ul id="tab" class="list_row">
 <li class="active task_unassigned">    
 
 </li>
 <li class="task_assigned">
     
 </li>
 <li class="task_completed">
     
 </li>
</ul>