<?php 

/**
 * 接收setting.php 中Ajax请求，处理文件上传
 */

if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {

	$files = $_FILES['file'];
	// 拿到上传文件的临时目录
	$temp = $files['tmp_name'];
	// 拿到目标路径附加文件名
	$target = "../static/uploads/" . '.' . $files['name'];

	$move = move_uploaded_file($temp, $target);
	if (!$move) {
		exit('文件上传失败');
	}
	
	$path = substr($target, 2);

	echo $path;
} 


