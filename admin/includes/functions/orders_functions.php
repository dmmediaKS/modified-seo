<?php
/* -----------------------------------------------------------------------------------------
   $Id: orders_functions.php 16384 2025-04-01 14:21:24Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


  defined('CHECKOUT_USE_PRODUCTS_SHORT_DESCRIPTION') OR define('CHECKOUT_USE_PRODUCTS_SHORT_DESCRIPTION', 'true');
  defined('DISCOUNT_MODULES') OR define('DISCOUNT_MODULES', 'ot_discount,ot_payment');
  defined('FORMAT_NEGATIVE') OR define('FORMAT_NEGATIVE', '<span class="color_ot_total"><b>%s</b></span>');


  // include needed functions
  require_once (DIR_FS_INC.'xtc_get_tax_class_id.inc.php');
  require_once (DIR_FS_INC.'xtc_get_tax_rate.inc.php');
  require_once (DIR_FS_INC.'xtc_oe_get_options_name.inc.php');
  require_once (DIR_FS_INC.'xtc_oe_get_options_values_name.inc.php');
  require_once (DIR_FS_INC.'xtc_oe_customer_infos.inc.php');
  require_once (DIR_FS_INC.'xtc_get_countries.inc.php');
  require_once (DIR_FS_INC.'xtc_get_address_format_id.inc.php');
  require_once (DIR_FS_INC.'get_customers_gender.inc.php');
  require_once (DIR_FS_INC.'xtc_get_vpe_name.inc.php');
  
  // include needed classes
  require_once (DIR_WS_CLASSES.'order.php');
  require_once (DIR_FS_CATALOG.DIR_WS_CLASSES.'xtcPrice.php');
  

  function get_customers_taxprice_status($status_id = '') {
    global $order, $lang;
    
    if ($status_id == '') {
      $status_id = $order->info['status'];
    }
    
    $status_query = xtc_db_query("SELECT customers_status_show_price_tax,
                                         customers_status_add_tax_ot,
                                         customers_status_discount,
                                         customers_status_discount_attributes,
                                         customers_status_show_tax_total,
                                         customers_status_name
                                    FROM ".TABLE_CUSTOMERS_STATUS."
                                   WHERE customers_status_id = '".(int)$status_id."'
                                     AND language_id = '".(int)$lang['languages_id']."'");
    return xtc_db_fetch_array($status_query);
  }


  function get_allow_tax($c_info, $status) {
    $allow_tax = 0;
    
    if ($status['customers_status_show_price_tax'] != 0) {
      $allow_tax = 1;
    }

    if ($status['customers_status_show_price_tax'] == 0 
        && $status['customers_status_add_tax_ot'] == 1
        ) 
    {
      $allow_tax = 2;
    }

    if ($status['customers_status_show_price_tax'] == 0 
        && $status['customers_status_add_tax_ot'] == 0
        ) 
    {
      $allow_tax = 3;
      if ($c_info['country_id'] != STORE_COUNTRY) {
        $allow_tax = 4;
      }
    }

    if ($c_info['country_id'] != STORE_COUNTRY) {
      $countries = array();
      $geo_zone_array = array();
      
      $geo_zone_query = xtc_db_query("SELECT geo_zone_id 
                                        FROM ".TABLE_GEO_ZONES." 
                                       WHERE geo_zone_info = '1'");
      if (xtc_db_num_rows($geo_zone_query) > 0) {
        while ($geo_zone = xtc_db_fetch_array($geo_zone_query)) {
          $geo_zone_array[] = $geo_zone['geo_zone_id'];
        }
        $duty_countries_query = xtc_db_query("SELECT c.countries_id
                                                FROM ".TABLE_COUNTRIES." c
                                                JOIN " . TABLE_ZONES_TO_GEO_ZONES . " gz 
                                                     ON c.countries_id = gz.zone_country_id
                                               WHERE gz.geo_zone_id IN ('".implode("', '", $geo_zone_array)."')");
        if (xtc_db_num_rows($duty_countries_query, true)) {
          while ($duty_countries = xtc_db_fetch_array($duty_countries_query, true)) {
            $countries[] = $duty_countries['countries_id'];
          }
        }
      }
      
      if (in_array($c_info['country_id'], $countries)) {
        $allow_tax = 0;
      }
    }

    return $allow_tax;
  }


  function calculate_price_tax($price, $tax, $allow_tax_old, $allow_tax_new) {
    global $xtPrice;
    
    if ($allow_tax_old == 1 && $allow_tax_new != 1) {
      $price = $xtPrice->xtcRemoveTax($price, $tax);
    }

    if ($allow_tax_old != 1 && $allow_tax_new == 1) {
      $price = $xtPrice->xtcAddTax($price, $tax, false);
    }
    
    return $price;
  }


  function calculate_tax($amount, $oID) {
    global $xtPrice, $status;

    $price = 'b_price';
    if ($status['customers_status_show_price_tax'] == 0 && $status['customers_status_add_tax_ot'] == 1) {
      $price = 'n_price';
    }

    $sum_query = xtc_db_query("SELECT SUM(".$price.") as price 
                                 FROM ".TABLE_ORDERS_RECALCULATE." 
                                WHERE orders_id = '".(int)$oID."' 
                                  AND class = 'products'");
    $sum_total = xtc_db_fetch_array($sum_query);

    if ($sum_total['price'] == 0) {
      return 0;
    }
    $amount_pro = $amount / $sum_total['price'] * 100;

    $tax_rate_query = xtc_db_query("SELECT tax_rate 
                                      FROM ".TABLE_ORDERS_RECALCULATE." 
                                     WHERE orders_id = '".(int)$oID."' 
                                       AND class = 'ot_tax' 
                                  GROUP BY tax_rate");

    $tod_amount = 0;
    while ($tax_rate = xtc_db_fetch_array($tax_rate_query)) {
      $tax_query = xtc_db_query("SELECT SUM(tax) as value 
                                   FROM ".TABLE_ORDERS_RECALCULATE." 
                                  WHERE orders_id = '".(int)$oID."' 
                                    AND tax_rate = '". $tax_rate['tax_rate']."'
                                    AND class = 'products'");
      $tax_total = xtc_db_fetch_array($tax_query);

      $god_amount = $tax_total['value'] * $amount_pro / 100;

      $new_tax_query = xtc_db_query("SELECT tax as value 
                                       FROM ".TABLE_ORDERS_RECALCULATE." 
                                      WHERE orders_id = '".(int)$oID."' 
                                        AND tax_rate = '". $tax_rate['tax_rate']."'
                                        AND class = 'ot_tax'");
      $new_tax_total = xtc_db_fetch_array($new_tax_query);
      $new_tax = $new_tax_total['value'] + $god_amount;
    
      xtc_db_query("UPDATE ".TABLE_ORDERS_RECALCULATE."
                       SET tax = '".xtc_db_prepare_input($new_tax)."'
                     WHERE orders_id = '".(int)$oID."'
                       AND tax_rate = '".xtc_db_prepare_input($tax_rate['tax_rate'])."'
                       AND class = 'ot_tax'");

      $tod_amount += $god_amount;
    }

    return $tod_amount;
  }


  function get_c_infos($customers_id, $delivery_country_iso_code_2) {
    $countries_query = xtc_db_query("SELECT countries_id
                                       FROM ".TABLE_COUNTRIES."
                                      WHERE countries_iso_code_2 = '".$delivery_country_iso_code_2."'");
    $countries = xtc_db_fetch_array($countries_query);

    $zone_id = -1;
    if ($countries['countries_id'] > 0) {
      $zones_query = xtc_db_query("SELECT z.zone_id
                                     FROM " . TABLE_ORDERS . " o
                                     JOIN " . TABLE_ZONES . " z
                                          ON z.zone_name = o.delivery_state
                                    WHERE o.customers_id = '" . $customers_id . "'
                                      AND z.zone_country_id = '" . $countries['countries_id'] . "'");
      if (xtc_db_num_rows($zones_query) > 0) {
        $zones = xtc_db_fetch_array($zones_query);
        $zone_id = $zones['zone_id'];
      }
    }

    $c_info_array = array(
      'country_id' => $countries['countries_id'],
      'zone_id' => $zone_id,
    );

    return $c_info_array;
  }


  function orders_payment_edit($oID, $data_array) {
    $sql_data_array = array (
      'payment_method' => xtc_db_prepare_input($data_array['payment']),
      'payment_class' => xtc_db_prepare_input($data_array['payment']),
      'last_modified' => 'now()'
    );
    xtc_db_perform(TABLE_ORDERS, $sql_data_array, 'update', "orders_id = '".(int)$oID."'");
  }


  function orders_shipping_edit($oID, $data_array) {
    global $order, $xtPrice;

    $module = $data_array['shipping'].'.php';
    require (DIR_FS_LANGUAGES.$order->info['language'].'/modules/shipping/'.$module);
    $shipping_text = constant('MODULE_SHIPPING_'.strtoupper($data_array['shipping']).'_TEXT_TITLE');
    $shipping_class = $data_array['shipping'].'_'.$data_array['shipping'];

    $text = $xtPrice->xtcFormat($data_array['value'], true);

    $shipping_order = (int)(MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER);
    $sql_data_array = array(
      'orders_id' => (int)($oID),
      'title' => xtc_db_prepare_input($shipping_text),
      'text' => $text,
      'value' => xtc_db_prepare_input($data_array['value']),
      'class' => 'ot_shipping',
      'sort_order' => xtc_db_prepare_input($shipping_order),
    );

    $check_shipping_query = xtc_db_query("SELECT class 
                                            FROM ".TABLE_ORDERS_TOTAL." 
                                           WHERE orders_id = '".(int)$oID."' 
                                             AND class = 'ot_shipping'");
    if (xtc_db_num_rows($check_shipping_query) > 0) {
      xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array, 'update', "orders_id = '".(int)($oID)."' AND class = 'ot_shipping'");
    } else {
      xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
    }

    $sql_data_array = array (
      'shipping_method' => xtc_db_prepare_input($shipping_text),
      'shipping_class' => xtc_db_prepare_input($shipping_class),
      'last_modified' => 'now()'
    );
    xtc_db_perform(TABLE_ORDERS, $sql_data_array, 'update', "orders_id = '".(int)$oID."'");
  }


  function orders_lang_edit($oID, $data_array) {
    $lang_query = xtc_db_query("SELECT languages_id, 
                                       name, 
                                       directory 
                                  FROM ".TABLE_LANGUAGES." 
                                 WHERE languages_id = '".(int)$data_array['lang']."'");
    $lang = xtc_db_fetch_array($lang_query);

    $order_products_query = xtc_db_query("SELECT orders_products_id, 
                                                 products_id 
                                            FROM ".TABLE_ORDERS_PRODUCTS." 
                                           WHERE orders_id = '".(int)$oID."'");
    while ($order_products = xtc_db_fetch_array($order_products_query)) {
      $products_query = xtc_db_query("SELECT products_name
                                        FROM ".TABLE_PRODUCTS_DESCRIPTION."
                                       WHERE products_id = '".(int)$order_products['products_id']."'
                                         AND language_id = '".(int)$data_array['lang']."'");
      $products = xtc_db_fetch_array($products_query);

      $sql_data_array = array(
        'products_name' => xtc_db_prepare_input($products['products_name'])
      );
      xtc_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array, 'update', "orders_products_id = '".(int)($order_products['orders_products_id'])."'");
    }

    $order_total_query = xtc_db_query("SELECT orders_total_id, 
                                              title, 
                                              class 
                                         FROM ".TABLE_ORDERS_TOTAL." 
                                        WHERE orders_id = '".(int)$oID."'");
    while ($order_total = xtc_db_fetch_array($order_total_query)) {
      if (is_file(DIR_FS_LANGUAGES.$lang['directory'].'/modules/order_total/'.$order_total['class'].'.php')) {
        require_once (DIR_FS_LANGUAGES.$lang['directory'].'/modules/order_total/'.$order_total['class'].'.php');
      }
      $name = str_replace('ot_', '', $order_total['class']);
      $text = ((defined('MODULE_ORDER_TOTAL_'.strtoupper($name).'_TITLE')) ? constant('MODULE_ORDER_TOTAL_'.strtoupper($name).'_TITLE') : $order_total['title']);

      $sql_data_array = array(
        'title' => xtc_db_prepare_input($text),
      );
      xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array, 'update', "orders_total_id  = '".(int)($order_total['orders_total_id'])."'");
    }

    $sql_data_array = array(
      'language' => xtc_db_prepare_input($lang['directory']), 
      'last_modified' => 'now()',
    );
    xtc_db_perform(TABLE_ORDERS, $sql_data_array, 'update', "orders_id = '".(int)$oID."'");
  }


  function orders_curr_edit($oID, $data_array) {
    global $order, $xtPrice;

    $curr_query = xtc_db_query("SELECT currencies_id,
                                       title,
                                       code,
                                       value
                                  FROM ".TABLE_CURRENCIES."
                                 WHERE currencies_id = '".(int)$data_array['currencies_id']."'");
    $curr = xtc_db_fetch_array($curr_query);

    $old_curr_query = xtc_db_query("SELECT currencies_id, 
                                           title, 
                                           code, 
                                           value 
                                      FROM ".TABLE_CURRENCIES." 
                                     WHERE code = '".$data_array['old_currency']."'");
    $old_curr = xtc_db_fetch_array($old_curr_query);

    $sql_data_array = array(
      'currency' => xtc_db_prepare_input($curr['code']),
      'currency_value'=>xtc_db_prepare_input($curr['value']),
    );
    xtc_db_perform(TABLE_ORDERS, $sql_data_array, 'update', "orders_id = '".(int)$oID."'");

    $order_products_query = xtc_db_query("SELECT orders_products_id, 
                                                 products_id, 
                                                 products_price, 
                                                 final_price 
                                            FROM ".TABLE_ORDERS_PRODUCTS." 
                                           WHERE orders_id = '".(int)$oID."'");
    while ($order_products = xtc_db_fetch_array($order_products_query)) {
      if ($old_curr['code'] == DEFAULT_CURRENCY) {
        $xtPrice = new xtcPrice($curr['code'], $order->info['status']);
        $products_price = $xtPrice->xtcGetPrice($order_products['products_id'], $format = false, '', '', $order_products['products_price'], '', $order->customer['ID']);
        $final_price = $xtPrice->xtcGetPrice($order_products['products_id'], $format = false, '', '', $order_products['final_price'], '', $order->customer['ID']);
      } else {
        $xtPrice = new xtcPrice($old_curr['code'], $order->info['status']);
        $p_price = $xtPrice->xtcRemoveCurr($order_products['products_price']);
        $f_price = $xtPrice->xtcRemoveCurr($order_products['final_price']);
        $xtPrice = new xtcPrice($curr['code'], $order->info['status']);
        $products_price = $xtPrice->xtcGetPrice($order_products['products_id'], $format = false, '', '', $p_price, '', $order->customer['ID']);
        $final_price = $xtPrice->xtcGetPrice($order_products['products_id'], $format = false, '', '', $f_price, '', $order->customer['ID']);
      }
    
      $sql_data_array = array(
        'products_price' => xtc_db_prepare_input($products_price), 
        'final_price' => xtc_db_prepare_input($final_price),
      );
      xtc_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array, 'update', "orders_products_id = '".(int)($order_products['orders_products_id'])."'");
    };

    $order_total_query = xtc_db_query("SELECT orders_total_id, 
                                              value 
                                         FROM ".TABLE_ORDERS_TOTAL." 
                                        WHERE orders_id = '".(int)$oID."'");
    while ($order_total = xtc_db_fetch_array($order_total_query)) {
      if ($old_curr['code'] == DEFAULT_CURRENCY) {
        $xtPrice = new xtcPrice($curr['code'], $order->info['status']);
        $value = $xtPrice->xtcGetPrice('', $format = false, '', '', $order_total['value'], '', $order->customer['ID']);
      } else {
        $xtPrice = new xtcPrice($old_curr['code'], $order->info['status']);
        $nvalue = $xtPrice->xtcRemoveCurr($order_total['value']);
        $xtPrice = new xtcPrice($curr['code'], $order->info['status']);
        $value = $xtPrice->xtcGetPrice('', $format = false, '', '', $nvalue, '', $order->customer['ID']);
      }
      $text = $xtPrice->xtcFormat($value, true);

      $sql_data_array = array(
        'text' => $text,
        'value' => xtc_db_prepare_input($value),
      );
      xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array, 'update', "orders_total_id = '".(int)($order_total['orders_total_id'])."'");
    }

    xtc_db_perform(TABLE_ORDERS, array('last_modified' => 'now()'), 'update', "orders_id = '".(int)$oID."'");
  }


  function orders_address_edit($oID, $data_array) {
    global $order, $lang, $messageStack;

    if ($order->info['status'] != $data_array['customers_status']) {
      $messageStack->add_session(ERROR_STATUS_CHANGE);
    }
  
    $customers_country = xtc_get_countriesList(xtc_db_prepare_input($data_array['customers_country_id']), true);
    $delivery_country = xtc_get_countriesList(xtc_db_prepare_input($data_array['delivery_country_id']), true);
    $billing_country = xtc_get_countriesList(xtc_db_prepare_input($data_array['billing_country_id']), true);

    $lang_query = xtc_db_query("SELECT languages_id 
                                  FROM ".TABLE_LANGUAGES." 
                                 WHERE directory = '".xtc_db_input($order->info['language'])."'");
    $lang = xtc_db_fetch_array($lang_query);

    $status = get_customers_taxprice_status($data_array['customers_status']);

    $sql_data_array = array(
      'customers_vat_id' => xtc_db_prepare_input($data_array['customers_vat_id']),
      'customers_status' => xtc_db_prepare_input($data_array['customers_status']),
      'customers_status_name' => xtc_db_prepare_input($status['customers_status_name']),
      'customers_company' => xtc_db_prepare_input($data_array['customers_company']),
      'customers_firstname' => xtc_db_prepare_input($data_array['customers_firstname']),
      'customers_lastname' => xtc_db_prepare_input($data_array['customers_lastname']),
      'customers_name' => xtc_db_prepare_input($data_array['customers_firstname']) . ' ' . xtc_db_prepare_input($data_array['customers_lastname']),
      'customers_street_address' => xtc_db_prepare_input($data_array['customers_street_address']),
      'customers_city' => xtc_db_prepare_input($data_array['customers_city']),
      'customers_postcode' => xtc_db_prepare_input($data_array['customers_postcode']),
      'customers_country' => $customers_country['countries_name'],
      'customers_country_iso_code_2' => $customers_country['countries_iso_code_2'],
      'customers_telephone' => xtc_db_prepare_input($data_array['customers_telephone']),
      'customers_email_address' => xtc_db_prepare_input($data_array['customers_email_address']),
      'customers_address_format_id' => xtc_get_address_format_id($data_array['customers_country_id']),
      'customers_cid' => xtc_db_prepare_input($data_array['customers_cid']),
      'delivery_company' => xtc_db_prepare_input($data_array['delivery_company']),
      'delivery_firstname' => xtc_db_prepare_input($data_array['delivery_firstname']),
      'delivery_lastname' => xtc_db_prepare_input($data_array['delivery_lastname']),
      'delivery_name' => xtc_db_prepare_input($data_array['delivery_firstname']) . ' ' . xtc_db_prepare_input($data_array['delivery_lastname']),
      'delivery_street_address' => xtc_db_prepare_input($data_array['delivery_street_address']),
      'delivery_city' => xtc_db_prepare_input($data_array['delivery_city']),
      'delivery_postcode' => xtc_db_prepare_input($data_array['delivery_postcode']),
      'delivery_country' => $delivery_country['countries_name'],
      'delivery_country_iso_code_2' => $delivery_country['countries_iso_code_2'],
      'delivery_address_format_id' => xtc_get_address_format_id($data_array['delivery_country_id']),
      'billing_company' => xtc_db_prepare_input($data_array['billing_company']),
      'billing_firstname' => xtc_db_prepare_input($data_array['billing_firstname']),
      'billing_lastname' => xtc_db_prepare_input($data_array['billing_lastname']),
      'billing_name' => xtc_db_prepare_input($data_array['billing_firstname']) . ' ' . xtc_db_prepare_input($data_array['billing_lastname']),
      'billing_street_address' => xtc_db_prepare_input($data_array['billing_street_address']),
      'billing_city' => xtc_db_prepare_input($data_array['billing_city']),
      'billing_postcode' => xtc_db_prepare_input($data_array['billing_postcode']),
      'billing_country' => $billing_country['countries_name'],
      'billing_country_iso_code_2' => $billing_country['countries_iso_code_2'],
      'billing_address_format_id' => xtc_get_address_format_id($data_array['billing_country_id']),
      'last_modified' => 'now()'
    );
  
    if (ACCOUNT_GENDER == 'true') {
      $sql_data_array['customers_gender'] = xtc_db_prepare_input($data_array['customers_gender']);
      $sql_data_array['delivery_gender'] = xtc_db_prepare_input($data_array['delivery_gender']);
      $sql_data_array['billing_gender'] = xtc_db_prepare_input($data_array['billing_gender']);
    }

    if (ACCOUNT_SUBURB == 'true') {
      $sql_data_array['customers_suburb'] = xtc_db_prepare_input($data_array['customers_suburb']);
      $sql_data_array['delivery_suburb'] = xtc_db_prepare_input($data_array['delivery_suburb']);
      $sql_data_array['billing_suburb'] = xtc_db_prepare_input($data_array['billing_suburb']);
    }

    if (ACCOUNT_STATE == 'true') {
      $sql_data_array['customers_state'] = xtc_db_prepare_input($data_array['customers_state']);
      $sql_data_array['delivery_state'] = xtc_db_prepare_input($data_array['delivery_state']);
      $sql_data_array['billing_state'] = xtc_db_prepare_input($data_array['billing_state']);
    }
  
    xtc_db_perform(TABLE_ORDERS, $sql_data_array, 'update', "orders_id = '".(int)$oID."'");
    
    $products_query = xtc_db_query("SELECT *,
                                           orders_products_id as opID,
                                           products_quantity as old_qty,
                                           products_weight_origin as old_weight
                                      FROM ".TABLE_ORDERS_PRODUCTS."
                                     WHERE orders_id = '".(int)$oID."'");
    if (xtc_db_num_rows($products_query) > 0) {
      $order = new order($oID);
      while ($products = xtc_db_fetch_array($products_query)) {
        orders_product_edit($oID, $products);
      }
    }
  }


  function orders_product_edit($oID, $data_array) {
    global $order, $xtPrice, $lang;
      
    if (empty($data_array['products_price'])) {
      $data_array['products_price'] = 0;
    }

    $lang_query = xtc_db_query("SELECT languages_id 
                                  FROM ".TABLE_LANGUAGES." 
                                 WHERE directory = '".xtc_db_input($order->info['language'])."'");
    $lang = xtc_db_fetch_array($lang_query);

    $status = get_customers_taxprice_status();

    $c_info = get_c_infos($order->customer['ID'], $order->delivery['country_iso_2']);

    $allow_tax = get_allow_tax($c_info, $status);

    $product_query = xtc_db_query("SELECT op.allow_tax,
                                          op.products_tax,
                                          p.products_tax_class_id,
                                          pd.products_name,
                                          pd.products_short_description,
                                          pd.products_order_description
                                     FROM ".TABLE_ORDERS_PRODUCTS." op
                                LEFT JOIN ".TABLE_PRODUCTS." p 
                                          ON op.products_id = p.products_id
                                LEFT JOIN ".TABLE_PRODUCTS_DESCRIPTION." pd 
                                          ON op.products_id = pd.products_id 
                                             AND pd.language_id = '".(int)$lang['languages_id']."'
                                    WHERE op.products_id = '".(int)$data_array['products_id']."'
                                      AND op.orders_products_id = '".(int)$data_array['opID']."'");
    $product = xtc_db_fetch_array($product_query);

    $product['products_tax_class_id'] = $xtPrice->xtc_get_tax_class($data_array['products_id'], $product['products_tax_class_id']);

    if ($status['customers_status_show_price_tax'] == 1
        && $status['customers_status_add_tax_ot'] == 0
        && $xtPrice->get_content_type_product((int)$data_array['products_id']) == 'virtual'
        ) 
    {
      $product['products_tax_class_id'] = xtc_get_tax_class($product['products_tax_class_id'], $c_info['country_id'], $c_info['zone_id']);
    }

    $tax_rate = xtc_get_tax_rate($product['products_tax_class_id'], $c_info['country_id'], $c_info['zone_id']);

    if (in_array($allow_tax, array('0', '4'))) {
      $tax_rate = 0;
    }
    
    $data_array['products_price'] = calculate_price_tax($data_array['products_price'], $tax_rate, $product['allow_tax'], $allow_tax);
    
    $final_price = $data_array['products_price'] * (int)$data_array['products_quantity'];
  
    $product['products_short_description'] = CHECKOUT_USE_PRODUCTS_SHORT_DESCRIPTION == 'true' ? $product['products_short_description'] : '';        
    $product['products_order_description'] = !empty($product['products_order_description']) ? nl2br($product['products_order_description']) : $product['products_short_description'];

    $sql_data_array = array (
      'orders_id' => (int)($oID),
      'products_name' => xtc_db_prepare_input($data_array['products_name']),
      'products_order_description' => xtc_db_prepare_input($product['products_order_description']),
      'products_price' => (float)$data_array['products_price'],
      'final_price' => (float)$final_price,
      'products_tax' => xtc_db_prepare_input($tax_rate),
      'products_quantity' => xtc_db_prepare_input($data_array['products_quantity']),
      'allow_tax' => $allow_tax,
      'products_model' => xtc_db_prepare_input($data_array['products_model']),
      'products_weight_origin' => xtc_db_prepare_input($data_array['products_weight_origin']),
    );
    xtc_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array, 'update', "orders_products_id = '".(int)($data_array['opID'])."'");

    $new_qty = (double)$data_array['old_qty'] - (double)$data_array['products_quantity'];
    xtc_db_query("UPDATE ".TABLE_PRODUCTS." 
                     SET products_quantity = products_quantity + " . $new_qty . " 
                   WHERE products_id = '" . (int)$data_array['products_id'] . "'");

    // Update Attributes Stock
    if (STOCK_LIMITED == 'true') {
      $delete_products_attributes_query = xtc_db_query("SELECT *
                                                          FROM ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES."
                                                         WHERE orders_products_id = '".(int)$data_array['opID']."'");
      while ($delete_products_attributes = xtc_db_fetch_array($delete_products_attributes_query)) {
        xtc_db_query("UPDATE " . TABLE_PRODUCTS_ATTRIBUTES . " 
                         SET attributes_stock = attributes_stock + '".$new_qty."'
                       WHERE products_id = '".(int)$data_array['products_id']."' 
                         AND options_id = '".(int)$delete_products_attributes['orders_products_options_id']."'
                         AND options_values_id = '".(int)$delete_products_attributes['orders_products_options_values_id']."'");
      }
    }

    // Update products_ordered
    xtc_db_query("UPDATE ".TABLE_PRODUCTS." 
                     SET products_ordered = products_ordered - ".sprintf('%d', $new_qty)." 
                   WHERE products_id = '".(int)$data_array['products_id']."'");

    xtc_db_perform(TABLE_ORDERS, array('last_modified' => 'now()'), 'update', "orders_id = '".(int)$oID."'");
    
    if (round($data_array['products_weight_origin'], 4) != round($data_array['old_weight'], 4)) {
      orders_product_update($oID, $data_array, $status);
    }
  }


  function orders_product_insert($oID, $data_array) {
    global $order, $xtPrice, $lang;
  
    $lang_query = xtc_db_query("SELECT languages_id 
                                  FROM ".TABLE_LANGUAGES." 
                                 WHERE directory = '".xtc_db_input($order->info['language'])."'");
    $lang = xtc_db_fetch_array($lang_query);

    $status = get_customers_taxprice_status();

    $c_info = get_c_infos($order->customer['ID'], $order->delivery['country_iso_2']);

    $allow_tax = get_allow_tax($c_info, $status);

    $product_query = xtc_db_query("SELECT p.products_model,
                                          p.products_ean,
                                          p.products_vpe,
                                          p.products_vpe_status,
                                          p.products_vpe_value,
                                          p.products_weight,
                                          p.products_tax_class_id,
                                          p.products_price as products_price_origin,
                                          pd.products_name,
                                          pd.products_short_description,
                                          pd.products_order_description,
                                          ps.shipping_status_name
                                     FROM ".TABLE_PRODUCTS." p
                                     JOIN ".TABLE_PRODUCTS_DESCRIPTION." pd
                                          ON pd.products_id = p.products_id
                                             AND pd.language_id = '".(int)$lang['languages_id']."'
                                LEFT JOIN ".TABLE_SHIPPING_STATUS." ps
                                          ON p.products_shippingtime = ps.shipping_status_id
                                             AND ps.language_id = '".(int)$lang['languages_id']."'
                                    WHERE p.products_id = '".(int)$data_array['products_id']."'");
    $product = xtc_db_fetch_array($product_query);

    $product['products_tax_class_id'] = $xtPrice->xtc_get_tax_class($data_array['products_id'], $product['products_tax_class_id']);
    
    if ($status['customers_status_show_price_tax'] == 1
        && $status['customers_status_add_tax_ot'] == 0
        && $xtPrice->get_content_type_product((int)$data_array['products_id']) == 'virtual'
        ) 
    {
      $product['products_tax_class_id'] = xtc_get_tax_class($product['products_tax_class_id'], $c_info['country_id'], $c_info['zone_id']);
    }

    $tax_rate = xtc_get_tax_rate($product['products_tax_class_id'], $c_info['country_id'], $c_info['zone_id']);

    if (in_array($allow_tax, array('0', '4'))) {
      $tax_rate = 0;
    }

    $price = $xtPrice->xtcGetPrice($data_array['products_id'], $format = false, $data_array['products_quantity'], $product['products_tax_class_id'], '', '', $order->customer['ID']);
    if ($status['customers_status_show_price_tax'] != 1) {
      $price = round($price, $xtPrice->currencies[$xtPrice->actualCurr]['decimal_places']);
    }

    if ($products_price_origin = $xtPrice->xtcGetGraduatedPrice($data_array['products_id'], 1)) {
      $product['products_price_origin'] = $products_price_origin;
    }

    $final_price = $price * (int)$data_array['products_quantity'];
  
    $product['products_short_description'] = CHECKOUT_USE_PRODUCTS_SHORT_DESCRIPTION == 'true' ? $product['products_short_description'] : '';        
    $product['products_order_description'] = !empty($product['products_order_description']) ? nl2br($product['products_order_description']) : $product['products_short_description'];

    $sql_data_array = array(
      'orders_id' => (int)($oID),
      'products_id' => (int)($data_array['products_id']),
      'products_model' => xtc_db_prepare_input($product['products_model']),
      'products_ean' => xtc_db_prepare_input($product['products_ean']),
      'products_name' => xtc_db_prepare_input($product['products_name']),
      'products_price' => (float)$price,
      'products_price_origin' => (float)$product['products_price_origin'],
      'products_shipping_time' => xtc_db_prepare_input($product['shipping_status_name']),
      'products_discount_made' => 0,
      'final_price' => (float)$final_price,
      'products_tax' => xtc_db_prepare_input($tax_rate),
      'products_quantity' => xtc_db_prepare_input($data_array['products_quantity']),
      'allow_tax' => $allow_tax,
      'products_order_description' => xtc_db_prepare_input($product['products_order_description']),
      'products_weight' => $product['products_weight'],
      'products_weight_origin' => $product['products_weight'],
    );
    
    if ($product['products_vpe_value'] == 1) {
      $sql_data_array['products_vpe'] = xtc_get_vpe_name($product['products_vpe'], $lang['languages_id']);
      $sql_data_array['products_vpe_value'] = $product['products_vpe_value'];
    }
        
    xtc_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array);

    if ($data_array['products_quantity'] != 0) {
      xtc_db_query("UPDATE ".TABLE_PRODUCTS." 
                       SET products_quantity = products_quantity - ".sprintf('%d', (double)$data_array['products_quantity'])." 
                     WHERE products_id = ".(int)$data_array['products_id']);

      xtc_db_query("UPDATE ".TABLE_SPECIALS." 
                       SET specials_quantity = specials_quantity - ".sprintf('%d', (double)$data_array['products_quantity'])." 
                     WHERE products_id = ".(int)$data_array['products_id']."
                       AND specials_quantity != 0");
    
      // Update products_ordered (for bestsellers list)
      xtc_db_query("UPDATE ".TABLE_PRODUCTS." 
                       SET products_ordered = products_ordered + ".sprintf('%d', (double)$data_array['products_quantity'])." 
                     WHERE products_id = '".(int)($data_array['products_id'])."'");
    }

    xtc_db_perform(TABLE_ORDERS, array('last_modified' => 'now()'), 'update', "orders_id = '".(int)$oID."'");
  }


  function orders_product_delete($oID, $data_array) {

    // Update Attributes Stock
    if (STOCK_LIMITED == 'true') {
      $delete_products_attributes_query = xtc_db_query("SELECT *
                                                          FROM ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." opa
                                                          JOIN ".TABLE_ORDERS_PRODUCTS." op
                                                               ON op.orders_products_id=opa.orders_products_id
                                                         WHERE op.orders_products_id = '".(int)$data_array['opID']."'");
      while ($delete_products_attributes = xtc_db_fetch_array($delete_products_attributes_query)) {
        xtc_db_query("UPDATE " . TABLE_PRODUCTS_ATTRIBUTES . " 
                         SET attributes_stock = attributes_stock + ".sprintf('%d', (double)$delete_products_attributes['products_quantity'])."
                       WHERE products_id = '".(int)$delete_products_attributes['products_id']."' 
                         AND options_id = '".(int)$delete_products_attributes['orders_products_options_id']."'
                         AND options_values_id = '".(int)$delete_products_attributes['orders_products_options_values_id']."'");
      }
    }

    xtc_db_query("DELETE FROM ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." WHERE orders_products_id = '".(int)($data_array['opID'])."'");
    xtc_db_query("DELETE FROM ".TABLE_ORDERS_PRODUCTS." WHERE orders_id = '".(int)($oID)."' AND orders_products_id = '".(int)($data_array['opID'])."'");

    xtc_db_query("UPDATE ".TABLE_PRODUCTS." 
                     SET products_quantity = products_quantity + ".sprintf('%d', (double)$data_array['del_qty'])."
                   WHERE products_id = ".(int)$data_array['products_id']);

    xtc_db_query("UPDATE ".TABLE_SPECIALS." 
                     SET specials_quantity = specials_quantity + ".sprintf('%d', (double)$data_array['del_qty'])."
                   WHERE products_id = ".(int)$data_array['products_id']."
                     AND specials_quantity != 0");

    xtc_db_perform(TABLE_ORDERS, array('last_modified' => 'now()'), 'update', "orders_id = '".(int)$oID."'");
  }


  function orders_product_update($oID, $data_array, $status) {
    global $order, $xtPrice, $lang;

    $products_query = xtc_db_query("SELECT op.products_id, 
                                           op.products_quantity,
                                           op.products_discount_made, 
                                           op.products_tax, 
                                           op.allow_tax,
                                           op.products_weight_origin
                                      FROM ".TABLE_ORDERS_PRODUCTS." op
                                 LEFT JOIN ".TABLE_PRODUCTS." p
                                           ON p.products_id = op.products_id
                                     WHERE op.orders_products_id = '".(int)$data_array['opID']."'");
    $products = xtc_db_fetch_array($products_query);

    $products_a_query = xtc_db_query("SELECT options_values_price, 
                                             price_prefix
                                        FROM ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." 
                                       WHERE orders_products_id = '".(int)$data_array['opID']."'
                                         AND options_values_price > 0");
    $ov_price = 0;
    $price_prefix = false;
    while ($products_a = xtc_db_fetch_array($products_a_query)) {    
      switch ($products_a['price_prefix']) {
        case '+':
          $ov_price += $products_a['options_values_price'];
          break;
        case '-':
          $ov_price += $products_a['options_values_price'] * (-1);
          break;
        case '=':
          if (xtc_db_num_rows($products_a_query) == 1) {
            $price_prefix = true;
            $ov_price = $products_a['options_values_price'];
          }
          break;
      }    
    }

    $products_a_query = xtc_db_query("SELECT options_values_weight,
                                             weight_prefix
                                        FROM ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." 
                                       WHERE orders_products_id = '".(int)$data_array['opID']."'
                                         AND options_values_weight > 0");
    $ov_weight = 0;
    $weight_prefix = false;
    while ($products_a = xtc_db_fetch_array($products_a_query)) {    
      switch ($products_a['weight_prefix']) {
        case '+':
          $ov_weight += $products_a['options_values_weight'];
          break;
        case '-':
          $ov_weight += $products_a['options_values_weight'] * (-1);
          break;
        case '=':
          if (xtc_db_num_rows($products_a_query) == 1) {
            $weight_prefix = true;
            $ov_weight = $products_a['options_values_weight'];
          }
          break;
      }    
    }

    $discount = 0;
    if ($status['customers_status_discount_attributes'] == 1 && $status['customers_status_discount'] != 0.00 && $ov_price > 0.00) {
      $discount = $status['customers_status_discount'];
      if ($products['products_discount_made'] < $status['customers_status_discount']) {
        $discount = $products['products_discount_made'];
      }
      $ov_price -= $ov_price / 100 * $discount;
    }

    $products_price = $ov_price;
    if ($price_prefix === false) {
      $products_old_price = $xtPrice->xtcGetPrice($products['products_id'], $format = false, $products['products_quantity'], '', '', '', $order->customer['ID']);
      $products_price = ($products_old_price + $ov_price);
    }

    $products_weight = $ov_weight;
    if ($weight_prefix === false) {
      $products_weight = $products['products_weight_origin'] + $ov_weight;
    }

    $tax_rate = $products['products_tax'];
    if (($status['customers_status_show_price_tax'] == 0 
         && $status['customers_status_add_tax_ot'] == 0
         ) || $products['allow_tax'] != '1'
        ) 
    {
      $tax_rate = 0;
    }

    $price = $xtPrice->xtcAddTax($products_price, $tax_rate); //tax by products
    if ($status['customers_status_show_price_tax'] != 1) {
      $price = round($price, $xtPrice->currencies[$xtPrice->actualCurr]['decimal_places']);
    }

    $final_price = $price * (int)$products['products_quantity'];

    $sql_data_array = array(
      'products_weight' => xtc_db_prepare_input($products_weight),
      'products_price' => xtc_db_prepare_input($price),
      'final_price' => xtc_db_prepare_input($final_price),
    );
    xtc_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array, 'update', "orders_products_id = '".(int)($data_array['opID'])."'");

    xtc_db_perform(TABLE_ORDERS, array('last_modified' => 'now()'), 'update', "orders_id = '".(int)$oID."'");

    return $products['products_id'];
  }


  function orders_product_option_edit($oID, $data_array) {
    global $order, $xtPrice, $lang;
  
    $lang_query = xtc_db_query("SELECT languages_id 
                                  FROM ".TABLE_LANGUAGES." 
                                 WHERE directory = '".xtc_db_input($order->info['language'])."'");
    $lang = xtc_db_fetch_array($lang_query);

    $status = get_customers_taxprice_status();

    $sql_data_array = array(
      'products_options' => xtc_db_prepare_input($data_array['products_options']),
      'products_options_values' => xtc_db_prepare_input($data_array['products_options_values']),
      'options_values_price' => xtc_db_prepare_input($data_array['options_values_price']),
      'price_prefix' => xtc_db_prepare_input($data_array['price_prefix']),
      'options_values_weight' => xtc_db_prepare_input($data_array['options_values_weight']),
      'weight_prefix' => xtc_db_prepare_input($data_array['weight_prefix']),
    );
    xtc_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array, 'update', "orders_products_attributes_id = '".xtc_db_input($data_array['opAID'])."'");

    $products_id = orders_product_update($oID, $data_array, $status);

    return $products_id;
  }


  function orders_product_option_insert($oID, $data_array) {
    global $order, $xtPrice, $lang;
  
    $lang_query = xtc_db_query("SELECT languages_id 
                                  FROM ".TABLE_LANGUAGES." 
                                 WHERE directory = '".xtc_db_input($order->info['language'])."'");
    $lang = xtc_db_fetch_array($lang_query);

    $status = get_customers_taxprice_status();

    $products_attributes_query = xtc_db_query("SELECT options_id,
                                                      options_values_id,
                                                      options_values_price,
                                                      options_values_weight,
                                                      price_prefix,
                                                      weight_prefix,
                                                      attributes_model,
                                                      attributes_ean
                                                 FROM ".TABLE_PRODUCTS_ATTRIBUTES."
                                                WHERE products_attributes_id = '".(int)$data_array['aID']."'");
    $products_attributes = xtc_db_fetch_array($products_attributes_query);

    $products_options_query = xtc_db_query("SELECT products_options_name
                                              FROM ".TABLE_PRODUCTS_OPTIONS."
                                             WHERE products_options_id = '".(int)$products_attributes['options_id']."'
                                               AND language_id = '".(int)$lang['languages_id']."'");
    $products_options = xtc_db_fetch_array($products_options_query);

    $products_options_values_query = xtc_db_query("SELECT products_options_values_name
                                                     FROM ".TABLE_PRODUCTS_OPTIONS_VALUES."
                                                    WHERE products_options_values_id = '".(int)$products_attributes['options_values_id']."'
                                                      AND language_id = '".(int)$lang['languages_id']."'");
    $products_options_values = xtc_db_fetch_array($products_options_values_query);

    $sql_data_array = array(
      'orders_id' => (int)($oID),
      'orders_products_id' => (int)($data_array['opID']),
      'products_options' => xtc_db_prepare_input($products_options['products_options_name']),
      'products_options_values' => xtc_db_prepare_input($products_options_values['products_options_values_name']),
      'options_values_price' => xtc_db_prepare_input($products_attributes['options_values_price']),
      'options_values_weight' => xtc_db_prepare_input($products_attributes['options_values_weight']),
      'orders_products_options_id' => xtc_db_prepare_input($products_attributes['options_id']),
      'orders_products_options_values_id' => xtc_db_prepare_input($products_attributes['options_values_id']),
      'price_prefix' => xtc_db_prepare_input($products_attributes['price_prefix']),
      'weight_prefix' => xtc_db_prepare_input($products_attributes['weight_prefix']),
      'attributes_model' => xtc_db_prepare_input($products_attributes['attributes_model']),
      'attributes_ean' => xtc_db_prepare_input($products_attributes['attributes_ean']),
    );
    xtc_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array);

    $products_query = xtc_db_query("SELECT op.products_id, 
                                           op.products_quantity
                                      FROM ".TABLE_ORDERS_PRODUCTS." op
                                 LEFT JOIN ".TABLE_PRODUCTS." p
                                           ON p.products_id = op.products_id
                                     WHERE op.orders_products_id = '".(int)$data_array['opID']."'");
    $products = xtc_db_fetch_array($products_query);

    // Update Attributes Stock
    if (STOCK_LIMITED == 'true') {
      xtc_db_query("UPDATE " . TABLE_PRODUCTS_ATTRIBUTES . " 
                       SET attributes_stock = attributes_stock - '".(double)$products['products_quantity']."' 
                     WHERE products_id = '".(int)$products['products_id']."' 
                       AND options_id = '".(int)$products_attributes['options_id']."'
                       AND options_values_id = '".(int)$products_attributes['options_values_id']."'");
    }

    if (DOWNLOAD_ENABLED == 'true') {
      $attributes_downloads_query = xtc_db_query("SELECT popt.products_options_name,
                                                         poval.products_options_values_name,
                                                         pa.options_values_price,
                                                         pa.price_prefix,
                                                         pad.products_attributes_maxdays,
                                                         pad.products_attributes_maxcount,
                                                         pad.products_attributes_filename
                                                    FROM ".TABLE_PRODUCTS_ATTRIBUTES." pa
                                                    JOIN ".TABLE_PRODUCTS_OPTIONS_VALUES." poval
                                                         ON pa.options_values_id = poval.products_options_values_id
                                                            AND poval.language_id = '".(int)$lang['languages_id']."'
                                                    JOIN ".TABLE_PRODUCTS_OPTIONS." popt
                                                         ON pa.options_id = popt.products_options_id
                                                            AND popt.language_id = '".(int)$lang['languages_id']."'
                                               LEFT JOIN ".TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD." pad
                                                      ON pa.products_attributes_id = pad.products_attributes_id
                                                   WHERE pa.products_id = '".(int)$products['products_id']."'
                                                     AND pa.options_id = '".(int)$products_attributes['options_id']."'
                                                     AND pa.options_values_id = '".(int)$products_attributes['options_values_id']."'");
      $attributes_downloads = xtc_db_fetch_array($attributes_downloads_query);

      if (isset($attributes_downloads['products_attributes_filename']) && xtc_not_null($attributes_downloads['products_attributes_filename'])) {
        $sql_data_array = array(
          'orders_id' =>(int)($oID),
          'orders_products_id' => (int)($data_array['opID']),
          'orders_products_filename' => $attributes_downloads['products_attributes_filename'],
          'download_maxdays' => $attributes_downloads['products_attributes_maxdays'],
          'download_count' => $attributes_downloads['products_attributes_maxcount'],
        );
        xtc_db_perform(TABLE_ORDERS_PRODUCTS_DOWNLOAD, $sql_data_array);
      }
    }

    $products_id = orders_product_update($oID, $data_array, $status);

    return $products_id;
  }


  function orders_product_option_delete($oID, $data_array) {
    global $order, $xtPrice, $lang;

    $lang_query = xtc_db_query("SELECT languages_id 
                                  FROM ".TABLE_LANGUAGES." 
                                 WHERE directory = '".xtc_db_input($order->info['language'])."'");
    $lang = xtc_db_fetch_array($lang_query);

    $status = get_customers_taxprice_status();

    // Update Attributes Stock
    if (STOCK_LIMITED == 'true') {
      $delete_products_attributes_query = xtc_db_query("SELECT *
                                                          FROM ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." opa
                                                          JOIN ".TABLE_ORDERS_PRODUCTS." op
                                                               ON op.orders_products_id=opa.orders_products_id
                                                         WHERE orders_products_attributes_id = '".(int)$data_array['opAID']."'");
      $delete_products_attributes = xtc_db_fetch_array($delete_products_attributes_query);
      xtc_db_query("UPDATE " . TABLE_PRODUCTS_ATTRIBUTES . " 
                       SET attributes_stock = attributes_stock + ".sprintf('%d', (double)$delete_products_attributes['products_quantity'])."
                     WHERE products_id = '".(int)$delete_products_attributes['products_id']."' 
                       AND options_id = '".(int)$delete_products_attributes['orders_products_options_id']."'
                       AND options_values_id = '".(int)$delete_products_attributes['orders_products_options_values_id']."'");
    }
               
    xtc_db_query("DELETE FROM ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." WHERE orders_products_attributes_id = '".(int)($data_array['opAID'])."'");
  
    $products_id = orders_product_update($oID, $data_array, $status);

    return $products_id;
  }


  function orders_ot_edit($oID, $data_array) {
    global $messageStack, $xtPrice;

    $check_total_query = xtc_db_query("SELECT orders_total_id 
                                         FROM ".TABLE_ORDERS_TOTAL." 
                                        WHERE orders_id = '".(int)$oID."' 
                                          AND class = '".$data_array['ot_class']."'");
    if (xtc_db_num_rows($check_total_query) > 0) {
      $check_total = xtc_db_fetch_array($check_total_query);

      $text = $xtPrice->xtcFormat($data_array['ot_value'], true);
      if ($data_array['ot_value'] < 0 ) {
        $text = ' '. sprintf(FORMAT_NEGATIVE, trim($xtPrice->xtcFormat($data_array['ot_value'], true)));
      }

      $sql_data_array = array(
        'title' => xtc_db_prepare_input($data_array['ot_title']),
        'text' => xtc_db_prepare_input($text),
        'value' => xtc_db_prepare_input($data_array['ot_value']),
        'sort_order' => xtc_db_prepare_input($data_array['ot_sort_order']),
      );

      xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array, 'update', "orders_total_id = '".(int)($check_total['orders_total_id'])."'");
    } else {
      $text = $xtPrice->xtcFormat($data_array['ot_value'], true);
      if ($data_array['ot_value'] < 0 ) {
        $text = ' '. sprintf(FORMAT_NEGATIVE, trim($xtPrice->xtcFormat($data_array['ot_value'], true)));
      }

      $sql_data_array = array(
        'orders_id' => (int)($oID),
        'title' => xtc_db_prepare_input($data_array['ot_title']),
        'text' => xtc_db_prepare_input($text),
        'value' => xtc_db_prepare_input($data_array['ot_value']),
        'class' => xtc_db_prepare_input($data_array['ot_class']),
        'sort_order' => xtc_db_prepare_input($data_array['ot_sort_order'])
      );
      xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
    }

    xtc_db_perform(TABLE_ORDERS, array('last_modified' => 'now()'), 'update', "orders_id = '".(int)$oID."'");
  }


  function orders_ot_delete($oID, $data_array) {
    xtc_db_query("DELETE FROM ".TABLE_ORDERS_TOTAL." WHERE orders_total_id = '".(int)($data_array['otID'])."'");
    xtc_db_perform(TABLE_ORDERS, array('last_modified' => 'now()'), 'update', "orders_id = '".(int)$oID."'");
  }


  function orders_save_order($oID, $data_array) {
    global $order, $xtPrice, $status, $lang;
    
    require_once(DIR_FS_LANGUAGES.$order->info['language'].'/extra/tax.php');
    
    $lang_query = xtc_db_query("SELECT languages_id,
                                       code 
                                  FROM ".TABLE_LANGUAGES." 
                                 WHERE directory = '".xtc_db_input($order->info['language'])."'");
    $lang = xtc_db_fetch_array($lang_query);

    $status = get_customers_taxprice_status();

    $c_info = get_c_infos($order->customer['ID'], trim($order->delivery['country_iso_2']));

    $allow_tax = get_allow_tax($c_info, $status);
    
    xtc_db_query("DELETE FROM ".TABLE_ORDERS_RECALCULATE." WHERE orders_id = '".(int)($oID)."'");
  
    $products_query = xtc_db_query("SELECT SUM(final_price) as subtotal_final 
                                      FROM ".TABLE_ORDERS_PRODUCTS." 
                                     WHERE orders_id = '".(int)$oID."'");
    $products = xtc_db_fetch_array($products_query);
    $subtotal_final = $products['subtotal_final'];
    $subtotal_text = $xtPrice->xtcFormat($subtotal_final, true);
  
    $total_data_array = array(
      'text' => xtc_db_prepare_input($subtotal_text),
      'value' => xtc_db_prepare_input($subtotal_final)
    );
        
    xtc_db_perform(TABLE_ORDERS_TOTAL, $total_data_array, 'update', "orders_id = '". (int)($oID). "' AND class = 'ot_subtotal'");
    
    $discount = 0;
    $subtotal = 0;
    foreach ($order->totals as $totals) {
      if ($totals['class'] == 'ot_subtotal') $subtotal = $totals['value'];
      if ($totals['class'] == 'ot_discount') $discount = abs($totals['value']);
    }
    
    if ($discount > 0) {
      $discount = $discount / $subtotal * 100;
    }
    
    $products_query = xtc_db_query("SELECT final_price, 
                                           products_tax, 
                                           allow_tax 
                                      FROM ".TABLE_ORDERS_PRODUCTS." 
                                     WHERE orders_id = '".(int)$oID."'");
    while ($products = xtc_db_fetch_array($products_query)) {
      $tax_rate = $products['products_tax'];

      if (in_array($allow_tax, array('0', '4'))) {
        $tax_rate = 0;
      }

      if ($allow_tax == '1') {
        $bprice = $products['final_price'];
        $nprice = $xtPrice->xtcRemoveTax($bprice, $tax_rate);
      } else {      
        $nprice = $products['final_price'];
        $bprice = $xtPrice->xtcAddTax($nprice, $tax_rate);
      }

      $cprice = $nprice;
      if ($discount > 0) {
        $cprice = $cprice - ($cprice / 100 * $discount);
      }
      $tax = $xtPrice->calcTax($cprice, $tax_rate);

      $sql_data_array = array(
        'orders_id' => (int)($oID),
        'n_price' => xtc_db_prepare_input($nprice),
        'b_price' => xtc_db_prepare_input($bprice),
        'tax' => xtc_db_prepare_input($tax),
        'tax_rate' => xtc_db_prepare_input($products['products_tax']),
        'class' => 'products',
      );
      xtc_db_perform(TABLE_ORDERS_RECALCULATE, $sql_data_array);
    }

    $tax_query = xtc_db_query("SELECT tax_rate, 
                                      SUM(tax) as tax_value
                                 FROM ".TABLE_ORDERS_RECALCULATE."
                                WHERE orders_id = '".(int)$oID."'
                                  AND class = 'products'
                             GROUP BY tax_rate");
    while ($tax = xtc_db_fetch_array($tax_query)) {
      $sql_data_array = array(
        'orders_id' => (int)($oID),
        'tax' => xtc_db_prepare_input($tax['tax_value']),
        'tax_rate' => xtc_db_prepare_input($tax['tax_rate']),
        'class' => 'ot_tax'
      );
      xtc_db_perform(TABLE_ORDERS_RECALCULATE, $sql_data_array);
    }

    $module_query = xtc_db_query("SELECT value, 
                                         class
                                    FROM ".TABLE_ORDERS_TOTAL."
                                   WHERE orders_id = '".(int)$oID."'
                                     AND sort_order < '".(int)MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER."'
                                     AND class != 'ot_subtotal_no_tax'
                                     AND class != 'ot_tax'
                                     AND class != 'ot_subtotal'");
    $discount_modules = array_map('trim', explode(",", DISCOUNT_MODULES));
    while ($module_value = xtc_db_fetch_array($module_query)) {
      $module_name = str_replace('ot_', '', $module_value['class']);
      
      $module_tax_class = '0';
      if (!in_array($module_value['class'], $discount_modules)) {
        if ($module_name != 'shipping' && defined('MODULE_ORDER_TOTAL_'.strtoupper($module_name).'_TAX_CLASS')) {
          $module_tax_class = constant('MODULE_ORDER_TOTAL_'.strtoupper($module_name).'_TAX_CLASS');
        } elseif ($module_name == 'shipping') {
          $module_tmp_name = explode('_', $order->info['shipping_class']);
          $module_tmp_name = $module_tmp_name[0];
          if ($module_tmp_name != 'selfpickup' && $module_tmp_name != 'free' && defined('MODULE_SHIPPING_'.strtoupper($module_tmp_name).'_TAX_CLASS')) {
            $module_tax_class = constant('MODULE_SHIPPING_'.strtoupper($module_tmp_name).'_TAX_CLASS');
          }
        }
      }

      $module_tax_rate = xtc_get_tax_rate($module_tax_class, $c_info['country_id'], $c_info['zone_id']);

      if (in_array($allow_tax, array('0', '4'))) {
        $module_tax_rate = 0;
      }

      if ($status['customers_status_show_price_tax'] == 1) {
        $module_b_price = $module_value['value'];
        if ($module_tax_class == '0') {
          $module_n_price = $module_value['value'];
        } else {
          $module_n_price = $xtPrice->xtcRemoveTax($module_b_price, $module_tax_rate);
        }
        $module_tax = $xtPrice->calcTax($module_n_price, $module_tax_rate);
      } else {
        $module_n_price = $module_value['value'];
        $module_b_price = $xtPrice->xtcAddTax($module_n_price, $module_tax_rate);
        $module_tax = $xtPrice->calcTax($module_n_price, $module_tax_rate);
      }

      if ($module_tax_rate == 0
          && defined('MODULE_ORDER_TOTAL_'.strtoupper($module_name).'_CALC_TAX')
          && strtolower(constant('MODULE_ORDER_TOTAL_'.strtoupper($module_name).'_CALC_TAX')) == 'true'
          )
      {
        $module_tax = calculate_tax($module_value['value'], $oID);
        if ($status['customers_status_show_price_tax'] == 0) {
          $module_tax = 0;
        }
        $module_n_price -= $module_tax;
      }

      $sql_data_array = array(
        'orders_id' => (int)($oID),
        'n_price' => xtc_db_prepare_input($module_n_price),
        'b_price' => xtc_db_prepare_input($module_b_price),
        'tax' => xtc_db_prepare_input($module_tax),
        'tax_rate' => xtc_db_prepare_input($module_tax_rate),
        'class' => $module_value['class'],
      );
      xtc_db_perform(TABLE_ORDERS_RECALCULATE, $sql_data_array);
    }

    $tax_rate_query = xtc_db_query("SELECT tax_rate 
                                      FROM ".TABLE_ORDERS_RECALCULATE." 
                                     WHERE orders_id = '".(int)$oID."' 
                                  GROUP BY tax_rate");
    while ($newtax = xtc_db_fetch_array($tax_rate_query)) {
      $new_tax_query = xtc_db_query("SELECT SUM(tax) as new_tax_value
                                       FROM ".TABLE_ORDERS_RECALCULATE."
                                      WHERE orders_id = '".(int)$oID."'
                                        AND class != 'products'
                                        AND tax_rate > 0
                                        AND tax_rate = '". $newtax['tax_rate'] ."'");
      $newtax_array = xtc_db_fetch_array($new_tax_query);
      
      $check_tax_query = xtc_db_query("SELECT *
                                         FROM ".TABLE_ORDERS_RECALCULATE."
                                        WHERE orders_id = '".(int)$oID."'
                                          AND tax_rate = '".xtc_db_input($newtax['tax_rate'])."'
                                          AND class = 'ot_tax'");
      if (xtc_db_num_rows($check_tax_query) > 0) {
        xtc_db_query("UPDATE ".TABLE_ORDERS_RECALCULATE."
                         SET tax = '".xtc_db_prepare_input($newtax_array['new_tax_value'])."'
                       WHERE orders_id = '".(int)$oID."'
                         AND tax_rate = '".xtc_db_prepare_input($newtax['tax_rate'])."'
                         AND class = 'ot_tax'");
      } else {
        $sql_data_array = array(
          'orders_id' => (int)($oID),
          'tax' => $newtax_array['new_tax_value'],
          'tax_rate' => $newtax['tax_rate'],
          'class' => 'ot_tax',
        );
        xtc_db_perform(TABLE_ORDERS_RECALCULATE, $sql_data_array);
      }
    }
        
    $sort_array = array(
      'MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_SORT_ORDER',
      'MODULE_ORDER_TOTAL_TAX_SORT_ORDER',
      'MODULE_ORDER_TOTAL_GV_SORT_ORDER',
      'MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER',
    );
    $sort_exlude = 0;
    foreach ($sort_array as $sort) {
      if (defined($sort)) {
        $sort_exlude = constant($sort);
        break;
      }
    }
    
    $where_array = array();
    $totals = explode(';', MODULE_ORDER_TOTAL_INSTALLED);
    for ($i=0; $i<count($totals); $i++) {
      $total = substr($totals[$i], 0, strrpos($totals[$i], '.'));
      $total_name = str_replace('ot_','',$total);
      if (constant('MODULE_ORDER_TOTAL_'.strtoupper($total_name).'_SORT_ORDER') > $sort_exlude) {
        $where_array[] = " AND class != '".$total."'";
      }
    }

    $check_no_tax_value_query = xtc_db_query("SELECT count(*) as count 
                                                FROM ".TABLE_ORDERS_TOTAL." 
                                               WHERE orders_id = '".(int)$oID."' 
                                                 AND class = 'ot_subtotal_no_tax'");
    $check_no_tax_value = xtc_db_fetch_array($check_no_tax_value_query);

    $display_to_subtotal_no_tax = false;
    if ((int)$check_no_tax_value['count'] > 0) {
      $display_to_subtotal_no_tax = true;
      include (DIR_FS_LANGUAGES.$order->info['language'].'/modules/order_total/ot_subtotal_no_tax.php');
    
      $subtotal_no_tax_query = xtc_db_query("SELECT SUM(n_price) as subtotal_no_tax_value 
                                               FROM ".TABLE_ORDERS_RECALCULATE." 
                                              WHERE orders_id = '".(int)$oID."'
                                                    ".implode(' ', $where_array));
      $subtotal_no_tax_value = xtc_db_fetch_array($subtotal_no_tax_query);
      $subtotal_no_tax_final = $subtotal_no_tax_value['subtotal_no_tax_value'];
      $subtotal_no_tax_text = '<b>'.$xtPrice->xtcFormat($subtotal_no_tax_final, true).'</b>';

      $sql_data_array = array(
        'title' => xtc_db_prepare_input(MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_TITLE). ':',
        'text'  => xtc_db_prepare_input($subtotal_no_tax_text),
        'value' => xtc_db_prepare_input($subtotal_no_tax_final),
        'sort_order' => xtc_db_prepare_input(MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_SORT_ORDER)
      );
      xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array, 'update', "orders_id = '".(int)($oID)."' AND class = 'ot_subtotal_no_tax'");
    } else {
      if (!in_array($allow_tax, array('0', '1', '4'))
          && defined('MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_STATUS') 
          && MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_STATUS == 'true'
          )
      {
        $display_to_subtotal_no_tax = true;
        include (DIR_FS_LANGUAGES.$order->info['language'].'/modules/order_total/ot_subtotal_no_tax.php');

        $subtotal_no_tax_value_query = xtc_db_query("SELECT SUM(n_price) as subtotal_no_tax_value 
                                                       FROM ".TABLE_ORDERS_RECALCULATE." 
                                                      WHERE orders_id = '".(int)$oID."'
                                                            ".implode(' ', $where_array));
        $subtotal_no_tax_value = xtc_db_fetch_array($subtotal_no_tax_value_query);
        $subtotal_no_tax_final = $subtotal_no_tax_value['subtotal_no_tax_value'];
        $subtotal_no_tax_text = '<b>'.$xtPrice->xtcFormat($subtotal_no_tax_final, true).'</b>';

        $sql_data_array = array(
          'orders_id' => (int)($oID),
          'title' => xtc_db_prepare_input(MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_TITLE). ':',
          'text' => xtc_db_prepare_input($subtotal_no_tax_text),
          'value' => xtc_db_prepare_input($subtotal_no_tax_final),
          'class' => 'ot_subtotal_no_tax',
          'sort_order' => xtc_db_prepare_input(MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_SORT_ORDER)
        );
        xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
      }
    }

    if (!defined('MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_STATUS') 
        || MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_STATUS == 'false' 
        || in_array($allow_tax, array('0', '1', '4'))
        )
    {
      $display_to_subtotal_no_tax = false;
      xtc_db_query("DELETE FROM ".TABLE_ORDERS_TOTAL." WHERE orders_id = '".(int)($oID)."' AND class = 'ot_subtotal_no_tax'");
    }

    xtc_db_query("DELETE FROM ".TABLE_ORDERS_TOTAL." WHERE orders_id = '".(int)($oID)."' AND class = 'ot_tax'");

    $add_tax = 0;
    $price = 'b_price';
    if ($allow_tax != '1') {
      $tax_query = xtc_db_query("SELECT SUM(tax) as value 
                                   FROM ".TABLE_ORDERS_RECALCULATE." 
                                  WHERE orders_id = '".(int)($oID)."' 
                                    AND class = 'ot_tax'");
      $tax = xtc_db_fetch_array($tax_query);
      $add_tax = $tax['value'];
      $price = 'n_price';
    }

    $total_query = xtc_db_query("SELECT SUM(".$price.") as value 
                                   FROM ".TABLE_ORDERS_RECALCULATE." 
                                  WHERE orders_id = '".(int)$oID."'
                                    AND class != 'ot_gv'");
    
    $total = xtc_db_fetch_array($total_query);
    $total_tax_final = $total['value'] + $add_tax;

    $tax_info = '';
    if ($status['customers_status_show_price_tax'] == 0
        || ($display_to_subtotal_no_tax === true && $total_tax_final >= $status['customers_status_show_tax_total'])
        )
    {
      $tax_info = TAX_NO_TAX;
    } elseif ($status['customers_status_show_price_tax'] == 1) {
      $tax_info = TAX_ADD_TAX;
    }

    $ust_query = xtc_db_query("SELECT tax_rate, SUM(tax) as tax_value_new
                                 FROM ".TABLE_ORDERS_RECALCULATE."
                                WHERE orders_id = '".(int)$oID."'
                                  AND tax != '0'
                                  AND class = 'ot_tax'
                             GROUP BY tax_rate 
                             ORDER BY tax_rate DESC");
    while ($ust = xtc_db_fetch_array($ust_query)) {
      $ust_desc_query = xtc_db_query("SELECT tax_description 
                                        FROM ".TABLE_TAX_RATES." 
                                       WHERE tax_rate = '".$ust['tax_rate']."'");
      $ust_desc = xtc_db_fetch_array($ust_desc_query);

      $title = parse_multi_language_value($ust_desc['tax_description'], $lang['code']);
      $title = $tax_info . $title.':';

      if ($ust['tax_value_new']) {
        $text = $xtPrice->xtcFormat($ust['tax_value_new'], true);

        $sql_data_array = array (
          'orders_id' => (int)($oID),
          'title' => xtc_db_prepare_input($title),
          'text' => xtc_db_prepare_input($text),
          'value' => xtc_db_prepare_input($ust['tax_value_new']),
          'class' => 'ot_tax',
          'sort_order' => xtc_db_prepare_input(MODULE_ORDER_TOTAL_TAX_SORT_ORDER)
        );
        xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
      }
    }

    if (in_array($allow_tax, array('0', '4'))) {
      xtc_db_query("DELETE FROM ".TABLE_ORDERS_TOTAL." WHERE orders_id = '".(int)($oID)."' AND class = 'ot_tax'");
    }

    $total_query = xtc_db_query("SELECT SUM(".$price.") as value 
                                   FROM ".TABLE_ORDERS_RECALCULATE." 
                                  WHERE orders_id = '".(int)$oID."'");
    $total = xtc_db_fetch_array($total_query);
    $total_final = $total['value'] + $add_tax;
    $total_text = '<b>'.$xtPrice->xtcFormat($total_final, true).'</b>';
  
    $total_data_array = array(
      'text' => xtc_db_prepare_input($total_text),
      'value' => xtc_db_prepare_input($total_final)
    );
    
    xtc_db_perform(TABLE_ORDERS_TOTAL, $total_data_array, 'update', "orders_id = '". (int)($oID). "' AND class = 'ot_total'");

    xtc_db_query("DELETE FROM ".TABLE_ORDERS_RECALCULATE." WHERE orders_id = '".xtc_db_input($oID)."'");

    xtc_db_perform(TABLE_ORDERS, array('last_modified' => 'now()'), 'update', "orders_id = '".(int)$oID."'");    
  }
