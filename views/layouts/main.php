<?php
/** @var $this YzBackController */

$developerModeMessage = Yii::t('AdminModule.t9n','Developer mode');

Yz::get()->registerBootstrapAssets();
Yz::get()->registerFontAwesome();

/** @var $cs CClientScript */
$cs = Yii::app()->clientScript;
$cs->registerCoreScript('bbq');
$cs->registerScriptFile($this->getAssetsUrl('yzAdmin.assets').'/js/admin.js');
$cs->registerScriptFile(CHtml::normalizeUrl(array('/admin/backend/js/constants')));
$cs->registerCssFile($this->getAssetsUrl('yzAdmin.assets').'/css/admin-styles.css');
$css =<<<CSS
body {
    padding-top: 50px;
}
CSS;
$cs->registerCss('top-offset',$css);
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

<!-- Navbar -->
<?php $this->widget('bootstrap.widgets.TbNavbar', array(
    'brand'=>Yii::t('AdminModule.t9n', 'Administration panel'),
    'brandUrl'=>$this->createUrl('/admin/backend/default/index'),
    'collapse'=>false, // requires bootstrap-responsive.css
    'fluid'=>true,
    'items'=>array(
        array(
            'class'=>'bootstrap.widgets.TbButtonGroup',
            'htmlOptions'=>array('class'=>'pull-right'),
            'buttons'=>array(
                array('label'=>Yii::app()->user->name, 'icon' => 'user', 'items'=>array(
                    array(
                        'label'=>Yii::t('AdminModule.t9n', 'Profile'),
                        'url'=>$this->createUrl('/admin/backend/adminUsers/ownProfile'),
                        'icon' => 'user',
                    ),
                    '---',
                    array(
                        'label'=>Yii::t('AdminModule.t9n', 'Logout'),
                        'url'=>$this->createUrl('/admin/backend/default/logout'),
                        'icon' => 'off',
                    ),
                )),
            ),
        ),
        (Yz::get()->developerMode?"<p class='developer-mode-message navbar-text'>{$developerModeMessage}</p>":''),
    ),
)); ?>
<!-- End Navbar -->

<?php echo $content; ?>

</body>
</html>