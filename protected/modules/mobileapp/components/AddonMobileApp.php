<?php
class AddonMobileApp
{
		
	
	public static function moduleBaseUrl()
	{
		return Yii::app()->getBaseUrl(true)."/protected/modules/mobileapp";
	}

	public static function prettyCuisineList($cuisine_json='')
	{		
		if (!empty($cuisine_json)){
			$cuisine_json=!empty($cuisine_json)?json_decode($cuisine_json):false;
			if($cuisine_json!=False){
				$cuisine_list=Yii::app()->functions->Cuisine(true);					
				$cuisine='';
				foreach ($cuisine_json as $cuisine_id) {					
					if(array_key_exists($cuisine_id,(array)$cuisine_list)){
						
						$cuisine_info=Yii::app()->functions->GetCuisine($cuisine_id);
						$cuisine_json_1['cuisine_name_trans']=!empty($cuisine_info['cuisine_name_trans'])?
	    					json_decode($cuisine_info['cuisine_name_trans'],true):'';
	    					
						$cuisine.= self::qTranslate($cuisine_list[$cuisine_id],'cuisine_name',$cuisine_json_1).", ";
					}
				}
				return substr($cuisine,0,-2);
			}
		}
		return false;
	}
				
	public static function q($data)
	{
		return Yii::app()->db->quoteValue($data);
	}
	
    public static function t($message='')
	{
		//return Yii::t("default",$message);		
		return Yii::t("mobile",$message);
	}	
	
	public static function isMerchantOpen($merchant_id='',$check_pre_order=true)
	{
		$open = Yii::app()->functions->isMerchantOpen($merchant_id); 			 			
	    $preorder= Yii::app()->functions->getOption("merchant_preorder",$merchant_id);		
	    if(!$check_pre_order){
	    	$preorder=false;	    	
	    }
	    
		$now=date('Y-m-d');				 			
	    if ( $m_holiday=Yii::app()->functions->getMerchantHoliday($merchant_id)){  
      	   if (in_array($now,(array)$m_holiday)){
      	   	  $open=false;
      	   }
        }
        
        if (!$open){
        	if($preorder){        		
        		$open=true;
        	}
        }
               
        return $open;
	}
	
	public static function isCashAvailable($merchant_id='')
	{
		$cod=self::t("Cash on delivery available");
        if ( Yii::app()->functions->isMerchantCommission($merchant_id)){
        	$paymentgateway=getOptionA('paymentgateway');
        	$paymentgateway=!empty($paymentgateway)?json_decode($paymentgateway,true):false;
        	if($paymentgateway!=false){
        		if(!in_array('cod',(array)$paymentgateway)){
        			$cod='';
        		} else {        			        			
        			if (getOption($merchant_id,'merchant_switch_master_cod')==2){
	        		    $cod='';
        			}        			
        		}
        	}
        } else {
        	$paymentgateway=getOptionA('paymentgateway');
        	$paymentgateway=!empty($paymentgateway)?json_decode($paymentgateway,true):false;
        	if (in_array('cod',(array)$paymentgateway )){        		
	        	if (getOption($merchant_id,'merchant_disabled_cod')!=""){
	        		$cod='';
	        	}
        	} else $cod='';
        }
        return $cod;
	}
	
	public static function getMerchantLogo($merchant_id='')
	{		
		if ( !$logo = getOption($merchant_id,'merchant_photo') ){			
			$logo = Yii::app()->functions->getOptionAdmin('mobile_default_image_not_available');
			if (empty($logo)){
			   $logo="mobile-default-logo.png";
			}
		}		
		$path_to_upload=Yii::getPathOfAlias('webroot')."/upload/";			
		if (file_exists($path_to_upload."/$logo")){
			return Yii::app()->getBaseUrl(true)."/upload/$logo";
		} 
		return self::moduleBaseUrl()."/assets/images/$logo";				
	}
	
	public static function getImage($image='')
	{		
		$default="mobile-default-logo.png";
		$path_to_upload=Yii::getPathOfAlias('webroot')."/upload/";				
		
		if (empty($image)){
			$image=Yii::app()->functions->getOptionAdmin('mobile_default_image_not_available');
		}	
		
		if (!empty($image)){			
			if (file_exists($path_to_upload."/$image")){							
				$default=$image;				
				$url = Yii::app()->getBaseUrl(true)."/upload/$default";
			} else $url=self::moduleBaseUrl()."/assets/images/$default";
		} else $url=self::moduleBaseUrl()."/assets/images/$default";
		return $url;
	}
	
	public static function merchantInformation($merchant_id='')
	{
		$data='';
		
		if ($merchantinfo=Yii::app()->functions->getMerchant($merchant_id)){
					
			$data['merchant_id']=$merchant_id;
			$data['restaurant_name']=stripslashes($merchantinfo['restaurant_name']);
			$data['country']=Yii::app()->functions->countryCodeToFull($merchantinfo['country_code']);
			$data['address']=$merchantinfo['street']." ".$merchantinfo['city']." ".
			$merchantinfo['state']." ".$merchantinfo['post_code']." ".$data['country'];
			
			$data['address']=stripslashes($data['address']);
			
			$data['service']=$merchantinfo['service'];		
			$data['contact_phone']=$merchantinfo['contact_phone'];		
						
			/*check if mechant is open*/			
			$data['open']=AddonMobileApp::isMerchantOpen($merchant_id);
			$data['merchant_close_store']=getOption($merchant_id,'merchant_close_store')=="yes"?true:false;
			
			$minimum_order=getOption($merchant_id,'merchant_minimum_order');
			$minimum_order_raw=$minimum_order;
 			if(!empty($minimum_order)){
	 			$minimum_order=displayPrice(getCurrencyCode(),prettyFormat($minimum_order));
 			}
 			$data['minimum_order']=$minimum_order;
 			$data['minimum_order_raw']=$minimum_order_raw;
 			
 			$data['logo']=AddonMobileApp::getMerchantLogo($merchant_id);
 			
 			$delivery_fee=getOption($merchant_id,'merchant_delivery_charges');
 			$delivery_fee_raw=$delivery_fee;
 			if (!empty($delivery_fee)){
 				$delivery_fee=displayPrice(getCurrencyCode(),prettyFormat($delivery_fee));
 			}
 			$data['delivery_fee']=$delivery_fee;
 			$data['delivery_fee_raw']=$delivery_fee_raw;
			 			
 			$data['ratings']=Yii::app()->functions->getRatings($merchant_id);
 			
 			if ( $res_offers=Yii::app()->functions->getMerchantOffersActive($merchant_id)){ 				
 				unset($res_offers['date_created']);
 				unset($res_offers['date_modified']);
 				unset($res_offers['ip_address']);
 				$res_offers['message']=number_format($res_offers['offer_percentage'],0)."% ".
 				self::t("off today on orders over")." ".
 				displayPrice(getCurrencyCode(),prettyFormat($res_offers['offer_price']));
 				
 				$data['offers_found']=2;
 				$data['offers']=$res_offers;
 			} else $data['offers_found']=1;
 			
 			$data['free_delivery']=1;
 			$price_above=Yii::app()->functions->getOption("free_delivery_above_price",$merchant_id); 			
 			if(is_numeric($price_above) && $price_above>=1){
 				$data['free_delivery']=2;
 				$data['free_price']=$price_above;
 				$data['free_price_pretty']=displayPrice(getCurrencyCode(),prettyFormat($price_above));
 				$data['free_message']=self::t("Free Delivery On Orders Over")." ".
 				displayPrice(getCurrencyCode(),prettyFormat($price_above));
 			}
 			 			
 			$resto_cuisine='';
 			$cuisine_list=Yii::app()->functions->Cuisine(true);	  			
 			$cuisine=!empty($merchantinfo['cuisine'])?(array)json_decode($merchantinfo['cuisine']):false;  
 			if($cuisine!=false){
 				foreach ($cuisine as $valc) {	    						
					if ( array_key_exists($valc,(array)$cuisine_list)){
						$resto_cuisine.=$cuisine_list[$valc].", ";
					}				
				}
				$resto_cuisine=!empty($resto_cuisine)?substr($resto_cuisine,0,-2):'';
 			} 		 		
 			$data['cuisine']=$resto_cuisine;
 			
 			
 			$data['can_checkout']=FunctionsV3::isMerchantcanCheckout($merchant_id); 
 			
 			return $data; 	
		}
		return false;
	}
	
	public static function prettyPrice($amount='')
	{
		if(!empty($amount)){
			return displayPrice(getCurrencyCode(),prettyFormat($amount));
		}
		return 0;
	}
	
	public static function isArray($data='')
	{
		if (is_array($data) && count($data)>=1){
			return true;
		}
		return false;
	}	
	
	public static function getDeliveryCharges($merchant_id='',$unit='',$distance='')
	{				
		$delivery_fee=0;
		
		$default_delivery_charges=getOption($merchant_id,'merchant_delivery_charges');
				
		if ($default_delivery_charges<0){
			return false;
		}

		$FunctionsK=new FunctionsK();
		$delivery_fee=$FunctionsK->getDeliveryChargesByDistance(
    	$merchant_id,
    	$distance,
    	$unit,
    	$default_delivery_charges); 			
    	return array(
    	  'delivery_fee'=>$delivery_fee,
    	  'unit'=>$unit,
    	  'use_distance'=>$distance
    	);	
		
		return false;		
	}
	
	public static function getDistance($merchant_id='',$customer_address='')
	{
		$merchant_distance_type=Yii::app()->functions->getOption("merchant_distance_type",$merchant_id);
		
		$DbExt=new DbExt; 
		$stmt="SELECT concat(street,' ',city,' ',state,' ',post_code,' ',country_code) as merchant_address
		FROM
		{{merchant}}
		WHERE
		merchant_id=".self::q($merchant_id)."
		LIMIT 0,1
		";
		$merchant_address='';
		if($res=$DbExt->rst($stmt)){
			$merchant_address=$res[0]['merchant_address'];
		}
		
		$miles=getDeliveryDistance2($customer_address,$merchant_address); 
		if (self::isArray($miles)){			
			if ( $merchant_distance_type=="km"){
				$unit="km";
				$use_distance=$miles['km'];
			} else {
				$unit='miles';
				$use_distance=$miles['mi'];
			}
			if (preg_match("/ft/i",$miles['mi'])) {
				$unit='ft';
			}
			
			$use_distance=str_replace(array(
			 'ft','mi','km',','
			),'',$use_distance);
			
			return array(
			  'unit'=>$unit,
			  'distance'=>$use_distance
			);
		}	
		return false;
	}
	
	public static function parseValidatorError($error='')
	{
		$error_string='';
		if (is_array($error) && count($error)>=1){
			foreach ($error as $val) {
				$error_string.="$val\n";
			}
		}
		return $error_string;		
	}		
		
	public static function generateUniqueToken($length,$unique_text=''){	
		$key = '';
	    $keys = array_merge(range(0, 9), range('a', 'z'));	
	    for ($i = 0; $i < $length; $i++) {
	        $key .= $keys[array_rand($keys)];
	    }	
	    return $key.md5($unique_text);
	}
			
