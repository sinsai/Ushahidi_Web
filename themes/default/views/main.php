<!-- main body -->
<div id="main" class="clearingfix">
	<div id="mainmiddle" class="floatbox withright">

	<?php if($site_message != '') { ?>
		<div class="news-box">
            <h2 class="uppercase">News</h2>
			<?php echo $site_message; ?>
		</div>
		<div class="help-box">
		<p>このサイトは震災情報をみんなで集め公開しているサイトです。<br>
		被災地復興を願うボランティアスタッフが運用しています。<a href="<?php echo url::base(); ?>page/index/4">ご利用方法はこちら</a></p>
		</div>
		<div class="mobile-box">
		    <div class="mobile-box-qr">
		    <img src="http://chart.apis.google.com/chart?chs=100x100&cht=qr&chl=http://www.sinsai.info/"/>
		    </div>
		    <div class="mobile-box-description">
			<h3>モバイルサイト＆アプリ</h3>
			モバイル端末からQRコードを読み取るか
			sinsai.infoにアクセスしてください。
			<a href="https://market.android.com/details?id=sinsai.info.android.app&feature=search_result">Androidアプリ</a>でもご利用できます。
			</div>
		</div>
		<div style="clear:both;"></div>
		
	<?php } ?>

		<!-- right column -->
		<div id="right" class="clearingfix">
	
			<!-- category filters -->
			
		
			<ul id="category_switch" class="category-filters">
            <!--<li>カテゴリーを選択してください</li>-->
				<li><a class="active" id="cat_0" href="#"><img src="<?php echo url::base() ?>media/img/all.png" width='16' height='16'/><span class="category-title"><?php echo Kohana::lang('ui_main.all_categories');?></span></a></li><?php
					foreach ($categories as $category => $category_info)
					{
						$category_title = $category_info[0];
						$category_color = $category_info[1];
						$category_image = '';
						$color_css = 'class="swatch" style="background-color:#'.$category_color.'"';
						if($category_info[2] != NULL && file_exists(Kohana::config('upload.relative_directory').'/'.$category_info[2])) {
							$category_image = html::image(array(
								'src'=>Kohana::config('upload.relative_directory').'/'.$category_info[2],
								'width' => '16',
                                'height' => '16',
								));
							$color_css = '';
						}
						echo '<li><a href="#" id="cat_'. $category .'"><span '.$color_css.'>'.$category_image.'</span><span class="category-title">'.$category_title.'</span></a>';
						// Get Children
						echo '<div class="hide" id="child_'. $category .'">';
                                                if( sizeof($category_info[3]) != 0)
                                                {
                                                    echo '<ul>';
                                                    foreach ($category_info[3] as $child => $child_info)
                                                    {
                                                            $child_title = $child_info[0];
                                                            $child_color = $child_info[1];
                                                            $child_image = '';
                                                            $color_css = 'class="swatch" style="background-color:#'.$child_color.'"';
                                                            if($child_info[2] != NULL && file_exists(Kohana::config('upload.relative_directory').'/'.$child_info[2])) {
                                                                    $child_image = html::image(array(
                                                                            'src'=>Kohana::config('upload.relative_directory').'/'.$child_info[2],
                                                                            'style'=>'float:left;padding-right:5px;width:16px;height:16px;',
                                                                            ));
                                                                    $color_css = '';
                                                            }
                                                            echo '<li style="padding-left:20px;"><a href="#" id="cat_'. $child .'"><span '.$color_css.'>'.$child_image.'</span><span class="category-title">'.$child_title.'</span></a></li>';
                                                    }
                                                    echo '</ul>';
                                                }
						echo '</div></li>';
					}
				?></ul>
			<!-- / category filters -->
			
			<?php
			if ($layers)
			{
				?>
				<!-- Layers (KML/KMZ) -->
				<div class="cat-filters clearingfix" style="margin-top:20px;">
					<strong><?php echo Kohana::lang('ui_main.layers_filter');?> <span>[<a href="javascript:toggleLayer('kml_switch_link', 'kml_switch')" id="kml_switch_link"><?php echo Kohana::lang('ui_main.hide'); ?></a>]</span></strong>
				</div>
				<ul id="kml_switch" class="category-filters">
					<?php
					foreach ($layers as $layer => $layer_info)
					{
						$layer_name = $layer_info[0];
						$layer_color = $layer_info[1];
						$layer_url = $layer_info[2];
						$layer_file = $layer_info[3];
						$layer_link = (!$layer_url) ?
							url::base().Kohana::config('upload.relative_directory').'/'.$layer_file :
							$layer_url;
						echo '<li><a href="#" id="layer_'. $layer .'"
						onclick="switchLayer(\''.$layer.'\',\''.$layer_link.'\',\''.$layer_color.'\'); return false;"><div class="swatch" style="background-color:#'.$layer_color.'"></div>
						<div>'.$layer_name.'</div></a></li>';
					}
					?>
				</ul>
				<!-- /Layers -->
				<?php
			}
			?>
			
			
			<?php
			if ($shares)
			{
				?>
				<!-- Layers (Other Ushahidi Layers) -->
				<div class="cat-filters clearingfix" style="margin-top:20px;">
					<strong><?php echo Kohana::lang('ui_main.other_ushahidi_instances');?> <span>[<a href="javascript:toggleLayer('sharing_switch_link', 'sharing_switch')" id="sharing_switch_link"><?php echo Kohana::lang('ui_main.hide'); ?></a>]</span></strong>
				</div>
				<ul id="sharing_switch" class="category-filters">
					<?php
					foreach ($shares as $share => $share_info)
					{
						$sharing_name = $share_info[0];
						$sharing_color = $share_info[1];
						echo '<li><a href="#" id="share_'. $share .'"><div class="swatch" style="background-color:#'.$sharing_color.'"></div>
						<div>'.$sharing_name.'</div></a></li>';
					}
					?>
				</ul>
				<!-- /Layers -->
				<?php
			}
			?>

			<!-- additional content -->
			<?php
			if (Kohana::config('settings.allow_reports'))
			{
				?>
				<div class="additional-content">
					<h5><?php echo Kohana::lang('ui_main.how_to_report'); ?></h5>
					<ol>
						<?php if (!empty($phone_array)) 
						{ ?><li><?php echo Kohana::lang('ui_main.report_option_1')." "; ?> <?php foreach ($phone_array as $phone) {
							echo "<strong>". $phone ."</strong>";
							if ($phone != end($phone_array)) {
								echo " or ";
							}
						} ?></li><?php } ?>
						<?php if (!empty($report_email)) 
						{ ?><li><?php echo Kohana::lang('ui_main.report_option_2')." "; ?> <a href="mailto:<?php echo $report_email?>"><?php echo $report_email?></a></li><?php } ?>
						<?php if (!empty($twitter_hashtag_array)) 
									{ ?><li><?php echo Kohana::lang('ui_main.report_option_3')." "; ?> <?php foreach ($twitter_hashtag_array as $twitter_hashtag) {
						echo "<strong>". $twitter_hashtag ."</strong>";
						if ($twitter_hashtag != end($twitter_hashtag_array)) {
							echo " or ";
						}
						} ?></li><?php
						} ?><li><a href="<?php echo url::site() . 'reports/submit/'; ?>"><?php echo Kohana::lang('ui_main.report_option_4'); ?></a></li>
					</ol>

				</div>
			<?php } ?>
			<!-- / additional content -->
			
			<?php
			// Action::main_sidebar - Add Items to the Entry Page Sidebar
			Event::run('ushahidi_action.main_sidebar');
			?>
			<!--content-container-->
                <!-- #related-link -->
            <div id="related-link" class="section">
                <h5>関連リンク</h5>
                <dl>
                    <dt><a href="<?php echo url::base(); ?>page/index/1">安否確認・伝言板</a></dt>
                    <dd>Googleパーソンファインダーや、各ケータイキャリアの災害掲示板などのリンク集。</dd>
                </dl>
                <dl>
                    <dt><a href="<?php echo url::base(); ?>page/index/2">被災地画像/映像</a></dt>
