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

  define('MODULE_ORDER_TOTAL_COUPON_TITLE', 'Discount Coupon');
  define('MODULE_ORDER_TOTAL_COUPON_HEADER', 'Gift Voucher / Discount Coupon');
  define('MODULE_ORDER_TOTAL_COUPON_DESCRIPTION', 'Discount Coupon');
  define('MODULE_ORDER_TOTAL_COUPON_USER_PROMPT', '');
  define('ERROR_NO_INVALID_REDEEM_COUPON', 'Invalid Coupon Code');
  define('REDEEMED_MIN_ORDER', 'on orders over ');  
  define('REDEEMED_RESTRICTIONS', ' [Product-Category restrictions apply]');  
  define('TEXT_ENTER_COUPON_CODE', 'Enter Redeem Code&nbsp;&nbsp;');
  
  define('MODULE_ORDER_TOTAL_COUPON_STATUS_TITLE', 'Display Total');
  define('MODULE_ORDER_TOTAL_COUPON_STATUS_DESC', 'Do you want to display the Discount Coupon value?');
  define('MODULE_ORDER_TOTAL_COUPON_SORT_ORDER_TITLE', 'Sort Order');
  define('MODULE_ORDER_TOTAL_COUPON_SORT_ORDER_DESC', 'Display Order');
  define('MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING_TITLE', 'Include Shipping');
  define('MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING_DESC', 'Include Shipping in calculation');
  define('MODULE_ORDER_TOTAL_COUPON_INC_TAX_TITLE', 'Include Tax');
  define('MODULE_ORDER_TOTAL_COUPON_INC_TAX_DESC', 'Include Tax in calculation.');
  define('MODULE_ORDER_TOTAL_COUPON_CALC_TAX_TITLE', 'Re-calculate Tax');
  define('MODULE_ORDER_TOTAL_COUPON_CALC_TAX_DESC', 'Re-Calculate Tax');
  define('MODULE_ORDER_TOTAL_COUPON_TAX_CLASS_TITLE', 'Tax Class');
  define('MODULE_ORDER_TOTAL_COUPON_TAX_CLASS_DESC', 'Use the following tax class when treating Discount Coupon as Credit Note.');
