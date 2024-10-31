<?php
$fillable = (new ReviewsSortedCommon)->get_form_default_fields();
?>
<div class="wrap">	
	<h1 class="wp-heading-inline"><?php _e('Form Settings', 'reviews-sorted'); ?></h1>

	<hr class="wp-header-end">

	<form action="<?php echo esc_url(admin_url( 'admin-post.php' )); ?>" method="post">
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="rs-form_redirect-page"><?php _e('Redirect Page', 'reviews-sorted'); ?></label></th>
					<td>
						<input required type="text" class="regular-text" id="rs-form_redirect-page" 
						name="reviews_sorted_settings[form_redirect_page]" value="<?php echo esc_attr($settings['form_redirect_page']); ?>">
					</td>
				</tr>
				<tr>
					<th><h2><?php _e('Field Heading', 'reviews-sorted'); ?></h2></th>
				</tr>
				<tr>
					<th scope="row"><label for="rs-form_main_heading"><?php _e('Form Main Heading', 'reviews-sorted'); ?></label></th>
					<td>
						<input required type="text" class="regular-text" id="rs-form_main_heading" 
						name="reviews_sorted_settings[form_main_heading]" value="<?php echo esc_attr($settings['form_main_heading']); ?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="rs-form_sub_heading"><?php _e('Form Sub Heading', 'reviews-sorted'); ?></label></th>
					<td>
						<input required type="text" class="regular-text" id="rs-form_sub_heading" 
						name="reviews_sorted_settings[form_sub_heading]" value="<?php echo esc_attr($settings['form_sub_heading']); ?>">
					</td>
				</tr>
				<tr>
					<th><h2><?php _e('Field Settings', 'reviews-sorted'); ?></h2></th>
				</tr>
				<tr>
					<th scope="row">
						<label for="rs-form_hidden_label"><?php _e('Hidden Label', 'reviews-sorted'); ?></label>
					</th>
					<td>
						<input name="reviews_sorted_settings[form_hidden_label]" id="rs-form_hidden_label" type="checkbox" 
						value="yes" <?php checked(esc_attr($settings['form_hidden_label']), 'yes'); ?>>
						
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="rs-form_hidden_placeholder"><?php _e('Hidden Placeholder', 'reviews-sorted'); ?></label>
					</th>
					<td>
						<input name="reviews_sorted_settings[form_hidden_placeholder]" id="rs-form_hidden_placeholder" type="checkbox" 
						value="yes" <?php checked(esc_attr($settings['form_hidden_placeholder']), 'yes'); ?>>
					</td>
					
				</tr>
				
				<?php 
				foreach($fillable as $field_key => $field_data): 
					$field_label = $field_data['label'];
					$fieldlabel = strtolower($field_data['placeholder']);
					if (strpos($fieldlabel, 'phone') !== false) {
						$fieldlabel = 'phone_number';
					}

					$hidden_field_name = "hide_" . str_replace(' ', '_', $fieldlabel);
					$is_field_hidden = isset($settings[$hidden_field_name]) && $settings[$hidden_field_name] === 'yes';			
					?>
					<tr>
						<th scope="row"><label><?php _e($field_data['placeholder'], 'reviews-sorted'); ?></label><br><input name="reviews_sorted_settings[hide_<?php echo str_replace(' ', '_', $fieldlabel); ?>]" id="rs-form_hidden_fields" type="checkbox" 
							value="yes" <?php if($is_field_hidden) { echo 'checked'; } ?>><span class="show-fields-sections" style="font-size: 10px;"> (Hide field on the form)</span>
						</th>
						<td style="width: auto;display: inline-block;"> 
							<input required type="text" class="regular-text" id="rs-form_<?php echo esc_attr($settings[$field_key . '_label']); ?>" 
							name="reviews_sorted_settings[<?php echo esc_attr($field_key) . '_label'; ?>]" value="<?php echo esc_attr($settings[$field_key . '_label']); ?>">
							
							<label for="rs-form_<?php echo esc_attr($field_key . '_label'); ?>" style="display:block; padding: 10px">
								<?php _e('Field Title', 'reviews-sorted'); ?></label>
							</td>
							
							<td style="width: auto;display: inline-block;">
								<input required type="text" class="regular-text" id="rs-form_<?php echo esc_attr($settings[$field_key . '_placeholder']); ?>" 
								name="reviews_sorted_settings[<?php echo $field_key . '_placeholder'; ?>]" value="<?php echo esc_attr($settings[$field_key . '_placeholder']); ?>">

								<label for="rs-form_<?php echo $field_key . '_placeholder'; ?>" style="display:block;padding: 10px">
									<?php _e('Field Placeholder', 'reviews-sorted'); ?></label>

								</td>
							</tr>
						<?php endforeach; ?>				
					</tbody>
				</table>
				<input type="hidden" name="page" value="reviews-sorted-form-settings">
				<?php 
				wp_nonce_field( 'review_sorted-settings-save', 'review_sorted-settings-nonce' ); 
				submit_button();
				?>
			</form>
		</div>