<?php
class ReviewsSortedCommon
{
	
    CONST PULGIN_SLUG = 'reviews-sorted';

    public static function get_options()
    {
        $settings = get_option('reviews_sorted_settings', []);

        self::get_default_business_details_settings($settings);
        self::get_default_testimonial_setting($settings);
        self::get_default_form_setting($settings);
        self::get_default_email_setting($settings);

        return $settings;
    }

    public static function load_template( $template_name, $template_path = '', $default_path = '' ) 
    {

        // Set variable to search in reviews-sorted folder of theme.
        if ( ! $template_path ) :
            $template_path = REVIEWS_SORTED_PLUGIN;
        endif;

        // Set default plugin templates path.
        if ( ! $default_path ) :
            $default_path = REVIEWS_SORTED_PLUGIN_DIR . 'templates/'; // Path to the template folder
        endif;

        // Search template file in theme folder.
        $template = locate_template( array(
            $template_path . $template_name,
            $template_name
        ) );

        // Get plugins template file.
        if ( ! $template ) :
            $template = $default_path . $template_name;
        endif;

        return $template;

    }

    public static function get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) 
    {

        if ( is_array( $args ) && isset( $args ) ) :
            extract( $args );
        endif;

        $template_file = self::load_template( $template_name, $template_path, $default_path );
        $template_file = apply_filters( self::PULGIN_SLUG . '_get_template', $template_file, $args, $template_path, $default_path);

        if ( ! file_exists( $template_file ) ) :
            _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file ), '1.0.0' );
            return;
        endif;

        ob_start();
        include $template_file;
        return ob_get_clean();
    }    

    public static function get_default_business_details_settings(&$settings = [])
    {
    	$settings = isset($settings) && is_array($settings) ? $settings : [];
    	$default_settings = [
    		'business_name'			=> get_bloginfo('name'),
    		'business_icon'			=> get_site_icon_url(),
    		'business_address'		=> '',
    		'business_phone'		=> '',
    		'business_priceRange'	=> '',
    	];

    	$settings = array_merge($default_settings, $settings);

    	return $settings;
    }

    public static function get_default_testimonial_setting(&$settings = [])
    {
    	$settings = isset($settings) && is_array($settings) ? $settings : [];
    	$default_settings = [
    		'testimonial_character_length' 	=> 180, 
			'rating_month_label' 			=> '6 Months',
			'testimonial_auto_publish' 		=> 'no',
			'testimonial_min_length' 		=> 15,
			'testimonial_min_rating' 		=> 4,
    	];

		$default_icons = array(
			'icon_for_1_star' => REVIEWS_SORTED_PLUGIN_DIR. 'assets/images/rs-icon-1-1.png',
			'icon_for_2_star' => REVIEWS_SORTED_PLUGIN_DIR . 'assets/images/rs-icon-2-1.png',
			'icon_for_3_star' => REVIEWS_SORTED_PLUGIN_DIR . 'assets/images/rs-icon-3-1.png',
			'icon_for_4_star' => REVIEWS_SORTED_PLUGIN_DIR . 'assets/images/rs-icon-4-1.png',
			'icon_for_5_star' => REVIEWS_SORTED_PLUGIN_DIR . 'assets/images/rs-icon-5-1.png',
		);
		foreach($default_icons as $key=> $img_url) {
			$file_name = pathinfo($img_url, PATHINFO_FILENAME);
			$attachment_id = get_attachment_id_by_name(basename($file_name));
			if(!is_null($attachment_id)){
				$default_settings[$key] = $attachment_id;
			}
		}
    	$settings = array_merge($default_settings, $settings);

    	return $settings;
    }

    public static function get_form_default_fields()
    {
    	$site_title = get_bloginfo('name');
    	$form_fields = [
    		'authorfname'   => ['label' => 'First Name', 'required' => true, 'placeholder' => 'First Name'],
            'authorlname'   => ['label' => 'Last Name', 'required' => true, 'placeholder' => 'Last Name'],
            'service'       => ['label' => 'Service Provided', 'required' => false, 'placeholder' => 'Service Provided'],
            'email'         => ['label' => 'Email', 'required' => true, 'placeholder' => 'Email', 'type' => 'email'],
            'phone'         => ['label' => 'Phone', 'required' => false, 'placeholder' => 'Phone Number'],
            'rating'        => ['label' => 'Rating', 'required' => true, 'placeholder' => 'Rating', 'type' => 'select', 
            'options'      =>  [5 => '5 Stars', 4 => '4 Stars', 3 => '3 Stars', 2 => '2 Stars', 1 => '1 Star']],
            'recommend'     => ['label' => 'Would you recommend <strong>'.$site_title.'</strong> to your family and friends?', 'required' => true, 'placeholder' => 'Recommend', 'fullwidth' => true, 'type' => 'select', 'options' => ['yes' => 'Yes', 'no' => 'No']],
            'content'       => ['label' => 'Feedback', 'required' => false, 'placeholder' => 'Feedback', 'fullwidth' => true, 'type' => 'textarea'],
    	];

    	return apply_filters(self::PULGIN_SLUG . '_form_fields', $form_fields);

    	return $form_fields;
    }
    
    public static function get_default_form_setting(&$settings = [])
    {
    	$settings = isset($settings) && is_array($settings) ? $settings : [];
    	$default_settings = [
    		'form_redirect_page' => home_url('/submit-a-review/thank-you/'),
    		'form_main_heading'	 => 'Submit your feedback!',
    		'form_sub_heading'	 => 'Please share your experience with us.',
    		'form_hidden_label'	 => 'no',
    		'form_hidden_placeholder' => 'no',
    	];

    	$form_fields = self::get_form_default_fields();

    	foreach($form_fields as $field_key => $field_data){
			$default_settings[$field_key . '_label'] 		= $field_data['label'];
			$default_settings[$field_key . '_placeholder'] 	= $field_data['placeholder'];
		}

		$settings = array_merge($default_settings, $settings);

    	return $settings;
    }

    public static function email_notifications(){
    	$default_templates = [
    		'admin' => [
				'active' 	=> false,
				'title' 	=> 'Admin notification',
				'subject' 	=> '1 Star Review',
				'body' 		=> '',
			],
			'thank_you' => [
				'active' 	=> false,
				'title' 	=> 'Thank You For Your Review',
				'subject' 	=> 'Thank You For Your Review',
				'body' 		=> '',
			],
			'1_star_review' => [
				'active' 	=> true,
				'title' 	=> '1 Star Review',
				'subject' 	=> '1 Star Review',
				'body' 		=> '',
			]
    	];

    	foreach ($default_templates as $key => $value) {
    		$file = REVIEWS_SORTED_PLUGIN_DIR . 'templates/emails/plain/' . $key . '.txt';
    		if ( file_exists( $file ) ){
    			$default_templates[$key]['body'] = file_get_contents($file);
    		}
    	}

    	$templates = get_option('reviews_sorted_email_notifications', []);
    	$templates = array_merge($default_templates, $templates);
        unset($templates['thank_you']);

    	return apply_filters(self::PULGIN_SLUG . '_email_notifications', $templates);
    }

    public static function get_default_email_setting(&$settings = [])
    {
    	$settings = isset($settings) && is_array($settings) ? $settings : [];
    	$default_settings = [
    		'email_sender_name'		=> get_bloginfo('name'),
			'email_sender_address'	=> get_bloginfo('admin_email'),
			'admin_emails'			=> get_bloginfo('admin_email'),
			'email_header_image'	=> '',
			'email_footer_text'		=> '',
		];

		$settings = array_merge($default_settings, $settings);

    	return $settings;
    }
}
