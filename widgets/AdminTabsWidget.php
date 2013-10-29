<?php

Yii::import('bootstrap.widgets.TbTabs');

/**
 * @property AdminTabsTabWidget[] $adminTabs
 */
class AdminTabsWidget extends TbTabs
{
    /**
     * @var AdminTabsTabWidget[]
     */
    protected $_adminTabs = array();

    /**
     * @var AdminTabsTabWidget Current tab
     */
    protected $_currentTab = null;

    public function init()
    {
        ob_start();
    }

    public function run()
    {
        if($this->_currentTab !== null)
            $this->endTab();

        ob_end_clean();

        $foundActiveTab = false;

        foreach($this->_adminTabs as $tab) {
            if($foundActiveTab && $tab->isActive)
                $tab->isActive = false; // Only one can be active at once
            if($tab->isActive)
                $foundActiveTab = true;
            $this->tabs[] = $tab->getTabConfig();
        }

        if($foundActiveTab == false && count($this->tabs) > 0)
            $this->tabs[0]['active'] = true;

        parent::init();
        parent::run();
    }

    /**
     * Begins new tab via creating {@see AdminTabsTabWidget}
     * @param string $label Label of the tab
     * @param string $id Id of the tab
     * @param bool $isActive Is current tab active. If all tabs are not active
     * than first added tab will be active
     * @return AdminTabsTabWidget
     */
    public function beginTab($label, $id = null, $isActive = false)
    {
        if($this->_currentTab !== null)
            $this->endTab();

        if($id === null)
            $id = $this->getId() . '_tab'.count($this->_adminTabs);

        $this->_currentTab = $this->beginWidget('yzAdmin.widgets.AdminTabsTabWidget',array(
            'tabs' => $this,
            'id' => $id,
            'label' => $label,
            'isActive' => $isActive,
        ));

        return $this->_currentTab;
    }

    /**
     * Ends current tab. This method is used with {@see beginTab}
     * @throws CException
     */
    public function endTab()
    {
        if($this->_currentTab === null)
            throw new CException('You must call beginTab method before endTab');

        $this->endWidget();
        $this->addTab($this->_currentTab);
        $this->_currentTab = null;
    }

    /**
     * @param $tab AdminTabsTabWidget
     * @throws CException
     * @return void
     */
    public function addTab($tab)
    {
        if(!($tab instanceof AdminTabsTabWidget))
            throw new CException('Tab must be an instance of AdminTabsTabWidget');
        $this->_adminTabs[$tab->id] = $tab;
    }

    public function deleteTab($id)
    {
        unset($this->_adminTabs[$id]);
    }

    /**
     * @return AdminTabsTabWidget[]
     */
    public function getAdminTabs()
    {
        return $this->_adminTabs;
    }        
}