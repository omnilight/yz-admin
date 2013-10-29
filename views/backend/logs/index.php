<?php
/**
 * @var $content string
 * @var $available bool
 */

$this->breadcrumbs=array(
    Yii::t('AdminModule.t9n','Logs'),
);

?>

<div class="page-header subnav">
    <h3><?php echo Yii::t('AdminModule.t9n','Logs'); ?></h3>
</div>

<?php if($available): ?>

    <div class="span12">
        <pre>
            <?php echo CHtml::encode($content); ?>
        </pre>
    </div>

<?php else: ?>
    <?php echo Yii::t('AdminModule.t9n', 'Logs are currently not available'); ?>
<?php endif; ?>