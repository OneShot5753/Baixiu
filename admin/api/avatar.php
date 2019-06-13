<?php 

// Demand： 
// 接收客户端的请求，拿到参数
// 连接数据库，找到参数对应的头像路径数据
// 输出头像路径数据,使客户端可以拿到

if(empty($_GET['email'])){
	exit('找不到数据');
}

$email = $_GET['email'];

// 调用配置文件，需要配置文件中的数据库参数
include "../../config.php";

$connect = mysqli_connect(DB_HOST , DB_USER , DB_PASS , DB_NAME);
if(!$connect){
	exit('连接数据库失败');
}

// 要注意SQL语句不要写错
$query = mysqli_query($connect , "select avatar from users where email = '{$email}' limit 1 ;");
if (!$query) {
	exit('查询数据库失败');
}

$user = mysqli_fetch_assoc($query);
if (!$user) {
	// echo "/static/assets/img/default.png";
	exit('找不到数据');
}

echo $user['avatar'];

// 要一边写一边测试，在浏览器url地址后面手动写上参数，直到可以准确拿到数据

 ?>