<?php
if (!isset($new_data_1)){
	$new_data_1='';
}

//dump($new_data_1);
?>

<table class="table top30 table-hover table-striped">
 <thead>
  <tr>
   <th class="text-muted"><?php echo Driver::t("Name")?></th>
   <th class="text-muted"><?php echo Driver::t("Successful Tasks")?></th>
   <th class="text-muted"><?php echo Driver::t("Cancelled Tasks")?></th>
   <th class="text-muted"><?php echo Driver::t("Failed Tasks")?></th>
   <th class="text-muted"><?php echo Driver::t("Total Tasks")?></th>
   <th class="text-muted"><?php echo Driver::t("Total")." ".Yii::app()->functions->getCurrencyCode()?></th>
  </tr>
 </thead>
 <tbody>
 <?php if (is_array($new_data_1) && count($new_data_1)>=1):?>
 <?php unset($new_data[0])?>
 <?php foreach ($new_data_1 as $val):?>
 <?php   
   $total = 0;
   if (isset($val['successful'])){
   	  $total+=$val['successful'];
   }
   if (isset($val['cancelled'])){
   	  $total+=$val['cancelled'];
   }
   if (isset($val['failed'])){
   	  $total+=$val['failed'];
   }   
   
//   $total_ordered+=$val['total_order_amount'];
 ?>
  <tr>
    <td><?php echo $val['driver_name']?></td>
    <td><?php echo isset($val['successful'])?$val['successful']:''?></td>
    <td><?php echo isset($val['cancelled'])?$val['cancelled']:''?></td>
    <td><?php echo isset($val['failed'])?$val['failed']:''?></td>
    <td><?php echo $total?></td>
    <td><b><?php echo Driver::prettyPrice($total_ordered)?></b></td>
  </tr>
  <?php endforeach;?>
  <?php endif;?>
  
  
 </tbody>
</table>