<?php
// Don't call the file directly
if (!defined('ABSPATH'))
    exit;

add_action('wp_ajax_site_coms', 'site_coms_dat');
require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/includes/cs365_admin_functions.php');
require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/includes/cs365_admin_help.php');
require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/includes/cs365_admin_help_content.php');
require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/includes/cs365_admin_activate_deacativate.php');
require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/includes/cs365_admin_backup.php');
require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/includes/cs365_api_check.php');
require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/includes/cs365_admin_tabs_menu.php');
require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/ajax/cs365_dropbox_ajax.php');
require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/ajax/cs365_malware_ajax.php');
require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/ajax/cs365_recovery_ajax.php');

add_action('init', 'cloudsafe365_init');
add_action('admin_menu', 'cloudsafe365_menus');
add_action('admin_init', 'cloudsafe365_admin_init');
//Let's check for an API Key and if it's set to none then it means the plugin hasn't been connected to cloudsafe365.com. If that's the case then let's show the nag screen
function cloudsafe365_init() {
  cloudsafe365_api_check();
  if (isset($_GET['page']) == 'cloudsafe365-setup')
      return;
  cloudsafe365_nonbinary_check();
}

//obsolete functoin
function cloudsafe365_activation_nag() {
  $url = admin_url() . 'admin.php?page=cloudsafe365-setup';
  ?>
  <script type="text/javascript">
    window.location = '<?php echo $url; ?>';
  </script>
  <meta http-equiv="refresh" content="1;url=<?php echo $url; ?>" />
  <?PHP
  exit;
  ?>
  <?php
}

//obsolete function
function cloudsafe365_key_investigate() {
  ?>
  <div id="cloudsafe365-message" class="updated">
    <br/><br/>
    <h4><strong>We will need to check security key</strong><br/><br/> - Connect to cloudsafe365.com to enable all features.</h4>
    <br/><br/>
    <p><a href="<?php echo cloudsafe365_api_check(); ?>" class="cloudsafe-connect button-primary">Connect to
        cloudsafe365.com</a></p>
    <br/><br/>
  </div>
  <?php
}

//obsolete function
function cloudsafe365_activated() {
  ?>
  <div id="cloudsafe365-message" class="updated"><br/><br/>
    <h4><strong>Congratulations! Your site is now fully protected with Cloudsafe365.<br/><br/> You can also configure your cloudsafe365 for WP <a href="<?php echo admin_url(); ?>admin.php?page=cloudsafe365-options" title="cloudsafe365 for WP Options"> options.</a></strong><br/><br/><p>You'll get daily reports in your email.</p>
      <br/><br/>
  </div>
  <?php
}

function getInstalledVersion() {

  if (!defined('CS365_CURRENT')) {
    if (!function_exists('get_plugins')) {
      require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
    }
    $allPlugins = get_plugins();

    if (isset($allPlugins['cloudsafe365-for-wp/cloudsafe365_for_WP.php'])) {
      define('CS365_CURRENT', $allPlugins['cloudsafe365-for-wp/cloudsafe365_for_WP.php']['Version']);
    }
    elseif (isset($allPlugins['cloudsafe365_for_WP'])) {
      define('CS365_CURRENT', $allPlugins['cloudsafe365_for_WP']['Version']);
    }
    else
        define('CS365_CURRENT', '');
  }
}

##Check for non binary database mainly for already insertd databased will be removed and reset to binary.
function cloudsafe365_nonbinary_check() {
  global $wpdb;
  $request = "SELECT expression FROM cs365_change LIMIT 1";
  $mysql = mysql_query($request);
  if ($mysql) {
    if (mysql_num_rows($mysql) > 0) {
      list($object->expression) = mysql_fetch_row($mysql);
      if (preg_match('/^\'/xsi', $object->expression)) {
        ##resetting client and activation.
        $options = get_option('cloudsafe365_');
        $options['cloudsafe365_api_key'] = 'none';
        $key = activation_update($options, 'r');
        $options = get_option('cloudsafe365_plugin_options');
        $options['cloudsafe365_api_key'] = $key;
        update_option('cloudsafe365_plugin_options', $options, '', 'yes');
        foreach (array('cs365_change', 'cs365_triggers', 'cs365_tables') as $table)
            $wpdb->query('truncate table ' . $table);
        require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/lib/cs365_back.php');
      }
    }
  }
  return false;
}

function thickbox_register() {
  global $current_screen;

  //Only load thickbox on the reports page rather than all of the admin area
  if ($current_screen['id'] == "cloudsafe365_page_cloudsafe365-reports"):
    wp_enqueue_script('jquery');
    wp_enqueue_script('thickbox');
    wp_enqueue_style('thickbox.css', '/' . WPINC . '/js/thickbox/thickbox.css', null, '1.0');
  endif;
}

function cloudsafe365_check_check($test) {
  if ($test == 1)
      return 'checked';
  return '';
}

function site_coms_dat() {
  $options = get_option('cloudsafe365_plugin_options');
  if (isset($options['cloudsafe365_api_key'])) {
    if (strlen($options['cloudsafe365_api_key']) == 33) {
      getInstalledVersion();
      wp_remote_retrieve_body(wp_remote_get(CS365 . '/reporting/info.php?v=' . CS365_CURRENT . '&k=' . $options['cloudsafe365_api_key'] . '&p=' . $_POST['page']));
      exit;
    }
  }
}
?>