<?php

// Shortcode to show the list of Attendees
function wphackathon_sc_attendees( $atts ) {

  // Add Shortcode Attributes
  $atts = shortcode_atts(
		array(
			'order'          => 'asc',
			'orderby'        => 'title',
			'posts_per_page' => 10000,
			'columns'        => 3,
		), $atts );

  // Add Attendees Query arguments
  $args = array(
    'order'          => $atts['order'],
    'orderby'        => $atts['orderby'],
    'posts_per_page' => $atts['posts_per_page'],
    'columns'        => $atts['columns'],
    'post_type'      => 'attendee'
  );

  $the_query = new WP_Query( $args ); ?>

  <?php if ( $the_query->have_posts() ) : ?>

    <div id="wph-attendees">
      <ul class="wph-attendee-list wph-columns-<?php echo $atts['columns']; ?>">

    <!-- the loop -->
      <?php while ( $the_query->have_posts() ) : $the_query->the_post();

      $email = get_post_meta( get_the_ID(), 'attendee_email', true );
      $twitter = get_post_meta( get_the_ID(), 'attendee_twitter', true );

      ?>

        <li>
          <?php echo get_avatar( $email, 120 ); ?>
          <a href="<?php the_permalink(); ?>" class="attendee-name" title="<?php _e( 'Attendee', 'wph_attendees' ); ?>"><?php echo the_title(); ?></a>

          <?php if ( !empty( $twitter ) ) : ?>
            <a href="//twitter.com/<?php echo $twitter; ?>" class="attendee-twitter" target="_blank">@<?php echo $twitter; ?></a>
          <?php endif; ?>
        </li>

      <?php endwhile; ?>
    <!-- end of the loop -->

      </ul>
    </div>

    <?php wp_reset_postdata(); ?>

  <?php else : ?>
      <p><?php _e( 'Sorry, still no attendees for this WPHackathon.' ); ?></p>
  <?php endif;

}
add_shortcode( 'wph_attendees', 'wphackathon_sc_attendees' );
