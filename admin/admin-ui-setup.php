<?php
/**
 * Admin setup for the plugin
 *
 * @since 1.0
 * @function	review_sorted_add_menu_links()		Add admin menu pages
 * @function	review_sorted_get_settings()		Get settings from database
 */

// Exit if accessed directly
if ( ! defined('ABSPATH') ) exit; 

/**
 * Add admin menu pages
 *
 * @since 1.0
 * @refer https://developer.wordpress.org/plugins/administration-menus/
 * @refer Top-Level Menus: 
   add_menu_page( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', string $icon_url = '', int $position = null );
 * 
 * @refer Sub-Menus
   add_submenu_page( string $parent_slug, string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '');
 */
   function review_sorted_add_menu_links() {

       add_menu_page ( __('Reviews Sorted','reviews-sorted'), __('Reviews Sorted','reviews-sorted'), 'update_core', 'reviews-sorted','review_sorted_admin_interface_render','dashicons-star-filled'  );
       
       add_submenu_page ( 'reviews-sorted', __('Reviews List','reviews-sorted'), __('Reviews List','reviews-sorted'), 'update_core', 'reviews-sorted-reviews-list','review_sorted_admin_reviews_list_interface_render'  );
       
       add_submenu_page ( 'reviews-sorted', __('Local Business Details','reviews-sorted'), __('Local Business Details','reviews-sorted'), 'update_core', 'reviews-sorted-business-details','review_sorted_admin_interface_render'  );

       add_submenu_page ( 'reviews-sorted', __('Review Settings','reviews-sorted'), __('Review Settings','reviews-sorted'), 'update_core', 'reviews-sorted-testimonial-settings','review_sorted_admin_interface_render'  );
       
       add_submenu_page ( 'reviews-sorted', __('Form Settings','reviews-sorted'), __('Form Settings','reviews-sorted'), 'update_core', 'reviews-sorted-form-settings','review_sorted_admin_interface_render'  );
       
       add_submenu_page ( 'reviews-sorted', __('Email notifications','reviews-sorted'), __('Email notifications','reviews-sorted'), 'update_core', 'reviews-sorted-email-templates','review_sorted_admin_interface_render'  );
       
       add_submenu_page ( 'reviews-sorted', __('Premium Version','reviews-sorted'), __('Premium Version','reviews-sorted'), 'update_core', 'reviews-sorted-premium-version','review_sorted_admin_interface_render'  );
   }
   add_action( 'admin_menu', 'review_sorted_add_menu_links' );

   add_action( 'admin_post', 'review_sorted_save_settings' );
   function review_sorted_save_settings(){
    
    // First, validate the nonce and verify the user as permission to save.
    if ( ! isset( $_POST['review_sorted-settings-nonce'] ) ) { // Input var okay.
        return;
    }

    $nonce_text     = sanitize_text_field($_POST['review_sorted-settings-nonce']);
    $is_valid_nonce = wp_verify_nonce( wp_unslash( $nonce_text ), 'review_sorted-settings-save' );

    if ( ! ( $is_valid_nonce && current_user_can( 'manage_options' ) ) ) {
        return;
    }    

    
    /**
     * Validates the incoming nonce value, verifies the current user has permission to save the value from the options page and saves the option to the atabase.
     */
    if( isset($_POST['reviews_sorted_settings']) && count($_POST['reviews_sorted_settings']) ){
        $settings = get_option('reviews_sorted_settings', []);

        $new_settings = sanitize_map_deep( $_POST['reviews_sorted_settings'] );

        if( isset($_POST['page']) && $_POST['page'] == 'reviews-sorted-form-settings' ){
            if( !isset($new_settings['form_hidden_label']) ){
                $new_settings['form_hidden_label'] = 'no';
            }
            if( !isset($new_settings['form_hidden_placeholder']) ){
                $new_settings['form_hidden_placeholder'] = 'no';
            }    
            if( !isset($new_settings['hide_first_name']) ){
                $new_settings['hide_first_name'] = 'no';
            }    
            if( !isset($new_settings['hide_last_name']) ){
                $new_settings['hide_last_name'] = 'no';
            }    
            if( !isset($new_settings['hide_service_provided']) ){
                $new_settings['hide_service_provided'] = 'no';
            }    
            if( !isset($new_settings['hide_email']) ){
                $new_settings['hide_email'] = 'no';
            }    
            if( !isset($new_settings['hide_phone_number']) ){
                $new_settings['hide_phone_number'] = 'no';
            }    
            if( !isset($new_settings['hide_rating']) ){
                $new_settings['hide_rating'] = 'no';
            }    
            if( !isset($new_settings['hide_recommend']) ){
                $new_settings['hide_recommend'] = 'no';
            }   
            if( !isset($new_settings['hide_feedback']) ){
                $new_settings['hide_feedback'] = 'no';
            }
        }

        $new_data = array_merge($settings, $new_settings);
        update_option('reviews_sorted_settings', $new_data, false);
    }

    if( isset($_POST['reviews_sorted_email']) && count($_POST['reviews_sorted_email']) ){
        $templates = get_option('reviews_sorted_email_notifications', []);

        $new_templates = map_deep( $_POST['reviews_sorted_email'], 'trim' );

        $new_data = array_merge($templates, $new_templates);
        update_option('reviews_sorted_email_notifications', $new_data, false);
    }

    if( isset($_POST['reviewupdate']) && count($_POST['reviewupdate']) ){
        $reviewupdate = sanitize_map_deep( $_POST['reviewupdate'] );

        $now = date('Y-m-d H:i:s');
        $reviewupdate['updated_at'] = $now;


        $review = new ReviewsSortedFeedback();  
        $review->update($reviewupdate);

        ReviewsSortedAPI::import_review($reviewupdate);
    }  

    /**
     * Redirect to the page from which we came (which should always be the admin page. If the referred isn't set, then we redirect the user to the login page.
     */
    // To make the Coding Standards happy, we have to initialize this.
    if ( ! isset( $_POST['_wp_http_referer'] ) ) { // Input var okay.
        $_POST['_wp_http_referer'] = wp_login_url();
    }
    
    // Sanitize the value of the $_POST collection for the Coding Standards.
    $url = sanitize_text_field(
        wp_unslash( $_POST['_wp_http_referer'] ) // Input var okay.
    );
    
    // Finally, redirect back to the admin page.
    wp_safe_redirect( add_query_arg( array( 'settings-updated' => 'done' ), $url ) );
    exit;
}

