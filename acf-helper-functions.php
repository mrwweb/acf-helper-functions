<?php
/**
 * helper function to output ACF fields
 * @param string $field_key id of field to get
 * @param array|string $args array or query_string of arguments
 * @param int|'option' $post_id integer of specific post ID or 'option' for site option
 * @return string the desired field with a nice output
 */
function get_acf_field( $field_key = false, $args = array(), $post_id = null ) {

	// stop if no $field or ACF isn't here
	if( !$field_key || !function_exists('get_field') )
		return;

	// Set defaults, parse args, `extract` for nice $vars
	$defaults = array(
		'type' => 'text',
		'label' => false,
		'link_text' => false,
		'image_size' => 'medium',
		'image_class' => null,
		'itemprop' => false,
		'date_format' => 'F j, Y',
		'before' => '',
		'after' => '',
		'sub_field' => false,
		'list_sep' => ', ',
		'list_links' => true,
		'list_type' => 'objects',
		'taxonomy' => false
	);
	extract(wp_parse_args( $args, $defaults ));

	// get field. stop if field doesn't have value
	if( ! $sub_field ) {
		$field = get_field( $field_key, $post_id );
	} else {
		$field = get_sub_field( $field_key );
	}
	if( !$field )
		return;

	if( $label )
		$label = '<span class="acf-label acf-label-' . esc_attr($field_key) . '">' . $label . ':</span> ';

	$itemprop_attr = $itemprop ? ' itemprop="' . $itemprop . '"' : false;

	switch ($type) {
		case 'text':
			if( $itemprop ) {
				$output = '<span' . $itemprop_attr . '>' . $field . '</span>';
			} else {
				$output = $field;
			}
			break;

		case 'url':
			if( $field == 'http://' ) {
				return; // so we can give people a nice default field value
			} else {
				$output = $field;
			}
			break;

		case 'link':
			if( $field == 'http://' ) {
				return; // so we can give people a nice default field value
			} else {
				$link_text = $link_text ? $link_text : $field;
				$output = sprintf(
					'<a href="%1$s"%2$s>%3$s</a>',
					$field,
					$itemprop_attr,
					$link_text
				);
			}
			break;

		case 'email':
			$link_text = $link_text ? $link_text : antispambot( $field );
			$output = sprintf(
				'<a href="mailto:%1$s"%2$s>%3$s</a>',
				antispambot( $field ),
				$itemprop_attr,
				$link_text
			);
			break;

		case 'image':
			$output = wp_get_attachment_image( $field, $image_size, false, array( 'class' => esc_attr($field_key) . $image_class, 'itemprop' => $itemprop ) );
			break;

		case 'date':
			$date = DateTime::createFromFormat('Ymd', $field);
			$output = $date->format( $date_format );
			break;

		case 'post_list':
			$output = acf_wp_posts_list( $field, $list_sep, '', '', $list_links, $list_type );
			break;

		case 'term':
			$term = get_term( $field[0], $taxonomy );
			if( ! is_wp_error( $term ) ) {
				$output = $term->name;
			} else {
				return;
			}
			break;

		case 'term_link':
			$term = get_term_link( $field[0], $taxonomy );
			if( ! is_wp_error( $term ) ) {
				$output = $term;
			} else {
				return;
			}
			break;

		case $type:
			return apply_filters( 'return_get_acf_field', false, $type, $args );
			break;
		
		default:
			return; // don't return anything if unknown field type
			break;
	}

	return $before . $label . $output . $after;

}

// Function to output get_acf_field output
function the_acf_field( $field_key = false, $args = array(), $post_id = null ) {
	echo get_acf_field( $field_key, $args, $post_id );
}

/**
 * Make a list of links to WordPress posts
 * @param array $posts array of post objects or post_ids
 * @param string $sep separater
 * @param string $before output before the list
 * @param string $after output after the list
 * @param bool $links whether to link titles to posts, default = true
 * @param 'objects'|'ids' $type whether the $posts param contains objects or IDs. Default: 'objects'
 * @return string links separated by commas
 */
function acf_wp_posts_list( $posts, $sep = ', ', $before = '', $after = '', $links = true, $type = 'objects' ) {
	if( ! $posts && ! is_array( $posts ) )
		return; // nothing to do

	if( $type = 'objects' ) {
		$posts = wp_list_pluck( $posts, 'ID' );
	}

	$post_links = array();
	if( (bool) $links ) {
		foreach ($posts as $key => $id) {
			$post_links[] = '<a href="' . esc_url( get_permalink( $id ) ) . '">' . esc_attr( get_the_title( $id ) ) . '</a>';
		}
	} else {
		foreach ($posts as $key => $id) {
			$post_links[] = esc_attr( get_the_title( $id ) );
		}
	}

	return $before . implode( $sep, $post_links ) . $after;
}