<div id="main" class="report_detail">

	<div class="left-col" style="float:left;width:520px; margin-right:20px">
	
  	  <?php
    	  if ($incident_verified)
    		{
    			echo '<p class="r_verified">'.Kohana::lang('ui_main.verified').'</p>';
    		}
    		else
    		{
    		    ?>
    		    <div style="color:red;">※本件、未確認情報です。かならず情報提供元のページで最新の情報を確認してください。</div>
    		    <?php
    			echo '<p class="r_unverified">'.Kohana::lang('ui_main.unverified').'</p>';
    		}
  	  ?>	
  	  
  	    <?php
  	     if((time() - strtotime($incident_date))/(3600*24)  > 6) { ?>
        <div style="color:red;">※情報提供から1週間以上経過しています。かならず情報提供元のページで最新の情報を確認してください。</div>
        <?php }?>
		<h1 class="report-title"><?php
			echo html::specialchars($incident_title);
			
			// If Admin is Logged In - Allow For Edit Link
			if ($logged_in)
			{
				echo " [&nbsp;<a href=\"".url::site()."admin/reports/edit/".$incident_id."\">".Kohana::lang('ui_main.edit')."</a>&nbsp;]";
			}
		?></h1>
	
		<p class="report-when-where">
			<span class="r_date"><?php echo $incident_date.' '.$incident_time; ?> </span>
			<span class="r_location"><?php echo  html::specialchars($incident_location); ?></span>
		</p>
	
		<div class="report-category-list">
		<p>
			<?php
				foreach($incident_category as $category) 
				{ 
				  if ($category->category->category_image_thumb)
					{
					?>
					<a href="<?php echo url::site()."reports/?c=".$category->category->id; ?>"><span class="r_cat-box" style="background:transparent url(<?php echo url::base().Kohana::config('upload.relative_directory')."/".$category->category->category_image_thumb; ?>) 0 0 no-repeat;">&nbsp;</span> <?php echo $category->category->category_title; ?></a>
					
					<?php 
					}
					else
					{
					?>
					  <a href="<?php echo url::site()."reports/?c=".$category->category->id; ?>"><span class="r_cat-box" style="background-color:#<?php echo $category->category->category_color; ?>">&nbsp;</span> <?php echo $category->category->category_title; ?></a>
				  <?php
				  }
				}
			?>
			</p>
			<?php
			// Action::report_meta - Add Items to the Report Meta (Location/Date/Time etc.)
			Event::run('ushahidi_action.report_meta', $incident_id);
			?>
		</div>
		
		<div class="report-description-text">
			<h5><?php echo Kohana::lang('ui_main.reports_description');?></h5>
			<?php echo  nl2br(preg_replace('/((https?|http)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+))/', '<a href="$1">$1</a>',html::specialchars($incident_description))); 
                              Event::run('ushahidi_action.report_extra', $incident_id);
                        ?>
			<?php
			 $show_comment = true;
			 if ( count($incident_news) > 0 ) {?>
            <br /><br /><h5><?php echo Kohana::lang('ui_main.reports_news');?></h5>
            <?php   
                foreach ($incident_news as $news)
                    {
                        if(strpos($news, "http", 0) === 0){
                           echo '<a href="'.$news.'">'.$news.'</a> ';
                        }
                        if(strpos($news, "http://tasukeai.heroku.com/messages/show/", 0) === 0){
                            $show_comment = false;
                        }
                        
                    } ?>
			<?php } ?>
			<div class="social_button">
				<div id="twitter"></div>
				<div id="facebook"></div>
				<div id="tumblr"></div>
				<div id="gree"></div>
				<div id="hatena"></div>
			</div>
		<?php
			foreach($twitter_htmls as $twitter_html){
				echo $twitter_html;
			}
		?>
			<div class="credibility">
				<table class="rating-table" cellspacing="0" cellpadding="0" border="0">
          <tr>
            <td><?php echo Kohana::lang('ui_main.credibility');?>:</td>
            <td><a href="javascript:rating('<?php echo $incident_id; ?>','add','original','oloader_<?php echo $incident_id; ?>')"><img id="oup_<?php echo $incident_id; ?>" src="<?php echo url::base() . 'media/img/'; ?>up.png" alt="UP" title="UP" border="0" /></a></td>
            <td><a href="javascript:rating('<?php echo $incident_id; ?>','subtract','original')"><img id="odown_<?php echo $incident_id; ?>" src="<?php echo url::base() . 'media/img/'; ?>down.png" alt="DOWN" title="DOWN" border="0" /></a></td>
            <td><a href="" class="rating_value" id="orating_<?php echo $incident_id; ?>"><?php echo $incident_rating; ?></a></td>
            <td><a href="" id="oloader_<?php echo $incident_id; ?>" class="rating_loading" ></a></td>
          </tr>
        </table>
			</div>
		</div>
		
		<?php
		    if($show_comment){
			    // Filter::comments_block - The block that contains posted comments
			    Event::run('ushahidi_filter.comment_block', $comments);
			    echo $comments;
			    // Filter::comments_form_block - The block that contains the comments form
			    Event::run('ushahidi_filter.comment_form_block', $comments_form);
			    echo $comments_form;
			}
		?>
	
	</div>
	
	<div style="float:right;width:350px;">

		<div class="report-media-box-tabs">
			<ul>
				<li class="report-tab-selected"><a class="tab-item" href="#report-map"><?php echo Kohana::lang('ui_main.map');?></a></li>
				<?php if( count($incident_photos) > 0 ) { ?>
					<li><a class="tab-item" href="#report-images"><?php echo Kohana::lang('ui_main.images');?></a></li>
				<?php } ?>
				<?php if( count($incident_videos) > 0 ) { ?>
					<li><a class="tab-item" href="#report-video"><?php echo Kohana::lang('ui_main.video');?></a></li>
				<?php } ?>
			</ul>
		</div>
		
		<div class="report-media-box-content">
			
			<div id="report-map" class="report-map">
				<div class="map-holder" id="map"></div>
        <ul class="map-toggles">
          <li><a href="#" class="smaller-map">Smaller map</a></li>
          <li style="display:block;"><a href="#" class="wider-map">Wider map</a></li>
          <li><a href="#" class="taller-map">Taller map</a></li>
          <li><a href="#" class="shorter-map">Shorter Map</a></li>
        </ul>
        <div style="clear:both"></div>
			</div>
			
			<!-- start images -->
			<?php if( count($incident_photos) > 0 ) { ?>
				<div id="report-images" style="display:none;">
						<?php
						foreach ($incident_photos as $photo)
						{
							$thumb = str_replace(".","_t.",$photo);
							$prefix = url::base().Kohana::config('upload.relative_directory');
							echo '<a class="photothumb" rel="lightbox-group1" href="'.$prefix.'/'.$photo.'"><img src="'.$prefix.'/'.$thumb.'"/></a> ';
						}
						?>
				</div>
			<?php } ?>
			<!-- end images -->
			
			<!-- start videos -->
			<?php if( count($incident_videos) > 0 ) { ?>
				<div id="report-video" style="display:none;">
					<?php
						// embed the video codes
						foreach( $incident_videos as $incident_video) {
							$videos_embed->embed($incident_video,'');
						}
					?>
				</div>
			<?php } ?>
			<!-- end videos -->
		
		</div>
		<div class="report-additional-reports">
			<h4><?php echo Kohana::lang('ui_main.additional_reports');?></h4>
			<?php foreach($incident_neighbors as $neighbor) { ?>
			  <div class="rb_report">
  			  <h5><a href="<?php echo url::site(); ?>reports/view/<?php echo $neighbor->id; ?>"><?php echo  html::specialchars($neighbor->incident_title); ?></a></h5>
  			  <p class="r_date r-3 bottom-cap"><?php echo date('H:i M d, Y', strtotime($neighbor->incident_date)); ?></p>
  			  <p class="r_location"><?php echo  html::specialchars($neighbor->location_name).", ".round($neighbor->distance, 2); ?> Kms</p>
  			</div>
      <?php } ?>
		</div>

	</div>
	
	<div style="clear:both;"></div>
	
	
	
	
</div>
