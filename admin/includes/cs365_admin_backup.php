<?PHP
 function cloudsafe365_last_backedup() {
  global $wpdb;
  $row = $wpdb->get_row("select date_option from cs365_triggers where id='1'", ARRAY_A);
  $day = $wpdb->get_row("select options from cs365_triggers where id='2'", ARRAY_A);
  $month = $wpdb->get_row("select options from cs365_triggers where id='3'", ARRAY_A);
  $year = $wpdb->get_row("select options from cs365_triggers where id='4'", ARRAY_A);
  $row['trigger_time'] = time() - $row['date_option'];
  $margin = '';
  if (isset($_GET['page'])) {
   switch ($_GET['page'])
    {
    case ('cloudsafe365-recover') :
     $margin = 'margin-top: 10px';
     break;
    case ('cloudsafe365-options') :
     $margin = 'margin-bottom: 3px';
     break;
    case ('cloudsafe365-reports') :
     $margin = 'margin-top: 3px';
     break;
    default :
    }
  }

  if (!isset($_GET["cs365do"])) {
   if ((isset($_GET["page"])) && ($_GET["page"] != 'cloudsafe365-setup')) {
    echo '<span  id="last_scans">';
    echo '<h3><strong>Last scan and backups done : <span style="color:#21759B;font-weight: bold">' . $row['trigger_time'] . '</span> seconds ago</strong><br/>';
    echo '<strong>Backup scans done today : <span style="color:#21759B;font-weight: bold">' . $day['options'] . '</span>';
    echo ' This month : <span style="color:#21759B;font-weight: bold">' . $month['options'] . '</span>';
    echo ' This year : <span style="color:#21759B;font-weight: bold">' . $year['options'] . '</span>';
    echo '</span>';
   }
  }
 }

 function cs365_backup_name($options) {
  if (isset($options['cloudsafe365_api_key'])) {
   if (strlen($options['cloudsafe365_api_key']) == 33) {
    if (function_exists('gzencode'))
      $e = 'gz';
    else
      $e = 'sql';
    $n = 'b' . substr($options['cloudsafe365_api_key'], 0, 10) . '.' . $e;
    $filename['direct'] = WP_PLUGIN_DIR . '/cloudsafe365-for-wp/' . $n;
    $filename['url'] = plugins_url() . '/cloudsafe365-for-wp/' . $n;
    return $filename;
   }
  }
 }
?>
