<?php
 if (!defined('ABSPATH'))
   exit;
 /*
   MySQL database backup
  */


 define('MSB_VERSION', '1.0.0');

 define('MSB_NL', "\r\n");

 define('MSB_STRING', 0);
 define('MSB_DOWNLOAD', 1);
 define('MSB_SAVE', 2);

 class MySQL_Backup
  {
  var $link_id = -1;
  var $connected = false;
  var $tables = array();
  var $drop_tables = true;
  var $struct_only = false;
  var $comments = true;
  var $backup_dir = '';
  var $fname_format = 'd_m_y__H_i_s';
  var $error = '';
  function Execute($task = MSB_STRING, $fname = '', $compress = false) {
   if (!($sql = $this->_Retrieve())) {
    return false;
   }
   if ($task == MSB_SAVE) {
    if (empty($fname)) {
     $fname = $this->backup_dir;
     $fname .= date($this->fname_format);
     $fname .= ($compress ? '.sql.gz' : '.sql');
    }
    return $this->_SaveToFile($fname, $sql, $compress);
   }
   elseif ($task == MSB_DOWNLOAD) {
    if (empty($fname)) {
     $fname = date($this->fname_format);
     $fname .= ($compress ? '.sql.gz' : '.sql');
    }
    return $this->_DownloadFile($fname, $sql, $compress);
   }
   else {
    return $sql;
   }
  }

  function _Query($sql) {
   if ($this->link_id !== -1) {
    $result = mysql_query($sql, $this->link_id);
   }
   else {
    $result = mysql_query($sql);
   }
   if (!$result) {
    $this->error = mysql_error();
   }
   return $result;
  }

  function _GetTables() {
   $value = array();
   if (!($result = $this->_Query('SHOW TABLES'))) {
    return false;
   }
   while ($row = mysql_fetch_row($result)) {
    if (empty($this->tables) || in_array($row[0], $this->tables)) {
     $value[] = $row[0];
    }
   }
   if (!sizeof($value)) {
    $this->error = 'No tables found in database.';
    return false;
   }
   return $value;
  }

  function _DumpTable($table) {
   $value = '';
   $this->_Query('LOCK TABLES ' . $table . ' WRITE');
   if ($this->comments) {
    $value .= '#' . MSB_NL;
    $value .= '# Table structure for table `' . $table . '`' . MSB_NL;
    $value .= '#' . MSB_NL . MSB_NL;
   }
   if ($this->drop_tables) {
    $value .= 'drop table IF EXISTS `' . $table . '`;' . MSB_NL;
   }
   if (!($result = $this->_Query('SHOW CREATE TABLE ' . $table))) {
    return false;
   }
   $row = mysql_fetch_assoc($result);
   $value .= str_replace("\n", MSB_NL, $row['Create Table']) . ';';
   $value .= MSB_NL . MSB_NL;
   if (!$this->struct_only) {
    if ($this->comments) {
     $value .= '#' . MSB_NL;
     $value .= '# Dumping data for table `' . $table . '`' . MSB_NL;
     $value .= '#' . MSB_NL . MSB_NL;
    }
    $value .= $this->_GetInserts($table);
   }
   $value .= MSB_NL . MSB_NL;
   $this->_Query('UNLOCK TABLES');
   return $value;
  }

  function _GetInserts($table) {
   $value = '';
   if (!($result = $this->_Query('SELECT * FROM ' . $table))) {
    return false;
   }
   while ($row = mysql_fetch_row($result)) {
    $values = '';
    foreach ($row as $data) {
     $values .= '\'' . addslashes($data) . '\', ';
    }
    $values = substr($values, 0, -2);
    $value .= 'INSERT INTO ' . $table . ' VALUES (' . $values . ');' . MSB_NL;
   }
   return $value;
  }

  function _Retrieve() {
   $value = '';
   if ($this->comments) {
    $value .= '#' . MSB_NL;
    $value .= '# MySQL database dump' . MSB_NL;
    $value .= '# Created by MySQL_Backup class, ver. ' . MSB_VERSION . MSB_NL;
    $value .= '#' . MSB_NL;
//      $value .= '# Host: ' . $this->server . MSB_NL;
    $value .= '# Generated: ' . date('M j, Y') . ' at ' . date('H:i') . MSB_NL;
    $value .= '# MySQL version: ' . mysql_get_server_info() . MSB_NL;
    $value .= '# PHP version: ' . phpversion() . MSB_NL;
    if (!empty($this->database)) {
     $value .= '#' . MSB_NL;
     $value .= '# Database: `' . $this->database . '`' . MSB_NL;
    }
    $value .= '#' . MSB_NL . MSB_NL . MSB_NL;
   }
   if (!($tables = $this->_GetTables())) {
    return false;
   }
   foreach ($tables as $table) {
    if (!($table_dump = $this->_DumpTable($table))) {
     $this->error = mysql_error();
     return false;
    }
    $value .= $table_dump;
   }
   return $value;
  }

  function _SaveToFile($fname, $sql, $compress) {
   if ($compress) {
    if (!($zf = gzopen($fname, 'w9'))) {
     if (!($f = fopen($fname, 'w'))) {
      $this->error = 'Can\'t create the output file most likely folder permission issue.';
      return false;
     }
     fwrite($f, $sql);
     fclose($f);
     return true;
    }
    gzwrite($zf, $sql);
    gzclose($zf);
   }
   else {
    if (!($f = fopen($fname, 'w'))) {
     $this->error = 'Can\'t create the output file most likely folder permission issue.';
     return false;
    }
    fwrite($f, $sql);
    fclose($f);
   }
   return true;
  }

  function _DownloadFile($fname, $sql, $compress) {

   global $wpdb;
   ##send content to datbase instead
   if (isset($_GET['db'])) {
    $fname = 'database_backup.sql.gz';
    if (!isset($_GET["root"]))
      $_GET["root"] = '';
    $email = '';
    $pass = '';
    //$wpdb->query('drop table IF EXISTS cs365_tmp_table');
    require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/lib/DropboxUploader.php');
    try {
     $uploader = new dropboxUploader($email, $pass);
     $uploader->upload_memory(gzencode($sql, 9), $fname, '/' . $_GET["root"] . '/database');
     echo '<span style="color: green">File Uploaded';
    }
    catch (Exception $e) {
     echo '<span style="color: red">Error: ' . htmlspecialchars($e->getMessage()) . '</span>';
    }
    return true;
   }
   header('Content-disposition: filename=' . $fname);
   header('Content-type: application/octetstream');
   header('Pragma: no-cache');
   header('Expires: 0');
   echo ($compress ? gzencode($sql) : $sql);
   return true;
  }

  }

?>