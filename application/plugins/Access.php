<?php

class Application_Plugin_Access extends Zend_Controller_Plugin_Abstract
{

    private $_acl = null;
    private $_auth = null;

    const ACCESS_DENIED = 401;

    public function __construct()
    {
        $this->_acl = $this->getRole();
        $this->_auth = Zend_Auth::getInstance();
    }

    protected function getRole()
    {
        $acl = new Zend_Acl();

        $acl->addRole('guest');
        $acl->addRole('pending' , 'guest');
        $acl->addRole('user', 'pending');
        $acl->addRole('admin', 'user');

        $acl->addResource('index');
        $acl->addResource('user');
        $acl->addResource('cms');
        $acl->addResource('error');

        #allow to user
        $acl->deny('user' , 'user', array('index', 'registration'));

        #allow to guest
        $acl->allow('guest' , 'index', array('index'));
        $acl->allow('guest' , 'user', array('index','registration', 'login'));
        $acl->allow('guest' , 'error', array('index'));


        Zend_Registry::set('Zend_Acl',$acl);

        $identity = Zend_Auth::getInstance()->getStorage()->read();

        $role = !empty($identity->role) ? $identity->role : 'guest';
        Zend_Registry::set('currentRole',$role);

        //Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl($acl);

        return $acl;

    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {

        // get current controller
        $resource = $request->getControllerName();

        // get current action
        $action = $request->getActionName();

        // get role
        $identity = $this->_auth->getStorage()->read();

        $role = !empty($identity->role) ? $identity->role : 'guest';

        //Zend_View_Helper_Navigation::setRole($role);
        //echo $role .' '. $resource .' '. $action . '<br>';
        if (!$this->_acl->isAllowed($role, $resource, $action)) {

            $request->setControllerName('error')->setActionName('page404');
            //throw new Zend_Acl_Exception("This page is not accessible.", Application_Plugin_Access::ACCESS_DENIED);

        }
    }
}


