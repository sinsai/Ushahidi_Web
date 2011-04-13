<div id="content">
	<div class="content-bg">
		<!-- start reports block -->
		<div class="big-block">
			<?php
			// Filter::report_stats - The block that contains reports list statistics
			Event::run('ushahidi_filter.report_stats', $report_stats);
			echo $report_stats;
			?>
			<h2><?php echo ($area_name) ? $area_name."周辺の" : ""?> <?php echo ($disp_distance) ? "半径".$disp_distance."の" : ""?> <?php echo Kohana::lang('ui_main.reports').": ";?> <?php echo ($category_title) ? " in $category_title" : ""?> <?php echo $pagination_stats; ?></h2>
<?php
			echo '<a class="category_menu" href="'.url::site().'reports/';
			if(isset($_GET['sw'])){
				echo '?sw='.$_GET['sw'];
				if($keyword)echo '&keyword='.$keyword;
				if($address)echo '&address='.$address;
				if($distance)echo '&distance='.$distance;
			}else{
				if($keyword)echo '?keyword='.$keyword;
				if($address)echo '&address='.$address;
				if($distance)echo '&distance='.$distance;
			}
			if(isset($_GET['ne']))echo '&ne='.$_GET['ne'];
			echo '"><img src="'.url::base().'/media/img/all.png" width="16" height="16"/>';
			echo '<span>全カテゴリ</span></a>';
			foreach($category_master as $key => $category){
				echo '<a class="category_menu" href="'.url::site().'reports/?c='.$key;
				if($sw)echo '&sw='.$sw;
				if($ne)echo '&ne='.$ne;
				if($keyword)echo '&keyword='.$keyword;
				if($address)echo '&address='.$address;
				if($distance)echo '&distance='.$distance;
				echo '" >';
				if(isset($category['category_image_thumb'])){
				    echo '<img src="/ushahidi/media/uploads/'.$category['category_image_thumb'].'"/>';
				}else{
				    echo '<span style="width:16px;height:16px;background-color:#'.$category['color'].'"> &nbsp;</span>';
				}
				echo '<span style="float:;">'.$category['title'].'</span></a>';
			}
?>
<div style="margin:10px 0px 10px 0px;padding:5px 0px 5px 0px;">
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" id="area-search" method="GET">
<input type="hidden" name="mode" value="areasearch">
<?php echo ($c) ? '<input type="hidden" name="c" value="'.$c.'">' : ""?>
<?php echo ($l) ? '<input type="hidden" name="l" value="'.$l.'">' : ""?>
<?php echo ($sw) ? '<input type="hidden" name="sw" value="'.$sw.'">' : ""?>
<?php echo ($ne) ? '<input type="hidden" name="ne" value="'.$ne.'">' : ""?>
検索キーワード：<input type="text" name="keyword" value="<?php if(isset($_GET["keyword"])){echo $_GET["keyword"];}?>" />
検索地区：<input type="text" name="address" value="<?php if(isset($_GET["address"])){echo $_GET["address"];}?>" />
<select name="distance">
	<option value="0.5" <?php if(isset($_GET["distance"]) && $_GET["distance"] == 0.5)echo "selected" ?>>500m</option>
	<option value="1" <?php if(isset($_GET["distance"]) && $_GET["distance"] == 1)echo "selected" ?>>1km</option>
	<option value="2" <?php if(isset($_GET["distance"]) && $_GET["distance"] == 2)echo "selected" ?>>2km</option>
	<option value="3" <?php if(isset($_GET["distance"]) && $_GET["distance"] == 3)echo "selected" ?>>3km</option>
	<option value="5" <?php if(isset($_GET["distance"]) && $_GET["distance"] == 5)echo "selected" ?>>5km</option>
	<option value="10" <?php if(isset($_GET["distance"]) && $_GET["distance"] == 10)echo "selected" ?>>10km</option>
	<option value="20" <?php if(isset($_GET["distance"]) && $_GET["distance"] == 20)echo "selected" ?>>20km</option>
	<option value="30" <?php if(isset($_GET["distance"]) && $_GET["distance"] == 30)echo "selected" ?>>30km</option>
