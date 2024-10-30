<?php
// Don't call the file directly
 if (!defined('ABSPATH'))
   exit;
 /*
  * To change this template, choose Tools | Templates
  * and open the template in the editor.
  */

 class cs365_exchange
  {
  /**
   * @var Logger
   */
  public static function instance() {
   static $self = false;
   if (!$self) {
    $self = new cs365_exchange();
   }

   return $self;
  }

  protected function __construct() {

  }

  //Checking if site is looking at itself
  public function get_atton() {
   if (isset($_SERVER['HTTP_HOST']) && (isset($_SERVER['REMOTE_ADDR']))):
    $aton = (int) ip2long(gethostbyname($_SERVER['HTTP_HOST']));
    if ($aton == (int) ip2long($_SERVER['REMOTE_ADDR'])):
     return $aton;
    endif;
   endif;
   return 0;
  }

  public function cs365_coms() {
   $options = array(
    'timeout' => 1);
  }

  }

 cs365_exchange::instance();
?>