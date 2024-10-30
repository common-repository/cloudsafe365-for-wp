<?php
          add_action('wp_ajax_recover_now2', 'recovery_action2');
          add_action('wp_ajax_recover_now', 'recovery_action');
          ##_______________________________________________________________________________________________________________________
          function recovery_action() {
               global $wpdb; // this is how you get access to the database
               require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/lib/cs365_restore.php');

               if (isset($_POST["table"])) {
                    require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/lib/class.remote_backup.php');
                    $cs365_remote_backup = new cs365_remote_backup();
                    $cs365_restore = new cs365_restore($_POST["whatever"] + 86399, $_POST['table']);
                    $cs365_remote_backup->truncate_cs365_tmp_restore();
                    ##
                    exit;
               }

               $cs365_restore = new cs365_restore($_POST["whatever"] + 86399);
               die(); // this is required to return a proper result
          }

          function recovery_action2() {
               global $wpdb; // this is how you get access to the database
               require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/lib/cs365_config.php');
               $options = get_option('cloudsafe365_plugin_options');
               $c = parse_url(get_option('home'));
               if (!isset($c['path'])) $c['path'] = '';
               ##
               require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/lib/class.remote_backup.php');
               $cs365_remote_backup = new cs365_remote_backup();

               $cs365_remote_backup->change_start = $cs365_remote_backup->check_dabase_count();
               $cs365_remote_backup->grab = 30;

               $url = CS365 . '/transport/return.php?k=' . md5($options['cloudsafe365_api_key']) . '&url=' . md5(str_replace('www.', '', $c['host'] . $c['path']));


               $url .='&start=' . $cs365_remote_backup->change_start;
               $url .='&grab=' . $cs365_remote_backup->grab;
               $url .='&time=' . $_POST["whatever"];
               $return = wp_remote_fopen($url);
               if ($return) {
                    $explode_array = explode('|', $return);
                    if (isset($explode_array[1])) {
                         if ($explode_array[1] == 'ok') {
                              $tmp = base64_decode($explode_array[0]);
                              $json = json_decode($tmp);
                              if (is_array($json)) {
                                   $cs365_remote_backup->insert_cs365_tmp_restore($json);
                                   $perc = round(($cs365_remote_backup->change_start / $explode_array[2]) * 100, 0);
                                   echo $perc . '%';
                                   die();
                              }
                              die();
                         }
                         die();
                    }
               }
               exit(); // this is required to return a proper result
          }

          function recovery_process($time, $cloud = '') {
               ?>
               <div id="cs365_response"></div>

               <?PHP
               ##If restore test local
               if (!preg_match('/\w/xsi', $cloud)) {
                    ?>
                    <script type="text/javascript" >
                         jQuery(document).ready(function($) {
                              var data = {
                                   action: 'recover_now',
                                   recover_test: ' <?PHP echo $_POST['recover_test']; ?>',
                                   whatever: <?PHP echo $time; ?>
                              };
                              // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                              jQuery.post(ajaxurl, data, function(response)
                              {
                                   var x=document.getElementById("cs365_response");
                                   x.innerHTML = response
                              });
                         });
                    </script>
                    <?php
               }
               else {
                    require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/lib/class.remote_backup.php');
                    $cs365_remote_backup = new cs365_remote_backup();
                    $cs365_remote_backup->create_tmp_restore_table();
                    if ($_POST['recover_test'] == 1)  $type_recovery = 'Test ';
                    else $type_recovery = 'Live ';
                    ?>
                    <div class="wrap about-wrap" style="width:100%">
                         <?php settings_fields('cloudsafe365_plugin_options'); ?>
                         <div style="width:90%; float:left; clear:left; display:inline; margin: 0 20px 12px 0">
                              <div class="postbox metabox-holder">
                                   <h3 style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:16px; color:#21759B;" id="cs365_heading"><?PHP echo $type_recovery; ?>Recovery</h3>
                                   <div class="inside" style="font-size:13px; font-weight:inherit;">
                                        <fieldset>
                                             <div id="cs365_response2">Connecting to Cloudsafe365 Backup Recovery system</div>
                                        </fieldset>
                                   </div>
                              </div>
                         </div>
                    </div>
                    <script type="text/javascript" >
                         var i = 1;
                         /**
                          * Comment
                          */
                         function recover()
                         {
                              jQuery(document).ready(function($) {
                                   async: false
                                   var data = {
                                        action: 'recover_now2',
                                        recover_test: ' <?PHP echo $_POST['recover_test']; ?>',
                                        whatever: <?PHP echo $time; ?>
                                   };
                                   // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                                   jQuery.post(ajaxurl, data, function(response)
                                   {
                                        var x=document.getElementById("cs365_response2");
                                        var t=document.getElementById("cs365_heading");
                                        i+=1;
                                        var patt=/\w/;
                                        if (patt.exec(response)) {
                                             recover();
                                             t.innerHTML = '<?PHP echo $type_recovery; ?>Recovery downloading database';
                                             x.innerHTML = 'Downloading Records from cloudsafe365 secure cloud storage <span style="color:green"> ' + response + '</span> Please be patient';
                                             return
                                        }
                                        else
                                        {
                                             //Recovery test of Data start
                                             x.innerHTML = '<?PHP echo $type_recovery; ?> Recovery in progress <i>(Do not close this page)</i>';
                                             t.innerHTML = '<?PHP echo $type_recovery; ?>Database Restore in prorgress';
                                             jQuery(document).ready(function($) {
                                                  var data = {
                                                       action: 'recover_now',
                                                       recover_test: ' <?PHP echo $_POST['recover_test']; ?>',
                                                       whatever: <?PHP echo $time; ?>,
                                                       table:"cs365_tmp_restore"
                                                  };
                                                  // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                                                  jQuery.post(ajaxurl, data, function(response)
                                                  {
                                                       x.innerHTML = response
                                                       t.innerHTML = '<?PHP echo $type_recovery; ?>Recovery process complete';
                                                  });
                                             });
                                             //Recovery test of Data end
                                             return;
                                        }
                                   });
                              });
                         }
                         recover() ;
                    </script>
                    <?php
                    exit;
               }
          }
?>