</select>
<?php
if(!isset($_GET["order"]) || $_GET["order"]==="new"){
echo '<input type="radio" name="order" value="new" checked>新着順&nbsp;';
echo '<input type="radio" name="order" value="dist">近隣順';
}else{
echo '<input type="radio" name="order" value="new">新着順&nbsp;';
echo '<input type="radio" name="order" value="dist" checked>近隣順';
}
?>&nbsp;
<input type="submit" name="submit" value="周辺のレポートを検索" />
</form>
</div>
			<div style="clear:both;"></div>
			<div class="r_cat_tooltip"> <a href="#" class="r-3">2a. Structures a risque | Structures at risk</a> </div>
			<div class="reports-box">
				<?php
				if($choices_flg){
					echo "検索地区が1件に絞り込めませんでした<br/>都道府県名から入力をしてみてください。";
				}else{
					foreach ($incidents as $incident)
					{
						$incident_id = $incident->id;
						$incident_title = html::specialchars($incident->incident_title);
						$incident_description = html::specialchars($incident->incident_description);
						$incident_description = preg_replace('/((https?|http)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+))/', '<a href="$1">$1</a>', $incident_description);
						$incident_description = nl2br($incident_description);
						if(isset($incident->dist))$incident_dist = $incident->dist;
						//$incident_category = $incident->incident_category;
						// Trim to 150 characters without cutting words
						// XXX: Perhaps delcare 150 as constant

						$incident_description = text::limit_chars(strip_tags($incident_description), 150, "...", true);
						$incident_date = date('Y/m/d H:i', strtotime($incident->incident_date));
						//$incident_time = date('H:i', strtotime($incident->incident_date));
						$location_id = $incident->location_id;
						$location_name = html::specialchars($incident->location->location_name);
						$incident_verified = $incident->incident_verified;

						if ($incident_verified)
						{
							$incident_verified = '<span class="r_verified">'.Kohana::lang('ui_main.verified').'</span>';
						}
						else
						{
							$incident_verified = '<span class="r_unverified">'.Kohana::lang('ui_main.unverified').'</span>';
						}
						
						$comment_count = $incident->comment->count();
						
						$incident_thumb = url::site()."media/img/report-thumb-default.jpg";
						$media = $incident->media;
						if ($media->count())
						{
							foreach ($media as $photo)
							{
								if ($photo->media_thumb)
								{ // Get the first thumb
									$prefix = url::base().Kohana::config('upload.relative_directory');
									$incident_thumb = $prefix."/".$photo->media_thumb;
									break;
								}
							}
						}
						?>
						<div class="rb_report">

							<div class="r_media">
								<p class="r_photo"> <a href="<?php echo url::site(); ?>reports/view/<?php echo $incident_id; ?>">
									<img src="<?php echo $incident_thumb; ?>" height="59" width="89" /> </a>
								</p>

								<!-- Only show this if the report has a video -->
								<p class="r_video" style="display:none;"><a href="#">Video</a></p>
								
								<!-- Category Selector -->
								<div class="r_categories">
									<h4><?php echo Kohana::lang('ui_main.categories'); ?></h4>
									<?php
									foreach ($incident->category AS $category)
									{
										if ($category->category_image_thumb)
										{
											?>
											<a class="r_category" href="<?php echo url::site(); ?>reports/?c=<?php echo $category->id; ?>"><span class="r_cat-box"><img src="<?php echo url::base().Kohana::config('upload.relative_directory')."/".$category->category_image_thumb; ?>" height="16" width="16" /></span> <span class="r_cat-desc"><?php echo $localized_categories[(string)$category->category_title];?></span></a>
											<?php
										}
										else
										{
											?>
											<a class="r_category" href="<?php echo url::site(); ?>reports/?c=<?php echo $category->id; ?>"><span class="r_cat-box" style="background-color:#<?php echo $category->category_color;?>;"></span> <span class="r_cat-desc"><?php echo $localized_categories[(string)$category->category_title];?></span></a>
											<?php
										}
									}
									?>
								</div>
							</div>

							<div class="r_details">
								<h3><a class="r_title" href="<?php echo url::site(); ?>reports/view/<?php echo $incident_id; ?>"><?php echo $incident_title; ?></a> <a href="<?php echo url::site(); ?>reports/view/<?php echo $incident_id; ?>#discussion" class="r_comments"><?php echo $comment_count; ?></a> <?php echo $incident_verified; ?></h3>
								<?php if(isset($incident_dist))echo " 選択地点からの距離:$incident_dist"."km" ; ?> 
								<p class="r_date r-3 bottom-cap"><?php echo $incident_date; ?></p>
								<div class="r_description"> <?php echo $incident_description; ?> </div>
								<p class="r_location"><a href="<?php echo url::site(); ?>reports/?l=<?php echo $location_id; ?>"><?php echo $location_name; ?></a></p>
							</div>
						</div>
					<?php
					}
				}
					?>
			</div>
			<?php echo $pagination; ?>
		</div>
		<!-- end reports block -->
	</div>
</div>
