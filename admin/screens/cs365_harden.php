<?php
require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/cs365_displays.php');

class harden
  {
  function harden() {
 $this->data = array('current_up_to_date_wordpress_version' => array('title' => 'Scan: if WordPress is up to date.',
          'msg_ok' => 'You are using the latest version of WordPress.',
          'msg_bad' => 'You are not using the latest version of WordPress.'),
       'current_up_to_date_plugins' => array('title' => 'Scan: active plugins are up to date.',
          'msg_ok' => 'All plugins are up to date.',
          'msg_bad' => 'Some plugins (%s) are outdated.'),
       'current_up_to_date_themes' => array('title' => 'Scan: active themes are up to date.',
          'msg_ok' => 'All themes are up to date.',
          'msg_bad' => 'Some themes (%s) are outdated.'),
       'does_site_reveal_wordpress' => array('title' => 'Scan: if vulnerable WordPress info is revealed in your page\'s meta data.',
          'msg_ok' => 'Your site doesn\'t reveal WordPress version info.',
          'msg_warning' => 'Site  could not be fetched.',
          'msg_bad' => 'Your site reveals WordPress version info in meta tags.'),
       'Readme_File_Accessable_By_Http' => array('title' => 'Scan: if <i>readme.html</i> file is available via HTTP.',
          'msg_ok' => '<i>readme.html</i> is not available.',
          'msg_warning' => 'Unable to determine status of <i>readme.html</i>.',
          'msg_bad' => '<i>readme.html</i> is available via HTTP.'),
       'PHP_return_http_headers' => array('title' => 'Scan: if server response headers contain vulnerable PHP version info.',
          'msg_ok' => 'Headers contain nil vulnerable PHP version info.',
          'msg_bad' => 'Server response headers contain vulnerable PHP version info.'),
       'Is_PHP_exposed_to_the_world' => array('title' => 'Scan: if <i>expose_php</i> PHP function is dis activated.',
          'msg_ok' => '<i>expose_php</i> PHP function is dis activated.',
          'msg_bad' => '<i>expose_php</i> PHP function is activated.'),
       'Is_your_admin_login_admin' => array('title' => 'Scan: if username "admin" available.',
          'msg_ok' => 'User "admin" doesn\'t exist.',
          'msg_bad' => 'User "admin" available.'),
       'can_anyone_can_register' => array('title' => 'Scan: if "anyone can register" option is active.',
          'msg_ok' => '"Anyone can register" option is inactive.',
          'msg_bad' => '"Anyone can register" option is activated.'),
       'Scan_unsuccessful_login_info' => array('title' => 'Scan: for vulnerable information on unsuccessful login attempts.',
          'msg_ok' => 'No vulnerable info is shown on unsuccessful login attempts.',
          'msg_bad' => 'Vulnerable information is displayed on unsuccessful login attempts.'),
       'db_table_prefix_Scan:' => array('title' => 'Scan: if database table prefix is the default one (<i>wp_</i>).',
          'msg_ok' => 'Database table prefix is not default.',
          'msg_bad' => 'Database table prefix is default.'),
       'configuration_keys_are_they_ok' => array('title' => 'Scan: if security keys and salts have normal values.',
          'msg_ok' => 'All keys have normal values set.',
          'msg_bad' => 'Following keys don\'t have normal values set: %s.'),
       'is_the_database_password_strong_enough' => array('title' => 'Test WordPress database password vulnerability.',
          'msg_ok' => 'Database password is strong.',
          'msg_bad' => 'Database password is vulnerable (%s).'),
       'wordpress_troubleshoot_is_it_on' => array('title' => 'Scan: if troubleshoot mode is active.',
          'msg_ok' => 'General troubleshoot mode is inactive.',
          'msg_bad' => 'General troubleshoot mode is active.'),
       'Database_troubleshoot_is_it_on' => array('title' => 'Scan: if database mode is active.',
          'msg_ok' => 'Database troubleshoot mode is active.',
          'msg_bad' => 'Database troubleshoot mode is inactive.'),
       'PHP_troubleshoot_is_it_on' => array('title' => 'Scan: if JavaScript troubleshoot mode is active.',
          'msg_ok' => 'JavaScript troubleshoot mode is inactive.',
          'msg_bad' => 'JavaScript troubleshoot mode is active.'),
       'php_display_errors_is_it_on' => array('title' => 'Scan: if <i>display_errors</i> PHP function is inactive.',
          'msg_ok' => '<i>display_errors</i> PHP function is inactive.',
          'msg_bad' => '<i>display_errors</i> PHP directive is activated.'),
       'Does_the_site_match_the_wordpress_installation' => array('title' => 'Scan: if WordPress   is the same as the domain .',
          'msg_ok' => 'WordPress  is different from the domain .',
          'msg_bad' => 'WordPress  is the same as the domain .'),
       'config_file_permissions' => array('title' => 'Scan: if <i>wp-config.php</i> file has the right permissions (chmod) set.',
          'msg_ok' => 'WordPress config file has the right chmod set.',
          'msg_warning' => 'Unable to read chmod of <i>wp-config.php</i>.',
          'msg_bad' => 'Current <i>wp-config.php</i> chmod (%s) is not recommended and other users on the server can access the file.'),
       'install_file_accessable_by_http' => array('title' => 'Scan: if <i>install.php</i> file is available via HTTP.',
          'msg_ok' => '<i>install.php</i> is not available.',
          'msg_warning' => 'Unable to determine <i>install.php</i> file.',
          'msg_bad' => '<i>install.php</i> is available via HTTP.'),
       'upgrade_File_Accessable_By_Http' => array('title' => 'Scan: if <i>upgrade.php</i> file is available via HTTP.',
          'msg_ok' => '<i>upgrade.php</i> is not available.',
          'msg_warning' => 'Unable to determine status of <i>upgrade.php</i> file.',
          'msg_bad' => '<i>upgrade.php</i> is available via HTTP. '),
       'register_globals_Scan:' => array('title' => 'Scan: if <i>register_globals</i> PHP function is active.',
          'msg_ok' => '<i>register_globals</i> PHP function is dis activated.',
          'msg_bad' => '<i>register_globals</i> PHP directive is active.'),
       'PHP_safe_mode_Scan' => array('title' => 'Scan: if PHP safe mode is inactive.',
          'msg_ok' => 'Safe mode is inactive.',
          'msg_bad' => 'Safe mode is active.'),
       'allow_url_include_Scan:' => array('title' => 'Scan: if <i>allow_url_include</i> PHP function is dis-activated.',
          'msg_ok' => '<i>allow_url_include</i> PHP directive is dis active.',
          'msg_bad' => '<i>allow_url_include</i> PHP directive is active.'),
       'plugins_and_themes_file_editor_is_it_on' => array('title' => 'Scan: if plugins/themes file editor is active.',
          'msg_ok' => 'File editor is dis active.',
          'msg_bad' => 'File editor is active.')
    );
  }

  function cs365_chmod_checks($type, $path, $shouldbe) {

    if ($this->i % 2 == 0)
        $bg = '#D1E5EE';else
        $bg = '#fff';
    $this->i++;
    ?>
    <?PHP
    $configmod = substr(sprintf("%o", @fileperms($path)), -4);

    if ((int) $configmod != (int) $shouldbe) {
      $img = 'cross';
      $this->chmod = 1;
    }
    else
        $img = 'tick';

    $cstable = '
    <tr style="background-color:' . $bg . '">
      <td><img src="' . WP_PLUGIN_URL . '/cloudsafe365-for-wp/images/' . $img . '.png" width="12" height="12" alt="" /></td>
      <td align="left">' . $type . '</td>
      <td align="left">' . $configmod . '</td>
      <td align="left">' . $shouldbe . '</td>
    </tr>
  ';
    return $cstable;
  }

  function wordpress_troubleshoot_is_it_on() {
    $function = __FUNCTION__;
    if (defined('WP_DEBUG') && WP_DEBUG)
        $this->$function->result = false;
    else
        $this->$function->result = true;
  }

  function current_up_to_date_wordpress_version() {
    $function = __FUNCTION__;
    if (!function_exists('get_preferred_from_update_core')) {
      require_once(ABSPATH . 'wp-admin/includes/update.php');
    }
    wp_version_check();
    $latest_core_update = get_preferred_from_update_core();
    if (isset($latest_core_update->response) && ($latest_core_update->response == 'upgrade'))
        $this->$function->result = false;
    $this->$function->result = true;
  }

  function current_up_to_date_plugins() {
    $function = __FUNCTION__;
    $inf = get_site_transient('update_plugins');
    if (!is_object($inf)) {
      $inf = new stdClass;
    }
    set_site_transient('update_plugins', $inf);
    wp_update_plugins();
    $inf = get_site_transient('update_plugins');
    if (isset($inf->response) && is_array($inf->response)) {
      $plugin_update_cnt = count($inf->response);
    }
    else
        $plugin_update_cnt = 0;
    if ($plugin_update_cnt > 0) {
      $this->$function->result = false;
      $this->$function->response = sizeof($inf->response) . ' Out of Date';
    }
    else
        $this->$function->result = true;
  }

  function current_up_to_date_themes() {
    $function = __FUNCTION__;
    $inf = get_site_transient('update_themes');
    if (!is_object($inf))
        $inf = new stdClass;
    set_site_transient('update_themes', $inf);
    wp_update_themes();
    $inf = get_site_transient('update_themes');
    if (isset($inf->response) && is_array($inf->response))
        $dat = count($inf->response);
    else
        $dat = 0;
    if ($dat > 0) {
      $this->$function->result = false;
      $this->$function->response = sizeof($inf->response);
    }
    else
        $this->$function->result = true;
  }

  function does_site_reveal_wordpress() {
    $function = __FUNCTION__;
    if (!class_exists('WP_Http')) {
      require( ABSPATH . WPINC . '/class-http.php' );
    }
    $http = new WP_Http();
    $response = (array) $http->request(get_bloginfo('wpurl'));
    $html = $response['body'];
    if ($html) {
      $this->$function->result = true;
      $start = strpos($html, '<head');
      $len = strpos($html, 'head>', $start + strlen('<head'));
      $html = substr($html, $start, $len - $start + strlen('head>'));
      preg_match_all('#<meta([^>]*)>#si', $html, $matches);
      $meta_tags = $matches[0];

      foreach ($meta_tags as $meta_tag) {
        if (stripos($meta_tag, 'generator') !== false &&
        stripos($meta_tag, get_bloginfo('version')) !== false) {
          $this->$function->result = false;
          break;
        }
      }
    }
    else
        $this->$function->result = 'error';
  }

  function Readme_File_Accessable_By_Http() {
    $function = __FUNCTION__;
    $url = get_bloginfo('wpurl') . '/readme.html?rnd=' . rand();
    $response = wp_remote_get($url);
    if (is_wp_error($response))
        $this->$function->result = 'error';
    elseif ($response['response']['code'] == 200)
        $this->$function->result = false;
    else
        $this->$function->result = true;
  }

  function install_file_accessable_by_http() {
    $function = __FUNCTION__;
    $response = wp_remote_get(get_bloginfo('wpurl') . '/wp-admin/install.php?rnd=' . rand());
    if (is_wp_error($response))
        $this->$function->result = 'error';
    elseif ($response['response']['code'] == 200)
        $this->$function->result = false;
    else
        $this->$function->result = true;
  }

  function config_file_permissions() {
    $function = __FUNCTION__;
    $mode = substr(sprintf('%o', fileperms(ABSPATH . '/wp-config.php')), -4);
    if (!$mode)
        $this->$function->result = 'error';
    elseif (substr($mode, -1) != 0) {
      $this->$function->result = false;
      $harden->$function->response = $mode;
    }
    else
        $this->$function->result = true;
  }

  function Scan_unsuccessful_login_info() {
    $function = __FUNCTION__;
    $params = array('log' => 'sn-test_3453344355',
       'pwd' => 'sn-test_2344323335');
    if (!class_exists('WP_Http')) {
      require( ABSPATH . WPINC . '/class-http.php' );
    }
    $http = new WP_Http();
    $response = (array) $http->request(get_bloginfo('wpurl') . '/wp-login.php', array('method' => 'POST', 'body' => $params));
    if (stripos($response['body'], 'invalid username') !== false) {
      $this->$function->result = false;
    }
    else
        $this->$function->result = true;
  }

  function PHP_return_http_headers() {
    $function = __FUNCTION__;
    if (!class_exists('WP_Http')) {
      require( ABSPATH . WPINC . '/class-http.php' );
    }
    $http = new WP_Http();
    $response = (array) $http->request(get_bloginfo('siteurl'));
    if ((isset($response['headers']['server']) && stripos($response['headers']['server'], phpversion()) !== false) || (isset($response['headers']['x-powered-by']) && stripos($response['headers']['x-powered-by'], phpversion()) !== false)) {
      $this->$function->result = true;
    }
    else
        $this->$function->result = false;
  }

  function PHP_safe_mode_Scan() {
    $function = __FUNCTION__;
    $check = (bool) ini_get('safe_mode');
    if ($check)
        $this->$function->result = false;
    else
        $this->$function->result = true;
    $this->$function->result = true;
  }

  function can_anyone_can_register() {
    $function = __FUNCTION__;
    $t = get_option('users_can_register');
    if ($t)
        $this->$function->result = false;
    else
        $this->$function->result = true;
  }

  function PHP_troubleshoot_is_it_on() {
    $function = __FUNCTION__;
    if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG)
        $this->$function->result = false;
    else
        $this->$function->result = true;
  }

  function Database_troubleshoot_is_it_on() {
    global $wpdb;
    $function = __FUNCTION__;
    if ($wpdb->show_errors == true)
        $this->$function->result = false;
    else
        $this->$function->result = true;
  }

  function upgrade_File_Accessable_By_Http() {
    $function = __FUNCTION__;
    $url = get_bloginfo('wpurl') . '/wp-admin/upgrade.php?rnd=' . rand();
    $response = wp_remote_get($url);
    if (is_wp_error($response))
        $this->$function->result = 'error';
    elseif ($response['response']['code'] == 200)
        $this->$function->result = false;
    else
        $this->$function->result = true;
  }

  function configuration_keys_are_they_ok() {
    $function = __FUNCTION__;
    $ok = true;
    $keys = array('AUTH_KEY', 'SECURE_AUTH_KEY', 'LOGGED_IN_KEY', 'NONCE_KEY',
       'AUTH_SALT', 'SECURE_AUTH_SALT', 'LOGGED_IN_SALT', 'NONCE_SALT');
    foreach ($keys as $key) {
      $constant = @constant($key);
      if (empty($constant) || trim($constant) == 'put your unique phrase here' || strlen($constant) < 50) {
        $bad_keys[] = $key;
        $ok = false;
      }
    }
    if ($ok == true)
        $this->$function->result = true;
    else
        $this->$function->result = false;
  }

  function php_display_errors_is_it_on() {
    $function = __FUNCTION__;

    $check = (bool) ini_get('display_errors');
    if ($check)
        $this->$function->result = false;
    else
        $this->$function->result = true;
  }

  function plugins_and_themes_file_editor_is_it_on() {
    $function = __FUNCTION__;
    if (defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT)
        $this->$function->result = true;
    else
        $this->$function->result = false;
  }

  function is_the_database_password_strong_enough() {
    $function = __FUNCTION__;
    $password = DB_PASSWORD;
    if (empty($password)) {
      $this->$function->result = false;
      $harden->$function->response = 'No password found';
    }
    elseif (strlen($password) < 6) {
      $this->$function->result = false;
      $harden->$function->response = 'password is to short ' . strlen($password) . ' chars';
    }
    elseif (sizeof(count_chars($password, 1)) < 5) {
      $this->$function->result = false;
      $harden->$function->response = 'password is extremly easy';
    }
    else {
      $this->$function->result = true;
      $harden->$function->response = 'password is strong';
    }
  }

  function Does_the_site_match_the_wordpress_installation() {
    $function = __FUNCTION__;
    if (get_bloginfo('siteurl') == get_bloginfo('wpurl'))
        $this->$function->result = false;
    else
        $this->$function->result = true;
  }

  function Is_your_admin_login_admin() {
    $function = __FUNCTION__;
    require_once(ABSPATH . WPINC . '/registration.php');
    if (username_exists('admin'))
        $this->$function->result = false;
    else
        $this->$function->result = true;
  }

  function Is_PHP_exposed_to_the_world() {
    $function = __FUNCTION__;
    $check = (bool) ini_get('expose_php');
    if ($check)
        $this->$function->result = true;
    else
        $this->$function->result = false;
  }

  }

