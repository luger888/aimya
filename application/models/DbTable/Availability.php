 <?php
class Application_Model_DbTable_Availability extends Application_Model_DbTable_Abstract
{
    protected $_name = 'user_availability';

    public function updateAvailability($array, $user_id)
    {
        $serializedArray = serialize($array);
        $data = array(

            'available_at' => $serializedArray

        );

        $this->update($data, 'user_id=?',$user_id);
    }

    public function createAvailability($user_id)
    {
        $data = array(

            'user_id' => $user_id

        );

        $this->insert($data);
    }

    public function getAvailability($user_id)
    {
        $user_id = (int)$user_id;
        $row = $this->fetchRow('user_id = ' . $user_id);
        if(!$row) {
            throw new Exception("There is no element with ID: $user_id");
        }
        $row = unserialize($row['available_at']);
       return $row;

    }

}
