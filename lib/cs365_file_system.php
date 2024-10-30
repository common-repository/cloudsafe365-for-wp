<?php
 if (!defined('ABSPATH'))
   exit;
 /*
  * To change this template, choose Tools | Templates
  * and open the template in the editor.
  */

 /**
  * Description of newPHPClass
  *
  * @author Brett Wraight
  */
 class cs365_file_system
  {
  public function __construct($backup_type) {
   global $wpdb;
   $this->backup_type = $backup_type;

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


   ##Checking and altering table with extra coluimns as needed for earlier versions.
   if (mysql_num_rows(mysql_query("SHOW TABLES LIKE 'cs365_external_back'"))) {
    if (!mysql_num_rows(mysql_query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'cs365_external_back' AND COLUMN_NAME = 'period'"))) {
     $wpdb->query('ALTER TABLE cs365_external_back ADD period INT(10) DEFAULT 1');
     $wpdb->query('ALTER TABLE cs365_external_back ADD error INT(1)  DEFAULT 0');
     $wpdb->query('ALTER TABLE cs365_external_back ADD error_msg tinytext');
    }
   }
  }

  function cs365listFiles($from = '.', $wp_type) {
   $content_exclude = array('plugins', 'themes', 'uploads');
   $this->wp_type = $wp_type;
   if (!is_dir($from))
     return false;
   $dirs = array($from);
   while (NULL !== ($dir = array_pop($dirs))) {
    if ($dh = opendir($dir)) {
     while (false !== ($file = readdir($dh))) {
      if ($file == '.' || $file == '..')
        continue;
      if ($wp_type == 'content')
        if (in_array($file, $content_exclude))
         continue;
      $path = $dir . '/' . $file;
      if (is_dir($path)) {
       $dirs[] = $path;
      }
      else {
       $this->counter+=1;
       if ($this->counter > $this->start) {
        $this->files[] = $path;
       }
       if ($this->counter >= $this->finish) {
        if (count($this->files) <= 0)
          return false;
        $this->cs365__test_files();
        return true;
       }
      }
     }
     closedir($dh);
    }
   }
   if (count($this->files) <= 0)
     return false;
   $this->cs365__test_files();
   return true;
  }

  private function cs365__test_files() {
   for ($i = 0; $i < count($this->files); $i++) {
    if (!$this->cs365_file_test($this->files[$i]))
      continue;
    $str = file_get_contents($this->files[$i]);
    $file = pathinfo($this->files[$i]);
    $file['hash_content'] = md5(md5($str) . md5($this->files[$i]));
    $file['hash_path'] = md5($this->files[$i]);
    $file['file_path'] = $this->files[$i];
    $this->prepdone[$i] = $this->cs365_test_database($file);
    unset($file);
   }
  }

  private function cs365_test_database($file) {

   if (preg_match('/\.zip|\.exe|\.gz|\.rar/xsi', $file['basename'])) {
    return 'Zip';
   }


   //File size checking file size inputed by user
   if (!isset($_POST["cs365_filesize_max"]))
     $_POST["cs365_filesize_max"] = 500;
   if (preg_match('/\D/xsi', $_POST["cs365_filesize_max"]))
     $_POST["cs365_filesize_max"] = 500;
   $file_size = round(filesize($file['file_path']) / 1024, 2);
   if ($file_size > $_POST["cs365_filesize_max"]) {
    return 'Large';
   }

   $request = "SELECT id FROM cs365_external_back WHERE hash_content = '" . $file['hash_content'] . "' AND backup_type = '" . $this->backup_type . "' LIMIT 1";
   $mysql = mysql_query($request);
   $num_mysql = mysql_num_rows($mysql);
   if ($num_mysql <= 0) {
    $request = "INSERT
INTO
	cs365_external_back
SET
	filename = '" . mysql_real_escape_string($file['basename']) . "',
	directory = '" . mysql_real_escape_string($file['dirname']) . "',
	file_path = '" . mysql_real_escape_string($file['file_path']) . "',
	wp_type ='" . $this->wp_type . "',
	hash_content ='" . $file['hash_content'] . "',
	hash_path ='" . $file['hash_path'] . "',
	backup_type = '" . $this->backup_type . "'
";
    mysql_query($request) or print mysql_error();

    $request = "SELECT id FROM cs365_external_back WHERE
hash_path = '" . $file['hash_path'] . "'
AND backup_type = 'db'
AND date_inserted is not null LIMIT 1";
    $mysql = mysql_query($request);
    $num_mysql = mysql_num_rows($mysql);
    if ($num_mysql > 0) {
     return 'Modified';
    }

    return 'Prepared';
   }
   else {
    $request = "SELECT id FROM cs365_external_back WHERE
hash_path = '" . $file['hash_path'] . "'
AND backup_type = 'db'
AND date_inserted is not null LIMIT 1";
    $mysql = mysql_query($request);
    $num_mysql = mysql_num_rows($mysql);
    if ($num_mysql > 0) {
     return 'Uploaded';
    }
    else {
     return 'Prepared';
    }
   }
  }

  function cs365_file_test($file) {
   if (!is_file($file))
     return false;
   if (!file_exists($file))
     return false;
   if (!is_readable($file))
     return false;
   return true;
  }

  }

?>