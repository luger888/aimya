<?php
class ResumeController extends Zend_Controller_Action implements Aimya_Controller_AccountInterface
{

    public function init()
    {

    }

    public function indexAction()
    {
        //basic tab
        $identity = Zend_Auth::getInstance()->getStorage()->read();

        $servicesModel = new Application_Model_DbTable_ServiceDetail();
        $this->view->services = $servicesModel->getServiceByUser($identity->id);

        $profileModel = new Application_Model_Profile();
        $this->view->profile = $profileModel->getProfileAccount($identity->id);
        $this->view->avatarPath = $profileModel->getAvatarPath($identity->id); //path to avatar
    }

     public function objectiveAction()
     {
         $this->_helper->layout()->disableLayout();

         $objectiveForm = new Application_Form_ResumeObjective();
         $this->view->objectiveForm = $objectiveForm;
     }

    public function experienceAction()
    {
        $this->_helper->layout()->disableLayout();

        $experienceForm = new Application_Form_ResumeExperience();
        $this->view->experienceForm = $experienceForm;
    }

    public function educationAction()
    {
        $this->_helper->layout()->disableLayout();

        $educationForm = new Application_Form_ResumeEducation();
        $this->view->educationForm = $educationForm;
    }

    public function skillsAction()
    {
        $this->_helper->layout()->disableLayout();

        $skillsForm = new Application_Form_ResumeSkills();
        $this->view->skillsForm = $skillsForm;
    }

}