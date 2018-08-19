
<div class="mobile-banner-wrap relative">
 <div class="layer"></div>
 <img class="mobile-banner" src="<?php echo empty($background)?assetsURL()."/images/b-2-mobile.jpg":uploadURL()."/$background"; ?>">
</div>

<div id="parallax-wrap" class="parallax-search parallax-menu" 
data-parallax="scroll" data-position="top" data-bleed="10" 
data-image-src="<?php echo empty($background)?assetsURL()."/images/b-2.jpg":uploadURL()."/$background"; ?>">

<div class="search-wraps center menu-header" style="color: white !important;">

<table style="width: 100%; margin-top: 110px;">
  <tbody>
    <tr>
      <td><img class="logo-medium bottom15" src="<?php echo $merchant_logo;?>"></td>
      <td>

      
	  <div class="mytable" style="color: white;margin-left: -25px;">
	     <div class="mycol" style="color: white !important;">
	        <div class="rating-stars" data-score="<?php echo $ratings['ratings']?>" style="color: white !important;"></div>   
	     </div>
	     <div class="mycol" style="color: white !important;">
	        <p class="small" style="color: white !important;">
	        <a href="javascript:;"class="goto-reviews-tab" style="color: white !important;">
	        <?php echo $ratings['votes']." ".t("reseñas")?>
	        </a>
	        </p>
	     </div>	        
	     <div class="mycol" style="color: white !important;">
	        <?php echo FunctionsV3::merchantOpenTag($merchant_id)?>             
	     </div>
	     <div class="mycol" style="color: white !important;">
	       <!-- <p class="small" style="color: white !important;"><?php //echo t("Minimum Order").": ".FunctionsV3::prettyPrice($minimum_order)?></p>-->
	     </div>
	   </div> <!--mytable-->

	<h1 style="color: white;text-align: left;margin-left: 35px;"><?php echo clearString($restaurant_name)?></h1>
	
	<?php if(!empty($social_facebook_page) || !empty($social_twitter_page) || !empty($social_google_page)):?>
	<ul class="merchant-social-list" style="color: white !important;">
	 <?php if(!empty($social_google_page)):?>
	 <li>
	   <a href="<?php echo FunctionsV3::prettyUrl($social_google_page)?>" target="_blank">
	    <i class="ion-social-googleplus"></i>
	   </a>
	 </li>
	 <?php endif;?>
	 
	 <?php if(!empty($social_twitter_page)):?>
	 <li>
	   <a href="<?php echo FunctionsV3::prettyUrl($social_twitter_page)?>" target="_blank">
	   <i class="ion-social-twitter"></i>
	   </a>
	 </li>
	 <?php endif;?>
	 
	 <?php if(!empty($social_facebook_page)):?>
	 <li>
	   <a href="<?php echo FunctionsV3::prettyUrl($social_facebook_page)?>" target="_blank">
	   <i class="ion-social-facebook"></i>
	   </a>
	 </li>
	 <?php endif;?>
	 
	</ul>
	<?php endif;?>
	
	<p style="color: white;"><i class="fa fa-map-marker"></i> <?php echo $merchant_address?></p>
	<p class="small" style="color: white !important;"><?php echo FunctionsV3::displayCuisine($cuisine);?></p>
	<p style="color: white !important;"><?php echo FunctionsV3::getFreeDeliveryTag($merchant_id)?></p>
	
	<?php if ( getOption($merchant_id,'merchant_show_time')=="yes"):?>
	<p class="small" style="color: white !important;">
	<?php echo t("Merchant Current Date/Time").": ".
	Yii::app()->functions->translateDate(date('F d l')." ".timeFormat(date('c'),true));?>
	</p>
	<?php endif;?>
	
	<?php if (!empty($merchant_website)):?>
	<p class="small" style="color: white !important;">
	<?php echo t("Website").": "?>
	<a target="_blank" href="<?php echo FunctionsV3::fixedLink($merchant_website)?>">
	  <?php echo $merchant_website;?>
	</a>
	</p>
	<?php endif;?>
			
      </td>
    </tr>
  </tbody>
</table>

      
</div> <!--search-wraps-->

</div> <!--parallax-container-->