<?php
 /*
  * Description of cloudsafe365
  *
  * @author cloudsafe365
  */

 class cs365_fast_back
  {
  public static function instance() {
   static $self = false;
   if (!$self) {
    $self = new cs365_fast_back();
   }
   return $self;
  }

  //put your code here
  function cs365_fast_back() {
   require_once("cs365_common.php");
   $this->common = new cs365_common();
   if ($this->common->wpoptions['cloudsafe365_api_key'] == 'none')
     return;
   $this->mysql_statement("show table status");
   $this->common->update_stats();
  }

//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com//Monday, January 2, 2012 at 11:25:25
//________________________________________________________________________________________________________________________________
  private function mysql_statement($request) {
   global $table_prefix;

   $mysql = mysql_query($request) or $this->common->Error_cs_back(mysql_error(), __function__);
   $num_mysql = mysql_num_rows($mysql);
   $array = array();
   if ($num_mysql > 0) {
    while ($row = mysql_fetch_assoc($mysql)) {
     if (preg_match('/' . $this->common->no_refer_table . '/xsi', $row['Name'])) {
      continue;
     }
     if ($row['Name'] == $table_prefix . 'posts' || $row['Name'] == $table_prefix . 'comments') {
      $tmp = $this->tmp_setup($row);
      unset($tmp);
     }
    }
   }
   mysql_free_result($mysql);
  }

//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Monday, January 2, 2012 at 13:06:31
//________________________________________________________________________________________________________________________________
  private function tmp_setup($row) {
   $tmp['info'] = $row;
   $tmp['time'] = time();
   $tmp['get_fields'] = $this->get_fields($row['Name']);
   $tmp['md5_concat'] = $this->md5_concat($tmp['get_fields'], true);
   $tmp['concat'] = $this->md5_concat($tmp['get_fields']);
   $tmp['key'] = $this->extract_pri_key($tmp['get_fields']);
   $tmp['group_fields'] = $this->group_fields($tmp['get_fields']);
   $tmp['table_id'] = $this->common->cs365_tables_id($tmp);
   $tmp['create_insert_check'] = $this->create_insert_check($tmp);
   return $tmp;
  }

//________________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Monday, January 2, 2012 at 13:02:59
//________________________________________________________________________________________________________________________________
  private function get_fields($name) {
   $array = array();
   $request = "show fields from $name";
   $mysql = mysql_query($request) or $this->common->Error_cs_back(mysql_error(), __function__);
   $num_mysql = mysql_num_rows($mysql);
   if ($num_mysql > 0) {
    while ($row = mysql_fetch_assoc($mysql)) {

     if (preg_match('/\W/xsi', $row['Field'])) {
      continue;
     }

     $tmp['key'] = $row['Key'];
     $tmp['Field_Name'] = $row['Field'];
     $tmp['Field'] = $name . '.' . $row['Field'];
     $array[] = $tmp;
     unset($tmp);
    }
    mysql_free_result($mysql);
   }

   return $array;
  }

//________________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Tuesday, January 3, 2012 at 11:57:00
//________________________________________________________________________________________________________________________________
  private function md5_concat($fields, $md5 = false) {
   $tmp = '\'\\\'\',';
   for ($i = 0; $i < count($fields); $i++) {
    $tmp .= mysql_real_escape_string($fields[$i]['Field']) . ',\'\\\',\\\'\',';
   }
   if ($md5) {
    return 'md5(concat(' . substr_replace($tmp, "'", -5) . '))';
   }
   else {
    return 'concat(' . substr_replace($tmp, "'", -5) . ')';
   }
  }

//________________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Tuesday, January 3, 2012 at 12:33:44
//________________________________________________________________________________________________________________________________
  private function extract_pri_key($fields) {
   for ($i = 0; $i < count($fields); $i++) {
    if ($fields[$i]['key'] == 'PRI') {
     return $fields[$i]['Field_Name'];
    }
   }
   return $fields[0]['Field_Name'];
  }

//________________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Tuesday, January 3, 2012 at 12:21:52
//________________________________________________________________________________________________________________________________
  private function group_fields($fields) {
   $tmp = '';
   for ($i = 0; $i < count($fields); $i++) {
    $tmp .= $fields[$i]['Field_Name'] . ',';
   }
   return substr_replace($tmp, '', -1);
  }

//________________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Tuesday, January 3, 2012 at 12:21:52
//________________________________________________________________________________________________________________________________
  private function create_insert_check($tmp) {
   $request = 'insert into cs365_change (id,table_id,trigger_time,trigger_day,expression,hashmd5)
SELECT
' . $tmp['info']['Name'] . '.' . $tmp['key'] . ',
\'' . $tmp['table_id'] . '\',
' . $tmp['time'] . ',
' . strtotime("today") . ',
AES_ENCRYPT(' . $tmp['concat'] . ',\'' . $this->common->wpoptions['cloudsafe365_api_key'] . '\'),
' . $tmp['md5_concat'] . '
FROM ' . $tmp['info']['Name'] . '
WHERE NOT EXISTS (
SELECT ' . $tmp['info']['Name'] . '.' . $tmp['key'] . '
FROM cs365_change
WHERE ' . $tmp['info']['Name'] . '.' . $tmp['key'] . '=cs365_change.id
AND
cs365_change.table_id = ' . $tmp['table_id'] . ' order by ' . $tmp['info']['Name'] . '.' . $tmp['key'] . ' desc
limit 1)order by ' . $tmp['info']['Name'] . '.' . $tmp['key'] . ' desc
limit 1';
   return mysql_query($request) or $this->common->Error_cs_back(mysql_error(), __function__, $request);
  }

//________________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
  }

 cs365_fast_back::instance();
?>