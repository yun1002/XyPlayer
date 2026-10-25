<?php 
/*
'软件名称：XyPlayer 智能视频解析  X4
'模块功能：搜索首页（科技感主题）
'--------------------------------------------------------
'说明：本页为纯展示层，业务逻辑全部来自 include/class.main.php 与 save/config.php。
'配色统一在下面的 $skin 数组中调整，CSS 变量会自动跟随。
'--------------------------------------------------------
*/

//运行根目录（不依赖当前工作目录，可被 index.php 直接包含）
$__ROOT = rtrim(str_replace("\\", "/", dirname(__FILE__)), "/") . "/";

require_once $__ROOT . 'include/class.main.php';
include_once $__ROOT . 'save/config.php';
include_once $__ROOT . 'save/top.inc.php';

$skin = array(
    'color'        => '#22d3ee',                        //主色（青）
    'accent_rgb'   => '34,211,238',                     //主色 RGB，用于半透明
    'color2'       => '#8b5cf6',                        //辅助色（紫）
    'input_border' => 'rgba(34,211,238,.35)',           //搜索框边框
    'input_color'  => '#e8f6ff',                        //搜索文字
    'background'   => '#03060d',                        //页面底色
);

//配置项兜底，避免 PHP 8 下 Undefined array key 警告
$__socode     = isset($CONFIG['socode']) && is_array($CONFIG['socode']) ? $CONFIG['socode'] : array();
$__top_off    = !empty($__socode['top_off']);
$__diy_off    = !empty($__socode['diy_off']);
$__not_off    = isset($__socode['not_off']) ? $__socode['not_off'] : '0';
$__not_val    = isset($__socode['not_val']) ? base64_decode($__socode['not_val']) : '';
$__diy_val    = isset($__socode['diy_val']) ? base64_decode($__socode['diy_val']) : '';
$__footer_off = !empty($CONFIG['FOOTER_LINK']['off']);
$__footer_lnk = isset($CONFIG['FOOTER_LINK']['info']) && is_array($CONFIG['FOOTER_LINK']['info']) ? $CONFIG['FOOTER_LINK']['info'] : array();
$__by         = isset($CONFIG['play']['all']['by']) ? $CONFIG['play']['all']['by'] : '';
$__sitetime   = isset($CONFIG['sitetime']) ? $CONFIG['sitetime'] : '';
$__api_path   = isset($CONFIG['API_PATH']) ? $CONFIG['API_PATH'] : 'api.php';
$__title      = isset($CONFIG['TITLE']) ? $CONFIG['TITLE'] : '';

//热门搜索词：优先取搜索统计（save/top.inc.php），为空时回退到后台配置的 resou
$__hot = array();
if(!empty($TOPDATA) && is_array($TOPDATA)){
    arsort($TOPDATA);
    foreach($TOPDATA as $__k => $__v){ $__hot[$__k] = (int)$__v; }
}
if(empty($__hot) && !empty($CONFIG['resou'])){
    foreach(explode('|', $CONFIG['resou']) as $__w){
        $__w = trim($__w);
        if($__w !== ''){ $__hot[$__w] = 0; }
    }
}

//搜索关键词做转义，修复反射型 XSS
$__wd = filter_input(INPUT_GET, 'wd');
$__wd = is_string($__wd) ? $__wd : '';
$__wd_safe = htmlspecialchars($__wd, ENT_QUOTES, 'UTF-8');

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge"/><meta name="renderer" content="webkit"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
<meta name="theme-color" content="<?php echo $skin['background']; ?>" />
<title><?php echo $__title; ?></title>
<script type="text/javascript" src="./include/jquery.min.js"></script>
<script type="text/javascript" src="./include/class.main.js"></script>
<link href="./include/jquery.autocomplete.css" rel="stylesheet">
<script type="text/javascript" src="./include/jquery.autocomplete.js?ver=1.2"></script>
<style>
:root{
  --bg: <?php echo $skin['background']; ?>;
  --accent: <?php echo $skin['color']; ?>;
  --accent-rgb: <?php echo $skin['accent_rgb']; ?>;
  --accent-2: <?php echo $skin['color2']; ?>;
  --text: <?php echo $skin['input_color']; ?>;
  --muted: #7d9ab8;
  --line: <?php echo $skin['input_border']; ?>;
}
*{box-sizing:border-box;}
html,body{
  margin:0;padding:0;
  width:100%;
  min-height:100%;
  background:var(--bg);
}
body{
  color:var(--text);
  font-family:"PingFang SC","Microsoft YaHei","Segoe UI",system-ui,sans-serif;
  -webkit-font-smoothing:antialiased;
  text-align:center;
  display:grid;
  place-items:center;
  min-height:100vh;
  overflow-x:hidden;
}

