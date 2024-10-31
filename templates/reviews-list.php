<?php
    if( !isset($reviews) || !is_array($reviews) ){
        return;
    }
?>
<div class="reviews-sorted slide-layout reviews-list">
    <div class="rs-list" style="<?php echo sprintf('--gap:%spx;', esc_attr($options['space'])); ?>">

        <!-- Slides -->
        <?php foreach($reviews as $review): ?>
            <div class="swiper-slide">
                <div class="inner">

                    <div class="swipe-content">
                    
                        <div class="reviewBody">
                            <?php _e( esc_html( $review->content ), 'reviews-sorted'); ?>
                        </div>
                    </div>

                    <div class="swipe-footer">
                        <?php
                            $created = date("F d, Y", strtotime($review->created_at));  
                        ?>
                        
                        <div>
                            <span class="author"><?php echo sprintf('%s %s', esc_html($review->authorfname), esc_html($review->authorlname) ); ?></span> 
                            - <span class="date"><?php _e( esc_html( $created ), 'reviews-sorted'); ?></span>
                        </div>
                        <div class="rs-rating" style="<?php echo sprintf('--rating:%s', esc_attr($review->rating) ); ?>" aria-label="<?php _e( sprintf('Rating of this product is %s out of 5.', esc_attr( $review->rating )), 'reviews-sorted'); ?>">
                            <span style="display:none;"><?php _e( sprintf('%s Stars', esc_html( $review->rating)), 'reviews-sorted'); ?></span>
                            
                        </div>
                    </div>

                </div>
            </div><!-- .swiper-slide -->
        <?php endforeach; ?>
    </div><!-- .swiper-wrapper -->

</div><!-- .swiper -->