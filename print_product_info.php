<?php
/* -----------------------------------------------------------------------------------------
   $Id: print_product_info.php 16380 2025-03-27 15:35:47Z AGI $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(product_info.php,v 1.94 2003/05/04); www.oscommerce.com
   (c) 2003   nextcommerce (print_product_info.php,v 1.16 2003/08/25); www.nextcommerce.org
   (c) 2006 XT-Commerce

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

include ('includes/application_top.php');

// include needed functions
require_once (DIR_FS_INC.'xtc_date_long.inc.php');
require_once (DIR_FS_INC.'xtc_get_products_mo_images.inc.php');
require_once (DIR_FS_INC.'xtc_get_vpe_name.inc.php');

// create smarty elements
$info_smarty = new Smarty();
$info_smarty->assign('language', $_SESSION['language']);
$info_smarty->assign('tpl_path', DIR_WS_BASE.'templates/'.CURRENT_TEMPLATE.'/');
$info_smarty->assign('html_params', ((TEMPLATE_HTML_ENGINE == 'xhtml') ? ' '.HTML_PARAMS : ' lang="'.$_SESSION['language_code'].'"'));
$info_smarty->assign('doctype', ((TEMPLATE_HTML_ENGINE == 'xhtml') ? ' PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"' : ''));
$info_smarty->assign('charset', $_SESSION['language_charset']);
if (DIR_WS_BASE == '') {
  $info_smarty->assign('base_href', (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG);
}

if (isset($_GET['pID']) && $_GET['pID']!='') {
  $_GET['products_id'] = xtc_input_validation($_GET['pID'], 'products_id');
  $info_smarty->assign('noprint',true); 
}

if (isset($_GET['products_id']) && $_GET['products_id'] != '') {
  $product = new product((int)$_GET['products_id']);
}

if (!is_object($product) || $product->isProduct() === false || $language_not_found === true) {
  // create smarty elements
  $smarty = new Smarty();

  // product not found in database
  $site_error = TEXT_PRODUCT_NOT_FOUND;
  include (DIR_WS_MODULES.FILENAME_ERROR_HANDLER);

  // build breadcrumb
  $breadcrumb->add(NAVBAR_TITLE_ERROR, xtc_href_link(FILENAME_ERROR));

  // include header
  require (DIR_WS_INCLUDES . 'header.php');

  // include boxes
  require (DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');

  $smarty->assign('language', $_SESSION['language']);
  $smarty->caching = 0;
  if (!defined('RM'))
    $smarty->load_filter('output', 'note');
  $smarty->display(CURRENT_TEMPLATE.'/index.html');

  include ('includes/application_bottom.php');
} else {
  
  $manufacturers_array = xtc_get_manufacturers();
  if (isset($manufacturers_array[$product->data['manufacturers_id']])) {
    $manufacturer = $manufacturers_array[$product->data['manufacturers_id']];
    $image = $main->getImage($manufacturer['manufacturers_image'], 'manufacturers/', MANUFACTURER_IMAGE_SHOW_NO_IMAGE);

    $info_smarty->assign('MANUFACTURER_IMAGE', (($image != '') ? DIR_WS_BASE . $image : ''));
    $info_smarty->assign('MANUFACTURER', $manufacturer['manufacturers_name']);
    $info_smarty->assign('MANUFACTURER_LINK', xtc_href_link(FILENAME_DEFAULT, xtc_manufacturer_link($manufacturer['manufacturers_id'], $manufacturer['manufacturers_name'])));
  }

  // load all definitions from product class
  $productDataArray = $product->buildDataArray($product->data, 'info');
  foreach ($productDataArray as $key => $value) {
    $info_smarty->assign($key, $value);
  }

  /*
   * assign smarty additional variables or overwrite them
   * START
   */
  
  // show expiry date of active special products
  if ($_SESSION['customers_status']['customers_status_specials'] != '0') {
    $special_expires_date_query = "SELECT expires_date
                                     FROM ".TABLE_SPECIALS."
                                    WHERE products_id = '".$product->data['products_id']."'
                                          ".SPECIALS_CONDITIONS;
    $special_expires_date_query = xtc_db_query($special_expires_date_query);
    if (xtc_db_num_rows($special_expires_date_query) > 0) {
      $sDate = xtc_db_fetch_array($special_expires_date_query);
      $info_smarty->assign('PRODUCTS_EXPIRES', $sDate['expires_date'] != '0000-00-00 00:00:00' ? xtc_date_short($sDate['expires_date']) : '');
      $info_smarty->assign('PRODUCTS_EXPIRES_C', $sDate['expires_date'] != '0000-00-00 00:00:00' ? date('c', strtotime($sDate['expires_date'])) : '');
    }
  }

  $info_smarty->assign('PRODUCTS_FSK18', $product->data['products_fsk18'] == '1' ? 'true' : '');  
  $info_smarty->assign('PRODUCTS_DESCRIPTION', stripslashes($product->data['products_description']));
  $info_smarty->assign('PRODUCTS_SHORT_DESCRIPTION', stripslashes($product->data['products_short_description']));
  $info_smarty->assign('PRODUCTS_URL', !empty($product->data['products_url']) ? sprintf(TEXT_MORE_INFORMATION, xtc_href_link(FILENAME_REDIRECT, 'action=product&id='.$product->data['products_id'], 'NONSSL', true, false)) : '');

  // more images
  if (MO_PICS != '0') {
    $mo_images = xtc_get_products_mo_images($product->data['products_id']);
    if ($mo_images != false) {
      $more_images_data = array();
      foreach ($mo_images as $img) {
        $mo_img = $product->productImage($img['image_name'], 'info');
        $mo_img_nr = $img['image_nr'];
        if ($mo_img != '') {
          $more_images_data[$mo_img_nr] = array();
          foreach ($img as $key => $value) {
            $more_images_data[$mo_img_nr][strtoupper($key)] = $value;
          }
          $more_images_data[$mo_img_nr]['PRODUCTS_IMAGE'] = $mo_img;
        }
        foreach(auto_include(DIR_FS_CATALOG.'includes/extra/modules/product_info_mo_images/','php') as $file) require ($file);
      }
      $info_smarty->assign('more_images', $more_images_data);
    }
  }

  // product discount
  $discount = $xtPrice->xtcCheckDiscount($product->data['products_id']);
  if ($discount != '0.00') {
    $info_smarty->assign('PRODUCTS_DISCOUNT', $discount.'%');
  }

  // date available/added
  if (isset($product->data['products_date_available']) && $product->data['products_date_available'] > date('Y-m-d H:i:s')) {
    $info_smarty->assign('PRODUCTS_DATE_AVIABLE', sprintf(TEXT_DATE_AVAILABLE, xtc_date_long($product->data['products_date_available'])));
    $info_smarty->assign('PRODUCTS_DATE_AVAILABLE', sprintf(TEXT_DATE_AVAILABLE, xtc_date_long($product->data['products_date_available']))); 
  } elseif (isset($product->data['products_date_added']) && $product->data['products_date_added'] != '0000-00-00 00:00:00') {
    $info_smarty->assign('PRODUCTS_ADDED', sprintf(TEXT_DATE_ADDED, xtc_date_long($product->data['products_date_added'])));
  }

  /*
   * assign smarty additional variables or overwrite them
   * END
   */
 
  //include modules
  if ($_SESSION['customers_status']['customers_status_graduated_prices'] == '1') {
    include (DIR_WS_MODULES.FILENAME_GRADUATED_PRICE);
  }

  if ($_SESSION['customers_status']['customers_status_read_reviews'] == '1') {
    $products_reviews_count = $product->getReviewsCount();
    $info_smarty->assign('PRODUCTS_AVERAGE_RATING', $product->getReviewsAverage());
    $info_smarty->assign('PRODUCTS_RATING_COUNT', $product->getReviewsCount());
  }

  include (DIR_WS_MODULES.FILENAME_PRODUCTS_MEDIA);
  include (DIR_WS_MODULES.'product_tags.php');
  include (DIR_WS_MODULES.'product_attributes.php');
  
  $module_content = array();
  if (isset($products_options_data) && is_array($products_options_data)) {  
    $key = 0;
    foreach ($products_options_data as $attributes) {
      foreach ($attributes['DATA'] as $value) {
        $module_content[$key] = $value;
        $module_content[$key]['GROUP'] = $attributes['NAME'];
        $module_content[$key]['NAME'] = $value['TEXT'] . ((isset($value['PREFIX'])) ? ' ('.$value['PREFIX'].$value['PRICE'].')' : '');
        if (!isset($_GET['pID']) || $_GET['pID'] == '') {
          $module_content[$key]['CHECKED'] = 0;
        }
        $key ++;
      }
    }
    
    $info_smarty->assign('module_content', $module_content);
  }
  
  $canonical_link = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($product->data['products_id'], $product->data['products_name']), $request_type, false);
  $info_smarty->assign('CanonicalLink', $canonical_link);
 
  $info_smarty->caching = 0;
  $info_smarty->display(CURRENT_TEMPLATE.'/module/print_product_info.html');
}