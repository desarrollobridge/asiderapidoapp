<?php
/**
 *   Last Update : 1.0.0 (09 Jan 16) 
 *   last Update : 1.0.1 (17 Jan 16)
 *   last Update : 1.0.2 (21 Jan 16)* 
 *   last Update : 1.0.3 (25 April 16)
 *   last Update : 1.0.4 (08 May 16)
 *   last Update : 1.0.5 (06 Nov 16)
 *   last Update : 1.0.6 (11 Nov 16)
 *   last Update : 2.0 (30 August 17)
 *   last Update : 2.1 (03 Jan 18)
 */
class MerchantappModule extends CWebModule
{
	public $require_login;
		
	public function init()
	{
		
		// this method is called when the module is being created
		// you may place code here to customize the module or the application
		
		// import the module-level models and components
		$this->setImport(array(			
			'merchantapp.components.*',
			'merchantapp.models.*'
		));
		
		$ajaxurl=Yii::app()->baseUrl.'/merchantapp/ajax';
		
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
				
		$js_lang=Yii::app()->functions->jsLanguageAdmin();        
        $cs->registerScript(
		  'js_lang',
		  'var js_lang = '.json_encode($js_lang).'
		  ',
		  CClientScript::POS_HEAD
		);		
		
		/*JS FILE*/
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/merchantapp/assets/jquery-1.10.2.min.js',
		CClientScript::POS_END
		);
				
		Yii::app()->clientScript->registerScriptFile(
        '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js',
		CClientScript::POS_END
		);
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/merchantapp/assets/chosen/chosen.jquery.min.js',
		CClientScript::POS_END
		);
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/merchantapp/assets/noty-2.3.7/js/noty/packaged/jquery.noty.packaged.min.js',
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
        Yii::app()->baseUrl . '/protected/modules/merchantapp/assets/jquery.sticky.js',
		CClientScript::POS_END
		);
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/merchantapp/assets/SimpleAjaxUploader.min.js',
		CClientScript::POS_END
		);		
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/merchantapp/assets/summernote/summernote.min.js',
		CClientScript::POS_END
		);		
			
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/merchantapp/assets/merchantapp.js?ver=1.0',
		CClientScript::POS_END
		);		
				
		/*CSS FILE*/
		$baseUrl = Yii::app()->baseUrl."/protected/modules/merchantapp"; 
		$cs = Yii::app()->getClientScript();		
		$cs->registerCssFile("//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css");		
		$cs->registerCssFile($baseUrl."/assets/chosen/chosen.min.css");		
		$cs->registerCssFile($baseUrl."/assets/animate.css");	
		$cs->registerCssFile($baseUrl."/assets/summernote/summernote.css");	
		$cs->registerCssFile("//cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css");		
		$cs->registerCssFile("//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css");
		$cs->registerCssFile($baseUrl."/assets/merchantapp.css?ver=1.0");
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