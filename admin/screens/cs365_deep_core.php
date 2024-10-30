<?php
$line_c = 1;
$f_pipe = '';

class cs365_deep_core
  {
  function cs365_deep_core() {
    $this->str = "";
  }

  function cs365listFiles($from = '.', $wp_type = '') {
    global $line_c, $f_pipe, $wpdb;



    $cs365_setup_defs = $this->cs365_setup_defs();

    if (preg_match('/cs365_message/xsi', $cs365_setup_defs)) {
      return $cs365_setup_defs;
    }
    $dns = '0';
    $this->start = $_POST['start'];
    $this->finish = $_POST['finish'];

    ##resetting data to 0 if start is 0
    if ($this->start == 0) {
      delete_option('cloudsafe365_deep_scan');
      $this->code_lines = 0;
      $this->fileno = 0;
      $this->files_moded = array();

      $cloudsafe365_deep_scan = get_option('cloudsafe365_deep_scan');
      if (isset($cloudsafe365_deep_scan['c'])) {
        $this->c = $cloudsafe365_deep_scan['c'];
      }
      else {
        $this->c = 0;
      }
      $this->c_finish = $this->c + 30000;

      if ($wp_type == 'pt') {
        if (!function_exists('get_plugins')) {
          require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        }
        $allPlugins = get_plugins();
        foreach ($allPlugins as $name_item => $value) {
          if (is_plugin_active($name_item)) {
            $explode_array = explode('/', $name_item);
            $this->cs365_dir_counter(WP_PLUGIN_DIR . '/' . $explode_array[0]);
          }
        }

        $from = get_theme_root();
        $this->cs365_dir_counter($from);
      }
      else
          $this->cs365_dir_counter($from);
      $query = "DELETE
FROM
	cs365_tmp_storage
WHERE
	name = 'files'";
      $wpdb->query($query);

      $query = "INSERT
INTO
	cs365_tmp_storage
SET
	name = 'files',
	sdata = '" . json_encode($this->files_array) . "'
";
      $wpdb->query($query);
    }
    else {
      $cloudsafe365_deep_scan = get_option('cloudsafe365_deep_scan');
      $this->code_lines = $cloudsafe365_deep_scan['code_lines'];
      $this->fileno = $cloudsafe365_deep_scan['fileno'];
      $this->malware = json_decode($cloudsafe365_deep_scan['malware']);
      $this->file_types = json_decode($cloudsafe365_deep_scan['file_types']);
      $this->file_mods = json_decode($cloudsafe365_deep_scan['file_mods']);
      $this->files_moded = json_decode($cloudsafe365_deep_scan['files_moded']);
      $this->c = $cloudsafe365_deep_scan['c'];
    }

    $tmp = $wpdb->get_results("SELECT sdata FROM cs365_tmp_storage WHERE name = 'files'");

    $files = json_decode($tmp[0]->sdata);
    unset($tmp);
    $this->threat_counter['suspeciousl'] = 0;
    $this->threat_counter['suspeciousv'] = 0;
    $this->threat_counter['known'] = 0;
    $this->wp_type = $wp_type;

    for ($i = $this->start; $i < $this->c; $i++) {

      if ($i == $this->finish) {
        $this->scanner();
        return false;
      }

      if (!isset($files[$i])) {
        $this->fileno++;
        continue;
      }
      $path = $files[$i];
      $parts = pathinfo($path);
      $parts['path'] = $path;
      $parts['md5_file'] = md5_file($path);
      $parts['hash_path'] = md5($path);
      $parts['mtime'] = filemtime($path);

      if (!isset($parts['extension'])) {
        $extension = 'no_ext';
      }

      $extension = $parts['extension'];

      if (!$extension) {
        $extension = 'no_ext';
      }

      $modified = 0;
      // $modified = $this->cs365_file_check($parts);

      if ($modified == 2) {
        $this->files_moded[$extension][] = $path;
      }

      if (!isset($this->file_types->$extension))
          $this->file_types->$extension = 1;
      else
          $this->file_types->$extension++;

      if (!isset($this->file_mods->$extension->$modified))
          $this->file_mods->$extension->$modified = 1;
      else
          $this->file_mods->$extension->$modified++;


      //if ($modified != 1) {
      if (!is_executable($path))
          if (!preg_match('/gif|png|jpg|zip|gz|rar|txt|db|DS_Store|mo|po|swf/xsi', $extension)) {
          $line_c = 0;
          $f_pipe = $this->fileno . '_';
          $str = file_get_contents($path);
          $str = preg_replace_callback(
          '|\n|', create_function(
          '$matches', 'global $line_c,$f_pipe;$line_c+=1;return $f_pipe.$line_c.$matches[0];'
          ), $str
          );
          $this->code_lines +=$line_c;


          $this->str .="\n\n" . $path . "\n\n";

          $this->str .= $str;
          // }
        }
      $this->files[$this->fileno] = $path;
      $this->fileno +=1;
    }

    $this->scanner();
    return true;
  }

  function cs365_dir_counter($from) {
    if (!is_dir($from))
        return false;
    $dirs = array($from);
    while (NULL !== ($dir = array_pop($dirs))) {
      if ($dh = opendir($dir)) {
        while (false !== ($file = readdir($dh))) {
          if ($file == '.' || $file == '..')
              continue;
          $path = $dir . '/' . $file;
          if (is_dir($path)) {
            $dirs[] = $path;
          }
          else {
            $this->files_array[] = $path;
            $this->c++;
            if ($this->c > $this->c_finish) {
              break;
              break;
            }
          }
        }
        closedir($dh);
      }
    }
  }

  function scanner() {
    $long = $this->cs365_get_defs();
    $cs365editor = WP_PLUGIN_URL . '/cloudsafe365-for-wp/admin/editor/cs365_edit.php';
    foreach (array_chunk(explode("\n", $this->str), 1000) as $item) {
      preg_match_all('/' . $long . '/xsi', implode("\n", $item), $tmp_preg);
      if (isset($tmp_preg)) {
        for ($i = 0; $i < count($tmp_preg[0]); $i++) {
          $preg_match_all[0][] = $tmp_preg[0][$i];
        }
        unset($tmp_preg);
      }
    }
    unset($item);
    unset($this->str);

    //preg_match_all('/' . $long . '/xsi', $this->str, $preg_match_all);
    //$fh = fopen('/var/www/extra/'.'string'.time().'.txt', "w");
    //fwrite($fh, $this->str);
    //fclose($fh);

    if (isset($preg_match_all)) {
      for ($i = 0; $i < count($preg_match_all[0]); $i++) {
        preg_match('/(\d+)_(\d+)\n$/xsi', $preg_match_all[0][$i], $match);
        if (isset($match[1])) {
          $match[3] = $this->files[$match[1]];
          $match[4] = trim($preg_match_all[0][$i]);
          $match[5] = '<a href="' . $cs365editor . '?file=' . $match[3] . '&line=' . $match[2] . '&malware=' . rawurlencode($match[4]) . '" rel="0" class="newWindow" target="_blank" >View</a>';
          $match[6] = 'suspeciousl';
          $this->threat_counter['suspeciousl'] +=1;
          $this->malware[] = $match;
        }
      }
      unset($preg_match_all);
    }

    $defaults = array(
       'fileno' => $fileno,
       'cloudsafe365_content_scraping' => 3,
       'code_lines' => $this->code_lines,
       'fileno' => $this->fileno,
       'malware' => json_encode($this->malware),
       'file_types' => json_encode($this->file_types),
       'file_mods' => json_encode($this->file_mods),
       'files_moded' => json_encode($this->files_moded),
       'c' => $this->c,
       'current_dir' => $this->current_dir,
       'current_file' => $this->current_file
    );
    update_option('cloudsafe365_deep_scan', $defaults, '', 'yes');
  }

  public function cs365_open_scan_results() {
    $this->options = get_option('cloudsafe365_plugin_options');
    $cloudsafe365_deep_scan = get_option('cloudsafe365_deep_scan');
    $this->malware = json_decode($cloudsafe365_deep_scan['malware']);
    $this->fileno = $cloudsafe365_deep_scan['fileno'];
    $this->code_lines = $cloudsafe365_deep_scan['code_lines'];
    $this->file_types = json_decode($cloudsafe365_deep_scan['file_types']);
    $this->file_mods = json_decode($cloudsafe365_deep_scan['file_mods']);
    $this->c = $cloudsafe365_deep_scan['c'];
    if (isset($cloudsafe365_deep_scan['files_moded'])) {
      $this->files_moded = json_decode($cloudsafe365_deep_scan['files_moded']);
    }

    $this->tables['Plugins'] = '';
    $this->tables['Themes'] = '';
    $this->tables['Core_Wordpress'] = '';
  }

  function cs365_database_scanner() {
    global $wpdb;
    $long = $this->cs365_get_defs(6);
    $fivesdrafts = $wpdb->get_results('select comment_ID,comment_date,comment_author_url,comment_content
from wp_comments');
    foreach ($fivesdrafts as $fivesdraft) {

      preg_match_all('/' . $long . '/xsi', $fivesdraft->comment_content, $preg_match_all);

      if (isset($preg_match_all[0][0])) {
        for ($i = 0; $i < count($preg_match_all); $i++) {
          $this->database_spam->id[] = $id[$preg_match_all[0][$i]];
          $this->database_spam->def_inf[] = $def_inf[$preg_match_all[0][$i]];
          $this->database_spam->content[] = $preg_match_all[0][$i];
          $this->database_spam->comment_ID[] = $fivesdraft->comment_ID;
          $this->database_spam->comment_content[] = str_replace($preg_match_all[0][$i], '<span style="background-color:yellow;color:red;font-weight:bold">' . $preg_match_all[0][$i] . '</span>', $fivesdraft->comment_content);
          $this->database_spam->comment_author_url[] = $fivesdraft->comment_author_url;
        }
      }
    }
    if (isset($this->database_spam)) {
      $comment = '<table width="100%" border="0" cellpadding="0" cellspacing="0" align="left">
<tr>
<td>Comment id</td>
<td>Spam URL</td>
<td>Comment Detected</td>
<td>About</td>
</tr>
';
      for ($i = 0; $i < count($this->database_spam->id); $i++) {
        if ($i % 2 == 0)
            $bg = '#D1E5EE';else
            $bg = '#fff';
        //<td>'.$this->database_spam['id'][$i].'</td>
        $comment .= '<tr style="background-color:' . $bg . '">
     <td>' . $this->database_spam->comment_ID[$i] . '</td>
     <td>' . $this->database_spam->content[$i] . '</td>
     <td>' . $this->database_spam->comment_content[$i] . '</td>
     <td>' . $this->database_spam->def_inf[$i] . '</td>
    </tr>';
      }
      $comment .= '</table>';
      $this->database_spam_content->comment = $comment;
      $this->database_spam_content->count = $i;
    }

    $this->database_spam_content->comment = '';
    $this->database_spam_content->count = 0;
  }

  function cs365_files() {
    $this->cs365_open_scan_results();
    ?>

    <?PHP
    /*
      $content = '<table  border="0" cellpadding="5" cellspacing="0" align="left">
      <tr>
      <td>Extension</td>
      <td>Qty</td>
      <td>Unchanged</td>
      <td>Modified</td>
      </tr>';
     */

    //file types...
    $content = '<table  border="0" cellpadding="5" cellspacing="0" align="left">
   <tr>
    <td>Extension</td>
    <td>Qty</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
   </tr>';

    $i = 0;
    $new_files = 0;
    $modified_files = 0;
    $unchanged_files = 0;
    foreach ($this->file_types as $name_item => $value) {

      if ($i % 2 == 0)
          $bg = '#D1E5EE';else
          $bg = '#fff';
      $i++;
      $content .= '<tr style="background-color:' . $bg . '"><td>' . $name_item . '</td>';
      $a = 0;
      if (isset($this->file_mods->$name_item->$a)) {
        $content .= '<td align="right">' . $this->file_mods->$name_item->$a . '</td>';
        $new_files +=$this->file_mods->$name_item->$a;
      }
      else
          $content .= '<td>&nbsp;</td>';

      $a = 1;
      if (isset($this->file_mods->$name_item->$a)) {
        $content .= '<td align="right">' . $this->file_mods->$name_item->$a . '</td>';
        $unchanged_files +=$this->file_mods->$name_item->$a;
      }
      else
          $content .= '<td>&nbsp;</td>';

      $a = 2;
      if (isset($this->file_mods->$name_item->$a)) {
        $content .= '<td align="right" style="color:red">' . $this->file_mods->$name_item->$a . '&nbsp;
      <a href="#" Onclick="set_cs365_file_list(\'' . trim($name_item) . '\');return false">List</a>
      </td>';
        $modified_files +=$this->file_mods->$name_item->$a;
      }
      else
          $content .= '<td>&nbsp;</td>';
      $content .= '</tr>';
    }
    $content .= '</table>';

    $txt = ' <span style="color:#ccc">|</span> Files Scanned : ' . number_format($this->fileno, 0, ',', ' '). '</span>';
   // $txt .= ' <span style="color:#ccc">|</span> New Files :<span style="color:blue">' . $new_files . '</span>';
    //$txt .= ' <span style="color:#ccc">|</span> Unchanged Files : <span style="">' . $unchanged_files . '</span>';
    //$txt .= ' <span style="color:#ccc">|</span> Modified Files : <span style="color:red">' . $modified_files . '</span>';

    $counter = 'a' . substr(md5(uniqid(mt_rand(), true)), -5);
    $content = '
      <table width="100%" border="0" cellpadding="0" cellspacing="10" align="left">
    <tr>
     <td width="200px" valign="top">' . $content . '</td>
      <td id="cs365_file_list" valign="top">&nbsp;</td>
    </tr>
   </table>';


    $heading = 'Files';

    if ($modified_files > 0) {
      $img = 'cross';
    }
    else {
      $img = 'tick';
    }

    $perc = ceil((($this->fileno / $this->c) * 100));
    $array['files_scanned'] = $this->fileno;
    $array['content'] = $content;
    $array['img'] = $img;
    $array['txt'] = $txt;
    $array['heading'] = $heading;
    $array['modified_files'] = $modified_files;
    $array['c'] = $perc;
    return $array;
    //cs365print_out($heading, $content, $img, $txt);
  }

  function cs365_grouped() {
    $this->cs365_open_scan_results();
    ?>

    <?PHP
    $this->group_counter->Plugins = 0;
    $this->group_counter->Themes = 0;
    $this->group_counter->Core_Wordpress = 0;
    for ($i = 0; $i < count($this->malware); $i++) {

      if ($i % 2 == 0)
          $bg = '#D1E5EE';else
          $bg = '#fff';
      $filename = explode('/', $this->malware[$i][3]);

      $test = preg_replace('/wp-admin\/.*/xsi', '', $_SERVER['SCRIPT_FILENAME']);
      $last = str_replace($test, '', $this->malware[$i][3]);
      if (preg_match('/wp-content\/plugins\//xsi', $last)) {
        preg_match('/wp-content\/plugins\/(\w+)/xsi', $last, $typef);
        $type = 'Plugins';
      }
      elseif (preg_match('/wp-content\/themes\//xsi', $last)) {
        preg_match('/wp-content\/themes\/(\w+)/xsi', $last, $typef);
        $type = 'Themes';
      }
      else {
        $type = 'Core_Wordpress';
      }

      $this->group_counter->$type+=1;

      if (!isset($this->tables[$type]))
          $this->tables[$type] = '';


      $this->tables[$type] .=
      '<tr style="background-color:' . $bg . '">' .
      '<td>' . end($filename) . '</td>' .
      '<td>';
      if (isset($typef[1])) {
        $this->tables[$type] .= $typef[1];
        unset($typef);
      }
      else
          $this->tables[$type] .= '';
      $this->tables[$type] .= '</td>' .
      '<td>' . $this->malware[$i][4];
      substr($this->malware[$i][4], 0, 20);
      if (strlen($this->malware[$i][4]) > 20)
          $this->tables[$type] .= '...';
      $this->tables[$type] .= '</td>' .
      '<td>' . $this->malware[$i][2] . '</td>' .
      '<td>' . $this->malware[$i][5] . '</td>' .
      '</tr>';
    }

    foreach ($this->tables as $name_item => $value) {
      if (preg_match('/\w/xsi', $value)) {
        $array->$name_item->content = '
    <table  width="100%" border="0" cellpadding="0" cellspacing="2" align="left">
     <tr>
      <td>File</td>
      <td>' . $name_item . '</td>
     <td>found</td>
      <td>line</td>
      <td>View</td>
     </tr>
     ' . $value . '
    </table>
    ';
        $array->$name_item->img = 'cross';
        $array->$name_item->group_counter = $this->group_counter->$name_item;
      }
      else {
        $array->$name_item->content = '<tr><td>No issues Found?</td></tr>';
        $array->$name_item->img = 'tick';
        $array->$name_item->group_counter = $this->group_counter->$name_item;
      }
    }

    //saving data..
    return $array;
  }

##_______________________________________________________________________________________________________________________
  function cs365_get_defs($defid = '') {
    global $wpdb;

    $request = "
SELECT
	sdata
FROM
	cs365_tmp_storage
WHERE
	name = 'defs'
LIMIT
	1
";
    $mysql = mysql_query($request) or print mysql_error();

    $num_mysql = mysql_num_rows($mysql) or print mysql_error();


    if ($num_mysql > 0) {

      list($defs) = mysql_fetch_row($mysql);
    }

    if (preg_match('/cs365_message/xsi', $defs)) {
      return $defs;
    }

    $b = json_decode(gzinflate(base64_decode($defs)));

    if ($defid == 6) {
      return $b->ul;
    }
    return $b->ls . '|' . $b->ul;
  }

##_______________________________________________________________________________________________________________________
  function cs365_file_check($parts) {
    $mysql = mysql_query("SELECT id from cs365_malware_system where m='" . $parts['md5_file'] . "' limit 1");
    $num_mysql = mysql_num_rows($mysql);
    if ($num_mysql > 0)
        return 1;

    else {
      $mysql = mysql_query("SELECT id FROM cs365_malware_files WHERE f = '" . $parts['hash_path'] . "' LIMIT 1");
      $num_mysql = mysql_num_rows($mysql);

      if ($num_mysql <= 0) {
        mysql_query("INSERT INTO cs365_malware_files SET f = '" . $parts['hash_path'] . "'");
        $id = mysql_insert_id();

        mysql_query("INSERT INTO cs365_malware_system SET id='$id',m = '" . $parts['md5_file'] . "'");
        return 0;
      }
    }
    list($id) = mysql_fetch_row($mysql);
    mysql_query("UPDATE	cs365_malware_system set m = '" . $parts['md5_file'] . "' WHERE 	id = '$id' LIMIT 1");
    return '2';
  }

  function cs365_setup_defs() {


    return;
  }

  }
?>