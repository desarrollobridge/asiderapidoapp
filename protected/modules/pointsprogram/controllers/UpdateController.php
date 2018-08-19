<?php
class UpdateController extends CController
{
	public function actionIndex()
	{
		$prefix=Yii::app()->db->tablePrefix;		
		$table_prefix=$prefix;
		
		$DbExt=new DbExt;
				
		echo "Updating item table<br/>";
		$new_field=array( 
		   'points_earned'=>"int(14) NOT NULL",
           'points_disabled'=>"int(1) NOT NULL DEFAULT '1'"
		);
		$this->alterTable('item',$new_field);
		echo "(Done)<br/>";
		
		echo "Updating order table<br/>";		
		$new_field=array( 
		   'points_discount'=>"float(14,4) NOT NULL"            
		);
		$this->alterTable('order',$new_field);
		echo "(Done)<br/>";
		
		$stmt="			
		CREATE TABLE IF NOT EXISTS ".$table_prefix."points_earn (
		  `id` int(14) NOT NULL AUTO_INCREMENT,
		  `client_id` int(14) NOT NULL,
		  `merchant_id` int(14) NOT NULL,
		  `order_id` int(14) NOT NULL,
		  `total_points_earn` int(14) NOT NULL,
		  `status` varchar(255) NOT NULL DEFAULT 'inactive',
		  `date_created` datetime NOT NULL,
		  `ip_address` varchar(50) NOT NULL,
		  `trans_type` varchar(100) NOT NULL DEFAULT 'order',
		  `points_type` varchar(50) NOT NULL DEFAULT 'earn',
		  PRIMARY KEY (`id`),
		  KEY `client_id` (`client_id`),
		  KEY `order_id` (`order_id`),
		  KEY `total_points_earn` (`total_points_earn`),
		  KEY `trans_type` (`trans_type`),
		  KEY `status` (`status`),
		  KEY `merchant_id` (`merchant_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
		";
			
		echo "Creating Table points_earn..<br/>";	
		$DbExt->qry($stmt);		
		echo "(Done)<br/>";
				
		$stmt="		
		CREATE TABLE IF NOT EXISTS ".$table_prefix."points_expenses (
		  `id` int(14) NOT NULL AUTO_INCREMENT,
		  `client_id` int(14) NOT NULL,
		  `merchant_id` int(14) NOT NULL,
		  `order_id` int(14) NOT NULL,
		  `total_points` int(14) NOT NULL,
		  `status` varchar(255) NOT NULL DEFAULT 'inactive',
		  `date_created` datetime NOT NULL,
		  `ip_address` varchar(50) NOT NULL,
		  `total_points_amt` float(14,4) NOT NULL,
		  `trans_type` varchar(100) NOT NULL DEFAULT 'order',
		  `points_type` varchar(50) NOT NULL DEFAULT 'expenses',
		  PRIMARY KEY (`id`),
		  KEY `client_id` (`client_id`),
		  KEY `merchant_id` (`merchant_id`),
		  KEY `order_id` (`order_id`),
		  KEY `status` (`status`),
		  KEY `date_created` (`date_created`),
		  KEY `trans_type` (`trans_type`),
		  KEY `total_points` (`total_points`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;		
		";
			
		echo "Creating Table points_expenses..<br/>";	
		$DbExt->qry($stmt);		
		echo "(Done)<br/>";
				
		
		echo "Updating points_earn table<br/>";		
		$new_field=array( 
		   'review_id'=>"int(14) NOT NULL DEFAULT '0'",   
		   'booking_id'=>"int(14) NOT NULL DEFAULT '0'",
		   'date_modified'=>"datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
		);
		$this->alterTable('points_earn',$new_field);
		echo "(Done)<br/>";
		
		$stmt="
		create or replace view ".$table_prefix."points_trans as 
		select 
		id,
		client_id,
		merchant_id,
		order_id,
		total_points_earn,
		status,
		date_created,
		points_type,
		trans_type
		from ".$table_prefix."points_earn
		
		UNION
		select 
		id,
		client_id,
		merchant_id,
		order_id,
		total_points,
		status,
		date_created,
		points_type,
		trans_type
		from 
		".$table_prefix."points_expenses
		";
		
		echo "Creating Table points_trans..<br/>";	
		$DbExt->qry($stmt);		
		echo "(Done)<br/>";
		
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