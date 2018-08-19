
<div class="pad10">

<form id="frm_table" method="POST" class="form-inline" >
<?php echo CHtml::hiddenField('action','registeredDeviceList')?>

<table id="table_list" class="table table-hover">
<thead>
  <tr>
    <th width="5%"><?php echo merchantApp::t("ID")?></th>
    <th><?php echo merchantApp::t("Merchant Name")?></th>
    <th><?php echo merchantApp::t("Platform")?></th>
    <th><?php echo merchantApp::t("Username")?></th>
    <th><?php echo merchantApp::t("User type")?></th>
    <th ><?php echo merchantApp::t("Device ID")?></th>
    <th><?php echo merchantApp::t("Enabled Push")?></th>    
    <th><?php echo merchantApp::t("Date Created")?></th>
    <th><?php echo merchantApp::t("Actions")?></th>
  </tr>
</thead>
<tbody> 
</tbody>
</table>

</form>

</div>