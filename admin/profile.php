
<?php 


// 引入封装文件，调用session校验函数
require_once '../functions.php';
$user = verify_session_user();

function update_user() {
  global $user;
  if (empty($_FILES['avatar'])
   || empty($_POST['email'])
   || empty($_POST['slug'])
   || empty($_POST['nickname'])
   || empty($_POST['bio'])) {
      $GLOBALS['fault'] = '请输入完整的表单';
      return;
  }
  
  $email = $_POST['email'] ||  $user['email'];
  $slug = $_POST['slug'] ||  $user['slug'];
  $nickname = $_POST['nickname'] ||  $user['nickname'];
  $bio = $_POST['bio'] ||  $user['bio'];
  $id = $user['id'];

  $affect = affectd_database("update users set email='{$email}',slug='{$slug}',nickname='{$nickname}',bio='{$bio}' where id = '{$id}' ");
  if ($affect <= 0) {
    $GLOBALS['fault'] = '数据更新失败';
    return;
  }

}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  update_user();
}



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
      <div class="page-title">
        <h1>我的个人资料</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($fault)): ?>
      <div class="alert alert-danger">
        <strong><?php echo $fault ?></strong>
      </div>
      <?php endif ?>
      <form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF'] ?>" method='post' enctype='multipart/form-data'>
        <div class="form-group">
          <label class="col-sm-3 control-label">头像</label>
          <div class="col-sm-6">
            <label class="form-image">
              <input id="avatar" type="file" name="avatar">
              <img src="<?php echo $user['avatar'] ?>">
              <i class="mask fa fa-upload"></i>
            </label>
          </div>
        </div>
        <div class="form-group">
          <label for="email" class="col-sm-3 control-label">邮箱</label>
          <div class="col-sm-6">
            <input id="email" class="form-control" name="email" type="type" value="<?php echo $user['email']; ?>" placeholder="邮箱" readonly>
            <p class="help-block">当前登录邮箱不允许修改</p>
          </div>
        </div>
        <div class="form-group">
          <label for="slug" class="col-sm-3 control-label">别名</label>
          <div class="col-sm-6">
            <input id="slug" class="form-control" name="slug" type="type" value="<?php echo $user['slug'] ?>" placeholder="slug">
            <p class="help-block">https://zce.me/author/<strong>zce</strong></p>
          </div>
        </div>
        <div class="form-group">
          <label for="nickname" class="col-sm-3 control-label">昵称</label>
          <div class="col-sm-6">
            <input id="nickname" class="form-control" name="nickname" type="type" value="<?php echo $user['nickname'] ?>" placeholder="昵称">
            <p class="help-block">限制在 2-16 个字符</p>
          </div>
        </div>
        <div class="form-group">
          <label for="bio" class="col-sm-3 control-label">简介</label>
          <div class="col-sm-6">
            <textarea id="bio" class="form-control" placeholder="Bio" cols="30" rows="6" name="bio"><?php echo $user['bio'] ?></textarea>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-6">
            <button type="submit" class="btn btn-primary">更新</button>
            <a class="btn btn-link" href="password-reset.php">修改密码</a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php $current = 'profile'; ?>
  <?php  include 'include/sidebar.php';?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
    <script>
    $(function(){
      $('#avatar').on('change',function(){
        // console.log($(this)) ---> 可见当前input对象之中有一个files对象
        
        // 利用H5中的FormData，与ajax结合，异步上传二进制文件

        var files = $(this).prop('files')
        if (!files) return
        var fileData = new FormData()
        // 将文件和文件相关参数传递进去
        fileData.append('file',files[0])


        // 发起ajax请求： 原生方式 / jquery方式 都可 （原生方式更简单）
        var xhr =new XMLHttpRequest()
        xhr.open('POST','/admin/upload.php')
        xhr.send(fileData) // 将formData文件传递到服务端
        xhr.onload = function(){
          $('#avatar + img').attr('src',this.responseText).fadeIn()
        }
        
      })
    })

  </script>
</body>
</html>
