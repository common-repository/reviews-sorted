<?php
class ReviewsSortedFeedback
{
    const STATUS_PENDING    = 'Pending';
    const STATUS_PUBLISHED  = 'Published';
    const STATUS_DECLINED   = 'Declined';
    const RATING            = '5.0';
    const LIMIT_PER_PAGE    = 20;
    const LIMIT_PER_PAGEL    = 999999;

    protected $fillable     = [];
    protected $casts = []; //questionnaire
    protected static $_instance = null;
    
    public static function get_instance($options = []){
        if(self::$_instance == null){
            self::$_instance = new self($options);
            return self::$_instance;
        } 
        else{
            return self::$_instance;
        } 
    }
    
    function __construct() {

    }

    public static function sendAutoResponder($review, $email_key = 'thank_you'){
        $settings  = ReviewsSortedCommon::get_options();
        $templates = ReviewsSortedCommon::email_notifications();
        
        if( !isset($templates[$email_key]) || $templates[$email_key]['active'] == 'no' || $templates[$email_key]['active'] == false ){
            return;
        }

        $params  = self::get_params($review);
        $email = isset($params['EMAIL']) ? $params['EMAIL'] : '';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $subject = self::apply_params($templates[$email_key]['subject'], $params);
        $body    = self::apply_params($templates[$email_key]['body'], $params);

        $headers    = array(
            'Content-Type: text/html; charset=UTF-8',
        );
        
        wp_mail( $email, $subject, apply_filters('the_content', $body), $headers); 
    }
    public static function sendToHeadOffice($review, $email_key = 'admin'){
        $settings  = ReviewsSortedCommon::get_options();
        $templates = ReviewsSortedCommon::email_notifications();
        // $email_key = 'admin';

        if( !isset($templates[$email_key]) || $templates[$email_key]['active'] == 'no' || $templates[$email_key]['active'] == false ){
            return;
        }

        $admin_emails = [];
        if( isset($settings['admin_emails']) && !empty($settings['admin_emails']) ) {
            $admin_emails = explode("\r\n", $settings['admin_emails']);
            foreach($admin_emails as $index => $email){
                $email = trim($email);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    unset($admin_emails[$index]);
                }
            }
        }

        if( count($admin_emails) == 0){
            $admin_emails = get_option( 'admin_email' );
        }

        $params  = self::get_params($review);
        $subject = self::apply_params($templates[$email_key]['subject'], $params);
        $body    = self::apply_params($templates[$email_key]['body'], $params);

        $headers    = array(
            'Content-Type: text/html; charset=UTF-8',
        );
        
