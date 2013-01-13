<?php
class Application_Model_DbTable_LessonCategory extends Application_Model_DbTable_Abstract
{
    protected $_name = 'lesson_category';

    public function getLessonCategories(){

        $data = $this   ->select()
            ->from('lesson_category', (array('id', 'title', 'status')));

        return $data->query()->fetchAll();

    }
    public function removeCat($id)
    {
        $data = array(
            'status' => 0,
            'updated_at' => date('Y-m-d H:i:s')

        );
        $where = array(
            $this->getAdapter()->quoteInto('id=?', $id)

        );
        return $this->update($data, $where);

    }

    public function  addCats($cats){

        for($i=0;$i<count($cats['categories']);$i++){
            $data = array(
                'title' => $cats['categories'][$i],
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')

            );

            $this->insert($data);
        }

    }
}
