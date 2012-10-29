<?php
/**
 * Created by  Volodymyr Pasika.
 * Date: 15.03.12
 * Time: 9:23
 * Skype: passika_web
 */
require_once APPLICATION_PATH . '/../public/js/ckeditor/ckeditor.php';

class Aimya_View_Helper_Wysiwyg extends Zend_View_Helper_FormElement
{

    public function Wysiwyg($name = null, $value = null, $attribs = null)
    {
        if (is_null($name) && is_null($value) && is_null($attribs)) {
            return $this;
        }
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable

        $editor = new CKEditor();
        //return value and not show in browser
        $editor->returnOutput = true;

        // path to Wysiwyg
        $editor->basePath = '/js/ckeditor/';

        // width
        $editor->config['width'] = 600;

        // $value
        return $editor->editor('content', $value);
    }

}
