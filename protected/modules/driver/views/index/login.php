

<div class="container">
  <div class="login-wrap rounded">
  <img src="<?php echo Yii::app()->baseUrl.'/protected/modules/driver/assets/images/logo.png'; ?>">
     
    <form id="frm" class="frm rounded3" method="POST" onsubmit="return false;">
    <?php echo CHtml::hiddenField('action','login')?>
    <div>
    <?php 
    echo CHtml::textField('username','',array(
      'placeholder'=>Driver::t("Username"),
      'class'=>"lightblue-fields rounded",
      'required'=>true
    ));
    ?>
    </div>
    
    <div class="top20">
    <?php 
    echo CHtml::passwordField('password','',array(
      'placeholder'=>Driver::t("Password"),
      'class'=>"lightblue-fields rounded",
      'required'=>true
    )); 
    ?>
    </div>
    
    <div class="top20">
    <?php 
    echo chtml::dropDownList('user_type',1,array(
      1=>Driver::t("administrator"),
      2=>Driver::t("merchant"),
    ),array(
      'class'=>"lightblue-fields rounded"
    ));
    ?>
    </div>
    
    <div class="top20">
    <button class="green-button medium rounded"><?php echo Driver::t("Login")?></button>
    </div>
    </form>
  
  </div> <!--login-wrap-->
</div> <!--container-->