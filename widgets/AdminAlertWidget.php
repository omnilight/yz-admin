<?php

/**
 * This widget is used to display user notice messages.
 *
 * Currently, it's overrights Bootstrap's TbAlert due to it bugs
 * and we add some more functionality
 */
Yii::import('bootstrap.widgets.TbAlert');

class AdminAlertWidget extends TbAlert
{
    public $autoClose = 15;

    /**
     * Initializes the widget.
     */
    public function init()
    {
        if (!isset($this->htmlOptions['id']))
            $this->htmlOptions['id'] = $this->getId();
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        parent::run();

        $id = $this->htmlOptions['id'];

        /** @var CClientScript $cs */
        $cs = Yii::app()->getClientScript();

        foreach ($this->alerts as $type => $alert)
        {
            if (is_string($alert))
            {
                $type = $alert;
                $alert = array();
            }

            if (isset($alert['visible']) && $alert['visible'] === false)
                continue;

            if (Yii::app()->user->hasFlash($type))
            {
                if(isset($alert['autoClose']))
                    $autoClose = $alert['autoClose'];
                else
                    $autoClose = $this->autoClose;

                if(is_null($autoClose))
                    continue;

                $autoClose *= 1000;

                $selector = $selector = "#{$id} .alert-{$type}";

                $cs->registerScript(__CLASS__.'#'.$id.'_autoclose_'.$type,
                    "setTimeout(function(){ jQuery('{$selector}').alert('close'); }, {$autoClose});"
                );
            }
        }
    }
}