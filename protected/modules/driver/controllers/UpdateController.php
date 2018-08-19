<?php
class UpdateController extends CController
{
	public function actionIndex()
	{
		$prefix=Yii::app()->db->tablePrefix;		
		$table_prefix=$prefix;
		
		$DbExt=new DbExt;
		
		$stmt="	
		CREATE TABLE IF NOT EXISTS ".$table_prefix."driver (
		  `driver_id` int(14) NOT NULL,
		  `user_type` varchar(50) NOT NULL,
		  `user_id` int(14) NOT NULL,
		  `on_duty` int(1) NOT NULL DEFAULT '1',
		  `first_name` varchar(255) NOT NULL,
		  `last_name` varchar(255) NOT NULL,
		  `email` varchar(100) NOT NULL,
		  `phone` varchar(20) NOT NULL,
		  `username` varchar(100) NOT NULL,
		  `password` varchar(100) NOT NULL,
		  `team_id` int(14) NOT NULL,
		  `transport_type_id` varchar(50) NOT NULL,
		  `transport_description` varchar(255) NOT NULL,
		  `licence_plate` varchar(255) NOT NULL,
		  `color` varchar(255) NOT NULL,
		  `status` varchar(255) NOT NULL DEFAULT 'active',
		  `date_created` datetime NOT NULL,
		  `date_modified` datetime NOT NULL,
		  `last_login` datetime NOT NULL,
		  `last_online` int(14) NOT NULL,
		  `location_address` text NOT NULL,
		  `location_lat` varchar(50) NOT NULL,
		  `location_lng` varchar(50) NOT NULL,
		  `ip_address` varchar(50) NOT NULL,
		  `forgot_pass_code` varchar(10) NOT NULL,
		  `token` varchar(255) NOT NULL,
		  `device_id` text NOT NULL,
		  `device_platform` varchar(50) NOT NULL DEFAULT 'Android',
		  `enabled_push` int(1) NOT NULL DEFAULT '1',
		  PRIMARY KEY (`driver_id`),
		  KEY `team_id` (`team_id`),
		  KEY `user_type` (`user_type`),
		  KEY `user_id` (`user_id`),
		  KEY `status` (`status`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		";		
		echo "Creating Table driver..<br/>";	
		$DbExt->qry($stmt);
		echo "(Done)<br/>";    		
		
		$stmt="
		ALTER TABLE ".$table_prefix."driver
        MODIFY `driver_id` int(14) NOT NULL AUTO_INCREMENT;
		";
		echo "ALTER Table driver..<br/>";
		$DbExt->qry($stmt);
		echo "(Done)<br/>";    	
		
		
		$stmt="
		CREATE TABLE IF NOT EXISTS ".$table_prefix."driver_pushlog (
		  `push_id` int(14) NOT NULL,
		  `device_platform` varchar(50) NOT NULL,
		  `device_id` text NOT NULL,
		  `push_title` varchar(255) NOT NULL,
		  `push_message` varchar(255) NOT NULL,
		  `push_type` varchar(50) NOT NULL DEFAULT 'task',
		  `actions` varchar(255) NOT NULL,
		  `status` varchar(255) NOT NULL DEFAULT 'pending',
		  `json_response` text NOT NULL,
		  `order_id` int(14) NOT NULL,
		  `driver_id` int(14) NOT NULL,
		  `task_id` int(14) NOT NULL,
		  `date_created` datetime NOT NULL,
		  `date_process` datetime NOT NULL,
		  `ip_address` varchar(50) NOT NULL,
		  `is_read` int(1) DEFAULT '2',
		   PRIMARY KEY (`push_id`),
		   KEY `device_platform` (`device_platform`),
		   KEY `status` (`status`),
		   KEY `order_id` (`order_id`),
		   KEY `task_id` (`task_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		";
		echo "Creating Table driver_pushlog..<br/>";
		$DbExt->qry($stmt);
		echo "(Done)<br/>";    	
		
		$stmt="
		  ALTER TABLE ".$table_prefix."driver_pushlog
           MODIFY `push_id` int(14) NOT NULL AUTO_INCREMENT;
		";
		echo "ALTER Table driver_pushlog..<br/>";
		$DbExt->qry($stmt);
		echo "(Done)<br/>";
		
		$stmt="
		  CREATE TABLE IF NOT EXISTS ".$table_prefix."driver_task (
		  `task_id` int(14) NOT NULL,
		  `order_id` int(14) NOT NULL,
		  `user_type` varchar(100) NOT NULL,
		  `user_id` int(14) NOT NULL,
		  `task_description` varchar(255) NOT NULL,
		  `trans_type` varchar(255) NOT NULL,
		  `contact_number` varchar(50) NOT NULL,
		  `email_address` varchar(200) NOT NULL,
		  `customer_name` varchar(255) NOT NULL,
		  `delivery_date` datetime NOT NULL,
		  `delivery_address` varchar(255) NOT NULL,
		  `team_id` int(14) NOT NULL,
		  `driver_id` int(14) NOT NULL,
		  `task_lat` varchar(50) NOT NULL,
		  `task_lng` varchar(50) NOT NULL,
		  `customer_signature` varchar(255) NOT NULL,
		  `status` varchar(255) NOT NULL DEFAULT 'unassigned',
		  `date_created` datetime NOT NULL,
		  `date_modified` datetime NOT NULL,
		  `ip_address` varchar(50) NOT NULL,
		   PRIMARY KEY (`task_id`),
		   KEY `order_id` (`order_id`),
		   KEY `user_type` (`user_type`),
		   KEY `user_id` (`user_id`),
		   KEY `team_id` (`team_id`),
		   KEY `driver_id` (`driver_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		";
		echo "Creating Table driver_task..<br/>";
		$DbExt->qry($stmt);
		echo "(Done)<br/>";
		
		$stmt="
		  ALTER TABLE ".$table_prefix."driver_task
          MODIFY `task_id` int(14) NOT NULL AUTO_INCREMENT;
		";
		echo "ALTER Table driver_task..<br/>";
		$DbExt->qry($stmt);
		echo "(Done)<br/>";
		
		$stmt="		 
		CREATE TABLE IF NOT EXISTS ".$table_prefix."driver_team (
		  `team_id` int(14) NOT NULL,
		  `user_type` varchar(100) NOT NULL,
		  `user_id` int(14) NOT NULL,
		  `team_name` varchar(255) NOT NULL,
		  `location_accuracy` varchar(50) NOT NULL,
		  `status` varchar(255) NOT NULL,
		  `date_created` datetime NOT NULL,
		  `date_modified` datetime NOT NULL,
		  `ip_address` varchar(50) NOT NULL,
		   PRIMARY KEY (`team_id`),
		   KEY `user_type` (`user_type`),
		   KEY `user_id` (`user_id`),
		   KEY `status` (`status`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		";
		echo "Creating Table driver_team..<br/>";
		$DbExt->qry($stmt);
		echo "(Done)<br/>";
		
	
		$stmt="
		  ALTER TABLE ".$table_prefix."driver_team
          MODIFY `team_id` int(14) NOT NULL AUTO_INCREMENT;
		";
		echo "ALTER Table driver_team..<br/>";
		$DbExt->qry($stmt);
		echo "(Done)<br/>";
		
		echo "Updating order_history<br/>";
		$new_field=array( 
		   'task_id'=>"int(14) NOT NULL",
		   'reason'=>"text NOT NULL",
		   'customer_signature'=>"varchar(255) NOT NULL",
		   'notification_viewed'=>"int(1) NOT NULL DEFAULT '2'",
		   'driver_id'=>"int(14) NOT NULL",
		   'driver_location_lat'=>"varchar(50) NOT NULL",
		   'driver_location_lng'=>"varchar(50) NOT NULL"		   
		);
		$this->alterTable('order_history',$new_field);
		
		
		$stmt="ALTER TABLE ".$table_prefix."driver_task AUTO_INCREMENT = 100000;";
		echo "Altering table driver_task<br/>";
		$DbExt->qry($stmt);
		
		dump("VERSION 1.1 UPDATE DB");
		
		$stmt="		 		
		CREATE TABLE IF NOT EXISTS ".$table_prefix."driver_assignment (
		  `assignment_id` int(14) NOT NULL,
		  `auto_assign_type` varchar(50) NOT NULL,
		  `task_id` int(14) NOT NULL,
		  `driver_id` int(14) NOT NULL,
		  `first_name` varchar(255) NOT NULL,
		  `last_name` varchar(255) NOT NULL,
		  `status` varchar(100) NOT NULL DEFAULT 'pending',
		  `task_status` varchar(255) NOT NULL DEFAULT 'unassigned',
		  `date_created` datetime NOT NULL,
		  `date_process` datetime NOT NULL,
		  `ip_address` varchar(50) NOT NULL,
		   PRIMARY KEY (`assignment_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		";
		echo "Creating Table driver_assignment..<br/>";
		$DbExt->qry($stmt);
		echo "(Done)<br/>";
		
		$this->setIncrement('driver_assignment','assignment_id');				
		
		echo "Updating driver_task<br/>";
		$new_field=array( 		   
		   'auto_assign_type'=>"varchar(50) NOT NULL",
		   'assign_started'=>"datetime NOT NULL",
		   'assignment_status'=>"varchar(255) NOT NULL"		   
		);
		$this->alterTable('driver_task',$new_field);
				
		$stmt="		 				
		CREATE TABLE IF NOT EXISTS ".$table_prefix."driver_bulk_push (
		  `bulk_id` int(14) NOT NULL,
		  `push_title` varchar(255) NOT NULL,
		  `push_message` text NOT NULL,
		  `status` varchar(255) NOT NULL DEFAULT 'pending',
		  `date_created` datetime NOT NULL,
		  `date_process` datetime NOT NULL,
		  `ip_address` varchar(50) NOT NULL,
		  PRIMARY KEY (`bulk_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		";
		echo "Creating Table driver_bulk_push..<br/>";
		$DbExt->qry($stmt);
		echo "(Done)<br/>";
		
		$this->setIncrement('driver_bulk_push','bulk_id');
				
		echo "Updating driver_pushlog<br/>";
		$new_field=array( 		   
		   'bulk_id'=>"int(14) NOT NULL"		   
		);
		$this->alterTable('driver_pushlog',$new_field);
		
		echo "Updating order_history<br/>";
		$new_field=array( 		   
		   'remarks2'=>"varchar(255) NOT NULL",
		   'remarks_args'=>"varchar(255) NOT NULL",
		);
		$this->alterTable('order_history',$new_field);
		
		dump("version 1.4");
				
		echo "Updating driver_task<br/>";
		$new_field=array( 		   
		   'dropoff_merchant'=>"int(14) NOT NULL",
		   'dropoff_contact_name'=>"varchar(255) NOT NULL",
		   'dropoff_contact_number'=>"varchar(20) NOT NULL",
		   'drop_address'=>"varchar(255) NOT NULL",
		   'dropoff_lat'=>"varchar(30) NOT NULL",
		   'dropoff_lng'=>"varchar(30) NOT NULL",
		   'recipient_name'=>"varchar(255) NOT NULL",		   
		);
		$this->alterTable('driver_task',$new_field);
								
		echo "Updating order_history<br/>";
		$new_field=array( 		   
		   'notes'=>"varchar(255) NOT NULL DEFAULT ''",		   
		   'photo_task_id'=>"int(14) NOT NULL",
		);
		$this->alterTable('order_history',$new_field);
		
		echo "Updating driver<br/>";
		$new_field=array( 		   
		   'profile_photo'=>"varchar(255) NOT NULL",
		   'is_signup'=>"int(1) NOT NULL DEFAULT '2'",
		);
		$this->alterTable('driver',$new_field);
		
		
		$stmt="		 				
		CREATE TABLE IF NOT EXISTS ".$table_prefix."driver_task_photo (
		  `id` int(14) NOT NULL,
		  `task_id` int(14) NOT NULL,
          `photo_name` varchar(255) NOT NULL,
          `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
           `ip_address` varchar(50) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		";		
		echo "Creating Table driver_task_photo..<br/>";
		$DbExt->qry($stmt);
		echo "(Done)<br/>";
				
		$this->setIncrement('driver_task_photo','id');		
		
		
		echo "Updating table order_delivery_address<br/>";
		$new_field=array( 
		   'formatted_address'=>"text NOT NULL",
		   'google_lat'=>"varchar(50) NOT NULL",
		   'google_lng'=>"varchar(50) NOT NULL",
		);
		$this->alterTable('order_delivery_address',$new_field);		
				
		/*1.5 new fields*/
				
		echo "Updating table driver_bulk_push<br/>";
		$new_field=array( 
		   'team_id'=>"int(14) NOT NULL DEFAULT '0'",
		   'user_type'=>"varchar(50) DEFAULT ''",
		   'user_id'=>"int(14) NOT NULL DEFAULT '0'",
		);
		$this->alterTable('driver_bulk_push',$new_field);
		$this->addIndex("driver_bulk_push",'team_id');
		$this->addIndex("driver_bulk_push",'user_type');
		$this->addIndex("driver_bulk_push",'user_id');
		
		echo "Updating table driver_pushlog<br/>";
		$new_field=array( 
		   'user_type'=>"varchar(50) NOT NULL DEFAULT ''",
		   'user_id'=>"int(14) NOT NULL DEFAULT '0'",		   
		);
		$this->alterTable('driver_pushlog',$new_field);
		$this->addIndex("driver_pushlog",'user_type');
		$this->addIndex("driver_pushlog",'user_id');
		
		echo "Updating table driver_task<br/>";
		$new_field=array( 
		   'critical'=>"int(14) NOT NULL DEFAULT '1'",		   
		);
		$this->alterTable('driver_task',$new_field);
		$this->addIndex("driver_task",'critical');
		
		echo "Updating table driver<br/>";
		$new_field=array( 
		   'app_version'=>"varchar(14) NOT NULL DEFAULT ''",		   
		);
		$this->alterTable('driver',$new_field);
		
		echo "Updating table order_history<br/>";
		$new_field=array( 
		   'receive_by'=>"varchar(255) NOT NULL DEFAULT ''",
		   'signature_base30'=>"text NOT NULL",
		);
		$this->alterTable('order_history',$new_field);
				
		$stmt="		 				
		CREATE TABLE IF NOT EXISTS ".$table_prefix."driver_sms_logs (
		  `id` int(14) NOT NULL,
		  `user_type` varchar(100) DEFAULT '',
		  `user_id` int(14) NOT NULL DEFAULT '0',
		  `contact_phone` varchar(50) NOT NULL,
		  `sms_message` varchar(255) NOT NULL,
		  `status` varchar(255) NOT NULL,
		  `gateway_response` varchar(255) NOT NULL,
		  `gateway` varchar(100) NOT NULL,
		  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `ip_address` varchar(50) NOT NULL,
		   PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		";		
		echo "Creating Table driver_task_photo..<br/>";
		$DbExt->qry($stmt);
		echo "(Done)<br/>";
		$this->setIncrement('driver_sms_logs','id');
		$this->addIndex('driver_sms_logs','user_type');
		$this->addIndex('driver_sms_logs','user_id');
		$this->addIndex('driver_sms_logs','status');
				
		$stmt="		 				
		CREATE TABLE IF NOT EXISTS ".$table_prefix."email_logs (
		  `id` int(14) NOT NULL,
		  `email_address` varchar(255) NOT NULL DEFAULT '',
		  `sender` varchar(255) NOT NULL DEFAULT '',
		  `subject` varchar(255) NOT NULL DEFAULT '',
		  `content` text NOT NULL,
		  `status` varchar(200) NOT NULL DEFAULT 'pending',
		  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `ip_address` varchar(50) NOT NULL DEFAULT '',
		  `module_type` varchar(255) NOT NULL DEFAULT '',
		  `user_type` varchar(100) NOT NULL DEFAULT '',
		  `user_id` int(14) NOT NULL DEFAULT '0',
		  `merchant_id` int(14) NOT NULL DEFAULT '0',
		   PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		";		
		echo "Creating Table email_logs..<br/>";
		$DbExt->qry($stmt);
		echo "(Done)<br/>";
		$this->setIncrement('email_logs','id');
		$this->addIndex('email_logs','user_id');
		$this->addIndex('email_logs','user_type');
		$this->addIndex('email_logs','merchant_id');
		$this->addIndex('email_logs','module_type');
		$this->addIndex('email_logs','email_address');
		
		$stmt="		 				
		CREATE TABLE IF NOT EXISTS ".$table_prefix."driver_track_location (
		  `id` int(14) NOT NULL,
		  `user_type` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
		  `user_id` int(14) NOT NULL DEFAULT '0',
		  `driver_id` int(14) NOT NULL DEFAULT '0',
		  `latitude` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
		  `longitude` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
		  `altitude` varchar(50) COLLATE utf8_unicode_ci DEFAULT '',
		  `accuracy` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
		  `altitudeAccuracy` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
		  `heading` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
		  `speed` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
		  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `ip_address` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
		  `track_type` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		";		
		echo "Creating Table driver_track_location..<br/>";
		$DbExt->qry($stmt);
		echo "(Done)<br/>";
		$this->setIncrement('driver_track_location','id');
		$this->addIndex('driver_track_location','user_type');
		$this->addIndex('driver_track_location','user_id');
		$this->addIndex('driver_track_location','driver_id');
		
		
		/*version 1.6.0*/
		$this->addIndex('driver_assignment','auto_assign_type');
		$this->addIndex('driver_assignment','task_id');
		$this->addIndex('driver_assignment','driver_id');
		$this->addIndex('driver_assignment','status');
		$this->addIndex('driver_assignment','task_status');
		
						
		/*VIEW TABLES*/		
		$stmt="
		  Create OR replace view ".$table_prefix."driver_task_view as
			SELECT a.*,
			concat(b.first_name,' ',b.last_name) as driver_name,
			b.device_id,
			b.phone as driver_phone,
			b.email as driver_email,
			b.device_platform,
			b.enabled_push,
			b.location_lat as driver_lat,
			b.location_lng as driver_lng,
			c.merchant_id,
			d.restaurant_name as merchant_name,
			concat(d.street,' ',d.city,' ',d.state,' ',d.post_code) as merchant_address,
			e.team_name,
			f.total_w_tax
				
			FROM
			".$table_prefix."driver_task a
					
			LEFT JOIN ".$table_prefix."driver b
			ON
			b.driver_id=a.driver_id
			
			left join ".$table_prefix."order c
			ON 
			c.order_id=a.order_id
			
			left join ".$table_prefix."merchant d
			ON 
			d.merchant_id=c.merchant_id
			
			left join ".$table_prefix."driver_team e
			ON 
			e.team_id=a.team_id
			
			left join mt_order f
			ON 
			f.order_id=a.order_id
			
		";
		echo "ALTER view driver_task_view..<br/>";
		$DbExt->qry($stmt);
		echo "(Done)<br/>";
		
		$stmt="
		Create OR replace view ".$table_prefix."driver_order_view as
		select a.order_id,
		a.client_id,
		b.device_platform,
		b.device_id,
		b.enabled_push,
		b.status,
		b.client_name
		
		from ".$table_prefix."order a
		LEFT JOIN ".$table_prefix."mobile_registered_view b
		ON
		a.client_id=b.client_id
		WHERE
		b.status='active'
		";
				
		if ( Driver::hasModuleAddon('mobileapp')){
			echo "ALTER view driver_order_view..<br/>";
			$DbExt->qry($stmt);
			echo "(Done)<br/>";
		}
		
		echo "(FINISH)<br/>";  		
	} /*end index*/
	
	public function setIncrement($table='', $field_name='')
	{
		$DbExt=new DbExt;
		$prefix=Yii::app()->db->tablePrefix;		
		
		$table=$prefix.$table;
		$stmt="ALTER TABLE `$table` CHANGE `$field_name` `$field_name` INT(14) NOT NULL AUTO_INCREMENT;";
		dump($stmt);		
		echo "Altering table $table<br/>";
		$DbExt->qry($stmt);
	}
	
	public function addIndex($table='',$index_name='')
	{
		$DbExt=new DbExt;
		$prefix=Yii::app()->db->tablePrefix;		
		
		$table=$prefix.$table;
		
		$stmt="
		SHOW INDEX FROM $table
		";		
		$found=false;
		if ( $res=$DbExt->rst($stmt)){
			foreach ($res as $val) {				
				if ( $val['Key_name']==$index_name){
					$found=true;
					break;
				}
			}
		} 
		
		if ($found==false){
			echo "create index<br>";
			$stmt_index="ALTER TABLE $table ADD INDEX ( $index_name ) ";
			dump($stmt_index);
			$DbExt->qry($stmt_index);
			echo "Creating Index $index_name on $table <br/>";		
            echo "(Done)<br/>";		
		} else echo "index exist $index_name<br>";
	}
	
	public function alterTable($table='',$new_field='')
	{
		$DbExt=new DbExt;
		$prefix=Yii::app()->db->tablePrefix;		
		$existing_field='';
		if ( $res = Yii::app()->functions->checkTableStructure($table)){
			foreach ($res as $val) {								
				$existing_field[$val['Field']]=$val['Field'];
			}			
			foreach ($new_field as $key_new=>$val_new) {				
				if (!in_array($key_new,$existing_field)){
					echo "Creating field $key_new <br/>";
					$stmt_alter="ALTER TABLE ".$prefix."$table ADD $key_new ".$new_field[$key_new];
					dump($stmt_alter);
				    if ($DbExt->qry($stmt_alter)){
					   echo "(Done)<br/>";
				   } else echo "(Failed)<br/>";
				} else echo "Field $key_new already exist<br/>";
			}
		}
	}	
	
} /*end class*/