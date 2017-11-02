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

// CODE TO REGISTER TAXONOMY
function wphackathon_custom_taxonomy() {
	wphackathon_ct_skill();
	$terms = get_terms( array(
    'taxonomy' => 'skill',
    'hide_empty' => false,
	) );
	
	if ( empty( $terms ) ){
		$terms = array(
			0 => array(
					'name'			=> __('Design', 'wph_attendees'),
					'slug'			=> 'design',
					'description'	=> ''
				),
			1 => array(
					'name'			=> __('Developer', 'wph_attendees'),
					'slug'			=> 'developer',
					'description'	=> ''
				),
			2 => array(
					'name'			=> __('Marketing', 'wph_attendees'),
					'slug'			=> 'marketing',
					'description'	=> ''
				),
			3 => array(
					'name'			=> __('Social Media','wph_attendees'),
					'slug'			=> 'social-media',
					'description'	=> ''
				),
			4 => array(
					'name'			=> __('Copywriter','wph_attendees'),
					'slug'			=> 'copywriter',
					'description'	=> ''
				),
			5 => array(
					'name'			=> __('Front end','wph_attendees'),
					'slug'			=> 'front-end',
					'description'	=> ''
				),
			6 => array(
					'name'			=> __('Content Manager','wph_attendees'),
					'slug'			=> 'content-manager',
					'description'	=> ''
				),
			7 => array(
					'name'			=> __('Other','wph_attendees'),
					'slug'			=> 'other',
					'description'	=> ''
				)
		);

		foreach($terms as $key => $term_key){
			   wp_insert_term(
				   $term_key['name'],
				   'skill',
				   array(
					 'description' => $term_key['description'],
					 'slug'        => $term_key['slug']
				   )
			   );
		}
	}
}
