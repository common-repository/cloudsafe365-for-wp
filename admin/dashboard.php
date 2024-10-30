<?PHP
if (!defined('ABSPATH'))
    exit;
?>
<a href="http://www.cloudsafe365.com" border="0" target="_blank"><img src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/cloudsafe365_120.png" height="77" width="120" alt="cloudsafe365 for WP Logo" align="right"></a>
<?PHP
cloudsafe365_last_backedup();
$cs365_active_dash['Protection'] = '';
$cs365_active_dash['Reporting'] = '';
$cs365_active_dash['Malware'] = '';
$cs365_active_dash['Recovery'] = '';
$cs365_active_dash['Options'] = '';
$cs365_active_dash['Logs'] = '';
$cs365_active_dash['Backup'] = '';
$cs365_active_dash['Upgrade'] = '';
$cs365_active_dash['Harden'] = '';

switch ($_GET["page"])
  {
  case ('cloudsafe365-protection') :
    $cs365_active_dash['Protection'] = ' nav-tab-active';
    break;
  case ('cloudsafe365-setup') :
    $cs365_active_dash['Upgrade'] = ' nav-tab-active';
    break;
  case ('cloudsafe365-malware') :
    $cs365_active_dash['Malware'] = ' nav-tab-active';
    break;
  case ('cloudsafe365-reports') :
    $cs365_active_dash['Reporting'] = ' nav-tab-active';
    break;
  case ('cloudsafe365-options') :
    $cs365_active_dash['Options'] = ' nav-tab-active';
    break;
  case ('cloudsafe365-recover') :
    $cs365_active_dash['Recovery'] = ' nav-tab-active';
    break;
  case ('cloudsafe365-log') :
    $cs365_active_dash['Logs'] = ' nav-tab-active';
    break;
  case ('cloudsafe365-download') :
    $cs365_active_dash['Backup'] = ' nav-tab-active';
    break;
  case ('cloudsafe365') :
    $cs365_active_dash['Home'] = ' nav-tab-active';
    break;
  case ('cloudsafe365-harden') :
    $cs365_active_dash['Harden'] = ' nav-tab-active';
    break;
  default :
    $cs365_active_dash['Protection'] = ' nav-tab-active';
  }
if ($_GET["page"] != 'cloudsafe365-download')
    cloudsafe365_remove_table();

$options = get_option('cloudsafe365_plugin_options');


if ($_GET["page"] != 'cloudsafe365-setup') {
  ?>
  <h2 class="nav-tab-wrapper"  style="font-style: normal;display: block;">
    <a class="nav-tab<?PHP echo $cs365_active_dash['Home']; ?>" style="margin-right:1px;padding: 4px 1px 2px;font-size:15px;font-weight: bold"  href="?page=cloudsafe365">Dashboard</a>
    <?PHP
    /* <a class="nav-tab<?PHP echo $cs365_active_dash['Malware']; ?>" style="margin-right:1px;padding: 4px 1px 2px;font-size:15px;font-weight: bold"  href="?page=cloudsafe365-malware">Malware</a> */
    ?>
    <a class="nav-tab<?PHP echo $cs365_active_dash['Malware']; ?>" style="margin-right:1px;padding: 4px 1px 2px;font-size:15px;font-weight: bold;" href="?page=cloudsafe365-malware">Malware Scan</a>
 <a class="nav-tab<?PHP echo $cs365_active_dash['Protection']; ?>" style="margin-right:1px;padding: 4px 1px 2px;font-size:15px;font-weight: bold"  href="?page=cloudsafe365-protection">Malware Protection</a>
  <a class="nav-tab<?PHP echo $cs365_active_dash['Harden']; ?>" style="margin-right:1px;padding: 4px 1px 2px;font-size:15px;font-weight: bold"  href="?page=cloudsafe365-harden">Hardening</a>
    <a class="nav-tab<?PHP echo $cs365_active_dash['Backup']; ?>" style="margin-right:1px;padding: 4px 1px 2px;font-size:15px;font-weight: bold" href="?page=cloudsafe365-download">Backup</a>
    <a class="nav-tab<?PHP echo $cs365_active_dash['Reporting']; ?>" style="margin-right:1px;padding: 4px 1px 2px;font-size:15px;font-weight: bold" href="?page=cloudsafe365-reports" >Analytics</a>
    <a class="nav-tab<?PHP echo $cs365_active_dash['Recovery']; ?>" style="margin-right:1px;padding: 4px 1px 2px;font-size:15px;font-weight: bold" href="?page=cloudsafe365-recover" >Restore</a>
    <a class="nav-tab<?PHP echo $cs365_active_dash['Options']; ?>" style="margin-right:1px;padding: 4px 1px 2px;font-size:15px;font-weight: bold" href="?page=cloudsafe365-options">Options</a>
    <a class="nav-tab<?PHP echo $cs365_active_dash['Logs']; ?>"style="margin-right:1px;padding: 4px 1px 2px;font-size:15px;font-weight: bold"  href="?page=cloudsafe365-log">Logs</a>

    <?PHP
    if ($options['cloudsafe365_type'] == 0) {
      ?>
      <a class="nav-tab<?PHP echo $cs365_active_dash['Upgrade']; ?>"  style="margin-right:1px;padding: 4px 3px 2px;font-size:15px;font-weight: bold;color: #008000"  href="?page=cloudsafe365-setup">Upgrade</a>
      <?PHP
    }
    ?>
  </h2>
  <?PHP
}
else {
  ?>
  <h2 class="nav-tab-wrapper"  style="font-style: normal;display: block;margin-top:50px">

    <?PHP
    if (isset($options['cloudsafe365_setup'])) {
      ?>
      <a class="nav-tab<?PHP echo $cs365_active_dash['Home']; ?>" style="margin-right:1px;padding: 4px 1px 2px;font-size:15px;font-weight: bold" href="?page=cloudsafe365">Dashboard</a>
      <?PHP
    }
    ?>
    <a class="nav-tab nav-tab-active" style="margin-right:1px;padding: 4px 1px 2px;font-size:15px;font-weight: bold"  href="?page=setup">cloudsafe365 Setup</a>
  </h2>
  <?PHP
}
?>

