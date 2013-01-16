<?php

class Application_Form_Cms extends Zend_Form
{

    public function init()
    {
        #hidden field for editing cms pages
        $id = new Zend_Form_Element_Hidden('id');

        $name = new Zend_Form_Element_Text('name');
        $name  ->setLabel('Page Name')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty')
            ->setAttrib('id', 'cmsPageName')
            ->addValidator('stringLength', false, array(2, 50));

        $uri =  new Zend_Form_Element_Text('uri');
        $uri   ->setLabel('Uri')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty')
            ->setAttrib('id', 'cmsUri')
            ->addValidator('stringLength', false, array(2, 50));

        $content = new Aimya_Form_Element_Wysiwyg('contentCKE');
       // $content->setLabel('Page content');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'cmsSubmit')
                ->setAttrib('class', 'button floatRight');

        $this->addElements(array($id, $name, $uri, $content, $submit));

    }


}

