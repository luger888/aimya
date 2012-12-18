<?php

class Aimya_Controller_Plugin_LangSelector extends Zend_Controller_Plugin_Abstract {

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $uri = ltrim($_SERVER["REQUEST_URI"], "/");
        $lang = substr($uri, 0, strpos($uri, "/"));
        if($lang == "") {
            $lang = "en";
        }

        try {
            $locale = new Zend_Locale(Zend_Locale::BROWSER);
        } catch (Exception $e) {
            $locale = new Zend_Locale();
        }

        /*         * ********************** */
        if ($lang == 'en')
            $loc = 'en_US';
        else if ($lang == 'ja')
            $loc = 'ja_JP';
        else if ($lang == 'zh')
            $loc = 'zh_CN';
        else {
            //$lang = $locale->getLanguage();
            $lang = 'en';
            $loc = 'en_US';
        }
        /*         * ********************** */
        $locale->setLocale($lang);
        Zend_Registry::set('Zend_Locale', $loc);


        try {
            $translate = new Zend_Translate('csv', APPLICATION_PATH . DIRECTORY_SEPARATOR .'configs'. DIRECTORY_SEPARATOR .'lang' . DIRECTORY_SEPARATOR . $lang . '.csv');
        } catch (Exception $e) {
            $translate = new Zend_Translate('csv', APPLICATION_PATH . DIRECTORY_SEPARATOR .'configs'. DIRECTORY_SEPARATOR .'lang' . DIRECTORY_SEPARATOR . 'en.csv');
        }

        Zend_Registry::set('Zend_Translate', $translate);

        $uri = ltrim($_SERVER["REQUEST_URI"], "/");
        $module = substr($uri, 0, strpos($uri, "/"));
        if($module == "") {
            $module = "en";
        }


        $controller = Zend_Controller_Front::getInstance();

        $controller->setBaseUrl('/' . $module); // set the base url!

    }



}