<?php
    /* @var $this YzBackController */
?>
<?php $this->beginContent('yzAdmin.views.layouts.main'); ?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span2">

        <?php $this->widget('yzAdmin.widgets.AdminMenuWidget',array(

        )); ?>

        </div><!--/span-->

        <div class="span10">

            <?php if(!empty($this->breadcrumbs))
                $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
                    'links'=> $this->breadcrumbs,
                    'homeLink' => CHtml::link(Yii::t('zii','Home'), array('/admin/backend/default/index')),
                )); ?>

            <?php $this->widget('yzAdmin.widgets.AdminAlertWidget', array(
                'autoClose'=>15, // use transitions?
                'alerts'=>array( // configurations per alert type
                    'success','info','warning',
                    'error'=>array('autoClose'=>null), // success, info, warning, error or danger
                    'danger'=>array('autoClose'=>null), // success, info, warning, error or danger
                ),
            )); ?>

            <?php echo $content; ?>

        </div>

    </div><!--/row-->
</div>

<?php $this->endContent(); ?>