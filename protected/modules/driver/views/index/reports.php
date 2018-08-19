
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
         <b><?php echo Driver::t("Reports")?></b>
        </div> <!--col-->
        <div class="col-md-6  text-right">
                     
        </div> <!--col-->
      </div> <!--row-->
   </div> <!--nav_option-->
  
   <div class="inner">
   
    <div class="row">
      <div class="col-md-3">
      
        <?php echo CHtml::hiddenField('chart_type','task_completion')?>
        <?php echo CHtml::hiddenField('chart_type_option','time')?>
      
        <p><?php echo Driver::t("Time") ?></p>
        <?php 
        echo CHtml::dropDownList('time_selection','',array(        
          "week"=>Driver::t("Past Week"),
          "month"=>Driver::t("Past Month"),
          "custom"=>Driver::t("Custom Date"),
        ),array(
         'class'=>"form-control"
        ))?>   
        
        <div class="custom_selection top20">
          <p><?php echo Driver::t("Start Date")?></p>
          <?php echo CHtml::textField('start_date',$start_date,array(
            'class'=>"form-control datetimepicker1"
          ))?>
          <p class="top20"><?php echo Driver::t("End Date")?></p>
          <?php echo CHtml::textField('end_date',$end_date,array(
            'class'=>"form-control datetimepicker1"
          ))?>
        </div> <!--custom_selection-->
        
         <p class="top20"><?php echo Driver::t("Team") ?></p>
          <?php 
         echo CHtml::dropDownList('team_selection','',(array)$team_list,array(
          'class'=>"form-control"
         ))
         ?>
         
         <p class="top20"><?php echo Driver::t("Driver") ?></p>
         <select name="driver_selection" id="driver_selection" class="driver_selection form-control">
		  <?php if(is_array($all_driver) && count($all_driver)>=1):?>
		    <option value="all"><?php echo Driver::t("All Driver")?></option>
		    <?php foreach ($all_driver as $val):?>
		    <option class="<?php echo "team_opion option_".$val['team_id']?>" value="<?php echo $val['driver_id']?>">
		      <?php echo $val['first_name']." ".$val['last_name']?>
		    </option>
		    <?php endforeach;?>
		  <?php endif;?>
		  </select>
         
       <!-- <h4 class="top20"><?php echo Driver::t("Task Performance")?></h4>
        <p>
        <a href="javascript:;" class="view_charts" data-id="task_completion" >
          <?php echo Driver::t("Task Completion")?>
        </a>
        </p>
        <p>
        <a href="javascript:;" class="view_charts" data-id="task_punctuality" >
          <?php echo Driver::t("Task Punctuality")?>
        </a>
        </p>-->
              
      </div> <!--col-->
      <div class="col-md-9">
         <div class="report_div"></div>         
         
         
         <div class="row top30">
         <div class="col-md-3 col-xs-offset-5">
	         <div class="btn-group">
	            <a href="javascript:;" data-id="time" class="btn btn-primary change_charts"><?php echo Driver::t("Time")?></a>
	            <a href="javascript:;" data-id="agent" class="btn btn-primary change_charts"><?php echo Driver::t("Agent")?></a>
	         </div>
	     </div>    
         </div>
         
      </div> <!--col-->
    </div> <!--row-->
    
   <!-- <table class="table top30 table-hover table-striped">
     <thead>
      <tr>
       <th><?php echo Driver::t("Date")?></th>
       <th><?php echo Driver::t("Successful Tasks")?></th>
       <th><?php echo Driver::t("Cancelled Tasks")?></th>
       <th><?php echo Driver::t("Failed Tasks")?></th>
       <th><?php echo Driver::t("Total Tasks")?></th>
      </tr>
     </thead>
    </table>-->
   
    <div class="table_charts"></div>
    
   </div> <!--inner-->
 
 </div> <!--content_2-->

</div> <!--parent-wrapper-->