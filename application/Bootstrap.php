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

    /*protected function _initAcl()
    {
        $fc = Zend_Controller_Front::getInstance();
        $fc->registerPlugin(new Application_Plugin_AccessCheck());
    }*/

    /*protected function _initRestRoute() {

        $this->bootstrap ('Request');
        $front = $this->getResource ('FrontController');
        $restRoute = new Zend_Rest_Route ($front, array (), array (
            'default' => array ('user', 'account')
        ));

        $front->getRouter()->addRoute('user', $restRoute );
    }*/

    protected function _initCMSRouter(){

        $frontController = Zend_Controller_Front::getInstance();
        $router = $frontController->getRouter();
        $route = new Zend_Controller_Router_Route_Regex(

            '([a-zA-Z0-9_&\-]+)\.html',

            array(

                'controller' => 'cms',
                'action'     => 'view'

            ),
            array(

                1 => 'uri'

            )

        );

        $router->addRoute('([a-zA-Z0-9_&\-]+)\.html', $route);

    }

    protected function _initUserRouter(){

        $frontController = Zend_Controller_Front::getInstance();
        $router = $frontController->getRouter();
        $route = new Zend_Controller_Router_Route_Regex(
            'user/(\d+)',
            array(
                'controller' => 'user',
                'action'     => 'index'
            ),
            array(
                1 => 'id'
            )

        );

        $router->addRoute('user/(\d+)', $route);

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


}

