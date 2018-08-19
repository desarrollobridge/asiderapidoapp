
<div class="blue_panel">
  <?php echo t("Tasks")?>
  <?php echo CHtml::textField('calendar','',array(
    'class'=>"lightblue-fields rounded3 medium center"
  ))?>
</div>

<ul id="tabs"> 
 <li class="active"><span>0</span> <?php echo t("Unassigned")?></li>
 <li><span>0</span> <?php echo t("Assigned")?></li>
 <li><span>0</span> <?php echo t("Completed")?></li>
</ul>

<ul id="tab" class="list_row">
 <li class="active task_un_assigned">

    <div class="row">
      <div class="col-md-2 center">
       <div class="tag rounded">P</div>
       <div class="top10"><i class="ion-ios-location"></i></div>
       <div class="top10"><i class="ion-ios-time-outline"></i></div>
      </div> <!--row-->
      
      <div class="col-md-10">      
        <div class="inline customer_name">customer name</div>
        <a href="javascript:;" class="task_status inline orange-button-small rounded">Assign Agent</a>
        
        <p class="task_address top10">Quezon city</p>
        <p class="task_time">07:00 pm</p>        
      </div> <!--row-->
    </div> <!--row-->    
 
 </li>
 <li class="task_assigned">
     tab 2
 </li>
 <li class="task_completed">
     tab 3
 </li>
</ul>