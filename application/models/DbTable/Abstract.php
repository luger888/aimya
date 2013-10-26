<?php

abstract class Application_Model_DbTable_Abstract extends Zend_Db_Table_Abstract
{

    public function getItem($id){
        $id = (int)$id;
        $row = $this->fetchRow($this->select()->where('id=?', $id));
        if(!$row) {
            throw new Exception("There is no element with ID: $id");
        }

        return $row->toArray();
    }

    public function getItemsList(){
        return $this->fetchAll()->toArray();
    }

    public function createItem($data)
    {

        try{
            $pk = $this->insert($data);
        } catch  (Exception $e) {
            $pk = false;
        }
        return $pk;

    }

    public function deleteItem($id)
    {

        $where = $this->getAdapter()->quoteInto('id = ?', $id);

        $this->delete($where);

    }

    public function updateItem($data,$id)
    {
        try{
            $where = $this->getAdapter()->quoteInto('id = ?', (int)$id);
            $pk = $this->update($data, $where);
            return true;
        } catch  (Exception $e) {
            $pk = false;
        }
        return $pk;

    }


}

