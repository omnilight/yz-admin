<?php
/** @var $navigationItems array */

$this->widget('bootstrap.widgets.TbMenu', array(
    'type'=>'list',
    'items'=>$navigationItems,
    'htmlOptions'=>array('class'=>'well'),
));
?>