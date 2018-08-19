<?php
$kr_search_adrress = FunctionsV3::getSessionAddress();

$home_search_text=Yii::app()->functions->getOptionAdmin('home_search_text');
if (empty($home_search_text)){
	$home_search_text=Yii::t("default","Find restaurants near you");
}

$home_search_subtext=Yii::app()->functions->getOptionAdmin('home_search_subtext');
if (empty($home_search_subtext)){
	$home_search_subtext=Yii::t("default","Order Delivery Food Online From Local Restaurants");
}

$home_search_mode=Yii::app()->functions->getOptionAdmin('home_search_mode');
$placholder_search=Yii::t("default","Calle, Avenida o Urbanización");
if ( $home_search_mode=="postcode" ){
	$placholder_search=Yii::t("default","Enter your postcode");
}
$placholder_search=Yii::t("default",$placholder_search);
?>

<?php if ( $home_search_mode=="address" || $home_search_mode=="") :?>

<div class="fondo-home">
<?php 
if ( $enabled_advance_search=="yes"){
	$this->renderPartial('/front/advance_search',array(
	  'home_search_text'=>$home_search_text,
	  'kr_search_adrress'=>$kr_search_adrress,
	  'placholder_search'=>$placholder_search,
	  'home_search_subtext'=>$home_search_subtext,
	  'theme_search_merchant_name'=>getOptionA('theme_search_merchant_name'),
	  'theme_search_street_name'=>getOptionA('theme_search_street_name'),
	  'theme_search_cuisine'=>getOptionA('theme_search_cuisine'),
	  'theme_search_foodname'=>getOptionA('theme_search_foodname'),
	  'theme_search_merchant_address'=>getOptionA('theme_search_merchant_address'),
	));
} else $this->renderPartial('/front/single_search',array(
      'home_search_text'=>$home_search_text,
	  'kr_search_adrress'=>$kr_search_adrress,
	  'placholder_search'=>$placholder_search,
	  'home_search_subtext'=>$home_search_subtext
));
?>
<?php if ($theme_hide_how_works<>2):?>
<!--TITULO CUANDO ES MOVIL-->
<div class="rapido-movil">
  <h2>¡Pedir con Así de Rápido es muy fácil!</h2>
</div>
<!--HOW IT WORKS SECTIONS-->
<div class="sections section-how-it-works">
<div class="container">
 <h2 style="font-size:45px; color: white;font-weight: 600;"><?php echo t("¿Como pedir Así de Rápido?")?></h2>
 <p class="center" style="font-size: 20px;line-height: 55px;color: white;"><?php echo t("Ten el producto que necesitas en 4 simples pasos")?></p>
 <div class="row">
   <div class="col-md-3 col-sm-3 center">
      <div class="steps step1-icon">
        <img src="<?php echo assetsURL()."/images/step1.png"?>">
      </div>
      <h3><?php echo t("Buscar")?></h3>
      <p><?php echo t("Busca a través de todos los establecimientos afiliados cerca de ti")?></p>
   </div>
   <div class="col-md-3 col-sm-3 center">
      <div class="steps step2-icon">
         <img src="<?php echo assetsURL()."/images/step2.png"?>">
      </div>
      <h3><?php echo t("Elegir")?></h3>
      <p><?php echo t("Elige entre cientos de productos disponibles facilmente")?></p>
   </div>
   <div class="col-md-3 col-sm-3  center">
      <div class="steps step2-icon">
        <img src="<?php echo assetsURL()."/images/step3.png"?>">
      </div>
      <h3><?php echo t("Paga")?></h3>
      <p><?php echo t("Es rápido, seguro y fácil")?></p>
   </div>
   <div class="col-md-3 col-sm-3  center">
     <div class="steps step2-icon">
       <img src="<?php echo assetsURL()."/images/step4.png"?>">
     </div>
      <h3><?php echo t("Disfruta")?></h3>
      <p><?php echo t("Nuestros despachadores te harán llegar tu pedido en tiempo record")?></p>
   </div>   
 </div>

 </div> <!--container-->
