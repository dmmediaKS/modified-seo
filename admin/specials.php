<?php
  /* --------------------------------------------------------------
   $Id: specials.php 16418 2025-04-28 09:01:15Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(specials.php,v 1.38 2002/05/16); www.oscommerce.com
   (c) 2003 nextcommerce (specials.php,v 1.9 2003/08/18); www.nextcommerce.org
   (c) 2006 XT-Commerce (specials.php 1125 2005-07-28)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  require('includes/application_top.php');
  
  // include needed functions
  require_once (DIR_FS_INC.'xtc_get_tax_rate.inc.php');

  // include needed classes
  require_once (DIR_WS_CLASSES.'categories.php');
  require_once (DIR_FS_CATALOG.DIR_WS_CLASSES . 'xtcPrice.php');

  $xtPrice = new xtcPrice(DEFAULT_CURRENCY,$_SESSION['customers_status']['customers_status_id']);
  $catfunc = new categories();
  
  //display per page
  $cfg_max_display_results_key = 'MAX_DISPLAY_SPECIALS_RESULTS';
  $page_max_display_results = xtc_cfg_save_max_display_results($cfg_max_display_results_key);

  $sID = (isset($_GET['sID']) ? (int)$_GET['sID'] : NULL);
  $page = (isset($_GET['page']) ? (int)$_GET['page'] : 1);
  $action = (isset($_GET['action']) ? $_GET['action'] : '');


  if (xtc_not_null($action)) {
    switch ($action) {
      case 'setflag':
        xtc_set_specials_status($_GET['id'], $_GET['flag']);
        xtc_redirect(xtc_href_link(FILENAME_SPECIALS, xtc_get_all_get_params(array('action', 'flag', 'id'))));
        break;

      case 'insert':
      case 'update':
        $specials_id = $catfunc->saveSpecialsData($_POST);
        if ((int)$specials_id > 0) {
          xtc_redirect(xtc_href_link(FILENAME_SPECIALS, xtc_get_all_get_params(array('action', 'sID')) . '&sID=' . (int)$specials_id));
        } else {
        
        }
        break;

      case 'deleteconfirm':
        xtc_db_query("DELETE FROM " . TABLE_SPECIALS . " WHERE specials_id = '" . xtc_db_prepare_input($sID) . "'");
        xtc_redirect(xtc_href_link(FILENAME_SPECIALS, xtc_get_all_get_params(array('action', 'sID'))));
        break;
    
      case 'multi_action':
        if (isset ($_POST['multi_status_off']) || isset ($_POST['multi_status_on'])) {
          if (is_array($_POST['multi_products'])) {
            foreach ($_POST['multi_products'] as $product_id) {
              xtc_set_specials_status($product_id, isset($_POST['multi_status_off']) ? 0 : 1);
            }
          }          
          xtc_redirect(xtc_href_link(FILENAME_SPECIALS, xtc_get_all_get_params(array('action', 'sID'))));
        }
        break;
      
      case 'multi_action_confirm':
        if (is_array($_POST['multi_products_confirm'])) {
          foreach ($_POST['multi_products_confirm'] as $product_id) {
            xtc_db_query("DELETE FROM " . TABLE_SPECIALS . " WHERE specials_id = '" . (int)$product_id . "'");
          }
        }        
        xtc_redirect(xtc_href_link(FILENAME_SPECIALS, xtc_get_all_get_params(array('action', 'sID'))));
        break;
    }
  }

  $sorting = (isset($_GET['sorting']) ? $_GET['sorting'] : '');
  if (xtc_not_null($sorting)) {
    switch ($sorting) {
      case 'name':
        $spsort = 'pd.products_name ASC';
        break;
      case 'name-desc':
        $spsort = 'pd.products_name DESC';
        break;
      case 'model':
        $spsort = 'p.products_model ASC';
        break;
      case 'model-desc':
        $spsort = 'p.products_model DESC';
        break;
      case 'quantity':
        $spsort = 'p.products_quantity ASC';
        break;
      case 'quantity-desc':
        $spsort = 'p.products_quantity DESC';
        break;
      case 'specials-quantity':
        $spsort = 's.specials_quantity ASC';
        break;
      case 'specials-quantity-desc':
        $spsort = 's.specials_quantity DESC';
        break;
      case 'start-date':
        $spsort = 's.start_date ASC';
        break;
      case 'start-date-desc':
        $spsort = 's.start_date DESC';
        break;
      case 'expires-date':
        $spsort = 's.expires_date ASC';
        break;
      case 'expires-date-desc':
        $spsort = 's.expires_date DESC';
        break;
      case 'price':
        $spsort = 'p.products_price ASC';
        break;
      case 'price-desc':
        $spsort = 'p.products_price DESC';
        break;
      case 'status':
        $spsort = 's.status ASC';
        break;
      case 'status-desc':
        $spsort = 's.status DESC';
        break;
      default:
        $spsort = 'pd.products_name ASC';
        break;
    }
  } else {
    $spsort = 'pd.products_name ASC';
  }

  require (DIR_WS_INCLUDES.'head.php');
?>
  <script type="text/javascript" src="includes/general.js"></script>
  <script type="text/javascript" src="includes/javascript/categories.js"></script>
  <?php 
  if ( ($action == 'new') || ($action == 'edit') ) {
    //jQueryDatepicker
    require (DIR_WS_INCLUDES.'javascript/jQueryDateTimePicker/datepicker.js.php');  
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
        <div class="pageHeadingImage"><?php echo xtc_image(DIR_WS_ICONS.'resources.png'); ?></div>
        <div class="flt-l">
          <div class="pageHeading pdg2"><?php echo HEADING_TITLE; ?></div>
          <div class="main pdg2">Products</div>
        </div>
        <?php if (empty($action)) { ?>
        <div class="main flt-l pdg2 mrg5" style="margin-left:20px;">
          <a class="button" onclick="this.blur();" href="<?php echo xtc_href_link(FILENAME_SPECIALS, xtc_get_all_get_params(array('action', 'sID')) . '&action=new'); ?>"><?php echo  BUTTON_NEW_PRODUCTS; ?></a>
        </div>
        <?php } ?>
        <?php
        if ($action == 'new' || $action == 'edit') {

          $price = 0;
          $price_netto = '';
          $new_price = 0;
          $new_price_netto = '';
          $specials_quantity = '';
          $form_action = 'insert';
          
          if ($action == 'edit' && isset($sID)) {
            $specials_query = xtc_db_query("SELECT p.products_id,
                                                   p.products_model,
                                                   p.products_price,
                                                   p.products_tax_class_id,
                                                   pd.products_name,
                                                   s.*
                                              FROM " . TABLE_PRODUCTS . " p
                                              JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd
                                                   ON p.products_id = pd.products_id
                                                      AND pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                                              JOIN " . TABLE_SPECIALS . " s
                                                   ON p.products_id = s.products_id
                                                      AND s.specials_id = '" . $sID ."'");
            if (xtc_db_num_rows($specials_query) > 0) {
              $form_action = 'update';
              $special = xtc_db_fetch_array($specials_query);
              $sInfo = new objectInfo($special);

              $specials_quantity = $sInfo->specials_quantity;

              $price = (($sInfo->specials_old_products_price > 0) ? $sInfo->specials_old_products_price : $sInfo->products_price);
              $new_price = $sInfo->specials_new_products_price;
              if (PRICE_IS_BRUTTO == 'true') {
                if ($price > 0) {
                  $price_netto = TEXT_NETTO.'<strong>'.xtc_round($price, PRICE_PRECISION).'</strong>';
                }
                if ($new_price > 0) {
                  $new_price_netto = TEXT_NETTO.'<strong>'.xtc_round($new_price, PRICE_PRECISION).'</strong>';
                }
                $price = ($price * (xtc_get_tax_rate($sInfo->products_tax_class_id) + 100) / 100);
                $new_price = ($new_price * (xtc_get_tax_rate($sInfo->products_tax_class_id) + 100) / 100);
              }
            }
          } 
          
          $price = xtc_round($price, PRICE_PRECISION);
          $new_price = xtc_round($new_price ,PRICE_PRECISION);           

          if ($form_action == 'insert') {
            $product_array = xtc_get_default_table_data(TABLE_SPECIALS);
            $sInfo = new objectInfo($product_array);
            $sInfo->products_price = 0;
            // create an array of products on special, which will be excluded from the pull down menu of products
            // (when creating a new product on special)
            $specials_array = array();
            $specials_query = xtc_db_query("SELECT p.products_id
                                              FROM " . TABLE_PRODUCTS . " p
                                              JOIN " . TABLE_SPECIALS . " s
                                                   ON s.products_id = p.products_id");
            while ($specials = xtc_db_fetch_array($specials_query)) {
              $specials_array[] = $specials['products_id'];
            }
          }

          // build the expires date in the format YYYY-MM-DD
          if (strtotime($sInfo->expires_date) !== false && strtotime($sInfo->expires_date) > 0) {
            $expires_date = date('Y-m-d H:i', strtotime($sInfo->expires_date));
          } else {
            $expires_date = '';
          }

          // build the start date in the format YYYY-MM-DD
          if (strtotime($sInfo->start_date) !== false && strtotime($sInfo->start_date) > 0) {
            $start_date = date('Y-m-d H:i', strtotime($sInfo->start_date));
          } else {
            $start_date = '';
          }

          echo xtc_draw_form('new_special', FILENAME_SPECIALS, xtc_get_all_get_params(array('action', 'info', 'sID')) . 'action=' . $form_action);
          if ($form_action == 'update') { 
            echo xtc_draw_hidden_field('specials_id', $sID);                
            echo xtc_draw_hidden_field('products_tax_class_id', $sInfo->products_tax_class_id);
          }
          echo xtc_draw_hidden_field('specials_action', $form_action);
          echo xtc_draw_hidden_field('products_price_hidden', $sInfo->products_price);
          ?>
          
          <table class="tableConfig">
            <tr>
              <td class="dataTableConfig col-left"><?php echo TEXT_SPECIALS_PRODUCT; ?></td>
              <td class="dataTableConfig col-middle"><?php echo ((isset($sInfo->products_name)) ? $sInfo->products_name . '<br/><small>(' . $xtPrice->xtcFormat($price,true). ') ' . $price_netto .'</small>'.xtc_draw_hidden_field('products_id', $sInfo->products_id) : xtc_draw_products_pull_down('products_id', 'style="font-size:10px"', $specials_array)); ?></td>
              <td class="dataTableConfig col-right">&nbsp;</td>
            </tr>
            <?php
            if ($form_action == 'update') {
            ?>
            <tr>
              <td class="dataTableConfig col-left"><?php echo TEXT_GLOBAL_PRODUCTS_MODEL; ?>:</td>
              <td class="dataTableConfig col-middle"><?php echo $sInfo->products_model;?></td>
              <td class="dataTableConfig col-right">&nbsp;</td>
            </tr>
            <?php
            }
            ?>
            <tr>
              <td class="dataTableConfig col-left"><?php echo TEXT_SPECIALS_SPECIAL_PRICE; ?></td>
              <td class="dataTableConfig col-middle"><?php echo xtc_draw_input_field('specials_price', $new_price).'<br/>' .$new_price_netto;?></td>
              <td class="dataTableConfig col-right"><?php echo TEXT_SPECIALS_PRICE_TIP; ?></td>
            </tr>
            <tr>
              <td class="dataTableConfig col-left"><?php echo TEXT_SPECIALS_SPECIAL_PRODUCTS_PRICE; ?></td>
              <td class="dataTableConfig col-middle"><?php echo xtc_draw_input_field('specials_old_products_price', $price).'<br/>' .$price_netto;?></td>
              <td class="dataTableConfig col-right"><?php echo TEXT_SPECIALS_PRODUCTS_PRICE_TIP; ?></td>
            </tr>
            <tr>
              <td class="dataTableConfig col-left"><?php echo TEXT_SPECIALS_SPECIAL_QUANTITY; ?></td>
              <td class="dataTableConfig col-middle"><?php echo xtc_draw_input_field('specials_quantity', $specials_quantity);?> </td>
              <td class="dataTableConfig col-right"><?php echo TEXT_SPECIALS_QUANTITY_TIP; ?></td>
            </tr>
            <tr>
              <td class="dataTableConfig col-left"><?php echo TEXT_SPECIALS_START_DATE; ?></td>
              <td class="dataTableConfig col-middle"><?php echo xtc_draw_input_field('specials_start', $start_date ,'id="Datetimepicker1"'); ?></td>
              <td class="dataTableConfig col-right"><?php echo TEXT_SPECIALS_START_DATE_TIP.SPECIALS_DATE_START_TT; ?>&nbsp;</td>
            </tr>
            <tr>
              <td class="dataTableConfig col-left"><?php echo TEXT_SPECIALS_EXPIRES_DATE; ?></td>
              <td class="dataTableConfig col-middle"><?php echo xtc_draw_input_field('specials_expires', $expires_date ,'id="Datetimepicker2"'); ?></td>
              <td class="dataTableConfig col-right"><?php echo TEXT_SPECIALS_EXPIRES_DATE_TIP.SPECIALS_DATE_END_TT; ?>&nbsp;</td>
            </tr>
          </table>

          <div class="main mrg5 nobr">
            <?php
            if ($form_action == 'insert') {
              echo '<input type="submit" class="button" onclick="this.blur();" value="' . BUTTON_INSERT . '"/>';
            } else {
              echo '<input type="submit" class="button" onclick="this.blur();" value="' . BUTTON_UPDATE . '"/> <a class="button" onclick="this.blur();" href="' . xtc_href_link(FILENAME_SPECIALS, xtc_get_all_get_params(array('action'))) . '">' . BUTTON_CANCEL . '</a>'; 
            }
            ?>
          </div>
        </form>
      </td>                   
        <?php
        } else {
          ?>              
          <table class="tableCenter">
            <tr>
              <?php 
              if ($action == '' || strpos($action, 'multi') !== false) {
                echo xtc_draw_form('multi_action_form', FILENAME_SPECIALS, xtc_get_all_get_params(array('action')) . ((isset($_POST['multi_products']) && xtc_not_null($_POST['multi_products'])) ? 'action=multi_action_confirm' : 'action=multi_action'), 'post', 'onsubmit="javascript:return CheckMultiForm()"');
              }
              ?>
              <td class="boxCenterLeft">
                <table class="tableBoxCenter collapse">
                  <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent txta-c" style="width:4%">
                      <?php 
                        echo TABLE_HEADING_EDIT . '<br />'; 
                        echo xtc_draw_checkbox_field('select_all', '1', false, '', 'onclick="javascript:CheckAll(this.checked);"');   
                      ?>
                    </td>
                    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS.xtc_sorting(FILENAME_SPECIALS, 'name'); ?></td>
                    <td class="dataTableHeadingContent"><?php echo TEXT_GLOBAL_PRODUCTS_MODEL.xtc_sorting(FILENAME_SPECIALS, 'model'); ?></td>
                    <td class="dataTableHeadingContent txta-c"><?php echo TABLE_HEADING_PRODUCTS_QUANTITY.xtc_sorting(FILENAME_SPECIALS, 'quantity'); ?></td>
                    <td class="dataTableHeadingContent txta-c"><?php echo TABLE_HEADING_SPECIALS_QUANTITY.xtc_sorting(FILENAME_SPECIALS, 'specials-quantity'); ?></td>
                    <td class="dataTableHeadingContent txta-r"><?php echo TABLE_HEADING_START_DATE.xtc_sorting(FILENAME_SPECIALS, 'start-date'); ?></td>
                    <td class="dataTableHeadingContent txta-r"><?php echo TABLE_HEADING_EXPIRES_DATE.xtc_sorting(FILENAME_SPECIALS, 'expires-date'); ?></td>
                    <td class="dataTableHeadingContent txta-r"><?php echo TABLE_HEADING_PRODUCTS_PRICE.xtc_sorting(FILENAME_SPECIALS, 'price'); ?></td>
                    <td class="dataTableHeadingContent txta-c"><?php echo TABLE_HEADING_STATUS.xtc_sorting(FILENAME_SPECIALS, 'status'); ?></td>
                    <td class="dataTableHeadingContent txta-r"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
                  </tr>
                  <?php
                  $specials_query_raw = "SELECT p.products_id,
                                                p.products_model,
                                                p.products_quantity,
                                                p.products_price,
                                                p.products_tax_class_id,
                                                p.products_image,
                                                pd.products_name,
                                                s.*
                                           FROM " . TABLE_PRODUCTS . " p
                                           JOIN " . TABLE_SPECIALS . " s
                                                ON p.products_id = s.products_id
                                           JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd
                                                ON p.products_id = pd.products_id
                                                   AND pd.language_id = '" .(int) $_SESSION['languages_id'] . "'
                                       ORDER BY ".$spsort;
                                   
                  $specials_split = new splitPageResults($page, $page_max_display_results, $specials_query_raw, $specials_query_numrows, 's.specials_id', 'sID');
                  $specials_query = xtc_db_query($specials_query_raw);
                  while ($specials = xtc_db_fetch_array($specials_query)) {
                    
                    $price = (($specials['specials_old_products_price'] > 0) ? $specials['specials_old_products_price'] : $specials['products_price']);
                    $new_price = $specials['specials_new_products_price'];
                    if (PRICE_IS_BRUTTO=='true') {
                      $price = ($price * (xtc_get_tax_rate($specials['products_tax_class_id']) + 100) / 100);
                      $new_price = ($new_price * (xtc_get_tax_rate($specials['products_tax_class_id']) + 100) / 100);
                    }
                    $specials['products_price'] = xtc_round($price, PRICE_PRECISION);
                    $specials['specials_new_products_price'] = xtc_round($new_price, PRICE_PRECISION);
                    
                    if ((!isset($sID) || (isset($sID) && ($sID == $specials['specials_id']))) && !isset($sInfo) ) {
                      $sInfo = new objectInfo($specials);
                    }
                    
                    if (isset($sInfo) && is_object($sInfo) && ($specials['specials_id'] == $sInfo->specials_id) ) {
                      $tr_attributes = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'pointer\'" data-event="' . xtc_href_link(FILENAME_SPECIALS, xtc_get_all_get_params(array('action', 'sID')) . 'sID=' . $sInfo->specials_id . '&action=edit') . '"';
                    } else {
                      $tr_attributes = 'class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'pointer\'" onmouseout="this.className=\'dataTableRow\'" data-event="' . xtc_href_link(FILENAME_SPECIALS, xtc_get_all_get_params(array('action', 'sID')) . 'sID=' . $specials['specials_id']) . '"';
                    }
                    ?>
                    <tr <?php echo $tr_attributes;?>>
                      <?php
                      $is_checked = false;
                      if (isset($_POST['multi_products']) && is_array($_POST['multi_products'])) {
                        if (in_array($specials['specials_id'], $_POST['multi_products'])) {
                          $is_checked = true;
                        }
                      }
                      ?>
                      <td class="dataTableContent txta-c">
                       <?php echo xtc_draw_checkbox_field('multi_products[]', $specials['specials_id'], $is_checked); ?>
                      </td>
                      <td  class="dataTableContent"><?php echo $specials['products_name']; ?></td>
                      <td  class="dataTableContent"><?php echo $specials['products_model']; ?></td>
                      <td  class="dataTableContent txta-c"><?php echo $specials['products_quantity']; ?></td>
                      <td  class="dataTableContent txta-c"><?php echo $specials['specials_quantity']; ?></td>
                      <td  class="dataTableContent txta-r"><?php echo (isset($specials['start_date']) ? xtc_datetime_short($specials['start_date']): '&nbsp;'); ?></td>
                      <td  class="dataTableContent txta-r"><?php echo (isset($specials['expires_date']) ? xtc_datetime_short($specials['expires_date']): '&nbsp;'); ?></td>
                      <td  class="dataTableContent txta-r">
                        <span class="oldPrice">
                          <?php echo $xtPrice->xtcFormat($specials['products_price'],true); ?>
                        </span>
                        &nbsp;
                        <span class="specialPrice">
                          <?php echo $xtPrice->xtcFormat($specials['specials_new_products_price'],true); ?>
                        </span>
                      </td>
                      <td  class="dataTableContent txta-c">
                        <?php
                        if ($specials['status'] == '1') {
                          echo xtc_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 12, 12, 'style="margin-right:5px;"') . '<a href="' . xtc_href_link(FILENAME_SPECIALS, xtc_get_all_get_params(array('action', 'sID', 'flag', 'id')).'action=setflag&flag=0&id=' . $specials['specials_id'], 'NONSSL') . '">' . xtc_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 12, 12) . '</a>';
                        } else {
                          echo '<a href="' . xtc_href_link(FILENAME_SPECIALS, xtc_get_all_get_params(array('action', 'sID', 'flag', 'id')).'action=setflag&flag=1&id=' . $specials['specials_id'], 'NONSSL') . '">' . xtc_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 12, 12, 'style="margin-right:5px;"') . '</a>' . xtc_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 12, 12);
                        }
                        ?>
                        </td>
                        <td class="dataTableContent txta-r"><?php if (isset($sInfo) && (is_object($sInfo)) && ($specials['specials_id'] == $sInfo->specials_id) ) { echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ICON_ARROW_RIGHT); } else { echo '<a href="' . xtc_href_link(FILENAME_SPECIALS, xtc_get_all_get_params(array('action', 'sID')) . 'sID=' . $specials['specials_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_arrow_grey.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
                      </tr>
                    <?php
                  }
                  ?>
                </table>
              </td>
              <?php
              $heading = array();
              $contents = array();
              switch ($action) {
                case 'multi_action':
                  if (xtc_not_null($_POST['multi_delete'])) {
                    $heading[]  = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_ELEMENTS . '</b>');
                    if (is_array($_POST['multi_products'])) {
                      foreach ($_POST['multi_products'] AS $specials_id) {
                        $product_query = xtc_db_query("SELECT pd.products_name
                                                         FROM ".TABLE_SPECIALS." s
                                                         JOIN ".TABLE_PRODUCTS_DESCRIPTION." pd
                                                              ON pd.products_id = s.products_id
                                                                 AND pd.language_id = '".(int)$_SESSION['languages_id']."'
                                                        WHERE s.specials_id = '".(int)$specials_id."'");
                        $product = xtc_db_fetch_array($product_query);
                        $contents[] = array('text' => xtc_draw_checkbox_field('multi_products_confirm[]', $specials_id, true) . '&nbsp;' . $product['products_name']);
                      }
                    }
                    $contents[] = array('align' => 'center', 'text' => '<input class="button" type="submit" name="multi_delete_confirm" value="' . BUTTON_DELETE . '"> <a class="button" href="' . xtc_href_link(FILENAME_SPECIALS, xtc_get_all_get_params(array('action', 'sID'))) . '">' . BUTTON_CANCEL . '</a>');
                  }
                  break;
              
                case 'delete':
                  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_SPECIALS . '</b>');
                  $contents = array('form' => xtc_draw_form('specials', FILENAME_SPECIALS, xtc_get_all_get_params(array('action', 'sID')) . '&sID=' . $sInfo->specials_id . '&action=deleteconfirm'));
                  $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
                  $contents[] = array('text' => '<br /><b>' . $sInfo->products_name . '</b>');
                  $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onclick="this.blur();" value="' . BUTTON_DELETE . '"/>&nbsp;<a class="button" onclick="this.blur();" href="' . xtc_href_link(FILENAME_SPECIALS, xtc_get_all_get_params(array('action', 'sID')).'sID=' . $sInfo->specials_id) . '">' . BUTTON_CANCEL . '</a>');
                  break;
                  
                default:
                  if (isset($sInfo) && is_object($sInfo)) {
                    $heading[] = array('text' => '<b>' . $sInfo->products_name . '</b>');

                    //Multi Element Actions
                    $contents[] = array('align' => 'center', 'text' => '<div style="padding-top: 5px; font-weight: bold; width: 100%;">' . TEXT_MARKED_ELEMENTS . '</div>');
                    $contents[] = array('align' => 'center', 'text' => '<input type="submit" class="button" name="multi_status_on" value="'. BUTTON_STATUS_ON . '"> <input type="submit" class="button" name="multi_status_off" value="' . BUTTON_STATUS_OFF . '"> <input type="submit" class="button" name="multi_delete" value="' . BUTTON_DELETE . '">');

                    $contents[] = array('align' => 'center', 'text' => '<div style="padding-top: 5px; font-weight: bold; width: 100%; border-top: 1px solid #aaa; margin-top: 5px;">' . TEXT_ACTIVE_ELEMENT . '</div>');
                    $contents[] = array('align' => 'center', 'text' => '<a class="button" onclick="this.blur();" href="' . xtc_href_link(FILENAME_SPECIALS, xtc_get_all_get_params(array('action', 'sID')) . 'sID=' . $sInfo->specials_id . '&action=edit') . '">' . BUTTON_EDIT . '</a> <a class="button" onclick="this.blur();" href="' . xtc_href_link(FILENAME_SPECIALS, xtc_get_all_get_params(array('action', 'sID')) . 'sID=' . $sInfo->specials_id . '&action=delete') . '">' . BUTTON_DELETE . '</a>');
                    $contents[] = array('text' => '<br />' . TEXT_INFO_DATE_ADDED . ' ' . xtc_date_short($sInfo->specials_date_added));
                    $contents[] = array('text' => '' . TEXT_INFO_LAST_MODIFIED . ' ' . xtc_date_short($sInfo->specials_last_modified));
                    $contents[] = array('align' => 'center', 'text' => '<br />' . xtc_product_thumb_image($sInfo->products_image, $sInfo->products_name));
                    $contents[] = array('text' => '<br />' . TEXT_INFO_ORIGINAL_PRICE . ' ' . $xtPrice->xtcFormat($sInfo->products_price,true));
                    $contents[] = array('text' => '' . TEXT_INFO_NEW_PRICE . ' ' . $xtPrice->xtcFormat($sInfo->specials_new_products_price,true));
                    $contents[] = array('text' => '' . TEXT_INFO_PERCENTAGE . ' ' . (($sInfo->products_price > 0) ? number_format(100 - (($sInfo->specials_new_products_price / $sInfo->products_price) * 100)) : '0') . '%');
                    $contents[] = array('text' => '<br />' . TEXT_INFO_START_DATE . ' <b>' . xtc_datetime_short($sInfo->start_date) . '</b>');
                    $contents[] = array('text' => TEXT_INFO_EXPIRES_DATE . ' <b>' . xtc_datetime_short($sInfo->expires_date) . '</b>');
                    $contents[] = array('text' => TEXT_INFO_STATUS_CHANGE . ' ' . xtc_date_short($sInfo->date_status_change));
                  }
                  break;
              }
              if ( (xtc_not_null($heading)) && (xtc_not_null($contents)) ) {
                echo '            <td class="boxRight">' . "\n";
                $box = new box;
                echo $box->infoBox($heading, $contents);
                echo '            </td>' . "\n";
              }
            }
            // END LISTING TABLE
            if ($action == '' || strpos($action, 'multi') !== false) {
              echo '</form>';
            }
            ?>
          </tr>
          <?php if (isset($specials_split) && is_object($specials_split)) { ?>
          <tr>
            <td>
              <!-- PAGINATION-->
              <div class="smallText flt-l pdg2"><?php echo $specials_split->display_count($specials_query_numrows, $page_max_display_results, $page, TEXT_DISPLAY_NUMBER_OF_SPECIALS); ?></div>
              <div class="smallText flt-r pdg2"><?php echo $specials_split->display_links($specials_query_numrows, $page_max_display_results, MAX_DISPLAY_PAGE_LINKS, $page, xtc_get_all_get_params(array('page', 'sID'))); ?></div>
              <?php echo draw_input_per_page($PHP_SELF,$cfg_max_display_results_key,$page_max_display_results); ?>
            </td>
            <td>&nbsp;</td>
          </tr>
          <?php } ?>
        </table>
      </td>
      <!-- body_text_eof //-->
    </tr>
  </table>

  <script>
    var action = false;
    $('.dataTableRow, .dataTableRowSelected, .dataTableRow a, .dataTableRowSelected a, .dataTableRow .ChkBox, .dataTableRowSelected .ChkBox').on('change, click', function (e) {          
      if (this.nodeName == 'A' || this.nodeName == 'INPUT') {
        action = true;
      }
      if (action === false && this.nodeName == 'TR') {
        var loc = $(this).data('event');
        if (loc !== undefined) {
          window.location.href = loc;
        }
      }
      if (this.nodeName == 'TR') {
        action = false;
      }
    });
  </script>

  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
  <br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>