<?php

/**
 * @property-read string $type
 */
abstract class AdminFilterCommon extends CWidget
{
    /** @var AdminFiltersWidget */
    public $filtersWidget = null;
    /** @var string */
    public $label = '';
    /** @var string */
    public $attribute = '';
    /** @var TbActiveForm */
    public $form;

    public function init()
    {
        if($this->label == '' && $this->attribute != '')
            $this->label = YzHtml::resolveNameSafe($this->filtersWidget->model,$this->attribute);
    }

    public abstract function getType();

    /**
     * @return CClientScript
     */
    protected function getClientScript()
    {
        return Yii::app()->clientScript;
    }
}