<?php

/**
 * Class WP_Hackaton_Atendees_Application
 *
 * Manages the attendees application form
 */
class WP_Hackaton_Atendees_Application {

	/**
	 * Shortcode name
	 *
	 * @var string
	 */
	private $shortcode = 'wph_attendees_application';

	/**
	 * Save validation errors list
	 *
	 * @var array
	 */
	private $errors = array();

	/**
	 * Save submitted fields values (once they are sanitized)
	 *
	 * @var array
	 */
	private $sanitized_values = array();


	/**
	 * Initialize the class
	 */
	public function init() {
		add_action( 'transition_post_status', array( $this, 'send_mails_on_publish' ), 10, 3 );
		add_action( 'template_redirect', array( $this, 'validate' ), 10, 3 );
		$this->register_shortcode();
	}

	/**
	 * Register the shortcode
	 */
	public function register_shortcode() {
		add_shortcode( $this->shortcode, array( $this, 'render' ) );
	}

	/**
	 * Return a list with the fields attributes, sanitize callbacks, etc
	 */
	private function get_fields_attributes() {
		return array(
			'name'         => array(
				'mandatory' => true,
				'sanitize'  => 'sanitize_text_field'
			),
			'description'  => array(
				'mandatory' => true,
				'sanitize'  => array( $this, 'sanitize_description' )
			),
			'email'        => array(
				'mandatory' => true,
				'sanitize'  => array( $this, 'sanitize_email' )
			),
			'twitter'      => array(
				'mandatory' => false,
				'sanitize'  => 'sanitize_text_field'
			),
			'orguser'      => array(
				'mandatory' => false,
				'sanitize'  => 'sanitize_text_field'
			),
			'explanation'  => array(
				'mandatory' => true,
				'sanitize'  => array( $this, 'sanitize_explanation' )
			),
			'organization' => array(
				'mandatory' => true,
				'sanitize'  => array( $this, 'sanitize_organization' )
			),
			'skill'        => array(
				'mandatory' => true,
				'sanitize'  => array( $this, 'sanitize_skill' )
			),
		);
	}

	/**
	 * Get a field submitted value
	 *
	 * @param string $name
	 *
	 * @return mixed|string
	 */
	private function get_field_value( $name ) {
		return isset( $this->sanitized_values[ $name ] ) ? $this->sanitized_values[ $name ] : '';
	}

	/**
	 * Check if the form has errors
	 */
	private function has_errors() {
		$count = 0;
		foreach ( $this->errors as $field_errors ) {
			$count += count( $field_errors );
		}
		return ( $count > 0 );
	}

	/**
	 * Validate the form
	 */
	public function validate() {
		if ( ! shortcode_exists( $this->shortcode ) ) {
			return;
		}

		$this->errors = array();

		$action = isset( $_POST['action'] ) ? $_POST['action'] : false;
		if ( 'new_attendee' !== $action ) {
			return;
		}

		$values = isset( $_POST['wph-attendee'] ) ? $_POST['wph-attendee'] : false;
		if ( ! $values ) {
			return;
		}

		check_admin_referer( 'new_attendee' );

		$this->sanitized_values = array();

		foreach ( $this->get_fields_attributes() as $field_name => $field_attr ) {
			$value = isset( $values[ $field_name ] ) ? $values[ $field_name ] : '';

			$this->errors[ $field_name ] = array();

			$value = call_user_func( $field_attr['sanitize'], $value );

			$is_mandatory = $field_attr['mandatory'];

			switch ( $field_name ) {
				case 'organization': {
					if ( ! $this->get_organizations() ) {
						$is_mandatory = false;
					}
					break;
				}
				case 'skill': {
					if ( ! $this->get_skills() ) {
						$is_mandatory = false;
					}
					break;
				}
				default: {
					break;
				}
			}

			if ( $is_mandatory && empty( $value ) ) {
				$this->errors[ $field_name ][] = __( 'Required field', 'wph_attendees' );
			}

			$this->sanitized_values[ $field_name ] = $value;
		}

		/**
		 * Allows to filter the submission errors
		 *
		 * @param array $errors List of errors in the submission
		 * @param array $sanitized_values List of sanitized values
		 * @param array $values List of submitted values
		 */
		$this->errors = apply_filters( 'wph_validate_attendees_form', $this->errors, $this->sanitized_values, $values );

		if ( ! $this->has_errors() ) {
			$this->process( $this->sanitized_values );
		}
	}

