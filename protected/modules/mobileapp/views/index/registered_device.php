
<div class="pad10">

<form id="frm_table" method="POST" class="form-inline" >
<?php echo CHtml::hiddenField('action','registeredDeviceList')?>

<table id="table_list" class="table table-hover">
<thead>
  <tr>
    <th width="5%"><?php echo AddonMobileApp::t("ID")?></th>
    <th><?php echo AddonMobileApp::t("Platform")?></th>
    <th><?php echo AddonMobileApp::t("Customer name")?></th>
    <th ><?php echo AddonMobileApp::t("Device ID")?></th>
    <th><?php echo AddonMobileApp::t("Enabled Push")?></th>
    <th><?php echo AddonMobileApp::t("Country Set")?></th>
    <th><?php echo AddonMobileApp::t("Date Created")?></th>
    <th><?php echo AddonMobileApp::t("Actions")?></th>
  </tr>
</thead>
<tbody> 
</tbody>
</table>

</form>

</div>