<?php

Yii::import('zii.widgets.grid.CGridColumn');

/**
 * This class lets us to extend any GridView by adding
 * special column, witch will display custom links. This
 * links are configurable in admin module configuration
 * through {@link AdminModule::$extensions} component
 *
 * @property array $links
 */
class AdminExtendedColumn extends CGridColumn
{
    public $_links = null;

    public $_headerText = null;

    /**
     * Constructor.
     * @param CGridView $grid the grid view that owns this column.
     */
    public function __construct($grid)
    {
        parent::__construct($grid);

        /** @var $controller YzBackController */
        $controller = $this->grid->controller;
        $route = $controller->route;
        $this->links = Yii::app()->getModule('admin')->extensions->getGridLinksForRoute($route);
    }

    protected function renderHeaderCellContent()
    {
        echo CHtml::encode($this->getHeaderText());
    }

    /**
     * Renders the data cell content.
     * @param integer $row the row number (zero-based)
     * @param mixed $data the data associated with the row
     */
    protected function renderDataCellContent($row,$data)
    {
        if(is_null($this->links))
            return;

        $links = array();

        foreach($this->links as $text => $routeExpression)
        {
            $links[] = CHtml::link($text, $this->evaluateExpression($routeExpression, array('data'=>$data)));
        }

        echo '<p>'.implode('</p><p>', $links).'</p>';
    }

    public function setHeaderText($headerText)
    {
        $this->_headerText = $headerText;
    }

    public function getHeaderText()
    {
        if(empty($this->_headerText))
            return ($this->_headerText = Yii::t('AdminModule.t9n','Relations'));
        else
            return $this->_headerText;
    }

    public function setLinks($links)
    {
        if(is_null($links))
            $this->visible = false;
        else
            $this->visible = true;

        if(!is_array($links))
            $this->_links = array($links);
        else
            $this->_links = $links;
    }

    public function getLinks()
    {
        return $this->_links;
    }
}