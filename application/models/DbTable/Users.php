<?php

class Application_Model_DbTable_Users extends Application_Model_DbTable_Abstract
{

    protected $_name = 'user';

    #check email to unique

    public function checkByMail($email)
    {

        $data = $this->select()
            ->from('user', array('id', 'username', 'firstname', 'lastname', 'password'))
            ->where('email=?', $email);

        return $data->query()->fetch();

    }

    public function checkByUsername($username)
    {

        $data = $this->select()
            ->from('user', array('id', 'firstname', 'lastname', 'password'))
            ->where('username=?', $username);

        return $data->query()->fetch();

    }

    public function createUser($array = array())
    {


        $status = (isset($array['status'])) ? 1 : 0;
        $password = (isset($array['password'])) ? md5($array['password']) : '';
        $token = (isset($array['token'])) ? $array['token'] : '';


        $data = array(

            'email' => preg_replace('#<(.*?)>#', '', trim($array['email'])),
            'password' => preg_replace('#<(.*?)>#', '', $password),
            'role' => (int)$array['role'],
            'username' => preg_replace('#<(.*?)>#', '', trim($array['username'])),
            'firstname' => preg_replace('#<(.*?)>#', '', $array['firstname']),
            'lastname' => preg_replace('#<(.*?)>#', '', $array['lastname']),
            'gender' => preg_replace('#<(.*?)>#', '', $array['gender']),
            'status' => $status,
            'confirmation_token' => preg_replace('#<(.*?)>#', '', $token),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')

        );

        $this->insert($data);

    }

    #Approve by e-mail

    public function approve($mail, $query)
    {

        $array = array(

            'status' => '1',
            'confirmation_token' => ''

        );

        $where[] = $this->getAdapter()->quoteInto('email=?', $mail);
        $where[] = $this->getAdapter()->quoteInto('confirmation_token=?', $query);

        return $this->update($array, $where);

    }

    #recoverPass

    public function recoverPass($data)
    {

        $array = array(

            'password' => preg_replace('#<(.*?)>#', '', md5($data['password']))

        );
        $where = $this->getAdapter()->quoteInto('email=?', $data['email']);


        $this->update($array, $where);

    }

    public function getUser($user_id)
    {
        $user_id = (int)$user_id;
        $row = $this->fetchRow($this->select()->where('id = ?', $user_id));
        if (!$row) {
            throw new Exception("There is no element with ID: $user_id");
        }

        return $row->toArray();
    }

    public function getUserInfo($user_id)
    {
        $data = $this->select()
            ->from('user', array('id', 'firstname', 'lastname', 'username', 'timezone', 'role'))
            ->where('id=?', (int)$user_id);

        return $data->query()->fetch();
    }

    public function updateUser($array = array(), $id)
    {

        $data = array(

            'firstname' => preg_replace('#<(.*?)>#', '', $array['firstname']),
            'lastname' => preg_replace('#<(.*?)>#', '', $array['lastname']),
            'gender' => preg_replace('#<(.*?)>#', '', $array['gender']),
            'timezone' => preg_replace('#<(.*?)>#', '', $array['timezone']),
            'email' => preg_replace('#<(.*?)>#', '', $array['email']),
            'username' => preg_replace('#<(.*?)>#', '', $array['username']),
            'updated_at' => date('Y-m-d H:i:s')

        );
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $this->update($data, $where);

    }

    public function getLatestFeatured($role = '0', $category = 'All', $offset = 0, $count = 5)
    {

        $userId = Zend_Auth::getInstance()->getIdentity()->id;

        $subQuery = $this->getAdapter()->select()
            ->from(array('service_detail'), array('MAX(updated_at)'))
            ->group('user_id')
            ->having('user_id = sd.user_id');

        $data = $this->getAdapter()->select()
            ->from(array('sd' => 'service_detail'), array('sd.lesson_category', 'sd.updated_at'))
            ->joinLeft('user', 'user.id = sd.user_id', array('user.id', 'user.firstname', 'user.lastname', 'user.username', 'user.role', 'user.timezone'))
            ->joinLeft('account', 'account.user_id = sd.user_id', array('account.add_info'))
            ->where('sd.updated_at=?', $subQuery);

        if (Zend_Auth::getInstance()->getIdentity()->role == 1) $role = 2;
        if ($role !== '0') {
            $data->where('user.role=?', $role);
        }
        if ($category !== 'All') {
            $data->where('sd.lesson_category=?', $category);
        }
        $data->order('sd.updated_at desc')
            ->limit($count, $offset);

        $author = $data->query()->fetchAll();

        foreach ($author as $index => $value) {
            $serviceOffered = $this->getAdapter()
                ->select()
                ->from('service_detail')
                ->where('user_id=?', $value['id'])
                ->where('service_type=?', 1);

            $serviceRequested = $this->getAdapter()
                ->select()
                ->from('service_detail')
                ->where('user_id=?', $value['id'])
                ->where('service_type=?', 2);


            $servicesOffered = $serviceOffered->query()->fetchAll();
            $servicesRequested = $serviceRequested->query()->fetchAll();

            $author[$index]['service_offered'] = $servicesOffered;
            $author[$index]['service_requested'] = $servicesRequested;

        }

        return $author;
    }

