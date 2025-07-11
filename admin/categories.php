<?php
  /* --------------------------------------------------------------
   $Id: categories.php 16327 2025-02-19 13:41:24Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(categories.php,v 1.26 2003/05/17); www.oscommerce.com
   (c) 2003 nextcommerce (categories.php,v 1.9 2003/08/18); www.nextcommerce.org
   (c) 2006 XT-Commerce (categories.php 1123 2005-07-27)
   --------------------------------------------------------------
   Third Party contribution:
   Enable_Disable_Categories 1.3               Autor: Mikel Williams | mikel@ladykatcostumes.com
   New Attribute Manager v4b                   Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com
   Category Descriptions (Version: 1.5 MS2)    Original Author:   Brian Lowe <blowe@wpcusrgrp.org> | Editor: Lord Illicious <shaolin-venoms@illicious.net>
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require_once ('includes/application_top.php');

// include needed function
require_once (DIR_FS_INC.'xtc_get_tax_rate.inc.php');
require_once (DIR_FS_INC.'xtc_get_products_mo_images.inc.php');
require_once (DIR_FS_INC.'xtc_wysiwyg.inc.php');
require_once (DIR_FS_INC.'xtc_get_order_description.inc.php');
require_once (DIR_FS_INC.'xtc_parse_category_path.inc.php');
require_once (DIR_FS_INC.'parse_multi_language_value.inc.php');
require_once (DIR_FS_INC.'xtc_get_manufacturers.inc.php');

// include needed classes
require_once (DIR_WS_CLASSES.FILENAME_IMAGEMANIPULATOR);
require_once (DIR_WS_CLASSES.'categories.php');
require_once (DIR_WS_CLASSES.'currencies.php');

$currencies = new currencies();
$catfunc = new categories();

$catfunc->set_page_parameter();

//this is used only by group_prices
$function = (isset($_GET['function']) ? $_GET['function'] : '');
if (xtc_not_null($function)) {
  switch ($function) {
    case 'delete' :
      xtc_db_query("DELETE FROM ".TABLE_PERSONAL_OFFERS_BY.(int)$_GET['statusID']."
                          WHERE products_id = '".(int)$_GET['pID']."'
                            AND quantity = '".(int)$_GET['quantity']."'");
      break;
  }
  xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('action', 'page')).'action=new_product'.$catfunc->page_parameter));
}

// Multi-Status Change, separated from $_GET['action'] //$action
// --- MULTI STATUS ---
if (isset ($_POST['multi_status_on'])) {
  //set multi_categories status=on
  if (isset($_POST['multi_categories']) && is_array($_POST['multi_categories'])) {
    foreach ($_POST['multi_categories'] AS $category_id) {
      $catfunc->set_category_recursive((int)$category_id, '1');
    }
  }
  //set multi_products status=on
  if (isset($_POST['multi_products']) && is_array($_POST['multi_products'])) {
    foreach ($_POST['multi_products'] AS $product_id) {
      $catfunc->set_product_status((int)$product_id, '1');
    }
  }
  xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('action', 'pID', 'cID'))));
}

if (isset ($_POST['multi_status_off'])) {
  //set multi_categories status=off
  if (isset($_POST['multi_categories']) && is_array($_POST['multi_categories'])) {
    foreach ($_POST['multi_categories'] AS $category_id) {
      $catfunc->set_category_recursive((int)$category_id, "0");
    }
  }
  //set multi_products status=off
  if (isset($_POST['multi_products']) && is_array($_POST['multi_products'])) {
    foreach ($_POST['multi_products'] AS $product_id) {
      $catfunc->set_product_status((int)$product_id, "0");
    }
  }
  xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('action', 'pID', 'cID'))));
}
// --- MULTI STATUS ENDS ---

//regular actions
$redirect_parameters = array('action', 'flag', 'page');
if (isset($_GET['search']) && $_GET['search'] != '') {
  array_push($redirect_parameters, 'cPath');
}
$action = (isset($_GET['action']) ? $_GET['action'] : '');
if (xtc_not_null($action)) {
  switch ($action) {
    case 'setcflag' :
      if (($_GET['flag'] == '0') || ($_GET['flag'] == '1')) {
        if ($_GET['cID']) {
          $catfunc->set_category_recursive($_GET['cID'], $_GET['flag']);
        }
      }
      xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params($redirect_parameters).$catfunc->page_parameter_plain));
      break;
      //EOB setcflag
    case 'setpflag' :
      if (($_GET['flag'] == '0') || ($_GET['flag'] == '1')) {
        if ($_GET['pID']) {
          $catfunc->set_product_status($_GET['pID'], $_GET['flag']);
        }
      }
      xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params($redirect_parameters).$catfunc->page_parameter_plain));
      break;
      //EOB setpflag
    case 'setsflag' :
      if (($_GET['flag'] == '0') || ($_GET['flag'] == '1')) {
        if ($_GET['pID']) {
          $catfunc->set_product_startpage($_GET['pID'], $_GET['flag']);
          if ($_GET['flag'] == '1') $catfunc->link_product($_GET['pID'], 0);
          $catfunc->set_product_remove_startpage_sql($_GET['pID'], $_GET['flag']);
        }
      }
      xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('action', 'pID', 'cID', 'flag'))));
      break;
      //EOB setsflag
    case 'update_category' :
      $categories_id = $catfunc->insert_category($_POST, '', 'update');
      
      $cPath = xtc_get_category_path($categories_id);
      $cPath_array = xtc_parse_category_path($cPath);
      array_unshift($cPath_array, 0);
      array_pop($cPath_array);
      $_GET['cPath'] = implode('_', $cPath_array);

      //redirect by update button
      if (isset($_POST['cat_update'])) {
        xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('action', 'cID')).'action=edit_category&cID='.$categories_id));
      }     
      xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('action', 'cID')).'cID='.$categories_id)); 
      break;
    case 'insert_category' :
      $categories_id = $catfunc->insert_category($_POST, $current_category_id);
      xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID')).xtc_get_path($categories_id).'&cID='.$categories_id)); 
      break;
    case 'update_product' :
      if (isset($_POST['action']) && $_POST['action'] == 'update_stock') {
        $result = $catfunc->update_product($_POST);
      } else {
        $result = $catfunc->insert_product($_POST, '', 'update');
      }
      //redirect by update button
      if(isset($_POST['prod_update']) || $result['error'] === true) {
        xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('action', 'pID')).'action=new_product&pID='.$result['products_id']));
      }
      if (isset($_GET['origin']) && $_GET['origin'] != '') {
        xtc_redirect(xtc_href_link(basename($_GET['origin']), 'pID='.$result['products_id'].$catfunc->page_parameter));
      }
      xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID', 'page')).xtc_get_path($current_category_id).'&pID='.$result['products_id'].$catfunc->page_parameter));
      break;
    case 'insert_product' :
      $result = $catfunc->insert_product($_POST, $current_category_id);
      xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID', 'page')).xtc_get_path($current_category_id).'&pID='.$result['products_id'].$catfunc->page_parameter));
      break;
    case 'edit_crossselling' :
      $catfunc->edit_cross_sell($_REQUEST);
      break;
    case 'multi_action_confirm' :
      // --- MULTI DELETE ---
      if (isset ($_POST['multi_delete_confirm'])) {
        //delete multi_categories
        if (isset($_POST['multi_categories']) && is_array($_POST['multi_categories'])) {
          foreach ($_POST['multi_categories'] AS $category_id) {
            $catfunc->remove_categories($category_id);
          }
        }
        //delete multi_products
        if (isset($_POST['multi_products']) 
            && is_array($_POST['multi_products']) 
            && isset($_POST['multi_products_categories'])
            && is_array($_POST['multi_products_categories'])
            )
        {
          foreach ($_POST['multi_products'] AS $product_id) {
            if (isset($_POST['multi_products_categories'][$product_id])) {
              $catfunc->delete_product($product_id, $_POST['multi_products_categories'][$product_id]);
            }
          }
        }
      }
      // --- MULTI DELETE ENDS ---

      // --- MULTI MOVE ---
      if (isset ($_POST['multi_move_confirm'])) {
        //move multi_categories
        if (isset($_POST['multi_categories']) 
            && is_array($_POST['multi_categories']) 
            && isset($_POST['move_to_category_id'])
            && xtc_not_null($_POST['move_to_category_id'])
            )
        {
          foreach ($_POST['multi_categories'] AS $category_id) {
            $dest_category_id = xtc_db_prepare_input($_POST['move_to_category_id']);
            if ($category_id != $dest_category_id) {
              $catfunc->move_category($category_id, $dest_category_id);
            }
          }
        }
        //move multi_products
        if (isset($_POST['multi_products']) 
            && is_array($_POST['multi_products']) 
            && isset($_POST['move_to_category_id']) 
            && xtc_not_null($_POST['move_to_category_id']) 
            && isset($_POST['src_category_id'])
            && xtc_not_null($_POST['src_category_id'])
            )
        {
          foreach ($_POST['multi_products'] AS $product_id) {
            $product_id = xtc_db_prepare_input($product_id);
            $src_category_id = xtc_db_prepare_input($_POST['src_category_id']);
            $dest_category_id = xtc_db_prepare_input($_POST['move_to_category_id']);
            $catfunc->move_product($product_id, $src_category_id, $dest_category_id);
          }
        }
        xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID')).'cPath='.$dest_category_id));
      }
      // --- MULTI MOVE ENDS ---

      // --- MULTI COPY ---
      if (isset ($_POST['multi_copy_confirm'])) {
        //copy multi_categories
        if (isset($_POST['multi_categories'])
            && is_array($_POST['multi_categories']) 
            && ((isset($_POST['dest_cat_ids']) && is_array($_POST['dest_cat_ids'])) 
                || (isset($_POST['dest_category_id']) && xtc_not_null($_POST['dest_category_id']))
                )
            )
        {
          if (!isset($_POST['dest_cat_ids']) && isset($_POST['dest_category_id'])) {
            $_POST['dest_cat_ids'] = array($_POST['dest_category_id']);
          }
          $_SESSION['copied'] = array();
          foreach ($_POST['multi_categories'] AS $category_id) {
            if (isset($_POST['dest_cat_ids']) && is_array($_POST['dest_cat_ids'])) {
              foreach ($_POST['dest_cat_ids'] AS $dest_category_id) {
                if ($_POST['copy_as'] == 'link') {
                  $catfunc->copy_category($category_id, $dest_category_id, 'link');
                } elseif ($_POST['copy_as'] == 'duplicate') {
                  $catfunc->copy_category($category_id, $dest_category_id, 'duplicate');
                } else {
                  $messageStack->add_session(ERROR_COPY_METHOD_NOT_SPECIFIED, 'error');
                }
              }
            } elseif (isset($_POST['dest_category_id']) && xtc_not_null($_POST['dest_category_id'])) {
              $dest_category_id = xtc_db_prepare_input($_POST['dest_category_id']);
              if ($_POST['copy_as'] == 'link') {
                $messageStack->add_session(ERROR_COPY_METHOD_NOT_ALLOWED, 'error');
              } elseif ($_POST['copy_as'] == 'duplicate') {
                $catfunc->copy_category($category_id, $dest_category_id, 'duplicate');
              } else {
                $messageStack->add_session(ERROR_COPY_METHOD_NOT_SPECIFIED, 'error');
              }
            }
          }
          unset ($_SESSION['copied']);
        }
        //copy multi_products
        if (isset($_POST['multi_products'])
            && is_array($_POST['multi_products']) 
            && ((isset($_POST['dest_cat_ids']) && is_array($_POST['dest_cat_ids']))
                || (isset($_POST['dest_category_id']) && xtc_not_null($_POST['dest_category_id']))
                )
            )
        {
          foreach ($_POST['multi_products'] AS $product_id) {
            $product_id = xtc_db_prepare_input($product_id);
            if (isset($_POST['dest_cat_ids']) && is_array($_POST['dest_cat_ids'])) {
              foreach ($_POST['dest_cat_ids'] AS $dest_category_id) {
                $dest_category_id = xtc_db_prepare_input($dest_category_id);
                if ($_POST['copy_as'] == 'link') {
                  $catfunc->link_product($product_id, $dest_category_id);
                  $pID = $product_id;
                } elseif ($_POST['copy_as'] == 'duplicate') {
                  $catfunc->duplicate_product($product_id, $dest_category_id);
                  $pID = $catfunc->dup_products_id;
                } else {
                  $messageStack->add_session(ERROR_COPY_METHOD_NOT_SPECIFIED, 'error');
                }
              }
            } elseif (isset($_POST['dest_category_id']) && xtc_not_null($_POST['dest_category_id'])) {
              $dest_category_id = xtc_db_prepare_input($_POST['dest_category_id']);
              if ($_POST['copy_as'] == 'link') {
                $catfunc->link_product($product_id, $dest_category_id);
                $pID = $product_id;
              } elseif ($_POST['copy_as'] == 'duplicate') {
                $catfunc->duplicate_product($product_id, $dest_category_id);
                $pID = $catfunc->dup_products_id;
              } else {
                $messageStack->add_session(ERROR_COPY_METHOD_NOT_SPECIFIED, 'error');
              }
            }
          }
        }

        $action = isset($_POST['multi_products']) && is_array($_POST['multi_products']) && isset($_POST['link_to_product']) ? '&action=new_product' : '';
        $pID = isset($pID) && $pID > 0 ? '&pID='. $pID : '';
        xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID')).'cPath='.$dest_category_id.$pID.$action));
      }
      // --- MULTI COPY ENDS ---
      xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('action', 'pID', 'cID'))));
      break;
      #EOB multi_action_confirm
  }
}

// check if the catalog image directory exists
if (is_dir(DIR_FS_CATALOG_IMAGES)) {
  if (!is_writeable(DIR_FS_CATALOG_IMAGES))
    $messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE, 'error');
} else {
  $messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST, 'error');
}
// end of pre-checks and actions, HTML output follows

//breadcrumb
require_once (DIR_FS_CATALOG.'includes/classes/breadcrumb.php');
$breadcrumb = new breadcrumb();
$breadcrumb->add(TEXT_TOP, xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'page'))));

if ((isset($_GET['search']) && xtc_not_null($_GET['search']))
    || (isset($_GET['search_id']) && xtc_not_null($_GET['search_id']))
    )
{
  $heading_title = HEADING_TITLE_SEARCH;
} else {
  $heading_title = HEADING_TITLE;
}

if (isset($cPath_array)) {
  $cPathLinkParam = array();
  for ($i = 0, $n = sizeof($cPath_array); $i < $n; $i ++) {
    if ($cPath_array[$i]
        && (!isset($_GET['cID']) || (int)$_GET['cID'] != $cPath_array[$i])
        )
    {
      $cPathLinkParam[] = $cPath_array[$i];
      $categories_query = xtc_db_query("SELECT categories_name
                                          FROM ".TABLE_CATEGORIES_DESCRIPTION."
                                         WHERE categories_id = '".(int)$cPath_array[$i]."'
                                           AND language_id = '".(int)$_SESSION['languages_id']."'");
      if (xtc_db_num_rows($categories_query) > 0) {
        $categories = xtc_db_fetch_array($categories_query);
        $breadcrumb->add($categories['categories_name'], xtc_href_link(FILENAME_CATEGORIES, 'cPath='.implode('_',$cPathLinkParam) . (isset($_GET['page']) ? '&page='.(int)$_GET['page'] : '')));
      } else {
        break;
      }
    }
  }
}

$breadcrumb_html = '<span class="breadcrumb">' . $breadcrumb->trail(' &raquo; ') . '</span>';

require (DIR_WS_INCLUDES.'head.php');
?>
<script type="text/javascript" src="includes/general.js"></script>
<script type="text/javascript" src="includes/javascript/categories.js"></script>
  <script type="text/javascript"> 
    var lang_chars_left = '<?php echo CHARS_LEFT; ?>'; 
    var lang_chars_max = '<?php echo CHARS_MAX; ?>'; 
  </script>  
  <script type="text/javascript" src="includes/javascript/countdown.js"></script> 
<?php
//jQueryDatepicker
require (DIR_WS_INCLUDES.'javascript/jQueryDateTimePicker/datepicker.js.php');
// Include WYSIWYG if is activated
if (USE_WYSIWYG == 'true') {
  // generate editor for categories EDIT
  $languages = xtc_get_languages();
  echo PHP_EOL . (!function_exists('editorJSLink') ? '<script type="text/javascript" src="includes/modules/fckeditor/fckeditor.js"></script>' : '') . PHP_EOL;
  // generate editor for categories
  if ($action == 'new_category' || $action == 'edit_category') {
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
      echo xtc_wysiwyg('categories_description', $_SESSION['language_code'], $languages[$i]['id']);
      echo xtc_wysiwyg('categories_short_description', $_SESSION['language_code'], $languages[$i]['id']);
    }
  }
  // generate editor for products
  if ($action == 'new_product' || $action == 'new_product_preview') {
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
      echo xtc_wysiwyg('products_description', $_SESSION['language_code'], $languages[$i]['id']);
      echo xtc_wysiwyg('products_short_description', $_SESSION['language_code'], $languages[$i]['id']);
    }
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
              <?php
              //----- new_category / edit_category (when ALLOW_CATEGORY_DESCRIPTIONS is 'true') -----
              if ($action == 'new_category' || $action == 'edit_category') {
                include (DIR_WS_MODULES.'new_category.php');
              } elseif ($action == 'new_product' || $action == 'new_product_preview') {
                include (DIR_WS_MODULES.'new_product.php');
              } elseif ($action == 'edit_crossselling') {
                include (DIR_WS_MODULES.'cross_selling.php');
              } else {
                //set $cPath to 0 if not set - FireFox workaround, didn't work when de/activating categories and $cPath wasn't set
                if (!$cPath) {
                  $cPath = '0';
                }
                include (DIR_WS_MODULES.'categories_view.php');
              }
              ?>
              <!-- close tables from above modules //-->
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