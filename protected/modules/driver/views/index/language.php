
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
 
 <div class="content_main language-page">

   <div class="nav_option">
      <div class="row">
        <div class="col-md-6 ">
         <b><?php echo t("Language")?></b>
        </div> <!--col-->
        <div class="col-md-6  text-right">
                  
         
        </div> <!--col-->
      </div> <!--row-->
   </div> <!--nav_option-->
  
   <div class="inner">
      
    <ul id="tabs"> 
	  <?php $x=1;?>
	  <?php foreach ($lang as $lang_code=>$val_lang):?>
	   <li class="<?php echo $x==1?"active":''?>">
	   <a href="#tab-<?php echo $lang_code?>" role="tab" data-toggle="tab"><?php echo ucwords($val_lang)?></a>
	   </li>
	  <?php $x++;?>
	  <?php endforeach;?>
    </ul>
    
   <form id="frm" class="frm form-horizontal">
	 <?php echo CHtml::hiddenField('action','SaveTranslation')?> 
   <ul id="tab" >
	 <?php $x=1;?>
	  <?php foreach ($lang as $lang_code=>$val_lang):?>
	   <li class="<?php echo $x==1?"active":''?>">
	      <div class="inner top20">
	      
	        <?php foreach ($dictionary as $key=>$val):?>     
		     <?php 
		       $value='';
		       $field_name=$key."[$lang_code]";
		       if ( $lang_code=="en"  && !is_array($mobile_dictionary)){
		       	  $value=$val;
		       } else {       	  
		       	  if(isset($mobile_dictionary[$key])){
			       	  $value=$mobile_dictionary[$key][$lang_code];
			       	  if (empty($value)){
			       	  	  $value=$val;
			       	  }
		       	  } else $value=$val;
		       }
		     ?>
		     <div class="form-group">
		       <label class="col-sm-2 control-label"><?php echo $key?></label>
		       <div class="col-sm-10">
		       <?php 
		       echo CHtml::textField($field_name,$value,array(
		         'class'=>"form-control"
		       ));
		       ?>
		       </div>
		     </div>
		     <?php endforeach;?>
	      
	      
	      </div> <!--inner-->
	   </li>
	  <?php $x++;?>
	  <?php endforeach;?>
   </ul>	 
   
    <div class="form-group top20">
	    <label class="col-sm-2 control-label"></label>
	    <div class="col-sm-6">
		  <button type="submit" class="orange-button medium rounded">
		  <?php echo Driver::t("Save")?>
		  </button>
	    </div>	 
	  </div>
	  
   </form>
   
   
   <div class="up-down-wrap">
     <a href="javascript:scroll('#tabs');" ><i class="ion-arrow-up-c"></i></a>
     <a href="javascript:scroll('.orange-button');"><i class="ion-arrow-down-c"></i></a>
   </div>
  
   </div> <!--inner-->
 
 </div> <!--content_2--> 

</div> <!--parent-wrapper-->