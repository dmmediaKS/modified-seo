<?php
/* -----------------------------------------------------------------------------------------
   $Id: 10_paypal.php 15927 2024-05-31 15:19:07Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  if ($checkout_position[$current_page] == 1 || $checkout_position[$current_page] == 2) {  
    if (isset($_SESSION['paypal'])
        && (!isset($_SESSION['paypal']['payment_modules']) || $_SESSION['paypal']['payment_modules'] != 'paypalexpress.php')
        )
    {
      unset ($_SESSION['paypal']);
    }
  }