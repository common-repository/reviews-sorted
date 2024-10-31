<?php
    if( !isset($reviews) || !is_array($reviews) ){
        return;
    }
    if(!isset($data['totalRatings']) || !isset($data['totalReviews'])){
        return;
    }

    if(intval($data['totalReviews']) == 0){
        return;
    }

    $average = intval($data['totalRatings']) / intval($data['totalReviews']);
    $average = number_format($average, 1);
?>

<!-- Layout-2 -->
<div class="reviews-sorted reviews-testimonials layout-2">
    <div class="rs-testimonials-list">

        <div class="rs-average">
            <div class="rs-heading"><?php _e('Our customers say <span>Excellent</span>', 'reviews-sorted'); ?></div>
            
            <div class="rs-bg-rating rs-bg-rating"
                style="<?php printf('--rating:%s;', esc_attr( $average )); ?>"
                aria-label="<?php printf( __('Rating of this product is %s out of 5.', 'reviews-sorted'), esc_attr($average) ); ?>" itemprop="reviewRating" itemscope
                itemtype="http://schema.org/Rating"
            >
                <span style="display:none;"><?php printf( __('%s Stars', 'reviews-sorted'), esc_html( $average )); ?></span>
                <meta itemprop="ratingValue" content="<?php esc_attr_e( $average); ?>">
                <meta itemprop="bestRating" content="5">
                <img class="rating-img" src="<?php echo esc_url( REVIEWS_SORTED_ASSETS_IMG.'/stars-active.png'); ?>" alt="stars active">
                <img src="<?php echo esc_url( REVIEWS_SORTED_PLUGIN_URL.'/assets/images/stars-inactive_2.png'); ?>" alt="stars inactive">
            </div>
            <div class="totalReviews">
                <?php printf( __('%s out of 5 based on %s reviews', 'reviews-sorted'), esc_html( $average ), esc_html( $data['totalReviews'] )); ?>
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
                    "image": "<?php echo esc_url(get_site_icon_url()) ?>",
                    "address": "<?php echo esc_attr_e($settings['business_address']); ?>",
                    "telephone": "<?php echo esc_js($settings['business_phone']) ?>",
                    "priceRange": "<?php echo esc_js($settings['business_priceRange']) ?>"
                }
            </script>
        </div> <!-- .rs-average -->
        
        <div class="rs-listing">

            <!-- item listing -->
            <?php foreach($reviews as $review): ?>
                <div class="list-item">
                    <div class="inner">
                        <?php
                            $created = date("F d, Y", strtotime($review->created_at));  
                        ?>
                        
    
                        
                        <div class="rs-rating-wrapper">
                            <div class="rs-bg-rating rs-custom-icons"
                                style="<?php printf('--rating:%s', esc_attr( $review->rating )) ?>"
                                aria-label="<?php printf( __('Rating of this product is %s out of 5.', 'reviews-sorted'), esc_attr($review->rating) ); ?>">
                             <span class="star-icons">
                                    
                                    <?php

                                    for($i=1; $i<= 5; $i++) {
                                        $icon_key = 'icon_for_'. $i .'_star';
                                        $row_key = 'icon_for_'. $i .'_star';
                                        $image_id = isset($settings[$icon_key]) ? intval($settings[$row_key]) : 0;
                                        $url  = REVIEWS_SORTED_PLUGIN_URL . 'assets/star.png';            
                                        if( $image = wp_get_attachment_image_src( $image_id ) ) {                
                                            $url = esc_url($image[0]);
                                        }
                                        $rating_active = $i <= $review->rating  ? 'rs-star-active' : '';
                                        echo sprintf('<span class="rs-start-icon-%s %s"><img src="%s"></span>', $i, $rating_active, $url);
                                    }
                                    ?>
                                </span>
                                <span style="display:none;"><?php printf( __('%s Stars', 'reviews-sorted'), esc_html( $review->rating )); ?></span>
                               
                                <img class="rating-img" src="<?php echo esc_url( REVIEWS_SORTED_ASSETS_IMG.'/stars-active.png'); ?>" alt="stars active">
                                <img src="<?php echo esc_url( REVIEWS_SORTED_PLUGIN_URL.'/assets/images/stars-inactive_2.png'); ?>" alt="stars inactive">
                            </div>
                        </div>
                        <div class="date"><?php esc_html_e($created); ?></div>
                        
                        <div class="reviews-content">
                            <?php echo wpautop(wp_kses_data(stripslashes($review->content))); ?>
                            <div >
                                <span class="author"><?php printf( '%s %s', esc_html($review->authorfname), esc_html($review->authorlname) ); ?></span>
                            </div>
                        </div>
    
                    </div>
                </div><!-- .list-item -->
            <?php endforeach; ?>

        </div>
    </div><!-- item listing -->

</div><!-- .layout2 -->