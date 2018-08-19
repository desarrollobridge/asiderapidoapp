<?php
/* ********************************************************
 *   Loyalty Points Program
 *
 *   Last Update : 28 Dec 2015 Version 1.0
 *   Last Update : 3 April 2016 Version 1.1
 *   Last Update : 15 February 2018 Version 2.0
***********************************************************/
class PointsprogramModule extends CWebModule
{
	public $require_login;
		
	public function init()
	{
		
		// this method is called when the module is being created
		// you may place code here to customize the module or the application
		
		// import the module-level models and components
		$this->setImport(array(			
			'pointsprogram.components.*',
		));
		
		$ajaxurl=Yii::app()->baseUrl.'/pointsprogram/ajax';
		
		Yii::app()->clientScript->scriptMap=array(
          'jquery.js'=>false,
          'jquery.min.js'=>false
        );

		$cs = Yii::app()->getClientScript();  
		$cs->registerScript(
		  'ajaxurl',
		 "var ajaxurl='$ajaxurl';",
		  CClientScript::POS_HEAD
		);
		
		
		$js_lang = json_encode(Yii::app()->functions->jsLanguageAdmin());		
		
		$cs = Yii::app()->getClientScript();  
		$cs->registerScript(
		  'js_lang',
		 "var js_lang=$js_lang;",
		  CClientScript::POS_HEAD
		);
		
		/*JS FILE*/
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/pointsprogram/assets/jquery-1.10.2.min.js',
		CClientScript::POS_END
		);
				
		Yii::app()->clientScript->registerScriptFile(
        '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js',
		CClientScript::POS_END
		);
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/pointsprogram/assets/chosen/chosen.jquery.min.js',
		CClientScript::POS_END
		);
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/pointsprogram/assets/noty-2.3.7/js/noty/packaged/jquery.noty.packaged.min.js',
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
        Yii::app()->baseUrl . '/protected/modules/pointsprogram/assets/pointsprogram.js?ver=1.0',
		CClientScript::POS_END
		);		
				
		/*CSS FILE*/
		$baseUrl = Yii::app()->baseUrl."/protected/modules/pointsprogram"; 
		$cs = Yii::app()->getClientScript();		
		$cs->registerCssFile("//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css");		
		$cs->registerCssFile($baseUrl."/assets/chosen/chosen.min.css");		
		$cs->registerCssFile($baseUrl."/assets/animate.css");	
		$cs->registerCssFile("//cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css");		
		$cs->registerCssFile("//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css");
		$cs->registerCssFile($baseUrl."/assets/pointsprogram.css?ver=1.0");
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