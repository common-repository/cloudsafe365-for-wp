<?php
// Don't call the file directly
if (!defined('ABSPATH'))
    exit;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of cs365_common
 *
 * @author cloudsafe365.com
 */
class cs365_common
  {
  function cs365_common() {
    $this->no_refer_table = 'cs365_tables|cs365_change|cs365_triggers|cs365_external_back|cs365_tmp_table|cs365_malware_system|cs365_tmp_restore|cs365_malware_defs|cs365_malware_files|cs365_tmp_storage|cs365_malware_files|cs365_malware_system';
    $this->no_refer_table_array = $explode_array = explode('|', $this->no_refer_table);
    $this->wpoptions = get_option('cloudsafe365_plugin_options');
    $this->encryption = 1;
  }

//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Tuesday, January 3, 2012 at 13:48:10
//________________________________________________________________________________________________________________________________
  function cs365_tables_id($tmp, $exp_type = 1) {
    $request = "
select id
from
cs365_tables
where
table_name = '" . $tmp['info']['Name'] . "'
AND
exp_type = '$exp_type'
AND
expressionmd5 = md5('" . $tmp['group_fields'] . "')
";
    $mysql = mysql_query($request) or $this->Error_cs_back(mysql_error(), __function__);
    if (is_resource($mysql)) {
      $num_mysql = mysql_num_rows($mysql);
      if ($num_mysql > 0) {
        list($id) = mysql_fetch_row($mysql);
        return $id;
      }
      mysql_free_result($mysql);
    }
    $tmp['create'] = (mysql_fetch_assoc(mysql_query('show create table `' . $tmp['info']['Name'] . '`')));

    $request = "INSERT
INTO
	cs365_tables
SET
	table_name = '" . $tmp['info']['Name'] . "',
	expressionmd5 = md5('" . $tmp['group_fields'] . "'),
	expression = '" . $tmp['group_fields'] . "',
     exp_type = '$exp_type',
     key_main =  '" . $tmp['key'] . "',
     create_table =  '" . mysql_real_escape_string($tmp['create']['Create Table']) . "'
";

    mysql_query($request) or $this->Error_cs_back(mysql_error(), __function__);
    return mysql_insert_id();
  }

//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Thursday, January 5, 2012 at 15:19:05
//________________________________________________________________________________________________________________________________
  function Error_cs_back($error, $func, $sql = '') {
    return '';
    echo '<pre>';
    echo __file__ . "\n" . $error . "\n" . $func . "\n" . $sql;
    exit;
    return '';
  }

//________________________________________________________________________________________________________________________________
//________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Thursday, January 12, 2012 at 17:50:00
//________________________________________________________________________________________________________________________________
  function cs365_create_tables() {
    global $wpdb;
    $request = "CREATE TABLE  IF NOT EXISTS `cs365_change` (
                `id_change` int(15) NOT NULL AUTO_INCREMENT,
                `id` int(15) NOT NULL,
                `table_id` int(15) DEFAULT NULL,
                `trigger_time` char(15) DEFAULT NULL,
                `trigger_day` char(15) DEFAULT NULL,
                `expression` longblob,
                `hashmd5` char(33) DEFAULT NULL,
                `set_type` char(1) DEFAULT 'i',
                PRIMARY KEY (`id_change`),
                KEY `hashmd5` (`id`,`table_id`,`hashmd5`)
              ) ENGINE=MyISAM DEFAULT CHARSET=latin1
";
    $wpdb->query($request);

    $request = "CREATE TABLE  IF NOT EXISTS  `cs365_triggers` (
                 `id` int(10) NOT NULL AUTO_INCREMENT,
                 `date_option` int(15) DEFAULT NULL,
                 `options` int(5) DEFAULT '0',
                 PRIMARY KEY (`id`)
               ) ENGINE=MyISAM DEFAULT CHARSET=latin1";
    $wpdb->query($request);

    $num_mysql = $wpdb->get_results("SELECT id FROM cs365_triggers WHERE id = '1' LIMIT 1");

    if (count($num_mysql) <= 0)
        $wpdb->query("INSERT INTO cs365_triggers SET id='1',date_option = '" . time() . "'");

    $num_mysql = $wpdb->get_results("SELECT id FROM cs365_triggers WHERE id = '2' LIMIT 1");
    if (count($num_mysql) <= 0)
        $wpdb->query("INSERT INTO cs365_triggers SET id='2',date_option = '" . date('d', time()) . "'");

    $num_mysql = $wpdb->get_results("SELECT id FROM cs365_triggers WHERE id = '3' LIMIT 1");
    if (count($num_mysql) <= 0)
        $wpdb->query("INSERT INTO cs365_triggers SET id='3',date_option = '" . date('m', time()) . "'");

    $num_mysql = $wpdb->get_results("SELECT id FROM cs365_triggers WHERE id = '4' LIMIT 1");
    if (count($num_mysql) <= 0)
        $wpdb->query("INSERT INTO cs365_triggers SET id='4',date_option = '" . date('Y', time()) . "'");

    $request = "CREATE TABLE  IF NOT EXISTS `cs365_tables` (
                `id` int(15) NOT NULL AUTO_INCREMENT,
                `table_name` char(50) DEFAULT NULL,
                `expressionmd5` char(33) DEFAULT NULL,
                `expression` text,
                `exp_type` char(25) DEFAULT NULL,
                `key_main` char(100) DEFAULT NULL,
                `create_table` text,
                PRIMARY KEY (`id`)
              ) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1  ";
    $wpdb->query($request);


    ##Creating cs365_tmp_table
    $wpdb->query('CREATE TABLE IF NOT EXISTS cs365_tmp_table (info mediumtext,token char(255) DEFAULT NULL, total_files int(6) DEFAULT NULL)ENGINE=InnoDB DEFAULT CHARSET=latin1');

## create `cs365_external_back`
    $query = 'CREATE TABLE  IF NOT EXISTS `cs365_external_back` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `filename` char(150) DEFAULT NULL,
  `directory` tinytext,
  `file_path` tinytext,
  `wp_type` char(100) DEFAULT NULL,
  `hash_content` char(33) DEFAULT NULL,
  `hash_path` char(33) DEFAULT NULL,
  `date_inserted` int(16) DEFAULT NULL,
  `backup_type` char(20) DEFAULT NULL,
  `period` int(10) DEFAULT 0,
  `error` int(1) DEFAULT 0,
  `error_msg` tinytext,
  PRIMARY KEY (`id`),
  KEY `hash_content` (`hash_content`)
) ENGINE=InnoDB AUTO_INCREMENT=604 DEFAULT CHARSET=latin1';
    $wpdb->query($query);
  }

//________________________________________________________________________________________________________________________________


  function update_stats() {
    global $wpdb;
    $time = time();
    $d = date('d', $time);
    $m = date('m', $time);
    $y = date('Y', $time);
    $wpdb->query("update `cs365_triggers` set `date_option`='" . $time . "',`options`= 1 where `id`='1'");
    $wpdb->query("UPDATE cs365_triggers SET options = IF (date_option = '$d' ,options+1,1), date_option = IF (date_option = '$d' ,$d,$d) WHERE id = 2");
    $wpdb->query("UPDATE cs365_triggers SET options = IF (date_option = '$m' ,options+1,1), date_option = IF (date_option = '$m' ,$m,$m) WHERE id = 3");
    $wpdb->query("UPDATE cs365_triggers SET options = IF (date_option = '$y' ,options+1,1), date_option = IF (date_option = '$y' ,$y,$y) WHERE id = 4");
  }

  }

?>