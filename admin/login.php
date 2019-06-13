<?php 

// Demand:
// 1. 验证表单：验证完整性 + 验证数据正确性
// 2. 将填写数据持久化
// 3. 响应，返回验证结果

// 调用配置文件，需要配置文件中的数据库参数
include '../config.php';

// 启动Session会话
session_start();

function login(){
  // 验证两个表单元素的完整性
  if (empty($_POST['email'])) {
    $GLOBALS['fault'] = '请输入邮箱！';
    return;
  }
  if (empty($_POST['password'])) {
    $GLOBALS['fault'] = '请输入密码！';
    return;
  }
  // 验证两个表单元素的正确性，先假设性验证
  // if( $_POST['email'] !== '123@com'){
  //   $GLOBALS['fault'] = '请先注册！';
  //   return;
  // }
  // if( $_POST['password'] !== '12345'){
  //   $GLOBALS['fault'] = '密码错误！';
  //   return;
  // }
  $email = $_POST['email'];
  $password = $_POST['password'];
  // 接入数据库进行验证
  $connect = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
  if(!$connect){
    $GLOBALS['fault'] = '登录失败！';
    return;
  }
  // 注意SQL语句中的PHP变量，双引号才能解析,变量必须由单引号和花括号包围
  $query = mysqli_query($connect,"select * from users where email = '{$email}' limit 1;");
  if(!$query){
    $GLOBALS['fault'] = '邮箱错误！';
    return;
  }
  // 取出email所对应的那一行数据，目的是拿到对应的密码进行验证
  $user = mysqli_fetch_assoc($query);
  if(!$user){
    $GLOBALS['fault'] = '登录失败！';
    return;
  }
  // 数据库中保存的密码以及用户填写的密码在真实环境下都是加密的
  if($user['password'] !== $password){
    $GLOBALS['fault'] = '密码错误！';
    return;
  }

  // 记录登录状态，设置Session，储存单个用户所有数据以便后面对用户的识别
  $_SESSION['logined_user'] = $user;

  // 验证成功，才能跳转页面
  header('Location: /admin/index.php');
}


if($_SERVER['REQUEST_METHOD'] === 'POST'){
  login();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'logout') {
  unset($_SESSION['logined_user']);
}

?>


<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <link rel="stylesheet" href="/static/assets/vendors/animate/animate.css">
</head>
<body>
  <div class="login">
    <!-- 确保form的action属性、method属性。为其加上有错误就shake的css特效 -->
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method='post' class="login-wrap <?php echo isset($fault)? 'shake animated':''?> " autocomplete="off">
      <img class="avatar" src="/static/assets/img/default.png">

      <!-- 有错误信息时展示，在静态页面就要写好结构 -->
      <?php if(isset($fault)): ?>
      <div class="alert alert-danger text-center">
        <strong>错误！<?php echo $fault ; ?></strong> 
      </div>
      <?php endif ?>

      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus value= <?php echo isset($_POST['email'])? $_POST['email'] : ''?> >
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <!-- form组内必须要有button按钮或者type为submit的按钮，否则无法提交数据 -->
      <button class="btn btn-primary btn-block">登 录</button>
    </form>
  </div>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script>

    // 页面加载完毕后再执行js代码
    $(function(){

      // OUTLINE:
      // 1. 选择恰当时机：邮箱失去焦点时
      // 2. 做合适的事情：发起ajax请求，将从服务端拿回来的数据呈现在界面中
      

      // 注册邮箱失去焦点事件
      $('#email').on('blur', function(){
        var userEmail = $(this).val();
        // ★★★避免空字符串和不是邮箱的文本格式
        var exp = /^[a-zA-Z0-9_.-]+[@][a-zA-Z0-9_.-]+([.][a-zA-Z]+){1,2}$/;
        if(!userEmail || !exp.test(userEmail)) return;
        // console.log(userEmail); 确保拿到正确邮箱
        
        // 如果失去焦点时邮箱与上一次重复，不发起请求????
        // console.log(userEmail)
        // if(userEmail === lastInput) return;

        // 失去焦点并能正确拿到邮箱，发起ajax请求
        $.get('/admin/api/avatar.php' , { email:userEmail } , function(data){
          // console.log(data); 确保拿到服务端数据
          
          // 如果输入的邮箱数据库找不到数据，不再执行添加到img
          if (!data) return;

          // 可以正确拿到头像数据，才呈现到页面上
          $('.avatar').fadeOut(function(){
            // 先淡出原来的，淡出后做回调函数的事情
            $(this).on('load',function(){
              // 等到加载完成，再淡入进来
              $(this).fadeIn()
            }).attr('src' , data);
          })
        })
      })
    })
      
  </script>
</body>
</html>

<?php 

// bug： 初次打开登录界面，输入数据库不存在的邮箱，失去焦点，默认灰色头像跟着失去了
// 失去焦点再获得焦点再失去焦点，会重复请求头像不断的淡入淡出

 ?>