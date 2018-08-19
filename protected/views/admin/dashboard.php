

<div style="margin-bottom:20px;float:right;">
<a class="uk-button" target="_blank" href="<?php echo Yii::app()->createUrl('/update')?>">
<?php echo t("Click aquí para actualizar BD")?> <i class="fa fa-database"></i>
</a>

<a class="uk-button" target="_blank" href="<?php echo Yii::app()->createUrl('/update/updatemerchant')?>">
<?php echo t("Click aquí para actualizar información establecimientos")?> <i class="fa fa-database"></i>
</a>

</div>
<div class="clear"></div>

<!--
<form id="frm_table_list" method="POST" class="report uk-form uk-form-horizontal" >
<h3><?php echo Yii::t("default","New Merchant Registration List For Today")?> <span class="uk-text-success">
<?php
 //echo date('F d, Y')
 echo FormatDateTime(date('c'),false);
 ?>
</span>
</h3>

<input type="hidden" name="action" id="action" value="newMerchantRegList">
<input type="hidden" name="tbl" id="tbl" value="item">
<table id="table_list" class="uk-table uk-table-hover uk-table-striped uk-table-condensed">
  <caption><?php echo Yii::t("default","Merchant List")?></caption>
   <thead>
        <tr> 
            <th width="2%"><?php echo Yii::t('default',"MerchantID")?></th>
            <th width="6%"><?php echo Yii::t('default',"Merchant Name")?></th>
            <th width="3%"><?php echo Yii::t('default',"Package Name")?></th>            
            <th width="3%"><?php echo Yii::t('default',"Price")?></th>
            <th width="3%"><?php echo Yii::t('default',"Payment Type")?></th>            
            <th width="3%"><?php echo Yii::t('default',"Status")?></th>
            <th width="3%"><?php echo Yii::t('default',"Date")?></th>
            <th width="3%"></th>
        </tr>
    </thead>
    <tbody>    
    </tbody>
</table>
<div class="clear"></div>
</form>

<div style="padding-top:50px;padding-bottom:20px;">
<hr></hr>
</div>-->

<h3><?php echo Yii::t("default","New Merchant Payment List For Today")?> <span class="uk-text-success">
<?php echo FormatDateTime(date('c'),false);//echo date('F d, Y')?></span></h3>

<form id="frm_table_list2" method="POST" class="report uk-form uk-form-horizontal" >
<input type="hidden" name="action" id="action" value="rptMerchantPaymentToday">
<input type="hidden" name="tbl" id="tbl" value="item">
<table id="table_list2" class="uk-table uk-table-hover uk-table-striped uk-table-condensed">
  <caption><?php echo Yii::t("default","Pagos a establecimientos")?></caption>
   <thead>
        <tr> 
            <th width="2%"><?php echo Yii::t('default',"ID")?></th>
            <th width="5%"><?php echo Yii::t('default',"Establecimiento")?></th>
            <th width="3%"><?php echo Yii::t('default',"Precio")?></th>
            <th width="3%"><?php echo Yii::t('default',"Tipo de pago")?></th>
            <th width="3%"><?php echo Yii::t('default',"Estado")?></th>            
            <th width="3%"><?php echo Yii::t('default',"Fecha")?></th> 
            <th width="3%"></th> 
        </tr>
    </thead>
    <tbody>    
    </tbody>
</table>
<div class="clear"></div>
</form>



<h3><?php echo Yii::t("default","Incoming orders from merchant for today")?> <span class="uk-text-success">
<?php echo FormatDateTime(date('c'),false); //echo date('F d, Y')?></span></h3>

<form id="frm_table_list3" method="POST" class="report uk-form uk-form-horizontal admin-neworders" >
<input type="hidden" name="action" id="action" value="rptIncomingOrders">
<input type="hidden" name="tbl" id="tbl" value="item">
<table id="table_list3" class="uk-table uk-table-hover uk-table-striped uk-table-condensed">  
   <thead>
        <tr> 
            <th width="2%"><?php echo Yii::t('default',"Ref#")?></th>
            <th width="2%"><?php echo Yii::t('default',"Establecimiento")?></th>           
            <th width="6%"><?php echo Yii::t('default',"Nombre")?></th>
            <th width="3%"><?php echo Yii::t('default',"Item")?></th>            
            <th width="3%"><?php echo Yii::t('default',"Tipo Transacción")?></th>
            <th width="3%"><?php echo Yii::t('default',"Tipo pago")?></th>
            <th width="3%"><?php echo Yii::t('default',"Total")?></th>
            <th width="3%"><?php echo Yii::t('default',"IVA")?></th>
            <th width="3%"><?php echo Yii::t('default',"Total con IVA")?></th>
            <th width="3%"><?php echo Yii::t('default',"Estado")?></th>
            <th width="3%"><?php echo Yii::t('default',"Integradora")?></th>
            <th width="3%"><?php echo Yii::t('default',"Fecha")?></th>            
        </tr>
    </thead>
    <tbody>    
    </tbody>
</table>
<div class="clear"></div>
</form>