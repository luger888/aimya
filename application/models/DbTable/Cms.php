<?php

class Application_Model_DbTable_Cms extends Application_Model_DbTable_Abstract
{

    protected $_name = 'static_page';

    public function createStaticPage($name, $uri, $language, $wysiwyg){

        $data = array(

            'name' => $name,
            'uri' => preg_replace('# #' , '_' , trim($uri)),
            'language' => preg_replace('# #' , '_' , trim($language)),
            'contentCKE' => $wysiwyg,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')

        );

      $this->createItem($data);
    }

    public function updateStaticPage($id, $name, $uri, $language, $content){

        $data = array(

            'name' => $name,
            'uri' => preg_replace('# #' , '_' , trim($uri)),
            'language' => preg_replace('# #' , '_' , trim($language)),
            'contentCKE' => $content,

        );


        $this->updateItem($data , $id);
    }

    public function getPageByUri($uri, $language){

        $data = $this   ->select()
                        ->from('static_page',array('id', 'name' , 'contentCKE', 'language'))
                        ->where('uri=?' , preg_replace('# #' , '_' , trim($uri)))
                        ->where('language=?' , preg_replace('# #' , '_' , trim($language)));

        $userData = $data->query();
        return $userData->fetch();

    }

    public function ifAlreadyExist($id, $uri, $language){

        $data = $this   ->select()
            ->from('static_page',array('id', 'name' , 'contentCKE', 'language'))
            ->where('uri=?' , preg_replace('# #' , '_' , trim($uri)))
            ->where('language=?' , preg_replace('# #' , '_' , trim($language)))
            ->where('id<>?', (int)$id);

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