</div> <!--section-how-it-works-->
<?php endif;?>
</div> <!--parallax-container-->
<?php else :?>

<!--SEARCH USING LOCATION-->
<img class="mobile-home-banner" src="<?php echo assetsURL()."/images/b6.jpg"?>">

<div style="background-image: url('https://asiderapido.com/assets/images/banner.jpg');">

  <?php 
  $location_type=getOptionA("admin_zipcode_searchtype");  
  $this->renderPartial('/front/location-search-'.$location_type,array(
   'location_search_type'=>$location_type
  ));
  ?>

</div> <!--parallax-container-->

<?php endif;?>


<!--FEATURED RESTAURANT SECIONS-->
<?php if ($disabled_featured_merchant==""):?>
<?php if ( getOptionA('disabled_featured_merchant')!="yes"):?>
<?php if ($res=FunctionsV3::getFeatureMerchant()):?>
<div class="sections section-feature-resto">
<div class="container">

  <?php $cuisine_list=Yii::app()->functions->Cuisine(true);?>

  <h1 style="text-align: center;font-weight: 900;font-size: 42px;padding-bottom: 50px;"><?php echo t("Destacados")?></h1>
  <br/>
  
  <div class="row">
  <?php foreach ($res as $val): //dump($val);?>
  <?php $address= $val['street']." ".$val['city'];
        $address.=" ".$val['state']." ".$val['post_code'];
        
        $ratings=Yii::app()->functions->getRatings($val['merchant_id']);
  ?>   
  
    <!--<a href="<?php echo Yii::app()->createUrl('/store/menu/merchant/'. trim($val['restaurant_slug']) )?>">-->
    <a href="<?php echo Yii::app()->createUrl("/menu-". trim($val['restaurant_slug']))?>">
    <div class="col-md-5 destacados-rest">
    
        <div class="col-md-3 col-sm-3 separador-rest">
           <img class="logo-small" src="<?php echo FunctionsV3::getMerchantLogo($val['merchant_id'],$val['logo']);?>">
        </div> <!--col-->
        
        <div class="col-md-9 col-sm-9 texto-destacados">
        
          <div class="row">
              <div class="col-sm-5">
		          <div class="rating-stars" data-score="<?php echo $ratings['ratings']?>"></div>   
	          </div>
	          <div class="col-sm-2 merchantopentag">
	          <?php echo FunctionsV3::merchantOpenTag($val['merchant_id'])?>   
	          </div>
          </div>
          
          <h4 class="concat-text"><?php echo clearString($val['restaurant_name'])?></h4>
          
          <p class="concat-text">
          <?php //echo wordwrap(FunctionsV3::displayCuisine($val['cuisine']),50,"<br />\n");?>
          <?php echo FunctionsV3::displayCuisine($val['cuisine'],$cuisine_list);?>
          </p>
          <p class="concat-text"><?php echo $address?></p>                             
          <?php echo FunctionsV3::displayServicesList($val['service'])?>          
        </div> <!--col-->
        
    </div> <!--col-6-->
    </a>
    <div class="col-md-1"></div>
      
  <?php endforeach;?>
  </div> <!--end row-->

  
</div> <!--container-->
</div>
<?php endif;?>
<?php endif;?>
<?php endif;?>
<!--END FEATURED RESTAURANT SECIONS-->


<?php if ($theme_hide_cuisine<>2):?>
<!--CUISINE SECTIONS-->
<?php if ( $list=FunctionsV3::getCuisine() ): ?>
<div class="sections section-cuisine" style="padding-top: 96px;padding-bottom: 96px;">
<div class="container  nopad">



