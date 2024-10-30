<?php
function cs365_setup() {


  if (isset($_GET["register"])) {

    if ($_GET["register"] == 3) {
      if (isset($_GET['api_key'])) {
        $_POST['api_key'] = $_GET['api_key'];
      }
    }
  }

  $options = get_option('cloudsafe365_plugin_options');

  if (isset($_GET["cancel"]) == 1) {
    unset($options['api_key']);
    update_option('cloudsafe365_plugin_options', $options, '', 'yes');
  }


  if (!isset($_GET["register"])) {
    $_GET["register"] = 0;
  }

  if (!isset($_POST["api_key"])) {
    if ($options['api_key'] == 1) {
      show_api_key($options);
      return;
    }
  }

  switch ($_GET["register"])
    {
    case (0) :
      cs365_setup0($options);
      break;
    case (1) :
      cs365_setup1($options);
      break;
    case (2) :
      cs365_setup2($options);
      break;
    case (3) :
      cs365_api_key($options);
      break;
    case (4) :

      show_api_key($options);
      break;
    default :
      break;
      cs365_setup0($options);
    }
}



function cs365_setup1($options) {
  $items = array('backup', 'security', 'protection');
  $type = 'basic';
  foreach ($_POST as $name => $value) {
    if (in_array($name, $items))
        if ($value == 'plus') {
        $type = 'plus';
      }
  }
  if ($type == 'basic')
      return;
  $hidden = '';
  $options['cloudsafe365_type'] = 1;
  update_option('cloudsafe365_plugin_options', $options, '', 'yes');
  foreach ($_POST as $name => $value)
      $hidden .= '<input type="hidden" name="' . $name . '"  value="' . $value . '"/>';
  cs365_plus_payment($hidden, $options);
  exit;
}

