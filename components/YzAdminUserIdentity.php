<?php

class YzAdminUserIdentity extends CUserIdentity
{
    private $_id;

    public function authenticate()
    {
        /** @var $record AdminUsers */
        $record=AdminUsers::model()->enabled()->findByAttributes(array('login'=>$this->username));

        if($record===null )
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        else if($record->passhash !== AdminUsers::hashPassword($this->password, $record->salt))
            $this->errorCode=self::ERROR_PASSWORD_INVALID;
        else
        {
            $this->_id=$record->id;

            $this->setPersistentStates(array(
                'name' => $record->name,
                'login' => $record->login,
                'isSuperAdmin' => $record->is_superadmin,
            ));

            $this->errorCode=self::ERROR_NONE;
        }
        return !$this->errorCode;
    }

    public function getId()
    {
        return $this->_id;
    }
}