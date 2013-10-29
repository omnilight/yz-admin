<?php
/** @var $this YzBackController */
/** @var $model AdminAuthItem */
?>
<?php /** @var $form AdminActiveFormWidget */
$form=$this->beginWidget('yzAdmin.widgets.AdminActiveFormWidget',array(
	'id'=>'admin-auth-item-form',
	'enableAjaxValidation'=>true,
	'type'=>'horizontal',
	'htmlOptions' => array('class' => 'well'),
)); ?>

<p class="help-block"><?php echo Yii::t('AdminModule.t9n','Fields with {star} are required.',array(
    '{star}'=>'<span class="required">*</span>'
    )); ?></p>

<?php echo $form->errorSummary($model); ?>

<?php if(Yz::isDeveloperMode(false)): ?>
    <ul class="nav nav-tabs" id="formTabs">
        <li class="active"><a href="#general" data-toggle="tab"><?php echo Yii::t('AdminModule.t9n','Main'); ?></a></li>
        <li><a href="#special" data-toggle="tab"><?php echo Yii::t('AdminModule.t9n','Special'); ?></a></li>
    </ul>

<?php endif; ?>
<?php if(Yz::isDeveloperMode(false)): ?>

<div class="tab-content">

    <div class="tab-pane fade in active" id="general">

<?php endif; ?>

        <legend><?php echo Yii::t('AdminModule.t9n','Role info'); ?></legend>

        <?php echo $form->textFieldRow($model,'description',array('class'=>'span5','maxlength'=>255)); ?>

        <?php echo $form->textFieldRow($model,'name',array('class'=>'span5','maxlength'=>64)); ?>

        <legend><?php echo Yii::t('AdminModule.t9n','Children roles'); ?></legend>

        <?php echo $form->dropDownListRow($model,'childrenRoles',
            CHtml::listData(AdminAuthItem::model()->roles()->findAll('name != :name', array(
                ':name' => $model->isNewRecord ? '' : $model->name,
            )),'name','description'), array('class'=>'span7','multiple'=>true, 'size'=>7)); ?>

        <legend><?php echo Yii::t('AdminModule.t9n','Permissions'); ?></legend>

        <?php echo $form->dropDownListRow($model,'childrenTasks',
        CHtml::listData(AdminAuthItem::model()->tasks()->findAll('name != :name', array(
            ':name' =>  $model->isNewRecord ? '' : $model->name,
        )),'name','description'), array('class'=>'span7','multiple'=>true, 'size'=>10)); ?>

        <?php echo $form->dropDownListRow($model,'childrenOperations',
        CHtml::listData(AdminAuthItem::model()->operations()->findAll('name != :name', array(
            ':name' =>  $model->isNewRecord ? '' : $model->name,
        )),'name','description'), array('class'=>'span7','multiple'=>true, 'size'=>15)); ?>

<?php if(Yz::isDeveloperMode(false)): ?>
    </div>
<?php endif; ?>

    <?php if(Yz::isDeveloperMode(false)): ?>

        <div class="tab-pane fade" id="special">

            <?php echo $form->textAreaRow($model,'bizrule',array('rows'=>6, 'cols'=>50, 'class'=>'span8')); ?>

            <?php echo $form->textAreaRow($model,'data',array('rows'=>6, 'cols'=>50, 'class'=>'span8')); ?>

        </div>

    <?php endif; ?>

<?php if(Yz::isDeveloperMode(false)): ?>
    </div>
<?php endif; ?>

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
