
<div id="layout_1">
<?php 
$this->renderPartial('/tpl/layout1_top',array(   
));
?> 
</div> <!--layout_1-->

<div class="parent-wrapper task-list-area">

 <div class="content_1 white">   
   <?php 
   $this->renderPartial('/tpl/menu',array(   
   ));
   ?>
 </div> <!--content_1-->
 
 <div class="content_main">

   <div class="nav_option">
      <div class="row">
        <div class="col-md-6 border">
         <b><?php echo Driver::t("SMS Logs")?></b>
        </div> <!--col-->
        <div class="col-md-6 border text-right">
                       
           <a class="orange-button left rounded" href="javascript:tableReload();"><?php echo Driver::t("Refresh")?></a>
         
        </div> <!--col-->
      </div> <!--row-->
   </div> <!--nav_option-->
  
   <div class="inner">
   
   <form id="frm_table" class="frm_table">
   <?php echo CHtml::hiddenField('action','SSMLogs')?>
   <table id="table_list" class="table table-hover">
   <thead>
    <tr>
      <th width="10%"><?php echo Driver::t("ID")?></th>      
      <th><?php echo Driver::t("Mobile number")?></th>      
      <th><?php echo Driver::t("Message")?></th>      
      <th><?php echo Driver::t("Gateway")?></th> 
      <th><?php echo Driver::t("Status")?></th>      
      <th><?php echo Driver::t("Date")?></th>      
    </tr>
    </thead>
    <tbody>     
    </tbody>     
   </table>
   </form>
   </div>
 
 </div> <!--content_2-->

</div> <!--parent-wrapper-->