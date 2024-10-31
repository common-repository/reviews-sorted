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

<!-- Layout-1 -->
<div class="reviews-sorted reviews-testimonials layout-1">
    <div class="rs-testimonials-list">

        <div class="rs-average">
            <div class="totalAverage">
                <?php esc_html_e( $average ); ?>
            </div>
            <div class="rs-rating"
                style="--rating:<?php echo $average ?>;"
                aria-label="<?php printf( __('Rating of this product is %s out of 5.', 'reviews-sorted'), esc_attr($average) ); ?>">
                <span style="display:none;"><?php printf( __('%s Stars', 'reviews-sorted'), esc_html( $average )); ?></span>
            </div>
            <div class="totalReviews">
                <?php echo wpautop(wp_kses_data($data['totalReviews'])); ?>
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
                    "image": "<?php echo esc_url(get_site_icon_url()); ?>",
                    "address": "<?php echo esc_attr_e($settings['business_address']); ?>",
                    "telephone": "<?php echo esc_js($settings['business_phone']) ?>",
                    "priceRange": "<?php echo esc_js($settings['business_priceRange']) ?>"
                }
            </script>
        </div> <!-- .rs-average -->

        <!-- item listing -->
        <?php foreach($reviews as $review): ?>
            <div class="list-item">
                <div class="inner">
                    <?php
                        $created = date("F d, Y", strtotime($review->created_at));  
                    ?>
                    
                    <div>
                        <span class="author"><?php printf( '%s %s', esc_html($review->authorfname), esc_html($review->authorlname) ); ?></span>
                    </div>

                    <div class="rs-rating-wrapper">
                        <div class="rs-rating" style="<?php printf('--rating:%s', esc_attr( $review->rating )); ?>"
                            aria-label="<?php printf( __('Rating of this product is %s out of 5.', 'reviews-sorted'), esc_attr($review->rating) ); ?>">
                            <span style="display:none;"><?php printf( __('%s Stars', 'reviews-sorted'), esc_html( $review->rating )); ?></span>
                            
                        </div>
                        <span class="date"><?php esc_html_e($created); ?></span>
                    </div>

                    <div class="reviewBody">
                        <?php echo wpautop(wp_kses_data(stripslashes($review->content))); ?>
                    </div>

                </div>
            </div><!-- .list-item -->
        <?php endforeach; ?>
    </div><!-- item listing -->

</div><!-- .layout-1 -->