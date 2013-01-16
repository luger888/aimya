<?php

class Application_Model_DbTable_Cms extends Application_Model_DbTable_Abstract
{

    protected $_name = 'static_page';

    public function createStaticPage($name, $uri, $wysiwyg){

        $data = array(

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
                        ->from('static_page',array('id', 'name' , 'content'))
                        ->where('uri=?' , preg_replace('# #' , '_' , trim($uri)));

        $userData = $data->query();
        return $userData->fetch();

    }

    public function deletePage($id)
    {
        $where = array(

            $this->getAdapter()->quoteInto('id =?', (int)$id)
        );
        $this->delete($where);

    }

}

