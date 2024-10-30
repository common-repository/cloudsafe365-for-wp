<?php
          function cloudsafe365_admin_log_pres() {
               $options = get_option('cloudsafe365_plugin_options');
               $request = "
select trigger_time
from
cs365_change
group by trigger_time
order by trigger_time desc
limit 30
";
               $mysql = mysql_query($request) or print mysql_error();
               $num_mysql = mysql_num_rows($mysql) or print mysql_error();
               if ($num_mysql > 0) {

                    while ($row = mysql_fetch_assoc($mysql)) {

                         $request = "select set_type,count(set_type) as counter
from
cs365_change
where
trigger_time = " . $row['trigger_time'] . "
group by set_type
";
                         $mysql_a = mysql_query($request) or print mysql_error();
                         $num_mysql = mysql_num_rows($mysql_a) or print mysql_error();
                         if ($num_mysql > 0) {
                              while ($row_a = mysql_fetch_assoc($mysql_a)) {

                                   $request = "
select table_name,count(table_id) as counter
from
cs365_change,cs365_tables
where
trigger_time = " . $row['trigger_time'] . "
AND
set_type ='" . $row_a['set_type'] . "'
AND
cs365_tables.id = cs365_change.table_id
group by table_id
";

                                   $mysql_b = mysql_query($request) or print mysql_error();
                                   $num_mysql = mysql_num_rows($mysql_b) or print mysql_error();
                                   if ($num_mysql > 0) {

                                        while ($row_b = mysql_fetch_assoc($mysql_b)) {
                                             $row_b['set_type'] = $row_a['set_type'];
                                             $array[$row['trigger_time']][] = $row_b;
                                        }
                                   }
                              }
                         }
                    }
                    ?>
                    <style>
                         td{white-space:nowrap;}
                    </style>

                    <div class="wrap about-wrap" style="width:500px">
                         <div style="width:500px; float:left; clear:left; display:inline; margin: 0 20px 12px 0">
                              <div class="postbox metabox-holder">
                                   <h3 style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:16px; color:#21759B;">Last 30 Backup points!</h3>
                                   <div class="inside" style="font-size:13px; font-weight:inherit;">
                                        <fieldset>
                                             <p class="form">
                                             <table  width="400px" border="0" cellpadding="5" cellspacing="1" align="left">
                                                  <tr>
                                                       <td style="font-weight: bold;white-space:nowrap">date time</td>
                                                       <td><table width="400px" border="0" cellpadding="0" cellspacing="0" align="left">
                                                                 <tr>
                                                                      <td width="200px" style="font-weight: bold">Table</td>
                                                                      <td  width="100px" style="font-weight: bold">Type</td>
                                                                      <td  width="100px" style="font-weight: bold">Qty</td>
                                                                 </tr>
                                                            </table>
                                                       </td>
                                                  </tr>
                                                  <?PHP
                                                  foreach ($array as $time => $datar) {
                                                       ?>
                                                       <tr>
                                                            <td><?PHP echo date('d-M-Y g:i:s', $time); ?></td>
                                                            <td>
                                                                 <table width="400px" border="0" cellpadding="0" cellspacing="0" align="left">
                                                                      <?PHP
                                                                      for ($i = 0; $i < count($datar); $i++) {
                                                                           switch ($datar[$i]['set_type'])
                                                                                {
                                                                                case ('d') :
                                                                                     $type = 'delete';
                                                                                     break;
                                                                                case ('i') :
                                                                                     $type = 'insert';
                                                                                     break;
                                                                                case ('u') :
                                                                                     $type = 'update';
                                                                                     break;
                                                                                }
                                                                           echo '<tr>
                                                                  <td width="200px" style="font-weight: normal">' . $datar[$i]['table_name'] . '</td>
                                                                  <td  width="100px" style="font-weight: normal">' . $type . '</td>
                                                                  <td  width="100px" style="font-weight: normal">' . $datar[$i]['counter'] . '</td>
                                                              </tr>';
                                                                      }
                                                                      ?>
                                                                 </table></td>
                                                       </tr>
                                                       <?PHP
                                                  }
                                                  ?>
                                             </table>
                                             </p>
                                        </fieldset>
                                   </div>
                              </div>
                         </div>
                    </div>
                    <?PHP
               }
          }
?>