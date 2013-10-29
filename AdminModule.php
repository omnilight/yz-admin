<?php

/**
 * Main module for Yz Engine backend.
 *
 * @property YzAdminExtensions $extensions Lets us configure extensions component
 * @version 0.2a
 */
class AdminModule extends YzWebModule
{
    const DEFAULT_PHPEXCEL_PATH = 'application.vendors.PHPExcel.PHPExcel';

    public $initBootstrap = true;
    /**
     * Whether or not to enable database administration tool.
     *
     * Note, that this tool is not shown in admin menu, but you can access it by
     * http://yoursite.com/backend/dbAdmin url
     * @var bool
     */
    public $enableDbAdmin = true;

    public $adminMenuOrder = 9999;

    protected $_urlRules = array(
        'prepend' => array(
            'backend' => 'admin/backend/default/index',
            'backend/login' => 'admin/backend/default/login',
            'backend/logout' => 'admin/backend/default/logout',
            'backend/dbAdmin' => 'admin/backend/dbAdmin/index',
            'backend/<_m:\w+>' => '<_m>',
            'backend/<_m:\w+>/<_c:\w+>' => '<_m>/backend/<_c>',
            'backend/<_m:\w+>/<_c:\w+>/<_a:\w+>' => '<_m>/backend/<_c>/<_a>',
        ),
    );

    public function getParamsLabels()
    {
        return array(
            'adminMenuOrder' => Yii::t('Yz.t9n','Menu order'),
        );
    }

    public function  getVersion()
    {
        return '0.2a';
    }

    public function getEditableParams()
    {
        return array(
            'adminMenuOrder' => 'integer',
        );
    }

    public function getName()
    {
        return Yii::t('AdminModule.t9n', 'Admin panel');
    }

    public function getDescription()
    {
        return Yii::t('AdminModule.t9n', 'Admin panel module');
    }

    public function getAuthor()
    {
        return Yii::t('AdminModule.t9n', 'Yz Core Team');
    }

    public function getAuthorEmail()
    {
        return '';
    }

    public function getUrl()
    {
        return '';
    }

    public function getIcon()
    {
        return 'gears';
    }

    public function getAdminNavigation()
    {
        $nav = array(
            array(
                'label' => Yii::t('AdminModule.t9n','Administrators'),
                'icon' => 'user',
                'items' => array(
                    array(
                        'label' => Yii::t('AdminModule.t9n','List'),
                        'route' => array('/admin/backend/adminUsers/index'),
                        'icon' => 'list'
                    ),
                    array(
                        'label' => Yii::t('AdminModule.t9n','Roles'),
                        'route' => array('/admin/backend/adminRoles/index'),
                        'icon' => 'list'
                    )
                ),
            ),
        );
        $nav[] = array(
            'label' => Yii::t('AdminModule.t9n','Settings'),
            'icon' => 'cogs',
            'items' => array(
                array(
                    'label' => Yii::t('AdminModule.t9n','Modules'),
                    'route' => array('/admin/backend/moduleSettings/index'),
                    'icon' => 'list'
                ),
                array(
                    'label' => Yii::t('AdminModule.t9n','General'),
                    'route' => array('/admin/backend/default/generalSettings'),
                    'icon' => 'list'
                ),
                array(
                    'label' => Yii::t('AdminModule.t9n','Information'),
                    'route' => array('/admin/backend/default/information'),
                    'icon' => 'list'
                ),
            ),
        );

        return $nav;
    }

    public function getAuthItems()
    {
        return array(
            'AdminUsers.ManageSuperAdminProfile' => array(
                'auth' => new CAuthItem(
                    Yii::app()->authManager,
                    'AdminUsers.ManageSuperAdminProfile',
                    CAuthItem::TYPE_OPERATION,
                    Yii::t('AdminModule.t9n','Manage super administrator profiles')
                ),
                'children' => array(),
            ),
            'BaseAccess' => array(
                'auth' => new CAuthItem(
                    Yii::app()->authManager,
                    'BaseAccess',
                    CAuthItem::TYPE_ROLE,
                    Yii::t('AdminModule.t9n','Base access')
                ),
                'children' => array(
                    'Admin.Backend/Default.Index',
                    'Admin.Backend/Default.Login',
                    'Admin.Backend/Default.Logout',
                    'Admin.Backend/AdminUsers.OwnProfile'
                ),
            ),
        );
    }

    public function beforeControllerAction($controller, $action)
    {
        if(parent::beforeControllerAction($controller, $action))
        {
            // this method is called before any module controller action is performed
            // you may place customized code here
            return true;
        }
        else
            return false;
    }

    public function init()
    {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application

        // import the module-level models and components

        parent::init();

        if(self::$safeInit)
            return;

        $this->setAliases(array(
            'yzAdmin' => realpath(dirname(__FILE__)),
        ));

        $this->setImport(array(
            'yzAdmin.models.*',
            'yzAdmin.components.*',
            'yzAdmin.components.widgets.*',
        ));

        if(!$this->hasComponent('extensions')) {
            $this->setComponent('extensions',Yii::createComponent('YzAdminExtensions'));
        }
    }

    /**
     * @property CEvent $event
     */
    public function onAdminUserLogin($event)
    {
        $this->raiseEvent('onAdminUserLogin', $event);
    }

    /**
     * @property CEvent $event
     */
    public function onAdminUserInsert($event)
    {
        $this->raiseEvent('onAdminUserInsert', $event);
    }

    /**
     * @property CEvent $event
     */
    public function onAdminUserUpdate($event)
    {
        $this->raiseEvent('onAdminUserUpdate', $event);
    }

    /**
     * @property CEvent $event
     */
    public function onAdminUserDelete($event)
    {
        $this->raiseEvent('onAdminUserDelete', $event);
    }
}
