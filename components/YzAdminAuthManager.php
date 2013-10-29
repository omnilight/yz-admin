<?php

/**
 *
 */
class YzAdminAuthManager extends CDbAuthManager
{
    /**
     * @var string the name of the table storing authorization items. Defaults to 'AuthItem'.
     */
    public $itemTable='{{admin_authitem}}';
    /**
     * @var string the name of the table storing authorization item hierarchy. Defaults to 'AuthItemChild'.
     */
    public $itemChildTable='{{admin_authitemchild}}';
    /**
     * @var string the name of the table storing authorization item assignments. Defaults to 'AuthAssignment'.
     */
    public $assignmentTable='{{admin_authassignment}}';
}