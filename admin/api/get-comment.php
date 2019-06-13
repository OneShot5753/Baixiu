<?php 

// 接收评论页发来的ajax请求，获取数据并返回

require "../../functions.php";

$page = isset($_GET['page'])? (int)$_GET['page'] : 1;

$show = 30 ;
$offset = ($page-1) * $show;

// 获取总条数以及最大页数
$all = query_database_one("select count(1) as num from comments
inner join posts on comments.post_id = posts.id")['num'];
// $max_page = (int)ceil($all/$show);


$data = query_database_all(sprintf("select 
comments.*,
posts.title as post_title
from comments
inner join posts on comments.post_id = posts.id
order by comments.created desc
limit %d , %d",$offset,$show));

// 一定要将数据约定好格式
header('Content-Type: application/json');

$json = json_encode($data);

$back = [
	// 'success' => true,
	'comments_data' => $json,
	'all' => $all,
	'curr' => $page
];

echo json_encode($back);

// header('Location: /admin/comments.php');


