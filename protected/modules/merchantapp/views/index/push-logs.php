
<div class="pad10">

<form id="frm_table" method="POST" class="form-inline" >
<?php echo CHtml::hiddenField('action','pushLogs')?>

<table id="table_list" class="table table-hover">
<thead>
  <tr>
    <th width="5%"><?php echo merchantApp::t("ID")?></th>
    <th><?php echo merchantApp::t("Merchant Name")?></th>
    <th><?php echo merchantApp::t("Username")?></th>
    <th><?php echo merchantApp::t("PlatForm")?></th>
    <th ><?php echo merchantApp::t("Device ID")?></th>
    <th><?php echo merchantApp::t("Push Title")?></th>        
    <th><?php echo merchantApp::t("Push Content")?></th>
    <th><?php echo merchantApp::t("Push Type")?></th>
    <th><?php echo merchantApp::t("Status")?></th>
    <th><?php echo merchantApp::t("Date Created")?></th>
  </tr>
</thead>
<tbody> 
</tbody>
</table>

</form>

</div>