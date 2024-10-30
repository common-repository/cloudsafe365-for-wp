<?php
// Don't call the file directly
 if (!defined('ABSPATH'))
   exit;

 define('QUERY_CACHE_TYPE_OFF', TRUE);

 if (!defined('SAVEQUERIES'))
   define('SAVEQUERIES', TRUE);

 if (!class_exists('Debug_Queries')) {

  class Debug_Queries
   {
   // constructor
   function debug_queries() {
    add_action('wp_footer', array($this, 'the_queries'));
   }

   // core
   function get_queries() {
    global $wpdb;
    if (isset($wpdb->queries[0][0])) {
     for ($i = 0; $i < count($wpdb->queries); $i++) {
      echo $wpdb->queries[$i][0] . "<br>";
     }
    }
    exit;
   }

   // echo in frontend
   function the_queries() {

    if (!current_user_can('DebugQueries'))
      return;

    $this->get_queries();
   }

   }

  $debug_queries = new Debug_Queries();
 }
?>