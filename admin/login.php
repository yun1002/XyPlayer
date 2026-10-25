<?php 
!defined('APP_PATH') AND define('APP_PATH', rtrim(str_replace('\\', '/', realpath(dirname(__FILE__) . '/../')), '/') . '/');
if(!file_exists(APP_PATH.'save/config.php')){ exit("input error!");}
include APP_PATH.'include/class.main.php';include APP_PATH.'save/config.php';
$action=filter_input(INPUT_GET, 'action');session_start();
switch ($action){ 
   case  "validate":  //验证码
      include 'validate.php';		
      exit;
   case  "logout":    //退出登录  
     
      if(!empty($_SESSION['hashstr'])){unset($_SESSION['hashstr']);unset($_SESSION['username']);}  
      if (filter_has_var(INPUT_COOKIE, 'login_token')){setcookie('login_token', '', time() - 3600*24, "/"); }  // 删除Cookie
      ShowMsg('注销登录成功！',"./");
      exit;
      
   default : //登录 
       if(!filter_has_var(INPUT_POST, 'username') || !filter_has_var(INPUT_POST, 'password')){ ShowMsg('用户和密码没填写完整!','-1'); exit(); } 
       
       // 验证码验证
       $validate = filter_input(INPUT_POST, 'validate');
       $session_validate = isset($_SESSION['authnum_session']) ? $_SESSION['authnum_session'] : '';
       if(empty($validate) || empty($session_validate) || strtolower($validate)!==strtolower($session_validate)){
           ShowMsg('验证码错误!','-1'); exit(); 
       }
       
       // 获取用户名密码
       $username = trim(htmlspecialchars(filter_input(INPUT_POST, 'username')));  
       $password = trim(filter_input(INPUT_POST, 'password')); 
       
       // 读取配置中的账号密码（原版此处硬编码，导致后台改密码无效）
       $user = (isset($CONFIG['user']) && $CONFIG['user'] !== '') ? $CONFIG['user'] : 'admin';
       $pass = (isset($CONFIG['pass']) && $CONFIG['pass'] !== '') ? $CONFIG['pass'] : 'admin';
       
       // 登录验证（使用 hash_equals 做恒定时间比较）
       if(hash_equals((string)$user, (string)$username) && hash_equals((string)$pass, (string)$password))
       {     //登录成功  
              $hashstr=md5($username.$password);       //构造session安全码
              session_regenerate_id(true);
              setcookie('login_token', '1', time() + 3600*24, "/"); // 设置有效期24小时
              $_SESSION['hashstr']=$hashstr; $_SESSION['username']=$username;
	          ShowMsg('成功登录，正在转向管理管理主页！',"./");	
	          exit();
       }else{  
              ShowMsg('用户名或密码错误！',"-1");
	     exit();
       }
          
}




 