<?php 


// 引入封装文件，调用session校验函数
require_once '../functions.php';
$user = verify_session_user();
// var_dump($user);

// 获取分类列表数据并在混编中遍历呈现到下拉菜单中
$get_category = query_database_all("select * from categories;");

function add_posts(){
  // 表单校验 偷工减料一下
  if (empty($_POST['title'])
       || empty($_POST['content']) 
       || empty($_POST['slug'])  
       || empty($_FILES['feature']) 
       || empty($_POST['category']) 
       || empty($_POST['created']) 
       || empty($_POST['status']) ) {
    
    $GLOBALS['fault'] = '请将所有表单填写完整';
    return;
  }

  $title = $_POST['title'];
  $content = $_POST['content'];
  $slug = $_POST['slug'];
  $feature = isset($_POST['feature'])? $_POST['feature']:'';
  $category = $_POST['category'];
  $created = $_POST['created'];
  $status = $_POST['status'];
  $file = $_FILES['feature'];
  global $user;

    // 如果没有文件上传错误代码，则文件持久到静态文件夹
  if ($file['error'] === UPLOAD_ERR_OK) {
    $temp = $file['tmp_name'];
    $target = '../static/uploads/' . $file['name']; // 不能在移动文件时写绝对路径，操
    $move = move_uploaded_file($temp, $target);
    if (!$move) {
      $GLOBALS['fault'] = '图片添加失败,请重试。';
      return;
    }
    $path = substr($target,2);
  } else {
      $GLOBALS['fault'] = '上传文件出错，请重新上传';
      return;
  }
  

  // 字符串和文件路径持久化到数据库
  $insert = affectd_database("insert into posts (title,content,slug,feature,category_id,created,`status`,user_id) VALUES ('{$title}','{$content}','{$slug}','{$path}',{$category},'{$created}','{$status}','{$user['id']}')");
  if ($insert<=0) {
    $GLOBALS['fault'] = '文章添加失败,请重试。';
    return;
  }


  // 响应: 可以是当前页面提示信息，也可以直接跳转到所有文章页面，及时看到文章更新
  // $GLOBALS['success'] = '添加成功';
  header('Location: /admin/posts.php');
}


if($_SERVER['REQUEST_METHOD'] === 'POST'){
  add_posts();
}

// 如果接收到参数id，获取这个id对应的文章相关数据 并呈现在界面上
if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $current_post = query_database_one("select * from posts where id = $id");
  if (!current_post) {
    exit('查询数据失败！');
  }
}

 ?>


<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Add new post &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
  <style type="text/css">
        .toolbar {
            border: 1px solid #ccc;
        }
        .text {
            border: 1px solid #ccc;
            height: 500px;
        }
    </style>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">

    <!-- 调用navbar的公共部分 -->
    <?php include 'include/navbar.php'; ?>
  
    <div class="container-fluid">
      <div class="page-title">
        <h1>写文章</h1>
      </div>
      <!-- 有失败或者成功信息时展示 -->
      <?php if (isset($fault)): ?>
      <div class="alert alert-danger">
        <strong><?php echo $fault ?></strong>
      </div>
      <!-- <?php// elseif (isset($success)):?>
      <div class="alert alert-success">
        <strong><?php// echo $success ?></strong>
      </div> -->
      <?php endif ?>

      <form class="row" action="<?php echo $_SERVER['PHP_SELF'] ?>" method='post' enctype = "multipart/form-data">
        <div class="col-md-9">

          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" class="form-control input-lg" name="title" type="text" placeholder="<?php echo isset($_POST['title'])? $_POST['title']:'文章标题'; ?>" autocomplete="off" >
          </div>

          <div class="form-group">
            <label for="content">正文</label>
            <div id="editorbar" class="toolbar"></div>
            <div style="padding: 5px 0; color: #ccc">  </div>
            <div id="editortext" class="text">
              <p><?php echo isset($_POST['content'])? $_POST['content']:'请输入内容'; ?></p>
            </div>
            <textarea id="text1" name="content" style="display: none;" ></textarea>
          </div>
        </div>

        <div class="col-md-3">
          <div class="form-group">
            <label for="slug">别名</label>
            <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo isset($_POST['slug'])? $_POST['slug']:''; ?>" autocomplete="off">
            <p class="help-block">https://zce.me/post/<strong>slug</strong></p>
          </div>

          <div class="form-group">
            <label for="feature">特色图像</label>
            <!-- show when image chose -->
            <img class="help-block thumbnail" style="display: none">
            <input id="feature" class="form-control" name="feature" type="file">
          </div>

          <div class="form-group">
            <label for="category">所属分类</label>
            <select id="category" class="form-control" name="category">
              <?php foreach ($get_category as $row): ?>
              <option value="<?php echo $row['id']; ?>" <?php echo isset($_POST['category'])&&$_POST['category']==$row['id']? 'selected':''; ?>><?php echo $row['name']; ?></option>
              <?php endforeach ?>
            </select>
          </div>

          <div class="form-group">
            <label for="created">发布时间</label>
            <input id="created" class="form-control" name="created" type="datetime-local" value="<?php echo isset($_POST['created'])? $_POST['created']:''; ?>">
          </div>

          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
              <option value="drafted">草稿</option>
              <option value="published">已发布</option>
              <option value="trashed">回收站</option>
            </select>
          </div>

          <div class="form-group">
            <button class="btn btn-primary" type="submit">保存</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php $current = 'post-add'; ?>
  <?php  include 'include/sidebar.php';?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <!-- wangEditor 富文本编辑器 -->
  <script type="text/javascript" src="/static/assets/vendors/wangEditor/release/wangEditor.min.js"></script>
  <script type="text/javascript">
        var E = window.wangEditor
        var editor = new E('#editorbar', '#editortext')  // 两个参数也可以传入 elem 对象，class 选择器
        // 编辑器内容传到textarea表单提交
        var $text1 = $('#text1')
        editor.customConfig.onchange = function (html) {
            // 监控变化，同步更新到 textarea
            $text1.val(html)
        }
        // 编辑器自定义菜单配置
        editor.customConfig.menus = [
            'head',  // 标题
            'bold',  // 粗体
            'fontSize',  // 字号
            'fontName',  // 字体
            'italic',  // 斜体
            'underline',  // 下划线
            'strikeThrough',  // 删除线
            'foreColor',  // 文字颜色
            'backColor',  // 背景颜色
            'link',  // 插入链接
            'list',  // 列表
            'justify',  // 对齐方式
            'quote',  // 引用
            'emoticon',  // 表情
            'image',  // 插入图片
            'table',  // 表格
            // 'video',  // 插入视频
            'code',  // 插入代码
            'undo',  // 撤销
            'redo'  // 重复
        ];
        editor.customConfig.fontNames = [
          '宋体',
          '微软雅黑',
          'Arial',
          'Tahoma',
          'Verdana',
          '腾讯体'
       ]
        editor.create()
        $text1.val(editor.txt.html())
  </script>
  <script>
    window.onload = function (){


      $('#feature').on('change' , function() {
        // 创建FileReader对象
        var reader = new FileReader()
        // 将选择的文件读取为DataURL并存在reader对象中
        reader.readAsDataURL(this.files[0])
        // 读取完成后，将其放到img的url中去
        reader.onload = function(){
          var preview = document.querySelector('.thumbnail')
          preview.style.display = 'block';
          preview.src = this.result
        }
      })





    }
  </script>
  <script>NProgress.done()</script>

</body>
</html>
