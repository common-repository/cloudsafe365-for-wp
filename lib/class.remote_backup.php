<?php

 class cs365_remote_backup
  {
  function create_tmp_restore_table() {
   global $wpdb;
   $request = "CREATE TABLE  IF NOT EXISTS `cs365_tmp_restore` (
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
              ) ENGINE=MyISAM DEFAULT CHARSET=latin1";
   $wpdb->query($request);
   $this->truncate_cs365_tmp_restore();
  }

  function check_dabase_count() {
   global $wpdb;
   return $wpdb->get_var("select count(id) as counter from cs365_tmp_restore");
  }

  function insert_cs365_tmp_restore($json) {
   global $wpdb;
   for ($i = 0; $i < count($json); $i++) {
    $request = "INSERT
INTO
	cs365_tmp_restore
SET
	id_change = '" . $json[$i]->id_change . "',
     id = '" . $json[$i]->id . "',
     table_id = '" . $json[$i]->table_id . "',
     trigger_time = '" . $json[$i]->trigger_time . "',
     trigger_day = '" . $json[$i]->trigger_day . "',
     expression = '" . mysql_real_escape_string(base64_decode($json[$i]->expression)) . "',
     hashmd5 = '" . $json[$i]->hashmd5 . "',
     set_type = '" . $json[$i]->set_type . "'
";
    $wpdb->query($request);
   }
  }

  function truncate_cs365_tmp_restore() {
   global $wpdb;
   $wpdb->query('truncate table  `cs365_tmp_restore`');
  }

  }

?>