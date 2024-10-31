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
<div class="<?php esc_attr_e($wraperClass); ?>" style="<?php printf('--gap:%spx', esc_attr( $options['space']) ); ?>">
    <div class="swiper reviews-sorted slide-layout reviews-carousel" data-options='<?php echo json_encode($slideOptions) ?>'>
        <div class="swiper-wrapper">

            <!-- Slides -->
            <?php foreach($reviews as $review): ?>
                <div class="swiper-slide">
                    <div class="inner">

                        <div class="swipe-content">
                            <div class="reviewBody">
                                <?php echo wpautop(wp_kses_data(stripslashes($review->content))); ?>
                            </div>
                        </div>

                        <div class="swipe-footer">
                            <?php
                                $created = date("F d, Y", strtotime($review->created_at));  
                            ?>
                            
                            <div >
                                <span class="author" ><?php printf('%s %s', esc_html( $review->authorfname ), esc_html( $review->authorlname )); ?></span> 
                                - <span class="date"><?php esc_html_e( $created ); ?></span>
                            </div>
                            <div class="rs-rating" style="--rating:<?php esc_attr_e($review->rating); ?>;" aria-label="<?php printf( __('Rating of this product is %s out of 5.', 'reviews-sorted'), esc_attr( $review->rating ) ); ?>" >
                                <span style="display:none;"><?php printf( __('%s Stars', 'reviews-sorted'), esc_html($review->rating)); ?></span>
                            </div>
                        </div>

                    </div>
                </div><!-- .swiper-slide -->
            <?php endforeach; ?>
        </div><!-- .swiper-wrapper -->

        <!-- navigation buttons -->
        <?php if ( $options['arrows'] ) : ?>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        <?php endif ?>

        <!-- pagination -->
        <?php if ( $options['dots'] ) : ?>
            <div class="swiper-pagination"></div>
        <?php endif ?>
        
    </div><!-- .swiper -->

</div>