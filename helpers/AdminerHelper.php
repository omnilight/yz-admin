<?php

function getDatabase()
{
    if(preg_match('/dbname=([^;]+)/',Yii::app()->db->connectionString,$m)) {
        $dbName = $m[1];
        return $dbName;
    } else
        throw new CException('Can not extract host name from connection string');
}

function adminer_object()
{
    // required to run any plugin
    $adminerDir = Yii::getPathOfAlias('yzAdmin.vendors.adminer');
    include_once $adminerDir . "/plugins/plugin.php";

    // autoloader
    foreach (glob($adminerDir . "/plugins/*.php") as $filename) {
        include_once $filename;
    }

    $plugins = array(new AdminerFrames(), new AdminerJsonColumn());

    // Ugly way to fix languages
    global $ag;
    $ag= get_translations(Yii::app()->language);

    class YzAdminer extends AdminerPlugin
    {
        function name()
        {
            return Yii::t('AdminModule.t9n', 'DB Admin');
        }

        function permanentLogin() {
            // key used for permanent login
            return md5('Yz Admin - DB Admin login');
        }

        function credentials()
        {
            if(preg_match('/host=([^;]+)/',Yii::app()->db->connectionString,$m)) {
                $host = $m[1];
            } else
                throw new CException('Can not extract host name from connection string');
            $username = Yii::app()->db->username;
            $password = Yii::app()->db->password;
            return array($host, $username, $password);
        }

        function database()
        {
            return getDatabase();
        }

        function databases()
        {
            return array($this->database());
        }

        function homepage()
        {
            return false;
        }
    }

    return new YzAdminer($plugins);
}

$adminerFile = Yii::getPathOfAlias('yzAdmin.vendors.adminer') . '/adminer-mysql.php';

$_GET['username'] = '';
if(isset($_GET['db']) && $_GET['db'] != getDatabase())
    $_GET['db'] = getDatabase();
require_once($adminerFile);