/* ================= 背景层 ================= */
.bg{position:fixed;inset:0;z-index:0;overflow:hidden;pointer-events:none;}
#stars{position:absolute;inset:0;width:100%;height:100%;display:block;}

.grid{
  position:absolute;left:-50%;right:-50%;bottom:-30%;height:120%;
  background-image:
    linear-gradient(rgba(var(--accent-rgb),.10) 1px,transparent 1px),
    linear-gradient(90deg,rgba(var(--accent-rgb),.10) 1px,transparent 1px);
  background-size:64px 64px;
  transform:perspective(520px) rotateX(62deg);
  transform-origin:50% 100%;
  animation:gridPan 14s linear infinite;
  -webkit-mask-image:radial-gradient(ellipse 70% 60% at 50% 88%,#000 0%,transparent 78%);
          mask-image:radial-gradient(ellipse 70% 60% at 50% 88%,#000 0%,transparent 78%);
}
@keyframes gridPan{from{background-position:0 0;}to{background-position:0 64px;}}

.glow{position:absolute;border-radius:50%;filter:blur(90px);opacity:.5;mix-blend-mode:screen;}
.glow.g1{width:52vw;height:52vw;left:-12vw;top:-16vw;background:radial-gradient(circle,rgba(var(--accent-rgb),.55),transparent 68%);animation:breathe 11s ease-in-out infinite;}
.glow.g2{width:46vw;height:46vw;right:-10vw;bottom:-14vw;background:radial-gradient(circle,rgba(139,92,246,.45),transparent 68%);animation:breathe 15s ease-in-out infinite reverse;}
.glow.g3{width:36vw;height:36vw;left:38%;top:26%;background:radial-gradient(circle,rgba(59,130,246,.30),transparent 70%);animation:breathe 19s ease-in-out infinite;}
@keyframes breathe{0%,100%{opacity:.32;transform:scale(1);}50%{opacity:.62;transform:scale(1.12);}}

.scan{
  position:absolute;inset:0;
  background:repeating-linear-gradient(to bottom,rgba(255,255,255,.022) 0 1px,transparent 1px 3px);
  opacity:.55;
}
.vignette{position:absolute;inset:0;background:radial-gradient(ellipse at 50% 45%,transparent 32%,rgba(0,0,0,.72) 100%);}

/* ================= 主容器 ================= */
#clock{
  position:relative;z-index:2;
  width:100%;max-width:820px;
  padding:56px 20px 64px;
  margin:0 auto;
}

/* 顶部标签 */
.brand{
  display:inline-flex;align-items:center;gap:8px;
  font-size:11px;letter-spacing:.32em;text-transform:uppercase;
  color:rgba(125,154,184,.9);
  margin-bottom:26px;padding:6px 14px;
  border:1px solid rgba(var(--accent-rgb),.18);
  border-radius:999px;
  background:rgba(8,20,34,.42);
  -webkit-backdrop-filter:blur(6px);
          backdrop-filter:blur(6px);
}
.brand i{
  width:6px;height:6px;border-radius:50%;background:var(--accent);
  box-shadow:0 0 10px var(--accent);
  animation:pulse 2.4s ease-in-out infinite;
}
@keyframes pulse{0%,100%{opacity:.35;transform:scale(.8);}50%{opacity:1;transform:scale(1.15);}}

/* 提示语 */
#clock .date{
  margin:0 0 14px;
  font-size:13px;line-height:1.8;
  letter-spacing:.14em;
  color:rgba(125,154,184,.95);
  min-height:18px;
  font-family:inherit;
}
#clock .date span{color:var(--accent);}

