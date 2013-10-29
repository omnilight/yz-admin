<?php
/**
 * @var $this YzBackController
 * @var $model LoginForm
 */

/** @var $bootstrap Bootstrap */
$bootstrap = Yii::app()->bootstrap;
$bootstrap->registerAllCss();
$bootstrap->registerCoreScripts();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo Yii::t('AdminModule.t9n', 'Administration panel'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <style type="text/css">
        body {
            padding-top:100px;
            padding-left: 20px;
            padding-right: 20px;
            background-color: #f8f8f8;
        }
        .align-center {
            text-align: center;
        }
    </style>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>

<div class="row-fluid">
    <div class="page-header align-center">
        <h1><?php echo Yii::t('AdminModule.t9n', 'Administration panel') ?></h1>
    </div>
    <div class="span4 offset4 align-center">

        <?php /** @var $form TbActiveForm  */
        $form = $this->beginWidget('yzAdmin.widgets.AdminActiveFormWidget', array(
            'type' => 'inline',
            'htmlOptions' => array('class' => 'well'),
        )); ?>
        <?php echo $form->errorSummary($model); ?>

        <?php echo $form->textFieldRow($model, 'login', array('class'=>'input-small','autocomplete'=>'off')); ?>
        <?php echo $form->passwordFieldRow($model, 'password', array('class'=>'input-small','autocomplete'=>'off')); ?>
        <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'icon'=>'lock', 'label'=>Yii::t('AdminModule.t9n', 'Enter'))); ?>

        <?php $this->endWidget(); ?>

    </div>
</div>

<?php $this->widget('bootstrap.widgets.TbNavbar', array(
    'fixed' => 'bottom',
    'brand' => false,
    'items' => array(
        '<p class="navbar-text">'.
            Yii::t('AdminModule.t9n', 'Powered by %YzEngineUrl%', array(
                '%YzEngineUrl%' => '<a href="http://yzengine.com">Yz Engine</a>',
            )).'</p>',
    ),
    'htmlOptions' => array('class'=>'align-center'),
)); ?>

</body>
</html>
<?php
/** @var $cs CClientScript */
$cs = Yii::app()->getClientScript();
$login_selector = YzHtml::resolveIdSafe($model,'login');
$script=<<<JS
$('#{$login_selector}').focus();
JS;


$cs->registerScript('login-focus',$script);

?>
