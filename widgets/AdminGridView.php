<?php

Yii::import('bootstrap.widgets.TbGridView');
Yii::import('yzAdmin.widgets.AdminDataColumn');

/**
 * This class uses parts of code from EExcelView
 * @todo Refactor this class
 * @todo This class has spaghetti-like code
 *
 * @property boolean $hasSubHeader
 * @property boolean $backgroundExport This option could be set to true
 * only if Queues module is installed. Otherwise it will be automatically
 * reseted to false
 *
 */
class AdminGridView extends TbGridView
{
    const GRID_MODE_GRID = 'grid';
    const GRID_MODE_EXPORT = 'export';

    const QUEUE_EXCHANGE_NAME = 'adminGridViewExport';

    /**
     * @var mixed the ID of the container whose content may be updated with an AJAX response.
     * Defaults to false, due to let us use such Yz admin futures as returnUrl and so on.
     * For list of possible values see {@see CGridView::$ajaxUpdate}
     */
    public $ajaxUpdate = false;

    public $enableExport = true;
    protected $_backgroundExport = false;

    public $gridMode = null;
    public $exportType = null;

    //Document properties
    public $creator = 'Yz Engine';
    public $title = 'export';
    public $subject = 'Subject';
    public $description = '';
    public $category = '';

    // Config
    /**
     * @var string|null If set to string, exported data will be saved as
     * a file with name $filename
     */
    public $filename = null; //export FileName
    /**
     * @var bool If true exported data will be streamed to browser (with request
     * to download file)
     */
    public $stream = true; //stream to browser
    /**
     * @var array Array of buttons that will be displayed for widget
     */
    public $exportButtons = array('Excel5','Excel2007',/*'PDF',*/'HTML','CSV',);
    /**
     * @var bool Whether to export all data (true) or only current page (false)
     */
    public $disableExportPaging = true;
    /**
     * @var bool Whether to disconnect from database between requesting pages of data.
     * This setting is actual only if {@see $disableExportPaging} is set to true
     */
    public $disconnectFromDatabase = false;
    /**
     * @var bool Whether to set columns width automatically
     */
    public $autoWidth = true;

    //mime types used for streaming
    public $mimeTypes = array(
        'Excel5'	=> array(
            'Content-type'=>'application/vnd.ms-excel',
            'extension'=>'xls',
            'caption'=>'Excel (*.xls)',
        ),
        'Excel2007'	=> array(
            'Content-type'=>'application/vnd.ms-excel',
            'extension'=>'xlsx',
            'caption'=>'Excel 2007+ (*.xlsx)',
        ),
        'PDF'		=>array(
            'Content-type'=>'application/pdf',
            'extension'=>'pdf',
            'caption'=>'PDF (*.pdf)',
        ),
        'HTML'		=>array(
            'Content-type'=>'text/html',
            'extension'=>'html',
            'caption'=>'HTML (*.html)',
        ),
        'CSV'		=>array(
            'Content-type'=>'application/csv',
            'extension'=>'csv',
            'caption'=>'CSV (*.csv)',
        )
    );

    /** @var PHPExcel */
    protected $phpExcel = null;