/* 时钟 */
#clock .time{
  margin:0;
  font-family:ui-monospace,"Cascadia Mono",Consolas,"Courier New",monospace;
  font-size:clamp(46px,8.2vw,78px);
  line-height:1;
  letter-spacing:.06em;
  color:#eafcff;
  font-variant-numeric:tabular-nums;
  text-shadow:
    0 0 18px rgba(var(--accent-rgb),.55),
    0 0 48px rgba(var(--accent-rgb),.22),
    0 0 90px rgba(59,130,246,.18);
}
#clock .bar{
  width:100%;height:1px;margin:16px 0 12px;
  background:linear-gradient(90deg,transparent,rgba(var(--accent-rgb),.65),rgba(139,92,246,.55),transparent);
}
#clock .text{
  margin:0;
  font-size:13px;letter-spacing:.22em;
  color:rgba(163,196,222,.92);
}

/* ================= 搜索框 ================= */
.search{padding:0;margin-top:30px;}
form{
  position:relative;
  width:min(480px,92vw);
  margin:0 auto;
  display:flex;
}
input,button{border:none;outline:none;font-family:inherit;}
#wd{
  flex:1;
  width:100%;height:48px;
  padding:0 16px;
  font-size:14px;
  color:var(--text);
  background:rgba(7,17,30,.62);
  border:1px solid var(--line);
  border-right:0;
  border-radius:10px 0 0 10px;
  -webkit-backdrop-filter:blur(10px);
          backdrop-filter:blur(10px);
  transition:border-color .25s,box-shadow .25s,background .25s;
}
#wd::placeholder{color:rgba(125,154,184,.75);}
#wd:focus{
  border-color:rgba(var(--accent-rgb),.75);
  background:rgba(9,22,38,.82);
  box-shadow:0 0 0 1px rgba(var(--accent-rgb),.25),0 0 28px rgba(var(--accent-rgb),.22);
}
form button{
  flex:0 0 auto;
  width:88px;height:48px;
  cursor:pointer;
  font-size:14px;letter-spacing:.12em;
  color:#02121a;font-weight:600;
  background:linear-gradient(135deg,var(--accent),#3b82f6);
  border-radius:0 10px 10px 0;
  box-shadow:0 0 24px rgba(var(--accent-rgb),.35);
  transition:filter .2s,box-shadow .2s;
}
form button:hover{filter:brightness(1.12);box-shadow:0 0 34px rgba(var(--accent-rgb),.55);}
form button:active{filter:brightness(.95);}

/* 搜索联想下拉 */
#word{
  position:absolute;
  top:50px;left:0;right:0;
  z-index:50;
  background:rgba(9,22,38,.97);
  color:var(--text);
  border:1px solid var(--line);
  border-radius:10px;
  max-height:170px;
  overflow-y:auto;overflow-x:hidden;
  text-align:left;
  box-shadow:0 12px 40px rgba(0,0,0,.6);
}
#word div,#word li{padding:8px 14px;cursor:pointer;font-size:13px;color:rgba(200,228,247,.9);}
#word div:hover,#word li:hover,#word .active{background:rgba(var(--accent-rgb),.14);}

/* ================= 热门搜索 ================= */
.resou{
  margin:22px auto 0;
  font-size:13px;
  color:rgba(125,154,184,.95);
  display:flex;flex-wrap:wrap;gap:8px 10px;
  justify-content:center;align-items:center;
  max-width:min(620px,94vw);
}
.resou a{
  color:rgba(198,228,247,.92);
  text-decoration:none;
  padding:4px 12px;
  font-size:12.5px;
  border:1px solid rgba(var(--accent-rgb),.20);
  border-radius:999px;
  background:rgba(10,24,40,.5);
  transition:color .22s,border-color .22s,box-shadow .22s,transform .22s;
}
.resou a:hover{
  color:#eafcff;
  border-color:rgba(var(--accent-rgb),.7);
  box-shadow:0 0 18px rgba(var(--accent-rgb),.28);
  transform:translateY(-1px);
}

