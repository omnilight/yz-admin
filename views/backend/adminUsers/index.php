<?php
$this->breadcrumbs=array(
	'Admin Users'
);

$this->actions=array(
	array('label'=>Yii::t('AdminModule.t9n','Add'),'url'=>array('create')),
    array('label'=>Yii::t('AdminModule.t9n','Delete selected...'), 'icon' => 'trash',
        'yzType'=>'deleteChecked', 'yzGridViewId' => 'admin-users-grid',
        'yzColumnId'=>'admin-users-grid-rows-checkboxes',
        'type'=>'danger'),
);
?>

<div class="page-header subnav">

    <?php $this->widget('yzAdmin.widgets.AdminActionsWidget', array(
        'actions' => $this->actions,
    )); ?>

    <h3><?php echo Yii::t('AdminModule.t9n','Admin Users'); ?></h3>
</div>


<?php $this->widget('yzAdmin.widgets.AdminGridView',array(
	'id'=>'admin-users-grid',
    'type'=>'striped bordered condensed',
	'dataProvider'=>$model->search(),
    	'columns'=>array(
            array(
                'class'=>'CCheckBoxColumn',
                'id'=>'admin-users-grid-rows-checkboxes',
                'selectableRows'=>2
            ),
			'id',
			'name',
			'login',
			//'passhash',
			//'salt',
			'email',
			'is_superadmin:boolean',
			'is_enabled:boolean',
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
