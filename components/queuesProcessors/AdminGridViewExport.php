<?php

/**
 * @todo Use special function in controller of this kind of export
 */
class AdminGridViewExport extends CComponent implements QueuesProcessorInterface
{
    protected $_adminModule;
    protected $_queuesModule;

    /**
     * @param string $queueName
     */
    public function run($queueName)
    {
        $this->prepare();

        /** @var $queues YzQueues */
        $queues = Yii::app()->queues;

        $exportConfig = $queues->adapter->queue($queueName)->get();

        if(empty($exportConfig))
            echo "Queue: {$queueName}. Nothing to process\n";
        else {

            try {

                echo "Queue: {$queueName}. Found task for export. Processing...\n";

                $tmpFileName = Yii::app()->getRuntimePath() .
                    '/report-'.date('YmdHis').'.'.$exportConfig['mimeType']['extension'];

                $widgetPath = 'admin.widgets.AdminGridView';

                Yii::app()->getModule($exportConfig['modelInfo']['module']);

                require_once($exportConfig['modelInfo']['path']);

                /** @var $model CActiveRecord */
                $model = new $exportConfig['modelInfo']['class'];
                /** @var $dataProvider CActiveDataProvider */
                $dataProvider = $model->search();
                $dataProvider->criteria = unserialize($exportConfig['criteria']);

                /** @var $gridView AdminGridView */
                $gridView = Yii::createComponent(array(
                    'class' => $widgetPath,
                    'dataProvider' => $dataProvider,
                    'columns' => $exportConfig['columns'],
                    'disableExportPaging' => true,
                    'disconnectFromDatabase' => true,
                    'filename' => $tmpFileName,
                    'stream' => false,
                    'gridMode' => 'export',
                ));

                $gridView->init();
                $gridView->run();

                unset($gridView);

                echo "Queue: {$queueName}. Memory peak usage: ".(memory_get_peak_usage()/1024)." Kb\n";
                echo "Queue: {$queueName}. Memory current usage: ".(memory_get_usage()/1024)." Kb\n";

                $queueFile = new QueuesFiles();
                $queueFile->publishFile($tmpFileName);
                $queueFile->title = Yii::t('AdminModule.t9n','Export result');
                $queueFile->admin_user_id = $exportConfig['adminUserId'];
                $queueFile->save();

                @unlink($tmpFileName);

                /*YzAdminUsersNotifier::notifyUser($exportConfig['adminUserId'],
                    Yii::t('AdminModule.t9n','Your export request is done. Check for file in "Queues / Files" section'));*/

                echo "Queue: {$queueName}. Done!\n";

            } catch(CException $e) {
                if(isset($tmpFileName) && file_exists($tmpFileName))
                    @unlink($tmpFileName);

                /*YzAdminUsersNotifier::notifyUser($exportConfig['adminUserId'],
                    Yii::t('AdminModule.t9n','Error has occurred during exporting process. Please, try to export data again.'));*/

                echo "Queue: {$queueName}. Error:\n" . $e->getMessage() . "\n" . $e->getTraceAsString();
            }
        }
    }

    protected function prepare()
    {
        $this->_adminModule = Yii::app()->getModule('admin');
        $this->_queuesModule = Yii::app()->getModule('queues');

        // Import for bootstrap
        Yii::setPathOfAlias('bootstrap',
            Yii::getPathOfAlias(Yz::gep('bootstrap')));

        if(!Yii::app()->hasComponent('bootstrap')) {
            if( $this->_adminModule->initBootstrap )
                Yii::app()->setComponent('bootstrap', Yii::createComponent(Yz::gep('bootstrap') . '.components.Bootstrap'));
            else
                throw new CException(Yii::t('Yz.t9n','You must define bootstrap component to use Yz Engine Admin panel'));
        }
        else
            Yii::app()->getComponent('bootstrap');
    }
}