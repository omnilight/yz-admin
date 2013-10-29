<?php

/**
 * AdminFiltersWidget generates configuration based filters for CGridView.
 * Possible filter types:
 * <ul>
 *  <li> text
 *  <li> boolean
 *  <li> range
 *  <li> date
 *  <li> dateRange
 * </ul>
 */
class AdminFiltersWidget extends YzWidget
{
    /** @var CModel */
    public $model;

    /**
     * Array of filters in following format:
     * <code>
     * array(
     *  array('filterType', 'attribute' => 'attr1', ...),
     * );
     * </code>
     * @var array
     */
    public $filters;

    /** @var CGridView */
    public $grid;
    /** @var int */
    public $gridId = 'admin-filters-widget-form';

    protected $_buildInFilters = array(
        'dateRange',
        'select',
        'text',
    );

    public function run()
    {
        $this->gridId = ($this->grid instanceof CGridView)?$this->grid->id:$this->gridId;

        $parsedFilters = $this->processFilters();

        $this->render('AdminFiltersWidget',array(
            'filters' => $parsedFilters,
        ));
    }

    protected function processFilters()
    {
        $filters = array();

        foreach($this->filters as $filterConfig)
        {
            $filterName = array_shift($filterConfig);
            if(empty($filterName) || !in_array($filterName, $this->_buildInFilters))
                throw new CException(Yii::t('AdminModule.t9n','Filter {filter} is not defined',array(
                    '{filter}' => $filterName,
                )));
            $className = 'AdminFilter' . ucfirst($filterName);

            Yii::import('yzAdmin.widgets.filters.'.$className);

            $filterConfig = CMap::mergeArray($filterConfig,array(
                'filtersWidget' => $this,
            ));

            $filters[] = array('yzAdmin.widgets.filters.'.$className, $filterConfig);
        }

        return $filters;
    }
}