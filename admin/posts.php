<?php 
// bug: 如果有一个分类下一篇文章都没有，此时进行筛选会无限重定向出错

// 引入封装文件，调用session校验函数
require_once '../functions.php';
verify_session_user();

// （2）服务端调用数据库查询函数获取所有分类数据
$get_category = query_database_all("select * from categories;");

//（4）筛选参数与分页参数发生冲突。定义筛选参数字符串
$select = '';
//（3）筛选数据与获取全部数据发生冲突。定义一个变量存放筛选条件，以便后续查询数据时作为条件
$where = '1 = 1';
if (isset($_GET['category']) && $_GET['category'] !== 'all') {
  $para_category = $_GET['category'];
  $where .= ' and posts.category_id =' . $para_category; //操，这里的一个空格，害我debug了好久！
  $select .= '&category=' . $para_category;
}
if (isset($_GET['status']) && $_GET['status'] !== 'all') {
  $para_status = $_GET['status'];
  // （6）cao! 这里的SQL字符串拼接与上面不同，因为这里的参数值是字符串，必须要有单引号
  $where .= " and posts.status = '{$para_status}'";
  $select .= '&status=' . $para_status;
}


// 最先要拿到点击页码按钮传来的参数，用来识别是多少页, 没有传参就呈现第一页
// 参数都是字符串，必须将其强制转换为数字，后面的运算才不会出错，为了保险，后面都int一下
$page = empty($_GET['page'])? 1 : (int)$_GET['page'];
// 在拿到所有数据之前，声明每页呈现多少条数据以及跳过多少条
$count = 20;
$offset = ($page - 1) * $count;  // 0,20,40,60...

