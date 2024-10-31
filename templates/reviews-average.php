<?php
    if(!isset($data['totalRatings']) || !isset($data['totalReviews'])){
        return;
    }

    if(intval($data['totalReviews']) == 0){
        return;
    }
    $is_layout_two= (isset($atts) && (isset($atts['layout']) && $atts['layout'] == 2)) ? true : false;
    $average = intval($data['totalRatings']) / intval($data['totalReviews']);
    $average = number_format($average, 1);
?>
<div class="reviews-average reviews-sorted">
    <div class="inner">

        <div class="site-name">
            <h3><?php _e('Our Customers Rating', 'reviews-sorted'); ?></h3>
            <div class="rs-rating <?= $is_layout_two ? 'rs-custom-icons' : '';?>" style="<?php printf('--rating: %s', esc_attr($average) ); ?>; --left-spacing: <?= (($average / 5 * 100) );?>px; --left-pspacing: -<?= (($average / 5 * 100) ) + 3;?>%">
            <?php if($is_layout_two) { ?>
            <span class="star-icons-gray">    
                <?php

                for($i=1; $i<= 5; $i++) {
                    $icon_key = 'icon_for_'. $i .'_star';
                    $row_key = 'icon_for_'. $i .'_star';
                    $image_id = isset($settings[$icon_key]) ? intval($settings[$row_key]) : 0;
                    $url  = REVIEWS_SORTED_PLUGIN_URL . 'assets/star.png';            
                    if( $image = wp_get_attachment_image_src( $image_id ) ) {                
                        $url = esc_url($image[0]);
                    }
                    $rating_active = $i <= $average  ? 'rs-star-active' : '';
                    echo sprintf('<span class="rs-start-icon-%s %s"><img src="%s"></span>', $i, $rating_active, $url);
                }
                ?>
            </span>
            <span class="star-icons" style="display:none">    
                <?php

                for($i=1; $i<= 5; $i++) {
                    $icon_key = 'icon_for_'. $i .'_star';
                    $row_key = 'icon_for_'. $i .'_star';
                    $image_id = isset($settings[$icon_key]) ? intval($settings[$row_key]) : 0;
                    $url  = REVIEWS_SORTED_PLUGIN_URL . 'assets/star.png';            
                    if( $image = wp_get_attachment_image_src( $image_id ) ) {                
                        $url = esc_url($image[0]);
                    }
                    $rating_active = $i <= $average  ? 'rs-star-active' : '';
                    echo sprintf('<span class="rs-start-icon-%s %s"><img src="%s"></span>', $i, 'rs-star-active', $url);
                }
                ?>
            </span>
        <?php } ?>
			</div>
            <div class="reviews-details">
                
                <div class="content">
                 <?php 
               $months = strtolower($settings['rating_month_label']);
                if ($months == 'all') {
                     _e( sprintf('<p>Based on <strong>%s</strong> %s reviews, including %s 5 star reviews </p>', 
                            esc_html($data['totalReviews']), 
                            esc_html(strtolower($settings['rating_month_label'])),
                            esc_html($FiveStarPer.'%')
                            
                        ), 
                        'reviews-sorted'
                    ); 
                }else{
                    _e( sprintf('<p>Based on <strong>%s</strong> reviews over the last %s, including %s 5 star reviews </p>', 
                            esc_html($data['totalReviews']), 
                            esc_html($settings['rating_month_label']),
                            esc_html($FiveStarPer.'%')
                            
                        ), 
                        'reviews-sorted'
                    ); 
                }
                ?>
                    <div class="tooltip">
                        <span class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" viewBox="0 0 310.277 310.277"
                                fill="#010002">
                                <path d="M155.139 0C69.598 0 0 69.598 0 155.139c0 85.547 69.598 155.139 155.139 155.139s155.139-69.592 155.139-155.139C310.283 69.592 240.686 0 155.139 0zm-5.848 227.815c-7.309 0-12.322-5.639-12.322-12.948 0-7.727 5.227-13.157 12.536-13.157 7.315 0 12.322 5.43 12.322 13.157-.005 7.309-4.809 12.948-12.536 12.948zm20.258-78.321c-9.183 10.86-12.53 20.049-11.904 30.706l.209 5.43h-16.29l-.418-5.43c-1.253-11.283 2.506-23.599 12.954-36.135 9.404-11.069 14.625-19.219 14.625-28.617 0-10.651-6.689-17.751-19.852-17.96-7.518 0-15.872 2.506-21.093 6.474l-5.012-13.157c6.892-5.012 18.796-8.354 29.87-8.354 24.023 0 34.882 14.828 34.882 30.706.001 14.2-7.941 24.433-17.971 36.337z" />
                            </svg>
                        </span>
                        <span class="content"><?php _e('This the total number of reviews received over this period.', 'reviews-sorted'); ?></span>
                    </div>
                </div>

                <div class="powered-by">
                    <?php _e( sprintf('Powered by <a href="%s" target="_blank">Reviews Sorted</a>', esc_url('https://www.reviewssorted.com/')), 'reviews-sorted'); ?>                    
                </div>
                <script type="application/ld+json">
                    {
                        "@context": "http://schema.org",
                        "@type": "LocalBusiness",
                        "name": "<?php echo esc_attr_e( $settings['business_name']); ?>",
                        "aggregateRating": {
                            "@type": "AggregateRating",
                            "ratingValue": "<?php echo esc_js($average); ?>",
                            "ratingCount": "<?php echo esc_js($data['totalReviews']); ?>"
                        },
                        "image": "<?php echo esc_url($settings['business_icon']); ?>",
						"address": "<?php echo esc_attr_e($settings['business_address']); ?>",
                        "telephone": "<?php echo esc_js($settings['business_phone']); ?>",
                        "priceRange": "<?php echo esc_js($settings['business_priceRange']); ?>"
                    }
                </script>
            </div>

        </div>

    </div>
</div>