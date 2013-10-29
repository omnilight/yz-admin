<?php
/** @var $actions array */
?>
<div class="btn-toolbar pull-right">
    <?php $this->widget('bootstrap.widgets.TbButtonGroup', array(
        'type'=>'info', // '', 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
        'buttons'=>$actions,
    )); ?>
</div>