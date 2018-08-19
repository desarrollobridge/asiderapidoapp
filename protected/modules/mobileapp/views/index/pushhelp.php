

<div class="pad10">

<br/>
<p>
<?php echo AddonMobileApp::t("Please run the following cron jobs in your server as http")?>.<br/>
<?php echo AddonMobileApp::t("set the running of cronjobs every minute")?>
<br/>
</p>
<ul>
 <li class="bg-success">
 <a href="<?php echo websiteUrl()."/mobileapp/cron/processpush"?>" target="_blank"><?php echo websiteUrl()."/mobileapp/cron/processpush"?></a>
 </li>
 
  <li class="bg-success">
 <a href="<?php echo websiteUrl()."/mobileapp/cron/processbroadcast"?>" target="_blank"><?php echo websiteUrl()."/mobileapp/cron/processbroadcast"?></a>
 </li>
 
</ul>

<p><?php echo AddonMobileApp::t("Eg. command")?> <br/>
 curl <?php echo websiteUrl()."/mobileapp/cron/processpush"?><br/>
 curl <?php echo websiteUrl()."/mobileapp/cron/processbroadcast"?></p>
 </p>

</div>