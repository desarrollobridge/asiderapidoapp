<?php
if (!isset($_SESSION)) { session_start(); }

class AjaxController extends CController
{
	public $layout='_store';	
	public $code=2;
	public $msg;
	public $details;
	public $data;
	
	public function __construct()
	{
		$this->data=$_POST;	
		if (isset($_GET['post_type'])){
			if ($_GET['post_type']=="get"){
				$this->data=$_GET;	
			}
		}
				
		/*ADD SECURITY VALIDATION TO ALL REQUEST*/		
		$validate_request_session = Yii::app()->params->validate_request_session;
        $validate_request_csrf = Yii::app()->params->validate_request_csrf;	
        
		if($validate_request_session){
			$session_id=session_id();		
			if($this->data['yii_session_token']!=$session_id){			
				$this->msg = Yii::t("default","Session token not valid");
				$this->jsonResponse();
				Yii::app()->end();
			}		
		}
				
		if($validate_request_csrf){
			if ( $this->data[Yii::app()->request->csrfTokenName] != Yii::app()->getRequest()->getCsrfToken()){
				$this->msg = Yii::t("default","Request token not valid");
				$this->jsonResponse();
				Yii::app()->end();
			}
		}
		
		/*ADD SECURITY VALIDATION TO ALL REQUEST*/	
				
	}
	
	public function init()
	{
		// set website timezone
		$website_timezone=Yii::app()->functions->getOptionAdmin("website_timezone");		 
		if (!empty($website_timezone)){		 	
		 	Yii::app()->timeZone=$website_timezone;
		}		 				 
		FunctionsV3::handleLanguage();
		//echo Yii::app()->language;
	}
	
	private function jsonResponse()
	{
		$resp=array('code'=>$this->code,'msg'=>$this->msg,'details'=>$this->details);
		echo CJSON::encode($resp);
		Yii::app()->end();
	}
	
	private function otableNodata()
	{
		if (isset($_GET['sEcho'])){
			$feed_data['sEcho']=$_GET['sEcho'];
		} else $feed_data['sEcho']=1;	   
		     
        $feed_data['iTotalRecords']=0;
        $feed_data['iTotalDisplayRecords']=0;
        $feed_data['aaData']=array();		
        echo json_encode($feed_data);
    	die();
	}

	private function otableOutput($feed_data='')
	{
	  echo json_encode($feed_data);
	  die();
    }    
    
    public function actionLoadAllRestoMap()
    {
    	$data='';
    	$stmt=$_SESSION['kmrs_search_stmt'];
    	if (!empty($stmt)){
    		$pos=strpos($stmt,'LIMIT');    		
    		$stmt=substr($stmt,0,$pos-1);       		
    		$DbExt=new DbExt();
    		$DbExt->qry("SET SQL_BIG_SELECTS=1");
    		if ( $res=$DbExt->rst($stmt)){
    			foreach ($res as $val) {    
    				if (!empty($val['latitude']) && !empty($val['lontitude'])){
	    				$data[]=array(
	    				  'restaurant_name'=>stripslashes($val['restaurant_name']),
	    				  'restaurant_slug'=>$val['restaurant_slug'],
	    				  'merchant_address'=>$val['merchant_address'],
	    				  'latitude'=>$val['latitude'],
	    				  'lontitude'=>$val['lontitude'],
	    				  'logo'=>FunctionsV3::getMerchantLogo($val['merchant_id']),
	    				  'link'=>Yii::app()->createUrl('/menu-'.$val['restaurant_slug'])
	    				);
    				}
    			}    			
    			$this->code=1;
    			$this->msg="OK";
    			$this->details=$data;
    		} else $this->msg=t("no records");
    	} else $this->msg=t("invalid statement query");
    	$this->jsonResponse();
    }
    
    public function actionloadAllMerchantMap()
    {    	
    	$datas='';
    	if ( $data=Yii::app()->functions->getAllMerchant(true)){
    		foreach ($data['list'] as $val) {
    			if (!empty($val['latitude']) && !empty($val['lontitude'])){
    				$datas[]=array(
    				  'restaurant_name'=>stripslashes($val['restaurant_name']),
    				  'restaurant_slug'=>$val['restaurant_slug'],
    				  'merchant_address'=>stripslashes($val['merchant_address']),
    				  'latitude'=>$val['latitude'],
    				  'lontitude'=>$val['lontitude'],
    				  'logo'=>FunctionsV3::getMerchantLogo($val['merchant_id']),
    				  'link'=>Yii::app()->createUrl('store/menu-'.$val['restaurant_slug'])
    				);
				}
    		}
    		$this->code=1;
			$this->msg="OK";
			$this->details=$datas;
    	} else $this->msg=t("no records");
    	$this->jsonResponse();
    }
    
