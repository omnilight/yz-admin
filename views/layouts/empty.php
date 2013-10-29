<?php
/** @var $this YzBackController */

Yz::get()->registerBootstrapAssets();
Yz::get()->registerFontAwesome();

/** @var $cs CClientScript */
$cs = Yii::app()->clientScript;
$cs->registerCoreScript('bbq');
$cs->registerScriptFile($this->getAssetsUrl('yzAdmin.assets').'/js/admin.js');
$cs->registerScriptFile(CHtml::normalizeUrl(array('/admin/backend/js/constants')));
$cs->registerCssFile($this->getAssetsUrl('yzAdmin.assets').'/css/admin-styles.css');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title><?php echo Yii::t('AdminModule.t9n', 'Administration panel') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <style type="text/css">

    </style>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>

<?php echo $content; ?>

</body>
</html>