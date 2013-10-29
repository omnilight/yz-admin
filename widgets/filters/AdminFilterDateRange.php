<?php

Yii::import('yzAdmin.widgets.filters.AdminFilterCommon');

class AdminFilterDateRange extends AdminFilterCommon
{
    public $startDateAttribute;
    public $endDateAttribute;

    public $template = 'Show records from {start} to {end}';

    public function getType()
    {
        return 'dateRange';
    }

    public function run()
    {
        $start = $this->widget('zii.widgets.jui.CJuiDatePicker',array(
            'language' => Yii::app()->language,
            'model' => $this->filtersWidget->model,
            'attribute' => $this->startDateAttribute,
            'htmlOptions' => array(
                'class' => 'input-small',
            ),
            'options' => array(
                'dateFormat' => 'dd.mm.yy',
            ),
        ), true);

        $end = $this->widget('zii.widgets.jui.CJuiDatePicker',array(
            'language' => Yii::app()->language,
            'model' => $this->filtersWidget->model,
            'attribute' => $this->endDateAttribute,
            'htmlOptions' => array(
                'class' => 'input-small',
            ),
            'options' => array(
                'dateFormat' => 'dd.mm.yy',
            ),
        ), true);

        $this->registerJs();

        echo Yii::t('AdminModule.t9n',$this->template,array(
            '{start}' => $start,
            '{end}' => $end,
        ));
    }

    protected function registerJs()
    {
        $startId = YzHtml::resolveIdSafe($this->filtersWidget->model,$this->startDateAttribute);
        $endId = YzHtml::resolveIdSafe($this->filtersWidget->model,$this->endDateAttribute);
        $js =<<<JS
(function(){
    $.fn.yzAdminFilters.filters[$('#{$startId}').attr('name')] =
        function(){
            var object = {};
            object[$('#{$startId}').attr('name')] = $('#{$startId}').val();
            return object;
        };
    $.fn.yzAdminFilters.filters[$('#{$endId}').attr('name')] =
        function(){
            var object = {};
            object[$('#{$endId}').attr('name')] = $('#{$endId}').val();
            return object;
        };
})();
JS;
        $this->getClientScript()->registerScript($this->id.'-script',$js);
    }
}