    public function getFullData($id)
    {

        $data = $this->getAdapter()
            ->select()
            ->from($this->_name)
        //->joinLeft('account', $where)
            ->joinLeft('account', 'account.user_id = user.id')
            ->where('user.id=?', $id);
        $services = $this->getAdapter()
            ->select()
            ->from('service_detail')
            ->where('service_detail.user_id=?', $id);

        $servicesResult = $services->query()->fetchAll();

        $result = $data->query()->fetch();
        $result['services'] = $servicesResult;
        $result['id'] = $id;

        return $result;
    }

    public function getTimeZone()
    {
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $data = $this->select()
            ->from('user', array('timezone'))
            ->where('id=?', (int)$userId);

        return $data->query()->fetch();

    }

    public function setDefaultTimezone($timezone)
    {
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $data = array(
            'timezone' => preg_replace('#<(.*?)>#', '', $timezone),
        );

        $where = $this->getAdapter()->quoteInto('id = ?', (int)$userId);
        $result = $this->update($data, $where);

        return $result;

    }

    public function getFeaturedCount($role = '0', $category = 'All')
    {

        $userId = Zend_Auth::getInstance()->getIdentity()->id;

        $subQuery = $this->getAdapter()->select()
            ->from(array('service_detail'), array('MAX(updated_at)'))
            ->group('user_id')
            ->having('user_id = sd.user_id');

        $data = $this->getAdapter()->select()
            ->from(array('sd' => 'service_detail'), array('featuredCount' => 'count(sd.updated_at)'))
            ->joinLeft('user', 'user.id = sd.user_id', array('user.id'))
            ->where('sd.updated_at=?', $subQuery);

        if (Zend_Auth::getInstance()->getIdentity()->role == 1) $role = 1;
        if ($role !== '0') {
            $data->where('user.role=?', $role);
        }
        if ($category !== 'All') {
            $data->where('sd.lesson_category=?', $category);
        }

        return $data->query()->fetch();
    }

    public function getUsersCount()
    {

        $lastMonth = date("Y-m-d H:i:s", strtotime("-1 month"));
        $usersCount = array();
        $total = $this->getAdapter()->select()
            ->from($this->_name, 'COUNT(*) AS COUNT');
        $result = $total->query()->fetch();

        $students = $this->getAdapter()->select()
            ->from($this->_name, 'COUNT(*) AS COUNT')
            ->where('role = ?', 1);
        $studentRes = $students->query()->fetch();

        $teachers = $this->getAdapter()->select()
            ->from($this->_name, 'COUNT(*) AS COUNT')
            ->where('role = ?', 2);
        $teacherRes = $teachers->query()->fetch();


        $totalLM = $this->getAdapter()->select()
            ->from($this->_name, 'COUNT(*) AS COUNT')
            ->where('created_at>=?', $lastMonth);
        $resultLM = $totalLM->query()->fetch();

        $studentsLM = $this->getAdapter()->select()
            ->from($this->_name, 'COUNT(*) AS COUNT')
            ->where('role = ?', 1)
            ->where('created_at>=?', $lastMonth);

        $studentResLM = $studentsLM->query()->fetch();

        $teachersLM = $this->getAdapter()->select()
            ->from($this->_name, 'COUNT(*) AS COUNT')
            ->where('role = ?', 2)
            ->where('created_at>=?', $lastMonth);
        $teacherResLM = $teachersLM->query()->fetch();



        $usersCount['allTime']['total'] = $result['COUNT'];
        $usersCount['allTime']['students'] = $studentRes['COUNT'];
        $usersCount['allTime']['teachers'] = $teacherRes['COUNT'];

        $usersCount['lastMonth']['total'] = $resultLM['COUNT'];
        $usersCount['lastMonth']['students'] = $studentResLM['COUNT'];
        $usersCount['lastMonth']['teachers'] = $teacherResLM['COUNT'];
        return $usersCount;
    }

    public function getUsers(){
        $students = $this->getAdapter()->select()
            ->from(array('us' => $this->_name), array('id', 'firstname', 'lastname', 'username', 'role', 'status', 'created_at'))
            ->joinLeft('subscription_history', 'us.id = subscription_history.user_id', array('active_to', 'updated_at'))
            ->where('us.status>=?', 1);


        return $students->query()->fetchAll();
    }

    public function changeUserStatus($id, $status){
        $array = array(

            'status' => $status

        );

        $where = $this->getAdapter()->quoteInto('id=?', $id);

        return $this->update($array, $where);
    }
}