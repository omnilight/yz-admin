<?php

class AdminUsersController extends YzBackController
{
    /**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new AdminUsers;

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['AdminUsers']))
		{
			$model->attributes=$_POST['AdminUsers'];
			if($model->save()) {
                Yii::app()->user->setFlash('success',Yii::t('AdminModule.t9n','Record was successfully created'));
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
            }
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

        if($model->is_superadmin && Yii::app()->user->checkAccess('AdminUsers.ManageSuperAdminProfile') == false) {
            Yii::app()->user->setFlash('error',Yii::t('AdminModule.t9n','<strong>Sorry!</strong> You don\'t have permissions to edit super administrator profile'));
            $this->redirect(isset($_GET['returnUrl']) ? $_GET['returnUrl'] : array('index'));
        }

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['AdminUsers']))
		{
			if(isset($_POST['AdminUsers']['p'])) {
                $model->attributes=$_POST['AdminUsers']['p'];
                $model->setScenario('changePassword');
            } else {
                $model->attributes=$_POST['AdminUsers'];
            }
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
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     */
    public function actionDelete()
    {
        /*
         * We don't use Yii's future of automatic parameters binding
         * as it allows passing only array or scalar values, but we
         * support both (in the case of multiple delete
         */
        // we only allow deletion via POST request
        if(Yii::app()->request->isPostRequest)
        {
            $ids = Yii::app()->request->getParam('id');

            if(!is_array($ids))
                $ids = array($ids);

            foreach($ids as $id) {
                $model = $this->loadModel($id);

                if(Yii::app()->user->checkAccess('adminUsers.manageSuperAdmin', array('user'=>$model)) == false) {
                    Yii::app()->user->setFlash('error',Yii::t('AdminModule.t9n','<strong>Sorry!</strong> You don\'t have permissions to delete super administrator profile'));
                    $this->redirect(isset($_GET['returnUrl']) ? $_GET['returnUrl'] : array('index'));
                }

                $model->delete();
            }

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if(!isset($_GET['ajax'])) {
                Yii::app()->user->setFlash('success', Yii::t('AdminModule.t9n',
                    'Record was successfully deleted|{n} records were successfully deleted',
                    count($ids)
                ));
                $this->redirect(isset($_GET['returnUrl']) ? $_GET['returnUrl'] : array('index'));
            }
        }
        else
            throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
    }

	/**
	 * Manages all models.
	 */
	public function actionIndex()
	{
		$model=new AdminUsers('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['AdminUsers']))
			$model->attributes=$_GET['AdminUsers'];

		$this->render('index',array(
			'model'=>$model,
		));
	}

    public function actionOwnProfile()
    {
        $id = Yii::app()->user->id;
        $model=$this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        $this->performAjaxValidation($model);

        if(isset($_POST['AdminUsers']))
        {
            if(isset($_POST['AdminUsers']['p'])) {
                $model->attributes=$_POST['AdminUsers']['p'];
                $model->setScenario('changePassword');
            } else {
                $model->setScenario('ownProfile');
                $model->attributes=$_POST['AdminUsers'];
            }
            if($model->save()) {
                Yii::app()->user->setFlash('success',Yii::t('AdminModule.t9n','Your profile was successfully updated'));
                $this->redirect(array('ownProfile'));
            }
        }

        $this->render('ownProfile',array(
            'model'=>$model,
        ));
    }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
     * @return AdminUsers
	 */
	public function loadModel($id)
	{
		$model=AdminUsers::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='admin-users-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
