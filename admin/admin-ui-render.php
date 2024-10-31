<?php
/**
 * Admin UI setup and render
 *
 * @since 1.0
 * @function	review_sorted_general_settings_section_callback()	Callback function for General Settings section
 * @function	review_sorted_general_settings_field_callback()	Callback function for General Settings field
 * @function	review_sorted_admin_interface_render()				Admin interface renderer
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
/**
 * Admin interface renderer
 *
 * @since 1.0
 */ 
function review_sorted_admin_reviews_list_interface_render () {
	
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( isset( $_GET['settings-updated'] ) ) {
		// Add settings saved message with the class of "updated"
		add_settings_error( 'review_sorted_settings_saved_message', 'review_sorted_settings_saved_message', __( 'Settings are Saved', 'reviews-sorted' ), 'updated' );
	}
 	

	// Show Settings Saved Message
	settings_errors( 'review_sorted_settings_saved_message' );

	if ( isset( $_GET['action']) && $_GET['action'] == 'trash' ) {
		$reviews = new ReviewsSortedFeedback();
		$review_id = isset($_GET['id']) ? sanitize_text_field($_GET['id']) : 0;
		$reviews->trash_review($_GET['id']);

		add_settings_error( 'review_sorted_settings_saved_message', 
				'review_sorted_settings_saved_message', 
				__( '1 review moved to the Trash.', 'reviews-sorted' ), 'updated' );
	}

	if ( isset( $_GET['action']) && $_GET['action'] == 'edit' ) {
		$reviews = new ReviewsSortedFeedback();
		$review_id = isset($_GET['id']) ? sanitize_text_field($_GET['id']) : 0;
		$review = $reviews->get_review($review_id);

		include_once REVIEWS_SORTED_PLUGIN_DIR . "templates/admin/review-edit.php";
	}
	else{

		settings_errors( 'review_sorted_settings_saved_message' ); 

		$RS_Review = new ReviewsSortedFeedback();
		
		$reviews = $RS_Review->query();
		$data    = $RS_Review->get_total();
		
		$current_paged = isset( $_GET['paged'] ) ? absint( sanitize_text_field($_GET['paged']) ) : 1;
		$per_page 	   = $RS_Review::LIMIT_PER_PAGE;
		$page_links    = $RS_Review->paginate($data['totalReviews'], $current_paged, $per_page);
		
		include_once REVIEWS_SORTED_PLUGIN_DIR . "templates/admin/reviews-list.php";
	}
}

function review_sorted_admin_interface_render () {
	
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	/**
	 * If settings are inside WP-Admin > Settings, then WordPress will automatically display Settings Saved. If not used this block
	 * @refer	https://core.trac.wordpress.org/ticket/31000
	 * If the user have submitted the settings, WordPress will add the "settings-updated" $_GET parameter to the url
	 */
	if ( isset( $_GET['settings-updated'] ) ) {
		// Add settings saved message with the class of "updated"
		add_settings_error( 'review_sorted_settings_saved_message', 'review_sorted_settings_saved_message', __( 'Settings are Saved', 'reviews-sorted' ), 'updated' );
	}
 	

	$template = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
	$template = str_replace('reviews-sorted-', '', $template);
	// Show Settings Saved Message

	settings_errors( 'review_sorted_settings_saved_message' ); 
	
	if( empty($template) || !file_exists(REVIEWS_SORTED_PLUGIN_DIR . "templates/admin/".$template.'.php') ){
	?>
		<div class="wrap">	
			<h1>Reviews Sorted</h1>
		</div>
	<?php
	}
	else{
		$settings = ReviewsSortedCommon::get_options();
		include_once REVIEWS_SORTED_PLUGIN_DIR . "templates/admin/{$template}.php";
	}
}