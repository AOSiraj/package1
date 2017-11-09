<?php namespace package1\Model\SurveyMonkey;
/**
 * Created by PhpStorm.
 * User: abdullah.s
 * Date: 8/25/17
 * Time: 12:06 AM
 */


use Zend\Db\TableGateway\TableGateway;

class SurveyMonkeyTable
{
    protected $tableGateway;

    /**
     * SurveyMonkeyTable constructor.
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway){
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param $idArray
     * @return null
     */
    public function getRowById($idArray){
        $rowset = $this->tableGateway->select($idArray)->getDataSource();
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }

    /**
     * @param $newEntry
     * @param array $idForEntry
     * @return mixed
     */
    public function insert($newEntry, $idForEntry = array()){
        return $this->tableGateway->insert($newEntry, $idForEntry);
    }

    /**
     * @param $newEntry
     * @param $idForUpdate
     * @return mixed
     */
    public function update($newEntry, $idForUpdate){
        return $this->tableGateway->update($newEntry, $idForUpdate);
    }
    public function fetchAll($params = array())
    {
        return iterator_to_array($this->tableGateway->select($params)->getDataSource());
    }

    /**
     * Fetch data from $orderBy,$columns,$where
     * @return ResultSet
     */
    public function fetchAllByCondition($columns=array(),$where=array(),$orderBy=array(),$limit=5,$offset=0)
    {
        $sql = $this->tableGateway->getSql();
        $select = $sql->select();
        if(!empty($columns)) $select->columns($columns);
        if(!empty($orderBy)) $select->order($orderBy);
        if(!empty($where)) $select->where($where);
        if(!empty($limit)) $select->limit($limit);
        if(!empty($offset)) $select->offset($offset);
        $result =  iterator_to_array($this->tableGateway->selectWith($select)->getDataSource());
        return $result;
    }


    /**
     * @param array $where
     * @return mixed
     */
    public function countAll($where = array())
    {
        $sql = $this->tableGateway->getSql();
        $select = $sql->select();
        if(!empty($where)) $select->where($where);
        return $this->tableGateway->selectWith($select)->count();
    }

    /**
     * @param $params
     * @return mixed
     */
    public function delete($params){
        return $this->tableGateway->delete($params);
    }

    /**
     * @return mixed
     */
    public function deleteAll(){
        return $this->tableGateway->delete('1=1');
    }
}