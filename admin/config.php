<?php 
!defined('APP_PATH') AND define('APP_PATH', rtrim(str_replace('\\', '/', realpath(dirname(__FILE__) . '/../')), '/') . '/');
if(!file_exists(APP_PATH.'save/config.php')){ exit("input error!");}
include APP_PATH.'include/class.main.php';include APP_PATH.'save/config.php';
if(function_exists("opcache_reset")){opcache_reset();} //清除PHP脚本缓存
session_start(); $webapi="http://xymov.nohacks.cn";$website="http://nohacks.cn";
// 后台登录校验：账号密码取自 save/config.php（原版把密码硬编码在代码里）
$__auth_user = (isset($CONFIG["user"]) && $CONFIG["user"] !== '') ? $CONFIG["user"] : 'admin';
$__auth_pass = (isset($CONFIG["pass"]) && $CONFIG["pass"] !== '') ? $CONFIG["pass"] : 'admin';
$__auth_hash = md5($__auth_user . $__auth_pass);
if(empty($_SESSION['hashstr']) || filter_input(INPUT_COOKIE, 'login_token')!=='1' || !hash_equals($__auth_hash, (string)$_SESSION['hashstr'])){echo "<script>location.href='load.php?url=login.htm'</script>";exit();}
$username=$_SESSION['username'];






