<?php
$this->breadcrumbs=array(
	'Admin Auth Items'
);

$this->actions=CMap::mergeArray(array(
	array('label'=>Yii::t('AdminModule.t9n','Add'),'url'=>array('create')),
    array('label'=>Yii::t('AdminModule.t9n','Delete selected...'), 'icon' => 'trash',
        'yzType'=>'deleteChecked', 'yzGridViewId' => 'admin-auth-item-grid',
        'yzColumnId'=>'admin-auth-item-grid-rows-checkboxes',
        'type'=>'danger'),
    ),
    Yz::isDeveloperMode(false)?
        array(
            array('label'=>Yii::t('AdminModule.t9n','Discover operations'),
                'type'=>'warning','items'=>array(
                    array('label'=>Yii::t('AdminModule.t9n','Previously delete existed operations...'),
                        'url'=>array('discoverAuthItems','method'=>'deleteExisted')),
                    array('label'=>Yii::t('AdminModule.t9n','Add new operations...'),
                        'url'=>array('discoverAuthItems','method'=>'addNew')),
                ),
            ),
        ):
        array()
);
?>

<div class="page-header subnav">

    <?php $this->widget('yzAdmin.widgets.AdminActionsWidget', array(
        'actions' => $this->actions,
    )); ?>

    <h3><?php echo Yii::t('AdminAuthItem','Admin Roles'); ?></h3>
</div>


<?php $this->widget('yzAdmin.widgets.AdminGridView',array(
	'id'=>'admin-auth-item-grid',
    'type'=>'striped bordered condensed',
	'dataProvider'=>$model->search(),
    	'columns'=>array(
            array(
                'class'=>'CCheckBoxColumn',
                'id'=>'admin-auth-item-grid-rows-checkboxes',
                'selectableRows'=>2
            ),
            'description',
			'name',
			//'type',
			//'bizrule',
			//'data',
            array(
                'class'=>'yzAdmin.widgets.AdminExtendedColumn',
                //'links'=>array(),
            ),
            array(
                'class'=>'yzAdmin.widgets.AdminButtonColumn',
                'template'=>'{update} {delete}',
            ),
	),
)); ?>
