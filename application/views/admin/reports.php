<?php
/**
 * Reports view page.
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

$query_filter_category = "";
$cats = array();
foreach($filter_categories as $item){
    $cats[] = "filter_category%5B$item%5D=$item";
}
$query_filter_category .= join($cats,"&");

?>
			<div class="bg">
				<h2>
					<?php admin::reports_subtabs("view"); ?>
				</h2>
				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<ul class="tabset">
						<li><a href="?status=0" <?php if ($status != 'a' && $status !='v' && $status != 'n' && $status != 'p' && $status != 'e' && $status != 'i') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.show_all');?></a></li>
						<li><a href="?status=a" <?php if ($status == 'a') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.awaiting_approval')."<br />(".$count_unapproved.")";?></a></li>
						<li><a href="?status=n" <?php if ($status == 'n') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.not_approval')."<br />(".$count_notapproved.")";?></a></li>
						<li><a href="?status=p" <?php if ($status == 'p') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.pending_approval')."<br />(".$count_pendingapproved.")";?></a></li>
						<li><a href="?status=e" <?php if ($status == 'e') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.escalation_approval')."<br />(".$count_escapproved.")"; ?></a></li>
						<li><a href="?status=v" <?php if ($status == 'v') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.awaiting_verification')."<br />(".$count_verificated.")";?></a></li>
						<li><a href="?status=i" <?php if ($status == 'i') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.verificated_nonactive')."<br />(".$count_verificated_nonactive.")";?></a></li>
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
					<!-- filter -->
					<div class="filter">
						<ul>
						<fieldset>
						<legend> Date. </legend>
							<div id="date_range">
							<form id="form_range" method="GET">
							<input type="hidden" name="via" value="<?php echo $via;?>" />
							<input type="hidden" name="order" value="<?php echo $order;?>" />
							<input type="hidden" name="status" value="<?php echo $status;?>" />
<?php foreach($filter_categories as $item){
print "<input type=\"hidden\" name=\"filter_category[$item]\" value=\"$item\">";
}?>
							<label for="from">From</label>
							<input size="12" type="text" id="from" name="from" value="<?php if(isset($from)) echo $from; ?>"/>
							<label for="to">to</label>
							<input size="12" type="text" id="to" name="to" value="<?php if(isset($to)) echo $to; ?>"/>
							<a href="#" id="down_range"><?php echo Kohana::lang('ui_admin.down_range');?></a>
							</form>
							</div>
						</fieldset>
						</ul>
						<ul>
						<fieldset>
						<legend> Order. </legend>
							<li><a <?php if($order == 0 ) echo "class=\"active\""; ?> href="?status=<?php echo $status;?>&order=0&via=<?php echo $via;?>&from=<?php echo $from;?>&to=<?php echo $to;?>&<?php echo $query_filter_category?>"><?php echo Kohana::lang('ui_admin.sort_desc');?></a></li>
							<li><a <?php if($order == 1 ) echo "class=\"active\""; ?> href="?status=<?php echo $status;?>&order=1&via=<?php echo $via;?>&from=<?php echo $from;?>&to=<?php echo $to;?>&<?php echo $query_filter_category?>"><?php echo Kohana::lang('ui_admin.sort_asc');?></a></li>
						</fieldset>
						</ul>
						<ul>
						<fieldset>
						<legend> Via. </legend>
							<li><a <?php if($via == 0 ) echo "class=\"active\""; ?> href="?status=<?php echo $status;?>&order=<?php echo $order;?>&from=<?php echo $from;?>&to=<?php echo $to;?>&<?php print $query_filter_category?>">All</a></li>
							<li><a <?php if($via == 1 ) echo "class=\"active\""; ?> href="?status=<?php echo $status;?>&order=<?php echo $order;?>&via=1&from=<?php echo $from;?>&to=<?php echo $to;?>&<?php print $query_filter_category?>">Web</a></li>
<!--							<li><a <?php if($via == 2 ) echo "class=\"active\""; ?> href="?status=<?php echo $status;?>&order=<?php echo $order;?>&via=2&from=<?php echo $from;?>&to=<?php echo $to;?>&<?php print $query_filter_category?>">sms</a></li> -->
							<li><a <?php if($via == 3 ) echo "class=\"active\""; ?> href="?status=<?php echo $status;?>&order=<?php echo $order;?>&via=3&from=<?php echo $from;?>&to=<?php echo $to;?>&<?php print $query_filter_category?>">Email</a></li>
							<li><a <?php if($via == 4 ) echo "class=\"active\""; ?> href="?status=<?php echo $status;?>&order=<?php echo $order;?>&via=4&from=<?php echo $from;?>&to=<?php echo $to;?>&<?php print $query_filter_category?>">Twitter</a></li>
					</fieldset>
						</ul>
                                       </div>
					<div calss="categoryFilter">
						<ul>
						<fieldset id="selectCategoryFields">
						<legend> Category </legend>
								<?php print $new_category_toggle_js; ?>
				<?php print form::open(NULL, array('id' => 'selectCategory', 'name' => 'selectCategory','method' => 'get')); ?>
							<?php echo category::tree($categories, $filter_categories, 'filter_category', 4); ?>
							<input type="submit" value="絞り込み">
                                                        <input type="hidden" name="status" value="<?php print $status; ?>">
                                                        <input type="hidden" name="order" value="<?php print $order; ?>">
                                                        <input type="hidden" name="from" value="<?php print $from; ?>">
                                                        <input type="hidden" name="to" value="<?php print $to; ?>">
                                                        <input type="hidden" name="via" value="<?php print $via; ?>">
				<?php print form::close(); ?>
						</fieldset>
						</ul>
					
					</div>
				</div>
				<?php
				if ($form_error) {
				?>
					<!-- red-box -->
					<div class="red-box">
						<h3><?php echo Kohana::lang('ui_main.error');?></h3>
						<ul><?php echo Kohana::lang('ui_main.select_one');?></ul>
					</div>
				<?php
				}

				if ($form_saved) {
				?>
					<!-- green-box -->
					<div class="green-box" id="submitStatus">
						<h3><?php echo Kohana::lang('ui_main.reports');?> <?php echo $form_action; ?> <a href="#" id="hideMessage" class="hide">hide this message</a></h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<?php print form::open(NULL, array('id' => 'reportMain', 'name' => 'reportMain')); ?>
					<input type="hidden" name="action" id="action" value="">
					<input type="hidden" name="incident_id[]" id="incident_single" value="">
					<div class="table-holder">
						<table class="table">
							<thead>
								<tr class="foot">
									<td colspan="4">
										<?php echo $pagination; ?>
									</td>
								</tr>
					<!-- tab -->
					<tr class="tab">
						<td colspan="4">
							<div class="tab">
								<ul>
									<li><a href="#" onclick="reportAction('u','<?php echo Kohana::lang('ui_main.disapprove');?>', '');"><?php echo Kohana::lang('ui_main.disapprove');?></a></li>
									<li><a href="#" onclick="reportAction('n','<?php echo Kohana::lang('ui_main.not_approval');?>', '');"><?php echo Kohana::lang('ui_main.not_approval');?></a></li>
									<li><a href="#" onclick="reportAction('p','<?php echo Kohana::lang('ui_main.pending_approval');?>', '');"><?php echo Kohana::lang('ui_main.pending_approval');?></a></li>
									<li><a href="#" onclick="reportAction('e','<?php echo Kohana::lang('ui_main.escalation_approval');?>', '');"><?php echo Kohana::lang('ui_main.escalation_approval');?></a></li>
									<li><a href="#" onclick="reportAction('a','<?php echo Kohana::lang('ui_main.approve');?>', '');"><?php echo Kohana::lang('ui_main.approve');?></a></li>
									<li><a href="#" onclick="reportAction('v','<?php echo Kohana::lang('ui_main.verify');?>', '');"><?php echo Kohana::lang('ui_main.verify');?></a></li>
									<li><a href="#" onclick="reportAction('d','<?php echo Kohana::lang('ui_main.delete');?>', '');"><?php echo Kohana::lang('ui_main.delete');?></a></li>
								</ul>
							</div>
						</td>
					</tr>
								<tr>
									<th class="col-1"><?php echo Kohana::lang('ui_main.report_details');?></th>
									<th class="col-2"><?php echo Kohana::lang('ui_main.date');?></th>
									<th class="col-3"><?php echo Kohana::lang('ui_main.statuses');?></th>
									<th class="col-4"><input id="checkallincidents" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'incident_id[]' )" /></th>
								</tr>
							</thead>
							<tfoot>
					<tr class="tab">
						<td colspan="4">
							<div class="tab">
								<ul>
									<li><a href="#" onclick="reportAction('u','<?php echo Kohana::lang('ui_main.disapprove');?>', '');"><?php echo Kohana::lang('ui_main.disapprove');?></a></li>
									<li><a href="#" onclick="reportAction('n','<?php echo Kohana::lang('ui_main.not_approval');?>', '');"><?php echo Kohana::lang('ui_main.not_approval');?></a></li>
									<li><a href="#" onclick="reportAction('p','<?php echo Kohana::lang('ui_main.pending_approval');?>', '');"><?php echo Kohana::lang('ui_main.pending_approval');?></a></li>
									<li><a href="#" onclick="reportAction('e','<?php echo Kohana::lang('ui_main.escalation_approval');?>', '');"><?php echo Kohana::lang('ui_main.escalation_approval');?></a></li>
									<li><a href="#" onclick="reportAction('a','<?php echo Kohana::lang('ui_main.approve');?>', '');"><?php echo Kohana::lang('ui_main.approve');?></a></li>
									<li><a href="#" onclick="reportAction('v','<?php echo Kohana::lang('ui_main.verify');?>', '');"><?php echo Kohana::lang('ui_main.verify');?></a></li>
									<li><a href="#" onclick="reportAction('d','<?php echo Kohana::lang('ui_main.delete');?>', '');"><?php echo Kohana::lang('ui_main.delete');?></a></li>
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
								foreach ($incidents as $incident)
								{
									$incident_id = $incident->id;
									$incident_title = $incident->incident_title;
									$incident_description = text::limit_chars($incident->incident_description, 150, "...", true);
									$incident_date = $incident->incident_date;
									$incident_date = date('Y/m/d H:i:s', strtotime($incident->incident_date));
									$incident_mode = $incident->incident_mode;	// Mode of submission... WEB/SMS/EMAIL?

									//XXX incident_Mode will be discontinued in favour of $service_id
									$submit_by = "";
									if ($incident_mode == 1)	// Submitted via WEB
									{
										$submit_mode = "WEB";
										// Who submitted the report?
										if ($incident->incident_person->id)
										{
											// Report was submitted by a visitor
											if (Kohana::lang('ui_main.reports_name_order') == '1') { 
												$submit_by = $incident_persons[$incident->id]['person_last'] . " " . $incident_persons[$incident->id]['person_first'];
											} else {
												$submit_by = $incident_persons[$incident->id]['person_first'] . " " . $incident_persons[$incident->id]['person_last'];
											}
										}
										else
										{
											if ($incident->user_id)					// Report Was Submitted By Administrator
											{
												$submit_by = $incident->user->name;
											}
											else
											{
												$submit_by = 'Unknown';
											}
										}
									}
									elseif ($incident_mode == 2) 	// Submitted via SMS
									{
										$submit_mode = "SMS";
										if(isset($incident_messages[$incident->id]['message_from'])){
											$submit_by =$incident_messages[$incident->id]['message_from'];
										}
									}
									elseif ($incident_mode == 3) 	// Submitted via Email
									{
										$submit_mode = "EMAIL";
										if(isset($incident_messages[$incident->id]['message_from'])){
											$submit_by =$incident_messages[$incident->id]['message_from'];
										}
									}
									elseif ($incident_mode == 4) 	// Submitted via Twitter
									{
										$submit_mode = "TWITTER";
										if(isset($incident_messages[$incident->id]['message_from'])){
											$submit_by =$incident_messages[$incident->id]['message_from'];
										}
									}
									elseif ($incident_mode == 5) 	// Submitted via Laconica
									{
										$submit_mode = "LACONICA";
										if(isset($incident_messages[$incident->id]['message_from'])){
											$submit_by =$incident_messages[$incident->id]['message_from'];
										}
									}
									$tasukeai = false;
									foreach ($incident->media as $media)
                                    {
                                        $link = $media->media_link;
                                        if(strpos($link, "http://tasukeai.heroku.com/messages/show/", 0) === 0){
                                            $tasukeai = true;
                                        }
                                    }
									$incident_location = $locations[$incident->location_id];

									// Retrieve Incident Categories
									$incident_category = "";
									foreach($incident_incident_categories[$incident->id] as $category)
									{
										$incident_category .=  $category . "&nbsp;&nbsp;";
									}

									// Incident Status
									$incident_approved = $incident->incident_active;
									$incident_verified = $incident->incident_verified;
                                    if( $incident_approved == 0 ) {
                                        $status_incident = Kohana::lang('ui_main.disapprove');
                                    } elseif ( $incident_approved == 1 ) {
                                        $status_incident = Kohana::lang('ui_main.approved');
                                    } elseif ( $incident_approved == 2 ) {
                                        $status_incident = Kohana::lang('ui_main.not_approval');
                                    } elseif ( $incident_approved == 3 ) {
                                        $status_incident = Kohana::lang('ui_main.pending_approval');
                                    } elseif ( $incident_approved == 4 ) {
                                        $status_incident = Kohana::lang('ui_main.escalation_approval');
                                    }
                                    if( $incident_verified == 0 ) {
                                        $status_verified = Kohana::lang('ui_main.unverified');
                                    } elseif ( $incident_verified == 1 ) {
                                        $status_verified = Kohana::lang('ui_main.verified');
                                    }
									// Get Edit Log
									$edit_count = $incident->verify->count();
									$edit_css = ($edit_count == 0) ? "post-edit-log-red" : "post-edit-log-gray";
									$edit_log  = "<div class=\"".$edit_css."\">";
									$edit_log .= "<a href=\"javascript:showLog('edit_log_".$incident_id."')\">".Kohana::lang('ui_admin.edit_log').":</a> (".$edit_count.")</div>";
									$edit_log .= "<div id=\"edit_log_".$incident_id."\" class=\"post-edit-log\"><ul>";
									foreach ($incident->verify as $verify)
									{
										$edit_log .= "<li>".Kohana::lang('ui_admin.edited_by')." ".$verify->user->name." : ".$verify->verified_date."</li>";
									}
									$edit_log .= "</ul></div>";

									// Get Any Translations
									$i = 1;
									$incident_translation  = "<div class=\"post-trans-new\">";
									$incident_translation .= "<a href=\"" . url::base() . 'admin/reports/translate/?iid=' . $incident_id . "\">".strtoupper(Kohana::lang('ui_main.add_translation')).":</a></div>";
									if(isset($incident_incident_langs[$incident->id])){
										foreach ($incident_incident_langs[$incident->id] as $translation) {
											$incident_translation .= "<div class=\"post-trans\">";
											$incident_translation .= Kohana::lang('ui_main.translation'). $i . ": ";
											$incident_translation .= "<a href=\"" . url::base() . 'admin/reports/translate/'. $translation['id'] .'/?iid=' . $incident_id . "\">"
												. text::limit_chars($translation->incident_title, 150, "...", true)
												. "</a>";
											$incident_translation .= "</div>";
										}
									}
										?>
									<tr <?php
									if($tasukeai){
									    echo "style='background-color:#cccccc;'";
									}
									
									?>>
										<td class="col-1">
											<div class="post">
												<h4><a href="<?php echo url::site() . 'admin/reports/edit/' . $incident_id; ?>" class="more"><?php echo $incident_title; ?></a></h4>
												<p><?php echo $incident_description; ?>... <a href="<?php echo url::base() . 'admin/reports/edit/' . $incident_id; ?>" class="more"><?php echo Kohana::lang('ui_main.more');?></a></p>
											</div>
											<ul class="info">
												<li class="none-separator"><?php echo Kohana::lang('ui_main.location');?>: <strong><?php echo $incident_location; ?></strong>, <strong><?php echo $countries[Kohana::config('settings.default_country')]; ?></strong></li>
												<li><?php echo Kohana::lang('ui_main.submitted_by');?> <strong><?php echo $submit_by; ?></strong> via <strong><?php echo $submit_mode; ?></strong><?php
												if($tasukeai){
												    echo "<font color='red'>[Tasukeai Japan]</font>";
												}
												
												?></li>
											</ul>
											<ul class="links">
												<li class="none-separator"><?php echo Kohana::lang('ui_main.categories');?>:<?php echo $incident_category; ?></li>
											</ul>
											<?php
											echo $edit_log;
											
											// Action::report_extra_admin - Add items to the report list in admin
											Event::run('ushahidi_action.report_extra_admin', $incident);
											?>
										</td>
										<td class="col-2"><?php echo "<p>".$incident_date."</p>";?>
</td>
										<td class="col-3">
											<ul>
<?php
echo "<li class=\"none-separator";
if( $incident_approved == 1 ) echo " status_yes";
elseif( $incident_approved != 0 ) echo " status_no";
echo "\">".$status_incident."</li><li class=\"none-separator";
if( $incident_verified == 1 ) echo " status_yes";
echo "\">".$status_verified."</li></ul>"; ?>
											</ul>
										</td>
										<td class="col-4"><input name="incident_id[]" id="incident" value="<?php echo $incident_id; ?>" type="checkbox" class="check-box"/></td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					</div>
				<?php print form::close(); ?>
			</div>
      <!-- for LDRize -->
      <style>
      tr.selected td {
        background-color:#D5E7FF;
      }
      </style>
  		<?php
	      echo html::script('media/js/ldrize', true);
  		?>
