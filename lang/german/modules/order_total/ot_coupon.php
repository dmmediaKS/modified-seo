<?php
/* -----------------------------------------------------------------------------------------
   $Id: ot_coupon.php 16003 2024-07-02 16:27:47Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(t_coupon.php,v 1.1.2.2 2003/05/15); www.oscommerce.com
   (c) 2006 XT-Commerce (ot_coupon.php 899 2005-04-29)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  define('MODULE_ORDER_TOTAL_COUPON_TITLE', 'Rabatt Coupon');
  define('MODULE_ORDER_TOTAL_COUPON_HEADER', 'Gutschein / Rabatt Coupon');
  define('MODULE_ORDER_TOTAL_COUPON_DESCRIPTION', 'Rabatt Coupon');
  define('MODULE_ORDER_TOTAL_COUPON_USER_PROMPT', '');
  define('ERROR_NO_INVALID_REDEEM_COUPON', 'Ung&uuml;ltiger Gutscheincode');
  define('REDEEMED_MIN_ORDER', 'f&uuml;r Waren &uuml;ber ');
  define('REDEEMED_RESTRICTIONS', ' [Artikel / Kategorie Einschr&auml;nkungen]');
  define('TEXT_ENTER_COUPON_CODE', 'Geben Sie hier Ihren Gutscheincode ein &nbsp;&nbsp;');
  
  define('MODULE_ORDER_TOTAL_COUPON_STATUS_TITLE', 'Wert anzeigen');
  define('MODULE_ORDER_TOTAL_COUPON_STATUS_DESC', 'M&ouml;chten Sie den Wert des Rabatt Coupons anzeigen?');
  define('MODULE_ORDER_TOTAL_COUPON_SORT_ORDER_TITLE', 'Sortierreihenfolge');
  define('MODULE_ORDER_TOTAL_COUPON_SORT_ORDER_DESC', 'Anzeigereihenfolge');
  define('MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING_TITLE', 'Inklusive Versandkosten');
  define('MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING_DESC', 'Versandkosten an den Warenwert anrechnen');
  define('MODULE_ORDER_TOTAL_COUPON_INC_TAX_TITLE', 'Inklusive MwSt');
  define('MODULE_ORDER_TOTAL_COUPON_INC_TAX_DESC', 'MwSt. an den Warenwert anrechnen');
  define('MODULE_ORDER_TOTAL_COUPON_CALC_TAX_TITLE', 'MwSt. neu berechnen');
  define('MODULE_ORDER_TOTAL_COUPON_CALC_TAX_DESC', 'MwSt. neu berechnen');
  define('MODULE_ORDER_TOTAL_COUPON_TAX_CLASS_TITLE', 'MwSt.-Satz');
  define('MODULE_ORDER_TOTAL_COUPON_TAX_CLASS_DESC', 'Folgenden MwSt. Satz benutzen, wenn Sie den Rabatt Coupon als Gutschrift verwenden.');
