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
        <th>Название настройки</th>
        <th>Значение</th>
        <th>Действие</th>
    </tr>
    </thead>

    <tbody>
    <tr>
        <td><?php echo Yii::t('AdminModule.t9n','Cache status'); ?></td>
        <td>
            <?php echo $settings['useCache']?strtr('Используется кэш: <strong>{cache}</strong>',
                array(
                    '{cache}' => $settings['cache']['type'],
                )) : '<strong>Не используется</strong>';
            ?>
            <?php if($settings['useCache'] && $settings['cache']['type'] == 'Memcache'): ?>
                <br>
                Всего записей: <?php echo $settings['cache']['items']; ?>
                <br>
                Размер кэша: <?php echo number_format($settings['cache']['bytes']/1024,2); ?> Кб
            <?php endif; ?>
        </td>
        <td><?php $this->widget('bootstrap.widgets.TbButton',array(
            'url'=>array('generalSettings', 'action'=>'cacheReset'),
            'label' => Yii::t('AdminModule.t9n','Reset cache'),
        )); ?></td>
    </tr>
    </tbody>
</table>

