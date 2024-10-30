<?php
 function cloudsafe365_admin_download_pres() {
  $options = get_option('cloudsafe365_plugin_options');
  ?>
  <style>
   #sc365left, #sc365right, #sc365middle {
    display: table-cell;
    padding: 10px;
   }
   #sc365leftj, #sc365rightj, #sc365middlej {
    display: table-cell;
    padding: 1px;
    font-weight: normal;
    white-space: nowrap;
   }

   #sc365leftj{
    width:400px;
   }
  </style>
  <?PHP
  if (isset($options['cloudsafe365_download'])) {
   $sevendays = strtotime('-7 day');
   $onedays = strtotime('-1 day');
   if ($options['cloudsafe365_download'] < $sevendays)
     $color = 'red';
   elseif ($options['cloudsafe365_download'] < $onedays)
     $color = 'orange';
   else
     $color = 'green';

   $long_date = '<span style="color:' . $color . ';font-weight: bold" id="span_id">' . date('d-M-Y', $options['cloudsafe365_download']) . '</span>';
  }
  else {
   $long_date = '<span style="color:red" id="span_id">No Downloads done</span>';
  }

  if (!isset($_GET["cs365do"]))
    $_GET["cs365do"] = 'default';
  switch ($_GET["cs365do"])
   {
   case ('default') :
    require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/sc365_backup.php');
    cs365_backup_main($options, $long_date);
    break;
   case ('set1') :
    require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/sc365_backup_Setup.php');
    cs365_backup_setup($options, $long_date);
    break;
   case ('dp1') :
    cs365_backup_dp1($options, $long_date);
    break;
   case ('dp2') :
    cs365_backup_dp2($options, $long_date);
    break;
   case ('dp3') :
      $options['cloudsafe365_download'] = time();
     update_option('cloudsafe365_plugin_options', $options, '', 'yes');
    cs365_backup_dp3($options, $long_date);
    break;
   case ('stop') :
    cs365_backup_stop($options, $long_date);
    break;
   default :
    cs365_backup_main($options, $long_date);
   }
 }
?>