/* 自定义内容 / 友情链接 / 运行时间 / 版权 */
#socode_diy{margin-top:16px;font-size:12.5px;line-height:2;color:rgba(125,154,184,.88);}
#socode_diy a{color:rgba(var(--accent-rgb),.92);text-decoration:none;}

#footer_link{margin-top:18px;font-size:12.5px;line-height:2;color:rgba(125,154,184,.88);}
#footer_link a{color:rgba(var(--accent-rgb),.92);text-decoration:none;margin:0 4px;}
#footer_link a:hover{text-decoration:underline;}

#sitetime{
  display:block;
  margin-top:20px;
  font-family:ui-monospace,"Cascadia Mono",Consolas,monospace;
  font-size:12px;letter-spacing:.08em;
  color:rgba(125,154,184,.8);
}

#footer{
  margin-top:10px;
  font-size:12px;
  color:rgba(103,130,155,.85);
  line-height:1.9;
}
#footer a{color:rgba(163,196,222,.9);text-decoration:none;margin-left:8px;}
#footer a:hover{color:var(--accent);}

/* ================= 搜索结果 ================= */
#main{position:relative;z-index:2;}
#main h3{
  font-size:14px;font-weight:400;
  letter-spacing:.06em;
  color:rgba(163,196,222,.92);
  margin:0 0 6px;
}
#main h3.err{color:#f87171;}
.list_btn{
  display:inline-block;
  text-decoration:none;
  margin:5px 4px;
  padding:8px 14px;
  min-width:31%;max-width:95%;
  color:rgba(198,228,247,.92);
  font-size:13px;
  border-radius:8px;
  border:1px solid rgba(var(--accent-rgb),.20);
  background:rgba(10,24,40,.5);
  text-overflow:ellipsis;
  overflow:hidden;
  white-space:nowrap !important;
  outline:0 !important;
  transition:color .22s,border-color .22s,box-shadow .22s,transform .22s;
}
.list_btn strong{font-weight:400;}
.list_btn:hover{
  color:#eafcff;
  border-color:rgba(var(--accent-rgb),.7);
  box-shadow:0 0 18px rgba(var(--accent-rgb),.28);
  transform:translateY(-1px);
}
.clearfloat{clear:both;}

/* ================= 响应式 ================= */
@media screen and (max-width:650px){
  #clock{padding:48px 16px 56px;}
  #clock .time{font-size:clamp(40px,12vw,56px);}
  form{width:100%;}
  .list_btn{min-width:95%;margin:5px 0;}
  .grid{opacity:.6;}
  .glow{filter:blur(60px);}
}
@media (prefers-reduced-motion:reduce){
  .grid,.glow,.brand i{animation:none !important;}
}
</style>
<script>
/* 非法字符检测（原版此处引用了未定义变量 value，校验实际从未生效，已修正） */
function check_str(val){
  var arr = ['{','}','=',',',';','"','<','>','script','iframe','@','&','%','$','#','*',':','.','if','/','\\'];
  if("<?php echo $__not_off; ?>" == '1'){
    var str = "<?php echo str_replace(array('"', "\r", "\n"), array('', '', ''), $__not_val); ?>";
    if(str != ''){ arr = arr.concat(str.split("|")); }
  }
  var low = (val || '').toLowerCase();
  for(var i = 0; i < arr.length; i++){
    if(arr[i] && low.indexOf(String(arr[i]).toLowerCase()) > -1){ return true; }
  }
  return false;
}

