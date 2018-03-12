<?php
namespace frame\dao\db;

use frame\ChelerApi;

class DB extends CDBs {

    use TSqlMaker;

    /**
     * 重写MYSQL中的QUERY，对SQL语句进行监控
     * @param string $sql
     */
    public function query($sql, $is_set_default = true) {
        $this->getHandle($sql);

        $conf = ChelerApi::getConfig();

        if(isset($conf['dbDebug']) && $conf['dbDebug'] == true) {
            $start = microtime(true);
        }

        $query = $this->db->query($sql);

        if(isset($conf['dbDebug']) && $conf['dbDebug'] == true){
            $end   =   microtime(true);
            if(isset($conf['sqlcontrolarr'])) {
                $k = \count( $conf['sqlcontrolarr'] );
            } else {
                $k = 0;
            }

            $conf['sqlcontrolarr'][$k]['sql'] = $sql;
            $cost = \substr(($end-$start),0,7);

            $conf['sqlcontrolarr'][$k]['queryTime'] = $cost;
            $ar = $this->affected_rows();
            $conf['sqlcontrolarr'][$k]['affectedRows'] = $ar;

            ChelerApi::setConfig('sqlcontrolarr', $conf['sqlcontrolarr']);
        }

        if ($this->db->error()) {
            ChelerApi::throwError($this->db->error());
        }

        if ($is_set_default) {
            $this->setDefaultHandle();
        }

        return $query;
    }

    /**
     * 结果集中的行数
     * DAO中使用方法：$this->dao->db->result($result, $num=1)
     * @param $result 结果集
     * @return array
     */
    public function result($result, $num=1) {
        return $this->db->result($result, $num);
    }

    /**
     * 从结果集中取得一行作为关联数组
     * DAO中使用方法：$this->dao->db->fetch_assoc($result)
     * @param $result 结果集
     * @return array
     */
    public function fetch_assoc($result) {
        return $this->db->fetch_assoc($result);
    }

    /**
     * 从结果集中取得列信息并作为对象返回
     * DAO中使用方法：$this->dao->db->fetch_fields($result)
     * @param  $result 结果集
     * @return array
     */
    public function fetch_fields($result) {
        return $this->db->fetch_fields($result);
    }


    /**
     * 结果集中的行数
     * DAO中使用方法：$this->dao->db->num_rows($result)
     * @param $result 结果集
     * @return int
     */
    public function num_rows($result) {
        return $this->db->num_rows($result);
    }

    /**
     * 结果集中的字段数量
     * DAO中使用方法：$this->dao->db->num_fields($result)
     * @param $result 结果集
     * @return int
     */
    public function num_fields($result) {
        return $this->db->num_fields($result);
    }

    /**
     * 释放结果内存
     * DAO中使用方法：$this->dao->db->free_result($result)
     * @param obj $result 需要释放的对象
     */
    public function free_result($result) {
        return $this->db->free_result($result);
    }

    /**
     * 获取上一INSERT的ID值
     * DAO中使用方法：$this->dao->db->insert_id()
     * @return int
     */
    public function insert_id() {
        return $this->db->insert_id();
    }

    /**
     * 前一次操作影响的记录数
     * DAO中使用方法：$this->dao->db->affected_rows()
     * @return int
     */
    public function affected_rows() {
        return $this->db->affected_rows();
    }

    /**
     * 关闭连接
     * DAO中使用方法：$this->dao->db->close()
     * @return bool
     */
    public function close() {
        return $this->db->close();
    }

    /**
     * 错误信息
     * DAO中使用方法：$this->dao->db->error()
     * @return string
     */
    public function error() {
        return $this->db->error();
    }

    /**
     * 开始事务操作
     * DAO中使用方法：$this->dao->db->transaction_start()
     */
    public function transaction_start() {
        $this->query('START TRANSACTION');
        self::$transaction_flag = true;
        return true;
    }

    /**
     * 提交事务
     * DAO中使用方法：$this->dao->db->transaction_commit()
     */
    public function transaction_commit() {
        $this->query('COMMIT');
        self::$transaction_flag = false;
        return true;
    }

