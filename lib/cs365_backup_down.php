<?php
 if (!defined('ABSPATH'))
   exit;
 require (ABSPATH . WPINC . '/pluggable.php');
 global $current_user;
 get_currentuserinfo();


 if (!isset($_GET['db'])) {
  if (!preg_match('/\w/xsi', $current_user->ID))
    exit('Seems you are not logged in');
  if (!preg_match('/\w/xsi', $current_user->user_login))
    exit('Cannont see user login');
  if (!current_user_can('administrator'))
    exit(' You are not the site administrator');
 }

 $options = get_option('cloudsafe365_plugin_options');

 if (md5($options['cloudsafe365_api_key']) != preg_replace('/\W/xsi', '', $_GET['k']))
   exit('Things do not match up');
 if (!isset($options['confirmcheck']))
   Only_once();
 if (strlen($options['confirmcheck']) != 32)
   exit('Seem to be a issue with your user status');
 if ($options['confirmcheck'] != $_GET['c'])
   exit('Seem to be a issue with confirmation');


 $options['cloudsafe365_download'] = time();
 if (isset($options['confirmcheck'])) {
  unset($options['confirmcheck']);
 }

 update_option('cloudsafe365_plugin_options', $options, '', 'yes');


 if (current_user_can('level_10') or isset($_GET['db'])) {
  require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/lib/cs365_backup.class.php');
  $backup_obj = new MySQL_Backup();
  $backup_obj->tables = array();
  $backup_obj->drop_tables = true;
  $backup_obj->struct_only = false;
  $backup_obj->comments = true;
  $backup_obj->backup_dir = '/';
  $backup_obj->fname_format = 'm_d_Y';
  $task = MSB_DOWNLOAD;
  $filename = '';
  $use_gzip = true;


  if (!$backup_obj->Execute($task, $filename, $use_gzip)) {
   $output = $backup_obj->error;
  }
 }
 function Only_once() {
  ?>
  <table border="0" cellpadding="10" cellspacing="0" align="center">
   <tr>
    <td><img src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/cloudsafe365_120.png" /></td>
    <td><b><h2>Security Message</h2></b></td>
   </tr>
  </table>
  <?PHP
  echo 'For Security reasons you will need to go back to the download page to refresh download capabilities again.<br><br>';
  echo 'Link to page is <a href="' . admin_url() . 'admin.php?page=cloudsafe365-download">Down load Admin Page</a>';
  exit;
 }
?>