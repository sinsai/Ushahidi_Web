<div class="report_list">
	<div class="block">
		<?php
		if ($category AND $category->loaded)
		{
			$category_id = $category->id;
			$color_css = 'class="swatch" style="background-color:#'.$category->category_color.'"';
			echo '<h2 class="other"><a href="#"><div '.$color_css.'></div>'.$category->category_title.'</a></h2>';
		}
		else
		{
			$category_id = "";
		}
		?>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" id="area-search" method="GET">
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
		<option value="100" <?php if(isset($_GET["distance"]) && $_GET["distance"] == 100)echo "selected" ?>>100km</option>
		<option value="150" <?php if(isset($_GET["distance"]) && $_GET["distance"] == 150)echo "selected" ?>>150km</option>
		<option value="200" <?php if(isset($_GET["distance"]) && $_GET["distance"] == 200)echo "selected" ?>>200km</option>
		<option value="250" <?php if(isset($_GET["distance"]) && $_GET["distance"] == 250)echo "selected" ?>>250km</option>
		<option value="300" <?php if(isset($_GET["distance"]) && $_GET["distance"] == 300)echo "selected" ?>>300km</option>
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
		<div class="list">
			<ul>
				<?php
				if($choices_flg){
					echo "<li>入力された住所が1件に絞り込めませんでした。<br/>都道府県から入力してみてください。</li>";
				}elseif ($incidents->count()){
					$page_no = (isset($_GET['page'])) ? $_GET['page'] : "";
					foreach ($incidents as $incident)
					{
						$incident_date = $incident->incident_date;
						$incident_date = date('Y/m/d', strtotime($incident->incident_date));
						$location_name = $incident->location_name;
						echo "<li><strong><a href=\"".url::site()."mobile/reports/view/".$incident->id."?c=".$category_id."&p=".$page_no."\">".$incident->incident_title."</a></strong>";
						echo "&nbsp;&nbsp;<i>$incident_date</i>";
						echo "<BR /><span class=\"location_name\">".$location_name."</span>";
						if($area_name!=""){
							$location_dist = $incident->dist;
							echo "<BR /><span class=\"location_name\">".$location_dist."km</span>";
						}
						echo "</li>";
					}
				}else{
					echo "<li>No Reports Found</li>";
				}
				?>
			</ul>
		</div>
		<?php echo $pagination; ?>
	</div>
</div>
