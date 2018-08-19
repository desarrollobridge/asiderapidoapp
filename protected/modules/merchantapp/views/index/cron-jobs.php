

<div class="pad10">

<br/>
<p>
<?php echo merchantApp::t("Please run the following cron jobs in your server as http.")?><br/>
<?php echo merchantApp::t("set the running of cronjobs every minute")?><br/>
</p>
<ul>

<!--<li class="bg-success">
 <a href="<?php echo websiteUrl()."/merchantapp/cron/getneworder"?>" target="_blank"><?php echo websiteUrl()."/merchantapp/cron/getneworder"?></a>
 </li>-->
 
 <li class="bg-success">
 <a href="<?php echo websiteUrl()."/merchantapp/cron/processpush"?>" target="_blank"><?php echo websiteUrl()."/merchantapp/cron/processpush"?></a>
 </li>
 
 
 
</ul>

<p><?php echo merchantApp::t("Eg. command")?> <br/>
 <!--curl <?php echo websiteUrl()."/merchantapp/cron/getneworder"?><br/>-->
 curl <?php echo websiteUrl()."/merchantapp/cron/processpush"?><br/>
 </p>
 </p>
 
 <p><?php echo merchantApp::t("OR")?></p>
 
 <p><?php echo merchantApp::t("Eg. command")?> <br/>
 <!--wget <?php echo websiteUrl()."/merchantapp/cron/getneworder"?><br/>-->
 wget <?php echo websiteUrl()."/merchantapp/cron/processpush"?><br/>
 </p>
 </p>

<p><?php echo merchantApp::t("example bluehost server toturial")?> 
<a target="_blank" href="https://my.bluehost.com/cgi/help/411">https://my.bluehost.com/cgi/help/411</a>
</p>
 
</div>