$harden = new harden();

$cstable = '';
$harden->i = 0;
$cstable .= $harden->cs365_chmod_checks("root directory", "../", "0755");
$cstable .= $harden->cs365_chmod_checks("wp-includes/", "../wp-includes", "0755");
$cstable .= $harden->cs365_chmod_checks(".htaccess", "../.htaccess", "0644");
$cstable .= $harden->cs365_chmod_checks("wp-admin/index.php", "index.php", "0644");
$cstable .= $harden->cs365_chmod_checks("wp-admin/js/", "js/", "0755");
$cstable .= $harden->cs365_chmod_checks("wp-content/themes/", "../wp-content/themes", "0755");
$cstable .= $harden->cs365_chmod_checks("wp-content/plugins/", "../wp-content/plugins", "0755");
$cstable .= $harden->cs365_chmod_checks("wp-admin/", "../wp-admin", "0755");
$cstable .= $harden->cs365_chmod_checks("wp-content/", "../wp-content", "0755");

$counter = 'file_perms';
$permisions = '      <div><a id="d' . $counter . '" href="#" Onclick="cs365_reveal(\'t' . $counter . '\',\'d' . $counter . '\');return false" >View Details</a></div>
       <div id="t' . $counter . '" style="display:none">
<table  width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
  <tr>
    <td>&nbsp;</td>
    <td>Folder</td>
    <td>What it is</td>
    <td>What it should be</td>
  </tr>
    ' . $cstable . '
