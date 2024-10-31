<?php
    if( !isset($reviews) || !is_array($reviews) ){
        return;
    }
?>
<div class="reviews-sorted slide-layout reviews-masonry">
    <div class="rs-grid-masonry" style="<?php printf('--column: %s; --gap: %spx', esc_attr(isset($options['column']) ?  $options['column'] : ''), esc_attr(isset($options['space']) ? $options['space']: '')); ?>">

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
                        
                        <div>
                            <span class="author"><?php printf('%s %s',esc_html($review->authorfname), esc_html($review->authorlname) ); ?></span> 
                            - 
                            <span class="date"><?php esc_html_e( $created ); ?></span>
                        </div>
                        <div class="rs-rating" style="<?php printf('--rating: %s', esc_attr($review->rating)); ?>;" aria-label="<?php printf( __('Rating of this product is %s out of 5.', 'reviews-sorted'), esc_attr($review->rating)); ?>">
                            <span style="display:none;"><?php printf( __('%s Stars', 'reviews-sorted'), esc_html($review->rating)); ?></span>
                            
                        </div>
                    </div>

                </div>
            </div><!-- .swiper-slide -->
        <?php endforeach; ?>
    </div><!-- .swiper-wrapper -->

</div><!-- .swiper -->