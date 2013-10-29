<?php

class DefaultController extends YzBackController
{
    public function accessRules()
    {
        return CMap::mergeArray(array(
            array('allow',
                'actions' => array('login'),
            )
        ), parent::accessRules());
    }

    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionLogin()
	{
        $model=new AdminLoginForm();
        if(isset($_POST['AdminLoginForm']))
        {
            // collects user input data
            $model->attributes=$_POST['AdminLoginForm'];
            // validates user input and redirect to previous page if validated
            if($model->validate()) {
                Yii::app()->user->login($model->getIdentity());
                $this->redirect(Yii::app()->user->returnUrl);
            }
        }
        // displays the login form
        $this->renderPartial('login',array('model'=>$model),false,true);
	}

    public function actionLogout()
    {
        Yii::app()->user->logout();

        $this->redirect(array('/admin/backend/default/login'));
    }

    public function actionGeneralSettings()
    {
        $settings = array();

        // Cache
        if(Yii::app()->hasComponent('cache'))
            $settings['useCache'] = true;
        else
            $settings['useCache'] = false;
        if( $settings['useCache'] )
            switch(get_class(Yz::cache())){
                case 'CDummyCache': $settings['cache']['type'] = 'Dummy'; break;
                case 'CMemCache':
                    $settings['cache']['type'] = 'Memcache';
                    /** @var $memcached Memcached */
                    $memcached = Yz::cache()->getMemCache();
                    $stats = $memcached->getStats();
                    $settings['cache']['items'] = isset($stats['curr_items'])?$stats['curr_items']: 'unknown';
                    $settings['cache']['bytes'] = isset($stats['bytes'])?$stats['bytes']:'unknown';
                    break;
                case 'CDbCache': $settings['cache']['type'] = 'Database'; break;
                case 'CFileCache': $settings['cache']['type'] = 'Files'; break;
                default: $settings['cache']['type'] = 'Unknown'; break;
            }

        if( Yii::app()->request->getParam('action') !== null ) {
            switch(Yii::app()->request->getParam('action')) {
                case 'cacheReset':
                    if($settings['useCache'])
                        Yz::clearCache();
                    Yii::app()->user->setFlash('success',Yii::t('AdminModule.t9n',
                        'Cache was successfully flushed'));
                    break;
                default:
                    Yii::app()->user->setFlash('danger',Yii::t('AdminModule.t9n',
                        'Unknown action'));
                    break;

            }
            $this->redirect(array('generalSettings'));
        }

        $this->render('generalSettings',array(
            'settings'=>$settings,
        ));
    }

    public function actionInformation()
    {
        $this->render('information',array(

        ));
    }
}