<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="keywords" content="東日本大震災,被災地,震災,地震,復興,支援,津波,災害,救援,物資,情報,shinsai,インフォ,311,ボランティア" />
<meta name="description" content="sinsai.infoでは、2011年3月11日に発生した東日本大震災の被災地周辺情報を収集し公開しています。インターネットを活用して、みなさまからの投稿だけでなく、twitterのつぶやきなどをピックアップして、幅広いカテゴリの情報を提供しています。ボランティアスタッフ一同、被災地の皆様の1日でも早い復興をお祈りしております。" />
<meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
<title><?php echo $site_name; ?></title>
<?php
echo plugin::render('stylesheet');

if ($show_map === TRUE)
{
	echo "\n<script type=\"text/javascript\" src=\"http://maps.google.com/maps/api/js?sensor=true\"></script>\n";
}
echo plugin::render('javascript');
?>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
$(function() {
	$("h2.expand").toggler({speed: "fast"});
});
//--><!]]>
</script>
<script type="text/javascript">
<?php echo $js; ?>
</script>
</head>

<body>
	<div id="container">
		<div id="header">
			<h1><img src="<?php echo url::site(); ?>plugins/mobile/views/images/logo.gif" alt="<?php echo $site_name.' - '.$site_tagline; ?>" /></h1>
			<span class="dnone"><?php echo $site_tagline; ?></span>
		</div>
		<div id="navigation">
			&raquo;&nbsp;<a href="<?php echo url::site()."mobile"; ?>">Home</a><?php echo $breadcrumbs; ?>
		</div>
		<div id="location_bar">
      <a href="javascript:void(0)" id="get_location">Detect Location</a> <a href="javascript:void(0)" id="del_location">[X]</a> <span id="loc_address"></span>
		</div>
		<div id="page">
