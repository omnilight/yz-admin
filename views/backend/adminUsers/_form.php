<?php
/** @var $this YzBackController */
/** @var $model AdminUsers */
/** @var $modelPassword AdminUsers */
?>
<?php /** @var $form AdminActiveFormWidget */
$form=$this->beginWidget('yzAdmin.widgets.AdminActiveFormWidget',array(
	'id'=>'admin-users-form',
	'enableAjaxValidation'=>true,
	'type'=>'horizontal',
	'htmlOptions' => array('class' => 'well'),
)); ?>

<?php echo $form->errorSummary($model); ?>

<p class="help-block">
    <?php echo Yii::t('AdminModule.t9n','Fields with {star} are required.',array(
        '{star}'=>'<span class="required">*</span>'
    )); ?>
</p>

<?php /** @var $tabs AdminTabsWidget */
    $tabs = $this->beginWidget('yzAdmin.widgets.AdminTabsWidget');
?>
    <?php $tabs->beginTab(Yii::t('AdminModule.t9n','Main'),'general'); ?>

        <?php echo $form->textFieldRow($model,'name',array('class'=>'span5')); ?>

        <?php echo $form->textFieldRow($model,'login',array('class'=>'span5')); ?>

        <?php echo $form->textFieldRow($model,'email',array('class'=>'span5')); ?>

        <?php if(Yii::app()->user->checkAccess('AdminUsers.ManageSuperAdminProfile')): ?>
            <?php echo $form->checkBoxRow($model,'is_superadmin'); ?>
        <?php endif; ?>

        <?php echo $form->checkBoxRow($model,'is_enabled'); ?>

        <?php echo $form->dropDownListRow($model,'assignedRoles',
            CHtml::listData(AdminAuthItem::model()->roles()->findAll(),'name','description'),
            array('class'=>'span5','multiple'=>true)); ?>

        <?php if( $model->isNewRecord ): ?>

            <?php echo $form->passwordFieldRow($model,'password',array('class'=>'span5')); ?>

            <?php echo $form->passwordFieldRow($model,'password_repeat',array('class'=>'span5')); ?>

        <? endif; ?>

    <?php $tabs->endTab(); ?>

    <?php $tabs->beginTab(Yii::t('AdminModule.t9n','Permissions'),'permissions'); ?>

        <p><?php echo Yii::t('AdminModule.t9n',
            '<strong>Note!</strong> If some of the tasks or operations were granted to user '.
                'by assigning them through roles, it\'s no reason to reassign them on this page.'); ?></p>

        <?php echo $form->dropDownListRow($model,'assignedTasks',
            CHtml::listData(AdminAuthItem::model()->tasks()->findAll(),'name','description'),
            array('class'=>'span7','multiple'=>true, 'size'=>15,
                'hint' => Yii::t('AdminModule.t9n','Tasks can be children of each other or roles'))); ?>

        <?php echo $form->dropDownListRow($model,'assignedOperations',
            CHtml::listData(AdminAuthItem::model()->operations()->findAll(),'name','description'),
            array('class'=>'span7','multiple'=>true, 'size'=>15,
                'hint' => Yii::t('AdminModule.t9n','Operations can be childs of tasks or roles'))); ?>

    <?php $tabs->endTab(); ?>
<?php $this->endWidget(); /* Tabs */ ?>

<div class="form-actions">
<?php $this->widget('bootstrap.widgets.TbButton', array(
    'buttonType'=>'submit',
    'type'=>'primary',
    'label'=>Yii::t('AdminModule.t9n',$model->isNewRecord ? 'Create & Leave' : 'Save & Leave'),
)); ?>
    <?php if($model->isNewRecord == false): ?>
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'submit',
        'type'=>'primary',
        'label'=>Yii::t('AdminModule.t9n',$model->isNewRecord ? 'Create' : 'Save'),
        'htmlOptions' => array(
            'name' => 'saveAndStay',
        ),
    )); ?>
    <?php endif; ?>
</div>

<?php $this->endWidget(); ?>

<?php if( $model->isNewRecord == false ): ?>

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
            'label'=>Yii::t('AdminModule.t9n',$model->isNewRecord ? 'Create & Leave' : 'Save & Leave'),
        )); ?>
        <?php if($model->isNewRecord == false): ?>
            <?php $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType'=>'submit',
                'type'=>'primary',
                'label'=>Yii::t('AdminModule.t9n',$model->isNewRecord ? 'Create' : 'Save'),
                'htmlOptions' => array(
                    'name' => 'saveAndStay',
                ),
            )); ?>
        <?php endif; ?>
    </div>

    <?php $this->endWidget(); ?>

<? endif; ?>
