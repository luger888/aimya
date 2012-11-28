<?php

class Application_Model_DbTable_Cms extends Application_Model_DbTable_Abstract
{

    protected $_name = 'static_page';

    public function createStatisPage($name, $uri, $wysiwyg){

        $data = array(

            'id' => 'NULL',
            'name' => $name,
            'uri' => preg_replace('# #' , '_' , trim($uri)),
            'content' => $wysiwyg,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')

        );

        $this->createItem($data);

    }

    public function updateStaticPage($id, $name, $uri, $content){

        $data = array(

            'name' => $name,
            'uri' => preg_replace('# #' , '_' , trim($uri)),
            'content' => $content,

        );


        $this->updateItem($data , $id);
    }

    public function getPageByUri($uri){

        $data = $this   ->select()
                        ->from('static_page',array('name' , 'content'))
                        ->where('uri=?' , $uri);

        $userData = $data->query();
        return $userData->fetch();

    }



}

