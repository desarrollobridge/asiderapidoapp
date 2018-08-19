<?php
/**
 *   Last Update : 1.4.0 - December 10, 2016
 *   Last Update : 1.5.0 - March 27, 2017
 *   Last Update : 1.6.0 - January 12, 2018
 */
class DriverModule extends CWebModule
{
	public $require_login;
		
	public function init()
	{
		
		// this method is called when the module is being created
		// you may place code here to customize the module or the application
		
		// import the module-level models and components
		$this->setImport(array(			
			'driver.components.*',
			'driver.models.*'
		));
		
		$ajaxurl=Yii::app()->baseUrl.'/driver/ajax';
		$site_url=Yii::app()->baseUrl.'/protected/modules/driver';
		$home_url=Yii::app()->baseUrl.'/driver';
		$upload_url=Yii::app()->baseUrl."/upload";
		
		
		Yii::app()->clientScript->scriptMap=array(
          'jquery.js'=>false,
          'jquery.min.js'=>false
        );

		$cs = Yii::app()->getClientScript();  
		$cs->registerScript(
		  'ajaxurl',
		 "var ajax_url='$ajaxurl'",
		  CClientScript::POS_HEAD
		);
		$cs->registerScript(
		  'site_url',
		 "var site_url='$site_url'",
		  CClientScript::POS_HEAD
		);
		$cs->registerScript(
		  'home_url',
		 "var home_url='$home_url'",
		  CClientScript::POS_HEAD
		);		
		$cs->registerScript(
		  'upload_url',
		 "var upload_url='$upload_url'",
		  CClientScript::POS_HEAD
		);
				
		/*MAP MARKER*/
		$delivery_icon=Yii::app()->baseUrl.'/protected/modules/driver/assets/images/yellow.png';
		$pickup_icon=Yii::app()->baseUrl.'/protected/modules/driver/assets/images/blue.png';
		
		$driver_icon=Yii::app()->baseUrl.'/protected/modules/driver/assets/images/icon54.png';
		$driver_icon_offline=Yii::app()->baseUrl.'/protected/modules/driver/assets/images/icon7.png';
		
		$delivery_icon_successful=Yii::app()->baseUrl.'/protected/modules/driver/assets/images/yellow-dot.png';
		$delivery_icon_failed=Yii::app()->baseUrl.'/protected/modules/driver/assets/images/X.png';
		
		$pickup_icon_ok=Yii::app()->baseUrl.'/protected/modules/driver/assets/images/blue-dot.png';
		
		$cs->registerScript(
		  'map_marker_delivery',
		 "var map_marker_delivery='$delivery_icon'",
		  CClientScript::POS_HEAD
		);
		$cs->registerScript(
		  'pickup_icon',
		 "var map_pickup_icon='$pickup_icon'",
		  CClientScript::POS_HEAD
		);
		$cs->registerScript(
		  'driver_icon',
		 "var driver_icon='$driver_icon'",
		  CClientScript::POS_HEAD
		);
		$cs->registerScript(
		  'driver_icon_offline',
		 "var driver_icon_offline='$driver_icon_offline'",
		  CClientScript::POS_HEAD
		);		
		$cs->registerScript(
		  'delivery_icon_successful',
		 "var delivery_icon_successful='$delivery_icon_successful'",
		  CClientScript::POS_HEAD
		);
		$cs->registerScript(
		  'delivery_icon_failed',
		 "var delivery_icon_failed='$delivery_icon_failed'",
		  CClientScript::POS_HEAD
		);
		$cs->registerScript(
		  'pickup_icon_ok',
		 "var pickup_icon_ok='$pickup_icon_ok'",
		  CClientScript::POS_HEAD
		);
		
		$default_country=Yii::app()->functions->getOptionAdmin('drv_default_location');
		$default_location_lat=Yii::app()->functions->getOptionAdmin('drv_default_location_lat');
		$default_location_lng=Yii::app()->functions->getOptionAdmin('drv_default_location_lng');
		$drv_map_style=Yii::app()->functions->getOptionAdmin('drv_map_style');
		
		$default_location_lat=!empty($default_location_lat)?$default_location_lat:-12.043333;
		$default_location_lng=!empty($default_location_lng)?$default_location_lng:-77.028333;
		
		$driver_disabled_auto_refresh=Yii::app()->functions->getOptionAdmin('driver_disabled_auto_refresh');
		if(empty($driver_disabled_auto_refresh)){
			$driver_disabled_auto_refresh=2;
		}
		
		/** START Set general settings */
		$cs->registerScript(
		  'default_country',
		 "var default_country='$default_country'",
		  CClientScript::POS_HEAD
		);
		$cs->registerScript(
		  'default_location_lat',
		 "var default_location_lat=$default_location_lat",
		  CClientScript::POS_HEAD
		);
		$cs->registerScript(
		  'default_location_lng',
		 "var default_location_lng=$default_location_lng",
		  CClientScript::POS_HEAD
		);
		$cs->registerScript(
		  'driver_disabled_auto_refresh',
		 "var driver_disabled_auto_refresh=$driver_disabled_auto_refresh",
		  CClientScript::POS_HEAD
		);
		
		
		$drv_map_style_res = json_decode($drv_map_style);
					
		if ( is_array($drv_map_style_res) && !empty($drv_map_style)){
			$cs->registerScript(
			  'map_style',
			 "var map_style=$drv_map_style",
			  CClientScript::POS_HEAD
			);
		} else {
			$map_style='[{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#0f252e"},{"lightness":17}]}];';
			$cs->registerScript(
			  'map_style',
			 "var map_style=$map_style",
			  CClientScript::POS_HEAD
			);
		}
		/** END Set general settings */
		
		
		/*JS FILE*/
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/driver/assets/jquery-1.10.2.min.js',
		CClientScript::POS_END
		);
				
