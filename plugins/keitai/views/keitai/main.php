<form action="<?php echo url::site()."keitai/reports/submit/" ?>">
<input type="submit" value="ﾚﾎﾟｰﾄの投稿"/>
</form>
<br>
<?php if ($device == 'd') { ?>現在地の情報(<a href="http://w1m.docomo.ne.jp/cp/iarea?ecode=OPENAREACODE&msn=OPENAREAKEY&nl=<?php echo url::site()."keitai/checkin" ?>&posinfo=2">簡易</a>/<a href="<?php echo url::site()."keitai/checkin" ?>?guid=ON" lcs>GPS</a>)<br><?php } ?>
<?php if ($device == 'a') { ?><a href="device:gpsone?url=<?php echo url::site()."keitai/checkin" ?>&amp;ver=1&amp;datum=0&amp;unit=0">現在地の情報</a><br><?php } ?>
<?php if ($device == 's') { ?><a href="location:auto?url=<?php echo url::site()."keitai/checkin" ?>">現在地の情報</a><br><?php } ?>
<?php
  $latlong_params = "";
  if ($latlong) {
    $latlong_params = "?latlong=".$latlong;
?>
<a href="<?php echo url::site(); ?>keitai">全ｴﾘｱの情報を見る</a><br>
<hr size="1" noshade>
▼現在地<br>
<?php if(isset($area_name))echo $area_name;?>
<div style="clear:both;text-align:center;" align="center"><center><a href="http://www.google.co.jp/m/local?q=<?php echo $lat.",".$lon;?>&z=14"><img src="http://maps.google.com/maps/api/staticmap?center=<?php echo $lat.",".$lon;?>&zoom=14&size=230x100&format=gif&maptype=roadmap&sensor=false&markers=<?php echo $lat.",".$lon;?>" border="0"></a></center></div>
<?php } ?>
<hr size="1" noshade>
▼最新ﾚﾎﾟｰﾄ<br>
<?php
  foreach ($incidents as $incident) {
    $incident_date = $incident->incident_date;
    $incident_date = date('Y/m/d', strtotime($incident->incident_date));
    echo $incident->incident_title."<br>";
    echo "<div style=\"color:#808080;\">$incident_date [<a href=\"".url::site()."keitai/reports/view/".$incident->id.''.$latlong_params."\">詳細</a>]</div>";
  }
?>
<div style="text-align:right;" align="right"><right>&raquo;<a href="<?php echo url::site(); ?>keitai/reports<?php echo $latlong_params; ?>">もっと見る</a></right></div>
<hr size='1' noshade>
▼ﾆｭｰｽ<br>
<?php
  foreach ($feeds as $feed) {
    $feed_date = date('Y/m/d', strtotime($feed->item_date));
    echo $feed->item_title." ";
    echo "<div style=\"color:#808080;\">$feed_date [<a href=\"http://www.google.co.jp/gwt/x?u=".urlencode ($feed->item_link)."\">詳細</a>]</div>";
  }
?>
<hr size='1' noshade>
▼ｶﾃｺﾞﾘ別ﾚﾎﾟｰﾄ<br>
<?php
  foreach ($categories as $category => $category_info) {
    $category_title = $category_info[0];
    if (preg_match("/^([^\/]+)\/([^\/]+)$/",$category_title,$matches)) {
      $category_title = $matches[1];
    }
    $category_color = $category_info[1];
    $category_image = '';
    $category_count = $category_info[3];
    $color_css = 'class="swatch" style="background-color:#'.$category_color.'"';
    if (count($category_info[4]) == 0) {
      echo '<a href="'.url::site().'keitai/reports/index/'.$category.''.$latlong_params.'">'.$category_image.$category_title.'</a>('.$category_count.')<br>';
    } else {
      echo ''.$category_image.$category_title.'<br>';
    }
    
    // Get Children
    foreach ($category_info[4] as $child => $child_info) {
      $child_title = $child_info[0];
      $child_color = $child_info[1];
      $child_image = '';
      $child_count = $child_info[3];
      $color_css = 'class="swatch" style="background-color:#'.$child_color.'"';
      echo '<a href="'.url::site().'keitai/reports/index/'.$child.''.$latlong_params.'">'.$child_image.$child_title.'</a>('.$child_count.')';
    }
  }
?>				