<div class="wrap">
  <?PHP
  switch ($_GET["page"])
    {
    case ('cloudsafe365') :
      require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/cs365_dashboard.php');
      cs365_dashboard();
      break;
    case ('cloudsafe365-malware') :
      require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/cs365_deep_display.php');
      $a = new cs365_deep_display();
      $a->cs365_display();
      break;
    case ('cloudsafe365-protection') :
      require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/cs365_protection.php');
      cloudsafe365_admin_protection_pres();
      break;
    case ('cloudsafe365-reports') :
      require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/cs365_reports.php');
      cloudsafe365_admin_report_press();
      break;
    case ('cloudsafe365-options') :
      require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/cs365_options.php');
      cloudsafe365_admin_options_pres();
      break;
    case ('cloudsafe365-recover') :
      require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/cs365_recovery.php');
      cloudsafe365_admin_recover_pres();
      break;
    case ('cloudsafe365-log') :
      require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/sc365_logs.php');
      cloudsafe365_admin_log_pres();
      break;
    case ('cloudsafe365-download') :
      require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/cs365_dropbox_sc.php');
      require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/cs365_download.php');
      cloudsafe365_admin_download_pres();
      break;
    case ('cloudsafe365-malware') :
      require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/cs365_malware.php');
      cloudsafe365_admin_malware_pres();
      break;
    case ('cloudsafe365-harden') :
      require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/cs365_harden.php');
      break;
    case ('cloudsafe365-setup') :
      require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/cs365_setup.php');
      cs365_setup();
      break;
    default :
      require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/cs365_protection.php');
      cloudsafe365_admin_protection_pres();
    }
  ?>
</div>

<?PHP
if (isset($_GET["page"])) {
  if (isset($options['cloudsafe365_api_key'])) {
    if (strlen($options['cloudsafe365_api_key']) == 33) {
      ?>
      <script type="text/javascript">
        function site_coms()
        {
          jQuery(document).ready(function($) {
            async: false
            var data = {
              action: 'site_coms',
              page:'<?PHP echo preg_replace('/cloudsafe365|\W/xsi', '', $_GET["page"]); ?>'
            };
            jQuery.post(ajaxurl, data, function(response){});
          });
        }
        site_coms();
      </script>
      <?PHP
    }
  }
}
function cs365_version() {
  getInstalledVersion();
  ?>
  <span style="font-size:13px;color:#21759B" >cloud<span style="color:black">safe</span>365 for WP<span style="color:#AAA"> <?PHP echo 'Version ' . CS365_CURRENT; ?> </span></span>
  <?PHP
}
?>