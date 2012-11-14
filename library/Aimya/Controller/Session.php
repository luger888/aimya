<?php
class Aimya_Controller_Session extends Zend_Controller_Request_Abstract
{
    public function checkSessionIdInPost(){
        //$phpSessionId = "tgpae3nf7oe20rbkuvnib6a4g7";
        $phpSessionId = $this->getParam('PHPSESSID');
        if ($phpSessionId){
            if (!empty($phpSessionId) && Zend_Session::getId() != $phpSessionId) {
                if(Zend_Session::isStarted()) {
                    Zend_Session::destroy();
                }
                Zend_Session::setId($phpSessionId);
                Zend_Session::start();
            }
        }
    }
}