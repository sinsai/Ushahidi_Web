<html> 
<head> 
<title><?php echo $site_name; ?></title> 
<meta name="keywords" content="東日本大震災,被災地,震災,地震,復興,支援,津波,災害,救援,物資,情報,shinsai,インフォ,311,ボランティア" />
<meta name="description" content="sinsai.infoでは、2011年3月11日に発生した東日本大震災の被災地周辺情報を収集し公開しています。インターネットを活用して、みなさまからの投稿だけでなく、twitterのつぶやきなどをピックアップして、幅広いカテゴリの情報を提供しています。ボランティアスタッフ一同、被災地の皆様の1日でも早い復興をお祈りしております。" />
<style type="text/css"> 
<![CDATA[
  a:link{color:#808080;}
  a:focus{color:#a0a0a0;}
  a:visited{color:#808080;}
]]>
</style> 
</head> 
<body bgcolor="#ffffff" text="#000000">
<?php
  function googleAnalyticsGetImageUrl() {
    $GA_ACCOUNT = "MO-22075443-2";
    $GA_PIXEL = "/ga.php";
    $url = "";
    $url .= $GA_PIXEL . "?";
    $url .= "utmac=" . $GA_ACCOUNT;
    $url .= "&utmn=" . rand(0, 0x7fffffff);
    $query = $_SERVER["QUERY_STRING"];
    $path = $_SERVER["REQUEST_URI"];
    if (empty($_SERVER["HTTP_REFERER"])) {
      $referer = "-";
    }else{
      $referer = $_SERVER["HTTP_REFERER"];
    }
    $url .= "&utmr=" . urlencode($referer);
    if (!empty($path)) {
      $url .= "&utmp=" . urlencode($path);
    }
    $url .= "&guid=ON";
    return str_replace("&", "&amp;", $url);
  }
?>
<a name="top" id="top"></a>
<div><img src="<?php echo url::base(); ?>plugins/keitai/views/img/logo.gif" alt="<?php echo $site_name . ': ' . $site_tagline; ?>" /></div>
<div style="clear:both;text-align:right;" align="right"><right><form method="get" id="search" action="/ushahidi/index.php/keitai/search/"><input type="hidden"  name="l" value="ja_JP" /><input type="text" name="k" value="" class="text" size="14" /><input type="submit" name="b" class="searchbtn" value="検索" /></form></right></div>
[0]<a href="<?php echo url::site(); ?>keitai<?php if ($latlong) { echo "?latlong=".$latlong; } ?>" accesskey="0">TOP</a><?php echo $breadcrumbs; ?><br>
<hr size="1" noshade>
