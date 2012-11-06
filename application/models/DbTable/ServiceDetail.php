<?php
class Application_Model_DbTable_ServiceDetail extends Application_Model_DbTable_Abstract
{
    protected $_name = 'service_detail';

    public function addService($array, $id)
    {

        $data = array(

            'user_id' => $id,
            'lesson_category' => $array['lesson_category'],
            'subcategory' => $array['subcategory'],
            'rate' => (int)$array['rate'],
            'duration' => $array['duration'],
            'description' => $array['description'],
            'created_at' => date('Y-m-d H:m:s'),
            'updated_at' => date('Y-m-d H:m:s')

        );

        $this->insert($data);

    }

    public function deleteService($id, $user_id)
    {
        $where = array(

            $this->getAdapter()->quoteInto('id = ?', $id),
            $this->getAdapter()->quoteInto('user_id = ?', $user_id)

        );
        $this->delete($where);

    }

    public function getServiceByUser($user_id)
    {
        $user_id = (int)$user_id;
        $row = $this->fetchAll('user_id = ' . $user_id);
        if (!$row) {
            throw new Exception("There is no element with ID: $user_id");
        }

        return $row->toArray();
    }


}
