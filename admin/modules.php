<?php
  /* --------------------------------------------------------------
   $Id: modules.php 16165 2024-10-09 05:30:02Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(modules.php,v 1.45 2003/05/28); www.oscommerce.com
   (c) 2003 nextcommerce (modules.php,v 1.23 2003/08/19); www.nextcommerce.org
   (c) 2006 XT-Commerce (categories.php 1123 2005-07-27)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  require('includes/application_top.php');

  // include needed functions
  require_once (DIR_FS_INC.'update_module_configuration.inc.php');

  $preferred_modules = array(
    'banktransfer',
    'cash',
    'cod',
    'invoice',
    'easycredit',
    'eustandardtransfer',
    'moneyorder',
    'paypal',
    'paypalacdc',
    'paypalpui',
    'paypalcard',
    'paypalexpress',
    'paypalsepa',
    'paypalsubscription',
    'sofort_sofortueberweisung_classic',
    'sofort_sofortueberweisung_gateway',

    'paypalapplepay',
    'paypalgooglepay',
    'paypaltrustly',
    'paypalprzelewy',
    'paypalmybank',
    'paypalideal',
    'paypaleps',
    'paypalblik',
    'paypalbancontact',
  );
  
  //Eingefügt um Fehler in CC Modul zu unterdrücken.
  require(DIR_FS_CATALOG.DIR_WS_CLASSES . 'xtcPrice.php');
  $xtPrice = new xtcPrice($_SESSION['currency'],'');
  $module_directory = '';
  $module_key = '';
  $installed = false;
  $deinstalled = false;

  $mTypeArr = array();

  $set = (isset($_GET['set']) ? strip_tags($_GET['set']) : 'categories');
  $module_class = (isset($_GET['module']) ? strip_tags($_GET['module']) : '');
  $box = (isset($_GET['box']) ? true : false);

  if (xtc_not_null($set)) {
    switch ($set) {
      case 'shipping':
        $module_type = 'shipping';
        $module_directory = DIR_FS_CATALOG_MODULES . 'shipping/';
        $module_directory_include = DIR_WS_CATALOG.DIR_WS_MODULES . 'shipping/';
        $module_key = 'MODULE_SHIPPING_INSTALLED';
        define('HEADING_TITLE', HEADING_TITLE_MODULES_SHIPPING);
        $check_language_file = true;
        break;
      case 'ordertotal':
        $module_type = 'order_total';
        $module_directory = DIR_FS_CATALOG_MODULES . 'order_total/';
        $module_directory_include = DIR_WS_CATALOG.DIR_WS_MODULES . 'order_total/';
        $module_key = 'MODULE_ORDER_TOTAL_INSTALLED';
        define('HEADING_TITLE', HEADING_TITLE_MODULES_ORDER_TOTAL);
        $check_language_file = true;
        break;
      case 'payment':
        $module_type = 'payment';
        $module_directory = DIR_FS_CATALOG_MODULES . 'payment/';
        $module_directory_include = DIR_WS_CATALOG.DIR_WS_MODULES . 'payment/';
        $module_key = 'MODULE_PAYMENT_INSTALLED';
        define('HEADING_TITLE', HEADING_TITLE_MODULES_PAYMENT);
        if (isset($_GET['error'])) {
          $messageStack->add($_GET['error'], 'error');
        }
        $check_language_file = true;
        break;
      default:
        $check_language_file = false;
        $module_directory_include = '';
        foreach(auto_include(DIR_FS_ADMIN.'includes/extra/submenu/modules/','php') as $file) require ($file);
        if (!defined('HEADING_TITLE')) {
          define('HEADING_TITLE', BOX_MODULE_TYPE);
        }
        break;
    }

    if (isset($module_key) && $module_key != '' && !defined($module_key)) {
      $sql_data_array = array(
        'configuration_key' => $module_key,
        'configuration_value' => '',
        'configuration_group_id' => '6',
        'sort_order' => 0,
        'date_added' => 'now()',
      );
      xtc_db_perform(TABLE_CONFIGURATION, $sql_data_array);
      xtc_redirect(xtc_href_link(FILENAME_MODULE_EXPORT, xtc_get_all_get_params()));
    }
  }
  $action = (isset($_GET['action']) ? $_GET['action'] : '');
  if (xtc_not_null($action)) {
    //load language file for action
    if (is_file(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/' . $module_type . '/' . basename($module_class) . '.php')) {
      include_once(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/' . $module_type . '/' . basename($module_class) . '.php');
    }
    switch ($action) {
      case 'save':
        if (isset($_POST['configuration']) 
            && is_array($_POST['configuration'])
            )
        {
          foreach ($_POST['configuration'] as $key => $value) {
            if (is_array($_POST['configuration'][$key])) {
              // multi language config
              $keys = array_keys($_POST['configuration'][$key]);
              if (gettype(array_shift($keys)) == 'string') {
                $config_value = array();
                foreach ($_POST['configuration'][$key] as $k => $v) {
                  if (xtc_not_null($v)) {
                    $config_value[] =  $k . '::' . $v;
                  }
                }
                $value = implode('||', $config_value);
              } else {
                $value = implode(',', $_POST['configuration'][$key]);
              }
            }
            xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . xtc_db_input($value) . "' where configuration_key = '" . $key . "'");
          }
        }
        xtc_redirect(xtc_href_link(FILENAME_MODULES, 'set=' . $set . '&module=' . $module_class));
        break;

      case 'install':
      case 'update':
      case 'backupconfirm':
      case 'removeconfirm':
      case 'restoreconfirm':
      case 'custom':
        $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
        $class = basename($module_class);
        if (is_file($module_directory . $class . $file_extension)) {
          include_once($module_directory . $class . $file_extension);
          if (class_exists($class)) {
            $module = instantiate_class($class);
          }
          if ($action == 'install') {
            $module->install();
          } elseif ($action == 'removeconfirm') {
            $module->remove();
          } elseif ($action == 'update') {
            // update keys
            if (method_exists($module,'update')) {        
              $message = $module->update();
              if ($message !== false) {
                $messageStack->add_session((($message != '') ? $message : MODULE_UPDATE_CONFIRM), 'success');
              }
            }
          } elseif ($action == 'backupconfirm') {            
            // save values
            xtc_backup_configuration($module->keys());
            $messageStack->add_session(MODULE_BACKUP_CONFIRM, 'success');            
          } elseif ($action == 'restoreconfirm') {
            // reset backup values 
            xtc_restore_configuration($module->keys());
            $messageStack->add_session(MODULE_RESTORE_CONFIRM, 'success');            
          } elseif ($action == 'custom') {
            // call custom method
            if (method_exists($module,'custom')) {
              $module->custom(); 
            }
          }
          
        }
        xtc_redirect(xtc_href_link(FILENAME_MODULES, 'set=' . $set . '&module=' . $module_class));
        break;

      case 'edit':
        if ($set == 'payment'
            && substr($module_class, 0, 6) == 'paypal'
            ) 
        {
          xtc_redirect(xtc_href_link('paypal_module.php', 'action=edit&module=' . $module_class));
        }
        break;
    }
  }

  //########## FUNCTIONS ##########//

  function get_module_info($module) {
    $module_info = array('code' => $module->code,
                         'title' => $module->title,
                         'description' => $module->description,
                         'extended_description' => isset($module->extended_description) ? $module->extended_description : '',
                         'status' => $module->check());
    $module_info['properties'] = isset($module->properties) ? $module->properties : array();
    $module_keys = method_exists($module,'keys') ? $module->keys() : array();

    $keys_extra = array();
    if (!empty($module_keys)) {
      $key_value_query = xtc_db_query("SELECT configuration_key,
                                              configuration_value,
                                              use_function,
                                              set_function
                                         FROM " . TABLE_CONFIGURATION . "
                                        WHERE configuration_key IN ('" . implode("', '", $module_keys) . "')
                                     ORDER BY FIELD(configuration_key, '".implode("', '", $module_keys)."')");
      while ($key_value = xtc_db_fetch_array($key_value_query)) {
        $keys_extra[$key_value['configuration_key']] = array(
          'title' => constant(strtoupper($key_value['configuration_key'] .'_TITLE')),
          'description' => constant(strtoupper($key_value['configuration_key'] .'_DESC')),
          'value' => $key_value['configuration_value'],
          'use_function' => $key_value['use_function'],
          'set_function' => $key_value['set_function'],
        );
      }
    }
    $module_info['keys'] = $keys_extra;
    
    return $module_info;
  }

  function output_modules($modules_array) {
    global $module_type, $module_directory, $module_class, $set, $mInfo, $installed_modules;
    
    for ($i = 0, $n = sizeof($modules_array); $i < $n; $i++) {
      $file = $modules_array[$i];
      if (is_file(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/' . $module_type . '/' . $file)) {
        include_once(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/' . $module_type . '/' . $file);
      }
      include_once($module_directory . $file);
      $class = substr($file, 0, strpos($file, '.'));
      if (class_exists($class)) {
        $module = instantiate_class($class);
        if ($module->check() > 0) {
          $installed_modules[$module->sort_order][] = $file;
          sort($installed_modules[$module->sort_order]);
        }
        if (($module_class == '' || (isset($module_class) && ($module_class == $class))) && !isset($mInfo)) {
          $module_info = get_module_info($module);
          $mInfo = new objectInfo($module_info);                          
        }
        if (isset($mInfo) && is_object($mInfo) && ($class == $mInfo->code)) {
          if ($module->check() > 0) {
            $tr_attribute = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'pointer\'" onclick="document.location.href=\'' . xtc_href_link(FILENAME_MODULES, 'set=' . $set . '&module=' . $class . '&action=edit') . '\'"';
          } else {
            $tr_attribute = 'class="dataTableRowSelected"';
          }
        } else {
          $tr_attribute = 'class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'pointer\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . xtc_href_link(FILENAME_MODULES, 'set=' . $set . '&module=' . $class) . '\'"';
        }
        ?>
        <tr <?php echo $tr_attribute;?>>
          <td class="dataTableContent"><?php echo $module->title; ?></td>
          <td class="dataTableContent"><?php echo str_replace('.php','',$file); ?></td>
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
          <td class="dataTableContent txta-r"><?php if (isset($mInfo) && is_object($mInfo) && ($class == $mInfo->code) ) { echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ICON_ARROW_RIGHT); } else { echo '<a href="' . xtc_href_link(FILENAME_MODULES, 'set=' . $set . '&module=' . $class) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_arrow_grey.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
        </tr>
        <?php
      }
    }
  }
  
  function check_update_needed($module_type) {
    global $module_directory, $messageStack;
    
    $installed_array = explode(';', constant('MODULE_'.strtoupper($module_type).'_INSTALLED'));
    $info = array();
    if (count($installed_array) > 0) {
      foreach ($installed_array as $file) {
        if (is_file($module_directory . $file)) {
          if (is_file(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/' . $module_type . '/' . $file)) {
            include_once(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/' . $module_type . '/' . $file);
          }
          include_once($module_directory . $file);
          $class = substr($file, 0, strpos($file, '.'));
          if (class_exists($class)) {
            $module = instantiate_class($class);
            if ($module instanceof $class && $module->check() > 0) {     
              $key_array = $module->keys();     
              foreach ($key_array as $key) {
                if (!defined($key)) {
                  $info[] = '<li>'.$module->title.' ('.$class.')</li>';
                  break;
                }
              }
            }
          }
        }
      }
    }
    return $info;
  }

  function instantiate_class($class) {
    static $module_array;
    
    if (!isset($module_array)) $module_array = array();
    
    if (!isset($module_array[$class]) || !is_object($module_array[$class])) {
      $module_array[$class] = new $class;
    }
    
    return $module_array[$class];
  }

require (DIR_WS_INCLUDES.'head.php');
if (xtc_not_null($action) && !$box) {
  echo '<link href="includes/css/module_box_full.css" rel="stylesheet" type="text/css" />';
  if (is_file('includes/css/'.basename($module_class).'.css')) {
    echo '<link href="includes/css/'.basename($module_class).'.css" rel="stylesheet" type="text/css" />';
  }
}
?>
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
        <div class="pageHeadingImage"><?php echo xtc_image(DIR_WS_ICONS.'heading/icon_modules.png'); ?></div>
        <div class="pageHeading pdg2"><?php echo HEADING_TITLE; ?></div>
        <div class="main">Modules</div>         
        <div class="clear"></div>        
        <?php
        if (count($mTypeArr) && !$action) {
          echo '<div class="submenu cf">'.implode(' ',$mTypeArr).'</div>';
        }
        ?>
        <table class="tableCenter">
          <tr>
            <?php 
            if (!xtc_not_null($action) || $box) { 

              $info = check_update_needed($module_type);
              if (count($info) > 0) {
                echo '<div class="error_message">'.TEXT_MODULE_UPDATE_NEEDED.'<ul>'.implode('', $info).'</ul></div>';
              }
              
              $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.') + 1);
              
              $directory_array = array(
                'installed' => array(),
                'preferred' => array(),
                'uninstalled' => array(),
              );

              foreach(auto_include($module_directory, $file_extension) as $file) {
                $filename = basename($file);
                
                if (is_file(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/' . $module_type . '/' . $filename)) {
                  include_once(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/' . $module_type . '/' . $filename);
                } elseif ($check_language_file) {
                  $messageStack->add_session(sprintf(TEXT_MODULE_FILE_MISSING, $_SESSION['language'], $filename), 'warning');
                }
                include_once($module_directory . $filename);
        
                $class = substr($filename, 0, strpos($filename, '.'));
                if (class_exists($class)) {
                  $module = instantiate_class($class);

                  if (is_object($module) && method_exists($module,'check')) {
                    if ($module instanceof $class && $module->check() > 0) {
                      if (!isset($module->sort_order) || !is_numeric($module->sort_order)) {
                        $module->sort_order = 0;
                      }
                      $directory_array['installed'][get_module_configuration_sorting($directory_array['installed'], $module->sort_order)] = $filename;
                    } elseif (in_array($class, $preferred_modules)) {
                      $directory_array['preferred'][] = $filename;
                    } else {
                      $directory_array['uninstalled'][] = $filename;
                    }
                  }
                  
                  unset($module);
                }
              }

              if (count($directory_array['installed']) > 0) {
                ksort($directory_array['installed']);
                $directory_array['installed'] = array_values($directory_array['installed']);
              }
              
              if (count($directory_array['preferred']) > 0) {
                sort($directory_array['preferred']);
              }
              
              if (count($directory_array['uninstalled']) > 0) {
                sort($directory_array['uninstalled']);
              }
              ?>
              <td class="boxCenterLeft">
                <table class="tableBoxCenter collapse">
                  <?php
                  $installed_modules = array();
                
                  if (count($directory_array['installed']) > 0) {
                    ?>
                    <tr class="dataTableHeadingRow sub">
                      <td colspan="5" class="dataTableHeadingContent txta-c" ><?php echo TABLE_HEADING_MODULES_INSTALLED; ?></td>
                    </tr>
                    <tr class="dataTableHeadingRow">
                      <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_MODULES; ?></td>
                      <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_FILENAME; ?></td>
                      <td class="dataTableHeadingContent txta-r"><?php echo TABLE_HEADING_SORT_ORDER; ?></td>
                      <td class="dataTableHeadingContent txta-c"><?php echo TABLE_HEADING_STATUS; ?>&nbsp;</td>
                      <td class="dataTableHeadingContent txta-r"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
                    </tr>
                    <?php
                    output_modules($directory_array['installed']);
                    echo '<tr><td colspan="5" style="height:35px;">&nbsp;</td></tr>'.PHP_EOL;
                  }

                  if (count($directory_array['preferred']) > 0) {
                    ?>
                    <tr class="dataTableHeadingRow sub">
                      <td colspan="5" class="dataTableHeadingContent txta-c" ><?php echo TABLE_HEADING_MODULES_PREFERRED; ?></td>
                    </tr>
                    <tr class="dataTableHeadingRow">
                      <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_MODULES; ?></td>
                      <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_FILENAME; ?></td>
                      <td class="dataTableHeadingContent txta-r"><?php echo TABLE_HEADING_SORT_ORDER; ?></td>
                      <td class="dataTableHeadingContent txta-c"><?php echo TABLE_HEADING_STATUS; ?>&nbsp;</td>
                      <td class="dataTableHeadingContent txta-r"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
                    </tr>
                    <?php
                    output_modules($directory_array['preferred']);
                    echo '<tr><td colspan="5" style="height:35px;">&nbsp;</td></tr>'.PHP_EOL;
                  }

                  if (count($directory_array['uninstalled']) > 0) {
                    ?>
                    <tr class="dataTableHeadingRow sub">
                      <td colspan="5" class="dataTableHeadingContent txta-c" ><?php echo TABLE_HEADING_MODULES_NOT_INSTALLED; ?></td>
                    </tr>
                    <tr class="dataTableHeadingRow">
                      <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_MODULES; ?></td>
                      <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_FILENAME; ?></td>
                      <td class="dataTableHeadingContent txta-r"><?php echo TABLE_HEADING_SORT_ORDER; ?></td>
                      <td class="dataTableHeadingContent txta-c"><?php echo TABLE_HEADING_STATUS; ?>&nbsp;</td>
                      <td class="dataTableHeadingContent txta-r"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
                    </tr>
                    <?php
                    output_modules($directory_array['uninstalled']);
                  }
                  
                  ksort($installed_modules);
                  $installed_modules = array_reduce($installed_modules, 'array_merge', array());
                  if ($module_key) {
                    $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = '" . $module_key . "'");
                    if (xtc_db_num_rows($check_query)) {
                      $check = xtc_db_fetch_array($check_query);
                      if ($check['configuration_value'] != implode(';', $installed_modules)) {
                        xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . implode(';', $installed_modules) . "', last_modified = now() where configuration_key = '" . $module_key . "'");
                      }
                    } else {
                      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ( '" . $module_key . "', '" . implode(';', $installed_modules) . "','6', '0', now())");
                    }
                  }
                  ?>
                </table>
                <div class="smallText pdg2"><?php echo TEXT_MODULE_DIRECTORY . $module_directory_include; ?></div>
              </td>
              <?php          
            }
            
            //BOC BOX RIGHT
            $heading = array();
            $contents = array();
            switch ($action) {
              case 'restore':
                  $heading[] = array('text' => '<b>' . $mInfo->title . '</b>');
                  $contents = array ('form' => (isset($mInfo->properties['form_restore']) ? $mInfo->properties['form_restore'] : xtc_draw_form('modules', FILENAME_MODULES, 'set=' . $set . '&module=' . $module_class . '&action=restoreconfirm')));
                  $contents[] = array ('text' => '<br />'.TEXT_INFO_MODULE_RESTORE);
                  if (isset($mInfo->properties['restore']) && count($mInfo->properties['restore']) > 0) {
                    foreach($mInfo->properties['restore'] as $key) {
                      $contents[] = array ('text' => '<br />'.$key);
                    }
                  }
                  $contents[] = array ('align' => 'center', 'text' => '<br /><input type="submit" class="button" onclick="this.blur();" value="'. BUTTON_RESTORE .'"><a class="button" onclick="this.blur();" href="'.xtc_href_link(FILENAME_MODULES, 'set=' . $set . '&module=' . $module_class).'">' . BUTTON_CANCEL . '</a><br/><br/>');
                  break;
              case 'backup':
                  $heading[] = array('text' => '<b>' . $mInfo->title . '</b>');
                  $contents = array ('form' => (isset($mInfo->properties['form_backup']) ? $mInfo->properties['form_backup'] : xtc_draw_form('modules', FILENAME_MODULES, 'set=' . $set . '&module=' . $module_class . '&action=backupconfirm')));
                  $contents[] = array ('text' => '<br />'.TEXT_INFO_MODULE_BACKUP);
                  if (isset($mInfo->properties['backup']) && count($mInfo->properties['backup']) > 0) {
                    foreach($mInfo->properties['backup'] as $key) {
                      $contents[] = array ('text' => '<br />'.$key);
                    }
                  }
                  $contents[] = array ('align' => 'center', 'text' => '<br /><input type="submit" class="button" onclick="this.blur();" value="'. BUTTON_BACKUP .'"><a class="button" onclick="this.blur();" href="'.xtc_href_link(FILENAME_MODULES, 'set=' . $set . '&module=' . $module_class).'">' . BUTTON_CANCEL . '</a><br/><br/>');
                  break;
              case 'remove':
                  $heading[] = array('text' => '<b>' . $mInfo->title . '</b>');
                  $contents = array ('form' => (isset($mInfo->properties['form_remove']) ? $mInfo->properties['form_remove'] : xtc_draw_form('modules', FILENAME_MODULES, 'set=' . $set . '&module=' . $module_class . '&action=removeconfirm')));
                  $contents[] = array ('text' => '<br />'.TEXT_INFO_MODULE_REMOVE);
                  if (isset($mInfo->properties['remove']) && count($mInfo->properties['remove']) > 0) {
                    foreach($mInfo->properties['remove'] as $key) {
                      $contents[] = array ('text' => '<br />'.$key);
                    }
                  }
                  $contents[] = array ('align' => 'center', 'text' => '<br /><input type="submit" class="button" onclick="this.blur();" value="'. BUTTON_MODULE_REMOVE .'"><a class="button" onclick="this.blur();" href="'.xtc_href_link(FILENAME_MODULES, 'set=' . $set . '&module=' . $module_class).'">' . BUTTON_CANCEL . '</a><br/><br/>');
                  break;
              case 'edit':
                if (isset($module_class) && !isset($mInfo)) {
                  $heading = array();
                  $contents = array();
                  $class = basename($module_class);
                  if (is_file(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/' . $module_type . '/' . $class. '.php')) {
                    include_once(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/' . $module_type . '/' . $class. '.php');
                  }  
                  include_once($module_directory . $class . '.php');
                  if (class_exists($class)) {
                    $module = new $class;
                    $module_info = get_module_info($module);
                    $mInfo = new objectInfo($module_info);
                  }
                } 
                $keys = '';
                foreach ($mInfo->keys as $key => $value) {
                  $keys .= '<b>' . $value['title'] . '</b>' . (($value['description'] != '') ? '<br />' . $value['description'] : '').'<br />';
                  if ($value['set_function']) {
                    if (strpos($value['set_function'], '->') !== false) {
                      $class_method = explode('->', $value['set_function']);
                      if (!isset(${$class_method[0]}) || !is_object(${$class_method[0]})) { // DokuMan - 2011-05-10 - check if object is first set
                        include(DIR_WS_CLASSES . $class_method[0] . '.php');
                        ${$class_method[0]} = new $class_method[0]();
                      }
                      $keys .= call_user_func_array(array(${$class_method[0]}, $class_method[1]), array($value['value'], $key));
                    } elseif (strpos($value['set_function'], '(') !== false) {
                      eval('$keys .= ' . $value['set_function'] . "'" . encode_htmlspecialchars($value['value'], ENT_QUOTES) . "', '" . $key . "');");
                    } else {
                      $parameters = explode(';', $value['set_function']);
                      $function = trim($parameters[0]);
                      $parameters[0] = $value['value'];
                      $parameters[] = 'configuration[%s]';
                      $keys .= xtc_call_function($function, $parameters);
                    }
                  } else {
                    $keys .= xtc_draw_input_field('configuration[' . $key . ']', $value['value'], 'class="inputModule"');
                  }
                  $keys .= '<br /><br />';
                }
                $keys = substr($keys, 0, strrpos($keys, '<br /><br />'));
                $heading[] = array('text' => '<b>' . $mInfo->title . '</b>');
                $contents = array('form' => (isset($mInfo->properties['form_edit']) ? $mInfo->properties['form_edit'] : xtc_draw_form('modules', FILENAME_MODULES, 'set=' . $set . '&module=' . $module_class . '&action=save')));
                $contents[] = array('text' => $keys);
                $contents[] = method_exists($module,'display') ? $module->display() : array();
                $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onclick="this.blur();" value="' . BUTTON_UPDATE . '"/> <a class="button" onclick="this.blur();" href="' . xtc_href_link(FILENAME_MODULES, 'set=' . $set . '&module=' . $module_class) . '">' . BUTTON_CANCEL . '</a>');
                $contents[] = method_exists($module,'display_end') ? $module->display_end() : array();
                break;

              default:
                if (isset($mInfo) && is_object($mInfo)) {
                  $heading[] = array('text' => '<b>' . $mInfo->title . ($mInfo->status > 1 ? ' '.sprintf(MULTIPLE_INSTALLATION,$mInfo->status) : '') . '</b>');
                  if ($mInfo->status != '0') {
                    $keys = '';
                    foreach ($mInfo->keys as $value) {
                      $keys .= '<b>' . (isset($value['title'])?$value['title']:'') . '</b><br />';
                      if ($value['use_function']) {
                        $use_function = $value['use_function'];
                        if (strpos($use_function, '->') !== false) {
                          $class_method = explode('->', $use_function);
                          if (!isset(${$class_method[0]}) || !is_object(${$class_method[0]})) { // DokuMan - 2011-05-10 - check if object is first set
                            include(DIR_WS_CLASSES . $class_method[0] . '.php');
                            ${$class_method[0]} = new $class_method[0]();
                          }
                          $keys .= xtc_call_function($class_method[1], $value['value'], ${$class_method[0]});
                        } else {
                          $keys .= xtc_call_function($use_function, $value['value']);
                        }
                      } else {
                        $keys .=  (strlen($value['value']) > 30) ? substr(strip_tags($value['value']),0,30) . ' ...' : $value['value'];
                      }
                      $keys .= '<br /><br />';
                    }
                    $keys = substr($keys, 0, strrpos($keys, '<br /><br />'));
                    $contents[] = array('align' => 'center', 
                                        'text' => '<a class="button btnbox" onclick="this.blur();" href="' . xtc_href_link(FILENAME_MODULES, 'set=' . $set . '&module=' . $mInfo->code . '&action=edit') . '">' . BUTTON_EDIT . '</a>'.
                                                  '<a class="button btnbox" onclick="this.blur();" href="' . xtc_href_link(FILENAME_MODULES, 'set=' . $set . '&module=' . $mInfo->code . '&action=backup&box=1') . '">' . BUTTON_BACKUP . '</a>'.
                                                  '<a class="button btnbox" onclick="this.blur();" href="' . xtc_href_link(FILENAME_MODULES, 'set=' . $set . '&module=' . $mInfo->code . '&action=restore&box=1') . '">' . BUTTON_RESTORE . '</a>'.
                                                  '<a class="button btnbox" onclick="this.blur();" href="' . xtc_href_link(FILENAME_MODULES, 'set=' . $set . '&module=' . $mInfo->code . '&action=remove&box=1') . '">' . BUTTON_MODULE_REMOVE . '</a>'. 
                                                  (isset($mInfo->properties['button_update']) ? $mInfo->properties['button_update'] : '')
                                                  );
                    $contents[] = array('text' => '<br />' . $mInfo->description);
                    if (isset($mInfo->extended_description) && $mInfo->extended_description != '') {
                      $contents[] = array('text' => '<br />' . $mInfo->extended_description);
                    }
                    $contents[] = array('text' => '<br />' . $keys);
                  } else {
                    $contents[] = array('align' => 'center', 'text' => '<a class="button btnbox" onclick="this.blur();" href="' . xtc_href_link(FILENAME_MODULES, 'set=' . $set . '&module=' . $mInfo->code . '&action=install') . '">' . BUTTON_MODULE_INSTALL . '</a>');
                    $contents[] = array('text' => '<br />' . $mInfo->description);
                  }

                }
                break;
            }
            if ( (xtc_not_null($heading)) && (xtc_not_null($contents)) ) {
              echo '            <td class="boxRight">' . "\n";
              echo '<div class="modulbox">';
              $box = new box;
              echo $box->infoBox($heading, $contents);
              if (isset($mInfo->properties['add_content'])) {
                echo $mInfo->properties['add_content'];
              }
              echo '</div>';
              echo '            </td>' . "\n";
            }
            //EOC BOX RIGHT
            ?>
          </tr>
        </table>        
      </td>
      <!-- body_text_eof //-->
    </tr>
  </table>
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
  <br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>