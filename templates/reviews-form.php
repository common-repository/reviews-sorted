<div class="reviews-sorted reviews-sorted_form">

    <?php do_action( 'reviews-sorted_before_review_form'); ?>

    <?php if($form_main_heading || $form_sub_heading): ?>
        <div class="entry-header">
           <?php if($form_main_heading): ?>
            <h2 class="title"><?php echo wp_kses_data($form_main_heading); ?></h2>
        <?php endif; ?>
        
        <?php if($form_sub_heading): ?>
            <p class="sub-title"><?php echo wp_kses_data($form_sub_heading); ?></p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="entry-content">
    <form action="<?php echo esc_url(admin_url( 'admin-ajax.php?action=rs_reviews_submit' )); ?>" method="POST" id="rs-form_enquiry-form">

        <?php $required_fields = ['authorfname', 'authorlname', 'email', 'rating', 'recommend' ];
        foreach($form_fields as $field_key => $field): 

            $fieldlabel = strtolower($field['placeholder']);

            if (strpos($fieldlabel, 'phone') !== false) {
                $fieldlabel = 'phone_number';
            }

            $hidden_field_name = "hide_" . str_replace(' ', '_', $fieldlabel);
            $hide_field = isset($settings[$hidden_field_name]) && $settings[$hidden_field_name] == 'yes';

            if( !isset($field['type']) ){
                $field['type'] = 'text';
            }
            $required = in_array($field_key, $required_fields) ? true : false;

            $field_class = 'rs-form_group rs-form_group_'.$field_key;

            if( isset($field['fullwidth']) && $field['fullwidth'] && $field['type'] == 'textarea'){
                $field_class .= ' col-span-2';
            }

            $field_label = isset($settings[$field_key . '_label']) ? $settings[$field_key . '_label'] : $field['label'];
            $field_placeholder = isset($settings[$field_key . '_placeholder']) ? $settings[$field_key . '_placeholder'] : $field['placeholder'];
            $asterisks = '<span class="asterisks">*</span>';
            ?>
            <?php if ($hide_field != 1) { ?>
                <div class="<?php esc_attr_e($field_class); ?>">
                    <?php if($form_hidden_label != 'yes'): 
                        if ($hide_field != 1) { 
                            ?>
                            <label for="rs-form_<?php esc_attr_e($field_key); ?>"><?php echo wp_kses_data( $field_label); ?><?= $required ? $asterisks : '';?>
                        </label>
                        <?php 
                    } 
                endif; ?>
                <?php
                switch ($field['type']) {
                    case 'select':
                    ?>
                    <?php if ($hide_field != 1) { ?>
                        <select name="<?php esc_attr_e($field_key); ?>" class="rs-form_control" id="rs-form_<?php esc_attr_e($field_key); ?>" <?=($required) ? 
                        'required' : '';?>>
                        <option value="" disabled selected hidden><?php esc_html_e($field_placeholder); ?></option>
                        <?php foreach($field['options'] as $val => $label): ?>
                            <option value="<?php esc_attr_e($val); ?>"><?php esc_html_e($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php } ?>
                <?php
                break;
                case 'textarea':
                ?>
                <?php if ($hide_field != 1) { ?>
                    <textarea id="rs-form_<?php esc_attr_e($field_key); ?>" class="rs-form_control" name="<?php esc_attr_e($field_key); ?>" 
                        placeholder="<?php esc_attr_e($field_placeholder); ?>" rows="8"></textarea>
                    <?php } ?>
                    <?php
                    break;
                    case 'email':
                    ?>
                    <?php if ($hide_field != 1) { ?>
                        <input <?=($required) ? 
                        'required' : '';?> type="email" class="rs-form_control" id="rs-form_<?php esc_attr_e($field_key); ?>" name="<?php esc_attr_e($field_key); ?>" 
                        placeholder="<?php esc_attr_e($field_placeholder); ?>" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                    <?php } ?>
                    <?php
                    break;

                    default:
                    ?>     
                    <?php if ($hide_field != 1) { ?>                       
                        <input <?=($required) ? 
                        'required' : '';?> type="text" class="rs-form_control" id="rs-form_<?php esc_attr_e($field_key); ?>" name="<?php esc_attr_e($field_key); ?>" 
                        placeholder="<?php esc_attr_e($field_placeholder); ?>">
                    <?php } ?>
                    <?php
                    break;
                }
                ?>    
            </div>
        <?php } ?>
    <?php endforeach; ?>

    <div class="rs-form_footer col-span-2">
        <button type="submit" class="rs-form_btn" style="width: 100%;"><?php _e('Submit', 'reviews-sorted'); ?></button>
    </div>
    <div class="rs-form_group col-span-2 desc">
       <small><?php _e('* Required Fields', 'reviews-sorted'); ?></small>
   </div>

   <input type="hidden" name="timestamp" value="<?php echo time(); ?>">
   <input type="hidden" name="action" value="rs_reviews_submit">
   <input type="hidden" name="redirect" value="<?php esc_attr_e($form_redirect_page); ?>">
   <?php wp_nonce_field( 'rs_reviews-form', 'security-code' ); ?>

</form>
</div>

<?php do_action( 'reviews-sorted_after_review_form' ); ?>
</div>