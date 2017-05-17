<?php

// Register Custom Taxonomy Skill
function wphackathon_ct_skill() {

	$labels = array(
		'name'                       => _x( 'Skills', 'Taxonomy General Name', 'wph_attendees' ),
		'singular_name'              => _x( 'Skill', 'Taxonomy Singular Name', 'wph_attendees' ),
		'menu_name'                  => __( 'Skills', 'wph_attendees' ),
		'all_items'                  => __( 'All Skills', 'wph_attendees' ),
		'parent_item'                => __( 'Parent Skill', 'wph_attendees' ),
		'parent_item_colon'          => __( 'Parent Skill:', 'wph_attendees' ),
		'new_item_name'              => __( 'New Skill Name', 'wph_attendees' ),
		'add_new_item'               => __( 'Add New Skill', 'wph_attendees' ),
		'edit_item'                  => __( 'Edit Skill', 'wph_attendees' ),
		'update_item'                => __( 'Update Skill', 'wph_attendees' ),
		'view_item'                  => __( 'View Skill', 'wph_attendees' ),
		'separate_items_with_commas' => __( 'Separate Skills with commas', 'wph_attendees' ),
		'add_or_remove_items'        => __( 'Add or remove Skills', 'wph_attendees' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'wph_attendees' ),
		'popular_items'              => __( 'Popular Skills', 'wph_attendees' ),
		'search_items'               => __( 'Search Skills', 'wph_attendees' ),
		'not_found'                  => __( 'Not Found', 'wph_attendees' ),
		'no_terms'                   => __( 'No Skills', 'wph_attendees' ),
		'items_list'                 => __( 'Skills list', 'wph_attendees' ),
		'items_list_navigation'      => __( 'Skills list navigation', 'wph_attendees' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'skill', array( 'attendee' ), $args );

}
add_action( 'init', 'wphackathon_ct_skill', 0 );
