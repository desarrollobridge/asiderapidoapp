<?php
if (!isset($_SESSION)) { session_start(); }

class AjaxadminController extends CController
{
	
	public $code=2;
	public $msg;
	public $details;
	public $data;
	static $db;
	
	public function __construct()
	{
		$this->data=$_POST;	
		if(isset($_GET['method'])){
			if($_GET['method']=="get"){
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
		
		self::$db=new DbExt;
	}	
	
	public function beforeAction($action)
	{
		$action_name= $action->id ;
		if ( !Yii::app()->functions->isAdminLogin()){
			 $this->msg=t("Error session has expired");
			 $this->jsonResponse();
			 Yii::app()->end();
		}
		return true;
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
	
	public function actionIndex()
	{
		$this->code=1;
		$this->msg=t("Ajax is working");
		$this->jsonResponse();
	}
	
	public function actionTemplate()
	{		
		$email_tags=''; $sms_tags =''; $tag_push='';
		if (isset($this->data['tag_email'])){
			$email_tags=explode(",",$this->data['tag_email']);			
		}
		if (isset($this->data['tag_sms'])){
			$sms_tags=explode(",",$this->data['tag_sms']);			
		}
		if (isset($this->data['tag_push'])){
			$tag_push=explode(",",$this->data['tag_push']);			
		}
		
		array_unshift($email_tags,t("Available Tags"));
		array_unshift($sms_tags,t("Available Tags"));
		$this->renderPartial('/admin/template',array(
		  'data'=>$this->data,
		  'key'=>$this->data['key'],
		  'tag_email'=>$email_tags,
		  'tag_sms'=>$sms_tags,
		  'lang_list'=>FunctionsV3::getLanguageList(),
		  'lang'=>Yii::app()->language,
		  'tag_push'=>$tag_push		  
		));
	}
	
	public function actionsaveTemplate()
	{
		
		$lang=$this->data['template_lang_selection'];		
		if ($lang=="0"){
			$this->msg=t("Invalid language");
		} else {
			$key=$this->data['key'];			
			/*EMAIL*/
			if(isset($this->data['email_subject'])){
				Yii::app()->functions->updateOptionAdmin($key."_tpl_subject_$lang",
				isset($this->data['email_subject'])?$this->data['email_subject']:'');
			}
			
			if(isset($this->data['email_content'])){
				Yii::app()->functions->updateOptionAdmin($key."_tpl_content_$lang",
				isset($this->data['email_content'])?$this->data['email_content']:'');
			}
			
			/*SMS*/		
			if (isset($this->data['sms_content'])){
				Yii::app()->functions->updateOptionAdmin($key."_sms_content_$lang",
				isset($this->data['sms_content'])?$this->data['sms_content']:'');
			}
			
			/*PUSH*/		
			if (isset($this->data['push_content'])){
				Yii::app()->functions->updateOptionAdmin($key."_push_content_$lang",
				isset($this->data['push_content'])?$this->data['push_content']:'');
			}
			if (isset($this->data['push_title'])){
				Yii::app()->functions->updateOptionAdmin($key."_push_title_$lang",
				isset($this->data['push_title'])?$this->data['push_title']:'');
			}
			
			$this->code=1;
			$this->msg=t("Setting saved");
		}
		$this->jsonResponse();
	}
	
	public function actionsaveTemplateSettings()
	{				
				
		$data=$this->data;
		$order_stats = FunctionsV3::orderStatusTPL(2);		
		$predefined=array(
		  'contact_us'."_email",
		  'contact_us'."_sms",
		  'customer_welcome_email'."_email",
		  'customer_welcome_email'."_sms",
		  'customer_forgot_password'."_email",
		  'customer_forgot_password'."_sms",
		  'customer_verification_code_email'."_email",
		  'customer_verification_code_email'."_sms",
		  'customer_verification_code_sms'."_email",
		  'customer_verification_code_sms'."_sms",
		  'merchant_verification_code'."_email",
		  'merchant_verification_code'."_sms",
		  'merchant_forgot_password'."_email",
		  'merchant_forgot_password'."_sms",
		  'admin_forgot_password'."_email",
		  'admin_forgot_password'."_sms",
		  'merchant_new_signup_email',
		  'merchant_new_signup_sms',
		  'receipt_template_email',
		  'receipt_template_sms',
		  'receipt_send_to_merchant_email',
		  'receipt_send_to_merchant_sms',
		  'receipt_send_to_merchant_push',
		  'receipt_send_to_admin_email',
		  'receipt_send_to_admin_sms',
		  'offline_bank_deposit_email',
		  'offline_bank_deposit_sms',
		  'offline_bank_deposit_signup_merchant_email',
		  'offline_bank_deposit_signup_merchant_sms',
		  'offline_bank_deposit_purchase_email',
		  'merchant_near_expiration_email',
		  'merchant_near_expiration_sms',
		  'merchant_change_status_email',
		  'merchant_change_status_sms',
		  'customer_booked_email',
		  'customer_booked_sms',
		  'booked_notify_admin_email',
		  'booked_notify_admin_sms',
		  'booked_notify_merchant_email',
		  'booked_notify_merchant_sms',
		  'booking_update_status_email',
		  'booking_update_status_sms',
		  'booking_update_status_push',
		  'merchant_welcome_signup_email',
		  'merchant_welcome_signup_sms',
		  'order_idle_to_merchant_email',
		  'order_idle_to_merchant_sms',
		  'order_idle_to_admin_email',
		  'order_idle_to_admin_sms',
		  'merchant_invoice_email',
		  'merchant_invoice_sms',
		  'booked_notify_merchant_push',
		);
		
		$predefined=array_merge($predefined,(array)$order_stats);		
		
		foreach ($predefined as $key) {
			if (array_key_exists($key,$data)){				
				Yii::app()->functions->updateOptionAdmin($key,$data[$key]);
			} else {
				Yii::app()->functions->updateOptionAdmin($key,'');
			}
		}
		
		$this->code=1;
		$this->msg=t("Setting saved");
		$this->jsonResponse();
	}
	
	public function actionloadETemplateByLang()
	{
		$lang=$this->data['lang'];
		$subject = $this->data['key']."_tpl_subject_$lang";
		$content = $this->data['key']."_tpl_content_$lang";
		$sms = $this->data['key']."_sms_content_$lang";
		$push = $this->data['key']."_push_content_$lang";
		$push_title = $this->data['key']."_push_title_$lang";
		
		$subject=getOptionA($subject);
		$content=getOptionA($content);
		$sms=getOptionA($sms);
		$push=getOptionA($push);
		$push_title=getOptionA($push_title);
				
		$this->code=1; $this->msg="OK";
		$this->details=array(
		  'subject'=>$subject,
		  'content'=>$content,
		  'sms'=>$sms,
		  'push'=>$push,
		  'push_title'=>$push_title
		);		
		$this->jsonResponse();
	}
	
	public function actionnotiSettings()
	{				
    		
		Yii::app()->functions->updateOptionAdmin('noti_new_signup_email',
		isset($this->data['noti_new_signup_email'])?$this->data['noti_new_signup_email']:'' );
		
		Yii::app()->functions->updateOptionAdmin('noti_new_signup_sms',
		isset($this->data['noti_new_signup_sms'])?$this->data['noti_new_signup_sms']:'' );
		
		Yii::app()->functions->updateOptionAdmin('noti_receipt_email',
		isset($this->data['noti_receipt_email'])?$this->data['noti_receipt_email']:'' );
		
		Yii::app()->functions->updateOptionAdmin('noti_receipt_sms',
		isset($this->data['noti_receipt_sms'])?$this->data['noti_receipt_sms']:'' );
		
		Yii::app()->functions->updateOptionAdmin('admin_disabled_order_notification',
		isset($this->data['admin_disabled_order_notification'])?$this->data['admin_disabled_order_notification']:'' );
		
		Yii::app()->functions->updateOptionAdmin('admin_disabled_order_notification_sounds',
		isset($this->data['admin_disabled_order_notification_sounds'])?$this->data['admin_disabled_order_notification_sounds']:'' );
		
		Yii::app()->functions->updateOptionAdmin('merchant_near_expiration_day',
		isset($this->data['merchant_near_expiration_day'])?$this->data['merchant_near_expiration_day']:'' );
		
		Yii::app()->functions->updateOptionAdmin('noti_booked_admin_email',
		isset($this->data['noti_booked_admin_email'])?$this->data['noti_booked_admin_email']:'' );
		
		Yii::app()->functions->updateOptionAdmin('order_idle_admin_email',
		isset($this->data['order_idle_admin_email'])?$this->data['order_idle_admin_email']:'' );
		
		Yii::app()->functions->updateOptionAdmin('order_idle_admin_minutes',
		isset($this->data['order_idle_admin_minutes'])?$this->data['order_idle_admin_minutes']:'' );
		
		$this->code=1;
		$this->msg=t("Setting saved");
		$this->jsonResponse();
	}
	
	public function actionloadCountryDetails()
	{		
		if ( $res=FunctionsV3::locationStateList($this->data['country_id'])){
			$html=Yii::app()->controller->renderPartial('/admin/manage-country-details',array(
			  'data'=>$res
			),true);
			$this->code=1; $this->msg="OK";
			$this->details=$html;
		} else $this->msg=t("Failed loading data");
		$this->jsonResponse();
	}
	
	public function actionaddCity()
	{		
		if ( $data=FunctionsV3::getStateByID($this->data['state_id'])){
			$this->render('admin/manage-loc-addcity',array(
			  'data'=>$data,
			  'state_id'=>$this->data['state_id'],
			  'data2'=>FunctionsV3::getCityByID( isset($this->data['id'])?$this->data['id']:'' )
			));
		} 
	}
	
	public function actionSaveCity()
	{
	
		$params=array(
		  'state_id'=>$this->data['state_id'],
		  'name'=>$this->data['city_name'],
		  'date_created'=>FunctionsV3::dateNow(),
		  'ip_address'=>$_SERVER['REMOTE_ADDR'],
		  'postal_code'=>isset($this->data['postal_code'])?$this->data['postal_code']:''
		);
		$DbExt=new DbExt;
		
		if ( isset($this->data['id'])){
			unset($params['date_created']);
			$params['date_modified']=FunctionsV3::dateNow();
			if ( $DbExt->updateData("{{location_cities}}",$params,'city_id',$this->data['id'])){
				$this->msg=t("Successful");	
				$this->code=1;
			} else $this->msg=t("Failed cannot update records");
		} else {
			if ($DbExt->insertData("{{location_cities}}",$params)){
				$this->msg=t("Successful");	
				$this->code=1;
			} else $this->msg=t("ERROR. cannot insert data.");
		}
		$this->jsonResponse();
	}
	
	public function actionDeleteCity()
	{		
		$DbExt=new DbExt;		
		$stmt="SELECT * FROM
		{{location_rate}}
		WHERE
		city_id=".FunctionsV3::q($this->data['id'])."
		";		
		if ( $DbExt->rst($stmt)){
			$this->msg=t("You cannot delete this record it has reference to other tables");
			$this->jsonResponse();
		} else {			
			$DbExt->qry("DELETE FROM
			{{location_cities}}
			WHERE
			city_id=".FunctionsV3::q($this->data['id'])."
			");
			$this->msg=t("Successful");	
			$this->code=1;
			$this->jsonResponse();
		}
	}
	
	public function actionAddState()
	{		
		if ( $data=FunctionsV3::getCountryByID($this->data['country_id'])){
			$this->render('admin/manage-loc-addstate',array(
			  'data'=>$data,		
			  'data2'=>FunctionsV3::getStateByID( isset($this->data['state_id'])?$this->data['state_id']:'' )
			));
		} 
	}
	
	public function actionSaveState()
	{		
		$params=array(
		  'country_id'=>$this->data['country_id'],
		  'name'=>$this->data['name'],
		  'date_created'=>FunctionsV3::dateNow(),
		  'ip_address'=>$_SERVER['REMOTE_ADDR']
		);
		$DbExt=new DbExt;		
		if ( isset($this->data['id'])){
			unset($params['date_created']);
			$params['date_modified']=FunctionsV3::dateNow();
			if ( $DbExt->updateData("{{location_states}}",$params,'state_id',$this->data['id'])){
				$this->msg=t("Successful");	
				$this->code=1;
			} else $this->msg=t("Failed cannot update records");
		} else {
			if ($DbExt->insertData("{{location_states}}",$params)){
				$this->msg=t("Successful");	
				$this->code=1;
			} else $this->msg=t("ERROR. cannot insert data.");
		}
		$this->jsonResponse();
	}
	
	public function actionDeleteState()
	{
		$DbExt=new DbExt;
		
		$stmt="SELECT * FROM
		{{location_rate}}
		WHERE
		state_id=".FunctionsV3::q($this->data['id'])."
		";		
		if ( $DbExt->rst($stmt)){
			$this->msg=t("You cannot delete this record it has reference to other tables");
			$this->jsonResponse();
		} else {			
			$DbExt->qry("DELETE FROM
			{{location_states}}
			WHERE
			state_id=".FunctionsV3::q($this->data['id'])."
			");
			$this->msg=t("Successful");	
			$this->code=1;
			$this->jsonResponse();
		}
	}
	
	public function actionAddArea()
	{
		if ( $data=FunctionsV3::getCityByID($this->data['city_id'])){
			$this->render('admin/manage-loc-addarea',array(
			  'data'=>$data,		
			  'data2'=>FunctionsV3::getAreaLocation( isset($this->data['area_id'])?$this->data['area_id']:'' )
			));
		} 
	}
	
	public function actionSaveArea()
	{
		$params=array(
		  'city_id'=>$this->data['city_id'],
		  'name'=>$this->data['name'],
		  'date_created'=>FunctionsV3::dateNow(),
		  'ip_address'=>$_SERVER['REMOTE_ADDR']
		);		
		$DbExt=new DbExt;		
		if ( isset($this->data['id'])){
			unset($params['date_created']);
			$params['date_modified']=FunctionsV3::dateNow();
			if ( $DbExt->updateData("{{location_area}}",$params,'area_id',$this->data['id'])){
				$this->msg=t("Successful");	
				$this->code=1;
			} else $this->msg=t("Failed cannot update records");
		} else {
			if ($DbExt->insertData("{{location_area}}",$params)){
				$this->msg=t("Successful");	
				$this->code=1;
			} else $this->msg=t("ERROR. cannot insert data.");
		}
		$this->jsonResponse();
	}
	
	public function actionDeleteArea()
	{		
		$DbExt=new DbExt;
		
		$stmt="SELECT * FROM
		{{location_rate}}
		WHERE
		area_id=".FunctionsV3::q($this->data['id'])."
		";		
		if ( $DbExt->rst($stmt)){
			$this->msg=t("You cannot delete this record it has reference to other tables");
			$this->jsonResponse();
		} else {	
			$DbExt->qry("DELETE FROM
			{{location_area}}
			WHERE
			area_id=".FunctionsV3::q($this->data['id'])."
			");
			$this->msg=t("Successful");	
			$this->code=1;
			$this->jsonResponse();
		}
	}
	
	public function actionSortArea()
	{		
		if (isset($this->data['ids'])){
			$DbExt=new DbExt;
			$id=explode(",",$this->data['ids']);
			foreach ($id as $sequence=>$area_id) {
				if(!empty($area_id)){
				   $sequence=$sequence+1;				   
				   $DbExt->updateData("{{location_area}}",array(
				     'sequence'=>$sequence,
				     'date_modified'=>FunctionsV3::dateNow(),
				     'ip_address'=>$_SERVER['REMOTE_ADDR']
				   ),'area_id', $area_id);
				}
				$this->msg="OK";
				$this->code=1;
			}
		} else $this->msg=t("Missing ID");
		$this->jsonResponse();
	}
	
	public function actionSortState()
	{
		if (isset($this->data['ids'])){
			$DbExt=new DbExt;
			$id=explode(",",$this->data['ids']);
			foreach ($id as $sequence=>$id) {
				if(!empty($id)){
				   $sequence=$sequence+1;				   
				   $DbExt->updateData("{{location_states}}",array(
				     'sequence'=>$sequence,
				     'date_modified'=>FunctionsV3::dateNow(),
				     'ip_address'=>$_SERVER['REMOTE_ADDR']
				   ),'state_id', $id);
				}
				$this->msg="OK";
				$this->code=1;
			}
		} else $this->msg=t("Missing ID");
		$this->jsonResponse();
	}
	
	public function actionEditInvoice()
	{
		$this->data=$_GET;		
		if ($res=FunctionsV3::getInvoiceByID($this->data['id'])){
			$this->renderPartial('/admin/invoice-edit',array(
			  'data'=>$res
			));
		} else echo t("No results");
	}
	
	public function actionSaveInvoice()
	{
		$DbExt=new DbExt;		
		$DbExt->updateData("{{invoice}}",array(
		  'payment_status'=>$this->data['payment_status']
		),'invoice_number',$this->data['invoice_number']);
		$this->code=1;
		$this->msg=t("Successful");
		
		$params=array(
		  'invoice_number'=>$this->data['invoice_number'],
		  'payment_status'=>$this->data['payment_status'],
		  'remarks'=>isset($this->data['remarks'])?$this->data['remarks']:'',
		  'date_created'=>FunctionsV3::dateNow(),
		  'ip_address'=>$_SERVER['REMOTE_ADDR']
		);
		$DbExt->insertData("{{invoice_history}}",$params);
		
		$this->jsonResponse();
	}
	
	public function actionInvoiceHistory()
	{
		$this->data=$_GET;		
		$res=FunctionsV3::getInvoiceHistory($this->data['id']);
		$this->renderPartial('/admin/invoice-history',array(
		   'invoice_number'=>$this->data['id'],
		   'data'=>$res
		));
	}
	
} /*end class*/