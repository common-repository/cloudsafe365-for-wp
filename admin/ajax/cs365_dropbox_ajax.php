<?php
 add_action('wp_ajax_add_dbdropbox', 'cs365_send_database_dp');
 add_action('wp_ajax_add_dropbox', 'cs365_backup_dp4');
 add_action('wp_ajax_file_dropbox', 'cs365_fileto_dropbox');
 add_action('wp_ajax_drop_report', 'cs365_dropbox_reporte');
 function cs365_send_database_dp() {
  if (isset($_POST['cs365_db_db'])) {

   $urlhome = get_option('home');
   $parsedurl = parse_url($urlhome);
   $location = preg_replace('/\W/xsi', '_', $parsedurl['host'] . $parsedurl['path']) . '_backup';

   $options = get_option('cloudsafe365_plugin_options');
   $options['confirmcheck'] = md5(uniqid(mt_rand(), true));
   update_option('cloudsafe365_plugin_options', $options, '', 'yes');
   $url = site_url() . '?cloudsafe365_backup_down=1&k=' . md5($options['cloudsafe365_api_key']) . '&c=' . $options['confirmcheck'] . '&db=1&root=' . $location;
   $result = wp_remote_retrieve_body(wp_remote_get($url, array('timeout' => 480)));
   if (isset($result->errors)) {
    ?>
    <span style="color:red" id="span_id">ERROR : Sending Database <?PHP echo $result->errors['http_request_failed'][0]; ?></span>
    <?PHP
    exit;
   }
   //$wpdb->query('drop table IF EXISTS cs365_tmp_table');
   echo $result;
   exit;
  }
 }

 function cs365_backup_dp4() {
  require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/lib/cs365_file_system.php');
  ##DB Dropbox
  $cs365_file_system = new cs365_file_system('db');

  $cs365_file_system->start = $_POST["goto_start"] - 50;
  $cs365_file_system->finish = $_POST["goto_start"];
  $cs365_file_system->next = $cs365_file_system->finish + 50;
  $cs365_file_system->counter = 0;
  $cs365_file_system->files = array();

  $cs365folders = array();
  if (isset($_POST['cs365_plugins_db'])) {
   $add_content = 1;
   $cs365folders['plugins'] = WP_CONTENT_DIR . '/plugins';
  }
  if (isset($_POST['cs365_plugins_db'])) {
   $add_content = 1;
   $cs365folders['themes'] = TEMPLATEPATH;
  }
  if (isset($_POST['cs365_plugins_db'])) {
   $add_content = 1;
   $cs365folders['uploads'] = WP_CONTENT_DIR . '/uploads';
  }
  if (isset($add_content)) {
   $cs365folders['content'] = WP_CONTENT_DIR;
  }

  if (count($cs365folders) == 0)
    exit;

  foreach ($cs365folders as $cs365wp_type => $cs365wpfolder) {
   $cs365_file_system->cs365listFiles($cs365wpfolder, $cs365wp_type);
   if (count($cs365_file_system->files) > 0) {
    $callback['next'] = $cs365_file_system->next;
    $callback['cs365wp_type'] = $string = preg_replace('/themes/xsi', 'theme', $cs365wp_type);
    for ($i = 0; $i < count($cs365_file_system->files); $i++) {
     $file = pathinfo($cs365_file_system->files[$i]);
     $callback['filename'][] = $file['filename'];
     $callback['extension'][] = $file['extension'];
     $callback['prepdone'][] = $cs365_file_system->prepdone[$i];
    }
    echo json_encode($callback);
    exit;
   }
  }
  exit;
 }

 ##_______________________________________________________________________________________________________________________
 function cs365_fileto_dropbox() {
  $re_login = cs365_testdropbox_login();
  if (function_exists('curl_multi_init'))
    cs365_dropbox_multi($re_login);
  else
    cs365_dropbox_single($re_login);
 }

 /* ________________________________________________________________
   developed by cloudsafe365
   ________________________________________________________________ */
 function cs365_testdropbox_login() {
  //Trying to login to Dropbox
  if (isset($_POST['sc365dp_email'])) {
   $re_login = 'Auto  Re-logged into Dropbox<br />';
   require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/lib/DropboxUploader.php');
   try {

    $uploader = new dropboxUploader(trim($_POST['sc365dp_email']), trim($_POST['sc365dp_password']));
    $uploader->test_login();
   }
   catch (Exception $e) {
    ?>
    <div class="inside" style="font-size:13px; font-weight:inherit;">
     <fieldset>
      <p class="form">
       <?PHP
       echo '<span style="color: red">Error: dropbox ' . htmlspecialchars($e->getMessage()) . '</span><br/><br/>';
       ?>
       <span style="color: red"><a href="http://www.dropbox.com" target="_blank">dropbox.com</a> could be unavailable or your login is incorrect</span><br/><br/>
       <a  title="Try Again" class="button-primary"   href="?page=cloudsafe365-download&cs365do=dp1">Try Again</a>
       </div>
       <?PHP
       exit;
      }
     }
     else {
      $re_login = '';
     }
     return $re_login;
    }

    /* ________________________________________________________________ */


    /* ________________________________________________________________
      developed by cloudsafe365

      ________________________________________________________________ */
    function cs365_dropbox_multi() {
     $request = "select * from cs365_external_back WHERE date_inserted is null limit 10";
     $mysql = mysql_query($request) or print mysql_error();
     $num_mysql = mysql_num_rows($mysql) or print mysql_error();

     if ($num_mysql > 0) {
      while ($row = mysql_fetch_assoc($mysql)) {
       $dropbox[] = $row;
      }
     }
     mysql_free_result($mysql);
     if (!isset($dropbox))
       exit;

##preparing files and directories...

     $request = "select count(id) as counter from cs365_external_back WHERE date_inserted is null";
     $mysql = mysql_query($request);
     $num_mysql = mysql_num_rows($mysql);
     $number_to_go = 0;
     $tmp_numb_files = 0;
     if ($num_mysql > 0) {
      list($number_to_go) = mysql_fetch_row($mysql);

      if ($number_to_go != 0) {
       $tmp_numb_files = $number_to_go - 10;
       if ($tmp_numb_files <= 0)
         $tmp_numb_files = 0;
      }
     }
     mysql_free_result($mysql);
     $urlhome = get_option('home');
     $parsedurl = parse_url($urlhome);
     $location = preg_replace('/\W/xsi', '_', $parsedurl['host'] . $parsedurl['path']) . '_backup';
     define('BACKUP_FOLDER', preg_replace('/\W/xsi', '_', $parsedurl['host'] . $parsedurl['path']) . '_backup');
     require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/lib/DropboxUploader.php');
     $uploader = new DropboxUploader('', '');
     for ($i = 0; $i < count($dropbox); $i++) {
      $files_path[] = $dropbox[$i]['file_path'];
      $dest[] = BACKUP_FOLDER . str_replace(WP_CONTENT_DIR, '', $dropbox[$i]['directory']);
      $base_d[] = str_replace(WP_CONTENT_DIR, '', $dropbox[$i]['directory']);
      $uploader->files[$i]['filename'] = $dropbox[$i]['filename'];
      $uploader->files[$i]['id'] = $dropbox[$i]['id'];
     }
     $uploader->upload($files_path, $dest);
     if (!isset($uploader->files[0]['what']))
       exit;

     $confirmed = '<table>
	<tr>
		<td colspan="3" style="font-size:smaller"><i>Multi Threading file transfer sending ' . count($dropbox) . ' files</i> </td>
	</tr>
<tr>
		<td colspan="3" style="font-weight:bold">Files to Go : ' . $tmp_numb_files . '</td>
	</tr>
';
     $confirmed .= '<tr><td colspan="3" style="color: #21759B;font-weight:bold">Files Uploaded to Dropbox and Confirmed</td></tr>';

     for ($i = 0; $i < count($uploader->files); $i++) {

      if ($uploader->files[$i]['what'] == true) {
       $confirmed .= '<tr><td><img src="' . WP_PLUGIN_URL . '/cloudsafe365-for-wp/images/tick.png"/></td><td>' . $uploader->files[$i]['filename'] . '</td><td>' . $base_d[$i] . '/</td></tr>';
       $request = "UPDATE cs365_external_back SET date_inserted = '" . time() . "',period = '" . $_POST["cs365_period"] . "' WHERE id = '" . $uploader->files[$i]['id'] . " AND ' LIMIT	1";
      }
      else {
       $errors = 1;
       $no_uploaded .= '<span style="color: red">' . $uploader->files[$i]['filename'] . '</span><br/>';
       if ($uploader->files[$i]['what'] != false) {
        $confirmed .= '<tr><td><img src="' . WP_PLUGIN_URL . '/cloudsafe365-for-wp/images/cross.png"/></td><td>' . $uploader->files[$i]['filename'] . '</td><td>' . $uploader->files[$i]['what'] . '</td></tr>';
       }
       else
         $confirmed .= '<tr><td><img src="' . WP_PLUGIN_URL . '/cloudsafe365-for-wp/images/cross.png"/></td><td>' . $uploader->files[$i]['filename'] . '</td><td>Upload Error</td></tr>';
       $request = "UPDATE cs365_external_back SET date_inserted = '" . time() . "',period = '" . $_POST["cs365_period"] . "',error = '1',error_msg = '" . mysql_real_escape_string(htmlspecialchars($error)) . "' WHERE id = '" . $uploader->files[$i]['id'] . " AND ' LIMIT	1";
      }
      mysql_query($request);
     }

     echo $confirmed;
     echo '</table>';

     exit;
    }

    /* ________________________________________________________________
      developed by cloudsafe365

      ________________________________________________________________ */
    function cs365_dropbox_single() {

     $request = "select * from cs365_external_back WHERE date_inserted is null limit 2";
     $mysql = mysql_query($request);
     $num_mysql = mysql_num_rows($mysql);
     if ($num_mysql > 0) {
      $dropbox = mysql_fetch_assoc($mysql);
      if ($num_mysql > 1)
        $dropbox_next = mysql_fetch_assoc($mysql);
     }
     mysql_free_result($mysql);

     if (isset($dropbox['id'])) {
      $request = "select count(id) as counter from cs365_external_back WHERE date_inserted is null";
      $mysql = mysql_query($request);
      $num_mysql = mysql_num_rows($mysql);
      if ($num_mysql > 0) {
       list($number_to_go) = mysql_fetch_row($mysql);
     mysql_free_result($mysql);
       ##checking number of files to go
       $request = "SELECT total_files FROM cs365_tmp_table LIMIT 1";
       $mysql = mysql_query($request);
       $num_mysql = mysql_num_rows($mysql);
       $tmp_numb_files = '';
       if ($num_mysql > 0) {
        list($tmp_numb_files) = mysql_fetch_row($mysql);
       }
       mysql_free_result($mysql);
       if (!preg_match('/\d/xsi', $tmp_numb_files)) {
        if ($number_to_go != 0) {
         $request = "UPDATE cs365_tmp_table SET total_files = '$number_to_go' LIMIT 1";
         $tmp_numb_files = $number_to_go;
         $wpdb->query($request);
        }
       }
      }
      $urlhome = get_option('home');
      $parsedurl = parse_url($urlhome);
      $location = preg_replace('/\W/xsi', '_', $parsedurl['host'] . $parsedurl['path']) . '_backup';
      define('BACKUP_FOLDER', preg_replace('/\W/xsi', '_', $parsedurl['host'] . $parsedurl['path']) . '_backup');
      require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/lib/DropboxUploader.php');
      try {
       $dest = BACKUP_FOLDER . str_replace(WP_CONTENT_DIR, '', $dropbox['directory']);
       $uploader = new DropboxUploader('', '');
       $uploader->upload($dropbox['file_path'], $dest);

       if (isset($re_login)) {
        echo '<span style="color: orange;font-weight:bold">' . $re_login . '</span><br/>';
       }
       echo '<span style="color: green">File Uploaded Confirmed : ' . $dropbox['filename'] . '</span><br/>';
       echo '<span style="color: green">Dropbox Folder : ' . $dest . '/</span><br/><br/>';

       $request = "UPDATE cs365_external_back SET date_inserted = '" . time() . "',period = '" . $_POST["cs365_period"] . "' WHERE id = '" . $dropbox['id'] . " AND ' LIMIT	1";
      }
      catch (Exception $e) {
       echo '<span style="color: red">Error: ' . htmlspecialchars($e->getMessage()) . '</span><br/>';
       echo '<span style="color: red">File Not Uploaded : ' . $dropbox['filename'] . '</span><br/>';
       $request = "UPDATE cs365_external_back SET date_inserted = '" . time() . "',period = '" . $_POST["cs365_period"] . "',error = '1',error_msg = '" . mysql_real_escape_string(htmlspecialchars($e->getMessage())) . "' WHERE id = '" . $dropbox['id'] . " AND ' LIMIT	1";
      }
      mysql_query($request);

      if (isset($dropbox_next)) {
       $dest = BACKUP_FOLDER . str_replace(WP_CONTENT_DIR, '', $dropbox_next['directory']);
       echo '<span style="color: blue">Now Sending to dropbox : ' . $dropbox_next['filename'] . '</span><br/>';
       echo '<span style="color: blue">dropbox Folder : ' . $dest . '</span><br/><br/>';
      }
      else {
       echo '<span style="color: red">No more to send</span><br/><br/>';
      }

      if ($tmp_numb_files > 0) {
       $perc = round(100 - (($number_to_go / $tmp_numb_files) * 100), 0);
       $uploaded = $tmp_numb_files - $number_to_go;
       echo '<span style="color: green">Percent Complete : ' . $perc . '%</span><br/>';
       echo '<span style="color: green">Number of files Uploaded this run :<span style="color: blue">' . $uploaded . '</span></span><br/>';
      }
      else {
       $request = "UPDATE cs365_tmp_table SET total_files = '$number_to_go' LIMIT 1";
       mysql_query($request);
      }
      echo '<span style="color: green">Number of files left : <span style="color: purple">' . $number_to_go . '</span></span><br/>';
      unset($dropbox);
     }
     exit;
    }

    function cs365_dropbox_reporte() {
     cs365_dropbox_report('e');
    }

    function cs365_dropbox_report($exit = '') {
     $report = (object) array();
     $report->start_time = $_POST["cs365start_time"];
     $report->error = 0;
     $report->total = 0;
     $report->finish_time = time();
     $mysql = mysql_query("SELECT period,date_inserted FROM cs365_external_back order by period desc LIMIT 1");
     $num_mysql = mysql_num_rows($mysql);
     if ($num_mysql > 0)
       list($report->period, $report->finish_time) = mysql_fetch_row($mysql);
     mysql_free_result($mysql);

     $tmp_time = $report->finish_time - $report->start_time;
     if ($tmp_time <= 0) {
      unset($report->finish_time);
      $report->finish_time = time();
     }

     $mysql = mysql_query("select count(id) as counter  from cs365_external_back where date_inserted is null");
     $num_mysql = mysql_num_rows($mysql);
     if ($num_mysql > 0)
       list($report->to_go) = mysql_fetch_row($mysql);
     mysql_free_result($mysql);

     $mysql = mysql_query("select count(id) as counter  from cs365_external_back where period = '" . $report->period . "'");
     $num_mysql = mysql_num_rows($mysql);
     if ($num_mysql > 0)
       list($report->total) = mysql_fetch_row($mysql);
     mysql_free_result($mysql);

     $mysql = mysql_query("select count(id) as counter from cs365_external_back where period = '" . $report->period . "' AND error = 1");
     $num_mysql = mysql_num_rows($mysql);
     if ($num_mysql > 0)
       list($report->error) = mysql_fetch_row($mysql);
     mysql_free_result($mysql);

     $report->success = $report->total - $report->error;

     if ($report->error > 0) {
      $mysql = mysql_query("SELECT * from cs365_external_back where period = '" . $report->period . "' AND error = 1");
      $num_mysql = mysql_num_rows($mysql);
      if ($num_mysql > 0) {
       while ($row = mysql_fetch_assoc($mysql)) {
        $report->error_dat[] = $row;
       }
      }
      mysql_free_result($mysql);
     }
     if ($report->total <= 0) {
      $report->finish_time = time();
      ?>
     <div style="width: 300px">
      <div style="display: table;">
       <div style="display: table-row;">
        <div id="sc365leftj" >It took:
        </div>
        <div id="sc365middlej">
         <?PHP
         $tmp_time = $report->finish_time - $report->start_time;
         echo cs365_secondsToTime($tmp_time);
         ?>
        </div>
       </div>
      </div>
      <br/>Backup: has been stopped looks like  nothing was transferred click the back up tab above and restart the process.<br/>
     </div>
     <?PHP
     return;
    }
    $report->tmp_time = $report->finish_time - $report->start_time;
    ?>
    <div style="width: 300px">
     <div style="display: table;">
      <div style="display: table-row;">
       <div id="sc365leftj" >Successful Insertions:
       </div>
       <div id="sc365middlej">
        <span style="color:green" id="span_id"><?PHP echo $report->success; ?></span>
       </div>
      </div>
      <div style="display: table-row;">
       <div id="sc365leftj" >Failed Insertions :
       </div>
       <div id="sc365middlej">
        <span style="color:red" id="span_id"> <?PHP echo $report->error; ?></span>
       </div>
      </div>
      <div style="display: table-row;">
       <div id="sc365leftj" >Total:
       </div>
       <div id="sc365middlej">
        <?PHP echo $report->total; ?>
       </div>
      </div>
      <div style="display: table-row;">
       <div id="sc365leftj" >It took:
       </div>
       <div id="sc365middlej">
        <?PHP
        echo cs365_secondsToTime($report->tmp_time);
        ?>
       </div>
      </div>
      <div style="display: table-row;">
       <div id="sc365leftj" >files to go:
       </div>
       <div id="sc365middlej">
        <span style="color:blue" id="span_id"><?PHP
      echo $report->to_go;
        ?></span>
       </div>
      </div>
      <?PHP
      if ($report->to_go <= 0) {
       ?>
       <div style="display: table-row;"><br />
        <div id="sc365leftj" ><a href="admin.php?page=cloudsafe365-download" class="button-primary" >Return to Backup</a>
        </div>
        <div id="sc365middlej">
         &nbsp;
        </div>
       </div>
      <?PHP } ?>
     </div>
    </div>
    <?PHP
    if ($report->error > 0) {
     ?>
     <span style="color:red;font-weight:bold" >Upload Failures</span>
     <br/>
     <div style="display: table;">
      <div style="display: table-row;">
       <div id="sc365leftj" style="font-weight: bold">Filename</div>
       <div id="sc365middlej" style="font-weight: bold">Location</div>
       <div id="sc365rightj" style="font-weight: bold">Type Error</div>
      </div>
      <?PHP
      for ($i = 0; $i < count($report->error_dat); $i++) {
       $preg_match_all[$i][0]
       ?>
       <div style="display: table-row;">
        <div id="sc365leftj"  style="color:black"><?PHP echo $report->error_dat[$i]['filename']; ?></div>
        <div id="sc365middlej" style="color:black" ><?PHP echo preg_replace('/.*?wp-content/xsi', '', $report->error_dat[$i]['directory']); ?></div>
        <div id="sc365rightj" style="color:red"><?PHP
   echo $report->error_dat[$i]['wp_type'];
   echo ' ';
   echo $report->error_dat[$i]['error_msg'];
       ?></div>
       </div>
       <?PHP
      }
      ?>
     </div><br/><br/>
     <?PHP
    }

    if ($report->error > 0) {
     ?>
     <br/><br/>
     <form method="post" action="admin.php?page=cloudsafe365-download&cs365do=dp3" style="margin-left: 0px;margin-top: 0px;margin-right: 0px;	margin-bottom: 0px;">
      <div style="width: 300px">
       <input type="checkbox" name="cs365retry" value="yes"/> Re-try uploading files with errors<br/><br/>
       <input type="hidden" name="cs365_plugins_db" value="<?PHP echo $_POST['cs365_plugins_db']; ?>"/>
       <input type="hidden" name="cs365_theme_db" value="<?PHP echo $_POST['cs365_theme_db']; ?>"/>
       <input type="hidden" name="cs365_files_db" value="<?PHP echo $_POST['cs365_files_db']; ?>"/>
       <input type="hidden" name="cs365_period" value="<?PHP echo $_POST['cs365_period']; ?>"/>
       <input type="submit" value="<?php esc_attr_e('Re-Run'); ?>" class="button-primary" name="Submit">
       <br/><br/><span style="white-space:nowrap">* if you have outstanding uploads you can re-run to upload those files as needed</span>
      </div>
     </form>
     <?PHP
    }
    if ($exit == 'e')
      exit;
   }

   function cs365_secondsToTime($seconds) {
    // extract hours
    $hours = floor($seconds / (60 * 60));

    // extract minutes
    $divisor_for_minutes = $seconds % (60 * 60);
    $minutes = floor($divisor_for_minutes / 60);

    // extract the remaining seconds
    $divisor_for_seconds = $divisor_for_minutes % 60;
    $seconds = ceil($divisor_for_seconds);

    // return the final array
    $obj = array(
     "h" => (int) $hours,
     "m" => (int) $minutes,
     "s" => (int) $seconds,
    );

    if ($hours != 0) {
     return $hours . ':' . $minutes . '.' . $seconds . ' hours';
    }
    elseif ($minutes != 0) {
     return $minutes . '.' . $seconds . ' minutes';
    }

    return $seconds . ' seconds';
   }
  ?>