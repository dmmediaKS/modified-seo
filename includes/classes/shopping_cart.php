<?php
/* -----------------------------------------------------------------------------------------
   $Id: shopping_cart.php 16426 2025-04-30 13:50:33Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(shopping_cart.php,v 1.32 2003/02/11); www.oscommerce.com
   (c) 2003 nextcommerce (shopping_cart.php,v 1.21 2003/08/17); www.nextcommerce.org
   (c) 2006 xt:Commerce (shopping_cart.php); www.xt-commerce.com

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:

   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c) Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
   
//new module support
require_once (DIR_FS_CATALOG.'includes/classes/shopping_cartModules.class.php');
$scModules = new shoppingCartModules();

// include needed functions
require_once (DIR_FS_INC.'xtc_create_random_value.inc.php');
require_once (DIR_FS_INC.'xtc_get_prid.inc.php');
require_once (DIR_FS_INC.'xtc_get_tax_description.inc.php');

class shoppingCart {

  var $contents;
  var $total; 
  var $total_netto;
  var $tax;
  var $tax_discount;
  var $weight; 
  var $cartID; 
  var $content_type; 
  var $attr_price; 
  var $attr_weight; 
  var $type; 
  var $table_basket; 
  var $table_basket_attributes; 
  var $shoppingCartModules;
  
  function __construct($type = 'cart') {
    //new module support
    $this->shoppingCartModules = new shoppingCartModules();
    $this->type = $type;
    
    $this->table_basket = TABLE_CUSTOMERS_BASKET;
    $this->table_basket_attributes = TABLE_CUSTOMERS_BASKET_ATTRIBUTES;
    
    if (defined('MODULE_WISHLIST_SYSTEM_STATUS') 
        && MODULE_WISHLIST_SYSTEM_STATUS == 'true'
        && $this->type == 'wishlist'
        )
    {
      $this->table_basket = TABLE_CUSTOMERS_WISHLIST;
      $this->table_basket_attributes = TABLE_CUSTOMERS_WISHLIST_ATTRIBUTES;
    }

    $this->reset();
  }

  /**
   * restore_contents
   *
   * @return unknown
   */
  function restore_contents() {
    $customer_id = false;
    if (isset($_SESSION['customer_id'])) {
      $customer_id = $_SESSION['customer_id'];
    }
    
    //new module support    
    $customer_id = $this->shoppingCartModules->restore_contents_customer_id($customer_id, $this->type);
    
    if ($customer_id === false) {
      return false;
    }
    
    $products_list = array();
    
    // insert current cart contents in database
    if (is_array($this->contents)) {
      foreach ($this->contents as $products_id => $data) {
        if ($this->check_products_status_permission($products_id) === true) {
          $qty = $this->contents[$products_id]['qty'];
          $product_query = xtc_db_query("SELECT products_id,
                                                customers_basket_quantity
                                           FROM ".$this->table_basket."
                                          WHERE customers_id = '".xtc_db_input($customer_id)."'
                                            AND products_id = '".xtc_db_input($products_id)."'");
          if (xtc_db_num_rows($product_query) < 1) {
            $sql_data_array = array(
              'customers_id' => $customer_id,
              'products_id' => $products_id,
              'customers_basket_quantity' => $qty,
              'customers_basket_date_added' => 'now()'
            );

            //new module support    
            $sql_data_array = $this->shoppingCartModules->restore_contents_products_db($sql_data_array, $products_id, $this->table_basket, $qty, $this->type);
            xtc_db_perform($this->table_basket, $sql_data_array);

            if (isset($this->contents[$products_id]['attributes'])) {
              foreach ($this->contents[$products_id]['attributes'] as $option => $value) {
                $sql_data_array = array(
                  'customers_id' => $customer_id,
                  'products_id' => $products_id,
                  'products_options_id' => (int)$option,
                  'products_options_value_id' => (int)$value
                );
                
                //new module support    
                $sql_data_array = $this->shoppingCartModules->restore_contents_attributes_db($sql_data_array, $products_id, $value, $this->type);
                xtc_db_perform($this->table_basket_attributes, $sql_data_array);
              }
            }
          } else {
            /* use this code to add up saved qty
            $product = xtc_db_fetch_array($product_query);

            $qty += $product['customers_basket_quantity'];
            $this->contents[$products_id] = array ('qty' => $qty);
            */
            xtc_db_query("UPDATE ".$this->table_basket."
                             SET customers_basket_quantity = '".xtc_db_input($qty)."'
                           WHERE customers_id = '".xtc_db_input($customer_id)."'
                             AND products_id = '".xtc_db_input($products_id)."'");
          }
      
          $products_list[] = $products_id;
        } else {
          // no permission
          $this->remove($products_id);
        }
      }
    }

    // restore saved content
    $_SESSION['old_customers_basket_'.$this->type] = false;
    $products_query = xtc_db_query("SELECT products_id,
                                           customers_basket_quantity
                                      FROM ".$this->table_basket."
                                     WHERE customers_id = '".xtc_db_input($customer_id)."'
                                       AND products_id NOT IN ('".implode("', '", $products_list)."') 
                                  ORDER BY customers_basket_id");
    if (xtc_db_num_rows($products_query) > 0) {
      while ($products = xtc_db_fetch_array($products_query)) {
        if ($this->check_products_status_permission($products['products_id']) === true) {
          $this->contents[$products['products_id']] = array(
            'qty' => $products['customers_basket_quantity']
          );
            
          //new module support 
          $this->shoppingCartModules->restore_contents_products_session($products, $this->table_basket, $this->type);
            
          // attributes
          $attributes_query = xtc_db_query("SELECT products_options_id,
                                                   products_options_value_id
                                              FROM ".$this->table_basket_attributes."
                                             WHERE customers_id = '".xtc_db_input($customer_id)."'
                                               AND products_id = '".xtc_db_input($products['products_id'])."'
                                          ORDER BY customers_basket_attributes_id");
          if (xtc_db_num_rows($attributes_query) > 0) {
            while ($attributes = xtc_db_fetch_array($attributes_query)) {
              $this->contents[$products['products_id']]['attributes'][$attributes['products_options_id']] = $attributes['products_options_value_id'];
            }
            
            //new module support 
            $this->shoppingCartModules->restore_contents_attributes_session($products,$this->table_basket_attributes, $this->type);
            
            if (ATTRIBUTES_VALID_CHECK == 'true' && !$this->validate_attributes($products['products_id'], $this->contents[$products['products_id']]['attributes'], 'restore_contents')) {
              $this->remove($products['products_id']); //TODO info message
            }
          }
          if ($this->get_quantity($products['products_id']) > 0) {
            $_SESSION['old_customers_basket_'.$this->type] = true;
          }
        } else {
          // no permission
          $this->remove($products['products_id']);
        }
      }
    }
    $this->cleanup();

    // unique ID to the order contents
    $this->cartID = $this->generate_cart_id();
  }

  /**
   * reset
   *
   * @param boolean $reset_database
   */
  function reset($reset_database = false) {
    $this->contents = array ();
    $this->total = 0;
    $this->total_netto = 0;
    $this->weight = 0;
    $this->content_type = false;
    $this->attr_price = 0; 
    $this->attr_weight = 0;    
    $this->tax = array ();
    $this->tax_discount = array();
    
    if (isset($_SESSION['customer_id']) && $reset_database == true) {
      xtc_db_query("DELETE FROM ".$this->table_basket." 
                          WHERE customers_id = '".(int)$_SESSION['customer_id']."'");
      
      xtc_db_query("DELETE FROM ".$this->table_basket_attributes." 
                          WHERE customers_id = '".(int)$_SESSION['customer_id']."'");
    }

    //new module support 
    $this->shoppingCartModules->reset($reset_database, $this->type);

    // unique ID to the order contents
    $this->cartID = $this->generate_cart_id();

    if (isset($_SESSION[$this->type.'ID'])) {
      unset ($_SESSION[$this->type.'ID']);
    }
  }

  /**
   * add_cart
   *
   * @param integer $products_id
   * @param integer $qty
   * @param string $attributes
   * @param boolean $notify
   */
  function add_cart($products_id, $qty = 1, $attributes = '', $notify = true) {
    $products_id = xtc_get_uprid($products_id, $attributes);

    if (ATTRIBUTES_VALID_CHECK == 'true' && !$this->validate_attributes($products_id,$attributes,'add_cart')) {
      return false; //TODO info message
    }
    
    if ($notify == true) {
      $_SESSION['new_products_id_in_'.$this->type] = $products_id;
    }

    if ($this->in_cart($products_id)) {
      $this->update_quantity($products_id, $qty, $attributes);
    } else {
      $this->contents[$products_id] = array(
        'qty' => $qty
      );
      
      //new module support           
      $this->shoppingCartModules->add_cart_products_session($products_id, $this->type, $qty, $attributes);

      if (isset($_SESSION['customer_id'])) {
        $sql_data_array = array(
          'customers_id' => (int)$_SESSION['customer_id'],
          'products_id' => $products_id,
          'customers_basket_quantity' => $qty,
          'customers_basket_date_added' => 'now()'
        );

        //new module support 
        $sql_data_array = $this->shoppingCartModules->add_cart_products_db($sql_data_array, $this->type);
        xtc_db_perform($this->table_basket, $sql_data_array);
      }

      if (is_array($attributes)) {
        foreach ($attributes as $option => $value) {
          $this->contents[$products_id]['attributes'][(int)$option] = (int)$value;
          
          //new module support           
          $this->shoppingCartModules->add_cart_attributes_session($value, $this->type, $products_id, $option);
         
          if (isset($_SESSION['customer_id'])) {
            $sql_data_array = array(
              'customers_id' => (int)$_SESSION['customer_id'],
              'products_id' => $products_id,
              'products_options_id' => (int)$option,
              'products_options_value_id' => (int)$value
            );

            //new module support 
            $sql_data_array = $this->shoppingCartModules->add_cart_attributes_db($sql_data_array, $this->type);
     
            xtc_db_perform($this->table_basket_attributes, $sql_data_array);
          }
        }
      }
    }
    $this->cleanup();

    // unique ID to the order contents
    $this->cartID = $this->generate_cart_id();
  }

  /**
   * update_quantity
   *
   * @param integer $products_id
   * @param integer $quantity
   * @param unknown_type $attributes
   * @return unknown
   */
  function update_quantity($products_id, $quantity = '', $attributes = '') {

    // nothing needs to be updated if theres no quantity, so we return true
    if (empty ($quantity)){
      return true;
    }
    
    $this->contents[$products_id]['qty'] = (int)$quantity;
    
    //new module support           
    $this->shoppingCartModules->update_cart_products_session($products_id, $this->type, $quantity, $attributes);
    
    // update database
    if (isset($_SESSION['customer_id'])){
      $sql_data_array = array(
        'customers_basket_quantity' => (int)$quantity,
        'customers_basket_date_added' => 'now()'
      );
      
      //new module support 
      $sql_data_array = $this->shoppingCartModules->update_cart_products_db($sql_data_array, $products_id, $attributes, $this->type);
      
      xtc_db_perform($this->table_basket, $sql_data_array, 'update', "customers_id = '".(int)$_SESSION['customer_id']."' AND products_id = '".xtc_db_input($products_id)."'");
    }

    if (is_array($attributes)) {
      foreach ($attributes as $option => $value) {
        $this->contents[$products_id]['attributes'][(int)$option] = (int)$value;
        
        //new module support           
        $this->shoppingCartModules->update_cart_attributes_session($value, $this->type, $products_id, $option);
        
        if (isset($_SESSION['customer_id'])) {
          $sql_data_array = array(
            'products_options_value_id' => (int)$value,
          );
          
          //new module support 
          $sql_data_array = $this->shoppingCartModules->update_cart_attributes_db($sql_data_array, $products_id, $option, $this->type);
          
          xtc_db_perform($this->table_basket_attributes, $sql_data_array, 'update', "customers_id = '".(int)$_SESSION['customer_id']."' AND products_id = '".xtc_db_input($products_id)."' AND products_options_id = '".(int)$option."'");
        }
      }
    }
  }

  /**
   * cleanup
   *
   */
  function cleanup() {
    //new module support           
    $this->shoppingCartModules->cleanup_before($this->type);

    foreach ($this->contents as $products_id => $data) {
      if (isset($this->contents[$products_id]['qty']) && $this->contents[$products_id]['qty'] < 1) {
        unset ($this->contents[$products_id]);

        if (isset($_SESSION['customer_id'])) {
          xtc_db_query("DELETE FROM ".$this->table_basket." 
                              WHERE customers_id = '".(int)$_SESSION['customer_id']."' 
                                AND products_id = '".xtc_db_input($products_id)."'");
                                
          xtc_db_query("DELETE FROM ".$this->table_basket_attributes." 
                              WHERE customers_id = '".(int)$_SESSION['customer_id']."' 
                                AND products_id = '".xtc_db_input($products_id)."'");
        }
      }
    }

    //new module support           
    $this->shoppingCartModules->cleanup_after($this->type);
  }

  /**
   * get total number of items in cart
   *
   * @return integer total items
   */
  function count_contents() {
    $total_items = 0;
    if (is_array($this->contents)) {
      foreach ($this->contents as $products_id => $data) {
        $total_items += $this->get_quantity($products_id);
      }
    }
    return $total_items;
  }

  /**
   * get_quantity
   *
   * @param integer $products_id
   * @return integer quantity
   */
  function get_quantity($products_id) {
    if (isset($this->contents[$products_id]['qty'])) {
      return $this->contents[$products_id]['qty'];
    } else {
      return 0;
    }
  }

  /**
   * check if product is in cart
   *
   * @param integer $products_id
   * @return boolean
   */
  function in_cart($products_id) {
    if (isset($this->contents[$products_id])) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * remove a product from cart
   *
   * @param integer $products_id
   */
  function remove($products_id) {
    unset($this->contents[$products_id]);

    //new module support 
    $this->shoppingCartModules->remove_custom_inputs_session($products_id, $this->type);
    
    // remove from database
    if (isset($_SESSION['customer_id'])) { 
      xtc_db_query("DELETE FROM ".$this->table_basket." 
                          WHERE customers_id = '".(int)$_SESSION['customer_id']."' 
                            AND products_id = '".xtc_db_input($products_id)."'");

      xtc_db_query("DELETE FROM ".$this->table_basket_attributes." 
                          WHERE customers_id = '".(int)$_SESSION['customer_id']."' 
                            AND products_id = '".xtc_db_input($products_id)."'");
    }
    
    // unique ID to the order contents
    $this->cartID = $this->generate_cart_id();
  }

  /**
   * alias for reset
   *
   */
  function remove_all() {
    $this->reset();
  }

  /**
   * get a comma seperated list of ids of all products in cart
   *
   * @return string
   */
  function get_product_id_list() {
    $product_id_list = '';
    if (is_array($this->contents)) {
      foreach ($this->contents as $products_id => $data) {
        $product_id_list .= ', '.$products_id;
      }
    }
    return substr($product_id_list, 2);
  }

  /**
   * get an array of ids of all products in cart
   *
   * @return array
   */
  function get_product_id_array() {
    $products_array = array();
    $products_list = $this->get_product_id_list();
    if ($products_list != '') {
      $products_array = explode(',', $products_list);
      $products_array = array_map('trim', $products_array);
      $products_array = array_map('xtc_get_prid', $products_array);
    }
    return $products_array;
  }

  /**
   * calculate cart totals
   *
   * @return unknown
   */
  function calculate() {
    global $xtPrice;
    static $calculate_cache_array;
    
    if (!isset($calculate_cache_array)) {
      $calculate_cache_array = array();
    }
    
    $this->total = 0;
    $this->total_netto = 0;
    $this->weight = 0;
    $this->tax = array ();
    $this->tax_discount = array ();
    
    if (!is_array($this->contents)) {
      return 0;
    }

    $cache_id = md5(serialize($this->contents));
    
    if (!isset($calculate_cache_array[$cache_id])) {
      foreach ($this->contents as $products_id => $data) {
        $qty = $this->contents[$products_id]['qty'];
        // products price
        if ($product = $this->get_product($products_id)) {

          if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1
              && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0
              && $xtPrice->get_content_type_product($product['products_id']) == 'virtual'
              ) 
          {
            $product['products_tax_class_id'] = xtc_get_tax_class($product['products_tax_class_id']);
          }

          $products_price = $xtPrice->xtcGetPrice($product['products_id'], false, $qty, $product['products_tax_class_id'], $product['products_price']);

          if ($_SESSION['customers_status']['customers_status_show_price_tax'] != 1) {
            $products_price = round($products_price, $xtPrice->currencies[$xtPrice->actualCurr]['decimal_places']);
          }
          
          //new module support       
          $products_price = $this->shoppingCartModules->calculate_product_price($products_price, $product, $this->contents[$products_id],$products_id);
          
          $total = $products_price * $qty;
          $this->weight += ($qty * $product['products_weight']);
          
          //attributes price
          $attribute_price = $this->attributes_price($products_id, $qty);
          
          if ($_SESSION['customers_status']['customers_status_show_price_tax'] != 1) {
            $attribute_price = round($attribute_price, $xtPrice->currencies[$xtPrice->actualCurr]['decimal_places']);
          }

          $this->weight += $this->attr_weight * $qty;
          $total += $this->attr_price * $qty;
          
          $this->total += $total;
          $this->total_netto += $total;
          
          // $this->total hat netto * St�ck in der 1. Runde
          // Artikel Rabatt ber�cksichtigt
          // Gesamt Rabatt auf Bestellung nicht
          // Nur weiterrechnen, falls Product nicht ohne Steuer
          // $this->total + $this->tax wird berechnet
          if ($product['products_tax_class_id'] != 0) {

            $products_price_total = $products_price + $attribute_price;
            if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1) {
              // Rabatt f�r die Steuerberechnung
              // der eigentliche Rabatt wird im order-details_cart abgezogen
              $products_price_tax = $products_price - ($products_price / 100 * $_SESSION['customers_status']['customers_status_ot_discount']);
              $attribute_price_tax = $attribute_price - ($attribute_price / 100 * $_SESSION['customers_status']['customers_status_ot_discount']);
              $products_price_total = $products_price_tax + $attribute_price_tax;
            }

            $products_tax = isset($xtPrice->TAX[$product['products_tax_class_id']]) ? $xtPrice->TAX[$product['products_tax_class_id']] : 0;
            $products_tax_description = xtc_get_tax_description($product['products_tax_class_id']);

            if (!isset($this->tax[$product['products_tax_class_id']])) {
              $this->tax[$product['products_tax_class_id']]['value'] = 0;
            }

            // price incl tax
            if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) {
              $this->tax[$product['products_tax_class_id']]['value'] += (($products_price_total / (100 + $products_tax)) * $products_tax) * $qty;
              $this->tax[$product['products_tax_class_id']]['desc'] = TAX_ADD_TAX.$products_tax_description;
            }

            // excl tax + tax at checkout
            if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 
                && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1
                )
            {
              $tax = $products_price_total / 100 * $products_tax * $qty;
            
              $this->tax[$product['products_tax_class_id']]['value'] += $tax;
              $this->tax[$product['products_tax_class_id']]['desc'] = TAX_NO_TAX.$products_tax_description;
              $this->total += $tax;
                   
              if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1) {
                if (!isset($this->tax_discount[$product['products_tax_class_id']])) $this->tax_discount[$product['products_tax_class_id']] = 0;
                $this->tax_discount[$product['products_tax_class_id']] += $tax;
              } 
            }
          }
        }
      }
      
      $calculate_cache_array[$cache_id] = array(
        'total' => $this->total,
        'total_netto' => $this->total_netto,
        'weight' => $this->weight,
        'tax' => $this->tax,
        'tax_discount' => $this->tax_discount,
      );
    } else {
      foreach ($calculate_cache_array[$cache_id] as $k => $v) {
        $this->{$k} = $v;
      }
    }
    
    $this->total = round($this->total, 4);
        
    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) {
      foreach ($this->tax as $key => $val) {
        $this->total_netto -= round($val['value'], 4);
      }
    }
  }


  /**
   * get price for a product's attribute
   *
   * @param integer $products_id
   * @return float
   */
  function attributes_price($products_id, $qty = 1) {
    global $xtPrice;
    
    $attributes_price = 0;
    $attributes_weight = 0;
    if (isset($this->contents[$products_id]['attributes'])) {
      foreach ($this->contents[$products_id]['attributes'] as $option => $value) {
        $values = $xtPrice->xtcGetOptionPrice($products_id, $option, $value);
        //new module support   
        $values['price'] = $this->shoppingCartModules->calculate_option_price($values['price'], $option, $value, $products_id, $qty);
        $attributes_price += $values['price'];
        $attributes_weight += $values['weight'];
      }
    }

    $this->attr_weight = $attributes_weight;
    $this->attr_price = $attributes_price;

    if ($_SESSION['customers_status']['customers_status_show_price_tax'] != 1) {
      $this->attr_price = round($this->attr_price, $xtPrice->currencies[$xtPrice->actualCurr]['decimal_places']);
    }

    return $attributes_price;
  }

  /**
   * get_product
   *
   * @param $products_id
   * @return array
   */
  function get_product($products_id) {
    global $xtPrice;
    static $products_array;
    
    if (!isset($products_array)) {
      $products_array = array();
    }
    
    if (!isset($products_array[(int)$products_id])) {
      $product_query = xtc_db_query("SELECT *
                                       FROM ".TABLE_PRODUCTS."
                                      WHERE products_id = '".(int)$products_id."'");
      $products_array[(int)$products_id] = xtc_db_fetch_array($product_query);
      $products_array[(int)$products_id]['products_tax_class_id'] = $xtPrice->xtc_get_tax_class($products_id, $products_array[(int)$products_id]['products_tax_class_id']);
    }
    
    return $products_array[(int)$products_id];
  }

  /**
   * get_products
   *
   * @return array
   */
  function get_products($cache = true) {
    global $xtPrice,$main;
    static $products_array;
    
    if (!is_array($this->contents)){
      return false;
    }
    
    if (!isset($products_array)) {
      $products_array = array();
    }
    
    if (!isset($products_array[$this->type]) || $cache === false) {
      $products_array[$this->type] = array();

      $index = 0;
      foreach ($this->contents as $products_id => $data) {
        if (!empty($this->contents[$products_id]['qty'])) {
          $products_query = xtc_db_query("SELECT ".ADD_SELECT_CART."
                                                 p.products_id,
                                                 p.products_shippingtime,
                                                 p.products_image,
                                                 p.products_model,
                                                 p.products_price,
                                                 p.products_ean,
                                                 p.products_vpe,
                                                 p.products_vpe_status,
                                                 p.products_vpe_value,
                                                 p.products_discount_allowed,
                                                 p.products_weight,
                                                 p.products_tax_class_id,
                                                 p.products_status,
                                                 p.products_fsk18,
                                                 p.manufacturers_id,
                                                 p.products_price as products_price_origin,
                                                 p.products_quantity as products_stock,
                                                 p.products_weight as products_weight_origin,
                                                 p.products_vpe as products_vpe_id,
                                                 pd.products_name,
                                                 pd.products_heading_title,
                                                 pd.products_description,
                                                 pd.products_short_description,
                                                 pd.products_order_description
                                            FROM ".TABLE_PRODUCTS." p
                                            JOIN ".TABLE_PRODUCTS_DESCRIPTION." pd
                                                 ON pd.products_id = p.products_id
                                                    AND pd.language_id = '".(int)$_SESSION['languages_id']."'
                                           WHERE p.products_id='".(int)$products_id."'");

          if (xtc_db_num_rows($products_query) > 0) {
            $products = xtc_db_fetch_array($products_query);
                 
            if ($this->check_products_status_permission($products_id) === false) {
              $this->remove($products_id);
            } elseif (ATTRIBUTES_VALID_CHECK == 'true' && isset($this->contents[$products_id]['attributes']) && !$this->validate_attributes($products_id, $this->contents[$products_id]['attributes'], 'get_products')) {
              $this->remove($products_id); //TODO info message
            } else {
              $products['products_tax_class_id'] = $xtPrice->xtc_get_tax_class($products_id, $products['products_tax_class_id']);
            
              if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1
                  && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0
                  && $xtPrice->get_content_type_product($products['products_id']) == 'virtual'
                  ) 
              {
                $products['products_tax_class_id'] = xtc_get_tax_class($products['products_tax_class_id']);
              }
            
              if ($products_price_origin = $xtPrice->xtcGetGraduatedPrice($products['products_id'], 1)) {
                $products['products_price_origin'] = $products_price_origin;
              }
            
              $products_price = $xtPrice->xtcGetPrice($products['products_id'], false, $this->contents[$products_id]['qty'], $products['products_tax_class_id'], $products['products_price']);
            
              if ($_SESSION['customers_status']['customers_status_show_price_tax'] != 1) {
                $products_price = round($products_price, $xtPrice->currencies[$xtPrice->actualCurr]['decimal_places']);
              }
            
              //new module support                                    
              $products_price = $this->shoppingCartModules->calculate_product_price($products_price, $products, $this->contents[$products_id],$products_id);
              $attributes_price = $this->attributes_price($products_id, $this->contents[$products_id]['qty']);

              $products_data = array();
              foreach ($products as $key => $value) {
                $products_data[str_replace('products_', '', $key)] = $value;
              }
            
              $products_data['price'] = $products_price + $attributes_price;
              if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1
                  && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0
                  )
              {
                $products_data['price'] = round($products_data['price'], $xtPrice->currencies[$xtPrice->actualCurr]['decimal_places']);
              }
              $products_data['id'] = $products_id;
              $products_data['vpe'] = $main->getVPEtext($products, $products_price);
              $products_data['quantity'] = $products_data['qty'] = $this->contents[$products_id]['qty'];
              $products_data['shipping_time'] = (ACTIVATE_SHIPPING_STATUS == 'true') ? $main->getShippingStatusName($products['products_shippingtime']) : null;
              $products_data['final_price'] = $products_data['price'] * $this->contents[$products_id]['qty'];
              $products_data['weight'] =  $products['products_weight'] + $this->attr_weight;
              $products_data['final_weight'] =  $products_data['weight'] * $this->contents[$products_id]['qty'];
              $products_data['tax'] = isset($xtPrice->TAX[$products['products_tax_class_id']]) ? $xtPrice->TAX[$products['products_tax_class_id']] : 0;
              $products_data['attributes_price'] = $attributes_price;
              $products_data['attributes'] = isset($this->contents[$products_id]['attributes']) ? $this->contents[$products_id]['attributes'] : null;

              $products_data = $this->shoppingCartModules->get_products($products_data, $products, $this->contents[$products_id], $this->type);

              $products_array[$this->type][$index++] = $products_data;
            }
          }
        }
      }
    }
    
    return $products_array[$this->type];
  }

  /**
   * show_total
   *
   * @return unknown
   */
  function show_total() {
    $this->calculate();
    return $this->total;
  }

  /**
   * show_total_net
   *
   * @return unknown
   */
  function show_total_net() {
    $this->calculate();
    return $this->total_netto;
  }

  /**
   * show_weight
   *
   * @return unknown
   */
  function show_weight() {
    $this->calculate();
    return $this->weight;
  }

  /**
   * show_tax
   *
   * @param boolean $format
   * @return unknown
   */
  function show_tax($format = true) {
    global $xtPrice;
    
    $this->calculate();
    $tax = 0;
    $output = array();
    foreach ($this->tax as $key => $value) {
      if ($this->tax[$key]['value'] > 0) {
        $output[] = $this->tax[$key]['desc'].': '.$xtPrice->xtcFormat($this->tax[$key]['value'], true);
        $tax += $this->tax[$key]['value'];
      }
    }
    
    if ($format) {
      return implode('<br/>', $output);
    }
    
    return $tax; 
  }

  /**
   * generate_cart_id
   *
   * @param integer $length
   * @return unknown
   */
  function generate_cart_id($length = 5) {
    return xtc_create_random_value($length, 'digits');
  }

  /**
   * get_content_type
   *
   * @return unknown
   */
  function get_content_type() {
    $this->content_type = false;
    if ((DOWNLOAD_ENABLED == 'true') && ($this->count_contents() > 0)) {
      foreach ($this->contents as $products_id => $data) {
        $db_products_id = $products_id;
        
        //new module support 
        $db_products_id = $this->shoppingCartModules->get_content_type($db_products_id);
        
        if (isset($this->contents[$products_id]['attributes'])) {

          if (defined('DOWNLOAD_MULTIPLE_ATTRIBUTES_ALLOWED') && DOWNLOAD_MULTIPLE_ATTRIBUTES_ALLOWED == 'true') {
            //multiple attributes for downloads
            $virtual_check_query = xtc_db_query("SELECT count(*) as total
                                                   FROM ".TABLE_PRODUCTS_ATTRIBUTES." pa
                                                   JOIN ".TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD." pad
                                                        ON pa.products_attributes_id = pad.products_attributes_id
                                                  WHERE pa.products_id = '".(int)$db_products_id."'
                                                    AND pa.options_values_id IN ('".implode("', '", $this->contents[$products_id]['attributes'])."')
                                                  ");

            $virtual_check = xtc_db_fetch_array($virtual_check_query);
            if ($virtual_check['total'] > 0) {
              switch ($this->content_type) {
                case 'physical' :
                  $this->content_type = 'mixed';
                  return $this->content_type;
                  break;

                default :
                  $this->content_type = 'virtual';
                  break;
              }
            } else {
              switch ($this->content_type) {
                case 'virtual' :
                  $this->content_type = 'mixed';
                  return $this->content_type;
                  break;

                default :
                  $this->content_type = 'physical';
                  break;
              }
            }          
          } else {
            //single attributes for downloads
            foreach ($this->contents[$products_id]['attributes'] as $options_values_id) {
              $virtual_check_query = xtc_db_query("SELECT count(*) as total
                                                     FROM ".TABLE_PRODUCTS_ATTRIBUTES." pa
                                                     JOIN ".TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD." pad
                                                          ON pa.products_attributes_id = pad.products_attributes_id
                                                    WHERE pa.products_id = '".(int)$db_products_id."'
                                                      AND pa.options_values_id = '".(int)$options_values_id."'
                                                    ");

              $virtual_check = xtc_db_fetch_array($virtual_check_query);
              if ($virtual_check['total'] > 0) {
                switch ($this->content_type) {
                  case 'physical' :
                    $this->content_type = 'mixed';
                    return $this->content_type;
                    break;

                  default :
                    $this->content_type = 'virtual';
                    break;
                }
              } else {
                switch ($this->content_type) {
                  case 'virtual' :
                    $this->content_type = 'mixed';
                    return $this->content_type;
                    break;

                  default :
                    $this->content_type = 'physical';
                    break;
                }
              }
            }
          }
        } else {
          switch ($this->content_type) {
            case 'virtual' :
              $this->content_type = 'mixed';
              return $this->content_type;
              break;

            default :
              $this->content_type = 'physical';
              break;
          }
        }
      }
    } else {
      $this->content_type = 'physical';
    }
    return $this->content_type;
  }

  /**
   * unserialize
   *
   * @param unknown_type $broken
   */
  function unserialize($broken) {
    foreach ($broken as $key => $val) {
      if (gettype($this->{$key}) != 'user function') {
        $this->{$key} = $val;
      }
    }
  }

  /**
   * get total number of items in cart disregard gift vouchers
   *
   * amend count_contents to show nil contents for shipping
   * as we don't want to quote for 'virtual' item
   *
   * @return integer
   */
  function count_contents_virtual() {
    $total_items = 0;
    if (is_array($this->contents)) {
      foreach ($this->contents as $products_id => $data) {
        $no_count = false;
        $gv_result = $this->get_product($products_id);
        if (preg_match('/^GIFT/', $gv_result['products_model'])) {
          $no_count = true;
        }

        //new module support 
        $no_count = $this->shoppingCartModules->count_contents_virtual($no_count, $products_id);

        if (!$no_count){
          $total_items += $this->get_quantity($products_id);
        }
      }
    }
    return $total_items;
  }
  
  /**
   * check products permission and status
   *
   * @param integer $products_id
   * @return boolean
   */
  function check_products_status_permission($products_id) {
    require(DIR_FS_CATALOG.'includes/define_conditions.php');
    $conditions = str_replace('p.', '', $products_conditions_p);

    $status = false;
    $check_query = xtDBquery("SELECT products_id 
                                FROM ".TABLE_PRODUCTS."
                               WHERE products_id = '".(int)$products_id."'
                                 AND products_status = '1'
                                     ".$conditions);
    if (xtc_db_num_rows($check_query, true) > 0) {
      $status = true;
    }
    
    //new module support 
    $status = $this->shoppingCartModules->check_products_status_permission($status, $products_id);
    
    return $status;
  }

  /**
   * create_products_attributes_array
   *
   * @param integer $products_id
   * @return array
   */
  function create_products_attributes_array($products_id) {
    $dataArray = array();
        
    $db_query = xtDBquery("SELECT options_id,
                                  options_values_id
                             FROM ".TABLE_PRODUCTS_ATTRIBUTES." 
                            WHERE products_id = '".(int)$products_id."'");
    while($data = xtc_db_fetch_array($db_query, true)) {
      $dataArray[$data['options_id']][] = $data['options_values_id'];
    }

    //new module support 
    $dataArray = $this->shoppingCartModules->create_products_attributes_array($dataArray, $products_id, $this->type);

    return $dataArray;
  }

  /**
   * Query for validate_attributes
   *
   * @param integer $products_id
   * @return boolean
   */
  function validate_attributes($products_id, $attributes, $flag = '') {
    if (!isset($products_attributes_array)) {
      $products_attributes_array = array();
    }

    $check = true;
    if (is_array($attributes) && count($attributes)) {
      $pID = (int)$products_id;
      if (!isset($products_attributes_array[$pID])) {
        $products_attributes_array[$pID] = $this->create_products_attributes_array($pID);
      }
      foreach($attributes as $option => $value) {
        if (!array_key_exists((int)$option, $products_attributes_array[$pID])) {
          $check = false;
          break;
        }
        if (!in_array($value,$products_attributes_array[$pID][(int)$option])) {
          $check = false;
          break;
        }
      }
    }
    return $check;
  }
  
  /**
   * get continue shopping link
   *
   * @return url
   */
  function get_continue_shopping_link() {
    global $request_type;
    
    $url = ((isset($_SESSION['continue_link']) && $_SESSION['continue_link'] != '') ? $_SESSION['continue_link'] : '');
    if (!empty($_SERVER['HTTP_REFERER']) 
        && filter_var($_SERVER['HTTP_REFERER'], FILTER_VALIDATE_URL) !== false
        )
    {
      $basename = '';
      $referer = parse_url($_SERVER['HTTP_REFERER']);

      if (isset($referer['path'])
          && isset($referer['host'])
          && (strpos(HTTP_SERVER, $referer['host']) !== false
              || strpos(HTTPS_SERVER, $referer['host']) !== false
              )
          )
      {
        switch (basename($referer['path'])) {
          case FILENAME_LOGOFF:
          case FILENAME_LOGIN:
            $basename = FILENAME_DEFAULT;
            break;
          
          default:
            if (basename($referer['path']) != FILENAME_SHOPPING_CART
                && strpos(basename($referer['path']), 'checkout_') === false
                )
            {
              $basename = $referer['path'];
              if (substr($basename, 0, strlen(DIR_WS_CATALOG)) == DIR_WS_CATALOG) {
                $basename = substr($basename, strlen(DIR_WS_CATALOG));
              }
            }
            break;
        }
      }      

      $params = '';
      if (isset($referer['query'])) {
        parse_str($referer['query'], $params_array);
    
        $valid_params = array(
          'cPath',
          'products_id',
          'filter_id',
          'filter',
          'manufacturers_id',
          'categories_id',
          'inc_subcat',
          'keywords',
          'pfrom',
          'pto',
          'page',
        );
    
        foreach ($params_array as $k => $v) {
          if (!in_array($k, $valid_params)) {
            unset($params_array[$k]);
          }
        }
        $params = http_build_query($params_array, '', '&');
      }
      
      if ($basename != '') {
        $url = xtc_href_link($basename, $params, $request_type);

        //new module support 
        $url = $this->shoppingCartModules->get_continue_shopping_link($url, $referer);
        
        $_SESSION['continue_link'] = $url;
      }
    }

    return $url;
  }
  
}
