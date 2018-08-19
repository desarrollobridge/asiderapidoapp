
<div class="pad10">

<form id="frm_table" method="POST" class="form-inline" >
<?php echo CHtml::hiddenField('action','pushLogs')?>

<table id="table_list" class="table table-hover">
<thead>
  <tr>
    <th width="5%"><?php echo AddonMobileApp::t("ID")?></th>
    <th><?php echo AddonMobileApp::t("PushType")?></th>
    <th><?php echo AddonMobileApp::t("Name")?></th>
    <th><?php echo AddonMobileApp::t("Platform")?></th>    
    <th><?php echo AddonMobileApp::t("Push Title")?></th>
    <th><?php echo AddonMobileApp::t("Push Message")?></th>    
    <th><?php echo AddonMobileApp::t("Date")?></th>
  </tr>
</thead>
<tbody> 
</tbody>
</table>

</form>

</div>