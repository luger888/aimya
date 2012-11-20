<?php

class Application_Form_MessageActions extends Zend_Form
{
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {

        $singleActions = new Zend_Form_Element_Select('singleactions');
        $singleActions->setAttrib('id', 'singleactions')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->addMultiOptions(array(
                '0' => 'Reply',
                '1' => 'Forward',
                '2' => 'Move To Archived',
                '3' => 'Trash'
        ));

        $massActions = new Zend_Form_Element_Select('massactions');
        $massActions->setAttrib('id', 'massactions')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->addMultiOptions(array(
                '0' => 'Move To Archived',
                '1' => 'Trash'
        ));

        $this->addElements(array($singleActions, $massActions));
    }
}

?>