<?php 

// Demand： 接收界面传来的id并将其数据库中对应的数据删除

require '../../functions.php';

if (isset($_GET['delete'])) {

	$id = $_GET['delete'];

	$affect = affectd_database("delete from comments where id in ( $id )");
	if ($affect<=0) {
		exit('删除失败');
	}
	
	// 要保证回到原来的分页啊
	header('Location:' .$_SERVER['HTTP_REFERER']);
}



