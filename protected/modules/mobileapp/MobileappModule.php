<?php
/* ********************************************************
 *   Karenderia Mobile App
 *
 *   Last Update : 03 Dec 2015 Version 1.0
 *   Last Update : 10 Dec 2015 Version 1.2
 *   Last Update : 16 Feb 2016 Version 1.3
 *   Last Update : 18 Feb 2016 Version 1.3.1
 *   Last Update : 23 Feb 2016 Version 1.3.2
 *   Last Update : 23 Apr 2016 Version 1.3.3
 *   Last Update : 26 Sep 2016 Version 1.3.4
 *   Last Update : 11 Oct 2016 Version 1.3.5 
 *   Last Update : 28 Oct 2016 Version 1.3.6
 *   Last Update : 25 June 2017 Version 2.0
 *   Last Update : 05 Nov 2017 Version 2.1
 *   Last Update : 18 Nov 2017 Version 2.2
 *   Last Update : 06 Dec 2017 Version 2.3
 *   Last Update : 30 March 18 Version 2.3.1
 *   Last Update : 03 May 2018 Version 2.4
***********************************************************/

class MobileappModule extends CWebModule
{
	public $require_login;
	
	public function init()
	{
		
		// this method is called when the module is being created
		// you may place code here to customize the module or the application
		
		// import the module-level models and components
		$this->setImport(array(			
			'mobileapp.components.*',
		));
		
		$ajaxurl=Yii::app()->baseUrl.'/mobileapp/ajax';
		
		Yii::app()->clientScript->scriptMap=array(
          'jquery.js'=>false,
          'jquery.min.js'=>false
        );

		$cs = Yii::app()->getClientScript();  
		$cs->registerScript(
		  'ajaxurl',
		 "var ajaxurl='$ajaxurl'",
		  CClientScript::POS_HEAD
		);
		
		/*JS FILE*/
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/mobileapp/assets/jquery-1.10.2.min.js',
		CClientScript::POS_END
		);
				
		Yii::app()->clientScript->registerScriptFile(
        '//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js',
		CClientScript::POS_END
		);
						
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/mobileapp/assets/chosen/chosen.jquery.min.js',
		CClientScript::POS_END
		);		
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/mobileapp/assets/SimpleAjaxUploader.min.js',
		CClientScript::POS_END
		);		
		
		Yii::app()->clientScript->registerScriptFile(
        '//cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js',
		CClientScript::POS_END
		);		
		Yii::app()->clientScript->registerScriptFile(
        '//cdn.datatables.net/plug-ins/1.10.9/api/fnReloadAjax.js',
		CClientScript::POS_END
		);		
				
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/mobileapp/assets/noty-2.3.7/js/noty/packaged/jquery.noty.packaged.min.js',
		CClientScript::POS_END
		);		
		
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/mobileapp/assets/jquery.sticky.js',
		CClientScript::POS_END
		);		
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/mobileapp/assets/mobileapp.js?ver=1.0',
		CClientScript::POS_END
		);		
				
				
		/*CSS FILE*/
		$baseUrl = Yii::app()->baseUrl."/protected/modules/mobileapp"; 
		$cs = Yii::app()->getClientScript();		
		$cs->registerCssFile("//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css");		
		$cs->registerCssFile("//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css");
		$cs->registerCssFile("//cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css");		
		$cs->registerCssFile($baseUrl."/assets/animate.css");						
		$cs->registerCssFile($baseUrl."/assets/chosen/chosen.min.css");						
		$cs->registerCssFile($baseUrl."/assets/mobileapp.css?ver=1.0");
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