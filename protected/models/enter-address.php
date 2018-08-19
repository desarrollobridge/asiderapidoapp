
<div class="container enter-address-wrap">

<div class="section-label">
    <a href="javascript:$.fancybox.close();"><i class="fa fa-close" style="font-size:24px;color: grey;"></i>
  </a>
    <a class="section-label-a">
      <span class="bold">
      <?php echo t("Por favor danos tu dirección")?></span>
      <b></b>
    </a>     
</div>  

<form id="frm-modal-enter-address" class="frm-modal-enter-address" method="POST" onsubmit="return false;" >
<?php echo CHtml::hiddenField('action','setAddress');?> 
<?php echo CHtml::hiddenField('web_session_id',
isset($this->data['web_session_id'])?$this->data['web_session_id']:''
);?>

<div class="row">
  <div class="col-md-12 ">
    <?php echo CHtml::textField('client_address',
	 isset($_SESSION['kr_search_address'])?$_SESSION['kr_search_address']:''
	 ,array(
	 'class'=>"grey-inputs",
	 'data-validation'=>"required"
	 ))?>
  </div> 
</div> <!--row-->

<div class="row food-item-actions top10">
  <div class="col-md-5 "></div>
  <div class="col-md-3 ">

       <input type="submit" class="orange-button inline center" value="<?php echo t("Enviar")?>">

  </div>
  <div class="col-md-3 ">
  </div>
</div>

 </form>

</div> <!--container-->

<script type="text/javascript">
$.validate({ 	
	language : jsLanguageValidator,
	language : jsLanguageValidator,
    form : '#frm-modal-enter-address',    
    onError : function() {      
    },
    onSuccess : function() {     
      form_submit('frm-modal-enter-address');
      return false;
    }  
})

jQuery(document).ready(function() {
	var google_auto_address= $("#google_auto_address").val();	
	if ( google_auto_address =="yes") {		
	} else {
		$("#client_address").geocomplete({
		    country: $("#admin_country_set").val()
		});	
	}
});

jQuery(document).ready(function() {		
	 $( "#client_address" ).on( "keydown", function(event) {
	 	if(event.which == 13){
	 	   $("#frm-modal-enter-address").submit();
	 	}
	 });
});
</script>
<?php
die();