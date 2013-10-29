<?php
/** @var $this YzBackController */
/** @var $model AdminUsers */
/** @var $modelPassword AdminUsers */
?>
<?php

$this->breadcrumbs=array(
    Yii::t('AdminModule.t9n','Your profile', array(
        '{username}' => $model->name,
    )),
);
?>

<div class="page-header subnav">
    <?php $this->widget('yzAdmin.widgets.AdminActionsWidget', array(
    'actions' => $this->actions,
)); ?>

    <h3><?php echo Yii::t('AdminModule.t9n','Your profile', array(
        '{username}' => $model->name,
    )); ?></h3>
</div>

<?php /** @var $form AdminActiveFormWidget */
$form=$this->beginWidget('yzAdmin.widgets.AdminActiveFormWidget',array(
    'id'=>'admin-users-form',
    'enableAjaxValidation'=>true,
    'type'=>'horizontal',
    'htmlOptions' => array('class' => 'well'),
)); ?>

<?php echo $form->errorSummary($model); ?>

<p class="help-block"><?php echo Yii::t('AdminModule.t9n','Fields with {star} are required.',array(
    '{star}'=>'<span class="required">*</span>'
)); ?></p>

<?php echo $form->textFieldRow($model,'name',array('class'=>'span5')); ?>

<?php echo $form->textFieldRow($model,'login',array('class'=>'span5')); ?>

<?php echo $form->textFieldRow($model,'email',array('class'=>'span5')); ?>

<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
    'buttonType'=>'submit',
    'type'=>'primary',
    'label'=>Yii::t('AdminModule.t9n','Save'),
)); ?>
</div>

<?php $this->endWidget(); ?>

<?php /** @var $form AdminActiveFormWidget */
    $form=$this->beginWidget('yzAdmin.widgets.AdminActiveFormWidget',array(
        'id'=>'admin-users-form',
        'enableAjaxValidation'=>true,
        'type'=>'horizontal',
        'htmlOptions' => array('class' => 'well'),
    )); ?>

<p class="help-block"><?php echo Yii::t('AdminModule.t9n','Fields with {star} are required.',array(
    '{star}'=>'<span class="required">*</span>'
)); ?></p>

<legend><?php echo Yii::t('AdminModule.t9n','Change password'); ?></legend>

<?php echo $form->passwordFieldRow($model,'[p]password',array('class'=>'span5')); ?>

<?php echo $form->passwordFieldRow($model,'[p]password_repeat',array('class'=>'span5')); ?>

<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
    'buttonType'=>'submit',
    'type'=>'primary',
    'label'=>Yii::t('AdminModule.t9n','Change'),
)); ?>
</div>

<?php $this->endWidget(); ?>