    public function actionClientCCList()
    {
    	$DbExt=new DbExt;
    	$stmt="SELECT * FROM
		{{client_cc}}		
		WHERE
		client_id ='".Yii::app()->functions->getClientId()."'	
		ORDER BY cc_id DESC
		";						
		if ($res=$DbExt->rst($stmt)){		
		   foreach ($res as $val) {	
		   	    $edit_url=Yii::app()->createUrl('/store/profile/?tab=4&do=add&id='.$val['cc_id']);
				$action="<div class=\"options\">
	    		<a href=\"$edit_url\" ><i class=\"ion-ios-compose-outline\"></i></a>
	    		<a href=\"javascript:;\" data-table=\"client_cc\" data-whereid=\"cc_id\" class=\"row_remove\" data-id=\"$val[cc_id]\" ><i class=\"ion-ios-trash\"></i></a>
	    		</div>";		   	   
		   	   $feed_data['aaData'][]=array(
		   	      $val['card_name'].$action,
		   	      Yii::app()->functions->maskCardnumber($val['credit_card_number']),
		   	      $val['expiration_month']."-".$val['expiration_yr']
		   	   );			       
		   }
		   $this->otableOutput($feed_data);
		}
		$this->otableNodata();			
    }
    
    public function actionUpdateClientCC()
    {
    	
    	$p = new CHtmlPurifier();
    	
    	if (Yii::app()->functions->isClientLogin()){
    	$client_id=Yii::app()->functions->getClientId();    	    	
	    	$params=array(
	    	  'client_id'=>$client_id,
	    	  'card_name'=> $p->purify($this->data['card_name']) ,
	    	  'credit_card_number'=> $p->purify($this->data['credit_card_number']),
	    	  'expiration_month'=> $p->purify($this->data['expiration_month']),
	    	  'expiration_yr'=> $p->purify($this->data['expiration_yr']),
	    	  'billing_address'=> $p->purify($this->data['billing_address']),
	    	  'cvv'=> $p->purify($this->data['cvv']),
	    	  'date_created'=>FunctionsV3::dateNow(),
	    	  'ip_address'=>$_SERVER['REMOTE_ADDR']
	    	);	    	
	    	$DbExt=new DbExt;
	    	if (isset($this->data['cc_id'])){
	    		unset($params['date_created']);
	    		$params['date_modified']=FunctionsV3::dateNow();	    		
	    		
	    		$stmt="SELECT * FROM
	    		{{client_cc}}
	    		WHERE
	    		client_id=".FunctionsV3::q($client_id)."
	    		AND
	    		cc_id<>".FunctionsV3::q($this->data['cc_id'])."
	    		AND credit_card_number=".FunctionsV3::q($this->data['credit_card_number'])."
	    		
	    		LIMIT 0,1
	    		";	    		
	    		if ($DbExt->rst($stmt)){
	    			$this->msg=t("Credit card number already exist in you credit card list");
	    			$this->jsonResponse();
	    			return ;
	    		}
	    			    		
	    		if ( $DbExt->updateData("{{client_cc}}",$params,'cc_id',$this->data['cc_id'])){
	    			$this->code=1;
	    			$this->msg=t("Card successfully updated.");
	    		} else $this->msg=t("Error cannot saved information");
	    	} else {
	    		if (!Yii::app()->functions->getCCbyCard($this->data['credit_card_number'],$client_id) ){
		    		if ( $DbExt->insertData("{{client_cc}}",$params)){
		    			$cc_id=Yii::app()->db->getLastInsertID();	    			
		    			$redirect=Yii::app()->createUrl('/store/profile/?tab=4&do=add&id='.$cc_id);
		    			
		    			$this->code=1;
		    			$this->msg=t("Card successfully added");
		    			$this->details=array('redirect'=>$redirect);
		    		} else $this->msg=t("Error cannot saved information");
	    		} else $this->msg=t("Credit card number already exist in you credit card list");
	    	}
    	} else $this->msg=t("ERROR: Your session has expired.");
    	$this->jsonResponse();
    }
    
