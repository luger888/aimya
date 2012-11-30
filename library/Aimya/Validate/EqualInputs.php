<?php
class Aimya_Validate_NotEqual extends Zend_Validate_Abstract {

    const EQUAL = 'equal';

    protected $_value = null;

    protected $_messageTemplates = array(
        self::EQUAL      => "'%value%' is an incorrect value"
    );

    public function __construct($value) {
        $this->_value = $value;
    }

    public function isValid($value) {
        if ($value == $this->_value) {
            $this->_error(self::EQUAL);
            return false;
        }

        return true;
    }
}
?>