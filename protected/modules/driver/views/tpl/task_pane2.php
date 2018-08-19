

<div class="blue_panel">
   
   <div class="search_agent_wrap">
   <?php echo CHtml::textField('search_agent','',array(
     'placeholder'=>Driver::t("Enter driver name"),
     'class'=>"search_agent"
   ));?>
   <button class="orange-button search_agent_btn" type="button"><i class="ion-ios-search-strong"></i></button>
   <button class="orange-button search_agent_back" type="button"><i class="ion-android-arrow-back"></i></button>
   </div> <!--search_agent_wrap-->
    
   <div class="row">
     <div class="col-xs-6"><?php echo Driver::t("Agent")?></div>
     <div class="col-xs-6 text-right">
     
     <a href="javascript:showAgentSearch();"><i class="ion-ios-search-strong"></i></a>
     <a href="javascript:loadAgentDashboard();"><i class="ion-android-refresh"></i></a>
     <!--<a href="javascript:;"><i class="ion-ios-search"></i></a>-->
     
     </div><!-- col-->
   </div> <!--row-->   
</div> <!--blue_panel-->


<ul id="tabs">
 <li class="active"><span class="agent-active-total" >0</span> <?php echo Driver::t("Active")?></li>
 <li><span class="agent-offline-total" >0</span> <?php echo Driver::t("Offline")?></li>
 <li><span class="agent-total-total">0</span> <?php echo Driver::t("Total")?></li>
</ul>

<ul id="tab" class="list_row">
 <li class="active agent-active">

 </li>
 <li class="agent-offline">

 </li>
 
 <li class="agent-total">

 </li>
</ul>