    /**
     * 回滚事务
     * DAO中使用方法：$this->dao->db->transaction_rollback()
     */
    public function transaction_rollback() {
        $this->query('ROLLBACK');
        self::$transaction_flag = false;
        return true;
    }


    /**
     * SQL操作-插入一条数据
     * DAO中使用方法：$this->dao->db->insert($data, $table_name)
     * @param array  $data array('key值'=>'值')
     * @param string $table_name 表名
     * @return int
     */
    public function insert(array $data, $table_name) {
        if (empty($data)) {
            return 0;
        }
        $data = $this->makeInsert($data);
        $sql = sprintf('INSERT INTO %s %s', $table_name, $data);
        $result = $this->query($sql, false);
        if (!$result) {
            return 0;
        }
        $id = $this->insert_id();
        $this->setDefaultHandle();
        return $id;
    }

    /**
     * SQL操作-如果不存在则出入，否则替换
     * DAO中使用方法：$this->dao->db->replace($data, $table_name)
     * @author lonphy 谨慎使用
     * @param array $data array('k'=>'v')
     * @param string $table_name 表名
     * @return num
     */
    public function replace(array $data, $table_name) {
        if (empty($data)) {
            return 0;
        }
        $data = $this->makeInsert($data);
        $sql = sprintf('REPLACE INTO %s %s', $table_name, $data);
        $result = $this->query($sql, false);
        $this->setDefaultHandle(); //设置默认的link_id
        return $result;
    }

    /**
     * SQL批量操作-如果不存在则出入，否则替换
     * DAO中使用方法：$this->dao->db->replace_more($data, $table_name)
     * @author lonphy 谨慎使用
     * @param array $data array('k'=>'v')
     * @param string $table_name 表名
     * @return num
     */
    public function replace_more(array $data, $table_name) {
        if ( empty($data[0])) {
            return 0;
        }

        $field = $this->makeInserts($data);

        $sql = sprintf('REPLACE INTO %s %s', $table_name, $field);
        $result = $this->query($sql,false);
        $this->setDefaultHandle(); //设置默认的link_id
        return $result;
    }

    /**
     * SQL操作-插入多条数据
     * DAO中使用方法：$this->dao->db->insert_more($field, $data, $table_name)
     * @param array $field 字段
     * @param array $data  对应的值，array(array('test1'),array('test2'))
     * @param string $table_name 表名
     * @return id
     */
    public function insert_more($field,array $data, $table_name) {
        if (empty($data)) {
            return 0;
        }
        $sql = $this->makeInserts($field,$data);
        $sql = sprintf('INSERT INTO %s %s', $table_name, $sql);
        $result = $this->query($sql);
        $this->setDefaultHandle(); //设置默认的link_id
        return $result;
    }

    /**
     * SQL操作-根据主键id更新数据
     * DAO中使用方法：$this->dao->db->update($id, $data, $table_name, $id_key = 'id')
     * @param  int    $id 主键ID
     * @param  array  $data 参数
     * @param  string $table_name 表名
     * @param  string $id_key 主键名
     * @return bool
     */
    public function update($id, array $data, $table_name, $id_key = 'id') {
        $id = (int) $id;
        if ($id < 1) {
            return 0;
        }
        $data = $this->makeUpdate($data);
        $where = $this->makeWhere([$id_key=>$id]);


        $sql = sprintf('UPDATE %s %s %s', $table_name, $data, $where);

        $this->query($sql, false);
        $rows = $this->affected_rows();
        $this->setDefaultHandle(); //设置默认的link_id
        return $rows;
    }