/* 提交前判断：链接走解析，片名走搜索 */
function validate_form(form){
  var input = document.getElementById('wd');
  var val = (input.value || '').replace(/^\s+|\s+$/g, '');
  if(val === ''){ alert('请输入视频名称或视频链接'); return false; }

  /* http(s) 链接：直接交给解析入口 /?v= */
  if(/^(https?:)?\/\//i.test(val)){
    input.name = 'v';
    form.action = './';
    return true;
  }

  if(check_str(val)){ alert('请勿输入非法字符！'); return false; }

  input.name = 'wd';
  form.action = './so.php';
  return true;
}
</script>
</head>
<body>

<!-- 背景层：粒子星座 + 透视网格 + 呼吸光晕 + 扫描线 -->
<div class="bg" aria-hidden="true">
  <canvas id="stars"></canvas>
  <div class="grid"></div>
  <div class="glow g1"></div>
  <div class="glow g2"></div>
  <div class="glow g3"></div>
  <div class="scan"></div>
  <div class="vignette"></div>
</div>

<div id="clock">

  <div id="main"></div>
  <div class="clearfloat"></div>

  <div id="body">

    <div class="brand"><i></i><?php echo $__title; ?></div>

    <div class="date"></div>
    <div class="time" id="time">00:00:00</div>
    <div class="bar"></div>
    <div class="text" id="text"><?php echo date('Y-m-d'); ?> 星期日</div>

    <div class="search bar6">
      <form target="_self" action="./" onsubmit="return validate_form(this);" method="get">
        <input id="wd" type="text" name="v" autocomplete="off" placeholder="请输入视频名称或视频链接" value="<?php echo $__wd_safe; ?>">
        <button type="submit">搜索</button>
        <div id="word" style="display:none;"></div>
      </form>
    </div>

    <?php if($__top_off): ?>
    <div class="resou" id="socode_top">
      <b style="font-weight:400;color:rgba(163,196,222,.9);">热门搜索：</b>
      <?php
        $i = 0;
        foreach($__hot as $key => $val){
            $__tip = $val > 0 ? ' title="人气：' . (int)$val . '℃"' : '';
            echo '<a href="./so.php?wd=' . rawurlencode($key) . '"' . $__tip . '>' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . '</a>';
            $i++;
            if($i >= 5){ break; }
        }
      ?>
      <a href="javascript:void(0);" onclick="echotop();">更多...</a>
    </div>
    <?php endif; ?>

    <?php if($__diy_off && $__diy_val != ''): ?>
    <div id="socode_diy"><?php echo $__diy_val; ?></div>
    <?php endif; ?>

  </div>

  <?php if($__footer_off && !empty($__footer_lnk)): ?>
  <div id="footer_link">
    友情链接：
    <?php foreach($__footer_lnk as $key => $val): ?>
    <a target="_blank" rel="noopener" href="<?php echo htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?></a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <span id="sitetime"></span>
  <div id="footer"></div>

</div>

<script>
/* ---------- 时钟与运行时间 ---------- */
var week = ['星期天','星期一','星期二','星期三','星期四','星期五','星期六'];
var sitetime = "<?php echo $__sitetime; ?>";
var timerID = setInterval(updateTime, 1000);
updateTime();

function updateTime(){
  var cd = new Date();
  document.getElementById('time').innerHTML =
    zeroPadding(cd.getHours(),2) + ':' + zeroPadding(cd.getMinutes(),2) + ':' + zeroPadding(cd.getSeconds(),2);
  document.getElementById('text').innerHTML =
    zeroPadding(cd.getFullYear(),4) + '-' + zeroPadding(cd.getMonth()+1,2) + '-' + zeroPadding(cd.getDate(),2) + ' ' + week[cd.getDay()];
  SiteTime(sitetime);
}

function zeroPadding(num, digit){
  var zero = '';
  for(var i = 0; i < digit; i++){ zero += '0'; }
  return (zero + num).slice(-digit);
}

