<?PHP
 function cloudsafe365_admin_init() {
  /* Register our stylesheet. */
  wp_register_style('cloudsafe365-style', WP_PLUGIN_URL . '/cloudsafe365-for-wp/css/style.css');
  if (get_bloginfo('version') < 3.3):
   wp_register_style('32-style', WP_PLUGIN_URL . '/cloudsafe365-for-wp/css/32.css');
   wp_enqueue_style('32-style');
  endif;

  wp_enqueue_style('cloudsafe365-style');
  register_setting('cloudsafe365_plugin_options', 'cloudsafe365_plugin_options', 'cloudsafe365_validate_options');
  //Setup a settings section for our cloudsafe365 for WP options
  add_settings_section('cloudsafe365_main', '', 'cloudsafe365_section_text', 'cloudsafe365-options');


  if (isset($_GET['settings-updated']) == 'true'):
   $options = get_option('cloudsafe365_plugin_options');
   $result = activation_update($options);
   ?>
   <div style="margin-top:35px;text-align:center;color:white" id="cloudsafe365-message" class="updated">
    <strong>cloudsafe365 updated</strong>
   </div>
   <?PHP
  endif;
 }

 function cloudsafe365_api_check() {
  getInstalledVersion();
  $options = get_option('cloudsafe365_plugin_options');

  if (isset($options['cloudsafe365_type'])) {
   if ($options['cloudsafe365_type'] == 0)
     define('CS365ACTIVE', 'DISABLED');
   else
     define('CS365ACTIVE', '');
  }

  if (isset($_GET['page']) == 'cloudsafe365-setup') {
   if (isset($_GET["register"])) {
    if ($_GET["register"] == '1') {
     $items = array('backup', 'security', 'protection');
     $type = 'basic';

     foreach ($_POST as $name => $value) {
      if (in_array($name, $items))
        if ($value == 'plus') {
        $type = 'plus';
        $options['cloudsafe365_type'] = 1;
        return;
       }
     }
     if ($type == 'basic') {
      $options['cloudsafe365_setup'] = 1;
      $options['cloudsafe365_type'] = 0;
      $cloudsafe365_api_key = activation_update($options, '');
      $options['cloudsafe365_api_key'] = $cloudsafe365_api_key;
      $options['cloudsafe365_email_address'] = $_POST['cloudsafe365_email_address'];
      update_option('cloudsafe365_plugin_options', $options, '', 'yes');

      //running initial back
      define('CS365_CREATE', 1);
      require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/lib/cs365_back.php');
      $url = admin_url() . 'admin.php?page=cloudsafe365';
      wp_redirect($url);
      return $url;
     }
    }

   }
  }
  elseif (isset($_GET['api_key']) == 'none') {
   $url = admin_url() . 'admin.php?page=cloudsafe365-setup';
   wp_redirect($url);
   return $url;
  }

  //If we are on set up we want to make sure we run our setup syste so we will
  //just return this.
  elseif (isset($_GET['page']) == 'cloudsafe365-setup') {
   return;
  }
  elseif (($options['cloudsafe365_api_key'] == 'none') && (isset($_GET['api_key']) != 'none')) {
   //checking to allow for escaping out...
   if (check_for_escape_out())
     return;
   $url = admin_url() . 'admin.php?page=cloudsafe365-setup';
   wp_redirect($url);
   return $url;
   //add_action('admin_notices', 'cloudsafe365_activation_nag');
   $url = wp_nonce_url(add_query_arg('api_key', 'none'));
   return $url;
  }
  elseif (strlen($options['cloudsafe365_api_key']) != 33) {
   $url = wp_nonce_url(add_query_arg('api_key', 'none'));
   return $url;
  }
  else {
   $url = wp_nonce_url(add_query_arg('api_key', 'fail'));
   return $url;
  }
 }

//Sanitize the input
 function cloudsafe365_validate_options($input) {
  //The only value we need to validate is the email address. We check if it's an email address and if it's not then we fall back to the admin_email address
  if (!is_email($input['cloudsafe365_email_address'])) {
   //If the email address is invalid we need to throw an error to the user
   add_settings_error('cloudsafe365_email_address', 'cloudsafe365_email_address_error', 'Invalid email address. Default admin email applied', 'error');
   //Notify the user that all other options they might've altered have been saved
   add_settings_error('cloudsafe365_plugin_options', 'cloudsafe365_plugin_options_updated', 'All other cloudsafe365 for WP settings have been updated.', 'updated');
   $input['cloudsafe365_email_address'] = get_option('admin_email');
  }
  else {
   //All settings were successfully saved so notify the user
   add_settings_error('cloudsafe365_plugin_options', 'cloudsafe365_plugin_options_updated', 'Your cloudsafe365 for WP settings have been updated.', 'updated');
  }
  return $input;
 }

 /* ________________________________________________________________
   developed by cloudsafe365
   ________________________________________________________________ */
 function check_for_escape_out() {
  //With the set we have to give the user the ability to access plugins and other folders
  if (isset($_SERVER['REQUEST_URI']))
    $check = $_SERVER['REQUEST_URI'];
  elseif (isset($_SERVER['SCRIPT_NAME']))
    $check = $_SERVER['SCRIPT_NAME'];
  elseif (isset($_SERVER['PHP_SELF']))
    $check = $_SERVER['PHP_SELF'];
  if (isset($check)) {
   if (preg_match('/plugins\.php/xsi', $check)) {
    if (!preg_match('/activate=/xsi', $check))
      return true;
   }
  }
  return false;
 }
?>