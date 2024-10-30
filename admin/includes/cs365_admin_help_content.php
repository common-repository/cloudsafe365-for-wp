<?PHP

function cloudsafe365_reports_content() {
  ob_start();
  ?>
  <h2>Cloudsafe365 Help <i>Reports</i></h2>

  <p>We protect and monitor your website for many different events ranging from Unauthorized robot visits, page time outs and security attacks. Users and search engine browser your site uninterrupted bots and other nasties are locked out and there details recorded. We\'ve outlined the common ones below:</p>
  <ul>
    <li><strong>Legitimate</strong> - Activity by normal users browsing your site through normal user of web browsers.</li>
    <li><strong>White Robots</strong> - Search Engines Google, Yahoo and Microsoft's bing as well as white listed bots.</li>
    <li><strong>Non Legitimate</strong> - Non legitimate bots and intrusion and hacking attempts stopped.</li>
    <li><strong>Google Microsoft and Yahoo</strong> - By Default these are considered White Robots and therefore considered totally safe  and go unchallenged by the cloudsafe365 web service</li>
    <li><strong>Internal Scans</strong> - These are self scans your website has done usually a automatic process such as a cron job or some other internal activity.</li>
    <li><strong>Blind Robot</strong> - Robot application or just a scout robot bot.</li>
    <li><strong>Page Timeout</strong>- a page view has multiple  session associated with it has attempted multiple interactions. this is usually occurs when someone using modern browsers views the source code of your website generally can be considered low priority you can prevent this is options and activating disable right click and prevent page copying </li>
    <li><strong>SQL Injection</strong> - A SQL injection is often used to attack the security of a website by inputting SQL statements in a web form to get a badly designed website to perform operations on the database (often to dump the database content to the attacker) other than the usual operations as intended by the designer.</li>
    <li><strong>Advanced SQL Injection</strong> - This is a highly sophisticated injection it has gotten around standard detection the advanced injection system detects all attempts by injecting the query by issuing the injection into CloudSafe365 secure sand boxed server.</li>
    <li><strong>Automatic Attempted</strong> - A scrape using a browser to scrape instead of socket connection.</li>
    <li><strong>Cross Site Scripting</strong> - Cross-site scripting (XSS) is a type of computer security vulnerability typically found in Web applications that enables attackers to inject client-side script into Web pages viewed by other users. A cross-site scripting vulnerability may be used by attackers to bypass access controls such as the same origin policy. </li>
    <li><strong>Meta Injection Redirect</strong> - Hacker has tried to either redirect or place in meta code into the site.</li>
    <li><strong>File Bypass Attempt</strong> - Professional hacker/Bot trying to bypass Cloudsafe365 detection system.</li>
    <li><strong>Hacker Override Attempt</strong> - Professional hacker/Bot trying to override using advanced techniques.</li>
    <li><strong>Hackers Quarantined</strong> - Hackers/bots making to many attempts are quarantined for a period.</li>
    <li><strong>Black Flagged IPs</strong> -The IP has been black flagged by the user to never allow the ip address access to the site. </li>
  </ul></p>
  <?PHP
  return cs365_return_help();
}

function cloudsafe365_options_content() {
  ob_start();
  ?>
  <h2>Cloudsafe365 Help <i>Options</i></h2>

  <p>Cloudsafe365 comes with options to suit both the novice and advanced user.</p>
  <ul>
    <li><strong>Backups</strong> - Backups are done with Advanced Encryption Standard (AES). It is used by the U.S. government,  AES is the first available cipher approved by the National Security Agency (NSA) for top secret information. if possible compression algorithms are also used <a href="http://en.wikipedia.org/wiki/Advanced_Encryption_Standard" border="0" target="_blank">information on AES</a> </li>
    <li><strong>Content Scraping</strong> - Turning on Content Scraping if you don\'t want other bogs to syndicate your content. <strong>Please note: </strong>Turning on Content Scraping will also block RSS readers such as Google Reader.</li>
    <li><strong>Advanced Reporting</strong> - If you would like more detailed statistics such as Time and Date, Country, IP addresses, Attack Types, Geolocation, ISP and more then please turn this option on.</li>
    <li><strong>Page Copying</strong> - Turning this option on disables browser copying and pasting features.</li>
    <li><strong>Disable Right Click</strong> - Enable this feature if you don\'t want the user to right click to View Source.</li>
    <li><strong>Email Alerts</strong> - Email reporting has been designed for those users who infrequently log into their Wordpress Dashboard. You can turn this option on to receive email summary reports of the activity that has occurred on your Wordpress site.</li>
  </ul></p>
  <?PHP
  $out = ob_get_contents();
  ob_end_clean();
  return $out;
}