<dd>空撮/衛星画像や、各国TV番組の映像や政府のメッセージやなどのリンク集。</dd>
                </dl>
                <dl>
                    <dt><a href="http://sinsai-info.blogspot.com/" target="_blank">sinsai.infoオフィシャルブログ</a></dt>
                    <dd>ボランティアスタッフが、更新情報やお知らせなどをお伝えし
ています。</dd>
                </dl>
                </div><!-- #related-link -->
                <!--banner area-->
                <div class="banner">
                    <a href="http://tasukeaijapan.jp/" target="_blank"><img src="<?php echo url::base(); ?>themes/default/images/banner_tasukeai.jpg" alt="助け合いジャパン"/ style="width:285;height:45"></a>
                    <a href="http://www.hack4.jp/" target="_blank" rel="nofollow"><img src="http://sites.google.com/site/hackforjapan/RelatedInfo/unity/234x60.png" style="width:234px;height:60px;" alt="Hack For Japan 「コードでつなぐ。想いと想い」"></a>
                    <a href="http://www.311er.jp"><img src="http://www.311er.jp/wp-content/uploads/2011/04/bnr_160b.gif" title="Rescue311" width="160" height="80" /></a>
                    <a href="http://www.tomoni.net/"><img src="<?php echo url::base(); ?>themes/default/images/banner_tomoni.gif" title="tomoni.net" width="234" height="60" /></a>
                    <a href="http://www.osmf.jp/"><img src="<?php echo url::base(); ?>themes/default/images/banner_osmfj.gif" title="osmf japan" width="233" height="100" /></a>
