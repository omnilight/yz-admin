<?php

Yii::import('bootstrap.widgets.TbDataColumn');

class AdminDataColumn extends TbDataColumn
{
    /**
     * Sub header cell content
     * @var string
     */
    public $subHeader = '';
    public $linkExpression = null;
    /**
     * @var array
     */
    public $actionButton = null;

    public function renderSubHeaderCell()
    {
        echo "<td>";
        $this->renderSubHeaderCellContent();
        echo '</td>';
    }

    protected function renderSubHeaderCellContent()
    {
        echo $this->subHeader;
    }

    public function hasSubHeader()
    {
        return !empty($this->subHeader);
    }

    protected function renderDataCellContent($row, $data)
    {
        if($this->linkExpression !== null) {
            ob_start();
            parent::renderDataCellContent($row, $data);
            $value = ob_get_clean();
            $url = $this->evaluateExpression($this->linkExpression,array('data'=>$data,'row'=>$row));
            if($url !== null)
                echo CHtml::link($value, $url);
            else
                echo $value;
        } else
            parent::renderDataCellContent($row,$data);
    }


}