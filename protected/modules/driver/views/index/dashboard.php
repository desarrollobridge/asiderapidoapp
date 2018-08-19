
<div id="layout_1">
<?php 
$this->renderPartial('/tpl/layout1_top',array(   
));
?> 


<div class="dashboard-work-area">
<div class="dashboard-tab">
  <div class="row" style="margin:0;padding:0;">
    <div class="col-xs-4"><a href="javascript:;" data-id="tab-task" class="tab-a"><?php echo t("Task")?></a></div>
    <div class="col-xs-4"><a href="javascript:;" data-id="tab-map"  class="tab-a active"><?php echo t("Map")?></a></div>
    <div class="col-xs-4"><a href="javascript:;" data-id="tab-agent" class="tab-a"><?php echo t("Driver")?></a></div>
  </div>
</div>


 <div class="content_1">   
    <?php 
	$this->renderPartial('/tpl/task_panel',array(   
	));
	?> 	
 </div> <!--content_1-->
 
 <div class="content_2">
  
  <div id="primary_map" class="primary_map"></div>
 
 </div> <!--content_2-->
 
 <div class="content_3">   
   <?php 
	$this->renderPartial('/tpl/task_pane2',array(   
	));
	?> 
 </div> <!--content_3-->

</div> <!--dashboard-work-area-->


</div> <!--layout_1-->
