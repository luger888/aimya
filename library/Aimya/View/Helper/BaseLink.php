<?php
/**
 * Created by JetBrains PhpStorm.
 * User: svitla
 * Date: 11.05.12
 * Time: 14:21
 * To change this template use File | Settings | File Templates.
 */

class Aimya_View_Helper_BaseLink extends Zend_View_Helper_Abstract
{
    /**
     * get base unsecure url of website
     */

    public function baseLink(){
        if($_SERVER) {
            return "https://" . $_SERVER['HTTP_HOST'];
        }
    }

}