    public function actionsaveAvatar()
    {    	
    	$DbExt=new DbExt;
    	if (!empty($this->data['filename'])){
    		$params=array(
    		  'avatar'=>$this->data['filename'],
    		  'date_modified'=>date(''),
    		  'ip_address'=>$_SERVER['REMOTE_ADDR']
    		);
    		$client_id=Yii::app()->functions->getClientId();    		
    		if (is_numeric($client_id)){
    			
    			$filename_delete='';
    			if ( $old_data=Yii::app()->functions->getClientInfo($client_id)){
    				if ( $old_data['avatar']!=$params['avatar']){
    					$filename_delete=$old_data['avatar'];
    				}
    			}
    			
    			$DbExt->updateData("{{client}}",$params,'client_id',$client_id);
    			$this->msg=t("You have succesfully change your profile picture");
    			$this->code=1;
    			
    			if($filename_delete){
    				FunctionsV3::deleteUploadedFile($filename_delete);
    			}
    			
    		} else $this->msg=t("ERROR: Your session has expired.");
    	} else $this->msg=t("Filename is empty");
    	$this->jsonResponse();
    }
    
    public function actionViewReceipt()
    {
    	/** Register all scripts here*/
    	ScriptManager::registerAllCSSFiles();
		$this->render('/store/receipt-front',array(
		  'data'=>Yii::app()->functions->getOrder2( isset($this->data['order_id'])?$this->data['order_id']:'' )
		));
    }
    
    public function actionResendEmailCode()
    {
    	$client_id=isset($this->data['client_id'])?$this->data['client_id']:'';
    	if( $res=Yii::app()->functions->getClientInfo( $client_id )){	
    		FunctionsV3::sendEmailVerificationCode($res['email_address'],$res['email_verification_code'],$res);
    		$this->code=1;
    		$this->msg=t("We have sent verification code to your email address");
    	} else $this->msg=t("Sorry but we cannot find your information.");
    	$this->jsonResponse();
    }
    
    public function actionCityList()
    {    	
    	$data=FunctionsV3::getCityList();    	
    	header('Content-Type: application/json');
    	echo json_encode($data);
    	Yii::app()->end();
    }
    
    public function actionAreaList()
    {    
    	$DbExt=new DbExt; 
    	$and='';
    	$data=''; $this->data=$_GET;    	
    	if (!empty($this->data['q'])){
    		$q=stripslashes($this->data['q']);
    		$and.=" AND name LIKE '$q%' ";
    	}
    	if (!empty($this->data['city_id'])){
    		$and.=" AND city_id=".FunctionsV3::q($this->data['city_id'])." ";
    	}
    	$stmt="
    	SELECT * FROM
    	{{location_area}}
    	WHERE 1
    	$and
    	ORDER BY name ASC
    	";
    	//dump($stmt);
    	if ($res=$DbExt->rst($stmt)){
    		foreach ($res as $val) {
    			$data[]=array(
    			 'id'=>$val['area_id'],
    			 'name'=>stripslashes($val['name'])
    			);
    		}
    	}
    	header('Content-Type: application/json');
    	echo json_encode($data);
    	Yii::app()->end();
    }
    
    public function actionSetLocationSearch()
    {    	    	    	
    	Cookie::setCookie('kr_location_search',json_encode($this->data));    	
    	$this->code=1; $this->msg="OK";
    	$this->details=Yii::app()->createUrl('store/searcharea',array(
    	 'location'=>true
    	));    	
    	$this->jsonResponse();
    }
    
    public function actionCheckLocationData()
    {    	
    	if ( $this->data['delivery_type']=="delivery"){
    		if ( !FunctionsV3::getSearchByLocationData()){
    			$this->code=1;
    			$this->msg=t("No delivery fee selected");
    		}
    	} else $this->msg="OK";
    	$this->jsonResponse();
    }
    
    public function actionShowLocationFee()
    {
    	$this->data=$_GET;    	
    	$this->renderPartial('/front/location-fee',array(
    	  'data'=>FunctionsV3::GetViewLocationRateByMerchant($this->data['merchant_id'])
    	));
    }
    
