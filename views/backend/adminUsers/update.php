<?php
$this->breadcrumbs=array(
	'Admin Users'=>array('index'),
	Yii::t('AdminModule.t9n','Update'),
);

$this->actions=array(
	array('label'=>Yii::t('AdminModule.t9n','List'),'icon'=>'arrow-left','url'=>array('index'),'type'=>'warning'),
	array('label'=>Yii::t('AdminModule.t9n','Add'),'url'=>array('create')),
);
?>

<div class="page-header subnav">
    <?php $this->widget('yzAdmin.widgets.AdminActionsWidget', array(
    'actions' => $this->actions,
    )); ?>

    <h3><?php echo Yii::t('AdminModule.t9n','Updating record'); ?></h3>
</div>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>