    /**
     * SQL操作-根据字段更新数据
     * DAO中使用方法：$this->dao->db->update_by_field($data, $field, $table_name)
     * @param  array  $data 参数
     * @param  array  $field 字段参数
     * @param  string $table_name 表名
     * @param  string $upGlue set字段附加，只支持+，-
     * @param  string $fieldGlue where条件附加,如 in等
     * @return int
     */
    public function update_by_field(array $data, array $field, $table_name) {
        if (empty($data) || empty($field)) {
            return 0;
        }
        $field = $this->makeWhere($field);
        $data = $this->makeUpdate($data);
        $sql = sprintf('UPDATE %s %s %s', $table_name, $data, $field);
        $this->query($sql, false);
        $rows = $this->affected_rows();
        $this->setDefaultHandle(); //设置默认的link_id
        return $rows;
    }

    /**
     * SQL操作-删除数据
     * DAO中使用方法：$this->dao->db->delete($ids, $table_name, $id_key = 'id')
     * @param  int|array $ids 单个id或者多个id
     * @param  string $table_name 表名
     * @param  string $id_key 主键名
     * @return bool
     */
    public function delete($ids, $table_name, $id_key = 'id') {
        if (\is_array($ids)) {
            $ids = $this->makeIn($ids);
            $sql = \sprintf('DELETE FROM %s WHERE %s %s', $table_name, $id_key, $ids);
        } else {
            $where = $this->makeWhere([$id_key=>$ids]);
            $sql = \sprintf('DELETE FROM %s %s', $table_name, $where);
        }
        return $this->query($sql);
    }

    /**
     * SQL操作-通过条件语句删除数据
     * DAO中使用方法：$this->dao->db->delete_by_field($field, $table_name)
     * @param  array  $field 条件数组
     * @param  string $table_name 表名
     * @return bool
     */
    public function delete_by_field(array $field, $table_name) {
        if (empty($field)) {
            return false;
        }
        $where = $this->makeWhere($field);
        $sql = \sprintf('DELETE FROM %s %s', $table_name, $where);
        return $this->query($sql);
    }

    /**
     * SQL操作-获取单条信息
     * DAO中使用方法：$this->dao->db->get_one($id, $table_name, $id_key = 'id')
     * @param int    $id 主键ID
     * @param string $table_name 表名
     * @param string $id_key 主键名称，默认id
     * @return array
     */
    public function get_one($id, $table_name, $id_key = 'id') {
        $id = (int) $id;
        if ($id < 1) {
            return [];
        }
        $where = $this->makeWhere([$id_key=>$id]);
        $sql = \sprintf('SELECT * FROM %s %s LIMIT 1', $table_name, $where);
        $result = $this->query($sql, false);
        if (!$result) {
            return [];
        }
        $r = $this->fetch_assoc($result);
        $this->setDefaultHandle(); //设置默认的link_id
        return $r;
    }

    /**
     * SQL操作-通过条件语句获取一条信息
     * DAO中使用方法：$this->dao->db->get_one_by_field($field, $table_name)
     * @param  array  $field 条件数组 array('username' => 'username')
     * @param  string $table_name 表名
     * @return array
     */
    public function get_one_by_field(array $field, $table_name) {
        if (empty($field)) {
            return [];
        }

        $where = $this->makeWhere($field);
        $sql = \sprintf('SELECT * FROM %s %s LIMIT 1', $table_name, $where);

        $result = $this->query($sql, false);
        if (!$result) {
            return [];
        }
        $r = $this->fetch_assoc($result);
        $this->setDefaultHandle(); //设置默认的link_id
        if (empty($r)) {
            return [];
        }
        return $r;
    }

    /**
     * SQL操作-获取单条信息-sql语句方式
     * DAO中使用方法：$this->dao->db->get_one_sql($sql)
     * @param  string $sql 数据库语句
     * @return array
     */
    public function get_one_sql($sql) {
        $sql = \trim($sql . ' ' .$this->makeLimit(1));
        $result = $this->query($sql, false);
        if (!$result) {
            return [];
        }
        $r = $this->fetch_assoc($result);
        $this->setDefaultHandle(); //设置默认的link_id
        if (empty($r)) {
            $r = [];
        }
        return $r;
    }

