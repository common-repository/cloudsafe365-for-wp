<?php
 ##_start help
 if (get_bloginfo('version') >= 3.3):
  function cloudsafe365_menupage_help() {
   //Use the new help tabs
   $screen = get_current_screen();
   $screen->add_help_tab(array(
    'id' => 'cloudsafe365-menupage',
    'title' => 'menupage',
    'content' => cloudsafe365_menupage_text()
   ));
  }

  function cloudsafe365_malware_help() {
   //Use the new help tabs
   $screen = get_current_screen();
   $screen->add_help_tab(array(
    'id' => 'cloudsafe365-malware',
    'title' => 'malware',
    'content' => cloudsafe365_malware_text()
   ));
  }

  function cloudsafe365_backup_help() {
   //Use the new help tabs
   $screen = get_current_screen();
   $screen->add_help_tab(array(
    'id' => 'cloudsafe365-download',
    'title' => 'backup',
    'content' => cloudsafe365_backup_text()
   ));
  }

  function cloudsafe365_protection_help() {
   //Use the new help tabs
   $screen = get_current_screen();
   $screen->add_help_tab(array(
    'id' => 'cloudsafe365-protection',
    'title' => 'protection',
    'content' => cloudsafe365_protection_text()
   ));
  }

  function cloudsafe365_log_help() {
   //Use the new help tabs
   $screen = get_current_screen();
   $screen->add_help_tab(array(
    'id' => 'cloudsafe365-download',
    'title' => 'log',
    'content' => cloudsafe365_log_text()
   ));
  }

  function cloudsafe365_setup_help() {
   //Use the new help tabs
   $screen = get_current_screen();
   $screen->add_help_tab(array(
    'id' => 'cloudsafe365-download',
    'title' => 'setup',
    'content' => cloudsafe365_setup_text()
   ));
  }

  function cloudsafe365_reporting_help() {
   //Use the new help tabs
   $screen = get_current_screen();
   $screen->add_help_tab(array(
    'id' => 'cloudsafe365-attacks',
    'title' => 'Reporting',
    'content' => help_text_reports()
   ));
  }


  function cloudsafe365_options_help() {
   $screen = get_current_screen();
   $screen->add_help_tab(array(
    'id' => 'cloudsafe365-options',
    'title' => 'cloudsafe365 for WP Options',
    'content' => help_text_options()
   ));
  }

  function cloudsafe365_recovery_help() {
   $screen = get_current_screen();
   $screen->add_help_tab(array(
    'id' => 'cloudsafe365-recovery',
    'title' => 'cloudsafe365 for WP recovery',
    'content' => help_text_recovery()
   ));
  }
##_______________________________________________________________________________________________________________________

 else:
  function add_cloudsafe365_menupage_help() {
   $text = __(cloudsafe365_menupage_text());
   add_contextual_help('cloudsafe365_page_cloudsafe365-reports', $text);
   add_action('admin_init', 'add_cloudsafe365_malware_help');
  }

  function add_cloudsafe365_malware_help() {
   $text = __(cloudsafe365_malware_text());
   add_contextual_help('cloudsafe365_page_cloudsafe365-reports', $text);
   add_action('admin_init', 'add_cloudsafe365_malware_help');
  }

  function add_cloudsafe365_backup_help() {
   $text = __(cloudsafe365_backup_text());
   add_contextual_help('cloudsafe365_page_cloudsafe365-reports', $text);
   add_action('admin_init', 'add_cloudsafe365_backup_help');
  }

  function add_cloudsafe365_protection_help() {
   $text = __(cloudsafe365_protection_text());
   add_contextual_help('cloudsafe365_page_cloudsafe365-reports', $text);
   add_action('admin_init', 'add_cloudsafe365_protection_help');

  function add_cloudsafe365_log_help() {
   $text = __(cloudsafe365_log_text());
   add_contextual_help('cloudsafe365_page_cloudsafe365-reports', $text);
  }
  add_action('admin_init', 'add_cloudsafe365_log_help');

  function add_cloudsafe365_setup_help() {
   $text = __(cloudsafe365_setup_text());
   add_contextual_help('cloudsafe365_page_cloudsafe365-reports', $text);
  }
  add_action('admin_init', 'add_cloudsafe365_setup_help');
  }

  function add_cloudsafe365_reporting_help() {
   $text = __(help_text_reports());
   add_contextual_help('cloudsafe365_page_cloudsafe365-reports', $text);
  }
  add_action('admin_init', 'add_cloudsafe365_reporting_help');

  function add_cloudsafe365_options_help() {
   $text = __(help_text_options());
   add_contextual_help('cloudsafe365_page_cloudsafe365-options', $text);
  }
  add_action('admin_init', 'add_cloudsafe365_options_help');

  function add_cloudsafe365_recovery_help() {
   $text = wp_remote_fopen(CS365 . '/reporting/help.php?help=help_recovery_help');
   add_contextual_help('cloudsafe365_page_cloudsafe365-recovery', $text);
  }
  add_action('admin_init', 'add_cloudsafe365_recovery_help');

 endif;
 ##_start end

 function cloudsafe365_malware_text() {
  return cloudsafe365_malware_content();
 }

 function help_text_options() {
  return cloudsafe365_options_content();
 }

 function help_text_reports() {
  return cloudsafe365_reports_content();
 }

 function help_text_recovery() {
  return cloudsafe365_recovery_content();
 }

 function cloudsafe365_backup_text() {
  return cloudsafe365_backup_content();
 }

 function cloudsafe365_protection_text() {
  return cloudsafe365_protection_content();
 }

 function cloudsafe365_menupage_text() {
  return cloudsafe365_menupage_content();
 }

 function cloudsafe365_log_text() {
  return cloudsafe365_log_content();
 }

 function cloudsafe365_setup_text() {
  return cloudsafe365_setup_content();
 }

?>