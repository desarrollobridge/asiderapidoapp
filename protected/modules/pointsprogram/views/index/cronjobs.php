

<div>
<p><?php echo t("Please run the following cron jobs in your server as http")?>.
<br/>
<?php echo t("run the cron jobs at the end of the year eg.")?>December 31 <?php echo date("Y")?> 11:59 PM
<br/>
<?php echo t("Or simply run it at the end of the month")?>
</p>

<ul>
 <li class="bg-success">
 <a href="<?php echo websiteUrl()."/pointsprogram/cron/processexpiry"?>" target="_blank"><?php echo websiteUrl()."/pointsprogram/cron/processexpiry"?></a>
 </li>
 
</ul>

<p><?php echo t("Eg. command")?> <br/>
 curl <?php echo websiteUrl()."/pointsprogram/cron/processexpiry"?><br/>
 or<br/>
 wget <?php echo websiteUrl()."/pointsprogram/cron/processexpiry"?>
 </p>

</div>