	public static function computeCart($data='')
	{
		
		if ($data['transaction_type']=="null" || empty($data['transaction_type'])){
			$data['transaction_type']="delivery";
		}
		
		if ($data['delivery_date']=="null" || empty($data['delivery_date'])){
			$data['delivery_date']=date("Y-m-d");
		}
				
		$mtid=$data['merchant_id'];
		
		$cart_content='';
		$subtotal=0;
		$taxable_total=0;
		$item_total=0;
		
		Yii::app()->functions->data="list";
		$subcat_list=Yii::app()->functions->getSubcategory2($mtid);		
		
		$cart=json_decode($data['cart'],true);
		//dump($cart); die();
				
		if (is_array($cart) && count($cart)>=1){			
			/*START COMBINE SAME ITEM IN ONE*/			
			$new_cart = '';$x=0;
			if(is_array($cart) && count($cart)>=1){
			   foreach ($cart as $keycart=>$cart_val) {
			   	    //dump("key=>".$keycart);
			   	    if (is_array($new_cart) && count($new_cart)>=1){
			   	    	
			   	    	$s_id = false; $s_price=false; $s_cookingref=false;
			   	    	$s_ingredients = false; $s_addon=false;
			   	    	
			   	    	$found_key='';
			   	    	
			   	    	foreach ($new_cart as $key_cart=>$val_cart) {		
			   	    		
			   	    		/*CHECK ITEM ID*/	   	    		
			   	    		if ( $val_cart['item_id']==$cart_val['item_id']){
			   	    			$s_id=true;
			   	    		}
			   	    		
			   	    		/*CHECK PRICE*/
			   	    		if ( $val_cart['price']==$cart_val['price']){
			   	    			$s_price=true;
			   	    		}			
			   	    		
			   	    		/*CHECK COOKING REF*/
			   	    		if(is_array($cart_val['cooking_ref']) && count($cart_val['cooking_ref'])>=1){
			   	    		   if(is_array($val_cart['cooking_ref']) && count($val_cart['cooking_ref'])>=1){
			   	    			   if($cart_val['cooking_ref'][0]['value']==$val_cart['cooking_ref'][0]['value']){
			   	    			   	  $s_cookingref=true;
			   	    			   }
			   	    		   }
			   	    		} else {
			   	    		   $c_cookingref_1=count($cart_val['cooking_ref']);	
			   	    		   $c_cookingref_2=count($val_cart['cooking_ref']);	
			   	    		   if($c_cookingref_1==$c_cookingref_2){
			   	    		   	  $s_cookingref=true;
			   	    		   }
			   	    		}			   	    				   	    	
			   	    		
			   	    		/*CHECK INGREDIENTS*/
			   	    		if (is_array($cart_val['ingredients']) && count($cart_val['ingredients'])>=1){
			   	    			if (is_array($val_cart['ingredients']) && count($val_cart['ingredients'])>=1){
			   	    				$i_1 = count($cart_val['ingredients']); $i_2 = count($val_cart['ingredients']);
			   	    				//dump($i_1); dump($i_2);
			   	    				if ( $i_1 == $i_2){			   	    					
			   	    					$flag_ingredients = 0;
			   	    					foreach ($cart_val['ingredients'] as $ingredients_key => $ingredients_val) {
			   	    						if ( $ingredients_val['value']==$val_cart['ingredients'][$ingredients_key]['value']){
			   	    							// do nothing
			   	    						} else $flag_ingredients++;
			   	    					}
			   	    					if($flag_ingredients<=0){
			   	    						$s_ingredients=true;
			   	    					}			   	    				
			   	    				}			   	    			
			   	    			}
			   	    		} else {
			   	    		   $c_ingredients_1=count($cart_val['ingredients']);	
			   	    		   $c_ingredients_2=count($val_cart['ingredients']);	
			   	    		   if($c_ingredients_1==$c_ingredients_2){
			   	    		   	  $s_ingredients=true;
			   	    		   }
			   	    		}

			   	    		/*CHECK ADDON*/			   	    		
			   	    		if (is_array($cart_val['sub_item']) && count($cart_val['sub_item'])>=1){
			   	    			if (is_array($val_cart['sub_item']) && count($val_cart['sub_item'])>=1){
			   	    				$sub_1 = count($cart_val['sub_item']); $sub_2 = count($val_cart['sub_item']);
			   	    				if ($sub_1 == $sub_2){
			   	    					$flag_addon = 0;			   	    					
			   	    					foreach ($cart_val['sub_item'] as $addon_key=>$addon_val) {
			   	    						if ($addon_val['subcat_id']==$val_cart['sub_item'][$addon_key]['subcat_id']){
			   	    							if ($addon_val['value']==$val_cart['sub_item'][$addon_key]['value']){
			   	    								// do nothing
			   	    							} else $flag_addon++; 
			   	    						} else $flag_addon++; 			   	    					
			   	    					}			   	    					
			   	    					if($flag_addon<=0){			   	    					
			   	    						$s_addon=true;
			   	    					}
			   	    				}			   	    			
			   	    			}
			   	    	    } else {
			   	    		   $c_addon_1=count($cart_val['sub_item']);	
			   	    		   $c_addon_2=count($val_cart['sub_item']);	
			   	    		   if($c_addon_1==$c_addon_2){
			   	    		   	  $s_addon=true;
			   	    		   }
			   	    		}	   	    	
			   	    		   	    	
			   	    		if(isset($_GET['debug'])){	
				   	    		if($s_id){
				   	    			dump("OK ID");
				   	    		}			   	    
				   	    		if($s_price){
				   	    			dump("OK PRICE");
				   	    		}
				   	    		if($s_cookingref){
				   	    			dump("OK COOKING REF");
				   	    		}
				   	    		if($s_ingredients){
				   	    			dump("OK INGREDIENTS");
				   	    		}
				   	    		if($s_addon){
				   	    			dump("OK ADDONS");
				   	    		}
			   	    		}
			   	    		
			   	    		if($s_id==true && $s_price==true && $s_cookingref==true 
			   	    		&& $s_ingredients == true && $s_addon == true){
			   	    			$found_key=$key_cart;
			   	    			break;			   	    			
			   	    		}   	    	
			   	    	} /*END FOREACH*/
			   	    	
			   	    	if(strlen($found_key)>=1){
			   	    	    //dump("found".$found_key);
			   	    	    $new_cart[$found_key]['qty']=$new_cart[$found_key]['qty']+$cart_val['qty'];
			   	    	} else {
			   	    		//dump("not");
			   	    		$new_cart[$keycart]=$cart_val;
			   	    	}			   	    
			   	    	
			   	    } else $new_cart[$keycart]=$cart_val;
			   	}	
			}		
			/*END COMBINE SAME ITEM IN ONE*/
			
			/*dump("<h1>NEW CART</h1>");
			dump($new_cart);*/			
			
			if(is_array($new_cart) && count($new_cart)>=1){
				$cart = $new_cart;
			}
		}
		
		if (is_array($cart) && count($cart)>=1){
			foreach ($cart as $val) {
			    				    	
		    	/*group sub item*/
		    	$new_sub='';
		    	if (AddonMobileApp::isArray($val['sub_item'])){
		    		foreach ($val['sub_item'] as $valsubs) {			    			
		    			$new_sub[$valsubs['subcat_id']][]=array( 
		    			  'value'=>$valsubs['value'],
		    			  'qty'=>$valsubs['qty']
		    			);
		    		}
		    		$val['sub_item']=$new_sub;
		    	}		
		    				    				    				   
		    	$item_price=0;
		    	$item_size='';
		    	$temp_price=explode("|",$val['price']);			    	
		    	if (AddonMobileApp::isArray($temp_price)){
		    		$item_price=isset($temp_price[0])?$temp_price[0]:'';
		    		$item_size=isset($temp_price[1])?$temp_price[1]:'';
		    	}			    
		    		    	
		    	$food=Yii::app()->functions->getFoodItem($val['item_id']);			    	
		    			    	
		    	/*check if item qty is less than 1*/
		    	if($val['qty']<1){
		    		$val['qty']=1;
		    	}			    
		    	
		    	$discounted_price=0;
		    	if ($val['discount']>0){
		    		$discounted_price=$item_price-$val['discount'];
		    		$subtotal+=($val['qty']*$discounted_price);
		    	} else {
		    		$subtotal+=($val['qty']*$item_price);
		    	}			   
		    	
		    	/*set the price zero if 2 flavors*/
		    	if ($food['two_flavors']==2){
		    		//$subtotal=0;
		    	}
		    	
		    	//dump("->".$subtotal);		    				    	
		    	
		    	//$subtotal+=($val['qty']*$item_price);
		    	if ( $food['non_taxable']==1){
		    	   $taxable_total=$subtotal;
		    	}
		    	
		    	$item_total+=$val['qty'];
		    				    	
		    	$sub_item='';
		    	
		    	$addon_prices='';
		    	
		    	if(is_array($val['sub_item']) && count($val['sub_item'])>=1){
		    		foreach ($val['sub_item'] as $sub_cat_id=> $valsub0) {			    			
		    			foreach ($valsub0 as $valsub) {				    				
			    			if(!empty($valsub['value'])){
			    				$sub=explode("|",$valsub['value']);
			    				
			    				$sub_item_id=$sub[0];			    				
			    				
			    				if ( $valsub['qty']=="itemqty"){
			    				   $qty=$val['qty'];
			    				} else {
			    					$qty=$valsub['qty'];
			    					if ($qty<1){
			    						$qty=1;
			    						$valsub['qty']=1;
			    					}				    				
			    				}				    			
			    				
			    				$subitem_total=($qty*$sub[1]);
			    				
			    				$addon_prices[]=$sub[1];
			    				
			    				/*check if food item is 2 flavor*/
			    				if ($food['two_flavors']!=2){
			    				   $subtotal+=$subitem_total;
			    				   if ( $food['non_taxable']==1){
				    				   $taxable_total+=$subitem_total;
				    			   }
			    				} else {			    				
			    					/*FIXED 2 FLAVOR NOT ADDING ADDON ITEM*/
									$found_2_flavor=false;
									$t_addon_item=!empty($food['addon_item'])?json_decode($food['addon_item'],true):false;
									$t_two_flavors_position=!empty($food['two_flavors_position'])?json_decode($food['two_flavors_position'],true):false;
									if(is_array($t_two_flavors_position) && count($t_two_flavors_position)>=1){
										foreach ($t_two_flavors_position as $t_two_key => $t_two_val) {
											if ($t_two_val[0]=="left" || $t_two_val[0]=="right"){
												//dump("found key $t_two_key");
												if (isset($t_addon_item[$t_two_key])){
													if (in_array($sub_item_id,(array)$t_addon_item[$t_two_key])){
														$found_2_flavor=true;
													}						    						
												}						    					
											}						    				
										}
									}						    				
									if($found_2_flavor==false){					    				   
									   $subtotal+=$subitem_total;
									   if ( $food['non_taxable']==1){	
										  $taxable_total+=$subitem_total;
									   }
									}			    						
			    				}			    			
			    								    				
			    				$category_name='';
			    				if(array_key_exists($sub_cat_id,(array)$subcat_list)){
			    					$category_name=$subcat_list[$sub_cat_id];
			    				}			    			
			    				
			    				$sub_item[$category_name][]=array(
			    				  'subcat_id'=>$sub_cat_id,
			    				  'category_name'=>$category_name,
			    				  'sub_item_id'=>$sub[0],
			    				  'price'=>$sub[1],
			    				  'price_pretty'=>AddonMobileApp::prettyPrice($sub[1]),
			    				  'qty'=>$valsub['qty'],
			    				  'total'=>$subitem_total,
			    				  'total_pretty'=>AddonMobileApp::prettyPrice($subitem_total),
			    				  'sub_item_name'=>$sub[2],
			    				  'two_flavors_position'=>isset($sub[3])?$sub[3]:'',
			    				);			    				
			    			}
		    			}
		    		}
		    	}
		    	
		    	//dump($subtotal);
		    	
		    	//dump($addon_prices);
		    	/*2 flavor*/
		    	if ($food['two_flavors']==2){
		    		if(is_array($addon_prices) && count($addon_prices)>=1){
		    		   $_subtotal=max($addon_prices);
		    		   if ( $food['non_taxable']==1){
		    	           $taxable_total=$subtotal;
		    	       }
		    		}
		    	}
		    	
		    	$cooking_ref='';
		    	if (AddonMobileApp::isArray($val['cooking_ref'])){
		    		foreach ($val['cooking_ref'] as $valcook) {
		    			$cooking_ref[]=$valcook['value'];
		    		}
		    	}
		    	
		    	$ingredients='';
		    	if (AddonMobileApp::isArray($val['ingredients'])){
		    		foreach ($val['ingredients'] as $valing) {
		    			//$ingredients[]=$valing['ingredients'];
		    			$ingredients[]=$valing['value'];
		    		}
		    	}
		    	
		    	$cooking_ref='';
		    	if(AddonMobileApp::isArray($val['cooking_ref'])){
		    		$cooking_ref=$val['cooking_ref'][0]['value'];
		    	}
		    	$ingredients='';
		    	if(AddonMobileApp::isArray($val['ingredients'])){
		    		foreach ($val['ingredients'] as $val_ing) {
		    			$ingredients[]=$val_ing['value'];
		    		}
		    	}			    

		    	$discount_amt=0;
		    	if (isset($val['discount'])){
		    		$discount_amt=$val['discount'];
		    	}
			    	  			   
		    	$cart_content[]=array(
		    	  'item_id'=>$val['item_id'],
		    	  'item_name'=>$food['item_name'],
		    	  'item_description'=>$food['item_description'],
		    	  'qty'=>$val['qty'],
		    	  'price'=>$item_price,
		    	  'price_pretty'=>AddonMobileApp::prettyPrice($item_price),
		    	  'total'=>$val['qty']*($item_price-$discount_amt),
		    	  'total_pretty'=>AddonMobileApp::prettyPrice($val['qty']* ($item_price-$discount_amt) ),
		    	  'size'=>$item_size,		
		    	  'two_flavors'=>$food['two_flavors'],
		    	  'discount'=>isset($val['discount'])?$val['discount']:'',
			      'discounted_price'=>$discounted_price,
			      'discounted_price_pretty'=>AddonMobileApp::prettyPrice($discounted_price),	
		    	  'cooking_ref'=>$cooking_ref,
		    	  'ingredients'=>$ingredients,
		    	  'order_notes'=>$val['order_notes'],
		    	  'sub_item'=>$sub_item,
		    	  'non_taxable'=>$food['non_taxable'],
		    	  'category_id'=>isset($val['category_id'])?$val['category_id']:'' //cart category
		    	);
		    	
		    } /*end foreach*/
		    
            $ok_distance=2;
		    $delivery_charges=0;
		    $distance='';
		    
		    $search_mode='';		    
		    if(isset($data['search_mode'])){
			    $search_mode=$data['search_mode'];
		    }
		    		    
		    if ($search_mode!="postcode"){
		    
			    $merchant_delivery_distance=getOption($mtid,'merchant_delivery_miles'); 
			    
			    if ( $data['transaction_type']=="delivery" && is_numeric($merchant_delivery_distance) ){					    			    				    	
			    	/*CHANGE TO NEW DISTANCE CALCULATION*/
			    	$client_address=$data['street']." ";
			    	$client_address.=$data['city']." ";
				    $client_address.=$data['state']." ";
				    $client_address.=$data['zipcode']." ";
				    
				    if ($merchantinfo=AddonMobileApp::getMerchantInfo($mtid)){
				    	$merchant_address=$merchantinfo['street']." ";
					    $merchant_address.=$merchantinfo['city']." ";
					    $merchant_address.=$merchantinfo['state']." ";
					    $merchant_address.=$merchantinfo['post_code']." ";
				    }
				    
			    	$merchant_info=array(
					  'merchant_id'=>$mtid,
					  'address'=>$merchant_address,
					  'delivery_fee_raw'=>getOption($mtid,'merchant_delivery_charges')
					);
										
			    	if($distance_new=AddonMobileApp::getDistanceNew($merchant_info,$client_address)){
			    		
			    	   $distance=array(
			    	     'unit'=>$distance_new['distance_type'],
			    	     'distance'=>$distance_new['distance'],
			    	   );
			    	   $delivery_charges=$distance_new['delivery_fee'];
			    	   
			    	   $merchant_delivery_distance=getOption($mtid,'merchant_delivery_miles'); 
			    	   if($distance_new['distance_type_raw']=="ft" || $distance_new['distance_type_raw']=="meter"){
			    	   	 // do nothing
			    	   } else {
			    	   	  if(is_numeric($merchant_delivery_distance)){
				    	   	  if ($merchant_delivery_distance<=$distance_new['distance']){
					    	  	  $ok_distance=1;
					    	  }
			    	   	  }
			    	   }
			    	} else $ok_distance=1;
			    	/*CHANGE TO NEW DISTANCE CALCULATION*/
			    } else {
			    	
			    	/*get default delivery fee*/		    			    	
			    	//if ( $this->data['transaction_type']=="delivery"){			    	
			    	if ( $data['transaction_type']=="delivery"){			    	
				    	$merchant_delivery_charges=getOption($mtid,'merchant_delivery_charges');			    	
				    	if(is_numeric($merchant_delivery_charges)){
				    		$delivery_charges=unPrettyPrice($merchant_delivery_charges);
				    	}			    	
			    	}
			    	
			    }
		    
		    } /*end search mode*/
		    
		    
		    /*CHECK DELIVERY BY LOCATION*/
	       if ( $data['transaction_type']=="delivery"){	
		      if ($search_mode=="postcode"){
		    	   $ok_distance=2;
		    	   $location_type=getOptionA('admin_zipcode_searchtype');
		    	   $location_data = array(
		        	  'location_type'=>$location_type,
		        	  'city_id'=>isset($data['city_id'])?$data['city_id']:'',
		        	  'area_id'=>isset($data['area_id'])?$data['area_id']:'',
		        	  'state_id'=>isset($data['state_id'])?$data['state_id']:'',
		        	  'postal_code'=>isset($data['postal_code'])?$data['postal_code']:'',
		        	);
		        	$delivery_charges = FunctionsV3::getLocationDeliveryFee(
	             	   $mtid,
	             	   $delivery_charges,
	             	   $location_data
	             	);			        	
		       }
		    }		    
		    
		    /*end checking of distance*/
		    		    
		    if ( $data['transaction_type']=="delivery"){	
			    if (AddonMobileApp::enabledExtraCharges()){			            
			            $delivery_date = isset($data['delivery_date'])?$data['delivery_date']:'';
			    		$delivery_time = isset($data['delivery_time'])?$data['delivery_time']:'';
			    		
			    		$delivery_charges = FunctionsV3::extraDeliveryFee(
	    				$mtid,
	    				$delivery_charges,
	    				$delivery_time,
	    				$delivery_date );
			    }		
		    }		    		    
			
			$merchant_tax_percent=0;
			$merchant_tax=getOption($mtid,'merchant_tax');			
			
            /*get merchant offers*/
	    	$discount='';
	    	//if ( $offer=Yii::app()->functions->getMerchantOffersActive($mtid)){	

	    	if (method_exists("FunctionsV3","getMerchantOffersActive")){    	
		    	if ( $offer=FunctionsV3::getMerchantOffersActive($mtid,$data['transaction_type'])){
		    		
		    		$offer_continue=true;
		    		
		    		if(isset($offer['applicable_to'])){
		        	  if (!empty($offer['applicable_to'])){
		        	  	  if ($offer['applicable_to']!="all"){
		        	  	     $applicable_to = json_decode($offer['applicable_to'],true);			        	  	     
		        	  	     if(!in_array($data['transaction_type'],(array)$applicable_to)){
		        	  	     	$offer_continue=false;
		        	  	     }
		        	  	  }
		        	  }	
		        	}
		    		
		        	if($offer_continue){
			    		$merchant_spend_amount=$offer['offer_price'];
			        	$merchant_discount_amount=number_format($offer['offer_percentage'],0);			        	
			        	if ( $subtotal>=$merchant_spend_amount){
			        		$merchant_discount_amount1=$merchant_discount_amount/100;
			        		$discounted_amount=$subtotal*$merchant_discount_amount1;
			        		
			        		$subtotal-=$discounted_amount;
			        		if ( $food['non_taxable']==1){
			        		    $taxable_total-=$discounted_amount;
			        		}		        		
			        		$discount=array(
			        		  'discount'=>$merchant_discount_amount,
			        		  'amount'=>$discounted_amount,
			        		  'amount_pretty'=>AddonMobileApp::prettyPrice($discounted_amount),
			        		  'display'=>self::t("Discount")." ".number_format($offer['offer_percentage'],0)."%"
			        		);
			        	}
		        	}
		    	}			
	    	}
						    	
	        /*check if has offer for free delivery*/
	    	$free_delivery_above_price=getOption($mtid,'free_delivery_above_price');
	        if(is_numeric($free_delivery_above_price)){
	        	if ($subtotal>=$free_delivery_above_price){
	        		$delivery_charges=0;
	        	}			        
	        }
	        
	        /*packaging*/		        
	        $merchant_packaging_charge=getOption($mtid,'merchant_packaging_charge');
	        if ($merchant_packaging_charge>0){
	        	if ( getOption($mtid,'merchant_packaging_increment')==2){		 		      		        		
	        		$merchant_packaging_charge=$merchant_packaging_charge*$item_total;
	        	}
	        } else $merchant_packaging_charge=0;	
	        
	        /*REMOVE PACKAGING IF TRANSACTION IS DINEIN*/
	        if($data['transaction_type']=="dinein"){
	        	$merchant_packaging_charge=0;
	        }
	        	        	        	        
	        //check if has voucher	        
	        if (isset($data['voucher_code'])){
	        	if (!empty($data['voucher_amount'])){
	        		if ( $data['voucher_type']=="fixed amount"){
	        			$subtotal=$subtotal-$data['voucher_amount'];
	        			$taxable_total=$taxable_total-$data['voucher_amount'];
	        		} else {
	        			$voucher_percent=$subtotal*($data['voucher_amount']/100);	        			
	        			$subtotal=$subtotal-$voucher_percent;
	        			$taxable_total=$taxable_total-$voucher_percent;
	        		}	        	
	        	}	        
	        }			     
	        
	        /*CARD FEE*/
	        $card_fee = 0;
	        if(isset($data['card_fee'])){	        	
	        	if ( $data['card_fee'] >=0.0001) {
	        	   $card_fee=$data['card_fee'];
	        	}
	        }	        
	        	        	        
	        /*pts*/
	        if(isset($data['pts_redeem_amount'])){
	        	$data['pts_redeem_amount']=unPrettyPrice($data['pts_redeem_amount']);
	        	if($data['pts_redeem_amount']>0.0001){
	        	   $subtotal=unPrettyPrice($subtotal)-$data['pts_redeem_amount'];
	        	   $taxable_total=$taxable_total-$data['pts_redeem_amount'];
	        	}
	        }   
	        
	        /*dump("taxable_total=>$taxable_total");        
	        dump("delivery_charges=>$delivery_charges");
	        dump("merchant_packaging_charge=>$merchant_packaging_charge");
	        dump($taxable_total+$delivery_charges+$merchant_packaging_charge);*/
	        
	        /*apply tips*/
	        $tips_amount=0;
	        if ( isset($data['tips_percentage'])){
	        	if (is_numeric($data['tips_percentage'])){
	        	    $tips_amount=$subtotal*($data['tips_percentage']/100);		        	    
	        	}
	        }
	        	        
           /*get the tax*/
	        $tax=0;
	        if ( $merchant_tax>0){
	        	$merchant_tax_charges=getOption($mtid,'merchant_tax_charges');
	        	if ( $merchant_tax_charges==2){	        		
	        		$tax=($taxable_total+$merchant_packaging_charge)*($merchant_tax/100);
	        	} else $tax=($taxable_total+$delivery_charges+$merchant_packaging_charge)*($merchant_tax/100);
	        }			    
	        
	        /*dump("sub total => ".$subtotal);
	        die();*/
	        //dump("taxable = ".$taxable_total);
	        //die();
	        		        			    
			$cart_final_content=array(
			  'cart'=>$cart_content,
			  'sub_total'=>array(
			    'amount'=>$subtotal,
			    'amount_pretty'=>AddonMobileApp::prettyPrice($subtotal)
			  )			      
			);				
							
			if (AddonMobileApp::isArray($discount)){
				$cart_final_content['discount']=$discount;
			}

			if ($delivery_charges>0){
				$cart_final_content['delivery_charges']=array(
				  'amount'=>$delivery_charges,
				  'amount_pretty'=>AddonMobileApp::prettyPrice($delivery_charges)
				);
			}
			if ($merchant_packaging_charge>0){
				$cart_final_content['packaging']=array(
				  'amount'=>$merchant_packaging_charge,
				  'amount_pretty'=>AddonMobileApp::prettyPrice($merchant_packaging_charge)
				);					
			}
			if ($tax>0){
				$cart_final_content['tax']=array(
				  'tax'=>$merchant_tax>0?$merchant_tax/100:0,
				  'amount_raw'=>$tax,
				  'amount'=>AddonMobileApp::prettyPrice($tax),
				  'tax_pretty'=>self::t("Tax")." ".$merchant_tax."%"
				);					
			}
			
			if ($tips_amount>0){
			   $cart_final_content['tips']=array(
				  'tips'=>$tips_amount,
				  'tips_pretty'=>AddonMobileApp::prettyPrice($tips_amount),
				  'tips_percentage'=>$data['tips_percentage'],
				  'tips_percentage_pretty'=>self::t("Tip")." (".$data['tips_percentage']."%)",
				);					
			}	
			
			$grand_total=$subtotal+$delivery_charges+$merchant_packaging_charge+$tax+$tips_amount+$card_fee;
			$cart_final_content['grand_total']=array(
			  'amount'=>$grand_total,
			  'amount_pretty'=>AddonMobileApp::prettyPrice($grand_total)
			);
			
			/*validation*/																
			$validation_msg='';
			
			/*$action=Yii::app()->controller->action->id;	
			dump();*/
							
			if ( $data['transaction_type']=="delivery"){
			if ($ok_distance==1){
				$distanceOption=Yii::app()->functions->distanceOption();
				$validation_msg=self::t("Sorry but this merchant delivers only with in ").
				getOption($mtid,'merchant_delivery_miles')." ".$distanceOption[getOption($mtid,'merchant_distance_type')];
			}
			}
			
			if ( $data['transaction_type']=="delivery"){
				/*delivery*/				
				$minimum_order=getOption($mtid,'merchant_minimum_order');
			    $maximum_order=getOption($mtid,'merchant_maximum_order');
			    
			    /*dump($subtotal);
			    dump($minimum_order);*/
			    
			    if(is_numeric($minimum_order)){				    	
			    	if ($subtotal<$minimum_order){
			    		//$validation_msg=self::t("Sorry but Minimum order is")." ".AddonMobileApp::prettyPrice($minimum_order);
			    	}				    
			    }
			    if(is_numeric($maximum_order)){				    	
			    	if ($subtotal>$maximum_order){
			    		//$validation_msg=self::t("Maximum Order is")." ".AddonMobileApp::prettyPrice($maximum_order);
			    	}				    
			    }				    				    
			} else {
				/*pickup*/
				$minimum_order_pickup=getOption($mtid,'merchant_minimum_order_pickup');
			    $maximum_order_pickup=getOption($mtid,'merchant_maximum_order_pickup');
			    if(is_numeric($minimum_order_pickup)){				    	
			    	if ($subtotal<$minimum_order_pickup){
			    		/*$validation_msg=self::t("sorry but the minimum pickup order is")." ".
			    		AddonMobileApp::prettyPrice($minimum_order_pickup);*/
			    	}				    
			    }
			    if(is_numeric($maximum_order_pickup)){				    	
			    	if ($subtotal>$maximum_order_pickup){
			    		/*$validation_msg=self::t("sorry but the maximum pickup order is")." ".
			    		AddonMobileApp::prettyPrice($maximum_order_pickup);*/
			    	}				    
			    }
			}			
			
			return array(			  
			  'cart'=>$cart_final_content,
			  'validation_msg'=>$validation_msg,
			  'distance'=>$distance
			);
				           	    
		} /*end is array*/
		
		return false;
	}
	
