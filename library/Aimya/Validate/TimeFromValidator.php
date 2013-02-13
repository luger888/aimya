<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 29.01.13
 * Time: 14:32
 * To change this template use File | Settings | File Templates.
 */

class Aimya_Validate_TimeFromValidator extends Zend_Validate_Abstract
{
    const NOT_LESS = 'Not valid!';

    protected $_messageTemplates = array(
        self::NOT_LESS => "Not valid!"
    );

    protected $_messageVariables = array(
        'field' => '_field'
    );

    protected $_field;

    protected $_from;

    public function __construct( $from )
    {
        $this->_from = $from;
    }

    public function isValid( $value, $context = null )
    {
        if ($value == '...' || $this->_from->getValue() == '...'){
            $res =  $value == $this->_from->getValue();
            if (!$res)     $this->_error(self::NOT_LESS);
            return $res;
        }
        $hour = substr($value,0,strlen($value) - 3);
        if (substr($value,strlen($value) - 2, 2) == 'pm'){
          $hour = $hour +12;
        }
        $fromhour = substr($this->_from->getValue(),0,strlen($value) - 3);
        if (substr($this->_from->getValue(),strlen($value) - 2, 2) == 'pm'){
            $fromhour = $fromhour +12;
        }
        if ($hour <= $fromhour) {
            $this->_error(self::NOT_LESS);
            return false;
        }else{
            return true;
        }
    }

}
