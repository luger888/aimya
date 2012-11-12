<?php

class Application_Form_FeaturesFilter extends Zend_Form
{
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {
        $lessonDbModel = new Application_Model_DbTable_LessonCategory();
        $lessonCategories = $lessonDbModel->getLessonCategories(); //category from db

        $author = new Zend_Form_Element_Select('author');
        $author->setAttrib('id', 'author')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->addMultiOptions(array('0' => 'All',
            '2' => 'Instructors only',
            '1' => 'Students only'
        ));

        $lesson_category = new Zend_Form_Element_Select('lesson_category');
        $lesson_category->setAttrib('id', 'lesson_category')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->addMultiOption('All', 'All');
        foreach ($lessonCategories as  $value) {
            $lesson_category->addMultiOption($value['title'], $value['title']);
        }
        $this->addElements(array($author, $lesson_category));
    }
}

?>