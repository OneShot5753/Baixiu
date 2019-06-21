
<?php $current = isset($current)? $current : ""; ?>
<!-- 如果被调用的文件中没有设置$current变量，那么我这里就将其设为空,防止报错找不到 -->

<?php 

// 时刻记着，此文件是公共部分代码，是其他许多页面当中的一个身体的部分


// Demand： 通过数据库，动态更新用户昵称和头像
//通过储存在客户端的Session来确定哪一个用户

// session_start(); 被调用文件已经启动，可加可不加

// 调用此文件的其他文件必须有session才有访问权限，所以直接拿到这个session就好

// 可以调用functions封装文件调用封装函数拿到session中的user数据，也可以直接拿
// require_once '../functions.php';
// $current_logined_user = verify_session_user();

$current_logined_user = $_SESSION['logined_user'];

// 使用户头像与昵称跟随数据库更新而更新，本地存储的Session是不会实时改变的
$id = $current_logined_user['id'];
require_once '../functions.php';
$user = query_database_one("select * from users where id = $id;")





 ?>



  <div class="aside">
    <div class="profile">
      <a href="/admin/profile.php">
      <img class="avatar" src="<?php echo $user['avatar'] ?>">
      <h3 class="name"><?php echo $user['nickname'] ?></h3>
      </a>
    </div>
    <ul class="nav">

      <li <?php echo $current === 'index'?  'class="active"' : '';?> >
        <a href="index.php"><i class="fa fa-dashboard"></i>首页</a>
      </li>

      <!-- 定义一个数组，存放$current可能出现的值 -->
      <?php $possible = ['posts','post-add','categories']; ?>
      <!-- 如果$current的值在数组中可以找到，就设置选中状态，否则不设置 -->
      <li <?php echo in_array($current , $possible)?  'class="active"' : '';?> >
        <!-- 如果$current的值在数组中可以找到，就取消折叠样式，否则设置折叠   -->
        <a href="#menu-posts" <?php echo in_array($current , $possible)?  '' : 'class="collapse"';?>  data-toggle="collapse">
          <i class="fa fa-thumb-tack"></i>文章<i class="fa fa-angle-right"></i>
        </a>
        <!-- 如果$current的值在数组中可以找到，就设置打开折叠，否则不设置 -->
        <ul id="menu-posts" class="collapse <?php echo in_array($current, $possible)?  'in' : '';?> ">
        <!-- 以下每一个li，如果$current的值等于所在目录设定值，则设置为选中状态 -->
          <li <?php echo $current === 'posts'?  'class="active"' : '';?> ><a href="posts.php">所有文章</a></li>
          <li <?php echo $current === 'post-add'?  'class="active"' : '';?>><a href="post-add.php">写文章</a></li>
          <li <?php echo $current === 'categories'?  'class="active"' : '';?>><a href="categories.php">分类目录</a></li>
        </ul>
      </li>

      <li  <?php echo $current === 'comments'?  'class="active"' : '';?> >
        <a href="comments.php"><i class="fa fa-comments"></i>评论</a>
      </li>

      <li  <?php echo $current === 'users'?  'class="active"' : '';?> >
        <a href="users.php"><i class="fa fa-users"></i>用户</a>
      </li>

      <?php $possible2 = ['nav-menus','slides','settings']; ?>
      <li <?php echo in_array($current , $possible2)?  'class="active"' : '';?>>
        <a href="#menu-settings" <?php echo in_array($current , $possible2)?  '' : 'class="collapse"';?> data-toggle="collapse">
          <i class="fa fa-cogs"></i>设置<i class="fa fa-angle-right"></i>
        </a>
        <ul id="menu-settings" class="collapse   <?php echo in_array($current ,$possible2)?  'in' : '';?> ">
          <li  <?php echo $current === 'nav-menus'?  'class="active"' : '';?> ><a href="nav-menus.php">导航菜单</a></li>
          <li  <?php echo $current === 'slides'?  'class="active"' : '';?> ><a href="slides.php">图片轮播</a></li>
          <li  <?php echo $current === 'settings'?  'class="active"' : '';?> ><a href="settings.php">网站设置</a></li>
        </ul>
      </li>
    </ul>
  </div>
