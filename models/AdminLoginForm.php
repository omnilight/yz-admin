<?php

class AdminLoginForm extends CFormModel
{
    public $login;
    public $password;

    private $_identity;

    public function rules()
    {
        return array(
            array('login, password', 'required'),
            array('password', 'authenticate'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'login' => Yii::t('AdminModule.t9n','Login'),
            'password' => Yii::t('AdminModule.t9n','Password'),
        );
    }

    public function authenticate($attribute, $params)
    {
        $this->_identity = new YzAdminUserIdentity($this->login, $this->password);
        if(!$this->_identity->authenticate())
            $this->addError('password', Yii::t('AdminModule.t9n', 'Incorrect username or password'));
    }

    public function getIdentity()
    {
        return $this->_identity;
    }
}