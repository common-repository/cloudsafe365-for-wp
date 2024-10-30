<?php
 /*
  * To change this template, choose Tools | Templates
  * and open the template in the editor.
  */

 /**
  * Description of cs365_calendar_restore
  *
  * @author cloudsafe365.com
  */
 class cs365_calendar_restore
  {
  //put your code here
  function cs365_calendar_restore() {

  }

  //________________________________________________________________________________________________________________________________
//Developed by cloudsafe365.com
//Monday, January 9, 2012 at 07:50:07
//________________________________________________________________________________________________________________________________
  function get_restore_calandar() {
   $request = "
select trigger_day from
cs365_change
group by trigger_day
order by trigger_day desc
limit 31
";
   $array = array();
   $mysql = mysql_query($request) or print mysql_error();
   $num_mysql = mysql_num_rows($mysql) or print mysql_error();
   if ($num_mysql > 0) {
    $c = 0;
    while ($row = mysql_fetch_assoc($mysql)) {
     if ($c == 0) {
      $this->start_date = $row['trigger_day'];
     }
     $array[] = $row['trigger_day'] + 86399;
     $c++;
    }
   }
   return $array;
  }

//________________________________________________________________________________________________________________________________
  }

?>