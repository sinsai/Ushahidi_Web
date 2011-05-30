<div id="content">
	<div class="content-bg">
		<!-- start contacts block -->
		<div class="big-block">
			<h2><?php echo Kohana::lang('ui_main.contact'); ?></h2>
			<div id="contact_us" class="contact">
				<?php
				if ($form_error)
				{
					?>
					<!-- red-box -->
					<div class="red-box">
						<?php /*<h3>Error!</h3>*/ ?>
						<ul>
							<?php
							foreach ($errors as $error_item => $error_description)
							{
								print (!$error_description) ? '' : "<li>" . $error_description . "</li>";
							}
							?>
						</ul>
					</div>
					<?php
				}

				if ($form_sent)
				{
					?>
					<!-- green-box -->
					<div class="green-box">
						<h3>Your Message Has Been Sent!</h3>
					</div>
					<?php
				}								
				?>
				<?php print form::open(NULL, array('id' => 'contactForm', 'name' => 'contactForm')); ?>
				<div class="report_row">
					<strong>お名前:</strong><span style="color:#990000">*</span><br />
					<?php print form::input('contact_name', $form['contact_name'], ' class="text"'); ?>
				</div>
				<div class="report_row">
					<strong>メールアドレス:</strong><span style="color:#990000">*</span><br />
					<?php print form::input('contact_email', $form['contact_email'], ' class="text"'); ?>
				</div>
				<div class="report_row">
					電話番号:<br />
					<?php print form::input('contact_phone', $form['contact_phone'], ' class="text"'); ?>
				</div>
				<div class="report_row">
					<strong>お問い合わせタイトル:</strong><span style="color:#990000">*</span><br />
					<?php print form::input('contact_subject', $form['contact_subject'], ' class="text_contact_subject"'); ?>
				</div>								
				<div class="report_row">
					<strong>お問い合わせ内容:</strong><span style="color:#990000">*</span><br />
					<?php print form::textarea('contact_message', $form['contact_message'], ' rows="4" cols="40" class="textarea long" ') ?>
				</div>		
				<div class="report_row">
					<strong>セキュリティコード:</strong><span style="color:#990000">*</span><br />
					<?php print $captcha->render(); ?><br />
					<?php print form::input('captcha', $form['captcha'], ' class="text"'); ?>
				</div>
				<div class="report_row">
					<span style="color:#990000">*</span>は必須項目です。
				</div>
				<div class="report_row">
					<input name="submit" type="submit" value="お問い合わせを送信する" class="btn_submit" />
				</div>
				<?php print form::close(); ?>
			</div>
			
		</div>
		<!-- end contacts block -->
	</div>
</div>
