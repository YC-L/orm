<?php


namespace EasySwoole\ORM;


use EasySwoole\ORM\Characteristic\ArrayAccess;
use EasySwoole\ORM\Characteristic\Base;
use EasySwoole\ORM\Characteristic\Iterator;
use EasySwoole\ORM\Characteristic\JsonSerializable;
use EasySwoole\ORM\Driver\Result;
use EasySwoole\ORM\Utility\QueryBuilder;

abstract class AbstractModel implements \ArrayAccess,\Iterator,\JsonSerializable
{
    use Base;
    use Iterator;
    use JsonSerializable;
    use ArrayAccess;

    private $queryBuilder;

    protected $connection = 'default';

    protected $queryResult;

    protected $pk = null;

    function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
        $this->initialize();
    }

    abstract function table():string ;


    protected function initialize()
    {

    }

    protected function queryBuilder():QueryBuilder
    {
        return $this->queryBuilder;
    }

    protected function exec()
    {
        return $this->query($this->queryBuilder()->getPrepareQuery(),$this->queryBuilder()->getBindParams());
    }

    function connect(string $name)
    {
        $this->connection = $name;
        return $this;
    }

    public function create(array $data = null)
    {
        $ret = new static();
        if($data){
            $ret->setData($data,true);
        }
        return $ret;
    }


    function where($whereProp, $whereValue = 'DBNULL', $operator = '=', $cond = 'AND')
    {
        $this->queryBuilder->where($whereProp, $whereValue, $operator, $cond);
        return $this;
    }

    function orWhere($whereProp, $whereValue = 'DBNULL', $operator = '=')
    {
        $this->where($whereProp, $whereValue, $operator, 'OR');
        return $this;
    }

    function join($joinTable, $joinCondition, $joinType = '')
    {
        $this->queryBuilder->join($joinTable, $joinCondition, $joinType);
        return $this;
    }

    function get($numRows = null, $columns = '*')
    {
        $this->queryBuilder()->get($this->table());
        return $this->exec();
    }

    function delete($numRows = null)
    {
        $this->queryBuilder()->delete($this->table(),$numRows);
        return $this->exec();
    }

    function update(?int $numRows = null,?array $data = null,array $columns = [])
    {
        if(!$data){
            $data = $this->data;
        }
        $this->queryBuilder->update($this->table(),$data,$numRows);
    }

    function find()
    {

    }

    function limit()
    {

    }

    function save()
    {

    }

    function query(string $sql,array $bindParams = [])
    {
        $ret = DbManager::getInstance()->getConnection($this->connection)->query($sql,$bindParams);
        $this->queryResult = $ret;
        if($ret){
            return $ret->getResult();
        }else{
            return null;
        }
    }

    function getQueryResult():?Result
    {
        return $this->queryResult;
    }

    public static function __callStatic($name, $arguments)
    {
        switch ($name){
            case 'create':{
                $ins = new static();
//
//                $ins->setData($arguments);
                break;
            }

            case 'table':{
                break;
            }

            case 'connect':{
                break;
            }
        }
    }

}