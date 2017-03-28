<?php

// Register Custom Taxonomy Skill
function wphackathon_ct_skill() {

	$labels = array(
		'name'                       => _x( 'Skills', 'Taxonomy General Name', $wph_ct_textdomain ),
		'singular_name'              => _x( 'Skill', 'Taxonomy Singular Name', $wph_ct_textdomain ),
		'menu_name'                  => __( 'Skills', $wph_ct_textdomain ),
		'all_items'                  => __( 'All Skills', $wph_ct_textdomain ),
		'parent_item'                => __( 'Parent Skill', $wph_ct_textdomain ),
		'parent_item_colon'          => __( 'Parent Skill:', $wph_ct_textdomain ),
		'new_item_name'              => __( 'New Skill Name', $wph_ct_textdomain ),
		'add_new_item'               => __( 'Add New Skill', $wph_ct_textdomain ),
		'edit_item'                  => __( 'Edit Skill', $wph_ct_textdomain ),
		'update_item'                => __( 'Update Skill', $wph_ct_textdomain ),
		'view_item'                  => __( 'View Skill', $wph_ct_textdomain ),
		'separate_items_with_commas' => __( 'Separate Skills with commas', $wph_ct_textdomain ),
		'add_or_remove_items'        => __( 'Add or remove Skills', $wph_ct_textdomain ),
		'choose_from_most_used'      => __( 'Choose from the most used', $wph_ct_textdomain ),
		'popular_items'              => __( 'Popular Skills', $wph_ct_textdomain ),
		'search_items'               => __( 'Search Skills', $wph_ct_textdomain ),
		'not_found'                  => __( 'Not Found', $wph_ct_textdomain ),
		'no_terms'                   => __( 'No Skills', $wph_ct_textdomain ),
		'items_list'                 => __( 'Skills list', $wph_ct_textdomain ),
		'items_list_navigation'      => __( 'Skills list navigation', $wph_ct_textdomain ),
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
