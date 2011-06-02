<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo (isset($action_name))? (html::specialchars($action_name)."：".$site_name) : $site_name; ?></title>
<meta name="keywords" content="東日本大震災,被災地,震災,地震,復興,支援,津波,災害,救援,物資,情報,shinsai,インフォ,311,ボランティア" />
<meta name="description" content="sinsai.infoでは、2011年3月11日に発生した東日本大震災の被災地周辺情報を収集し公開しています。インターネットを活用して、みなさまからの投稿だけでなく、twitterのつぶやきなどをピックアップして、幅広いカテゴリの情報を提供しています。ボランティアスタッフ一同、被災地の皆様の1日でも早い復興をお祈りしております。" />
<?php echo $header_block; ?>
<?php
// Action::header_scripts - Additional Inline Scripts from Plugins
Event::run('ushahidi_action.header_scripts');
echo map::layers_scripts(TRUE);
?>
<link rel="shortcut icon" href="<?php echo url::base(); ?>media/img/favicon.ico" type="image/x-icon" />
<link rel="search" type="application/opensearchdescription+xml" title="Sinsai.info" href="<?php echo url::base(); ?>media/sinsaiinfo.searchbar.xml">
</head>

<body id="page">
<!-- wrapper -->
<div class="rapidxwpr floatholder">

<!-- header -->
<div id="header">

<!-- logo -->
<div id="logo">

<h1><a href="/"><img src="<?php echo url::base(); ?>media/img/logo.gif" alt="東北沖地震 震災情報サイト sinsai.info: 3/11 東北地方太平洋沖地震,Earthquake Tohoku area in Japan 3/11" /></a></h1>
<span class="dnone"><?php echo $site_tagline; ?></span>

</div>
<!-- / logo -->

<!-- searchbox -->
<div id="searchbox">
<div id="nations">
<p><span class="uppercase">Select Language</span>
<?php
$nations = array("ja_JP","en_US","ko_KR","zh_CN","de_DE","fr_FR","it_IT");
foreach ($nations as $nation){
    echo "<a href='?l=".$nation."'><img src='".url::base()."media/img/flags/".$nation.".png' ></a>";
}
?>
</p></div>
<!-- searchform -->
<?php echo $search; ?>
<!-- / searchform -->

<!-- submit incident -->
<?php echo $submit_btn; ?>
<!-- / submit incident -->

</div>
<!-- / searchbox -->
</div>
<!-- / header -->

<!-- main body -->
<div id="middle">
<div class="background layoutleft">

<!-- mainmenu -->
<div id="mainmenu">
<ul>
<?php
$menu = "";
$lang = "";
if (isset($_GET['l']) && !empty($_GET['l']))
{
	if($_GET['l'] != 'ja_JP')
	{
	$lang = "?l=".$_GET['l'];
	}
}

// Home
$menu .= "<li><a href=\"/\" ";
$menu .= ($this_page == 'home') ? " class=\"active\"" : "";
$menu .= ">".Kohana::lang('ui_main.home')."</a></li>";

// Reports List
$menu .= "<li><a href=\"".url::site()."reports".$lang."\" ";
$menu .= ($this_page == 'reports') ? " class=\"active\"" : "";
$menu .= ">".Kohana::lang('ui_main.reports')."</a></li>";

// Alerts
$menu .= "<li><a href=\"".url::site()."alerts".$lang."\" ";
$menu .= ($this_page == 'alerts') ? " class=\"active\"" : "";
$menu .= ">".Kohana::lang('ui_main.alerts')."</a></li>";

// StrickenAreaVolunteer
$menu .= "<li><a href=\"".url::site()."reports/?c=13".$lang."\" ";
$menu .= ">".Kohana::lang('ui_main.strickenareavolunteer')."</a></li>";

// Custom Pages
$pages = ORM::factory('page')->where('page_active', '1')->find_all();
foreach ($pages as $page)
{
	$menu .= "<li><a href=\"".url::site()."page/index/".$page->id.$lang."\" ";
	$menu .= ($this_page == 'page_'.$page->id) ? " class=\"active\"" : "";
	$menu .= ">".Kohana::lang('ui_main.'.$page->page_tab)."</a></li>";
}

// App and API
$menu .= "<li><a href=https://docs.google.com/document/d/12odG3IxDYHY7KeSa_wSUFkbctFPCtbN_JxZ_JUg-O2I/edit?hl=ja".$lang."\" ";
$menu .= ">".Kohana::lang('ui_main.appandapi')."</a></li>";

echo $menu;
?>
</ul>

</div>
<!-- / mainmenu -->
