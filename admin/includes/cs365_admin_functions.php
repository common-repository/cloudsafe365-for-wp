<?php

 function sc365_setup_hidden($options, $used_data) {
  $hidden = '';
  foreach (array_keys($options) as $item) {
   if (in_array($item, $used_data))
     continue;
   $hidden .= '<input type="hidden" name="cloudsafe365_plugin_options[' . $item . ']" value="' . $options[$item] . '"/>' . "\n";
  }
  return $hidden;
 }
?>