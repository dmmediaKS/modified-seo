<?php
/* -----------------------------------------------------------------------------------------
   $Id: flat.php 15760 2024-02-29 17:00:47Z GTB $   

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(flat.php,v 1.40 2003/02/05); www.oscommerce.com 
   (c) 2003	 nextcommerce (flat.php,v 1.7 2003/08/24); www.nextcommerce.org
   (c) 2006 xt:Commerce; www.xt-commerce.com

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/


  class flat {

    var $code;
    var $title;
    var $description;
    var $sort_order;
    var $icon;
    var $tax_class;
    var $enabled;
    var $quotes;
    var $_check;

    function __construct() {
      global $order;

      $this->code = 'flat';
      $this->title = MODULE_SHIPPING_FLAT_TEXT_TITLE;
      $this->description = MODULE_SHIPPING_FLAT_TEXT_DESCRIPTION;
      $this->sort_order = ((defined('MODULE_SHIPPING_FLAT_SORT_ORDER')) ? MODULE_SHIPPING_FLAT_SORT_ORDER : '');
      $this->icon = '';
      $this->tax_class = ((defined('MODULE_SHIPPING_FLAT_TAX_CLASS')) ? MODULE_SHIPPING_FLAT_TAX_CLASS : '');
      $this->enabled = ((defined('MODULE_SHIPPING_FLAT_STATUS') && MODULE_SHIPPING_FLAT_STATUS == 'True') ? true : false);

      if ($this->enabled == true 
          && !defined('RUN_MODE_ADMIN')
          && (int)MODULE_SHIPPING_FLAT_ZONE > 0 
          && is_object($order)
          )
      {
        $check_flag = false;
        $check_query = xtc_db_query("SELECT zone_id 
                                       FROM " . TABLE_ZONES_TO_GEO_ZONES . "
                                      WHERE geo_zone_id = '" . (int)MODULE_SHIPPING_FLAT_ZONE . "' 
                                        AND zone_country_id = '" . (int)$order->delivery['shipping']['id'] . "' 
                                   ORDER BY zone_id");
        while ($check = xtc_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->delivery['shipping']['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
    }


    function quote($method = '') {
      global $order;
      $this->quotes = array('id' => $this->code,
                            'module' => MODULE_SHIPPING_FLAT_TEXT_TITLE,
                            'methods' => array(array('id' => $this->code,
                                                     'title' => MODULE_SHIPPING_FLAT_TEXT_WAY,
                                                     'cost' => (double)MODULE_SHIPPING_FLAT_COST)));

      if ($this->tax_class > 0) {
        $this->quotes['tax'] = xtc_get_tax_rate($this->tax_class, $order->delivery['shipping']['id'], $order->delivery['shipping']['zone_id']);
      }

      if (xtc_not_null($this->icon)) $this->quotes['icon'] = xtc_image($this->icon, $this->title);

      return $this->quotes;
    }

    function check() {
      if (!isset($this->_check)) {
        if (defined('MODULE_SHIPPING_FLAT_STATUS')) {
          $this->_check = true;
        } else {
          $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_FLAT_STATUS'");
          $this->_check = xtc_db_num_rows($check_query);
        }
      }
      return $this->_check;
    }

    function install() {
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_FLAT_STATUS', 'True', '6', '0', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_FLAT_ALLOWED', '', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_FLAT_COST', '5.00', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_FLAT_TAX_CLASS', '0', '6', '0', 'xtc_get_tax_class_title', 'xtc_cfg_pull_down_tax_classes(', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_FLAT_ZONE', '0', '6', '0', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_FLAT_SORT_ORDER', '0', '6', '0', now())");
    }

    function remove() {
      xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array(
        'MODULE_SHIPPING_FLAT_STATUS', 
        'MODULE_SHIPPING_FLAT_COST',
        'MODULE_SHIPPING_FLAT_ALLOWED', 
        'MODULE_SHIPPING_FLAT_TAX_CLASS', 
        'MODULE_SHIPPING_FLAT_ZONE', 
        'MODULE_SHIPPING_FLAT_SORT_ORDER'
      );
    }
  }