		/*Yii::app()->clientScript->registerScriptFile(
        '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js',
		CClientScript::POS_END
		);*/
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/driver/assets/bootstrap/js/bootstrap.min.js',
		CClientScript::POS_END
		);
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/driver/assets/chosen/chosen.jquery.min.js',
		CClientScript::POS_END
		);
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/driver/assets/noty-2.3.7/js/noty/packaged/jquery.noty.packaged.min.js',
		CClientScript::POS_END
		);						
		
		/*Yii::app()->clientScript->registerScriptFile(
        '//cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js',
		CClientScript::POS_END
		);		
		Yii::app()->clientScript->registerScriptFile(
        '//cdn.datatables.net/plug-ins/1.10.9/api/fnReloadAjax.js',
		CClientScript::POS_END
		);*/		
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/driver/assets/DataTables/jquery.dataTables.min.js',
		CClientScript::POS_END
		);						
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/driver/assets/DataTables/fnReloadAjax.js',
		CClientScript::POS_END
		);						
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/driver/assets/jquery.sticky2.js',
		CClientScript::POS_END
		);
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/driver/assets/SimpleAjaxUploader.min.js',
		CClientScript::POS_END
		);		
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/driver/assets/summernote/summernote.min.js',
		CClientScript::POS_END
		);		
		
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/driver/assets/markercluster.js?ver=1.0',
		CClientScript::POS_END
		);		
				
		/*Yii::app()->clientScript->registerScriptFile(
	        '//google-maps-utility-library-v3.googlecode.com/svn/tags/markerclusterer/1.0/src/markerclusterer.js',
			CClientScript::POS_END
		);		*/
		
		$google_key=Yii::app()->functions->getOptionAdmin('drv_google_api');		
		if (!empty($google_key)){
			Yii::app()->clientScript->registerScriptFile(
	        '//maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key='.urlencode($google_key),
			CClientScript::POS_END
			);		
		} else {
			Yii::app()->clientScript->registerScriptFile(
	        '//maps.googleapis.com/maps/api/js?v=3.exp&libraries=places',
			CClientScript::POS_END
			);		
		}
				
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/driver/assets/gmaps.js',
		CClientScript::POS_END
		);		
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/driver/assets/jquery.geocomplete.min.js',
		CClientScript::POS_END
		);		
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/driver/assets/form-validator/jquery.form-validator.min.js',
		CClientScript::POS_END
		);		
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/driver/assets/intel/build/js/intlTelInput.js?ver=2.1.5',
		CClientScript::POS_END
		);		
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/driver/assets/nprogress/nprogress.js',
		CClientScript::POS_END
		);		
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/driver/assets/datetimepicker/jquery.datetimepicker.full.min.js',
		CClientScript::POS_END
		);		
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/driver/assets/moment.js',
		CClientScript::POS_END
		);								
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/driver/assets/js-date-format.min.js',
		CClientScript::POS_END
		);								
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/driver/assets/switch/bootstrap-switch.min.js',
		CClientScript::POS_END
		);								
		
		Yii::app()->clientScript->registerScriptFile(
        "//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js",
		CClientScript::POS_END
		);								
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/driver/assets/jplayer/jquery.jplayer.min.js',
		CClientScript::POS_END
		);								
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/driver/assets/js.kookie.js',
		CClientScript::POS_END
		);								
					
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/driver/assets/driver.js?ver=1.0',
		CClientScript::POS_END
		);								
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/driver/assets/driver-js.js?ver=1.0',
		CClientScript::POS_END
		);								
				
		/*CSS FILE*/
		$baseUrl = Yii::app()->baseUrl."/protected/modules/driver"; 
		$cs = Yii::app()->getClientScript();		
		//$cs->registerCssFile("//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css");		
		$cs->registerCssFile($baseUrl."/assets/bootstrap/css/bootstrap.min.css");		
		
		$cs->registerCssFile($baseUrl."/assets/chosen/chosen.min.css");		
		$cs->registerCssFile($baseUrl."/assets/animate.css");	
		$cs->registerCssFile($baseUrl."/assets/summernote/summernote.css");	
		$cs->registerCssFile("//cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css");		
		//$cs->registerCssFile($baseUrl."/assets/DataTables");	
		$cs->registerCssFile("//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css");
		$cs->registerCssFile("//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css");
		
		$cs->registerCssFile($baseUrl."/assets/intel/build/css/intlTelInput.css");
		$cs->registerCssFile($baseUrl."/assets/nprogress/nprogress.css");	
		$cs->registerCssFile($baseUrl."/assets/datetimepicker/jquery.datetimepicker.css");	
		$cs->registerCssFile($baseUrl."/assets/switch/bootstrap-switch.min.css");
		
		$cs->registerCssFile("//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css");
		
		$cs->registerCssFile($baseUrl."/assets/driver.css?ver=1.0");
		$cs->registerCssFile($baseUrl."/assets/driver-responsive.css?ver=1.0");
	}

	public function beforeControllerAction($controller, $action)
	{		
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here									
			return true;
		}
		else
			return false;
	}
}