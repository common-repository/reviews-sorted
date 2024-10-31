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
    if($options['arrows'] == 1) {
        $slideOptions['navigation'] = [
            'nextEl' => '.swiper-button-next',
            'prevEl' => '.swiper-button-prev',
        ];
    }
    $wraperClass = 'reviews-swiper-wrapper';
    $options['equalHeight'] ? $wraperClass .= ' item-height_equal ' : '';
?>
<div class="<?php echo $wraperClass; ?>" style="<?php printf('--gap: %spx', $options['space']); ?>">
    <div class="swiper reviews-sorted layout-1 reviews-slider" data-options='<?php echo json_encode($slideOptions) ?>'>
        <div class="swiper-wrapper">

            <!-- Slides -->
            <?php foreach($reviews as $review): ?>
                <div class="swiper-slide">
                    <div class="inner">
                        <?php
                            $created = date("F d, Y", strtotime($review->created_at));  
                        ?>
                        
                        <div>
                            <span class="author"><?php printf('%s %s', esc_html($review->authorfname), esc_html($review->authorlname) ); ?></span>
                        </div>

                        <div class="rs-rating-wrapper">
                            <div class="rs-rating" style="<?php printf('--rating:%s', esc_attr($review->rating) ); ?>"
                                aria-label="<?php printf( __('Rating of this product is %s out of 5.', 'reviews-sorted'), esc_attr( $review->rating ) ); ?>">
                                
                                <span style="display:none;"><?php printf( __('%s Stars', 'reviews-sorted'), esc_html( $review->rating)); ?></span>
                            </div>
                            <span class="date"><?php esc_html_e( $created ); ?></span>
                        </div>

                        <div class="reviewBody">
                            <?php echo wpautop(wp_kses_data(stripslashes($review->content))); ?>
                        </div>

                    </div>
                </div><!-- .swiper-slide -->
            <?php endforeach; ?>
        </div><!-- .swiper-wrapper -->

        <!-- navigation buttons -->
        <?php if($options['arrows'] == 1): ?>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        <?php endif; ?>
    </div><!-- .swiper -->
</div>