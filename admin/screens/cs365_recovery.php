<?php
 function cloudsafe365_recovery() {
  $options = get_option('cloudsafe365_plugin_options');
  ?>
  <div class="wrap about-wrap" style="width:100%">
   <div style="width:60%; float:left; clear:left; display:inline; margin: 0 20px 12px 0"><div class="postbox metabox-holder">
     <h3 style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:16px; color:#21759B;">Recovery Settings</h3>
     <div class="inside" style="font-size:13px; font-weight:normal;"><div class="table table_content">
       <table width="100%" border="0" cellpadding="0" cellspacing="0" align="left">
        <tr>
         <td>                                        <!-- Left side start -->
          <form method="post" action="">
           <table width="100%" border="0" cellpadding="0" cellspacing="10" align="left">
            <tr>
             <td>
              <table border="0">
               <tr valign="middle">
                <td style="text-align:right"><strong>Source:</strong></td>
                <td style="text-align:left"><select id="cloudsafe365_source" name="cloudsafe365_source">
                  <?PHP
                  if (CS365ACTIVE != 'DISABLED') {
                   ?>
                   <option value="0">cloudsafe365</option>
                   <?PHP
                  }
                   ?>
                   <option value="1">local</option>
                 </select></td>
                <td style="text-align:right">&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
               </tr>
              </table>
             </td>
            </tr>
            <tr>
             <td>
              <table border="0">
               <tr valign="middle">
                <td style="text-align:right"><strong>Time:</strong></td>
                <td style="text-align:left"><select id="cloudsafe365_backup_when" name="cloudsafe365_backup_when">
                  <?PHP
                  require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/lib/cs365_calendar_restore.php');
                  $cs365_calendar_restore = new cs365_calendar_restore();
                  $dates = $cs365_calendar_restore->get_restore_calandar();
                  $hours = '';
                  $hours .= '<option value="' . $dates[0] . '">All</option>';
                  $hours .= '<option value="' . (strtotime("now") - 3600) . '">1 hour ago</option>';
                  $hours .= '<option value="' . (strtotime("now") - 3600 * 2) . '">2 hours ago</option>';
                  $hours .= '<option value="' . (strtotime("now") - 3600 * 4) . '">4 hours ago</option>';
                  $hours .= '<option value="' . (strtotime("now") - 3600 * 8) . '">8 hour ago</option>';
                  $hours .= '<option value="' . (strtotime("now") - 3600 * 16) . '">16 hours ago</option>';
                  echo $hours;
                  if (isset($dates[1]))
                    for ($i = 1; $i < count($dates); $i++) {
                    ?>
                    <option value='<?PHP echo $dates[$i]; ?>'><?PHP echo date('D d M Y', $dates[$i]); ?></option>
                    <?PHP
                    $long_date = date('d-M-Y g:i:s', $dates[$i]);
                   }
                  ?>
                 </select></td>
                <td style="text-align:right">&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
               </tr>
              </table>
             </td>
            </tr>
            <tr>
             <td>
              <table border="0" cellpadding="0" cellspacing="10" align="left">
               <tr>
                <td colspan="2"><strong>Data Type</strong></td>
               </tr>
               <tr>
                <td style="padding-left:15px">Database</td>
                <td><input type="checkbox" name="recover_db" value="yes" checked/></td>
               </tr>
               <tr>
                <td style="padding-left:15px">Themes</td>
                <td><input type="checkbox" name="recover_th" value="yes" DISABLED/></td>
               </tr>
               <tr>
                <td style="padding-left:15px">Plugins</td>
                <td><input type="checkbox" name="recover_pl" value="yes" DISABLED/></td>
               </tr>
               <tr>
                <td style="padding-left:15px">Media</td>
                <td><input type="checkbox" name="recover_md" value="yes" DISABLED/></td>
               </tr>
               <tr>
                <td style="padding-left:15px">Files</td>
                <td><input type="checkbox" name="recover_fl" value="yes" DISABLED/></td>
               </tr>
               <td colspan="2"><strong>Recovery Option</strong></td>
            </tr>
            <tr>
             <td style="padding-left:15px" title="Test database recovery reporting any issues.">Recovery Test</td>
             <td><input onclick="change_button('Revovery Test Start','#339900')" title="CHECK to Test database recovery reporting any issues." type="radio" id="name" name="recover_test" value="1" checked/></td>
            </tr>
            <tr>
             <td style="padding-left:15px" title="Live recovery to point of date chosen">Recovery Live</td>
             <td><input onclick="change_button('Do Live Restore Now','#21759B')" title="CHECK to set Live recovery to point of date chosen" type="radio" id="name" name="recover_test" value="0" /></td>
            </tr>
           </table>
         </td>
        </tr>
        <tr>
         <td colspan="2">
          <input class="button-primary" id="test_or_restore" type="submit" name="submit" value="Recovery Test Start"/>
          <br />  </td>
        </tr>
       </table>
       <script type="text/javascript">
        /**
         * Comment
         */
        function change_button(text,color)
        {
         var x=document.getElementById("test_or_restore");
         x.value = text;
         x.style.backgroundColor = color;
        }
       </script>
       </form>
       <!-- Left side end -->
       </td>
       </tr>
       </table>
      </div></div>
     <?PHP echo cs365_version(); ?><br /></div></div>
   <div style="width:24%; float:right; display:inline; margin-right:100px;"><div class="postbox metabox-holder">
     <h3 style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:16px; color:#21759B;">News Updates - Cloudsafe365</h3>
     <div class="inside" style="font-size:13px; font-weight:normal;" id="cs365_news">
     </div>
    </div>
   </div>
  </div>
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
          var x=document.getElementById(cs365_id);
          x.innerHTML = response;
         }
        });
       });
      }
      cs365_site_simpledash('cs365_news','cs365_news');
     </script>
   <?PHP
   }
    ?>