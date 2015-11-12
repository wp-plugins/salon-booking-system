<?php

class SLN_TaxonomyType_ServiceCategory extends SLN_TaxonomyType_Abstract
{
    protected function getTaxonomyTypeArgs(){
        // hook into the init action and call create_book_taxonomies when it fires
	$labels = array(
		'name'              => _x( 'Service Categories', 'taxonomy general name' ),
		'singular_name'     => _x( 'Service Category', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Service Category', 'sln' ),
		'all_items'         => __( 'All Service Categories','sln' ),
		'edit_item'         => __( 'Edit Service Category','sln' ),
		'update_item'       => __( 'Update Service Category','sln' ),
		'add_new_item'      => __( 'Add New Service Category','sln' ),
		'new_item_name'     => __( 'New Service Category Name','sln' ),
		'menu_name'         => __( 'Service Category','sln' ),
	);

	$args = array(
		'hierarchical'      => false,
		'labels'            => $labels,
                'show_in_menu'       => 'salon',
		'show_ui'           => false,
		'show_admin_column' => true,
		'query_var'         => true,
                
		'rewrite'           => array( 'slug' => 'servicecategory' ),
	);

        return $args;
    }

    public function initAdmin() {
        $tax_name = $this->taxonomyType;
		$taxonomy = get_taxonomy( $tax_name );

		foreach ( $taxonomy->object_type as $pt ) {
			# Remove default metabox
//			remove_meta_box( "{$tax_name}div", $pt, 'side' );
			# Add our own
			add_meta_box( "unique-{$tax_name}-div", $taxonomy->labels->singular_name, array($this, 'unique_taxonomies_metabox'), $pt, 'side', 'low', array('taxonomy' => $tax_name) );
		}
	
}


    function terms_radiolist( $post_id, $taxonomy, $echo = true ) {
	$terms = get_terms( $taxonomy, array('hide_empty' => false) );
	if ( empty($terms) )
		return;
	$name = ( $taxonomy == 'category' ) ? 'post_category' : "tax_input[{$taxonomy}]";

	$post_terms = get_the_terms( $post_id, $taxonomy );
	$nu_post_terms = array();
	if ( !empty($post_terms) ) {
		foreach ( $post_terms as $post_term )
			$nu_post_terms[] = $post_term->term_id;
	}

	$output = '';
	foreach ( $terms as $term ) {
		$output .= "<li class='selectit'>";
		$output .= "<label>";
		$output .= "<input type='radio' name='{$name}[]' value='{$term->name}' ".checked( in_array($term->term_id, $nu_post_terms), true, false )."/>";
		$output .= " {$term->name}</label>";
		$output .= "</li>";
	}
        $output .= "<li class='selectit'><label><input type='radio' name='{$name}[]' value='' ".checked(empty($nu_post_terms),true,false)."/>".__('Not defined','sln')."</label></li>";
	if ( $echo )
		echo $output;
	else
		return $output;
    }

    function unique_taxonomies_metabox( $post, $box ) {
	if ( !isset($box['args']) || !is_array($box['args']) )
		$args = array();
	else
		$args = $box['args'];

	$defaults = array('taxonomy' => 'category');
	extract( wp_parse_args($args, $defaults), EXTR_SKIP );
	$tax = get_taxonomy($taxonomy);

	?>
	<div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
		<?php
			$name = ( $taxonomy == 'category' ) ? 'post_category' : 'tax_input[' . $taxonomy . ']';
			echo "<input type='hidden' name='{$name}' value='0' />"; // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
		?>
		<ul id="<?php echo $taxonomy; ?>checklist" class="list:<?php echo $taxonomy?> categorychecklist form-no-clear">
			<?php $this->terms_radiolist( $post->ID, $taxonomy ) ?>
		</ul>
	<?php if ( !current_user_can($tax->cap->assign_terms) ) { ?>
		<p><em><?php _e('You cannot modify this taxonomy.'); ?></em></p>
	<?php } ?>
	<?php if ( current_user_can($tax->cap->edit_terms) ) { ?>
			<div id="<?php echo $taxonomy; ?>-adder" class="wp-hidden-children">
<h4>
					<a href="<?php echo admin_url('edit-tags.php?taxonomy='.$taxonomy) ?>">
                                            _<?php _e('Manage service categories', 'sln') ?>
					</a>
</h4>
			</div>
		<?php } ?>
	</div>
        <?php
    }
}
