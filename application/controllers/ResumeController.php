<?php
class ResumeController extends Zend_Controller_Action implements Aimya_Controller_AccountInterface
{

    public function init()
    {
        $this->_helper->AjaxContext()
            ->addActionContext('ajax', 'json')
            ->initContext('json');
    }

    public function indexAction()
    {
        //basic tab
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $this->view->headScript()->appendFile('../../js/jquery/resume/experience.js');
        $this->view->headScript()->appendFile('../../js/jquery/resume/education.js');
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
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $experienceForm = new Application_Form_ResumeExperience();
        $this->view->experienceForm = $experienceForm;

        $dbExperience = new Application_Model_DbTable_ResumeExperience();
        $this->view->experienceList = $dbExperience -> getExperiences($identity->id);
    }

    public function educationAction()
    {
        $this->_helper->layout()->disableLayout();
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $educationForm = new Application_Form_ResumeEducation();
        $this->view->educationForm = $educationForm;

        $dbEducation = new Application_Model_DbTable_ResumeEducation();
        $this->view->educationList = $dbEducation -> getEducations($identity->id);
    }

    public function skillsAction()
    {
        $this->_helper->layout()->disableLayout();

        $skillsForm = new Application_Form_ResumeSkills();
        $this->view->skillsForm = $skillsForm;
    }

    public function ajaxAction()
    {
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $this->_helper->layout()->disableLayout();

        if ($this->getRequest()->isXmlHttpRequest()){
            $dbExperience = new Application_Model_DbTable_ResumeExperience();
            $dbEducation = new Application_Model_DbTable_ResumeEducation();
            $data = $this->getRequest()->getPost();
            /* EXPERIENCE TAB*/
            if($this->getRequest()->getParam('experience'))  {
                $form = new Application_Form_ResumeExperience();
                if ($form->isValid($data)){
                    $this->view->lastId = $dbExperience -> createExperience($data, $identity->id);
                    $this->view->success = '1';
                }else{

                    $this->view->errors = $form->getErrors();

                }
            }
            if ($this->getRequest()->getParam('deleteExperience')) {
                $dbExperience->deleteExperience($this->getRequest()->getParam('deleteExperience'), $identity->id);
            }
            if ($this->getRequest()->getParam('updateExperience')) {
                $dbExperience->updateExperience($this->getRequest()->getParams(), $identity->id);
            }
            /*END -- EXPERIENCE TAB*/

            /* EDUCATION TAB*/
            if($this->getRequest()->getParam('education'))  {
                $form = new Application_Form_ResumeEducation();
                if ($form->isValid($data)){
                    $this->view->lastId = $dbEducation -> createEducation($data, $identity->id);
                    $this->view->success = '1';
                }else{

                    $this->view->errors = $form->getErrors();

                }
            }
            if ($this->getRequest()->getParam('deleteEducation')) {
                $dbEducation->deleteEducation($this->getRequest()->getParam('deleteEducation'), $identity->id);
            }
            if ($this->getRequest()->getParam('updateEducation')) {
                $dbEducation->updateEducation($this->getRequest()->getParams(), $identity->id);
            }
            /* END -- EDUCATION TAB*/
        }

    }

}