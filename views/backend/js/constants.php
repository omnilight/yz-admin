<?php
/**
 * @var $this YzBackController
 */
?>
$.admin.setFlashUrl = <?php echo CJavaScript::encode(CHtml::normalizeUrl(array(
    '/admin/backend/js/setFlash'
))); ?>;
$.admin.redirectUrl = <?php echo CJavaScript::encode(CHtml::normalizeUrl(array(
    '/admin/backend/js/redirect'
))); ?>;
$.admin.fileManagerAvailable = <?php echo CJavaScript::encode(Yii::app()->hasModule('files')); ?>;
$.admin.fileManagerUrl = <?php echo CJavaScript::encode(CHtml::normalizeUrl(array(
    '/files/backend/fileManager/page'
))); ?>;