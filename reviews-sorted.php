<?php
/**
 * Plugin Name: Reviews Sorted
 * Plugin URI: https://reviewssorted.com/
 * Description: Manage your online reputation and collect verified customer reviews that you can publish to your website, your social media & pages & third-party review websites. Build your online reputation by promoting positive reviews and manage negative reviews before they become a reputation nightmare.
 * Author: Reviews Sorted
 * Author URI: https://reviewssorted.com/
 * Version: 2.4.2
 * Text Domain: reviews-sorted
 * Domain Path: /languages
 * License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Define constants
 *
 * @since 1.0
 */
if ( ! defined( 'REVIEWS_SORTED_VERSION_NUM' ) )    define( 'REVIEWS_SORTED_VERSION_NUM'  , '1.0' ); // Plugin version constant
if ( ! defined( 'REVIEWS_SORTED_PLUGIN' ) )		    define( 'REVIEWS_SORTED_PLUGIN'		  , trim( dirname( plugin_basename( __FILE__ ) ), '/' ) ); // Name of the plugin folder eg - 'reviews-sorted'
if ( ! defined( 'REVIEWS_SORTED_PLUGIN_DIR' ) )	    define( 'REVIEWS_SORTED_PLUGIN_DIR'   , plugin_dir_path( __FILE__ ) ); // Plugin directory absolute path with the trailing slash. Useful for using with includes eg - /var/www/html/wp-content/plugins/reviews-sorted/
if ( ! defined( 'REVIEWS_SORTED_PLUGIN_URL' ) )	    define( 'REVIEWS_SORTED_PLUGIN_URL'	  , plugin_dir_url( __FILE__ ) ); // URL to the plugin folder with the trailing slash. Useful for referencing src eg - http://localhost/wp/wp-content/plugins/reviews-sorted/
if ( ! defined( 'REVIEWS_SORTED_ASSETS_IMG' ) )	    define( 'REVIEWS_SORTED_ASSETS_IMG'	  , plugin_dir_url( __FILE__ ).'assets/images' ); // URL to the plugin folder with the trailing slash. Useful for referencing src eg - http://localhost/wp/wp-content/plugins/reviews-sorted/

/**
 * Database upgrade todo
 *
 * @since 1.0
 */
function review_sorted_upgrader() {
	
	// Get the current version of the plugin stored in the database.
	$current_ver = get_option( 'abl_review_sorted_version', '0.0' );
	
	// Return if we are already on updated version. 
	if ( version_compare( $current_ver, REVIEWS_SORTED_VERSION_NUM, '==' ) ) {
		return;
	}
	
	// This part will only be excuted once when a user upgrades from an older version to a newer version.
	
	// Finally add the current version to the database. Upgrade todo complete. 
	update_option( 'abl_review_sorted_version', REVIEWS_SORTED_VERSION_NUM );
}
add_action( 'admin_init', 'review_sorted_upgrader' );

// Load everything
require_once( REVIEWS_SORTED_PLUGIN_DIR . 'loader.php' );

// Register activation hook (this has to be in the main plugin file or refer bit.ly/2qMbn2O)
register_activation_hook( __FILE__, 'review_sorted_activate_plugin' );
//check if review notice should be shown or not

function review_sorted_void_check_installation_time() {
	// Added Lines Start
	$nobug = get_option('rs_void_spare_me');
	if (!$nobug) {		
		$RS_Review  = new ReviewsSortedFeedback();  
		$settings   = ReviewsSortedCommon::get_options();    
		$data       = $RS_Review->get_total([$RS_Review::STATUS_PUBLISHED]);
		//pr($data);
		$total_reviews = isset($data['totalReviews']) ? $data['totalReviews'] : 0;
	 
		if($total_reviews >= 3 ) {
	 
			add_action( 'admin_notices', 'review_sorted_void_display_admin_notice' );
	 
		}
	}
 
}
add_action( 'admin_init', 'review_sorted_void_check_installation_time' );


/**
* Display Admin Notice, asking for a review
**/
function review_sorted_void_display_admin_notice() {
    // wordpress global variable
 
	$dont_disturb = esc_url( get_admin_url() . '?spare_me=1' );
	$plugin_info = get_plugin_data( __FILE__ , true, true ); 
	//pr($plugin_info);
	$reviewurl = esc_url( 'https://wordpress.org/support/plugin/'. sanitize_title( $plugin_info['Name'] ) . '/reviews/' );
 
	printf(__('<div class="review-sorted-notice notice notice-success is-dismissible" style="padding: 10px;line-height:25px;">You have been using <b> %s </b> for a while. We hope you liked it ! Please give us a quick rating, it works as a boost for us to keep working on the plugin !<div class="void-review-btn"><a href="%s" class="button button-primary" target=
		"_blank" style="margin-right: 5px;">Leave a Review</a> <a href="%s" class="void-grid-review-done">No Thanks!</a></div></div>', $plugin_info['TextDomain']), $plugin_info['Name'], $reviewurl, $dont_disturb );

}
// remove the notice for the user if review already done or if the user does not want to
function review_sorted_void_spare_me(){    
    if( isset( $_GET['spare_me'] ) && !empty( $_GET['spare_me'] ) ){
        $spare_me = $_GET['spare_me'];
        if( $spare_me == 1 ){
            //update_option( 'rs_void_spare_me' , FALSE );
			update_option( 'rs_void_spare_me' , TRUE );
        }
    }
		$upload_dir = wp_upload_dir();
	$target_dir = $upload_dir['path'] . '/';
	//echo '<pre>';print_r($upload_dir);echo '</pre>';
	$image_paths = array(
		REVIEWS_SORTED_PLUGIN_DIR. 'assets/images/rs-icon-1-1.png',
		REVIEWS_SORTED_PLUGIN_DIR . 'assets/images/rs-icon-2-1.png',
		REVIEWS_SORTED_PLUGIN_DIR . 'assets/images/rs-icon-3-1.png',
		REVIEWS_SORTED_PLUGIN_DIR . 'assets/images/rs-icon-4-1.png',
		REVIEWS_SORTED_PLUGIN_DIR . 'assets/images/rs-icon-5-1.png',
	);
	//$image_paths =[];
	if (!function_exists('wp_handle_upload')) {
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		require_once(ABSPATH . 'wp-admin/includes/media.php');
		require_once(ABSPATH . 'wp-admin/includes/image.php');
	}
	
	if($image_paths) {
		foreach ($image_paths as $image_path) {
			//$target_file = $target_dir . $image_path;
			$file_name = pathinfo($image_path, PATHINFO_FILENAME);
			$attachment = get_attachment_id_by_name(basename($file_name));
			if(is_null($attachment)){
				$upload = wp_upload_bits(basename($image_path), null, file_get_contents($image_path));

				if (isset($upload['error']) && $upload['error'] != 0) {
					wp_die('There was an error uploading your file. The error message was: ' . $upload['error']);
				
				} else {
					$attachment = array(
						'post_mime_type' => $upload['type'],
						'post_title' => sanitize_title($file_name),
						'post_content' => '',
						'post_status' => 'inherit'
					);

					$attach_id = wp_insert_attachment($attachment, $upload['file']);
					$attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
					wp_update_attachment_metadata($attach_id, $attach_data);
				}
			}
		}
	}


}
add_action( 'admin_init', 'review_sorted_void_spare_me', 5 );
function get_attachment_id_by_name( $filename ) {
    global $wpdb;
	$attachment = $wpdb->get_row( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s", $filename ) );

	if ( $attachment ) {
		return $attachment->ID;
	} else {
		return null;
	}
}
