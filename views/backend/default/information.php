<?php
/**
 * @var $settings array
 * @var $this YzBackController
 */

$this->breadcrumbs=array(
    Yii::t('AdminModule.t9n','Information'),
);

?>

<h3>System Information</h3>

<table class="table table-striped">
    <tr>
        <td>Yii Framework Version</td>
        <td><?php echo Yii::getVersion(); ?></td>
    </tr>
    <tr>
        <td>Yz Engine Version</td>
        <td><?php echo Yz::getVersion(); ?></td>
    </tr>
</table>

<h3>PHP Information</h3>

<table class="table table-striped">
    <tr>
        <td>PHP Version</td>
        <td><?php echo PHP_VERSION; ?></td>
    </tr>
    <tr>
        <td>SAPI Name</td>
        <td><?php echo php_sapi_name(); ?></td>
    </tr>
</table>

<h3>Server Information</h3>

<table class="table table-striped">
    <tr>
        <td>Operation System</td>
        <td><?php echo php_uname(); ?></td>
    </tr>
</table>

<hr>

<?php echo Yii::powered(); ?>

