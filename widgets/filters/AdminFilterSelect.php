<?php

Yii::import('yzAdmin.widgets.filters.AdminFilterCommon');

/**
 * Class AdminFilterSelect
 */
class AdminFilterSelect extends AdminFilterCommon
{
    public $data = array();

    public function getType()
    {
        return 'select';
    }

    public function run()
    {
        $model = $this->filtersWidget->model;

        $this->registerJs();

        echo $this->form->dropDownListRow($model, $this->attribute,
            array_merge(array('' => Yii::t('AdminModule.t9n', 'All')),$this->data));
    }

    protected function registerJs()
    {
        $id = YzHtml::resolveIdSafe($this->filtersWidget->model,$this->attribute);
        $js =<<<JS
(function(){
    $.fn.yzAdminFilters.filters[$('#{$id}').attr('name')] =
        function(){
            var object = {};
            if($('#{$id}').val() != '')
                object[$('#{$id}').attr('name')] = $('#{$id}').val();
            return object;
        };
})();
JS;
        $this->getClientScript()->registerScript($this->id.'-script',$js);
    }
}