<?php
namespace frame\dao\db\driver;

interface IDriver {

	/**
	 * 抽象数据库链接
	 * @param  string $host sql服务器
	 * @param  string $user 数据库用户名
	 * @param  string $password 数据库登录密码
	 * @param  string $database 数据库
	 * @param  string $charset 编码
	 */
	 function init($host, $user, $password, $database, $charset);
	
	/**
	 * 抽象数据库执行语句
	 * @param  string $sql SQL语句
	 * @return obj
	 */
	 function query($sql);
	
	/**
	 * 抽象数据库-从结果集中取得一行作为关联数组
	 * @param $result 结果集
	 * @return array
	 */
	 function fetch_assoc($result);
	
	/**
	 * 抽象数据库-从结果集中取得列信息并作为对象返回
	 * @param  $result 结果集
	 * @return array
	 */
	 function fetch_fields($result);
	
	/**
	 * 抽象数据库-前一次操作影响的记录数
	 * @return int
	 */
	 function affected_rows();
	
	/**
	 * 抽象数据库-结果集中的行数
	 * @param $result 结果集
	 * @return int
	 */
	 function num_rows($result);
	
	/**
	 * 抽象数据库-结果集中的字段数量
	 * @param $result 结果集
	 * @return int
	 */
	 function num_fields($result);
	
	/**
	 * 抽象数据库-获取上一INSERT的ID值
	 * @return Int
	 */
	 function insert_id();
	
	/**
	 * 抽象数据库-释放结果内存
	 * @param obj $result 需要释放的对象
	 */
	 function free_result($result);
	
	/**
	 * 抽象数据库链接关闭
	 * @param  string $sql SQL语句
	 * @return obj
	 */
	 function close();
	
	/**
	 * 错误信息
	 * @return string
	 */
	 function error();
}
