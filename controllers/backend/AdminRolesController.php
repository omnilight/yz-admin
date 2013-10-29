<?php

class AdminRolesController extends YzBackController
{
    /**
     * @var YzAdminAuthManager
     */
    protected $_authManager;

    public function init()
    {
        parent::init();

        $this->_authManager = Yii::app()->getAuthManager();
    }

    /**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new AdminAuthItem;

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['AdminAuthItem']))
		{
			$model->attributes=$_POST['AdminAuthItem'];
            $model->type = CAuthItem::TYPE_ROLE;
			if($model->save()) {
                Yii::app()->user->setFlash('success',Yii::t('AdminModule.t9n','Record was successfully created'));
                if(!isset($_POST['saveAndStay']))
                    $this->redirect(isset($_GET['returnUrl']) ? $_GET['returnUrl'] : array('index'));
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

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['AdminAuthItem']))
		{
			$model->attributes=$_POST['AdminAuthItem'];
            $model->type = CAuthItem::TYPE_ROLE;
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
         * support both (in the case of multiple delete)
         */
        // we only allow deletion via POST request
		if(Yii::app()->request->isPostRequest)
		{
            $ids = Yii::app()->request->getParam('id');

            if(!is_array($ids))
                $ids = array($ids);

            foreach($ids as $id) {
			    $this->loadModel($id)->delete();
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
		$model=new AdminAuthItem('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['AdminAuthItem']))
			$model->attributes=$_GET['AdminAuthItem'];

        $model->roles();

        $model->with(array(
            'children' => array('condition'=>'children.type = '.CAuthItem::TYPE_ROLE),
        ));

		$this->render('index',array(
			'model'=>$model,
		));
	}

    public function actionDiscoverAuthItems()
    {
        Yz::isDeveloperMode();

        $isClear = $_GET['method'] == 'deleteExisted';

        $authItems = $this->getAllAuthItems();

        $childrenRelations = array();

        $transaction = $this->_authManager->db->beginTransaction();

        if($isClear)
            $this->_authManager->db->createCommand()
                ->delete($this->_authManager->itemTable);

        foreach( $authItems as $authItem ) {
            /** @var $authItem['auth'] CAuthItem */
            if($this->_authManager->getAuthItem($authItem['auth']->getName()))
                $this->_authManager->saveAuthItem($authItem['auth']);
            else
                $this->_authManager->createAuthItem(
                    $authItem['auth']->getName(),
                    $authItem['auth']->getType(),
                    $authItem['auth']->getDescription(),
                    $authItem['auth']->getBizRule(),
                    $authItem['auth']->getData()
                );
            if(!empty($authItem['children']))
                $childrenRelations[$authItem['auth']->getName()] = $authItem['children'];
        }

        foreach($childrenRelations as $parent => $children)
            foreach($children as $child)
                if($this->_authManager->hasItemChild($parent,$child) == false)
                    $this->_authManager->addItemChild($parent,$child);

        $transaction->commit();

        Yii::app()->user->setFlash('success',Yii::t('AdminModule.t9n','All operations were successfully added to database'));

        $this->redirect(array('index'));
    }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
     * @return AdminAuthItem
	 */
	public function loadModel($id)
	{
		$model=AdminAuthItem::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

    /**
     * Collects operations, based on modules controllers (one's that extend YzBackController),
     * also from modules getOperations() methods and so on.
     *
     * TODO Move this method to other place?
     */
    protected function getAllAuthItems()
    {
        $authItems = array();

        // Operations of moduless
        $modules = Yz::get()->getModules();

        foreach( $modules as $module ) {
            /** @var $module YzWebModule */
            if($module->isShowInAdminPanel == false)
                continue;

            if( $module->isAutoDiscoverAuthItems ) {
                $controllers = $this->getModuleControllers($module);

                $mName = ucfirst($module->id) . '.*';
                $authItems[$mName]['auth'] = new CAuthItem(
                    $this->_authManager,
                    $mName,
                    CAuthItem::TYPE_TASK,
                    Yii::t('AdminModule.t9n', 'Module "{module}"', array(
                        '{module}' => $module->getName(),
                    ))
                );
                $authItems[$mName]['children'] = array();

                foreach( $controllers as $controllerInfo ) {
                    $tmp = $this->getControllerAuthItems($module, $controllerInfo);
                    $authItems = array_merge($authItems, $tmp);
                    foreach( $tmp as $childName => $authItem )
                        if( $authItem['auth']->getType() == CAuthItem::TYPE_TASK )
                            $authItems[$mName]['children'][] = $childName;
                }
            }

            $authItems = CMap::mergeArray($authItems, $module->getAuthItems());
        }

        return $authItems;
    }

    /**
     * @param $module YzWebModule
     * @param $controllerInfo array
     *
     * @return array
     */
    protected function getControllerAuthItems($module, $controllerInfo)
    {
        $actions = $this->getControllerActions($controllerInfo);

        if( $actions === false )
            return array();

        $authItems = array();

        $cName = ucfirst($module->id) . ".".ucfirst($controllerInfo['id']).".*";
        $authItems[$cName]['auth'] = new CAuthItem(
            $this->_authManager,
            $cName,
            CAuthItem::TYPE_TASK,
            Yii::t('AdminModule.t9n', 'Controller "{module} > {controller}"', array(
                '{controller}' => $controllerInfo['name'],
                '{module}' => $module->getName(),
            ))
        );
        $authItems[$cName]['children'] = array();

        foreach( $actions as $name => $action ) {
            $oName = ucfirst($module->id) . ".".ucfirst($controllerInfo['id']).".".ucfirst($action['name']);
            $authItems[$oName]['auth'] = new CAuthItem(
                $this->_authManager,
                $oName,
                CAuthItem::TYPE_OPERATION,
                Yii::t('AdminModule.t9n', 'Action "{module} > {controller} > {action}"', array(
                    '{action}' => $action['name'],
                    '{controller}' => $controllerInfo['name'],
                    '{module}' => $module->getName(),
                ))
            );
            $authItems[$cName]['children'][] = $oName;
        }

        return $authItems;
    }

    public function getControllerActions($controllerInfo)
    {
        /*
         * TODO Rewrite due to text analyzing, maybe Reflection
         *
         * The following algorithm of discovering controller actions
         * uses regular expression. This is not good due to possible
         * parsing bugs. Also this algorithm can't find actions of classes,
         * that aren't childs of YzBackController (ex. extends MyBackController
         * with extends YzBackController)
         */
        $actions = array();
        $file = fopen($controllerInfo['path'], 'r');
        $lineNumber = 0;
        $yzWebModuleChild = false;
        while( feof($file)===false )
        {
            ++$lineNumber;
            $line = fgets($file);
            preg_match('/public[ \t]+function[ \t]+action([A-Z]{1}[a-zA-Z0-9]+)[ \t]*\(/', $line, $matches);
            if( $matches!==array() )
            {
                $name = $matches[1];
                $actions[ strtolower($name) ] = array(
                    'name'=>$name,
                    'line'=>$lineNumber
                );
            }
            preg_match('/class[ \t]+([A-Z]{1}[a-zA-Z0-9_]+)[ \t]extends[ \t]YzBackController/', $line,$matches);
            if( $matches!==array() )
                $yzWebModuleChild = true;
        }

        return $yzWebModuleChild? $actions : false;
    }


    /**
     * @param $module YzWebModule
     * @param $subPath string
     * @return array
     */
    protected function getModuleControllers($module, $subPath = '')
    {
        $controllers = array();

        $path = $module->getControllerPath() . $subPath;

        if( file_exists($path)===true )
        {
            $controllerDirectory = scandir($path);
            foreach( $controllerDirectory as $entry )
            {
                if( $entry{0}!=='.' )
                {
                    $entryPath = $path.DIRECTORY_SEPARATOR.$entry;
                    $newSubPath = $subPath.DIRECTORY_SEPARATOR.$entry;

                    if( is_dir($entryPath)===true )
                        $controllers = array_merge($controllers,
                            $this->getModuleControllers($module, $newSubPath));
                    elseif( strpos(strtolower($entry), 'controller')!==false )
                    {
                        $name = substr($entry, 0, -14);
                        $controllerId = trim($subPath,DIRECTORY_SEPARATOR) . '/'.lcfirst($name);
                        $controllerId = str_replace('\\','/',$controllerId);
                        $controllers[] = array(
                            'name'=>$name,
                            'id'=>$controllerId,
                            'file'=>$entry,
                            'path'=>$entryPath,
                        );
                    }
                }
            }
        }

        return $controllers;
    }


    /**
	 * Performs the AJAX validation.
	 * @param CModel $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='admin-auth-item-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