add_action( 'wp_ajax_reviews_sorted_verify_key', 'ajax_reviews_sorted_verify_key_callback' );
//add_action( 'wp_ajax_nopriv_reviews_sorted_verify_key', 'ajax_reviews_sorted_verify_key_callback' );
function ajax_reviews_sorted_verify_key_callback(){    
    $licence    = isset($_REQUEST['licence']) && !empty($_REQUEST['licence']) ? sanitize_text_field($_REQUEST['licence']) : '';
    $deactivate = isset($_REQUEST['deactivate']) ? sanitize_text_field($_REQUEST['deactivate']) : false;

    if($deactivate){        
        update_option('reviews_sorted_verify_key', '', false);
        wp_send_json_success();
    }

    if( empty($licence) ){
        wp_send_json_error('Please enter a value!');
    }


    $data = array("activation_key" => $licence);
    $data_string = json_encode($data);

    $response = wp_remote_post( 'https://sys.reviewssorted.com/api/v1/account/check', array(
        'body'      => $data,
        'headers'   => array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string)
        )        
    ) );
    
    $result = json_decode($response["body"]);
    if($result == null){
        wp_send_json_error('Server error, malformed result');
    }

    if( isset($result->error) ){
        wp_send_json_error($result->error);
    }

    update_option('reviews_sorted_verify_key', $licence, false);
    update_option('reviews_sorted_verify_data', json_encode($result), false);

    wp_send_json_success();
}

function sanitize_map_deep( $value, $field_name = '' ) {
    if ( is_array( $value ) ) {
        foreach ( $value as $index => $item ) {
            $value[ $index ] = sanitize_map_deep( $item, $index );
        }
    } elseif ( is_object( $value ) ) {
        $object_vars = get_object_vars( $value );
        foreach ( $object_vars as $property_name => $property_value ) {
            $value->$property_name = sanitize_map_deep( $property_value, $property_name );
        }
    } else {
        switch ($field_name) {
            case 'email_sender_address':
            case 'email':
            $value = sanitize_email($value);
            break;
            case 'admin_emails':
            $value = sanitize_textarea_field($value);
            break;
            case 'star_color':
            $value = sanitize_hex_color($value);
            break;
            case 'form_redirect_page':
            case 'email_header_image':
            case 'business_icon':
            $value = sanitize_url($value);
            break;
            case 'authorfname_label':
            case 'authorlname_label':
            case 'service_label':
            case 'email_label':
            case 'phone_label':
            case 'rating_label':
            case 'recommend_label':
            case 'content_label':
            case 'form_main_heading':
            case 'form_sub_heading':
            case 'email_footer_text':
            case 'content':
            case 'body':
            $value = wp_kses($value, [
                'a' => [
                    'href'  => [],  
                    'title' => [],
                    'class' => [],                        
                ],
                'img' => [
                    'src'   => [],
                    'class' => [],
                    'title' => [],
                    'alt'   => []
                ],
                'p' => [
                    'class' => []
                ],
                'span' => [
                    'class' => []
                ],
                'br' => [],
                'em' => [],
                'strong' => [
                    'class' => []
                ],
            ]);
            break;
            
            default:
            $value = sanitize_text_field($value);
            break;
        }
    }
    
    return $value;
}
