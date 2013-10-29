<?php
/**
 * @var $this ModuleSettingsController
 * @var $model YzModuleSettings
 */
$this->breadcrumbs=array(
	Yii::t('AdminModule.t9n','Modules Settings')
);

/*$this->actions=array(
	array('label'=>Yii::t('AdminModule.t9n','Add'),'url'=>array('create')),
    array('label'=>Yii::t('AdminModule.t9n','Delete selected...'), 'icon' => 'trash',
        'yzType'=>'deleteChecked', 'yzGridViewId' => 'yz-module-settings-grid',
        'yzColumnId'=>'yz-module-settings-grid-rows-checkboxes',
        'type'=>'danger'),
);*/
?>

<div class="page-header subnav">

    <?php $this->widget('yzAdmin.widgets.AdminActionsWidget', array(
        'actions' => $this->actions,
    )); ?>

    <h3><?php echo Yii::t('YzModuleSettings','Yz Module Settings'); ?></h3>
</div>


<?php $this->widget('yzAdmin.widgets.AdminGridView',array(
	'id'=>'yz-module-settings-grid',
    'type'=>'striped bordered condensed',
    'enableExport' => false,
	'dataProvider'=>$model->search(),
    	'columns'=>array(
			//'id',
            array(
                'header' => '',
                'type' => 'html',
                'value' => '\'<i class="icon-\'.$data->getModule()->getIcon().\'"></i>\'',
            ),
            'module_id',
            array(
                'header' => Yii::t('AdminModule.t9n','Module name'),
                'type' => 'html',
                'value' => '$data->getModule()->getName()',
            ),
            array(
                'header' => Yii::t('AdminModule.t9n','Description'),
                'value' => '$data->getModule()->getDescription()',
            ),
            array(
                'header' => Yii::t('AdminModule.t9n','Author'),
                'value' => '$data->getModule()->getAuthor()',
            ),
            array(
                'header' => Yii::t('AdminModule.t9n','Version'),
                'value' => '$data->getModule()->getVersion()',
            ),
			//'parameters',
			//'create_date',
			//'update_date',
            array(
                'class'=>'yzAdmin.widgets.AdminExtendedColumn',
                //'links'=>array(),
            ),
            array(
                'class'=>'bootstrap.widgets.TbButtonColumn',
                'template'=>'{update}',
            ),
	),
)); ?>