	/**
	 * Process the form values
	 *
	 * @param array $values
	 */
	public function process( $values = array() ) {
		$post = array(
			'post_title'    => $values['name'],
			'post_content'  => $values['description'],
			'post_status'   => 'draft',
			'post_type'     => 'attendee',
			'meta_input'    => array(
				'attendee_email'        => $values['email'],
				'attendee_twitter'      => $values['twitter'],
				'attendee_orguser'      => $values['orguser'],
				'attendee_explanation'  => $values['explanation'],
				'attendee_organization' => $values['organization'],
			),
		);


		$post_id = wp_insert_post( $post );

		if ( is_wp_error( $post_id ) ) {
			wp_die( __( 'There was an error processing the form. Please, try again.', 'wph_attendees' ) );
		}

		wp_set_post_terms( $post_id, $values['skill'], 'skill', false );

		/**
		 * Triggered when the attendees form has already been processed
		 *
		 * @param int $post_id The post ID generated for the new attendee
		 */
		do_action( 'wph_processed_attendees_form', $post_id );

		// Send the email to admin
		$to      = get_option( 'admin_email' );
		$url     = get_edit_post_link( $post_id );
		$subject = __( 'New WPHackathon attendee', 'wph_attendees' );
		$message = __( 'There is a new attendee for the WPHackathon.<p> Please, <a href="' . esc_url( $url ) . '" title="attendee">login in to the web and check it</a>.</p>', 'wph_attendees' );
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		wp_mail( $to, $subject, $message, $headers );

		// Send the email to the attendee
		$to      = $values['email'];
		$subject = __( 'Thanks for your application to WPHackathon', 'wph_attendees' );
		$message = __( 'Hey ' . $values['name'] . ',<br/><br/> Thank you for your interest in WPHackathon. Now, the organisers will check your application.<br/><br/> You will receive an email when your application has been approved.<br/><br/> Thanks again for your interest in WPHackathon.<br> <p><strong>The WPHackathon team</strong></p>', 'wph_attendees' );
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		wp_mail( $to, $subject, $message, $headers );

		wp_redirect( add_query_arg( 'submitted', 'true' ) );
		exit;
	}

	/**
	 * Sanitize an email string
	 *
	 * @param $value
	 *
	 * @return bool|string Sanitized email or false if this is not a valid one
	 */
	public function sanitize_email( $value ) {
		$value = sanitize_email( $value );
		return is_email( $value );
	}

	/**
	 * Sanitize explanation field
	 *
	 * Removes all HTML tags from the field
	 *
	 * @param $value
	 *
	 * @return string Sanitized explanation field
	 */
	public function sanitize_explanation( $value ) {
		return wp_strip_all_tags( $value );
	}

	/**
	 * Sanitize explanation field
	 *
	 * Removes forbidden HTML tags in post content
	 *
	 * @param $value
	 *
	 * @return string Sanitized explanation field
	 */
	public function sanitize_description( $value ) {
		return wp_filter_post_kses( $value );
	}

	/**
	 * Sanitize organization field
	 *
	 * @param $value
	 *
	 * @return bool|int
	 */
	public function sanitize_organization( $value ) {
		$value = absint( $value );
		$allowed_ids = wp_list_pluck( $this->get_organizations(), 'ID' );
		if ( ! in_array( $value, $allowed_ids ) ) {
			return '';
		}

		return $value;
	}

