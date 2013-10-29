<?php

class DbAdminController extends YzBackController
{
    public $layout = 'yzAdmin.views.layouts.main';

    public function checkAccess($user)
    {
        // Only super admin users can have access to this controller
        return $this->module->enableDbAdmin && $user->isSuperAdmin;
    }


    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionAdminer()
    {
        include_once(Yii::getPathOfAlias('yzAdmin.helpers').'/AdminerHelper.php');
    }
}
