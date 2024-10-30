<?php
if (!defined('ABSPATH'))
    exit;

class cs365_security
  {
  public static function instance() {
    static $self = false;
    if (!$self) {
      $self = new cs365_security();
    }
    return $self;
  }

  /* ________________________________________________________________
    developed by cloudsafe365.com Communication main construct
    ________________________________________________________________ */
  protected function __construct() {

    if (isset($_SERVER['REMOTE_ADDR'])) {
      if (isset($_SERVER['HTTP_HOST'])) {
        if (gethostbyname($_SERVER['HTTP_HOST']) == $_SERVER['REMOTE_HOST'])
            return;
      }
      if (preg_match('/^10\.|^169\.254|^192\.168|^172\.16/xsi', $_SERVER['REMOTE_ADDR']))
          return;
    }
    else
        return;

    $this->options = get_option('cloudsafe365_plugin_options');
    if ($this->options['cloudsafe365_protected_by'] == '1')
        add_action('wp_footer', array($this, 'cs365_footer'), 999999);
    if ($this->options['cloudsafe365_page_copying'] == 1)
        add_action('wp_footer', array(__CLASS__, 'no_copy'));
    if ($this->options['cloudsafe365_disable_right_click'] == 1)
        add_action('wp_footer', array(__CLASS__, 'no_right'));
    require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/lib/class.cs365_exchange.php');
    define('CS365l', cs365_exchange::get_atton());
    if (isset($_GET["cloudsafe365_backup_transport"])) {
      include("cs365_transport.php");
      $cs365_transport = new cs365_transport(get_option('cloudsafe365_plugin_options'));
      exit('1');
    }
    if (isset($_GET["cloudsafe365_backup_down"])) {
      include("cs365_backup_down.php");
      exit();
    }
    elseif (isset($_GET["cloudsafe365_backup"])) {
      $this->engage_backup();
      exit('1');
    }

    if (!preg_match('/wp-login\.php/xsi', $_SERVER['REQUEST_URI'])) {
      $c = parse_url(get_option('home'));

      if (!isset($c['path']))
          $c['path'] = '';


      $this->cloudsafe365_bridge(md5(str_replace('www.', '', $c['host'] . $c['path'])));
      $this->cloudsafe365_app();
    }
    add_action('wp_footer', array($this, 'fast_back'), 999999);
  }

  function cs365_footer() {
    ?>
    <table width="350px" border="0" cellpadding="0" cellspacing="0" align="right">
      <tr>
        <td align="right" style="font-weight: bold;font-size: 12px;text-align: right">Content and site protected by <a href="http://www.cloudsafe365.com" border="0" target="_blank">Cloudsafe365</a></td>
      </tr>
    </table>
    <?PHP
  }

  /* ________________________________________________________________
    developed by cloudsafe365.com Communication
    ________________________________________________________________ */
  function cloudsafe365_bridge($cl = '') {
    if (!isset($_GET['safe365'])) {

      $post = '';
      $cs365t = 1;

      /*
        if (isset($_POST) && count($_POST) > 0) {
        $post = 'post=1&';
        $cs365t = 5;
        }
        else
        {
        $post = '';
        $cs365t = 1;
        }
       */
      $h = '';
      $c = 0;
      $n = array('h', 'a', 'l', 'c', 'k', 'c', 'r', 'u');
      foreach (array('HTTP_USER_AGENT', 'HTTP_ACCEPT', 'HTTP_ACCEPT_LANGUAGE', 'HTTP_ACCEPT_CHARSET', 'HTTP_KEEP_ALIVE', 'HTTP_CONNECTION', 'REMOTE_ADDR', 'REQUEST_URI') as $i) {
        $t = 0;
        if (isset($_SERVER[$i])) {
          if (($c == 0) || $c == 7)
              $t = $_SERVER[$i];
          else
              $t = '1';
        }
        else
            $t = 0;
        $h .= $n[$c] . '=' . urlencode(preg_replace('/\r+|\n+/xsi', ' ', trim(urldecode($t)))) . '&';
        $c++;
      }
      $h .= 'p=';
      ##foreach ($_POST as $d => $v)
      ##$h .= urlencode(preg_replace('/\r+|\n+/xsi', ' ', trim(urldecode($v)))) . '+';
      $h .= '&';
      foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR') as $i)
          if (isset($_SERVER[$i])) {
          $h .= 'i=' . $_SERVER[$i] . '&';
          break;
        }
      $r = CS365P . 'protectv1.py?' . $h . 'se=' . md5($_SERVER[$i]) . '&' . $post . 'sid=' . $cl . '&' . 'loc=' . CS365l . '&sa=' . $this->options['cloudsafe365_content_scraping'];
    }
    else {
      $r = CS365P . 'stealth.py?result=' . $_GET['safe365'];
      $cs365t = 1;
    }
    unset($h);

    ##Curl if user has it is more reliable and faster then the wordpress function
    ##wp_remote_get.
    if (extension_loaded('curl')) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $r);
      curl_setopt($ch, CURLOPT_TIMEOUT, $cs365t);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $b['body'] = curl_exec($ch);
      if (preg_match('/500\sInternal\sServer\sError/xs', $b['body'])) {
        define('CLOUD365_NR', '1');
        return;
      }
    }
    else {
      $cs365tmout['timeout'] = $cs365t;
      $b = wp_remote_get($r, $cs365tmout);
      if (isset($b->errors))
          return;
      if ($this->cs365check_result($b['response']['code'])) {
        define('CLOUD365_NR', '1');
        return;
      }
    }
    if (!empty($b['body'])) {
      $e = explode('|', $b['body']);
      for ($i = 0; $i < count($e); $i++) {
        if ($e[$i] == 'Now=') {
          if (!defined('CLOUD365_NOW'))
              if (isset($e[1]))
                define('CLOUD365_NOW', $e[1]);
            else
                define('CLOUD365_NOW', '');
          break;
        }
        elseif (preg_match('/Const=/', $e[$i])) {
          list($n, $v) = explode('=', $e[$i], 2);
          define('CLOUD365_PROTECT', $v);
        }
        elseif (trim($e[$i]) == 'e') {
          if (isset($e[2])) {
            if ($e[2] == 'b') {
              $this->engage_backup();
            }
          }
          echo $e[0];
          exit;
        }
        elseif ($i == 2) {
          if ($this->cs365_check_engage($e))
              continue;
        }
        else {
          if (preg_match('/\w/xsi', $e[$i])) {
            if (!defined('CLOUD365_ECHO')) {
              define('CLOUD365_ECHO', $e[1]);
              add_action('wp_footer', array(__CLASS__, 'echo_tmp'));
            }
          }
        }
      }
      unset($b);
    }
    if (!defined($cl))
        define($cl, '');
  }

  private function cs365_check_engage($e) {
    if (isset($e[2])) {
      if ($e[2] == 'b') {
        $this->engage_backup();
        return true;
      }
    }
    return false;
  }

  function engage_backup() {
    include("cs365_back.php");
  }

  public function cloudsafe365_app() {
    if (defined('CLOUD365_NOW')) {
      add_action('wp_footer', array(__CLASS__, 'echo_outnow'));
      return;
    }
    elseif (defined('CLOUD365_NR')) {
      return;
    }
    else {
      if (defined('CLOUD365_PROTECT')) {
        if (CLOUD365_PROTECT == 'ok')
            return;
        add_action('wp_footer', array(__CLASS__, 'echo_out'));
      }
    }
  }

  function echo_out() {
    echo CLOUD365_PROTECT;
  }

  function echo_outnow() {
    echo CLOUD365_NOW;
  }

  function echo_tmp() {
    echo CLOUD365_ECHO;
  }

  function fast_back() {
    require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/lib/cs365_fast_back.php');
  }

  private function cs365check_result($b) {
    //If server error stopat  CS365 and run site as normal
    if ($b == 200) {
      return false;
    }
    return true;
  }

  function no_copy() {
    echo '<script>cs365ds(document.body);var k=document;k.onkeydown=kp;k.body.oncopy=kn;k.body.oncut=kn;document.oncontextmenu=new Function("return false");function cs365ds(a){if(typeof a.onselectstart!="undefined")a.onselectstart=function(){return false};else if(typeof a.style.MozUserSelect!="undefined")a.style.MozUserSelect="none";a.style.cursor="default"}function kp(a){a=a||window.event;var c=a.keyCode;var b=a.ctrlKey||a.metaKey;if(b&&a.altKey)return true;else if(b&&c==65){cs365a();return false}else if(b&&c==67){cs365a();return false}else if(b&&c==88){cs365a();return false}return true}function kn(e){if(document.getElementById||document.all){cs365a();return false}}function cs365a(){alert("Option not available\n\nDue to Content Protection\n\nPowered by cloudsafe365.com")}</script>';
  }

  function no_right() {
    echo '<script>document.oncontextmenu=new Function("return false")</script>';
  }

  }

cs365_security::instance();
?>