<?php
/* -----------------------------------------------------------------------------------------
   $Id: checkout_shipping.php 15927 2024-05-31 15:19:07Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(checkout_shipping.php,v 1.15 2003/04/08); www.oscommerce.com
   (c) 2003 nextcommerce (checkout_shipping.php,v 1.20 2003/08/20); www.nextcommerce.org
   (c) 2006 xtCommerce (checkout_shipping.php 1037 2005-07-17)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c) Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
include ('includes/application_top.php');

// pre-selection the cheapest shipping option
defined('CHECK_CHEAPEST_SHIPPING_MODUL') or define('CHECK_CHEAPEST_SHIPPING_MODUL', 'false'); // default: 'false'

// show selfpickup on free shipping
defined('SHOW_SELFPICKUP_FREE') or define('SHOW_SELFPICKUP_FREE', 'false'); // default: 'false'

// include needed functions
require_once (DIR_FS_INC.'xtc_address_label.inc.php');
require_once (DIR_FS_INC.'xtc_get_address_format_id.inc.php');
require_once (DIR_FS_INC.'xtc_count_shipping_modules.inc.php');

// create smarty elements
$smarty = new Smarty();

require (DIR_WS_INCLUDES.'checkout_requirements.php');

unset ($_SESSION['tmp_oID']);

//express checkout
if (defined('MODULE_CHECKOUT_EXPRESS_STATUS') && MODULE_CHECKOUT_EXPRESS_STATUS == 'true') {
  if (isset($_GET['express']) && $_GET['express'] == 'on') {
    $express_query = xtc_db_query("SELECT checkout_shipping,
                                          checkout_shipping_address
                                     FROM ".TABLE_CUSTOMERS_CHECKOUT." 
                                    WHERE customers_id = '".(int)$_SESSION['customer_id']."'");
    if (xtc_db_num_rows($express_query) > 0) {
      $express = xtc_db_fetch_array($express_query);
      if ($express['checkout_shipping_address'] != '') {
        $_SESSION['sendto'] = $express['checkout_shipping_address'];
      }
    }
  }
}

// if no shipping destination address was selected, use the customers own address as default
if (!isset($_SESSION['sendto'])) {
	$_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
} else {
	// verify the selected shipping address
	$check_address_query = xtc_db_query("SELECT count(*) as total 
	                                       FROM ".TABLE_ADDRESS_BOOK." 
	                                      WHERE customers_id = '".(int) $_SESSION['customer_id']."' 
	                                        AND address_book_id = '".(int) $_SESSION['sendto']."'");
	$check_address = xtc_db_fetch_array($check_address_query);
	if ($check_address['total'] != '1') {
		$_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
		if (isset($_SESSION['shipping']))
			unset ($_SESSION['shipping']);
	}
}

if (!isset($_SESSION['billto'])) {
  $_SESSION['billto'] = $_SESSION['customer_default_address_id'];
}

require_once (DIR_WS_CLASSES.'order.php');
$order = new order();

// avoid hack attempts during the checkout procedure by checking the internal cartID
if (isset($_SESSION['cart']->cartID) && isset($_SESSION['cartID'])) {
  if ($_SESSION['cart']->cartID !== $_SESSION['cartID']) {
    unset($_SESSION['shipping']);
    unset($_SESSION['payment']);
  }
}

// register a random ID in the session to check throughout the checkout procedure
// against alterations in the shopping cart contents
$_SESSION['cartID'] = $_SESSION['cart']->cartID;

// if the order contains only virtual products, forward the customer to the billing page as
// a shipping address is not needed
if ($order->content_type == 'virtual' || ($order->content_type == 'virtual_weight') || ($_SESSION['cart']->count_contents_virtual() == 0)) { // GV Code added
	$_SESSION['shipping'] = false;
	$_SESSION['sendto'] = false;
	xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, xtc_get_all_get_params(), 'SSL'));
}

$total_weight = $_SESSION['cart']->show_weight();
$total_count = $_SESSION['cart']->count_contents();

if ($order->delivery['country']['iso_code_2'] != '') {
	$_SESSION['delivery_zone'] = $order->delivery['country']['iso_code_2'];
}

if (isset($order->delivery['delivery_zone']) && $order->delivery['delivery_zone'] != '') {
	$_SESSION['delivery_zone'] = $order->delivery['delivery_zone'];
}

// load all enabled shipping modules
require_once (DIR_WS_CLASSES.'shipping.php');
$shipping_modules = new shipping;

$free_shipping = false;
if (xtc_not_null(MODULE_ORDER_TOTAL_INSTALLED)) {
  require_once (DIR_WS_CLASSES . 'order_total.php');
  $order_total_modules = new order_total();
  $order_total_modules->process();
}

//express checkout
if (defined('MODULE_CHECKOUT_EXPRESS_STATUS') && MODULE_CHECKOUT_EXPRESS_STATUS == 'true') {
  if (isset($_GET['express']) && $_GET['express'] == 'on') {
    if (isset($express['checkout_shipping']) && $express['checkout_shipping'] != '') {
      if ($free_shipping === false && $express['checkout_shipping'] == 'free_free') {
        unset($express['checkout_shipping']);
      } elseif ($free_shipping === false && $express['checkout_shipping'] == 'cheapest_cheapest') {
        // get all available shipping quotes
        $quotes = $shipping_modules->quote();
        $cheapest = $shipping_modules->cheapest();
        $express['checkout_shipping'] = $cheapest['id'];
      }
      $_POST['action'] = 'process';
      $_POST['shipping'] = (($free_shipping === true) ? 'free_free' : $express['checkout_shipping']);
    }
  }
}

// process the selected shipping method
if (isset($_POST['action']) && ($_POST['action'] == 'process')) {
  $redirect_link = xtc_href_link(FILENAME_CHECKOUT_PAYMENT, xtc_get_all_get_params(), 'SSL');
  require(DIR_WS_INCLUDES.'shipping_action.php');
}

// get all available shipping quotes
$quotes = $shipping_modules->quote();

// if no shipping method has been selected, automatically select the cheapest method.
// if the modules status was changed when none were available, to save on implementing
// a javascript force-selection method, also automatically select the cheapest shipping
// method if more than one module is now enabled
if ((!isset($_SESSION['shipping']) && CHECK_CHEAPEST_SHIPPING_MODUL == 'true') || (isset($_SESSION['shipping']) && ($_SESSION['shipping'] == false) && (xtc_count_shipping_modules() > 1))) {
	$_SESSION['shipping'] = $shipping_modules->cheapest();
}

// build breadcrumb
$breadcrumb->add(NAVBAR_TITLE_1_CHECKOUT_SHIPPING, xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2_CHECKOUT_SHIPPING, xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));

// include header
require (DIR_WS_INCLUDES . 'header.php');

// include boxes
$display_mode = 'checkout';
require (DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');

$smarty->assign('FORM_ACTION', xtc_draw_form('checkout_address', xtc_href_link(FILENAME_CHECKOUT_SHIPPING, xtc_get_all_get_params(), 'SSL'), 'post', 'onSubmit="return check_form();"').xtc_draw_hidden_field('action', 'process'));
$smarty->assign('ADDRESS_LABEL', xtc_address_label($_SESSION['customer_id'], $_SESSION['sendto'], true, ' ', '<br />'));
$smarty->assign('BUTTON_ADDRESS', '<a href="'.xtc_href_link(FILENAME_CHECKOUT_SHIPPING_ADDRESS, '', 'SSL').'">'.xtc_image_button('button_change_address.gif', IMAGE_BUTTON_CHANGE_ADDRESS).'</a>');
$smarty->assign('BUTON_CONTINUE', xtc_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE));// 'BUTON_CONTINUE' to remain compatible to standard templates
$smarty->assign('BUTTON_CONTINUE', xtc_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE));
$smarty->assign('BUTTON_CHECKOUT_STEP2', xtc_image_submit('button_checkout_step2.gif', IMAGE_BUTTON_CHECKOUT_STEP2));
$smarty->assign('FORM_END', '</form>');

$backlink = xtc_href_link(FILENAME_SHOPPING_CART, '', 'NONSSL');
$smarty->assign('BUTTON_BACK', '<a href="'.$backlink.'">'.xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK).'</a>');
$smarty->assign('BUTTON_BACK_LINK', $backlink);

if (SHOW_SELFPICKUP_FREE == 'true') {
  if ($free_shipping == true) {
    $free_shipping = false;
    
    $ot_shipping = new ot_shipping();
    $quotes_array = $ot_shipping->quote();
    for ($i = 0, $n = sizeof($quotes); $i < $n; $i ++) {
      if (isset($GLOBALS[$quotes[$i]['id']])
          && is_object($GLOBALS[$quotes[$i]['id']])
          && method_exists($GLOBALS[$quotes[$i]['id']], 'display_free')
          )
      {
        if ($GLOBALS[$quotes[$i]['id']]->display_free() === true) {
          $quotes_array = array_merge($quotes_array, $shipping_modules->quote($quotes[$i]['id'], $quotes[$i]['methods'][0]['id']));
        }
      }
    }
    $quotes = $quotes_array;
  }
}

if (isset ($_SESSION['cc_id'])) {
  $coupon_query = xtc_db_query("SELECT *
                                  FROM ".TABLE_COUPONS."
                                 WHERE coupon_id = '".(int)$_SESSION['cc_id']."'
                                   AND coupon_active = 'Y'
                                   AND (restrict_to_customers = ''
                                        OR restrict_to_customers IS NULL
                                        OR FIND_IN_SET ('". (int)$_SESSION['customers_status']['customers_status_id'] ."', restrict_to_customers)
                                        )");
  if (xtc_db_num_rows($coupon_query) != 0) {
    $coupon_array = xtc_db_fetch_array($coupon_query);
    if ($coupon_array['coupon_type'] == 'S') {
      $messageStack->add('checkout_shipping', TEXT_INFO_FREE_SHIPPING_COUPON, 'success');
    }
  }
}

if ($messageStack->size('checkout_shipping') > 0) {
  $smarty->assign('error_message', $messageStack->output('checkout_shipping'));
}

if ($messageStack->size('checkout_shipping', 'success') > 0) {
  $smarty->assign('success_message', $messageStack->output('checkout_shipping', 'success'));
}
    
// build shipping block
require(DIR_WS_INCLUDES.'shipping_block.php');

$smarty->assign('language', $_SESSION['language']);
$smarty->assign('SHIPPING_BLOCK', $shipping_block);
$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/checkout_shipping.html');
$smarty->assign('main_content', $main_content);
$smarty->caching = 0;
if (!defined('RM'))
  $smarty->load_filter('output', 'note');
$smarty->display(CURRENT_TEMPLATE.'/index.html');
include ('includes/application_bottom.php');