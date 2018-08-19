<?php 
if ($header){
	$this->renderPartial('/front/default-header');
}
?>

<?php if ($header):?>
<div class="sections section-grey">
<div class="container">
<?php endif;?>

<div class="section-notfound">

  <div class="row">
	  <div class="col-md-7  text-center">
		  <h1><?php echo t("404")?></h1>
		  <h3><?php echo t("Lo sentimos pero no podemos encontrar lo que estás buscando")?></h3>
		  
		  <p>
		  <?php echo t("La página no existe o se produjo algún otro error. Ve a nuestro")?>
		  <a class="orange-text bold" href="<?php echo Yii::app()->createUrl('/store')?>"><?php echo t("Inicio")?></a> <?php echo t("o vuelve a")?> 
		  <a href="javascript:window.history.back();" class="orange-text bold"> <?php echo t("pagina anterior")?></a>
		  </p>
	  </div> <!--col-->
	  
	  <div class="col-md-5 ">
	  <img src="<?php echo assetsURL()."/images/404.png"?>" />
	  </div>
  
  </div><!-- row-->

</div> <!--section-notfound-->

<?php if ($header):?>
</div>
</div>
<?php endif;?>