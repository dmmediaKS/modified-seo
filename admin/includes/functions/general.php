<?php
  /* --------------------------------------------------------------
   $Id: general.php 16436 2025-05-05 18:02:04Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.156 2003/05/29); www.oscommerce.com
   (c) 2003 nextcommerce (general.php,v 1.35 2003/08/1); www.nextcommerce.org
   (c) 2006 XT-Commerce (general.php 1316 2005-10-21)

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contributions:

   Customers Status v3.x (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Enable_Disable_Categories 1.3 Autor: Mikel Williams | mikel@ladykatcostumes.com

   Category Descriptions (Version: 1.5 MS2) Original Author: Brian Lowe <blowe@wpcusrgrp.org> | Editor: Lord Illicious <shaolin-venoms@illicious.net>

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c) Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Released under the GNU General Public License
   --------------------------------------------------------------*/
   
  defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );

  /**
   * clear_string()
   *
   * @param mixed $value
   * @return
   */
  require_once(DIR_FS_INC . 'clear_string.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_parse_input_field_data()
   *
   * @param mixed $data
   * @param mixed $parse
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_parse_input_field_data.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_output_string()
   *
   * @param mixed $string
   * @param bool $translate
   * @param bool $protected
   * @return
  */
  function xtc_output_string($string, $translate = false, $protected = false) {
    if ($protected == true) {
      return encode_htmlspecialchars($string);
    } else {
      if ($translate == false) {
        return xtc_parse_input_field_data($string, array('"' => '&quot;'));
      } else {
        return xtc_parse_input_field_data($string, $translate);
      }
    }
  }

  /**
   * check_stock()
   *
   * @param mixed $products_id
   * @return
   */
  function check_stock($products_id) {
    $stock_query = xtc_db_query("SELECT products_quantity
                                   FROM ".TABLE_PRODUCTS."
                                  WHERE products_id = '".(int)$products_id."'");
    $stock_values = xtc_db_fetch_array($stock_query);
    if ($stock_values['products_quantity'] <= STOCK_REORDER_LEVEL) {
      $stock_flag = 'true';
      $stock_warn = '<li>'.TEXT_WARN_MAIN.'</li>';
      $attribute_stock_query = xtc_db_query("SELECT attributes_stock,
                                                    options_values_id
                                               FROM ".TABLE_PRODUCTS_ATTRIBUTES."
                                              WHERE products_id = '".(int)$products_id."'");
      while ($attribute_stock_values = xtc_db_fetch_array($attribute_stock_query)) {
        if ($attribute_stock_values['attributes_stock'] <= STOCK_REORDER_LEVEL) {
          $stock_flag = 'true';
          $which_attribute_query = xtc_db_query("SELECT products_options_values_name
                                                   FROM ".TABLE_PRODUCTS_OPTIONS_VALUES."
                                                  WHERE products_options_values_id = '".$attribute_stock_values['options_values_id']."'
                                                    AND language_id = '".(int)$_SESSION['languages_id']."'");
          if (xtc_db_num_rows($which_attribute_query) == 1) {
            $which_attribute = xtc_db_fetch_array($which_attribute_query);
            $stock_warn .= '<li>'.$which_attribute['products_options_values_name'].'</li>';
          }
        }
      }
    }
    
    if (isset($stock_flag) && $stock_flag == 'true' && $products_id != '') {
      return '<div class="stock_warn"><ul>'.TEXT_WARN.$stock_warn.'</ul></div>';
    }
  }

  /**
   * xtc_check_permission()
   *
   * @param mixed $pagename
   * @return
   */
  function xtc_check_permission($pagename) {
    $permit_array = array(
      'start',
      'support'
    );
    if ($pagename != 'index') {
      $access_permission = get_admin_access($_SESSION['customer_id']);
      if ($_SESSION['customers_status']['customers_status_id'] == '0'
          && ((isset($access_permission[$pagename]) 
               && $access_permission[$pagename] == '1'
               ) || in_array($pagename, $permit_array)
              )
          )
      {
        return true;
      }
    }
    xtc_redirect(xtc_catalog_href_link(FILENAME_LOGIN));
  }

  /**
   * xtc_redirect()
   *
   * @param mixed $url
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_redirect.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_get_path()
   *
   * @param string $current_category_id
   * @return
   */
  function xtc_get_path($current_category_id = '', $clean = false) {
    global $cPath_array;
    
    if (empty($current_category_id)) {
      $cPath_new = implode('_', (array)$cPath_array);
    } else {
      if (sizeof($cPath_array) == 0) {
        $cPath_new = $current_category_id;
      } else {
        $cPath_new = '';
        $last_category_query = xtc_db_query("SELECT parent_id
                                               FROM ".TABLE_CATEGORIES."
                                              WHERE categories_id = '".(int)$cPath_array[(sizeof($cPath_array) - 1)]."'");
        $last_category = xtc_db_fetch_array($last_category_query);
        
        $current_category_query = xtc_db_query("SELECT parent_id
                                                  FROM ".TABLE_CATEGORIES."
                                                 WHERE categories_id = '".(int)$current_category_id."'");
        $current_category = xtc_db_fetch_array($current_category_query);
                        
        if (isset($last_category['parent_id']) 
            && isset($current_category['parent_id']) 
            && $last_category['parent_id'] == $current_category['parent_id']
            )
        {
          for ($i = 0, $n = sizeof($cPath_array) - 1; $i < $n; $i ++) {
            $cPath_new .= '_'.$cPath_array[$i];
          }
        } else {
          for ($i = 0, $n = sizeof($cPath_array); $i < $n; $i ++) {
            $cPath_new .= '_'.$cPath_array[$i];
          }
        }
        $cPath_new .= '_'.$current_category_id;
        if (substr($cPath_new, 0, 1) == '_') {
          $cPath_new = substr($cPath_new, 1);
        }
      }
    }
    
    if ($clean) {
      $cleanPath = explode('0_', $cPath_new);
      $cleanPath = array_reverse($cleanPath);
      return $cleanPath[0];
    }
    
    return 'cPath='.$cPath_new;
  }

  /**
   * xtc_get_all_get_params()
   *
   * @param string $exclude_array
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_get_all_get_params.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_date_long()
   *
   * @param mixed $raw_date
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_date_long.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_date_short()
   *
   * @param mixed $raw_date
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_date_short.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_datetime_short()
   *
   * @param mixed $raw_datetime
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_datetime_short.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_cfg_get_category_tree()
   *
   * @param string $category_id
   * @return
   */
  function xtc_cfg_get_category_tree($category_id, $key = '') {
    $name = (($key) ? 'configuration['.$key.']' : 'configuration_value');
    return xtc_draw_pull_down_menu($name, xtc_get_category_tree(), $category_id);
  }

  /**
   * xtc_get_category_tree()
   *
   * @param string $parent_id
   * @param string $spacing
   * @param string $exclude
   * @param string $category_tree_array
   * @param bool $include_itself
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_get_category_tree.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_draw_products_pull_down()
   *
   * @param mixed $name
   * @param string $parameters
   * @param string $exclude
   * @return
   */
  function xtc_draw_products_pull_down($name, $parameters = '', $exclude = '', $add_price = true, $add_model = true) {
    global $xtPrice;
    
    if (empty($exclude)) {
      $exclude = array ();
    }
    $select_string = '<select name="'.$name.'"';
    if ($parameters) {
      $select_string .= ' '.$parameters;
    }
    $select_string .= '>';

    $products_query = xtc_db_query("SELECT p.products_id,
                                           p.products_model,
                                           pd.products_name,
                                           p.products_tax_class_id,
                                           p.products_price
                                      FROM ".TABLE_PRODUCTS." p
                                      JOIN ".TABLE_PRODUCTS_DESCRIPTION." pd
                                           ON p.products_id = pd.products_id
                                              AND pd.language_id = '".(int)$_SESSION['languages_id']."'
                                  ORDER BY pd.products_name"
                                  );
    while ($products = xtc_db_fetch_array($products_query)) {
      if (!in_array($products['products_id'], $exclude)) {
        //brutto admin:
        if (PRICE_IS_BRUTTO == 'true') {
          $products['products_price'] = xtc_round($products['products_price'] * ((100 + xtc_get_tax_rate($products['products_tax_class_id'])) / 100), PRICE_PRECISION);
        }
        $products_price = $add_price ? ' ('.trim($xtPrice->xtcFormat($products['products_price'],true)).')' : '';
        $products_model = $add_model ? ' ['.TEXT_GLOBAL_PRODUCTS_MODEL.': '.$products['products_model'].']' : '';
        $select_string .= '<option value="'.$products['products_id'].'">'.$products['products_name'].$products_price.$products_model.'</option>';
      }
    }
    $select_string .= '</select>';
    return $select_string;
  }

  /**
   * xtc_info_image()
   *
   * @param mixed $image
   * @param mixed $alt
   * @param string $width
   * @param string $height
   * @param string $params
   * @return
   */
  function xtc_info_image($image, $alt, $width = '', $height = '', $params = '') {
    if (($image) && (file_exists(DIR_FS_CATALOG_IMAGES.$image))) {
      $image .= '?t='.filemtime(DIR_FS_CATALOG_IMAGES.$image);
      $image = xtc_image(DIR_WS_CATALOG_IMAGES.$image, $alt, $width, $height, $params);
    } else {
      $image = TEXT_IMAGE_NONEXISTENT;
    }
    return $image;
  }

  /**
   * xtc_info_image_c()
   *
   * @param mixed $image
   * @param mixed $alt
   * @param string $width
   * @param string $height
   * @param string $params
   * @return
   */
  function xtc_info_image_c($image, $alt, $width = '', $height = '', $params = '') {
    if (($image) && (file_exists(DIR_FS_CATALOG_IMAGES.'categories/'.$image))) {
      $image .= '?t='.filemtime(DIR_FS_CATALOG_IMAGES.'categories/'.$image);
      $image = xtc_image(DIR_WS_CATALOG_IMAGES.'categories/'.$image, $alt, $width, $height, $params);
    } else {
      $image = TEXT_IMAGE_NONEXISTENT;
    }
    return $image;
  }

  /**
   * xtc_product_thumb_image()
   *
   * @param mixed $image
   * @param mixed $alt
   * @param string $width
   * @param string $height
   * @param string $params
   * @return
   */
  function xtc_product_thumb_image($image, $alt, $width = '', $height = '', $params = '') {
    if (($image) && (file_exists(DIR_FS_CATALOG_THUMBNAIL_IMAGES.$image))) {
      $image .= '?t='.filemtime(DIR_FS_CATALOG_THUMBNAIL_IMAGES.$image);
      $image = xtc_image(DIR_WS_CATALOG_THUMBNAIL_IMAGES.$image, $alt, $width, $height, $params);
    } else {
      $image = TEXT_IMAGE_NONEXISTENT;
    }
    return $image;
  }

  /**
   * xtc_break_string()
   *
   * @param mixed $string
   * @param mixed $len
   * @param string $break_char
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_break_string.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_get_country_name()
   *
   * @param mixed $country_id
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_get_country_name.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_get_zone_name()
   *
   * @param mixed $country_id
   * @param mixed $zone_id
   * @param mixed $default_zone
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_get_zone_name.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_browser_detect()
   *
   * @param mixed $component
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_browser_detect.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_tax_classes_pull_down()
   *
   * @param mixed $parameters
   * @param string $selected
   * @return
   */
  function xtc_tax_classes_pull_down($parameters, $selected = '') {
    require_once(DIR_FS_INC.'parse_multi_language_value.inc.php');
    
    $select_string = '<select '.$parameters.'>';
    $classes_query = xtc_db_query("SELECT tax_class_id,
                                          tax_class_title
                                     FROM ".TABLE_TAX_CLASS."
                                 ORDER BY sort_order, tax_class_id");
    while ($classes = xtc_db_fetch_array($classes_query)) {
      $select_string .= '<option value="'.$classes['tax_class_id'].'"';
      if ($selected == $classes['tax_class_id'])
        $select_string .= ' SELECTED';
      $select_string .= '>'.parse_multi_language_value($classes['tax_class_title'], $_SESSION['language_code']).'</option>';
    }
    $select_string .= '</select>';
    return $select_string;
  }

  /**
   * xtc_geo_zones_pull_down()
   *
   * @param mixed $parameters
   * @param string $selected
   * @return
   */
  function xtc_geo_zones_pull_down($parameters, $selected = '') {
    require_once(DIR_FS_INC.'parse_multi_language_value.inc.php');
    
    $select_string = '<select '.$parameters.'>';
    $zones_query = xtc_db_query("SELECT geo_zone_id,
                                        geo_zone_name
                                   FROM ".TABLE_GEO_ZONES."
                               ORDER BY geo_zone_name");
    while ($zones = xtc_db_fetch_array($zones_query)) {
      $select_string .= '<option value="'.$zones['geo_zone_id'].'"';
      if ($selected == $zones['geo_zone_id'])
        $select_string .= ' SELECTED';
      $select_string .= '>'.parse_multi_language_value($zones['geo_zone_name'], $_SESSION['language_code']).'</option>';
    }
    $select_string .= '</select>';
    return $select_string;
  }

  /**
   * xtc_address_format()
   *
   * @param mixed $address_format_id
   * @param mixed $address
   * @param mixed $html
   * @param mixed $boln
   * @param mixed $eoln
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_address_format.inc.php'); // Use existing function from "/inc/" folder
  
  /**
   * xtc_get_zone_code()
   *
   * @param mixed $country
   * @param mixed $zone
   * @param mixed $def_state
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_get_zone_code.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_get_uprid()
   *
   * @param mixed $prid
   * @param mixed $params
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_get_uprid.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_get_prid()
   *
   * @param mixed $uprid
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_get_prid.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_get_languages()
   *
   * @return
   */
  function xtc_get_languages() {
    static $languages_array;
    
    if (!isset($languages_array)) {
      $languages_array = array();
      $languages_query = xtc_db_query("SELECT *,
                                              languages_id as id
                                         FROM ".TABLE_LANGUAGES."
                                        WHERE status_admin = '1'
                                     ORDER BY sort_order");
  
      while ($languages = xtc_db_fetch_array($languages_query)) {
        $languages_array[] = $languages;
      }
    }
    
    return $languages_array;
  }

  /**
   * xtc_cfg_pull_down_language_code()
   *
   * @param mixed $language_code
   * @param string $key
   * @return
   */
  function xtc_cfg_pull_down_language_code($language_code, $key = '') {
    $name = (($key) ? 'configuration['.$key.']' : 'configuration_value');
    
    $languages_array = array();
    $languages_query = xtc_db_query("SELECT languages_id,
                                            name,
                                            code,
                                            image,
                                            directory
                                       FROM ".TABLE_LANGUAGES."
                                      WHERE status = '1'
                                   ORDER BY sort_order");

    while ($languages = xtc_db_fetch_array($languages_query)) {
      $languages_array[] = array (
        'id' => $languages['code'],
        'text' => $languages['name'],
      );
    }
    
    return xtc_draw_pull_down_menu($name, $languages_array, $language_code);
  }

  /**
   * xtc_get_category_data()
   *
   * @param mixed $category_id
   * @param mixed $language_id
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_get_category_data.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_get_categories_name()
   *
   * @param mixed $category_id
   * @param mixed $language_id
   * @return
   */
  function xtc_get_categories_name($category_id, $language_id) {
    $category_data = xtc_get_category_data($category_id, $language_id);
    if (isset($category_data['categories_name'])) {
      return $category_data['categories_name'];
    }
  }

  /**
   * xtc_get_categories_heading_title()
   *
   * @param mixed $category_id
   * @param mixed $language_id
   * @return
   */
  function xtc_get_categories_heading_title($category_id, $language_id) {
    $category_data = xtc_get_category_data($category_id, $language_id);
    if (isset($category_data['categories_heading_title'])) {
      return $category_data['categories_heading_title'];
    }
  }

  /**
   * xtc_get_categories_description()
   *
   * @param mixed $category_id
   * @param mixed $language_id
   * @return
   */
  function xtc_get_categories_description($category_id, $language_id) {
    $category_data = xtc_get_category_data($category_id, $language_id);
    if (isset($category_data['categories_description'])) {
      return $category_data['categories_description'];
    }
  }

  /**
   * xtc_get_categories_meta_title()
   *
   * @param mixed $category_id
   * @param mixed $language_id
   * @return
   */
  function xtc_get_categories_meta_title($category_id, $language_id) {
    $category_data = xtc_get_category_data($category_id, $language_id);
    if (isset($category_data['categories_meta_title'])) {
      return $category_data['categories_meta_title'];
    }
  }

  /**
   * xtc_get_categories_meta_description()
   *
   * @param mixed $category_id
   * @param mixed $language_id
   * @return
   */
  function xtc_get_categories_meta_description($category_id, $language_id) {
    $category_data = xtc_get_category_data($category_id, $language_id);
    if (isset($category_data['categories_meta_description'])) {
      return $category_data['categories_meta_description'];
    }
  }

  /**
   * xtc_get_categories_meta_keywords()
   *
   * @param mixed $category_id
   * @param mixed $language_id
   * @return
   */
  function xtc_get_categories_meta_keywords($category_id, $language_id) {
    $category_data = xtc_get_category_data($category_id, $language_id);
    if (isset($category_data['categories_meta_keywords'])) {
      return $category_data['categories_meta_keywords'];
    }
  }

  /**
   * xtc_get_orders_status_name()
   *
   * @param mixed $orders_status_id
   * @param string $language_id
   * @return
   */
  function xtc_get_orders_status_name($orders_status_id, $language_id = '') {
    if (!$language_id) {
      $language_id = $_SESSION['languages_id'];
    }
    $orders_status_query = xtc_db_query("SELECT orders_status_name
                                           FROM ".TABLE_ORDERS_STATUS."
                                          WHERE orders_status_id = '".(int)$orders_status_id."'
                                            AND language_id = '".(int)$language_id."'");
    if (xtc_db_num_rows($orders_status_query) > 0) {
      $orders_status = xtc_db_fetch_array($orders_status_query);
      return $orders_status['orders_status_name'];
    }
  }

  /**
   * xtc_get_cross_sell_name()
   *
   * @param mixed $cross_sell_group
   * @param string $language_id
   * @return
   */
  require_once(DIR_FS_INC . 'get_cross_sell_name.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_get_shipping_status_name()
   *
   * @param mixed $shipping_status_id
   * @param string $language_id
   * @return
   */
  function xtc_get_shipping_status_name($shipping_status_id, $language_id = '') {
    static $shipping_status_array;
    
    if (!isset($shipping_status_array)) {
      $shipping_status_array = array();
    }
    
    if (!$language_id) {
      $language_id = (int)$_SESSION['languages_id'];
    }
    
    if (!isset($shipping_status_array[$language_id][$shipping_status_id])) {
      $shipping_status_array[$language_id][$shipping_status_id] = '';
      $shipping_status_query = xtc_db_query("SELECT shipping_status_name
                                               FROM ".TABLE_SHIPPING_STATUS."
                                              WHERE shipping_status_id = '".(int)$shipping_status_id."'
                                                AND language_id = '".(int)$language_id."'
                                           ORDER BY sort_order");
      if (xtc_db_num_rows($shipping_status_query) > 0) {
        $shipping_status = xtc_db_fetch_array($shipping_status_query);
        $shipping_status_array[$language_id][$shipping_status_id] = $shipping_status['shipping_status_name'];
      }
    }
    
    return $shipping_status_array[$language_id][$shipping_status_id];
  }

  /**
   * xtc_get_orders_status()
   *
   * @return
   */
  function xtc_get_orders_status() {
    $orders_status_array = array ();
    $orders_status_query = xtc_db_query("SELECT orders_status_id,
                                                orders_status_name
                                           FROM ".TABLE_ORDERS_STATUS."
                                          WHERE language_id = '".(int)$_SESSION['languages_id']."'
                                       ORDER BY sort_order");
    while ($orders_status = xtc_db_fetch_array($orders_status_query)) {
      $orders_status_array[] = array ('id' => $orders_status['orders_status_id'], 'text' => $orders_status['orders_status_name']);
    }
    return $orders_status_array;
  }

  /**
   * xtc_get_cross_sell_groups()
   *
   * @return
   */
  function xtc_get_cross_sell_groups() {
    $cross_sell_array = array ();
    $cross_sell_array[] = array ('id' => '', 'text' => TEXT_NONE);
    $cross_sell_query = xtc_db_query("SELECT products_xsell_grp_name_id,
                                             groupname
                                        FROM ".TABLE_PRODUCTS_XSELL_GROUPS."
                                       WHERE language_id = '".(int)$_SESSION['languages_id']."'
                                    ORDER BY products_xsell_grp_name_id");
    while ($cross_sell = xtc_db_fetch_array($cross_sell_query)) {
      $cross_sell_array[] = array ('id' => $cross_sell['products_xsell_grp_name_id'], 'text' => $cross_sell['groupname']);
    }
    return $cross_sell_array;
  }

  /**
   * xtc_get_products_vpe_name()
   *
   * @param mixed $products_vpe_id
   * @param string $language_id
   * @return
   */
  function xtc_get_products_vpe_name($products_vpe_id, $language_id = '') {
    if (!$language_id) {
      $language_id = (int)$_SESSION['languages_id'];
    }
    $products_vpe_query = xtc_db_query("SELECT products_vpe_name
                                          FROM ".TABLE_PRODUCTS_VPE."
                                         WHERE products_vpe_id = '".(int)$products_vpe_id."'
                                           AND language_id = '".(int)$language_id."'");
    if (xtc_db_num_rows($products_vpe_query) > 0) {
      $products_vpe = xtc_db_fetch_array($products_vpe_query);
      return $products_vpe['products_vpe_name'];
    }
  }

  /**
   * xtc_get_shipping_status()
   *
   * @return
   */
  function xtc_get_shipping_status() {
    $shipping_status_array = array ();
    $shipping_status_query = xtc_db_query("SELECT shipping_status_id,
                                                  shipping_status_name
                                             FROM ".TABLE_SHIPPING_STATUS."
                                            WHERE language_id = '".(int)$_SESSION['languages_id']."'
                                         ORDER BY sort_order");
    while ($shipping_status = xtc_db_fetch_array($shipping_status_query)) {
      $shipping_status_array[] = array ('id' => $shipping_status['shipping_status_id'], 'text' => $shipping_status['shipping_status_name']);
    }
    return $shipping_status_array;
  }

  /**
   * xtc_get_products_name()
   *
   * @param mixed $product_id
   * @param integer $language_id
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_get_products_name.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_get_products_description()
   *
   * @param mixed $product_id
   * @param mixed $language_id
   * @return
   */
  function xtc_get_products_description($product_id, $language_id) {
    $product_query = xtc_db_query("SELECT products_description
                                     FROM ".TABLE_PRODUCTS_DESCRIPTION."
                                    WHERE products_id = '".(int)$product_id."'
                                      AND language_id = '".(int)$language_id."'");
    if (xtc_db_num_rows($product_query) > 0) {
      $product = xtc_db_fetch_array($product_query);
      return $product['products_description'];
    }
  }

  /**
   * xtc_get_products_short_description()
   *
   * @param mixed $product_id
   * @param mixed $language_id
   * @return
   */
  function xtc_get_products_short_description($product_id, $language_id) {
    $product_query = xtc_db_query("SELECT products_short_description
                                     FROM ".TABLE_PRODUCTS_DESCRIPTION."
                                    WHERE products_id = '".(int)$product_id."'
                                      AND language_id = '".(int)$language_id."'");
    if (xtc_db_num_rows($product_query) > 0) {
      $product = xtc_db_fetch_array($product_query);
      return $product['products_short_description'];
    }
  }

  /**
   * xtc_get_products_keywords()
   *
   * @param mixed $product_id
   * @param mixed $language_id
   * @return
   */
  function xtc_get_products_keywords($product_id, $language_id) {
    $product_query = xtc_db_query("SELECT products_keywords
                                     FROM ".TABLE_PRODUCTS_DESCRIPTION."
                                    WHERE products_id = '".(int)$product_id."'
                                      AND language_id = '".(int)$language_id."'");
    if (xtc_db_num_rows($product_query) > 0) {
      $product = xtc_db_fetch_array($product_query);
      return $product['products_keywords'];
    }
  }

  /**
   * xtc_get_products_meta_title()
   *
   * @param mixed $product_id
   * @param mixed $language_id
   * @return
   */
  function xtc_get_products_meta_title($product_id, $language_id) {
    $product_query = xtc_db_query("SELECT products_meta_title
                                     FROM ".TABLE_PRODUCTS_DESCRIPTION."
                                    WHERE products_id = '".(int)$product_id."'
                                      AND language_id = '".(int)$language_id."'");
    if (xtc_db_num_rows($product_query) > 0) {
      $product = xtc_db_fetch_array($product_query);
      return $product['products_meta_title'];
    }
  }

  /**
   * xtc_get_products_meta_description()
   *
   * @param mixed $product_id
   * @param mixed $language_id
   * @return
   */
  function xtc_get_products_meta_description($product_id, $language_id) {
    $product_query = xtc_db_query("SELECT products_meta_description
                                     FROM ".TABLE_PRODUCTS_DESCRIPTION."
                                    WHERE products_id = '".(int)$product_id."'
                                      AND language_id = '".(int)$language_id."'");
    if (xtc_db_num_rows($product_query) > 0) {
      $product = xtc_db_fetch_array($product_query);
      return $product['products_meta_description'];
    }
  }

  /**
   * xtc_get_products_meta_keywords()
   *
   * @param mixed $product_id
   * @param mixed $language_id
   * @return
   */
  function xtc_get_products_meta_keywords($product_id, $language_id) {
    $product_query = xtc_db_query("SELECT products_meta_keywords
                                     FROM ".TABLE_PRODUCTS_DESCRIPTION."
                                    WHERE products_id = '".(int)$product_id."'
                                      AND language_id = '".(int)$language_id."'");
    if (xtc_db_num_rows($product_query) > 0) {
      $product = xtc_db_fetch_array($product_query);
      return $product['products_meta_keywords'];
    }
  }

  /**
   * xtc_get_products_url()
   *
   * @param mixed $product_id
   * @param mixed $language_id
   * @return
   */
  function xtc_get_products_url($product_id, $language_id) {
    $product_query = xtc_db_query("SELECT products_url
                                     FROM ".TABLE_PRODUCTS_DESCRIPTION."
                                    WHERE products_id = '".(int)$product_id."'
                                      AND language_id = '".(int)$language_id."'");
    if (xtc_db_num_rows($product_query) > 0) {
      $product = xtc_db_fetch_array($product_query);
      return $product['products_url'];
    }
  }

  /**
   * xtc_get_manufacturer_url()
   *
   * @param mixed $manufacturer_id
   * @param mixed $language_id
   * @return
   */
  function xtc_get_manufacturer_url($manufacturer_id, $language_id) {
    $manufacturer_query = xtc_db_query("SELECT manufacturers_url
                                          FROM ".TABLE_MANUFACTURERS_INFO."
                                         WHERE manufacturers_id = '".(int)$manufacturer_id."'
                                           AND languages_id = '".(int)$language_id."'");
    if (xtc_db_num_rows($manufacturer_query) > 0) {
      $manufacturer = xtc_db_fetch_array($manufacturer_query);
      return $manufacturer['manufacturers_url'];
    }
  }

  /**
   * xtc_get_countries()
   *
   * @param string $default
   * @param int $status
   * @return
   */
  function xtc_get_countries($default = '', $status = '') {
    $status = ($status != '') ? " WHERE status = '" . (int)$status ."' " : '';
    $countries_array = array ();
    if ($default) {
      $countries_array[] = array ('id' => STORE_COUNTRY, 'text' => $default);
    }
    $countries_query = xtc_db_query("SELECT countries_id,
                                            countries_name
                                       FROM ".TABLE_COUNTRIES."
                                            ".$status."
                                   ORDER BY sort_order ASC, countries_name");
    while ($countries = xtc_db_fetch_array($countries_query)) {
      $countries_array[] = array ('id' => $countries['countries_id'], 'text' => $countries['countries_name']);
    }
    return $countries_array;
  }

  /**
   * xtc_prepare_country_zones_pull_down()
   *
   * @param string $country_id
   * @return
   */
  function xtc_prepare_country_zones_pull_down($country_id = '') {
    // preset the width of the drop-down for Netscape
    $pre = '';
    if ((!xtc_browser_detect('MSIE')) && (xtc_browser_detect('Mozilla/4'))) {
      for ($i = 0; $i < 45; $i ++)
        $pre .= '&nbsp;';
    }
    $zones = xtc_get_country_zones($country_id);
    if (sizeof($zones) > 0) {
      $zones_select = array (array ('id' => '', 'text' => PLEASE_SELECT));
      $zones = array_merge($zones_select, $zones);
    } else {
      $zones = array (array ('id' => '', 'text' => TYPE_BELOW));
      // create dummy options for Netscape to preset the height of the drop-down
      if ((!xtc_browser_detect('MSIE')) && (xtc_browser_detect('Mozilla/4'))) {
        for ($i = 0; $i < 9; $i ++) {
          $zones[] = array ('id' => '', 'text' => $pre);
        }
      }
    }
    return $zones;
  }

  /**
   * xtc_get_address_formats()
   *
   * @return
   */
  function xtc_get_address_formats() {
    $address_format_query = xtc_db_query("SELECT address_format_id
                                            FROM ".TABLE_ADDRESS_FORMAT."
                                        ORDER BY address_format_id");
    $address_format_array = array ();
    while ($address_format_values = xtc_db_fetch_array($address_format_query)) {
      $address_format_array[] = array ('id' => $address_format_values['address_format_id'], 'text' => $address_format_values['address_format_id']);
    }
    return $address_format_array;
  }

  /**
   * xtc_cfg_pull_down_country_list()
   *
   * @param mixed $country_id
   * @return
   */
  function xtc_cfg_pull_down_country_list($country_id, $key = '') {
    $name = (($key) ? 'configuration['.$key.']' : 'configuration_value');
    return xtc_draw_pull_down_menu($name, xtc_get_countries(), $country_id);
  }

  /**
   * xtc_cfg_pull_down_zone_list()
   *
   * @param mixed $zone_id
   * @return
   */
  function xtc_cfg_pull_down_zone_list($zone_id, $key = '') {
    $name = (($key) ? 'configuration['.$key.']' : 'configuration_value');
    return xtc_draw_pull_down_menu($name, xtc_get_country_zones(STORE_COUNTRY), $zone_id);
  }

  /**
   * xtc_cfg_pull_down_tax_classes()
   *
   * @param mixed $tax_class_id
   * @param string $key
   * @return
   */
  function xtc_cfg_pull_down_tax_classes($tax_class_id, $key = '') {
    require_once(DIR_FS_INC.'parse_multi_language_value.inc.php');

    $name = (($key) ? 'configuration['.$key.']' : 'configuration_value');
    $tax_class_array = array (array ('id' => '0', 'text' => TEXT_NONE));
    $tax_class_query = xtc_db_query("SELECT tax_class_id,
                                            tax_class_title
                                       FROM ".TABLE_TAX_CLASS."
                                   ORDER BY sort_order, tax_class_id");
    while ($tax_class = xtc_db_fetch_array($tax_class_query)) {
      $tax_class_array[] = array ('id' => $tax_class['tax_class_id'], 'text' => parse_multi_language_value($tax_class['tax_class_title'], $_SESSION['language_code']));
    }
    return xtc_draw_pull_down_menu($name, $tax_class_array, $tax_class_id);
  }

  /**
   * xtc_cfg_textarea()
   *
   * @param mixed $text
   * @param string $key
   * @return
   */
  function xtc_cfg_textarea($text, $key = '') {
    $name = (!empty($key)) ? 'configuration[' . $key . ']' : 'configuration_value';
    return xtc_draw_textarea_field($name, false, 35, 3, $text, 'class="textareaModule"');
  }

  /**
   * xtc_cfg_get_zone_name()
   *
   * @param mixed $zone_id
   * @return
   */
  function xtc_cfg_get_zone_name($zone_id) {
    $zone_query = xtc_db_query("SELECT zone_name
                                  FROM ".TABLE_ZONES."
                                 WHERE zone_id = '".(int)$zone_id."'");
    if (!xtc_db_num_rows($zone_query)) {
      return $zone_id;
    } else {
      $zone = xtc_db_fetch_array($zone_query);
      return $zone['zone_name'];
    }
  }

  /**
   * xtc_set_banner_status()
   *
   * @param mixed $banners_id
   * @param mixed $status
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_set_banner_status.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_set_specials_status()
   *
   * @param mixed $specials_id
   * @param mixed $status
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_set_specials_status.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_set_time_limit()
   *
   * @param mixed $limit
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_set_time_limit.inc.php'); // Use existing function from "/inc/" folder
  
  /**
   * xtc_cfg_select_option()
   *
   * @param mixed $select_array
   * @param mixed $key_value
   * @param string $key
   * @return
   */
  function xtc_cfg_select_option($select_array, $key_value, $key = '') {
    $string = '';
    $name = (($key) ? 'configuration['.$key.']' : 'configuration_value');

    if (count($select_array) > 2) {
      $option_array = array();
      for ($i = 0, $n = sizeof($select_array); $i < $n; $i++) {
        $option_array[] = array(
          'id' => $select_array[$i],
          'text' => xtc_multi_lang_values($select_array[$i]),
        );
      }
      $string = xtc_draw_pull_down_menu($name, $option_array, $key_value);
    } else {
      if (NEW_SELECT_CHECKBOX == 'true') {
        $string .= '<span class="cfg_select_option">';
      }
      $name_lower = (($key) ? 'cfg_so_k_'.strtolower($key) : 'cfg_so_k');
      for ($i = 0, $n = sizeof($select_array); $i < $n; $i++) {
        $string .= '<input id="'.$name_lower.($i?"_$i":'').'" type="radio" name="'.$name.'" value="'.$select_array[$i].'"';
        if ($key_value == $select_array[$i]) $string .= ' checked';
        $string .= '><label for="'.$name_lower.($i?"_$i":'').'" class="'.($key_value == $select_array[$i]?'cfg_so_before ':'').'cfg_sov_'.strtolower($select_array[$i]).'">';
        $string .= xtc_multi_lang_values($select_array[$i]) . '</label>';
        if (NEW_SELECT_CHECKBOX != 'true') {
          $string .= '<br/>';
        }
      }
      if (NEW_SELECT_CHECKBOX == 'true') {
        $string .= '</span>';
      }
    }
    return $string;
  }

  /**
   * xtc_mod_select_option()
   *
   * @param mixed $select_array
   * @param mixed $key_name
   * @param mixed $key_value
   * @return
   */
  function xtc_mod_select_option($select_array, $key_name, $key_value) {
    foreach ($select_array as $key => $value) {
      if (is_int($key))
        $key = $value;
      $string .= '<br /><input type="radio" name="configuration['.$key_name.']" value="'.$key.'"';
      if ($key_value == $key)
        $string .= ' CHECKED';
      $string .= '> '.$value;
    }
    return $string;
  }

  /**
   * xtc_get_system_information()
   *
   * @return
   */
  function xtc_get_system_information() {
    $db_query = xtc_db_query("SELECT now() as datetime");
    $db = xtc_db_fetch_array($db_query);

    //get server uptime on Windows & Unix/Linux systems
    $uptime = 'n/a';
    if (function_exists('exec')) {
      if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $uptime = @exec("net statistics srv | find /i \"Stat\"");
      } else {
        $uptime = @exec('uptime');
      }
    }

    return array (
      //System information
      'date' => date('Y-m-d H:i:s O T'),
      'os' => PHP_OS,
      'system' => php_uname('s'),
      'kernel' => php_uname('v'),
      'host' => php_uname('n'),
      'ip' => gethostbyname(php_uname('n')),
      'uptime' => $uptime,
      'http_server' => $_SERVER['SERVER_SOFTWARE'],

      //MYSQL information
      'db_server' => DB_SERVER, 'db_ip' => gethostbyname(DB_SERVER),
      'db_version' => 'MySQL '. xtc_db_get_server_info(),
      'db_date' => $db['datetime'],

      //PHP information
      'php' => PHP_VERSION,
      'zend' => (function_exists('zend_version') ? zend_version() : ''),
      'sapi' => PHP_SAPI,
      'int_size' => defined('PHP_INT_SIZE') ? PHP_INT_SIZE : '',
      'safe_mode' => (int) @ini_get('safe_mode'),
      'open_basedir' => (int) @ini_get('open_basedir'),
      'memory_limit' => @ini_get('memory_limit'),
      'error_reporting' => error_reporting(),
      'display_errors' => (int)@ini_get('display_errors'),
      'allow_url_fopen' => (int) @ini_get('allow_url_fopen'),
      'allow_url_include' => (int) @ini_get('allow_url_include'),
      'file_uploads' => (int) @ini_get('file_uploads'),
      'upload_max_filesize' => @ini_get('upload_max_filesize'),
      'post_max_size' => @ini_get('post_max_size'),
      'disable_functions' => @ini_get('disable_functions'),
      'disable_classes' => @ini_get('disable_classes'),
      'enable_dl' => (int) @ini_get('enable_dl'),
      'magic_quotes_gpc' => (int) @ini_get('magic_quotes_gpc'),
      'register_globals' => (int) @ini_get('register_globals'),
      'filter.default' => @ini_get('filter.default'),
      'zend.ze1_compatibility_mode' => (int) @ini_get('zend.ze1_compatibility_mode'),
      'unicode.semantics' => (int) @ini_get('unicode.semantics'),
      'zend_thread_safty' => (int) function_exists('zend_thread_id'),
      'extensions' => get_loaded_extensions()
    );
  }

  /**
   * xtc_generate_category_path()
   *
   * @param mixed $id
   * @param string $from
   * @param string $categories_array
   * @param integer $index
   * @return
   */
  function xtc_generate_category_path($id, $from = 'category', $categories_array = '', $index = 0) {
    if (!is_array($categories_array)) {
      $categories_array = array ();
    }
    if ($from == 'product') {
      $categories_query = xtc_db_query("SELECT categories_id
                                          FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
                                         WHERE products_id = '".(int)$id."'");
      while ($categories = xtc_db_fetch_array($categories_query)) {
        if ($categories['categories_id'] == '0') {
          $categories_array[$index][] = array ('id' => '0', 'text' => TEXT_TOP);
        } else {
          $category = xtc_get_category_data($categories['categories_id']);          
          $categories_array[$index][] = array ('id' => $categories['categories_id'], 'text' => $category['categories_name']);
          if ((xtc_not_null($category['parent_id'])) && ($category['parent_id'] != '0')){
            $categories_array = xtc_generate_category_path($category['parent_id'], 'category', $categories_array, $index);
          }
          $categories_array[$index] = array_reverse($categories_array[$index]);
        }
        $index ++;
      }
    } elseif ($from == 'category') {
      $category = xtc_get_category_data($id);  
      $categories_array[$index][] = array ('id' => $id, 'text' => $category['categories_name']);
      if ((xtc_not_null($category['parent_id'])) && ($category['parent_id'] != '0')) {
        $categories_array = xtc_generate_category_path($category['parent_id'], 'category', $categories_array, $index);
      }
    }
    return $categories_array;
  }

  /**
   * xtc_output_generated_category_path()
   *
   * @param mixed $id
   * @param string $from
   * @return
   */
  function xtc_output_generated_category_path($id, $from = 'category') {
    $calculated_category_path_string = '<ul>';
    $calculated_category_path = xtc_generate_category_path($id, $from);

    for ($i = 0, $n = sizeof($calculated_category_path); $i < $n; $i ++) {
      $categories_array = array();
      if ($from == 'category') {
        $calculated_category_path[$i] = array_reverse($calculated_category_path[$i]);
      }
      for ($j = 0, $k = sizeof($calculated_category_path[$i]); $j < $k; $j ++) {
        $categories_array['id'][] = $calculated_category_path[$i][$j]['id'];
        $categories_array['text'][] = $calculated_category_path[$i][$j]['text'];
      }
      $calculated_category_path_string .= '<li><a href="'.xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . implode('_', $categories_array['id'])).'">'.implode('&nbsp;&gt;&nbsp;', $categories_array['text']).'</a></li>';
    }
    
    if (strlen($calculated_category_path_string) < 5) {
      $calculated_category_path_string .= '<li>'.TEXT_TOP.'</li>';
    }
    $calculated_category_path_string .= '</ul>';
    
    return $calculated_category_path_string;
  }

  /**
   * xtc_del_image_file()
   *
   * @param mixed $image
   * @return
   */
  function xtc_del_image_file($image) {
    if (is_file(DIR_FS_CATALOG_ORIGINAL_IMAGES.$image)) {
      unlink(DIR_FS_CATALOG_ORIGINAL_IMAGES.$image);
    }
    if (is_file(DIR_FS_CATALOG_POPUP_IMAGES.$image)) {
      unlink(DIR_FS_CATALOG_POPUP_IMAGES.$image);
    }
    if (is_file(DIR_FS_CATALOG_INFO_IMAGES.$image)) {
      unlink(DIR_FS_CATALOG_INFO_IMAGES.$image);
    }
    if (is_file(DIR_FS_CATALOG_MIDI_IMAGES.$image)) {
      unlink(DIR_FS_CATALOG_MIDI_IMAGES.$image);
    }
    if (is_file(DIR_FS_CATALOG_THUMBNAIL_IMAGES.$image)) {
      unlink(DIR_FS_CATALOG_THUMBNAIL_IMAGES.$image);
    }
    if (is_file(DIR_FS_CATALOG_MINI_IMAGES.$image)) {
      unlink(DIR_FS_CATALOG_MINI_IMAGES.$image);
    }
  }

  /**
   * xtc_restock_order()
   *
   * @param mixed $order_id
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_restock_order.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_remove_order()
   *
   * @param mixed $order_id
   * @param bool $restock
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_remove_order.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_reverse_order()
   *
   * @param mixed $order_id
   * @param bool $restock
   * @return
   */
  function xtc_reverse_order($order_id, $restock, $order_status_id, $activate = true) {
    if ($restock == 'on') {
      xtc_restock_order($order_id, $activate);
    }
    $check_query = xtc_db_query("SELECT orders_status 
                                   FROM ".TABLE_ORDERS." 
                                  WHERE orders_id = '".(int)$order_id."'");
    $check = xtc_db_fetch_array($check_query);
    if ($check['orders_status'] != $order_status_id) {
      xtc_db_query("UPDATE ".TABLE_ORDERS." SET orders_status = '".(int)$order_status_id."' WHERE orders_id = '".(int)$order_id."'");
      $sql_data_array = array(
        'orders_id' => (int)$order_id,
        'orders_status_id' => (int)$order_status_id,
        'date_added' => 'now()',
        'customer_notified' => '0',
        'comments' => 'Storniert',
        'comments_sent' => '0',
      );
      xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
    }
    xtc_db_query("UPDATE ".TABLE_ORDERS_TOTAL." SET value = '0.0000' WHERE orders_id = '".(int)$order_id."'");
    xtc_db_query("UPDATE ".TABLE_ORDERS_TOTAL." SET text = '0.00' WHERE orders_id = '".(int)$order_id."'");
    xtc_db_query("UPDATE ".TABLE_ORDERS_PRODUCTS_DOWNLOAD." SET download_count = '0' WHERE orders_id = '".(int)$order_id."'");    
    xtc_db_query("UPDATE ".TABLE_COUPON_GV_QUEUE." SET amount = '0.0000' WHERE order_id = '".(int)$order_id."'");
  }

  /**
   * xtc_remove()
   *
   * @param mixed $source
   * @return
   */
  function xtc_remove($source) {
    global $messageStack, $xtc_remove_error;
    if (isset ($xtc_remove_error)) {
      $xtc_remove_error = false;
    }
    if (is_dir($source)) {
      $dir = dir($source);
      while ($file = $dir->read()) {
        if (($file != '.') && ($file != '..')) {
          if (is_writeable($source.'/'.$file)) {
            xtc_remove($source.'/'.$file);
          } else {
            $messageStack->add(sprintf(ERROR_FILE_NOT_REMOVEABLE, $source.'/'.$file), 'error');
            $xtc_remove_error = true;
          }
        }
      }
      $dir->close();
      if (is_writeable($source)) {
        rmdir($source);
      } else {
        $messageStack->add(sprintf(ERROR_DIRECTORY_NOT_REMOVEABLE, $source), 'error');
        $xtc_remove_error = true;
      }
    } else {
      if (is_writeable($source)) {
        unlink($source);
      } else {
        $messageStack->add(sprintf(ERROR_FILE_NOT_REMOVEABLE, $source), 'error');
        $xtc_remove_error = true;
      }
    }
  }

  /**
   * xtc_display_tax_value()
   *
   * @param mixed $value
   * @param mixed $padding
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_display_tax_value.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_get_tax_class_title()
   *
   * @param mixed $tax_class_id
   * @return
   */
  function xtc_get_tax_class_title($tax_class_id) {
    require_once(DIR_FS_INC.'parse_multi_language_value.inc.php');

    if ($tax_class_id == '0') {
      return TEXT_NONE;
    } else {
      $classes_query = xtc_db_query("SELECT tax_class_title
                                       FROM ".TABLE_TAX_CLASS."
                                      WHERE tax_class_id = '".(int)$tax_class_id."'");
      if (xtc_db_num_rows($classes_query) > 0) {
        $classes = xtc_db_fetch_array($classes_query);
        return parse_multi_language_value($classes['tax_class_title'], $_SESSION['language_code']);
      }
    }
  }

  /**
   * xtc_banner_image_extension()
   *
   * @return
   */
  function xtc_banner_image_extension() {
    if (function_exists('imagetypes')) {
      if (imagetypes() & IMG_PNG) {
        return 'png';
      } elseif (imagetypes() & IMG_JPG) {
        return 'jpg';
      } elseif (imagetypes() & IMG_GIF) {
        return 'gif';
      }
    } elseif (function_exists('imagecreatefrompng') && function_exists('imagepng')) {
      return 'png';
    } elseif (function_exists('imagecreatefromjpeg') && function_exists('imagejpeg')) {
      return 'jpg';
    } elseif (function_exists('imagecreatefromgif') && function_exists('imagegif')) {
      return 'gif';
    }
    return false;
  }

  /**
   * xtc_call_function()
   *
   * @param mixed $function
   * @param mixed $parameter
   * @param string $object
   * @return
   */
  function xtc_call_function($function, $parameter, $object = '') {
    if (empty($object)) {
      return call_user_func($function, $parameter);
    } else {
      return call_user_func(array ($object, $function), $parameter);
    }
  }

  /**
   * xtc_get_zone_class_title()
   *
   * @param mixed $zone_class_id
   * @return
   */
  function xtc_get_zone_class_title($zone_class_id) {
    require_once(DIR_FS_INC.'parse_multi_language_value.inc.php');

    if ($zone_class_id == '0') {
      return TEXT_NONE;
    } else {
      $classes_query = xtc_db_query("SELECT geo_zone_name
                                       FROM ".TABLE_GEO_ZONES."
                                      WHERE geo_zone_id = '".(int)$zone_class_id."'");
      if (xtc_db_num_rows($classes_query) > 0) {
        $classes = xtc_db_fetch_array($classes_query);
        return parse_multi_language_value($classes['geo_zone_name'], $_SESSION['language_code']);
      }
    }
  }

  /**
   * xtc_cfg_pull_down_template_sets()
   *
   * @return
   */
  function xtc_cfg_pull_down_template_sets() {
    $name = (isset($key) ? 'configuration['.$key.']' : 'configuration_value'); //DokuMan - set undefined $key
    if ($dir = opendir(DIR_FS_CATALOG.'templates/')) {
      while (($templates = readdir($dir)) !== false) {
        if (is_dir(DIR_FS_CATALOG.'templates/'.$templates) && ($templates != "CVS") && substr($templates, 0, 1) != ".") {
          $templates_array[] = array ('id' => $templates, 'text' => $templates);
        }
      }
      closedir($dir);
      sort($templates_array);
      return xtc_draw_pull_down_menu($name, $templates_array, CURRENT_TEMPLATE);
    }
  }

  /**
   * xtc_cfg_pull_down_zone_classes()
   *
   * @param mixed $zone_class_id
   * @param string $key
   * @return
   */
  function xtc_cfg_pull_down_zone_classes($zone_class_id, $key = '') {
    require_once(DIR_FS_INC.'parse_multi_language_value.inc.php');

    $name = (($key) ? 'configuration['.$key.']' : 'configuration_value');
    $zone_class_array = array (array ('id' => '0', 'text' => TEXT_NONE));
    $zone_class_query = xtc_db_query("select geo_zone_id, geo_zone_name from ".TABLE_GEO_ZONES." order by geo_zone_name");
    while ($zone_class = xtc_db_fetch_array($zone_class_query)) {
      $zone_class_array[] = array ('id' => $zone_class['geo_zone_id'], 'text' => parse_multi_language_value($zone_class['geo_zone_name'], $_SESSION['language_code']));
    }
    return xtc_draw_pull_down_menu($name, $zone_class_array, $zone_class_id);
  }

  /**
   * xtc_cfg_pull_down_order_statuses()
   *
   * @param mixed $order_status_id
   * @param string $key
   * @return
   */
  function xtc_cfg_pull_down_order_statuses($order_status_id, $key = '') {
    $name = (($key) ? 'configuration['.$key.']' : 'configuration_value');
    $statuses_array = array();
    $statuses_query = xtc_db_query("SELECT orders_status_id,
                                           orders_status_name
                                      FROM ".TABLE_ORDERS_STATUS."
                                     WHERE language_id = '".(int)$_SESSION['languages_id']."'
                                  ORDER BY sort_order");
    while ($statuses = xtc_db_fetch_array($statuses_query)) {
      $statuses_array[] = array ('id' => $statuses['orders_status_id'], 'text' => $statuses['orders_status_name'] . (($statuses['orders_status_id'] == DEFAULT_ORDERS_STATUS_ID) ? ' ('.TEXT_DEFAULT.')' : ''));
    }
    return xtc_draw_pull_down_menu($name, $statuses_array, $order_status_id);
  }

  /**
   * xtc_get_order_status_name()
   *
   * @param mixed $order_status_id
   * @param string $language_id
   * @return
   */
  function xtc_get_order_status_name($order_status_id, $language_id = '') {
    if ($order_status_id < 1) {
      return TEXT_DEFAULT;
    }
    if (!is_numeric($language_id)) {
      $language_id = $_SESSION['languages_id'];
    }
    $status_query = xtc_db_query("SELECT orders_status_name
                                    FROM ".TABLE_ORDERS_STATUS."
                                   WHERE orders_status_id = '".(int)$order_status_id."'
                                     AND language_id = '".(int)$language_id."'");
    if (xtc_db_num_rows($status_query) > 0) {
      $status = xtc_db_fetch_array($status_query);
      return $status['orders_status_name'] . (($order_status_id == DEFAULT_ORDERS_STATUS_ID) ? ' ('.TEXT_DEFAULT.')' : '');
    }
  }

  /**
   * xtc_rand()
   *
   * @param int $min
   * @param int $max
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_rand.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_convert_linefeeds()
   *
   * @param mixed $from
   * @param mixed $to
   * @param mixed $string
   * @return
   */
  function xtc_convert_linefeeds($from, $to, $string) {
    return str_replace($from, $to, $string);
  }

  /**
   * xtc_get_customers_statuses()
   *
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_get_customers_statuses.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_get_customers_status_name()
   *
   * @param mixed $customers_status_id
   * @param string $language_id
   * @return
   */
  function xtc_get_customers_status_name($customers_status_id, $language_id = '') {
    if (!$language_id) {
      $language_id = $_SESSION['languages_id'];
    }
    $customers_status_query = xtc_db_query("SELECT customers_status_name
                                              FROM ".TABLE_CUSTOMERS_STATUS."
                                             WHERE customers_status_id = '".(int)$customers_status_id."'
                                               AND language_id = '".(int)$language_id."'");
    if (xtc_db_num_rows($customers_status_query) > 0) {
      $customers_status = xtc_db_fetch_array($customers_status_query);
      return $customers_status['customers_status_name'];
    }
  }

  /**
   * xtc_cfg_pull_down_customers_status_list()
   *
   * @param mixed $customers_status_id
   * @param string $key
   * @return
   */
  function xtc_cfg_pull_down_customers_status_list($customers_status_id, $key = '') {
    $name = (($key) ? 'configuration['.$key.']' : 'configuration_value');
    return xtc_draw_pull_down_menu($name, xtc_get_customers_statuses(), $customers_status_id);
  }

  /**
   * xtc_get_user_info()
   *
   * @param mixed $customer_id
   * @return
   */
  function xtc_get_user_info($customer_id) {
    $user_info_array = xtc_db_query("SELECT customers_ip,
                                            customers_ip_date,
                                            customers_host,
                                            customers_advertiser,
                                            customers_referer_url
                                       FROM ".TABLE_CUSTOMERS_IP."
                                      WHERE customers_id = '".(int)$customer_id."'");
    return $user_info_array;
  }

  /**
   * get_group_price()
   *
   * @param mixed $group_id
   * @param mixed $product_id
   * @return
   */
  function get_group_price($group_id, $product_id) {
    $personal_offer = 0;
    $group_price_query = xtc_db_query("SELECT personal_offer
                                         FROM ".TABLE_PERSONAL_OFFERS_BY.$group_id."
                                        WHERE products_id = '".(int)$product_id."'
                                          AND quantity = '1'");
    if (xtc_db_num_rows($group_price_query) > 0) {
      $group_price = xtc_db_fetch_array($group_price_query);
      $personal_offer = $group_price['personal_offer'];
    }
    return $personal_offer;
  }

  /**
   * format_price()
   *
   * @param mixed $price_string
   * @param mixed $price_special
   * @param mixed $currency
   * @param mixed $allow_tax
   * @param mixed $tax_rate
   * @return
   */
  function format_price($price_string, $price_format, $currency, $allow_tax, $tax_rate) {
    $currencies_query = xtc_db_query("SELECT symbol_left,
                                             symbol_right,
                                             decimal_places,
                                             decimal_point,
                                             thousands_point
                                        FROM ".TABLE_CURRENCIES."
                                        WHERE code = '".xtc_db_input($currency)."'");
    $currencies = xtc_db_fetch_array($currencies_query);

    if ($allow_tax == '1') {
      $price_string = (double)$price_string / ((100 + (double)$tax_rate) / 100);
    }

    if ($price_format == '1') {
      $price_string = number_format($price_string, $currencies['decimal_places'], $currencies['decimal_point'], $currencies['thousands_point']);
      $price_string = $currencies['symbol_left'].' '.$price_string.' '.$currencies['symbol_right'];
    } else {
      $price_string = precision($price_string, $currencies['decimal_places']);
    }
    
    return $price_string;
  }

  /**
   * precision()
   *
   * @param mixed $number
   * @param mixed $places
   * @return
   */
  function precision($number, $places) {
    $number = number_format($number, (int)$places, '.', '');
    return $number;
  }

  /**
   * xtc_round()
   *
   * @param mixed $value
   * @param mixed $precision
   * @return
   */
  function xtc_round($value, $precision) {
    return round((double)$value, (int)$precision);
  }

  /**
   * xtc_get_lang_definition()
   *
   * @param mixed $search_lang
   * @param mixed $lang_array
   * @param mixed $modifier
   * @return
   */
  function xtc_get_lang_definition($search_lang, $lang_array, $modifier) {
    $search_lang = $search_lang.$modifier;
    return $lang_array[$search_lang];
  }

  /**
   * xtc_CheckExt()
   *
   * @param mixed $filename
   * @param mixed $ext
   * @return
   */
  function xtc_CheckExt($filename, $ext) {
    $passed = FALSE;
    $testExt = "\.".$ext."$";
    if (preg_match('/'.$testExt.'/i', $filename)) {
      $passed = TRUE;
    }
    return $passed;
  }

  /**
   * xtc_get_status_users()
   *
   * @param mixed $status_id
   * @return
   */
  function xtc_get_status_users($status_id) {
    $status_query = xtc_db_query("SELECT count(customers_status) as count
                                    FROM ".TABLE_CUSTOMERS."
                                   WHERE customers_status = '".(int)$status_id."'");
    $status_data = xtc_db_fetch_array($status_query);
    return $status_data['count'];
  }

  /**
   * xtc_mkdirs()
   *
   * @param mixed $path
   * @param mixed $perm
   * @return
   */
  function xtc_mkdirs($path, $perm) {
    if (is_dir($path)) {
      return true;
    } else {
      //$path=dirname($path);
      if (!mkdir($path, $perm))
        return false;
      mkdir($path, $perm);
      return true;
    }
  }

  /**
   * xtc_spaceUsed()
   *
   * @param mixed $dir
   * @return float
   */
  function xtc_spaceUsed($dir) {
    $totalspaceUsed = 0;

    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
          if (is_dir($dir.$file) && $file != '.' && $file != '..') {
            xtc_spaceUsed($dir.$file.'/');
          } elseif (is_readable($dir.$file)) {
            $totalspaceUsed += @filesize($dir.$file);
          }
        }
        closedir($dh);
      }
    }
    return $totalspaceUsed;
  }

  /**
   * create_coupon_code()
   *
   * @param string $salt
   * @param mixed $length
   * @return
   */
  require_once(DIR_FS_INC . 'create_coupon_code.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_gv_account_update()
   *
   * @param mixed $customer_id
   * @param mixed $gv_id
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_gv_account_update.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_getDownloads()
   *
   * @return
   */
  function xtc_getDownloads() {
    $files = $sort_key = array ();
    $dir = DIR_FS_CATALOG . 'download/';
    if ($fp = opendir($dir)) {
      while ($file = readdir($fp)) {
        if (is_file($dir.$file) && $file != '.htaccess') {
          $size = filesize($dir.$file);
          $files[] = array ('id' => $file, 'text' => $file.' | '.xtc_format_filesize($size), 'size' => $size, 'date' => date("F d Y H:i:s.", filemtime($dir.$file)));
          $sort_key[] = $file;
        }
      }
      closedir($fp);
      array_multisort($sort_key, SORT_ASC, $files);
    }
    return $files;
  }

  /**
   * xtc_try_upload()
   *
   * @param string $file
   * @param string $destination
   * @param string $permissions
   * @param string $extensions
   * @param string $mime_types
   * @return
   */
  require_once(DIR_FS_INC . 'xtc_try_upload.inc.php'); // Use existing function from "/inc/" folder

  /**
   * xtc_button()
   *
   * @param mixed $value
   * @param string $type
   * @param string $parameter
   * @return
   */
  function xtc_button($value, $type='submit', $parameter='') {
    return '<input type="'.$type.'" class="button" onclick="this.blur();" value="' . $value . '" ' . $parameter . ' >';
  }

  /**
   * xtc_button_link()
   *
   * @param mixed $value
   * @param string $href
   * @param string $parameter
   * @return
   */
  function xtc_button_link($value, $href='javascript:void(null)', $parameter='') {
    return '<a href="'.$href.'" class="button" onclick="this.blur()" '.$parameter.' >'.$value.'</a>';
  }

  /**
   * xtc_get_products_special_price()
   *
   * @param mixed $product_id
   * @return
   */
  function xtc_get_products_special_price($product_id){
    $product_query = xtc_db_query("SELECT specials_new_products_price
                                     FROM " . TABLE_SPECIALS . "
                                    WHERE products_id = '" . (int)$product_id . "'
                                      AND status = '1'
                                      AND (now() >= s.start_date OR s.start_date IS NULL)");
    if (xtc_db_num_rows($product_query) > 0) {
      $product = xtc_db_fetch_array($product_query);
      return $product['specials_new_products_price'];
    }
  }

  /**
   * xtc_get_geoip_data()
   *
   * @author DokuMan
   * @date 2011-03-16
   * @param mixed $host
   * @return
   *
   * Usage:
   * $response = xtc_get_geoip_data(192.168.0.1);
   * $data = unserialize($response);
   * returns an array (
   *   'geoplugin_city' => 'Mannheim',
   *   'geoplugin_region' => 'Baden-Württemberg',
   *   'geoplugin_areaCode' => '0',
   *   'geoplugin_dmaCode' => '0',
   *   'geoplugin_countryCode' => 'DE',
   *   'geoplugin_countryName' => 'Germany',
   *   'geoplugin_continentCode' => 'EU',
   *   'geoplugin_latitude' => '49.488300323486',
   *   'geoplugin_longitude' => '8.4646997451782',
   *   'geoplugin_regionCode' => '01',
   *   'geoplugin_regionName' => 'Baden-Württemberg',
   *   'geoplugin_currencyCode' => 'EUR',
   *   'geoplugin_currencySymbol' => '€',
   *  'geoplugin_currencyConverter' => 0.7195162136,
   * )
   *
   */
  function xtc_get_geoip_data($ip) {
    $host = 'http://www.geoplugin.net/php.gp?ip='.$ip;
    if (function_exists('curl_init') ) {
      //use cURL to fetch data
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $host);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_USERAGENT, 'geoPlugin PHP Class v1.0');
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, '1');
      $response = curl_exec($ch);
      curl_close ($ch);
      unset($ch);
    } else if (ini_get('allow_url_fopen') ) {
      //fall back to file_get_contents()
      $response = file_get_contents($host, 'r');
    } else {
      trigger_error('geoPlugin class Error: Cannot retrieve data. Either compile PHP with cURL support or enable allow_url_fopen in php.ini ', E_USER_ERROR);
      return;
    }
    return $response;
  }

  /**
   * xtc_cfg_checkbox_unallowed_module()
   *
   * @author rpa-com.de
   * @date 2013-09-29
   * @param string $module_type like payment, shipping
   * @param string $checkbox_name is database field
   * @param string $data is database field data
   *
   * @return string html checkboxes by configuration set_function
   */
  function xtc_cfg_checkbox_unallowed_module($module_type,$checkbox_name,$data,$name='')
  {
    $module_unallowed = array();
    $unallowed_module = '';
    $customers_status_module_unallowed = explode(',', $data);
    foreach ($customers_status_module_unallowed as $value) {
      $module_unallowed[] = $value;
    }
    $module_const = constant('MODULE_'.strtoupper($module_type).'_INSTALLED');
    if (xtc_not_null($module_const)) {
      $module_array = explode(';', $module_const);
      for ($p=0, $x=sizeof($module_array); $p<$x; $p++) {
        if (is_file(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/'.$module_type.'/' . $module_array[$p])) {
          include_once(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/'.$module_type.'/' . $module_array[$p]);
        }
        $module_name = substr($module_array[$p], 0,-4);
        $unallowed_module .= '<label>';
        $unallowed_module .= xtc_draw_checkbox_field($checkbox_name.'[]', $module_name, (in_array($module_name, $module_unallowed) ? true : false)).constant('MODULE_'.strtoupper($module_type).'_'.strtoupper($module_name).'_TEXT_TITLE').' ('.$module_array[$p].')';
        $unallowed_module .= '</label><br>';
      }
    } else {
      $unallowed_module = sprintf(constant('TEXT_'.strtoupper($module_type).'_ERROR'), xtc_href_link(FILENAME_MODULES, 'set='.$module_type));
    }
    return $unallowed_module;
  }

  function order_statuses() {
    $statuses_array = array ();
    $statuses_query = xtc_db_query("SELECT orders_status_id,
                                           orders_status_name
                                      FROM ".TABLE_ORDERS_STATUS."
                                     WHERE language_id = '".(int)$_SESSION['languages_id']."'
                                  ORDER BY orders_status_name");
    while ($statuses = xtc_db_fetch_array($statuses_query)) {
      $statuses_array[] = array ('id' => $statuses['orders_status_id'], 'text' => $statuses['orders_status_name']);
    }
    return $statuses_array;
  }

  function xtc_cfg_display_orders_statuses($cfg_val) {
    $orders_status_id_array = explode(',', $cfg_val);
    
    $status_names_array = array();
    foreach ($orders_status_id_array as $orders_status_id) {
      $status_names_array[] = xtc_get_orders_status_name($orders_status_id);
    }
    
    return implode('<br>', $status_names_array);
  }

  /**
   * xtc_cfg_multi_checkbox()
   *
   * @author Hacker Solutions / Web28
   * @date 2014-03-10
   * @param string|array $format
   *   the parameter can be the function name which returns an array with
   *   the below pattern or the array with the pattern itself
   *   array( array('id' => 'db_key', 'text' => 'desc for key'), ...)
   * @param string $separator for configuration_value (e.g. ',' or ';')
   * @param string $checked is programmatically set by the configuration_value in the database
   *
   * @return string html checkboxes by configuration set_function
   */
  function xtc_cfg_multi_checkbox($format, $separator, $checked, $key = '') {
    $name = (($key) ? 'configuration['.$key.'][]' : 'configuration_value[]');
    
    if (preg_match("'chr\(([0-9]{1,3})\)'",$separator, $matches)) {
      $separator  = chr($matches[1]);
    }
    $checkboxes = '';
    $checkedboxes = (array) explode($separator, $checked);
    $format_array =  (!is_array($format) && function_exists($format)) ? (array)$format() : (array)$format;
        
    foreach ($format_array as $key => $value) {
      if (is_array($value)) {
        $key   = isset($value['id'])   ? $value['id']   : $key;
        $value = isset($value['text']) ? $value['text'] : $value;
      }
      $checkboxes .= '<label>';
      $checkboxes .= xtc_draw_checkbox_field($name, $key, (bool)in_array($key, $checkedboxes));
      $checkboxes .= $value . '</label><br>';
    }
    return $checkboxes;
  }

  /**
   * cfg_save_max_display_results()
   *
   * @author rpa-com.de
   * @date 2013-10-03
   * @param string configuration key
   *
   * @return int configuration value
   */
  function xtc_cfg_save_max_display_results($cfg_key) {
    if (isset($_POST[$cfg_key])) {
      $configuration_value = preg_replace('/[^0-9-]/','',$_POST[$cfg_key]);
      $configuration_value = xtc_db_prepare_input($configuration_value);
      $configuration_query = xtc_db_query("SELECT configuration_key,
                                                  configuration_value
                                             FROM " . TABLE_CONFIGURATION . "
                                            WHERE configuration_key = '" . xtc_db_input($cfg_key) . "'
                                         ");
      if (xtc_db_num_rows($configuration_query) > 0) {
        //update
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . "
                         SET configuration_value ='" . xtc_db_input($configuration_value) . "',
                             last_modified = NOW()
                       WHERE configuration_key='" . xtc_db_input($cfg_key) . "'");
      } else {
        //new entry
        $sql_data_array = array(
          'configuration_key' => $cfg_key,
          'configuration_value' => $configuration_value,
          'configuration_group_id' => '1000',
          'sort_order' => '-1',
          'last_modified' => 'now()',
          'date_added' => 'now()'
          );
        xtc_db_perform(TABLE_CONFIGURATION,$sql_data_array);
      }
      return $configuration_value;
    }
    return defined($cfg_key) && (int)constant($cfg_key) > 0 ? constant($cfg_key) : 20;
  }

  /**
   * xtc_cfg_input_email_language()
   *
   * @author GTB / Web28
   * @date 2014-03-08
   * @param string configuration key
   *
   * @return input fields
   */
  function xtc_cfg_input_email_language($parameters) {
    require_once(DIR_FS_INC.'parse_multi_language_value.inc.php');

    // set languages
    $languages = xtc_get_languages();
    
    $name = '%s';
    if (isset($parameters[2])) {
      $name = $parameters[2];
    }
    
    // build input fileds
    $email_fields = '';
    for ($i=0, $n=count($languages); $i<$n; $i++) {
      $email_fields .= '<div>'.PHP_EOL;
      $email_fields .= xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] .'/admin/images/'. $languages[$i]['image'], $languages[$i]['name'], '18px');
      if (trim($parameters[1]) == 'SMTP_PASSWORD') {
        $email_fields .= xtc_draw_password_field(sprintf($name, trim($parameters[1])).'[' . strtoupper($languages[$i]['code']) . ']', parse_multi_language_value($parameters[0], $languages[$i]['code'], true), false , 'style="margin-left:2px; width:200px"');
      } else {
        $email_fields .= xtc_draw_input_field(sprintf($name, trim($parameters[1])).'[' . strtoupper($languages[$i]['code']) . ']', parse_multi_language_value($parameters[0], $languages[$i]['code'], true), 'style="margin-left:2px; width:360px"');
      }
      $email_fields .= '</div>'.PHP_EOL;
    }

    return $email_fields;
  }

  /**
   * xtc_get_email_languages()
   *
   * @param string $key
   * @return
   */
  function xtc_get_email_language_names($key) {
    require_once(DIR_FS_INC . 'parse_multi_language_value.inc.php');
    
    $languages = xtc_get_languages();

    $val_array = array();
    for ($i=0, $n=count($languages); $i<$n; $i++) {
      $val_array[] = '<b>'.strtoupper($languages[$i]['code']).'</b>: '.parse_multi_language_value($key, $languages[$i]['code'], true);
    }
    
    return implode('<br/>', $val_array);
  }

  /**
   * xtc_draw_gender_pull_down()
   *
   * @author MK
   * @param string $name
   * @param string $value
   * @return
   */
  function xtc_draw_gender_pull_down($name, $value, $parameters = '') {
    return xtc_draw_pull_down_menu(
      $name,
      array(
        array('id' => '', 'text' => ''),
        array('id' => 'm', 'text' => TEXT_MR),
        array('id' => 'f', 'text' => TEXT_MRS)
      ),
      $value,
      $parameters
    );
  }

  /**
   * xtc_multi_lang_values()
   *
   * @author h-h-h
   * @param string $value
   * @return string
   */
  function xtc_multi_lang_values($value) {
    $upperValue = strtoupper($value);
    if (defined('CFG_TXT_'.$upperValue)) {
      return constant('CFG_TXT_'.$upperValue);
    }
    switch($value) {
      case 'true': return CFG_TXT_YES; break;
      case 'True': return CFG_TXT_YES; break;
      case 'false': return CFG_TXT_NO; break;
      case 'False': return CFG_TXT_NO; break;
      default: return $value;
    }
  }

  /**
   * xtc_cfg_select_content()
   *
   * @param string $cfg_key
   * @param string $cfg_value
   * @param string $name
   * @return pulldown
   */
  function xtc_cfg_select_content($cfg_key, $cfg_value, $name = '%s') {
    $content_array = array(array('id' => '', 'text' => TEXT_SELECT));
    $content_query = xtc_db_query("SELECT content_group, 
                                          content_title 
                                     FROM ".TABLE_CONTENT_MANAGER." 
                                    WHERE languages_id = '".(int)$_SESSION['languages_id']."'
                                 GROUP BY content_group");
    while ($content = xtc_db_fetch_array($content_query)) {
      $content_array[] = array('id' => $content['content_group'], 'text' => $content['content_title'] . ' (coID: '.$content['content_group'].')');
    }
    return xtc_draw_pull_down_menu(sprintf($name, $cfg_key), $content_array, $cfg_value);
  }

  /**
   * xtc_cfg_select_content_module()
   *
   * @param string $configuration
   * @param string $key
   * @return pulldown
   */
  function xtc_cfg_select_content_module($cfg_value, $cfg_key) {
    return xtc_cfg_select_content($cfg_key, $cfg_value, 'configuration[%s]');
  }

  /**
   * xtc_cfg_display_content()
   *
   * @param string $content_group
   * @return string
   */
  function xtc_cfg_display_content($content_group) {
    $content_query = xtc_db_query("SELECT content_title 
                                     FROM ".TABLE_CONTENT_MANAGER." 
                                    WHERE languages_id = '".(int)$_SESSION['languages_id']."' 
                                      AND content_group = '".$content_group."'
                                    LIMIT 1");
    if (xtc_db_num_rows($content_query) > 0) {
      $content = xtc_db_fetch_array($content_query);
      return $content['content_title'];
    }
  }

  /**
   * xtc_cfg_pull_down_cache_type()
   *
   * @param string $cfg_key
   * @param string $cfg_value
   * @return pulldown
   */
  function xtc_cfg_pull_down_cache_type($cfg_key, $cfg_value) {
    $cache_array = array();
    
    if(is_writeable(SQL_CACHEDIR)) {
      $cache_array[] = array('id' => 'modified', 'text' => 'Files');
    }
    
    foreach(auto_include(DIR_FS_CATALOG.'includes/extra/cache/','php') as $file) {
      $name = substr(basename($file), 0, strrpos(basename($file), '_cache.php'));
      $cache_array[] = array('id' => $name, 'text' => ucwords(str_replace(array('.', '_'), ' ', $name)));
    }

    return xtc_draw_pull_down_menu($cfg_key, $cache_array, $cfg_value);
  }

  /**
   * clear_dir()
   *
   * @param string $dir
   * @param boolean $basefiles
   */
  function clear_dir($dir, $basefiles = false, $ignore_files = array()) {
    $dir = rtrim($dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    $ignore_files = array_merge($ignore_files, array('.htaccess', 'index.html'));
    if ($handle = opendir($dir)) {
      while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
          if (is_dir($dir.$file)) {
            clear_dir($dir.$file, true);
            rmdir($dir.$file);
          } else {
            if (!$basefiles && in_array($file, $ignore_files)) {
              continue;
            }
            unlink($dir.$file);
          }
        }
      }
      closedir($handle);
    }
  }

  /**
   * xtc_cfg_password_field()
   *
   * @param array $parameters
   * @param string $name
   * @return password input
   */
  function xtc_cfg_password_field($parameters, $name = '%s') {
    return xtc_draw_password_field(sprintf($name, $parameters[1]), $parameters[0]);
  }

  /**
   * xtc_cfg_password_field_module()
   *
   * @param string $value
   * @param string $key
   * @return password input
   */
  function xtc_cfg_password_field_module($cfg_value, $cfg_key) {
    return xtc_cfg_password_field(array($cfg_value, $cfg_key), 'configuration[%s]');
  }

  /**
   * xtc_cfg_display_password()
   *
   * @param string $configuration
   * @return stars
   */
  function xtc_cfg_display_password($cfg_value) {
    return '**********';
  }

  /**
   * xtc_cfg_select_mod_seo_url()
   *
   * @return dropdown
   */
  function xtc_cfg_select_mod_seo_url() {
    $name = (isset($key) ? 'configuration['.$key.']' : 'configuration_value');
    if ($dir = opendir(DIR_FS_CATALOG.'includes/extra/seo_url_mod/')) {
      $seo_url_mod_array = array();
      while (($seo_url_mod = readdir($dir)) !== false) {
        if (is_file(DIR_FS_CATALOG.'includes/extra/seo_url_mod/'.$seo_url_mod) && substr($seo_url_mod, -4) == ".php") {
          $seo_url_mod_array[] = array ('id' => substr($seo_url_mod, 0, -4), 'text' => substr($seo_url_mod, 0, -4));
        }
      }
      closedir($dir);
      sort($seo_url_mod_array);
      return xtc_draw_pull_down_menu($name, $seo_url_mod_array, SEO_URL_MOD_CLASS);
    }
  }

  /**
   * xtc_cfg_select_interval_module()
   *
   * @param string $value
   * @param string $key
   * @return pulldown
   */
  function xtc_cfg_select_interval_module($cfg_value, $cfg_key) {
    return xtc_cfg_select_interval($cfg_key, $cfg_value, 'configuration[%s]');
  }

  /**
   * xtc_cfg_select_interval()
   *
   * @param string $configuration_value
   * @param string $configuration_key
   * @return dropdown
   */
  function xtc_cfg_select_interval($cfg_key, $cfg_value, $name = '%s') {  
    $interval_array = array(array('id' => '86400', 'text' => '24 '.TEXT_HOURS),
                            array('id' => '43200', 'text' => '12 '.TEXT_HOURS),
                            array('id' => '21600', 'text' => '6 '.TEXT_HOURS),
                            array('id' => '10800', 'text' => '3 '.TEXT_HOURS),
                            array('id' => '3600',  'text' => '1 '.TEXT_HOUR),
                           );

    return xtc_draw_pull_down_menu(sprintf($name, $cfg_key), $interval_array, $cfg_value);
  }

  /**
   * xtc_cfg_display_interval()
   *
   * @param string $configuration
   * @return string
   */
  function xtc_cfg_display_interval($value) {
    $interval_array = array(
      '86400' => '24 '.TEXT_HOURS,
      '43200' => '12 '.TEXT_HOURS,
      '21600' => '6 '.TEXT_HOURS,
      '10800' => '3 '.TEXT_HOURS,
      '3600' => '1 '.TEXT_HOUR,
    );
    if (isset($interval_array[$value])) {
      return $interval_array[$value];
    }
    return $value;
  }


  /**
   * xtc_cfg_select_mod_captcha()
   *
   * @return dropdown
   */
  function xtc_cfg_select_mod_captcha() {
    $name = (isset($key) ? 'configuration['.$key.']' : 'configuration_value');
    if ($dir = opendir(DIR_FS_CATALOG.'includes/extra/captcha/')) {
      $captcha_mod_array = array(
        array('id' => 'modified_captcha', 'text' => xtc_multi_lang_values('modified_captcha')),
      );
      while (($captcha_mod = readdir($dir)) !== false) {
        if (is_file(DIR_FS_CATALOG.'includes/extra/captcha/'.$captcha_mod) && substr($captcha_mod, -4) == ".php") {
          $class = substr($captcha_mod, 0, -4);
          if (defined('MODULE_SYSTEM_'.strtoupper($class).'_STATUS')
              && constant('MODULE_SYSTEM_'.strtoupper($class).'_STATUS') == 'true'
              )
          {
            $captcha_mod_array[] = array ('id' => $class, 'text' => xtc_multi_lang_values($class));
          }
        }
      }
      closedir($dir);
      sort($captcha_mod_array);
      return xtc_draw_pull_down_menu($name, $captcha_mod_array, CAPTCHA_MOD_CLASS);
    }
  }


  /**
   * xtc_get_default_table_data()
   *
   * @param string $table
   * @return array
   */
  function xtc_get_default_table_data($table) {
    $default_array = array();
    $default_query = xtc_db_query("SHOW COLUMNS FROM ".$table."");
    while ($default = xtc_db_fetch_array($default_query)) {      
      $value = '';
      if ($default['Default'] != '') {
        $value = $default['Default'];
      } elseif (strtolower($default['Null']) == 'no'
                && (strpos(strtolower($default['Type']), 'int') !== false
                    || strpos(strtolower($default['Type']), 'decimal') !== false
                    )
                )
      {
        $value = 0;
      }
      $default_array[$default['Field']] = $value ;
    }
    return $default_array;
  }


  /**
   * xtc_cfg_image_extension()
   *
   * @param string $image_extension
   * @return dropdown
   */
  function xtc_cfg_image_extension($value) {
    $image_extension_array = array();
    $image_extension_array[] = array('id' => 'default', 'text' => 'Default');
    if (function_exists('imagewebp')) {
      $image_extension_array[] = array('id' => 'webp', 'text' => 'WebP');
    }
    return xtc_draw_pull_down_menu('configuration_value', $image_extension_array, $value);
  }


  function xtc_set_log_level($log_level) {
    $log_array = array();

    switch ($log_level) {
      case 'error':
        $log_array[] = '_error_reporting.err';
        break;
      case 'warning':
        $log_array[] = '_error_reporting.shop';
        $log_array[] = '_error_reporting.admin';
        break;
      case 'notice':
        $log_array[] = '_error_reporting.all';
        break;
      case 'dev':
        $log_array[] = '_error_reporting.dev';
        break;
      case 'none':
        $log_array[] = '_error_reporting.none';
        break;
    }

    foreach (glob(DIR_FS_CATALOG.'export/_error_reporting.*') as $filename) {
      if (!in_array(basename($filename), $log_array)) {
        unlink($filename);
      }
      if (in_array(basename($filename), $log_array)) {
        $log_array = array_diff($log_array, array(basename($filename)));
      }
    }
    
    if (count($log_array) > 0) {
      foreach ($log_array as $filename) {
        file_put_contents(DIR_FS_CATALOG.'export/'.$filename, '');
      }
    }
  }
  
  
  function xtc_cfg_check_not_empty($val) {
    if (empty($val)) {
      $val = TEXT_ERROR_EMPTY_NOT_ALLOWED;
    }
    return $val;
  }
  
  /********************************************** NOT USED FUNCTIONS **********************************************/
  
  /**
   * xtc_set_categories_status()
   *
   * @param mixed $categories_id
   * @param mixed $status
   * @return
   */
  function xtc_set_categories_status($categories_id, $status) {
    if ($status != '1' && $status != '0') {
      return -1;
    }
    return xtc_db_query("UPDATE ".TABLE_CATEGORIES." SET categories_status = '".(int)$status."' WHERE categories_id = '".(int)$categories_id."'");
  }

  /**
   * xtc_set_admin_access()
   *
   * @param mixed $fieldname
   * @param mixed $status
   * @param mixed $cID
   * @return
   */
  function xtc_set_admin_access($fieldname, $status, $customers_id) {
    if ($status != '1') {
      $status = '0';
    }
    return xtc_db_query("UPDATE ".TABLE_ADMIN_ACCESS." SET ".$fieldname." = '".(int)$status."' WHERE customers_id = '".(int)$customers_id."'");
  }

  /**
   * xtc_set_prodcat_data()
   *
   * @param mixed $categories_id
   * @param mixed $data_array
   * @param boolean $set_products, $set_categories
   * @return
   */
  function xtc_set_prodcat_data($categories_id, $data_array, $set_products = true, $set_categories = true) {
    //update products
    if ($set_products) {
      // get products in categorie
      $products_query = xtc_db_query("SELECT products_id
                                        FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
                                       WHERE categories_id='".(int)$categories_id."'
                                     ");
      while ($products = xtc_db_fetch_array($products_query)) {
        xtc_db_perform(TABLE_PRODUCTS, $data_array, 'update', "products_id = '".$products['products_id']."'");
      }
    }
    //update categorie
    if ($set_categories) {
      xtc_db_perform(TABLE_CATEGORIES, $data_array, 'update', "categories_id = '".(int)$categories_id."'");
    }
    // look for deeper categories and go rekursiv
    $categories_query = xtc_db_query("SELECT categories_id
                                        FROM ".TABLE_CATEGORIES."
                                       WHERE parent_id='".(int)$categories_id."'
                                    ");
    while ($categories = xtc_db_fetch_array($categories_query)) {
      xtc_set_prodcat_data($categories['categories_id'], $data_array, $set_products, $set_categories);
    }
  }

  /**
   * xtc_customers_name()
   *
   * @param mixed $customers_id
   * @return
   */
  function xtc_customers_name($customers_id) {
    $customers = xtc_db_query("SELECT customers_firstname,
                                      customers_lastname
                                 FROM ".TABLE_CUSTOMERS."
                                WHERE customers_id = '".(int)$customers_id."'");
    $customers_values = xtc_db_fetch_array($customers);
    return $customers_values['customers_firstname'].' '.$customers_values['customers_lastname'];
  }

  /**
   * xtc_options_name()
   *
   * @param mixed $options_id
   * @return
   */
  function xtc_options_name($options_id) {
    $options = xtc_db_query("SELECT products_options_name
                               FROM ".TABLE_PRODUCTS_OPTIONS."
                              WHERE products_options_id = '".(int)$options_id."'
                                AND language_id = '".(int)$_SESSION['languages_id']."'");
    $options_values = xtc_db_fetch_array($options);
    return $options_values['products_options_name'];
  }

  /**
   * xtc_values_name()
   *
   * @param mixed $values_id
   * @return
   */
  function xtc_values_name($values_id) {
    $values = xtc_db_query("SELECT products_options_values_name
                              FROM ".TABLE_PRODUCTS_OPTIONS_VALUES."
                             WHERE products_options_values_id = '".(int)$values_id."'
                               AND language_id = '".(int)$_SESSION['languages_id']."'");
    $values_values = xtc_db_fetch_array($values);
    return $values_values['products_options_values_name'];
  }

  /**
   * xtc_get_geo_zone_name()
   *
   * @param mixed $geo_zone_id
   * @return
   */
  function xtc_get_geo_zone_name($geo_zone_id) {
    require_once(DIR_FS_INC.'parse_multi_language_value.inc.php');

    $zones_query = xtc_db_query("SELECT geo_zone_name
                                   FROM ".TABLE_GEO_ZONES."
                                  WHERE geo_zone_id = '".(int)$geo_zone_id."'");
    if (!xtc_db_num_rows($zones_query)) {
      $geo_zone_name = $geo_zone_id;
    } else {
      $zones = xtc_db_fetch_array($zones_query);
      $geo_zone_name = parse_multi_language_value($zones['geo_zone_name'], $_SESSION['language_code']);
    }
    return $geo_zone_name;
  }

  /**
   * xtc_get_country_zones()
   *
   * @param mixed $country_id
   * @return
   */
  function xtc_get_country_zones($country_id) {
    $zones_array = array ();
    $zones_query = xtc_db_query("SELECT zone_id,
                                        zone_name
                                   FROM ".TABLE_ZONES."
                                  WHERE zone_country_id = '".(int)$country_id."'
                               ORDER BY zone_name");
    while ($zones = xtc_db_fetch_array($zones_query)) {
      $zones_array[] = array ('id' => $zones['zone_id'], 'text' => $zones['zone_name']);
    }
    return $zones_array;
  }

  /**
   * xtc_get_file_permissions()
   *
   * @param mixed $mode
   * @return
   */
  function xtc_get_file_permissions($mode) {
    // determine type
    if (($mode & 0xC000) == 0xC000) { // unix domain socket
      $type = 's';
    } elseif (($mode & 0x4000) == 0x4000) { // directory
      $type = 'd';
    } elseif (($mode & 0xA000) == 0xA000) { // symbolic link
      $type = 'l';
    } elseif (($mode & 0x8000) == 0x8000) { // regular file
      $type = '-';
    } elseif (($mode & 0x6000) == 0x6000) { //bBlock special file
      $type = 'b';
    } elseif (($mode & 0x2000) == 0x2000) { // character special file
      $type = 'c';
    } elseif (($mode & 0x1000) == 0x1000) { // named pipe
      $type = 'p';
    } else { // unknown
      $type = '?';
    }
    // determine permissions
    $owner['read'] = ($mode & 00400) ? 'r' : '-';
    $owner['write'] = ($mode & 00200) ? 'w' : '-';
    $owner['execute'] = ($mode & 00100) ? 'x' : '-';
    $group['read'] = ($mode & 00040) ? 'r' : '-';
    $group['write'] = ($mode & 00020) ? 'w' : '-';
    $group['execute'] = ($mode & 00010) ? 'x' : '-';
    $world['read'] = ($mode & 00004) ? 'r' : '-';
    $world['write'] = ($mode & 00002) ? 'w' : '-';
    $world['execute'] = ($mode & 00001) ? 'x' : '-';
    // adjust for SUID, SGID and sticky bit
    if ($mode & 0x800)
      $owner['execute'] = ($owner['execute'] == 'x') ? 's' : 'S';
    if ($mode & 0x400)
      $group['execute'] = ($group['execute'] == 'x') ? 's' : 'S';
    if ($mode & 0x200)
      $world['execute'] = ($world['execute'] == 'x') ? 't' : 'T';
    return $type.$owner['read'].$owner['write'].$owner['execute'].$group['read'].$group['write'].$group['execute'].$world['read'].$world['write'].$world['execute'];
  }

  /**
   * xtc_get_customer_status()
   *
   * @param mixed $customers_id
   * @return
   */
  function xtc_get_customer_status($customers_id) {
    $customer_status_array = array ();
    $customer_status_query = xtc_db_query("SELECT customers_status,
                                                  member_flag,
                                                  customers_status_name,
                                                  customers_status_public,
                                                  customers_status_image,
                                                  customers_status_discount,
                                                  customers_status_ot_discount_flag,
                                                  customers_status_ot_discount,
                                                  customers_status_graduated_prices
                                             FROM ".TABLE_CUSTOMERS."
                                        LEFT JOIN ".TABLE_CUSTOMERS_STATUS."
                                                  ON customers_status = customers_status_id
                                            WHERE customers_id='".$customers_id."'
                                              AND language_id = '".(int)$_SESSION['languages_id']."'");
    $customer_status_array = xtc_db_fetch_array($customer_status_query);
    return $customer_status_array;
  }

  /**
   * xtc_get_uploaded_file()
   *
   * @param mixed $filename
   * @return
   */
  function xtc_get_uploaded_file($filename) {
    if (isset ($_FILES[$filename])) {
      $uploaded_file = array ('name' => $_FILES[$filename]['name'], 'type' => $_FILES[$filename]['type'], 'size' => $_FILES[$filename]['size'], 'tmp_name' => $_FILES[$filename]['tmp_name']);
    } elseif (isset ($_FILES[$filename])) {
      $uploaded_file = array ('name' => $_FILES[$filename]['name'], 'type' => $_FILES[$filename]['type'], 'size' => $_FILES[$filename]['size'], 'tmp_name' => $_FILES[$filename]['tmp_name']);
    } else {
      $uploaded_file = array ('name' => $GLOBALS[$filename.'_name'], 'type' => $GLOBALS[$filename.'_type'], 'size' => $GLOBALS[$filename.'_size'], 'tmp_name' => $GLOBALS[$filename]);
    }
    return $uploaded_file;
  }

  /**
   * xtc_draw_date_selector()
   *
   * @param mixed $prefix
   * @param string $date
   * @return
   */
  function xtc_draw_date_selector($prefix, $date = '') {
    $month_array = array ();
    $month_array[1] = _JANUARY;
    $month_array[2] = _FEBRUARY;
    $month_array[3] = _MARCH;
    $month_array[4] = _APRIL;
    $month_array[5] = _MAY;
    $month_array[6] = _JUNE;
    $month_array[7] = _JULY;
    $month_array[8] = _AUGUST;
    $month_array[9] = _SEPTEMBER;
    $month_array[10] = _OCTOBER;
    $month_array[11] = _NOVEMBER;
    $month_array[12] = _DECEMBER;
    $usedate = getdate($date);
    $day = $usedate['mday'];
    $month = $usedate['mon'];
    $year = $usedate['year'];
    $date_selector = '<select name="'.$prefix.'_day">';
    for ($i = 1; $i < 32; $i ++) {
      $date_selector .= '<option value="'.$i.'"';
      if ($i == $day) {
        $date_selector .= 'selected';
      }
      $date_selector .= '>'.$i.'</option>';
    }
    $date_selector .= '</select>';
    $date_selector .= '<select name="'.$prefix.'_month">';
    for ($i = 1; $i < 13; $i ++) {
      $date_selector .= '<option value="'.$i.'"';
      if ($i == $month) {
        $date_selector .= 'selected';
      }
      $date_selector .= '>'.$month_array[$i].'</option>';
    }
    $date_selector .= '</select>';
    $date_selector .= '<select name="'.$prefix.'_year">';
    for ($i = date("Y"); $i < date("Y") + 4; $i ++) {
      $date_selector .= '<option value="'.$i.'"';
      if ($i == $year) {
        $date_selector .= 'selected';
      }
      $date_selector .= '>'.$i.'</option>';
    }
    $date_selector .= '</select>';
    return $date_selector;
  }

  /**
   * xtc_convert_value()
   * @note value correction function for wrong input prices, weight, dicscount
   * @author franky_n
   * @date 2011-01-17
   * @param mixed $number
   * @return
   */
  function xtc_convert_value($number) {
    // Correct wrong input number
    if ((strpos($number, ",") !== false) && (strpos($number, ".")) !== false) {
      // if price scheme like 1.000,00 change to 1000.00
      $number = str_replace(".","", $number);
      $number = str_replace(",",".", $number);
    }
    if (strpos($number, ",")) {
      // if price scheme like 1000,00 change to 1000.00
      $number = str_replace(",",".", $number);
    }
    return $number;
  }
