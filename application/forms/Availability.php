<?php

class Application_Form_Availability extends Zend_Form
{
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {
        $this->setName('availability');


        $hours = array();
        foreach (range(1,24) as $fullhour) {
            $parthour = $fullhour > 12 ? $fullhour - 12 : $fullhour;
            $parthour .= $fullhour > 12 ? " pm" : " am";
            $hours["$fullhour:00"] = $parthour;
        }


        $duration = new Zend_Form_Element_Select('clock');
        $duration->setAttrib('id', 'clock')
            ->setAttrib('class', 'input-small')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->addMultiOptions($hours
        );

        $this->addElements(array($duration));

    }
}

?>