<?php
/**
 * @var $settings array
 */

$this->breadcrumbs=array(
    Yii::t('AdminModule.t9n','General settings'),
);

?>
<table class="table table-striped">
    <thead>
    <tr>
        <th>Setting name</th>
        <th>Setting value</th>
        <th>Action</th>
    </tr>
    </thead>

    <tbody>
    <tr>
        <td><?php echo Yii::t('AdminModule.t9n','Cache status'); ?></td>
        <td>
            <?php echo $settings['useCache']?strtr('Using cache: <strong>{cache}</strong>',
                array(
                '{cache}' => $settings['cache']['type'],
                )) : '<strong>Not using</strong>';
            ?>
            <?php if($settings['useCache'] && $settings['cache']['type'] == 'Memcache'): ?>
                <br>
                Total items: <?php echo $settings['cache']['items']; ?>
                <br>
                Size: <?php echo number_format($settings['cache']['bytes']/1024,2); ?> Kb
            <?php endif; ?>
        </td>
        <td><?php $this->widget('bootstrap.widgets.TbButton',array(
            'url'=>array('generalSettings', 'action'=>'cacheReset'),
            'label' => Yii::t('AdminModule.t9n','Reset cache'),
        )); ?>
        </td>
    </tr>
    </tbody>
</table>

