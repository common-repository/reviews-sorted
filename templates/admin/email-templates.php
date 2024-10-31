<style type="text/css">
	.rs-custom-accordion-wrapper{
		box-shadow: 0 3px 10px rgb(0 0 0 / 3%);
    	margin-bottom: 15px;
    	max-width: 850px;
	}
	.rs-custom-accordion-wrapper .accordion {
	  width: 100%;
	  background-color: #2271b1;
	  border: none;
	  outline: none;
	  text-align: left;
	  padding: 15px 20px;
	  font-size: 18px;
	  color: #333;
	  cursor: pointer;
	  transition: background-color 0.2s linear;
	}

	.rs-custom-accordion-wrapper .accordion:hover,
	.rs-custom-accordion-wrapper .accordion.is-open {
	  background-color: #ddd;
	}

	.rs-custom-accordion-wrapper .accordion-content {
	  background-color: white;
	  border-left: 1px solid whitesmoke;
	  border-right: 1px solid whitesmoke;
	  padding: 0 20px;
	  max-height: 0;
	  overflow: hidden;
	  transition: max-height 0.2s ease-in-out;
	  width: 100%;
	}
	.rs-custom-accordion-wrapper input,.rs-custom-accordion-wrapper textarea{
		width: 100%;
	}

</style>
<div class="wrap">	
	<h1 class="wp-heading-inline"><?php _e('Email Notifications', 'reviews-sorted'); ?></h1>

	<hr class="wp-header-end">

	<form action="<?php echo esc_url(admin_url( 'admin-post.php' )); ?>" method="post">
		
		<h2><?php _e('Email sender options', 'reviews-sorted'); ?></h2>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="rs-form_from_name"><?php _e('"From" name', 'reviews-sorted'); ?></label></th>
					<td>
					  	<input type="text" class="regular-text" 
					  		id="rs-form_from_name" 
					  		name="reviews_sorted_settings[email_sender_name]" 
					  		placeholder="<?php _e('"From" name', 'reviews-sorted'); ?>*" 
					  		value="<?php echo esc_attr($settings['email_sender_name']); ?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="rs-form_from_address"><?php _e('"From" address', 'reviews-sorted'); ?></label></th>
					<td>
					  	<input type="email" class="regular-text" 
					  		id="rs-form_from_address" 
					  		name="reviews_sorted_settings[email_sender_address]" 
					  		placeholder="<?php _e('"From" address', 'reviews-sorted'); ?>*" 
					  		value="<?php echo esc_attr($settings['email_sender_address']); ?>">
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="rs-form_from_address"><?php _e('Notification Emails', 'reviews-sorted'); ?></label></th>
					<td>
					  	<textarea class="large-text" rows="10" 
					  		id="rs-form_from_address" 
					  		name="reviews_sorted_settings[admin_emails]"><?php echo esc_attr($settings['admin_emails']); ?></textarea>
					</td>
				</tr>
			</tbody>
		</table>

		<h2>Email template</h2>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="rs-form_header_image"><?php _e('Header image', 'reviews-sorted'); ?></label></th>
					<td>
					  	<input type="url" class="regular-text" 
					  		id="rs-form_header_image" 
					  		name="reviews_sorted_settings[email_header_image]" 
					  		placeholder="N/A" 
					  		value="<?php echo esc_attr($settings['email_header_image']); ?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="rs-form_footer_text"><?php _e('Footer text', 'reviews-sorted'); ?></label></th>
					<td>
					    <textarea id="rs-form_footer_text" class="regular-text" 
					    	name="reviews_sorted_settings[email_footer_text]" 
					    	rows="4"><?php echo esc_attr($settings['email_footer_text']); ?></textarea>
					</td>
				</tr>
			</tbody>
		</table>

		<h2>Email notifications</h2>
		<?php
			$templates = ReviewsSortedCommon::email_notifications();
			
			foreach($templates as $index => $template){
				?>
				<div class="rs-custom-accordion-wrapper">
				 	<div class="accordion">
			 			<?php echo $template['title']; ?>
		 			</div>
				  	<div class="accordion-content">
				  		<table class="form-table">
							<tbody>
								<tr>
									<td><?php _e('Active', 'reviews-sorted'); ?></td>
									<td>
										<select name="reviews_sorted_email[<?php echo $index; ?>][active]">
											<option value="yes" <?php selected( esc_attr($template['active']), 'yes'); ?>><?php _e('Yes', 'reviews-sorted'); ?></option>
											<option value="no" <?php selected( esc_attr($template['active']), 'no'); ?>><?php _e('No', 'reviews-sorted'); ?></option>
										</select>
									</td>
								</tr>
								<tr>
									<td><?php _e('Subject', 'reviews-sorted'); ?></td>
									<td><input type="text" class="regular-text" 
										name="reviews_sorted_email[<?php echo $index; ?>][subject]" 
										value="<?php echo esc_attr($template['subject']); ?>">
									</td>
								</tr>
								<tr>
									<td><?php _e('Body', 'reviews-sorted'); ?></td>
									<td><textarea class="regular-text"
										name="reviews_sorted_email[<?php echo $index; ?>][body]" 
										rows="14"><?php echo esc_attr($template['body']); ?></textarea>
									</td>
								</tr>
							</tbody>
						</table>
					    	
					    <input type="hidden" name="reviews_sorted_email[<?php echo $index; ?>][title]" value="<?php echo esc_attr($template['title']); ?>">	
					 </div>
				</div>
				<?php
			}
		?>
		
		<script>
			const accordionBtns = document.querySelectorAll(".accordion");
				accordionBtns.forEach((accordion) => {
			  	accordion.onclick = function () {
				    this.classList.toggle("is-open");
				    let content = this.nextElementSibling;

				    if (content.style.maxHeight) {
				      //this is if the accordion is open
				      content.style.maxHeight = null;
				    } else {
				      //if the accordion is currently closed
				      content.style.maxHeight = content.scrollHeight + "px";
				    }
			  	};
			});

	  	</script>
		<?php 
			wp_nonce_field( 'review_sorted-settings-save', 'review_sorted-settings-nonce' ); 
			submit_button();
		?>
	</form>
</div>