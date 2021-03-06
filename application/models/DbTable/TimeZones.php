<?php
class Application_Model_DbTable_TimeZones extends Application_Model_DbTable_Abstract
{
    protected $_name = 'timezones';

    public function getTimeZones(){

        $data = $this   ->select()
            ->from('timezones', (array('id', 'gmt', 'name', 'code')));

        return $data->query()->fetchAll();

    }

    public function getTimezoneByGmt($gmt){

        $data = $this->select()
            ->from($this->_name, array('id', 'code', 'name'))
            ->where('gmt = ?', $gmt);

        return $data->query()->fetch();

    }

}