	public static function cartMobile2WebFormat($data='',$post_data='')
	{
		//dump($post_data);
		$json_data='';
		if (self::isArray($data['cart']['cart'])){
			foreach ($data['cart']['cart'] as $val) {				
				$sub_item=''; $addon_qty=''; $addon_ids='';
				if (self::isArray($val['sub_item'])){
					foreach ($val['sub_item'] as $key=>$val2) {								
						$addon_item='';	
						$addon_qtys='';
						foreach ($val2 as $val3) {			
							//dump($val3);
							/*$sub_item[$val3['subcat_id']]=array(
							     $val3['sub_item_id']."|".$val3['price']."|".$val3['sub_item_name']
							);*/			
							//$addon_item[]=$val3['sub_item_id']."|".$val3['price']."|".$val3['sub_item_name'];
							$addon_item[]=$val3['sub_item_id']."|".$val3['price']."|".$val3['sub_item_name']."|".$val3['two_flavors_position'];
							//$addon_qty[]=$val3['qty']=="itemqty"?$val['qty']:$val3['qty'];
							$addon_qtys[]=$val3['qty']=="itemqty"?$val['qty']:$val3['qty'];
							$addon_ids[]=$val3['sub_item_id'];
						}						
						$addon_qty[$val3['subcat_id']]=$addon_qtys;
						$sub_item[$val3['subcat_id']]=$addon_item;
					}
				}
				
				/*dump($val);
				die();*/
				
				$json_data[]=array(
				  'item_id'=>$val['item_id'],
				  'merchant_id'=>$post_data['merchant_id'],
				  'discount'=>$val['discount'],
				  'price'=>$val['price']."|".$val['size'],
				  'qty'=>$val['qty'],
				  'two_flavors'=>$val['two_flavors'],
				  'cooking_ref'=>$val['cooking_ref'],
				  'ingredients'=>$val['ingredients'],
				  'notes'=>$val['order_notes'],
				  'order_notes'=>$val['order_notes'],				  
				  'sub_item'=>$sub_item,
				  'addon_qty'=>$addon_qty,
				  'addon_ids'=>$addon_ids,
				  'non_taxable'=>isset($val['non_taxable'])?$val['non_taxable']:1,
				  'category_id'=>isset($val['category_id'])?$val['category_id']:'' //cart category
				);
			}
			//dump($json_data);
			return $json_data;
		}
		return json_encode(array());
	}
	
	public static function getClientTokenInfo($token='')
	{
		if(empty($token)){
			return false;
		}
		$DbExt=new DbExt; 
		$stmt="
		SELECT * FROM
		{{client}}
		WHERE
		token=".self::q($token)."
		LIMIT 0,1
		";				
		if ($res=$DbExt->rst($stmt)){			
			return $res[0];
		}
		return false;
	}
	
	public static function getMerchantPaymentMethod($mtid='')
	{
		$merchant_payment_list='';
						
		//$mobile_payment=array('cod','paypal','pyr','pyp','atz','stp','rzr','');
		$mobile_payment=array('cod','paypal','pyr','pyp','atz','stp','rzr','obd','ocr');
			
		$payment_list=getOptionA('paymentgateway');
		$payment_list=!empty($payment_list)?json_decode($payment_list,true):false;		
		
		$pay_on_delivery_flag=false;
		$paypal_flag=false;
		
		$paypal_credentials='';
		
		$stripe_publish_key='';
		
		/*check master switch for offline payment*/
		if(is_array($payment_list) && count($payment_list)>=1){
		   $payment_list=array_flip($payment_list);
		   
		    $merchant_switch_master_cod=getOption($mtid,'merchant_switch_master_cod');
			if($merchant_switch_master_cod==2){
			   unset($payment_list['cod']);
			}
			$merchant_switch_master_pyr=getOption($mtid,'merchant_switch_master_pyr');
			if($merchant_switch_master_pyr==2){
			   unset($payment_list['pyr']);
			}
		}
		
		if(is_array($payment_list) && count($payment_list)>=1){
		   $payment_list=array_flip($payment_list);
		}		
		
		if (AddonMobileApp::isArray($payment_list)){			
			foreach ($mobile_payment as $val) {
				if(in_array($val,(array)$payment_list)){					
					switch ($val) {
						case "cod":			
						    if (Yii::app()->functions->isMerchantCommission($mtid)){
						    	$merchant_payment_list[]=array(
								  'icon'=>'fa-usd',
								  'value'=>$val,
								  'label'=>self::t("Cash On delivery")
								);
						    	continue;
						    }
							if ( getOption($mtid,'merchant_disabled_cod')!="yes"){
								$merchant_payment_list[]=array(
								  'icon'=>'fa-usd',
								  'value'=>$val,
								  'label'=>self::t("Cash On delivery")
								);
							}
							break;
					
						case "paypal":	
						case "pyp":	
						  
						  if (Yii::app()->functions->isMerchantCommission($mtid)){
						  	  if ( getOptionA('adm_paypal_mobile_enabled')=="yes"){
						  	  	$merchant_payment_list[]=array(
							       'icon'=>'fa-paypal',
							        'value'=>$val,
							        'label'=>self::t("Paypal")
							    );
						  	  }						  
						  	  continue;
						  }
						  
						  if (getOption($mtid,'mt_paypal_mobile_enabled') =="yes"){
						      $merchant_payment_list[]=array(
							     'icon'=>'fa-paypal',
							     'value'=>$val,
							     'label'=>self::t("Paypal")
							  );
						   }
						   break;
						
						case "pyr":	
						    $pay_on_delivery_flag=true;
						   if (Yii::app()->functions->isMerchantCommission($mtid)){
						   	   $merchant_payment_list[]=array(
							    'icon'=>'fa-cc-visa',
							    'value'=>$val,
							    'label'=>self::t("Pay On Delivery")
							   );
						   	   continue;
						   }
						   if ( getOption($mtid,'merchant_payondeliver_enabled')=="yes"){
						      $merchant_payment_list[]=array(
							    'icon'=>'fa-cc-visa',
							    'value'=>$val,
							    'label'=>self::t("Pay On Delivery")
							  );
						   }
						   break;
						   
						case "atz":
							if (Yii::app()->functions->isMerchantCommission($mtid)){
								$merchant_payment_list[]=array(
								   'icon'=>'ion-card',
								   'value'=>$val,
								   'label'=>self::t("Authorize.net")
								);
							} else {
								if(getOption($mtid,'merchant_enabled_autho')=="yes"){
									$merchant_payment_list[]=array(
									   'icon'=>'ion-card',
									   'value'=>$val,
									   'label'=>self::t("Authorize.net")
									);
								}
							}
							break;   
							
						  case "stp":
					   	
							if (Yii::app()->functions->isMerchantCommission($mtid)){
								
								$stripe_enabled=getOptionA('admin_stripe_enabled');
								if($stripe_enabled!="yes"){
									continue;
								}
								
								$mode=Yii::app()->functions->getOptionAdmin('admin_stripe_mode');  
			                    $mode=strtolower($mode);								
								if ( $mode=="sandbox"){
								   	$stripe_publish_key=getOptionA('admin_sandbox_stripe_pub_key');
								} else {
									$stripe_publish_key=getOptionA('admin_live_stripe_pub_key');
								}
								if(!empty($stripe_publish_key)){
									$merchant_payment_list[]=array(
									   'icon'=>'ion-card',
									   'value'=>$val,
									   'label'=>self::t("Stripe")
									);
								}
							} else {
								if(getOption($mtid,'stripe_enabled')=="yes"){
									
									$stripe_enabled=getOption($mtid,'stripe_enabled');
									if($stripe_enabled!="yes"){
										continue;
									}
								
									$mode=Yii::app()->functions->getOption('stripe_mode',$mtid);   
				                    $mode=strtolower($mode);
				                    if ( $mode=="sandbox"){
									   $stripe_publish_key=getOption($mtid,'sandbox_stripe_pub_key');
				                    } else {
				                       $stripe_publish_key=getOption($mtid,'live_stripe_pub_key'); 
				                    }
									if(!empty($stripe_publish_key)){
										$merchant_payment_list[]=array(
										   'icon'=>'ion-card',
										   'value'=>$val,
										   'label'=>self::t("Stripe")
										);
									}
								}
							}
							break;	
						         
						
						 case "rzr":
					    						    
					   	  if (Yii::app()->functions->isMerchantCommission($mtid)){
					   	  	 /*commission*/
					   	  	 $enabled=getOptionA('admin_rzr_enabled');
					   	  	 $mode=getOptionA('admin_rzr_mode');
					   	  	 if($enabled==2){					   	  	 	
					   	  	 	if($mode=="sandbox"){
					   	  	 		$razor_key=getOptionA('admin_razor_key_id_sanbox');
					   	  	 		$razor_secret=getOptionA('admin_razor_secret_key_sanbox');
					   	  	 	} else {
					   	  	 		$razor_key=getOptionA('admin_razor_key_id_live');
					   	  	 		$razor_secret=getOptionA('admin_razor_secret_key_live');
					   	  	 	}	
					   	  	 	
					   	  	 	$merchant_payment_list[]=array(
								   'icon'=>'ion-card',
								   'value'=>$val,
								   'label'=>AddonMobileApp::t("Razorpay")
								);
					   	  	 					   	  	 
					   	  	 }
					   	  } else {
					   	  	 /*merchant*/					   	  	 
					   	  	 $enabled=getOptionA('merchant_rzr_enabled');
					   	  	 $mode=getOptionA('merchant_rzr_mode');
					   	  	 if($enabled==2){					   	  	 	
					   	  	 	if($mode=="sandbox"){
					   	  	 		$razor_key=getOptionA('merchant_razor_key_id_sanbox');
					   	  	 		$razor_secret=getOptionA('merchant_razor_secret_key_sanbox');
					   	  	 	} else {
					   	  	 		$razor_key=getOptionA('merchant_razor_key_id_live');
					   	  	 		$razor_secret=getOptionA('merchant_razor_secret_key_live');
					   	  	 	}	
					   	  	 	
					   	  	 	$merchant_payment_list[]=array(
								   'icon'=>'ion-card',
								   'value'=>$val,
								   'label'=>AddonMobileApp::t("Razorpay")
								);
					   	  	 					   	  	 
					   	  	 }
					   	  }					
					   	  
					   	  break;	
					   	  
					   	case "obd":					   		
					   		if (Yii::app()->functions->isMerchantCommission($mtid)){
					   		   $obd_enabled=getOptionA('admin_bankdeposit_enabled');
					   		   if($obd_enabled=="yes"){
					   		   	 $merchant_payment_list[]=array(
								   'icon'=>'ion-card',
								   'value'=>$val,
								   'label'=>self::t("Offline Bank Deposit")
								 );
					   		   }					   		
					   		} else {
					   		   $obd_enabled=getOption($mtid,'merchant_bankdeposit_enabled');
					   		   if($obd_enabled=="yes"){
					   		   	  $merchant_payment_list[]=array(
								   'icon'=>'ion-card',
								   'value'=>$val,
								   'label'=>AddonMobileApp::t("Offline Bank Deposit")
								 );
					   		   }
					   		}					   	
					   		break;
					   		
					   	case "ocr":						   	    
					   	    if (Yii::app()->functions->isMerchantCommission($mtid)){
					   	    	$switch_master_ccr=getOption($mtid,'merchant_switch_master_ccr');					   	    	
					   	    	if($switch_master_ccr!=2){
						   	    	 $merchant_payment_list[]=array(
									   'icon'=>'ion-card',
									   'value'=>$val,
									   'label'=>self::t("Offline Credit Card")
									 );
					   	    	}
					   	    } else {
					   	    	$switch_master_ccr=getOption($mtid,'merchant_switch_master_ccr');
					   	    	if($switch_master_ccr!=2){
					   	    	   	if ( getOption($mtid,'merchant_disabled_ccr')!="yes"){
					   	    	   		$merchant_payment_list[]=array(
										   'icon'=>'ion-card',
										   'value'=>$val,
										   'label'=>self::t("Offline Credit Card")
										);
					   	    	   	}					   	    	
					   	    	}					   	    
					   	    }					
					   	    break;  
							
						default:
							break;
					}					
				}			
			}
			
			if (AddonMobileApp::isArray($merchant_payment_list)){				
				return $merchant_payment_list;
			}	
		} 
		return false;
	}
	
