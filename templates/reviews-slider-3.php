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
<div class="<?php esc_attr_e($wraperClass) ?>" style="<?php printf('--gap: %spx',$options['space']); ?>">
    <div class="swiper reviews-sorted layout-3 reviews-slider" data-options='<?php echo json_encode($slideOptions) ?>'>
        <div class="swiper-wrapper">

            <!-- item listing -->
            <?php foreach($reviews as $review): ?>
                <div class="list-item swiper-slide">
                    <div class="inner">
                        <?php
                            $created = date("F d, Y", strtotime($review->created_at));  
                        ?>
                        
                        
                        <div class="rs-rating-wrapper">
                            <div class="rs-rating" style="<?php printf('--rating: %s', esc_attr( $review->rating )); ?>"
                                aria-label="<?php printf( __('Rating of this product is %s out of 5.', 'reviews-sorted'), esc_attr($review->rating) ); ?>">
                                <span style="display:none;"><?php printf( __('%s Stars', 'reviews-sorted'), esc_html($review->rating) ); ?></span>
                               
                            </div>
                        </div>                        
                        
                        <div class="reviewBody">
                            <div class="quote-icon">
                                <img src="<?php echo esc_url( REVIEWS_SORTED_ASSETS_IMG.'/quote-icon.png'); ?>" alt="quote-icon">
                            </div>
                            <?php echo wpautop(wp_kses_data(stripslashes($review->content))); ?>
                            <div>
                                <span class="author">
                                    <span><?php esc_html_e($review->authorfname); ?></span>
                                    <?php esc_html_e($review->authorlname); ?>
                                </span>
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