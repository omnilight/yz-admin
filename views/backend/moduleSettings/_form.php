<?php
/** @var $this YzBackController */
/** @var $model YzModuleSettings */

$module = $model->getModule();
?>
<?php /** @var $form AdminActiveFormWidget */
$form=$this->beginWidget('yzAdmin.widgets.AdminActiveFormWidget',array(
	'id'=>'yz-module-settings-form',
	'enableAjaxValidation'=>true,
	'type'=>'horizontal',
	'htmlOptions' => array('class' => 'well'),
)); ?>

	<p class="help-block"><?php echo Yii::t('AdminModule.t9n','Fields with {star} are required.',array(
        '{star}'=>'<span class="required">*</span>'
        )); ?></p>

	<?php echo $form->errorSummary($model); ?>

	<legend><?php echo $module->getName(); ?></legend>

    <?php $parametersLabels = $module->getParamsLabels(); ?>
    <?php foreach( $module->getEditableParams() as $name => $type ): ?>

        <?php if($type == 'string'): ?>

            <?php echo $form->textFieldRow($model, 'parameters['.$name.']', array(
                'labelOptions' => array(
                    'label' => $parametersLabels[$name],
                ), 'class'=>'span8',
            )); ?>

        <?php endif; ?>
        <?php if($type == 'text'): ?>

            <?php echo $form->textAreaRow($model, 'parameters['.$name.']', array(
                'labelOptions' => array(
                    'label' => $parametersLabels[$name],
                ), 'class'=>'span8', 'rows' => '5',
            )); ?>

        <?php endif; ?>
        <?php if($type == 'password'): ?>

            <?php echo $form->passwordFieldRow($model, 'parameters['.$name.']', array(
                'labelOptions' => array(
                    'label' => $parametersLabels[$name],
                ), 'class'=>'span8',
            )); ?>

        <?php endif; ?>
        <?php if($type == 'integer'): ?>

            <?php echo $form->textFieldRow($model, 'parameters['.$name.']', array(
                'labelOptions' => array(
                    'label' => $parametersLabels[$name],
                ), 'class'=>'span8',
            )); ?>

        <?php endif; ?>
        <?php if($type == 'boolean'): ?>

            <?php echo $form->checkBoxRow($model, 'parameters['.$name.']', array(
                'labelOptions' => array(
                    'label' => $parametersLabels[$name],
                )
            )); ?>

        <?php endif; ?>
        <?php if($type == 'html'): ?>

            <?php
                echo $form->textAreaRow($model,'parameters['.$name.']',array(
                    'labelOptions' => array(
                        'label' => $parametersLabels[$name],
                    ), 'class'=>'span8', 'rows' => '5',
                ));
                $this->widget('yz.widgets.YzWisiwigEditorWidget',array(
                    'name' =>  YzHtml::resolveNameSafe($model, 'parameters['.$name.']'),
                    'format' => 'html',
                ));
            ?>

        <?php endif; ?>
        <?php if($type == 'email'): ?>

            <?php echo $form->textFieldRow($model, 'parameters['.$name.']', array(
                'labelOptions' => array(
                    'label' => $parametersLabels[$name],
                ), 'class'=>'span8',
            )); ?>

        <?php endif; ?>


    <?php endforeach; ?>

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