	public static function getOperationalHours($merchant_id='')
	{
        $stores_open_day=Yii::app()->functions->getOption("stores_open_day",$merchant_id);
		$stores_open_starts=Yii::app()->functions->getOption("stores_open_starts",$merchant_id);
		$stores_open_ends=Yii::app()->functions->getOption("stores_open_ends",$merchant_id);
		$stores_open_custom_text=Yii::app()->functions->getOption("stores_open_custom_text",$merchant_id);
		
		$stores_open_day=!empty($stores_open_day)?(array)json_decode($stores_open_day):false;
		$stores_open_starts=!empty($stores_open_starts)?(array)json_decode($stores_open_starts):false;
		$stores_open_ends=!empty($stores_open_ends)?(array)json_decode($stores_open_ends):false;
		$stores_open_custom_text=!empty($stores_open_custom_text)?(array)json_decode($stores_open_custom_text):false;
		
		
		$stores_open_pm_start=Yii::app()->functions->getOption("stores_open_pm_start",$merchant_id);
		$stores_open_pm_start=!empty($stores_open_pm_start)?(array)json_decode($stores_open_pm_start):false;
		
		$stores_open_pm_ends=Yii::app()->functions->getOption("stores_open_pm_ends",$merchant_id);
		$stores_open_pm_ends=!empty($stores_open_pm_ends)?(array)json_decode($stores_open_pm_ends):false;		
						
		$tip='';						
		$open_starts='';
		$open_ends='';
		$open_text='';		
		if (is_array($stores_open_day) && count($stores_open_day)>=1){
			foreach ($stores_open_day as $val_open) {	
				if (array_key_exists($val_open,(array)$stores_open_starts)){
					$open_starts=timeFormat($stores_open_starts[$val_open],true);
				}							
				if (array_key_exists($val_open,(array)$stores_open_ends)){
					$open_ends=timeFormat($stores_open_ends[$val_open],true);
				}							
				if (array_key_exists($val_open,(array)$stores_open_custom_text)){
					$open_text=$stores_open_custom_text[$val_open];
				}					
				
				$pm_starts=''; $pm_ends=''; $pm_opens='';
				if (array_key_exists($val_open,(array)$stores_open_pm_start)){
					$pm_starts=timeFormat($stores_open_pm_start[$val_open],true);
				}											
				if (array_key_exists($val_open,(array)$stores_open_pm_ends)){
					$pm_ends=timeFormat($stores_open_pm_ends[$val_open],true);
				}								
							
				$full_time='';
				if (!empty($open_starts) && !empty($open_ends)){					
					$full_time=$open_starts." - ".$open_ends."&nbsp;&nbsp;";
				}			
				if (!empty($pm_starts) && !empty($pm_ends)){
					if ( !empty($full_time)){
						$full_time.=" / ";
					}				
					$full_time.="$pm_starts - $pm_ends";
				}
																				
				$tip.= ucwords(self::t($val_open))." ".$full_time." ".$open_text."<br/>";
				
				$open_starts='';
		        $open_ends='';
		        $open_text='';
			}
		} else $tip=self::t("Not available.");
		return $tip;
	}	
	
	public static function previewMerchantReview($mtid='')
	{
		$DbExt=new DbExt; 
		$stmt="
		SELECT a.client_id,
		a.date_created,
		(
		  select first_name
		  from {{client}}
		  where
		  client_id=a.client_id
		) as client_name
		,
		(
		  select count(*) as total_review
		  from {{review}}
		  where
		  merchant_id=".self::q($mtid)."
		) as total_review
		 FROM
		{{review}} a
		WHERE
		merchant_id=".self::q($mtid)."
		AND status IN ('publish','published')
		ORDER BY date_created DESC
		LIMIT 0,1
		";
		if ( $res=$DbExt->rst($stmt)){
			return $res[0];
		}
		return false;
	}
	
	public static function getOrderDetails($order_id='')
	{
		$DbExt=new DbExt; 
		$stmt="
		SELECT a.item_id, a.item_name,
		a.qty,
		(
		  select total_w_tax 
		  from {{order}}
		  where
		  order_id= a.order_id
		) as total_w_tax
		FROM
		{{order_details}} a
		WHERE
		order_id=".self::q($order_id)."
		ORDER BY id ASC
		";
		if ($res=$DbExt->rst($stmt)){
			return $res;
		}
		return false;
	}
	
    public static function getAddressBook($client_id='')
    {
    	$db_ext=new DbExt;    	
    	/*$stmt="SELECT  
    	       concat(street,' ',city,' ',state,' ',zipcode) as address,
    	       id,location_name,country_code,as_default,
    	       street,city,state,zipcode,location_name
    	       FROM
    	       {{address_book}}
    	       WHERE
    	       client_id =".self::q($client_id)."
    	       ORDER BY id DESC    	       
    	";*/    	    	
    	$stmt="SELECT  
    	       concat(street,' ',city,' ',state,' ',zipcode) as address,
    	       a.id,
    	       a.location_name,
    	       a.country_code,
    	       a.as_default,
    	       a.street,
    	       a.city,
    	       a.state,
    	       a.zipcode,
    	       a.location_name,
    	       (
    	       select contact_phone from {{client}} where client_id=a.client_id limit 0,1
    	       ) as contact_phone
    	       FROM
    	       {{address_book}} a
    	       WHERE
    	       client_id =".self::q($client_id)."
    	       ORDER BY id DESC    	       
    	";
    	if ($res=$db_ext->rst($stmt)){    		
    		return $res;
    	}
    	return false;
    } 	        	
    
    public static function hasAddressBook($client_id='')
    {
    	$db_ext=new DbExt;    	
    	$stmt="SELECT * FROM
    	{{address_book}}
    	WHERE client_id =".self::q($client_id)."
    	LIMIT 0,1
    	";    	    	
    	if ($res=$db_ext->rst($stmt)){    		
    		return $res;
    	}
    	return false;
    }
    
    public static function checkifEmailExists($email='')
    {
    	$db_ext=new DbExt;    	
    	$stmt="SELECT client_id,email_address,first_name,last_name,
    	password FROM
    	{{client}}
    	WHERE email_address =".self::q($email)."
    	LIMIT 0,1
    	";    	    	
    	if ($res=$db_ext->rst($stmt)){    		
    		return $res[0];
    	}
    	return false;
    }
    
    public static function getDeviceID($device_id='')
    {
    	$db_ext=new DbExt;    	
    	$stmt="SELECT * FROM
    	{{mobile_registered}}
    	WHERE
    	device_id=".self::q($device_id)."
    	LIMIT 0,1
    	";    	    	
    	if ($res=$db_ext->rst($stmt)){    		
    		return $res[0];
    	}
    	return false;
    }
    
   public static function getMerchantInfo($merchant_id='')
    {
    	$db_ext=new DbExt;    	
    	$stmt="SELECT * FROM
    	{{merchant}}
    	WHERE
    	merchant_id=".self::q($merchant_id)."
    	LIMIT 0,1
    	";    	    	
    	if ($res=$db_ext->rst($stmt)){    		
    		return $res[0];
    	}
    	return false;
    }    
    
    public static function updateDeviceInfo($device_id='',$client_id='')
    {    	    	
    	
    }
    
    public static function getDeviceByID($id='')
    {
    	$db_ext=new DbExt;    	
    	$stmt="SELECT * FROM
    	{{client}}
    	WHERE
    	client_id=".self::q($id)."
    	LIMIT 0,1
    	";    	    	
    	if ($res=$db_ext->rst($stmt)){    		
    		return $res[0];
    	}
    	return false;
    }    
    
    public static function sendPush($platform='Android',$api_key='',$device_id='',$message='')
    {    	
    	if (empty($api_key)){
    		return array(
    		  'success'=>0,
    		  'results'=>array(
    		     array(
    		       'error'=>'missing api key'
    		     )
    		  )
    		);
    	}
    	if (empty($device_id)){
    		return array(
    		  'success'=>0,
    		  'results'=>array(
    		     array(
    		       'error'=>'missing device id'
    		     )
    		  )
    		);
    	}
    	    	
    	$url = 'https://android.googleapis.com/gcm/send';
		$fields = array(
           'registration_ids' => array($device_id),
           'data' => $message,
        );
        //dump($fields);
        
        $headers = array(
		  'Authorization: key=' . $api_key,
		  'Content-Type: application/json'
        );
        //dump($headers);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));		
		$result = curl_exec($ch);
		if ($result === FALSE) {
		    //die('Curl failed: ' . curl_error($ch));
		   return array(
    		  'success'=>0,
    		  'results'=>array(
    		     array(
    		       'error'=>'Curl failed: '. curl_error($ch)
    		     )
    		  )
    		);
		}
		
        curl_close($ch);
        //echo $result; 
        $result=!empty($result)?json_decode($result,true):false;
        //dump($result);
        if ($result==false){
        	return array(
    		  'success'=>0,
    		  'results'=>array(
    		     array(
    		       'error'=>'invalid response from push service'
    		     )
    		  )
    		);
        }
        return $result;   
    }
    
    public static function savedOrderPushNotification($data='')
    {    	
    	if(!is_array($data)){
    		return ;
    	}
    	    
    	/*$push_title=getOptionA('mobile_push_order_title');
    	$push_message=getOptionA('mobile_push_order_message');    	
    	
    	if (empty($push_message)){
    		$push_message=$data['remarks'];
    	}
    	
    	if (empty($push_title)){
    		return ;
    	}
    	if (empty($push_message)){
    		return ;
    	}
    	
    	$push_title=Yii::app()->functions->smarty('order_id',$data['order_id'],$push_title);
    	$push_title=Yii::app()->functions->smarty('order_status', t($data['status']) ,$push_title);
    	
    	$push_message=Yii::app()->functions->smarty('order_id',$data['order_id'],$push_message);
    	$push_message=Yii::app()->functions->smarty('order_status', t($data['status']) ,$push_message);
    	
    	$stmt="
    	SELECT a.order_id,
    	a.merchant_id,
    	a.client_id,
    	b.client_name,
    	b.device_platform,
    	b.device_id,
    	b.enabled_push ,
    	b.status as client_status	
    	FROM
    	{{order}} a
    	LEFT JOIN {{mobile_registered_view}} b
    	ON
    	a.client_id=b.client_id
    	WHERE
    	order_id=".q($data['order_id'])."
    	AND 
    	b.status='active'
    	ORDER BY id DESC
    	LIMIT 0,1
    	";
    	$db_ext=new DbExt; 
    	if ( $res=$db_ext->rst($stmt)){
    		$val=$res[0];    		
    		if ($val['enabled_push']==1){
    			$params=array(
    			  'client_id'=>$val['client_id'],
    			  'client_name'=>$val['client_name'],
    			  'device_platform'=>$val['device_platform'],
    			  'device_id'=>$val['device_id'],
    			  'push_title'=>$push_title,
    			  'push_message'=>$push_message,
    			  'date_created'=>date('c'),
    			  'ip_address'=>$_SERVER['REMOTE_ADDR']
    			);    			
    			$db_ext->insertData("{{mobile_push_logs}}",$params);
    		}    	
    	}*/
    }
    
    public static function sendOrderSMS($data='',$info='',$order_id='',$full_data='',$ok_send_notification=true)
    {    	    	
    	if (!self::isArray($data)){
    	    return false;
    	}
    	    	    
    	$merchant_id=isset($info['merchant_id'])?$info['merchant_id']:'';
    	    	
    	if (!is_numeric($merchant_id)){
    		return false;
    	}    
    	
    	$sms_enabled_alert=Yii::app()->functions->getOption("sms_enabled_alert",$merchant_id);
		if ($sms_enabled_alert!=1){
			return false;
		}
				
    	if (isset($data['cart'])){
    		
    		$db_ext=new DbExt; 
    		
    		$sms_provider=Yii::app()->functions->getOptionAdmin('sms_provider');    	    	    	    	
            $sms_provider=strtolower($sms_provider);
    		
            $merchant_notify_number=Yii::app()->functions->getOption("sms_notify_number",$merchant_id);
            $merchant_sms_tpl=Yii::app()->functions->getOption("sms_alert_message",$merchant_id);
                		
    		$client_info=Yii::app()->functions->getClientInfo($info['client_id']);    
    		if ($client_info){
    		    $client_fullname=$client_info['first_name']." ".$client_info['last_name'];
    		} else $client_fullname='';
    		
    		if (!empty($merchant_sms_tpl) && !empty($merchant_notify_number) ){
	    		$item_order='';
	    		$in_msg=t("OrderNo:").$order_id." ";
	            $in_msg.=AddonMobileApp::t("ClientName:").$client_fullname; 
	            $in_msg.=" ";
	            
	    		foreach ($data['cart'] as $val) {	    			
	    			$item_order.="(".$val['qty']."x)".$val['item_name']." ".$val['order_notes'].", ";
	    			if (is_array($val['sub_item']) && count($val['sub_item'])>=1){ 
	    				foreach ($val['sub_item'] as $subcategory=>$sub_val) {      	    				   
	    				   if(self::isArray($sub_val)){	    				   	   
		    				   foreach ($sub_val as $sub_val2) {	
		    				   	   $sub_val2['qty']=$sub_val2['qty']=="itemqty"?$val['qty']:$sub_val2['qty'];
			    				   $item_order.=$sub_val2['category_name'].":";
		        				   $item_order.="(".$sub_val2['qty']."x)".$sub_val2['sub_item_name'];
		        				   $item_order.=",";		        				   
		    				   }		    				   
	    				   }
	    				}
	    			}	
	    		}
	    			    		
	    		$item_order=substr($item_order,0,-1);    		
	    		$merchant_sms_tpl=Yii::app()->functions->smarty("receipt",$in_msg.$item_order,$merchant_sms_tpl); 
	    		
	    		$merchant_sms_tpl=Yii::app()->functions->smarty("orderno",
	    		$order_id,$merchant_sms_tpl);
	    		
	    		$merchant_sms_tpl=Yii::app()->functions->smarty("customername",
	    		$client_fullname,$merchant_sms_tpl);
	    		$merchant_sms_tpl=Yii::app()->functions->smarty("customer-name",
	    		$client_fullname,$merchant_sms_tpl);
	    		
	    		$merchant_sms_tpl=Yii::app()->functions->smarty("payment-type",
	    		$info['payment_type'],$merchant_sms_tpl);
	    		
	    		$merchant_sms_tpl=Yii::app()->functions->smarty("transaction-type",
	    		$info['trans_type'],$merchant_sms_tpl);
	    		
	    		if(isset($full_data['delivery_instruction'])){
	    		$merchant_sms_tpl=Yii::app()->functions->smarty("delivery-instruction",
	    		$full_data['delivery_instruction'],$merchant_sms_tpl);
	    		}
	    		
	    		$merchant_sms_tpl=Yii::app()->functions->smarty("delivery-date",
	    		Yii::app()->functions->translateDate(Yii::app()->functions->FormatDateTime($info['delivery_date']))
	    		,$merchant_sms_tpl);
	    		
	    		$merchant_sms_tpl=Yii::app()->functions->smarty("delivery-time",
	    		$info['delivery_time'],$merchant_sms_tpl);
	    		
	    		$merchant_sms_tpl=Yii::app()->functions->smarty("order-change",
	    		displayPrice(adminCurrencySymbol(),
	    		Yii::app()->functions->standardPrettyFormat($info['order_change']))
	    		,$merchant_sms_tpl);
	    		
	    		if(isset($full_data['contact_phone'])){
	    		$merchant_sms_tpl=Yii::app()->functions->smarty("customermobile",
	    		$full_data['contact_phone'],$merchant_sms_tpl);  		
	    		}
	    		
	    		if(!isset($full_data['street'])){
	    			$full_data['street']='';
	    		}
	    		if(!isset($full_data['city'])){
	    			$full_data['city']='';
	    		}
	    		if(!isset($full_data['state'])){
	    			$full_data['state']='';
	    		}
	    		if(!isset($full_data['zipcode'])){
	    			$full_data['zipcode']='';
	    		}
	    		
	    		$customer_address=$full_data['street']." ";
	    		$customer_address.=$full_data['city']." ";
	    		$customer_address.=$full_data['state']." ";
	    		$customer_address.=$full_data['zipcode']." ";
	    		
	    		$merchant_sms_tpl=Yii::app()->functions->smarty("customeraddress",
	    		$customer_address,$merchant_sms_tpl);  		
	    		
	    		$merchant_sms_tpl=Yii::app()->functions->smarty("amount",
	    		$info['total_w_tax'],$merchant_sms_tpl);  		
	    		
	    		$merchant_sms_tpl=Yii::app()->functions->smarty("website-ddress",
	    		websiteUrl(),$merchant_sms_tpl);  		
	    			    			    		
	    		/*send sms to merchant*/	    		
	    		$merchant_notify_number=explode(",",$merchant_notify_number);
	    		if (self::isArray($merchant_notify_number)){
	    			foreach ($merchant_notify_number as $merchant_number) {	    				
	    				$balance=Yii::app()->functions->getMerchantSMSCredit($merchant_id);	    				
	    				if (is_numeric($balance) && $balance>=1){
	    					
	    					if ($ok_send_notification){	    						    					
		    					$resp=Yii::app()->functions->sendSMS($merchant_number,$merchant_sms_tpl);
					        	$params=array(
					        	  'merchant_id'=>$merchant_id,
					        	  'broadcast_id'=>"999999999",
					        	  'client_id'=>$info['client_id'],
					        	  'client_name'=>$client_fullname,
					        	  'contact_phone'=>$merchant_number,
					        	  'sms_message'=>$merchant_sms_tpl,
					        	  'status'=>$resp['msg'],
					        	  'gateway_response'=>$resp['raw'],
					        	  'date_created'=>AddonMobileApp::dateNow(),
					        	  'date_executed'=>AddonMobileApp::dateNow(),
					        	  'ip_address'=>$_SERVER['REMOTE_ADDR'],
					        	  'gateway'=>$sms_provider
					        	);				        	        
					        	$db_ext->insertData("{{sms_broadcast_details}}",$params);	
	    					} else {
	    						// saved sms for future sending
	    						$params=array(
	    						  'email_type'=>"sms",
	    						  'order_id'=>$order_id,
	    						  'client_email'=>$merchant_number,
	    						  'tpl'=>$merchant_sms_tpl,
	    						  'merchant_id'=>$merchant_id,
	    						  'client_name'=>$client_fullname,
	    						  'gateway'=>$sms_provider
	    						);
	    						$db_ext->insertData("{{mobile_temp_email}}",$params);	
	    					}     	
	    				}
	    			}
	    		}	    		    
    		}
    		
    		
    		if(!isset($full_data['contact_phone'])){
    			$full_data['contact_phone']='';
    		}
    		
    		/*send sms to customer*/
    		$sms_tpl=getOption($merchant_id,'sms_alert_customer');    		
    		$client_contact_phone=$full_data['contact_phone'];    		
    		if (!empty($sms_tpl) && !empty($client_contact_phone)){
    			
    			$sms_tpl=Yii::app()->functions->smarty("customer-name",
	    		$client_fullname,$sms_tpl);  		
	    		
	    		$sms_tpl=Yii::app()->functions->smarty("orderno",
	    		$order_id,$sms_tpl);  		
	    		
	    		$sms_tpl=Yii::app()->functions->smarty("website-address",
	    		websiteUrl(),$sms_tpl);  		
	    		
	    		$sms_tpl=Yii::app()->functions->smarty("payment-type",
	    		$info['payment_type'],$sms_tpl);  	
	    		
	    		$sms_tpl=Yii::app()->functions->smarty("transaction-type",
	    		$info['trans_type'],$sms_tpl);	
	    		
	    		$sms_tpl=Yii::app()->functions->smarty("delivery-instruction",
	    		$full_data['delivery_instruction'],$sms_tpl);
	    		
	    		$sms_tpl=Yii::app()->functions->smarty("delivery-date",
	    		Yii::app()->functions->translateDate(Yii::app()->functions->FormatDateTime($info['delivery_date']))
	    		,$sms_tpl);
	    		
	    		$sms_tpl=Yii::app()->functions->smarty("delivery-time",
	    		$info['delivery_time'],$sms_tpl);
	    		
	    		$sms_tpl=Yii::app()->functions->smarty("order-change",
	    		displayPrice(adminCurrencySymbol(),
	    		Yii::app()->functions->standardPrettyFormat($full_data['order_change']))
	    		,$sms_tpl);
	    		
	    		if ($merchant_info=self::getMerchantInfo($merchant_id)){
	    			$sms_tpl=Yii::app()->functions->smarty("merchantname",
	    		    $merchant_info['restaurant_name'],$sms_tpl);
	    		    
	    		    $sms_tpl=Yii::app()->functions->smarty("merchantphone",
	    		    $merchant_info['restaurant_phone'],$sms_tpl);
	    		    
	    		    $merchant_address=$merchant_info['street']." ";
	    		    $merchant_address.=$merchant_info['city']." ";
	    		    $merchant_address.=$merchant_info['state']." ";
	    		    $merchant_address.=$merchant_info['post_code']." ";
	    		    
	    		    $sms_tpl=Yii::app()->functions->smarty("merchant-address",
	    		    $merchant_address,$sms_tpl);
	    		}    			    			    	
	    			    		
	    		$balance=Yii::app()->functions->getMerchantSMSCredit($merchant_id);		    		
	    		if (is_numeric($balance) && $balance>=1){
	    			
	    			if ($ok_send_notification){
		    			$resp2=Yii::app()->functions->sendSMS($client_contact_phone,$sms_tpl);
		        		$params=array(
			        	  'merchant_id'=>$merchant_id,
			        	  'broadcast_id'=>"999999999",
			        	  'client_id'=>$info['client_id'],
			        	  'client_name'=>$client_fullname,
			        	  'contact_phone'=>$client_contact_phone,
			        	  'sms_message'=>$sms_tpl,
			        	  'status'=>$resp2['msg'],
			        	  'gateway_response'=>$resp2['raw'],
			        	  'date_created'=>AddonMobileApp::dateNow(),
			        	  'date_executed'=>AddonMobileApp::dateNow(),
			        	  'ip_address'=>$_SERVER['REMOTE_ADDR'],
			        	  'gateway'=>$sms_provider
			        	);	  			        	
			        	$db_ext->insertData("{{sms_broadcast_details}}",$params);
	    			} else {	    				
	    				// saved sms for future sending
	    				$params=array(
						  'email_type'=>"sms",
						  'order_id'=>$order_id,
						  'client_email'=>$client_contact_phone,
						  'tpl'=>$sms_tpl,
						  'client_id'=>$info['client_id'],
						  'merchant_id'=>$merchant_id,
						  'client_name'=>$client_fullname,
						  'gateway'=>$sms_provider
						);
						$db_ext->insertData("{{mobile_temp_email}}",$params);	
	    			}	        	
	    		}
    		}    
    		return true;	    		
    	}       	
    	return false;
    }
    
