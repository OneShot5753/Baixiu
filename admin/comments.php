<?php 


// 引入封装文件，调用session校验函数
require_once '../functions.php';
verify_session_user();

 ?>


<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <link rel="stylesheet" href="/static/assets/vendors/layui/css/layui.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">

    <!-- 调用navbar的公共部分 -->
    <?php include 'include/navbar.php'; ?>
    
    <div class="container-fluid">
      <div class="page-title">
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
        <div id="page"></div>
      </div>
      <table class="table table-striped table-bordered table-hover" id="tab">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>作者</th>
            <th width="500">评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="170">操作</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>

  <?php $current = 'comments';?>
  <?php  include 'include/sidebar.php';?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/layui/layui.js"></script>
  <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
  <script type="text/x-jsrender" id="tmp" >
    <!-- 在这里面写模板文件 -->
    {{for comments}}
    <tr {{if status === 'rejected'}} class="danger" {{else status === 'approved'}} class="success" {{/if}} data-id="{{:id}}">
      <td class="text-center"><input type="checkbox"></td>
      <td>{{:author}}</td>
      <td>{{:content}}</td>
      <td>{{:post_title}}</td>
      <td>{{:created}}</td>
      <td>{{:status === 'held'? '待审':status==='approved'? '已批准':'已拒绝'}}</td>
      <td class="text-center" >
        {{if status == 'held'}}
        <a href="post-add.php" class="btn btn-success btn-xs">批准</a>
        <a href="javascript:;" class="btn btn-warning btn-xs">拒绝</a>
        {{/if}}
        <a href="javascript:;" class="btn btn-danger btn-xs" id="delete">删除</a>
      </td>
    </tr>
    {{/for}}


  </script>
  <script>
    $(function(){

      // 时机: 静态 页面加载完后
      // 事情: Ajax请求服务端拿到数据，并将其渲染到界面上
      
      // nprogress
      $(document)
      .ajaxStart(function (){
        NProgress.start()
      })
      .ajaxStop(function (){
        NProgress.done()
      })
      

      // 方法1： 通过ajax获取评论数据并将其分页展示在页面中   可以还加一个参数用来设置显示条数
      function ajax_get_comments(current){
        $('#tab tbody').fadeOut(100)
        $.getJSON(
          '/admin/api/get-comment.php' , 
          {'page':current || 1} ,
          function(data){

            var datas = JSON.parse(data.comments_data);

            // 模板 + 数据 ==> 有数据的html结构
            var html = $('#tmp').render({
              comments:datas
            })
            $('#tab tbody').html(html).fadeIn()

            // 调用分页处理函数方法,传入总条数和当前页码（从服务端来）
            layui_page_comments(data.all , data.curr)
          })
          
      }

      // 方法二： 运用layui框架，分页展示数据（数据总条数，当前页码）
      function layui_page_comments(all,now){
        layui.use('laypage', function(){
          //执行一个laypage实例
          var laypage = layui.laypage;
      
          laypage.render({
            elem: 'page' ,
            count: all,
            limit: 30,  
            curr: now || 1, 
            layout: ['count', 'prev', 'page', 'next', 'refresh', 'skip'],
            jump: function(obj, first){
                  //obj包含了当前分页的所有参数
                  // console.log(obj.curr);

                  //首次不执行,使用原始的curr,后面需要自己通过回传来更新
                  if(!first){
                      ajax_get_comments(obj.curr)
                  }
              }
          });
            

        });

      }

      ajax_get_comments()

      // 删除按钮 ajax请求
      // on的方法尤其适用于静态元素下动态添加的后代元素，利用了回调函数
      $('tbody').on('click','#delete',function(){
        var id = parseInt($(this).parent().parent().data('id'))

        $.get('/admin/api/delete-comment.php' , {delete:id} ,function(){
          // 当服务端删除成功后，应该刷新原来的分页界面（通过ajax再分页一次）
          ajax_get_comments()
          // cao!!!  如何才能拿到ajax_get_comments()里面的变量，闭包???
        })
      })

    })


  </script>
  <script>NProgress.done()</script>
</body>
</html>
