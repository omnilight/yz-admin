<?php

/**
 * @property string $name
 * @property string $login
 * @property boolean $isSuperAdmin
 */
class YzAdminWebUser extends CWebUser
{
    public $loginUrl = array('/admin/backend/default/login');

    /** @var AdminUsers */
    private $_model = null;

    public function getReturnUrl($defaultUrl=array('/admin/backend/default/index'))
    {
        return parent::getReturnUrl($defaultUrl);
    }

    public function checkAccess($operation,$params=array(),$allowCaching=true)
    {
        if( $this->isSuperAdmin )
            return true;
        else
            return parent::checkAccess($operation,$params,$allowCaching);
    }

    public function getIsSuperAdmin()
    {
        if( $this->hasState('isSuperAdmin') )
            return $this->getState('isSuperAdmin');
        else
            return false;
    }

    public function getLogin()
    {
        if( $this->hasState('login') )
            return $this->hasState('login');
        else
            return '';
    }

    protected function afterLogin($fromCookie)
    {
        Yii::app()->getModule('admin')->onAdminUserLogin(new CEvent($this,array(
            'user_id' => $this->id,
        )));
    }

    /**
     * @return AdminUsers
     */
    public function getModel(){
        if (!$this->isGuest && $this->_model === null){
            $this->_model = AdminUsers::model()->findByPk($this->id);
        }
        return $this->_model;
    }
}