/* ________________________________________________________________ */
function cs365_setup0($options) {
  ?>
  <div class="wrap about-wrap" style="width:800px">
    <div style="width:100%; float:left; clear:left; display:inline; margin: 0 20px 12px 0">
      <div class="postbox metabox-holder">
        <h3 style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:16px; color:#21759B;">Extreme Web Protection: Quick Set Up</h3>
        <div class="inside" style="font-size:13px; font-weight:inherit;">
          <fieldset>
            <form  onsubmit="if (checkEmail() == false){return false}" method="post" name="setup_form" id="setup_form" action="?page=cloudsafe365-setup&register=1" style="margin-left: 0px;margin-top: 0px;margin-right: 0px;margin-bottom: 0px;">
              <p class="form">
              <table width="800px" border="0" cellpadding="5" cellspacing="0" align="left">
                <tr>
                  <td colspan="2" align="left" style="white-space:none"><strong>Contact Email :</strong>&nbsp;<input type="text" name="cloudsafe365_email_address" id="cloudsafe365_email_address" value="<?php echo $options['cloudsafe365_email_address']; ?>" style="font-size:16px;width:200px;text-align: left;background-color:#D1E5EE" />
                    <br /><span style="font-size:10px"> * A valid email  address required to send  application Key to.</span>
                  </td>
                </tr>
                <tr>
                  <td width="25px" id="back_imga"><img src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/tick.png" width="25" height="25" alt="" /></td>
                  <td style="cursor: pointer" onClick="checkthis('back_imga','back_imgb','backup','backup_plus',this.nextSibling)">
                    <input OnChange="cs365add_remove_tick('back_imga','back_imgb',this.id)" type="radio" id="backup" name="backup" value="basic" checked/>
                    <span style="width:25px;font-size:25px;font-weight: bold"width="25px"align="center">1</span>
                    <span style="font-weight: bold">
                      Security and site  Monitoring Backup to Local and Manual Backup to Dropbox :  </span>
                    <br>Security Monitoring<br>
                    Malware Site Scanning<br>
                    Malware Deep Core Site Scanning<br>
                    Site Hardening Report<br>
                    Disable Right Copy Click of site content
                  </td>
                </tr>
                <tr>
                  <td width="25px" id="back_imgd">&nbsp;</td>
                  <td style="cursor: pointer" onClick="checkthis('back_imgd','back_imgc','security_plus','security',this.nextSibling)">
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td width="25px" id="back_imgb"><img src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/clear.png" width="25" height="25" alt="" /></td>
                  <td style="cursor: pointer" onClick="checkthis('back_imgb','back_imga','backup_plus','backup',this.nextSibling)">
                    <input type="radio" OnChange="cs365add_remove_tick('back_imgb','back_imga',this.id)" id="backup_plus" name="backup" value="plus"/>
                    <span style="width:25px;font-size:25px;font-weight: bold"width="25px"align="center">2</span>
                    <span style="font-weight: bold">
                      Plus : Malware, Spam Protection, Site Surface and Deep Core Scanning and Automatic Backup : </span>
                    <br/>
                    Malware  Protection<br>
                    Spam Protection<br>
                    Site Infiltration Protection<br>
                    Site Phishing and Hijack Protection<br>
                    Prevent all forms of content theft Site Scraping<br>
                    Automatic Backup securely to Amazon Web Service<br>
                    Ctrl C file copy right click and download entire site<br>
                    Detailed reporting of intrusion and black robot attempts
                  </td>
                </tr>
                <tr>
                  <td width="25px" id="back_imgc">&nbsp;</td>
                  <td style="cursor: pointer" onClick="checkthis('back_imgc','back_imgd','security','security_plus',this.nextSibling)">
                    <input  OnChange="cs365add_remove_tick('back_imgc','back_imgd',this)" type="hidden" id="security" name="security" value="basic" checked/>&nbsp;</td>
                </tr>
                <tr>
                  <td width="25px" id="back_imge">&nbsp;</td>
                  <td style="cursor: pointer" onClick="checkthis('back_imge','back_imgf','protection','protection_plus',this.nextSibling)"
                      <input type="hidden"  OnChange="cs365add_remove_tick('back_imge','back_imgf',this)" id="protection" name="protection" value="basic" checked/> </td>
                </tr>
                <tr>
                  <td width="25px" id="back_imgf"></td>
                  <td style="cursor: pointer" onClick="checkthis('back_imgf','back_imge','protection_plus','protection',this.nextSibling)">
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td colspan="2" align="center" style="padding-right: 50px" style="white-space:nowrap:">
                    <span id="cs365totals"style="font-size:16px;font-weight: bold"></span>
                    <input type="submit" name="submit"  class="button-primary" style="height:25px;width:100px" value="Activate"/>
                    <?PHP
                    if (isset($options['cloudsafe365_setup'])) {
                      ?>
                      &nbsp;&nbsp;
                      <input type="button" OnClick="window.location='?page=cloudsafe365'" name="button"  class="button-primary" style="height:25px;width:100px" value="Dashboard"/>
                      <?PHP
                    }
                    ?>
                    <span id="cs365pricing"style="font-size:18px;font-weight: bold;color:#21759B">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                  </td>
                </tr>
              </table>
              </p>
            </form>
          </fieldset>
        </div>
      </div>
    </div>
  </div>
  <script language="javascript">
    function checkEmail() {
      var email = document.getElementById('cloudsafe365_email_address');
      var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
      if (!filter.test(email.value)) {
        alert("Please provide a valid email address\n\nWe require a email  address to send you your  application Key");
        email.focus;
        return false;
      }
      return true;
    }
  </script>
  <script type="text/javascript">
    <!--

    function checkthis(add,remove,radiocheck,radiouncheck,radioobject)
    {
      var x=document.getElementById(radiocheck);
      var y=document.getElementById(radiouncheck);
      x.checked = true;
      y.checked = false;
      cs365add_remove_tick(add,remove,radiocheck)
    }

    function cs365add_remove_tick(add,remove,radioobject)
    {
      var x=document.getElementById(add);
      var y=document.getElementById(remove);
      var z=document.getElementById(radioobject);
      if (z.checked) {
        x.innerHTML = '<img src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/tick.png" width="25" height="25" alt="" />';
        y.innerHTML = '<img src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/clear.png" width="25" height="25" alt="" />';
      }
      maths_payment();
    }

    function maths_payment()
    {
      var test = 'free';
      var x=document.getElementById('backup_plus');
      if (x.checked) {
        test = 'plus';
      }
      x=document.getElementById('cs365totals');
      y=document.getElementById('cs365pricing');
      if (test == 'plus') {
        x.innerHTML = 'No risk 30 day money back guarantee&nbsp;';
        y.innerHTML = '&nbsp;Just&nbsp;$15.00 a month';
      }
      else
      {
        x.innerHTML = 'Total Monthly Charge Free';
        y.innerHTML = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
      }
    }
    //-->
  </script>

  <?PHP
}