    public static function sendOrderEmail($data='',$info='',$order_id='',$full_data='',$ok_send_notification=true)
    {
    	
    	if (!self::isArray($data)){
    	    return false;
    	}
    	    	        	
    	$merchant_id=isset($info['merchant_id'])?$info['merchant_id']:'';
    	    	
    	if (!is_numeric($merchant_id)){
    		return false;
    	}    
    	    		      
        if($client_info=Yii::app()->functions->getClientInfo($info['client_id'])){
	        $client_fullname=$client_info['first_name']." ".$client_info['last_name'];
	        $client_email=$client_info['email_address'];
        } else {
        	$client_fullname='';
        	$client_email='';
        }    
                
        if($merchant_info=self::getMerchantInfo($merchant_id)){        
	        $merchant_address=$merchant_info['street']." ";
		    $merchant_address.=$merchant_info['city']." ";
		    $merchant_address.=$merchant_info['state']." ";
		    $merchant_address.=$merchant_info['post_code']." ";
        } else $merchant_address='';
	    
        if (isset($full_data['street']) && isset($full_data['city'])){
		    $customer_address=$full_data['street']." ";
			$customer_address.=$full_data['city']." ";
			$customer_address.=$full_data['state']." ";
			$customer_address.=$full_data['zipcode']." ";
        } else $customer_address='';
		    		        
        $print[]=array(
          'label'=>Yii::t("default","Customer Name"),
          'value'=>$client_fullname
        );
                
        $print[]=array(
	         'label'=>Yii::t("default","Merchant Name"),
	         'value'=>$merchant_info['restaurant_name']
	       );
	       
	    if (!empty($merchant_info['abn'])){
		  $print[]=array(
	         'label'=>Yii::t("default","ABN"),
	         'value'=>$merchant_info['abn']
	       );		
	    }
		$print[]=array(
	         'label'=>Yii::t("default","Telephone"),
	         'value'=>$merchant_info['restaurant_phone']
	       );		
		$print[]=array(
	         'label'=>Yii::t("default","Address"),
	         'value'=>$merchant_address
	       );		
		$print[]=array(
	         'label'=>Yii::t("default","TRN Type"),
	         'value'=>$info['trans_type']
	       );		
		$print[]=array(
	         'label'=>Yii::t("default","Payment Type"),
	         'value'=>strtoupper($info['payment_type'])
	       );				
	       	       
	    if ( isset($info['payment_provider_name'])){
	    	$print[]=array(
	         'label'=>Yii::t("default","Card#"),
	         'value'=>strtoupper($info['payment_provider_name'])
	       );
	    }   
	    	    
	    if ( $info['payment_type'] =="pyp"){
	    	$paypal_info=Yii::app()->functions->getPaypalOrderPayment($order_id);
	    	if ($paypal_info){
		    	$print[]=array(
		         'label'=>Yii::t("default","Paypal Transaction ID"),
		         'value'=>isset($paypal_info['TRANSACTIONID'])?$paypal_info['TRANSACTIONID']:''
		        );
	    	}
	    }    
	    
	    $print[]=array(
          'label'=>Yii::t("default","Reference #"),
          'value'=>Yii::app()->functions->formatOrderNumber($order_id)
        );

        $trn_date=date('M d,Y G:i:s',strtotime($info['date_created']));
	    $trn_date= Yii::app()->functions->translateDate($trn_date);
	         
        $print[]=array(
         'label'=>Yii::t("default","TRN Date"),
         'value'=>$trn_date
        );       
        
        if ($info['trans_type']=="delivery"){
        	$print[]=array(
		         'label'=>Yii::t("default","Delivery Date"),
		         'value'=>$info['delivery_date']
		     );        
                
	        if (isset($info['delivery_time'])){
	        	if (!empty($info['delivery_time'])){
	        		$print[]=array(
			         'label'=>Yii::t("default","Delivery Time"),
			         'value'=>$info['delivery_time']
			       );
	        	}     
	        } 
	        		        
	        $print[]=array(
			  'label'=>Yii::t("default","Deliver to"),
			  'value'=>$customer_address
			);
					
			$print[]=array(
			  'label'=>Yii::t("default","Delivery Instruction"),
			  'value'=>$info['delivery_instruction']
			);
			
			$print[]=array(
			  'label'=>Yii::t("default","Location Name"),
			  'value'=>$full_data['location_name']
			);
			
			$print[]=array(
			  'label'=>Yii::t("default","Contact Number"),
			  'value'=>$full_data['contact_phone']
			);
			
			if (isset($full_data['order_change'])){
			   if ($full_data['order_change']>0){
			   	    $print[]=array(
					  'label'=>Yii::t("default","Change"),
					  'value'=>normalPrettyPrice($full_data['order_change'])
					);
			   }		
			}    		
			
        } else {
        	//pickup
        	
        	if (isset($full_data['contact_phone'])){
        	$print[]=array(
			  'label'=>Yii::t("default","Contact Number"),
			  'value'=>$full_data['contact_phone']
			);
        	}
			
			$print[]=array(
		         'label'=>Yii::t("default","Pickup Date"),
		         'value'=>$info['delivery_date']
		     );        
                
	        if (isset($info['delivery_time'])){
	        	if (!empty($info['delivery_time'])){
	        		$print[]=array(
			         'label'=>Yii::t("default","Pickup Time"),
			         'value'=>$info['delivery_time']
			       );
	        	}     
	        }   
	        
	        if (isset($full_data['order_change'])){
			   if ($full_data['order_change']>0){
			   	    $print[]=array(
					  'label'=>Yii::t("default","Change"),
					  'value'=>normalPrettyPrice($full_data['order_change'])
					);
			   }		
			}    		
        	
        }    
        				                
        $item='';        
        if (self::isArray($data['cart'])){
        	foreach ($data['cart'] as $val) {
        		
        		$sub_item='';
        		
        		if (self::isArray($val['sub_item'])){
        			foreach ($val['sub_item'] as $val2) {
        				if (self::isArray($val2)){
        				    foreach ($val2 as $val3) {        				       
        				       $sub_item[]=array(
        				         'addon_name'=>$val3['sub_item_name'],
        				         'addon_category'=>$val3['category_name'],
        				         'addon_qty'=>$val3['qty']=="itemqty"?$val['qty']:$val3['qty'],
        				         'addon_price'=>$val3['price']
        				       );
        				    }	
        				}        			
        			}
        		}
        		
        		$item[]=array(
        		  'item_id'=>$val['item_id'],
        		  'item_name'=>$val['item_name'],
        		  'size_words'=>$val['size'],
        		  'qty'=>$val['qty'],
        		  'normal_price'=>$val['price'],
        		  'discounted_price'=>$val['discounted_price']>0?$val['discounted_price']:$val['price'],
        		  'order_notes'=>$val['order_notes'],
        		  'cooking_ref'=>$val['cooking_ref'],
        		  'ingredients'=>$val['ingredients'],
        		  'non_taxable'=>1,
        		  'sub_item'=>$sub_item
        		);
        	}
        }
        
        $paypal_card_fee=isset($info['card_fee'])?$info['card_fee']:0;  
        
        $total=array(
          'mid'=>$merchant_id,
          'subtotal'=>$data['sub_total']['amount'],
          'delivery_charges'=>isset($data['delivery_charges']['amount'])?$data['delivery_charges']['amount']:'',
          /*'taxable_total'=>$data['tax']['amount_raw'],
          'tax_amt'=>str_replace(array("Tax","%"),'',$data['tax']['tax_pretty']),*/
          'total'=>$data['grand_total']['amount']+$paypal_card_fee,
          'curr'=>Yii::app()->functions->adminCurrencySymbol(),
          'packaging'=>isset($data['packaging']['amount'])?$data['packaging']['amount']:'',
          'card_fee'=>$paypal_card_fee
        );    
        
        if(isset($data['tax'])){
        	$total['taxable_total']=$data['tax']['amount_raw'];
        	$total['tax_amt']=str_replace(array("Tax","%"),'',$data['tax']['tax_pretty']);
        }
        
        /*dump($data);
        dump($total);*/
        
        /*tips*/        
        if (isset($data['tips'])){
        	$total['tips_percent']=$data['tips']['tips_percentage']."%";
            $total['tips']=$data['tips']['tips'];
        }                
                                                     
    	$receipt=EmailTPL::salesReceipt($print,array(
    	  'item'=>$item,
    	  'total'=>$total
    	));    	    	
    	$tpl=Yii::app()->functions->getOption("receipt_content",$merchant_id);
		if (empty($tpl)){
			$tpl=EmailTPL::receiptTPL();
		}
		$tpl=Yii::app()->functions->smarty('receipt',$receipt,$tpl);
        $tpl=Yii::app()->functions->smarty('customer-name',$client_fullname,$tpl);
        $tpl=Yii::app()->functions->smarty('receipt-number',Yii::app()->functions->formatOrderNumber($order_id),$tpl);
    	    	
    	$receipt_sender=Yii::app()->functions->getOption("receipt_sender",$merchant_id);
		$receipt_subject=Yii::app()->functions->getOption("receipt_subject",$merchant_id);
		if (empty($receipt_subject)){	
			$receipt_subject=getOptionA('receipt_default_subject');
			if (empty($receipt_subject)){
			    $receipt_subject="We have receive your order";
			}
		}
		if (empty($receipt_sender)){
			$receipt_sender='no-reply@'.$_SERVER['HTTP_HOST'];
		}
				
		/*dump($tpl);
		die();*/
		
		$db_ext=new DbExt; 
				
		if ($ok_send_notification){
		   sendEmail($client_email,$receipt_sender,$receipt_subject,$tpl);
		} else {
			/// saved to database and send it once actually paid
			$params=array(
			  'order_id'=>$order_id,
			  'client_email'=>$client_email,
			  'receipt_sender'=>$receipt_sender,
			  'receipt_subject'=>$receipt_subject,
			  'tpl'=>$tpl			  
			);
			$db_ext->insertData("{{mobile_temp_email}}",$params);
		}   
		
		/*send email to merchant address*/ 
		$merchant_notify_email=Yii::app()->functions->getOption("merchant_notify_email",$merchant_id);    
        $enabled_alert_notification=Yii::app()->functions->getOption("enabled_alert_notification",$merchant_id);            
        
        if ( $enabled_alert_notification==""){ 
        	
        	$merchant_receipt_subject=Yii::app()->functions->getOption("merchant_receipt_subject",$merchant_id);
    	
    	    $merchant_receipt_subject=empty($merchant_receipt_subject)?self::t("New Order From").
    	    " ".$client_fullname:$merchant_receipt_subject;
    	
    	    $merchant_receipt_content=Yii::app()->functions->getMerchantReceiptTemplate($merchant_id);
    	    
    	    $final_tpl='';    	
	    	if (!empty($merchant_receipt_content)){
	    		$merchant_token=Yii::app()->functions->getMerchantActivationToken($merchant_id);
	    		$confirmation_link=Yii::app()->getBaseUrl(true)."/store/confirmorder/?id=".$order_id."&token=$merchant_token";
	    		$final_tpl=smarty('receipt-number',Yii::app()->functions->formatOrderNumber($order_id)
	    		,$merchant_receipt_content);    		
	    		$final_tpl=smarty('customer-name',$client_fullname,$final_tpl);
	    		$final_tpl=smarty('receipt',$receipt,$final_tpl); 
	    		$final_tpl=smarty('confirmation-link',$confirmation_link,$final_tpl); 
	    	} else $final_tpl=$tpl;
	    	    	
	    	$global_admin_sender_email=Yii::app()->functions->getOptionAdmin('global_admin_sender_email');
	    	if (empty($global_admin_sender_email)){
	    		$global_admin_sender_email=$receipt_sender;
	    	}     	
	    		    		    	
	    	// fixed if email is multiple
	    	$merchant_notify_email=explode(",",$merchant_notify_email);    	
	    	if (is_array($merchant_notify_email) && count($merchant_notify_email)>=1){
	    		foreach ($merchant_notify_email as $merchant_notify_email_val) {    			
	    			if(!empty($merchant_notify_email_val)){
	    				if ($ok_send_notification){
		    			    sendEmail(trim($merchant_notify_email_val),
		    			    $global_admin_sender_email,$merchant_receipt_subject,$final_tpl);
	    				} else {
	    					/// saved to database and send it once actually paid
	    					$params=array(
							  'order_id'=>$order_id,
							  'client_email'=>$merchant_notify_email_val,
							  'receipt_sender'=>$global_admin_sender_email,
							  'receipt_subject'=>$merchant_receipt_subject,
							  'tpl'=>$final_tpl,
							  'email_type'=>"merchant"
							);
							$db_ext->insertData("{{mobile_temp_email}}",$params);
	    				}	    			
	    			}
	    		}
	    	}    	    	
	    	
        }	
		
    }
    