<div class="col-xl  nopad">
  <img src="">
  <h2 style="font-size:45px; font-weight: 800;color: #4d4d4d"><?php echo t("Navega a través de las categorias")?></h2>
  <br>
  <br>
  <br>
  <div class="row">
    <?php $x=1;?>
    <?php foreach ($list as $val): ?>
    <div class="col-md-4 col-sm-4 indent-5percent nopad">
      <a href="<?php echo Yii::app()->createUrl('/store/cuisine',array("category"=>$val['cuisine_id']))?>" 
     class="<?php echo ($x%2)?"even":'odd'?>">
      <?php 
      $cuisine_json['cuisine_name_trans']=!empty($val['cuisine_name_trans'])?json_decode($val['cuisine_name_trans'],true):'';	 
      echo qTranslate($val['cuisine_name'],'cuisine_name',$cuisine_json);
      if($val['total']>0){
      	echo "<span>(".$val['total'].")</span>";
      }
      ?>
      </a>
    </div>   
    <?php $x++;?>
    <?php endforeach;?>
  </div> 

</div>
  
</div> <!--container-->
</div> <!--section-cuisine-->
<?php endif;?>
<?php endif;?>


<?php if ($theme_show_app==2):?>
<!--MOBILE APP SECTION-->
<div id="mobile-app-sections" class="container">
<div >
    <div class="row">
        <div class="col-sm-4">
          <h2 style="font-weight: 700;font-size:36px;text-align: center;">¡Así de Rápido en tu celular! </h2>
          <br>
       <h3 class="green-text">Descarga nuestra aplicación, es la forma más fácil de pedir todo lo que quieras rapidamente.</h3>
       <br>
       <div class="row border" id="getapp-wrap">
                   
           <a href="" target="_blank">
           <img src="<?php echo assetsURL()."/images/get-app-store.png"?>" width="180">
           </a>           
           <a href="<?php echo $theme_app_android?>" target="_blank">
             <img class="get-app" src="<?php echo assetsURL()."/images/get-google-play.png"?>" width="180">
           </a>
     </div>
        </div>
        <div class="col-sm-8">
                        <img class="app-phone" src="https://asiderapido.com/assets/images/descargala.webp" width="750">

        </div>
</div>

       
     </div> <!--col-->
  </div> <!--row-->
  </div> <!--container-medium-->

  <div class="mytable border" id="getapp-wrap2">
     <?php if(!empty($theme_app_ios) && $theme_app_ios!="https://"):?>
     <div class="mycol border">
           <a href="<?php echo $theme_app_ios?>" target="_blank">
           <img class="get-app" src="<?php echo assetsURL()."/images/get-app-store.png"?>" width="125">
           </a>
     </div> <!--col-->
     <?php endif;?>
     <?php if(!empty($theme_app_android) && $theme_app_android!="https://"):?>
     <div class="mycol border">
          <a href="<?php echo $theme_app_android?>" target="_blank">
             <img class="get-app" src="<?php echo assetsURL()."/images/get-google-play.png"?>">
           </a>
     </div> <!--col-->
     <?php endif;?>
  </div> <!--mytable-->

<div class="container">
  <div class="separador-menu-abajo">
    <div class="row" style="max-width: 80%;padding-top: 60px;padding-bottom: 60px;margin-left: 80px;">
        <div class="col-sm-5">
         <h2>Tu restaurante, a domicilio</h2>
         <p>Asóciate con Así de Rápido para ganar dinero de una nueva forma, llegar a nuevos clientes y entregar comida a domicilio.</p>
         <b><p style="font-family: 'Open Sans', sans-serif;font-size: 600; font-size: 18px; color: #294bab;">Comenzar ></p></b>
        </div>
        <div class="col-sm-2"></div>
        <div class="col-sm-5">
         <h2>Entrega comida a domicilio</h2>
         <p>Entrega pedidos de Así de Rápido y gana dinero con tu propio horario. Dependiendo de tu ciudad, podrás hacer entregas en coche, bicicleta o scooter.</p>
         <b><p style="font-family: 'Open Sans', sans-serif;font-size: 600; font-size: 18px; color: #294bab;">Comenzar ></p></b>
        </div>
    </div>
</div>
</div>
  
  
</div> <!--container-->
<!--END MOBILE APP SECTION-->
<?php endif;?>


 