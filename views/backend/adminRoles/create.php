<?php
$this->breadcrumbs=array(
	Yii::t('AdminModule.t9n','Admin Roles')=>array('index'),
	Yii::t('AdminModule.t9n','Create'),
);

$this->actions=array(
	array(
        'label'=>Yii::t('AdminModule.t9n','List'),
        'icon'=>'arrow-left',
        'url'=>array('index'),
        'type'=>'warning',
    ),
);
?>

<div class="page-header subnav">
    <?php $this->widget('yzAdmin.widgets.AdminActionsWidget', array(
        'actions' => $this->actions,
    )); ?>

    <h3><?php echo Yii::t('AdminModule.t9n','Creating new record'); ?></h3>
</div>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>