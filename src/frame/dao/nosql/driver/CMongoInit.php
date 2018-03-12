<?php
/**
 * CMongoInit.php
 * 描述
 * User: lixin
 * Date: 17-6-27
 */

namespace frame\dao\nosql\driver;


class CMongoInit
{

    private $database;
    private $collection = '';
    private $manager;

    /**
     * 初始化
     * @param array $config
     * @author lixin
     */
    public function init($config = array())
    {
        if ($config['server'] == '') {
            $config['server'] = '127.0.0.1';
        }
        if ($config['port'] == '') {
            $config['port'] = '27017';
        }

        if (empty($config['username'])) {
            $dns = 'mongodb://' . $config['server'] . ':' . $config['port'];
        } else {
            $dns = 'mongodb://' . $config['username'] . ':'
                . $config['password'] .
                '@' . $config['host'] . ':'
                . $config['port'];
        }

        if (!isset($config['option'])) {
            $config['option'] = array('connect' => true);
        }

        $this->manager = new \MongoDB\Driver\Manager($dns, $config['option']);
    }

    /**
     * executeBulkWrite
     * @author lxm
     * @verion 2.0
     */
    private function _executeBulkWrite($bulk)
    {
        if ($manager = $this->manager) {
            $writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 1000);
            $db = $this->database . '.' . $this->collection;

            try {
                $result = $manager->executeBulkWrite($db, $bulk, $writeConcern);
                //TEST Query
                return $result;
            } catch (\MongoDB\Driver\Exception\BulkWriteException $e) {
                return;
            } catch (\MongoDB\Driver\Exception\Exception $e) {
                return;
            }
        }
    }

    /**
     * mongod inster
     * @param object|array $document
     * @param $document
     * @return string
     */
    public function bulkWriteInster($document)
    {
        if (empty($document)) return "";
        $bulk = new \MongoDB\Driver\BulkWrite;
        $document_id = $bulk->insert($document);
        $this->_executeBulkWrite($bulk);
        return (string)$document_id;
    }
    
    /**
     * mongod update
     * @param array $set
     * @param $document_id
     * @return string
     */
    public function bulkWriteUpdate(array $set, $document_id)
    {
        if (empty($document_id)) return "";
        $bulk = new \MongoDB\Driver\BulkWrite;
        $document_id = $bulk->update(['_id' => new \MongoDB\BSON\ObjectId($document_id)], ['$set' => $set]);
        $this->_executeBulkWrite($bulk);
        return (string)$document_id;
    }

    /**
     * mongod query
     * @param array|object $filter
     * @param array $options
     * @link http://php.net/manual/zh/mongodb-driver-query.construct.php
     * @author lxm
     * @verion 2.0
     */
    public function query(array $filter, array $options)
    {
        $readPreference = new \MongoDB\Driver\ReadPreference(\MongoDB\Driver\ReadPreference::RP_PRIMARY);
        $query = new \MongoDB\Driver\Query($filter, $options);
        $db = $this->database . '.' . $this->collection;
        $cursor = $this->manager->executeQuery($db, $query, $readPreference);


        return $cursor->toArray();
    }

    /**
     * mongodb command
     * @param array $commands
     * 例如
     * $commands = [
     * 'count' => "RD5512G500000301",
     * 'query' => [
     * 'time'=>['$gt' => $startTime, '$lt' => $endTime],
     *  ],
     * ];
     * @return mixed
     * @author lixin
     */
    public function command($commands)
    {
        $db = $this->database . '.' . $this->collection;
        $command = new \MongoDB\Driver\Command($commands);
        $cursor = $this->manager->executeCommand($db, $command);
        return $cursor->toArray();
    }

    /**
     * 获取当前连接
     * @return mixed
     */
    public function getManager()
    {
        return $this->manager;
    }


    /**
     * @return mixed $collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param mixed $collection
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    /**
     * @param $database
     * @return $this
     */
    public function switchDb($database)
    {
        $this->database = $database;
        return $this;
    }

    /**
     * @param $collection
     * @return $this
     */
    public function collection($collection)
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * @param $collection
     * @return $this
     */
    public function table($collection)
    {
        return $this->collection($collection);
    }

}