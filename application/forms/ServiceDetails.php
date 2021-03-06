<?php

class Application_Form_ServiceDetails extends Zend_Form
{
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {

        $translator = $this->getTranslator();

        $lessonDbModel = new Application_Model_DbTable_LessonCategory();
        $lessonCategories = $lessonDbModel->getLessonCategories();//category from db
        $durationDbModel = new Application_Model_DbTable_LessonDuration();
        $lessonPeriods =  $durationDbModel->getLessonDurations();



        $this->setName('serviceDetails');

        $action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
        $serviceType = new Zend_Form_Element_Hidden('service_type');
        $serviceType->setAttrib('id', 'service_type')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);
        if($action == 'requestservices') {
            $serviceType->setValue(2);
        } else {
            $serviceType->setValue(1);
        }
        $lesson_category = new Aimya_Form_Element_SelectAttribs('lesson_category');

        $lesson_category->setAttrib('id', 'lesson_categoryInput')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $lesson_category->addOption('', $translator->translate(' Specify your area of expertise'),array());
        foreach ($lessonCategories as  $value) {
            $lesson_category->addOption($value['title'], $translator->translate($value['title']));
        }

        $lesson_categoryReq = new Aimya_Form_Element_SelectAttribs('lesson_category');

        $lesson_categoryReq->setAttrib('id', 'lesson_categoryInput')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $lesson_categoryReq->addOption('', $translator->translate(' Specify an area you need help with'),array());
        foreach ($lessonCategories as  $value) {
            $lesson_categoryReq->addOption($value['title'], $translator->translate($value['title']));
        }

        $subcategory = new Zend_Form_Element_Text('subcategory');
        $subcategory ->setAttrib('placeholder', $translator->translate('specify your area of expertise'))
            ->setAttrib('id', 'subcategoryInput')
            ->addFilters($this->basicFilters)
            ->setAttrib('maxlength', '25')
            ->setDecorators($this->basicDecorators)
            ->setErrorMessages(array('This must be a number. Currency is in US Dollars.'));

        $rate = new Zend_Form_Element_Text('rate');
        $rate ->setAttrib('class', 'required input-small')
            ->addValidator('Digits')
            ->setAttrib('maxlength', '10')
            ->setAttrib('placeholder', $translator->translate('rate'))
            ->setAttrib('id', 'rateInput')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $duration = new Zend_Form_Element_Select('duration');
        $duration->setAttrib('id', 'durationInput')
            ->setAttrib('class', 'input-small')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);
        foreach ($lessonPeriods as  $value) {
            $duration->addMultiOption($value['duration'], $value['duration'].$translator->translate(' min'));
        }

        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel($translator->translate('Describe Your Service Details'))
            ->setAttrib('id', 'descriptionInput')
            ->setAttrib('maxlength', '500')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            -> setAttrib('rows', '7');


        $submit = new Zend_Form_Element_Submit('saveService');
        $submit ->setLabel('save')
            ->setAttrib('id', 'saveService')
           // ->setAttrib('onClick', 'validateService();return false;')
            ->setAttrib('class', 'button-2 save')
            ->setDecorators($this->basicDecorators);

        $submitReq = new Zend_Form_Element_Submit('saveService');
        $submitReq ->setLabel('save')
            ->setAttrib('id', 'saveRequestedService')
            ->setAttrib('class', 'button-2 save')

            ->setDecorators($this->basicDecorators);

        if($action == 'requestservices') {
            $this->addElements(array($serviceType, $lesson_categoryReq, $subcategory, $description, $submitReq));
        }
        if($action == 'services') {
            $this->addElements(array($serviceType, $lesson_category, $subcategory, $rate, $duration, $description, $submit));
        }
    }
}

?>