/* 网站运行时间 */
function SiteTime(time){
  var el = document.getElementById('sitetime');
  if(!el || !time){ return; }
  var seconds = 1000, minutes = seconds*60, hours = minutes*60, days = hours*24, years = days*365;
  var begin = new Date(String(time).replace(/-/g, "/"));
  if(isNaN(begin.getTime())){ el.innerHTML = ''; return; }
  var diff = new Date().getTime() - begin.getTime();
  if(diff < 0){ diff = 0; }
  var y = Math.floor(diff / years);
  var d = Math.floor(diff / days - y*365);
  var h = Math.floor((diff - (y*365 + d)*days) / hours);
  var m = Math.floor((diff - (y*365 + d)*days - h*hours) / minutes);
  var s = Math.floor((diff - (y*365 + d)*days - h*hours - m*minutes) / seconds);
  el.innerHTML = '已运行 ' + y + ' 年 ' + d + ' 天 ' + h + ' 小时 ' + m + ' 分钟 ' + s + ' 秒';
}

/* ---------- 入口 ---------- */
function init(){
  var wd  = _GET("wd");
  var url = _GET("url");

  if(wd === "" && url === ""){
    document.querySelector('.date').innerHTML = '<span>···</span> 视频地址不能为空 <span>···</span>';
  }
  if(wd !== ""){ getvideo(wd); }

  var w = "<?php echo str_replace(array('"', "\r", "\n"), array('', '', ''), $__by); ?>";
  w += '  <a style="color:#daf6ff;" href="javascript:void(0);" onclick="echoby();">免责声明</a>';
  document.getElementById('footer').innerHTML = w;

  toggleCenter();
}

/* 免责声明 */
function echoby(){
  alert("本站所有视频均来自外部引用，本站不存储不制作任何视频！\r\n如有侵权问题，请与源站联系,谢谢合作！");
}

/* 搜索排行榜 */
function echotop(){
  var main = document.getElementById('main');
  if(main.innerHTML.indexOf("搜索排行榜") != -1){
    main.innerHTML = "";
  }else{
    var w = "<h3>搜索排行榜 · TOP100</h3><div>";
    <?php
      $i = 0;
      foreach($__hot as $key => $val){
          echo 'w += \'<a target="_self" class="list_btn" href="./so.php?wd=' . rawurlencode($key) . '" title="人气：' . (int)$val . '℃"><strong>' . str_replace(array("'", "\\"), array("\\'", "\\\\"), $key) . '</strong></a>\';' . "\n";
          $i++;
          if($i >= 100){ break; }
      }
    ?>
    w += "</div>";
    main.innerHTML = w;
  }
  toggleCenter();
}

/* 刷新结果列表 */
function relist(data){
  var main = document.getElementById('main');
  var w = '';

  if(data && data.success){
    var v = data.info || [];
    w = "<h3>搜索到相关视频 " + v.length + " 个，请点击访问</h3><div>";
    for(var i = 0; i < v.length; i++){
      var href  = "./?index" + v[i].id + "-" + v[i].flag + "-1.htm";
      var title = removeHTMLTag(v[i].title, true) + "(" + v[i].from + ")";
      w += '<a class="list_btn" target="_self" href="' + href + '" title="' + title + '"><strong>' + title + '</strong></a>';
    }
    w += "</div>";
  }else{
    w = '<h3 class="err">很抱歉，未搜索到相关资源</h3>';
  }

  main.innerHTML = w;
  $('#body').show();
  toggleCenter();
}

/* 取视频数据 */
function getvideo(word){
  $.ajax({
    url: './<?php echo $__api_path; ?>?out=jsonp&wd=' + encodeURIComponent(word),
    timeout: 30000,
    dataType: 'jsonp',
    jsonp: 'cb',
    beforeSend: function(){
      $('#body').hide();
      document.getElementById('main').innerHTML = '<h3>正在搜索中，请稍后...</h3>';
    },
    success: function(data){ relist(data); },
    error: function(){ relist(); }
  });
}

/* 布局已改为 CSS 自适应居中，此函数保留为空实现以兼容旧调用 */
function toggleCenter(){}
$(window).on('resize', function(){ toggleCenter(); });

