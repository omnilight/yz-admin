<?php

class ModuleSettingsController extends YzBackController
{
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

        // Checking for new or deleted settings of the module
        $module = Yz::get()->getModule($model->module_id);

        if($module === null)
            throw new CHttpException(404);

        $dbParameters = $model->parameters;
        $moduleParameters = array();
        foreach($module->getEditableParams() as $param => $type)
            $moduleParameters[$param] = $module->{$param};

        $newParameters = array_diff_key($moduleParameters, $dbParameters);
        $currentParameters = array_intersect_key($dbParameters, $moduleParameters);

        $parameters = array_merge($currentParameters, $newParameters);

        if(!empty($newParameters)) {
            $model->parameters = $parameters;
            $model->save();
        }

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['YzModuleSettings']))
		{
			$model->attributes=$_POST['YzModuleSettings'];
			if($model->save()) {
                Yii::app()->user->setFlash('success',Yii::t('AdminModule.t9n','Record was successfully updated'));
                if(!isset($_POST['saveAndStay']))
                    $this->redirect(isset($_GET['returnUrl']) ? $_GET['returnUrl'] : array('index'));
            }
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionIndex()
	{
        /**
         * Each time we rescan enabled modules to add settings for them
         * and to delete settings for ones that were deleted
         */

        $settings = YzModuleSettings::model()->findAll(array('index'=>'module_id'));
        $modules = Yz::get()->getModules();

        $newModules = array_diff_key($modules, $settings);
        $deleteSettings = array_diff_key($settings, $modules);

        foreach($newModules as $moduleId => $module) {
            /** @var $module YzWebModule */
            $setting = new YzModuleSettings();
            $setting->module_id = $moduleId;
            $parameters = array();
            foreach($module->getEditableParams() as $param => $type)
                $parameters[$param] = $module->{$param};
            $setting->parameters = $parameters;
            $setting->save();
            $settings[$moduleId] = $setting;
        }

        foreach($deleteSettings as $moduleId => $setting) {
            /** @var $setting YzModuleSettings */
            $setting->delete();
        }

        $model=new YzModuleSettings('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['YzModuleSettings']))
			$model->attributes=$_GET['YzModuleSettings'];

		$this->render('index',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
     * @return YzModuleSettings
	 */
	public function loadModel($id)
	{
		$model=YzModuleSettings::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='yz-module-settings-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
