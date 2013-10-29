<?php

/**
 * This is the model class for table "{{admin_authitem}}".
 *
 * The followings are the available columns in table '{{admin_authitem}}':
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $bizrule
 * @property string $data
 * @property array $childrenRoles
 * @property array $childrenTasksOperations
 * @property array $children
 */
class AdminAuthItem extends YzBaseModel
{
	public $allowAutoName = true;

    /**
	 * @var YzAdminAuthManager
	 */
    protected $_authManager = null;

    private $_childrenRoles = null;
    private $_childrenTasks = null;
    private $_childrenOperations = null;

    /**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AdminAuthItem the static model class
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

        return $this->getAuthManager()->itemTable;
	}

    public function getChildrenRoles()
    {
        if(is_null($this->_childrenRoles))
            return ($this->_childrenRoles = array_keys($this->children(array(
                'select'=>'name',
                'index'=>'name',
                'scopes'=>array('roles'),
            ))));
        else
            return $this->_childrenRoles;
    }

    /**
     * @param $childrenRoles array
     */
    public function setChildrenRoles($childrenRoles)
    {
        if(empty($childrenRoles))
            $childrenRoles = array();
        if(!is_array($childrenRoles))
            $childrenRoles = explode(',',$childrenRoles);
        $this->_childrenRoles = $childrenRoles;
    }

    public function getChildrenTasks()
    {
        if(is_null($this->_childrenTasks))
            return ($this->_childrenTasks = array_keys($this->children(array(
                'select'=>'name',
                'index'=>'name',
                'scopes'=>array('tasks'),
            ))));
        else
            return $this->_childrenTasks;
    }

    /**
     * @param $childrenTasks array
     */
    public function setChildrenTasks($childrenTasks)
    {
        if(empty($childrenTasks))
            $childrenTasks = array();
        if(!is_array($childrenTasks))
            $childrenTasks = explode(',',$childrenTasks);
        $this->_childrenTasks = $childrenTasks;
    }

    public function getChildrenOperations()
    {
        if(is_null($this->_childrenOperations))
            return ($this->_childrenOperations = array_keys($this->children(array(
                'select'=>'name',
                'index'=>'name',
                'scopes'=>array('operations'),
            ))));
        else
            return $this->_childrenOperations;
    }

    /**
     * @param $childrenOperations array
     */
    public function setChildrenOperations($childrenOperations)
    {
        if(empty($childrenOperations))
            $childrenOperations = array();
        if(!is_array($childrenOperations))
            $childrenOperations = explode(',',$childrenOperations);
        $this->_childrenOperations = $childrenOperations;
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

    public function scopes()
    {
        return array(
            'roles' => array(
                'condition' => $this->getTableAlias().'.type = '.CAuthItem::TYPE_ROLE,
            ),
            'operations' => array(
                'condition' => $this->getTableAlias().'.type = '.CAuthItem::TYPE_OPERATION,
            ),
            'tasks' => array(
                'condition' => $this->getTableAlias().'.type = '.CAuthItem::TYPE_TASK,
            ),
        );
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		$rules = array(
			array('type, description', 'required'),
            array('type', 'in', 'range' => $this->attributeValues('type', true)),
			array('name', 'length', 'max'=>64),
            array('name, description', 'unique'),
            array('name', 'myNameValidator'),
			array('description', 'length', 'max'=>255),
            array('childrenRoles,childrenTasks,childrenOperations','safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('name, type, description, bizrule, data', 'safe', 'on'=>'search'),
		);

        if($this->allowAutoName == false)
            $rules[] = array('name', 'required');

        return $rules;
	}

    public function myNameValidator()
    {
        $name_reg = '/^[a-zA-Z0-9_]+/';
        if(!preg_match($name_reg, $this->name))
            $this->addError('name',Yii::t('AdminModule.t9n','{attribute} must contains only a-zA-Z0-9_ characters',array(
                '{attribute}' => 'name',
            )));
    }

    public function attributesValues()
    {
        return array(
            'type' => array(
                CAuthItem::TYPE_OPERATION => Yii::t('AdminModule.t9n','Operation'),
                CAuthItem::TYPE_ROLE => Yii::t('AdminModule.t9n','Role'),
                CAuthItem::TYPE_TASK => Yii::t('AdminModule.t9n','Task'),
            )
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
            'children'=>array(self::MANY_MANY, 'AdminAuthItem',
                $this->getAuthManager()->itemChildTable.'(parent, child)'),
		);
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'name' => Yii::t('AdminModule.t9n','Name'),
			'type' => Yii::t('AdminModule.t9n','Type'),
			'description' => Yii::t('AdminModule.t9n','Description'),
			'bizrule' => Yii::t('AdminModule.t9n','Bizness rule'),
			'data' => Yii::t('AdminModule.t9n','Data'),
            'childrenRoles' => Yii::t('AdminModule.t9n','Children Roles'),
            'childrenTasks' => Yii::t('AdminModule.t9n','Allowed Tasks'),
            'childrenOperations' => Yii::t('AdminModule.t9n','Allowed Operations'),
		);
	}

    /**
     * @return CAuthItem
     */
    public function getAuthItem()
    {
        return $this->_authManager->getAuthItem($this->name);
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

		$criteria->compare('t.name',$this->name,true);
		$criteria->compare('t.type',$this->type);
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.bizrule',$this->bizrule,true);
		$criteria->compare('t.data',$this->data,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    protected function beforeValidate()
    {
        if($this->allowAutoName && $this->name == '') {
            $names = array(
                CAuthItem::TYPE_ROLE => 'role_',
                CAuthItem::TYPE_OPERATION => 'operation_',
                CAuthItem::TYPE_TASK => 'task_',
            );
            $total = AdminAuthItem::model()->countByAttributes(array(
                'type' => $this->type,
            ));
            $this->name = $names[$this->type] . ($total+1);
        }

        return parent::beforeValidate();
    }

    protected function afterSave()
    {
        $children = array_merge($this->getChildrenRoles(), $this->getChildrenTasks(),
            $this->getChildrenOperations());

        $oldChildren = array_keys($this->children(array(
            'select'=>'name',
            'index'=>'name',
        )));

        $removedChildren = array_diff($oldChildren, $children);
        $addedChildren = array_diff($children, $oldChildren);

        foreach($removedChildren as $childName)
            $this->_authManager->removeItemChild($this->name, $childName);
        foreach($addedChildren as $childName)
            $this->_authManager->addItemChild($this->name, $childName);

        parent::afterSave();
    }
}