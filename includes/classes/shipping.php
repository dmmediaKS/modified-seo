<?php
/* -----------------------------------------------------------------------------------------
   $Id: shipping.php 15797 2024-04-10 12:25:23Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(shipping.php,v 1.22 2003/05/08); www.oscommerce.com
   (c) 2003 nextcommerce (shipping.php,v 1.9 2003/08/17); www.nextcommerce.org
   (c) 2006 XT-Commerce (shipping.php 1305 2005-10-14)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


  class shipping {

    var $modules;
    var $checkoutModules;

    function __construct($module = '') {
      global $PHP_SELF, $order;

      require_once (DIR_FS_CATALOG.'includes/classes/checkoutModules.class.php');
      $this->checkoutModules = new checkoutModules();

      $this->modules = array();
      
      if (defined('MODULE_SHIPPING_INSTALLED') && xtc_not_null(MODULE_SHIPPING_INSTALLED)) {
        $modules = explode(';', MODULE_SHIPPING_INSTALLED);
        
        $module_directory = DIR_WS_MODULES . 'shipping/';
        foreach($modules as $file) {
          $class = substr($file, 0, strrpos($file, '.'));
          $module_status = (defined('MODULE_SHIPPING_'. strtoupper($class) .'_STATUS') && strtolower(constant('MODULE_SHIPPING_'. strtoupper($class) .'_STATUS')) == 'true') ? true : false;
          if (is_file($module_directory . $file) && $module_status) {
            $this->modules[] = $file;
          }
        }
        unset($modules);

        //new module support
        $this->modules = $this->checkoutModules->shipping_modules($this->modules);

        $include_modules = array();

        if (xtc_not_null($module)
            && isset($module['id'])
            && in_array(substr($module['id'], 0, strpos($module['id'], '_')) . '.php', $this->modules) 
            ) 
        {
          $class = substr($module['id'], 0, strpos($module['id'], '_'));
          $include_modules[] = array(
            'class' => $class, 
            'file' => $class.'.php'
          );
        } else {
          foreach ($this->modules as $value) {
            $class = substr($value, 0, strrpos($value, '.'));
            $include_modules[] = array(
              'class' => $class, 
              'file' => $value
            );
          }
        }
        
        // load unallowed modules into array - remove spaces and line breaks by web28
        $unallowed_modules = preg_replace("'[\r\n\s]+'",'',$_SESSION['customers_status']['customers_status_shipping_unallowed'].','. (isset($order->customer['shipping_unallowed']) ? $order->customer['shipping_unallowed']: ''));
        $unallowed_modules = explode(',', $unallowed_modules);

        //new module support
        $unallowed_modules = $this->checkoutModules->unallowed_shipping_modules($unallowed_modules);
        $unallowed_modules = array_filter($unallowed_modules);
        
        for ($i = 0, $n = sizeof($include_modules); $i < $n; $i++) {
          if (!in_array($include_modules[$i]['class'], $unallowed_modules)) {
            // check if zone is allowed to see module
            $allowed_zones = array();
            if (constant('MODULE_SHIPPING_' . strtoupper($include_modules[$i]['class']) . '_ALLOWED') != '') {
              $allowed_zones = explode(',', strtoupper(preg_replace("'[\r\n\s]+'",'',constant('MODULE_SHIPPING_' . strtoupper($include_modules[$i]['class']) . '_ALLOWED'))));
              $allowed_zones = array_filter($allowed_zones);
            }
            if ((isset($_SESSION['delivery_zone']) && in_array($_SESSION['delivery_zone'], $allowed_zones))
                || count($allowed_zones) == 0
                )
            {
              include_once(DIR_WS_LANGUAGES . $_SESSION['language'] . '/modules/shipping/' . $include_modules[$i]['file']);
              include_once(DIR_WS_MODULES . 'shipping/' . $include_modules[$i]['file']);
              
              if (class_exists($include_modules[$i]['class'])) {
                $GLOBALS[$include_modules[$i]['class']] = new $include_modules[$i]['class'];
              }
            }
          }
        }
      }
    }

    function quote($method = '', $module = '') {
      global $total_weight, $shipping_weight, $shipping_quoted, $shipping_num_boxes;

      $quotes_array = array();

      if (is_array($this->modules)) {
        $shipping_quoted = '';
        $shipping_num_boxes = 1;
        $shipping_weight = $total_weight;

        if ((double)SHIPPING_BOX_WEIGHT >= ($shipping_weight * (double)SHIPPING_BOX_PADDING / 100)) {
          $shipping_weight = $shipping_weight + (double)SHIPPING_BOX_WEIGHT;
        } else {
          $shipping_weight = $shipping_weight + ($shipping_weight * (double)SHIPPING_BOX_PADDING / 100);
        }

        if ((double)SHIPPING_MAX_WEIGHT > 0 && $shipping_weight > (double)SHIPPING_MAX_WEIGHT) { // Split into many boxes
          $shipping_num_boxes = ceil($shipping_weight / (double)SHIPPING_MAX_WEIGHT);
          $shipping_weight = $shipping_weight / $shipping_num_boxes;
        }

        $include_quotes = array();

        foreach ($this->modules as $value) {
          $class = substr($value, 0, strrpos($value, '.'));
          if (xtc_not_null($module) && isset($GLOBALS[$class])) {
            if ($module == $class && $GLOBALS[$class]->enabled) {
              $include_quotes[] = $class;
            }
          } elseif (isset($GLOBALS[$class]) && $GLOBALS[$class]->enabled) {
            $include_quotes[] = $class;
          }
        }

         for ($i=0, $size = sizeof($include_quotes); $i<$size; $i++) {
          $quotes = $GLOBALS[$include_quotes[$i]]->quote($method);
          if (is_array($quotes)) $quotes_array[] = $quotes;
        }
      }

      return $quotes_array;
    }

    function cheapest() {
      global $xtPrice;
      
      if (is_array($this->modules)) {
        $rates = array();

        foreach ($this->modules as $value) {
          $class = substr($value, 0, strrpos($value, '.'));
          if (isset($GLOBALS[$class])
              && is_object($GLOBALS[$class]) 
              && $GLOBALS[$class]->enabled
              ) 
          {
            $quotes = $GLOBALS[$class]->quotes;
            $size = isset($quotes['methods']) && is_array($quotes['methods']) ? sizeof($quotes['methods']) : 0;
            for ($i=0; $i<$size; $i++) {
              if (array_key_exists('cost', $quotes['methods'][$i])
                  && (!method_exists($GLOBALS[$class], 'ignore_cheapest') || $GLOBALS[$class]->ignore_cheapest() !== true)
                  )
              {
                $quotes['methods'][$i]['total'] = $xtPrice->xtcFormat($quotes['methods'][$i]['cost'], false);        
                if ($quotes['methods'][$i]['cost'] > 0) {
                  if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 || !isset($quotes['tax'])) {
                    $quotes['tax'] = 0;
                  }
                  $quotes['methods'][$i]['total'] = $xtPrice->xtcFormat($xtPrice->xtcAddTax($quotes['methods'][$i]['cost'], $quotes['tax'], false), false);						
                }
                $title = $quotes['module'];
                if (!defined('SHOW_SHIPPING_MODULE_TITLE') || SHOW_SHIPPING_MODULE_TITLE == 'shipping_default') {
                  $title .= ((trim($quotes['methods'][$i]['title']) != '') ? ' ('.$quotes['methods'][$i]['title'].')' : '');
                } elseif (SHOW_SHIPPING_MODULE_TITLE == 'shipping_custom' && parse_multi_language_value(CUSTOM_SHIPPING_TITLE, $_SESSION['language_code']) != '') {
                  $title = parse_multi_language_value(CUSTOM_SHIPPING_TITLE, $_SESSION['language_code']);
                }
                $rates[] = array(
                  'id' => $quotes['id'] . '_' . $quotes['methods'][$i]['id'],
                  'title' => $title,
                  'cost' => $quotes['methods'][$i]['cost'],
                  'total' => $quotes['methods'][$i]['total'],
                );
              }
            }
          }
        }

        $cheapest = false;
        for ($i=0, $size = sizeof($rates); $i<$size; $i++) {
          if (is_array($cheapest)) {
            if ($rates[$i]['cost'] < $cheapest['cost']) {
              $cheapest = $rates[$i];
            }
          } else {
            $cheapest = $rates[$i];
          }
        }
        
        return $cheapest;
      }
    }

    function javascript_validation() {
      $js = '';
      if (is_array($this->modules)) {
        $js = '<script type="text/javascript"><!-- ' . "\n" .
              'function check_form() {' . "\n" .
              '  var error = 0;' . "\n" .
              '  var error_message = unescape("' . xtc_js_lang(JS_ERROR) . '");' . "\n" .
              '  var shipping_value = null;' . "\n" .
              '  if (document.getElementById("checkout_address").shipping.length) {' . "\n" .
              '    for (var i=0; i<document.getElementById("checkout_address").shipping.length; i++) {' . "\n" .
              '      if (document.getElementById("checkout_address").shipping[i].checked) {' . "\n" .
              '        shipping_value = document.getElementById("checkout_address").shipping[i].value;' . "\n" .
              '      }' . "\n" .
              '    }' . "\n" .
              '  } else if (document.getElementById("checkout_address").shipping.checked) {' . "\n" .
              '    shipping_value = document.getElementById("checkout_address").shipping.value;' . "\n" .
              '  } else if (document.getElementById("checkout_address").shipping.value) {' . "\n" .
              '    shipping_value = document.getElementById("checkout_address").shipping.value;' . "\n" .
              '  }' . "\n\n";

        foreach ($this->modules as $value) {
          $class = substr($value, 0, strrpos($value, '.'));
          if (isset($GLOBALS[$class]) 
              && $GLOBALS[$class]->enabled 
              && method_exists($GLOBALS[$class], 'javascript_validation')
              ) 
          {
            $js .= $GLOBALS[$class]->javascript_validation();
          }
        }
        $js .= "\n" . '  if (shipping_value == null) {' . "\n" .
               '    error_message = error_message + unescape("' . xtc_js_lang(JS_ERROR_NO_SHIPPING_MODULE_SELECTED) . '");' . "\n" .
               '    error = 1;' . "\n" .
               '  }' . "\n\n" .
               '  if (error == 1) {' . "\n" . 
               '    alert(error_message);' . "\n" .
               '    return false;' . "\n" .
               '  } else {' . "\n" .
               '    return true;' . "\n" .
               '  }' . "\n" .
               '}' . "\n" .
               '//--></script>' . "\n";
      }
      return $js;
    }

    public static function shipping_title($shipping_class, $shipping_method) {
      static $static_shipping_array;

      if (!isset($static_shipping_array)) {
        $static_shipping_array = array();
      }

      $shipping_class_array = explode('_', $shipping_class);
      $shipping_class = $shipping_class_array[0];
      
      if (!isset($static_shipping_array[$shipping_class])) {
        $static_shipping_array[$shipping_class] = $shipping_method;
        if (file_exists(DIR_FS_CATALOG.'lang/'.$_SESSION['language'].'/modules/shipping/'.$shipping_class.'.php')){
          include_once(DIR_FS_CATALOG.'lang/'.$_SESSION['language'].'/modules/shipping/'.$shipping_class.'.php');
          $static_shipping_array[$shipping_class] = constant(strtoupper('MODULE_SHIPPING_'.$shipping_class.'_TEXT_TITLE'));
        }
      }
      
      return $static_shipping_array[$shipping_class];  
    }
  }
