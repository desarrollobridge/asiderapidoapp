<?php
$this->renderPartial('/front/default-header',array(
   'h1'=>t("Checkout"),
   'sub_text'=>t("Ingresa a tu cuenta")
));?>

<?php 
$this->renderPartial('/front/order-progress-bar',array(
   'step'=>isset($step)?$step:4,
   'show_bar'=>true
));

echo CHtml::hiddenField('mobile_country_code',Yii::app()->functions->getAdminCountrySet(true));
?>

<div class="sections section-grey2 section-checkout">

<div class="container">

  <div class="row">
     <!--LEFT CONTENT-->
	  <div class="col-md-6 border" >
       
	   <div class="box-grey rounded">      
		  <div class="section-label bottom20">
		    <a class="section-label-a">
		       <i class="ion-android-contact i-big"></i>
		      <span class="bold" style="background:#fff;">
		      <?php echo t("Ingresa a tu cuenta")?></span>
		      <b></b>
		    </a>     
		  </div>    
		  		  
		  
		  <form id="forms" class="forms" method="POST">
         <?php echo CHtml::hiddenField('action','clientLogin')?>
         <?php echo CHtml::hiddenField('currentController','store')?>
         <?php echo CHtml::hiddenField('redirect', Yii::app()->createUrl('/store/paymentoption') )?>
         <?php FunctionsV3::addCsrfToken(false);?>
             
           <?php if ($google_login_enabled==2 || $fb_flag==2 ) :?>
              <?php if ( $fb_flag==2):?>
	          <a href="javascript:fbcheckLogin();" class="fb-button orange-button medium rounded">
	           <i class="ion-social-facebook"></i><?php echo t("inicia sesioón con facebook")?>
	          </a> 
	          <?php endif;?>
          
	          <?php if ($google_login_enabled==2):?>
	          <div class="top10"></div>
	          <a href="<?php echo Yii::app()->createUrl('/store/googleLogin')?>" 
	          class="google-button orange-button medium rounded">
	            <i class="ion-social-googleplus-outline"></i><?php echo t("Inicia con Google")?>
	          </a> 
	          <?php endif;?>
          
          <div class="login-or">
            <span><?php echo t("Or")?></span>
          </div>
          <?php endif;?>
          
         
		  <div class="row top10">
		     <div class="col-md-12 ">
		     <?php echo CHtml::textField('username','',
                array('class'=>'grey-fields',
                'placeholder'=>t("Numero de telefono o correo electronico"),
               'required'=>true
               ))?>
		     </div>
		  </div> <!--row-->
		  
		  <div class="row top10">
		     <div class="col-md-12 ">
		     <?php echo CHtml::passwordField('password','',
                array('class'=>'grey-fields',
                'placeholder'=>t("Contraseña"),
               'required'=>true
               ))?>
		     </div>
		  </div> <!--row-->	  
		  
		 <?php if ($captcha_customer_login==2):?>
           <div class="top10">
             <div id="kapcha-1"></div>
           </div>
          <?php endif;?>  
		  
		  <div class="row top15">		   		   
		   <div class="col-md-6">
		     <a href="javascript:;" class="forgot-pass-link2 block orange-text text-center">
		     <?php echo t("Olvidaste tu contraseña");?> <i class="ion-help"></i>
		     </a>      
		   </div>
		   <div class="col-md-6">
		     <input type="submit" value="<?php echo t("Iniciar sesión")?>" class="green-button medium full-width">
		   </div>
		  </div>
		  		  
		</form>  		  		
		  
		</div> <!--box-grey-->  
		
		
		<form id="frm-modal-forgotpass" class="frm-modal-forgotpass" method="POST" onsubmit="return false;" >
		<?php echo CHtml::hiddenField('action','forgotPassword')?>
		<?php echo CHtml::hiddenField('do-action', isset($_GET['do-action'])?$_GET['do-action']:'' )?>     
		<div class="section-forgotpass">
		    <div class="box-grey rounded">      
			  <div class="section-label bottom20">
			    <a class="section-label-a">
			       <i class="ion-unlocked i-big"></i>
			      <span class="bold" style="background:#fff;">
			      <?php echo t("Olvidé mi contraseña")?></span>
			      <b></b>
			    </a>     
			  </div>    
			  
			   <div class="row top15">
		        <div class="col-md-12 ">
			     <?php echo CHtml::textField('username-email','',
	                array('class'=>'grey-fields',
	                'placeholder'=>t("Correo Electronico"),
	               'required'=>true
	               ))?>
			     </div>
			  </div> <!--row-->	
			  
			   <div class="row top10">		   		   
			   <div class="col-md-6 ">
			     <a href="javascript:;" class="back-link block orange-text text-center">
			     <?php echo t("Close");?>
			     </a>      
			   </div>
			   <div class="col-md-6 ">
			     <input type="submit" value="<?php echo t("Recuperar Contraseña")?>" class="green-button medium full-width">
			   </div>
			  </div>  
		  
		  </div> <!--box-grey-->
		</div> <!--section-forgotpass-->
		</form>
		
	  
	  </div> <!--col-->
	  <!--END LEFT CONTENT-->
	  
	  <!--RIGHT CONTENT-->
	  <div class="col-md-6 border" >
	  	    
	    <div class="box-grey rounded top-line-green">      
	    
	    <form id="form-signup" class="form-signup uk-panel uk-panel-box uk-form" method="POST">
	     <?php echo CHtml::hiddenField('action','clientRegistration')?>
        <?php echo CHtml::hiddenField('currentController','store')?>
         <?php echo CHtml::hiddenField('redirect',Yii::app()->createUrl('/store/paymentoption'))?>
         <?php FunctionsV3::addCsrfToken(false);?>
		<?php 
		$verification=Yii::app()->functions->getOptionAdmin("website_enabled_mobile_verification");	    
		if ( $verification=="yes"){
		    echo CHtml::hiddenField('verification',$verification);
		}
		if (getOptionA('theme_enabled_email_verification')==2){
	     	echo CHtml::hiddenField('theme_enabled_email_verification',2);
	    }
		?>
        
	    <?php if ( $disabled_guest_checkout!="yes"):?>
		  <div class="section-label bottom20">
		    <a class="section-label-a">
		       <i class="ion-android-contact i-big green-color"></i>
		      <span class="bold" style="background:#fff;">
		      <?php echo t("Pagar como visitante")?></span>
		      <b></b>
		    </a>     
		  </div>    
		  
		  <p style="margin-bottom:0;">
		  <?php echo t("Proceda a pagar y tendrá una opción para crear una cuenta al final.")?>
		  </p>
		  <a href="<?php echo $this->createUrl('/store/guestcheckout');?>" 
	               class="text-center block orange-text bottom20"><?php echo t("Continua como invitado")?></a>
		  <?php endif;?>
		  
		  <div class="section-label bottom20">
		    <a class="section-label-a">
		       <i class="ion-compose i-big green-color"></i>
		      <span class="bold" style="background:#fff;">
		      <?php echo t("Registrate")?></span>
		      <b></b>
		    </a>     
		  </div>    
		  
		   <div class="row top10">
		     <div class="col-md-12 ">
		     <?php echo CHtml::textField('first_name','',
                array('class'=>'grey-fields',
                'placeholder'=>t("Nombre"),
               'required'=>true               
               ))?>
		     </div>
		  </div> <!--row-->	  
		  
		  <div class="row top10">
		     <div class="col-md-12 ">
		     <?php echo CHtml::textField('last_name','',
                array('class'=>'grey-fields',
                'placeholder'=>t("Apellido"),
               'required'=>true
               ))?>
		     </div>
		  </div> <!--row-->	  
		  
		  <div class="row top10">
		     <div class="col-md-12 ">
		     <?php echo CHtml::textField('contact_phone','',
                array('class'=>'grey-fields mobile_inputs',
                'placeholder'=>t("Teléfono"),
               'required'=>true
               ))?>
		     </div>
		  </div> <!--row-->	  
		  
		 <div class="row top10">
		     <div class="col-md-12 ">
		     <?php echo CHtml::textField('email_address','',
                array('class'=>'grey-fields',
                'placeholder'=>t("Correo Electronico"),
               'required'=>true
               ))?>
		     </div>
		  </div> <!--row-->	   
		  
		  <div class="row top10">
		     <div class="col-md-12 ">
		     <?php echo CHtml::passwordField('password','',
                array('class'=>'grey-fields',
                'placeholder'=>t("Contraseña"),
               'required'=>true
               ))?>
		     </div>
		  </div> <!--row-->	   
		 
		  <div class="row top10">
		     <div class="col-md-12 ">
		     <?php echo CHtml::passwordField('cpassword','',
                array('class'=>'grey-fields',
                'placeholder'=>t("Confirmar contraseña"),
               'required'=>true
               ))?>
		     </div>
		  </div> <!--row-->	     
		  		 
		 <?php 
         $FunctionsK=new FunctionsK();
         $FunctionsK->clientRegistrationCustomFields();
         ?>  
		  
		 <?php if ($captcha_customer_signup==2):?>
           <div class="top10">
             <div id="kapcha-2"></div>
           </div>
         <?php endif;?> 
           
         <p class="text-muted">
          <?php echo Yii::t("default","Al crear una cuenta, usted acepta recibir SMS del proveedor.")?>
         </p>  
         
        <?php if ($terms_customer=="yes"): ?> 
         <div class="row">
           <div class="col-md-1">
           <?php 
		    echo CHtml::checkBox('terms_n_condition',false,array(
		     'value'=>2,
		     'class'=>"icheck",
		     'required'=>true
		   ));?>
           </div>
           <div class="col-md-11 text-left">
           <?php 
           echo " ". t("I Agree To")." <a href=\"$terms_customer_url\" target=\"_blank\">".t("The Terms & Conditions")."</a>";
            ?>
           </div>
         </div>
         <?php endif;;?>
         
         <div class="row top10">
         <div class="col-md-12 ">
          <input type="submit" value="<?php echo t("Crear una cuenta")?>" class="orange-button medium block full-width">
          </div>
         </div>
		  
		</form> 
		</div> <!--box-grey-->  
	  
	  
	  </div> <!--col-->
	  <!--END RIGHT CONTENT-->
	  
  </div> <!--row-->

</div> <!--container-->

</div> <!--section-grey-->

