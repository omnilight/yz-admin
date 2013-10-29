<?php

/**
 * This is the model class for table "{{admin_users}}".
 *
 * The followings are the available columns in table '{{admin_users}}':
 * @property integer $id
 * @property string $name
 * @property string $login
 * @property string $passhash
 * @property string $salt
 * @property string $email
 * @property boolean $is_superadmin
 * @property boolean $is_enabled
 */
class AdminUsers extends YzBaseModel
{
    public $password;
    public $password_repeat;

    private $_assignedRoles = null;
    private $_assignedTasks = null;
    private $_assignedOperations = null;

    /**
     * @var YzAdminAuthManager
     */
    protected $_authManager = null;

    /**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AdminUsers the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{admin_users}}';
	}

    public function scopes()
    {
        return array(
            'enabled' => array(
                'condition' => 'is_enabled = 1',
            ),
        );
    }

    public function getAssignedRoles()
    {
        if(is_null($this->_assignedRoles))
            return ($this->_assignedRoles = array_keys($this->authItems(array(
                'select'=>'name',
                'index'=>'name',
                'scopes'=>array('roles'),
            ))));
        else
            return $this->_assignedRoles;
    }

    /**
     * @param $assignedRoles array
     */
    public function setAssignedRoles($assignedRoles)
    {
        if(empty($assignedRoles))
            $assignedRoles = array();
        if(!is_array($assignedRoles))
            $assignedRoles = explode(',',$assignedRoles);
        $this->_assignedRoles = $assignedRoles;
    }

    public function getAssignedTasks()
    {
        if(is_null($this->_assignedTasks))
            return ($this->_assignedTasks = array_keys($this->authItems(array(
                'select'=>'name',
                'index'=>'name',
                'scopes'=>array('tasks'),
            ))));
        else
            return $this->_assignedTasks;
    }

    /**
     * @param $assignedTasks array
     */
    public function setAssignedTasks($assignedTasks)
    {
        if(empty($assignedTasks))
            $assignedTasks = array();
        if(!is_array($assignedTasks))
            $assignedTasks = explode(',',$assignedTasks);
        $this->_assignedTasks = $assignedTasks;
    }

    public function getAssignedOperations()
    {
        if(is_null($this->_assignedOperations))
            return ($this->_assignedOperations = array_keys($this->authItems(array(
                'select'=>'name',
                'index'=>'name',
                'scopes'=>array('operations'),
            ))));
        else
            return $this->_assignedOperations;
    }

    /**
     * @param $assignedOperations array
     */
    public function setAssignedOperations($assignedOperations)
    {
        if(empty($assignedOperations))
            $assignedOperations = array();
        if(!is_array($assignedOperations))
            $assignedOperations = explode(',',$assignedOperations);
        $this->_authItemsOperations = $assignedOperations;
    }

    /**
     * @param \YzAdminAuthManager $authManager
     */
    public function setAuthManager($authManager)
    {
        $this->_authManager = $authManager;
    }

