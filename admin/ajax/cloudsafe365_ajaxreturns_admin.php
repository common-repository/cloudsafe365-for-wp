<?php
// Don't call the file directly
if (!defined('ABSPATH'))
    exit;

/* AJAX RETURNS */
add_action('wp_ajax_cs365_site_simpledash', 'cs365_get_dashboard');
add_action('wp_ajax_cs365_news', 'cs365_get_latest_news');
add_action('wp_ajax_cs365_get_report_one', 'cs365_get_report_one');
add_action('wp_ajax_cs365_get_report_two', 'cs365_get_report_two');
add_action('wp_ajax_cs365_get_report_four', 'cs365_get_report_four');
add_action('wp_ajax_cs365_get_report_five', 'cs365_get_report_five');

add_action('wp_ajax_cs365_save_backup_options', 'cs365_save_backup_settings');
add_action('wp_ajax_cs365_backup_layer', 'cs365_local_site_backup');
add_action('wp_ajax_cs365_backup_dp1_go', 'cs365_backup_dp1_go');
add_action('wp_ajax_cs365_backup_dp2_go', 'cs365_backup_dp2_go');
add_action('wp_ajax_cs365_backup_dp3_go', 'cs365_backup_dp3_go');

add_action('wp_ajax_cs365_deep_defs', 'cs365_deep_defs');
add_action('wp_ajax_cs365_deep_scan', 'cs365_deep_scan');
add_action('wp_ajax_deep_update_files', 'deep_update_files');
add_action('wp_ajax_cs365_grouped_data', 'cs365_grouped_data');
add_action('wp_ajax_set_cs365_file_list', 'set_cs365_file_list');
add_action('wp_ajax_cs365_scan_database', 'cs365_scan_database');

add_action('wp_ajax_cs365_scan_site', 'cs365_scan_site');
function cs365_get_dashboard() {
  $date_filter = urlencode('this month');
  $options = get_option('cloudsafe365_plugin_options');
  $result = wp_remote_fopen(CS365 . '/reporting/reporting.php?key=' . $options['cloudsafe365_api_key'] . '&no_drop=1&report=impressions&date_filter&date_filter=' . $date_filter);
  echo $result;
  exit;
}

function cs365_get_report_one() {
  $date_filter = urlencode('this month');
  $options = get_option('cloudsafe365_plugin_options');
  echo wp_remote_fopen(CS365 . '/reporting/reporting.php?key=' . $options['cloudsafe365_api_key'] . '&report=one&date_filter=' . $date_filter);
  exit;
}

function cs365_get_report_two() {
  $date_filter = urlencode('this month');
  $options = get_option('cloudsafe365_plugin_options');
  echo wp_remote_fopen(CS365 . '/reporting/reporting.php?key=' . $options['cloudsafe365_api_key'] . '&report=two&date_filter=' . $date_filter);
  exit;
}

function cs365_get_report_four() {
  $date_filter = urlencode('this month');
  $options = get_option('cloudsafe365_plugin_options');
  echo wp_remote_fopen(CS365 . '/reporting/reporting.php?key=' . $options['cloudsafe365_api_key'] . '&report=four&date_filter=' . $date_filter);
  exit;
}

function cs365_get_report_five() {
  $date_filter = urlencode('this month');
  $options = get_option('cloudsafe365_plugin_options');
  echo wp_remote_fopen(CS365 . '/reporting/reporting.php?key=' . $options['cloudsafe365_api_key'] . '&report=five&date_filter=' . $date_filter);
  exit;
}

function cs365_get_latest_news() {
  echo wp_remote_retrieve_body(wp_remote_get('http://www.cloudsafe365.com/reporting/latest_update.php'));
  exit;
}

function cs365_scan_site() {
  $cs365tmout['timeout'] = 60;
  $c = parse_url(get_option('home'));
  $a = wp_remote_get('http://www.cloudsafe365.com/malware/?url=' . $c['host'], $cs365tmout);
  if (isset($b->errors)) {
    $a['body'] = 'Issue with scan please try again';
    $a['img'] = 'cross';
    $a['count'] = '-';
  }
  else {
    if (preg_match('/cross\./xsi', $a['body'])) {
      $a['img'] = 'cross';

      $a['body'] = preg_replace('/<style.*?>.*?<\/style>|<center>|<\/center>/xsi', '', $a['body']);
      preg_replace('/regexpression/xsi', '', $str);

      preg_match_all('/cross\./xsi', $a['body'], $preg_match_all);
      if (isset($preg_match_all[0])) {
        $a['count'] = count($preg_match_all[0]) - 1;
      }
    }
    else {
      $a['img'] = 'tick';
      $a['count'] = 0;
    }
    echo json_encode($a);
  }
  exit;
}

