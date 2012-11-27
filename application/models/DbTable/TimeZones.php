<?php
class Application_Model_DbTable_TimeZones extends Application_Model_DbTable_Abstract
{
    protected $_name = 'timezones';

    public function getTimeZones(){

        $data = $this   ->select()
            ->from('timezones', (array('gmt', 'name')));

        return $data->query()->fetchAll();

    }

}
