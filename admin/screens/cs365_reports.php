<?php
 function cloudsafe365_admin_report_press() {
  $options = get_option('cloudsafe365_plugin_options');

  if (isset($_POST["cs365allowed_ips"])) {
   $allowed = '';
   $array = array('cs365allowed_ips', 'Submit');
   foreach ($_POST as $aton => $value) {
    if (in_array($aton, $array)) {
     continue;
    }
    $allowed .= $aton . '|';
   }
   $allowed = substr_replace($allowed, '', -1);
   //echo wp_remote_fopen(CS365 . '/reporting/reporting.php?key=' . $options['cloudsafe365_api_key'] . '&ips=' . $_POST["cs365allowed_ips"] . '&allowed_ips=' . $allowed);
  }

  if (!isset($_POST["date_filter"])) {
   $date_filter = urlencode('this month');
  }
  else {
   $date_filter = urlencode($_POST["date_filter"]);
  }
  ?>
  <div class="wrap cloudsafe365">
   <div id="dashboard-widgets" class="metabox-holder">
    <div id="cs365_dashwait"><img src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/wait.gif"  alt="Please Wait" height="40px" width="40px" />Loading Reports...</div>
    <div id="cs365_dashcontent"></div>
    <div id="cs365_get_report_one"></div>
    <div id="cs365_get_report_two"></div>
    <div id="cs365_get_report_four"></div>
    <div id="cs365_get_report_five"></div>
    <?php
   }
  ?>

  <script type="text/javascript">
   function cs365_site_simpledash(cs365_action,cs365_id)
   {
    jQuery(document).ready(function($) {
     async: true
     var data = {
      action: cs365_action
     };
     jQuery.post(ajaxurl, data, function(response)
     {
      var patt=/\w/;
      if (patt.exec(response))
      {


       var y=document.getElementById('cs365_dashwait');
       y.innerHTML = '';
       var x=document.getElementById(cs365_id);
       x.innerHTML = response;
      }
     });
    });
   }
   cs365_site_simpledash('cs365_site_simpledash','cs365_dashcontent');
   cs365_site_simpledash('cs365_get_report_one','cs365_get_report_one');
   cs365_site_simpledash('cs365_get_report_two','cs365_get_report_two');
   cs365_site_simpledash('cs365_get_report_four','cs365_get_report_four');
   cs365_site_simpledash('cs365_get_report_five','cs365_get_report_five');
  </script>