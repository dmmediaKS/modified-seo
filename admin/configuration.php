<?php
/* --------------------------------------------------------------
   $Id: configuration.php 16445 2025-05-12 09:28:20Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(configuration.php,v 1.40 2002/12/29); www.oscommerce.com
   (c) 2003 nextcommerce (configuration.php,v 1.16 2003/08/19); www.nextcommerce.org
   (c) 2006 XT-Commerce (configuration.php 229 2007-03-06)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  require('includes/application_top.php');

  //include special language file
  if (isset($_GET['gID']) && is_file(DIR_FS_LANGUAGES . $_SESSION['language'] . '/admin/configuration_'.(int)$_GET['gID'].'.php')) {
    include(DIR_FS_LANGUAGES . $_SESSION['language'] . '/admin/configuration_'.(int)$_GET['gID'].'.php');
  }
  //install new configurations
  if (file_exists(DIR_WS_INCLUDES.'configuration_installer.php')) {
    include(DIR_WS_INCLUDES.'configuration_installer.php');
  }
  //set value_limits
  $value_limits = array(); 
  if (file_exists(DIR_WS_INCLUDES.'configuration_limits.php')) {
    include(DIR_WS_INCLUDES.'configuration_limits.php');
  }

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (xtc_not_null($action)) {
    switch ($action) {
      case 'save':
        // moneybookers payment module version 2.4
        if ($_GET['gID']=='31') {
          if (isset($_POST['_PAYMENT_MONEYBOOKERS_EMAILID'])) {
            $url = 'https://www.skrill.com/app/email_check.pl?email=' . urlencode($_POST['_PAYMENT_MONEYBOOKERS_EMAILID']) . '&cust_id=8644877&password=1a28e429ac2fcd036aa7d789ebbfb3b0';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch);
            if ($result=='NOK') {
              $messageStack->add_session(MB_ERROR_NO_MERCHANT, 'error');
            }
            if (strpos($result,'OK,') !== false) {
              $data = explode(',',$result);
              $_POST['_PAYMENT_MONEYBOOKERS_MERCHANTID'] = $data[1];
              $messageStack->add_session(sprintf(MB_MERCHANT_OK,$data[1]), 'success');
            }
          }
        }

        // update changed configurations
        if ($_GET['gID'] != '6' && isset($_POST) && count($_POST) > 0) {
          $configuration_query = xtc_db_query("SELECT *
                                                 FROM " . TABLE_CONFIGURATION . " 
                                                WHERE configuration_group_id = '" . (int)$_GET['gID'] . "'
                                                  AND sort_order >= 0
                                             ORDER BY sort_order, configuration_id");
          while ($configuration = xtc_db_fetch_array($configuration_query)) {
            $configuration['configuration_value'] = stripslashes($configuration['configuration_value']);
            
            if (!isset($_POST[$configuration['configuration_key']])) {
              $_POST[$configuration['configuration_key']] = '';
            }
            
            if (isset($_POST[$configuration['configuration_key']]) 
                && is_array($_POST[$configuration['configuration_key']])
                )
            {
              // multi language config
              $keys = array_keys($_POST[$configuration['configuration_key']]);
              if (gettype(array_shift($keys)) == 'string') {
                $config_value = array();
                foreach ($_POST[$configuration['configuration_key']] as $key => $value) {
                  if (xtc_not_null($value)) {
                    $config_value[] =  $key . '::' . $value;
                  }
                }
                $_POST[$configuration['configuration_key']] = implode('||', $config_value);
              } else {
                $_POST[$configuration['configuration_key']] = implode(',', $_POST[$configuration['configuration_key']]);
              }
            }
            
            if (isset($_POST[$configuration['configuration_key']]) 
                && $_POST[$configuration['configuration_key']] != $configuration['configuration_value']
                )
            {
              //value_limits min
              if (isset($value_limits[$configuration['configuration_key']]['min']) 
                  && preg_match("/^([0-9\.]+)$/", $_POST[$configuration['configuration_key']]) 
                  && (int)$_POST[$configuration['configuration_key']] < $value_limits[$configuration['configuration_key']]['min']
                  )
              {
                $_POST[$configuration['configuration_key']] = (int)$configuration['configuration_value'];
                $configuration_key_title = constant(strtoupper($configuration['configuration_key'].'_TITLE'));
                $messageStack->add_session(sprintf(CONFIG_MIN_VALUE_WARNING,$configuration_key_title,$_POST[$configuration['configuration_key']],$value_limits[$configuration['configuration_key']]['min'] ), 'warning');
              }
              
              //value_limits max
              if (isset($value_limits[$configuration['configuration_key']]['max']) 
                  && preg_match("/^([0-9\.]+)$/", $_POST[$configuration['configuration_key']]) 
                  && (int)$_POST[$configuration['configuration_key']] > $value_limits[$configuration['configuration_key']]['max']
                  )
              {
                $_POST[$configuration['configuration_key']] = (int)$configuration['configuration_value'];
                $configuration_key_title = constant(strtoupper($configuration['configuration_key'].'_TITLE'));
                $messageStack->add_session(sprintf(CONFIG_MAX_VALUE_WARNING,$configuration_key_title,$_POST[$configuration['configuration_key']],$value_limits[$configuration['configuration_key']]['max'] ), 'warning');
              }
              
              //check numeric input
              if (!preg_match("/^([0-9\.]+)$/", $_POST[$configuration['configuration_key']]) 
                  && (isset($value_limits[$configuration['configuration_key']]['min']) 
                      || isset($value_limits[$configuration['configuration_key']]['max'])
                      )
                  )
              {
                $_POST[$configuration['configuration_key']] = (int)$configuration['configuration_value'];
                $configuration_key_title = constant(strtoupper($configuration['configuration_key'].'_TITLE'));
                $messageStack->add_session(sprintf(CONFIG_INT_VALUE_ERROR,$configuration_key_title,$_POST[$configuration['configuration_key']],''), 'error');
              }
             
              xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " 
                               SET configuration_value = '" . xtc_db_input($_POST[$configuration['configuration_key']]) . "', 
                                   last_modified = NOW() 
                             WHERE configuration_key = '" . $configuration['configuration_key'] . "'");
             
              // load template config install/uninstall if exist
              if ($configuration['configuration_key'] == 'CURRENT_TEMPLATE') {
                if (is_file(DIR_FS_CATALOG.'templates/'.$configuration['configuration_value'].'/source/tmpl_config_uninstall.php')) {
                  include(DIR_FS_CATALOG.'templates/'.$configuration['configuration_value'].'/source/tmpl_config_uninstall.php');
                }
                if (is_file(DIR_FS_CATALOG.'templates/'.$_POST[$configuration['configuration_key']].'/source/tmpl_config_install.php')) {
                  include(DIR_FS_CATALOG.'templates/'.$_POST[$configuration['configuration_key']].'/source/tmpl_config_install.php');
                }
              }
            }
          }
        }
        xtc_redirect(xtc_href_link(FILENAME_CONFIGURATION, 'gID=' . (int)$_GET['gID']));
        break;

      case 'delcache':
        clear_dir(DIR_FS_CATALOG.'cache/');
        require_once(DIR_FS_CATALOG.'includes/modified_cache.php');
        $modified_cache->clear();
        $messageStack->add_session(DELETE_CACHE_SUCCESSFUL, 'success');
        xtc_redirect(xtc_href_link(FILENAME_CONFIGURATION, 'gID=' . (int)$_GET['gID']));
        break;

      case 'deltempcache':
        clear_dir(DIR_FS_CATALOG.'templates_c/');
        require_once(DIR_FS_CATALOG.'includes/modified_cache.php');
        $modified_cache->deleteByTag('template');
        file_put_contents(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/stylesheet.min.css', '');
        file_put_contents(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/css/tpl_plugins.min.css', '');        
        file_put_contents(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/javascript/tpl_plugins.min.js', '');
        $messageStack->add_session(DELETE_TEMP_CACHE_SUCCESSFUL, 'success');
        xtc_redirect(xtc_href_link(FILENAME_CONFIGURATION, 'gID=' . (int)$_GET['gID']));
        break;

      case 'dellog':
        clear_dir(DIR_FS_CATALOG.'log/', false, array('xss_blacklist.log'));
        $messageStack->add_session(DELETE_LOGS_SUCCESSFUL, 'success');
        xtc_redirect(xtc_href_link(FILENAME_CONFIGURATION, 'gID=' . (int)$_GET['gID']));
        break;
    }
  }

  $cfg_group_query = xtc_db_query("select configuration_group_title, configuration_group_id from " . TABLE_CONFIGURATION_GROUP . " where configuration_group_id = '" . (int)$_GET['gID'] . "'"); // Hetfield - 2010-01-15 - multilanguage title in configuration
  $cfg_group = xtc_db_fetch_array($cfg_group_query);
  
  if ((int)$_GET['gID'] == 11) {
    $messageStack->add(CACHE_LIFETIME_NOTE, 'error');
  }

  require (DIR_WS_INCLUDES.'head.php');
?>
  <script type="text/javascript" src="includes/general.js"></script>
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
          <div class="pageHeading pdg2 flt-l">
            <?php
              $box_conf_gid = 'BOX_CONFIGURATION_'.$cfg_group['configuration_group_id'];
              echo (defined($box_conf_gid) && constant($box_conf_gid) != '' ? constant($box_conf_gid) : $cfg_group['configuration_group_title']);
            ?>
            <div class="main pdg2">Configuration</div> 
          </div> 
                   
          <div class="main pdg2 flt-l" style="padding-left:30px;">
            <?php
              if ($_GET['gID'] == '11') { // delete cache files in admin section
                echo xtc_draw_form('configuration', FILENAME_CONFIGURATION, 'gID=' . (int)$_GET['gID'] . '&action=delcache');
                echo '<input type="submit" class="button" onclick="this.blur();" value="' . BUTTON_DELETE_CACHE . '"/></form> ';
                echo xtc_draw_form('configuration', FILENAME_CONFIGURATION, 'gID=' . (int)$_GET['gID'] . '&action=deltempcache');
                echo '<input type="submit" class="button" onclick="this.blur();" value="' . BUTTON_DELETE_TEMP_CACHE . '"/></form>';
              }
              if ($_GET['gID'] == '10') {
                echo xtc_draw_form('configuration', FILENAME_CONFIGURATION, 'gID=' . (int)$_GET['gID'] . '&action=dellog');
                echo '<input type="submit" class="button" onclick="this.blur();" value="' . BUTTON_DELETE_LOGS . '"/></form>';
              }
            ?>
          </div>
          <div class="clear"></div> 
       
          <?php
            $tabs = false;
            switch ($_GET['gID']) {
              case '21': //Afterbuy                 
              case '19': // Google Conversion-Tracking
              case '31': // moneybookers payment module version 2.4        
                echo '<div class="configPartner cf">
                        <a class="configtab'.(($_GET['gID'] == '21') ? ' activ' : '').'" href="'.xtc_href_link(FILENAME_CONFIGURATION, 'gID=21', 'NONSSL').'">Afterbuy</a>
                        <a class="configtab'.(($_GET['gID'] == '19') ? ' activ' : '').'" href="'.xtc_href_link(FILENAME_CONFIGURATION, 'gID=19', 'NONSSL').'">Google Conversion</a>
                        <a class="configtab'.(($_GET['gID'] == '31') ? ' activ' : '').'" href="'.xtc_href_link(FILENAME_CONFIGURATION, 'gID=31', 'NONSSL').'">Skrill.com</a>
                      </div>';

                $tabs = true;
                echo '<div class="configPartner content">';

                if ($_GET['gID'] == '21') {
                  echo '<div class="clear bg_notice pdg2">'.AFTERBUY_URL.'</div>';
                }
                if ($_GET['gID'] == '31') {
                  echo '<div class="clear div_box pdg2" style="max-width:100%">'. MB_INFO.'</div>';
                }
                break;
            }
            
            echo xtc_draw_form('configuration', FILENAME_CONFIGURATION, 'gID=' . (int)$_GET['gID'] . '&action=save');
            ?>
              <table class="clear tableConfig">
                <?php
                  $configuration_query = xtc_db_query("SELECT configuration_key,
                                                              configuration_id, 
                                                              configuration_value, 
                                                              use_function,
                                                              set_function 
                                                         FROM " . TABLE_CONFIGURATION . " 
                                                        WHERE configuration_group_id = '" . (int)$_GET['gID'] . "'
                                                          AND sort_order >= 0
                                                     ORDER BY sort_order, configuration_id");
                  while ($configuration = xtc_db_fetch_array($configuration_query)) {
                    $configuration['configuration_value'] = stripslashes($configuration['configuration_value']);
                    
                    if ($_GET['gID'] == 6) {
                      switch ($configuration['configuration_key']) {
                        case 'MODULE_PAYMENT_INSTALLED':
                          if ($configuration['configuration_value'] != '') {
                            $payment_installed = explode(';', $configuration['configuration_value']);
                            for ($i = 0, $n = sizeof($payment_installed); $i < $n; $i++) {
                              include(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/payment/' . $payment_installed[$i]); 
                            }
                          }
                          break;
                        case 'MODULE_SHIPPING_INSTALLED':
                          if ($configuration['configuration_value'] != '') {
                            $shipping_installed = explode(';', $configuration['configuration_value']);
                            for ($i = 0, $n = sizeof($shipping_installed); $i < $n; $i++) {
                              include(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/shipping/' . $shipping_installed[$i]); 
                            }
                          }
                          break;
                        case 'MODULE_ORDER_TOTAL_INSTALLED':
                          if ($configuration['configuration_value'] != '') {
                            $ot_installed = explode(';', $configuration['configuration_value']);
                            for ($i = 0, $n = sizeof($ot_installed); $i < $n; $i++) {
                              include(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/order_total/' . $ot_installed[$i]); 
                            }
                          }
                          break;
                      }
                    }
                    
                    if (xtc_not_null($configuration['use_function'])) {
                      $use_function = $configuration['use_function'];
                      if (preg_match('/->/', $use_function)) { 
                        $class_method = explode('->', $use_function);
                        if (!is_object(${$class_method[0]})) {
                          include(DIR_WS_CLASSES . $class_method[0] . '.php');
                          ${$class_method[0]} = new $class_method[0]();
                        }
                        $cfgValue = xtc_call_function($class_method[1], $configuration['configuration_value'], ${$class_method[0]});
                      } else {
                        $cfgValue = xtc_call_function($use_function, $configuration['configuration_value']);
                      }
                    } else {
                      $cfgValue = encode_htmlspecialchars($configuration['configuration_value']);
                    }
                    
                    if ($_GET['gID'] != '6') {
                      if ($configuration['set_function']) {
                        if (strpos($configuration['set_function'], '(') !== false) {
                          eval('$value_field = ' . $configuration['set_function'] . ' "' . encode_htmlspecialchars($configuration['configuration_value'], ENT_QUOTES) . '");');
                        } else {
                          $parameters = explode(';', $configuration['set_function']);
                          $function = trim($parameters[0]);
                          $parameters[0] = $configuration['configuration_value'];
                          $value_field = xtc_call_function($function, $parameters);
                        }
                      } else {
                        $value_field = xtc_draw_input_field($configuration['configuration_key'], $configuration['configuration_value'], 'style="width:100%;"');
                      }
                    } else {
                      $value_field = '<div class="wordwrap">'.$cfgValue.'</div>';
                    }
                    
                    if (strpos($value_field, 'cfg_so_k') !== false) {
                      $value_field = str_replace('cfg_so_k', strtolower($configuration['configuration_key']), $value_field);
                    }
                    
                    if (strpos($value_field, 'configuration_value') !== false) {
                      $value_field = str_replace('configuration_value', $configuration['configuration_key'], $value_field);
                    }

                    // catch up warnings if no language-text defined for configuration-key
                    $configuration_key_title = strtoupper($configuration['configuration_key'].'_TITLE');
                    $configuration_key_desc  = strtoupper($configuration['configuration_key'].'_DESC');
                    if (defined($configuration_key_title) ) {                                         // if language definition
                      $configuration_key_title = constant($configuration_key_title);
                      $configuration_key_desc  = constant($configuration_key_desc);
                    } else {                                                                          // if no language
                      $configuration_key_title = $configuration['configuration_key'];                 // name = key
                      $configuration_key_desc  = '&nbsp;';                                            // description = empty
                    }
                    if ($configuration_key_desc!=str_replace("<meta ","",$configuration_key_desc)) {
                      $configuration_key_desc = encode_htmlentities($configuration_key_desc);
                    }
                    $class_mark = strpos(strtoupper($configuration['configuration_key']), 'SMTP') !== false || 
                                  strpos(strtoupper($configuration['configuration_key']), 'CONTACT_US') !== false || 
                                  strpos(strtoupper($configuration['configuration_key']), 'EMAIL_BILLING') !== false ||
                                  strpos(strtoupper($configuration['configuration_key']), 'PRODUCT_IMAGE_') !== false ||
                                  strpos(strtoupper($configuration['configuration_key']), 'MANUFACTURER_IMAGE_') !== false  
                                  ? ' mark' 
                                  : '';
                    echo '<tr>
                            <td class="dataTableConfig col-left">'.$configuration_key_title.'</td>
                            <td class="dataTableConfig col-middle'.$class_mark.'">'.$value_field.'</td>
                            <td class="dataTableConfig col-right">'.$configuration_key_desc.'</td>
                          </tr>
                          ';
                  }
                ?>
              </table>
              <?php if ($_GET['gID'] != '6') { ?>
              <div class="main pdg2 txta-r mrg5"><input type="submit" class="button" onclick="this.blur();" value="<?php echo BUTTON_SAVE; ?>"/></div>
              <?php } ?>
            </form>
          <?php echo (($tabs === true) ? '</div>' : ''); ?>
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