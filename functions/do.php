<?php
/**
 * Operations of the plugin are included here. 
 *
 * @since 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class ReviewsSortedFrontend {

    protected static $_instance = null;

    protected $options      = [];
    protected $templates    = [];
    protected $security_code  = 'rs_reviews-form';

    public static function get_instance($options = []){
        if(self::$_instance == null){
            self::$_instance = new self($options);
            return self::$_instance;
        } 
        else{
            return self::$_instance;
        } 
    }

    public function __construct()
    {
        $this->setup();
    }

    public function setup()
    {

        // setup ajax
        add_action('wp_ajax_rs_reviews_submit', [$this, 'reviews_submit']);
        add_action('wp_ajax_nopriv_rs_reviews_submit', [$this, 'reviews_submit']);
        
        add_action( 'wp_enqueue_scripts', [$this, 'enqueue_scripts'] );

        // setup shortcode
        add_shortcode('reviews-form', [$this, 'reviews_form']);
        add_shortcode('reviews-slider', [$this, 'reviews_slider']);
        add_shortcode('reviews-average', [$this, 'reviews_average']);
        add_shortcode('reviews-carousel', [$this, 'reviews_carousel']);
        add_shortcode('reviews-grid', [$this, 'reviews_grid']);
        add_shortcode('reviews-list', [$this, 'reviews_list']);
        add_shortcode('reviews-masonry', [$this, 'reviews_masonry']);
        add_shortcode('reviews-testimonials', [$this, 'reviews_testimonials']);
        add_action('admin_enqueue_scripts', [$this, 'my_plugin_enqueue_scripts']);

        add_action('wp_head', [$this, 'reviews_style']);

    }

    /**
     *  Loading admin side scripts
     * */
    
     function my_plugin_enqueue_scripts() {
      wp_enqueue_script('jquery');
      wp_enqueue_media();
    }

    function enqueue_scripts(){

        // SwiperJS
        wp_enqueue_style( 'swiper-css', REVIEWS_SORTED_PLUGIN_URL . 'includes/swiper/swiper-bundle.min.css' );
        wp_enqueue_script( 'swiper-js', REVIEWS_SORTED_PLUGIN_URL . 'includes/swiper/swiper-bundle.min.js' );

        wp_enqueue_style( 'reviews-sorted_css', REVIEWS_SORTED_PLUGIN_URL . 'public/frontend-styles.css' );
        wp_enqueue_script( 'reviews-sorted_js', REVIEWS_SORTED_PLUGIN_URL . 'public/frontend-scripts.js', [], '', true );
        wp_localize_script( 'reviews-sorted_js', 'RS_PLUGIN_VARS',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),                
            )
        );
    }

    function reviews_form($atts)
    {

        $settings  = ReviewsSortedCommon::get_options();
        $fields    = ReviewsSortedCommon::get_form_default_fields();
        
        foreach($fields as $field_key => $field_data){
            if( $settings['form_hidden_placeholder'] == 'yes' ){
                $fields[$field_key]['placeholder'] = '';
            }
        }

        $settings['form_fields'] = $fields;
        $settings['settings']    = $settings;
        return ReviewsSortedCommon::get_template( 'reviews-form.php', $settings);
    }
    
    function reviews_submit()
    {

        if ( !isset( $_REQUEST['security-code'] ) || !wp_verify_nonce( $_REQUEST['security-code'], $this->security_code ) ) {

            wp_send_json_error();
            
        } else {

            // process form data
            foreach ($_REQUEST as &$param) {
                $param = stripslashes($param);
            }
            
            $RS_Reviews = new ReviewsSortedFeedback();     
            $review_id = $RS_Reviews->insert($_REQUEST);

            if($review_id){

                $RS_Reviews->sendAutoResponder($review_id);
                $RS_Reviews->sendToHeadOffice($review_id);

                if($_REQUEST['rating'] == 1){
                    $RS_Reviews->sendToHeadOffice($review_id, '1_star_review');
                }
                
                $_REQUEST['id'] = $review_id;
                ReviewsSortedAPI::import_review($_REQUEST);

                do_action('reviews-sorted_after_review_insert');
            }
            else{
                wp_send_json_error();
            }

            wp_send_json_success( __( 'Thanks for reporting!', 'reviews-sorted' ) );
        }
    }

    function reviews_slider($atts){

        $options = shortcode_atts( array(
            'space'         => 20,
            'speed'         => 500,
            'loop'          => true,
            'autoplay'      => true,
            'delay'         => 5000,
            'desktop'       => 2,
            'tablet'        => 1,
            'mobile'        => 1,
            'arrows'        => true,
            'dots'          => false,
            'equalHeight'   => true,
            'layout'        => '', // 1 | 2 | 3
        ), $atts );

        $RS_Review  = new ReviewsSortedFeedback();
        
        $settings   = ReviewsSortedCommon::get_options();
        $reviews    = $RS_Review->custom_query([$RS_Review::STATUS_PUBLISHED]);
        $data       = $RS_Review->get_custom_total([$RS_Review::STATUS_PUBLISHED]);
        

        $template = 'reviews-slider-'. $options['layout'] .'.php';
        if( !file_exists(REVIEWS_SORTED_PLUGIN_DIR . '/templates/' . $template) ){
            $template = 'reviews-slider.php';
        }

        return ReviewsSortedCommon::get_template( $template, ['settings' => $settings, 'reviews' => $reviews, 'options' => $options, 'data' => $data] );
    }

    function reviews_average($atts){
        $RS_Review  = new ReviewsSortedFeedback();  
        
        $settings   = ReviewsSortedCommon::get_options();    
        $data       = $RS_Review->get_custom_total([$RS_Review::STATUS_PUBLISHED]);
        $reviews    = $RS_Review->custom_query([$RS_Review::STATUS_PUBLISHED]);
        
        return ReviewsSortedCommon::get_template( 'reviews-average.php', ['settings' => $settings, 'reviews' => $reviews,'data' => $data, 'atts'=> $atts] );
    }

    function reviews_carousel($atts){

        $options = shortcode_atts( array(
            'space'         => 20,
            'speed'         => 500,
            'loop'          => true,
            'autoplay'      => true,
            'delay'         => 5000,
            'desktop'       => 1,
            'tablet'        => 1,
            'mobile'        => 1,
            'arrows'        => true,
            'dots'          => false,
            'equalHeight'   => false
        ), $atts );

        $RS_Review  = new ReviewsSortedFeedback();  
        
        $settings   = ReviewsSortedCommon::get_options();    
        $reviews    = $RS_Review->query([$RS_Review::STATUS_PUBLISHED]);
        
        return ReviewsSortedCommon::get_template( 'reviews-carousel.php', ['settings' => $settings, 'reviews' => $reviews, 'options' => $options] );
    }

    function reviews_grid ($atts){

        $options = shortcode_atts( array(
            'column'    => 4,
            'space'     => 10
        ), $atts );

        $RS_Review  = new ReviewsSortedFeedback();  
        
        $settings   = ReviewsSortedCommon::get_options();    
        $reviews    = $RS_Review->query([$RS_Review::STATUS_PUBLISHED]);
        
        return ReviewsSortedCommon::get_template( 'reviews-grid.php', ['settings' => $settings, 'reviews' => $reviews, 'options' => $options] );
    }

    function reviews_list ($atts){

        $options = shortcode_atts( array(
            'space'     => 20
        ), $atts );

        $RS_Review  = new ReviewsSortedFeedback();  
        
        $settings   = ReviewsSortedCommon::get_options();    
        $reviews    = $RS_Review->query([$RS_Review::STATUS_PUBLISHED]);
        
        return ReviewsSortedCommon::get_template( 'reviews-list.php', ['settings' => $settings, 'reviews' => $reviews, 'options' => $options] );
    }

    function reviews_masonry ($atts){

        $options = shortcode_atts( array(
            'space'     => 20
        ), $atts );

        $RS_Review  = new ReviewsSortedFeedback();  
        
        $settings   = ReviewsSortedCommon::get_options();
        $reviews    = $RS_Review->query([$RS_Review::STATUS_PUBLISHED]);
        
        return ReviewsSortedCommon::get_template( 'reviews-masonry.php', ['settings' => $settings, 'reviews' => $reviews, 'options' => $options] );
    }

    function reviews_testimonials ($atts){

        $options = shortcode_atts( array(
            'space'     => 20,
            'layout'    => '' // 1 | 2 | 3
        ), $atts );

        $RS_Review  = new ReviewsSortedFeedback();
        
        $settings   = ReviewsSortedCommon::get_options();
        $reviews    = $RS_Review->query([$RS_Review::STATUS_PUBLISHED]);
        $data       = $RS_Review->get_total([$RS_Review::STATUS_PUBLISHED]);
        

        $template = 'reviews-testimonials-'. $options['layout'] .'.php';
        if( !file_exists(REVIEWS_SORTED_PLUGIN_DIR . '/templates/' . $template) ){
            $template = 'reviews-testimonials.php';
        }

        return ReviewsSortedCommon::get_template( $template, ['settings' => $settings, 'reviews' => $reviews, 'options' => $options, 'data' => $data] );
    }

    function reviews_style(){

        $settings   = ReviewsSortedCommon::get_options();
        $icons = [];
        for($i = 1; $i<=5; $i++){
            $icon_key = 'icon_for_'. $i .'_star';
            $row_key = 'icon_for_'. $i .'_star';
            $image_id = isset($settings[$icon_key]) ? intval($settings[$row_key]) : 0;
            
            if( $image = wp_get_attachment_image_src( $image_id ) ) {                
                $icons[] = 'url(' . esc_url($image[0]) . ')';
            }
            else{
                $icons[] = 'url(' . REVIEWS_SORTED_PLUGIN_URL . 'assets/star.png)';
            }

            $bgicons = implode(',', $icons);
        }
        ?>
        <style type="text/css">
            html,
            .reviews-sorted{
                <?php if(isset($settings['star_color'])): ?>
                    --star-background: <?php echo esc_html($settings['star_color']); ?>;
                <?php endif; ?>
            }
            .reviews-sorted .rs-bg-rating{
                background-color: var(--star-background);
                background-size: 35px;
                background-image: linear-gradient(90deg, var(--star-background) 97%, rgb(255 255 255) 0%);
            }
            .reviews-sorted .rs-bg-rating img {
                visibility: hidden;
            }
            .reviews-sorted .rs-bg-rating:before{
                background-image: <?php echo esc_html($bgicons); ?>;
                -webkit-background-clip: unset;
            }

            .reviews-sorted .rs-custom-icons.rs-rating::before{display:none;} 

            .reviews-sorted .rs-bg-rating:after {
                background: #ccc;
                -webkit-filter: grayscale(100%);
                filter: grayscale(100%);
            }

            .star-icons-gray, .star-icons{
               display: flex;
               column-gap: 1px;
               align-items: center;
               justify-content: center;
           }

           .star-icons-gray span, .star-icons span {
            background-color: var(--star-background);
        }
        
        .star-icons-gray img, span.star-icons img{
            width: 34px;
            padding: 3px 5px 0px 5px;
            height: 28px;
        }

        .star-icons-gray img, span.star-icons img{
            filter: grayscale(100%);
        }

        .star-icons-gray .rs-star-active img, span.star-icons .rs-star-active img{filter: grayscale(0);}
        .rs-rating.rs-custom-icons {
            font-size: 25px !important;position: relative;
        }
        .reviews-average .star-icons {
            position: absolute;
            top: 0;
            width: calc(var(--rating) / 5 * 100%);
            overflow: hidden;
            padding-left: var(--left-spacing);
            transform: translateX(var(--left-pspacing));
            left: 50%;
        }

    </style>
    <?php
}

}

ReviewsSortedFrontend::get_instance();