<?php
/* -----------------------------------------------------------------------------------------
   $Id: paypal_module.php 16441 2025-05-06 11:28:15Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


require('includes/application_top.php');

function get_module_info($module) {
  $module_info = array('code' => $module->code,
                       'title' => $module->title,
                       'description' => $module->description,
                       'extended_description' => isset($module->extended_description) ? $module->extended_description : '',
                       'status' => $module->check());
  $module_info['properties'] = isset($module->properties) ? $module->properties : array();
  $module_keys = method_exists($module,'keys') ? $module->keys() : array();
  $keys_extra = array();
  for ($j = 0, $k = sizeof($module_keys); $j < $k; $j++) {
    $key_value_query = xtc_db_query("SELECT configuration_key,
                                            configuration_value,
                                            use_function,
                                            set_function
                                       FROM " . TABLE_CONFIGURATION . "
                                      WHERE configuration_key = '" . $module_keys[$j] . "'");
    $key_value = xtc_db_fetch_array($key_value_query);
    if ($key_value['configuration_key'] != '') {
      $keys_extra[$module_keys[$j]]['title'] = constant(strtoupper($key_value['configuration_key'] .'_TITLE'));
    }
    $keys_extra[$module_keys[$j]]['value'] = $key_value['configuration_value'];
    if ($key_value['configuration_key'] != '') {
      $keys_extra[$module_keys[$j]]['description'] = constant(strtoupper($key_value['configuration_key'] .'_DESC'));
    }
    $keys_extra[$module_keys[$j]]['use_function'] = $key_value['use_function'];
    $keys_extra[$module_keys[$j]]['set_function'] = $key_value['set_function'];
  }
  $module_info['keys'] = $keys_extra;
  
  return $module_info;
}

// languages
$languages = xtc_get_languages(); 

$orders_v1_array = array(
  'paypalclassic',
  'paypalcart',
  'paypalplus',
  'paypallink',
  'paypalpluslink',
  'paypalsubscription',
);

$orders_v2_array = array(
  'paypal',
  'paypalacdc',
  'paypalpui',
  'paypalexpress',
  'paypalapplepay',
  'paypalgooglepay',
  'paypalcard',
  'paypalsepa',
  'paypalsofort',
  'paypaltrustly',
  'paypalprzelewy',
  'paypalmybank',
  'paypalideal',
  'paypaleps',
  'paypalblik',
  'paypalbancontact',
);

$fixed_zones_modules_array = array(
  'paypalacdc',
  'paypalpui',
  'paypalexpress',
  'paypalsofort',
  'paypaltrustly',
  'paypalprzelewy',
  'paypalmybank',
  'paypalideal',
  'paypaleps',
  'paypalblik',
  'paypalbancontact',
);

$payment_array = array_merge($orders_v2_array, $orders_v1_array);

$payment_disallowed_array = array(
  'banktransfer',
  'billpay',
  'billpaydebit',
  'billpaypaylater',
  'billpaytransactioncredit',
  'payone_installment',
  'payone_otrans',
);

$status_array = array(
  array('id' => 1, 'text' => YES),
  array('id' => 0, 'text' => NO),
); 

// include needed classes
require_once(DIR_FS_EXTERNAL.'paypal/classes/PayPalAdmin.php');
$paypal = new PayPalAdmin();

if (isset($_GET['action'])) {
  switch ($_GET['action']) {
    case 'delete':
      $paypal->delete_webhook($_GET['id']);      
      xtc_redirect(xtc_href_link(basename($PHP_SELF)));
      break;

    case 'update':
      if (isset($_POST['config'])) {
        if (isset($_POST['config']['thirdparty'])) {
          $thirdparty = array();
          foreach ($_POST['config']['thirdparty'] as $key => $value) {
            if ($value == '1') {
              $thirdparty[] = $key;
            }
          }
          xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_PAYMENT_PAYPAL_PLUS_THIRDPARTY_PAYMENT'");
          xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, last_modified) VALUES ('MODULE_PAYMENT_PAYPAL_PLUS_THIRDPARTY_PAYMENT', '" . xtc_db_input(implode(';', $thirdparty)) . "', NOW())");
        }
        if (isset($_POST['config']['description'])) {
          $sql_data_array = array();
          foreach ($_POST['config']['description'] as $key => $value) {
            if ($value != '') {
              $sql_data_array[] = array(
                'config_key' => $key,
                'config_value' => $value,
              );
            } else {
              $paypal->delete_config($key);
            }
          }
          $paypal->save_config($sql_data_array);
        }
        if (isset($_POST['config']['profile'])) {
          $sql_data_array = array();
          foreach ($_POST['config']['profile'] as $key => $value) {
            $sql_data_array[] = array(
              'config_key' => $key,
              'config_value' => ((preg_replace('/[^0-9,.]/', '', $value) == $value) ? str_replace(',', '.', $value) : $value),
            );
          }
          $paypal->save_config($sql_data_array);
        }
      }
      if (isset($_POST['configuration']) && count($_POST['configuration']) > 0) {
        foreach ($_POST['configuration'] as $key => $value) {
          xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . xtc_db_input($value) . "', last_modified = NOW() WHERE configuration_key='" . $key . "'");
        }
      }
      xtc_redirect(xtc_href_link(basename($PHP_SELF)));
      break;

    case 'install':
      if (in_array($_GET['module'], $payment_array)) {                  
        include_once(DIR_FS_LANGUAGES.$_SESSION['language'].'/modules/payment/'.$_GET['module'].'.php');
        require_once(DIR_FS_CATALOG.'includes/modules/payment/'.$_GET['module'].'.php');
        $module = new $_GET['module']();
        $module->install();
        
        $installed_modules = explode(';', MODULE_PAYMENT_INSTALLED);
        if (!in_array($_GET['module'].'.php', $installed_modules)) {
          $installed_modules[] = $_GET['module'].'.php';
          xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " 
                           SET configuration_value = '" . implode(';', $installed_modules) . "', 
                               last_modified = now() 
                         WHERE configuration_key = 'MODULE_PAYMENT_INSTALLED'");
        }
        
        if ($module->code == 'paypalapplepay') {
          $config = $paypal->get_config('PAYPAL_MODE');
          $paypal->applepay_association($config, true);
        }
      }
      xtc_redirect(xtc_href_link(basename($PHP_SELF)));

    case 'remove':
      if (in_array($_GET['module'], $payment_array)) {                  
        include_once(DIR_FS_LANGUAGES.$_SESSION['language'].'/modules/payment/'.$_GET['module'].'.php');
        require_once(DIR_FS_CATALOG.'includes/modules/payment/'.$_GET['module'].'.php');
        $module = new $_GET['module']();
        $module->remove();
        
        $installed_modules = explode(';', MODULE_PAYMENT_INSTALLED);
        if (in_array($_GET['module'].'.php', $installed_modules)) {
          $key = array_search($_GET['module'].'.php', $installed_modules);        
          unset($installed_modules[$key]);
          
          xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " 
                           SET configuration_value = '" . implode(';', $installed_modules) . "', 
                               last_modified = now() 
                         WHERE configuration_key = 'MODULE_PAYMENT_INSTALLED'");
        }
      }
      xtc_redirect(xtc_href_link(basename($PHP_SELF)));
      break;
  }
}

require (DIR_WS_INCLUDES.'head.php');
?>
<link rel="stylesheet" type="text/css" href="../includes/external/paypal/css/stylesheet.css">  
<style type="text/css">
  .tableConfig td a.button { font-size: 10px; }
</style>
</head>
<body>
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->

    <!-- body //-->
    <table class="tableBody">
      <tr>
        <?php //left_navigation
        if (USE_ADMIN_TOP_MENU == 'false') {
          echo '<td class="columnLeft2">'.PHP_EOL;
          echo '<!-- left_navigation //-->'.PHP_EOL;       
          require_once(DIR_WS_INCLUDES . 'column_left.php');
          echo '<!-- left_navigation eof //-->'.PHP_EOL; 
          echo '</td>'.PHP_EOL;      
        }
        ?>
        <!-- body_text //-->
        <td class="boxCenter">         
          <div class="pageHeadingImage"><?php echo xtc_image(DIR_WS_ICONS.'heading/icon_configuration.png'); ?></div>
          <div class="flt-l">
            <div class="pageHeading pdg2"><?php echo TEXT_PAYPAL_MODULE_HEADING_TITLE; ?></div>
            <div class="main">v<?php echo $paypal->paypal_version; ?></div>
          </div>
          <?php
            include_once(DIR_FS_EXTERNAL.'paypal/modules/admin_menu.php');
          ?>
          <div class="clear div_box mrg5" style="margin-top:-1px;">
            <table class="clear tableConfig">
            <?php
              if (isset($_GET['action']) && $_GET['action'] == 'edit') {
 
                if (in_array($_GET['module'], $payment_array)) {
                  echo xtc_draw_form('config', basename($PHP_SELF), xtc_get_all_get_params(array('action')).'action=update');
                  
                  require_once(DIR_FS_CATALOG.'includes/modules/payment/'.$_GET['module'].'.php');
                  require_once(DIR_FS_CATALOG.'lang/'.$_SESSION['language'].'/modules/payment/'.$_GET['module'].'.php');

                  $module = new $_GET['module'];                  
                  $module_info = get_module_info($module);
                  $mInfo = new objectInfo($module_info);
                  
                  ?>
                  <tr>
                    <td colspan="3" style="border-top:none;padding-top:0;padding-bottom:20px;">
                    <h3><?php echo $mInfo->title; ?></h3>
                    <?php echo $mInfo->description; ?>
                    </td>
                  </tr>
                  <?php
                  
                  foreach ($mInfo->keys as $key => $value) {
                    if (strpos($key, '_ZONE') === false) {
                      ?>
                      <tr>
                        <td class="dataTableConfig col-left"><?php echo $value['title']; ?></td>
                        <td class="dataTableConfig col-middle">
                        <?php
                          if (in_array($module->code, $fixed_zones_modules_array)
                              && isset($module->allowed_zones) 
                              && is_array($module->allowed_zones)
                              && strpos($key, '_ALLOWED') !== false
                              )
                          {
                            echo implode(', ', $module->allowed_zones);
                          } else {
                            if ($value['set_function']) {
                              eval('echo ' . $value['set_function'] . "'" . $value['value'] . "', '" . $key . "');");
                            } else {
                              echo xtc_draw_input_field('configuration[' . $key . ']', $value['value'], 'style="width: 300px;"');
                            }
                          }
                        ?>
                        </td>
                        <td class="dataTableConfig col-right"><?php echo $value['description']; ?></td>
                      </tr>
                      <?php
                    }
                  }

                  if ($module->code == 'paypallink' || $module->code == 'paypalpluslink') {
                    ?>
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo TEXT_PAYPAL_MODULE_LINK_ACCOUNT; ?></td>
                      <td class="dataTableConfig col-middle"><?php echo draw_on_off_selection('config[profile][MODULE_PAYMENT_'.strtoupper($module->code).'_USE_ACCOUNT]', $status_array, (($paypal->get_config('MODULE_PAYMENT_'.strtoupper($module->code).'_USE_ACCOUNT') == '1') ? true : false)); ?></td>
                      <td class="dataTableConfig col-right"><?php echo TEXT_PAYPAL_MODULE_LINK_ACCOUNT_INFO; ?></td>
                    </tr>
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo TEXT_PAYPAL_MODULE_LINK_SUCCESS; ?></td>
                      <td class="dataTableConfig col-middle"><?php echo draw_on_off_selection('config[profile][MODULE_PAYMENT_'.strtoupper($module->code).'_SUCCESS]', $status_array, (($paypal->get_config('MODULE_PAYMENT_'.strtoupper($module->code).'_SUCCESS') == '1') ? true : false)); ?></td>
                      <td class="dataTableConfig col-right"><?php echo TEXT_PAYPAL_MODULE_LINK_SUCCESS_INFO; ?></td>
                    </tr>
                    <?php
                  }

                  if ($module->code == 'paypalcart') {
                    ?>
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo TEXT_PAYPAL_MODULE_PRODUCT; ?></td>
                      <td class="dataTableConfig col-middle"><?php echo draw_on_off_selection('config[profile][MODULE_PAYMENT_'.strtoupper($module->code).'_SHOW_PRODUCT]', $status_array, (($paypal->get_config('MODULE_PAYMENT_'.strtoupper($module->code).'_SHOW_PRODUCT') == '1') ? true : false)); ?></td>
                      <td class="dataTableConfig col-right"><?php echo TEXT_PAYPAL_MODULE_PRODUCT_INFO; ?></td>
                    </tr>
                    <?php
                  }

                  if ($module->code == 'paypal') {
                    ?>
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo TEXT_PAYPAL_BUTTON_LAYOUT; ?></td>
                      <td class="dataTableConfig col-middle"><?php echo xtc_draw_pull_down_menu('config[profile][PAYPAL_BUTTON_LAYOUT]', array(array('id' => 'horizontal', 'text' => 'horizontal'), array('id' => 'vertical', 'text' => 'vertical')), $paypal->get_config('PAYPAL_BUTTON_LAYOUT')); ?></td>
                      <td class="dataTableConfig col-right"><?php echo TEXT_PAYPAL_BUTTON_LAYOUT_INFO; ?></td>
                    </tr>
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo TEXT_PAYPAL_BUTTON_SHAPE; ?></td>
                      <td class="dataTableConfig col-middle"><?php echo xtc_draw_pull_down_menu('config[profile][PAYPAL_BUTTON_SHAPE]', array(array('id' => 'rect', 'text' => 'rectangular'), array('id' => 'pill', 'text' => 'pill')), $paypal->get_config('PAYPAL_BUTTON_SHAPE')); ?></td>
                      <td class="dataTableConfig col-right"><?php echo TEXT_PAYPAL_BUTTON_SHAPE_INFO; ?></td>
                    </tr>
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo TEXT_PAYPAL_BUTTON_PRIMARY_COLOR; ?></td>
                      <td class="dataTableConfig col-middle"><?php echo xtc_draw_pull_down_menu('config[profile][PAYPAL_BUTTON_PRIMARY_COLOR]', array(array('id' => 'gold', 'text' => 'gold'), array('id' => 'blue', 'text' => 'blue'), array('id' => 'silver', 'text' => 'silver'), array('id' => 'white', 'text' => 'white'), array('id' => 'black', 'text' => 'black')), $paypal->get_config('PAYPAL_BUTTON_PRIMARY_COLOR')); ?></td>
                      <td class="dataTableConfig col-right"><?php echo TEXT_PAYPAL_BUTTON_PRIMARY_COLOR_INFO; ?></td>
                    </tr>
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo TEXT_PAYPAL_BUTTON_SECONDARY_COLOR; ?></td>
                      <td class="dataTableConfig col-middle"><?php echo xtc_draw_pull_down_menu('config[profile][PAYPAL_BUTTON_SECONDARY_COLOR]', array(array('id' => 'gold', 'text' => 'gold'), array('id' => 'blue', 'text' => 'blue'), array('id' => 'silver', 'text' => 'silver'), array('id' => 'white', 'text' => 'white'), array('id' => 'black', 'text' => 'black')), $paypal->get_config('PAYPAL_BUTTON_SECONDARY_COLOR')); ?></td>
                      <td class="dataTableConfig col-right"><?php echo TEXT_PAYPAL_BUTTON_SECONDARY_COLOR_INFO; ?></td>
                    </tr>
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo TEXT_PAYPAL_BUTTON_HEIGHT; ?></td>
                      <td class="dataTableConfig col-middle"><?php echo xtc_draw_input_field('config[profile][PAYPAL_BUTTON_HEIGHT]', $paypal->get_config('PAYPAL_BUTTON_HEIGHT'), 'style="width: 300px;"'); ?></td>
                      <td class="dataTableConfig col-right"><?php echo TEXT_PAYPAL_BUTTON_HEIGHT_INFO; ?></td>
                    </tr>
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo TEXT_PAYPAL_MODULE_SAVE_PAYMENT; ?></td>
                      <td class="dataTableConfig col-middle"><?php echo draw_on_off_selection('config[profile][MODULE_PAYMENT_'.strtoupper($module->code).'_SAVE_PAYMENT]', $status_array, (($paypal->get_config('MODULE_PAYMENT_'.strtoupper($module->code).'_SAVE_PAYMENT') == '1') ? true : false)); ?></td>
                      <td class="dataTableConfig col-right"><?php echo TEXT_PAYPAL_MODULE_SAVE_PAYMENT_INFO; ?></td>
                    </tr>
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo TEXT_PAYPAL_MODULE_CHECKOUT_BNPL; ?></td>
                      <td class="dataTableConfig col-middle"><?php echo draw_on_off_selection('config[profile][MODULE_PAYMENT_'.strtoupper($module->code).'_SHOW_CHECKOUT_BNPL]', $status_array, (($paypal->get_config('MODULE_PAYMENT_'.strtoupper($module->code).'_SHOW_CHECKOUT_BNPL') == '1') ? true : false)); ?></td>
                      <td class="dataTableConfig col-right"><?php echo TEXT_PAYPAL_MODULE_CHECKOUT_BNPL_INFO; ?></td>
                    </tr>
                    <?php
                  }

                  if ($module->code == 'paypalapplepay') {
                    ?>
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo TEXT_PAYPAL_COMPANY_LABEL; ?></td>
                      <td class="dataTableConfig col-middle"><?php echo xtc_draw_input_field('config[profile][PAYPAL_COMPANY_LABEL]', $paypal->get_config('PAYPAL_COMPANY_LABEL'), 'style="width: 300px;"'); ?></td>
                      <td class="dataTableConfig col-right"><?php echo TEXT_PAYPAL_COMPANY_LABEL_INFO; ?></td>
                    </tr>
                    <?php
                  }

                  if ($module->code == 'paypalacdc') {
                    ?>
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo TEXT_PAYPAL_MODULE_SAVE_PAYMENT; ?></td>
                      <td class="dataTableConfig col-middle"><?php echo draw_on_off_selection('config[profile][MODULE_PAYMENT_'.strtoupper($module->code).'_SAVE_PAYMENT]', $status_array, (($paypal->get_config('MODULE_PAYMENT_'.strtoupper($module->code).'_SAVE_PAYMENT') == '1') ? true : false)); ?></td>
                      <td class="dataTableConfig col-right"><?php echo TEXT_PAYPAL_MODULE_SAVE_PAYMENT_INFO; ?></td>
                    </tr>
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo TEXT_PAYPAL_MODULE_ACDC_EXTEND_CARDS; ?></td>
                      <td class="dataTableConfig col-middle"><?php echo draw_on_off_selection('config[profile][MODULE_PAYMENT_'.strtoupper($module->code).'_EXTEND_CARDS]', $status_array, (($paypal->get_config('MODULE_PAYMENT_'.strtoupper($module->code).'_EXTEND_CARDS') == '1') ? true : false)); ?></td>
                      <td class="dataTableConfig col-right"><?php echo TEXT_PAYPAL_MODULE_ACDC_EXTEND_CARDS_INFO; ?></td>
                    </tr>
                    <?php
                  }

                  if ($module->code == 'paypalexpress') {
                    ?>
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo TEXT_PAYPAL_MODULE_OFFER_SAVE_PAYMENT; ?></td>
                      <td class="dataTableConfig col-middle"><?php echo draw_on_off_selection('config[profile][MODULE_PAYMENT_'.strtoupper($module->code).'_SAVE_PAYMENT]', $status_array, (($paypal->get_config('MODULE_PAYMENT_'.strtoupper($module->code).'_SAVE_PAYMENT') == '1') ? true : false)); ?></td>
                      <td class="dataTableConfig col-right"><?php echo TEXT_PAYPAL_MODULE_OFFER_SAVE_PAYMENT_INFO; ?></td>
                    </tr>
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo TEXT_PAYPAL_MODULE_CART_BNPL; ?></td>
                      <td class="dataTableConfig col-middle"><?php echo draw_on_off_selection('config[profile][MODULE_PAYMENT_'.strtoupper($module->code).'_SHOW_CART_BNPL]', $status_array, (($paypal->get_config('MODULE_PAYMENT_'.strtoupper($module->code).'_SHOW_CART_BNPL') == '1') ? true : false)); ?></td>
                      <td class="dataTableConfig col-right"><?php echo TEXT_PAYPAL_MODULE_CART_BNPL_INFO; ?></td>
                    </tr>
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo TEXT_PAYPAL_MODULE_PRODUCT; ?></td>
                      <td class="dataTableConfig col-middle"><?php echo draw_on_off_selection('config[profile][MODULE_PAYMENT_'.strtoupper($module->code).'_SHOW_PRODUCT]', $status_array, (($paypal->get_config('MODULE_PAYMENT_'.strtoupper($module->code).'_SHOW_PRODUCT') == '1') ? true : false)); ?></td>
                      <td class="dataTableConfig col-right"><?php echo TEXT_PAYPAL_MODULE_PRODUCT_INFO; ?></td>
                    </tr>
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo TEXT_PAYPAL_MODULE_PRODUCT_BNPL; ?></td>
                      <td class="dataTableConfig col-middle"><?php echo draw_on_off_selection('config[profile][MODULE_PAYMENT_'.strtoupper($module->code).'_SHOW_PRODUCT_BNPL]', $status_array, (($paypal->get_config('MODULE_PAYMENT_'.strtoupper($module->code).'_SHOW_PRODUCT_BNPL') == '1') ? true : false)); ?></td>
                      <td class="dataTableConfig col-right"><?php echo TEXT_PAYPAL_MODULE_PRODUCT_BNPL_INFO; ?></td>
                    </tr>
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo TEXT_PAYPAL_MODULE_BOX_CART; ?></td>
                      <td class="dataTableConfig col-middle"><?php echo draw_on_off_selection('config[profile][MODULE_PAYMENT_'.strtoupper($module->code).'_SHOW_BOX_CART]', $status_array, (($paypal->get_config('MODULE_PAYMENT_'.strtoupper($module->code).'_SHOW_BOX_CART') == '1') ? true : false)); ?></td>
                      <td class="dataTableConfig col-right"><?php echo TEXT_PAYPAL_MODULE_BOX_CART_INFO; ?></td>
                    </tr>
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo TEXT_PAYPAL_MODULE_BOX_CART_BNPL; ?></td>
                      <td class="dataTableConfig col-middle"><?php echo draw_on_off_selection('config[profile][MODULE_PAYMENT_'.strtoupper($module->code).'_SHOW_BOX_CART_BNPL]', $status_array, (($paypal->get_config('MODULE_PAYMENT_'.strtoupper($module->code).'_SHOW_BOX_CART_BNPL') == '1') ? true : false)); ?></td>
                      <td class="dataTableConfig col-right"><?php echo TEXT_PAYPAL_MODULE_BOX_CART_BNPL_INFO; ?></td>
                    </tr>
                    <?php
                  }
                  
                  if (in_array($module->code, $orders_v1_array)) {
                    $list = $paypal->list_profile();
                    $profile_array = array(array('id' => '', 'text' => TEXT_PAYPAL_NO_PROFILE));
                  
                    if (count($list) > 0) {
                      $profile_array = array(array('id' => '', 'text' => TEXT_PAYPAL_STANDARD_PROFILE));
                      for ($i=0, $n=count($list); $i<$n; $i++) {
                        $profile_array[] = array('id' => $list[$i]['id'], 'text' => $list[$i]['name']);
                      }
                    }
                    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                    ?>
                    <tr>
                      <td class="dataTableConfig col-left"><div style="float:left;margin-right:5px;"><?php echo xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']); ?></div><?php echo TEXT_PAYPAL_MODULE_PROFILE; ?></td>
                      <td class="dataTableConfig col-middle"><?php echo xtc_draw_pull_down_menu('config[profile][PAYPAL_'.strtoupper($module->code.'_'.$languages[$i]['code']).'_PROFILE]', $profile_array, $paypal->get_config('PAYPAL_'.strtoupper($module->code.'_'.$languages[$i]['code']).'_PROFILE')); ?></td>
                      <td class="dataTableConfig col-right"></td>
                    </tr>
                    <?php
                    }
                  }
                                         
                  if ($module->code == 'paypalplus') {
                    ?>
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo TEXT_PAYPAL_MODULE_USE_TABS; ?></td>
                      <td class="dataTableConfig col-middle"><?php echo draw_on_off_selection('config[profile][MODULE_PAYMENT_PAYPALPLUS_USE_TABS]', $status_array, $paypal->get_config('MODULE_PAYMENT_PAYPALPLUS_USE_TABS')); ?></td>
                      <td class="dataTableConfig col-right"><?php echo TEXT_PAYPAL_MODULE_USE_TABS_INFO; ?></td>
                    </tr>
                    <?php
                    if (xtc_not_null(MODULE_PAYMENT_INSTALLED)) {
                      $thirdparty_module = explode(';', ((defined('MODULE_PAYMENT_PAYPAL_PLUS_THIRDPARTY_PAYMENT')) ? MODULE_PAYMENT_PAYPAL_PLUS_THIRDPARTY_PAYMENT : ''));
                      $module_array = explode(';', MODULE_PAYMENT_INSTALLED);
                      
                      $thirdparty_exists = false;
                      for ($p=0, $x=sizeof($module_array); $p<$x; $p++) {
                        $module_name = substr($module_array[$p], 0,-4);
                        if (!in_array($module_name, $payment_array)
                            && !in_array($module_name, $payment_disallowed_array)
                            ) 
                        {
                          $thirdparty_exists = true;
                        }
                      }
                      if ($thirdparty_exists === true) {
                        ?>
                        <tr class="dataTableHeadingRow">
                          <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_MODULES; ?></td>
                          <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_WALL_STATUS; ?></td>
                          <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_WALL_DESCRIPTION; ?></td>
                        </tr>
                        <?php                      
                        for ($p=0, $x=sizeof($module_array); $p<$x; $p++) {
                          $module_name = substr($module_array[$p], 0,-4);
                          if (!in_array($module_name, $payment_array)
                              && !in_array($module_name, $payment_disallowed_array)
                              ) 
                          {
                            if (file_exists(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/payment/' . $module_array[$p])) {
                              include_once(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/payment/' . $module_array[$p]);
                            }
                            ?>
                            <tr>
                              <td class="dataTableConfig col-left"><?php echo strip_tags(constant('MODULE_PAYMENT_'.strtoupper($module_name).'_TEXT_TITLE')).'<br/>('.$module_array[$p].')'; ?></td>
                              <td class="dataTableConfig col-middle"><?php echo draw_on_off_selection('config[thirdparty]['.$module_name.']', $status_array, ((in_array($module_name, $thirdparty_module)) ? true : false)); ?></td>
                              <td class="dataTableConfig col-right">
                                <?php
                                  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                                    echo '<div style="float:left;margin-right:5px;">'.xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']).'</div>';
                                    echo xtc_draw_textarea_field('config[description]['.strtoupper($module_name.'_'.$languages[$i]['code']).']', '', '55', '8', $paypal->get_config(strtoupper($module_name.'_'.$languages[$i]['code'])), 'style="display:block;"'); 
                                  }
                                ?>
                              </td>
                            </tr>
                            <?php
                          }
                        }
                      }
                    }
                  }
                }
                ?>
                <tr>
                  <td class="txta-r" colspan="3" style="border:none;">
                    <a class="button" href="<?php echo xtc_href_link(basename($PHP_SELF)); ?>"><?php echo BUTTON_CANCEL; ?></a>
                    <input type="submit" class="button" name="submit" value="<?php echo BUTTON_UPDATE; ?>">
                  </td>
                </tr>
                <?php              
              } else {
                ?>
                <table class="tableBoxCenter collapse">
                  <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_MODULES; ?></td>
                    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_FILENAME; ?></td>
                    <td class="dataTableHeadingContent txta-r"><?php echo TABLE_HEADING_SORT_ORDER; ?></td>
                    <td class="dataTableHeadingContent txta-c"><?php echo TABLE_HEADING_STATUS; ?>&nbsp;</td>
                    <td class="dataTableHeadingContent txta-r"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
                  </tr>
                  <?php
                  foreach ($payment_array as $payment_module) {
                    if (is_file(DIR_FS_CATALOG.'includes/modules/payment/'.$payment_module.'.php')) {
                      require_once(DIR_FS_CATALOG.'includes/modules/payment/'.$payment_module.'.php');
                      require_once(DIR_FS_CATALOG.'lang/'.$_SESSION['language'].'/modules/payment/'.$payment_module.'.php');
                      $module = new $payment_module;
                      ?>
                        <tr class="dataTableRow">
                          <td class="dataTableContent">
                            <?php
                              echo $module->title;
                            ?>
                          </td>
                          <td class="dataTableContent">
                            <?php
                              echo $payment_module;
                            ?>
                          </td>
                          <td class="dataTableContent txta-r">
                          <?php if (isset($module->sort_order) && is_numeric($module->sort_order)) echo $module->sort_order; ?>&nbsp;</td>
                          <td class="dataTableContent txta-c">
                            <?php
                              if ($module->check() > 0) {
                                if (isset($module->enabled) && $module->enabled) {
                                  echo xtc_image(DIR_WS_IMAGES . 'icon_lager_green.gif', ICON_ARROW_RIGHT);
                                } else {
                                  echo xtc_image(DIR_WS_IMAGES . 'icon_lager_red.gif', ICON_ARROW_RIGHT);
                                }
                              }
                            ?>
                            &nbsp;
                          </td>
                          <td class="dataTableContent txta-r">
                            <?php
                              if ($module->_check == 1) {
                                echo '<a class="button" href="'.xtc_href_link(basename($PHP_SELF), 'action=edit&module='.$module->code).'">'.BUTTON_EDIT.'</a>';
                                echo '<a class="button" href="'.xtc_href_link(basename($PHP_SELF), 'action=remove&module='.$module->code).'">'.BUTTON_MODULE_REMOVE.'</a>';
                              } else {
                                echo '<a class="button" href="'.xtc_href_link(basename($PHP_SELF), 'action=install&module='.$module->code).'">'.BUTTON_MODULE_INSTALL.'</a>';                            
                              }
                            ?>
                          </td>
                        </tr>
                      <?php                  
                    }
                  }

                  // update installed modules
                  if (xtc_not_null(MODULE_PAYMENT_INSTALLED)) {
                    $installed_modules = array();
                    $modules_array = explode(';', MODULE_PAYMENT_INSTALLED);        
                    for ($i = 0, $n = sizeof($modules_array); $i < $n; $i++) {
                      $file = $modules_array[$i];
                      if (is_file(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/payment/' . $file)) {
                        include_once(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/payment/' . $file);
                      }
                      include_once(DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/' . $file);
                      $class = substr($file, 0, strpos($file, '.'));
                      if (class_exists($class)) {
                        $module = new $class();
                        if ($module->check() > 0) {
                          $installed_modules[$module->sort_order][] = $file;
                          sort($installed_modules[$module->sort_order]);
                        }
                      }
                    }        

                    ksort($installed_modules);
                    $installed_modules = array_reduce($installed_modules, 'array_merge', array());
                    xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " 
                                     SET configuration_value = '" . implode(';', $installed_modules) . "', 
                                         last_modified = now() 
                                   WHERE configuration_key = 'MODULE_PAYMENT_INSTALLED'");
                  }
              }
            ?>
            </table>
          </div>
        </td>
        <!-- body_text_eof //-->
      </tr>
    </table>
    <!-- body_eof //-->
    <!-- footer //-->
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
    <!-- footer_eof //-->
  </body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>