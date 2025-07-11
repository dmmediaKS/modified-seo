<?php
/* -----------------------------------------------------------------------------------------
   $Id: dp.php 15760 2024-02-29 17:00:47Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(dp.php,v 1.36 2003/03/09 02:14:35); www.oscommerce.com
   (c) 2003 nextcommerce (dp.php,v 1.12 2003/08/24); www.nextcommerce.org
   (c) 2006 xt:commerce (dp.php 899 2005-04-29);

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   German Post (Deutsche Post WorldNet)
   Autor:  Copyright (C) 2002 - 2003 TheMedia, Dipl.-Ing Thomas Pl�nkers | http://www.themedia.at & http://www.oscommerce.at

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   enhanced on 2010-12-08 18:17:30Z franky_n
   ---------------------------------------------------------------------------------------*/

  class dp {

    var $code;
    var $title;
    var $description;
    var $sort_order;
    var $icon;
    var $tax_class;
    var $enabled;
    var $num_zones;
    var $quotes;
    var $_check;

    function __construct() {
      global $order;

      $this->code = 'dp';
      $this->title = MODULE_SHIPPING_DP_TEXT_TITLE;
      $this->description = MODULE_SHIPPING_DP_TEXT_DESCRIPTION;
      $this->sort_order = ((defined('MODULE_SHIPPING_DP_SORT_ORDER')) ? MODULE_SHIPPING_DP_SORT_ORDER : '');
      $this->icon = DIR_WS_ICONS . 'shipping_dp.gif';
      $this->tax_class = ((defined('MODULE_SHIPPING_DP_TAX_CLASS')) ? MODULE_SHIPPING_DP_TAX_CLASS : '');
      $this->enabled = ((defined('MODULE_SHIPPING_DP_STATUS') && MODULE_SHIPPING_DP_STATUS == 'True') ? true : false);
      $this->num_zones = defined('MODULE_SHIPPING_DP_NUMBER_ZONES') ? (int)MODULE_SHIPPING_DP_NUMBER_ZONES : 0;

      if ($this->enabled == true 
          && !defined('RUN_MODE_ADMIN')
          && (int)MODULE_SHIPPING_DP_ZONE > 0 
          && is_object($order)
          )
      {
        $check_flag = false;
        $check_query = xtc_db_query("SELECT zone_id 
                                       FROM " . TABLE_ZONES_TO_GEO_ZONES . "
                                      WHERE geo_zone_id = '" . (int)MODULE_SHIPPING_DP_ZONE . "' 
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
      
      if ($this->check() > 0) {      
        $check_zones_query = xtc_db_query("SELECT * FROM " . TABLE_CONFIGURATION . " WHERE configuration_key LIKE 'MODULE_SHIPPING_DP_COUNTRIES_%'");
        $check_zones_rows = xtc_db_num_rows($check_zones_query);
        
        //update compatibility
        if (!defined('MODULE_SHIPPING_DP_NUMBER_ZONES')) {
          $this->num_zones = $check_zones_rows;
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_DP_NUMBER_ZONES', '". (int)$this->num_zones ."', '6', '0', now())");
        }

        if ($check_zones_rows != $this->num_zones) {
          $this->install_zones($check_zones_rows);
        }
        //update compatibility
        if (!defined('MODULE_SHIPPING_DP_DISPLAY')) {
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_DP_DISPLAY', 'True', '6', '7', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        }
      }
    }

    function quote($method = '') {
      global $order, $shipping_weight, $shipping_num_boxes;

      $dest_country = $order->delivery['shipping']['iso_code_2'];
      $dest_zone = 0;
      $error = false;

      for ($i=1; $i<=$this->num_zones; $i++) {
        $countries_table = constant('MODULE_SHIPPING_DP_COUNTRIES_' . $i);
        $countries_table  = preg_replace("'[\r\n\s]+'",'',$countries_table);
        $country_zones = explode(",", $countries_table);
        if (in_array($dest_country, $country_zones)) {
          $dest_zone = $i;
          break;
        }
        // rest of the world
        if ($countries_table == 'WORLD') {
          $dest_zone = $i;
        }
        // rest of the world eof
      }

      $this->quotes = array('id' => $this->code,
                            'module' => $this->title);

      if ($dest_zone == 0) {
        if (MODULE_SHIPPING_DP_DISPLAY == 'True') {
          $this->quotes['error'] = MODULE_SHIPPING_DP_INVALID_ZONE;
        } else {
          $this->enabled = false;
        }
      } else {
        $shipping = -1;
        $dp_cost = constant('MODULE_SHIPPING_DP_COST_' . $dest_zone);

        $dp_table = preg_split("/[:,]/" , $dp_cost);
        for ($i=0, $n=count($dp_table); $i<$n; $i+=2) {
          if ($shipping_weight <= $dp_table[$i]) {
            $shipping = (double)$dp_table[$i+1];
            $shipping_method = MODULE_SHIPPING_DP_TEXT_WAY . ' ' . $dest_country . ': ';
            break;
          }
        }

        if ($shipping == -1) {
          if (MODULE_SHIPPING_DP_DISPLAY == 'True') {
            $this->quotes['error'] = MODULE_SHIPPING_DP_UNDEFINED_RATE;
          } else {
            $this->enabled = false;
          }
        } else {
          $shipping_cost = ($shipping * $shipping_num_boxes) + (double)MODULE_SHIPPING_DP_HANDLING;
          $this->quotes['methods'] = array(array('id' => $this->code,
                                                 'title' => $shipping_method . ' (' . ($shipping_num_boxes > 1 ? $shipping_num_boxes . ' x ' : '') . round($shipping_weight, 2) . ' ' . MODULE_SHIPPING_DP_TEXT_UNITS .')',
                                                 'cost'  => $shipping_cost));
        }
      }

      if ($this->tax_class > 0) {
        $this->quotes['tax'] = xtc_get_tax_rate($this->tax_class, $order->delivery['shipping']['id'], $order->delivery['shipping']['zone_id']);
      }

      if (xtc_not_null($this->icon)) $this->quotes['icon'] = xtc_image($this->icon, $this->title);
      
      if ($this->enabled) {
        return $this->quotes;
      }
    }

    function check() {
      if (!isset($this->_check)) {
        if (defined('MODULE_SHIPPING_DP_STATUS')) {
          $this->_check = true;
        } else {
          $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_DP_STATUS'");
          $this->_check = xtc_db_num_rows($check_query);
        }
      }
      return $this->_check;
    }

    function install() {
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_SHIPPING_DP_STATUS', 'True', '6', '0', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_DP_HANDLING', '0', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_DP_TAX_CLASS', '0', '6', '0', 'xtc_get_tax_class_title', 'xtc_cfg_pull_down_tax_classes(', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_DP_ZONE', '0', '6', '0', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_DP_SORT_ORDER', '0', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_DP_ALLOWED', '', '6', '0', 'xtc_cfg_textarea(', now())");
      if (!defined('MODULE_SHIPPING_DP_NUMBER_ZONES')) {
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_DP_NUMBER_ZONES', '5', '6', '0', now())");
      }
      if (!defined('MODULE_SHIPPING_DP_DISPLAY')) {
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_DP_DISPLAY', 'True', '6', '7', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
      }

      $check_zones_query = xtc_db_query("SELECT * FROM " . TABLE_CONFIGURATION . " WHERE configuration_key LIKE 'MODULE_SHIPPING_".strtoupper($this->code)."_COUNTRIES_%'");
      $check_zones_rows_query = xtc_db_num_rows($check_zones_query);

      if ($check_zones_rows_query != 0) {
        $this->install_zones($check_zones_rows_query);
        xtc_db_query("UPDATE ".TABLE_CONFIGURATION." 
                         SET configuration_value = '".(int)$check_zones_rows_query."' 
                       WHERE configuration_key = 'MODULE_SHIPPING_".strtoupper($this->code)."_NUMBER_ZONES'");
      }
    }

    function install_zones($number_of_zones) {
                    
      // backup old values
      xtc_backup_configuration($this->keys_zones($number_of_zones));

      // add new zone
      if ($number_of_zones <= $this->num_zones) {
        for ($i = (($number_of_zones == 0) ? 1 : $number_of_zones); $i <= $this->num_zones; $i ++) {
          $check_zones_query = xtc_db_query("SELECT * FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_SHIPPING_DP_COUNTRIES_".$i."'");
          if (xtc_db_num_rows($check_zones_query) < 1) {
            xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_DP_COUNTRIES_".$i."', '', '6', '0', 'xtc_cfg_textarea(', now())");
            xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_DP_COST_".$i."', '', '6', '0', now())");
          }
        }      
      } else {
        // remove zone
        for ($i = $number_of_zones; $i >= $this->num_zones; $i --) {
          xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_DP_COUNTRIES_".$i."'");
          xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_DP_COST_".$i."'");      
        }
      }

      // set standard values
      for ($i = 1; $i <= $this->num_zones; $i ++) {
        if ($i == 1) {
          xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = 'DE' WHERE configuration_key = 'MODULE_SHIPPING_DP_COUNTRIES_1'");
          xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '10:6.90,20:11.90,31.5:13.90' WHERE  configuration_key = 'MODULE_SHIPPING_DP_COST_1'");
        }
        if ($i == 2) {
          xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = 'AT,BE,BG,CY,CZ,DK,EE,ES,FI,FR,GB,GR,HU,IE,IT,LT,LU,LV,MC,MT,NL,PL,PT,RO,SE,SI,SK' WHERE configuration_key = 'MODULE_SHIPPING_DP_COUNTRIES_2'");
          xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '5:17.00,10:22.00,20:32.00,31.5:42.00' WHERE  configuration_key = 'MODULE_SHIPPING_DP_COST_2'");
        }
        if ($i == 3) {
          xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = 'AD,AL,AM,AZ,BA,BY,CH,FO,GE,GI,GL,HR,IS,KZ,LI,MD,ME,MK,NO,RS,RU,SM,TR,UA,VA' WHERE configuration_key = 'MODULE_SHIPPING_DP_COUNTRIES_3'");
          xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '5:30.00,10:35.00,20:45.00,31.5:55.00' WHERE  configuration_key = 'MODULE_SHIPPING_DP_COST_3'");
        }
        if ($i == 4) {
          xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = 'CA,DZ,EG,IL,JO,LB,LR,LY,MA,PM,PS,SY,TN,US' WHERE configuration_key = 'MODULE_SHIPPING_DP_COUNTRIES_4'");
          xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '5:35.00,10:45.00,20:65.00,31.5:85.00' WHERE  configuration_key = 'MODULE_SHIPPING_DP_COST_4'");
        }
        if ($i == 5) {
          xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = 'AE,AF,AG,AI,AN,AO,AR,AU,AW,BB,BD,BF,BH,BI,BJ,BM,BN,BO,BR,BS,BT,BW,BZ,CD,CF,CG,CI,CK,CL,CM,CN,CO,CR,CU,CV,DJ,DM,DO,EC,ER,ET,FJ,FK,FM,GA,GD,GF,GH,GM,GN,GP,GQ,GT,GU,GW,GY,HK,HN,HT,ID,IN,IQ,IR,JM,JP,KE,KG,KH,KI,KM,KN,KP,KR,KW,KY,LA,LC,LK,LS,MG,MH,ML,MM,MN,MO,MP,MQ,MR,MS,MU,MV,MW,MX,MY,MZ,NA,NC,NE,NG,NI,NP,NR,NZ,OM,PA,PE,PF,PG,PH,PK,PN,PR,PY,QA,RE,RW,SA,SB,SC,SD,SG,SH,SL,SN,SO,SR,ST,SV,SZ,TC,TD,TG,TH,TJ,TM,TO,TT,TV,TW,TZ,UG,UY,UZ,VC,VE,VN,VU,WF,WS,YE,ZA,ZM,ZW' WHERE configuration_key = 'MODULE_SHIPPING_DP_COUNTRIES_5'");
          xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '5:40.00,10:55.00,20:85.00,31.5:115.00' WHERE  configuration_key = 'MODULE_SHIPPING_DP_COST_5'");
        }
      }
      
      // restore old values
      xtc_restore_configuration($this->keys_zones($this->num_zones));
    }

    function remove() {
      xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key IN ('" . implode("', '", $this->keys()) . "')");
    }

    function keys_zones($zones) {
      $keys_zones = array();
      for ($i = 1; $i <= $zones; $i ++) {
        $keys_zones[] = 'MODULE_SHIPPING_DP_COUNTRIES_' . $i;
        $keys_zones[] = 'MODULE_SHIPPING_DP_COST_' . $i;
      }
      return $keys_zones;
    }
    
    function keys() {
      $keys = array(
        'MODULE_SHIPPING_DP_STATUS', 
        'MODULE_SHIPPING_DP_HANDLING',
        'MODULE_SHIPPING_DP_ALLOWED', 
        'MODULE_SHIPPING_DP_TAX_CLASS', 
        'MODULE_SHIPPING_DP_ZONE', 
        'MODULE_SHIPPING_DP_SORT_ORDER',
        'MODULE_SHIPPING_DP_NUMBER_ZONES',
        'MODULE_SHIPPING_DP_DISPLAY'
      );
      $keys = array_merge($keys, $this->keys_zones($this->num_zones));

      return $keys;
    }
  }
