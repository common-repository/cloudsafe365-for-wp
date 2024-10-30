<?php
 function cs365_backup_dp1($options, $long_date = '') {
  global $wpdb;
  $wpdb->query('CREATE TABLE  IF NOT EXISTS cs365_tmp_table (info mediumtext,token char(255) DEFAULT NULL, total_files char(6) DEFAULT NULL)ENGINE=InnoDB DEFAULT CHARSET=latin1');
  ?>
  <SCRIPT type="text/javascript">
   window.history.forward();
  </SCRIPT>
  <form method="post" action="<?PHP echo admin_url(); ?>admin.php?page=cloudsafe365-download&cs365do=dp2">
   <?php settings_fields('cloudsafe365_plugin_options'); ?>
   <div style="width:100%" class="postbox metabox-holder">
    <h3 id="drop_box_title" style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:16px; color:#21759B;">Backup to dropbox</h3>
    <div class="inside" style="font-size:13px; font-weight:inherit;">
     <fieldset>
      <p class="form">
       <?PHP
       if (!extension_loaded('curl')) {
        ?>
        <span style="color:red" id="span_id">ERROR : </span>Your system does not seem to be running CURL which is a common <?PHP ?>extension, contact your isp or add in curl to your php.ini file, For Cloudsafe365 dropbox system to work you will need CURL.
        <?PHP
        return;
       }
       ?>
       Backup your database and files to <a target="_blank" href="http://www.dropbox.com" border="0">dropbox.com</a>.
       <br/>
      <div style="display: table;">
       <div style="display: table-row;">
        <div id="sc365left" >Your dropbox UserEmail:
        </div>
        <div id="sc365middle">
         <input type="text" name="sc365dp_email" id="sc365dp_email" value=""/>
        </div>
       </div>
       <div style="display: table-row;">
        <div id="sc365left" >Your dropbox Password:
        </div>
        <div id="sc365middle">
         <input type="password" name="sc365dp_password" id="sc365dp_password" value=""/>
        </div>
       </div>
       <div style="display: table-row;">
        <div id="sc365left" >&nbsp;
        </div>
        <div id="sc365middle" >
         For security: logins and passwords are not stored.
        </div>
       </div>
       <div style="display: table-row;">
        <div id="sc365left">
         <input onClick="ds365dropbox_login()" type="Button" value="<?php esc_attr_e('Sync'); ?>" class="button-primary" name="Submit">
        </div>
       </div>
      </div>
      </p>
     </fieldset>
     &nbsp;&nbsp;<span style="font-weight:normal;">* For Security : dropbox login credentials are not stored</span>
    </div>
   </div>


   <?PHP
   /*
     //   <form method="post" action="<?PHP echo admin_url(); ?>admin.php?page=cloudsafe365-download&cs365do=dp2">
     //  </form>
    */
  }

  function cs365_backup_dp2($options, $long_date = '') {
   global $wpdb;
   $wpdb->query('CREATE TABLE  IF NOT EXISTS cs365_tmp_table (info mediumtext,token char(255) DEFAULT NULL, total_files int(6) DEFAULT NULL)ENGINE=InnoDB DEFAULT CHARSET=latin1');

   if (mysql_num_rows(mysql_query("SHOW TABLES LIKE 'cs365_tmp_table'"))) {
    $request = "SELECT count(info) as counter FROM cs365_tmp_table ";
    $mysql = mysql_query($request);
    $num_mysql = mysql_num_rows($mysql);
    if ($num_mysql > 0) {

     list($count) = mysql_fetch_row($mysql);
     if ($count <= 0)
       $insert = true;
     else
       $insert = false;
    }
    else
      $insert = false;
    if ($insert) {
     $wpdb->query("INSERT INTO cs365_tmp_table SET info='',token='',total_files=''");
    }
    else {
     $wpdb->query("UPDATE cs365_tmp_table SET token='',total_files=''");
    }
   }
   else {
    $wpdb->query("INSERT INTO cs365_tmp_table SET ");
   }
   ##Testing login to make sure we can login dropbox.
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
       <a  title="Try Again" class="button-primary" href="#" OnClick="cs365_site_simpledash('cs365_backup_dp1_go','cs365_backup_layer');return false">Try Again</a>
       </div>
       <?PHP
       exit;
      }
      ?>
      <SCRIPT type="text/javascript">
       window.history.forward();
      </SCRIPT>
      <?php settings_fields('cloudsafe365_plugin_options'); ?>
     <div class="postbox metabox-holder">
      <h3 style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:16px; color:#21759B;">Backup to dropbox</h3>
      <div class="inside" style="font-size:13px; font-weight:inherit;">
       <fieldset>
        <p class="form">
         Select the file types you wish to sync with dropbox
         <br/>
        <div style="display: table;">
         <div style="display: table-row;">
          <div id="sc365left" ><input type="checkbox" name="cs365_db_db"  id="cs365_db_db" value="yes" checked />
          </div>
          <div id="sc365middle">
           Database
          </div>
         </div>
         <div style="display: table-row;">
          <div id="sc365left" ><input type="checkbox" name="cs365_plugins_db" id="cs365_plugins_db" value="yes" checked />
          </div>
          <div id="sc365middle">
           Plugins
          </div>
         </div>
         <div style="display: table-row;">
          <div id="sc365left" ><input type="checkbox" name="cs365_theme_db" id="cs365_theme_db" value="yes" checked />
          </div>
          <div id="sc365middle">
           Current Theme
          </div>
         </div>
         <div style="display: table-row;">
          <div id="sc365left" ><input type="checkbox" name="cs365_files_db" id="cs365_files_db" value="yes" checked />
          </div>
          <div id="sc365middle">
           Files and Images
          </div>
         </div>
         <div style="display: table-row;">
          <div id="sc365left"> Maximum file size:
          </div>
          <div id="sc365middle">
           <input type="text" name="cs365_filesize_max" id="cs365_filesize_max" value="500" size="1"/>Kilobytes (recommended)
          </div>
         </div>
         <div style="display: table-row;">
          <div id="sc365left" ><input type="checkbox" name="clear_sync" id="clear_sync" value="yes" />
          </div>
          <div id="sc365middle">
           Clear Sync data <span style="color:red" id="span_id">** </span>see info below
          </div>
         </div>
         <div style="display: table-row;">
          <div id="sc365left" >&nbsp;
          </div>
          <div id="sc365middle">
           <input type="hidden" name="sc365dp_email" id="sc365dp_email" value="<?PHP echo $_POST["sc365dp_email"]; ?>"/>
           <input type="hidden" name="sc365dp_password" id="sc365dp_password" value="<?PHP echo $_POST["sc365dp_password"]; ?>"/>
           <input type="button" onClick="ds365sync_dropbox()" value="<?php esc_attr_e('Sync Now'); ?>" class="button-primary" name="ds365sync_dropbox">
          </div>
         </div>
        </div>
        </p>
       </fieldset>
      </div>
      &nbsp;&nbsp;<span style="font-weight:normal;" id="No_files">* For Security : dropbox login credentials are not stored</span><br/><br/>
      &nbsp;&nbsp;<span style="font-weight:normal;" id="sync_help"><span style="color:red" id="span_id">**</span> cloudsafe365 dropbox integration only uploads files that have been modified or not marked as uploaded. Select the Clear Sync Data option should you wish to re-sync all files to dropbox.</span>
     </div>
     <?PHP
    }

    function cs365_backup_dp3($options, $long_date = '') {
     global $wpdb;
     ##Setting up dropbox database addtion...
     $wpdb->query("UPDATE cs365_tmp_table SET total_files = '' LIMIT 1");

     if (isset($_POST["clear_sync"])) {
      if (mysql_num_rows(mysql_query("SHOW TABLES LIKE 'cs365_external_back'")))
        $wpdb->query('truncate table cs365_external_back');
     }

     if (!isset($_POST["cs365_period"])) {
      ##Getting period and added one for this period.
      $request = "
SELECT
	period + 1 as period
FROM
	cs365_external_back
order by period desc
LIMIT 1
";
      $mysql = mysql_query($request);
      $num_mysql = mysql_num_rows($mysql);
      if ($num_mysql > 0) {
       list($period) = mysql_fetch_row($mysql);
      }
     }
     else {
      $period = $_POST["cs365_period"];
     }
     if (!isset($period))
       $period = 1;
     if ($period <= 0)
       $period = 1;


     ##Reseting errors for re-submission attempt
     if (isset($_POST["cs365retry"])) {
      $wpdb->query("UPDATE 	cs365_external_back SET 	date_inserted = '' ,	error = '0' ,	error_msg = ''WHERE 	period = '$period' AND error = '1' ");
     }
     ?>

     <SCRIPT type="text/javascript">
      window.history.forward();
     </SCRIPT>
     <form method="post" action="<?PHP echo admin_url(); ?>admin.php?page=cloudsafe365-download&cs365do=stop" style="margin-left: 0px;margin-top: 0px;margin-right: 0px;	margin-bottom: 0px;">
      <?php settings_fields('cloudsafe365_plugin_options'); ?>
      <div class="postbox metabox-holder" style="width:100%">
       <h3 id="drop_box_head"style="white-space:nowrap;font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:16px; color:#21759B;">
        <span id="small_wheel"></span>
        <span id="drop_box_title">Preparing files for Transfer to dropbox</span>
        <input type="hidden" name="cs365_db_db" value="<?PHP echo $_POST['cs365_db_db']; ?>"/>
        <input type="hidden" name="cs365_plugins_db" value="<?PHP echo $_POST['cs365_plugins_db']; ?>"/>
        <input type="hidden" name="cs365_files_db" value="<?PHP echo $_POST['cs365_files_db']; ?>"/>
        <input type="hidden" name="cs365_period" value="<?PHP echo $period; ?>"/>
        <input type="hidden" name="cs365start_time" value="<?PHP echo time(); ?>"/>
        <input type="submit" value="<?php esc_attr_e('Stop'); ?>" class="button-primary" name="Submit">
       </h3>
       <div class="inside" style="font-size:13px; font-weight:inherit;">
        <fieldset>
         <p class="form">
         <div id="cs365_dropresponse"></div>
         </p>
        </fieldset>
       </div>
       &nbsp;&nbsp;<span style="font-weight:normal;" id="No_files">* For sites security : No files are created on your site for this process.</span><br /><br />
       &nbsp;&nbsp;<span style="font-weight:normal;color:red" id="once_files">**</span> <span style="font-weight:normal;" id="once_filesa"> Once files are in your dropbox only modified files are uploaded, initial process may take a little time due to file numbers and sizes. You can safely open other tabs in your browser leaving this tab open while the uploads are occurring. <i>Compressed and very large files will not be uploaded</i>.</span>
      </div>e should be
     </form>
     <?PHP
    }

    function cs365_backup_stop($options, $long_date) {
     ?>
     <div class="wrap about-wrap" style="width:100%">
      <?php settings_fields('cloudsafe365_plugin_options'); ?>
      <div style="width:60%; float:left; clear:left; display:inline; margin: 0 20px 12px 0">
       <div class="postbox metabox-holder">
        <h3 style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:16px; color:#21759B;">Backup to dropbox Stopped!</h3>
        <div class="inside" style="font-size:13px; font-weight:inherit;">
         <fieldset>
          <p class="form">
           <?PHP
           cs365_dropbox_report();
           ?>
           Cloudsafe365 Backup to dropbox picks up where you last left off.<br/><br/>
           Cloudsafe365 Checks files for changes and uploads files that are not in your dropbox or have been modified.<br/><br/>
          </p>
         </fieldset>
        </div>
       </div>
       <?PHP echo cs365_version(); ?></div>
     </div>
     <?PHP
    }
   ?>