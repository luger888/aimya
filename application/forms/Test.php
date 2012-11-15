<?php

class Application_Form_Test extends Zend_Form
{

    public function init()
    {

        $controller = new Zend_Form_Element_Text('controller');
        $controller     ->setLabel('controller')
                        ->setRequired(true)
                        ->addFilter('StripTags')
                        ->addFilter('StringTrim')
                        ->addValidator('NotEmpty')
                        ->setAttrib('id', 'controller');

        $array = array('0'=>'GET', '1'=>'POST', '2'=>'PUT', '3'=>'DELETE');
        $method = new Zend_Form_Element_Select('method');
        $method     ->setLabel('method')
                    ->setRequired(true)
                    ->addMultiOptions($array)
                    ->setValue('0')
                    ->setAttrib('id', 'method');

        $data = new Zend_Form_Element_Textarea('data');
        $data   ->setLabel('data')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty')
                ->setAttrib('rows', '5')
                ->setAttrib('cols', '20')
                ->setDescription('Please enter GET data in format "/key/value/" and other dato in format "key:value"')
                ->setAttrib('id', 'data');

        $submit = new Zend_Form_Element_Submit('Send');
        $submit ->setAttrib('id', 'send');

        $this->addElements(array($controller, $method, $data, $submit));

    }


}