        wp_mail( $admin_emails, $subject, apply_filters('the_content', $body), $headers);
    }

    public static function get_params($review){
        if( is_numeric($review) ){
            $review = self::get_review($review);
        }

        global $wp;

        $params = [
            'SITENAME'   => home_url(),
            'SITEURL'    => get_bloginfo('name'),
            'PAGEURL'    => home_url( $wp->request ),
            'FNAME'      => isset($review['authorfname']) ? $review['authorfname'] : '',
            'LNAME'      => isset($review['authorlname']) ? $review['authorlname'] : '',
            'EMAIL'      => isset($review['email']) ? $review['email'] : '',
            'PHONE'      => isset($review['phone']) ? $review['phone'] : '',
            'STARRATING' => isset($review['rating']) ? $review['rating'] : '',
            'RATING'     => isset($review['rating']) ? $review['rating'] : '',
            'RECOMMEND'  => isset($review['recommend']) ? $review['recommend'] : '',
            'FEEDBACK'   => isset($review['content']) ? $review['content'] : '',
            'STATE'      => isset($review['state']) ? $review['state'] : '',
            'REGION'     => isset($review['region']) ? $review['region'] : '',
            'BRANCH'     => isset($review['branch']) ? $review['branch'] : '',
            'STATUS'     => isset($review['status']) ? $review['status'] : '',
            'SERVICE'    => isset($review['service']) ? $review['service'] : '',
            'USERIP'     => isset($review['userip']) ? $review['userip'] : '',
            'CREATED'    => isset($review['created_at']) ? date("F d, Y g:i a", strtotime($review['created_at'])) : '',
            'UPDATED'    => isset($review['updated_at']) ? date("F d, Y g:i a", strtotime($review['updated_at'])) : '',
            'FORMDATA'   => '',
            'DATE'       => date("F d, Y"),
        ];

        $fields = ReviewsSortedCommon::get_form_default_fields();
        $fields['userip'] = ['label' => 'User IP', 'placeholder' => 'User IP'];
        $fields['created_at'] = ['label' => 'Created', 'placeholder' => 'Created'];
        $fields['updated_at'] = ['label' => 'Updated', 'placeholder' => 'Updated'];

        foreach ($fields as $key => $field) {
            if( $review[$key] && !empty($review[$key]) ){

                $formdata .= '<tr bgcolor="#EAF2FA">
                <td colspan="2">
                <font style="font-family:sans-serif;font-size:12px"><strong>'. $field['placeholder'] .'</strong></font>
                </td>
                </tr>
                <tr bgcolor="#FFFFFF">
                <td width="20">&nbsp;</td>
                <td>
                <font style="font-family:sans-serif;font-size:12px">'. $review[$key] .'</font>
                </td>
                </tr>';
            }
        } 

        $params['FORMDATA'] = '<table width="100%" border="0" cellpadding="5" cellspacing="0" bgcolor="#FFFFFF"><tbody>' . $formdata . '</tbody></table>';
        return apply_filters( ReviewsSortedCommon::PULGIN_SLUG . '_email_params', $params );
    }

    public static function apply_params($text, $params){
        foreach($params as $key => $val){
            $text = str_replace('*|'. $key .'|*', $val, $text);
        }

        return $text;
    }

    function query($status = []){
        global $wpdb;

        $status       = count($status) ? $status : [self::STATUS_PENDING, self::STATUS_PUBLISHED, self::STATUS_DECLINED];
        $status       = implode("','", $status);

        $paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
        $limit = self::LIMIT_PER_PAGE;
        $start = ($paged-1)*$limit;

        $sql     = "SELECT * FROM {$wpdb->prefix}reviews WHERE status IN ('{$status}') ORDER BY created_at DESC LIMIT {$start},{$limit} ";
        $results = $wpdb->get_results( $sql, OBJECT );

        return $results;
    }
    function custom_query($status = []){

        global $wpdb;
        
        $status       = implode("','", $status);
        $settings = get_option('reviews_sorted_settings', []);

        $paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
        $limit = self::LIMIT_PER_PAGEL;

        if (isset($settings['rating_month_label'])) {
            $months = strtolower($settings['rating_month_label']);
        }else{
            $months ='3 month';
        }
        
        $rating_status = self::RATING;
        $start_date = date('Y-m-d', strtotime('-'.$months));
        $end_date = date('Y-m-d');

        $start = ($paged-1)*$limit;

        $sql     = "SELECT * FROM {$wpdb->prefix}reviews WHERE rating IN ('{$rating_status}') AND created_at BETWEEN '{$start_date}' AND '{$end_date}'";
        //$sql     = "SELECT * FROM {$wpdb->prefix}reviews WHERE rating IN ('{$rating_status}') AND created_at BETWEEN '{$start_date}' AND '{$end_date}' AND status IN ('{$status}')";
        
        $results = $wpdb->get_results( $sql, OBJECT );

        return $results;
    }
    function get_total($status = []){
        global $wpdb;
        $status       = count($status) ? $status : [self::STATUS_PENDING, self::STATUS_PUBLISHED, self::STATUS_DECLINED];
        $status       = implode("','", $status);

        $totalReviews = $wpdb->get_var( "SELECT count(*) FROM {$wpdb->prefix}reviews WHERE status IN ('{$status}')" );
        $totalRatings = $wpdb->get_var( "SELECT SUM(rating) FROM {$wpdb->prefix}reviews WHERE status IN ('{$status}')" );
        

        return ['totalReviews' => $totalReviews, 'totalRatings' => $totalRatings];
    }
    function get_custom_total($status = []){
        global $wpdb;
        $settings = get_option('reviews_sorted_settings', []);

        // $status       = count($status) ? $status : [self::STATUS_PENDING, self::STATUS_PUBLISHED, self::STATUS_DECLINED];
        // $status       = implode("','", $status);

        $paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
        $limit = self::LIMIT_PER_PAGEL;
        $start = ($paged-1)*$limit;

        if (isset($settings['rating_month_label'])) {
            $months = strtolower($settings['rating_month_label']);
        }else{
            $months ='3 month';
        }

        $rating_status = self::RATING;
        $start_date = date('Y-m-d', strtotime('-'.$months));
        $end_date = date('Y-m-d');

        $totalReviews = $wpdb->get_var( "SELECT count(*) FROM {$wpdb->prefix}reviews WHERE rating IN ('{$rating_status}') AND created_at BETWEEN '{$start_date}' AND '{$end_date}'" );

        // $totalRatingss = $wpdb->get_var( "SELECT SUM(rating) FROM {$wpdb->prefix}reviews WHERE rating IN ('{$rating_status}') AND created_at BETWEEN '{$start_date}' AND '{$end_date}'" );
        // $totalRatings = number_format($totalRatingss);
        
        //$totalReviews = $wpdb->get_var( "SELECT count(*) FROM {$wpdb->prefix}reviews WHERE created_at BETWEEN '{$start_date}' AND '{$end_date}'" );
        
        $totalRatings = $wpdb->get_var( "SELECT SUM(rating) FROM {$wpdb->prefix}reviews WHERE created_at BETWEEN '{$start_date}' AND '{$end_date}'" );

        return ['totalReviews' => $totalReviews, 'totalRatings' => $totalRatings];
    }

    public static function get_review($review_id){
        global $wpdb;

        $review = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}reviews WHERE id = %d", $review_id) , ARRAY_A );
        
        $form_fields = ReviewsSortedCommon::get_form_default_fields();
        foreach($form_fields as $field_key => $field_data){
            if( !isset($review[$field_key]) ){
                $review[$field_key] = '';
            }
        }
        return $review;
    }

    function paginate($total_items, $paged, $per_page = 10){
        $number_of_page = ceil ($total_items / $per_page);  

        $big = 999999999; // need an unlikely integer

        $page_links = paginate_links( array(
            'base'    => add_query_arg( 'paged', '%#%' ),
            'format'  => '?paged=%#%',
            'current' => $paged,
            'total'   => $number_of_page
        ) );

        if ( $page_links ) {
            $page_links = '<div class="tablenav" style=" display: flex; font-size: 16px; justify-content: flex-start; "><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
        }

        return $page_links;
    }

    function auto_publish($review){
        $settings = get_option('reviews_sorted_settings', []);
        if( !isset($settings['testimonial_min_length']) ){
            $settings['testimonial_min_length'] = 15;
        }

        if( !isset($settings['testimonial_min_rating']) ){
            $settings['testimonial_min_rating'] = 4;
        }

        $valid = false;
        
        if( !isset($settings['testimonial_auto_publish']) || $settings['testimonial_auto_publish'] == 'no'){
            return $valid;
        }

        if( !isset($review['content']) || strlen($review['content']) < $settings['testimonial_min_length']){
            return $valid;
        }

        if( !isset($review['rating']) || intval($review['rating']) < $settings['testimonial_min_rating']){
            return $valid;
        }

        $valid = true;
        return apply_filters( 'reviews_sorted_testimonial_auto_publish', $valid );
    }

    function trash_review($review_id){
        global $wpdb;

        $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}reviews WHERE id = %d", $review_id));
    }

    public function insert($review) 
    {
        global $wpdb;
        
        $status = $this->auto_publish($review) ? self::STATUS_PUBLISHED : self::STATUS_PENDING;

        $now = date('Y-m-d H:i:s');

        $data = [
            'authorfname'   => isset($review['authorfname'])    ? $review['authorfname']    : '',
            'authorlname'   => isset($review['authorlname'])    ? $review['authorlname']    : '',
            'state'         => isset($review['state'])      ? $review['state']      : '',
            'phone'         => isset($review['phone'])      ? $review['phone']      : '',
            'email'         => isset($review['email'])      ? $review['email']      : '',
            'region'        => isset($review['region'])     ? $review['region']     : '',
            'branch'        => isset($review['branch'])     ? $review['branch']     : '',
            'content'       => isset($review['content'])    ? $review['content']    : '',
            'rating'        => isset($review['rating'])     ? intval($review['rating']) : 0,
            'recommend'     => isset($review['recommend'])  ? $review['recommend']  : 'yes',
            'questionnaire' => isset($review['questionnaire']) ? $review['questionnaire'] : '',
            'service'       => isset($review['service']) ? $review['service'] : '',
            'status'        => $status,
            'userip'        => $this->get_the_user_ip(),
            'created_at'    => $now,
            'updated_at'    => $now,
        ];

        $wpdb->insert(
            $wpdb->prefix . 'reviews',
            $data,
            array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ),
        );

        return $wpdb->insert_id;
    }

    public function update($review) {

        global $wpdb;
        
        $data = [
            'authorfname'   => isset($review['authorfname'])    ? $review['authorfname']    : '',
            'authorlname'   => isset($review['authorlname'])    ? $review['authorlname']    : '',
            'email'         => isset($review['email'])      ? $review['email']      : '',
            'phone'         => isset($review['phone'])      ? $review['phone']      : '',
            'rating'        => isset($review['rating'])     ? intval($review['rating']) : 0,
            'recommend'     => isset($review['recommend'])  ? $review['recommend']  : 'yes',
            'content'       => isset($review['content'])    ? $review['content']    : '',
            'status'        => isset($review['status'])     ? $review['status'] : self::STATUS_PENDING,
            'updated_at'    => isset($review['updated_at']) ? $review['updated_at'] : '',
            'service'       => isset($review['service']) ? $review['service'] : '',
        ];

        return $wpdb->update(
            $wpdb->prefix . 'reviews',
            $data,
            ['id' => $review['id']],
            array( '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s'),
        );
    }

    function get_the_user_ip() {
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            //check ip from share internet
            $ip = sanitize_text_field($_SERVER['HTTP_CLIENT_IP']);
        } 
        elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            //to check ip is pass from proxy
            $ip = sanitize_text_field($_SERVER['HTTP_X_FORWARDED_FOR']);
        } 
        else {
            $ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
        }
        
        return apply_filters( ReviewsSortedCommon::PULGIN_SLUG . '_get_ip', $ip );
    }
}