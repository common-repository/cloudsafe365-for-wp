<?php
function cs365_dashboard() {

  $options = get_option('cloudsafe365_plugin_options');
  ?>
  <div class="wrap about-wrap" >
    <div style="width:60%; float:left; clear:left; display:inline; margin: 0 20px 12px 0" >
      <div id="cs365_dashwait"><img src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/wait.gif"  alt="Please Wait" height="40px" width="40px" />Loading Summary...
        <br/><br/>
      </div>

      <div id="cs365_dashcontent"></div>

      <?PHP cs365_version(); ?></div>
    <div style="width:24%; float:right; display:inline; margin-right:100px;">
      <div class="postbox metabox-holder">
        <h3 style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:16px; color:#21759B;">News Updates - cloudsafe365</h3>
        <div class="inside" style="font-size:13px; font-weight:normal;" id="cs365_news">
        </div>
        <div style="width:100%;margin:auto;white-space:none">&nbsp;&nbsp;
          <a href="http://www.cloudsafe365.com/blog/" target="_blank"><img src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/btn-blog-promo.jpg"  alt="image" style="border:0"/></a>
        </div>
      </div>
    </div>
  </div>

  <?PHP
  if (isset($_GET["page"])) {
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
      //cs365_site_simpledash('cs365_scan_site','cs365_scan_site');
      cs365_site_simpledash('cs365_news','cs365_news');
      cs365_site_simpledash('cs365_site_simpledash','cs365_dashcontent');
    </script>
    <?PHP
  }
}
?>