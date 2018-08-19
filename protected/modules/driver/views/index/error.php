
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

   <div class="inner">
     <p class="alert alert-danger"><?php echo isset($msg)?$msg:'';?></p>
   </div>
 
 </div> <!--content_main-->
 
 </div> <!--content_2-->

</div> <!--parent-wrapper-->