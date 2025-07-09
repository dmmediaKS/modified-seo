<?php
 /*-------------------------------------------------------------
   $Id: jquery.entry_state.js.php 16430 2025-05-02 12:11:12Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   --------------------------------------------------------------
   Released under the GNU General Public License
   --------------------------------------------------------------*/

  defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );
?>

<script>
  $(document).ready(function () {
    create_states($('select[name="entry_country_id"]').val());
    
    $('select[name="entry_country_id"]').change(function() {
      create_states($(this).val());
    });
  });
  
  function create_states(val, container) {
    var type = '';
    var zone = '&zone=' + $('[name="'+container+'"]').val();
    if ($('select[name="'+container+'"]').length) {
      type = '&type=select';
    }
    $('#'+container).html('<img src="images/loading.gif">');
    
    var ajax_url = "<?php echo basename($PHP_SELF) . '?' . xtc_session_name() . '=' . xtc_session_id(); ?>";
    var token = "<?php echo (defined('CSRF_TOKEN_SYSTEM') && CSRF_TOKEN_SYSTEM == 'true') ? '&' . $_SESSION['CSRFName'] . '='.  $_SESSION['CSRFToken'] : ''; ?>";
    var data = 'action=get_states&countryid=' + val + type + zone + token + '&field=' + container;

    jQuery.ajax({
      data:     data,
      url:      ajax_url,
      type:     "POST",
      async:    true,
      success:  function(t_states) {        
        $('#'+container).html(t_states);
        <?php if (NEW_SELECT_CHECKBOX == 'true') { ?>
          $('.select_states').not('.noStyling').SumoSelect({ createElems: 'mod', placeholder: '-'});
        <?php } ?>
      }
    });
  }
</script>
