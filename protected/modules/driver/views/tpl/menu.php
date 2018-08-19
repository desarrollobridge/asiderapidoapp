
<?php
$visible=true;
if ( Driver::getUserType()=="merchant"){
	$visible=false;
}

$menu =  array(  		    		    
    'activeCssClass'=>'active', 
    'encodeLabel'=>false,
    'items'=>array(
    
        array('visible'=>true,'label'=>'<i class="ion-grid"></i>&nbsp; '.Driver::t('Dashboard'),
        'url'=>array('/driver/index'),'linkOptions'=>array()),               
        
        array('visible'=>true,'label'=>'<i class="ion-gear-b"></i>&nbsp; '.Driver::t("Settings"),
        'url'=>array('/driver/index/settings'),'linkOptions'=>array()),       
        
        array('visible'=>true,'label'=>'<i class="ion-android-contacts"></i>&nbsp; '.Driver::t("Teams"),
        'url'=>array('/driver/index/teams'),'linkOptions'=>array()),       
        
        array('visible'=>true,'label'=>'<i class="ion-android-contact"></i>&nbsp; '.Driver::t("Driver"),
        'url'=>array('/driver/index/agents'),'linkOptions'=>array()),       
        
        array('visible'=>true,'label'=>'<i class="ion-android-locate"></i>&nbsp; '.Driver::t("Driver Track Back"),
        'url'=>array('/driver/index/agentstrackback'),'linkOptions'=>array()),       
        
        
        array('visible'=>true,'label'=>'<i class="ion-ios-checkmark"></i>&nbsp; '.Driver::t("Tasks"),
        'url'=>array('/driver/index/tasks'),'linkOptions'=>array()),       
        
        /*array('visible'=>$visible,'label'=>'<i class="ion-flag"></i>&nbsp; '.Driver::t("Language"),
        'url'=>array('/driver/index/language'),'linkOptions'=>array()),        
        */        
        array('visible'=>$visible,'label'=>'<i class="ion-ios-bell"></i>&nbsp; '.Driver::t("Notifications"),
        'url'=>array('/driver/index/notifications'),'linkOptions'=>array()),        
                
        array('visible'=>true,'label'=>'<i class="ion-android-car"></i>&nbsp; '.Driver::t("Assignment"),
        'url'=>array('/driver/index/assignment'),'linkOptions'=>array()),                
        
        array('visible'=>true,'label'=>'<i class="ion-ios-list"></i>&nbsp; '.Driver::t("Reports"),
        'url'=>array('/driver/index/reports'),'linkOptions'=>array()),       
        
       array('visible'=>true,'label'=>'<i class="ion-iphone"></i>&nbsp; '.Driver::t("Push Broadcast Logs"),
        'url'=>array('/driver/index/bulklogs'),'linkOptions'=>array()),                 
        
        array('visible'=>true,'label'=>'<i class="ion-iphone"></i>&nbsp; '.Driver::t("Push Logs"),
        'url'=>array('/driver/index/pushlogs'),'linkOptions'=>array()),        
        
       array('visible'=>true,'label'=>'<i class="ion-android-textsms"></i>&nbsp; '.Driver::t("SMS Logs"),
        'url'=>array('/driver/index/smslogs'),'linkOptions'=>array()),         
        
      array('visible'=>true,'label'=>'<i class="ion-email"></i>&nbsp; '.Driver::t("Email Logs"),
        'url'=>array('/driver/index/emaillogs'),'linkOptions'=>array()),           
                
     )   
);       
?>

<div class="left-menu">
  <?php $this->widget('zii.widgets.CMenu', $menu);?>
</div> <!--left-menu-->
