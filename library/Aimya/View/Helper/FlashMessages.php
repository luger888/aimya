<?php
/**
 * Created by JetBrains PhpStorm.
 * User: svitla
 * Date: 23.04.12
 * Time: 19:46
 * To change this template use File | Settings | File Templates.
 */

class Aimya_View_Helper_FlashMessages extends Zend_View_Helper_Abstract
{
    public function flashMessages()
    {
        $messages = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getMessages();
        $output = '';

        if (!empty($messages)) {

            foreach ($messages as $message) {
                $output .= '<div id="messages" class="alert alert-block"><div class ="'.key($message).'">
            <button type="button" class="close" data-dismiss="alert"></button>
            <div class="header">'.key($message).'!</div>';
                $output .= '<div>' . current($message) . '</div>';
            }
            $output .= '</div></div>';
        }

        return $output;
    }
}
