<?php

class Application_Form_ServiceDetails extends Zend_Form
{
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {
        $this->setName('serviceDetails');

        $lesson_category = new Zend_Form_Element_Select('lesson_category');
        $lesson_category->setAttrib('id', 'lesson_category')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->addMultiOptions(array('Languages'   => 'Languages',
            'Business'   => 'Business',
            'Tutoring'   => 'Tutoring',
            'Consulting'   => 'Consulting',
            'other?'   => 'other?'
        ));

        $subcategory = new Zend_Form_Element_Text('subcategory');
        $subcategory ->setAttrib('placeholder', 'Specify Category')
            ->setAttrib('id', 'subcategory')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $rate = new Zend_Form_Element_Text('rate');
        $rate ->setAttrib('class', 'required input-small')
            ->setAttrib('placeholder', 'rate')
            ->setAttrib('id', 'rate')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $duration = new Zend_Form_Element_Select('duration');
        $duration->setAttrib('id', 'duration')
            ->setAttrib('class', 'input-small')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->addMultiOptions(array('15 min'   => '15 min',
            '45 min'   => '45 min',
            'Hr'   => 'Hr',
            'Lesson'   => 'Lesson'
        ));

        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel('Describe Your Service Details')
            ->setAttrib('id', 'description')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            -> setAttrib('rows', '7');

        $submit = new Zend_Form_Element_Submit('saveService');
        $submit ->setLabel('Save')
            ->setAttrib('id', 'saveService')
            ->setAttrib('class', 'btn')
            ->setDecorators($this->basicDecorators);

        $this->addElements(array($lesson_category, $subcategory, $rate, $duration, $description, $submit));

    }
}

?>