</table>';

$content = '<table  width="100%" border="0" cellpadding="2px" cellspacing="0" align="center">
    <tr>
    <td align="center" style="font-size:14px">Status</td>
    <td align="left" style="font-size:14px">Scanning For</td>
    <td align="left" style="font-size:14px;padding-left:20px">Scan Description</td>
  </tr>';
$i = 0;
foreach (array('Is_your_admin_login_admin'
   , 'current_up_to_date_wordpress_version'
   , 'current_up_to_date_plugins'
   , 'current_up_to_date_themes'
   , 'does_site_reveal_wordpress'
   , 'Readme_File_Accessable_By_Http'
   , 'install_file_accessable_by_http'
   , 'upgrade_File_Accessable_By_Http'
   , 'config_file_permissions'
   , 'Scan_unsuccessful_login_info'
   , 'PHP_return_http_headers'
   , 'PHP_safe_mode_Scan'
   , 'can_anyone_can_register'
   , 'wordpress_troubleshoot_is_it_on'
   , 'PHP_troubleshoot_is_it_on'
   , 'Database_troubleshoot_is_it_on'
   , 'Is_PHP_exposed_to_the_world'
   , 'configuration_keys_are_they_ok'
   , 'php_display_errors_is_it_on'
   , 'plugins_and_themes_file_editor_is_it_on'
   , 'is_the_database_password_strong_enough'
   , 'Does_the_site_match_the_wordpress_installation'
) as $function) {

  if ($i % 2 == 0)
      $bg = '#D1E5EE';else
      $bg = '#fff';
  $i++;
  call_user_func(array($harden, $function));

  if ($harden->$function->result) {
    $img = 'tick';
    if (!isset($harden->$function->response))
        $harden->$function->response = $harden->data[$function]['msg_ok'];
  }
  elseif ($harden->$function->result == 'error') {
    $img = 'blank';
    $harden->$function->response = 'ERROR doing this process';
  }
  else {
    $img = 'cross';
    $analysis = 1;
    if (!isset($harden->$function->response))
        $harden->$function->response = $harden->data[$function]['msg_bad'];
  }
  $content .= '<tr style="background-color:' . $bg . '">' .
  '<td><img id="ics365' . $function . ';" src="' . WP_PLUGIN_URL . '/cloudsafe365-for-wp/images/' . $img . '.png" width="25" height="25" alt="" /></td>' .
  '<td>' . ucwords(preg_replace('/_/xsi', ' ', $function)) . '</td>' .
  '<td style="padding-left:20px" align="left">' . $harden->$function->response . '</td>' .
  '</tr>';
}
$content .= '</table>';
if (!isset($harden->chmod))
    $img = 'tick';
else
    $img = 'cross';
cs365print_out('File_Permissions', $permisions, $img, '');

if (!isset($analysis))
    $img = 'tick';  
else
    $img = 'cross';
cs365print_out('Hardening_Analysis', $content, $img, '');
?>