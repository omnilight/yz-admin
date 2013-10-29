<?php

/**
 * This class adds admin panel actions buttons.
 * This class utilizes {@see TbButtonGroup} class.
 */
class AdminActionsWidget extends CWidget
{
    /**
     * List of actions.
     * For default keys possible for {@see TbButtonGroup::buttons} this
     * class adds the following properties:
     * <ul>
     *  <li>yzAppendFilter - appends Yii::app()->request->getParam(yzAppendFilter) to get query</li>
     *  <li>yzType - type of the button: deleteChecked</li>
     *  <li>yzAppendFilterWidget - boolean. If set appends filters values from AdminFiltersWidget</li>
     * </ul>
     *
     * @var array
     */
    public $actions = array();

    public function run()
    {
        if($this->actions === null)
            return;

        foreach( $this->actions as &$action ) {
            if( isset($action['url']) && $action['url'] == array('create')) {
                $action['url']['returnUrl'] = Yii::app()->request->url;
            }
            if(!empty($action['yzAppendFilter']) &&
                Yii::app()->request->getParam($action['yzAppendFilter']) !== null) {
                $action['url'][$action['yzAppendFilter']] =
                    Yii::app()->request->getParam($action['yzAppendFilter']);
            }
            if(isset($action['yzAppendFilterWidget']) && $action['yzAppendFilterWidget'] == true) {
                if( isset($action['htmlOptions']['class']) )
                    $action['htmlOptions']['class'] .= ' action-appendFilters';
                else
                    $action['htmlOptions']['class'] = 'action-appendFilters';

                $this->registerJs($action);
            }
            if(isset($action['yzType'])) {
                switch( $action['yzType'] ) {
                    case 'deleteChecked':
                        $action['url'] = '';
                        if( isset($action['htmlOptions']['class']) )
                            $action['htmlOptions']['class'] .= ' gridView-deleteChecked';
                        else
                            $action['htmlOptions']['class'] = 'gridView-deleteChecked';
                        break;
                }

                $this->registerJs($action);
            }
        }

        if(($returnUrl = Yii::app()->request->getParam('returnUrl'))!==null &&
            $returnUrl != $this->controller->createUrl('index')) {
            $this->actions[] = array(
                'label'=>Yii::t('AdminModule.t9n','Go Back'),
                'icon'=>'arrow-left',
                'url'=>$returnUrl,
                //'type'=>'success',
            );
        }

        $this->render('AdminActionsWidget',array(
            'actions' => $this->actions,
        ));
    }

    protected function registerJs($action)
    {
        /** @var $cs CClientScript */
        $cs = Yii::app()->getClientScript();
        if(isset($action['yzType']))
            switch( $action['yzType'] ) {
                case 'deleteChecked':

                    $gridId = CJavaScript::encode($action['yzGridViewId']);
                    $columnId = CJavaScript::encode($action['yzColumnId']);

                    $deleteUrl = CJavaScript::encode(Yii::app()->controller->createUrl("delete"));

                    $confirmMessage = CJavaScript::encode(Yii::t('AdminModule.t9n',
                        'Are you sure you want to delete this items?'
                    ));
                    $alertMessage = CJavaScript::encode(Yii::t('AdminModule.t9n',
                        'You must select at least one record'
                    ));

                    $js =<<<JS
    jQuery('.gridView-deleteChecked').click(function(){
        var checked = $.fn.yiiGridView.getChecked({$gridId}, {$columnId});
        if( checked.length == 0 ) { alert({$alertMessage}); return false; }

        if(!confirm({$confirmMessage})) return false;

        var deleteUrl = {$deleteUrl};

        var items = $.map(checked, function(item){
            return 'id[]='+item;
        });

        deleteUrl += '?' + items.join('&');

        var th=this;
        var afterDelete=function(){};
        $.ajax({
            type:'POST',
            url:deleteUrl,
            success:function(data) {
                $.fn.yiiGridView.update({$gridId});
                afterDelete(th,true,data);
            },
            error:function(XHR) {
                return afterDelete(th,false,XHR);
            }
        });
        return false;
    });
JS;

                    $cs->registerScript('gridView-deleteChecked', $js);

                    break;
            }

        if(isset($action['yzAppendFilterWidget']) && $action['yzAppendFilterWidget'] == true) {
            $js =<<<JS
$('.action-appendFilters').click(function(){
    var data = $('.admin-filters-widget form#admin-filters-widget-form').serialize();
    var href = $(this).attr('href');
    href += '?' + data;
    //console.log(href);
    $(this).attr('href',href);
    return true;
});
JS;
            $cs->registerScript('action-appendFilters', $js);
        }
    }
}