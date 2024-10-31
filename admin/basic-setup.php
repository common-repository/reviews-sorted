<?php 
/**
 * Basic setup functions for the plugin
 *
 * @since 1.0
 * @function	review_sorted_activate_plugin()		Plugin activatation todo list
 * @function	review_sorted_load_plugin_textdomain()	Load plugin text domain
 * @function	review_sorted_settings_link()			Print direct link to plugin settings in plugins list in admin
 * @function	review_sorted_plugin_row_meta()		Add donate and other links to plugins list
 * @function	review_sorted_footer_text()			Admin footer text
 * @function	review_sorted_footer_version()			Admin footer version
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
/**
 * Plugin activatation todo list
 *
 * This function runs when user activates the plugin. Used in register_activation_hook in the main plugin file. 
 * C
 * @since 1.0
 */
function review_sorted_activate_plugin() {
	
	/**
	 * Create MYSQL tables to store data
	 * */
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	//1.  Reviews table
	$table_name = $wpdb->prefix . 'reviews';

	$sql = "CREATE TABLE $table_name (
		`id` mediumint(9) NOT NULL AUTO_INCREMENT,
		`authorfname` VARCHAR(255) NOT NULL,
		`authorlname` VARCHAR(255) NOT NULL,
		`service` VARCHAR(255) NOT NULL,
		`phone` VARCHAR(20) NOT NULL,
		`email` VARCHAR(100) NOT NULL,
		`state` VARCHAR(255) NOT NULL,
		`region` VARCHAR(10) NOT NULL,
		`branch` VARCHAR(100) NOT NULL,						
		`content` TEXT NOT NULL,
		`rating` decimal(2,1) NOT NULL,
		`recommend` VARCHAR(255) NOT NULL,
		`userip` VARCHAR(100) NOT NULL,
		`status` VARCHAR(10) NOT NULL,
		`questionnaire` TEXT NOT NULL,
		`created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
		`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		UNIQUE KEY id (id)
	) $charset_collate;";

	dbDelta($sql);

	

	//2. Reviews Email table	
	$table_name = $wpdb->prefix . 'reviews_email';

	$sqls = "CREATE TABLE $table_name (
		`id` INT(255) NOT NULL AUTO_INCREMENT, 
		`email` VARCHAR(255) NOT NULL ,
		`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
		`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,     UNIQUE KEY id (id)
	) $charset_collate;";

	dbDelta($sqls);


	//3. Email Schedule table
	$table_name = $wpdb->prefix . 'email_schedule';

	$sqls = "CREATE TABLE $table_name (
		`ID` INT(255) NOT NULL AUTO_INCREMENT, 
		`review_id` INT(255) NOT NULL , 
		`email_template` VARCHAR(255) NOT NULL ,
		`date_send` DATE NOT NULL, 
		`status` INT(255) NOT NULL, 
		PRIMARY KEY (`ID`)
	) $charset_collate;";

	dbDelta($sqls);

	/**
	 * Two new pages are also created on installation:
	 * @link 1)	/submit-a-review/
	 * @link 2)	/submit-a-review/thank-you/
	 * */
	$page_created = get_option('reviews_sorted_default_pages_created', false );
	if( !$page_created ){

	    $survey_page = array(
	      'post_title'    => __( 'Submit A Review', 'reviews-sorted' ),
	      'post_content'  => '[reviews-form]',
	      'post_status'   => 'publish',
	      'post_type'     => 'page',
	    );

	    // Insert the page into the database
	    $page_id = wp_insert_post( $survey_page );
	    if($page_id){
	    	$thank_you_page = array(
		      	'post_title'    => __( 'Thank you', 'reviews-sorted' ),
		      	'post_content'  => '<h2>Thank you so much for rating your experience with us at '. get_bloginfo('name') .'.</h2><p>As a valued customer, your review and feedback are important to us as we strive to improve our processes and deliver a better service to you.</p>',
		      	'post_status'   => 'publish',
		      	'post_type'     => 'page',
		      	'post_parent'   => $page_id,
		    );
	    	wp_insert_post( $thank_you_page );

	    	update_option('reviews_sorted_default_pages_created', $page_id, false );
	    }
	}
}

/**
 * Load plugin text domain
 *
 * @since 1.0
 */
function review_sorted_load_plugin_textdomain() {
    load_plugin_textdomain( 'reviews-sorted', false, '/reviews-sorted/languages/' );
}
add_action( 'plugins_loaded', 'review_sorted_load_plugin_textdomain' );

/**
 * Print direct link to plugin settings in plugins list in admin
 *
 * @since 1.0
 */
function review_sorted_settings_link( $links ) {
	return array_merge(
		array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=reviews-sorted' ) . '">' . __( 'Settings', 'reviews-sorted' ) . '</a>'
		),
		$links
	);
}
add_filter( 'plugin_action_links_' . REVIEWS_SORTED_PLUGIN . '/reviews-sorted.php', 'review_sorted_settings_link' );

/**
 * Admin footer text
 *
 * A function to add footer text to the settings page of the plugin. Footer text contains plugin rating and donation links.
 * Note: Remove the rating link if the plugin doesn't have a WordPress.org directory listing yet. (i.e. before initial approval)
 *
 * @since 1.0
 * @refer https://codex.wordpress.org/Function_Reference/get_current_screen
 */
function review_sorted_footer_text($default) {
    
	// Retun default on non-plugin pages
	$screen = get_current_screen();
	if ( $screen->id !== "settings_page_reviews-sorted" ) {
		return $default;
	}
	
    $review_sorted_footer_text = sprintf( __( 'If you like this plugin, please leave me a <a href="%s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating to support continued development. Thanks a bunch!', 'reviews-sorted' ), 
			'https://wordpress.org/support/plugin/reviews-sorted/reviews/?rate=5#new-post' 
	);
	
	return $review_sorted_footer_text;
}
add_filter('admin_footer_text', 'review_sorted_footer_text');

/**
 * Admin footer version
 *
 * @since 1.0
 */
function review_sorted_footer_version($default) {
	
	// Retun default on non-plugin pages
	$screen = get_current_screen();
	if ( $screen->id !== 'settings_page_reviews-sorted' ) {
		return $default;
	}
	
	return 'Plugin version ' . REVIEWS_SORTED_VERSION_NUM;
}
add_filter( 'update_footer', 'review_sorted_footer_version', 11 );