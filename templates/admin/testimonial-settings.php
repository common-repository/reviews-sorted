<?php
	wp_enqueue_media();
	//echo '<pre>';print_r($settings);echo '</pre>';
?>
<div class="wrap">	
	<h1 class="wp-heading-inline"><?php _e('Review Settings', 'reviews-sorted'); ?></h1>

	<hr class="wp-header-end">

	<form action="<?php echo esc_url(admin_url( 'admin-post.php' )); ?>" method="post">
		<table class="form-table">
			<tbody>
				<tr>
					<th><?php _e('Review Character Length', 'reviews-sorted'); ?></th>
					<td>
						<input required type="number" class="regular-text" name="reviews_sorted_settings[testimonial_character_length]" 
							value="<?php echo esc_attr($settings['testimonial_character_length']); ?>" >
					</td>
				</tr>
				<tr>
					<th><?php _e('Overall Rating Month Label', 'reviews-sorted'); ?></th>
					<td>
						<?php
							$selected = $settings['rating_month_label'];
							$label_options = [
								__('1 Month',  'reviews-sorted'),
								__('3 Months', 'reviews-sorted'),
								__('6 Months', 'reviews-sorted'),
								__('1 Year',   'reviews-sorted'),
								__('All',   'reviews-sorted'),
							];
						?>
						<select class="regular-text" required name="reviews_sorted_settings[rating_month_label]">
						<?php 
							foreach($label_options as $option){
								printf('<option value="%s" %s>%s</option>',
									esc_attr($option),
									selected($selected, $option),
									$option
								);
							}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<th><?php _e('Auto Publish Posts', 'reviews-sorted'); ?></th>
					<td>
						<?php
							$selected = esc_attr($settings['testimonial_auto_publish']);
						?>
						<select class="regular-text" required name="reviews_sorted_settings[testimonial_auto_publish]" required>
							<option value="yes" <?php selected( esc_attr($settings['testimonial_auto_publish']), 'yes'); ?>><?php _e('Yes', 'reviews-sorted'); ?></option>
							<option value="no" <?php selected( esc_attr($settings['testimonial_auto_publish']), 'no'); ?>><?php _e('No', 'reviews-sorted'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th><?php _e('Minimum Length', 'reviews-sorted'); ?></th>
					<td>
						<input type="number" name="reviews_sorted_settings[testimonial_min_length]"
							value="<?php echo esc_attr($settings['testimonial_min_length']); ?>">
					</td>
				</tr>
				<tr>
					<th><?php _e('Minimum Rating', 'reviews-sorted'); ?></th>
					<td>
						<input type="number" name="reviews_sorted_settings[testimonial_min_rating]"
							value="<?php echo esc_attr($settings['testimonial_min_rating']); ?>">
					</td>
				</tr>
				<tr>
					<th><?php _e('Color', 'reviews-sorted'); ?></th>
					<td>
						<input type="color" name="reviews_sorted_settings[star_color]"
							value="<?php echo isset($settings['star_color']) ? esc_attr($settings['star_color']) : '#de3a00'; ?>">
					</td>
				</tr>
				<?php for($i = 1; $i <= 5; $i++ ) : ?>
				<tr>
					<th><?php _e('Icon for '. $i .' stars', 'reviews-sorted'); ?> 
						<br><small><?php _e('Suggested size: 26x26', 'reviews-sorted'); ?></small>
					</th>
					<td>						
						<?php
							$row_key = 'icon_for_'. $i .'_star';
							$image_id = isset($settings[$row_key]) ? intval($settings[$row_key]) : 0;
							
							if( $image = wp_get_attachment_image_src( $image_id ) ) {

								echo '<a href="#" class="review-icon-upl"><img src="' . esc_url($image[0]) . '" /></a>
								      <a href="#" class="review-icon-rmv">Remove icon</a>
								      <input type="hidden" name="reviews_sorted_settings['. esc_attr($row_key) .']" value="' . esc_attr($image_id) . '">';

							} else {

								echo '<a href="#" class="review-icon-upl">Upload image</a>
								      <a href="#" class="review-icon-rmv" style="display:none">Remove icon</a>
								      <input type="hidden" name="reviews_sorted_settings['. esc_attr($row_key) .']" value="">';

							} 
						?>	
					</td>
				</tr>
				<?php endfor; ?>
			</tbody>
		</table>
		<?php 
			wp_nonce_field( 'review_sorted-settings-save', 'review_sorted-settings-nonce' ); 
			submit_button();
		?>
	</form>
</div>

<script type="text/javascript">
jQuery(function($){

	// on upload button click
	$('body').on( 'click', '.review-icon-upl', function(e){

		e.preventDefault();

		var button = $(this),
		custom_uploader = wp.media({
			title: 'Insert image',
			library : {				
				type : 'image' // uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
			},
			button: {
				text: 'Use this image' // button label text
			},
			multiple: false
		}).on('select', function() { // it also has "open" and "close" events
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			button.html('<img src="' + attachment.url + '">').next().show().next().val(attachment.id);
		}).open();
	
	});

	// on remove button click
	$('body').on('click', '.review-icon-rmv', function(e){

		e.preventDefault();

		var button = $(this);
		button.next().val(''); // emptying the hidden field
		button.hide().prev().html('Upload image');
	});

});
</script>