<?php
class LocationController extends CController
{
	public function actionIndex()
	{		
		$DbExt = new DbExt;		
		
		/*MERCHANT ID TO COPY*/
		$merchant_id = 1; 
		
		$stmt="SELECT * FROM
		{{location_rate}}
		WHERE
		merchant_id = ".FunctionsV3::q($merchant_id)."
		LIMIT 0,1
		";
		if ( $res=$DbExt->rst($stmt)){			
			$stmt2="
			SELECT merchant_id FROM
			{{merchant}}
			WHERE
			merchant_id NOT IN ('".$merchant_id."')
			ORDER BY merchant_id ASC			
			";
			if ($res2=$DbExt->rst($stmt2)){
				foreach ($res2 as $val) {
					$merchant_id_transfer = $val['merchant_id'];
										
					/*DELETE CURRENT LOCATION RATE*/
					$DbExt->qry("
					DELETE FROM {{location_rate}}
					WHERE
					merchant_id =".FunctionsV3::q($merchant_id_transfer)."
					");
					
					/*NOW COPY THE EXISTING LOCATION RATE*/
					$stmt_copy = "
					INSERT INTO {{location_rate}} ( 
					  merchant_id,
					  country_id,
					  state_id,
					  city_id,
					  area_id,
					  fee,
					  sequence,
					  date_created,
					  date_modified,
					  ip_address
					)
					
					SELECT
					  '".$merchant_id_transfer."',
					  country_id,
					  state_id,
					  city_id,
					  area_id,
					  fee,
					  sequence,
					  date_created,
					  date_modified,
					  ip_address
					  FROM {{location_rate}}
					  WHERE merchant_id = ".FunctionsV3::q($merchant_id)."
					";
					dump($stmt_copy);
					$DbExt->qry($stmt_copy);
					dump("DONE copying to merchant id : $merchant_id_transfer");
				}
			}
		} else echo "no location rate to copy";
		
	}
	
} /*end class*/