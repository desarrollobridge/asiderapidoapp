
<div class="">

<form id="frm_table" method="POST" class="form-inline" >
<?php echo CHtml::hiddenField('action','userViewLogs')?>
<?php echo CHtml::hiddenField('client_id',$_GET['client_id'])?>

<a href="<?php echo Yii::app()->createUrl('pointsprogram/index/rewardpoints');?>">
<i class="fa fa-long-arrow-left"></i> <?php echo t("Back")?></a>

<table id="table_list" class="table table-hover">
<thead>
  <tr>    
    <th width="10%"><?php echo t("Date")?></th>   
    <th width="13%"><?php echo t("Customer Name")?></th>
    <th width="13%"><?php echo t("Transaction")?></th>
    <th width="10%"><?php echo t("Total Points")?></th>        
    <th width="10%"><?php echo t("Redeemed Points")?></th>    
  </tr>
</thead>
<tbody> 
</tbody>
</table>

</form>

</div>