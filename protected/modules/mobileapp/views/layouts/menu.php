<?php
$menu =  array(  		    		    
    'activeCssClass'=>'active', 
    'encodeLabel'=>false,
    'items'=>array(
        array('visible'=>true,'label'=>'<i class="fa fa-cog"></i>&nbsp; '.AddonMobileApp::t("General Settings"),
        'url'=>array('/mobileapp/index/settings'),'linkOptions'=>array()),
        
        array('visible'=>true,'label'=>'<i class="fa fa-user-plus"></i>&nbsp; '.AddonMobileApp::t('Registered Device'),
        'url'=>array('/mobileapp/index/registereddevice'),'linkOptions'=>array()),
        
       array('visible'=>true,'label'=>'<i class="fa fa-comment-o"></i>&nbsp; '.AddonMobileApp::t('Push Broadcast'),
        'url'=>array('/mobileapp/index/broadcast'),'linkOptions'=>array()), 
        
        array('visible'=>true,'label'=>'<i class="fa fa-mobile"></i>&nbsp; '.AddonMobileApp::t("Push Notification Logs"),
        'url'=>array('/mobileapp/index/pushlogs'),'linkOptions'=>array()),
        
        array('visible'=>true,'label'=>'<i class="fa fa-file-text-o"></i>&nbsp; '.AddonMobileApp::t("Pages"),
        'url'=>array('/mobileapp/index/pages'),'linkOptions'=>array()),
        
        array('visible'=>true,'label'=>'<i class="fa fa-info-circle"></i>&nbsp; '.AddonMobileApp::t("Push CronJobs"),
        'url'=>array('/mobileapp/index/pushhelp'),'linkOptions'=>array()),
        
        array('visible'=>true,'label'=>'<i class="fa fa-database"></i>&nbsp; '.AddonMobileApp::t("Update DB Tables"),
        'url'=>array('/mobileapp/update'),'linkOptions'=>array('target'=>'_blank')),
        
        /*array('visible'=>true,'label'=>'<i class="fa fa-flag-checkered"></i>&nbsp; '.AddonMobileApp::t("Mobile Translation"),
        'url'=>array('/mobileapp/index/translation')),*/
     )   
);       
?>
<div class="menu">
<?php $this->widget('zii.widgets.CMenu', $menu);?>
</div>