    public function init()
    {
        if($this->gridMode === null)
            $this->gridMode = Yii::app()->request->getParam('gridMode',self::GRID_MODE_GRID);
        if($this->exportType === null)
            $this->exportType = Yii::app()->request->getParam('exportType','Excel5');

        if($this->gridMode == self::GRID_MODE_EXPORT  && $this->enableExport && $this->backgroundExport) {

            /** @var $queues YzQueues */
            $queues = Yii::app()->queues;

            /** @var $dataProvider CActiveDataProvider */
            $dataProvider = $this->dataProvider;

            $ref = new ReflectionClass($dataProvider->model);

            $data = array(
                'criteria' => serialize($dataProvider->criteria),
                'modelInfo' => array(
                    'module' => $this->controller->module->getId(),
                    'path' => $ref->getFileName(),
                    'class' => $dataProvider->modelClass,
                ),
                'columns' => $this->columns,
                'exportType' => $this->exportType,
                'mimeType' => $this->mimeTypes[$this->exportType],
                'adminUserId' => Yii::app()->user->id,
            );

            $queues->adapter->exchange(self::QUEUE_EXCHANGE_NAME)
                ->publish($data);

            Yii::app()->user->setFlash('info',Yii::t('AdminModule.t9n','Your export request was added to queue. You will notified when it will be ready to download'));
            $this->controller->redirect(Yii::app()->request->urlReferrer);
        } elseif($this->gridMode == self::GRID_MODE_EXPORT  && $this->enableExport) {
            /** @var $adminModule AdminModule */
            $adminModule = Yz::get()->getModule('admin');
            $lib = Yii::getPathOfAlias(Yz::gep('phpExcel',AdminModule::DEFAULT_PHPEXCEL_PATH)).'.php';
            if(!is_file($lib))
                throw new Exception(Yii::t('AdminModule.t9n','"PHP Excel lib not found ({lib})',array(
                    '{lib}' => $lib,
                )));

            $this->initColumns();
            //Autoload fix
            spl_autoload_unregister(array('YiiBase','autoload'));
            Yii::import(Yz::gep('phpExcel',AdminModule::DEFAULT_PHPEXCEL_PATH), true);

            // Caching
            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod);

            $this->phpExcel = new PHPExcel();
            spl_autoload_register(array('YiiBase','autoload'));
            // Creating a workbook
            $this->phpExcel->getProperties()->setCreator($this->creator);
            $this->phpExcel->getProperties()->setTitle($this->title);
            $this->phpExcel->getProperties()->setSubject($this->subject);
            $this->phpExcel->getProperties()->setDescription($this->description);
            $this->phpExcel->getProperties()->setCategory($this->category);
        } else {
            parent::init();
        }
    }

    public function run()
    {
        if($this->gridMode == self::GRID_MODE_EXPORT && $this->enableExport)
        {
            $this->exportRenderHeader();
            $row = $this->exportRenderBody();
            $this->exportRenderFooter($row);

            //set auto width
            if($this->autoWidth)
                foreach($this->columns as $n=>$column)
                    $this->phpExcel->getActiveSheet()->getColumnDimension($this->getExcelColumnName($n+1))->setAutoSize(true);
            //create writer for saving
            $objWriter = PHPExcel_IOFactory::createWriter($this->phpExcel, $this->exportType);
            if(!$this->stream)
                $objWriter->save($this->filename);
            else //output to browser
            {
                if(!$this->filename)
                    $this->filename = $this->title;
                $this->cleanOutput();
                // TODO Use CHttpRequest::sendFile function instead
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-type: '.$this->mimeTypes[$this->exportType]['Content-type']);
                header('Content-Disposition: attachment; filename="'.$this->filename.'.'.$this->mimeTypes[$this->exportType]['extension'].'"');
                header('Cache-Control: max-age=0');
                $objWriter->save('php://output');
                Yii::app()->end();
            }
        } else {

            $this->renderExportButtons();

            parent::run();
        }
    }

    /**
     * Renders the table body.
     */
    public function renderTableBody()
    {
        $data=$this->dataProvider->getData();
        $n=count($data);
        echo "<tbody>\n";

        if($n>0)
        {
            $this->renderTableSubHeader(); // Difference between this function and parent

            for($row=0;$row<$n;++$row)
                $this->renderTableRow($row);
        }
        else
        {
            echo '<tr><td colspan="'.count($this->columns).'" class="empty">';
            $this->renderEmptyText();
            echo "</td></tr>\n";
        }
        echo "</tbody>\n";
    }

    public function renderExportButtons()
    {
        if($this->enableExport == false)
            return;

        $buttons = array();
        foreach($this->exportButtons as $key=>$button)
        {
            $item = is_array($button) ? CMap::mergeArray($this->mimeTypes[$key], $button) : $this->mimeTypes[$button];
            $type = is_array($button) ? $key : $button;
            $url = parse_url(Yii::app()->request->requestUri);
            //$content[] = CHtml::link($item['caption'], '?'.$url['query'].'exportType='.$type.'&'.$this->grid_mode_var.'=export');
            if (isset($url['query']))
                $url = '?'.$url['query'].'&exportType='.$type.'&gridMode=export';
            else
                $url = '?exportType='.$type.'&gridMode=export';
            $buttons[] = array(
                'label'=>$item['caption'],
                'url'=>$url
            );

        }
        if($buttons !== array())
            $this->widget('bootstrap.widgets.TbButtonGroup', array(
                'type'=>'', // '', 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
                'size'=>'mini',
                'buttons'=>array(
                    array(
                        'label'=>Yii::t('AdminModule.t9n','Export as...'),
                        'items'=>$buttons
                    ),
                ),
                'htmlOptions' => array(
                    'class' => 'pull-right',
                )
            ));

    }

    /**
     * Renders table sub header. This is generaly used for show sums above all columns
     */
    public function renderTableSubHeader()
    {
        if($this->getHasSubHeader() == false)
            return;

        echo "<tr class=\"sub-header\">\n";
        foreach($this->columns as $column) {
            if(method_exists($column, 'renderSubHeaderCell'))
                $column->renderSubHeaderCell();
            else
                echo '<td></td>';
        }
        echo "</tr>\n";
    }

    /**
     * @return boolean whether the table should render a sub header.
     * This is true if any of the {@link columns} has a true {@link AdminDataColumn::hasSubHeader} value.
     */
    public function getHasSubHeader()
    {
        foreach($this->columns as $column)
            if(method_exists($column, 'hasSubHeader'))
                if($column->hasSubHeader())
                    return true;
        return false;
    }

    /**
     * Creates column objects and initializes them.
     */
    protected function initColumns()
    {
        foreach ($this->columns as $i => $column)
        {
            if (is_array($column) && !isset($column['class']))
                $this->columns[$i]['class'] = 'yzAdmin.widgets.AdminDataColumn';
        }

        parent::initColumns();
    }

    /**
     * Creates a column based on a shortcut column specification string.
     * @param mixed $text the column specification string
     * @return \TbDataColumn|\CDataColumn the column instance
     * @throws CException if the column format is incorrect
     */
    protected function createDataColumn($text)
    {
        if (!preg_match('/^([\w\.]+)(:(\w*))?(:(.*))?$/', $text, $matches))
            throw new CException(Yii::t('zii', 'The column must be specified in the format of "Name:Type:Label", where "Type" and "Label" are optional.'));

        $column = new AdminDataColumn($this);
        $column->name = $matches[1];

        if (isset($matches[3]) && $matches[3] !== '')
            $column->type = $matches[3];

        if (isset($matches[5]))
            $column->header = $matches[5];

        return $column;
    }

    public function setBackgroundExport($backgroundExport)
    {
        if($backgroundExport == true) {
            if(Yz::get()->getModule('queues') === null)
                $backgroundExport = false;
        }
        $this->_backgroundExport = $backgroundExport;
    }

    public function getBackgroundExport()
    {
        return $this->_backgroundExport;
    }

    protected function exportRenderHeader()
    {
        $a=0;
        foreach($this->columns as $column)
        {
            if($column instanceof CButtonColumn)
                continue;
            elseif($column instanceof CCheckBoxColumn)
                continue;
            elseif($column->header===null && $column->name!==null)
            {
                if($column->grid->dataProvider instanceof CActiveDataProvider)
                    $head = $column->grid->dataProvider->model->getAttributeLabel($column->name);
                else
                    $head = $column->name;
            } else
                $head =trim($column->header)!=='' ? $column->header : $column->grid->blankDisplay;

            $a++;

            $cell = $this->phpExcel->getActiveSheet()->setCellValue($this->getExcelColumnName($a)."1" ,$head, true);
        }
    }

    protected function exportRenderBody()
    {
        if($this->disableExportPaging) //if needed disable paging to export all data
            $this->dataProvider->getPagination()->setPageSize(1000);

        $totalData = $this->dataProvider->getTotalItemCount(true);
        $i = $totalRowsCount = 0;
        do {
            /**
             * When exporting the whole data at once,
             * we can get MySQL's 'MySQL has gone away' error. It's happening
             * because script run for too long time.
             *
             * So the fix is to disconnect from database after getting new portion of data
             */
            $this->dataProvider->getPagination()->setCurrentPage($i);
            $data=$this->dataProvider->getData(true);
            $currentRowsCount=count($data);

            if($this->disableExportPaging && $this->disconnectFromDatabase)
                Yii::app()->db->setActive(false);

            if($currentRowsCount>0)
            {
                for($row=0;$row<$currentRowsCount;++$row)
                    $this->exportRenderRow($data,$row, $row + $totalRowsCount);
            }
            $totalRowsCount += $currentRowsCount;
            unset($data);
        } while( $this->disableExportPaging && ++$i < $this->dataProvider->getPagination()->getPageCount());
        return $totalData;
    }

    protected function exportRenderRow(&$data, $row, $totalRow)
    {
        $a=0;
        foreach($this->columns as $n=>$column)
        {
            if($column instanceof CLinkColumn)
            {
                if($column->labelExpression!==null)
                    $value=$column->evaluateExpression($column->labelExpression,array('data'=>$data[$row],'row'=>$row));
                else
                    $value=$column->label;
            } elseif($column instanceof CButtonColumn)
                $value = ""; //Dont know what to do with buttons
            elseif($column->value!==null)
                $value=$this->evaluateExpression($column->value ,array('data'=>$data[$row]));
            elseif($column->name!==null) {
                //$value=$data[$row][$column->name];
                $value= CHtml::value($data[$row], $column->name);
                $value=$value===null ? "" : $column->grid->getFormatter()->format($value,'raw');
            } elseif($column instanceof CCheckBoxColumn)
                continue;

            $a++;
            $cell = $this->phpExcel->getActiveSheet()->setCellValue($this->getExcelColumnName($a).($totalRow+2) , strip_tags($value), true);
        }
    }

    protected function exportRenderFooter($row)
    {
        $a=0;
        foreach($this->columns as $n=>$column)
        {
            $a=$a+1;
            if($column->footer)
            {
                $footer =trim($column->footer)!=='' ? $column->footer : $column->grid->blankDisplay;

                $cell = $this->phpExcel->getActiveSheet()->setCellValue($this->getExcelColumnName($a).($row+2) ,$footer, true);
            }
        }
    }

    /**
     * Returns the coresponding excel column.(Abdul Rehman from yii forum)
     *
     * @param int $index
     * @throws Exception
     * @return string
     */
    protected function getExcelColumnName($index)
    {
        --$index;
        if($index >= 0 && $index < 26)
            return chr(ord('A') + $index);
        else if ($index > 25)
            return ($this->getExcelColumnName($index / 26)).($this->getExcelColumnName($index%26 + 1));
        else
            throw new Exception("Invalid Column # ".($index + 1));
    }

    /**
     * Performs cleaning on mutliple levels.
     *
     * From le_top @ yiiframework.com
     *
     */
    private static function cleanOutput()
    {
        for($level=ob_get_level();$level>0;--$level)
        {
            @ob_end_clean();
        }
    }
}