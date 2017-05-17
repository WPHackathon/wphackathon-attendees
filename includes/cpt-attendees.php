<?php

// Register Custom Post Type Attendees
function wphackathon_cpt_attendees() {

	$labels = array(
		'name'                  => _x( 'Attendees', 'Post Type General Name', 'wph_attendees' ),
		'singular_name'         => _x( 'Attendee', 'Post Type Singular Name', 'wph_attendees' ),
		'menu_name'             => __( 'Attendees', 'wph_attendees' ),
		'name_admin_bar'        => __( 'Attendee', 'wph_attendees' ),
		'archives'              => __( 'Attendee Archives', 'wph_attendees' ),
		'attributes'            => __( 'Attendee Attributes', 'wph_attendees' ),
		'parent_item_colon'     => __( 'Parent Attendee:', 'wph_attendees' ),
		'all_items'             => __( 'All Attendees', 'wph_attendees' ),
		'add_new_item'          => __( 'Add New Attendee', 'wph_attendees' ),
		'add_new'               => __( 'Add New', 'wph_attendees' ),
		'new_item'              => __( 'New Attendee', 'wph_attendees' ),
		'edit_item'             => __( 'Edit Attendee', 'wph_attendees' ),
		'update_item'           => __( 'Update Attendee', 'wph_attendees' ),
		'view_item'             => __( 'View Attendee', 'wph_attendees' ),
		'view_items'            => __( 'View Attendees', 'wph_attendees' ),
		'search_items'          => __( 'Search Attendee', 'wph_attendees' ),
		'not_found'             => __( 'Not found', 'wph_attendees' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'wph_attendees' ),
		'featured_image'        => __( 'Featured Image', 'wph_attendees' ),
		'set_featured_image'    => __( 'Set featured image', 'wph_attendees' ),
		'remove_featured_image' => __( 'Remove featured image', 'wph_attendees' ),
		'use_featured_image'    => __( 'Use as featured image', 'wph_attendees' ),
		'insert_into_item'      => __( 'Insert into Attendee', 'wph_attendees' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Attendee', 'wph_attendees' ),
		'items_list'            => __( 'Attendees list', 'wph_attendees' ),
		'items_list_navigation' => __( 'Attendees list navigation', 'wph_attendees' ),
		'filter_items_list'     => __( 'Filter Attendees list', 'wph_attendees' ),
	);
	$args = array(
		'label'                 => __( 'Attendee', 'wph_attendees' ),
		'description'           => __( 'Post Type Description', 'wph_attendees' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-groups',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
		'show_in_rest'          => true,
	);
	register_post_type( 'attendee', $args );

}
add_action( 'init', 'wphackathon_cpt_attendees', 0 );


// Add custom columns to Attendees Admin Page
add_filter('manage_attendee_posts_columns', 'wph_columns_head_only_attendee', 10);
add_action('manage_attendee_posts_custom_column', 'wph_columns_content_only_attendee', 10, 2);

// Create two functions to handle the columns
function wph_columns_head_only_attendee( $defaults ) {

    $defaults['attendee_twitter'] = 'Twitter';
		$defaults['attendee_orguser'] = '.org User';

		// Move the Date column to the last position
		unset($defaults['date']);
		$defaults['date'] = __( 'Date' );

    return $defaults;
}


function wph_columns_content_only_attendee( $column_name, $post_ID ) {

	// Add the Attendees Twitter column
  if ( $column_name == 'attendee_twitter' ) {

		$custom_field_values = get_post_meta( $post_ID, 'attendee_twitter' );

		if (!empty($custom_field_values)) {

				echo '<p> '. join( ', ', $custom_field_values ) .' </p>';

		} else {

			echo '-';
		}

  }

	// Add the Attendees WordPress.org User column
  if ( $column_name == 'attendee_orguser' ) {

		$custom_field_values = get_post_meta( $post_ID, 'attendee_orguser' );

		if ( !empty( $custom_field_values ) ) {

				echo '<p> '. join( ', ', $custom_field_values ) .' </p>';

		} else {

			echo '-';

		}

	}

}
