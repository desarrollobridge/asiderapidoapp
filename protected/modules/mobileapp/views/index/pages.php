
<div class="pad10">

<form id="frm_table" method="POST" class="form-inline" >
<?php echo CHtml::hiddenField('action','pagesList')?>

<a href="<?php echo Yii::app()->createUrl('/mobileapp/index/pagenew')?>"
 class="btn btn-primary"><?php echo AddonMobileApp::t("Add new")?> <i class="fa fa-plus"></i>
</a>

<table id="table_list" class="table table-hover">
<thead>
  <tr>
    <th width="5%"><?php echo AddonMobileApp::t("ID")?></th>
    <th><?php echo AddonMobileApp::t("Title")?></th>
    <th><?php echo AddonMobileApp::t("Content")?></th>    
    <th><?php echo AddonMobileApp::t("Icon")?></th>    
    <th><?php echo AddonMobileApp::t("HTML format")?></th>
    <th><?php echo AddonMobileApp::t("Sequence")?></th>
    <th><?php echo AddonMobileApp::t("Date")?></th>
    <th><?php echo AddonMobileApp::t("Actions")?></th>
  </tr>
</thead>
<tbody> 
</tbody>
</table>

</form>

</div>