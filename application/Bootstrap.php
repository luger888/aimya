<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    function _initViewRes() {

        $this->bootstrap('view');
        $this->bootstrap('layout');
        $layout=$this->getResource('layout');
        $view=$layout->getView();

        $view->addHelperPath("Aimya/View/Helper", "Aimya_View_Helper");
        return $view;
    }

    protected function _initACL()
    {
        $session = new Aimya_Controller_Session();
        $session->checkSessionIdInPost();

        $fc = Zend_Controller_Front::getInstance();
        $fc -> registerPlugin(new Application_Plugin_Access());

    }
    protected function _initRequest() {

        $this->bootstrap ('FrontController' );
        $front = $this->getResource ('FrontController');
        $request = $front->getRequest();

        if (null === $front->getRequest()) {
            $request = new Zend_Controller_Request_Http ();
            $front->setRequest ($request);
        }

        return $request;
    }

    protected function _initActivity() {

        if(Zend_Auth::getInstance()->getIdentity()) {
            $this->bootstrap('db');

            $userId = Zend_Auth::getInstance()->getIdentity()->id;
            $onlineUserTable = new Application_Model_DbTable_OnlineUsers();
            $onlineUserTable->makeOnline($userId);
        }
    }

    protected function _initRoutes(){

        $frontController = Zend_Controller_Front::getInstance();
        $router = $frontController->getRouter();

        $langRoute = new Zend_Controller_Router_Route(
            '/:lang',
            array(
                'lang' => 'ja'
            ),
            array(
                'lang' => '[a-z]{2}'
            )
        );

        $defaultRoute = new Zend_Controller_Router_Route(
            ':module/:controller/:action/*',
            array(
                'module'=>'default',
                'controller'=>'index',
                'action'=>'index'
            )
        );

        $cmsRoute = new Zend_Controller_Router_Route_Regex(
            '([a-zA-Z0-9_&\-]+)\.html',
            array(
                'controller' => 'cms',
                'action'     => 'view'
            ),
            array(
                1 => 'uri'
            )

        );
        //$router->addRoute('([a-zA-Z0-9_&\-]+)\.html', $cmsRoute);

        $userRoute = new Zend_Controller_Router_Route_Regex(
            'user/(\d+)',
            array(
                'controller' => 'user',
                'action'     => 'index'
            ),
            array(
                1 => 'id'
            )
        );
        //$router->addRoute('user/(\d+)', $userRoute);

        $messageRoute = new Zend_Controller_Router_Route_Regex(
            'message/view/(\d+)',
            array(
                'controller' => 'message',
                'action'     => 'view'
            ),
            array(
                1 => 'message_id',
                //2 => 'new'
            )
        );

        $defaultRoute = $langRoute->chain($defaultRoute);
        $cmsRoute = $langRoute->chain($cmsRoute);
        $userRoute = $langRoute->chain($userRoute);
        $messageRoute = $langRoute->chain($messageRoute);
        $defaultRoute = $langRoute->chain($defaultRoute);

        $router->addRoute('langRoute', $langRoute);
        $router->addRoute('defaultRoute', $defaultRoute);
        $router->addRoute('cmsRoute', $cmsRoute);
        $router->addRoute('userRoute', $userRoute);
        $router->addRoute('messageRoute', $messageRoute);

    }

    public function _initNavigation()
    {

        $this -> bootstrap('view');
        $view = $this->getResource('view');
        $navigation = new Aimya_Navigation(new Zend_Config(require APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'navigation.php'));

        Zend_Registry::set('Zend_Navigation', $navigation);

        $acl = Zend_Registry::get('Zend_Acl');

        $view -> navigation($navigation)
            ->setAcl($acl)
            ->setRole(Zend_Registry::get('currentRole'));
    }


    public function _initConfig(){
        Zend_Registry::set('constants',
            new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/application.ini')
        );
    }
}

