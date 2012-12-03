<?php

class Application_Form_Search extends Zend_Form
{
    //need password and email validators!
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {
        $this->setName("search_form");

        $searchString = new Zend_Form_Element_Text('search_string');
        $searchString
            ->setAttrib('id', 'search_string')
            ->setAttrib('class', 'searchField')
            ->setAttrib('placeholder', 'Search services')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $submit = new Zend_Form_Element_Submit('searchbtn');
        $submit->setAttrib('id', 'searchbtn')
            ->setLabel('Search')
            ->setAttrib('class', 'btn btnSearch')
            ->setDecorators($this->basicDecorators);

        $this->addElements(array($searchString, $submit));

    }


}