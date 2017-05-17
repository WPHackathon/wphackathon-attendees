<?php
// Create custom pages for attendees
function wphackathon_custom_pages(){
	// Create Page
	// Initialize the page ID to -1. This indicates no action has been taken.
	$post_id = -1;

	// Setup the author, slug, and title for the post
	$author_id = 1;
	$title = array(
		0	=> array(
				'title'			=> __( 'Attendees Application', 'wphackathon-cpt-attendees' ),
				'slug'			=> 'attendees-application',
				'post_content'	=> '[wph_attendees_application]'
			),
		1	=> array(
				'title'		=> __( 'Attendees', 'wphackathon-cpt-attendees' ),
				'slug'		=> 'attendees',
				'post_content'	=> '[wph_attendees]'
			)
		);
	foreach( $title as $key => $title_key ){

		// If the page doesn't already exist, then create it
		if( null == get_page_by_title( $title_key['title'] ) ) {

		// Set the post ID so that we know the post was created successfully
		$post_id = wp_insert_post(
			array(
				'comment_status'	=>	'closed',
				'ping_status'		=>	'closed',
				'post_author'		=>	$author_id,
				'post_name'			=>	$title_key['slug'],
				'post_title'		=>	$title_key['title'],
				'post_content'		=>  $title_key['post_content'],
				'post_status'		=>	'publish',
				'post_type'			=>	'page'
			)
		);

		// Otherwise, we'll stop
		} else {

			// Arbitrarily use -2 to indicate that the page with the title already exists
			$post_id = -2;

		} // end if
	}
}
