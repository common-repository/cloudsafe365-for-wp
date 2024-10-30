<?php
require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/screens/cs365_displays.php');

class cs365_deep_display
  {
  function cs365_display() {
    global $wpdb;
    if ($wpdb->query('show tables like "cs365_malware_files"') <= 0) {
      ?>
      <span style="color:red;font-style: italic;font-size:12px">Initial first scan may take a few moments</span>
      <?PHP
    }
    ?>

    <div style="width:100%;">
      <!-- This goes in the HEAD of the html file -->
      <script language="JavaScript" type="text/javascript">
        <!--
        var sec = 0;
        var min = 0;
        var hour = 0;
        function stopwatch(text) {
          sec++;
          if (sec == 60) {
            sec = 0;
            min = min + 1; }
          else {
            min = min; }
          if (min == 60) {
            min = 0;
            hour += 1; }

          if (sec<=9) { sec = "0" + sec; }
          document.clock.stwa.value = ((hour<=9) ? "0"+hour : hour) + " : " + ((min<=9) ? "0" + min : min) + " : " + sec;

          if (text == "Start") { document.clock.theButton.value = "Stop "; }
          if (text == "Stop ") { document.clock.theButton.value = "Start"; }

          if (document.clock.theButton.value == "Start") {
            window.clearTimeout(SD);
            return true; }
          SD=window.setTimeout("stopwatch();", 1000);
        }

        function resetIt() {
          sec = -1;
          min = 0;
          hour = 0;
          if (document.clock.theButton.value == "Stop ") {
            document.clock.theButton.value = "Start"; }
          window.clearTimeout(SD);
        }
        // -->
      </script>
      <table bgcolor="#ffffff" align="left" border="0" width="130" cellspacing="0">
        <tr>
          <td align="center">
            <form name="clock">
              <input type="text" size="12" name="stwa" value="00 : 00 : 00" style="text-align:center;border:none;color:#21759B" /><br />
              <input type="hidden" name="theButton" onClick="stopwatch(this.value);" value="Start" />
              <input type="hidden" value="Reset" onClick="resetIt();reset();" />
            </form>
          </td>
        </tr>
      </table>
      <script language="JavaScript" type="text/javascript">
        stopwatch('Start')
      </script>
      <div id="cs365_spinning" style="float:left;width:70%;text-align: left">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="left">
          <tr>
            <td><img width="25px" height="25px" src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/wait.gif" alt="" /></td>
            <td valign="5middle">Deep Server internal Scanning in Progress   <progress id="cs365bar" value="" max="100"></progress>&nbsp; <span style="color:#21759B;font-weight: bold" id="cs365_sofar">-</span>&nbsp;</td>
          </tr>
        </table>
      </div>
      <?PHP
      /*

        <div id="update_defs" style="float:left;width:30%;text-align: right">
        <input style="color:white;background-color:#21759B"type="button" value="Update Malware Definitions" onclick="" />
        </div>
       * /
       */
      ?>
    </div>

    <div class="postbox metabox-holder" style="clear:both;">
      <h3 style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:16px; color:#21759B;"><img  id="deep_image" src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/clear.png" width="25px" height="25px" alt="" />Deep Inside Server Scan - Malware Results </h3>
      <div class="inside" style="font-size:13px; font-weight:inherit;">
        <fieldset>
          <p class="form" id="cs365_scan_site">
          <table width="250px" border="0" cellpadding="0" cellspacing="5" align="left">

            <?PHP
            //left out : Core_Wordpress|Themes
            foreach (array('Site_External_Browser_Scan', 'Database', 'Files', 'Plugins', 'Themes') as $name) {

              if ($name == 'Files')
                  $display = 'File changes Plugins';
              else
                  $display = $name;
              $display = preg_replace('/_/xsi', ' ', $display);
              ?>
              <tr>
                <td><img id="tics365<?PHP echo $name; ?>" src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/clear.png" width="12px" height="12px" alt="" /></td>
                <td><?PHP echo $display; ?></td>
                <td id="fcs365<?PHP echo $name; ?>">0</td>
              </tr>
              <tr>
                <?PHP
              }
              ?>
          </table>
          </p>
        </fieldset>
      </div>
    </div>
    <?PHP
    $heading = 'Site_External_Browser_Scan';
    $this->cs365_create_dataheaders('cs365' . $heading, $heading, 'clear', '');
    $this->cs365_dns();
    $this->cs365_create_displays();
    $calls = 25;
    ?>
    <script type="text/javascript">
      function cs365_scan_database()
      {
        cs365_replace('cs365_sofar','Database Scanning','');
        jQuery(document).ready(function($) {
          async: false
          var data = {
            action: 'cs365_scan_database'
          };
          jQuery.post(ajaxurl, data, function(response)
          {
            var patt=/\w/;
            if (patt.exec(response))
            {
              var patt=/cs365_message/;
              if (patt.exec(response))
              {
                var x=document.getElementById('ecs365Files');
                x.innerHTML = '';
                var x=document.getElementById('cs365_spinning');
                x.innerHTML = '<span style="color:#21759B;font-weight:bold">'+response+'</span>';
                stopwatch('Stop ')
                alert(response)
                return;
              }
              var JSONObject = eval("(" + response + ")");

              if (JSONObject.count != 0)
              {
                cs365_replace('ecs365Database','Spam/Malware number found : <span style="color:red">' + JSONObject.count + '</span>','');
                cs365_replace('ics365Database','cross','img');
                cs365_replace('fcs365Database','<span style="color:red">' + JSONObject.count + '</span>','');
                cs365_replace('tics365Database','cross','img');
                cs365_replace('deep_image','cross','img');
              }
              else
              {
                cs365_replace('ics365Database','tick','img');
                cs365_replace('tics365Database','tick','img');
                cs365_replace('deep_image','tick','img');
              }
              cs365_replace('tcs365Database',JSONObject.comment,'');
              cs365_replace('cs365_sofar',' scanned  files','');

              cs365_site_deepscan('cs365_deep_scan',0,<?PHP echo $calls; ?>);
            }
          });
        });


      }

      function cs365_site_surface_scan(cs365_action,cs365_id)
      {
        jQuery(document).ready(function($) {
          async: false
          var data = {
            action: cs365_action
          };
          jQuery.post(ajaxurl, data, function(response)
          {
            var patt=/\w/;
            if (patt.exec(response))
            {
              var JSONObject = eval("(" + response + ")");
              cs365_replace(cs365_id,JSONObject.body,'');
              if (JSONObject.count > 0) {
                cs365_replace('fcs365Site_External_Browser_Scan','<span style="color:red">'+JSONObject.count+'</span>','');
              }
              cs365_replace('tics365Site_External_Browser_Scan',JSONObject.img,'img');
              cs365_replace('ics365Site_External_Browser_Scan',JSONObject.img,'img');
            }
          });
        });
      }

      function cs365_site_deepscan(cs365_action,cs365_start,cs365_finish)
      {
        jQuery(document).ready(function($) {
          async: false
          var data = {
            action: cs365_action,
            start: cs365_start,
            finish: cs365_finish
          };
          jQuery.post(ajaxurl, data, function(response)
          {
            var patt=/\w/;
            if (patt.exec(response))
            {
              cs365_start = cs365_finish;
              var randomnumber=Math.floor( 50+(Math.random()*(100-50)))
              cs365_finish += randomnumber;
              cs365_grouped_data()
              cs365_update_files(cs365_start,cs365_finish);
            }
            else
            {
              cs365_grouped_data()
              cs365_update_files(0,0);
              var x=document.getElementById('ecs365Files');
              x.innerHTML = '';
              var x=document.getElementById('cs365_spinning');
              x.innerHTML = '<span style="color:#21759B;font-weight:bold">Scan Completed</span>';
              stopwatch('Stop ')
            }
          });
        });
      }

      function cs365_grouped_data() {
        var Database = '0';
        var plugins = '0';
        var Themes = '0';
        //var Core_Wordpress = '0';
        jQuery(document).ready(function($) {
          async: false
          var data = {
            action: 'cs365_grouped_data'
          };
          jQuery.post(ajaxurl, data, function(response)
          {
            var patt=/\w/;
            if (patt.exec(response))
            {
              var JSONObject = eval("(" + response + ")");
              cs365_replace('tcs365Plugins',JSONObject.Plugins.content,'');
              cs365_replace('tcs365Themes',JSONObject.Themes.content,'');
              //cs365_replace('tcs365Core_Wordpress',JSONObject.Core_Wordpress.content,'');
              if (JSONObject.Plugins.group_counter > 0) {
                cs365_replace('fcs365Plugins','<span style="color:red">'+JSONObject.Plugins.group_counter+'</span>','');
              }
              //if (JSONObject.Core_Wordpress.group_counter > 0) {
              //cs365_replace('fcs365Core_Wordpress','<span style="color:red">'+JSONObject.Core_Wordpress.group_counter+'</span>','');
              //}
              if (JSONObject.Themes.group_counter > 0) {
                cs365_replace('fcs365Themes','<span style="color:red">'+JSONObject.Themes.group_counter+'</span>','');
              }
              plugins = cs365_cross_tick(JSONObject.Plugins.img,plugins,'ics365Plugins','ecs365Plugins','tics365Plugins','vdcs365Plugins')
              Themes = cs365_cross_tick(JSONObject.Themes.img,Themes,'ics365Themes','ecs365Themes','tics365Themes','vdcs365Themes')
              //Core_Wordpress = cs365_cross_tick(JSONObject.Core_Wordpress.img,Core_Wordpress,'ics365Core_Wordpress','ecs365Core_Wordpress','tics365Core_Wordpress')



            }
          });
        });
      }

      function cs365_cross_tick(img,item,image_id,txt_id,txt_id2,hideshow)
      {
        if (item == 1)
        {
          cs365_replace(txt_id,'Potential Issues Found','');
          cs365_replace(image_id,'cross','img');
          cs365_replace(txt_id2,'cross','img');
                    document.getElementById(hideshow).style.display = 'block';
          return 1
        }
        if (img == 'cross')
        {
          cs365_replace(txt_id,'Potential Issues Found','');
          cs365_replace(image_id,'cross','img');
          cs365_replace(txt_id2,'cross','img');
          document.getElementById(hideshow).style.display = 'block';
          return 1
        }

        cs365_replace(txt_id,'','');
        cs365_replace(image_id,'tick','img');
        cs365_replace(txt_id2,'tick','img');
        return 0;
      }

      function cs365_update_files(cs365_start,cs365_finish)  {
        jQuery(document).ready(function($) {
          async: false
          var data = {
            action: 'deep_update_files'
          };
          jQuery.post(ajaxurl, data, function(response)
          {
            var patt=/\w/;
            if (patt.exec(response))
            {
              var JSONObject = eval("(" + response + ")");

              if (document.getElementById('cs365_sofar'))
              {
                x = document.getElementById('cs365bar')
                x.value = JSONObject.c ;
              }

              var x=document.getElementById('tcs365Files');
              x.innerHTML = JSONObject.content;

              var x=document.getElementById('ecs365Files');
              x.innerHTML = JSONObject.txt;


              if (JSONObject.modified_files > 0)
                cs365_replace('fcs365Files','<span style="color:red">'+JSONObject.modified_files + '</span>','');

              var x=document.getElementById('ics365Files');
              x.src =  '<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/'+JSONObject.img+'.png';

              var x=document.getElementById('tics365Files');
              x.src =  '<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/'+JSONObject.img+'.png';

              if (cs365_finish != 0) {
                cs365_site_deepscan('cs365_deep_scan',cs365_start,cs365_finish)
              }
            }
          });
        });
      }

      function set_cs365_file_list(extension)
      {
        var x=document.getElementById('cs365_file_list');
        x.innerHTML = '<img src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/wait.gif" width=25px" height="25px" alt="" />Getting list..';

        jQuery(document).ready(function($) {
          async: false
          var data = {
            action: 'set_cs365_file_list',
            extension:extension
          };
          jQuery.post(ajaxurl, data, function(response)
          {
            var patt=/\w/;
            if (patt.exec(response))
            {
              cs365_replace('cs365_file_list',response,'');
            }
          });
        });
      }

      function cs365_replace(id,value,type) {
        var x=document.getElementById(id);
        if (type=='img') {
          x.src =  '<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/'+value+'.png';
        }
        else
          x.innerHTML = value;
      }

      //function to get random number from 1 to n
      function randomToN(maxVal,floatVal)
      {
        var randVal = Math.random()*maxVal;
        return typeof floatVal=='undefined'?Math.round(randVal):randVal.toFixed(floatVal);
      }


      function cs365_update_definitions()
      {

      }

      cs365_scan_database();
      cs365_site_surface_scan('cs365_scan_site','tcs365Site_External_Browser_Scan');
    </script>
    <?PHP
  }

  function cs365_dns() {
    //dns
    $this->options = get_option('cloudsafe365_plugin_options');
    $c = parse_url(get_option('home'));

    if (function_exists(apache_response_headers))
        $apache_response_headers = apache_response_headers();

    if (function_exists(apache_request_headers))
        $apache_request_headers = apache_request_headers();

    $checkdnsrr = checkdnsrr($c['host'] . '.');

    if ($checkdnsrr == 1) {
      $checkdnsrr = 'DNS Records found';

      $result = dns_get_record($c['host'], DNS_ANY, $authns, $addtl);
      $ip = gethostbyname($c['host']);
      $gethostbyaddr = gethostbyaddr($ip);

      $dns_md5 = md5($ip . $gethostbyaddr . $c['host']);
      if (!isset($this->options['cs365_check_dns'])) {
        $this->options['cs365_check_dns'] = $dns_md5;
        $dns = '0';
      }
      else {

        if ($this->options['cs365_check_dns'] != $dns_md5) {
          $dns = '1';
        }
      }
      update_option('cloudsafe365_plugin_options', $this->options, '', 'yes');


      $content = '<table border="0" cellpadding="0" cellspacing="5" align="left">';

      $content .= '<tr>' .
      '<td>Info</td>' .
      '<td>' . $checkdnsrr . '</td>' .
      '</tr>';

      $content .= '<tr>' .
      '<td>Host</td>' .
      '<td>' . $c['host'] . '</td>' .
      '</tr>';

      $content .= '<tr>' .
      '<td>Server IP Address</td>' .
      '<td>' . $ip . '</td>' .
      '</tr>';

      $content .= '<tr>' .
      '<td>Host By IP</td>' .
      '<td>' . $gethostbyaddr . '</td>' .
      '</tr>';


      $second_ip = gethostbyname($c['host']);


      if ($second_ip == $ip) {
        $content .= '<tr>' .
        '<td>Host IP Match</td>' .
        '<td style="color:green">Yes</td>' .
        '</tr>';
      }
      else {
        $dns = 1;
        $content .= '<tr>' .
        '<td>Host IP Mach</td>' .
        '<td style="color:red">No  ' . $second_ip . ' = ' . $ip . '</td>' .
        '</tr>';
      }
    }
    else {
      $content = 'DNS ERROR could not find records';
      $no_dns = 1;
    }
    ?>

    <?PHP
    if (!isset($no_dns)) {
      if (isset($result))
          for ($i = 0; $i < count($result); $i++) {

          if (isset($result[$i]['ip']))
              $server_d = $result[$i]['ip'];
          if (isset($result[$i]['mname']))
              $server_d = $result[$i]['mname'];
          if (isset($result[$i]['target']))
              $server_d = $result[$i]['target'];

          if (isset($server_d)) {
            $content .= '<tr>' .
            '<td>' . $result[$i]['type'] . '</td>' .
            '<td>' . $server_d . '</td>' .
            '</tr>';
          }
        }


      if (isset($apache_response_headers))
          foreach ($apache_request_headers as $header => $value) {

          $content .= '<tr>' .
          '<td>' . $header . '</td>' .
          '<td>' . $value . '</td>' .
          '</tr>';
        }

      if (isset($apache_response_headers))
          foreach ($apache_response_headers as $header => $value) {

          $content .= '<tr>' .
          '<td>' . $header . '</td>' .
          '<td>' . $value . '</td>' .
          '</tr>';
        }

      if ($dns == 0)
          $content .= '<tr>' .
        '<td colspan="2" style="color:green">Dns information is unchanged</td>' .
        '</tr>';
      else
          $content .= '<tr>' .
        '<td colspan="2"  style="color:red">Dns information has changed</td>' .
        '</tr>';

      $content .= ' </table>';

      $counter = 'a' . substr(md5(uniqid(mt_rand(), true)), -5);
      $content = '
      <div><a id="d' . $counter . '" href="#" Onclick="cs365_reveal(\'t' . $counter . '\',\'d' . $counter . '\');return false" >View Details</a></div>
       <div id="t' . $counter . '" style="display:none">
        ' . $content . '
       </div>';
    }


    $heading = 'DNS information';
    if ($dns == 1)
        $img = 'cross';
    else
        $img = 'tick';
    cs365print_out($heading, $content, $img, '');
    //end DNS
  }

  function cs365_create_displays() {

    //left outCore_Wordpres, Themes
    foreach (array('Database', 'Files', 'Plugins', 'Themes') as $heading) {
      $this->cs365_create_dataheaders('cs365' . $heading, $heading, 'clear', '');
    }
  }

  function cs365_create_dataheaders($type, $heading, $img, $txt) {
    if (($heading == 'Files') or ($heading == 'Site_External_Browser_Scan'))
	{
          $content = '
      <div><a  id="vd' . $type . '" href="#" Onclick="cs365_reveal(\'t' . $type . '\',\'vd' . $type . '\');return false" >View Details</a></div>
       <div id="t' . $type . '" style="display:none">
       </div>';
    }
    else
    $content = '
      <div><a style="display:none" id="vd' . $type . '" href="#" Onclick="cs365_reveal(\'t' . $type . '\',\'vd' . $type . '\');return false" >View Details</a></div>
       <div id="t' . $type . '" style="display:none">
       </div>';
    cs365print_out($heading, $content, $img, $txt);
  }

  }
?>