<?php

class AdminMenuWidget extends CWidget
{
    public $cache = 'cache';

    public function run()
    {
        $navigation = $this->getNavigation();

        $this->render('AdminMenuWidget',array(
            'navigationItems' => $navigation,
        ));
    }

    protected function getNavigation()
    {
        /** @var $user YzAdminWebUser */
        $user = Yii::app()->getUser();

        $cacheId = $this->getCacheId() . '_user_'.$user->id;
        if(!is_null($this->cache) && ($navigationItems = Yii::app()->{$this->cache}->get($cacheId))!==false)
            return $navigationItems;

        /** @var $modules array */
        $modules = Yz::get()->getModules();

        $navigationItems = array();

        foreach( $modules as $module ){
            /** @var $module YzWebModule */

            if(!$module->isShowInAdminPanel)
                continue;

            $moduleNavigation = $module->adminNavigation;
            $moduleOrder = $module->adminMenuOrder;

            if(empty($moduleNavigation))
                continue;

            foreach( $moduleNavigation as $header ){
                $tmp = array();
                if(!empty($header['items']))
                    foreach( $header['items'] as $item ) {
                        if(!empty($item['route']) && !$this->checkAccessByRoute($item['route']))
                            continue;
                        if(!empty($item['route'])) {
                            $item['url'] = CHtml::normalizeUrl($item['route']);
                            unset($item['route']);
                        }
                        $tmp[] = $item;
                    }
                if(!empty($header['icon']))
                    $header['icon'] .= ' large';
                if(!empty($tmp)) {
                    unset($header['items']);
                    $navigationItems[$moduleOrder][] = $header;
                    $navigationItems[$moduleOrder] = array_merge(
                        $navigationItems[$moduleOrder],
                        $tmp,
                        array('---')
                    );
                }
            }
        }

        ksort($navigationItems);

        if(!empty($navigationItems))
            $navigationItems = call_user_func_array('array_merge', $navigationItems);

        if(!is_null($this->cache))
            Yii::app()->{$this->cache}->set($cacheId, $navigationItems);
        return $navigationItems;
    }

    protected function checkAccessByRoute($route)
    {
        static $_routesCache = array();

        if(is_array($route))
            $route = $route[0];

        if(isset($_routesCache[$route]))
            return $_routesCache[$route];

        /** @var $user YzAdminWebUser */
        $user = Yii::app()->getUser();

        if(($ca=Yii::app()->createController($route))===null) {
            return true;
        }
        /** @var $controller CController */
        /** @var $action string */
        list($controller, $actionID) = $ca;

        $authItem = '';

        if(($module=$controller->getModule())!==null){
            $authItem = ucfirst($module->id) . '.';
            if($user->checkAccess($authItem.'*'))
                return ($_routesCache[$route] = true);
        }

        $authItem .= ucfirst($controller->id) . '.';

        if($user->checkAccess($authItem.'*'))
            return ($_routesCache[$route] = true);

        if($user->checkAccess($authItem.ucfirst($actionID)))
            return ($_routesCache[$route] = true);

        return ($_routesCache[$route] = false);
    }

    public function getCacheId()
    {
        return __CLASS__ . '_widget';
    }
}