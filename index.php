<?php
/*
Plugin Name: WPHackathon - Attendees
Version: 1.0.0
Plugin URI: https://wphackathon.com
Description: Creates a Custom Post Type to manage the Attendees participating in the WPHackathon
Author: WPHackathon
Author URI: https://www.wphackathon.com
*/

define( 'WPH_ATTENDEES_PATH', dirname( __FILE__ ) );

$wph_textdomain = 'wphackathon-cpt-attendees';
$wph_ct_textdomain = 'wphackathon-ct-attendees-skill';

/* Custom Post Type - Attendees */
include( WPH_ATTENDEES_PATH . '/includes/cpt-attendees.php' );

/* Custom Taxonomy Attendees Skills */
include( WPH_ATTENDEES_PATH . '/includes/ct-skills.php' );

/* Shortcode - Attendees */
include( WPH_ATTENDEES_PATH . '/includes/sc-attendees.php' );

/* Shortcode - Attendees Application */
include( WPH_ATTENDEES_PATH . '/includes/sc-attendees-application.php' );

/* Widget - Attendees */
// include( WPH_ATTENDEES_PATH . '/includes/widget-attendees.php' );