<A HREF="http://fumbaro.org/" TARGET="_top"><IMG SRC="http://fumbaro.org/about/images/fjm_logo_harf.gif" BORDER="0" WIDTH="234" HEIGHT="60"></A>
                </div>
                <!--banner area-->
		<!-- / right column -->
		</div>
		<!-- content column -->
		<div id="content" class="clearingfix">
			<div class="floatbox">
			
				<!-- filters -->
				<div class="filters-top clearingfix map_above">
					<div class="map_description">
						<h3>レポートを地図から探す</h3>
                        <div>◎をクリックすると、そのエリアで投稿されたレポートがご覧になれます。</div>
					</div>
                                </div>
                <div style="clear:both;"></div>
				<!-- / filters -->
				
				<?php								
				// Map and Timeline Blocks
				echo $div_map;
				echo $div_timeline;
				?>
                                <div style="float:right;font-size:large;">
                                        <?php
                                        // Action::main_filters - Add items to the main_filters
                                        //Event::run('ushahidi_action.map_main_filters');
                                        $menu = "";
					$menu .= "<a href=\"#\" id=\"view_this_location\">現在地を表示</a>";
					$menu .= "&nbsp;&nbsp;";
					$menu .= "<a target=\"bigmap\" href=\"".url::site()."bigmap\" ";
                                        $menu .= ">全画面表示</a>";
                                        echo $menu;
                                        ?>
                                </div>

			</div>
			<!-- content -->
			<div class="content-container">
				<!-- content blocks -->
				<div class="content-blocks clearingfix">
					<!-- newreports-container -->
					<div class="newreports-container">
						<div class="clearfix">
							<div style="float:left"><h5><?php echo Kohana::lang('ui_main.incidents_listed'); ?></h5></div>
							<div style="float:right"><a class="more" href="<?php echo url::site() . 'reports/' ?>"><?php echo Kohana::lang('ui_main.view_more'); ?></a></div>
						</div>
						<table class="table-list">
							<thead>
								<tr>
									<th scope="col" class="title"><?php echo Kohana::lang('ui_main.title'); ?></th>
									<th scope="col" class="location"><?php echo Kohana::lang('ui_main.location'); ?></th>
									<th scope="col" class="date"><?php echo Kohana::lang('ui_main.date'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
									if ($total_items == 0)
								{
								?>
								<tr><td colspan="3"><?php echo Kohana::lang('ui_main.no_reports'); ?></td></tr>

								<?php
								}
								foreach ($incidents as $incident)
								{
									$incident_id = $incident->id;
									$incident_title = text::limit_chars(html::specialchars($incident->incident_title), 40, '...', True);
									$incident_date = $incident->incident_date;
									$incident_date = date('Y/m/d', strtotime($incident->incident_date));
									$incident_location = html::specialchars($incident->location->location_name);
								?>
								<tr>
									<td><a class="wordBreak" href="<?php echo url::site() . 'reports/view/' . $incident_id; ?>"> <?php echo $incident_title ?></a></td>
									<td><?php echo $incident_location ?></td>
									<td><?php echo $incident_date; ?></td>
								</tr>
								<?php
								}
								?>

							</tbody>
						</table>
					</div>
					<!-- / newreports-container -->
					<!-- newcomments-container -->
					<div class="newcomments-container">
						<h5><?php echo Kohana::lang('ui_main.comments_listed'); ?></h5>
						<table class="table-list">
							<thead>
								<tr>
									<th scope="col" class="title"><?php echo Kohana::lang('ui_main.title'); ?></th>
									<th scope="col" class="location"><?php echo Kohana::lang('ui_main.location'); ?></th>
									<th scope="col" class="date"><?php echo Kohana::lang('ui_main.date'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
									if ($total_items == 0)
								{
								?>
								<tr><td colspan="3"><?php echo Kohana::lang('ui_main.no_reports'); ?></td></tr>

								<?php
								}
								foreach ($comment_incidents as $comment_incident)
								{
									$incident_id = $comment_incident->id;
									$incident_title = text::limit_chars(html::specialchars($comment_incident->incident_title), 40, '...', True);
									$incident_date = date('Y/m/d', strtotime($comment_incident->comment_date));
									$incident_location = html::specialchars($comment_incident->location_name);
								?>
								<tr>
									<td><a class="wordBreak" href="<?php echo url::site() . 'reports/view/' . $incident_id; ?>"> <?php echo $incident_title ?></a></td>
									<td><?php echo $incident_location ?></td>
									<td><?php echo $incident_date; ?></td>
								</tr>
								<?php
								}
								?>

							</tbody>
						</table>
					</div>
					<!-- / newcomments-container -->
				<!--officialnews-container-->
				<div class="officialnews-container">
					<div class="clearingfix">
						<div style="float:left"><h5><?php echo Kohana::lang('ui_main.official_news'); ?></h5></div>
						<div style="float:right"><a class="more" href="<?php echo url::site() . 'feeds' ?>"><?php echo Kohana::lang('ui_main.view_more'); ?></a></div>
					</div>
							<?php
								foreach($feeds as $feed){
									echo "<div>".$feed["name"]."</div>";
									echo '<div style="font-size:0.85em">';
		                            if ($feed["feed_item"]->count() != 0)
		                            {
		                                foreach ($feed["feed_item"] as $feed)
		                                {
		                                        $feed_id = $feed->id;
		                                        $feed_title = text::limit_chars($feed->item_title, 40, '...', True);
		                                        $feed_link = $feed->item_link;
		                                        $feed_date = date('Y/m/d', strtotime($feed->item_date));
		                                        $feed_source = text::limit_chars($feed->feed->feed_name, 15, "...");
		                                ?>
		                                        <div class="official-detail"><a href="<?php echo $feed_link; ?>" target="_blank"><?php echo html::specialchars($feed_title) ?></a></div>
		                                <?php
		                                }
		                            }
									echo "</div>";
								}
							?>
                </div><!-- / officialnews-container-->
				</div>
				<!-- /content blocks -->
			</div>
			<!-- content -->
		</div>
		<!-- / content column -->

	</div>
</div>
<!-- / main body -->