    public function actionSetLocationFee()
    {    	
    	if ( $data=FunctionsV3::GetFeeByRateIDView($this->data['rate_id'])){    		    		    		
    		//dump($data);
    		$params=array(    		   
    		  'location_action'=>"SetLocationSearch",
    		  'city_id'=>$data['city_id'],
    		  'city_name'=>$data['city_name'],
    		  'area_id'=>$data['area_id'],
    		  'location_city'=>$data['city_name'],
    		  'location_area'=>$data['area_name'],
    		  'state_id'=>$data['state_id'],
    		  'state_name'=>$data['state_name'],
    		  'postal_code'=>$data['postal_code']
    		);    		
    		//dump($params);
    		Cookie::setCookie('kr_location_search',json_encode($params));    	
    		$this->code=1;
    	    $this->msg="OK";    	        	
    	} else $this->msg=t("Failed getting fee details");
    	$this->jsonResponse();
    }
    
    public function actionLoadCityList()
	{
		if ( $data=FunctionsV3::ListCityList($this->data['state_id'])){
		   $html='';
		   foreach ($data as $key=>$val) {		   	  
		   	  $html.="<option value=\"".$key."\">".$val."</option>";
		   }		   
		   $this->code=1;
		   $this->msg="OK";
		   $this->details=$html;
		} else $this->msg= t("No results");
		$this->jsonResponse();
	}    
	
	public function actionLoadArea()
	{		
		if ( $data=FunctionsV3::AreaList($this->data['city_id'])){
			$html='';
		    foreach ($data as $key=>$val) {		   	  
		   	   $html.="<option value=\"".$key."\">".$val."</option>";
		    }		   
		    $this->code=1;
		    $this->msg="OK";
		    $this->details=$html;
		} else $this->msg= t("No results");
		$this->jsonResponse();
	}	
	
	public function actionLoadPostCodeByArea()
	{		
		$DbExt=new DbExt;
		$stmt="SELECT 
		a.area_id,
		a.city_id,
		b.city_id as city_ids,
		b.postal_code
		FROM {{location_area}} a
		left join {{location_cities}} b
        on
        a.city_id=b.city_id	   
        WHERE
        a.area_id=".FunctionsV3::q($this->data['area_id'])."
		";
		if($res=$DbExt->rst($stmt)){
			$this->code=1;
			$this->msg="OK";
			$this->details=$res[0]['postal_code'];
		} else $this->msg=t("No results");
		$this->jsonResponse();
	}
	
    public function actionStateList()
    {    	
    	$data='';
    	$country_id=FunctionsV3::getLocationDefaultCountry();
    	if ( $res=FunctionsV3::locationStateList($country_id)){
    		foreach ($res as $val) {
    			$data[]=array(
   	    		  'id'=>$val['state_id'],
   	    		  'name'=>stripslashes($val['name'])
   	    		);
    		}
    	}    	    	
    	header('Content-Type: application/json');
    	echo json_encode($data);
    	Yii::app()->end();
    }	
    
    public function actionPostalCodeList()
    {
    	$data=''; $state_ids='';
    	$country_id=FunctionsV3::getLocationDefaultCountry();
    	if ( $res=FunctionsV3::locationStateList($country_id)){
    		foreach ($res as $val) {
    			$state_ids.="'$val[state_id]',";
    		}
    		$state_ids=substr($state_ids,0,-1);
    		//dump($state_ids);
    		$DbExt=new DbExt;
    		$stmt="
    		SELECT rate_id,postal_code
    		FROM
    		{{view_location_rate}}
    		WHERE
    		state_id IN ($state_ids)
    		GROUP BY postal_code
    		";
    		//dump($stmt);
    		if($resp=$DbExt->rst($stmt)){
    			foreach ($resp as $valp) {
    				$data[]=array(
	   	    		  'id'=>$valp['rate_id'],
	   	    		  'name'=>stripslashes($valp['postal_code'])
	   	    		);
    			}
    		}
    	}    	    	
    	header('Content-Type: application/json');
    	echo json_encode($data);
    	Yii::app()->end();
    }
    
    public function actionAgeRestriction()
    {
    	
    	$this->renderPartial('/front/age-restriction',array(
    	  'restriction_content'=>getOptionA('age_restriction_content'),
    	  'restriction_exit_link'=>getOptionA('age_restriction_exit_link'),
    	));
    }
        
} /*end class*/    