function cloudsafe365_recovery_content() {
  ob_start();
  ?>
  <h2>Cloudsafe365 Help <i>Recovery</i></h2>
  <p>Choose the times you wish or need to recover to the date and time you select. <i>We recommend to do a Recover Test first see explanations below.</p>
  <ol>
    <li><strong>Recovery Test</strong> - This will do a Test on the recovery process and present a report on what is to be recovered for the date and time chosen.</li>
    <li><strong>Recovery Live</strong> - This will do a live recover, tables will be reset with the recovered information for the date and time selected.</li>
  </ol>
  <?PHP
  return cs365_return_help();
}

function cloudsafe365_backup_content() {
  ob_start();
  ?>
  <h2>Cloudsafe365 Help <i>Backup</i></h2>

  <p>Choose from the various backup capabilities .</p>

  <?PHP
  return cs365_return_help();
}

function cloudsafe365_protection_content() {
  ob_start();
  ?>
  <h2>Cloudsafe365 Help <i>Protection</i></h2>
  <pre>
  Cloudsafe365 unique XP engine plus stops content theft from automatic bots and
  scrapers as well as prevents hackers from infiltrating into the html layer of your website,
  give your site unprecedented protection.

  <strong>Stop All :</strong> All Pages pages are protected from Scraping content theft.

  <strong>Allow first page :</strong> First page is passed (Note: Antihacking still applies
  to first page this is for site scraping/content theft.

  <strong>Monitor only :</strong> Content theft and scraping is monitored and reported but
  not stopped anti hacking still active.

  <strong>None :</strong> Content theft and scraping is not monitored or stopped anti hacking
  still active.

  <strong>Stop General Hacking :</strong> Protect your site against XSS, RFI, CRLF, CSRF, Base64,
  Code Injection, SQL Injection hacking as well meta and Remote file injections you are also
  protect from command based attacks,Advanced SQL and Remote and advanced file injections.
  </pre>

  <?PHP
  return cs365_return_help();
}

function cloudsafe365_harden_help() {
  ob_start();
  ?>
  <h2>Cloudsafe365 Help <i>Harden</i></h2>

  <?PHP
  return cs365_return_help();
}

function cloudsafe365_log_content() {
  ob_start();
  ?>
  <h2>Cloudsafe365 Help <i>Logs</i></h2>

  <p>See logs below of local backups done..</p>
  <?PHP
  return cs365_return_help();
}

function cloudsafe365_menupage_content() {
  ob_start();
  ?>
  <h2>Cloudsafe365 Help <i>DashBoard</i></h2>

  <p></p>
  <?PHP
  return cs365_return_help();
}

function cloudsafe365_setup_content() {
  ob_start();
  ?>
  <h2>Cloudsafe365 Help <i>Setup</i></h2>

  <p>Choose options suited to your site</p>
  <?PHP
  return cs365_return_help();
}

function cloudsafe365_malware_content() {
  ob_start();
  ?>
  <h2>Cloudsafe365 Help <i>Malware</i></h2>
  <h3>How does it work</h3>
  <p>
    The Cloudsafe365 malware scanner checks your site for any existing malware we also scan black lists to see if your site is listed or
    has been reported for having malware. The scanner also checks the integurity of your websites DNS, who is.
    Click on the Scan Now to test your site.
  </p>
  <?PHP
  return cs365_return_help();
}

function cs365_return_help() {
  $out = ob_get_contents();
  ob_end_clean();
  return $out;
}
?>