<?php
$menu =  array(  		    		    
    'activeCssClass'=>'active', 
    'encodeLabel'=>false,
    'items'=>array(
        array('visible'=>true,'label'=>'<i class="fa fa-cog"></i>&nbsp; '.PointsProgram::t("General Settings"),
        'url'=>array('/pointsprogram/index/settings'),'linkOptions'=>array()),
        
        array('visible'=>true,'label'=>'<i class="fa fa-file-text-o"></i>&nbsp; '.PointsProgram::t('User Reward Points'),
        'url'=>array('/pointsprogram/index/rewardpoints'),'linkOptions'=>array()),
        
        array('visible'=>true,'label'=>'<i class="fa fa-file-text-o"></i>&nbsp; '.PointsProgram::t('Points logs'),
        'url'=>array('/pointsprogram/index/pointslogs'),'linkOptions'=>array()),
        
        array('visible'=>true,'label'=>'<i class="fa fa-info-circle"></i>&nbsp; '.PointsProgram::t('CronJobs'),
        'url'=>array('/pointsprogram/index/cronjobs'),'linkOptions'=>array()),
               
        array('visible'=>true,'label'=>'<i class="fa fa-database"></i>&nbsp; '.PointsProgram::t("Update DB Tables"),
        'url'=>array('/pointsprogram/update/'),'linkOptions'=>array('target'=>'_blank')),
     )   
);       
?>
<div class="menu">
<?php $this->widget('zii.widgets.CMenu', $menu);?>
</div>