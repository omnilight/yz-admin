<?php

/**
 * This component stores configuration for {@link AdminExtendedColumn}
 *
 * @property array $gridLinks
 * @property array $formWidgets
 */
class YzAdminExtensions extends CApplicationComponent
{
    public $_gridLinks = array();
    public $_formWidgets = array();

    public function setGridLinks($gridLinks)
    {
        $this->_gridLinks = $gridLinks;
    }

    public function getGridLinks()
    {
        return $this->_gridLinks;
    }

    public function setFormWidgets($formWidgets)
    {
        $this->_formWidgets = $formWidgets;
    }

    public function getFormWidgets()
    {
        return $this->_formWidgets;
    }

    public function getGridLinksForRoute($route)
    {
        return isset($this->_gridLinks[$route])?$this->_gridLinks[$route]:null;
    }

    public function getFormWidgetsForRoute($route)
    {
        return isset($this->_formWidgets[$route])?$this->_formWidgets[$route]:null;
    }
}