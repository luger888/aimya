<?php
class ResumeController extends Zend_Controller_Action implements Aimya_Controller_AccountInterface
{


    public function init()
    {
        $this->_helper->layout->setLayout("layoutInner");
        $this->_helper->AjaxContext()
            ->addActionContext('ajax', 'json')
            ->addActionContext('upload', 'json')
            ->initContext('json');
    }

    public function indexAction()
    {
        $this->_helper->layout->setLayout("layoutInner");
        //basic tab
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $this->view->headScript()->appendFile('../../js/jquery/resume/resume.js');
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
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $this->_helper->layout()->disableLayout();
        $profile = new Application_Model_DbTable_Profile();

        $objectiveForm = new Application_Form_ResumeObjective();
        $this->view->objectiveForm = $objectiveForm->populate($profile->getProfile($identity->id));
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
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $skillsForm = new Application_Form_ResumeSkills();
        $this->view->skillsForm = $skillsForm;
        if ($this->getRequest()->isPost()) {
            $this->view->succes = 1;
        }

        $dbSkills = new Application_Model_DbTable_ResumeSkills();
        $this->view->skillList = $dbSkills->getSkills($identity->id);
    }

    public function ajaxAction()
    {
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $this->_helper->layout()->disableLayout();


        if ($this->getRequest()->isXmlHttpRequest()) {

            $dbExperience = new Application_Model_DbTable_ResumeExperience();
            $dbEducation = new Application_Model_DbTable_ResumeEducation();
            $dbSkills = new Application_Model_DbTable_ResumeSkills();
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
            if ($this->getRequest()->getParam('deleteexperience')) {
                $dbExperience->deleteExperience($this->getRequest()->getParam('deleteexperience'), $identity->id);
            }
            if ($this->getRequest()->getParam('updateexperience')) {
                $dbExperience->updateExperience($this->getRequest()->getParams(), $identity->id);
            }
            /*END -- EXPERIENCE TAB*/
            /* SKILLS TAB*/

            if ($this->getRequest()->getParam('skill')) {
                $form = new Application_Form_ResumeSkills();
                if ($form->isValid($data)) {
                    $this->view->lastId = $dbSkills->createSkill($data, $identity->id);
                    $this->view->success = '1';
                } else {

                    $this->view->errors = $form->getErrors();

                }
            }
            if ($this->getRequest()->getParam('deleteskill')) {
                $dbSkills->deleteSkill($this->getRequest()->getParam('deleteskill'), $identity->id);
            }
            if ($this->getRequest()->getParam('updateskill')) {
                $dbSkills->updateSkill($this->getRequest()->getParams(), $identity->id);
            }
            /*END -- SKILLS TAB*/
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
            if ($this->getRequest()->getParam('deleteeducation')) {
                $dbEducation->deleteEducation($this->getRequest()->getParam('deleteeducation'), $identity->id);
            }
            if ($this->getRequest()->getParam('updateeducation')) {
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
        $identity = Zend_Auth::getInstance()->getStorage()->read();



        // Set the upload directory
        // $_POST['resumeType']  == experience, education or skill
        $uploadDir = '/img/uploads/' .$identity->id .'/certificate/' . $_POST['resumeType'] .'/'. $_POST['resumeTypeId'] .'/';
        //mkdir('/img/uploads/' .$identity->id .'/' . $_POST['resumeType'] .'/'. $_POST['resumeTypeId'] .'/');
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


            } else {

                // The file type wasn't allowed

            }
        }

    }

}