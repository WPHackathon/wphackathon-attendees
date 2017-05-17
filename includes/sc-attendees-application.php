<?php

// Shortcode to show the form for Attendees Application
function wphackathon_sc_attendees_application( $atts ) {

	/* Load Plugin Messages */
	include( WPH_ATTENDEES_PATH . '/includes/wph-messages.php' );

	// Enqueue Javascript file to validate form
	wp_enqueue_script('attendees-application-js', WPH_ATTENDEES_URL.'/assets/js/attendees-application.js', array('jquery'), true);

	// Add Shortcode Attributes
	$a = shortcode_atts( array(

		'skills' => array("Front-end", "Back-end", "Designer", "Copywriter"),

	), $atts );

	?>


    <!-- New Attendee Form -->

    <div id="postbox">

        <?php
        // Server fields verification. Show error message in case of required or error in fields.
        if( isset($_GET['msg']) ):
            $message = array_key_exists($_GET['msg'], $wph_error_messages) ? $wph_error_messages[$_GET['msg']] : false;
            if($message):
            ?>
        <div class="alert alert-danger" role="alert"><?php echo $message; ?></div>
        <?php
            endif;
        endif; ?>

        <form id="new_post" name="new_post" method="post" action="">

            <p>
                <label for="wph-attendee-name"><?php _e( 'First and Second Name (required)', 'wph_attendees' ); ?></label><br />
                <input type="text" id="wph-attendee-name" value="" name="wph-attendee-name" tabindex="2" size="20">
            </p>

            <p>
                <label for="wph-attendee-email"><?php _e( 'Email - we use Gravatar service for the photograph (required)', 'wph_attendees'); ?></label><br />
                <input type="email" id="wph-attendee-email" name="wph-attendee-email" value="" tabindex="3" size="20">
            </p>

            <p>
                <label for="wph-attendee-description"><?php _e( 'Talk us about: what do you do and what is your experience? (required)', 'wph_attendees' ); ?></label><br />
                <textarea id="wph-attendee-description" name="wph-attendee-description" tabindex="4" cols="50" rows="6"></textarea>
            </p>

            <p>
                <label for="wph-attendee-twitter"><?php _e( 'Twitter Username', 'wph_attendees'); ?></label><br />
                <input type="text" id="wph-attendee-twitter" name="wph-attendee-twitter" value="" tabindex="5" size="20">
            </p>

            <p>
                <label for="wph-attendee-org-user"><?php _e( 'WordPress.org Username', 'wph_attendees'); ?></label><br />
                <input type="text" id="wph-attendee-org-user" name="wph-attendee-org-user" value="" tabindex="6" size="20">
            </p>

            <p>
                <label><?php _e( 'Select your Skill (required)', 'wph_attendees' ); ?></label><br />
				<?php

				$select_cats = wp_dropdown_categories( array( 'echo' => 0, 'taxonomy' => 'skill', 'hide_empty' => 0 ) );
				$select_cats = str_replace( "name='cat' id=", "name='cat[]' id=", $select_cats );
				echo $select_cats;

				?>

            </p>

			<?php

			// Add Organizations Query arguments
			$args = array(
				'order'          => 'asc',
				'orderby'        => 'title',
				'posts_per_page' => 30,
				'post_type'      => 'organization'
			);

			$the_query = new WP_Query( $args ); ?>

			<?php if ( $the_query->have_posts() ) : ?>

                <p>
                    <label><?php _e( 'With which Organization in particular would you like to take part?', 'wph_attendees' ); ?></label><br />

                    <select class="" name="wph-attendee-organization-selection" tabindex="8">

                        <option value="<?php _e( 'Anyone', 'wph_attendees' ); ?>"><?php _e( 'Anyone', 'wph_attendees' ); ?></option>

                        <!-- the loop -->
						<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

                            <option value="<?php get_the_ID(); ?>"><?php the_title(); ?></option>

						<?php endwhile; ?>
                        <!-- end of the loop -->

                    </select>

                </p>

				<?php wp_reset_postdata(); ?>

			<?php else : ?>

			<?php endif; ?>
            </p>

            <p>
                <label for="wph-attendee-explanation"><?php _e( 'Why do you want to participate in WPHackathon? (required)', 'wph_attendees' ); ?></label><br />
                <textarea id="wph-attendee-explanation" name="wph-attendee-explanation" tabindex="9" cols="50" rows="6"></textarea>
            </p>

            <div class="alert alert-danger hidden" role="alert"><?php _e('Some fields are required', 'wph_attendees'); ?></div>

            <p><input type="submit" value="<?php _e( 'Participate', 'wph_attendees' ); ?>" tabindex="10" id="submit" name="submit" /></p>

            <input type="hidden" name="action" value="new_attendee" />

        </form>

    </div>

    <!--// New Attendee Form -->

	<?php

}
add_shortcode( 'wph_attendees_application', 'wphackathon_sc_attendees_application' );


/**
 * This function checks the Attendees Application form.
 * If everything is OK saves in database.
 * Otherwise return to the same page with a message.
 */
function wphackathon_attendees_application_register(){

	if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] && $_POST['action'] == "new_attendee" ) ) {

	    // Check the required fields
        if(
            empty( $_POST['wph-attendee-name'] ) ||
            empty( $_POST['wph-attendee-email'] ) ||
            empty( $_POST['wph-attendee-description'] ) ||
            empty( $_POST['wph-attendee-explanation'] ) ||
            empty( $_POST['wph-attendee-explanation'] )
        ){
            wp_redirect($_SERVER['HTTP_REFERER'] . '?msg=incomplete-fields');
	        exit;
        }

        // Check if the attendee email is correct
        if( !is_email( $_POST['wph-attendee-email'] ) ){
	        wp_redirect($_SERVER['HTTP_REFERER'] . '?msg=field-values-not-valid');
	        exit;
        }

		// Asign the form content to variables
		$name         = sanitize_text_field( $_POST['wph-attendee-name'] );
		$description  = sanitize_text_field( $_POST['wph-attendee-description'] );
		$email        = sanitize_email( $_POST['wph-attendee-email'] );
		$twitter      = sanitize_text_field( $_POST['wph-attendee-twitter'] );
		$orguser      = sanitize_text_field( $_POST['wph-attendee-org-user'] );
		$explanation  = sanitize_text_field( $_POST['wph-attendee-explanation'] );
		$organization = isset( $_POST['wph-attendee-organization-selection'] ) ? $_POST['wph-attendee-organization-selection'] : false;
		$skill        = $_POST['cat'];

		// Add the content of the form to $post as an array
		$post = array(
			'post_title'	  => $name,
			'post_content'	=> $description,
			'post_category' => $skill,
			'post_status'	  => 'draft',
			'post_type'	    => 'attendee',
			'meta_input'    => array(
				'attendee_email'        => $email,
				'attendee_twitter'      => $twitter,
				'attendee_orguser'      => $orguser,
				'attendee_explanation'  => $explanation,
				'attendee_organization' => $organization,
			),
		);

		$post_id = wp_insert_post( $post );

		if( is_wp_error( $post_id ) ) {
			// Redirect the user to the same page with a message error
			wp_redirect($_SERVER['HTTP_REFERER'] . '?msg=err-attendees-saving');
        }
        else{
		    // Save the catogyr
	        wp_set_post_terms( $post_id, $_POST['cat'], 'skill', false );

	        // Redirect the user to the Attendees list with a success message
            wp_redirect(get_bloginfo('url') . "/attendees?msg=attendees-register-success");
        }

	} // end IF

}
add_action('template_redirect', 'wphackathon_attendees_application_register');