    /**
     * SQL操作-获取全部数据[2014.8.25之后开发作废次封装方法]
     * DAO中使用方法：$this->dao->db->get_all()
     * @param string $table_name 表名
     * @param array  $field 条件语句
     * @param int    $num 分页参数
     * @param int    $offest 获取总条数
     * @param int    $key_id KEY值
     * @param string $sort 排序键
     * @return array array(数组数据，统计数)
     * @deprecated
     */
    public function get_all($table_name, $num = 20, $offest = 0, array $field = [], $id_key = 'id', $sort = 'DESC') {
        $where = $this->makeWhere($field);
        $limit = $this->makeLimit($offest, $num);
        $sql = sprintf('SELECT * FROM %s %s ORDER BY %s %s %s', $table_name, $where, $id_key, $sort, $limit);
        $result = $this->query($sql, false);
        if (!$result) {
            return [[], 0];
        }
        $temp = [];
        while ($row = $this->fetch_assoc($result)) {
            $temp[] = $row;
        }
        $count = $this->get_count($table_name, $field);
        $this->setDefaultHandle(); //设置默认的link_id
        return [$temp, $count];
    }

    /**
     * SQL操作-通过条件语句获取所有信息
     * DAO中使用方法：$this->dao->db->get_all_by_field($field, $table_name)
     * @param  array  $field 条件数组 array('username' => 'username')
     * @param  string $table_name 表名
     * @author baiyuxiong
     * @return array|null
     */
    public function get_all_by_field(array $field, $table_name) {
        if ( empty($field) ) {
            return [];
        }
        $where = $this->makeWhere($field);
        $sql = \sprintf('SELECT * FROM %s %s', $table_name, $where);
        $result = $this->query($sql, false);
        if (!$result) {
            return [];
        }

        $temp = array();
        while ($row = $this->fetch_assoc($result)) {
            $temp[] = $row;
        }
        $this->setDefaultHandle(); //设置默认的link_id
        return $temp;
    }

    /**
     * SQL操作-获取全部数据[不统计总数，只显示下一页，提高翻页性能]
     * ==============
     * 查询时 多查一条数据，用来判断是否有下一页内容
     * 通过 array_pop 删除最后一条数据
     * ==============
     * DAO中使用方法：$this->dao->db->get_all_next()
     * @param string $table_name 表名
     * @param array  $field 条件语句
     * @param int    $num 分页参数
     * @param int    $offest 获取总条数
     * @param int    $key_id KEY值
     * @param string $sort 排序键
     * @return array array(数组数据，是否可以翻页)
     */
    public function get_all_next($table_name, $num = 20, $offest = 0, array $field = [], $id_key = 'id', $sort = 'DESC') {
        $where = $this->makeWhere($field);
        $newnum = $num + 1;
        $limit = $this->makeLimit($offest, $newnum);
        $sql = sprintf('SELECT * FROM %s %s ORDER BY %s %s %s', $table_name, $where, $id_key, $sort, $limit);
        $result = $this->query($sql, false);
        if (!$result) {
            return [[], -1];
        }
        $temp = [];
        while ($row = $this->fetch_assoc($result)) {
            $temp[] = $row;
        }
        $this->free_result($result);
        $this->setDefaultHandle(); //设置默认的link_id

        //删除多余的一条数据 -- begin
        if(count($temp) > $num ){
            $nextPage = 1;
            array_pop($temp);
        }else{
            $nextPage = -1;
        }
        //删除多余的一条数据 -- end
        return [$temp,$nextPage];
    }
    /**
     * SQL操作-获取所有数据
     * DAO中使用方法：$this->dao->db->get_all_sql($sql)
     * @param string $sql SQL语句
     * @return array
     */
    public function get_all_sql($sql) {
        $sql = \trim($sql);
        $result = $this->query($sql, false);
        if (!$result) {
            return [];
        }
        $temp = [];
        while ($row = $this->fetch_assoc($result)) {
            $temp[] = $row;
        }
        $this->setDefaultHandle(); //设置默认的link_id
        return $temp;
    }

