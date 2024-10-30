<?php
 function cloudsafe365_menus() {
//if the user is in plugins.php we want then to see
//the option in right hand panel not the fill links.
  if (check_for_escape_out()) {
   $options = get_option('cloudsafe365_plugin_options');
   if (strlen($options['cloudsafe365_api_key']) != 33)
     add_setup_menu();
   return;
  }
  elseif ((isset($_GET["page"])) && ($_GET["page"] == 'cloudsafe365-setup')) {
   add_setup_menu();
   return;
  }

  $add_menu_page = add_menu_page('cloudsafe365_for_WP', 'cloudsafe365 WP', 'manage_options', 'cloudsafe365', 'cloudsafe365_admin_run', WP_PLUGIN_URL . '/cloudsafe365-for-wp/images/cloudicon.png');
  if (get_bloginfo('version') >= 3.3):
   if ($add_menu_page)
     add_action('load-' . $add_menu_page, 'cloudsafe365_menupage_help');
  endif;

  $cloudsafe365_malware = add_submenu_page('cloudsafe365', 'cloudsafe365 for WP malware ', 'malware', 'manage_options', 'cloudsafe365-malware', 'cloudsafe365_admin_malware');
  if (get_bloginfo('version') >= 3.3):
   if ($cloudsafe365_malware)
     add_action('load-' . $cloudsafe365_malware, 'cloudsafe365_malware_help');
  endif;

  $cloudsafe365_protection = add_submenu_page('cloudsafe365', 'cloudsafe365 for WP protection ', 'Protection', 'manage_options', 'cloudsafe365-protection', 'cloudsafe365_admin_protection');
  if (get_bloginfo('version') >= 3.3):
   if ($cloudsafe365_protection)
     add_action('load-' . $cloudsafe365_protection, 'cloudsafe365_protection_help');
  endif;
  $cloudsafe365_download = add_submenu_page('cloudsafe365', 'cloudsafe365 for WP download ', 'Backup', 'manage_options', 'cloudsafe365-download', 'cloudsafe365_admin_download');
  if (get_bloginfo('version') >= 3.3):
   if ($cloudsafe365_download)
     add_action('load-' . $cloudsafe365_download, 'cloudsafe365_backup_help');
  endif;

  $cloudsafe365_reporting_page = add_submenu_page('cloudsafe365', 'cloudsafe365 for WP Reporting', 'Analytics', 'manage_options', 'cloudsafe365-reports', 'cloudsafe365_admin_report');
  if (get_bloginfo('version') >= 3.3):
   if ($cloudsafe365_reporting_page)
     add_action('load-' . $cloudsafe365_reporting_page, 'cloudsafe365_reporting_help');
  endif;

  $cloudsafe365_reporting_options = add_submenu_page('cloudsafe365', 'cloudsafe365 for WP Recovery', 'Recovery', 'manage_options', 'cloudsafe365-recover', 'cloudsafe365_admin_recover');
  if (get_bloginfo('version') >= 3.3):
   if ($cloudsafe365_reporting_options)
     add_action('load-' . $cloudsafe365_reporting_options, 'cloudsafe365_recovery_help');
  endif;

  $cloudsafe365_reporting_options = add_submenu_page('cloudsafe365', 'cloudsafe365 for WP Options ', 'Options', 'manage_options', 'cloudsafe365-options', 'cloudsafe365_admin_options');
  if (get_bloginfo('version') >= 3.3):
   if ($cloudsafe365_reporting_options)
     add_action('load-' . $cloudsafe365_reporting_options, 'cloudsafe365_options_help');
  endif;

  $cloudsafe365_reporting_log = add_submenu_page('cloudsafe365', 'cloudsafe365 for WP log ', 'Log', 'manage_options', 'cloudsafe365-log', 'cloudsafe365_admin_log');
  if (get_bloginfo('version') >= 3.3):
   if ($cloudsafe365_reporting_log)
     add_action('load-' . $cloudsafe365_reporting_log, 'cloudsafe365_log_help');
  endif;

  $cloudsafe365_reporting_setup = add_submenu_page('cloudsafe365', 'cloudsafe365 for WP setup ', 'Upgrade', 'manage_options', 'cloudsafe365-setup', 'cloudsafe365_admin_setup');
  if (get_bloginfo('version') >= 3.3):
   if ($cloudsafe365_reporting_setup)
     add_action('load-' . $cloudsafe365_reporting_setup, 'cloudsafe365_setup_help');
  endif;
 }

 /* ________________________________________________________________
   ________________________________________________________________ */
 function add_setup_menu() {
  $add_menu_page = add_menu_page('cloudsafe365_for_WP', 'cloudsafe365 WP ', 'manage_options', 'cloudsafe365-setup', 'cloudsafe365_admin_run', WP_PLUGIN_URL . '/cloudsafe365-for-wp/images/cloudicon.png');
  if (get_bloginfo('version') >= 3.3):
   if ($add_menu_page)
     add_action('load-' . $add_menu_page, 'cloudsafe365_menupage_help');
  endif;

  $cloudsafe365_reporting_setup = add_submenu_page('cloudsafe365', 'cloudsafe365 for WP setup ', 'Setup', 'manage_options', 'cloudsafe365-setup', 'cloudsafe365_admin_setup');
  if (get_bloginfo('version') >= 3.3):
   if ($cloudsafe365_reporting_setup)
     add_action('load-' . $cloudsafe365_reporting_setup, 'cloudsafe365_setup_help');
  endif;
 }



 function cloudsafe365_admin_report() {
  if (isset($_GET['Updated_app'])) {
   add_action('admin_notices', 'cloudsafe365_activated');
  }
  require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/dashboard.php');
 }

 function cloudsafe365_admin_protection() {
  require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/dashboard.php');
 }

 function cloudsafe365_admin_malware() {
  require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/dashboard.php');
 }
?>