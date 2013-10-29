<?php

/**
 * Represents basic class for all tabs
 *
 * @property array $tabConfig Returns this tab config used for {@see TbTabs}
 */
class AdminTabsTabWidget extends YzWidget
{
    /**
     * @var string
     */
    public $id = null;
    /**
     * @var AdminTabsWidget
     */
    public $tabs = null;
    /**
     * @var string
     */
    public $content;
    /**
     * @var string
     */
    public $label;
    /**
     * @var boolean If this property is false for all tabs added to tabs widget
     * then fist tab will be active by default
     */
    public $isActive = false;

    /**
     * @throws CException
     */
    public function init()
    {
        if($this->tabs === null)
            throw new CException('You must set $tabs property');

        ob_start();
    }

    public function run()
    {
        $this->content = ob_get_clean();
        if($this->id === null) {
            $this->id = $this->tabs->getId() . '_tab'.count($this->tabs->adminTabs);
        }
        $this->tabs->addTab($this);
    }

    /**
     * @return array
     */
    public function getTabConfig()
    {
        return array(
            'id' => $this->id,
            'label' => $this->label,
            'content' => $this->content,
            'active' => $this->isActive,
        );
    }
}