    /**
     * SQL操作-获取数据总数
     * DAO中使用方法：$this->dao->db->get_count($table_name, $field = array())
     * @param  string $table_name 表名
     * @param  array  $field 条件语句
     * @return int
     */
    public function get_count($table_name, $field = array(),$glue=array()) {
        if(is_array($glue)&&!empty($glue)){
            $where = $this->build_whereGlue($field,$glue);
        }else{
            $where = $this->build_where($field);
        }
        $sql = sprintf("SELECT COUNT(*) as count FROM %s %s LIMIT 1", $table_name, $where);
        $result = $this->query($sql, false);
        $result =  $this->fetch_assoc($result);
        return $result['count'];
    }


    /**
     * SQL组装-组装INSERT语句
     * 返回：('key') VALUES ('value')
     * DAO中使用方法：$this->dao->db->build_insert($val)
     * @param  array $val 参数  array('key' => 'value')
     * @return string
     */
    public function build_insert($val) {
        if (!is_array($val) || empty($val)) return '';
        $temp_v = '(' . $this->build_implode($val). ')';
        $val = array_keys($val);
        $temp_k = '(' . $this->build_implode($val, 1). ')';
        return $temp_k . ' VALUES ' . $temp_v;
    }

    /**
     * SQL组装-组装多条语句插入
     * 返回：('key') VALUES ('value'),('value2')
     * DAO中使用方法：$this->dao->db->build_insertmore($field, $data)
     * @param array $field 字段
     * @param array $data  对应的值，array(array('test1'),array('test2'))
     * @return string
     */
    public function build_insertmore($field, $data) {
        $field = ' (' . $this->build_implode($field, 1) . ') '; //字段组装
        $temp_data = array();
        $data = (array) $data;
        foreach ($data as $val) {
            $temp_data[] = '(' . $this->build_implode($val) . ')';
        }
        $temp_data = implode(',', $temp_data);
        return $field . ' VALUES ' . $temp_data;
    }

    /**
     * SQL组装-组装UPDATE语句
     * 返回：SET name = 'aaaaa'
     * DAO中使用方法：$this->dao->db->build_update($val)
     * @param  array $val  array('key' => 'value')
     * @return string `key` = 'value'
     */
    public function build_update($val) {
        if (!is_array($val) || empty($val)) return '';
        $temp = array();
        foreach ($val as $k => $v) {
            if (is_array($v)) {
                $ktmp = $this->build_escape($k, 1);
                if(is_assoc($v)) {
                    foreach($v as $op => $value) {
                        $temp[] = $ktmp .' = '. $ktmp .' '. $op .' '. $this->build_escape($value);
                    }
                }
            }else{
                $temp[] = $this->build_kv($k, $v);
            }
        }
        return 'SET ' . implode(',', $temp);
    }
    /**
     * SQL组装-组装UPDATE语句
     * 返回：SET name = 'aaaaa'
     * DAO中使用方法：$this->dao->db->build_update($val)
     * @param  array $val  array('key' => 'value')
     * @return string `key` = 'value'
     */
    public function build_updateGlue($val,$upGlue) {
        if (!is_array($val) || empty($val)) return '';
        $temp = array();
        foreach ($val as $k => $v) {
            $temp[] = $this->build_field($k, $v,$upGlue[$k]);
        }
        return 'SET ' . implode(',', $temp);
    }
    /**
     * SQL组装-组装LIMIT语句
     * 返回：LIMIT 0,10
     * DAO中使用方法：$this->dao->db->build_limit($start, $num = NULL)
     * @param  int $start 开始
     * @param  int $num   条数
     * @return string
     */
    public function build_limit($start, $num = NULL) {
        $start = (int) $start;
        $start = ($start < 0) ? 0 : $start;
        if ($num === NULL) {
            return 'LIMIT ' . $start;
        } else {
            $num = abs((int) $num);
            return 'LIMIT ' . $start .' ,'. $num;
        }
    }

