<?php

// Order to create custom pages in multisite when a new site is created
function wphackathon_create_pages_new_site($blog_id, $user_id, $domain, $path, $site_id, $meta) {

  //replace with your base plugin path E.g. dirname/filename.php
  if ( is_plugin_active_for_network( 'wphackathon-attendees/wphackathon-attendees.php' ) ) {
    switch_to_blog($blog_id);
    	wphackathon_custom_pages();
    restore_current_blog();
  } 

}




// Create custom pages for attendees
function wphackathon_custom_pages(){

}
