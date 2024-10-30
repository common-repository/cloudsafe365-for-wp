<?php
 function cs365_backup_setup($options, $long_date='') {
  ?>
   <?php settings_fields('cloudsafe365_plugin_options'); ?>
   <?php
   if ($options['cloudsafe365_type'] == 0)
     $cs365_heading = 'Local Site Backup';
   else
     $cs365_heading = 'Automatic Encrypted Site Backup';
   ?>

   <?PHP
   $used_data = array(
    'cloudsafe365_backup_database',
    'cloudsafe365_real_time_backups',
    'cloudsafe365_backup_when');
   echo sc365_setup_hidden($options, $used_data)
   ?>

   <?PHP
   if (isset($_GET['settings-updated'])) {
    if ($_GET['settings-updated'] == 'true') {
     ?>
   <h2 style="color:#21759B;font-weight:bold;width:59%;background-color:#ffffca">Backup settings updated</h2>

     <?PHP
    }
   }
   ?>

   <div style="width:100%" class="postbox metabox-holder">
    <h3 style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:16px; color:#21759B;"><?php echo $cs365_heading; ?></h3>
    <div class="inside" style="font-size:13px; font-weight:inherit;">
     <fieldset>
      <p class="form">
       <input name="cloudsafe365_plugin_options[cloudsafe365_backup_database]" id="cloudsafe365_backup_database" type="checkbox" value="1"  <?PHP echo checked(1, isset($options['cloudsafe365_backup_database']), false); ?> />
       Database<br/><br/>
       <input name="cloudsafe365_plugin_options[cloudsafe365_real_time_backups]" id="cloudsafe365_real_time_backups" type="checkbox" value="1"  <?PHP echo checked(1, isset($options['cloudsafe365_real_time_backups']), false); ?> />
       Real time back ups of comments, posts and new content<br/><br/>
       Default Backup Cycle*<br/>
       <input type="radio" id="cloudsafe365_backup_when1" name="cloudsafe365_plugin_options[cloudsafe365_backup_when]" value="1"   <?php checked('1', $options['cloudsafe365_backup_when']); ?>/>&nbsp;Daily&nbsp;&nbsp;
       <input type="radio" id="cloudsafe365_backup_when2" name="cloudsafe365_plugin_options[cloudsafe365_backup_when]" value="2"  <?php checked('2', $options['cloudsafe365_backup_when']); ?>/>&nbsp;Weekly&nbsp;&nbsp;
       <input type="radio" id="cloudsafe365_backup_when3" name="cloudsafe365_plugin_options[cloudsafe365_backup_when]" value="3"  <?php checked('3', $options['cloudsafe365_backup_when']); ?>/>&nbsp;Monthly&nbsp;&nbsp;
       <input type="radio" id="cloudsafe365_backup_when4" name="cloudsafe365_plugin_options[cloudsafe365_backup_when]" value="0"  <?php checked('0', $options['cloudsafe365_backup_when']); ?>/>&nbsp;No Backups
      </p>
      <?PHP
      if ($options['cloudsafe365_type'] == 0) {
       ?>
       <span style="font-weight:normal;">
        *Automatic backups are performed every time a change is made to your site. In the event no changes are made, the default backup cycle setting will apply.</span>
        <br /><br />
        <span style="font-weight:normal;color:#21759B">Upgrade to Plus version for full External backup powered by Amazon </span><br />
       <?php
      }
      ?>
     </fieldset>
    </div>
    <table class="form-table">
     <tr>
      <td><input type="button" OnClick="cs365_save_backup_options()" value="<?php esc_attr_e('Save Changes'); ?>" class="button-primary" name="Submit"></td>
     </tr>
    </table>
   </div>
  <?PHP
  if (isset($_GET['settings-updated'])) {
   if ($_GET['settings-updated'] == 'true') {
    ?>
    <script type="text/javascript">
     function timeMsg()
     {
      window.location = "?page=cloudsafe365-download";
     }
     timeMsg()
    </script>
    <?PHP
   }
  }
 }
?>