<?php

Yii::import('bootstrap.widgets.TbButtonColumn');

class AdminButtonColumn extends TbButtonColumn
{
    public $updateButtonUrl='Yii::app()->controller->createUrl("update",array("id"=>$data->primaryKey, "returnUrl"=>Yii::app()->request->getRequestUri()))';

    public $deleteButtonUrl='Yii::app()->controller->createUrl("delete",array("id"=>$data->primaryKey, "returnUrl"=>Yii::app()->request->getRequestUri()))';

    public $template = '{update} {delete}';
}