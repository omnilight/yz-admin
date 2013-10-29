<?php

class JsController extends YzBackController
{
    public function accessRules()
    {
        return CMap::mergeArray(array(
            array(
                'allow',
                'actions' => array('constants'),
            )
        ), parent::accessRules());
    }

    public function actionConstants()
    {
        $this->renderPartial('constants');
    }

    public function actionSetFlash()
    {
        $type = Yii::app()->request->getParam('type','success');
        $message = Yii::app()->request->getParam('message');

        Yii::app()->user->setFlash($type,$message);

        YzAsyncResponse::success();
    }

    public function actionRedirect()
    {
        $route = Yii::app()->request->getParam('route','/admin/backend/default/index');
        $params = Yii::app()->request->getParam('params',array());
        $route = array_merge(array($route),$params);
        $this->redirect($route);
    }
}