    /**
     * SQL组装-组装IN语句
     * 返回：('1','2','3')
     * DAO中使用方法：$this->dao->db->build_in($val)
     * @param  array $val 数组值  例如：ID:array(1,2,3)
     * @return string
     */
    public function build_in($val) {
        $val = $this->build_implode($val);
        return ' IN (' . $val . ')';
    }

    /**
     * SQL组装-组装AND符号的WHERE语句
     * 返回：WHERE a = 'a' AND b = 'b'
     * DAO中使用方法：$this->dao->db->build_where($val)
     * @param array $val array('key' => 'val')
     * @return string
     */
    public function build_where($val) {
        if (!is_array($val) || empty($val)) return '';
        $temp = array();
        foreach ($val as $k => $v) {
            if (is_array($v)) {
                $ktmp = $this->build_escape($k, 1);
                if(is_assoc($v)) {
                    foreach($v as $op => $value) {
                        if(is_array($value)){
                            $temp[] = $ktmp .' '. $op .' ('.$this->build_implode($value).') ';
                        }else {
                            $temp[] = $ktmp .' '. $op .' '. $this->build_escape($value);
                        }
                    }
                } else {
                    $temp[] = $ktmp . $this->build_in($v);
                }
            }else{
                $temp[] = $this->build_kv($k, $v);
            }
        }
        return ' WHERE ' . implode(' AND ', $temp);
    }
    /**
     * SQL组装-组装AND符号的WHERE语句
     */
    public function build_whereGlue($filed,$glue){
        foreach ($filed as $k => $v){
            $temp[] = $this -> build_field($k, $v,$glue[$k]);
        }
        return ' WHERE ' . implode(' AND ', $temp);
    }
    /**
     * SQL组装-单个或数组参数过滤
     * DAO中使用方法：$this->dao->db->build_escape($val, $iskey = 0)
     * @param  string|array $val
     * @param  int          $iskey 0-过滤value值，1-过滤字段
     * @return string
     */
    public function build_escape($val, $iskey = 0) {
        if (is_array($val)) {
            foreach ($val as $k => $v) {
                $val[$k] = trim($this->build_escape_single($v, $iskey));
            }
            return $val;
        }
        return $this->build_escape_single($val, $iskey);
    }

    /**
     * SQL组装-组装KEY=VALUE形式
     * 返回：a = 'a'
     * DAO中使用方法：$this->dao->db->build_kv($k, $v)
     * @param  string $k KEY值
     * @param  string $v VALUE值
     * @return string
     */
    public function build_kv($k, $v) {
        return $this->build_escape($k, 1) . ' = ' . $this->build_escape($v);
    }

    /**
     * SQL组装-将数组值通过，隔开
     * 返回：'1','2','3'
     * DAO中使用方法：$this->dao->db->build_implode($val, $iskey = 0)
     * @param  array $val   值
     * @param  int   $iskey 0-过滤value值，1-过滤字段
     * @return string
     */
    public function build_implode($val, $iskey = 0) {
        if (!is_array($val) || empty($val)) return '';
        return implode(',', $this->build_escape($val, $iskey));
    }

