<?php
function cloudsafe365_admin_protection_pres() {
  $options = get_option('cloudsafe365_plugin_options');
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
  ?>
  <div class="wrap about-wrap" style="width:100%">
    <form action="options.php" method="post">
      <?php settings_fields('cloudsafe365_plugin_options'); ?>
      <div style="width:60%; float:left; clear:left; display:inline; margin: 0 20px 12px 0">
        <div class="postbox metabox-holder">
          <h3 style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:16px; color:#21759B;">Site Malware Protection</h3>
          <div class="inside" style="font-size:13px; font-weight:inherit;">
            <?PHP
            if (!isset($options['cloudsafe365_disable_general_hack']))
                $options['cloudsafe365_disable_general_hack'] = 1;
            $used_data = array(
               'cloudsafe365_content_scraping',
               'cloudsafe365_page_copying',
               'cloudsafe365_disable_right_click',
               'cloudsafe365_disable_general_hack');
            echo sc365_setup_hidden($options, $used_data);
            ?>
            <fieldset>
              <p class="form">
                <?PHP
                $t = CS365ACTIVE;
                if ($t == 'DISABLED') {
                  ?>
                Monitor Malware, Block Bots and Hackers
                  <?PHP

                }
                else {
                  ?>
                  Stop Malware and Spam Black Bots and Hackers
                  <?PHP
                }
                ?>

                <span style="color:red" id="span_id"> * </span> <span style="font-weight: normal;font-style: italic">(Stop Malware entering site & content)</span>
                <br/><br/>
                &nbsp;&nbsp;<input type="radio" id="name" name="cloudsafe365_plugin_options[cloudsafe365_content_scraping]" value="1" <?php
              checked('1', $options['cloudsafe365_content_scraping']);
              echo CS365ACTIVE;
                ?>/>&nbsp;Stop All
                                <?PHP
                $t = CS365ACTIVE;
                if ($t == 'DISABLED') {
                  ?>
                <i style="font-size:10px">&nbsp;(Plus Version)</i>
                  <?PHP
                }
                ?>
                <br/>
                &nbsp;&nbsp;<input type="radio" id="name" name="cloudsafe365_plugin_options[cloudsafe365_content_scraping]" value="2" <?php
              checked('2', $options['cloudsafe365_content_scraping']);
              echo CS365ACTIVE;
                ?>/>&nbspAllow first page

                                                <?PHP
                $t = CS365ACTIVE;
                if ($t == 'DISABLED') {
                  ?>
                <i>&nbsp;Plus Version</i>
                  <?PHP
                }
                ?>

                <br/>
                &nbsp;&nbsp;<input type="radio" id="name" name="cloudsafe365_plugin_options[cloudsafe365_content_scraping]" value="3" <?php checked('3', $options['cloudsafe365_content_scraping']); ?>/>&nbsp;Monitor Only&nbsp;
                <br/>
                &nbsp;&nbsp;<input type="radio" id="name" name="cloudsafe365_plugin_options[cloudsafe365_content_scraping]" value="4" <?php checked('4', $options['cloudsafe365_content_scraping']); ?>/>&nbsp;None&nbsp;
                <br/>  <br/>

                <!--
                           &nbsp;&nbsp;<input name="cloudsafe365_plugin_options[cloudsafe365_stop_spam]" id="cloudsafe365_stop_spam" type="checkbox" value="1" <?PHP echo cloudsafe365_check_check($options['cloudsafe365_stop_spam']); ?>/>&nbsp;Stop Form Spam/malware
                           <br/>  <br/>
                -->
                &nbsp;&nbsp;<input name="cloudsafe365_plugin_options[cloudsafe365_page_copying]" id="cloudsafe365_page_copying" type="checkbox" value="1" <?PHP
              echo cloudsafe365_check_check($options['cloudsafe365_page_copying']);
              echo CS365ACTIVE;
                ?> />&nbsp;Prevent Page Copying&nbsp; <span style="font-weight: normal;font-style: italic">(From Browser)</span>
                <br/>
                &nbsp;&nbsp;<input name="cloudsafe365_plugin_options[cloudsafe365_disable_right_click]" id="cloudsafe365_disable_right_click" type="checkbox" value="1" <?PHP echo cloudsafe365_check_check($options['cloudsafe365_disable_right_click']); ?>/>&nbsp;Disable Right Click&nbsp; <span style="font-weight: normal;font-style: italic">(From Browser)</span>
                <br/><br/>
                &nbsp;&nbsp;<input name="cloudsafe365_plugin_options[cloudsafe365_disable_general_hack]" id="cloudsafe365_disable_general_hack" type="checkbox" value="1" <?PHP echo cloudsafe365_check_check($options['cloudsafe365_disable_general_hack']); ?>/>&nbsp;Stop General Hacking &nbsp;<span style="color:red" id="span_id"> * * </span> <span style="font-weight:normal;"> <span style="font-weight: normal;font-style: italic">(Stop sql injections and many more types of penetration attempts)</span>
                  <br/><br/>
                  <span style="color:red" id="span_id"> * </span> <span style="font-weight:normal;cursor:pointer;color:#21759B;font-style: italic" id="Show_one_star" Onclick="show_stars(1)" >Show...<br /></span>
                  <br/>
                  <span style="color:red" id="span_id"> * * </span> </span> <span style="font-weight:normal;cursor:pointer;color:#21759B;font-style: italic" id="Show_two_star" Onclick="show_stars(2)" >Show...<br /></span>
                <br/><br/>
                <input type="submit" value="<?php esc_attr_e('Save Changes'); ?>" class="button-primary" name="Submit">
            </fieldset>
          </div>
        </div>
        <?PHP echo cs365_version(); ?></div></form>
    <div style="width:24%; float:right; display:inline; margin-right:100px;"><div class="postbox metabox-holder">
        <h3 style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:16px; color:#21759B;">Help Tips</h3>
        <div class="inside" style="font-size:13px; font-weight:normal;">
          <p><strong>Prevent Page Copying:</strong> Turn on this option if you don't want users to be able to copy your content from the browser using ctrl c and copy options. </p>
          <p><strong>Disable Right Click :</strong> Turn this option on to stop the ability to mouse right click on your site.</p>
          <a href="http://www.cloudsafe365.com/blog/" target="_blank"><img src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/btn-blog-promo.jpg"  alt="image" style="border:0"/></a></div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    <!--
    /**
     * sho
     */
    function show_stars(number) {
      var content = '<span style="color:black;pointer:normal;font-style: normal">';
      if (number == 1) {
        var x=document.getElementById("Show_one_star");
        content +=  'Cloudsafe365 unique XP engine plus stops content theft from automatic bots and scrapers as well as prevents hackers from infiltrating into the html layer of your website, give your site unprecedented protection'
        content +=  '<ul>';
        content +=  '<li><strong>Stop All</strong> : All Pages pages are protected from Scraping content theft</li>';
        content +=  '<li><strong>Allow first page</strong> : First page is passed (Note: Antihacking still applies to first page this is for site scraping/content theft</li>';
        content +=  '<li><strong>Monitor only</strong> : Content theft and scraping is monitored and reported but not stopped anti hacking still active</li>';
        content +=  '<li><strong>None : </strong> Content theft and scraping is not monitored or stopped anti hacking still active</li>';
        content +=  '</ul>';
      }
      else
      {
        var x=document.getElementById("Show_two_star");
        content +=  'Protect your site against XSS, RFI, CRLF, CSRF, Base64, Code Injection, SQL Injection hacking as well meta and  Remote file injections you are also protect  from command based attacks, Advanced SQL and Remote and advanced file injections';
      }
      content += '</span>';
      x.innerHTML = content;
    }
    //-->
  </script>
  <?PHP
}
?>