<?php
 function cloudsafe365_admin_options_pres() {
  settings_errors();
  $options = get_option('cloudsafe365_plugin_options');
  ob_start();
  ?>
  <div class="wrap about-wrap" style="width:100%">
   <form action="options.php" method="post">
    <?php settings_fields('cloudsafe365_plugin_options'); ?>
    <div style="width:60%; float:left; clear:left; display:inline; margin: 0 20px 12px 0">
     <div class="postbox metabox-holder">
      <h3 style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:16px; color:#21759B;">General Options</h3>
      <?PHP
      $used_data = array(
       'cloudsafe365_email_reports',
       'cloudsafe365_email_address',
       'cloudsafe365_protected_by');
      echo sc365_setup_hidden($options, $used_data)
      ?>
      <div class="inside" style="font-size:13px; font-weight:inherit;">
       <fieldset>
        <p class="form">
         Email Reports to <input name='cloudsafe365_plugin_options[cloudsafe365_email_address]' id='cloudsafe365_email_address' type='text' class='regular-text' value='<?PHP echo $options['cloudsafe365_email_address']; ?>' />
        <table class="form-table" style="border:1px solid grey;">
         <tr>
          <td>Email Frequency</td>
         </tr>
         <tr valign="top">
          <td>
           <input type="radio" id="name" name="cloudsafe365_plugin_options[cloudsafe365_email_reports]" value="daily" <?php checked('daily', $options['cloudsafe365_email_reports']); ?>/>&nbsp;Daily&nbsp;&nbsp;
           <input type="radio" id="name" name="cloudsafe365_plugin_options[cloudsafe365_email_reports]" value="weekly" <?php checked('weekly', $options['cloudsafe365_email_reports']); ?>/>&nbsp;Weekly&nbsp;&nbsp;
           <input type="radio" id="name" name="cloudsafe365_plugin_options[cloudsafe365_email_reports]" value="monthly" <?php checked('monthly', $options['cloudsafe365_email_reports']); ?>/>&nbsp;Monthly&nbsp;&nbsp;
           <input type="radio" id="name" name="cloudsafe365_plugin_options[cloudsafe365_email_reports]" value="none" <?php checked('none', $options['cloudsafe365_email_reports']); ?>/>&nbsp;None&nbsp;&nbsp;

          </td>
         </tr>
        </table><br/>
        <input name="cloudsafe365_plugin_options[cloudsafe365_protected_by]" id="cloudsafe365_protected" type="checkbox" value="1" <?PHP echo cloudsafe365_check_check($options['cloudsafe365_protected_by']); ?> />&nbsp;&nbsp;Add Protected by cloudsafe365
        </p>
        <p class="submit"><input type="submit" value="<?php esc_attr_e('Save Changes'); ?>" class="button-primary" name="Submit"></p>
       </fieldset>
      </div>
     </div>
     <?PHP echo cs365_version(); ?></div></form>
   <div style="width:24%; float:right; display:inline; margin-right:100px;"><div class="postbox metabox-holder">
     <h3 style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:16px; color:#21759B;">Help Tips</h3>
     <div class="inside" style="font-size:13px; font-weight:normal;">
      <p><strong>Emails:</strong> Add in multiple email addresses by comma seperating the addresses. </p>
      <a href="http://www.cloudsafe365.com/blog/" target="_blank"><img src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/btn-blog-promo.jpg"  alt="image" style="border:0"/></a></div>
    </div>
      </div>
      </div>
      <?PHP
      ob_end_flush();
     }
    ?>