	/**
	 * Sanitize skill field
	 *
	 * @param $value
	 *
	 * @return bool|int
	 */
	public function sanitize_skill( $value ) {
		$value = absint( $value );
		$allowed_ids = wp_list_pluck( $this->get_skills(), 'term_id' );
		if ( ! in_array( $value, $allowed_ids ) ) {
			return '';
		}

		return $value;
	}

	/**
	 * Return a list of skill terms
	 *
	 * @return array|int|WP_Error
	 */
	private function get_skills() {
		return get_terms( array(
			'hide_empty' => 0,
			'taxonomy' => 'skill'
		) );
	}

	/**
	 * Return a list of organizations
	 *
	 * @return array
	 */
	private function get_organizations() {
		return get_posts( array(
			'order'          => 'asc',
			'orderby'        => 'title',
			'posts_per_page' => 30,
			'post_type'      => 'organization'
		) );
	}




	/**
	 * Send an email to attendee when admin publish the post
	 *
	 * @param string $new_status
	 * @param string $old_status
	 * @param WP_Post $post
	 */
	function send_mails_on_publish( $new_status, $old_status, $post ) {
		if (( $new_status == 'publish' ) && ( $old_status !== 'publish' )
		    && ( $post -> post_type == 'attendee' )){
			$email = get_post_meta($post->ID, "attendee_email", $single = true);
			$subject = __('Your application has been approved!', 'wph_attendees');
			$name = get_the_title($post->ID);
			$message = __('Hey '. $name .',<br/><br/> The organisers have approved your application. You can see it <a href="' . get_permalink( $post ) . '">here</a>.<br/><br/>  Thank you for your interest in WPHackathon.<br> <p><strong>The WPHackathon team</strong></p>.', 'wph_attendees');
			$headers = array( 'Content-Type: text/html; charset=UTF-8' );

			wp_mail( $email, $subject, $message, $headers );
		}
	}

	private function _field_error( $field_name ) {
		if ( empty( $this->errors[ $field_name ] ) ) {
			return;
		}

		?>
		<div class="wph-field-error alert alert-danger">
			<?php foreach ( $this->errors[ $field_name ] as $error ): ?>
				<p><?php echo $error; ?></p>
			<?php endforeach; ?>
		</div>
		<?php
	}