function cs365_local_site_backup() {

  include(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/sc365_backup_Setup.php');
  $options = get_option('cloudsafe365_plugin_options');
  cs365_backup_setup($options);
  exit;
}

function cs365_backup_dp1_go() {
  include(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/cs365_dropbox_sc.php');
  $options = get_option('cloudsafe365_plugin_options');
  cs365_backup_dp1($options);
  exit;
}

function cs365_backup_dp2_go() {
  include(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/cs365_dropbox_sc.php');
  $options = get_option('cloudsafe365_plugin_options');
  cs365_backup_dp2($options);
  exit;
}

function cs365_backup_dp3_go() {

  include(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/cs365_dropbox_sc.php');
  $options = get_option('cloudsafe365_plugin_options');
  cs365_backup_dp3($options);
  exit;
}

function cs365_save_backup_settings() {
  // include(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/includes/cs365_admin_activate_deacativate.php');
  $options = get_option('cloudsafe365_plugin_options');

  if (isset($_POST['cloudsafe365_backup_when']))
      $options['cloudsafe365_backup_when'] = $_POST['cloudsafe365_backup_when'];

  if (isset($_POST['cloudsafe365_backup_database']))
      $options['cloudsafe365_backup_database'] = $_POST['cloudsafe365_backup_database'];

  if (isset($_POST['cloudsafe365_real_time_backups']))
      $options['cloudsafe365_real_time_backups'] = $_POST['cloudsafe365_real_time_backups'];

  update_option('cloudsafe365_plugin_options', $options, '', 'yes');

  $return = activation_update($options);
  if (strlen($return) == 33) {
    ?>
    <h2 style="color:green">Setting have been saved</h2>
    <?PHP
  }
  else {
    ?>
    <h2 style="color:red" id="span_id">Error saving settings API key issue</h2>
    <?PHP
  }
  ?>
  <br/><br/>
  <?PHP
  exit;
}

function cs365_deep_scan() {
  require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/cs365_deep_core.php');
  //$root = preg_replace('/\W+wp-content/xsi', '', WP_CONTENT_DIR);
  $a = new cs365_deep_core();
  $test = $a->cs365listFiles(WP_PLUGIN_DIR, 'pt');

  if (preg_match('/cs365_message/xsi', $test)) {
    echo $test;
      }
  elseif (!$test) {
    echo 1;
  }
  exit;
}

function deep_update_files() {
  require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/cs365_deep_core.php');
  $a = new cs365_deep_core();
  $array = $a->cs365_files();

  if (!isset($array['scanned'])) {
    $array['scanned'] = '';
  }
  echo json_encode($array);
  exit;
}

function cs365_grouped_data() {
  require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/cs365_deep_core.php');
  $a = new cs365_deep_core();
  $array = $a->cs365_grouped();
  echo json_encode($array);
  exit;
}

function set_cs365_file_list() {
  require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/cs365_deep_core.php');
  $a = new cs365_deep_core();
  $a->cs365_open_scan_results();

  $test = preg_replace('/wp-admin\/.*/xsi', '', $_SERVER['SCRIPT_FILENAME']);

  if (isset($a->files_moded->$_POST['extension'])) {
    ?>
    <table width="100%" border="0" cellpadding="5" cellspacing="0" align="left">

      <tr>
        <td>File</td>
        <td>Type</td>
        <td>Found</td>
        <td>Modified</td>
      </tr>
      <?PHP
      $i = 0;
      foreach ($a->files_moded->$_POST['extension'] as $item) {

        if ($i % 2 == 0)
            $bg = '#D1E5EE';else
            $bg = '#fff';
        $i++;
        $last = str_replace($test, '', $item);

        $filename = explode('/', $item);

        if (preg_match('/wp-content\/plugins\//xsi', $last)) {
          preg_match('/wp-content\/plugins\/(\w+)/xsi', $last, $typef);
          $type = 'Plugins';
        }
        elseif (preg_match('/wp-content\/themes\//xsi', $last)) {
          preg_match('/wp-content\/themes\/(\w+)/xsi', $last, $typef);
          $type = 'Themes';
        }
        else
            $type = 'Core_Worpdress';

        if (isset($typef[1])) {
          $plugin_name = $typef[1];
          unset($typef);
        }
        else
            $plugin_name = '';
        if (isset($filename)) {
          ?>
          <tr style="background-color:<?PHP echo $bg; ?>">
            <td><?PHP echo end($filename); ?></td>
            <td><?PHP echo $plugin_name; ?></td>
            <td><?PHP echo $type; ?></td>
            <td><?PHP echo date('d-M-Y g:i:s', filemtime($item)); ?></td>
          </tr>

          <?PHP
        }
      }
      ?>
    </table>
    <?PHP
  }
  exit;
}

function cs365_scan_database() {
global $wpdb;
    $wpdb->query('CREATE TABLE IF NOT EXISTS `cs365_tmp_storage` (
                     `id` int(10) NOT NULL AUTO_INCREMENT,
                     `name` char(20) DEFAULT NULL,
                     `skey` char(10) DEFAULT NULL,
                     `sdata` longblob,
                     PRIMARY KEY (`id`),
                     KEY `id` (`id`),
                     KEY `name` (`name`)
                   ) ENGINE=InnoDB AUTO_INCREMENT=156 DEFAULT CHARSET=latin1');

    $wpdb->query('CREATE TABLE IF NOT EXISTS`cs365_malware_system` (
                        `id` int(15) NOT NULL,
                        `m` char(33) NOT NULL,
                        KEY `id` (`id`)
                      ) ENGINE=InnoDB DEFAULT CHARSET=latin1');

    $wpdb->query('CREATE TABLE IF NOT EXISTS`cs365_malware_files` (
                       `id` int(6) NOT NULL AUTO_INCREMENT,
                       `f` char(33) DEFAULT NULL,
                       PRIMARY KEY (`id`),
                       KEY `f` (`f`)
                     ) ENGINE=InnoDB DEFAULT CHARSET=latin1');


   $options = get_option('cloudsafe365_plugin_options');
    $r = 'http://www.cloudsafe365.com/malware/coms.php?c=' . $options[$cloudsafe365_api_key] . '&a=1';
    if (extension_loaded('curl')) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $r);
      curl_setopt($ch, CURLOPT_TIMEOUT, 25);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $b = curl_exec($ch);
    }
    else {
      $cs365tmout['timeout'] = 25;
      $bd = wp_remote_get($r, $cs365tmout);
      if (isset($bd->errors))
          return;
      $b = $bd['body'];
      unset($bd);
    }


    if (preg_match('/cs365_message/xsi', $b)) {
      echo $b;
      exit;
    }
    else {
      global $wpdb;
      $wpdb->query("DELETE
FROM
	cs365_tmp_storage
WHERE
	name = 'defs'");

      $wpdb->query("INSERT
INTO
	cs365_tmp_storage
SET
	name = 'defs',
	sdata = '$b'
");
    }

  require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/cs365_deep_core.php');
  $a = new cs365_deep_core();
  $a->cs365_database_scanner();
  if (!isset($a->database_spam_content)) {
    $a->database_spam_content->comment = 'No Suspecious Malware or spam';
    $a->database_spam_content->count = 0;
  }
  echo json_encode($a->database_spam_content);
  exit;
}

function cs365_deep_defs() {
  global $wpdb;

  ##Curl if user has it is more reliable and faster then the wordpress function
  ##wp_remote_get.'
  $options = get_option('cloudsafe365_plugin_options');
  $r = 'http://www.cloudsafe365.com/malware/coms.php?c=' . $options[$cloudsafe365_api_key];
  if (extension_loaded('curl')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $r);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $b = curl_exec($ch);
  }
  else {
    $cs365tmout['timeout'] = 25;
    $bd = wp_remote_get($r, $cs365tmout);
    if (isset($bd->errors))
        return;
    $b = $bd['body'];
    unset($bd);
  }
  $b = json_decode(gzinflate(base64_decode($b)));

  if (isset($b[0]->msg)) {
    echo $b[0]->msg;
    exit;
  }

  $wpdb->query('truncate table `test_cs365`.`cs365_malware_defs`');
  for ($i = 0; $i < count($b); $i++) {
    $wpdb->query("INSERT
INTO
	cs365_malware_defs
SET
	defs = '" . $b[$i]->defs . "',
	def_id = '" . $b[$i]->def_id . "',
  	def_inf = '" . $b[$i]->def_inf . "'
");
  }

  exit;
}

/* AJAX RETURNS END */
?>