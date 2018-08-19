<?php 
$team_list=Driver::teamList( Driver::getUserType(),Driver::getUserId());
if($team_list){
	 $team_list=Driver::toList($team_list,'team_id','team_name',
	   Driver::t("All Team")
	 );
}
/*dump($team_list);
die();*/
?>
<div class="container-fluid border" id="layout1_top">
<div class="row top">
  <div class="col-md-5 border">
  
    <div class="search-team-wrap">
    <?php echo CHtml::dropDownList('team','',(array)$team_list,array(
     'class'=>"lightblue-fields rounded3"
    ))?>
    <?php echo CHtml::textField('search_map','',array(
      'placeholder'=>Driver::t("Search map"),
      'class'=>"blue-fields rounded3"
    ))?>
    </div> <!--search-team-wrap-->
    
    <div class="back-dashboard">
     <a href="<?php echo Yii::app()->createUrl('/driver')?>">
     <i class="ion-ios-arrow-thin-left"></i> <?php echo Driver::t("Back To Dashboard")?>
     </a>
    </div>
   
  </div> <!--row-->
  
  <div class="col-md-2 border center-top-logo">
    <!--<a class="logo" href="<?php echo Yii::app()->createUrl('/driver')?>">
    <img src="<?php echo Driver::assetsUrl()."/images/logo-small.png";?>">
    </a>-->
    <a href="<?php echo Yii::app()->createUrl('/driver/index',array(
      'lang'=>Yii::app()->language
    ))?>" class="logo">
    <?php
    $website_title=Yii::app()->functions->getOptionAdmin("driver_website_title");
    if(!empty($website_title)){
    	echo $website_title;
    } else echo Driver::t("Driver Back Office");
    ?>
    </a>
  </div>
  
   <div class="col-md-5 border text-right">
   
   <a href="javascript:;" class="nav-menu">
   <i class="ion-android-menu"></i>
   </a>
      
    <a href="javascript:;" class="green-button left rounded add-new-task">
    <?php echo Driver::t("Add New Task")?>
    </a>
    
    <!--<a href="<?php echo Yii::app()->createUrl('/driver/index/agentnew')?>" class="black-button left rounded">
    <?php echo Driver::t("Add New Agent")?>
    </a>-->
    
    <div class="left">
    <ul class="menu">
      <li>
        <!--<a href="javascript:;" class="menu-pop"><i class="ion-grid"></i></a>-->
        
        <a href="<?php echo Yii::app()->createUrl('driver/index/settings')?>" class="menu-popx"><i class="ion-grid"></i></a>
        
        
        <div class="popup_menu nav">
          <div class="row">
            <div class="col-md-6 ">
                <a href="<?php echo Yii::app()->createUrl('/driver')?>">
                  <i class="ion-grid"></i>
                  <p><?php echo Driver::t("Dashboard")?></p>
                </a>
            </div> <!--col-->
            <div class="col-md-6 ">
                <a href="<?php echo Yii::app()->createUrl('/driver/index/tasks')?>">
                  <i class="ion-ios-checkmark"></i>
                  <p><?php echo Driver::t("Tasks")?></p>
                </a>
            </div> <!--col-->            
            
            <div class="col-md-6 ">
                <a href="<?php echo Yii::app()->createUrl('/driver/index/teams')?>">
                  <i class="ion-ios-people"></i>
                  <p><?php echo Driver::t("Teams")?></p>
                </a>
            </div> <!--col-->            
            
            <div class="col-md-6 ">
                <a href="<?php echo Yii::app()->createUrl('/driver/index/agents')?>">
                  <i class="ion-android-contacts"></i>
                  <p><?php echo Driver::t("Drivers")?></p>
                </a>
            </div> <!--col-->            
            
            <?php if ( Driver::getUserType()=="admin"):?>
            <div class="col-md-6 ">
                <a href="<?php echo Yii::app()->createUrl('/driver/index/settings')?>">
                  <i class="ion-gear-b"></i>
                  <p><?php echo Driver::t("Settings")?></p>
                </a>
            </div> <!--col-->            
            <?php endif;?>
            
             
            <div class="col-md-6 ">
                <a href="<?php echo Yii::app()->createUrl('/driver/index/reports')?>">
                  <i class="ion-ios-paper"></i>
                  <p><?php echo Driver::t("Reports")?></p>
                </a>
            </div> <!--col-->    
            
            <div class="col-md-6 ">
                <a href="<?php echo Yii::app()->createUrl('/driver/index/pushlogs')?>">
                  <i class="ion-chatbox-working"></i>
                  <p><?php echo Driver::t("Push")?></p>
                </a>
            </div> <!--col-->    
            
            <div class="col-md-6 ">
                <a href="<?php echo Yii::app()->createUrl('/driver/index/assignment')?>">
                  <i class="ion-android-car"></i>
                  <p><?php echo Driver::t("Assignment")?></p>
                </a>
            </div> <!--col-->    
            
            <div class="col-md-6 ">
                <a href="<?php echo Yii::app()->createUrl('/driver/index/teams')?>">
                  <i class="ion-more"></i>
                  <p><?php echo Driver::t("More")?>...</p>
                </a>
            </div> <!--col-->    
            
          </div><!-- row-->
        </div> <!--popup_menu-->
        
      </li>
      <li class="li-hide">
        <a href="javascript:;" class="menu-sound"><i class="ion-volume-high"></i></a>
      </li>
      <li class="li-hide">
         <a href="javascript:;" class="menu-notification"><i class="ion-ios-bell-outline"></i></a>
         
         <div class="popup_menu notification">
           <ul id="notification_list">
           </ul>
         </div>
         
      </li>     
      
      <li>
        <a href="<?php echo Yii::app()->createUrl('driver/index/logout')?>" 
        title="<?php echo Driver::t("logout")?>" ><i class="ion-log-out"></i></a>
      </li>
       
    </ul>
    </div>
   
  </div> <!--row-->
</div> <!--row-->
</div>