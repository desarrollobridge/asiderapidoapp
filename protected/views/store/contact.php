<?php
$this->renderPartial('/front/banner-contact',array(
   'h1'=>t("Contáctanos"),
   'sub_text'=>$address." ".$country,
   'contact_phone'=>$contact_phone,
   'contact_email'=>$contact_email
));

$fields=yii::app()->functions->getOptionAdmin('contact_field');
if (!empty($fields)){
	$fields=json_decode($fields);
}
?>

<div class="sections section-grey2 section-contact relative">
       <div class="inner">
        <div class="row contacto" style="width: 80%;padding-top: 96px;padding-bottom: 96px;margin-left: 10%">
           <div class="col-md-7 dim">
             <h2>Vende a través de Así de Rápido</h2>
             <br/>
             <p>Puedes aumentar tus ventas uniéndote a la red de partners de <b>Así de Rápido</b> y vender tus productos en nuestra app.</p>
             <br/>
             <p>Por favor, necesitamos más información sobre tu comercio o restaurante para asesorarte sobre las posibilidades de colaboración.</p>
             
             <p><?php echo $contact_content?></p>
                          
           </div> <!--col-->
           <div class="col-md-5 black">
           
             <div class="top30"></div>
           
             <form class="uk-form uk-form-horizontal forms" id="forms" onsubmit="return false;">   
             <?php echo CHtml::hiddenField('action','contacUsSubmit')?>
             <?php echo CHtml::hiddenField('currentController','store')?>
             <?php FunctionsV3::addCsrfToken();?>
             <?php if (is_array($fields) && count($fields)>=1):?>
             <?php foreach ($fields as $val):?>
             <?php  
        $placeholder='';
        $validate_default="required";
        switch ($val) {
          case "name":
            $placeholder="Nombre del establecimiento";
            break;
          case "email":  
              $placeholder="Correo electrónico";
              $validate_default="email";
            break;
          case "phone":  
              $placeholder="Telefono";
            break;
          case "country":  
              $placeholder="Pais / Ciudad";
            break;
          case "message":  
              $placeholder="Cuentanos más sobre ti...";
            break;      
          default:
            break;
        }
       ?>                 
       <?php if ( $val=="message"):?>
             <div class="row top10">
             <div class="col-md-12">
               <?php echo CHtml::textArea($val,'',array(
                'placeholder'=>t($placeholder),
                'class'=>'grey-fields full-width'
               ))?>
             </div>
             </div>
             <?php else :?>
             <div class="row top10">
             <div class="col-md-12">
               <?php echo CHtml::textField($val,'',array(
                'placeholder'=>t($placeholder),
                'class'=>'grey-fields full-width',
                'data-validation'=>$validate_default
               ))?>
             </div>
             </div>
             <?php endif;?>
             
             <?php endforeach;?>
             
             <div class="row top10">
             <div class="col-md-12 text-center">
                <input type="submit" value="<?php echo t("Enviar")?>" class="orange-button medium inline rounded">
             </div>
             </div>
             <?php endif;?>
             </form>
             
             
           </div> <!--col-->
        </div> <!--row-->
     </div> <!--inner-->  
  <div id="contact-map"></div>
  
  <div class="container-map">

  </div> <!--container-->

</div> <!--sections-->
