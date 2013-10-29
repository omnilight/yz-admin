<?php

Yii::import('yzAdmin.widgets.filters.AdminFilterCommon');

class AdminFilterText extends AdminFilterCommon
{
    public function getType()
    {
        return 'text';
    }

    public function run()
    {
        $model = $this->filtersWidget->model;

        $this->registerJs();

        echo $this->form->textFieldRow($model, $this->attribute);
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