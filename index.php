<?php
/*
Plugin Name: WPHackathon - Attendees
Version: 1.0.1
Plugin URI: https://wphackathon.com
Description: Creates a Custom Post Type to manage the Attendees participating in the WPHackathon
Author: WPHackathon
Author URI: https://www.wphackathon.com
*/

define( 'WPH_ATTENDEES_PATH', dirname( __FILE__ ) );
define( 'WPH_ATTENDEES_FOLDER', basename( WPH_ATTENDEES_PATH ) );
define( 'WPH_ATTENDEES_URL', plugins_url() . '/' . WPH_ATTENDEES_FOLDER );

/* Custom Post Type - Attendees */
include( WPH_ATTENDEES_PATH . '/includes/cpt-attendees.php' );

/* Custom Taxonomy Attendees Skills */
include( WPH_ATTENDEES_PATH . '/includes/ct-skills.php' );

/* Shortcode - Attendees */
include( WPH_ATTENDEES_PATH . '/includes/sc-attendees.php' );

/* Shortcode - Attendees Application */
include( WPH_ATTENDEES_PATH . '/includes/sc-attendees-application.php' );

/* Create custom pages for attendeens*/
include( WPH_ATTENDEES_PATH . '/includes/create-custom-pages.php' );
register_activation_hook( __FILE__, 'wphackathon_custom_pages' );

/* Widget - Attendees */
// include( WPH_ATTENDEES_PATH . '/includes/widget-attendees.php' );

class WPH_ATTENDEES_Base
{

    public function __construct()
    {
        $this->loadPluginTextDomain();
        $this->registerScripts();
        $this->removePluginUpdates();

    }

    public function loadPluginTextDomain() {
        add_action( 'plugins_loaded', array( $this, 'loadPluginTextDomainCallBack' ) );
    }
    public function loadPluginTextDomainCallBack() {
        load_plugin_textdomain( 'wph_attendees', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
    }

    function registerScripts(){
        add_action( 'wp_enqueue_scripts', array( $this, 'registerJSScripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'registerCSSScripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'registerJSadminScripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'registerCSSadminScripts' ) );
    }

    public function registerJSScripts( $hook ) {
        wp_enqueue_script( 'jquery' );
    }

    public function registerCSSScripts( $hook ) {
    }

    public function registerJSadminScripts( $hook ) {
    }

    public function registerCSSadminScripts( $hook ) {
    }

    function removePluginUpdates(){
        add_filter('site_transient_update_plugins', array( $this, 'removePluginUpdatesCallback' ), 10, 1);
    }
    function removePluginUpdatesCallback($value) {
        if (!empty($value) && is_object($value) && !isset($value->response[ plugin_basename(__FILE__) ])){
            unset($value->response[ plugin_basename(__FILE__) ]);
        }
        return $value;
    }

}

add_action( 'admin_notices', 'wph_attendees_adminErrorsShow');
function wph_attendees_adminErrorsShow(){
    global $wph_attendees_adminErrors_message;
    if (!empty($wph_attendees_adminErrors_message)) {
        foreach ($wph_attendees_adminErrors_message as $message) {
            $class = 'notice notice-error';
            printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
        }
    }
}

$WPH_ATTENDEES_plugin_base = new WPH_ATTENDEES_Base();