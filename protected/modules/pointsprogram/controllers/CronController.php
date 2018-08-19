<?php
class CronController extends CController
{
	
	public function actionIndex()
	{
		
	}
	
	public function init()
	{			
		 // set website timezone
		 $website_timezone=Yii::app()->functions->getOptionAdmin("website_timezone");		 
		 if (!empty($website_timezone)){		 	
		 	Yii::app()->timeZone=$website_timezone;
		 }		 
	}
	
	public function actionProcessExpiry()
	{
		$year=date('Y',strtotime("-1 year"));		
		$pts_expiry=Yii::app()->functions->getOptionAdmin('pts_expiry');					
		if ( $pts_expiry==3){
			echo "Cron is stop point does not expired";
			Yii::app()->end();
		}
		
		$expired_timedate=date('Y')."12312359";
		$todays=date('YmdHi');		
		dump("expired year=>".$expired_timedate);
		dump("todays year=>".$todays);
		/*check if the date and time is the last year */
		if ( $expired_timedate==$todays){							
		    $and=" AND date_created LIKE '".$year."%' ";				
			$db=new DbExt();
			$stmt="
			UPDATE			
			{{points_earn}}
			SET status='expired'
			WHERE
			status ='active'		
			$and
			";		
			dump($stmt);
			if ($res=$db->qry($stmt)){
				dump($res);
				echo 'OK';
			} else echo "No record found";
		} else echo "Not end of the year";
	}
	
} /*end class*/