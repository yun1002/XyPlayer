<?php
$YUN_MATCH=array (
  'ERROR_404' => '404|页面不存在|Not Found|资源不存在',
  'type_match' => 
  array (
    '!m3u8!i' => 'hls',
    '!\\/share\\/!i' => 'url',
    '!\\.mp4(\\?|$)!i' => 'mp4',
    '!\\.flv(\\?|$)!i' => 'flv',
    '!\\.ts(\\?|$)!i' => 'hls',
  ),
  'url_replace' => 
  array (
    0 => '&tdsourcetag=s_pcqq_aiomsg',
  ),
  'title_replace' => 
  array (
    0 => '独播',
    1 => '全集',
    2 => '高清在线观看',
    3 => '高清',
    4 => '超清',
    5 => '1080P',
    6 => '720P',
    7 => '4K',
    8 => '蓝光原盘',
    9 => '蓝光',
    10 => 'BD',
    11 => 'DVD',
    12 => '预告片',
    13 => '精彩花絮',
    14 => '抢先版',
    15 => '抢先看',
    16 => '抢先',
    17 => '预告',
    18 => '花絮',
    19 => '中文字幕',
    20 => '国语中字',
    21 => '中字',
    22 => '字幕',
    23 => '官方高清',
    24 => '官方',
    25 => '英语版',
    26 => '英文版',
    27 => '国语版',
    28 => '原声版',
    29 => '普通话版',
    30 => '《',
    31 => '》',
    32 => '【',
    33 => '】',
    34 => '（',
    35 => '）',
    36 => '(',
    37 => ')',
    38 => ':',
    39 => 'VIP',
    40 => '|',
    41 => 'iQiyi',
    42 => 'Episode',
  ),
  'title_match' => 
  array (
    '!iqiyi.com!i' => 
    array (
      0 => '{og:title.*?content="(.*?)"}i',
    ),
    '!youku.com!i' => 
    array (
      0 => '{og:title.*?content="(.*?)"}i',
    ),
    '!qq.com!i' => 
    array (
      0 => '{VIDEO_INFO.*?"title"="(.*?)"}i',
    ),
    '!mgtv.com!i' => 
    array (
      0 => '{.*?"videoName":"(.*?)"}i',
    ),
    '!le.com!i' => 
    array (
      0 => '{irTitle.*?content="(.*?)"}i',
    ),
    '!sohu.com!i' => 
    array (
      0 => '{og:title.*?content="(.*?)"}i',
    ),
    '!pptv.com!i' => 
    array (
      0 => '{og:title.*?content="(.*?)"}i',
    ),
    '!tudou.com!i' => 
    array (
      0 => '{.*?td-playbase__info[\\s\\S]*?(<h1[\\s\\S]*?</h1>)}i',
      1 => '{.*?play-video-desc[\\s\\S]*?(<div[\\s\\S]*?</div>)}i',
    ),
    '!bilibili.com!i' => 
    array (
      0 => '{.*?media-wrapper[\\s\\S]*?(<h1[\\s\\S]*?</h1>)}i',
      1 => '{.*?ep-info-center[\\s\\S]*?(<div[\\s\\S]*?</div>)}i',
      2 => '{application/ld+json[\\s\\S]*?"name":"(.*?)"}i',
    ),
    '!huya.com!i' => 
    array (
      0 => '{.*?video-detail[\\s\\S]*?(<h2[\\s\\S]*?</h2>)}i',
      1 => '{.*?video-title-new-t[\\s\\S]*?(<h1[\\s\\S]*?</h1>)}i',
    ),
    '!douyu.com|miguvideo.com!i' => 
    array (
      0 => '{.*?video-title[\\s\\S]*?(<h1[\\s\\S]*?</h1>)}i',
      1 => '{.*?video-describe[\\s\\S]*?(<div[\\s\\S]*?</div>)}i',
    ),
    '!^.*$!i' => 
    array (
      0 => '{<title.*?>(.*?)(?:-|—|_|——|-).*?</title>}i',
      1 => '{application/ld+json[\\s\\S]*?"name":"(.*?)"}i',
    ),
  ),
  'name_match' => 
  array (
    '!qq.com!i' => 
    array (
      0 => '{(.*?)_(.*?)}i',
      1 => '{^(?:《|)(.*?)(?:》|)(?:第|)(\\d{1,3})(?=话|集|期).*?}i',
      2 => '{(.*?)($)}i',
    ),
    '!mgtv.com!i' => 
    array (
      0 => '{^(.*)(?:：|:|\\s|$)[\\s\\S]*?(?:第|)(\\d{1,3})(?:话|集|期)}i',
    ),
    '!iqiyi.com!i' => 
    array (
      0 => '{^(\\s*?).*?第(\\d{1,3})集}i',
      1 => '{^(.*?)(?:第|)(\\d{1,3})(?:话|集)}i',
      2 => '{^(.*?)(?:-|—|_|——|-|$)}i',
    ),
    '!le.com|tudou.com!i' => 
    array (
      0 => '{^(.*?)(?:第|)(\\d{1,3})(?:话|集|期)}i',
    ),
    '!pptv.com!i' => 
    array (
      0 => '{^(.*?)第(\\d{1,3})(?=话|集|期)}i',
      1 => '{^(.*?)(?:-|—|_|——|-|$)}i',
    ),
    '!bilibili.com!i' => 
    array (
      0 => '{^(.*?)：(?:第|)(\\d{1,3})(?=话|集)}i',
    ),
    '!miguvideo.com!i' => 
    array (
      0 => '{^(?:《|)(.*?)(?:》|)(?:第|)(\\d{1,3})(?=话|集)}i',
    ),
    '!^.*$!i' => 
    array (
      0 => '{^(.*)(?:：|:|\\s)[\\s\\S]*?(?:第|)(\\d{1,3})(?:话|集|期)}i',
      1 => '{《(.*?)》.*?}',
      2 => '{^(.*?)\\s*(?:：|:|\\s)}i',
      3 => '{^(.*?)(?:第|)(\\d{1,3})(?:集|期|话)}i',
      4 => '{^(.*?)$}i',
    ),
  ),
  'url_match' => 
  array (
    '!m.mgtv.com/b/(\\d+)/(\\d+).html!i' => 'https://www.mgtv.com/b/(?1)/(?2).html',
    '!m.v.qq.com.*?cid=(.*?)&vid=(.*?)(?:&|$)!i' => 'https://v.qq.com/x/cover/(?1)/(?2).html',
    '!m.v.qq.com.*?cid=(.*?)(?:&|$)!i' => 'https://v.qq.com/x/cover/(?1).html',
    '!m.v.qq.com/cover/r/(.*?)cid=(.*?)(?:&|$)!i' => 'https://v.qq.com/x/cover/(?1).html',
    '!m.v.qq.com/cover/./(.*?)\\.html\\?vid=(.*?)(?:&|$)!i' => 'https://v.qq.com/x/cover/(?1)/(?2).html',
    '!m.v.qq.com/(.*)$!i' => 'https://v.qq.com/(?1)',
    '!m.fun.tv/mplay/\\?mid=(\\d+)&vid=(\\d+)(?:&|$)!i' => 'https://www.fun.tv/vplay/g-(?1).v-(?2)',
    '!http://(.*?mgtv.com/.*?html)!i' => 'https://(?1)',
    '!m.youku.com/video/id_(.*?)==.html!i' => 'https://v.youku.com/v_show/id_(?1)==.html',
    '!m.bilibili.com/(.*)$!i' => 'https://www.bilibili.com/(?1)',
    '!m.le.com/(.*)$!i' => 'https://www.le.com/(?1)',
    '!m.pptv.com/(.*)$!i' => 'https://www.pptv.com/(?1)',
    '!m.sohu.com/(.*)$!i' => 'https://www.sohu.com/(?1)',
    '!mgtv.com.*?/(\\d+).html!i' => 'https://pcweb.api.mgtv.com/video/info?vid=(?1)',
  ),
);