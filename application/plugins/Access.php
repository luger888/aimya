<?php

class Application_Plugin_Access extends Zend_Controller_Plugin_Abstract
{
    
    const GUEST = '0';
    const STUDENT = '1';
    const TEACHER = '2';
    const ADMIN = '3';

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

        $acl->addRole(self::GUEST);
        $acl->addRole(self::STUDENT , self::GUEST);
        $acl->addRole(self::TEACHER, self::STUDENT);
        $acl->addRole(self::ADMIN, self::TEACHER);

        $acl->addResource('index');
        $acl->addResource('user');
        $acl->addResource('cms');
        $acl->addResource('error');
        $acl->addResource('account');

        #allow to user
        $acl->allow(self::STUDENT , 'user', array('logout'));
        $acl->allow(self::STUDENT , 'account', array('index'));
        $acl->deny(self::STUDENT , 'user', array('index', 'registration', 'login'));

        #allow to guest
        $acl->deny(self::GUEST , 'user', array('logout'));
        $acl->deny(self::GUEST , 'account', array('index'));
        $acl->allow(self::GUEST , 'index', array('index'));
        $acl->allow(self::GUEST , 'user', array('index','registration', 'login'));
        $acl->allow(self::GUEST , 'error', array('index'));


        Zend_Registry::set('Zend_Acl',$acl);

        $identity = Zend_Auth::getInstance()->getStorage()->read();

        $role = !empty($identity->role) ? $identity->role : self::GUEST;
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

        $role = !empty($identity->role) ? $identity->role : self::GUEST;

        //Zend_View_Helper_Navigation::setRole($role);
        //echo $role .' '. $resource .' '. $action . '<br>';
        if (!$this->_acl->isAllowed($role, $resource, $action)) {

            #$request->setControllerName('error')->setActionName('page404');
            //throw new Zend_Acl_Exception("This page is not accessible.", Application_Plugin_Access::ACCESS_DENIED);

        }
    }
}


