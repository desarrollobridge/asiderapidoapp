<?php
class UpdateController extends CController
{
	public function actionIndex()
	{
		$prefix=Yii::app()->db->tablePrefix;		
		$table_prefix=$prefix;
		
		$DbExt=new DbExt;
		
		echo "Updating merchant table<br/>";
		$new_field=array( 
		   'mobile_session_token'=>"varchar(255) NOT NULL" 
		);
		$this->alterTable('merchant',$new_field);
				
		echo "Updating merchant_user table<br/>";
		$new_field=array( 
		   'mobile_session_token'=>"varchar(255) NOT NULL",
		   'lost_password_code'=>"varchar(20) NOT NULL",
		);
		$this->alterTable('merchant_user',$new_field);
				
		$stmt="		
		CREATE TABLE IF NOT EXISTS ".$table_prefix."mobile_device_merchant (
		  `id` int(14) NOT NULL AUTO_INCREMENT,
		  `merchant_id` int(14) NOT NULL,
		  `user_type` varchar(100) NOT NULL,
		  `merchant_user_id` int(14) NOT NULL,
		  `device_platform` varchar(255) NOT NULL DEFAULT 'Android',
		  `device_id` text NOT NULL,
		  `enabled_push` int(1) NOT NULL DEFAULT '1',
		  `date_created` datetime NOT NULL,
		  `date_modified` datetime NOT NULL,
		  `ip_address` varchar(50) NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `merchant_id` (`merchant_id`),
		  KEY `enabled_push` (`enabled_push`),
		  KEY `device_platform` (`device_platform`),
		  KEY `merchant_user_id` (`merchant_user_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
		";		
		echo "Creating Table mobile_device_merchant..<br/>";	
		$DbExt->qry($stmt);
		echo "(Done)<br/>";    
						
		$stmt="		
		CREATE TABLE IF NOT EXISTS ".$table_prefix."mobile_merchant_pushlogs (
		  `id` int(14) NOT NULL AUTO_INCREMENT,
		  `merchant_id` int(14) NOT NULL,
		  `user_type` varchar(50) NOT NULL,
		  `merchant_user_id` int(14) NOT NULL,
		  `device_platform` varchar(100) NOT NULL,
		  `device_id` text NOT NULL,
		  `push_title` varchar(255) NOT NULL,
		  `push_message` varchar(255) NOT NULL,
		  `push_type` varchar(100) NOT NULL DEFAULT 'order',
		  `status` varchar(255) NOT NULL DEFAULT 'pending',
		  `json_response` text NOT NULL,
		  `date_created` datetime NOT NULL,
		  `date_process` datetime NOT NULL,
		  `ip_address` varchar(50) NOT NULL,
		  `broadcast_id` int(14) NOT NULL,
		  `order_id` int(14) NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `device_platform` (`device_platform`),
		  KEY `push_type` (`push_type`),
		  KEY `status` (`status`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
		";		
		echo "Creating Table mobile_merchant_pushlogs..<br/>";	
		$DbExt->qry($stmt);
		echo "(Done)<br/>";   		
		
		
		/*add status decline and accepted*/
		$stmt_c="SELECT * FROM
		{{order_status}}
		WHERE description IN ('decline','accepted')
		LIMIT 0,2
		";
		$new_status=array('decline','accepted');
		$old_status='';
		if ($res = $stmt_c=$DbExt->rst($stmt_c)){		
			foreach ($res as $val) {
				$old_status[]=$val['description'];
			}
		}		
		foreach ($new_status as $val) {			
			if (!in_array($val,(array)$old_status)){
				$params=array(
				  'description'=>$val,
				  'date_created'=>FunctionsV3::dateNow(),
				  'ip_address'=>$_SERVER['REMOTE_ADDR']
				);
				dump($params);
				$DbExt->insertData("{{order_status}}",$params);
			}
		}
		
		
		echo "Updating mobile_merchant_pushlogs table<br/>";
		$new_field=array( 
		   'booking_id'=>"int(14) NOT NULL"	
		);
		$this->alterTable('mobile_merchant_pushlogs',$new_field);
		
		
		echo "Updating mobile_device_merchant table<br/>";
		$new_field=array( 
		   'status'=>"varchar(100) NOT NULL DEFAULT 'active'"	
		);
		$this->alterTable('mobile_device_merchant',$new_field);
		$this->addIndex('mobile_device_merchant','status');
		
		
		echo "Updating table order_delivery_address<br/>";
		$new_field=array( 
		   'formatted_address'=>"text NOT NULL",
		   'google_lat'=>"varchar(50) NOT NULL",
		   'google_lng'=>"varchar(50) NOT NULL",
		);
		$this->alterTable('order_delivery_address',$new_field);	
		
		
		echo "Updating table bookingtable<br/>";
		$new_field=array( 
		   'client_id'=>"int(14) NOT NULL"		   
		);
		$this->alterTable('bookingtable',$new_field);	
		
		echo "Updating order_history<br/>";
		$new_field=array( 		   
		   'remarks2'=>"varchar(255) NOT NULL",
		   'remarks_args'=>"varchar(255) NOT NULL",
		);
		$this->alterTable('order_history',$new_field);
		
		echo "(FINISH)<br/>";    
		
	} /*end index*/
	
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
		} else echo 'index exist<br>';
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