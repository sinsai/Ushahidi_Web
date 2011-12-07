<?php 
/**
 * Messages view page.
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
<script type="text/javascript" src="http://plugins.jquery.com/files/jquery.query-2.1.7.js.txt"></script>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
			<div class="bg">
				<h2>
					<?php admin::messages_subtabs($service_id); ?>
				</h2>

<?php
	Event::run('ushahidi_action.admin_messages_custom_layout');
	// Kill the rest of the page if this event has been utilized by a plugin
	if( ! Event::has_run('ushahidi_action.admin_messages_custom_layout')){
?>

				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<!-- tab -->
					<script type="text/javascript"> 
					$(function() {
						$('#down_range').click(function() {
							$('#form_range').submit();
						});
					});
					$(function() {
						var dates = $( "#datepick" ).datepicker({
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
								<label for="datepick">Pick</label>
								<input size="12" type="text" id="datepick" name="datepick" value="<?php if(isset($datepick)) echo $datepick; ?>"/>
                                <input type="hidden" name="feed_id" value="<?php echo $sel_feeds ?>">
								<a href="#" id="down_range"><?php echo Kohana::lang('ui_admin.down_range');?></a>
								</form>
								</div>
						</fieldset>
						</ul>
                        <ul>
					<fieldset>
						<legend> Feeds. </legend>
              <li>
                <form action="<?php echo url::site()."admin/messages/index/".$service_id ?>" method="GET"> 
                  <span style="padding-top: 0px; padding-bottom: 1px;" class="formspan">
                    <input type="hidden" name="datepick" value="<?php echo $datepick ?>">
                    購読中：<select name="feed_id"><option value="">ALL</option><?php
                foreach ($feeds as $item)
                {
                    if( $item->id == $sel_feeds )
                    {
                        echo "<option value=\"".$item->id."\" selected>".$item->feed_name."</option>";
                    } else {
                        echo "<option value=\"".$item->id."\">".$item->feed_name."</option>";
                    }
                }
?></select>
                    <input type="submit" value="<?php echo Kohana::lang('ui_admin.submitting');?>"/>
                  </span>
                </form>
              </li>
					</fieldset>
                        </ul>
						<ul>
					<fieldset>
						<legend> JumpPage. </legend>
              <li>
                <form action="<?php echo url::site()."admin/messages/index/".$service_id ?>" method="GET"> 
                    <input type="hidden" name="datepick" value="<?php echo $datepick ?>">
                    <input type="hidden" name="feed_id" value="<?php echo $sel_feeds ?>">
                   No.<input type="text" name="page" value="" size="3">
                    <input type="submit" value="<?php echo Kohana::lang('ui_admin.submitting');?>"/>
                </form>
              </li>
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
						<h3><?php echo Kohana::lang('ui_main.messages');?> <?php echo $form_action; ?> <a href="#" id="hideMessage" class="hide">hide this message</a></h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<?php print form::open(NULL, array('id' => 'messageMain', 'name' => 'messageMain')); ?>
					<input type="hidden" name="action" id="action" value="">
					<input type="hidden" name="message_id[]" id="message_single" value="">
					<div class="table-holder">
						<table class="table">
							<thead>
								<tr class="foot">
									<td colspan="4">
										<?php echo $pagination; ?>
									</td>
								</tr>
								<tr>
									<th class="col-1"><?php echo Kohana::lang('ui_main.message_details');?></th>
									<th class="col-2"><?php echo Kohana::lang('ui_main.date');?></th>
									<th class="col-3"><?php echo Kohana::lang('ui_main.actions');?></th>
									<th class="col-4"><input id="checkall" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'item_id[]' )" /></th>
								</tr>
							</thead>
							<tfoot>
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

                                foreach ($feed_items as $item)
                                {
                                    $item_id = $item->id;
                                    $item_title = $item->item_title;
                                    $item_description = $item->item_description;
                                    $item_link = $item->item_link;
                                    $item_date = date('Y-m-d', strtotime($item->item_date));
                                    $item_time = date('H:i', strtotime($item->item_date));
                                                                                                                    $feed_name = $item->feed->feed_name;

                                    $location_id = $item->location_id;
                                    $incident_id = $item->incident_id;
									?>
									<tr>
                                        <td class="col-1"><div class="post">
                                            <p><?php echo $item_title; ?></p>
                                            <?php
                                            if ($item_description)
                                            { ?>
                                                <p><a href="javascript:preview('feed_preview_<?php echo $item_id?>')"><?php echo Kohana::lang('ui_main.preview_item');?></a></p>
                                                <div id="feed_preview_<?php echo $item_id?>" style="display:none;"><?php echo $item_description; ?>
                                                </div><?php
                                            }
												// Action::message_extra_admin  - Message Additional/Extra Stuff
												Event::run('ushahidi_action.message_extra_admin', $item_id);
												?>
											</div>
                                             <ul class="info">
                                                <li class="none-separator"><?php echo Kohana::lang('ui_main.feed');?>: <strong><a href="<?php echo $item_link; ?>"><?php echo $feed_name; ?></a></strong>
                                                <!--li><?php echo Kohana::lang('ui_main.geolocation_available');?>?: <strong><?php echo ($location_id) ? strtoupper(Kohana::lang('ui_main.yes')) : strtoupper(Kohana::lang('ui_main.no'));?></strong></li-->
                                            </ul>
										</td>
										<td class="col-2"><?php echo $item_date."<br />".$item_time; ?></td>
										<td class="col-3">
											<ul>
												<?php
												if ($incident_id != 0) {
													echo "<li class=\"none-separator\"><a target=\"edit\" href=\"". url::base() . 'admin/reports/edit/' . $incident_id ."\" class=\"status_yes\"><strong>作成済み</strong></a></li>";
												}
												else
												{
													echo "<li class=\"none-separator\"><a target=\"edit\"   href=\"". url::base() . 'admin/reports/edit?fid=' . $item_id ."\">".Kohana::lang('ui_admin.create_report')."</a></li>";
												}
												?>
												<li><a href="javascript:messagesAction('d','DELETE','<?php echo(rawurlencode($item_id)); ?>')" class="del"><?php echo Kohana::lang('ui_main.delete');?></a></li>
											</ul>
										</td>
<td class="col-4"><input name="item_id[]" id="message" value="<?php echo $item_id; ?>" type="checkbox" class="check-box"/></td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					</div>
				<?php print form::close(); ?>
			</div>

<?php
	}
?>
