<?php

/**
 * Converts an email address to the username.
 */
class Aimya_Filter_EmailToUsername implements Zend_Filter_Interface
{
    public function filter( $value )
    {
        if ( Zend_Validate::is($value, 'EmailAddress') ) {

            $user_table = new Application_Model_DbTable_Users();
            $user = $user_table->checkByMail($value);
            if ( $user ) {
                return $user['username'];
            }
        }

        /**
         * Nothing happened, so don't filter.
         */
        return $value;
    }
}