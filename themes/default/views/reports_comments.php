<?php if(count($incident_comments) > 0) { ?>

<div class="report-comments">
					
	<h5><?php echo Kohana::lang('ui_main.comments'); ?></h5>

	<?php foreach($incident_comments as $comment) { ?>
		<div class="report-comment-box">
	
			<div>
				<strong><?php echo  html::specialchars($comment->comment_author); ?></strong>&nbsp;(<?php echo date('Y/m/d', strtotime($comment->comment_date)); ?>)
			</div>
			
			<div><?php echo nl2br(preg_replace('/((https?|http)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+))/', '<a href="$1">$1</a>',html::specialchars($comment->comment_description))); ?></div>
	
	  <div class="credibility">  
	      <table class="rating-table" cellspacing="0" cellpadding="0" border="0">
	        <tr>
	          <td><?php echo Kohana::lang('ui_main.comment_rating');?>:</td>
	          <td><a href="javascript:rating('<?php echo $comment->id; ?>','add','comment','cloader_<?php echo $comment->id; ?>')"><img id="cup_<?php echo $comment->id; ?>" src="<?php echo url::base(); ?>media/img/up.png" alt="UP" title="UP" border="0" /></a></td>
	          <td><a href="javascript:rating('<?php echo $comment->id; ?>','subtract','comment','cloader_<?php echo $comment->id; ?>')"><img id="cdown_<?php echo $comment->id; ?>" src="<?php echo url::base(); ?>media/img/down.png" alt="DOWN" title="DOWN" border="0" /></a></td>
	          <td><div class="rating_value" id="crating_<?php echo $comment->id; ?>"><?php echo $comment->comment_rating; ?></div></td>
	          <td><div id="cloader_<?php echo $comment->id; ?>" class="rating_loading" ></div></td>
	        </tr>
	      </table>
	  </div>
	
		</div>
	<?php } ?>
	
</div>

<?php } ?>
