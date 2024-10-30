<?PHP




 function cloudsafe365_install() {
//delete_option
  if (current_user_can('manage_options'))
   cloudsafe365_defaults();
 }

 /* ________________________________________________________________
   developed by cloudsafe365
   //Loading defaiults from a function as needed.
   ________________________________________________________________ */
 function cloudsafe365_defaults() {
  global $cloudsafe365_version;
  $cloudsafe365_version = "1.45.6";
  $admin_email = get_bloginfo('admin_email');
  $defaults = array(
   'cloudsafe365_advanced_reporting' => 1,
   'cloudsafe365_content_scraping' => 3,
   'cloudsafe365_real_time_backups' => 1,
   'cloudsafe365_backup_when' => 2,
   'cloudsafe365_page_copying' => 0,
   'cloudsafe365_disable_right_click' => 0,
   'cloudsafe365_email_reports' => 'weekly',
   'cloudsafe365_email_address' => $admin_email,
   'cloudsafe365_version' => $cloudsafe365_version,
   'cloudsafe365_api_key' => 'none',
   'cloudsafe365_backup_database' => 1,
   'cloudsafe365_backup_themes' => 0,
   'cloudsafe365_backup_plugins' => 0,
   'cloudsafe365_backup_media' => 0,
   'cloudsafe365_backup_files' => 0,
   'cloudsafe365_email_send_backup' => 1,
   'cloudsafe365_protected_by' => 0,
   'cloudsafe365_type' => 0,
   'cloudsafe365_disable_general_hack' => 1,
   'cloudsafe365_stop_spam'=>0
  );
  update_option('cloudsafe365_plugin_options', $defaults, '', 'yes');
  return $defaults;
 }

 /* ________________________________________________________________ */


 function activation_update($options, $r = '') {
  $activate = '';
  ##Setting up the Aton self ip check system
  if (isset($_SERVER['HTTP_HOST'])):
   $aton = sprintf("%u", ip2long(gethostbyname($_SERVER['HTTP_HOST'])));
  else:
   $aton = 2;
  endif;
  $c = parse_url(get_option('home'));
  if (!isset($c['path']))
    $c['path'] = '';

  if (preg_match('/\w/xsi', $r)) {
   $r = '&r=' . get_option('home');
  }
  if (isset($options['cloudsafe365_api_key'])) {
   if (strlen($options['cloudsafe365_api_key']) != 33)
     $options['cloudsafe365_api_key'] = 'none';
  }
  else
    $options['cloudsafe365_api_key'] = 'none';

  if ($options['cloudsafe365_type'] == 1)
    $activate = '&t=1';

//using curl faster  if they have it...
if (extension_loaded('curl')) {
                         $ch = curl_init();
		     $r = CS365 . '/activate/activate.php?activate=1&aton=' . $aton . '' . $activate . '&h=' . str_replace('www.', '', $c['host'] . $c['path']) . '&' . http_build_query($options) . $r;
                         curl_setopt($ch, CURLOPT_URL, $r);
                         curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                         return curl_exec($ch);
                         }
//no curl so we will rely on wp to contact.
  return wp_remote_retrieve_body(wp_remote_get(CS365 . '/activate/activate.php?activate=1&aton=' . $aton . '' . $activate . '&h=' . str_replace('www.', '', $c['host'] . $c['path']) . '&' . http_build_query($options) . $r));
 }

 function cloudsafe365_remove_table() {
  global $wpdb;
  if (mysql_num_rows(mysql_query("SHOW TABLES LIKE 'cs365_tmp_table'")))
    $wpdb->query('truncate table cs365_tmp_table');
  $wpdb->query("INSERT INTO cs365_tmp_table SET info = ''");
 }


 function acloudsafe365_deactivate() {
  global $wpdb;
  $options = get_option('cloudsafe365_plugin_options');
  //delete remove backups
  require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/lib/cs365_common.php');
  $common = new cs365_common();
  for ($i = 0; $i < count($common->no_refer_table_array); $i++)
    $wpdb->query('drop table ' . $common->no_refer_table_array[$i]);
  wp_remote_fopen(CS365 . '/activate/unactivate.php?key=' . $options['cloudsafe365_api_key']);
  delete_option('cloudsafe365_plugin_options');
 }
 ?>