<?php
    if( !isset($reviews) || !is_array($reviews) ){
        return;
    }
    $slideOptions = [
        'slidesPerView' => $options['mobile'],
        'spaceBetween'  => $options['space'],
        'speed'         => $options['speed'],
        'loop'          => $options['loop'],
        'autoplay'      => $options['autoplay'] === false ? false : [ 'delay' => $options['delay'], 'disableOnInteraction' => false ],
        'breakpoints'   => [
            '768' => [
                'slidesPerView' => $options['tablet'],
            ],
            '1024' => [
              'slidesPerView' => $options['desktop'],
            ]
        ]
    ];
    $wraperClass = 'reviews-swiper-wrapper';
    $options['equalHeight'] ? $wraperClass .= ' item-height_equal ' : '';
?>
<div class="<?php esc_attr_e($wraperClass) ?>" style="<?php printf('--gap: %spx', $options['space']); ?>">
    <div class="swiper reviews-sorted layout-2 reviews-slider" data-options='<?php echo json_encode($slideOptions) ?>'>
        <div class="swiper-wrapper">

            <?php foreach($reviews as $review): ?>
                <div class="list-item swiper-slide">
                    <div class="inner">
                        <?php
                            $created = date("F d, Y", strtotime($review->created_at));  
                        ?>
                        

                        
                        <div class="rs-rating-wrapper">
                            <div class="rs-rating rs-custom-icons"
                                style="<?php echo sprintf('--rating:%s', esc_attr($review->rating) ); ?>"
                                aria-label="<?php printf( __('Rating of this product is %s out of 5.', 'reviews-sorted'), esc_attr( $review->rating )); ?>">
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
                            </div>
                        </div>
                        <div class="date"><?php esc_html_e( $created ); ?></div>
                        
                        
                        <div class="reviews-content">
                            <?php echo wpautop(wp_kses_data(stripslashes($review->content))); ?>
                            <div>
                                <span class="author"><?php printf('%s %s', esc_html($review->authorfname), esc_html($review->authorlname) ); ?></span>
                            </div>
                        </div>

                    </div>
                </div><!-- .list-item -->
            <?php endforeach; ?>
            
        </div><!-- .swiper-wrapper -->

        <!-- navigation buttons -->
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div><!-- .swiper -->
</div>