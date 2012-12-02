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

    protected function _initCache() {
        /*$cacheFrontendOptions = array(
            'automatic_serialization' => true,
            'cache_id_prefix' => 'translate'
        );
        $cacheBackendOptions = array(
            'cache_dir' => APPLICATION_PATH . '/data/cache/',
            'file_name_prefix' => 'aimya',
            'hashed_directory_level' => 2
        );

        $cache = Zend_Cache::factory('Core', 'File', $cacheFrontendOptions, $cacheBackendOptions);
        Zend_Translate::setCache($cache);
        Zend_Translate::clearCache();*/



        /*     $this->bootstrap('db');
          $cacheFrontendOptions = array (
          'automatic_serialization' => true,
          'cache_id_prefix' => 'z'
          );
          $cacheBackendOptions = array (
          'cache_dir' => APPLICATION_PATH . '/data/cache/',
          'file_name_prefix' => 'uds',
          'hashed_directory_level' => 2
          );
          $cache = Zend_Cache::factory('Core', 'Memcached', $cacheFrontendOptions, $cacheBackendOptions);
          Zend_Registry::set('cache', $cache);
          return $cache;

         */
        /*$frontendOptions = array(
            'lifetime' => 10800,
            'automatic_serialization' => true,
            'debug_header' => true,
            'regexps' => array(
                '^/$' => array('cache' => true),
                '^/cms/' => array('cache' => true),
            ),
        );
        $backendOptions = array('cache_dir' => APPLICATION_PATH . '/data/cache');
        $cachePage = Zend_Cache::factory('Page', 'File', $frontendOptions, $backendOptions);
        $uri      = explode('/', $_SERVER['REQUEST_URI']);
        $cacheKey = array();
        foreach ($uri as $key) {
            if (empty($key)) {
                continue 1;
            }
            $cacheKey[] = $key;
        }

        echo $cachePage->start();


        $this->bootstrap('db');
        $cacheFrontendOptions = array(
            'automatic_serialization' => true,
            'cache_id_prefix' => 'z',
            'lifetime' => 7200
        );
        $cacheBackendOptions = array(
            'cache_dir' => APPLICATION_PATH . '/data/cache/',
            'file_name_prefix' => 'aimya',
            'hashed_directory_level' => 2
        );
        $cache = Zend_Cache::factory('Core', 'File', $cacheFrontendOptions, $cacheBackendOptions);
        Zend_Registry::set('cache', $cache);
        return $cache;*/
    }

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

   /* public function _initConfig(){
        Zend_Registry::set('constants',
            new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/application.ini',
                'constants')
        );
    }*/

    public function _initConfig(){
        Zend_Registry::set('constants',
            new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/application.ini')
        );
    }
}

