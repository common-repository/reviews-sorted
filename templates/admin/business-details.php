<div class="wrap">	
	<h1 class="wp-heading-inline"><?php _e('Local Business', 'reviews-sorted'); ?></h1>

	<hr class="wp-header-end">

	<form action="<?php echo esc_url(admin_url( 'admin-post.php' )); ?>" method="post">
		<table class="form-table">
			<tbody>

				<tr>
					<th scope="row"><label for="rs-form_business_address"><?php _e('Business Name', 'reviews-sorted'); ?></label></th>
					<td>
						<input 
						required type="text" 
						class="regular-text" 
						id="rs-business_name" 
						value="<?php echo esc_attr( $settings['business_name']); ?>"  
						placeholder="<?php _e('Your Business Name', 'reviews-sorted'); ?>"
						name="reviews_sorted_settings[business_name]">
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="rs-form_business_icon"><?php _e('Business Icon', 'reviews-sorted'); ?></label></th>
					<td>
						<input type="hidden" class="regular-text" id="rs-form_business_icon" name="reviews_sorted_settings[business_icon]" value="<?php echo esc_url($settings['business_icon']); ?>">
						<button type="button" class="button rs-form-media-uploader"><?php _e('Select Icon', 'reviews-sorted'); ?></button>
						<span class="rs-form-media-preview" style="display: inline-block; position: relative;"><?php if (!empty($settings['business_icon'])) : ?><img src="<?php echo esc_url($settings['business_icon']); ?>" alt="Business Icon" width="100" height="auto" /><?php endif; ?></span>
						<span class="rs-form-remove-icon" style="position: absolute;border-radius: 15px;background-color: red;width: 18px;color: white;margin: -15px;text-align: center;font-weight: 600;display: inline-block;cursor: pointer;"><?php _e(' X ', 'reviews-sorted'); ?></span>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="rs-form_business_address"><?php _e('Business Address', 'reviews-sorted'); ?></label></th>
					<td>
						<input 
						required type="text" 
						class="regular-text" 
						id="rs-form_business_address" 
						placeholder="<?php _e('Your Address', 'reviews-sorted'); ?>"
						value="<?php _e($settings['business_address'], 'reviews-sorted'); ?>"  
						name="reviews_sorted_settings[business_address]">
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="rs-form_business_phone"><?php _e('Business Phone No', 'reviews-sorted'); ?></label></th>
					<td>
						<input 
						required type="text" 
						class="regular-text" 
						value="<?php _e($settings['business_phone'], 'reviews-sorted'); ?>"
						placeholder="<?php _e('Your Phone Number', 'reviews-sorted'); ?>"
						id="rs-form_business_phone" 
						name="reviews_sorted_settings[business_phone]">
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="rs-form_business_priceRange"><?php _e('Business Price Range', 'reviews-sorted'); ?></label></th>
					<td>
						<input 
						required type="text" 
						class="regular-text" 
						value="<?php echo esc_attr($settings['business_priceRange']); ?>"
						id="rs-form_business_priceRange" 
						placeholder="<?php _e('Add Price Range - $ or $$ or $$$ or $$$$', 'reviews-sorted'); ?>"
						name="reviews_sorted_settings[business_priceRange]">
					</td>
				</tr>

			</tbody>
		</table>
		
		<?php _e('<p class="description">This information is used to add <a href="https://schema.org/docs/gs.html" target="_blank">Microdata</a> to each slider in the Reviews sorted slider</p>', 'reviews-sorted'); ?>

		<?php 
		wp_nonce_field( 'review_sorted-settings-save', 'review_sorted-settings-nonce' ); 
		submit_button();
		?>
	</form>	
</div>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$('.rs-form-media-uploader').click(function(e) {
			e.preventDefault();

			var mediaUploader;

			 // If the media uploader has already been initialized, reuse it.
			if (mediaUploader) {
				mediaUploader.open();
				return;
			}

    		// Create a new media uploader instance.
			mediaUploader = wp.media({
				title: '<?php _e('Select Business Icon', 'reviews-sorted'); ?>',
				button: {
					text: '<?php _e('Use This Image', 'reviews-sorted'); ?>'
				},
				multiple: false
			});

    		// When an image is selected, update the hidden input and preview.
			mediaUploader.on('select', function() {
				var attachment = mediaUploader.state().get('selection').first().toJSON();
				$('#rs-form_business_icon').val(attachment.url);
				$('.rs-form-media-preview').html('<img src="' + attachment.url + '" alt="Business Icon" width="100" height="auto" />');
			});

   			// Open the media uploader.
			mediaUploader.open();
			$('.rs-form-remove-icon').show();
		});
		$('.rs-form-remove-icon').click(function(e) {
			e.preventDefault();

			$('#rs-form_business_icon').val('');
			$('.rs-form-media-preview').html('');
			$(this).hide();
		});
	});

</script>