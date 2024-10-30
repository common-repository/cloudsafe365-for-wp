<script type="text/javascript">
  function cs365_reveal(elementid,elementid2) {
    if (document.getElementById(elementid).style.display == 'block')
    {
      var disp = 'none';
      var txt = 'View Details';
    }
    else
    {
      var disp = 'block';
      var txt = 'Hide Details';
    }
    document.getElementById(elementid).style.display = disp;
    document.getElementById(elementid2).innerHTML = txt;
  }
</script>
<?php
function cs365print_out($heading, $content, $img = 'tick', $txt) {
  ?>
  <div class="postbox metabox-holder" style="width:100%">
    <h3 style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:16px; color:#21759B;"><img id="<?php echo 'ics365' . $heading; ?>" src="<?PHP echo WP_PLUGIN_URL; ?>/cloudsafe365-for-wp/images/<?PHP echo $img; ?>.png" width="25" height="25" alt="" /><?PHP echo preg_replace('/_/xsi', ' ', $heading); ?>&nbsp;<span style="font-size: 12px" id="ecs365<?PHP echo $heading; ?>"><?PHP echo $txt; ?></span></h3>
    <div class="inside" style="font-size:13px; font-weight:inherit;">
      <fieldset>
        <p class="form" id="cs365_scan_site">
          <?PHP echo $content; ?>
        </p>
      </fieldset>
    </div>
  </div>
  <?PHP
}

function cs365_Standard($heading = '', $content = '') {
  ?>
  <div class="postbox metabox-holder"  style="width:100%">
    <h3 style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:16px; color:#21759B;"><?PHP echo preg_replace('/_/xsi', ' ', $heading); ?></h3>
    <div class="inside" style="font-size:13px; font-weight:inherit;">
      <fieldset>
        <p class="form" id="cs365_scan_site">
          <?PHP echo $content; ?>
        </p>
      </fieldset>
    </div>
  </div>
  <?PHP
}
?>