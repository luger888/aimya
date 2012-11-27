<?php
class Application_Model_DbTable_ServiceDetail extends Application_Model_DbTable_Abstract
{
    protected $_name = 'service_detail';

    public function addService($array = array(), $id)
    {

        $data = array(

            'user_id' => (int)$id,
            'lesson_category' => $array['lesson_category'],
            'subcategory' => $array['subcategory'],
            'rate' => (int)$array['rate'],
            'duration' => $array['duration'],
            'description' => $array['description'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')

        );

        $this->insert($data);

    }

    public function updateService($array = array(), $id)
    {

        $data = array(

            'lesson_category'=> $array['lesson_category'],
            'subcategory' => $array['subcategory'],
            'rate' => (int)$array['rate'],
            'duration' => $array['duration'],
            'description' => $array['description'],
            'updated_at' => date('Y-m-d H:i:s')

        );
        $where = array(

            $this->getAdapter()->quoteInto('id=?', $array['hiddenId']),
            $this->getAdapter()->quoteInto('user_id=?', $id)

        );
        $this->update($data, $where);

    }

    public function deleteService($id, $user_id)
    {
        $where = array(

            $this->getAdapter()->quoteInto('id =?', (int)$id),
            $this->getAdapter()->quoteInto('user_id=?', (int)$user_id)

        );
        $this->delete($where);

    }

    public function getServiceByUser($user_id)
    {
        $user_id = (int)$user_id;
        $row = $this->fetchAll($this->select()->where('user_id=?' , (int)$user_id));
        if (!$row) {
            throw new Exception("There is no element with ID: $user_id");
        }

        return $row->toArray();
    }


}
