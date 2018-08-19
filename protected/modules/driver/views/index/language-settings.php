
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
        <div class="col-md-6 border">
         <b><?php echo Driver::t("Language Settings")?></b>
        </div> <!--col-->
        <div class="col-md-6 border text-right">         
        </div> <!--col-->
      </div> <!--row-->
   </div> <!--nav_option-->
  
   <div class="inner">
     
   </div>
 
 </div> <!--content_2-->

</div> <!--parent-wrapper-->


<?php 
$this->renderPartial('/index/agent-new',array(   
));