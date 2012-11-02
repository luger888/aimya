<?php
class Application_Model_DbTable_LessonCategory extends Application_Model_DbTable_Abstract
{
    protected $_name = 'lesson_category';

    public function getLessonCategories(){

        $data = $this   ->select()
            ->from('lesson_category', (array('title')));

        return $data->query()->fetchAll();

    }

}