    public static function processPendingReceiptEmail($order_id='')
    {    	
    	
    	return false;
    	
    	$db_ext=new DbExt; 
    	$stmt="SELECT * FROM
    	{{mobile_temp_email}}
    	WHERE
    	order_id=".self::q($order_id)."
    	AND 
    	status='pending'    	    
    	";
    	if ($res=$db_ext->rst($stmt)){    		
    		foreach ($res as $val) {    			
    			$id=$val['id'];
    			    			    			
    			switch ($val['email_type']) {
    				
    				case "client":   
    				case "merchant": 					    					    			
	    			$send_stats=sendEmail($val['client_email'],$val['receipt_sender'],
	    			$val['receipt_subject'],$val['tpl']);
	    			if($send_stats){
	    				$send_stats='sent';
	    			} else $send_stats='sending failed';    		
	    			
	    			$params=array('status'=>$send_stats);      			
	    			$db_ext->updateData("{{mobile_temp_email}}",$params,'id',$id);
    			    break;

    				case "sms":
    					$params=array(
    					  'merchant_id'=>$val['merchant_id'],
    					  'broadcast_id'=>'999999999',
    					  'client_id'=>$val['client_id'],
    					  'client_name'=>$val['client_name'],
    					  'contact_phone'=>$val['client_email'],
    					  'sms_message'=>$val['tpl'],
    					  'date_created'=>AddonMobileApp::dateNow(),
    					  'ip_address'=>$_SERVER['REMOTE_ADDR'],
    					  'gateway'=>$val['gateway']
    					);    					
    					$db_ext->insertData("{{sms_broadcast_details}}",$params);
    					
    					$params=array('status'=>'saved');      			
	    			    $db_ext->updateData("{{mobile_temp_email}}",$params,'id',$id);
    					break ;	    						    
    			}
    		}
    	}
    	return false;  
    }
    
    public static function platFormList()
    {
    	return array(
	    	1=>"android",
	        2=>'ios',
	        3=>"all platform"
    	);
    }
    
    public static function getBroadcast($broadcast_id='')
    {
    	$stmt="
    	SELECT * FROM
    	{{mobile_broadcast}}
    	WHERE
    	broadcast_id=".self::q($broadcast_id)."
    	";
    	$db_ext=new DbExt; 
    	if ($res=$db_ext->rst($stmt)){
    		return $res;
    	}
    	return false;    
    }
    
    public static function availableLanguages()
    {
    	$lang['en']='English';
    	$stmt="
    	SELECT * FROM
    	{{languages}}
    	WHERE
    	status in ('publish','published')
    	";
    	$db_ext=new DbExt; 
    	if ($res=$db_ext->rst($stmt)){
    		foreach ($res as $val) {
    			$lang[$val['lang_id']]=$val['language_code'];
    		}    		
    	}
    	return $lang;
    }
    
    public static function translateItem($type='category',$string='',$id='',$field1='category_name_trans')
    {    	
    	$lang_id=$_GET['lang_id'];
    	
    	$db_ext=new DbExt; 
    	    	
    	switch ($type) {
    		case "category":    			
    			$stmt="SELECT $field1 FROM
    			{{category}}
    			WHERE
    			cat_id=".self::q($id)."
    			LIMIT 0,1
    			";    		    			
    			break;
    			
    		case "item":	
    		   $stmt="SELECT $field1 FROM
    			{{item}}
    			WHERE
    			item_id=".self::q($id)."
    			LIMIT 0,1
    			";    	
    		    //dump($stmt);	    			
    			break;
    			
    		case "cookingref":	
    		   $stmt="SELECT $field1 FROM
    			{{cooking_ref}}
    			WHERE
    			cook_id=".self::q($id)."
    			LIMIT 0,1
    			";    	
    		    //dump($stmt);	    			
    			break;	
    		
    		case "ingredients":	
    		   $stmt="SELECT $field1 FROM
    			{{ingredients}}
    			WHERE
    			ingredients_id =".self::q($id)."
    			LIMIT 0,1
    			";    	
    		    //dump($stmt);	    			
    			break;			
    			
    			
    		case "subcategory":	
    		   $stmt="SELECT $field1 FROM
    			{{subcategory}}
    			WHERE
    			subcat_id =".self::q($id)."
    			LIMIT 0,1
    			";    	
    		    //dump($stmt);	    			
    			break;				
    	
    		default:
    			break;
    	}
    	
    	if ($res=$db_ext->rst($stmt)){
    		$res=$res[0];
    		//dump($res);
    		$text=!empty($res[$field1])?json_decode($res[$field1],true):false;
    		if ($text!=false){
    			//dump($text);
    			if (array_key_exists($lang_id,(array)$text)){
    				if (!empty($text[$lang_id])){
    					return $text[$lang_id];
    				}    			
    			}    		
    		}    	
    	}
    	return $string;
    }
    
    public static function getVoucherCodeNew($client_id='',$voucher_code='',$merchant_id='')
    {
    	$db_ext=new DbExt;    	
    	$stmt="
    	SELECT a.*,
    	(
    	select count(*) from
    	{{order}}
    	where
    	voucher_code=".self::q($voucher_code)."
    	and
    	client_id=".$client_id."  	
    	LIMIT 0,1
    	) as found,
    	
    	(
    	select count(*) from
    	{{order}}
    	where
    	voucher_code=".self::q($voucher_code)."    	
    	LIMIT 0,1
    	) as number_used    
    	
    	FROM
    	{{voucher_new}} a
    	WHERE
    	voucher_name=".self::q($voucher_code)."
    	AND
    	merchant_id=".self::q($merchant_id)."
    	AND status IN ('publish','published')
    	LIMIT 0,1
    	";    	    	
    	if ($res=$db_ext->rst($stmt)){    		    		
    		return $res[0];
    	}
    	return false;
    } 
    
    public static function getVoucherCodeAdmin($client_id='',$voucher_code='')
    {
    	$db_ext=new DbExt;    	
    	$stmt="
    	SELECT a.*,
    	(
    	select count(*) from
    	{{order}}
    	where
    	voucher_code=".self::q($voucher_code)."
    	and
    	client_id=".$client_id."  	
    	and 
    	status NOT IN ('".initialStatus()."')
    	LIMIT 0,1
    	) as found,
    	
    	(
    	select count(*) from
    	{{order}}
    	where
    	voucher_code=".self::q($voucher_code)."  
    	and 
    	status NOT IN ('".initialStatus()."')
    	LIMIT 0,1
    	) as number_used    	
    	
    	FROM
    	{{voucher_new}} a
    	WHERE
    	voucher_name=".self::q($voucher_code)."
    	AND
    	voucher_owner='admin'
    	AND status IN ('publish','published')
    	LIMIT 0,1
    	";    	     	
    	if ($res=$db_ext->rst($stmt)){    		    		
    		return $res[0];
    	}
    	return false;
    }         
        
    
    public static function getMerchantOffers($merchant_id='')
    {
    	$offer='';
    	$price_above=Yii::app()->functions->getOption("free_delivery_above_price",$merchant_id);
    	if (is_numeric($price_above) && $price_above>=1){
    		$offer[]=self::t("Free Delivery On Orders Over")." ".AddonMobileApp::prettyPrice($price_above);
    	}
    	    	    	
    	if (method_exists("FunctionsV3","getOffersByMerchantNew")){
	    	if($res=FunctionsV3::getOffersByMerchantNew($merchant_id)){
	    	   foreach ($res as $val) {
	    	   	   $offer[] = $val;
	    	   }
	    	}
    	} else {
    		if ( $res=Yii::app()->functions->getMerchantOffersActive($merchant_id)){
    		   $offer[]=number_format($res['offer_percentage'],0)."% ".self::t("Off");
    	    } 
    	}    
    	return $offer;
    }
    
    
    /*VERSION 1.3.3*/
    
    public static function getDistanceNew($merchant_info='',$client_address='')
    {
    	if(!is_array($merchant_info)){
    		return false;
    	}
    	if(empty($client_address)){
    		return false;
    	}
    	$merchant_address=$merchant_info['address'];
    	$mtid=isset($merchant_info['merchant_id'])?$merchant_info['merchant_id']:'';
    	if(empty($mtid)){
    		return false;
    	}
    	$merchant_lat=getOption($mtid,'merchant_latitude');
    	$merchant_lng=getOption($mtid,'merchant_longtitude');
    	
    	if(!is_numeric($merchant_lat)){
	    	if ($lat_res=Yii::app()->functions->geodecodeAddress($merchant_address)){
		        $merchant_lat=$lat_res['lat'];
				$merchant_lng=$lat_res['long'];
	    	} 
    	}
    	
    	$client_lat=0;
    	$client_lng=0;
    	    	
    	if ($client_position=Yii::app()->functions->geodecodeAddress($client_address)){
	        $client_lat=$client_position['lat'];
			$client_lng=$client_position['long'];
    	} else return false;
    	
    	/*dump($client_lat);
    	dump($client_lng);*/
    	
    	/*get the distance from client address to merchant Address*/             
	    $distance_type=FunctionsV3::getMerchantDistanceType($mtid); 
	    $distance_type_orig=$distance_type;
	    
	    $distance=FunctionsV3::getDistanceBetweenPlot(
	        $client_lat,
	        $client_lng,
	        $merchant_lat,
	        $merchant_lng,
	        $distance_type
	    ); 
	    	   
	    if(!is_numeric($distance)){
		    if(!$distance){
		    	return false;
		    }	    
	    }   
	    /*if(!$distance){
	    	return false;
	    }*/
	    //dump($distance);
	   
	    $distance_type_raw = $distance_type=="M"?"miles":"kilometers";            		            
        $distance_type=$distance_type=="M"?AddonMobileApp::t("miles"):AddonMobileApp::t("kilometers");
        $distance_type_orig = $distance_type; 
        
        if(!empty(FunctionsV3::$distance_type_result)){
	       $distance_type_raw=FunctionsV3::$distance_type_result;
	       $distance_type=t(FunctionsV3::$distance_type_result);
	    }
	    
	    /*GET DELIVERY FEE*/
	    $delivery_fee=FunctionsV3::getMerchantDeliveryFee(
		                          $mtid,
		                          isset($merchant_info['delivery_fee_raw'])?$merchant_info['delivery_fee_raw']:0,
		                          $distance,
		                          $distance_type_raw);
		                          
		return array(
		  'distance_type'=>$distance_type,
		  'distance_type_raw'=>$distance_type_raw,
		  'distance'=>$distance,
		  'delivery_fee'=>$delivery_fee
		);                   
    }
    
	public static function verifyMobileCode($code='',$client_id='')
	{	
		if( $res=Yii::app()->functions->getClientInfo($client_id)){
			if ( $code==$res['mobile_verification_code']){
				return $res;
			} 	
		} 
		return false;
	}
	
	public static function hasModuleAddon($modulename='')
	{
		if (Yii::app()->hasModule($modulename)){
		   $path_to_upload=Yii::getPathOfAlias('webroot')."/protected/modules/$modulename";	
		   if(file_exists($path_to_upload)){
		   	   return true;
		   }
		}
		return false;
	}
	
	/*pts*/
	public static function updatePoints($order_id='',$client_id='',$status='active')
	{		
		$db=new DbExt();
		$params=array('status'=>$status);
		$db->updateData("{{points_earn}}",$params,'order_id',$order_id);
		
		/*update points_expenses*/
		$params=array('status'=>$status);
		$db->updateData("{{points_expenses}}",$params,'order_id',$order_id);
		
		/* update first order */
		$stmt="
		SELECT * FROM
		{{points_earn}}
		WHERE
		client_id =".self::q($client_id)."
		AND
		trans_type='first_order'
		AND
		status ='inactive'
		LIMIT 0,1
		";		
		if ($res=$db->rst($stmt)){
			$res=$res[0];
			$db->updateData('{{points_earn}}',array(
			  'status'=>"active"
			),'id',$res['id']);
		}
	}	
	
	public static function getIncomePoints($client_id='')
	{
		$feed_data='';
		$db=new DbExt();
		$stmt="
		SELECT * FROM
		{{points_earn}}
		WHERE
		status='active'
		AND
		client_id=".Yii::app()->functions->q($client_id)."
		ORDER BY id DESC
		LIMIT 0,1000
		";
		if ( $res=$db->rst($stmt)){
			foreach ($res as $val) {
				$label=PointsProgram::PointsDefinition('earn',$val['trans_type'],
				$val['order_id']);
				$feed_data[]=array(
				   Yii::app()->functions->displayDate($val['date_created']),
				   $label,
				   "<span>+".$val['total_points_earn']."</span>"
				);
			}
			return $feed_data;
		}
		return false;	
	}	
	
	public static function getExpensesPointsTotal($client_id='')
	{
		$db=new DbExt();
		$stmt="
		SELECT sum(total_points) as total
		FROM
		{{points_expenses}}
		WHERE
		status='active'
		AND
		client_id=".Yii::app()->functions->q($client_id)."
		";
		if ($res=$db->rst($stmt)){
			return $res[0]['total'];
		}
		return 0;
	}
		
	public static function qTranslate($text='',$key='',$data='')
	{		
		if (Yii::app()->functions->getOptionAdmin("enabled_multiple_translation")!=2){
			return stripslashes($text);
		}
		$key=$key."_trans";			
		//$id=$_GET['lang_id'];		
		$id=$_GET['lang'];		
		if ( $id>0){
			if (is_array($data) && count($data)>=1){
				if (isset($data[$key])){
					if (array_key_exists($id,(array)$data[$key])){
						if (!empty($data[$key][$id])){
						    return stripslashes($data[$key][$id]);
						}
					}
				}
			}
		}	
		return stripslashes($text);
	}
	
    public static function getAvatar($client_id='',$res='')
    {
    	//if ( $res = Yii::app()->functions->getClientInfo($client_id) ){
    	if($res){
    		$file=isset($res['avatar'])?$res['avatar']:'';
    	} else $file='avatar.jpg';
    	
    	if (empty($file)){
    		$file='avatar.jpg';
    	}
    	    	    
    	$path=Yii::getPathOfAlias('webroot')."/upload/$file";    	
    	if ( file_exists($path) ){       		 		    	
    		return Yii::app()->getBaseUrl(true)."/upload/$file";
    	} else return Yii::app()->getBaseUrl(true)."/assets/images/avatar.jpg";    	
    }
    
    public static function getCartByDeviceID($device_id='')
    {
    	$db=new DbExt();
		$stmt="
		SELECT * FROM
		{{mobile_cart}}
		WHERE
		device_id=".Yii::app()->functions->q($device_id)."
		LIMIT 0,1
		";
		if ($res=$db->rst($stmt)){
			return $res[0];
		}
		return false;		
    }
    
    public static function displayServicesList($service='')
    {
    	$service_offer='';
    	switch ($service) {
    		 
    		case 1:    		
    		   $service_offer=array(
    			  "delivery"=>self::t("Delivery"),
    			  "pickup"=>self::t("Pickup")
    			);	
    			break;
    			
    	    case 2:    			
    	       $service_offer=array(
    			  "delivery"=>self::t("Delivery")    			
    			);	
    			break;
    	    	
    	    case 3:    			
    	       $service_offer=array(    			
    			  "pickup"=>self::t("Pickup")
    			);	
    			break;    	    			
    			
    		case 4:    			
    		   $service_offer=array(
    			  "delivery"=>self::t("Delivery"),
    			  "pickup"=>self::t("Pickup"),
    			  "dinein"=>self::t("Dinein"),
    			);	
    			break;    	    			
    			
    		case 5:    	
    		   $service_offer=array(
    			  "delivery"=>self::t("Delivery"),    			  
    			  "dinein"=>self::t("Dinein"),
    			);			
    			break;    	    					
    			
    		case 6:    		
    		    $service_offer=array(
    			  "pickup"=>self::t("Pickup"), 
    			  "dinein"=>self::t("Dinein"),
    			);				
    			break;  	
    			
    		case 7:    			
    		   $service_offer=array(
    			  "dinein"=>self::t("Dinein"),
    			);				
    			break;	
    	
    		default:
    			break;
    	}
    	return $service_offer;
    }
    
    public static function latToAdress($lat='' , $lng='')
	{
		$lat_lng="$lat,$lng";
		$protocol = isset($_SERVER["https"]) ? 'https' : 'http';
		if ($protocol=="http"){
			$api="http://maps.googleapis.com/maps/api/geocode/json?latlng=".urlencode($lat_lng);
		} else $api="https://maps.googleapis.com/maps/api/geocode/json?latlng=".urlencode($lat_lng);
		
		/*check if has provide api key*/
		$key=Yii::app()->functions->getOptionAdmin('google_geo_api_key');		
		if ( !empty($key)){
			$api="https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($lat_lng)."&key=".urlencode($key);
		}	
		
		//$api.="&language=ar";
					
		if (!$json=@file_get_contents($api)){
			$json=Yii::app()->functions->Curl($api,'');
		}
		
		if (isset($_GET['debug'])){
			dump($api);		
			dump($json);    
		}
		
		$address_out='';
			
		if (!empty($json)){			
			$results = json_decode($json,true);				
			$parts = array(
		      'address'=>array('street_number','route'),
		      //'address'=>array('street_number'),
		      'city'=>array('locality','political','sublocality','administrative_area_level_2','administrative_area_level_1'),
		      'state'=>array('administrative_area_level_1'),
		      'zip'=>array('postal_code'),
		      'country'=>array('country'),
		    );		    
		    if (!empty($results['results'][0]['address_components'])) {
		      $ac = $results['results'][0]['address_components'];		      
		      foreach($parts as $need=>$types) {
		        foreach($ac as &$a) {		          
			          /*dump($need);
			          dump($types);
			          dump($a)	;*/
			          /*if (in_array($a['types'][0],$types)) $address_out[$need] = $a['long_name'];
			          elseif (empty($address_out[$need])) $address_out[$need] = '';*/
			          if (in_array($a['types'][0],$types)){
			          	  if (in_array($a['types'][0],$types)){
			          	  	  if($need=="address"){
			          	  	  	  if(isset($address_out[$need])) {
			          	  	  	     $address_out[$need] .= " ".$a['long_name'];
			          	  	  	  } else $address_out[$need]= $a['long_name'];
			          	  	  } else $address_out[$need] = $a['long_name'];			          	  	  
			          	  }
			          } elseif (empty($address_out[$need])) $address_out[$need] = '';	
		        }
		      }
		      
		      if(!empty($results['results'][0]['formatted_address'])){
		         $address_out['formatted_address']=$results['results'][0]['formatted_address'];
		      }
		      
		      return $address_out;
		    } 				
		}			
		return false;
	}

    
    public static function getDefaultAddressBook($client_id='')
    {
    	$db_ext=new DbExt;    	    	
    	$stmt="SELECT  
    	       concat(street,' ',city,' ',state,' ',zipcode) as address,
    	       a.id,
    	       a.location_name,
    	       a.country_code,
    	       a.as_default,
    	       a.street,
    	       a.city,
    	       a.state,
    	       a.zipcode,
    	       a.location_name,
    	       (
    	       select contact_phone from {{client}} where client_id=a.client_id limit 0,1
    	       ) as contact_phone
    	       FROM
    	       {{address_book}} a
    	       WHERE
    	       client_id =".self::q($client_id)."
    	       AND
    	       as_default='2'
    	       LIMIT 0,1    
    	";    	
    	if ($res=$db_ext->rst($stmt)){    		
    		return $res[0];
    	}
    	return false;
    } 	        	

    
    public static function orderHistory($order_id='')
    {
    	$db_ext=new DbExt;
    	$stmt="SELECT * FROM
    	{{order_history}}
    	WHERE
    	order_id=".q($order_id)."
    	ORDER BY id DESC
    	";
    	if ( $res=$db_ext->rst($stmt)){
    		return $res;
    	}
    	return false;
    }
    
