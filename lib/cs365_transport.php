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
 class cs365_transport
  {
  //put your code here
  function cs365_transport($options) {
   require_once("cs365_common.php");
   $this->common = new cs365_common();
   if (preg_replace('/\W/xsi', '', $this->aes_encrypt_api_key($options)) == preg_replace('/\W/xsi', '', $_GET['cloudsafe365_backup'])) {
    switch ($_GET["type"])
     {
     case ('t') :
      $this->cs365_change['id'] = $this->get_last_id_cs365_tables();
      if ($_GET['change_start'] < $this->cs365_change['id']) {
       $this->getcs365_tables();
      }
      break;

     case ('c') :
      $this->cs365_change['id'] = $this->get_last_id_cs365_change();
      if ($_GET['change_start'] < $this->cs365_change['id']) {
       $this->getcs365_change();
      }
      break;

     default :
     }
    if (!isset($this->transport)) {
     echo '0';
    }
    elseif (count($this->transport > 0)) {
     echo json_encode($this->transport);
    }
    else {
     echo '0';
    }
   }
  }

//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Wednesday, January 11, 2012 at 23:34:42
//________________________________________________________________________________________________________________________________
  public function get_last_id_cs365_change() {
   $request = "
select id_change
from
cs365_change
order by id_change desc
limit 1
";
   $mysql = mysql_query($request) or print mysql_error();
   $num_mysql = mysql_num_rows($mysql) or print mysql_error();
   if ($num_mysql > 0) {
    list($get_last_id) = mysql_fetch_row($mysql);
    return $get_last_id;
   }
   return 0;
  }

//________________________________________________________________________________________________________________________________
  //________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Thursday, January 12, 2012 at 09:10:20
//________________________________________________________________________________________________________________________________
  public function get_last_id_cs365_tables() {
   $request = "
SELECT
	id
FROM
	cs365_tables
order by id desc
limit 1
";
   $mysql = mysql_query($request) or print mysql_error();
   $num_mysql = mysql_num_rows($mysql) or print mysql_error();
   if ($num_mysql > 0) {
    list($get_last_id) = mysql_fetch_row($mysql);
    return $get_last_id;
   }
   return 0;
  }

//________________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Thursday, January 12, 2012 at 08:55:05
//________________________________________________________________________________________________________________________________
  public function aes_encrypt_api_key($options) {
   $request = "SELECT AES_ENCRYPT('" . $this->common->wpoptions['cloudsafe365_api_key'] . "','" . $options['cloudsafe365_api_key'] . "') as client_key";
   $mysql = mysql_query($request) or print mysql_error();
   $num_mysql = mysql_num_rows($mysql) or print mysql_error();
   if ($num_mysql > 0) {
    list($tmp) = mysql_fetch_row($mysql);
    mysql_free_result($mysql);
    return base64_encode($tmp);
   }
   else {
    exit('no client key here');
   }
  }

//________________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Thursday, January 12, 2012 at 01:07:10
//________________________________________________________________________________________________________________________________
  public function getcs365_change() {
   $request = "
SELECT
	*
FROM
	cs365_change
limit " . $_GET["change_start"] . " ,  " . $_GET["change_limit"] . "
";
   $mysql = mysql_query($request) or print mysql_error();
   $num_mysql = mysql_num_rows($mysql) or print mysql_error();
   if ($num_mysql > 0) {
    while ($row = mysql_fetch_assoc($mysql)) {
     $row['expression'] = base64_encode($row['expression']);
     $this->transport[] = $row;
    }
   }
  }

//________________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Thursday, January 12, 2012 at 11:04:39
//________________________________________________________________________________________________________________________________
  public function getcs365_tables() {
   $request = "
SELECT
	*
FROM
	cs365_tables
limit " . $_GET["change_start"] . " ,  " . $_GET["change_limit"] . "
";
   $mysql = mysql_query($request) or print mysql_error();
   $num_mysql = mysql_num_rows($mysql) or print mysql_error();
   if ($num_mysql > 0) {
    while ($row = mysql_fetch_assoc($mysql)) {
     $this->transport[] = $row;
    }
   }
  }

//________________________________________________________________________________________________________________________________
  }

?>