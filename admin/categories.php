<?php 


// 引入封装文件，调用session校验函数
require_once '../functions.php';
verify_session_user();

// Demand 1 :  动态呈现分类列表 (拿数据)
// Demand 2 ： 添加一个分类（改数据）    --- 对当前文件POST请求不传参
// Demand 3 ： 单行与批量删除分类 （改数据） --- 对外部文件GET请求传参
// Demand 4 ： 编辑单行分类 （改数据） --- 对当前文件GET请求传参拿数据 + POST传参改数据
// 格局大一点看，应该先改数据，再呈现数据，这样改数据的可以马上得到反馈
// 将删除功能放在了别的php文件单独处理

// 添加分类功能
function add_category(){
  // 校验表单 + 持久化 + 响应
  if (empty($_POST['name'])) {
    $GLOBALS['fault'] = '请填写分类名称';
    return;
  }
  if (empty($_POST['slug'])) {
    $GLOBALS['fault'] = '请填写分类别名';
    return;
  }

  $name = $_POST['name'];
  $slug = $_POST['slug'];

  $affect = affectd_database("INSERT INTO categories VALUES ( NULL , '{$slug}' ,'".$name."');");
  if ($affect <= 0) {
     $GLOBALS['fault'] = '很抱歉，添加失败，请重试。';
     return;
  }
  if ($affect > 0) {
     $GLOBALS['success'] = '添加成功!';
     return;
  }
}

// 编辑分类功能
function edit_category(){

  // 在函数内，将变量暴露给全局，以便页面中数据能根据函数中变量的变化而变化
  global $current_need_edit;

  // 如果有值，就按填写值提交，如果为空，就还是按照原来的数据提交
  $name = isset($_POST['name'])?  $_POST['name'] : $current_need_edit['name'];
  $slug = isset($_POST['slug'])?  $_POST['slug'] : $current_need_edit['slug'];
  
  // 数据更新到数据库，持久化
  $id = $current_need_edit['id'];
  $affect = affectd_database("UPDATE categories SET name = '{$name}' , slug = '{$slug}' WHERE id = '{$id}';");

  // ★★★将改动过的数据继续呈现在界面上，因为这是全局变量，页面会跟随我的变化
  $current_need_edit['name'] = $name;
  $current_need_edit['slug'] = $slug;

  if ($affect <= 0) {
    $GLOBALS['fault'] = '更新数据失败';
    return;
  }
  if ($affect > 0) {
     $GLOBALS['success'] = '更新数据成功';
     return;
  }
}


// 触发时机非常关键：同是post请求，有id传参则调用编辑功能，没有传参则调用添加功能
// 但是这样的判断无法触发编辑功能，这里有一个顺序问题，
// 必须先拿到全局变量，才有编辑页面以及编辑页面的url参数
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//   if (empty($_GET['id'])) {
//     add_category();
//   } else {
//     $get_id = $_GET['id'];
//     $current_need_edit = query_database_one('SELECT * FROM categories WHERE id = '.$get_id.';');
//     edit_category();
//   }
// }

// 执行顺序是真的非常重要！！！
if(empty($_GET['id'])){
  // 如果没有传参，也是post请求，那么调用添加分类的功能
  if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    add_category();
  }
} else {
  // 如果有参数，那么一定是编辑按钮传来的，接收id + 拿到对应数据并将编辑数据呈现在界面上
  $get_id = $_GET['id'];
  $current_need_edit = query_database_one('SELECT * FROM categories WHERE id = '.$get_id.';');
  if (!$current_need_edit) {
    $GLOBALS['fault'] = '查询数据库失败';
  }
  // 一定是先拿到数据把页面呈现出来，再用post请求更新数据的
  if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    edit_category();
  }
}