    public static function getOrderTask($order_id='')
    {
    	$db_ext=new DbExt;
    	$stmt="SELECT a.*,
    	 concat(b.first_name,' ',b.last_name) as driver_name,
    	 b.email,
    	 b.phone,
    	 b.licence_plate,
    	 b.transport_description,
    	 b.transport_type_id,
    	 b.location_lat,
    	 b.location_lng,   	 
    	 b.profile_photo
    	 FROM
    	{{driver_task}} a
    	
    	left join {{driver}} b
		ON 
		a.driver_id = b.driver_id
    	
    	WHERE
    	order_id=".q($order_id)."
    	LIMIT 0,1
    	";
    	if ( $res=$db_ext->rst($stmt)){
    		return $res[0];
    	}
    	return false;
    }
    
    public static function getTaskDistance($lat1='',$lon1='', $lat2='',$lon2='',
    $transport_type='')
    {    	 
    	 $use_curl=getOptionA('google_use_curl');    	
    	 $key=Yii::app()->functions->getOptionAdmin('google_geo_api_key');
    	 
    	 $units_params='imperial';    	 
    	 
    	 $home_search_unit_type=getOptionA('home_search_unit_type');    	 
    	 if(!empty($home_search_unit_type)){
    	 	if($home_search_unit_type=="km"){
    	 	   $units_params='metric';
    	 	} 
    	 }
    	 
    	 switch ($transport_type) {
    	 	case "truck":
    	 	case "car":
    	 	case "scooter":
    	 		$method='driving';
    	 		break;
    	 
    	 	case "bicycle":    	 		
    	 		$method='bicycling';
    	 		break;
    	 			
    	    case "walk":    	 		
    	 		$method='walking';
    	 		break;
    	 				
    	 	default:
    	 		$method='driving';
    	 		break;
    	 }
    	     	 
    	 $url="https://maps.googleapis.com/maps/api/distancematrix/json";
	  	 $url.="?origins=".urlencode("$lat1,$lon1");
	  	 $url.="&destinations=".urlencode("$lat2,$lon2");
	  	 $url.="&mode=".urlencode($method);    	  
	  	 $url.="&units=".urlencode($units_params);
	  	 if(!empty($key)){
	  	 	$url.="&key=".urlencode($key);
	  	 }
	  	 
	  	 //$url.="&language=en";
	  	 
	  	 //dump($url);
	  	 
	  	 /*if ($use_curl==2){
	  	 	$data = Yii::app()->functions->Curl($url);
	  	 } else $data = @file_get_contents($url);*/
	  	 
	  	 if (!$data=@file_get_contents($url)){
			$data=Yii::app()->functions->Curl($url,'');
		}
		
	  	 $data = json_decode($data,true);  
	  	 	  	 
	  	 if(is_array($data) && count($data)>=1){
	  	 	//dump($data);
	  	 	if($data['rows'][0]['elements'][0]['status']=="OK"){	  		
	  	 		return $data['rows'][0]['elements'][0]['duration']['text'];
	  	 	} 
	  	 }
	  	 return false;
    }
     
       
	public static function addToTask($order_id='')
	{		
		$db=new DbExt;
		
		if($order_id<=0){
			return ;
		}
		$order_status=Yii::app()->functions->getOptionAdmin('drv_order_status');	
		if(empty($order_status)){
			$order_status='accepted';
		}
		
		$plus_hour=Yii::app()->functions->getOptionAdmin('drv_delivery_time');
		if(empty($plus_hour)){
			$plus_hour=0;
		}
						
		$stmt="
		SELECT a.*,
		concat(b.first_name,' ' ,b.last_name) as customer_name,
		b.email_address,
		concat( c.street,' ', c.city, ' ', c.state,' ',c.zipcode ,' ', c.country ) as delivery_address,
		c.contact_phone	as contact_number,
		c.formatted_address,
		c.google_lat,
		c.google_lng
		
		FROM
		{{order}} a
		
		left join {{client}} b
        ON
        b.client_id=a.client_id
        
        left join {{order_delivery_address}} c
        ON
        c.order_id=a.order_id
		
		WHERE
		a.order_id = '".$order_id."'
		AND
		a.status in ('$order_status','paid')
		AND
		a.trans_type in ('delivery')
		AND
		a.order_id NOT IN (
		  select order_id
		  from
		  {{driver_task}}
		  WHERE
		  order_id=a.order_id		  
		)
		
		LIMIT 0,1
		";		
		//dump($stmt);
		if ( $res=$db->rst($stmt)){
			foreach ($res as $val) {
				
				//dump($val);
				$lat=0;
				$long=0;			
				
				$delivery_date=!empty($val['delivery_date'])?$val['delivery_date']:date("Y-m-d");
				if(!empty($val['delivery_time'])){
					//$delivery_date.=" ".$val['delivery_time'];					
					$delivery_date=" ".date("Y-m-d G:i",strtotime($delivery_date." ".$val['delivery_time']." +$plus_hour hour" ));	
				} else {
					//$delivery_date.=" 23:00";					
					$delivery_date.= " ".date("G:i",strtotime("+$plus_hour hour"));
				}

				$driver_owner_task=getOptionA('driver_owner_task');
				if($driver_owner_task=="default"){
					$driver_owner_task='merchant';
				} 
				if(empty($driver_owner_task)){
				   $driver_owner_task='admin';	
				}
				
				$params=array(
				  'order_id'=>$val['order_id'],
				  //'user_type'=>'merchant',
				  'user_type'=>$driver_owner_task,
				  'user_id'=>$val['merchant_id'],
				  'trans_type'=>$val['trans_type'],				  
				  'email_address'=>isset($val['email_address'])?$val['email_address']:'',
				  'customer_name'=>isset($val['customer_name'])?$val['customer_name']:'',
				  'contact_number'=>isset($val['contact_number'])?$val['contact_number']:'',
				  'delivery_date'=>$delivery_date,
				  'delivery_address'=>isset($val['delivery_address'])?$val['delivery_address']:'' ,				  
				  'date_created'=>AddonMobileApp::dateNow(),
				  'ip_address'=>$_SERVER['REMOTE_ADDR']
				);
				
				if (!empty($val['google_lat']) && !empty($val['google_lng'])){
					$params['task_lat']=$val['google_lat'];
					$params['task_lng']=$val['google_lng'];
				} else {
					if ( $location=Driver::addressToLatLong($params['delivery_address'])){
						$params['task_lat']=$location['lat'];
						$params['task_lng']=$location['long'];
					}
				}
						
				if(!empty($val['formatted_address'])){
					$params['delivery_address']=addslashes($val['formatted_address']);
				}
				
				//dump($params);
				$db->insertData("{{driver_task}}",$params);
			}
		} //else echo 'no records';
	}	    
    
	public static function saveCartToDb()
	{
		$mobile_save_cart_db=getOptionA('mobile_save_cart_db');
		if($mobile_save_cart_db==1){
			return true;
		}
		return false;
	}
	
	public static function getCustomerCCList($customer_id='')
	{
		$db_ext=new DbExt;
    	$stmt="
    	SELECT * FROM
    	{{client_cc}}
    	WHERE
    	client_id=".self::q($customer_id)."
    	ORDER BY cc_id DESC
    	";
    	if ( $res=$db_ext->rst($stmt)){
    		unset($db_ext);
    		return $res;
    	}
    	unset($db_ext);
    	return false;
	}
	
	public static function getAppLanguage()
	{
		$translation='';
		$enabled_lang=FunctionsV3::getEnabledLanguage();		
		if(is_array($enabled_lang) && count($enabled_lang)>=1){			
			$path=Yii::getPathOfAlias('webroot')."/protected/messages";    	
    	    $res=scandir($path);
    	    if(is_array($res) && count($res)>=1){
    	    	foreach ($res as $val) {
    	    		if(in_array($val,$enabled_lang)){
    	    			$lang_path=$path."/$val/mobileapp.php";    
    	    			if (file_exists($lang_path)){
    	    				$temp_lang='';
		    				$temp_lang=require_once($lang_path);    				
		    				foreach ($temp_lang as $key=>$val_lang) {
		    					$translation[$key][$val]=$val_lang;
		    				}
    	    			}
    	    		}
    	    	}
    	    }    	     	    
		}
		return $translation;
	}
	
	
    public static function displayCashAvailable($merchant_id='', $services='')
    {
    	//dump($services);
    	$payment_list=FunctionsV3::PaymentOptionList();        
    	$payment_available='';
    	    	
        $is_commission=false;
		if ( Yii::app()->functions->isMerchantCommission($merchant_id)){			
			$is_commission=true;
			$payment_available=Yii::app()->functions->getMerchantListOfPaymentGateway();			
		} else {			
			$pay_available=Yii::app()->functions->getMerchantListOfPaymentGateway();			
			if ( getOption($merchant_id,'merchant_disabled_cod')==""){
				if (in_array('cod',(array)$pay_available)){
				   $payment_available[]='cod';
				}
			}				
		}
		
		$new_payment_list='';
		if (is_array($payment_list) && count($payment_list)>=1){
			foreach ($payment_list as $key=>$val) {
				if(in_array($key,(array)$payment_available)){
				   $new_payment_list[$key]=$val;
				}
			}
		}
		/*Check Admin individual settings for cod, offline cc, payon delivery*/
		if ( getOption($merchant_id,'merchant_switch_master_cod')==2){
			//cod
			if (array_key_exists('cod',(array)$new_payment_list)){
				unset($new_payment_list['cod']);
			}
		}
		
		/*check if has payment on delivery = pyr */
		if (array_key_exists('pyr',(array)$new_payment_list)){
			if ($is_commission){
				$provider_list=Yii::app()->functions->getPaymentProviderListActive();         	
			} else {
				$provider_list=Yii::app()->functions->getPaymentProviderMerchant($merchant_id);
			}			
			if (!is_array($provider_list) && count($provider_list)<=1){				
				unset($new_payment_list['pyr']);
			} 			
		}
		
		if (array_key_exists('ocr',(array)$new_payment_list)){
			$cc_offline_master=getOption($merchant_id,'merchant_switch_master_ccr');
			if ($cc_offline_master==2){
				unset($new_payment_list['ocr']);
			}
		}
				
		$payment_accepted='';
		if (array_key_exists('cod',(array)$new_payment_list)){					
			switch ($services) {
				case 1:
				case 2:	
				case 4:	
				case 5:	
				    $payment_accepted[]=AddonMobileApp::t("Cash on delivery available");			
					break;
				case 3:
					$payment_accepted[]=AddonMobileApp::t("Cash on pickup available");
					break;
				case 6:
					$payment_accepted[]=AddonMobileApp::t("Cash on pickup available");
					break;
				case 7:
					$payment_accepted[]=AddonMobileApp::t("Pay in person available");
					break;	
				default:
					break;
			}
		}
		if (array_key_exists('ocr',(array)$new_payment_list)){			
			$payment_accepted[]=AddonMobileApp::t("Credit Card available");
		}
		
		if(is_array($payment_accepted) && count($payment_accepted)>=1){
			return $payment_accepted;
		}
					
		return false;
    }	
    
    public static function dateNow()
	{
		return date('Y-m-d G:i:s');
	}    
	
	public static function notifyCustomer($order_id='')
	{
		$_GET['backend']=true; $print='';
		if ( $data=Yii::app()->functions->getOrder2($order_id)){
			$merchant_id=$data['merchant_id'];
	        $json_details=!empty($data['json_details'])?json_decode($data['json_details'],true):false;				
	        if ( $json_details !=false){
	        	Yii::app()->functions->displayOrderHTML(array(
	        	  'order_id'=>$order_id,
				  'merchant_id'=>$data['merchant_id'],
				  'delivery_type'=>$data['trans_type'],
				  'delivery_charge'=>$data['delivery_charge'],
				  'packaging'=>$data['packaging'],
				  'cart_tip_value'=>$data['cart_tip_value'],
				  'cart_tip_percentage'=>$data['cart_tip_percentage']/100,
				  'card_fee'=>$data['card_fee'],
				  'tax'=>$data['tax'],
				  'points_discount'=>isset($data['points_discount'])?$data['points_discount']:'' /*POINTS PROGRAM*/,
				  'voucher_amount'=>$data['voucher_amount'],
				  'voucher_type'=>$data['voucher_type']
				  ),$json_details,true);
	        }
	        
	        $print[]=array( 'label'=> t("Customer Name"), 'value'=>$data['full_name'] );
	        $print[]=array( 'label'=> t("Merchant Name"), 'value'=>$data['merchant_name']);
	        if (isset($data['abn']) && !empty($data['abn'])){
	        	$print[]=array(
		         'label'=>Yii::t("default","ABN"),
		         'value'=>$data['abn']
		        );
	        }
	        $print[]=array('label'=>Yii::t("default","Telephone"),'value'=>$data['merchant_contact_phone']);
	        
	        $merchant_info=Yii::app()->functions->getMerchant(isset($merchant_id)?$merchant_id:'');
			$full_merchant_address=$merchant_info['street']." ".$merchant_info['city']. " ".$merchant_info['state'].
			" ".$merchant_info['post_code'];

	        $print[]=array('label'=>Yii::t("default","Address"),'value'=>$full_merchant_address);
	        
	        $print[]=array('label'=>Yii::t("default","TRN Type"),'value'=>t($data['trans_type']));
	        
	        $print[]=array(
	         'label'=>Yii::t("default","Payment Type"),
	         'value'=>FunctionsV3::prettyPaymentType('payment_order',$data['payment_type'],$order_id,$data['trans_type'])
	        );	       
	       
	        if ( $data['payment_provider_name']){
	        	$print[]=array('label'=>Yii::t("default","Card#"),'value'=>strtoupper($data['payment_provider_name']));
	        }
	        
	        if ( $data['payment_type'] =="pyp"){
	        	$paypal_info=Yii::app()->functions->getPaypalOrderPayment($order_id);
	        	$print[]=array(
                   'label'=>Yii::t("default","Paypal Transaction ID"),
	               'value'=>isset($paypal_info['TRANSACTIONID'])?$paypal_info['TRANSACTIONID']:''
	            );
	        }
	        	        
	        $print[]=array(
	         'label'=>Yii::t("default","Reference #"),
	         'value'=>Yii::app()->functions->formatOrderNumber($data['order_id'])
	        );
	        
	        if ( !empty($data['payment_reference'])){
	        	$print[]=array(
		          'label'=>Yii::t("default","Payment Ref"),
		          'value'=>isset($data['payment_reference'])?$data['payment_reference']:Yii::app()->functions->formatOrderNumber($data['order_id'])
		        );
	        }
	        
	        if ( $data['payment_type']=="ccr" || $data['payment_type']=="ocr"){
	        	$print[]=array(
		          'label'=>Yii::t("default","Card #"),
		          'value'=>Yii::app()->functions->maskCardnumber($data['credit_card_number'])
		        );
	        }
	        
	        $trn_date=date('M d,Y G:i:s',strtotime($data['date_created']));
	        $print[]=array(
	          'label'=>Yii::t("default","TRN Date"),
	          'value'=>$trn_date
	        );
	        	        
	        /*dump($data);
	        dump($print);
	        die();*/
	        
	        switch ($data['trans_type']) {
	        	case "delivery":	        		
	        		$print[]=array(
			         'label'=>Yii::t("default","Delivery Date"),
			         'value'=>Yii::app()->functions->translateDate($data['delivery_date'])
			        );
			        
			        if(!empty($data['delivery_time'])){
			           $print[]=array(
				         'label'=>Yii::t("default","Delivery Time"),
				         'value'=>Yii::app()->functions->timeFormat($data['delivery_time'],true)
				       );
			        }
			        
			        if(!empty($data['delivery_asap'])){
			        	$delivery_asap=$data['delivery_asap']==1?t("Yes"):'';
			        	$print[]=array(
						 'label'=>Yii::t("default","Deliver ASAP"),
						 'value'=>$delivery_asap
						);
			        }
			        
			        if (!empty($data['client_full_address'])){		         	
		         	   $delivery_address=$data['client_full_address'];
		            } else $delivery_address=$data['full_address'];		
		            		            
			        $print[]=array(
					  'label'=>Yii::t("default","Deliver to"),
					  'value'=>$delivery_address
					);
					
					$print[]=array(
					  'label'=>Yii::t("default","Delivery Instruction"),
					  'value'=>$data['delivery_instruction']
					);         
					
					$print[]=array(
					  'label'=>Yii::t("default","Location Name"),
					  'value'=>$data['location_name']
					);
		       
					$print[]=array(
					  'label'=>Yii::t("default","Contact Number"),
					  'value'=>$data['contact_phone']
					);
					
					if ($data['order_change']>=0.1){
						$print[]=array(
						  'label'=>Yii::t("default","Change"),
						  'value'=>normalPrettyPrice($data['order_change'])
						);
					}
				
	        		break;
	        	
	        	case "pickup":		
	        	case "dinein":		
	        	
		            $label_date=t("Pickup Date");
			        $label_time=t("Pickup Time");
			        if ($data['trans_type']=="dinein"){
			      	    $label_date=t("Dine in Date");
			            $label_time=t("Dine in Time");
			        }   
			        
			        if (isset($data['contact_phone1'])){
						if (!empty($data['contact_phone1'])){
							$data['contact_phone']=$data['contact_phone1'];
						}
					}
				
			        $print[]=array(
					  'label'=>Yii::t("default","Contact Number"),
					  'value'=>$data['contact_phone']
					);
					
					$print[]=array(
			         'label'=>$label_date,
			         'value'=>Yii::app()->functions->translateDate($data['delivery_date'])
			        );
			        
			        if(!empty($data['delivery_time'])){
			           $print[]=array(
				         'label'=>$label_time,
				         'value'=>Yii::app()->functions->timeFormat($data['delivery_time'],true)
				       );
			        }
			        
			        if ($data['order_change']>=0.1){
						$print[]=array(
						  'label'=>Yii::t("default","Change"),
						  'value'=>normalPrettyPrice($data['order_change'])
						);
					}
			        
					if ($data['trans_type']=="dinein"){
						$print[]=array(
						  'label'=>t("Number of guest"),
						  'value'=>$data['dinein_number_of_guest']
						);
						$print[]=array(
						  'label'=>t("Special instructions"),
						  'value'=>$data['dinein_special_instruction']
						);
					}
	        	
	        	   break;
	        
	        	default:
	        		break;
	        }
	        
	        $to=isset($data['email_address'])?$data['email_address']:'';
	        $receipt=EmailTPL::salesReceipt($print,Yii::app()->functions->details['raw']);	      
	        
	          
	        FunctionsV3::notifyCustomer($data,Yii::app()->functions->additional_details,$receipt, $to);
	        FunctionsV3::notifyMerchant($data,Yii::app()->functions->additional_details,$receipt);
	        FunctionsV3::notifyAdmin($data,Yii::app()->functions->additional_details,$receipt);
	        	        
	        FunctionsV3::fastRequest(FunctionsV3::getHostURL().Yii::app()->createUrl("cron/processemail"));
	        FunctionsV3::fastRequest(FunctionsV3::getHostURL().Yii::app()->createUrl("cron/processsms"));	  
	        	       
	        /*PRINTER ADDON*/
	        if (FunctionsV3::hasModuleAddon("printer")){
	        	Yii::app()->setImport(array('application.modules.printer.components.*'));
	        	$html=getOptionA('printer_receipt_tpl');
				if($print_receipt = ReceiptClass::formatReceipt($html,$print,Yii::app()->functions->details['raw'],$data)){							
					PrinterClass::printReceipt($data['order_id'],$print_receipt);												
				}
				
				$html = getOption($merchant_id,'mt_printer_receipt_tpl');
				if($print_receipt = ReceiptClass::formatReceipt($html,$print,Yii::app()->functions->details['raw'],$data)){
			       PrinterClass::printReceiptMerchant($merchant_id,$data['order_id'],$print_receipt);		
				}		
				FunctionsV3::fastRequest(FunctionsV3::getHostURL().Yii::app()->createUrl("printer/cron/processprint"));	
	        }
	              
		}
	}
	
