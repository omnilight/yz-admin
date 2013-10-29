<?php

class LogsController extends YzBackController
{
    public function actionIndex()
    {
        $logFile = Yii::getPathOfAlias('application.runtime').'/application.log';

        if(file_exists($logFile)) {
            $data = array(
                'available' => true,
                'content' => file_get_contents($logFile),
            );
        } else {
            $data = array(
                'available' => false,
            );
        }

        $this->render('index',$data);
    }
}