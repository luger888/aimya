<?php

class Aimya_Form_Element_File extends Aimya_Form_Element_Uploadify
{
    public function setup()
    {
        $options = array('uploader'     => '/flash/uploadify.swf',
            'cancelImg'    => '/images/cancel.png',
            'onSelect'	    => 'function() {}',
            'onCancel'     => 'function() {}',
            'onComplete'   => 'function() { $(\'#'.$this->getId().'Upload\').parents(\'form:first\').submit(); }',
            'myShowUpload' => true
        );
        $this->getView()->headLink()->appendStylesheet('/styles/uploadify.css', 'screen');
        $this->getView()->headScript()->appendFile('/scripts/jquery.uploadify.v2.1.0.min.js')
            ->appendFile('/scripts/swfobject.js')
            ->appendScript($this->getJavaScript($options));
        // Rename uploaded file
        $this->addFilter('rename', $this->getRandomFileName());
    }
}