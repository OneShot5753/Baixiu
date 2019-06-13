<?php 

require '../../functions.php';

if(empty($_GET['id'])){
	exit('请传入参数！');
}

$id = $_GET['id'];

// 删除一条数据可以用 id = 1 , 删除多条数据就要用 id in (2,1,3,5...) 注意两边的单双引号
$affect = affectd_database('DELETE FROM posts WHERE id in ('. $id .') ;');

if($affect <= 0){
	exit('删除失败');
	
}


// http中的请求头中有一个 Referer（HTTP来源地址）表示从哪儿链接到目前的网页
// 删除操作完成后，返回到原来的url地址，无论原来的url中有参还是无参
header('Location: '.$_SERVER['HTTP_REFERER']);


 ?>