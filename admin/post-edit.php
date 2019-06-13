<?php 


// 引入封装文件，调用session校验函数
require_once '../functions.php';
$user = verify_session_user();
var_dump($user);



// 获取分类列表数据并在混编中遍历呈现到下拉菜单中
$get_category = query_database_all("select * from categories;");

function edit_posts(){

  global $current_post;

  $title = isset($_POST['title'])? $_POST['title']:$current_post['title'];
  $content = isset($_POST['content'])? $_POST['content']:$current_post['content'];
  $slug = isset($_POST['slug'])? $_POST['slug']:$current_post['slug'];
  $feature = isset($_POST['feature'])? $_POST['feature']:$current_post['feature'];;
  $category = $_POST['category'];
  $created = isset($_POST['created'])? $_POST['created']:$current_post['created'];
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

  $id = $current_post['id'];
  echo $id;

  // 字符串和文件路径持久化到数据库
  $update = affectd_database("UPDATE posts set title='{$title}',content='{$content}',slug='{$slug}',feature='{$path}',category_id={$category},created='{$created}',`status`='{$status}' WHERE id = {$id};");
  if ($update<=0) {
    $GLOBALS['fault'] = '文章更新失败,请重试。';
    return;
  }

   // $GLOBALS['fault'] = '文章更新成功';
  // header('Location: /admin/posts.php');
}


if(isset($_GET['id'])){
  // 如果接收到参数id，获取这个id对应的文章相关数据 并呈现在界面上
  $id = $_GET['id'];
  $current_post = query_database_one("select * from posts where id = $id");
  if (!$current_post) {
    exit('查询数据失败！');
  }
  // 在有参数id的同时，再判断是否是POST请求，决定是否执行更新数据
  if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {
    edit_posts();
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
      <?php endif ?>

      <div class="alert alert-danger">
        <strong>此页有待维护，暂时无法使用</strong>
      </div>

      <form class="row" action="<?php echo $_SERVER['PHP_SELF'] ?>" method='post' enctype = "multipart/form-data">
        <div class="col-md-9">

          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" class="form-control input-lg" name="title" type="text" value="<?php echo $current_post['title']; ?>" autocomplete="off" >
          </div>

          <div class="form-group">
            <label for="content">正文</label>
            <div id="editorbar" class="toolbar"></div>
            <div style="padding: 5px 0; color: #ccc">  </div>
            <div id="editortext" class="text">
              <p><?php echo $current_post['content']; ?></p>
            </div>
            <textarea id="text1" name="content" style="display: none;" ></textarea>
          </div>
        </div>

        <div class="col-md-3">
          <div class="form-group">
            <label for="slug">别名</label>
            <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo $current_post['slug']; ?>" autocomplete="off">
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
              <option value="<?php echo $row['id']; ?>" <?php echo $current_post['category_id']==$row['id']? 'selected':''; ?>><?php echo $row['name']; ?></option>
              <?php endforeach ?>
            </select>
          </div>

          <div class="form-group">
            <label for="created">发布时间</label>
            <input id="created" class="form-control" name="created" type="datetime-local" value="<?php echo $current_post['created']; ?>">
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
            <button class="btn btn-primary" type="submit">更新</button>
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
  <script>NProgress.done()</script>

</body>
</html>
