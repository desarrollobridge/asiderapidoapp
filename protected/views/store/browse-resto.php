<?php
$this->renderPartial('/front/default-header',array(
   'h1'=>t("Browse Restaurant"),
   'sub_text'=>t("choose from your favorite restaurant")
));
?>

<div class="sections section-grey2 section-browse" id="section-browse">
  
 <div class="container">

    <div class="tabs-wrapper">
      <ul id="tabs">
		  <li class="<?php echo $tabs==1?"active":''?> noclick"  >
		    <a href="<?php echo Yii::app()->createUrl('/store/browse')?>">
		    <i class="ion-coffee"></i>
		     <span><?php echo t("Lista de Restaurantes")?></span>
		    </a>
		  </li>
		  <li class="<?php echo $tabs==2?"active":''?> noclick">
		    <a href="<?php echo Yii::app()->createUrl('/store/browse/?tab=2')?>">
		    <i class="ion-pizza"></i>
		     <span><?php echo t("Newest")?></span>
		    </a>
		  </li>
		  <li class="<?php echo $tabs==3?"active":''?> noclick" >
		    <a href="<?php echo Yii::app()->createUrl('/store/browse/?tab=3')?>">
		    <i class="ion-fork"></i>
		      <span><?php echo t("Destacado")?></span>
		    </a>
		  </li>
		  <li class="full-maps nounderline">				  
		    <a href="javascript:;" >
		    <i class="ion-android-globe"></i>    
		     <span><?php echo t("Ver Restaurant en el mapa")?></span>	    
		  </li>
		   </a>
      </ul>		    
      
      <ul id="tab">
          <li class="<?php echo $tabs==1?"active":''?>" >            
            <?php
            if ( $tabs==1):
	            if (is_array($list['list']) && count($list['list'])>=1){
		            $this->renderPartial('/front/browse-list',array(
					   'list'=>$list,
					   'tabs'=>$tabs
					));
	            } else echo '<p class="text-danger">'.t("No restaurant found").'</p>';
            endif;
            ?>
          </li>
          <li class="<?php echo $tabs==2?"active":''?>" >
          <?php          
            if ( $tabs==2):
	            if (is_array($list['list']) && count($list['list'])>=1){
		            $this->renderPartial('/front/browse-list',array(
					   'list'=>$list,
					   'tabs'=>$tabs			   
					));
	            } else echo '<p class="text-danger">'.t("No restaurant found").'</p>';
            endif;
            ?>
          </li>
          <li class="<?php echo $tabs==3?"active":''?>" >
          
          <?php          
            if ( $tabs==3):
	            if (is_array($list['list']) && count($list['list'])>=1){
		            $this->renderPartial('/front/browse-list',array(
					   'list'=>$list,
					   'tabs'=>$tabs
					));
	            } else echo '<p class="text-danger">'.t("No restaurant found").'</p>';
            endif;
            ?>
          
          </li>
          
          <li>
            <div class="full-map-wrapper" >
               <div id="full-map"></div>
               <a href="javascript:;" class="view-full-map green-button"><?php echo t("Ver en pantalla completa")?></a>
            </div> <!--full-map-->
          </li>          
      </ul>     
      
    </div> <!--tabs-wrapper-->
 
 </div><!-- container-->

</div> <!--sections-->