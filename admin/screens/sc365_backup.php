<?PHP
 function cs365_backup_main($options, $long_date) {

  if ($options['cloudsafe365_type'] == 0) {
   $back_type = 'Local Backup';
   $back_icon = 'network_local.png';
  }
  else {
   $back_type = 'Automatic Backup';
   $back_icon = 'icon_update.png';
  }
  ?>
  <div class="wrap about-wrap" style="width:100%">
   <div style="width:60%; float:left; clear:left; display:inline; margin: 0 20px 12px 0">
    <div class="postbox metabox-holder">
     <h3 style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:16px; color:#21759B;">Backup : Select  and setup backup options</h3>
     <table border="0" cellspacing="0px" cellpadding="10px" width="100%" align="center">
      <tr>
       <td align="center" width="150px" id="cs365_backup_td1" style="background-color:#D1E5EE">
        <a title="Encrypted automatic back powered by Amazon"  href="#" OnClick="cs365_tick_radio('cs365_backup_r');cs365_site_simpledash('cs365_backup_layer','cs365_backup_layer');return false">
         <img title="Encrypted automatic back powered by Amazon" src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/<?php echo $back_icon; ?>"  width="59" width="59" alt="Automatic backup" />
        </a>
       </td>
       <td align="center"  id="cs365_dropbpx_td1" width="150px">
        <a title="Sync your Wordpress files and database to dropbox"  href="#" OnClick="cs365_tick_radio('cs365_drop_r');cs365_site_simpledash('cs365_backup_dp1_go','cs365_backup_layer');return false">
         <img title="Sync your Wordpress files and database to dropbox" src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/dropbox.png"  alt="dropbox" />
        </a>
       </td>
       <td align="center" id="cs365_yourpc_td1"  width="150px">
        <a  title="Download your Wordpress database  to your PC" OnClick="cs365_tick_radio('cs365_your_computer_r');alert('Database will start to be downloaded')" href="<?PHP echo site_url() . '?cloudsafe365_backup_down=1&k=' . md5($options['cloudsafe365_api_key']) . '&c=' . $options['confirmcheck']; ?>">
         <img title="Download your Wordpress database  to your PC" src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/pc.png"  alt="Your computer" />
        </a>
       </td>
      </tr>
      <tr>
       <td align="center"  width="150px"  id="cs365_backup_td2" style="background-color:#D1E5EE"><?PHP echo $back_type; ?></td>
       <td align="center" width="150px"  id="cs365_dropbpx_td2">dropbox</td>
       <td align="center" width="150px" id="cs365_yourpc_td2">Your Computer</td>
      </tr>
      <tr>
       <td  align="center" id="cs365_backup_td3" style="background-color:#D1E5EE">
        <a title="Backup" class="button-primary"  href="#" OnClick="cs365_tick_radio('cs365_backup_r');cs365_site_simpledash('cs365_backup_layer','cs365_backup_layer');return false">&nbsp;&nbsp;&nbsp;Encrypted Backup&nbsp;&nbsp;&nbsp;</a>
        <br/><br/>
        <span id="local_backup_tick" >
         <img src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/tick.png"  alt="image" style="border:0"/>
        </span>
        <input type="radio" name="cs365type_in" id="cs365_backup_r" OnClick="cs365movetick(this.id);cs365_site_simpledash('cs365_backup_layer','cs365_backup_layer')"  value="cs365_backup_r" onClick="" CHECKED/>
       </td>
       <td  align="center"  id="cs365_dropbpx_td3">
        <a title="Sync your Wordpress files and database to dropbox" class="button-primary" href="#" OnClick="cs365_tick_radio('cs365_drop_r');cs365_site_simpledash('cs365_backup_dp1_go','cs365_backup_layer');return false">&nbsp;&nbsp;&nbsp;&nbsp;Sync DB & files&nbsp;&nbsp;&nbsp;&nbsp;</a>
        <br/><br/>
        <span id="drop_tick" ></span>
        <input type="radio" name="cs365type_in" id="cs365_drop_r" OnClick="cs365movetick(this.id);cs365_site_simpledash('cs365_backup_dp1_go','cs365_backup_layer')" value="cs365_drop_r"/>
       </td>
       <td  align="center" id="cs365_yourpc_td3">
        <a  title="Download your Wordpress database  to your PC" class="button-primary" OnClick="cs365_tick_radio('cs365_your_computer_r');alert('Database will start to be downloaded')" href="<?PHP echo site_url() . '?cloudsafe365_backup_down=1&k=' . md5($options['cloudsafe365_api_key']) . '&c=' . $options['confirmcheck']; ?>">&nbsp;&nbsp;Database Backup&nbsp;&nbsp;</a>
        <br/><br/>
        <span id="your_computer_tick"></span>

        <input type="radio" name="cs365type_in"  id="cs365_your_computer_r" OnClick="cs365movetick(this.id);window.location='<?PHP echo site_url() . '?cloudsafe365_backup_down=1&k=' . md5($options['cloudsafe365_api_key']) . '&c=' . $options['confirmcheck']; ?>'"  value="cs365_your_computer_r"/>
       </td>
      </tr>
     </table>
     <br/>
     &nbsp;&nbsp;Last backup date was : <?PHP echo $long_date; ?>
     <br/><br/>
     &nbsp;&nbsp;<span style="font-weight:normal;">* For site security : no files are created on your site for this process.</span>
    </div>

    <div id="cs365_backup_layer"></div>


    <?PHP echo cs365_version(); ?></div>
   <div style="width:24%; float:right; display:inline; margin-right:100px;">
    <div class="postbox metabox-holder">
     <h3 style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:16px; color:#21759B;">Help Tips</h3>
     <div class="inside" style="font-size:13px; font-weight:normal;">
      <p><strong>Database Backup:</strong>
       <?php if ($options['cloudsafe365_type'] == 0) {
        ?>
        Your database should be periodically backed up to your local machine or dropbox to ensure a disaster recover solution is in place. We recommend to upgrade to cloudsafe365 plus as backups are snt automatically to a secure cloudsafe365 hosting environment - powered by Amazon Web Services
        <?Php
       }
       else {
        ?>
        Your database should be periodically  backed up to ensure a disaster  recover solution is in place.
        <?PHP
       }
       ?>
       <a href="http://www.cloudsafe365.com/blog/" target="_blank"><img src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/btn-blog-promo.jpg"  alt="image" /></a></div>
    </div>
   </div>
  </div>

  <script type="text/javascript">


   function cs365_save_backup_options() {

    var data = {};
    var inputs = new Array('cloudsafe365_backup_when1','cloudsafe365_backup_when2','cloudsafe365_backup_when3','cloudsafe365_backup_when4');

    for (i = 0; i < inputs.length; i++)
    {
     var x= document.getElementById(inputs[i]);
     if (x.checked)
     {
      data['cloudsafe365_backup_when'] = x.value
     }
    }

    var inputs = new Array('cloudsafe365_backup_database','cloudsafe365_real_time_backups');

    for (i = 0; i < inputs.length; i++)
    {
     var x= document.getElementById(inputs[i]);
     if (x.checked)
     {
      data[inputs[i]] = x.value
     }
    }

    cs365_db_data.extra = data;

    var x= document.getElementById('cs365_backup_layer');
    x.innerHTML ='<img src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/wait.gif"  alt="Please Wait" height="40px" width="40px" />&nbsp;&nbsp;Saving<br/><br/>';

    cs365_site_simpledash('cs365_save_backup_options','cs365_backup_layer');

   }

   function cs365movetick(radio_id) {

    var inputs = new Array('cs365_backup_r','cs365_drop_r','cs365_your_computer_r');
    var tick = new Array('local_backup_tick','drop_tick','your_computer_tick');

    var cs365_colors = new Array(3);
    cs365_colors[0] = new Array('cs365_backup_td1','cs365_backup_td2','cs365_backup_td3');
    cs365_colors[1] = new Array('cs365_dropbpx_td1','cs365_dropbpx_td2','cs365_dropbpx_td3');
    cs365_colors[2] = new Array('cs365_yourpc_td1','cs365_yourpc_td2','cs365_yourpc_td3');

    for (i = 0; i < inputs.length; i++)
    {
     if (inputs[i] != radio_id)
     {
      var x= document.getElementById(inputs[i]);
      x.checked=false;
      var x= document.getElementById(tick[i]);
      x.innerHTML = '';

      for (j = 0; j <  cs365_colors[i].length; j++) {
       var x= document.getElementById(cs365_colors[i][j]);
       x.style.backgroundColor = '#F7FCFE';
      }


     }
     else
     {

      if (radio_id == 'cs365_your_computer_r') {
       var x= document.getElementById('cs365_backup_layer');
       x.innerHTML = '';
      }

      var x= document.getElementById(inputs[i]);
      x.checked=true;
      var x= document.getElementById(tick[i]);
      x.innerHTML = '<img src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/tick.png"  alt="image" style="border:0"/>';

      for (j = 0; j <  cs365_colors[i].length; j++) {
       var x= document.getElementById(cs365_colors[i][j]);
       x.style.backgroundColor = '#D1E5EE';
      }

     }
    }
   }

   function cs365_tick_radio(radio_id) {
    var x= document.getElementById(radio_id);
    x.checked=true;
    cs365movetick(radio_id);
   }


   function cs365_dropbox(){}

   var cs365_db_data = new cs365_dropbox();

   function cs365_site_simpledash(cs365_action,cs365_id)
   {
    jQuery(document).ready(function($) {
     async: true
     var data = {
      action: cs365_action
     };

     jQuery.extend(data, cs365_db_data.extra);

     jQuery.post(ajaxurl, data, function(response)
     {
      var patt=/\w/;
      if (patt.exec(response))
      {
       var x=document.getElementById(cs365_id);
       x.innerHTML = response;
      }
     });
    });
   }
   cs365_site_simpledash('cs365_backup_layer','cs365_backup_layer');


   function ds365sync_dropbox() {
    var data = {};
    var inputs = new Array('cs365_db_db','cs365_plugins_db','cs365_theme_db','cs365_files_db','clear_sync');
    for (i = 0; i < inputs.length; i++) {
     var x= document.getElementById(inputs[i]);
     if (x.checked) {
      data[inputs[i]]= 'yes';
     }
    }


    var inputs = new Array('sc365dp_email','sc365dp_password','cs365_filesize_max');
    for (i = 0; i < inputs.length; i++) {
     var x= document.getElementById(inputs[i]);
     data[inputs[i]]= x.value;

    }

    cs365_db_data.extra = data;

    cs365_site_simpledash('cs365_backup_dp3_go','cs365_backup_layer');

    cs365disptimer()


   }

   function cs365disptimer()
   {
    var t=setTimeout("cs365timera()",1000);
   }
   function cs365timera()
   {
    var data = cs365_db_data.extra;
    db_to_dropBox(data);
   }

   function ds365dropbox_login()
   {
    var x= document.getElementById('sc365dp_email');
    var data = {
     sc365dp_email: x.value
    };
    x= document.getElementById('sc365dp_password');
    data['sc365dp_password']= x.value;

    cs365_db_data.extra = data;

    x= document.getElementById('cs365_backup_layer');
    x.innerHTML ='<img src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/wait.gif"  alt="Please Wait" height="40px" width="40px" />&nbsp;&nbsp;Logging into dropbox<br/><br/>';

    cs365_site_simpledash('cs365_backup_dp2_go','cs365_backup_layer');
   }
  </script>


  <script type="text/javascript" >
   var cs365add_counter = 1;
   var cs365_relogin = ''
   function files_resport(datain)
   {
    jQuery(document).ready(function($) {
     async: false
     var data = {
      action: 'drop_report',
      sc365dp_email: datain['sc365dp_email'],
      sc365dp_password: datain['sc365dp_password'],
      cs365_db_db: datain['cs365_db_db'],
      cs365_plugins_db: datain['cs365_plugins_db'],
      cs365_theme_db: datain['cs365_theme_db'],
      cs365_files_db: datain['cs365_files_db'],
      cs365_filesize_max: datain['cs365_filesize_max'],
      cs365_period: '<?PHP echo $period; ?>',
      cs365start_time:'<?PHP echo time(); ?>'
     };
     jQuery.post(ajaxurl, data, function(response)
     {
      var x=document.getElementById("cs365_dropresponse");
      var t=document.getElementById("drop_box_head");
      var patt=/\w/;
      if (patt.exec(response))
      {
       t.innerHTML = 'File Transfer Report';
       x.innerHTML = response;
       var r=document.getElementById("No_files");
       r.innerHTML = '';
       var r=document.getElementById("once_files");
       r.innerHTML = '';
       var r=document.getElementById("once_filesa");
       r.innerHTML = '';
      }
      else
      {
       t.innerHTML = 'Something is wrong';
       x.innerHTML = response;
      }
     });
    });
   }

   function file_to_dropBox(datain)
   {
    jQuery(document).ready(function($) {
     async: false;
     var data = {
      action: 'file_dropbox',
      cs365_plugins_db: datain['cs365_plugins_db'],
      cs365_theme_db: datain['cs365_theme_db'],
      cs365_files_db: datain['cs365_files_db'],
      cs365_filesize_max: datain['cs365_filesize_max'],
      cs365_period: '<?PHP echo $period; ?>'
     };

     if (cs365add_counter >=100) {
      cs365add_counter = 1;
      cs365_relogin = '';
      data['sc365dp_email'] =  datain['sc365dp_email'],
      data['sc365dp_password'] =  datain['sc365dp_password']
     }
     else
     {
      cs365add_counter += 1;
      var cs365tmp = 100 - cs365add_counter;
      cs365_relogin = ''
     }

     jQuery.post(ajaxurl, data, function(response)
     {
      var x=document.getElementById("cs365_dropresponse");
      var t=document.getElementById("drop_box_title");
      var patt=/\w/;
      if (patt.exec(response))
      {
       var patt=/Error\W+dropbox/;
       if (patt.exec(response))
       {
        x.innerHTML = response;
       }
       else
       {
        x.innerHTML = response + cs365_relogin;
        file_to_dropBox(datain);
       }
      }
      else
      {
       t.innerHTML = 'File transfer complete';
       x.innerHTML = '<br/><br/>Retrieving Report!';
       var r=document.getElementById("No_files");
       r.innerHTML = '';
       var r=document.getElementById("once_files");
       r.innerHTML = '';
       var r=document.getElementById("once_filesa");
       r.innerHTML = '';
       files_resport(datain);
      }
     });
    });
   }

   function drop_box_prepare(start,datain)
   {
    var x=document.getElementById("cs365_dropresponse");
    var t=document.getElementById("drop_box_title");
    var sm=document.getElementById("small_wheel");
    sm.innerHTML ='<img src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/wait.gif"  alt="Please Wait" height="40px" width="40px" />';
    t.innerHTML = ' Preparing files for Transfer to dropbox';
    jQuery(document).ready(function($) {
     async: false
     var data = {
      action: 'add_dropbox',
      cs365_plugins_db: datain['cs365_plugins_db'],
      cs365_theme_db: datain['cs365_theme_db'],
      cs365_files_db: datain['cs365_files_db'],
      cs365_filesize_max: datain['cs365_filesize_max'],
      cs365_period: '<?PHP echo $period; ?>',
      goto_start: start
     };
     jQuery.post(ajaxurl, data, function(response)
     {
      var patt=/\w/;
      if (patt.exec(response))
      {
       var JSONObject = eval("(" + response + ")");

       var arLen=JSONObject.filename.length;
       var fileoutput = '<div style="display: table;">';

       for ( var i=0, len=arLen; i<len; ++i ){

        if (/Prepared/.exec(JSONObject.prepdone[i]))
        {
         var prepdone = '<div id="sc365middlej" style="color:#298CBA" > Ready for Upload</div>';
        }
        if (/Modified/.exec(JSONObject.prepdone[i]))
        {
         var prepdone = '<div id="sc365middlej" style="color:orange">' + JSONObject.prepdone[i] + '</div>';
        }
        if (/Uploaded/.exec(JSONObject.prepdone[i]))
        {
         var prepdone = '<div id="sc365middlej" style="color:blue">Uploaded no change</div>';
        }
        if (/Zip/.exec(JSONObject.prepdone[i]))
        {
         var prepdone = '<div id="sc365middlej" style="color:red">Zips,exe\'s Excluded</div>';
        }
        if (/Large/.exec(JSONObject.prepdone[i]))
        {
         var prepdone = '<div id="sc365middlej" style="color:red">file to large Excluded</div>';
        }
        fileoutput += '<div style="display: table-row;">' +
         '<div id="sc365leftj" >' +
         JSONObject.filename[i]+
         '</div>' +
         '<div id="sc365middlej">' +
         JSONObject.extension[i]+
         '</div>' +
         prepdone +
         '</div>'
       }
       fileoutput += '</div>';
       x.innerHTML = 'Wordpress Folder Type<span style="color:green" id="span_id"> ' + JSONObject.cs365wp_type + '</span>'+
        '<br>' +
        fileoutput +
        '<br>';
       drop_box_prepare(JSONObject.next,datain);
      }
      else
      {
       t.innerHTML = 'Transfering files to dropbox';
       x.innerHTML = 'Contacting and sending first files to dropbox';
       file_to_dropBox(datain);
      }
     });
    });
   }

   function db_to_dropBox(datain)
   {
    jQuery(document).ready(function($) {
     var x=document.getElementById("cs365_dropresponse");
     var t=document.getElementById("drop_box_title");
     t.innerHTML = 'Preparing and Sending Database to dropbox';
     x.innerHTML = '<table><tr><td><img src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/wait.gif"  alt="Please Wait" /></td><td>May take a few moments...</td></tr></table>';
     async: false
     var data = {
      action: 'add_dbdropbox',
      sc365dp_email: datain['sc365dp_email'],
      sc365dp_password: datain['sc365dp_password'],
      cs365_db_db: datain['cs365_db_db'],
      cs365_period: '<?PHP echo $period; ?>'
     };
     jQuery.post(ajaxurl, data, function(response)
     {
      var patt=/\w/;
      if (patt.exec(response))
      {
       x.innerHTML = response;
       var patt=/error/;
       if (patt.exec(response))
       {
        t.innerHTML = 'Database Database  transfer Error!';
       }
       else
       {
        t.innerHTML = 'Database Database  transfer complete' ;
       }
       t.innerHTML += '<br> Preparing files';
       drop_box_prepare(50,datain);
      }
      else
      {
       t.innerHTML = 'UNKNOWN ERROR SENDING TO DROPBOX';
       t.innerHTML += '<br> Preparing files';
       drop_box_prepare(50,datain);
      }
     });
    });
   }


  </script>
  <?PHP
 }
?>