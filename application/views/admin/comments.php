<?php 
/**
 * Comments view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
			<div class="bg">
				<h2>
					<?php admin::reports_subtabs("comments"); ?>
				</h2>
				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<ul class="tabset">
						<li><a href="?status=0" <?php if ($status != 'a' && $status !='p' && $status !='s') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.show_all');?></a></li>
						<li><a href="?status=p" <?php if ($status == 'p') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.pending');?></a></li>
						<li><a href="?status=a" <?php if ($status == 'a') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.approved');?></a></li>
						<li><a href="?status=s" <?php if ($status == 's') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.spam');?></a></li>
					</ul>
					<script type="text/javascript"> 
					$(function() {
						$('#down_range').click(function() {
							$('#form_range').submit();
						});
					});
					$(function() {
						var dates = $( "#from, #to" ).datepicker({
							defaultDate: "-3d",
							dateFormat: 'yy/mm/dd',
							changeMonth: true,
							numberOfMonths: 2,
							gotoCurrent: true,
							onSelect: function( selectedDate ) {
								var option = this.id == "from" ? "minDate" : "maxDate",
									instance = $( this ).data( "datepicker" ),
									date = $.datepicker.parseDate(
										instance.settings.dateFormat ||
										$.datepicker._defaults.dateFormat,
										selectedDate, instance.settings );
								dates.not( this ).datepicker( "option", option, date );
							}
						});
					});
					</script>
					<div class="filter">
						<ul>
						<fieldset>
							<legend> Date. </legend>
							<div id="date_range">
							<form id="form_range" method="GET">
							<input type="hidden" name="order" value="<?php echo $order;?>" />
<?php if (Kohana::config('locale.language.0') === 'ja_JP') { ?>
							<input size="12" type="text" id="from" name="from" value="<?php if(isset($from)) echo $from; ?>"/>
							<label for="from"><?php echo Kohana::lang('ui_admin.from') ?></label>
							<input size="12" type="text" id="to" name="to" value="<?php if(isset($to)) echo $to; ?>"/>
							<label for="to"><?php echo Kohana::lang('ui_admin.to') ?></label>
<?php } else { ?>
							<label for="from"><?php echo Kohana::lang('ui_admin.from') ?></label>
							<input size="12" type="text" id="from" name="from" value="<?php if(isset($from)) echo $from; ?>"/>
							<label for="to"><?php echo Kohana::lang('ui_admin.to') ?></label>
							<input size="12" type="text" id="to" name="to" value="<?php if(isset($to)) echo $to; ?>"/>
<?php } ?>
							<a href="#" id="down_range"><?php echo Kohana::lang('ui_admin.down_range');?></a>
							</form>
							</div>
						</fieldset>
						</ul>
						<ul>
						<fieldset>
							<legend> Order. </legend>
								<li><a <?php if($order == 0 ) echo "class=\"active\""; ?> href="?status=<?php echo $status;?>&order=0&from=<?php echo $from;?>&to=<?php echo $to;?>"><?php echo Kohana::lang('ui_admin.sort_desc');?></a></li>
								<li><a <?php if($order == 1 ) echo "class=\"active\""; ?> href="?status=<?php echo $status;?>&order=1&from=<?php echo $from;?>&to=<?php echo $to;?>"><?php echo Kohana::lang('ui_admin.sort_asc');?></a></li>
						</fieldset>
						</ul>
					</div>
				</div>
				<?php
				if ($form_error)
				{
				?>
					<!-- red-box -->
					<div class="red-box">
						<h3><?php echo Kohana::lang('ui_main.error');?></h3>
						<ul><?php echo Kohana::lang('ui_main.select_one');?></ul>
					</div>
				<?php
				}

				if ($form_saved)
				{
				?>
					<!-- green-box -->
					<div class="green-box" id="submitStatus">
						<h3><?php echo Kohana::lang('ui_admin.comments'); ?> <?php echo $form_action; ?> <a href="#" id="hideMessage" class="hide"><?php echo Kohana::lang('ui_main.hide_this_message');?></a></h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<?php print form::open(NULL, array('id' => 'commentMain', 'name' => 'commentMain')); ?>
					<input type="hidden" name="action" id="action" value="">
					<input type="hidden" name="comment_id[]" id="comment_single" value="">
					<div class="table-holder">
						<table class="table">
							<thead>
								<tr class="foot">
									<td colspan="4">
										<?php echo $pagination; ?>
									</td>
								</tr>
					<tr class="tab">
						<td colspan="4">
							<div class="tab">
						<ul>
							<li><a href="#" onclick="commentAction('a','APPROVE', '');"><?php echo strtoupper(Kohana::lang('ui_main.approve'));?></a></li>
							<li><a href="#" onclick="commentAction('u','UNAPPROVE', '');"><?php echo strtoupper(Kohana::lang('ui_main.disapprove'));?></a></li>
							<li><a href="#" onclick="commentAction('s','MARK AS SPAM', '');"><?php echo strtoupper(Kohana::lang('ui_main.spam'));?></a></li>
							<li><a href="#" onclick="commentAction('n','MARK AS NOT SPAM', '');"><?php echo strtoupper(Kohana::lang('ui_main.not_spam'));?></a></li>
							<li><a href="#" onclick="commentAction('d','DELETE', '');"><?php echo strtoupper(Kohana::lang('ui_main.delete'));?></a></li>
							<?php 
							if ($status == 's')
							{
								?>
								<li><a href="#" onclick="commentAction('x','DELETE ALL SPAM', '000');"><?php echo strtoupper(Kohana::lang('ui_main.delete_spam'));?></a></li>
								<?php
							}
							?>
						</ul>
							</div>
						</td>
					</tr>
								<tr>
									<th class="col-1"><?php echo Kohana::lang('ui_main.comment_details');?></th>
									<th class="col-2"><?php echo Kohana::lang('ui_main.date');?></th>
									<th class="col-3"><?php echo Kohana::lang('ui_main.actions');?></th>
									<th class="col-4"><input id="checkallcomments" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'comment_id[]' )" /></th>
								</tr>
							</thead>
							<tfoot>
					<tr class="tab">
						<td colspan="4">
							<div class="tab">
						<ul>
							<li><a href="#" onclick="commentAction('a','APPROVE', '');"><?php echo strtoupper(Kohana::lang('ui_main.approve'));?></a></li>
							<li><a href="#" onclick="commentAction('u','UNAPPROVE', '');"><?php echo strtoupper(Kohana::lang('ui_main.disapprove'));?></a></li>
							<li><a href="#" onclick="commentAction('s','MARK AS SPAM', '');"><?php echo strtoupper(Kohana::lang('ui_main.spam'));?></a></li>
							<li><a href="#" onclick="commentAction('n','MARK AS NOT SPAM', '');"><?php echo strtoupper(Kohana::lang('ui_main.not_spam'));?></a></li>
							<li><a href="#" onclick="commentAction('d','DELETE', '');"><?php echo strtoupper(Kohana::lang('ui_main.delete'));?></a></li>
							<?php 
							if ($status == 's')
							{
								?>
								<li><a href="#" onclick="commentAction('x','DELETE ALL SPAM', '000');"><?php echo strtoupper(Kohana::lang('ui_main.delete_spam'));?></a></li>
								<?php
							}
							?>
						</ul>
							</div>
						</td>
					</tr>
								<tr class="foot">
									<td colspan="4">
										<?php echo $pagination; ?>
									</td>
								</tr>
							</tfoot>
							<tbody>
								<?php
								if ($total_items == 0)
								{
								?>
									<tr>
										<td colspan="4" class="col">
											<h3><?php echo Kohana::lang('ui_main.no_results');?></h3>
										</td>
									</tr>
								<?php	
								}
								foreach ($comments as $comment)
								{
									$comment_id = $comment->id;
									$comment_author = $comment->comment_author;
									$comment_description = $comment->comment_description;
									$comment_email = $comment->comment_email;
									$comment_ip = $comment->comment_ip;
									$comment_active = $comment->comment_active;
									$comment_spam = $comment->comment_spam;
									$comment_rating = $comment->comment_rating;
									$comment_date = date('Y-m-d', strtotime($comment->comment_date));
									
									$incident_id = $comment->incident->id;
									$incident_title = $comment->incident->incident_title;
									?>
									<tr>
										<td class="col-1">
											<div class="post">
												<h4><?php echo $comment_author; ?></h4>
												<?php
												if ($incident_title != "")
												{
													?><div class="comment_incident"><?php echo Kohana::lang('ui_main.in_response_to');?>: <strong><a href="<?php echo url::base() . 'admin/reports/edit/' . $incident_id; ?>"><?php echo $incident_title; ?></a></strong></div><?php
												}
												?>
												<p><?php echo $comment_description; ?></p>
											</div>
											<ul class="info">
												<li class="none-separator"><?php echo Kohana::lang('ui_main.email');?>: <strong><?php echo $comment_email; ?></strong></li>
												<li><?php echo Kohana::lang('ui_main.ip_address');?>: <strong><?php echo $comment_ip; ?></strong></li>
												<li><?php echo Kohana::lang('ui_main.comment_rating');?>: <strong><?php echo $comment_rating; ?></strong></li>
											</ul>
										</td>
										<td class="col-2"><?php echo $comment_date; ?></td>
										<td class="col-3">
											<ul>
												<li class="none-separator"><?php
												if ($comment_active)
												{
													?><a href="#" class="status_yes" onclick="commentAction('u','UNAPPROVE', '<?php echo $comment_id; ?>');"><?php echo Kohana::lang('ui_main.approved');?></a><?php
												}
												else
												{
													?><a href="#" class="status_no" onclick="commentAction('a','APPROVE', '<?php echo $comment_id; ?>');"><?php echo Kohana::lang('ui_main.approve');?></a><?php
												}
												?></li>
												<li><?php
												if ($comment_spam)
												{
													?><a href="#" class="status_yes" onclick="commentAction('n','MARK AS NOT SPAM', '<?php echo $comment_id; ?>');"><?php echo Kohana::lang('ui_main.not_spam');?></a><?php
												}
												else
												{
													?><a href="#" class="status_no" onclick="commentAction('s','MARK AS SPAM', '<?php echo $comment_id; ?>');"><?php echo Kohana::lang('ui_main.spam');?></a><?php
												}
												?></li>
												<li><a href="#" class="del" onclick="commentAction('d','DELETE', '<?php echo $comment_id; ?>');"><?php echo Kohana::lang('ui_main.delete');?></a></li>
											</ul>
										</td>
										<td class="col-4"><input name="comment_id[]" id="comment" value="<?php echo $comment_id; ?>" type="checkbox" class="check-box"/></td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					</div>
				<?php print form::close(); ?>
			</div>
