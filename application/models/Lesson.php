<?php

class Application_Model_Lesson
{
    public function createFlashParams($array = array()) {
        $result = array();
        if(is_array($array)) {
            foreach($array as $key=>$value) {
                $value = md5($value . time());
                $result[$key] = $value;
            }
            return $result;
        } else {
            return false;
       }
    }

    public function createPresentationPath($lessonId) {

        $identityId = Zend_Auth::getInstance()->getIdentity()->id;
        @mkdir(realpath(APPLICATION_PATH . '/../public/') . DIRECTORY_SEPARATOR . 'presentatio');
        @mkdir(realpath(APPLICATION_PATH . '/../public/') . DIRECTORY_SEPARATOR . 'presentatio' . DIRECTORY_SEPARATOR . $identityId);
        $presPath = realpath(APPLICATION_PATH . '/../public/') . DIRECTORY_SEPARATOR . 'presentatio' . DIRECTORY_SEPARATOR . $identityId . DIRECTORY_SEPARATOR . $lessonId . DIRECTORY_SEPARATOR;
        @mkdir($presPath);

        return $presPath;
    }

    public function getImages($lessonId) {
        $lessonTable = new Application_Model_DbTable_Lesson();
        $lessonData = $lessonTable->getItem($lessonId);
        $imagesPath = realpath(APPLICATION_PATH . '/../public/') . DIRECTORY_SEPARATOR . 'presentatio' . DIRECTORY_SEPARATOR . $lessonData['creator_id'] . DIRECTORY_SEPARATOR . $lessonId . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
        $imageNames = scandir($imagesPath);
        $imagePath = array();
        foreach($imageNames as $name) {
            $imagePath[] = $imagesPath . $name;
        }

        return $imagePath;
    }


    public function delTree($dir) {
        $files = glob( $dir . '*', GLOB_MARK );
        foreach( $files as $file ){
            if( substr( $file, -1 ) == '/' )
                delTree( $file );
            else
                unlink( $file );
        }
        rmdir( $dir );
    }
}