    /**
     * SQL组装-检查DAO中进来的数组参数是否key键存在
     * DAO中使用方法：$this->dao->db->build_key($data, $fields)
     * @param array $data  例如：array("username" => 'asdasd')
     * @param string $fields  例如："username,password"
     */
    public function build_key($data, $fields) {
        $fields = explode(',', $fields);
        $temp = array();
        foreach ($data as $key => $value) {
            if (in_array($key, $fields)) {
                $temp[$key] = $value;
            }
        }
        return $temp;
    }
    /**
     * SQL组装-SQL 过滤
     * @param string $field 字段
     * @param string $val 值
     * @param string $glue 判断条件
     * @author lxm
     */
    public function build_field($field, $val, $glue = '='){
        $field = self::build_escape_single($field,1);//过滤字段
        $glue = self::build_check_glue($glue);//过滤条件
        if (is_array($val)) {
            if(!$glue) $glue = $glue == 'notin' ? 'notin' : 'in';
        } elseif ($glue == 'in') {
            $glue = '=';
        }
        if(empty($glue)){
            $glue = '=';
        }
        switch ($glue) {
            case '=':
                return $field . $glue . self::quote($val);
                break;
            case '-':
            case '+':
                return $field . '=' . $field . $glue . self::quote((string) $val);
                break;
            case '|':
            case '&':
            case '^':
                return $field . '=' . $field . $glue . self::quote($val);
                break;
            case '>':
            case '<':
            case '<>':
            case '<=':
            case '>=':
                return $field . $glue . self::quote($val);
                break;

            case 'like':
                return $field . ' LIKE ' . self::quote('%'.$val.'%') . '';
                break;

            case 'in':
            case 'notin':
                $val = $val ? implode(',', self::quote($val)) : '\'\'';
                return $field . ($glue == 'notin' ? ' NOT' : '') . ' IN(' . $val . ')';
                break;
            case 'day'://某天数据
                return 'to_days(date_format(from_UNIXTIME('.$field.'),\'%Y-%m-%d\')) = to_days(\''.$val.'\')';
                break;
            case 'rangetime'://时间范围
                return $field . '>='. self::quote($val[0]) . ' AND ' . $field . '<='. self::quote($val[1]);
                break;
            default:
                //throw new DbException('Not allow this glue between field and value: "' . $glue . '"');
                break;
        }
    }
    public function quote($str, $noarray = false) {
        if (is_string($str))
            return '\'' . addcslashes($str, "\n\r\\'\"\032") . '\'';

        if (is_int($str) or is_float($str))
            return '\'' . $str . '\'';

        if (is_array($str)) {
            if($noarray === false) {
                foreach ($str as &$v) {
                    $v = self::quote($v, true);
                }
                return $str;
            } else {
                return '\'\'';
            }
        }

        if (is_bool($str))
            return $str ? '1' : '0';

        return '\'\'';
    }
    /**
     * SQL条件检查
     * @param string $glue
     * @return string
     */
    public function  build_check_glue($glue = '=') {
        return in_array($glue, array('=', '<', '<=', '>', 'like','in','notin', '>=', '!=', '+', '-', '|', '&', '<>','day','rangetime')) ? $glue : '=';
    }
    /**
     * SQL操作-获取全部数据[新增方法-封装page翻页]
     * DAO中使用方法：$this->dao->db->get_list()
     * @param string $table_name 表名
     * @param array  $page 分页数组
     * @param array  $field 条件语句
     * @param array  $glue 条件判断数组
     * @param int    $id_key KEY值
     * @param string $sort 排序键
     * @return array array(数组数据，统计数)
     * @author lxm
     * @version 2.0
     * @copyright 2014.8.25
     */
    public function get_list($page, $field, $glue, $id_key, $sort, $table_name) {
        if(is_array($glue)&&!empty($glue)){
            $where = $this->build_whereGlue($field,$glue);
        }else{
            $where = $this->build_where($field);
        }
        $limit = $this->build_limit($page['offset'], $page['num']);
        $sql = sprintf("SELECT * FROM %s %s ORDER BY %s %s %s", $table_name, $where, $id_key, $sort, $limit);
        $result = $this->query($sql, false);
        if (!$result) return [];
        $temp = array();
        while ($row = $this->fetch_assoc($result)) {
            $temp[] = $row;
        }
        $count = $this->get_count($table_name, $field, $glue);
        return array($temp, $count);
    }
    /**
     * SQL组装-私有SQL过滤
     *
     * @param  string $val 过滤的值
     * @param  int    $iskey 0-过滤value值，1-过滤字段
     * @return string
     */
    private function build_escape_single($val, $iskey = 0) {
        if ($iskey === 0) {
            if (is_numeric($val)) {
                return " '" . $val . "' ";
            } else {
                return " '" .$val . "' ";
            }
        } else {
            $val = str_replace(array('`', ' '), '', $val);
            return ' `'.addslashes(stripslashes($val)).'` ';
        }
    }
}
