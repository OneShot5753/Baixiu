#  百秀自媒体写作后台

> A self-media writing platform.





**界面预览**



![图片](https://github.com/doubleyao5753/baixiu/blob/master/preview/images/image.png)

![图片](https://github.com/doubleyao5753/baixiu/blob/master/preview/images/image%20(1).png)

![图片](https://github.com/doubleyao5753/baixiu/blob/master/preview/images/image%20(2).png)

![图片](https://github.com/doubleyao5753/baixiu/blob/master/preview/images/image%20(3).png)

![图片](https://github.com/doubleyao5753/baixiu/blob/master/preview/images/image%20(4).png)

![picture](https://github.com/doubleyao5753/baixiu/blob/master/preview/images/ima2ge.png)


[TOC]



## 1. 产品开发完整流程

![图片](https://uploader.shimo.im/f/gBTsAvdbl7MbnIcV.png!thumbnail)

> 完整的软件开发流程究竟是什么样的？
> [http://www.chanpin100.com/article/105638](http://www.chanpin100.com/article/105638)

- **了解网页产品前台与后台的关系：**

![图片](https://uploader.shimo.im/f/qyNmdnikF74it7zg.png!thumbnail)

**省略 需求和设计 ，我们从技术选型开始*

## 2. 技术选型

没什么好选的，只有目前掌握的技术栈：

- 服务端： PHP + MySQL
- 客户端：JQuery  + Bootstrap + plugins ... 

## 3. 数据库结构

![图片](https://uploader.shimo.im/f/bG5w5GxDGpcHHPNX.png!thumbnail)

> 数据库结构是由专门的数据库工程师或者后端开发人员所负责的。

## 4. 项目架构

### 4.1. 基本的目录结构

![图片](https://uploader.shimo.im/f/A0UTXTqE0SAhzTCf.png!thumbnail)

### 4.2. 整合静态资源文件

**静态文件：**服务端不经过任何处理就返回客户端的文件，或者说，单纯由客户端处理的文件。例如：图片、字体、css样式... （静态文件在开发流程的UI设计阶段就应该准备好）
**动态文件：**服务端对请求的文件进行处理，并将处理后的结果返回给客户端的文件，例如：PHP文件、ASP文件、JSP文件...

- 将css、img、js、第三方资源venders等文件夹复制到静态文件夹static下的资产文件夹assets之中。
- 如果用户量大，可以在用户上传资源文件夹uploads文件夹中建立分类子文件夹方便管理。

![图片](https://uploader.shimo.im/f/OHHXF7X08sQjgY2U.png!thumbnail)

### 4.3. 建立项目配置文件

项目配置文件是一个纯PHP文件(config.php)
目的：将项目中公共的多个不同地方都会调用的东西存放在一个文件之中，以便后续修改。
例如：数据库的相关配置

![图片](https://uploader.shimo.im/f/xBuTDkBcM9YDf4Ex.png!thumbnail)
在配置文件中将数据库配置信息定义为常量，再其他php文件中用 
 **require_once ' config.php' ;   **将其引用

> 有关 php.ini配置中的 display_errors 是打开还是关闭的问题
> [https://www.jianshu.com/p/5af8c0ba13e5](https://www.jianshu.com/p/5af8c0ba13e5)

### 4.4. 批量整合后台页面

- 将静态网页文件复制到admin（后台）网站目录下
- 将静态网页转换为动态网页  .html格式  -->  .php格式  （用CMD批量修改）
- 将HTML代码中引入其他文件的相对路径，批量修改为绝对路径
- 将所有代码中url地址中的  .html  替换为 .php

![图片](https://uploader.shimo.im/f/lCz3Hc1iaL8gG0wZ.png!thumbnail)
![图片](https://uploader.shimo.im/f/c4yOdPeCuN4os7I9.png!thumbnail)![图片](https://uploader.shimo.im/f/klUlD4TJprc39jmL.png!thumbnail)
![图片](https://uploader.shimo.im/f/pge0R0GzLVkpOjY0.png!thumbnail)
![图片](https://uploader.shimo.im/f/R0lYDiwPtDUNYlke.png!thumbnail)



### 4.5. 抽离公共部分

![图片](https://uploader.shimo.im/f/oMHgIoN2skE4UdfO.png!thumbnail)![图片](https://uploader.shimo.im/f/Hh2MOYY3ZAs0YfSg.png!thumbnail)
![图片](https://uploader.shimo.im/f/j1GYPIkvY9QgX5B8.png!thumbnail)
... ...
由于后台页面中，侧边栏和顶部导航栏都是一样的，为了缩减代码，提高效率，我们有必要将公用的部分抽离出来单独放到一个文件中，大家共同调用。

- 在动态网页文件当前文件夹新建一个公共部分文件夹 include  ，在里面建立单独的公共部分文件。

![图片](https://uploader.shimo.im/f/NFEuV31gOCwuE49j.png!thumbnail)

- 将公共部分的HTML结构代码存放在各自的PHP文件中

![图片](https://uploader.shimo.im/f/Rpa3pRKebHAdhXU8.png!thumbnail)

- 在所有需要用到公共部分的文件中，将包含这部分的HTML代码替换为php引用代码

![图片](https://uploader.shimo.im/f/47wn36HNly8MLTlV.png!thumbnail)
![图片](https://uploader.shimo.im/f/HiEDIRUq998tcp3a.png!thumbnail)

## 5. 业务迭代开发

### 5.1. 解决公共部分问题

- **解决公共部分侧边栏一级目录的点击高亮显示问题**

Error：由于公共部分的侧边栏默认选中了“仪表盘”的目录，点击其他目录，虽然页面可以跳转，但是选中状态不会发生改变，与“点击谁就选中谁”的需求不一致。需要解决这个问题。

> include 一个文件，就可以看做那个文件的代码被插入到了这里。

1. 在各个调用公共部分的文件的include语句之前，设置一个 $current 变量，每一个文件的这个变量值可以指定自己是谁。

![图片](https://uploader.shimo.im/f/nvVzkdeFcqkMFFKK.png!thumbnail)

2. 在公共sidebar的HTML代码结构中，通过判断这个变量是不是等于目录所对应的那个文件所指定的变量值，来决定要不要加active的样式属性。

![图片](https://uploader.shimo.im/f/3FZ09bP09fMcQEsZ.png!thumbnail)
![图片](https://uploader.shimo.im/f/nFKIXd2KALQismV1.png!thumbnail)
![图片](https://uploader.shimo.im/f/mUi5FYMypms69POt.png!thumbnail)        ... ...
**当然，为了防止找不到$current这变量，在sidebar所有代码开头设置判断isset*

- **解决公共部分侧边栏二级目录的点击高亮显示问题**

由于侧边栏还有二级目录，上面仅仅可以使一级目录点击高亮。
二级目录要考虑的问题：
目录的折叠与展开，选中子目录时，父级目录为展开状态。
**在控制台，对比展开与折叠的目录代码的所有差异！！！**
![图片](https://uploader.shimo.im/f/AJwseRnDmtEsibRo.png!thumbnail)

![图片](https://uploader.shimo.im/f/2IdRdFtoD5ABJNZz.gif)



### 5.2. 用户登录界面功能

核心功能：

1. 完善form标签的action、method等各种属性，表单标签的name属性，确保有提交按钮submit
2. 表单验证：验证完整性 + 验证正确性
3. 通过调用配置文件，接入数据库，用来验证用户填写数据的正确性
4. 页面访问权限控制，在登录页面使用Session记录登录状态，在首页页面验证Session，没有则无权访问，跳转登录页面
5. 除了首页，几乎其他所有后台页面都需要页面访问权限控制，都需要判断有无session，为了优化代码，将检验Session的代码块封装起来

![图片](https://uploader.shimo.im/f/VULzSWO12ZgckXi1.gif)
![图片](https://uploader.shimo.im/f/Wlv7eL72JfgL3N4Y.png!thumbnail)
***>>>>>>>  login.php***
![图片](https://uploader.shimo.im/f/ruX2CbD1lfAjXxfb.png!thumbnail)
![图片](https://uploader.shimo.im/f/IWRiBphTUts66CIy.png!thumbnail)

***>>>>>>>  index.php***
![图片](https://uploader.shimo.im/f/5oprLXvutC4Njntk.png!thumbnail)

***>>>>>>>  封装session校验：functions.php***
![图片](https://uploader.shimo.im/f/bWhFXNYvazYRb6jg.png!thumbnail)
专门建立一个文件储存封装代码。封装之后，所有需要登录权限控制的文件，即可引入文件，调用函数即可。

```
<?php 
// 引入封装文件，调用session校验函数
require_once '../functions.php';
verify_session_user();
?>
```

**现象需求：登录界面动态获取用户登录头像**

![图片](https://uploader.shimo.im/f/YNW72pVj9l0Bwxtu.png!thumbnail)
Demand：邮箱输入完毕后，自动显示邮箱在数据库所对应的头像。
当邮箱表单元素失去焦点时，利用ajax向服务端中的数据库请求头像图片路径并展示在HTML中。

***>>>>>>>  login.php （客户端）***
引入JQuery，在js中注册事件和ajax。
![图片](https://uploader.shimo.im/f/BEb3PzwbYB0GY4SH.png!thumbnail)



***>>>>>>>  avata.php （服务端）***
尽管登录界面也是php文件，但是为了将功能分区，最好将服务端做的事与客户端分开。单独放在一个文件下。
![图片](https://uploader.shimo.im/f/MhiNaD9EysIOSdpg.png!thumbnail)

### 5.3. 更新登录用户信息首页

- **基本信息**

用户登录后，跳转到后台页面，此时应该更新用户的头像、昵称等基本信息。
![图片](https://uploader.shimo.im/f/GO2pfWCuzI8pNQQW.png!thumbnail)
由于这一部分的信息在公共部分的侧边栏中，所以在sidebar.php文件中修改
因为session中存的是user的数据，所以直接从session中拿到即可
![图片](https://uploader.shimo.im/f/uCqaWoS6LIYuDN5l.png!thumbnail)
![图片](https://uploader.shimo.im/f/hwzLIn6Jz5wFI8Kx.png!thumbnail)

> require 引入文件的路径问题：由于各个文件都有互相引用的关系，为了不使路径混乱，我们在配置文件中声明常量获取盘符路径文件夹

![图片](https://uploader.shimo.im/f/42aWl4J68f0eBkz5.png!thumbnail)

- **首页文章统计数据信息**

![图片](https://uploader.shimo.im/f/3uDcGfdxMXYbHQ7M.png!thumbnail)
数据库查询语句（最好先在数据库环境下查询测试一下）
![图片](https://uploader.shimo.im/f/Wlj4gEi8N0UkNSBi.png!thumbnail)
***>>> 数据库查询功能的封装***
封装一个功能，首先写出一个实例，再将其参数抽象出来。
封装函数思维不能太死板，如果有需要，要适当的将这个函数的能力进行扩展，多构想使用场景。例如封装这个数据库查询功能：
由于有许多查询语句需要提取数据，这是封装最常见的情况 ，也是必须的情况。
有时候我们要提取到所有数据，但有时我们又只需要一行数据（例如count），因此封装的时候要尽量将两个不同的能力都封装起来。
![图片](https://uploader.shimo.im/f/XP5wvVqFO9IVITGv.png!thumbnail)
在index.php 以及其他需要查询数据库的文件调用这个函数！！！
![图片](https://uploader.shimo.im/f/jItteb6UoUoJ3IdC.png!thumbnail)
![图片](https://uploader.shimo.im/f/U3xArsd9WlM9PNXg.png!thumbnail)

- **统计数据信息可视化**

接入  chart.js  或者  Echart.js 库

### 5.4. 分类目录页功能

- **动态呈现分类列表**

![图片](https://uploader.shimo.im/f/NpWx8xxu8qUA5adP.png!thumbnail)

1. 调用前面已经封装过的查询数据库函数，拿到所有分类数据
2. 混编，遍历数组并创建行
3. 替换数据为php变量

![图片](https://uploader.shimo.im/f/JeGy2hKfV2E0c1Sg.png!thumbnail)
![图片](https://uploader.shimo.im/f/9dJhhTr0XtIKaraU.png!thumbnail)

- **动态添加分类功能**

1. 完善form表单：包括其中的各种应有属性，例如action、method、name、button
2. 请求服务端，服务端：校验表单+持久化+响应

![图片](https://uploader.shimo.im/f/L0fUAtX5yJI51J40.gif)
![图片](https://uploader.shimo.im/f/XzSyR4ANV5ANU52C.png!thumbnail)
![图片](https://uploader.shimo.im/f/zN3FEDCyeUgHriqz.png!thumbnail)
在此期间，完善了数据库封装功能：添加增删改的操作能力
![图片](https://uploader.shimo.im/f/Q7a6eVM9uJ4Cfydk.png!thumbnail)

- **单条数据删除功能**

1. 点击删除按钮，通过url传参的方式告诉服务端当前的id
2. 服务端接收响应，调用封装的函数进行数据删除，然后返回原来的界面

![图片](https://uploader.shimo.im/f/NpWx8xxu8qUA5adP.png!thumbnail)
![图片](https://uploader.shimo.im/f/wHQPaPKyF6YXBeds.png!thumbnail)
![图片](https://uploader.shimo.im/f/U2vmFGeG9YMdL1KF.png!thumbnail)
**Attention：请求与响应都用绝对路径！**

- **批量删除功能**

Demand：当列表中任意一个或多个复选框被选中时，显示批量删除按钮，点击批量删除即可删除被选中的数据，否则，一个都不选中的时候，隐藏批量删除按钮。（主要是客户端的功能，有JS实现）

1. *拿到所有复选框以及批量删除的按钮，为每一个复选框注册change事件，遍历每一个复选框用prop查看其DOM对象中的checked属性，若有一个为true则淡入按钮，否则淡出。*

![图片](https://uploader.shimo.im/f/NPWQc59GvAIivCFH.png!thumbnail)

1. **拿到所有复选框及批量删除按钮，为每个复选框注册change事件，在事件函数中，定义一个空数组存放被选中的复选框，为当前点击的复选框添加一个附加数据，如果当前复选框有checked则附加数据加到数组中，否则从数组中删去。最后根据数组中的数量来决定“批量删除”按钮的显示与隐藏。**

![图片](https://uploader.shimo.im/f/sg2ktMurvvwGNBH6.png!thumbnail)

1. **在目录列表tbody中为input复选框设置H5中的data-知识，设置data-id为这条数据真正的id编号，**在Js中将那个附加数据替换为真正的id，批量删除按钮的url设置为单条删除服务端的那个文件，用prop()将数组中的id传到url中去，实现单条删除与批量删除共用一个服务端文件。

![图片](https://uploader.shimo.im/f/DSpd00fnSFIdzJUY.png!thumbnail)
![图片](https://uploader.shimo.im/f/fu9T4VLLYwoL6WeQ.png!thumbnail)
![图片](https://uploader.shimo.im/f/ZmWlC1kSnSwihxIX.png!thumbnail)

1. 服务端改造成既支持删除一条也支持删除多条的功能  sql 语句中的 in

![图片](https://uploader.shimo.im/f/XcOgQT8H7RI4pqch.png!thumbnail)

1. 全选与取消全选的功能

![图片](https://uploader.shimo.im/f/dg4kzbkWfdgrmAea.png!thumbnail)

**重难点：**

- jQuery中 .attr() 与 .prop() 的区别和用法
- 多个复选框同时选中的优化写法
- H5 中的data- 以及jQuery中的.data() 的意义和用法
- SQL语句与PHP变量的混合拼接

- **单条数据编辑功能    ****比较复杂，理清思路**

**两个功能: 呈现编辑页面 + 提交表单更新数据**![图片](https://uploader.shimo.im/f/e9TdZK88wJoix5RS.gif)

1. 在编辑按钮中指定url地址为本身绝对路径，并通过url传参将当前按钮的id传给服务端

![图片](https://uploader.shimo.im/f/hkQSBQCbAgsaR0v6.png!thumbnail)

> 由于此时当前文件的PHP代码中已经有 添加分类 和 呈现数据 两个功能，要将 编辑功能 添加进去，不得不认真考虑顺序以及逻辑问题

1. 首先就是要考虑 **通过编辑按钮传的id去从数据库拿到对应数据并将其呈现在页面之中 。**

> 由于编辑功能要与前面默认打开时的添加功能共用一个页面，因此，利用从数据库拿到的数据变量来判定是呈现编辑页面还是呈现默认的添加分类页面

```
  $get_id = $_GET['id'];
  $current_need_edit = query_database_one('SELECT * FROM categories WHERE id = '.$get_id.';');
// 调用之前封装好的查询数据库函数
```

![图片](https://uploader.shimo.im/f/0FwuUFVWxOoMDRQD.png!thumbnail)

1. 确保点击编辑按钮能够呈现编辑功能页面后，再进入子功能二准备：完善编辑页面的form表单，指向自身，同时跟上id
2. **重难点：****提交表单更新分类数据**** 与 ****提交表单添加分类数据**** 糅合在一个PHP文件之中。**

> 理清触发两个功能的时机和先后顺序：添加功能是POST请求但没有传参，编辑功能是POST请求但有参数传递。因此写出判断条件，执行代码的时机

![图片](https://uploader.shimo.im/f/e7gg2rcOH3cjSCLv.png!thumbnail)

1. 在触发判断条件之前完善编辑功能函数 edit_category() 

![图片](https://uploader.shimo.im/f/o09ty0hrdY8bfgUU.png!thumbnail)
![图片](https://uploader.shimo.im/f/PgZmrTJoriAucXHc.png!thumbnail)
分类目录页的完整代码直接去翻源文件。



### 5.5. 所有文章页功能

- **数据动态呈现**

1. 调用查询数据封装功能函数，查询页面中要呈现的指定数据。foreach遍历创建列表，替换变量，即可动态呈现基本数据到页面中

```
// Demand 1： 数据动态呈现在页面中，正确呈现作者名、分类名、发布状态，以及指定时间格式
$posts_data = query_database_all("SELECT title,user_id,category_id,created,`status` FROM posts;");
if (!$posts_data) {
  echo "查询数据库失败";
}
```

![图片](https://uploader.shimo.im/f/GkEgBhMoTrMZw6vK.png!thumbnail)
![图片](https://uploader.shimo.im/f/RIAYNsNJip07Ft00.png!thumbnail)

1. 不能单纯呈现数据库里面存放的数据。我们还应该将其转换成我们希望的数据，作者与分类显示名字、状态显示正确状态、时间显示正确格式。

**状态与时间格式的转换 - 函数的封装与调用**
![图片](https://uploader.shimo.im/f/BC69ymVYowQ1HI1E.png!thumbnail)
![图片](https://uploader.shimo.im/f/JmCxvHQmLi4N1iPf.png!thumbnail)

**作者与分类名的转换 - 数据库关联查询语句**
![图片](https://uploader.shimo.im/f/jDyAvwddQtQAM3sh.png!thumbnail)
![图片](https://uploader.shimo.im/f/STRjNhODDAYMRVbj.png!thumbnail)
![图片](https://uploader.shimo.im/f/JvMVXHRXHWQfvqBI.png!thumbnail)

- **分页页码展示功能   ****比较复杂，理清思路**

1. 数据库有关分页的查询语句，将其添加到php代码中的获取数据中去

![图片](https://uploader.shimo.im/f/NBW6jxLexgU6q1A5.png!thumbnail)

1. **数据分页展示：**点击页码按钮，通过向本页面url传递参数，用来识别第几页，从而做出相应跳转，先手动在地址栏对url传递参数测试，在php中接收这个参数

![图片](https://uploader.shimo.im/f/vZhLuHQBmtA6rnhI.png!thumbnail)

1. **正确显示页码****★★★**

> [https://www.layui.com/demo/laypage.html#!fenye=5](https://www.layui.com/demo/laypage.html#!fenye=5)
> 框架库中写好的分页

```
/*
Demand:
  1. 当前页码显示高亮
  2. 当前高亮左侧和右侧各有2个页码
  3. 开始页码不能小于1
  4. 结束页码不能大于最大页数
  5. 当前页码为1时'上一页'按钮禁用
  6. 当前页码为最大值时'下一页'按钮禁用
  7. 当开始页码不等于1时显示省略号
  8. 当结束页码不等于最大时显示省略号
  9. 永远显示首页和尾页按钮，但不能影响中间的变化
*/
//  php大量的计算，首先就要确保是数字类型，(int）尤为必要!!!!!
```

1. 计算页码：假设页码展示变量$show5个，定义左右偏移变量 $side，开始页码变量$begin，结束页码变量 $end
2. 在页面中以 $begin 为计数器，$end 为终止器进行for循环创建页码，页码中的a标签向自身传参传当前页码数
3. 当前页码高亮显示，在循环中的标签中设置判断，如果循环数字等于当前参数指定页码，那么这个页码就class高亮
4. 求出最大页码：(数据总条数 /  每页展示条数) 向上取整，强制转换int() 确保，尤其注意查询语句，数据总条数应该等于所有数据都存在时的总条数，所以应该关联查询
5. 处理 $begin必须从 1 开始，并调整由此对$end带来的副作用
6. 处理$end必须以 最大页码 结束，并调整由此对$begin 带来的副作用
7. 防止用户在地址栏输入比最小值小比最大值大的参数，设置两个判断跳转网页
8. 页码为 1 时 禁用上一页按钮；页码为 最大页码 时 禁用下一页按钮
9. 开始页码等于1时，隐藏前省略号和首页按钮；结束页码等于最大页码时，隐藏后省略号和尾页按钮
10. 上一页与下一页的变量设置以及写进html结构
11. debug 考虑到$max_page=1也就是总共就一页的情况，应该对$begin再进行一次判断

***>>>成品：***
![图片](https://uploader.shimo.im/f/97YiP266YkUSzyze.gif)
***>>>php代码中的页码计算：***
![图片](https://uploader.shimo.im/f/bjBqyEy0tKEDBOtc.png!thumbnail)
***>>>html混编：***
![图片](https://uploader.shimo.im/f/u7lFWNXpMgccpJ9w.png!thumbnail)

- **筛选功能**

实质：以GET传参的方式提交一个表单，通过参数重新获取对应的数据。 
核心：两个变量 $where  $select
（1）完善form表单相关属性，确保name属性和button按钮。
（2）服务端调用数据库查询函数获取所有分类数据，遍历创建category下拉列表子菜单，并设置value值（参数值）为数据中的id
（3）**筛选数据与获取全部数据发生冲突。**在服务端代码，如果有设置分类的参数，那么定义一个变量存放这个值，以便后续查询数据时作为条件，因为有一个所有分类，$where必须存在，但为了谨防后续查询语句中的where后为空，在判断筛选参数之前将$where先定义为一个恒等式

> 注意点：
> 1.php中的类似+=的运算符，字符串拼接可以 .=
> 2.拼接要注意前后的空格隔开，很不容易发现的bug
> 3.几乎所有数据库查询语句都要加入where语句和这个$where变量

（4）**筛选参数与分页参数发生冲突。**因此需要定义一个变量$select存放参数字符串，并拼接到分页参数后。同样由于逃不过all没有分类展示所有数据的情况，因此判断前定义空值并在判断条件中做拼接。

> 注意要拼接的地方不能漏，尤其是HTML中循环向url中传参的时候最关键
> 操，要加到每一个分页参数的屁股后面！！！

（5）筛选后，下拉菜单的选项要持久在页面上（设置value的selected）
（6）葫芦画瓢。将status的筛选以及value持久写出来

> 注意的是：这里的SQL字符串拼接与category不同，因为这里的参数值是字符串而不是数字，必须要有单引号包裹
> $where .= " and posts.status = '{$para_status}'";

![图片](https://uploader.shimo.im/f/7rpx9rxrCDYlnS8G.png!thumbnail)
![图片](https://uploader.shimo.im/f/JztOr86Nnk8l4zl6.png!thumbnail)

- **单个删除及批量删除功能**

**单个删除：**同分类目录页中的单个删除功能。
同样是新建一个php文件，将删除按钮的href指向这个文件，并在其url之后传入当前行的id。
在php文件中接收这个id并通过SQL语句在数据库中将其永久删除。

> 需要注意：
> 删除执行完毕后，要返回原来的页面，但是由于原来的页面可能通过筛选、分页等在url中传了参数，如何才能原封不动的拿到原来的url地址？
> **利用HTTP请求头中的Referer字段。**

![图片](https://uploader.shimo.im/f/vdihsHta75AIwS6O.png!thumbnail)

**批量删除：同分类目录的批量删除功能。**

***>>>所有文章页面完整代码  posts.php***

### 5.6. 写文章页功能

- **富文本编辑器的接入**

常用富文本编辑器轮子：

- UEditor   [https://ueditor.baidu.com/website/index.html](https://ueditor.baidu.com/website/index.html)
- Tiny  [https://www.tiny.cloud/](https://www.tiny.cloud/)
- CKEditor [https://ckeditor.com/](https://ckeditor.com/)
- wangEditor [http://www.wangeditor.com/](http://www.wangeditor.com/)

怎么用？看官方文档就可以了。
你也可以寻找MarkDown编辑器的轮子接入
根据wangEditor的文档（使用手册），将编辑器接入到页面中去。
![图片](https://uploader.shimo.im/f/bDtUhGlbEro3uoIM.png!thumbnail)

- **原有数据呈现**

呈现数据库中的分类列表
拿到数据，遍历呈现到页面下拉菜单中即可

- **提交文章数据**

1. 完善和检查form表单。确保所有表单元素的name，有文件域要设置form-data。
2. 提交表单三件套：校验表单 + 持久化（文件路径和数据库） + 响应

- **原有文章编辑**

搞了半天卡着了，好好理理get 与post 请求的顺序流程吧。

### 5.7. 评论页功能（Ajax 异步）

- **数据动态呈现与分页**

流程：

1. 普通方式通过url打开评论页，返回所有静态页面元素，其中包含一个空表单
2. Ajax请求服务端，从服务端查询数据库并获取表单数据
3. 与模板引擎配合将有格式的数据呈现在原来的空表单中

![图片](https://uploader.shimo.im/f/9mCrD2dAWXArEyRC.png!thumbnail)
![图片](https://uploader.shimo.im/f/wWazjMv69Q4lZaV3.png!thumbnail)

> 模板引擎不会用？ 查文档 or 找博客

4. 服务端分页获取数据，手动url传参获取指定页的数据
5. 界面设置页码条功能，使其可以对url传参             整了我一整天

![图片](https://uploader.shimo.im/f/0mvxWGycAcosmwDf.png!thumbnail)
***>>> get-comment.php***
![图片](https://uploader.shimo.im/f/1fkhFicEhsggtsma.png!thumbnail)

> 数据呈现用到了 **模板引擎 **
> 分页功能调用了** layui** 组件库配合ajax来实现 （重难点）学会用轮子

***用Ajax的方式  VS  服务端直输出方式***

- **单条数据的删除功能**

1. 为了后续批量删除，利用h5中的data-属性为行<tr>设置id，而不是设在<td>。
2. Js中，为之前ajax**动态创建的**删除按钮绑定点击事件，点击则对服务端发起ajax请求，传递当前行的id，删除当前行

> on的方法尤其适用于静态元素下动态添加的后代元素，利用了委托事件

1. 服务端接收 id ， 在数据库中进行删除
2. Debug：点击删除按钮执行删除数据，页面应该及时得到刷新，有分页的情况下应该使界面保持在原来页码界面。

### 5.8. 网站设置页功能

- **异步文件即传即用**

![图片](https://uploader.shimo.im/f/IJqGfpbHkHULZbNQ.gif)

1. 自定义表单控件：见html+css部分笔记

```
<label>
    <input type='file'>
    <img src=''>
    <i></i>
</label>
隐藏的表单元素、显示的其他元素 都放在《lable》标签中
```

![图片](https://uploader.shimo.im/f/DSw8TBLCdKM5qXgb.gif)

1. **利用H5中的FormData，与ajax结合，异步上传二进制文件**

![图片](https://uploader.shimo.im/f/X4P74zYOcK0z2Y1L.png!thumbnail)

> 关于 FormData  [点击我](https://blog.csdn.net/wangmx1993328/article/details/79947525)
>
> 1. 客户端：创建FormData对象，传入文件及其文件参数，将FormData二进制文件通过Ajax传递到服务端
> 2. 服务端：确认有文件上传后，将文件从临时目录转移到静态资源目录，并拿到路径将其返回给Ajax。

![图片](https://uploader.shimo.im/f/hGcVq64OCxkNHyTf.png!thumbnail)
![图片](https://uploader.shimo.im/f/EIEtIcjyklQ0ebYa.png!thumbnail)

## 6. 完成剩余

- [x] 用户管理页功能
- [x] 个人中心页修改个人信息
- [ ] Bug：评论列表页，点击操作按钮执行操作后立即拿到页码并重新加载当前分页
- [ ] 解决上一个bug才能做 删除、批准、拒绝 以及批量操作的功能
- [ ] 编辑文章功能，点击“编辑”按钮，跳转到“写文章页面”，同时有对应数据显示
- [ ] 仪表盘后台首页，数据可视化
- [ ] 导航菜单页所有功能
- [ ] 图片轮播设置页所有功能
- [ ] 网站设置页功能
- [ ] 前台页面所有



























































