
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
         <b><?php echo Driver::t("Agents Track Back")?></b>
        </div> <!--col-->        
      </div> <!--row-->
   </div> <!--nav_option-->
   
   
   <div class="inner" style="padding-top:3px;">
   
   <form id="frm" class="frm form-horizontal">
   
    <div class="row">
       <div class="col-md-4">
         <p><?php echo Driver::t("Select driver")?></p>
         <?php echo CHtml::dropDownList('track_driver_id','',(array)$driver_list,array(
           'form-control'
         ))?>
       </div>
       <div class="col-md-4">
         <p><?php echo Driver::t("Select Date")?></p>     
             
         <select name="track_date" id="track_date" disabled >
           <option value="-1"><?php echo Driver::t("Please select")?></option>
           <?php if (is_array($track_list) && count($track_list)>=1):?>
           <?php foreach ($track_list as $val): $date_created=date("M d Y",strtotime($val['date_created']))?>
             <option class="tr_d track_driver_<?php echo $val['driver_id']?>" 
                value="<?php echo date("Y-m-d",strtotime($val['date_created']));?>">
                <?php echo $date_created?>
             </option>
           <?php endforeach;?>
           <?php endif;?>
         </select>
         
       </div>
       
       <div class="col-md-2">
         <a href="javascript:;" class="btn btn-success track_replay"><?php echo Driver::t("Replay")?></a>
       </div>
       
    </div> <!--row-->
        
    <div class="track-map-parent">
      <div id="track-map" class="track-map"></div>
    </div> <!--track-map-parent-->
    
    <div class="track-details-wrap">
      <?php echo Driver::t("Track details logs")?>...      
    </div> <!--track-details-wrap-->
   
   </form> 
    
   </div> <!--inner-->
   
 </div> <!--content_2-->

</div> <!--parent-wrapper-->