// 获取分类所有数据，永远要在修改数据的后面
$list = query_database_all("SELECT * FROM categories;");
// $list 此时是一个索引数组下的关联数组下的数据


 ?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
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
        <h1>分类目录</h1>
      </div>

      <!-- 有错误信息时展示 -->
      <?php if (isset($fault)): ?>
      <div class="alert alert-danger">
        <strong><?php echo $fault; ?></strong>
      </div>
      <?php endif ?>
      <!-- 有成功信息时展示 -->
      <?php if (isset($success)): ?>
      <div class="alert alert-success">
        <strong><?php echo $success; ?></strong>
      </div>
      <?php endif ?>

      <!-- 如果有通过编辑按钮提供的id拿到了数据，那么将页面呈现为编辑页面 -->
      <?php if (isset($current_need_edit)): ?>
        <div class="row">
        <div class="col-md-4">
          <!-- 给这个post请求传参id主要是为了将其触发时机与添加分类功能分开来 -->
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $current_need_edit['id'] ?>" method='post' autocomplete="off">
            <h2>编辑《<strong><?php echo $current_need_edit['name']; ?></strong>》分类</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="<?php echo $current_need_edit['name']; ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="<?php echo $current_need_edit['slug']; ?>">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">保存</button>
            </div>
          </form>
        </div>
      <!-- 否则，将页面呈现为添加表单页面 -->
      <?php else: ?>
        <div class="row">
        <div class="col-md-4">
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method='post' autocomplete="off">
            <h2>添加新分类目录</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
        </div>
      <?php endif ?>

        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm" href="/admin/api/category-delete.php" style="display: none" id="delete_all">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody id="xiu_list">

              <?php foreach ($list as $row): ?>
              <tr>
                <td class="text-center"><input type="checkbox"  data-id="<?php echo $row['id']; ?>"></td>
                <td><?php echo $row['name'] ?></td>
                <td><?php echo $row['slug'] ?></td>
                <td class="text-center">
                  <!-- 用当前文件处理编辑功能 -->
                  <a href="/admin/categories.php?id= <?php echo $row['id'] ?>" class="btn btn-info btn-xs">编辑</a>
                  <!-- 用另一个文件处理删除功能 -->
                  <a href="/admin/api/category-delete.php?id= <?php echo $row['id'] ?>" class="btn btn-danger btn-xs">删除</a>
                  <!-- 编辑和删除都必须通过url传参以识别，get的方式 ，参数就是数据库中的id编号-->
                </td>
              </tr>
              <?php endforeach ?>
              
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current = 'categories'; ?>
  <?php  include 'include/sidebar.php';?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    // DEMAND: 选中任意一个复选框时，显示批量删除按钮，否则隐藏
    $(function($){

      // 拿到所有复选框
      var everyCheckbox = $('#xiu_list input');
      // 拿到批量删除按钮
      var deleteAll = $('#delete_all');

      // 为每一个复选框注册change事件
      // everyCheckbox.on('change',function(){
      //    var flag = false;    // 假设没有一个被选中
      //    everyCheckbox.each(function(index,element){
      //     // .attr() 与 .prop() 的区别:
      //     // - attr 访问的是 元素<>内的属性
      //     // - prop 访问的是 元素对应的DOM对象中的属性
      //       if ($(element).prop('checked')) {
      //         // 如果有一个的checked，那么flag为true
      //         flag = true;
      //       }
      //    })
      //    flag ? deleteAll.fadeIn() : deleteAll.fadeOut();

      // // 缺点：每一次选中复选框，都要进行判断

      // })
      
      // 定义一个数组，用来存放被选中的复选框
      var checkedBox = [];
      everyCheckbox.on('change',function(){

        // 拿到之前已经通过data-id设置好了的id号
        // console.log( $(this).attr('data-id'))
        var dataId = $(this).data('id');

        // 如果这个被点击的复选框有checked，则将 id 加到数组中
        if ($(this).prop('checked')) {
          checkedBox.push(dataId);
        } else {
          // 如果当前被点击的复选框没有被checked，将其 id 从数组移除
          checkedBox.splice(checkedBox.indexOf(dataId) , 1);
        }

        // 最后根据数组中剩下的数量来决定是否显示"批量删除"按钮
        checkedBox.length ? deleteAll.fadeIn() : deleteAll.fadeOut();

        // 将数组中的有checked的复选框 id 通过url传给服务端
        deleteAll.prop('search' , "?id=" + checkedBox);
        // 1. 用到了DOM对象中的search属性，直接加到当前的href中
        // 2. 字符串 + 数组 ，拼接起来，数组会自动转为以逗号分隔的字符串
      })

      // 全选与全不选功能,就是将全选复选框状态与所有子复选框状态相等就可以了
      $('thead input').on('change',function(){
        var status = $(this).prop('checked');
        // 在其他事件中触发另一事件，用扳机API trigger
        everyCheckbox.prop('checked' , status).trigger('change')
      })

      



    })



  </script>
  <script>NProgress.done()</script>
</body>
</html>