	/**
	 * Render the shortcode
	 */
	function render() {

		// Enqueue Javascript file to validate form
		wp_enqueue_script( 'attendees-application-js', WPH_ATTENDEES_URL . '/assets/js/attendees-application.js', array( 'jquery' ), '', true );

		$skills = $this->get_skills();
		$organizations = $this->get_organizations();
		?>

		<?php if ( isset( $_GET['submitted'] ) ): ?>
			<h2><?php esc_html_e( "Thanks for you interest in the next WPHackathon!", 'wph_attendees' ) ?></h2>
			<p><?php _e( "We're analysing your application and soon you will be listed in this page with rest of the attendees.", 'wph_attendees' ) ?></p>
			<?php return; ?>
		<?php endif; ?>

		<?php if ( $this->has_errors() ): ?>
			<div class="alert alert-danger wph-form-error">
				<?php esc_html_e( 'There are errors in the submission', 'wph_attendees' ); ?>
			</div>
		<?php endif; ?>

		<!-- New Attendee Form -->


		<form id="new-attendee" method="post" action="">
			<p>
				<label for="wph-attendee-name"><?php _e( 'First and Second Name (required)', 'wph_attendees' ); ?></label><br/>
				<input type="text" id="wph-attendee-name" value="<?php echo esc_attr( $this->get_field_value( 'name' ) ); ?>" name="wph-attendee[name]" size="20">
				<?php $this->_field_error( 'name' ); ?>
			</p>

			<p>
				<label for="wph-attendee-email"><?php _e( 'Email - we use Gravatar service for the photograph (required)', 'wph_attendees' ); ?></label><br/>
				<input type="email" id="wph-attendee-email" name="wph-attendee[email]" value="<?php echo esc_attr( $this->get_field_value( 'email' ) ); ?>" size="20">
				<?php $this->_field_error( 'email' ); ?>
			</p>

			<p>
				<label for="wph-attendee-description"><?php _e( 'Talk us about: what do you do and what is your experience? (required)', 'wph_attendees' ); ?></label><br/>
				<textarea id="wph-attendee-description" name="wph-attendee[description]" cols="50" rows="6"><?php echo esc_textarea( $this->get_field_value( 'description' ) ); ?></textarea>
				<?php $this->_field_error( 'description' ); ?>
			</p>

			<p>
				<label for="wph-attendee-twitter"><?php _e( 'Twitter Username', 'wph_attendees' ); ?></label><br/>
				<input type="text" id="wph-attendee-twitter" name="wph-attendee[twitter]" value="<?php echo esc_attr( $this->get_field_value( 'twitter' ) ); ?>" size="20">
				<?php $this->_field_error( 'twitter' ); ?>
			</p>

			<p>
				<label for="wph-attendee-orguser"><?php _e( 'WordPress.org Username', 'wph_attendees' ); ?></label><br/>
				<input type="text" id="wph-attendee-orguser" name="wph-attendee[orguser]" value="<?php echo esc_attr( $this->get_field_value( 'orguser' ) ); ?>" size="20">
				<?php $this->_field_error( 'orguser' ); ?>
			</p>

			<p>
				<label for="wph-attendee-skill"><?php _e( 'Select your Skill (required)', 'wph_attendees' ); ?></label><br/>
				<select name="wph-attendee[skill]" id="wph-attendee-skill" class="postform">
					<?php foreach ( $skills as $skill ): ?>
						<option value="<?php echo $skill->term_id; ?>" <?php checked( $this->get_field_value( 'skill' ), $skill->term_id ) ?>><?php echo $skill->name; ?></option>
					<?php endforeach; ?>
				</select>
				<?php $this->_field_error( 'skill' ); ?>
			</p>

			<?php if ( ! empty( $organizations ) ): ?>
				<p>
					<label for="wph-attendee-organization"><?php _e( 'With which Organization in particular would you like to take part?', 'wph_attendees' ); ?></label><br/>
					<select class="" name="wph-attendee[organization]" id="wph-attendee-organization">
						<option value="<?php _e( 'Anyone', 'wph_attendees' ); ?>"><?php _e( 'Anyone', 'wph_attendees' ); ?></option>
						<!-- the loop -->
						<?php foreach ( $organizations as $organization ): ?>
							<option value="<?php $organization->ID; ?>" <?php checked( $this->get_field_value( 'organization' ), $organization->ID ) ?>><?php echo esc_html( get_the_title( $organization->ID ) ); ?></option>
						<?php endforeach; ?>
						<!-- end of the loop -->
					</select>
					<?php $this->_field_error( 'organization' ); ?>
				</p>
			<?php endif; ?>

			<p>
				<label for="wph-attendee-explanation"><?php _e( 'Why do you want to participate in WPHackathon? (required)', 'wph_attendees' ); ?></label><br/>
				<textarea id="wph-attendee-explanation" name="wph-attendee[explanation]" cols="50" rows="6"><?php echo esc_textarea( $this->get_field_value( 'explanation' ) ); ?></textarea>
				<?php $this->_field_error( 'explanation' ); ?>
			</p>


			<?php
			/**
			 * Triggered after the attendees submission form fields are rendered
			 *
			 * @param WP_Hackaton_Atendees_Application $this Instance of this class
			 */
			do_action( 'wph_after_attendee_form_fields', $this );
			?>

			<div class="alert alert-danger hidden" role="alert"><?php _e( 'Some fields are required', 'wph_attendees' ); ?></div>

			<p>
				<input type="submit" value="<?php _e( 'Participate', 'wph_attendees' ); ?>" id="submit" name="submit"/>
			</p>

			<input type="hidden" name="action" value="new_attendee"/>
			<?php wp_nonce_field( 'new_attendee' ); ?>
		</form>


		<!--// New Attendee Form -->

		<?php

	}
}

