<?php

class Aimya_Controller_Plugin_LangSelector extends Zend_Controller_Plugin_Abstract {

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $lang = $request->getParam('lang', '');
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
        /*Zend_Registry::set('aimya_lang', $lang);
        $langm = new Admin_Model_LanguageMapper();
        $lang_id = $langm->getLanguageId();
        Zend_Registry::set('aimya_lang_id', $lang_id);*/
    }



}