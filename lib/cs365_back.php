<?php
 /*
  * Description of cloudsafe365
  *
  * @author cloudsafe365
  */

 class cs365_back
  {
  public static function instance() {
   static $self = false;
   if (!$self) {
    $self = new cs365_back();
   }

   return $self;
  }

  //put your code here
  function cs365_back($create = '') {
   require_once("cs365_common.php");
   $this->common = new cs365_common();
   if (defined('CS365_CREATE')) {
    $this->common->cs365_create_tables();
   }
   if ($this->common->wpoptions['cloudsafe365_api_key'] == 'none')
     return;
   $this->mysql_statement("show table status");
   $this->common->update_stats();
  }

//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com//Monday, January 2, 2012 at 11:25:25
//________________________________________________________________________________________________________________________________
  private function mysql_statement($request) {
   $mysql = mysql_query($request) or $this->common->Error_cs_back(mysql_error(), __function__);
   $num_mysql = mysql_num_rows($mysql);
   if ($num_mysql > 0) {
    while ($row = mysql_fetch_assoc($mysql)) {
     if (preg_match('/' . $this->common->no_refer_table . '/xsi', $row['Name'])) {
      continue;
     }
     $tmp = $this->tmp_setup($row);
     unset($tmp);
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
   $tmp['create_delete_check'] = $this->create_delete_check($tmp);
   $tmp['create_insert_check'] = $this->create_insert_check($tmp);
   $tmp['create_update_check'] = $this->create_update_check($tmp);
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
cs365_change.table_id = ' . $tmp['table_id'] . ')';
   return mysql_query($request) or $this->common->Error_cs_back(mysql_error(), __function__, $request);
  }

//________________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Wednesday, January 4, 2012 at 14:26:32
//________________________________________________________________________________________________________________________________
  private function create_update_check($tmp) {
   $request = "SELECT id,t,hashmd5,count(hashmd5) as counter
FROM
(
select concat('a') as t," . $tmp['md5_concat'] . " as hashmd5," . $tmp['info']['Name'] . "." . $tmp['key'] . " as id
 from
" . $tmp['info']['Name'] . "
group by hashmd5

UNION ALL

Select concat('b') as t,hashmd5,cs365_change.id
from
cs365_change," . $tmp['info']['Name'] . "
WHERE
cs365_change.table_id = " . $tmp['table_id'] . "
AND
cs365_change.id=" . $tmp['info']['Name'] . "." . $tmp['key'] . "
)
as tmp_table
group by hashmd5
HAVING COUNT(*) = 1
AND
t = 'a'";

   $mysql = mysql_query($request) or $this->common->Error_cs_back(mysql_error(), __function__, $request);
   $num_mysql = mysql_num_rows($mysql);
   if ($num_mysql > 0) {
    while ($row = mysql_fetch_assoc($mysql)) {
     $request2 = "
SELECT
	*
FROM
	" . $tmp['info']['Name'] . "
WHERE
	" . $tmp['key'] . " = '" . $row['id'] . "'
LIMIT
	1
";
     $mysql2 = mysql_query($request2) or $this->common->Error_cs_back(mysql_error(), __function__);
     $num_mysqls = mysql_num_rows($mysql2);
     $expression = array();
     if ($num_mysqls > 0) {
      foreach (mysql_fetch_assoc($mysql2) as $name_item => $value) {
       $row['dat'][] = $value;
       $expression[] = '\\\'' . mysql_real_escape_string($value) . '\\\'';
      }
      mysql_free_result($mysql2);

      $update = implode(",", $expression);


      if ($this->common->encryption == 1) {
       $row['update'] = 'insert into cs365_change (id,table_id,trigger_time,trigger_day,expression,hashmd5,set_type) VALUES
(' . $row['id'] . ',' . $tmp['table_id'] . ',' . $tmp['time'] . ',' . strtotime("today") . ',AES_ENCRYPT(\'' . $update . '\',\'' . $this->common->wpoptions['cloudsafe365_api_key'] . '\'),\'' . $row['hashmd5'] . '\',\'u\' )';
      }
      else {
       $row['update'] = 'insert into cs365_change (id,table_id,trigger_time,trigger_day,expression,hashmd5,set_type) VALUES
(' . $row['id'] . ',' . $tmp['table_id'] . ',' . $tmp['time'] . ',' . strtotime("today") . ', \'' . $update . '\'  ,\'' . $row['hashmd5'] . '\',\'u\' )';
      }
      mysql_query($row['update']) or $this->common->Error_cs_back(mysql_error(), __function__);
      $array[] = $row;
      unset($row);
     }
    }
    mysql_free_result($mysql);
   }
  }

//________________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Thursday, January 5, 2012 at 13:52:13
//Setting up table for deletes...
//________________________________________________________________________________________________________________________________
  private function create_delete_check($tmp) {
   $request = "select id,hashmd5
from cs365_change
where cs365_change.id
NOT in
(
SELECT " . $tmp['info']['Name'] . "." . $tmp['key'] . " as id
from " . $tmp['info']['Name'] . "
WHERE cs365_change.id
)
AND id
NOT in
(
SELECT cs365_change.id
from cs365_change
WHERE set_type = 'd'
)
AND
table_id = " . $tmp['table_id'] . "
group by id
";

   $mysql = mysql_query($request) or $this->common->Error_cs_back(mysql_error(), __function__);
   $num_mysql = mysql_num_rows($mysql);
   if ($num_mysql > 0) {
    while ($row = mysql_fetch_assoc($mysql)) {
     ##checking if already deleted
     mysql_query('insert into cs365_change (id,table_id,trigger_time,trigger_day,expression,hashmd5,set_type) VALUES
                                   (' . $row['id'] . ',' . $tmp['table_id'] . ',' . $tmp['time'] . ',' . strtotime("today") . ',\'\',\'' . $row['hashmd5'] . '\',\'d\' )');
    }
    mysql_free_result($mysql);
   }
  }

//________________________________________________________________________________________________________________________________
  }

 cs365_back::instance();
?>