<?php
 /*
  * To change this template, choose Tools | Templates
  * and open the template in the editor.
  */

 /**
  * Description of cs365_restore
  *
  * @author cloudsafe365.com
  */
 class cs365_restore
  {
  //put your code here
  function cs365_restore($time, $table = 'cs365_change') {
   require_once("cs365_common.php");
   $this->common = new cs365_common();
   if ($this->common->wpoptions['cloudsafe365_api_key'] == 'none')
     return;
   if (strlen($this->common->wpoptions['cloudsafe365_api_key']) != 33) {
    cloudsafe365_key_investigate();
    return;
   }
   $this->recover_time = date('D d M Y g:i:s', $time);
   $this->totals['inserts'] = 0;
   $this->totals['updates'] = 0;
   $this->totals['deletes'] = 0;
   $this->totals['tales reset'] = 0;
   $this->totals['tables altered'] = 0;
   $this->totals['errors'] = 0;
   $this->totals['No_data'] = 0;
   $this->tables = $this->setup_cs365_tables($this->mysql_statement("show table status"));
   $this->get_restore_point($time, $table);
   $this->return_print();
  }

//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com//Monday, January 2, 2012 at 11:25:25
//________________________________________________________________________________________________________________________________
  private function mysql_statement($request) {
   $mysql = mysql_query($request) or $this->Error_cs_back(mysql_error(), __function__);
   $num_mysql = mysql_num_rows($mysql);
   $tmp = array();
   if ($num_mysql > 0) {
    while ($row = mysql_fetch_assoc($mysql)) {
     if (preg_match('/' . $this->common->no_refer_table . '/xsi', $row['Name'])) {
      continue;
     }
     $tmp[$row['Name']] = $this->tmp_setup($row);
    }
   }
   mysql_free_result($mysql);
   return $tmp;
  }

//________________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Monday, January 9, 2012 at 00:49:35
//________________________________________________________________________________________________________________________________
  private function tmp_setup($row) {
   $row['time'] = time();
   return $row;
  }

//_______________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Thursday, January 5, 2012 at 16:53:17
//________________________________________________________________________________________________________________________________
  private function setup_cs365_tables($info) {
   $request = "
SELECT
	*
FROM
	cs365_tables
";
   $mysql = mysql_query($request) or print mysql_error();
   $num_mysql = mysql_num_rows($mysql) or print mysql_error();
   $array = array();
   if ($num_mysql > 0) {
    while ($row = mysql_fetch_assoc($mysql)) {
     $row['fields'] = explode(',', $row['expression']);
     $row['info'] = $info[$row['table_name']];
     $array[$row['id']] = $row;
     $array['names'][] = $row['table_name'];
    }
    return $array;
   }
  }

//________________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Thursday, January 5, 2012 at 16:46:56
//________________________________________________________________________________________________________________________________
//select id_change,id,table_id,trigger_time,trigger_day,AES_DECRYPT(expression,'".$this->common->wpoptions['cloudsafe365_api_key']."') as expression,hashmd5,set_type
  private function get_restore_point($time, $table) {
   $array = array();
   if ($this->common->encryption == 1) {
    $request = "SELECT id_change,id,table_id,trigger_time,trigger_day,AES_DECRYPT(expression,'" . $this->common->wpoptions['cloudsafe365_api_key'] . "') as result,hashmd5,set_type FROM " . $table . " WHERE trigger_time <= '$time'";
   }
   else {
    $request = "SELECT * FROM " . $table . " WHERE trigger_time <= '$time'";
   }
   $mysql = mysql_query($request) or print mysql_error();
   $num_mysql = mysql_num_rows($mysql);
   if ($num_mysql > 0) {
    $this->setup_truncate($time, $table);
    $this->setup_auto_increment();
    while ($row = mysql_fetch_assoc($mysql)) {
     if ($this->common->encryption == 1) {
      $row['expression'] = $row['result'];
      unset($row['result']);
     }

     switch ($row['set_type'])
      {
      case ('i') :
       $this->create_insert($row);
       $this->totals['inserts']++;
       break;
      case ('u') :
       $this->create_update($row);
       $this->totals['updates']++;
       break;
      case ('d') :
       $this->create_delete($row);
       $this->totals['deletes']++;
       break;
      }
    }
    mysql_free_result($mysql);
   }
  }

//________________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Friday, January 6, 2012 at 12:25:45
//________________________________________________________________________________________________________________________________
  private function setup_truncate($time, $table) {
   $this->show_table_status = $this->show_table_status();
   for ($i = 0; $i < count($this->show_table_status); $i++) {
    if (preg_match('/cs365_tables|' . $table . '/xsi', $this->show_table_status[$i]['Name'])) {
     continue;
    }
    if (in_array($this->show_table_status[$i]['Name'], $this->tables['names'])) {
     $this->restore_execute('truncate table ' . $this->show_table_status[$i]['Name'], __function__ . ' table = ' . $this->tables['names'], $this->show_table_status[$i]['Name'], 'truncate');
     $this->totals['tales reset']++;
    }
   }
  }

//________________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Sunday, January 8, 2012 at 22:55:19
//________________________________________________________________________________________________________________________________
  private function setup_auto_increment() {
   for ($i = 0; $i < count($this->tables); $i++) {
    if (isset($this->tables[$i])) {
     if (isset($this->tables[$i]['info']['Auto_increment'])) {
      $this->restore_execute('ALTER TABLE ' . $this->tables[$i]['table_name'] . '  AUTO_INCREMENT = ' . $this->tables[$i]['info']['Auto_increment'], __function__, $this->tables[$i]['table_name'], 'Alter');
      $this->totals['tables altered']++;
     }
    }
   }
  }

//________________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Friday, January 6, 2012 at 12:28:39
//________________________________________________________________________________________________________________________________
  private function show_table_status() {
   $array = array();
   $mysql = mysql_query('show table status') or print mysql_error();
   $num_mysql = mysql_num_rows($mysql) or print mysql_error();
   if ($num_mysql > 0) {
    while ($row = mysql_fetch_assoc($mysql)) {
     $array[] = $row;
    }
   }
   return $array;
  }

//________________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Thursday, January 5, 2012 at 17:19:09
//________________________________________________________________________________________________________________________________
  private function create_insert($row) {
   $row['expression'] = explode('\',\'', preg_replace('/^\'|\'$/xsi', '', $this->replace_returns($row['expression'])));
   for ($i = 0; $i < count($row['expression']); $i++) {
    $row['expression'][$i] = '\'' . addcslashes($row['expression'][$i], '\'') . '\'';
   }
   $str = 'INSERT INTO ' . $this->tables[$row['table_id']]['table_name'] .
    '(' .
    '`' . preg_replace('/,/xsi', '`,`', $this->tables[$row['table_id']]['expression']) . '`' .
    ')' .
    'VALUES' .
    '(' .
    $this->replace_returns(implode(',', $row['expression'])) .
    ')'
   ;
   $this->restore_execute($str, __function__ . ' id = ' . $row['id_change'], $this->tables[$row['table_id']]['table_name'], 'inserts');
  }

//________________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Monday, January 9, 2012 at 00:51:37
//________________________________________________________________________________________________________________________________
  private function create_update($row) {
   $row['expression'] = explode('\',\'', preg_replace('/^\'|\'$/xsi', '', $this->replace_returns($row['expression'])));
   $key_value = $row['expression'][0];
   for ($i = 0; $i < count($row['expression']); $i++) {
    $row['expression'][$i] = $this->tables[$row['table_id']]['fields'][$i] . ' =\'' . addcslashes($row['expression'][$i], '\'') . '\'';
   }

   if (preg_match('/\w/xsi', $key_value)) {
    $this->restore_execute('UPDATE ' . $this->tables[$row['table_id']]['table_name'] . ' SET ' .
     $comma_separated = implode(",", $row['expression']) .
     ' WHERE ' .
     $this->tables[$row['table_id']]['fields'][0] .
     ' = ' .
     $key_value, __function__ . ' id = ' . $row['id_change'], $this->tables[$row['table_id']]['table_name'], 'updates');
   }
   else {
    $this->totals['No_data']++;
    $this->action_counter($this->tables[$row['table_id']]['table_name'], 'No_data');
   }
  }

//________________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Monday, January 9, 2012 at 00:51:04
//________________________________________________________________________________________________________________________________
  private function create_delete($row) {
   $this->restore_execute('DELETE FROM ' . $this->tables[$row['table_id']]['table_name'] . ' WHERE ' . $this->tables[$row['table_id']]['fields'][0] .
    ' = \'' . $row['id'] . '\'', __function__ . ' id = ' . $row['id_change'], $this->tables[$row['table_id']]['table_name'], 'deletes');
  }

//________________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Friday, January 6, 2012 at 13:15:18
//________________________________________________________________________________________________________________________________
  private function restore_execute($sql, $func, $table = '', $type = '') {
   if ($_POST['recover_test'] == 0) {
    mysql_query($sql) or $this->common->Error_cs_back(mysql_error(), $func, $sql);
   }
   else {
    //Will be adding in tests of the sql queries to make sure that they are all ok and match.
   }

   $this->action_counter($table, $type);
  }

//________________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Monday, January 9, 2012 at 14:33:24
//________________________________________________________________________________________________________________________________
  private function action_counter($table, $type) {
   if (!isset($this->totals['info'][$table][$type])) {
    $this->totals['info'][$table][$type] = 1;
    return;
   }
   $this->totals['info'][$table][$type]++;
  }

//________________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Monday, January 9, 2012 at 14:13:28
//________________________________________________________________________________________________________________________________
  private function return_print() {

   if (!isset($this->totals['info'])) {
    ?>
    The cloudsafe365 sever will need to backup your system and place in secure cloud storage please give it a few moments and try again in around 20 mins or so, you can restore from local backup.
    <?PHP
    return;
   }
   elseif (!is_array($this->totals['info'])) {
    ?>
    The cloudsafe365 sever will need to backup your system and place in secure cloud storage please give it a few moments and try again in around 20 mins or so, you can restore from local backup.
    <?PHP
    return;
   }

   if ($_POST['recover_test'] == 0) {
    if ($this->totals['No_data'] == 0) {
     $this->recover_heading = 'Recovery Successfull';
     $this->color = 'green';
    }
    else {
     $this->recover_heading = "Recovery made with some errors";
     $this->color = 'red';
     $this->recover_sub = 'If you only have a few errors then it should be safe to recover Cautions is Advised';
    }
    $this->recovery_system = 'Table Recovery information set back to';
   }
   else {
    if ($this->totals['No_data'] == 0) {
     $this->recover_heading = 'Recovery Test Successfull';
     $this->recover_sub = 'You are safe to do a live recovery';
     $this->recover_sub .= ' (click the restore tab to start the restore process)';
     $this->color = 'green';
    }
    else {
     $this->recover_heading = "Recovery Test made with some errors";
     $this->color = 'red';
     $this->recover_sub = 'If you only have a few errors then it should be safe to recover Cautions is Advised';
    }
    $this->recovery_system = 'Test Table Recovery information test to';
   }
   ?>
   <table width="100%" border="0" cellpadding="0" cellspacing="20" align="left">
    <tr>

     <td>
      <h2 style="font-weight: bold;color:<?PHP echo $this->color; ?>"><?PHP echo $this->recover_heading; ?>! </h2>
      <?PHP echo $this->recover_sub; ?>
     </td>
    </tr>
    <tr>
    <tr>
     <td>
      <h3><?PHP echo $this->recovery_system; ?> <?PHP echo $this->recover_time; ?></h3>
     </td>
    </tr>
    <tr>
     <td>
      <table width="100%" border="0" cellpadding="0" cellspacing="0" align="left">
       <tr style="font-weight: bold">
        <td  align="center">Inserts</td>
        <td align="center">Updates</td>
        <td align="center">Deletions</td>
        <td align="center">Tables reset</td>
        <td align="center">Tables altered</td>
        <td align="center">Data Issue</td>
        <td align="center">Errors</td>
       </tr>
       <tr>
        <td align="center"><?PHP echo $this->totals['inserts']; ?></td>
        <td align="center"><?PHP echo $this->totals['updates']; ?></td>
        <td align="center"><?PHP echo $this->totals['deletes']; ?></td>
        <td align="center"><?PHP echo $this->totals['tales reset']; ?></td>
        <td align="center"><?PHP echo $this->totals['tables altered']; ?></td>
        <td align="center"><?PHP echo $this->totals['No_data']; ?></td>
        <td align="center"><?PHP echo $this->totals['errors']; ?></td>
       </tr>
      </table>
     </td>
    </tr>
    <tr>
     <td>  <hr /></td>
    </tr>
    <tr>
     <td>

      <table width="100%" border="0" cellpadding="0" cellspacing="0" align="left">
       <tr style="font-weight: bold">
        <td>Table</td>
        <td align="center">Reset</td>
        <td align="center">Altered</td>
        <td align="center">Inserts</td>
        <td align="center">Updates</td>
        <td align="center">Data Issue</td>
        <td align="center">Deletes</td>
       </tr>
       <?PHP
       foreach ($this->totals['info'] as $name_item => $value) {
        if (!isset($this->totals['info'][$name_item]['truncate']))
          $this->totals['info'][$name_item]['truncate'] = 0;
        if (!isset($this->totals['info'][$name_item]['Alter']))
          $this->totals['info'][$name_item]['Alter'] = 0;
        if (!isset($this->totals['info'][$name_item]['inserts']))
          $this->totals['info'][$name_item]['inserts'] = 0;
        if (!isset($this->totals['info'][$name_item]['updates']))
          $this->totals['info'][$name_item]['updates'] = 0;
        if (!isset($this->totals['info'][$name_item]['deletes']))
          $this->totals['info'][$name_item]['deletes'] = 0;
        if (!isset($this->totals['info'][$name_item]['No_data']))
          $this->totals['info'][$name_item]['No_data'] = 0;
        echo ' <tr><td>' . $name_item . '</td>';
        echo '<td align="center">' . $this->totals['info'][$name_item]['truncate'] . '</td>';
        echo '<td align="center">' . $this->totals['info'][$name_item]['Alter'] . '</td>';
        echo '<td align="center">' . $this->totals['info'][$name_item]['inserts'] . '</td>';
        echo '<td align="center">' . $this->totals['info'][$name_item]['updates'] . '</td>';
        echo '<td align="center">' . $this->totals['info'][$name_item]['No_data'] . '</td>';
        echo '<td align="center">' . $this->totals['info'][$name_item]['deletes'] . '</td></tr>';
       }
       ?>
      </table>
     </td>
    </tr>
    <tr>
     <td><?PHP echo $this->cs365_version(); ?></td>
    </tr>
   </table>

   <?PHP
  }

//________________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Sunday, January 8, 2012 at 12:21:47
//________________________________________________________________________________________________________________________________
  function replace_returns($txt) {
   return preg_replace('/\r\n|\n/xsi', '\\r\\n', $txt);
  }

//________________________________________________________________________________________________________________________________

  function cs365_version() {
   ?>
   <span style="font-size:13px;color:#21759B" >
    cloud
    <span style="color:black">
     safe
    </span>
    365
    <span style="color:black">
     <?PHP echo CS365_CURRENT; ?>
    </span>
   </span>
   <?PHP
  }

  }
?>