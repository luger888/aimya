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

    protected function _initRouter(){

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


}

