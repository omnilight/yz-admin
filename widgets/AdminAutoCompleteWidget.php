<?php

Yii::import('zii.widgets.jui.CJuiAutoComplete');

class AdminAutoCompleteWidget extends CJuiAutoComplete
{
    /**
     * @var null|AdminActiveFormWidget
     */
    public $form = null;

    public function run()
    {
        list($name,$id)=$this->resolveNameID();

        if(isset($this->htmlOptions['id']))
            $id=$this->htmlOptions['id'];
        else
            $this->htmlOptions['id']=$id;

        if(isset($this->htmlOptions['name']))
            $name=$this->htmlOptions['name'];

        if($this->hasModel()) {
            if($this->hasForm()) {
                echo $this->form->textFieldRow($this->model, $this->attribute, $this->htmlOptions);
            }
            else {
                echo CHtml::activeTextField($this->model, $this->name, $this->htmlOptions);
            }
        }
        else {
            echo CHtml::textField($this->name, $this->value, $this->htmlOptions);
        }

        if($this->sourceUrl!==null)
            $this->options['source']=CHtml::normalizeUrl($this->sourceUrl);
        else
            $this->options['source']=$this->source;

        $options=CJavaScript::encode($this->options);

        $js = "jQuery('#{$id}').autocomplete($options);";

        $cs = Yii::app()->getClientScript();
        $cs->registerScript(__CLASS__.'#'.$id, $js);
    }

    public function hasForm()
    {
        return !is_null($this->form) && is_a($this->form, 'AdminActiveFormWidget');
    }
}