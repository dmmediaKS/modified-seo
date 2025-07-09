<?php
  /* --------------------------------------------------------------
   $Id: default.js.php 16426 2025-04-30 13:50:33Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2019 [www.modified-shop.org]
   --------------------------------------------------------------
   Released under the GNU General Public License
   --------------------------------------------------------------*/
?>
<script>
  $(window).on('load',function () {
    $('.show_rating input').change(function () {
      var $radio = $(this);
      $('.show_rating .selected').removeClass('selected');
      $radio.closest('label').addClass('selected');
    });
    $('.show_rating :radio').each(function() {
      if($(this).attr("checked")){
        $(this).closest('label').addClass('selected');
      }
    });
  });

  function alert(message, title) {
    title = title || "<?php echo TEXT_LINK_TITLE_INFORMATION; ?>";
    $.alertable.alert('<span id="alertable-title"></span><span id="alertable-content"></span>', { 
      html: true 
    });
    $('#alertable-content').html(message);
    $('#alertable-title').html(title);
  }

  <?php if (basename($PHP_SELF) != FILENAME_SHOPPING_CART && !strpos($PHP_SELF, 'checkout')) { ?>
    $(function() {
      $('body').on('click', '#toggle_cart', function() {
        $('.toggle_cart').slideToggle('slow');
        $('.toggle_wishlist').slideUp('slow');
        ac_closing();
        return false;
      });
      $('html').on('click', function(e) {
        if (!$(e.target).closest('.toggle_cart').length > 0 ) {
          $('.toggle_cart').slideUp('slow');
        }
      });
      <?php if (DISPLAY_CART == 'false' && isset($_SESSION['new_products_id_in_cart'])) { ?>
        $('.toggle_cart').slideToggle('slow');
        timer = setTimeout(function(){$('.toggle_cart').slideUp('slow');}, 3000);
        $('.toggle_cart').mouseover(function() {clearTimeout(timer);});
      <?php } ?>
    });     

    $(function() {
      $('body').on('click', '#toggle_wishlist', function() {
        $('.toggle_wishlist').slideToggle('slow');
        $('.toggle_cart').slideUp('slow');
        ac_closing();
        return false;
      });
      $('html').on('click', function(e) {
        if (!$(e.target).closest('.toggle_wishlist').length > 0 ) {
          $('.toggle_wishlist').slideUp('slow');
        }
      });
      <?php if (DISPLAY_CART == 'false' && isset($_SESSION['new_products_id_in_wishlist'])) { ?>
        $('.toggle_wishlist').slideToggle('slow');
        timer = setTimeout(function(){$('.toggle_wishlist').slideUp('slow');}, 3000);
        $('.toggle_wishlist').mouseover(function() {clearTimeout(timer);});
      <?php } ?>
    });     
  <?php } ?>
</script>