	public static function registeredDevice($client_id='',$device_id='',$device_platform='')
	{
		$DbExt=new DbExt; 
		if(!empty($client_id)){
			$params=array(
			  'client_id'=>$client_id,
			  'device_id'=>$device_id,
			  'device_platform'=>$device_platform,
			  'date_created'=>AddonMobileApp::dateNow(),
			  'ip_address'=>$_SERVER['REMOTE_ADDR'],
			  'status'=>'active'
			);			
			$DbExt->qry("DELETE 
			  FROM {{mobile_registered}} 
			  WHERE client_id=".self::q($client_id)." 
			  AND status='inactive'		
			");		
			
			$stmt="SELECT * FROM
			{{mobile_registered}} 
			WHERE
			client_id=".self::q($client_id)."
			AND status='active'
			LIMIT 0,1
			";	
			if($res=$DbExt->rst($stmt)){
				$res=$res[0];				
				unset($params['date_created']);
				$params['date_modified']=AddonMobileApp::dateNow();
				$DbExt->updateData("{{mobile_registered}}",$params,'client_id',$client_id);
			} else {
				$DbExt->insertData("{{mobile_registered}}",$params);
			}
		}
	}
	
	public static function getRegisteredDeviceByClientID($client_id='')
	{
		$DbExt=new DbExt; 
		$stmt="
		SELECT * FROM
		{{mobile_registered_view}}
		WHERE
		client_id=".self::q($client_id)."
		AND
		status = 'active'
		LIMIT 0,1
		";
		if($res=$DbExt->rst($stmt)){
			return $res[0];
		}
		return false;
	}
	
	public static function SendForgotPassword($to='',$res='')
	{
		$enabled=getOptionA('customer_forgot_password_email');
		if($enabled){
			$lang=Yii::app()->language; 
			$subject=getOptionA("customer_forgot_password_tpl_subject_$lang");
			if(!empty($subject)){
				$subject=FunctionsV3::smarty('firstname',
				isset($res['first_name'])?$res['first_name']:'',$subject);
				
				$subject=FunctionsV3::smarty('lastname',
				isset($res['last_name'])?$res['last_name']:'',$subject);
			}
										
			$tpl=getOptionA("customer_forgot_password_tpl_content_$lang") ;
			if (!empty($tpl)){								
				$tpl=FunctionsV3::smarty('firstname',
				isset($res['first_name'])?$res['first_name']:'',$tpl);
				
				$tpl=FunctionsV3::smarty('lastname',
				isset($res['last_name'])?$res['last_name']:'',$tpl);
				
				$tpl=FunctionsV3::smarty('change_pass_link',
				FunctionsV3::getHostURL().Yii::app()->createUrl('store/forgotpassword',array(
				  'token'=>$res['token']
				))
				,$tpl);
				
				$tpl=FunctionsV3::smarty('sitename',getOptionA('website_title'),$tpl);
				$tpl=FunctionsV3::smarty('siteurl',websiteUrl(),$tpl);
			}
			if (!empty($subject) && !empty($tpl)){
				sendEmail($to,'',$subject, $tpl );
			}						
		}					
	}
	
   public static function sendBankInstructionPurchase($mtid='', $order_id='', $total_amount='',$client_id='')
   {
   	   $enabled=''; $subject=''; $tpl=''; $client_info='';
   	   $verify_link = FunctionsV3::getHostURL().Yii::app()->createUrl('store/depositverify',array(
   	     'ref'=>$order_id
   	   ));
   	      	   
   	   if(!$client_info=Yii::app()->functions->getClientInfo($client_id)){
   	   	  return false;
   	   }
   	      	   
   	   $data['full_name']=$client_info['first_name']." ".$client_info['last_name'];
   	   $data['amount']=self::prettyPrice($total_amount);
   	   $data['verify_payment_link']=$verify_link;
   	   $data['order_id']=$order_id;
   	   
   	   $to=$client_info['email_address'];
   	   
   	   $lang=Yii::app()->language;      	   
   	   if (FunctionsV3::isMerchantPaymentToUseAdmin($mtid)){
   	   	   $enabled=getOptionA("offline_bank_deposit_purchase_email");
   	   	   if ($enabled){
   	   	   	   $subject=getOptionA("offline_bank_deposit_purchase_tpl_subject_$lang");
   	   	   	   $tpl=getOptionA("offline_bank_deposit_purchase_tpl_content_$lang");
   	   	   }
   	   } else {   	   	   
   	   	  $enabled=getOption($mtid,'merchant_bankdeposit_enabled');
   	   	  if($enabled=="yes"){
   	   	  	 $enabled=1;
   	   	  	 $subject=getOption($mtid,"merchant_deposit_subject");
   	   	  	 $tpl=getOption($mtid,"merchant_deposit_instructions");
   	   	  }
   	   }   	   
   	   $pattern=array(
		   'customer_name'=>'full_name',		   
		   'amount'=>'amount',
		   'order_id'=>'order_id',
		   'verify_payment_link'=>"verify_payment_link",
		   'verify-payment-link'=>"verify_payment_link",
		   'sitename'=>getOptionA('website_title'),
		   'siteurl'=>websiteUrl(),	    		   
		);
		$tpl=FunctionsV3::replaceTemplateTags($tpl,$pattern,$data); 
		$subject=FunctionsV3::replaceTemplateTags($subject,$pattern,$data);
   	   
   	   if($enabled==1){   	      
   	      $sender=getOptionA("global_admin_sender_email");
   	      $DbExt=new DbExt();
   	      $params=array(
		    'email_address'=>$to,
		    'sender'=>$sender,
		    'subject'=>$subject,
		    'content'=>$tpl,
		    'date_created'=>FunctionsV3::dateNow(),
		    'ip_address'=>$_SERVER['REMOTE_ADDR'],
		    'module_type'=>'core'
		  );	  		  
		  $DbExt->insertData("{{email_logs}}",$params);    		    
		  FunctionsV3::runCronEmail();
		  unset($DbExt);
   	   }
   }	
   
   public static function getStateByCityID($city_id='')
   {  
   	  $DbExt=new DbExt();
   	  $stmt="
   	  SELECT a.state_id,
   	  b.name as state_name
   	  FROM
   	  {{location_cities}} a   	  
   	  left join {{location_states}} b
	  ON
	  a.state_id = b.state_id
	  WHERE
	  a.city_id = ".self::q($city_id)."
   	  ";   	  
   	  if ($res=$DbExt->rst($stmt)){
   	  	  $res=$res[0];
   	  	  return $res;
   	  } 
   	  return false;
   }
   
   public static function getPaymentMethodNew($mtid='')
   {
   	  $list='';
   	  $payment_list  = FunctionsV3::getMerchantPaymentListNew($mtid);				
   	  if(is_array($payment_list) && count($payment_list)>=1){
   	  	 foreach ($payment_list as $key=>$val) {
   	  	 	$list[]=array(
   	  	 	  'icon'=>"ion-card",
   	  	 	  'value'=>$key,
   	  	 	  'label'=>$val
   	  	 	);
   	  	 }
   	  	 return $list;
   	  }
   	  return false;
   }
   
   public static function getCityList($s='')
   {
   	    $country_id=getOptionA('location_default_country'); $state_ids='';
   	    if(empty($country_id)){
   	    	if($res=self::getCountryByCode()){   	    	
   	    		$country_id=$res['country_id'];
   	    	}
   	    }
   	    if(!empty($country_id)){
   	    	if ($res=FunctionsV3::locationStateList($country_id)){
   	    		foreach ($res as $val) {
   	    			$state_ids.="'$val[state_id]',";
   	    		}
   	    		$state_ids=substr($state_ids,0,-1);
   	    	}
   	    }
   	    
   	    $and="";
   	    if(!empty($state_ids)){
   	    	$and.=" AND state_id IN ($state_ids) ";
   	    }
   	    if(!empty($s)){
   	    	$and.=" AND name LIKE '%".$s."%' ";
   	    }
   	    
   	    $DbExt=new DbExt; $data='';
   	    $stmt="SELECT * FROM
   	    {{location_cities}}
   	    WHERE 1
   	    $and
   	    ORDER BY name ASC
   	    LIMIT 0,100
   	    ";
   	    if(isset($_GET['debug'])){
   	       dump($stmt);
   	    }
   	    if($res=$DbExt->rst($stmt)){
   	    	foreach ($res as $val) {
   	    		$data[]=array(
   	    		  'id'=>$val['city_id'],
   	    		  'name'=>$val['name']
   	    		);
   	    	}
   	    	return $data;
   	    }
   	    return false;
   }   
   
   public static function enabledExtraCharges()
   {
   	  return false;
   }
   
   public static function saveGoogleAvatar($link='',$user_id='')
	{		
		$avatar_filename="avatar_".$user_id.".jpg";		
		if(!empty($link)){			
			$image = file_get_contents($link);
			@file_put_contents( FunctionsV3::uploadPath()."/$avatar_filename",$image);
			return $avatar_filename;
		}		
		return false;
	}
	
	public static function getCategoryCount($merchant_id='')
	{
		$DbExt=new DbExt;
		$stmt="
		SELECT count(*) as total
	    FROM
		{{category}}
		WHERE 
		merchant_id=".self::q($merchant_id)."
		AND status in ('publish','published')		
		ORDER BY sequence ASC
		";			
		if($res=$DbExt->rst($stmt)){
			$res=$res[0];			
			if($res['total']>=1){
			   return $res['total'];
			}
		}
		return false;
	}
	
	public static function getCategoryByID($cat_id='')
	{
	    $DbExt=new DbExt;
		$stmt="
		SELECT cat_id,category_name, category_description
	    FROM
		{{category}}
		WHERE 
		cat_id=".self::q($cat_id)."
		LIMIT 0,1
		";			
		if($res=$DbExt->rst($stmt)){
			$res=$res[0];			
			return $res;
		}
		return false;
	}
	
	public static function getPagesByID($page_id='',$fields='*')
	{
		
	    $DbExt=new DbExt;
		$stmt="
		SELECT $fields
	    FROM
		{{mobile_pages}}
		WHERE 
		page_id=".self::q($page_id)."
		LIMIT 0,1
		";			
		//dump($stmt);
		if($res=$DbExt->rst($stmt)){
			$res=$res[0];			
			return $res;
		}
		return false;
	}	
	
	public static function getActivePages()
	{
		$enabled_lang = Yii::app()->functions->multipleField();
		$fields_required = 'page_id,title,icon';
		if ( $enabled_lang ==2){
			if ( $fields=FunctionsV3::getLanguageList(false)){
				foreach ($fields as $f_val){
					$fields_required.=",lang_title_$f_val";
				}			
			}			
		}				
		
		
	    $DbExt=new DbExt;
		$stmt="
		SELECT $fields_required
	    FROM
		{{mobile_pages}}
		WHERE 
		status in ('publish','published')
		ORDER BY sequence ASC
		LIMIT 0,4
		";			
		if($res=$DbExt->rst($stmt)){			
			return $res;
		}
		return false;
	}		
	
	public static function getMercapago($merchant_id='')
	{
		if(FunctionsV3::isMerchantPaymentToUseAdmin($merchant_id)){
			$mercado_enabled=getOptionA('admin_mercado_enabled');
   	   	    $mercado_id=getOptionA('admin_mercado_id'); 
   	   	    $mercado_key=getOptionA('admin_mercado_key');   	   	    
		} else {
			$mercado_enabled=getOption($mtid,'merchant_mercado_enabled');
   	   	    $mercado_id=getOption($mtid,'merchant_mercado_id');
   	   	    $mercado_key=getOption($mtid,'merchant_mercado_key');
		}
		if($mercado_enabled=="yes" && !empty($mercado_id) && !empty($mercado_key)){
			return array(
			  'mercado_id'=>$mercado_id,
			  'mercado_key'=>$mercado_key
			);
		}
		return false;
	}
	
	public static function clearCart($device_id='')
	{
		if(!empty($device_id)){
			$DbExt=new DbExt;		
			$DbExt->qry("
			DELETE FROM {{mobile_cart}}
			WHERE
			device_id=".AddonMobileApp::q($device_id)."
			");		
			return true;
		} 
		return false;
	}
	
	public static function getAvatar2($client_id='')
    {
    	if ( $res= Yii::app()->functions->getClientInfo($client_id) ){
    		$file=$res['avatar'];
    	} else $file='avatar.jpg';
    	
    	if (empty($file)){
    		$file='avatar.jpg';
    	}    	    	    
    	$path=Yii::getPathOfAlias('webroot')."/upload/$file";    	
    	if ( file_exists($path) ){       		 		    	
    		return Yii::app()->getBaseUrl(true)."/upload/$file";
    	} else return Yii::app()->getBaseUrl(true)."/assets/images/avatar.jpg";   
    }
    
    public static function getGallerPicURL($filename='')
    {
    	$path=Yii::getPathOfAlias('webroot')."/upload/$filename";  
    	if(file_exists($path)){
    		return Yii::app()->getBaseUrl(true)."/upload/$filename";
    	} 
    	return false;
    }
    
    public static function getDishPics($list='')
    {
    	$dish_list = array();
    	$dish = json_decode($list,true);
		if(is_array($dish) && count($dish)>=1){
			foreach ($dish as $dish_id) {
				if($dish_info = Yii::app()->functions->GetDish($dish_id)){								
					if ($icon_link = AddonMobileApp::getGallerPicURL($dish_info['photo'])){
						$dish_list[]=$icon_link;
					}							
				}						
			}
			
		} else $dish_list='';
		return $dish_list;
    }
 	
}/* end class*/