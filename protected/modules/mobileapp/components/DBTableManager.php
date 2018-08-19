<?php
class DBTableManager
{
	public function __construct()
	{		
	}	
	
	public static function alterTablePages()
	{
		if ($res=FunctionsV3::getLanguageList(false)){
			foreach ($res as $val) {		
				$val=str_replace(" ","_",$val);
				$new_field=array(
				  "lang_title_$val"=>"varchar(255) NOT NULL DEFAULT ''",
				  "lang_content_$val"=>"varchar(255) NOT NULL DEFAULT ''",
				);
				self::alterTable("mobile_pages",$new_field);
			}			
		}
	}
	
	public static function alterTable($table='',$new_field='')
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
					//echo "Creating field $key_new <br/>";
					$stmt_alter="ALTER TABLE ".$prefix."$table ADD $key_new ".$new_field[$key_new];
					//dump($stmt_alter);
				    if ($DbExt->qry($stmt_alter)){
					   //echo "(Done)<br/>";
				   } //else echo "(Failed)<br/>";
				} //else echo "Field $key_new already exist<br/>";
			}
		}
	}	
	
} /*end class*/