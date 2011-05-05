<div class="block">
	<h2 class="submit"><a href="<?php echo url::site()."mobile/reports/submit/" ?>">Submit A Report</a></h2>
</div>
<div class="block">
	<h2 class="expand">Recent Reports</h2>
	<div class="collapse">
		<ul>
			<?php
			foreach ($incidents as $incident)
			{
				$incident_date = $incident->incident_date;
				$incident_date = date('M j Y', strtotime($incident->incident_date));
				echo "<li><strong><a href=\"".url::site()."mobile/reports/view/".$incident->id."\">".$incident->incident_title."</a></strong>";
				echo "&nbsp;&nbsp;<i>$incident_date</i></li>";
			}
			?>
		</ul>
	</div>
</div>
<div class="block">
	<h2 class="expand">Related News</h2>
	<div class="collapse">
		<ul>
			<?php
			foreach ($feeds as $feed)
			{
				$feed_date = date('M j Y', strtotime($feed->item_date));
				echo "<li><strong><a href=\"".$feed->item_link."\">".$feed->item_title."</a></strong>";
				//echo "&nbsp;&nbsp;<i>$incident_date</i></li>";
				echo "</li>";
			}
		?>
		</ul>
	</div>
</div>
<h2 class="block_title">Reports By AreaName</h2>
<form action="<?php echo rtrim($_SERVER['PHP_SELF'],'/') ?>/reports/index/" id="area-search" method="GET">
<input type="hidden" name="mode" value="areasearch">
	Keyword：<input type="text" name="keyword" value="<?php if(isset($_GET["keyword"])){echo $_GET["keyword"];}?>" />
	AreaName：<input type="text" name="address" value="<?php if(isset($_GET["address"])){echo $_GET["address"];}?>" />
	<select name="distance">
		<option value="0.5" <?php if(isset($_GET["distance"]) && $_GET["distance"] == 0.5)echo "selected" ?>>500m</option>
		<option value="1" <?php if(isset($_GET["distance"]) && $_GET["distance"] == 1)echo "selected" ?>>1km</option>
		<option value="2" <?php if(isset($_GET["distance"]) && $_GET["distance"] == 2)echo "selected" ?>>2km</option>
		<option value="3" <?php if(isset($_GET["distance"]) && $_GET["distance"] == 3)echo "selected" ?>>3km</option>
		<option value="5" <?php if(isset($_GET["distance"]) && $_GET["distance"] == 5)echo "selected" ?>>5km</option>
		<option value="10" <?php if(isset($_GET["distance"]) && $_GET["distance"] == 10)echo "selected" ?>>10km</option>
		<option value="20" <?php if(isset($_GET["distance"]) && $_GET["distance"] == 20)echo "selected" ?>>20km</option>
		<option value="30" <?php if(isset($_GET["distance"]) && $_GET["distance"] == 30)echo "selected" ?>>30km</option>
		<option value="50" <?php if(isset($_GET["distance"]) && $_GET["distance"] == 50)echo "selected" ?>>50km</option>
	</select>
	<?php
	if(!isset($_GET["order"]) || $_GET["order"]==="new"){
	echo '<input type="radio" name="order" value="new" checked>news&nbsp;';
	echo '<input type="radio" name="order" value="dist">nears';
	}else{
	echo '<input type="radio" name="order" value="new">news&nbsp;';
	echo '<input type="radio" name="order" value="dist" checked>nears';
	}
	?>&nbsp;
	<input type="submit" name="submit" value="Search" />
</form>

<h2 class="block_title">Reports By Category</h2>
<div class="block">
	<?php
	foreach ($categories as $category => $category_info)
	{
		$category_title = $category_info[0];
		$category_color = $category_info[1];
		$category_image = '';
		$color_css = 'class="swatch" style="background-color:#'.$category_color.'"';
		if($category_info[2] != NULL && file_exists(Kohana::config('upload.relative_directory').'/'.$category_info[2])) {
			$category_image = html::image(array(
				'src'=>Kohana::config('upload.relative_directory').'/'.$category_info[2],
				'style'=>'float:left;padding-right:5px;',
				'width' => '16',
                'height' => '16',
				));
			$color_css = '';
		}
		$category_count = $category_info[3];
		if (count($category_info[4]) == 0)
		{
			echo '<h2 class="other"><a href="'.url::site().'mobile/reports/index/'.$category.'"><div '.$color_css.'>'.$category_image.'</div>'.$category_title.'</a><span>'.$category_count.'</span></h2>';
		}
		else
		{
			echo '<h2 class="expand"><div '.$color_css.'>'.$category_image.'</div>'.$category_title.'</h2>';
		}
		
		// Get Children
		echo '<div class="collapse">';
		foreach ($category_info[4] as $child => $child_info)
		{
			$child_title = $child_info[0];
			$child_color = $child_info[1];
			$child_image = '';
			$child_count = $child_info[3];
			$color_css = 'class="swatch" style="background-color:#'.$child_color.'"';
			echo '<h2 class="other"><a href="'.url::site().'mobile/reports/index/'.$child.'"><div '.$color_css.'>'.$child_image.'</div>'.$child_title.'</a><span>'.$child_count.'</span></h2>';
		}
		echo '</div>';
	}
	?>				
</div>
<h2 class="block_title">More</h2>
<div class="block">
	<h2 class="other"><a href="<?php echo url::site()."contact"; ?>">Contact Us</a></h2>
	<h2 class="other"><a href="<?php echo url::site()."page/index/9"; ?>">About Us</a></h2>
</div>
