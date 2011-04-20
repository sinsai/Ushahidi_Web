<?php
// Filter::report_stats - The block that contains reports list statistics
Event::run('ushahidi_filter.report_stats', $report_stats);
echo $report_stats;
?>
<?php

?>
<div style="margin:10px 0px 10px 0px;padding:5px 0px 5px 0px;">
<?php
	$sws = explode(',',$sw);
	$nes = explode(',',$ne);
?>

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
							<div class="r_details">
								<h3><a class="r_title" href="<?php echo url::site(); ?>reports/view/<?php echo $incident_id; ?>"><?php echo $incident_title; ?></a> <a href="<?php echo url::site(); ?>reports/view/<?php echo $incident_id; ?>#discussion" class="r_comments"><?php echo $comment_count; ?></a></h3>
								<?php if(isset($incident_dist))echo " 選択地点からの距離:$incident_dist"."km" ; ?> 
								
								<div class="r_description"> <?php echo $incident_description; ?> </div>
								<p class="r_location"><a href="<?php echo url::site(); ?>reports/?l=<?php echo $location_id; ?>"><?php echo $location_name; ?></a></p>
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
									<h3><?php echo $incident_verified; ?></h3>
									<p class="r_date r-3 bottom-cap"><?php echo $incident_date; ?></p>
								</div>
							</div>
						</div>
					<?php
					}
				}
					?>
			</div>
			<?php echo $lon?>
			<script language="text/javascript">
			<!--
			<?php if(isset($lon)){?>
			var lonlat = new OpenLayers.LonLat(<?php echo $lon?>,<?php echo $lat?>).transform(proj_4326, map.getProjectionObject());
			map.setCenter(lonlat, <?echo $zoom_level ?>);
			//-->
			<?php }?>
			</script>
			<?php echo $pagination; ?>

