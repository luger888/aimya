<?php

class Application_Form_Resume extends Zend_Form
{
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {



        $this->addElements(array());

    }
}

?>