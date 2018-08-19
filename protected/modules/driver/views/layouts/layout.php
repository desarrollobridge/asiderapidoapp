<?php $this->renderPartial('/layouts/header');?>

<body class="<?php echo isset($this->body_class)?$this->body_class:'';?>">

<?php if ($this->is_newupdate==TRUE):?>
<div class="update-link-wrapper">
<?php echo t("Your database needs update")?> 
<a href="<?php echo Yii::app()->createUrl('/driver/update')?>" target="_blank"><?php echo t("click here")?></a> 
<?php echo t("to update your database")?>
</div>
<?php endif;?>
<?php echo $content;?>

</body>

<?php $this->renderPartial('/layouts/footer');?>