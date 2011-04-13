<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title><?php echo (isset($action_name))? ($action_name."：".$site_name) : $site_name; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php echo $header_block; ?>
<?php
// Action::header_scripts - Additional Inline Scripts from Plugins
Event::run('ushahidi_action.header_scripts');
echo map::layers_scripts(TRUE);
?>
<link rel="shortcut icon" href="/ushahidi/media/img/favicon.ico" type="image/x-icon" />
<link rel="search" type="application/opensearchdescription+xml" title="Sinsai.info Search Bar" href="/ushahidi/misc/sinsaiinfo.searchbar.xml">
</head>

<body id="page">
<!-- wrapper -->
<div class="rapidxwpr floatholder">

<!-- header -->
<div id="header">

<!-- searchbox -->
<div id="searchbox">
<!-- languages -->
<!-- <?php echo $languages;?> -->
<!-- / languages -->
<!-- searchform -->
<?php echo $search; ?>
<!-- / searchform -->
<br sytle="clear:both;"/>
<div id="nations">
SELECT LANGUAGE<br>
<?php
$nations = array("ja_JP","en_US","ko_KR","zh_CN","de_DE","fr_FR","it_IT");
foreach ($nations as $nation){
    echo "<a href='?l=".$nation."'><img src='".url::base()."/media/img/flags/".$nation.".png' ></a>";
}
?>
</div>

</div>
<!-- / searchbox -->

<!-- logo -->
<div id="logo">

<h1><a href="/"><img src="/ushahidi//media/img/logo.gif" alt="東北沖地震 震災情報サイト sinsai.info: 3/11 東北地方太平洋沖地震,Earthquake Tohoku area in Japan 3/11" /></a></h1>
<span class="dnone"><?php echo $site_tagline; ?></span>

</div>
<!-- / logo -->

<!-- submit incident -->
<?php echo $submit_btn; ?>
<!-- / submit incident -->

</div>
<!-- / header -->

<!-- main body -->
<div id="middle">
<div class="background layoutleft">

<!-- mainmenu -->
<div id="mainmenu">
<ul>
<!-- <?php nav::main_tabs($this_page); ?> -->
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
$menu .= "<li><a href=\"".url::site()."main".$lang."\" ";
$menu .= ($this_page == 'home') ? " class=\"active\"" : "";
$menu .= ">".Kohana::lang('ui_main.home')."</a></li>";

// Reports List
$menu .= "<li><a href=\"".url::site()."reports".$lang."\" ";
$menu .= ($this_page == 'reports') ? " class=\"active\"" : "";
$menu .= ">".Kohana::lang('ui_main.reports')."</a></li>";

// Reports Submit
//if (Kohana::config('settings.allow_reports'))
//{
//$menu .= "<li><a href=\"".url::site()."reports/submit\" ";
//$menu .= ($this_page == 'reports_submit') ? " class=\"active\"":"";
//$menu .= ">".Kohana::lang('ui_main.submit')."</a></li>";
//}

// Alerts
$menu .= "<li><a href=\"".url::site()."alerts".$lang."\" ";
$menu .= ($this_page == 'alerts') ? " class=\"active\"" : "";
$menu .= ">".Kohana::lang('ui_main.alerts')."</a></li>";

// Contacts
if (Kohana::config('settings.site_contact_page'))
{
$menu .= "<li><a href=\"".url::site()."contact".$lang."\" ";
$menu .= ($this_page == 'contact') ? " class=\"active\"" : "";
$menu .= ">".Kohana::lang('ui_main.contact')."</a></li>";
}

// Custom Pages
$pages = ORM::factory('page')->where('page_active', '1')->find_all();
foreach ($pages as $page)
{
$menu .= "<li><a href=\"".url::site()."page/index/".$page->id.$lang."\" ";
$menu .= ($this_page == 'page_'.$page->id) ? " class=\"active\"" : "";
$menu .= ">".Kohana::lang('ui_main.'.$page->page_tab)."</a></li>";
}

echo $menu;
?>
</ul>

</div>
<!-- / mainmenu -->
