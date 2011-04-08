<?php
$latlong_params = "";
if ($latlong) {
  $latlong_params = "&latlong=".$latlong;
?>
▼現在地<br>
<?php if(isset($area_name))echo $area_name;?>
<div style="clear:both;text-align:center;" align="center"><center><a href="http://www.google.co.jp/m/local?q=<?php echo $latlong;?>&z=14"><img src="http://maps.google.com/maps/api/staticmap?center=<?php echo $latlong;?>&zoom=14&size=230x100&format=gif&maptype=roadmap&sensor=false&markers=<?php echo $latlong;?>" border="0"></a></center></div>
<hr size="1" noshade>
<?php
}
if ($category AND $category->loaded)
{
	$category_id = $category->id;
	$color_css = 'class="swatch" style="background-color:#'.$category->category_color.'"';
	$category_title = $category->category_title;
	if (preg_match("/^([^\/]+)\/([^\/]+)$/",$category_title,$matches)) {
		$category_title = $matches[1];
	}
	//echo '[1]<a href="#" accesskey="1">'.$category_title.'</a><br>';
}
else
{
	$category_id = "";
}
?>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" id="report-search" method="GET">
<input type="hidden" name="c" value="<?php echo $c; ?>">
<input type="hidden" name="l" value="<?php echo $l; ?>">
<input type="hidden" name="sw" value="<?php echo $sw; ?>">
<input type="hidden" name="ne" value="<?php echo $ne; ?>">
<input type="text" name="keyword" value="<?php if(isset($_GET["keyword"])){echo $_GET["keyword"];}?>" />
<input type="submit" name="submit" value="検索" />
</form>
<?php
if ($incidents->count())
{
	$page_no = (isset($_GET['page'])) ? $_GET['page'] : "";
	foreach ($incidents as $incident)
	{
		$incident_date = $incident->incident_date;
		$incident_date = date('Y/m/d', strtotime($incident->incident_date));
		$location_name = $incident->location_name;
		echo $incident->incident_title;
		echo "&nbsp;";
		echo "(<span class=\"location_name\">".$location_name."</span>) <br>";
		echo "<div style=\"color:#808080;\">$incident_date [<a href=\"".url::site()."keitai/reports/view/".$incident->id."?c=".$category_id."&p=".$page_no.''.$latlong_params."\">詳細</a>]</div>";
		echo "<hr size=\"1\" noshade>";
	}
}
else
{
	echo "ﾚﾎﾟｰﾄはありません";
}
?>
<?php echo $pagination; ?>
