<?php
/* -----------------------------------------------------------------------------------------
   $Id: shopping_cart.php 16426 2025-04-30 13:50:33Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(shopping_cart.php,v 1.71 2003/02/14); www.oscommerce.com
   (c) 2003 nextcommerce (shopping_cart.php,v 1.24 2003/08/17); www.nextcommerce.org
   (c) 2006 xtCommerce (shopping_cart.php)

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contributions:
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   ab 15.08.2008 Teile vom Hamburger-Internetdienst ge�ndert
   Hamburger-Internetdienst Support Forums at www.forum.hamburger-internetdienst.de
   Stand 04.03.2012
   ---------------------------------------------------------------------------------------*/

require ('includes/application_top.php');

// create smarty elements
$smarty = new Smarty();

if (ACTIVATE_GIFT_SYSTEM == 'true' 
    && ((defined('MODULE_ORDER_TOTAL_GV_STATUS') && MODULE_ORDER_TOTAL_GV_STATUS == 'true') 
        || (defined('MODULE_ORDER_TOTAL_COUPON_STATUS') && MODULE_ORDER_TOTAL_COUPON_STATUS == 'true')
        )
    )
{
  include (DIR_WS_MODULES.'gift_cart.php');
}
if (defined('MODULE_WISHLIST_SYSTEM_STATUS') && MODULE_WISHLIST_SYSTEM_STATUS == 'true') {
  include (DIR_WS_MODULES.'wishlist.php');
}

if ($_SESSION['cart']->count_contents() > 0) {

  $smarty->assign('FORM_ACTION', xtc_draw_form('cart_quantity', xtc_href_link(FILENAME_SHOPPING_CART, 'action=update_product', $request_type))); //GTB - 2010-11-26 - fix SSL/NONSSL to request
  $smarty->assign('FORM_END', '</form>');

  $_SESSION['any_out_of_stock'] = 0;
  require (DIR_WS_MODULES.'order_details_cart.php');
  
  $_SESSION['allow_checkout'] = 'true';
  if (STOCK_CHECK == 'true') {
    if ($_SESSION['any_out_of_stock'] == 1) {
      if (STOCK_ALLOW_CHECKOUT == 'true') {
        $_SESSION['allow_checkout'] = 'true';
        $messageStack->add('shopping_cart', OUT_OF_STOCK_CAN_CHECKOUT);
      } else {
        $_SESSION['allow_checkout'] = 'false';
        $messageStack->add('shopping_cart', OUT_OF_STOCK_CANT_CHECKOUT);
      }
    } else {
      $_SESSION['allow_checkout'] = 'true';
    }
  }

  // cart requirements
  require (DIR_WS_INCLUDES.'cart_requirements.php');
    
  // cart buttons
  $smarty->assign('BUTTON_RELOAD', xtc_image_submit('button_update_cart.gif', IMAGE_BUTTON_UPDATE_CART));
  //$smarty->assign('BUTTON_CHECKOUT', '<a href="'.xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL').'">'.xtc_image_button('button_checkout.gif', IMAGE_BUTTON_CHECKOUT).'</a>');
  $smarty->assign('BUTTON_CHECKOUT', xtc_image_submit('button_checkout.gif', IMAGE_BUTTON_CHECKOUT, 'name="checkout_redirect"'));  
} else {
  // empty cart
  $smarty->assign('cart_empty', true);
  $smarty->assign('BUTTON_CONTINUE', '<a href="'.xtc_href_link(FILENAME_DEFAULT).'">'.xtc_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE).'</a>');
}

// info message cart
if ($messageStack->size('info_message_3') > 0) {
  $smarty->assign('info_message_3', $messageStack->output('info_message_3'));
}
// compatibility for old template
if ($messageStack->size('coupon_message') > 0) {
  $smarty->assign('coupon_message', $messageStack->output('coupon_message'));
}
if ($messageStack->size('coupon_message', 'success') > 0) {
	$smarty->assign('coupon_message_success', $messageStack->output('coupon_message', 'success'));
}
// coupon min order info
if (isset($cc_amount_min_order_info)) {
  $messageStack->add('shopping_cart', $cc_amount_min_order_info);
}

if ($messageStack->size('shopping_cart') > 0) {
  $smarty->assign('error_message', $messageStack->output('shopping_cart'));
}

// continue shopping link
$_SESSION['continue_link'] = $_SESSION['cart']->get_continue_shopping_link();
if (!empty($_SESSION['continue_link'])) {
  $smarty->assign('CONTINUE_LINK', $_SESSION['continue_link']);
  $smarty->assign('BUTTON_CONTINUE_SHOPPING', xtc_image_button('button_continue_shopping.gif', IMAGE_BUTTON_CONTINUE_SHOPPING));
}

if (defined('MODULE_CHECKOUT_EXPRESS_STATUS') && MODULE_CHECKOUT_EXPRESS_STATUS == 'true') {
  if (isset($_SESSION['customer_id']) && $_SESSION['customers_status']['customers_status_id'] != DEFAULT_CUSTOMERS_STATUS_ID_GUEST) {
    $express_query = xtc_db_query("SELECT *
                                     FROM ".TABLE_CUSTOMERS_CHECKOUT." 
                                    WHERE customers_id = '".(int)$_SESSION['customer_id']."'");
    if (xtc_db_num_rows($express_query) > 0) {
      $smarty->assign('BUTTON_CHECKOUT_EXPRESS', '<a href="'.xtc_href_link(FILENAME_CHECKOUT_SHIPPING, 'express=on', 'SSL').'">'.xtc_image_button('button_checkout_express.gif', IMAGE_BUTTON_CHECKOUT).'</a>');
    } else {
      $smarty->assign('ACTIVATE_EXPRESS_LINK', xtc_href_link(FILENAME_ACCOUNT_CHECKOUT_EXPRESS, 'cart=true', 'SSL'));
    }
  }
  if (MODULE_CHECKOUT_EXPRESS_CONTENT != '') {
    $smarty->assign('EXPRESS_LINK', $main->getContentLink(MODULE_CHECKOUT_EXPRESS_CONTENT, TEXT_CHECKOUT_EXPRESS_INFO_LINK, $request_type, false));
  }
}

// build breadcrumb
$breadcrumb->add(NAVBAR_TITLE_SHOPPING_CART, xtc_href_link(FILENAME_SHOPPING_CART, '', $request_type));

// include header
require (DIR_WS_INCLUDES . 'header.php');

// include boxes
$display_mode = 'shoppingcart';
require (DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');

$smarty->assign('language', $_SESSION['language']);
$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/shopping_cart.html');
$smarty->assign('main_content', $main_content);
$smarty->caching = 0;
if (!defined('RM'))
  $smarty->load_filter('output', 'note');
$smarty->display(CURRENT_TEMPLATE.'/index.html');
include ('includes/application_bottom.php');