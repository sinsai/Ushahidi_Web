<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
<title><?php echo $site_name; ?></title>
<?php
echo plugin::render('stylesheet');
echo plugin::render('javascript');

if ($show_map === TRUE)
{
	echo "\n<script type=\"text/javascript\" src=\"http://maps.google.com/maps/api/js?sensor=true\"></script>\n";
}
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
