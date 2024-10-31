<div class="wrap">	
	<h1 class="wp-heading-inline">Edit Review</h1>

	<p><?php printf(
				__('<a href="%s">Reviews List</a> > Edit Review', 'reviews-sorted'),
				esc_url(admin_url('admin.php?page=reviews-sorted-reviews-list'))
			); ?> </p>
	
	<hr class="wp-header-end">

	<?php if( !$review ): ?>
		<div id="setting-error-invalid_review-id" class="notice notice-error settings-error is-dismissible"> 
			<p><strong><?php _e('This review does not exist!', 'reviews-sorted'); ?></strong></p>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e('Dismiss this notice.', 'reviews-sorted'); ?></span></button>
		</div>
	<?php else: ?>

	<form action="<?php echo esc_url(admin_url( 'admin-post.php' )); ?>" method="post">
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="rs-form_fname"><?php _e('First Name', 'reviews-sorted'); ?><span class="asterisks">*</span>'</label></th>
					<td>
					  	<input required type="text" class="regular-text" id="rs-form_fname" name="reviewupdate[authorfname]" 
					  		value="<?php echo esc_attr($review['authorfname']); ?>">
					</td>
				</tr>
			
				<tr>
					<th scope="row"><label for="rs-form_lname"><?php _e('Last Name', 'reviews-sorted'); ?></label><span class="asterisks">*</span>'</th>
					<td>
					 	<input required type="text" class="regular-text" id="rs-form_lname" name="reviewupdate[authorlname]" 
					 		value="<?php echo esc_attr($review['authorlname']); ?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="rs-form_service"><?php _e('Service Provided', 'reviews-sorted'); ?></label></th>
					<td>
					 	<input class="regular-text" id="rs-form_service" name="reviewupdate[service]" 
					 		value="<?php echo esc_attr(($review['service'])); ?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="rs-form_email"><?php _e('Email', 'reviews-sorted'); ?><span class="asterisks">*</span>'</label></th>
					<td>
					 	<input required type="email" class="regular-text" id="rs-form_email" name="reviewupdate[email]" 
					 		value="<?php echo esc_attr($review['email']); ?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="phone"><?php _e('Phone Number', 'reviews-sorted'); ?></label></th>
					<td>
					 	<input type="tel" class="regular-text" id="phone" name="reviewupdate[phone]" 
					 		value="<?php echo esc_attr($review['phone']); ?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="rs-form_rating"><?php _e('Rating', 'reviews-sorted'); ?><span class="asterisks">*</span>'</label></th>
					<td>
						<select name="reviewupdate[rating]" class="regular-text" id="rs-form_rating" required>
						<?php
							printf('<option value="5.0" %s>%s</option>', selected(esc_attr($review['rating']), '5.0'), __('5 Stars', 'reviews-sorted'));
							printf('<option value="4.0" %s>%s</option>', selected(esc_attr($review['rating']), '4.0'), __('4 Stars', 'reviews-sorted'));
							printf('<option value="3.0" %s>%s</option>', selected(esc_attr($review['rating']), '3.0'), __('3 Stars', 'reviews-sorted'));
							printf('<option value="2.0" %s>%s</option>', selected(esc_attr($review['rating']), '2.0'), __('2 Stars', 'reviews-sorted'));
							printf('<option value="1.0" %s>%s</option>', selected(esc_attr($review['rating']), '1.0'), __('1 Star', 'reviews-sorted'));
						?>
		                </select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="rs-form_recommend"><?php _e('Recommend', 'reviews-sorted'); ?><span class="asterisks">*</span>'</label></th>
					<td>
					  	<select name="reviewupdate[recommend]" class="regular-text" id="rs-form_recommend" required>
					  	<?php
					  		printf('<option value="yes" %s>%s</option>', selected(esc_attr($review['recommend']), 'yes'), __('Yes', 'reviews-sorted'));
					  		printf('<option value="no" %s>%s</option>', selected(esc_attr($review['recommend']), 'no'), __('No', 'reviews-sorted'));
					  	?>
	                	</select>
					</td>
				</tr>
				 <tr>
					<th scope="row"><label for="rs-form_feedback"><?php _e('Feedback', 'reviews-sorted'); ?></label></th>
					<td>
					    <textarea id="rs-form_feedback" class="large-text" name="reviewupdate[content]" placeholder="Feedback" rows="4"><?php echo esc_html(stripslashes($review['content'])); ?></textarea>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="rs-form_recommend"><?php _e('Status', 'reviews-sorted'); ?></label></th>
					<td>
					  	<select name="reviewupdate[status]" class="regular-text" id="rs-form_recommend" required>
					  	<?php
					  		printf('<option value="Pending" %s>%s</option>', selected(esc_attr($review['status']), 'Pending'), __('Pending', 'reviews-sorted'));
					  		printf('<option value="Published" %s>%s</option>', selected(esc_attr($review['status']), 'Published'), __('Published', 'reviews-sorted'));
					  		printf('<option value="Declined" %s>%s</option>', selected(esc_attr($review['status']), 'Declined'), __('Declined', 'reviews-sorted'));
					  	?>	
	                	</select>
					</td>
				</tr>
			</tbody>
		</table>

		<?php wp_nonce_field( 'review_sorted-settings-save', 'review_sorted-settings-nonce' );  ?>
		<input type="hidden" name="reviewupdate[id]" value="<?php echo esc_attr($review['id']); ?>">
		<input type="hidden" name="_wp_http_referer" value="<?php echo esc_url(admin_url( 'admin.php?page=reviews-sorted-reviews-list&action=edit&id='.esc_attr($review['id']) )); ?>">
		<p class="submit">
			<?php submit_button(null, 'primary', 'submit', false); ?>
			<?php printf(
				'<a href="%s" onclick="return confirm(\'%s\');" class="button button-secondary">%s</a>',
				esc_url(admin_url('/admin.php?page=reviews-sorted-reviews-list&action=trash&id='. esc_attr($review['id']))),
				__('Are you sure you want to remove this review?', 'reviews-sorted'),
				__('Trash Review', 'reviews-sorted')
			); ?>
		</p>
	</form>	

	<?php endif; ?>

</div>