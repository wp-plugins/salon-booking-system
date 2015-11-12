<?php

class SLN_Metabox_Helper
{
    public static function updateMetas($post_id, $meta)
    {
        foreach ($meta as $meta_key => $new_meta_value) {
            $meta_value = get_post_meta($post_id, $meta_key, true);
            if ($new_meta_value && empty($meta_value)) {
                add_post_meta($post_id, $meta_key, $new_meta_value, true);
            } elseif ($new_meta_value && $new_meta_value != $meta_value) {
                update_post_meta($post_id, $meta_key, $new_meta_value);
            } elseif ('' == $new_meta_value && $meta_value) {
                delete_post_meta($post_id, $meta_key, $meta_value);
            }
        }
    }

    public static function processRequest($postType, $fieldList)
    {
        foreach ($fieldList as $k => $v) {
            $field        = self::getFieldName($postType, $k);
            $meta[$field] = SLN_Func::filter(isset($_POST[$field]) ? $_POST[$field] : null, $v);
        }
        return $meta;
    }

    public static function getFieldName($postType, $key)
    {
        return '_' . $postType . '_' . $key;
    }

    public static function isValidRequest($postType, $post_id, $post)
    {
        if (!isset($_POST[$postType . '_details_meta_nonce']) || !wp_verify_nonce(
                $_POST[$postType . '_details_meta_nonce'],
                $postType
            )
        ) {
            return false;
        }

        /* Get the post type object. */
        $post_type = get_post_type_object($post->post_type);

        /* Check if the current user has permission to edit the post. */
        if (!current_user_can($post_type->cap->edit_post, $post_id)) {
            return false;
        }

        /* Don't save if the post is only a revision. */
        if ('revision' == $post->post_type) {
            return false;
        }

        return true;
    }

    public static function showNonce($postType)
    {
        ?>
        <input type="hidden" name="<?php echo $postType ?>_details_meta_nonce"
               value="<?php echo wp_create_nonce($postType); ?>"/>
    <?php
    }

    public static function showFieldText($field, $label, $val)
    {
        ?>
        <div class="form-group sln_meta_field">
            <label for="<?php echo $field ?>"><?php echo $label ?></label>
            <?php SLN_Form::fieldText($field, $val); ?>
        </div>
    <?php
    }

    public static function showFieldTextArea($field, $label, $val)
    {
        ?>
        <div class="form-group sln_meta_field">
            <label for="<?php echo $field ?>"><?php echo $label ?></label>
            <?php SLN_Form::fieldTextarea($field, $val); ?>
        </div>
    <?php
    }

} 
