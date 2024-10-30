<?php
 if (!defined('ABSPATH'))
   exit;

 class dropboxUploader
  {
  protected $email;
  protected $password;
  protected $caCertSourceType = self::CACERT_SOURCE_SYSTEM;
  const CACERT_SOURCE_SYSTEM = 0;
  const CACERT_SOURCE_FILE = 1;
  const CACERT_SOURCE_DIR = 2;
  protected $caCertSource;
  protected $loggedIn = false;
  protected $cookies = array();
  /**
   * Constructor
   *
   * @param string $email
   * @param string|null $password
   */
  public function __construct($email, $password) {
   // Check requirements
   if (!extension_loaded('curl'))
     throw new Exception('dropboxUploader requires the cURL extension.');

   $this->email = trim($email);
   $this->password = trim($password);
  }

  public function setCaCertificateFile($file) {
   $this->caCertSourceType = self::CACERT_SOURCE_FILE;
   $this->caCertSource = $file;
  }

  public function setCaCertificateDir($dir) {
   $this->caCertSourceType = self::CACERT_SOURCE_DIR;
   $this->caCertSource = $dir;
  }

  public function upload($filename, $remoteDir = '/') {

   ##Getting token
   $request = "SELECT token FROM cs365_tmp_table LIMIT 1";
   $token = '';
   $mysql = mysql_query($request);
   $num_mysql = mysql_num_rows($mysql);
   if ($num_mysql > 0) {
    list($token) = mysql_fetch_row($mysql);
   }

   if (function_exists('curl_multi_init')) {
    $error = array();
    for ($i = 0; $i < count($filename); $i++) {

     if (!file_exists($filename[$i]) or !is_file($filename[$i]) or !is_readable($filename[$i]))
       $this->files[$i]['what'] = "File '$filename[$i]' does not exist or is not readable.";
    }

    $this->request_multi('https://dl-web.dropbox.com/upload', true, $filename, $remoteDir, $token);
    return;
   }
   if (!file_exists($filename) or !is_file($filename) or !is_readable($filename))
     throw new Exception("File '$filename' does not exist or is not readable.");

   if (!is_string($remoteDir))
     throw new Exception("Remote directory must be a string, is " . gettype($remoteDir) . " instead.");

   $data = $this->request('https://dl-web.dropbox.com/upload', true, array('plain' => 'yes', 'file' => '@' . $filename, 'dest' => $remoteDir, 't' => $token));
   if (strpos($data, 'HTTP/1.1 302 FOUND') === false)
     throw new Exception('Upload failed!');
  }

  public function upload_memory($content, $filename, $dest) {
   ##Getting token
   $request = "SELECT token FROM cs365_tmp_table LIMIT 1";
   $token = '';
   $mysql = mysql_query($request);
   $num_mysql = mysql_num_rows($mysql);
   if ($num_mysql > 0) {
    list($token) = mysql_fetch_row($mysql);
   }

   $data = $this->request_memory('https://dl-web.dropbox.com/upload', $token, $content, $filename, $dest);
   if (strpos($data, 'HTTP/1.1 302 FOUND') === false)
     throw new Exception('Upload failed!');
  }

  public function test_login() {

   global $wpdb;
   mysql_query('CREATE TABLE  IF NOT EXISTS cs365_tmp_table (info mediumtext,token char(255) DEFAULT NULL, total_files int(6) DEFAULT NULL)ENGINE=InnoDB DEFAULT CHARSET=latin1');
   mysql_query('truncate table cs365_tmp_table');
   mysql_query("INSERT INTO cs365_tmp_table SET info = ''");

   $data = $this->request('https://www.dropbox.com/login');

   $data = $this->request('https://www.dropbox.com/login', true, array('login_email' => trim($this->email), 'login_password' => trim($this->password)));

   if (stripos($data, 'location: /home') === false)
     throw new Exception('Login unsuccessful.');

   $data = $this->request('https://www.dropbox.com/home');
   $token = $this->extractToken($data, 'https://dl-web.dropbox.com/upload');

   if (!preg_match('/\w/xsi', $token)) {
    throw new Exception('Error: Issue with Dropbox Token.');
   }

   $request = "UPDATE cs365_tmp_table SET token = '$token', total_files = '' LIMIT 1";
   mysql_query($request);
   $this->loggedIn = true;
  }

  protected function login() {
   $this->loggedIn = true;
  }

  protected function request_multi($url, $post = false, $filename, $remoteDir, $token) {
   $mh = curl_multi_init();
   $handles = array();
   $output = '';
   for ($i = 0; $i < count($filename); $i++) {
    $postData = array('plain' => 'yes', 'file' => '@' . $filename[$i], 'dest' => $remoteDir[$i], 't' => $token);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    switch ($this->caCertSourceType)
     {
     case self::CACERT_SOURCE_FILE:
      curl_setopt($ch, CURLOPT_CAINFO, $this->caCertSource);
      break;
     case self::CACERT_SOURCE_DIR:
      curl_setopt($ch, CURLOPT_CAPATH, $this->caCertSource);
      break;
     }
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if ($post) {
     curl_setopt($ch, CURLOPT_POST, $post);
     curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    }

    // Send cookies
    $rawCookies = array();

    $request = "SELECT info FROM cs365_tmp_table  LIMIT 1";
    $mysql = mysql_query($request) or print mysql_error();
    $num_mysql = mysql_num_rows($mysql) or print mysql_error();
    if ($num_mysql > 0) {
     list($this->cookies) = mysql_fetch_row($mysql);
     $this->cookies = unserialize($this->cookies);
    }

    if (is_array($this->cookies)) {
     foreach ($this->cookies as $k => $v)
       $rawCookies[] = "$k=$v";
     $rawCookies = implode(';', $rawCookies);
     curl_setopt($ch, CURLOPT_COOKIE, $rawCookies);
    }

    // add this handle to the multi handle
    curl_multi_add_handle($mh, $ch);

    // put the handles in an array to loop this later on
    $handles[] = $ch;
   }

   // execute the multi handle
   $running = null;
   do {
    curl_multi_exec($mh, $running);
   }
   while ($running > 0);

   // get the content of the urls (if there is any)
   for ($i = 0; $i < count($handles); $i++) {
    // get the content of the handle
    $data = curl_multi_getcontent($handles[$i]);
    if (strpos($data, 'HTTP/1.1 302 FOUND') === false)
      $this->files[$i]['what'] = false;


    else
      $this->files[$i]['what'] = true;
    // remove the handle from the multi handle
    curl_multi_remove_handle($mh, $handles[$i]);
   }

   // close the multi curl handle to free system resources
   curl_multi_close($mh);

   return $output;
  }

  protected function request($url, $post = false, $postData = array()) {
   global $wpdb;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
   switch ($this->caCertSourceType)
    {
    case self::CACERT_SOURCE_FILE:
     curl_setopt($ch, CURLOPT_CAINFO, $this->caCertSource);
     break;
    case self::CACERT_SOURCE_DIR:
     curl_setopt($ch, CURLOPT_CAPATH, $this->caCertSource);
     break;
    }
   curl_setopt($ch, CURLOPT_HEADER, 1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   if ($post) {
    curl_setopt($ch, CURLOPT_POST, $post);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
   }

   // Send cookies
   $rawCookies = array();

   $request = "SELECT info FROM cs365_tmp_table  LIMIT 1";
   $mysql = mysql_query($request) or print mysql_error();
   $num_mysql = mysql_num_rows($mysql) or print mysql_error();
   if ($num_mysql > 0) {
    list($this->cookies) = mysql_fetch_row($mysql);
    $this->cookies = unserialize($this->cookies);
   }

   if (is_array($this->cookies)) {
    foreach ($this->cookies as $k => $v)
      $rawCookies[] = "$k=$v";
    $rawCookies = implode(';', $rawCookies);
    curl_setopt($ch, CURLOPT_COOKIE, $rawCookies);
   }

   $data = curl_exec($ch);

   if ($data === false)
     throw new Exception('Cannot execute request: ' . curl_error($ch));

   // Store received cookies
   preg_match_all('/Set-Cookie: ([^=]+)=(.*?);/i', $data, $matches, PREG_SET_ORDER);
   foreach ($matches as $match)
     $this->cookies[$match[1]] = $match[2];
   mysql_query("update cs365_tmp_table set info='" . serialize($this->cookies) . "' limit 1");
   curl_close($ch);

   return $data;
  }

  protected function request_memory($url, $token, $content, $filename, $dest) {
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
   switch ($this->caCertSourceType)
    {
    case self::CACERT_SOURCE_FILE:
     curl_setopt($ch, CURLOPT_CAINFO, $this->caCertSource);
     break;
    case self::CACERT_SOURCE_DIR:
     curl_setopt($ch, CURLOPT_CAPATH, $this->caCertSource);
     break;
    }
   curl_setopt($ch, CURLOPT_HEADER, 1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
##_______________________________________________________________________________________________________________________
   $delimiter = '-------------' . uniqid();
   $fileFields = array(
    'file' => array(
     'type' => 'application/x-gzip',
     'content' => $content,
     'filename' => $filename
    ),
   );
// all other fields (not file upload): name => value
   $postFields = array(
    't' => $token,
    'plain' => 'yes',
    'dest' => $dest,
   );

   $data = '';
   foreach ($postFields as $name => $content) {
    $data .= "--" . $delimiter . "\r\n";
    $data .= 'Content-Disposition: form-data; name="' . $name . '"' . "\r\n\r\n";
    $data .= $content;
    $data .= "\r\n";
   }

   foreach ($fileFields as $name => $file) {
    $data .= "--" . $delimiter . "\r\n";
    $data .= 'Content-Disposition: form-data; name="' . $name . '";' . ' filename="' . $file['filename'] . '"' . "\r\n";
    $data .= 'Content-Type: ' . $file['type'] . "\r\n";
    $data .= "\r\n";
    $data .= $file['content'] . "\r\n";
   }
   $data .= "--" . $delimiter . "--\r\n";
   $array = array('Content-Type: multipart/form-data; boundary=' . $delimiter, 'Content-Length: ' . strlen($data));
   curl_setopt($ch, CURLOPT_POST, true);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $array);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
##_______________________________________________________________________________________________________________________
   // Send cookies
   $rawCookies = array();

   $request = "SELECT 	info FROM cs365_tmp_table  LIMIT 1";
   $mysql = mysql_query($request) or print mysql_error();
   $num_mysql = mysql_num_rows($mysql) or print mysql_error();
   if ($num_mysql > 0) {
    list($this->cookies) = mysql_fetch_row($mysql);
    $this->cookies = unserialize($this->cookies);
   }

   if (is_array($this->cookies)) {
    foreach ($this->cookies as $k => $v)
      $rawCookies[] = "$k=$v";
    $rawCookies = implode(';', $rawCookies);
    curl_setopt($ch, CURLOPT_COOKIE, $rawCookies);
   }

   $data = curl_exec($ch);

   if ($data === false)
     throw new Exception('Cannot execute request: ' . curl_error($ch));

   // Store received cookies
   preg_match_all('/Set-Cookie: ([^=]+)=(.*?);/i', $data, $matches, PREG_SET_ORDER);
   foreach ($matches as $match)
     $this->cookies[$match[1]] = $match[2];

   $request = "update cs365_tmp_table set  info='" . serialize($this->cookies) . "' limit 1";
   mysql_query($request) or print mysql_error();
   curl_close($ch);

   return $data;
  }

  protected function extractToken($html, $formAction) {

   if (preg_match('/Something\s+went\s+wrong/xsi', $html)) {
    throw new Exception("Drop is experiencing a problem at the moment don't worry, your files are still safe and the Dropbox has been notified recommend to retry in a little while.");
    return $matches[2];
   }

   if (!preg_match('/<form [^>]*' . preg_quote($formAction, '/') . '[^>]*>.*?(<input [^>]*name="t" [^>]*value="(.*?)"[^>]*>).*?<\/form>/is', $html, $matches) || !isset($matches[2]))
     throw new Exception("Cannot extract token! (form action=$formAction)");
   return $matches[2];
  }

  }

?>