// d.求出最大页码：(数据总条数 /  每页展示条数) 向上取整，为确保整数，务必加上强制转换 int()
// 数据总条数应该等于所有数据都存在时的总条数，所以应该关联查询
$all_count = (int)query_database_one("SELECT count(*) as num FROM posts
inner join categories on posts.category_id = categories.id
inner join users on posts.user_id = users.id
where {$where}")['num'];
$max_page = (int)ceil($all_count/$count);
// echo $max_page;  //826条，42页

// g. 防止用户在地址栏输入比最小值小比最大值大的参数
if ($page<1) {
  header('Location: /admin/posts.php?page=1'.$select);
}
if ($page>$max_page) {
  header('Location: /admin/posts.php?page='.$max_page.$select);
}

// Demand 1： 数据动态呈现在页面中，正确呈现作者名、分类名、发布状态，以及指定时间格式
// 01. 获取所有页面需要展示的数据 + 分页时间降序展示
$posts_data = query_database_all("select
  posts.id,
  posts.title,
  users.nickname as user_name,
  categories.name as category_name,
  posts.created,
  posts.status
from posts
inner join categories on posts.category_id = categories.id
inner join users on posts.user_id = users.id
where {$where}
order by posts.created desc
limit {$offset},{$count}; ");

// if (!$posts_data) {
//   die("查询数据库失败") ;
// }

// a.计算页码: 页码展示奇数5 左右偏移2  开始页码1  结束页码5
$show = 5;
$side = ($show-1)/2;
$begin = $page - $side;  
// $end = $begin + $show - 1;
$end = $page + $side;

// e. 处理 $begin 必须从1开始,副作用使得 $end 少了两个偏移，所以$end随之更改 其实可以和定义时合并
$begin = $begin<1? 1 : $begin;
$end = $begin + $side*2;

// f. 处理 $end 必须以最大页码结束,副作用使得 $begin 多了两个偏移
$end = $end>$max_page? $max_page : $end;
$begin = $end - $side*2; 
// k. 考虑到$max_page=1也就是总共就一页的情况，应该对$begin再进行一次判断
$begin = $begin<1? 1 : $begin;

// j.上一页与下一页的变量
$pre = $page-1;
$next = $page+1;

/**
 * 02.将数据中的状态转换成中文，在php写功能函数，在页面中调用
 * @param  [string] $status [英文发布状态]
 * @return [string]         [中文发布状态]
 */
function convert_status($status){
  $arr = [
    'drafted' => '草稿',
    'published'=> '已发布',
    'trashed' => '回收站'
  ];
  // $status会等于关联数组中的其中一个键，将关联数组中的值返回，返回之前先判断有无
  return isset($arr[$status])? $arr[$status]:'未知状态';
}

/**
 * 03.将数据中的时间转换成年月日格式，在php写功能函数，在页面中调用
 * @param  [string] $created ['Y-m-d H:i:s' 格式时间]
 * @return [string]         ['Y年m月d日 H:i:s' 格式时间]
 */
function convert_created($created){
  $times = strtotime($created);
  // 时间格式要换行，必须在<br>中的 r 前加转义符，否则与时间格式字符重复
  return date('Y年m月d日 <b\r> H:i:s',$times);
}

 ?>


<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">

    <!-- 调用navbar的公共部分 -->
    <?php include 'include/navbar.php'; ?>
    
    <div class="container-fluid">
      <div class="page-title">
        <h1>所有文章</h1>
        <a href="post-add.php" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="/admin/api/posts-delete.php?" style="display: none" id="deleteAll">批量删除</a>

        <!-- （1）完善form表单相关属性，确保name属性和button按钮。 -->
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>" method='get'>
          <select name="category" class="form-control input-sm">
            <option value="all">所有分类</option>
            <!-- （2）遍历创建下拉列表子菜单并设置value值为数据中的id -->
            <?php foreach ($get_category as $item): ?>
              <!-- （5）筛选后，下拉菜单的选项要持久在页面上（设置value的selected） -->
            <option value="<?php echo $item['id']; ?>" <?php echo isset($para_category)&&$para_category==$item['id']? 'selected':''; ?>><?php echo $item['name']; ?></option>
            <?php endforeach ?>
          </select>
          <select name="status" class="form-control input-sm">
            <option value="all">所有状态</option>
            <option value="drafted" <?php echo isset($para_status)&&$para_status=="drafted"? 'selected':''; ?>>草稿</option>
            <option value="published" <?php echo isset($para_status)&&$para_status=="published"? 'selected':''; ?>>已发布</option>
            <option value="trashed" <?php echo isset($para_status)&&$para_status=="trashed"? 'selected':''; ?>>回收站</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>

        <ul class="pagination pagination-sm pull-right">
          <!-- h.页码为 1 时 禁用上一页按钮 -->
          <li <?php echo $page===1? 'class="disabled"':''; ?>><a href="?page=<?php echo $pre.$select; ?>">上一页</a></li>

          <!-- i.开始页码不等于1时，显示省略号和首页按钮 -->
          <?php if ($begin!==1): ?>
            <li><a href="?page=1<?php echo $select ?>">1</a></li>
            <li><a href="#"><strong>···</strong></a></li>
          <?php endif ?>
         
          <!-- b.以$begin为计数器，$end终止器进行for循环创建页码，页码按钮向url传参，传页码数 -->
          <?php for ($i = $begin; $i <= $end ; $i++): ?>
          <!-- c. 当前页码高亮显示 -->
          <li <?php echo $i === $page ? 'class="active"' : ''; ?>><a href="?page=<?php echo $i .$select; ?>"><?php echo $i ?></a></li>
          <?php endfor ?>
          
          <!-- i.结束页码不等于最大页码时，显示省略号和首页按钮 -->
          <?php if ($end!==$max_page): ?>
            <li><a href="#"><strong>···</strong></a></li>
            <li><a href="?page= <?php echo $max_page.$select; ?>"><?php echo $max_page ?></a></li>
          <?php endif ?>

          <!-- h.页码为 最大页码 时 禁用下一页按钮 -->
          <li <?php echo $page===$max_page? 'class="disabled"':'' ?>><a href="?page=<?php echo $next.$select; ?>">下一页</a></li>
        </ul>

      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>

          <?php foreach ($posts_data as $row): ?>
          <tr>
            <td class="text-center"><input type="checkbox" data-id="<?php echo $row['id']; ?>"></td>
            <td><?php echo $row['title'] ?></td>
            <td><?php echo $row['user_name'] ?></td>
            <td><?php echo $row['category_name'] ?></td>
            <td class="text-center"><?php echo convert_created($row['created']); ?></td>
            <td class="text-center"><?php echo convert_status($row['status']); ?></td>
            <td class="text-center">
              <a href="/admin/post-edit.php?id=<?php echo $row['id']; ?>" class="btn btn-default btn-xs">编辑</a>
              <a href="/admin/api/posts-delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
          <?php endforeach ?>
          
        </tbody>
      </table>
    </div>
  </div>

  <?php $current = 'posts'; ?>
  <?php  include 'include/sidebar.php';?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>  
    $(function($){


      // 选中任意一个复选框时，显示批量删除按钮，否则隐藏
      var everyCheckbox = $('tbody input');
      var deleteAll = $('#deleteAll');
      // 定义一个数组，用来存放被选中的复选框
      var checkedBox = [];
      everyCheckbox.on('change',function(){
        var dataId = $(this).data('id');
        if ($(this).prop('checked')) {
          checkedBox.push(dataId);
        } else {
          checkedBox.splice(checkedBox.indexOf(dataId) , 1);
        }
        checkedBox.length ? deleteAll.fadeIn() : deleteAll.fadeOut();
        deleteAll.prop('search' , "?id=" + checkedBox);
      })

      // 全选与全不选功能
      $('thead input').on('change',function(){
        var status = $(this).prop('checked');
        everyCheckbox.prop('checked' , status).trigger('change')
      })







    })
  </script>
  <script>NProgress.done()</script>
  
</body>
</html>
