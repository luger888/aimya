<?php
class ResumeController extends Zend_Controller_Action implements Aimya_Controller_AccountInterface
{

    public function init()
    {
        $this->_helper->layout->setLayout("layoutInside");
        $this->_helper->AjaxContext()
            ->addActionContext('ajax', 'json')
            ->addActionContext('upload', 'json')
            ->initContext('json');
    }

    public function indexAction()
    {
        $this->_helper->layout->setLayout("layoutInside");
        //basic tab
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $this->view->headScript()->appendFile('../../js/jquery/resume/experience.js');
        $this->view->headScript()->appendFile('../../js/jquery/resume/education.js');
        $this->view->headScript()->appendFile('../../js/jquery/resume/objective.js');
        $this->view->headScript()->appendFile('../../js/jquery/jquery.uploadifive.js');
        $servicesModel = new Application_Model_DbTable_ServiceDetail();
        $this->view->services = $servicesModel->getServiceByUser($identity->id, 1);
        $this->view->servicesOffered = $servicesModel->getServiceByUser($identity->id, 2);

        $profileModel = new Application_Model_Profile();
        $this->view->profile = $profileModel->getProfileAccount($identity->id);
        $this->view->avatarPath = $profileModel->getAvatarPath($identity->id, 'base'); //path to avatar
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
        $this->view->experienceList = $dbExperience->getExperiences($identity->id);
    }

    public function educationAction()
    {
        $this->_helper->layout()->disableLayout();
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $educationForm = new Application_Form_ResumeEducation();
        $this->view->educationForm = $educationForm;

        $dbEducation = new Application_Model_DbTable_ResumeEducation();
        $this->view->educationList = $dbEducation->getEducations($identity->id);
    }

    public function skillsAction()
    {
        $this->_helper->layout()->disableLayout();

        $skillsForm = new Application_Form_ResumeSkills();
        $this->view->skillsForm = $skillsForm;
        if ($this->getRequest()->isPost()) {
            $this->view->succes = 1;
        }
    }

    public function ajaxAction()
    {
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $this->_helper->layout()->disableLayout();


        if ($this->getRequest()->isXmlHttpRequest()) {

            $dbExperience = new Application_Model_DbTable_ResumeExperience();
            $dbEducation = new Application_Model_DbTable_ResumeEducation();
            $dbProfile = new Application_Model_DbTable_Profile();
            $data = $this->getRequest()->getPost();

            /* EXPERIENCE TAB*/

            if ($this->getRequest()->getParam('experience')) {
                $form = new Application_Form_ResumeExperience();
                if ($form->isValid($data)) {
                    $this->view->lastId = $dbExperience->createExperience($data, $identity->id);
                    $this->view->success = '1';
                } else {

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
            if ($this->getRequest()->getParam('education')) {
                $form = new Application_Form_ResumeEducation();
                if ($form->isValid($data)) {
                    $dbEducation->createEducation($data, $identity->id);
                    $this->view->success = '1';
                } else {

                    $this->view->errors = $form->getErrors();

                }
            }
            if ($this->getRequest()->getParam('deleteEducation')) {
                $dbEducation->deleteEducation($this->getRequest()->getParam('deleteEducation'), $identity->id);
            }
            if ($this->getRequest()->getParam('updateEducation')) {
                $dbEducation->updateEducation($this->getRequest()->getParams(), $identity->id);
            }
            /*  END -- EDUCATION TAB*/
            /*  OBJECTIVE TAB*/
            if ($this->getRequest()->getParam('objective')) {
                $form = new Application_Form_ResumeObjective();
                if ($form->isValid($data)) {
                    $this->view->lastId = $dbProfile->updateObjective($data, $identity->id);
                } else {
                    $this->view->errors = $form->getErrors();
                }

            }
            /*  END --  OBJECTIVE TAB*/


        }

    }

    public function uploadAction()
    {

        // Set the upload directory
        $uploadDir = '/img/uploads/';

        // Set the allowed file extensions
        $fileTypes = array('jpg', 'jpeg', 'gif', 'png'); // Allowed file extensions

        if (!empty($_FILES)) {
            $tempFile = $_FILES['Filedata']['tmp_name'];
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . $uploadDir;
            $targetFile = $uploadDir . $_FILES['Filedata']['name'];

            // Validate the filetype
            $fileParts = pathinfo($_FILES['Filedata']['name']);
            if (in_array(strtolower($fileParts['extension']), $fileTypes)) {

                // Save the file
                move_uploaded_file($tempFile, $targetFile);
                echo 1;

            } else {

                // The file type wasn't allowed
                echo 'Invalid file type.';

            }
        }

    }

}