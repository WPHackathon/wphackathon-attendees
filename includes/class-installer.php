<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WP_Hackaton_Installer
 *
 * Manage the plugin activation/deactivation
 */
class WP_Hackaton_Installer {

	/**
	 * Check if the plugin needs an upgrade
	 */
	public static function maybe_upgrade() {

		$saved_version = get_option( 'wph_version' );
		if ( WPH_VERSION === $saved_version ) {
			return;
		}

		// In a multisite, there could be sites that haven't been
		// activated yet
		if ( false === $saved_version ) {
			self::activate();
			return;
		}

		// The following lines should contain logic to upgrade any
		// future version. Example:
		// if ( version_compare( $saved_version, '2.0', '<' ) ) {
		//  self::upgrade_to_2_0();
		//}

	}

	/**
	 * Activate a blog in a multisite
	 *
	 * @param $blog_id
	 */
	public static function activate_blog( $blog_id ) {
		switch_to_blog( $blog_id );
		self::activate();
		restore_current_blog();
	}

	/**
	 * Activate the plugin
	 */
	public static function activate() {
		self::insert_default_skill_terms();
		self::insert_default_pages();

		update_option( 'wph_version', WPH_VERSION );
	}


	/**
	 * Insert a default list of skills
	 */
	public static function insert_default_skill_terms() {
		wphackathon_ct_skill();

		$terms = get_terms( array(
			'taxonomy'   => 'skill',
			'hide_empty' => false,
		) );

		if ( empty( $terms ) ) {
			$terms = array(
				0 => array(
					'name'        => __( 'Design', 'wph_attendees' ),
					'slug'        => 'design',
				),
				1 => array(
					'name'        => __( 'Developer', 'wph_attendees' ),
					'slug'        => 'developer',
				),
				2 => array(
					'name'        => __( 'Marketing', 'wph_attendees' ),
					'slug'        => 'marketing',
				),
				3 => array(
					'name'        => __( 'Social Media', 'wph_attendees' ),
					'slug'        => 'social-media',
				),
				4 => array(
					'name'        => __( 'Copywriter', 'wph_attendees' ),
					'slug'        => 'copywriter',
				),
				5 => array(
					'name'        => __( 'Front end', 'wph_attendees' ),
					'slug'        => 'front-end',
				),
				6 => array(
					'name'        => __( 'Content Manager', 'wph_attendees' ),
					'slug'        => 'content-manager',
				),
				7 => array(
					'name'        => __( 'Other', 'wph_attendees' ),
					'slug'        => 'other',
				)
			);

			foreach ( $terms as $key => $term_key ) {
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

	/**
	 * Create custom pages in multisite and single sites when the plugin is activated
	 */
	public static function insert_default_pages() {
		// Setup the author, slug, and title for the post
		$author_id = 0;
		if ( current_user_can( 'publish_pages' ) ) {
			$author_id = get_current_user_id();
		}

		$title = array(
			array(
				'title'			=>  __( 'Attendees Application', 'wph_attendees' ),
				'slug'			=> 'attendees-application',
				'post_content'	=> '[wph_attendees_application]'
			),
			array(
				'title'		=> __( 'Attendees', 'wph_attendees' ),
				'slug'		=> 'attendees',
				'post_content'	=> '[wph_attendees]'
			)
		);

		foreach( $title as $key => $title_key ){
			// If the page doesn't already exist, then create it
			if( ! get_page_by_title( $title_key['title'] ) ) {
				// Set the post ID so that we know the post was created successfully
				wp_insert_post(
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
			}
		}

	}
}