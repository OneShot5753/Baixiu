<?php 


require '../../functions.php';

if(empty($_GET['id'])){
	exit('请传入参数！');
}

$id = $_GET['id'];

// 删除一条数据可以用 id = 1 , 删除多条数据就要用 id in (2,1,3,5...) 注意两边的单双引号
$affect = affectd_database('DELETE FROM users WHERE id in ('. $id .') ;');

if($affect <= 0){
	exit('删除失败');
	
}

// echo "删除成功！";

header('Location:'.$_SERVER['HTTP_REFERER']);