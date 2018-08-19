<?php
$lang_params='';
if(isset($_COOKIE['kr_admin_lang_id'])){	
	if($_COOKIE['kr_admin_lang_id']!="-9999"){
	   $lang_params="/?lang_id=".$_COOKIE['kr_admin_lang_id'];
	}	
}

$menu =  array(  		    		    
    'activeCssClass'=>'active', 
    'encodeLabel'=>false,
    'items'=>array(
        array('visible'=>true,'label'=>'<i class="fa fa-cog"></i>&nbsp; '.merchantApp::t("General Settings"),
        'url'=>array('/merchantapp/index/settings'.$lang_params),'linkOptions'=>array()),
               
        array('visible'=>true,'label'=>'<i class="fa fa-user-plus"></i>&nbsp; '.merchantApp::t('Registered Merchant Device'),
        'url'=>array('/merchantapp/index/registereddevice'.$lang_params),'linkOptions'=>array()),
        
        array('visible'=>true,'label'=>'<i class="fa fa-comment-o"></i>&nbsp; '.merchantApp::t('Push Logs'),
        'url'=>array('/merchantapp/index/pushlogs'.$lang_params),'linkOptions'=>array()),
                       
        array('visible'=>true,'label'=>'<i class="fa fa-info-circle"></i>&nbsp; '.merchantApp::t('CronJobs'),
        'url'=>array('/merchantapp/index/cronjobs'.$lang_params),'linkOptions'=>array()),
                       
        array('visible'=>true,'label'=>'<i class="fa fa-database"></i>&nbsp; '.merchantApp::t("Update DB Tables"),
        'url'=>array('/merchantapp/update/'),'linkOptions'=>array('target'=>'_blank')),
        
        /*array('visible'=>true,'label'=>'<i class="fa fa-flag-checkered"></i>&nbsp; '.merchantApp::t("Mobile Translation"),
        'url'=>array('/merchantapp/index/translation'.$lang_params)), */
     )   
);       
?>
<div class="menu">
<?php $this->widget('zii.widgets.CMenu', $menu);?>
</div>