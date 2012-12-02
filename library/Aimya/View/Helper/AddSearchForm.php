<?php
class Aimya_View_Helper_AddSearchForm extends Zend_View_Helper_Abstract
{
    public function addSearchForm() {
        return new Application_Form_Search();
    }
}