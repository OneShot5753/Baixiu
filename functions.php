<?php 
/**
 * 此文件专门用来封装该产品中常用的功能
 *
 * 定义函数时要注意：函数名与多达一千多个内置函数冲突问题
 *
 * PHP 判断函数是否定义的方式： function_exists('get_current_user')
 */


// 载入配置选项，为了防止 functions.php 重复被载入时载入配置报错，所以使用 require_once  ????????????
// 此文件绝大多被/admin/..下的文件调用，因此应该用../config.php的父级路径 ????????????
include 'config.php';

/**
 * 测试自定义函数名是否可以正常使用
 * @param  string $name [自定义函数名]
 * @return string       [校验结果]
 */
function function_name_exist($name){
	$exit = function_exists($name);
	return $exit? '函数重名' : '放心使用';
}


/**
 * 校验Session，以开关调用页面的访问权限
 * @return array [用户session中user的数据库数据]
 */
session_start();
function verify_session_user(){
	if(empty($_SESSION['logined_user'])){
		header('Location: login.php');
		die();
	}
	return $_SESSION['logined_user'];
}



/**
 * 执行一个查询语句，返回查询到的数据（索引数组嵌套关联数组）
 * @param  string $sql 查询语句
 * @return array       查询结果（嵌套数组）
 */
function query_database_all( $sql ){
	$connect = mysqli_connect(DB_HOST , DB_USER , DB_PASS , DB_NAME);
	if (!$connect) {
	  exit('连接数据库失败');
	}
	$query = mysqli_query($connect , $sql);
	if (!$query) return;
	while ($row = mysqli_fetch_assoc($query)) {
		// 将查询并提取出来的所有数据放到一个索引数组中
		$result[] = $row;
	}
	return $result;
}


/**
 * 执行一个查询语句，返回一行查询到的数据（关联数组）
 * @param  string $sql 查询语句
 * @return array null  查询结果（一个关联数组）
 */
function query_database_one( $sql ){
	$query_all = query_database_all( $sql );
	// 先查询所有数据，如果能拿到第一行就拿，拿不到就给我null
	return isset($query_all[0])? $query_all[0] : null ;
}


/**
 * 执行一个增删改语句，获取受影响的行数
 * @param  string $sql 增删改语句
 * @return int         受影响行数
 */
function affectd_database( $sql ){
	$connect = mysqli_connect(DB_HOST , DB_USER , DB_PASS , DB_NAME);
	if (!$connect) {
	  exit('连接数据库失败');
	}
	$query = mysqli_query($connect , $sql);
	if (!$query) return;
	$affect = mysqli_affected_rows($connect);
	return $affect;
}






 ?>