    /**
     * @return \YzAdminAuthManager
     */
    public function getAuthManager()
    {
        if($this->_authManager === null) {
            $this->_authManager = Yii::app()->authManager;
        }
        return $this->_authManager;
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, login, email', 'required', 'on'=>'insert,update,ownProfile'),
            array('login', 'unique', 'allowEmpty' => false),
            array('email', 'unique', 'allowEmpty' => false),
			array('is_enabled', 'boolean'),
			array('is_superadmin', 'boolean'),
			array('name, passhash', 'length', 'max'=>128),
			array('login, email', 'length', 'max'=>255),
            array('email','email'),
			array('salt', 'length', 'max'=>32),
            array('password, password_repeat', 'required', 'on'=>'changePassword,insert'),
            array('password, password_repeat', 'length', 'min'=>6),
            array('password_repeat', 'compare', 'compareAttribute'=>'password'),
            array('assignedRoles,assignedTasks,assignedOperations','safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, login, email, is_superadmin, is_enabled', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
        return array(
            'authItems'=>array(self::MANY_MANY, 'AdminAuthItem',
                $this->getAuthManager()->assignmentTable.'(userid, itemname)'),
        );
	}

    public function behaviors()
    {
        return array(
            'PassHash' => array(
                'class' => 'yz.behaviors.YzPasshashBehavior',
                'hashMethod' => array(__CLASS__, 'hashPassword'),
                'on' => 'insert,changePassword',
            ),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('AdminModule.t9n','ID'),
			'name' => Yii::t('AdminModule.t9n','Name'),
			'login' => Yii::t('AdminModule.t9n','Login'),
			'password' => Yii::t('AdminModule.t9n','Password'),
			'password_repeat' => Yii::t('AdminModule.t9n','Repeat password'),
			'passhash' => Yii::t('AdminModule.t9n','Passhash'),
			'salt' => Yii::t('AdminModule.t9n','Salt'),
			'email' => Yii::t('AdminModule.t9n','Email'),
			'is_superadmin' => Yii::t('AdminModule.t9n','Super Administrator'),
			'is_enabled' => Yii::t('AdminModule.t9n','Enabled'),
            'assignedRoles' => Yii::t('AdminModule.t9n','Roles'),
            'assignedTasks' => Yii::t('AdminModule.t9n','Allowed Tasks'),
            'assignedOperations' => Yii::t('AdminModule.t9n','Allowed Operations'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('login',$this->login,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('is_enabled',$this->is_enabled);
		$criteria->compare('is_superadmin',$this->is_superadmin);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * @return string
     */
    public function getUserToken()
    {
        $rand = sprintf('%08x%08x',mt_rand(),mt_rand());
        $id = $this->id;
        $hash = sha1($rand . $id . $this->passhash);
        $token = array(
            $id,$hash,$rand,
        );
        $token = base64_encode(CJSON::encode($token));
        return $token;
    }

    /**
     * @param string $token
     * @return bool
     */
    public function isUserTokenValid($token)
    {
        $token = CJSON::decode(base64_decode($token));
        if($token == null)
            return false;
        $hash = sha1($token[2] . $this->id . $this->passhash);
        if($hash === $token[1])
            return true;
        else
            return false;
    }

    /**
     * @param string $token
     * @return AdminUsers|null
     */
    public function findByToken($token)
    {
        $ptoken = CJSON::decode(base64_decode($token));
        if($ptoken == null)
            return null;
        $id = $ptoken[0];
        /** @var $user AdminUsers */
        $user = $this->findByPk($id);
        if($user === null)
            return null;
        if($user->isUserTokenValid($token))
            return $user;
        else
            return null;
    }

    public function loginToAdminPanel()
    {
        /** @var $user YzAdminWebUser */
        $user = Yii::app()->user;
        $identity = new  AdminUserAutoLoginIdentity($this);
        if($identity->authenticate()) {
            if($user->login($identity))
                return true;
        }
        return false;
    }

    protected function beforeValidate()
    {
        if($this->scenario == 'ownProfile')
            unset($this->is_superadmin, $this->is_enabled);

        return parent::beforeValidate();
    }

    protected function afterSave()
    {
        $assignments = array_merge($this->getAssignedRoles(), $this->getAssignedTasks(),
            $this->getAssignedOperations());

        $oldAssignments = array_keys($this->authItems(array(
            'select'=>'name',
            'index'=>'name',
        )));

        $removedAssignments = array_diff($oldAssignments, $assignments);
        $addedAssignments = array_diff($assignments, $oldAssignments);

        foreach($removedAssignments as $assignmentName)
            $this->getAuthManager()->revoke($assignmentName, $this->id);
        foreach($addedAssignments as $assignmentName)
            $this->getAuthManager()->assign($assignmentName, $this->id);

        if($this->isNewRecord)
            Yii::app()->getModule('admin')->onAdminUserInsert(new CEvent($this,array(
                'user' => $this,
            )));
        else
            Yii::app()->getModule('admin')->onAdminUserUpdate(new CEvent($this,array(
                'user' => $this,
            )));

        parent::afterSave();
    }

    public static function hashPassword($password, $salt)
    {
        return md5(md5($password) . $salt);
    }
}

class AdminUserAutoLoginIdentity extends CUserIdentity
{
    /** @var AdminUsers */
    private $_user;

    /**
     * @param AdminUsers $user
     */
    public function __construct($user)
    {
        $this->_user = $user;
    }


    public function authenticate()
    {
        if($this->_user->is_enabled===null )
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        else
        {
            $this->setPersistentStates(array(
                'name' => $this->_user->name,
                'login' => $this->_user->login,
                'isSuperAdmin' => $this->_user->is_superadmin,
            ));

            $this->errorCode=self::ERROR_NONE;
        }
        return !$this->errorCode;
    }

    public function getId()
    {
        return $this->_user->id;
    }
}