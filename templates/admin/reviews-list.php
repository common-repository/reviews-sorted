<div class="wrap">	
	<h1 class="wp-heading-inline"><?php _e('Reviews List','reviews-sorted'); ?></h1>

	<hr class="wp-header-end">

	<table class="wp-list-table widefat fixed striped table-view-list posts">
		<thead>
			<tr>
				<th class="manage-column column-cb check-column" style=" padding: 8px 10px; ">ID</th>
				<th class="manage-column"><?php _e('Name','reviews-sorted'); ?></th>
				<th class="manage-column"><?php _e('Email','reviews-sorted'); ?></th>
				<th class="manage-column"><?php _e('Phone','reviews-sorted'); ?></th>
				<th class="manage-column"><?php _e('Date/Time','reviews-sorted'); ?></th>
				<th class="manage-column"><?php _e('Star Rating','reviews-sorted'); ?></th>
				<th class="manage-column"><?php _e('Recommended','reviews-sorted'); ?></th>
				<th class="manage-column"><?php _e('Status','reviews-sorted'); ?></th>
				<!-- <th class="manage-column"><?php _e('User IP','reviews-sorted'); ?></th> -->
				<th class="manage-column"><?php _e('Actions','reviews-sorted'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if( !$reviews ): ?>
				<tr>
					<td colspan="9"><?php _e('There are no reviews yet','reviews-sorted'); ?></td>
				</tr>
			<?php else: ?>
			<?php foreach($reviews as $review): ?>
			<tr>
				<td><?php echo esc_html($review->id); ?></td>
				<td><?php echo esc_html($review->authorfname . ' ' . $review->authorlname); ?></td>
				<td><?php echo esc_html($review->email); ?></td>
				<td><?php echo esc_html($review->phone); ?></td>
				<td><?php 
					// Form Submission Date formatted like January 13, 2022 5:08 am
					// ‘F d, Y g:i a’
				 	$created = date("F d, Y g:i a", strtotime($review->created_at));  
					echo esc_html($created); 
				?></td> 
				<td><?php echo esc_html($review->rating); ?></td>
				<td><?php echo ($review->recommend == 'no') ? _e('No','reviews-sorted') : _e('Yes','reviews-sorted'); ?></td>
				<td><?php echo esc_html($review->status); ?></td>
				<td>
				<?php
					$alert_message = __('Please be aware that deleting or changing customer testimonials could be considered as false, misleading or deceptive conduct and could contravene local laws. Click OK to continue or Cancel to return.','reviews-sorted');
				?>	
					<a href="<?php echo esc_url(admin_url('/admin.php?page=reviews-sorted-reviews-list&action=edit&id='.$review->id)); ?>" onclick="return confirm('<?php echo esc_attr($alert_message); ?>');"><?php _e('Edit','reviews-sorted'); ?></a> 
					| 
					<a href="<?php echo esc_url(admin_url('/admin.php?page=reviews-sorted-reviews-list&action=trash&id='.$review->id)); ?>" onclick="return confirm('<?php echo esc_attr($alert_message); ?>');"><?php _e('Trash','reviews-sorted'); ?></a></td>
			</tr>	
			<?php endforeach; ?>
			<?php endif; ?>
		</tbody>

	</table>
	<?php if($page_links): echo $page_links; endif; ?>
</div>