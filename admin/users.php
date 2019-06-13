<?php 


// 引入封装文件，调用session校验函数
require_once '../functions.php';
verify_session_user();

// 用户管理页功能： 与分类列表页的功能完全一致
// Demand:
// 1. 将数据库数据动态呈现到界面
// 2. 添加功能
// 3. 删除功能
// 4. 编辑功能


// 添加分类功能
function add_users(){
  // 校验表单 + 持久化 + 响应
  if (empty($_POST['email'])) {
    $GLOBALS['fault'] = '请填写邮箱';
    return;
  }
  if (empty($_POST['slug'])) {
    $GLOBALS['fault'] = '请填写别名';
    return;
  }
  if (empty($_POST['nickname'])) {
    $GLOBALS['fault'] = '请填写昵称';
    return;
  }
  if (empty($_POST['password'])) {
    $GLOBALS['fault'] = '请填写密码';
    return;
  }

  $email = $_POST['email'];
  $slug = $_POST['slug'];
  $nickname = $_POST['nickname'];
  $password = $_POST['password'];

  $affect = affectd_database("INSERT INTO users (slug,email,password,nickname) VALUES ( '{$slug}','{$email}','{$password}','{$nickname}');");
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
function edit_users(){

  // 在函数内，将变量暴露给全局，以便页面中数据能根据函数中变量的变化而变化
  global $current_need_edit;

  // 如果有值，就按填写值提交，如果为空，就还是按照原来的数据提交
  $email = isset($_POST['email'])?  $_POST['email'] : $current_need_edit['email'];
  $slug = isset($_POST['slug'])?  $_POST['slug'] : $current_need_edit['slug'];
  $nickname = isset($_POST['nickname'])?  $_POST['nickname'] : $current_need_edit['nickname'];
  $password = isset($_POST['password'])?  $_POST['password'] : $current_need_edit['password'];

  $commit = [$email,$slug,$nickname,$password];
  $current = [$current_need_edit['email'],$current_need_edit['slug'],$current_need_edit['nickname'],$current_need_edit['password']];
  if ($commit === $current) {
    $GLOBALS['fault'] = '写点不一样的吧';
    return;
  }
  
  // 数据更新到数据库，持久化
  $id = $current_need_edit['id'];
  $affect = affectd_database("UPDATE users SET email = '{$email}',slug = '{$slug}',nickname='{$nickname}',password='{$password}' WHERE id = '{$id}';");

  // ★★★将改动过的数据继续呈现在界面上，因为这是全局变量，页面会跟随我的变化
  $current_need_edit['email'] = $email;
  $current_need_edit['slug'] = $slug;
  $current_need_edit['nickname'] = $nickname;
  $current_need_edit['password'] = $password;

  if ($affect <= 0) {
    $GLOBALS['fault'] = '更新数据失败';
    return;
  }
  if ($affect > 0) {
     $GLOBALS['success'] = '更新数据成功';
     return;
  }
}
 
if(empty($_GET['id'])){
  if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    add_users();
  }
} else {
  $get_id = $_GET['id'];
  $current_need_edit = query_database_one('SELECT * FROM users WHERE id = '.$get_id.';');
  if (!$current_need_edit) {
    $GLOBALS['fault'] = '查询数据库失败';
  }
  if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    edit_users();
  }
}

// 1.获取用户所有数据
$list = query_database_all("SELECT * FROM users;");
 ?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Users &laquo; Admin</title>
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
        <h1>用户</h1>
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

      <div class="row">
        <?php if (isset($current_need_edit)): ?>
          <div class="col-md-4">
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $current_need_edit['id'] ?>" method='post' autocomplete="off">
            <h2>编辑用户>>><strong><?php echo $current_need_edit['nickname'];?></strong></h2>
            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" value="<?php echo $current_need_edit['email']; ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" value="<?php echo $current_need_edit['slug']; ?>">
              <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" value="<?php echo $current_need_edit['nickname']; ?>">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="text" value="<?php echo $current_need_edit['password']; ?>">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">更新</button>
            </div>
          </form>
        <?php else: ?>
        <div class="col-md-4">
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method='post' autocomplete="off">
            <h2>添加新用户</h2>
            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" placeholder="邮箱">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="text" placeholder="密码">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
        <?php endif ?>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm" href="/admin/api/users-delete.php" style="display: none" id="deleteAll" >批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
               <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th class="text-center" width="80">头像</th>
                <th>邮箱</th>
                <th>别名</th>
                <th>昵称</th>
                <th>状态</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($list as $row): ?>
              <tr data-id='<?php echo $row['id']; ?>'>
                <td class="text-center"><input type="checkbox"></td>
                <td class="text-center"><img class="avatar" src="<?php echo $row['avatar']; ?>"></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['slug']; ?></td>
                <td><?php echo $row['nickname'] ?></td>
                <td>激活</td>
                <td class="text-center">
                  <a href="/admin/users.php?id= <?php echo $row['id'] ?>" class="btn btn-default btn-xs">编辑</a>
                  <a href="/admin/api/users-delete.php?id= <?php echo $row['id'] ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current = 'users';?>
  <?php  include 'include/sidebar.php';?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
    <script>
    $(function(){
      // 选中任意一个复选框时，显示批量删除按钮，否则隐藏
      var everyCheckbox = $('tbody input');
      var deleteAll = $('#deleteAll');
      // 定义一个数组，用来存放被选中的复选框
      var checkedBox = [];
      everyCheckbox.on('change',function(){
        var dataId = $(this).parent().parent().data('id');
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
