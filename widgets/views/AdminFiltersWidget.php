<?php
/**
 * @var AdminFiltersWidget $this
 * @var CModel $model
 * @var array $filters
 */
/** @var $widgets AdminFilterCommon[] */
$widgets = array();
?>
<div class="row admin-filters-widget">
    <div class="span8 offset1">
        <?php /** @var $form TbActiveForm */
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm',array(
            'id' => 'admin-filters-widget-form-id',
            'action' => Yii::app()->createUrl($this->controller->route),
            'method' => 'get',
            'type' => 'horizontal',
            'htmlOptions' => array('class' => 'admin-filters-widget-form'),
        )); ?>

        <?php $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'submit',
            //'type'=>'primary',
            'label'=>Yii::t('AdminModule.t9n','Apply'),
            'htmlOptions' => array(
                'class' => 'pull-right',
            ),
        )); ?>

        <?php foreach($filters as $filter ): ?>
        <div class="row">
            <?php $widgets[] = $this->widget($filter[0], array_merge($filter[1], array('form'=>$form))); ?>
        </div>
        <?php endforeach; ?>

        <?php $this->endWidget(); ?>
    </div>
</div>

<?php

$gridId = CJavaScript::encode($this->gridId);

$js =<<<JS
$.fn.yzAdminFilters.settings.gridId = {$gridId};
JS;

/** @var $cs CClientScript */
$cs = Yii::app()->clientScript;
$cs->registerScript('admin-filters-widget',$js);
$cs->registerScriptFile($this->getAssetsUrl().'/js/filters.js');
?>