			</div>
		</div>
		<!-- / main body -->

	</div>
	<!-- / wrapper -->

	<!-- footer -->
	<div id="footer" class="clearingfix">

		<div id="underfooter"></div>

		<!-- footer content -->
	<div class="rapidxwpr floatholder">

			<!-- footer credits -->
			<div class="footer-credits">
				the &nbsp; <a href="http://www.ushahidi.com/"><img src="<?php echo url::base(); ?>/media/img/footer-logo.png" alt="Ushahidi" style="vertical-align:middle" /></a>&nbsp; Platform
			</div>
			<!-- / footer credits -->

			<!-- footer menu -->
			<div class="footermenu">
				<ul class="clearingfix">
					<li><a class="item1" href="<?php echo url::site();?>"><?php echo Kohana::lang('ui_main.home'); ?></a></li>
					<li><a href="<?php echo url::site()."page/index/9"; ?>"><?php echo Kohana::lang('ui_main.about_us'); ?></a></li>
					<li><a href="<?php echo url::site()."contact"; ?>"><?php echo Kohana::lang('ui_main.contact'); ?></a></li>
					<li><a href="https://spreadsheets.google.com/viewform?formkey=dGlnajlENUtFOFZnWlN2XzlqbklickE6MQ"><?php echo Kohana::lang('ui_main.feedback'); ?></a></li>
                    <li><a href="<?php echo url::site()."page/index/10"; ?>"><?php echo Kohana::Lang('ui_main.terms');?></a></li>
                    <li><a href="<?php echo url::site()."page/index/11"; ?>"><?php echo Kohana::Lang('ui_main.privacypolicy');?></a></li>
					<?php
					// Action::nav_main_bottom - Add items to the bottom links
					Event::run('ushahidi_action.nav_main_bottom');
					?>
				</ul>
				<?php if($site_copyright_statement != '') { ?>
      	<?php } ?>
			</div>
			<!-- / footer menu -->

			<!-- footer credits links -->
			<div class="footer-credits-links">
				<div class="footer-credit">
					Powered by
				</div>
				<ul>
					<li>
						<a href="http://aws.amazon.com/" target="_blank">
							<img src="<?php echo url::base(); ?>/media/img/Powered-by-Amazon-Web-Services.jpeg" alt="Amazon-Web-Services" style="vertical-align:middle" />
						</a>
					</li>
					<li>
						<a href="http://heartbeats.jp/" target="_blank">
							<img src="<?php echo url::base(); ?>/media/img/powered-by-heartbeats.gif" alt="heartbeats" style="vertical-align:middle" />
						</a>
					</li>
					<li>
						<a href="http://www.gree.co.jp/" target="_blank">
							<img src="<?php echo url::base(); ?>/media/img/powered-by-gree.gif" alt="gree" style="vertical-align:middle" />
						</a>
					</li>
					<li>
						<a href="http://www.nttdata.co.jp/" target="_blank">
							<img src="<?php echo url::base(); ?>/media/img/nttdata-gi.gif" alt="nttdata" style="vertical-align:middle" />
						</a>
					</li>
					<li>
						<a href="http://www.yahoo.co.jp/" target="_blank">
							<img src="<?php echo url::base(); ?>/media/img/logo_yahoo.gif" alt="yahoo" style="vertical-align:middle" />
						</a>
					</li>
					<li>
						<a href="http://www.nifty.co.jp/english/" target="_blank">
							<img src="<?php echo url::base(); ?>/media/img/nifty_yoko.png" alt="nifty" style="vertical-align:middle" />
						</a>
					</li>
				</ul>
			</div>
      		<div style="text-align:center;margin-top:10px;"><p class="copyright">Copyright&nbsp;&copy;&nbsp;sinsai.info&nbsp;All&nbsp;Rights&nbsp;Reserved.&nbsp;<?php echo $site_copyright_statement; ?></p></div>
			<!-- / footer credits links -->

<?php /*
			<h2 class="feedback_title" style="clear:both">
				<a href="https://spreadsheets.google.com/viewform?formkey=dGlnajlENUtFOFZnWlN2XzlqbklickE6MQ"><?php echo Kohana::lang('ui_main.feedback'); ?></a>
			</h2>
*/?>

		</div>
		<!-- / footer content -->

	</div>
	<!-- / footer -->

	<?php /*echo $ushahidi_stats;*/ ?>
	<?php echo $google_analytics; ?>
	<?php
	// Action::main_footer - Add items before the </body> tag
	Event::run('ushahidi_action.main_footer');
	?>
</body>
</html>
