<?php
class Application_Model_DbTable_ServiceDetail extends Application_Model_DbTable_Abstract
{
    protected $_name = 'service_detail';

    public function addService($array = array(), $id)
    {

        $data = array(

            'user_id' => (int)$id,
            'lesson_category' => preg_replace('#<(.*?)>#', '', $array['lesson_category']),
            'service_type' => preg_replace('#<(.*?)>#', '', $array['service_type']),
            'subcategory' => preg_replace('#<(.*?)>#', '', trim($array['subcategory'])),
            'description' => preg_replace('#<(.*?)>#', '', $array['description']),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')

        );

        if($array['service_type'] == 1) {
            $data['rate'] = (int)$array['rate'];
            $data['duration'] = (int)$array['duration'];
        }

        $this->insert($data);

    }

    public function updateService($array = array(), $id)
    {

        $data = array(
            'service_type' => preg_replace('#<(.*?)>#', '', $array['service_type']),
            'lesson_category'=> preg_replace('#<(.*?)>#', '', $array['lesson_category']),
            'subcategory' => preg_replace('#<(.*?)>#', '', $array['subcategory']),
            'description' => preg_replace('#<(.*?)>#', '', $array['description']),
            'updated_at' => date('Y-m-d H:i:s')

        );

        if($array['service_type'] == 1) {
            $data['rate'] = (int)$array['rate'];
            $data['duration'] = (int)$array['duration'];
        }
        $where = array(

            $this->getAdapter()->quoteInto('id=?', $array['updateService']),
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

    public function getServiceByUser($user_id, $serviceType)
    {
        $user_id = (int)$user_id;
        $row = $this->fetchAll(
            $this->select()
                ->where('(' . $this->getAdapter()->quoteInto('user_id=?' , (int)$user_id) . ') AND (' . $this->getAdapter()->quoteInto('service_type=?' , (int)$serviceType) . ')')
        );


        return $row->toArray();
    }

    public function getServices()
    {
        $row = $this->fetchAll(
            $this->select()
        );
        if (!$row) {
            throw new Exception("There is no any services");
        }

        return $row->toArray();
    }

}
