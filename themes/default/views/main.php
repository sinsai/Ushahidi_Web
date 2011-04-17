<!-- main body -->
<div id="main">
<div id="left-pane">
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
						$incident_date = $comment_incident->incident_date;
						$incident_date = date('Y/m/d', strtotime($comment_incident->comment_date));
						$incident_location = html::specialchars($comment_incident->location->location_name);
					?>
					<tr>
						<td><a href="<?php echo url::site() . 'reports/view/' . $incident_id; ?>"> <?php echo $incident_title ?></a></td>
						<td><?php echo $incident_location ?></td>
						<td><?php echo $incident_date; ?></td>
					</tr>
					<?php
					}
					?>

				</tbody>
			</table>
</div>
<!-- content -->

<div id="right-pane">
<?php								
// Map and Timeline Blocks
echo $div_map;
echo $div_timeline;
?>
</div>
<br style="clear:both;"/>
	<?php if($site_message != '') { ?>
		<div class="news-box">
			<?php echo $site_message; ?>
		</div>
		<div class="help-box">
		<p>このサイトは震災情報をみんなで集め公開しているサイトです。<br>
		被災地復興を願うボランティアスタッフが運用しています。<a href="http://www.sinsai.info/ushahidi/page/index/4">ご利用方法はこちら</a></p>
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

</div>
<!-- / main body -->