function cs365_plus_payment($hidden, $options) {
  ?>
  <div class="wrap about-wrap" style="width:800px">
    <div style="width:100%; float:left; clear:left; display:inline; margin: 0 20px 12px 0">
      <div class="postbox metabox-holder">
        <h3 style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:16px; color:#21759B;">Extreme Web Protection: Quick Set Up</h3>
        <div class="inside" style="font-size:13px; font-weight:inherit;">
          <fieldset>
            <?PHP
            $r = '';
            if (isset($_SERVER['HTTP_HOST'])):
              $aton = sprintf("%u", ip2long(gethostbyname($_SERVER['HTTP_HOST'])));
            else:
              $aton = 2;
            endif;
            $c = parse_url(get_option('home'));
            if (!isset($c['path']))
                $c['path'] = '';

            if (preg_match('/\w/xsi', $r)) {
              $r = '&r=' . get_option('home');
            }
            if (isset($_POST['cloudsafe365_email_address'])) {
              $options['cloudsafe365_email_address'] = $_POST['cloudsafe365_email_address'];
              update_option('cloudsafe365_plugin_options', $options, '', 'yes');
            }

            if (isset($options['cloudsafe365_api_key'])) {
              if (strlen($options['cloudsafe365_api_key']) != 33)
                  $options['cloudsafe365_api_key'] = 'none';
            }
            else
                $options['cloudsafe365_api_key'] = 'none';
            $query = '?activate=1&aton=' . $aton . '&t=1&h=' . str_replace('www.', '', $c['host'] . $c['path']) . '&' . http_build_query($options);
            ?>
            <div id='api_key'>
              <form onsubmit="if (check_tos() == false){return false};add_api_key()" method="post" name="setup_form" id="setup_form" action="http://www.cloudsafe365.com/subscription/pay_now.php<?PHP echo $query; ?>" style="margin-left: 0px;margin-top: 0px;margin-right: 0px;margin-bottom: 0px;">
                <p class="form">
                <style>
                  td{font-size:16px;}
                </style>
                <table width="100%" border="0" cellpadding="10" cellspacing="0" align="left">
                  <tr>
                    <td colspan="5">You have selected cloudsafe365 plus as your extreme web protection for WordPress please select your payment method</td>
                  </tr>
                  <tr>
                    <td colspan="5">&nbsp;</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td style="font-weight:bold">Billing Cycle</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td style="font-weight:bold">Payment Method</td>
                  </tr>

                  <tr>
                    <td>&nbsp;</td>
                    <td><input type="text" id="coupon" name="coupon" value="" />&nbsp;Coupon Code</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>

                  <tr>
                    <td>&nbsp;</td>
                    <td><input type="radio" id="name" name="billing" value="m" checked/>No risk 30 day money back guarantee Monthly : USD $15:00</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><input type="radio" id="Paypal" name="Paypal" value="true" checked/>Paypal</td>
                  </tr>

                  <tr>
                    <td>&nbsp;</td>
                    <td><input type="radio" id="name" name="billing" value="y"/>No risk 30 day money back guarantee Yearly : USD $180:00</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td colspan="4"><textarea id="termofservice" name="termofservice" rows="8" style="font-size:10px;width:95%" >The cloudsafe365.com Website ("Website") provides a cloud based security & backup Service ("Service") operated by cloudsafe365 Inc. ("cloudsafe365").

                                  Use of the Website and Service is subject to the following Terms of Service ("TOS") as well as cloudsafe365's Privacy Policy and Refund Policies, both of which are incorporated by reference into this TOS.

                                  When you use a cloudsafe365 API Key ("API Key"), download the cloudsafe365 for WordPress Plugin ("Plugin") or access any part of the Website or use the Service, you agree that you have read, understood, and agree to be bound by the these Terms of Service concerning your use of: (a) the Website and Service; (b) the API Key; and (c) the Plugin.

                                cloudsafe365 Inc licences cloudsafe365 & XPengine Service from cloudsafe365 Holdings Pty Ltd ACN 154 486 898.

                                cloudsafe365 for WordPress Plugin Licence

                                cloudsafe365 for WordPress is licensed under the GNU general public license available at: (http://www.gnu.org/licenses/gpl.html). Serviceware means the cloudsafe365 Service that can be thought of as a Serviceware Plugin as per guidelines referred to in http://wordpress.org/extend/Plugins/about/guidelines.

                                The Plugin can function in its own right however when activated via an API key it accesses extra processing and storage capabilities.

                                General

                                To use the Service, download the Plugin from either the Website, where you will have to register, or the WordPress Plugin Store where registration is automatic.

                                After activating the Service and being sent a confirmation API Key which provides access to the cloud based web service, you will be able to use the Service.

                                cloudsafe365 may in its sole discretion change, modify, suspend, make improvements to or discontinue any aspect of the Website and Service, temporarily or permanently, at any time and without notice to you.  Under no circumstances will cloudsafe365 be liable for any such change, modification, suspension, improvement or discontinuance.

                                If you do not agree with any of these changes, you may terminate your account as set forth in Section 13.

                                Registration

                                If you download the Plugin from the WordPress Store, registration happens automatically when you activate the Plugin.  If you download the Plugin from the Website that registration is manual.

                                By registering with cloudsafe365, you represent and warrant that the information you provide to cloudsafe365 in connection with any registration process is true and accurate, and that you will promptly notify cloudsafe365 if any of that information changes.

                                cloudsafe365 may use the information that you provide during the registration process, in particular your email address: (a) to communicate with you about the Website and Service, including without limitation, any changes to cloudsafe365's Privacy Policy or other policies; (b) respond to your emails to us regarding security alerts and (c) for all other purposes stated in cloudsafe365's Privacy Policy.

                                 cloudsafe365 reserves the right to terminate your access to and use of all of the cloudsafe365 Website if you provide false or inaccurate information.

                                If you obtain or purchase an API Key, you are responsible for maintaining its security, and you are fully responsible for all activities that occur under the account and any other actions taken in connection with your API Key.  You must immediately notify cloudsafe365 of any unauthorized uses of your API Key, your account or any other breaches of security.  cloudsafe365 will not be liable for any acts or omissions by you, including any damages of any kind incurred as a result of such acts or omissions.

                                Payment

                                You agree to pay cloudsafe365 the Service fees as indicated in the Pricing Plans presented to you when purchasing a Service in accordance with the Service selected.  cloudsafe365 reserves the right to change the payment terms and fees upon thirty (30) days prior written notice to you.  Your continued use of the API Key constitutes your acceptance of all of the changed payment and fee terms.

                                Copyright Infringement and DMCA Policy

                                As cloudsafe365 asks others to respect its intellectual property rights, it respects the intellectual property rights of others.  If you believe that material located on or linked to by the Website violates your copyright, you are encouraged to notify cloudsafe365 in accordance with cloudsafe365's Digital Millennium Copyright Act ("DMCA") Policy. cloudsafe365 will respond to all such notices, including as required or appropriate by removing the infringing material or disabling all links to the infringing material.  In the case of a user who may infringe or repeatedly infringes the copyrights or other intellectual property rights of cloudsafe365 or others, cloudsafe365 may, in its discretion, terminate or deny access to and use of the Website or Service.  In the case of such termination, cloudsafe365 will have no obligation to provide a refund of any amounts previously paid to cloudsafe365.

                                Trademarks

                                cloudsafe365 and XP Engine and all other trademarks, service marks, graphics and logos used in connection with  the Plug In or the Service, or the Website are trademarks or registered trademarks of cloudsafe365 or cloudsafe365's licensors or licensees.  Other trademarks, service marks, graphics and logos used in connection with the Website may be the trademarks of other third parties.  Your use of the Website grants you no right or license to reproduce or otherwise use any cloudsafe365 or third-party registered or unregistered trademarks.

                                Changes

                                The Website, Service, and this TOS may be changed at the sole discretion of cloudsafe365 and without notice.  You are bound by any such updates or changes, including but not limited to those affecting these Terms of Service, and so should periodically review this TOS.

                                Communications with cloudsafe365

                                All notices and other communications to cloudsafe365 required under this TOS should be directed to the cloudsafe365 contact page.  Notice is deemed given twenty-four (24) hours after notice is sent.  Alternatively, we may give notice by mail to the address provided during the registration process.  In such case, notice is deemed given three (3) days after the date of mailing.

                                Limitation of warranties of cloudsafe365, its suppliers and licensors

                                Except as otherwise expressly stated, all content posted to or available from the Website and the Service are provided "as is", and cloudsafe365, its supplies and its licensors make no representations or warranties, express or implied, including but not limited to warranties of merchantability, fitness for a particular purpose, title or non-infringement of proprietary rights.  cloudsafe365 makes no representations and warranties regarding uptime for the Service and the accuracy of the Service in identifying security threats.  You understand and agree that you download from, or otherwise obtain Services through the Website at your own discretion and risk, and that cloudsafe365, its suppliers and its licensors will have no liability or responsibility for any damage to your computer system or data that results from the download or use of such content or Services. Some jurisdictions may not allow the exclusion of implied warranties, so some of the above may not apply to you.

                                Limitation of liability of cloudsafe365, its suppliers and its licensors

                                Except as otherwise expressly stated, in no event will cloudsafe365, its suppliers or its licensors be liable to you or any other party for any direct, indirect, special, consequential or exemplary damages, regardless of the basis or nature of the claim, resulting from any use of the Website or Service, or the contents thereof or of any hyperlinked Website including without limitation any lost profits, business interruption, loss of data or otherwise, even if cloudsafe365, its suppliers or its licensors were expressly advised of the possibility of such damages.  In no event will the aggregate liability for any and all of your claims against cloudsafe365, its suppliers and its licensors arising out of or related to use of the Website and Service, or the contents thereof or of any hyperlinked Website exceed the amounts actually paid by you to cloudsafe365 during the 12-month period prior to the date a claim is made.  Some jurisdictions may not allow the exclusion or limitation of liability for certain incidental or consequential damages, so some of the above limitations may not apply to you. You agree that this Section 10 represents a reasonable allocation of risk.

                                General Representation and Warranty

                                You represent and warrant that your use of the Website and Service will be in accordance with the cloudsafe365 Privacy Policy, with these Terms of Service, with any applicable laws and regulations, including without limitation any local laws or regulations in your country, state, city, or other governmental area, regarding online conduct and acceptable content, and including all applicable laws regarding the transmission of technical data exported from the United States or the country in which you reside, and with any other applicable policy or terms and conditions.

                                Indemnification

                                You agree to defend, indemnify and hold harmless cloudsafe365, its licensee, its contractors and its licensors, and their respective directors, officers, employees and agents from and against any and all claims and expenses, including attorneys' fees, arising out of your use of the Service, including but not limited to out of your violation of any representation or warranty contained in this TOS.

                                Termination

                                cloudsafe365 may terminate this Agreement, your rights under this Agreement, and your access to and use of the Website and Service in its sole discretion, for any reason or no reason at all, (including but not limited to failure to pay the Service Fees), with or without cause and without notice or liability to you or any third party. Any termination of these Terms of Service cloudsafe365 terminates the license to use the Service and this Website and to use your API Key.  You may terminate this Agreement contained in this Terms of Service for any reason upon thirty (30) days prior written notice to cloudsafe365.

                                Survival

                                Upon termination, all rights and obligations created by this Agreement will terminate, except that you will continue to be bound by those terms that would by their nature survive such termination, including without limitation those concerning intellectual property rights, disclaimers of warranties and limitations of liability; representations, warranties and indemnity obligations; and general provisions.

                                Miscellaneous

                                These Terms of Service constitute the entire agreement between cloudsafe365 and you concerning the subject matter hereof, and they may only be modified by a written amendment signed by an authorized executive of cloudsafe365, or by the posting by cloudsafe365 of a revised version.  Except to the extent applicable law, if any, provides otherwise, these Terms of Service, any access to or use of the Website will be governed by the laws of the state of California, U.S.A., excluding its conflict of law provisions, and the proper venue for any disputes arising out of or relating to any of the same will be the state and federal courts located in San Francisco County, California. If any part of these Terms of Service is held invalid or unenforceable, that part will be construed to reflect the parties' original intent, and the remaining portions will remain in full force and effect. A waiver by either party of any term or condition of these Terms of Service or any breach thereof, in any one instance, will not waive such term or condition or any subsequent breach thereof. cloudsafe365 may assign its rights under these Terms of Service without condition.  These Terms of Service will be binding upon and will inure to the benefit of the parties, their successors and permitted assigns.

                                Automatic upgrades

                                Automatic upgrades are available for all versions of the cloudsafe365 Service.  A new version of the Plugin may need to be activated to take advantage of the Service.

                                Refund Policy

                                We will be happy to provide refund within the first thirty (30) days of your purchase.  If you are unable to get the Plugin to install properly on your website or if the Plugin fails to perform the basic functions as designed after this and you have worked with the product support team to try to resolve these issues.  Refunds will be granted at the sole discretion of cloudsafe365 Inc.  No automatic refunds will be given after thirty (30) days from the initial purchase. Please note that by purchasing the Plugin, you agree to the terms of the Refund Policy.</textarea></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td colspan="4">
                      <?PHP
                      $manual = admin_url('admin.php?page=cloudsafe365-setup|register=4');
                      $automatic = admin_url('admin.php?page=cloudsafe365-setup|register=3');
                      ?>
                      <input type="hidden" name="automatic" value="<?php echo $automatic ?>"/>
                      <input type="hidden" name="manual" value="<?php echo $manual ?>"/>
                      <input type="checkbox" id="terms" name="terms" />&nbsp; I agree with the Terms of Service.
                    </td>
                  </tr>
                  <tr>
                    <td colspan="5" align="right" style="padding-right:200px" style="white-space:nowrap:">
                      <?PHP echo $hidden; ?>
                      <input type="submit" name="submit" class="button-primary" style="height:25px;width:150px;font-size:16px" value="Activate Now"/>
                      &nbsp;&nbsp;
                      <input type="button" OnClick="window.location='?page=cloudsafe365-setup'" name="Bacl" class="button-primary" style="height:25px;width:150px;font-size:16px" value="Back"/>
                    </td>
                  </tr>
                </table>
              </form>
            </div>
            </p>
          </fieldset>
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript">
    <!--
    function check_tos()
    {
      var x=document.getElementById('terms');
      if (x.checked != true)
      {
        alert('You must agree to Terms of Service before proceeding');
        return false;
      }
      return true
    }

    function add_api_key()
    {
      var x=document.getElementById('termofservice');
      x.value = '';
      x.display = 'none';
      cs365_site_simpledash();
    }

    function checkthis(add,remove,radiocheck,radiouncheck,radioobject)
    {
      var x=document.getElementById(radiocheck);
      var y=document.getElementById(radiouncheck);
      x.checked = true;
      y.checked = false;
      cs365add_remove_tick(add,remove,radiocheck)
    }

    function cs365add_remove_tick(add,remove,radioobject)
    {
      var x=document.getElementById(add);
      var y=document.getElementById(remove);
      var z=document.getElementById(radioobject);
      if (z.checked) {
        x.innerHTML = '<img src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/tick.png" width="25" height="25" alt="" />';
        y.innerHTML = '<img src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/clear.png" width="25" height="25" alt="" />';
      }
      maths_payment();
    }

    function maths_payment()
    {
      var test_objs=new Array("backup_plus");
      var test = 'free';
      for (i = 0; i < test_objs.length; i++) {
        var x=document.getElementById(test_objs[i]);
        if (x.checked) {
          test = 'plus';
        }
      }
      x=document.getElementById('cs365totals');
      if (test == 'plus') {
        x.innerHTML = 'No Risk Trial for 30 Days $15.00 a month';
      }
      else
      {
        x.innerHTML = 'Total Monthly Charge Free';
      }
    }

    //-->
  </script>
  <?PHP
}

function show_api_key($options) {
  $options['api_key'] = 1;
  update_option('cloudsafe365_plugin_options', $options, '', 'yes');
  ?>
  <form method="post" name="setup_form" id="setup_form" action="?page=cloudsafe365-setup&register=3" style="margin-left: 0px;margin-top: 0px;margin-right: 0px;margin-bottom: 0px;">
    <table align="center">
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td style='font-size:16px; text-align: center' >Enter API KEY</td>
      <tr>
        <td>&nbsp;</td>
      </tr>
      </tr>
      <tr>
        <td><input type="text" id="api_key"  name="api_key" style='font-size:16px;width:500px'/></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td  style='text-align: center;white-spact:none'><input title="You can deactivate the plugin if you are having issues and reactivate" type="submit" name="submit"  class="button-primary" style="height:25px;width:150px" value="Activate Paid Version"/>
          &nbsp;&nbsp;<input  class="button-primary"   style="height:25px;width:150px"  type="button" value="Back" onclick="window.location='?page=cloudsafe365-setup&cancel=1'" />
        </td>
      </tr>
    </table>
  </form>
  <?PHP
}

function cs365_api_key($options) {
  if (isset($options['api_key']))
      unset($options['api_key']);
  $options['cloudsafe365_setup'] = 1;
  $options['cloudsafe365_type'] = 1;
  $check_key = activation_update($options);
  if (strlen($check_key) == 33) {
    $options['cloudsafe365_api_key'] = trim($check_key);
    update_option('cloudsafe365_plugin_options', $options, '', 'yes');
    //running initial back
    define('CS365_CREATE', 1);
    require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/lib/cs365_back.php');
    $url = admin_url() . 'admin.php?page=cloudsafe365';
    ?>
    <img width="50px" height="50px" src="../wp-content/plugins/cloudsafe365-for-wp/images/wait.gif" alt="Going to cloudsafe365 Dashboard." />Going to cloudsafe365 Dashboard....
    <script type="text/javascript">
      window.header("Location: <?php echo $url; ?>");
    </script>
    <meta http-equiv="refresh" content="1;url=<?php echo $url; ?>" />
    <?PHP
  }
  else {
    ?>
    <form method="post" name="setup_form" id="setup_form" action="?page=cloudsafe365-setup&register=3" style="margin-left: 0px;margin-top: 0px;margin-right: 0px;margin-bottom: 0px;">
      <table align="center">
        <tr>
          <td style='font-size:16px; text-align: center;color:red' >There was an error with your API key please try again</td>
        <tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td style='font-size:16px; text-align: center' >Enter your cloudsafe365 API KEY</td>
        <tr>
          <td>&nbsp;</td>
        </tr>
        </tr>
        <tr>
          <td><input type="text" id="api_key"  name="api_key" style='font-size:16px;width:500px'/></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td  style='text-align: center'><input title="You can deactivate the plugin if you are having issues and reactivate" type="submit" name="submit"  class="button-primary" style="height:25px;width:150px" value="Activate Paid Version"/></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td style='font-size:10px; text-align: center;color:red' ><a href="http://www.cloudsafe365.com" title="You can deactivate the plugin if you are having issues and reactivate">contact cloudsafe365 if having issues</a></td>
        <tr>
      </table>
    </form>
    <?PHP
  }
  exit();
}

function cs365_setup2() {
  echo '<br /><br />Please pay now and you can use this great software or big brother will come and get you!';
}
?>