init();
</script>

<script>
/* ---------- 背景粒子星座 ---------- */
(function(){
  var cvs = document.getElementById('stars');
  if(!cvs || !cvs.getContext){ return; }

  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var ctx = cvs.getContext('2d');
  var dpr = Math.min(window.devicePixelRatio || 1, 2);
  var W = 0, H = 0, pts = [], raf = null;
  var mouse = { x: -9999, y: -9999 };

  function resize(){
    W = cvs.clientWidth; H = cvs.clientHeight;
    cvs.width = W * dpr; cvs.height = H * dpr;
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

    var target = Math.round(Math.min(96, Math.max(38, W * H / 16000)));
    pts = [];
    for(var i = 0; i < target; i++){
      pts.push({
        x: Math.random() * W,
        y: Math.random() * H,
        vx: (Math.random() - 0.5) * 0.22,
        vy: (Math.random() - 0.5) * 0.22,
        r: Math.random() * 1.5 + 0.6
      });
    }
  }

  function draw(){
    ctx.clearRect(0, 0, W, H);
    var i, j, p, q, dx, dy, dist, lim = 132;

    for(i = 0; i < pts.length; i++){
      p = pts[i];
      p.x += p.vx; p.y += p.vy;
      if(p.x < -20){ p.x = W + 20; }
      if(p.x > W + 20){ p.x = -20; }
      if(p.y < -20){ p.y = H + 20; }
      if(p.y > H + 20){ p.y = -20; }

      dx = p.x - mouse.x; dy = p.y - mouse.y;
      dist = Math.sqrt(dx * dx + dy * dy);
      if(dist < 110 && dist > 0.1){
        p.x += (dx / dist) * 0.7;
        p.y += (dy / dist) * 0.7;
      }

      ctx.beginPath();
      ctx.arc(p.x, p.y, p.r, 0, 6.2832);
      ctx.fillStyle = 'rgba(150,232,255,' + (0.35 + p.r * 0.22) + ')';
      ctx.fill();
    }

    for(i = 0; i < pts.length; i++){
      p = pts[i];
      for(j = i + 1; j < pts.length; j++){
        q = pts[j];
        dx = p.x - q.x; dy = p.y - q.y;
        dist = Math.sqrt(dx * dx + dy * dy);
        if(dist < lim){
          ctx.beginPath();
          ctx.moveTo(p.x, p.y);
          ctx.lineTo(q.x, q.y);
          ctx.strokeStyle = 'rgba(34,211,238,' + (0.16 * (1 - dist / lim)) + ')';
          ctx.lineWidth = 0.7;
          ctx.stroke();
        }
      }
    }
    raf = requestAnimationFrame(draw);
  }

  function staticFrame(){
    ctx.clearRect(0, 0, W, H);
    for(var k = 0; k < pts.length; k++){
      ctx.beginPath();
      ctx.arc(pts[k].x, pts[k].y, pts[k].r, 0, 6.2832);
      ctx.fillStyle = 'rgba(150,232,255,.55)';
      ctx.fill();
    }
  }

  resize();
  if(reduce){ staticFrame(); } else { draw(); }

  var rt = null;
  window.addEventListener('resize', function(){
    clearTimeout(rt);
    rt = setTimeout(function(){
      cancelAnimationFrame(raf);
      resize();
      if(reduce){ staticFrame(); } else { draw(); }
    }, 200);
  });
  window.addEventListener('mousemove', function(e){ mouse.x = e.clientX; mouse.y = e.clientY; });
  window.addEventListener('mouseleave', function(){ mouse.x = -9999; mouse.y = -9999; });

  /* 页面不可见时暂停，省电 */
  document.addEventListener('visibilitychange', function(){
    if(document.hidden){
      cancelAnimationFrame(raf);
    }else if(!reduce){
      cancelAnimationFrame(raf);
      draw();
    }
  });
})();
</script>
</body>
</html>
