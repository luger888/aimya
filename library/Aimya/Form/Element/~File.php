<?php

class Aimya_Form_Element_File extends Aimya_Form_Element_Uploadify
{
    public function setup()
    {
        $elementID = $this->getId();
        
        $options = array('auto'             => false,
                         'formData'         => "{'test' : 'something'}",
                         'queueID'          => 'queue',
                         'uploadScript'     => 'uploadifive.php',
				         'onUploadComplete' => '',
                         /*'uploader'     => '/flash/uploadify.swf',
            			 'cancelImg'    => '/images/public/cancel.png',
            			 'onSelect'	    => 'function() { $(\'#'.$elementID.'Upload\').show(); }',
            			 'onCancel'     => 'function() { $(\'#'.$elementID.'Upload\').hide(); }',
            			 'onComplete'   => 'function() { $(\'#'.$elementID.'Upload\').hide().parents(\'form:first\').submit(); }',
                         'myShowUpload' => false*/
                   );
        $this->getView()->headLink()->appendStylesheet('/css/uploadify/uploadifive.css', 'screen');
        $this->getView()->headScript()->appendFile('/js/uploadify/jquery.uploadifive.js')
                                      //->appendFile('/js/swfobject.js')
                                      ->appendScript($this->getJavaScript($options));

        $this->addFilter('rename', $this->getRandomFileName());
        
        /*$baseDir = realpath(APPLICATION_PATH . '/../public/media');
        print_r($_FILES);
        echo "<br>";
        if(file_exists($baseDir)){
            echo "OK";
        } else {
            echo "nima";
        }*/
        
        //configs/application.ini
    }
}