<?php 

// 验证Session之前也务必先启动session会话
// session_start();

// 引入封装文件，调用session校验函数,加持登录权限控制，没有登录权限一切都是白搭
require_once '../functions.php';
verify_session_user();

// 调用查询数据库函数，拿到相应条件下所对应的行数
$count_posts = query_database_one("SELECT count(*) AS NUM FROM `posts`;")['NUM'];
$count_posts_drafted = query_database_one("SELECT COUNT(*) AS NUM FROM `posts` WHERE `status` = 'drafted';")['NUM'];
$count_posts_published = query_database_one("SELECT COUNT(*) AS NUM FROM `posts` WHERE `status` = 'published';")['NUM'];
$count_posts_trashed = query_database_one("SELECT COUNT(*) AS NUM FROM `posts` WHERE `status` = 'trashed';")['NUM'];
$count_categories = query_database_one("SELECT COUNT(*) AS NUM FROM categories ;")['NUM'];
$count_comments = query_database_one("SELECT COUNT(*) AS NUM FROM comments ;")['NUM'];
$count_comments_held = query_database_one("SELECT COUNT(*) AS NUM FROM comments WHERE `status` = 'held' ;")['NUM'];

?>



<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Dashboard &laquo; Admin</title>
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
      <div class="jumbotron text-center">
        <h1>往之不谏，来者可追</h1>
        <p>万事无他，唯手熟尔。</p>
        <p><a class="btn btn-primary btn-lg" href="post-add.php" role="button">写文章</a></p>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">站点内容统计：</h3>
            </div>
            <ul class="list-group">
              <li class="list-group-item"><strong><?php echo $count_posts; ?></strong>篇文章（<strong><?php echo $count_posts_drafted; ?></strong>篇草稿、<strong><?php echo $count_posts_published ?></strong>篇已发布、<strong><?php echo $count_posts_trashed ?></strong>篇回收站）</li>
              <li class="list-group-item"><strong><?php echo $count_categories; ?></strong>个分类</li>
              <li class="list-group-item"><strong><?php echo $count_comments ;?></strong>条评论（<strong><?php echo $count_comments_held ;?></strong>条待审核）</li>
            </ul>
          </div>
        </div>
        <div class="col-md-4" style="position: relative; height:40vh; width:40vw">
            <canvas id="myChart"></canvas>
        </div>
        <div class="col-md-4"></div>
      </div>
    </div>
  </div>

  <!-- 定义一个php变量，在公共部分判断是不是这个变量，如果是，就加上active使其被选中 -->
  <?php $current = 'index'; ?>
  <!-- 调用sidebar的公共部分 -->
  <!-- 这里不要使用绝对路径，因为这是和公共页面文件绑在一起的，方便以后整体移动 -->
  <?php  include 'include/sidebar.php';?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/chart/Chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
  <script>NProgress.done()</script>
  <script>
    var postsNum = $('.list-group-item:first-child strong')
    var chartNum = [];
    for(var i=0;i<postsNum.length;i++){
      chartNum.push(postsNum[i].innerText);
    }

    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
      type: 'pie',
      data: {
        datasets: [{
          data: [chartNum[1], chartNum[2], chartNum[3]],
          backgroundColor : [
            'rgba(102, 204, 204, 1)',
            'rgba(255, 153, 204, 1)',
            'rgba(204, 204, 255, 1)'
          ]
        }],
        
        // These labels appear in the legend and in the tooltips when hovering different arcs
        labels: [
          '草稿',
          '已发布',
          '回收站',
        ]
          
      },
      options: {
          title: {
            display: true,
            text: '所有文章数据一览',
            position: 'bottom',
            fontSize: 14,
            lineHeight: 2
          },
          legend: {
            // display: false,
            position: 'bottom'
          }
      }

    })
  </script>
</body>
</html>
