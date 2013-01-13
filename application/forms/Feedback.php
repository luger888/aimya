<?php

class Application_Form_Feedback extends Zend_Form
{
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {

        $lessonId = new Zend_Form_Element_Hidden('lessonId');
        $lessonId->setAttrib('id', 'lessonId')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $content = new Zend_Form_Element_Textarea('feedbackContent');
        $content->setLabel('Feedback Content')
            ->setAttrib('id', 'feedbackContent')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->setAttrib('rows', '10')
            ->setAttrib('cols', '55');

        $submit = new Zend_Form_Element_Submit('saveFeedback');
        $submit ->setLabel('save')
             ->setAttrib('id', 'saveFeedback')
             ->setAttrib('class', 'button-2 save')
            ->setDecorators($this->basicDecorators);


        $this->addElements(array($content, $lessonId, $submit));

    }
}

?>