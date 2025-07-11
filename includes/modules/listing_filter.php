<?php
/* -----------------------------------------------------------------------------------------
   $Id: listing_filter.php 16147 2024-10-01 18:08:49Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// include needed functions
require_once (DIR_FS_INC.'get_filter_tags.inc.php');

$filter_smarty = new Smarty();
$filter_smarty->assign('language', $_SESSION['language']);
$filter_smarty->assign('tpl_path', DIR_WS_BASE.'templates/'.CURRENT_TEMPLATE.'/');

// set cache ID
if (!CacheCheck()) {
  $cache = false;
  $filter_smarty->caching = 0;
  $cache_id = null;
} else {
  $cache = true;
  $filter_smarty->caching = 1;
  $filter_smarty->cache_lifetime = CACHE_LIFETIME;
  $filter_smarty->cache_modified_check = CACHE_CHECK == 'true';

  //setting/clearing params
  $get_params = '|params:'.xtc_get_all_get_params();
  $get_params .= isset($_SESSION['filter_sorting']) ? '|fsort:'.$_SESSION['filter_sorting'] : '';
  $get_params .= isset($_SESSION['filter_set']) ? '|fset:'.$_SESSION['filter_set'] : '';

  $cache_id = md5('lID:'.$_SESSION['language'].'|csID:'.$_SESSION['customers_status']['customers_status_id'].'|self:'.basename($PHP_SELF).((isset($current_category_id)) ? '|cID:'.$current_category_id : '').$get_params);
}

$module_filter = '';
if (!$filter_smarty->is_cached(CURRENT_TEMPLATE.'/module/listing_filter.html', $cache_id) || !$cache) {
  $filter_set_dropdown = '';
  $filter_sort_dropdown = '';
  $manufacturer_dropdown = '';
  $filter_dropdown = array();
  $filter_selected = array();

  $filter_set_const = strtoupper(substr(basename($PHP_SELF), 0, -4));
  
  if (defined('DISPLAY_FILTER_'.$filter_set_const)) {
    $filter_vars_array = explode(',', constant('DISPLAY_FILTER_'.$filter_set_const));

    $filter_set_array = array(
      array('id' => '',  'text' => TEXT_FILTER_SETTING_DEFAULT),
    );

    for ($i=0, $n=count($filter_vars_array); $i<$n; $i++) {
      if (trim($filter_vars_array[$i]) != 'all') {
        $filter_set_array[] = array('id' => $filter_vars_array[$i], 'text' => sprintf(TEXT_FILTER_SETTING, trim($filter_vars_array[$i])));
      } else {
        $filter_set_array[] = array('id' => '999999', 'text' => TEXT_FILTER_SETTING_ALL);
      }
    }

    $filter_set_dropdown  = xtc_draw_form('set', xtc_href_link(basename($PHP_SELF), xtc_get_all_get_params(array('show'))), 'post').PHP_EOL;
    $filter_set_dropdown .= xtc_draw_pull_down_menu('filter_set', $filter_set_array, ((isset($_SESSION['filter_set'])) ? (int)$_SESSION['filter_set'] : ''), 'onchange="this.form.submit()"').PHP_EOL;
    $filter_set_dropdown .= '<noscript><input type="submit" value="'.SMALL_IMAGE_BUTTON_VIEW.'" id="filter_set_submit" /></noscript>'.PHP_EOL;
    $filter_set_dropdown .= '</form>'.PHP_EOL;
  
    $filter_smarty->assign('FILTER_SET', $filter_set_dropdown);
  }

  $filter_sort_array = array(
    array ('id' => '',  'text' => TEXT_FILTER_SORTING_DEFAULT),
    array ('id' => '1', 'text' => TEXT_FILTER_SORTING_ABC_ASC),
    array ('id' => '2', 'text' => TEXT_FILTER_SORTING_ABC_DESC),
    array ('id' => '3', 'text' => TEXT_FILTER_SORTING_PRICE_ASC),
    array ('id' => '4', 'text' => TEXT_FILTER_SORTING_PRICE_DESC),
    array ('id' => '5', 'text' => TEXT_FILTER_SORTING_DATE_DESC),
    array ('id' => '6', 'text' => TEXT_FILTER_SORTING_DATE_ASC),
    array ('id' => '7', 'text' => TEXT_FILTER_SORTING_ORDER_DESC),
  );

  $filter_sort_dropdown  = xtc_draw_form('sort', xtc_href_link(basename($PHP_SELF), xtc_get_all_get_params(array('show'))), 'post').PHP_EOL;
  $filter_sort_dropdown .= xtc_draw_pull_down_menu('filter_sort', $filter_sort_array, ((isset($_SESSION['filter_sort'])) ? (int)$_SESSION['filter_sort'] : ''), 'onchange="this.form.submit()"').PHP_EOL;
  $filter_sort_dropdown .= '<noscript><input type="submit" value="'.SMALL_IMAGE_BUTTON_VIEW.'" id="filter_sort_submit" /></noscript>'.PHP_EOL;
  $filter_sort_dropdown .= '</form>'.PHP_EOL;

  $filter_smarty->assign('FILTER_SORT', $filter_sort_dropdown);

  // optional Product List Filter
  if (PRODUCT_LIST_FILTER == 'true') {

    // filter
    $filter_where = '';
    $tags_array = get_filter_tags();
    if (count($tags_array) > 0) {
      $filter_where .= "AND p.products_id IN (".implode(', ', $tags_array).")";
    }
  
    // manufacturers
    $join = '';
    $where = '';
    $select = "m.manufacturers_id as id,
               m.manufacturers_name as name ";
    if (isset($_GET['manufacturers_id']) && (int)$_GET['manufacturers_id'] > 0 && basename($PHP_SELF) != FILENAME_ADVANCED_SEARCH_RESULT) {
      $select = "c.categories_id as id,
                 cd.categories_name as name ";
      $join = " JOIN ".TABLE_PRODUCTS_TO_CATEGORIES." p2c 
                     ON p2c.products_id = p.products_id
                JOIN ".TABLE_CATEGORIES." c 
                     ON c.categories_id = p2c.categories_id 
                        AND c.categories_status = 1
                        ".CATEGORIES_CONDITIONS_C."
                JOIN ".TABLE_CATEGORIES_DESCRIPTION." cd 
                     ON cd.categories_id = p2c.categories_id
                        AND cd.language_id = '".(int) $_SESSION['languages_id']."' ";
      $where = " AND p.manufacturers_id = '".(int)$_GET['manufacturers_id']."' ";
    } elseif (isset($current_category_id) && $current_category_id > 0) {
      $join = " JOIN ".TABLE_PRODUCTS_TO_CATEGORIES." p2c 
                     ON p2c.products_id = p.products_id
                        AND p2c.categories_id ".((isset($subcategories_array) && count($subcategories_array) > 1) ? "IN (".implode(', ', $subcategories_array).") " : "= '".(int)$current_category_id."' ");
    } elseif (basename($PHP_SELF) == FILENAME_SPECIALS) {
      $join = " JOIN ".TABLE_SPECIALS." s 
                     ON p.products_id = s.products_id
                        ".SPECIALS_CONDITIONS_S." ";
    } elseif (basename($PHP_SELF) == FILENAME_PRODUCTS_NEW) {
      if (MAX_DISPLAY_NEW_PRODUCTS_DAYS != '0' && isset($daysfound) && $daysfound == true) {
        $date_new_products = date("Y-m-d", mktime(1, 1, 1, date("m"), date("d") - MAX_DISPLAY_NEW_PRODUCTS_DAYS, date("Y")));
        $where = " AND p.products_date_added > '".$date_new_products."' ";
      }
    } elseif (basename($PHP_SELF) == FILENAME_ADVANCED_SEARCH_RESULT) {
      $where = " AND p.products_id IN ('".implode("', '", $products_search_array)."') ";
      if ($pfrom_check != '' || $pto_check != '') {
        $where .= $pfrom_check;
        $where .= $pto_check;
        $join .= " LEFT JOIN ".TABLE_SPECIALS." AS s 
                             ON p.products_id = s.products_id 
                                AND s.status = '1' ";
      }
      if ($NeedTax === true) {
        if (!isset ($_SESSION['customer_country_id'])) {
          $_SESSION['customer_country_id'] = STORE_COUNTRY;
          $_SESSION['customer_zone_id'] = STORE_ZONE;
        }
        $join .= " LEFT OUTER JOIN ".TABLE_TAX_RATES." tr ON (p.products_tax_class_id = tr.tax_class_id) 
                   LEFT OUTER JOIN ".TABLE_ZONES_TO_GEO_ZONES." gz ON (tr.tax_zone_id = gz.geo_zone_id) ";
        $where .= " AND (gz.zone_country_id IS NULL OR gz.zone_country_id = '0' OR gz.zone_country_id = '".(int) $_SESSION['customer_country_id']."') 
                    AND (gz.zone_id is null OR gz.zone_id = '0' OR gz.zone_id = '".(int) $_SESSION['customer_zone_id']."')";
      }
    }

    $filterlist_sql = "SELECT DISTINCT ".$select."
                                  FROM ".TABLE_PRODUCTS." p
                                  JOIN ".TABLE_PRODUCTS_DESCRIPTION." pd
                                       ON p.products_id = pd.products_id
                                          AND pd.language_id = '".(int)$_SESSION['languages_id']."'
                                          AND trim(pd.products_name) != ''
                                  JOIN ".TABLE_MANUFACTURERS." m 
                                       ON m.manufacturers_id = p.manufacturers_id
                                          AND m.manufacturers_status = 1
                                       ".$join."
                                 WHERE p.products_status = '1'
                                       ".$where."
                                       ".$filter_where."
                                       ".PRODUCTS_CONDITIONS_P."
                              ORDER BY name";
    
    $filterlist_query = xtDBquery($filterlist_sql);
    if (xtc_db_num_rows($filterlist_query, true) > 0) {
      $manufacturer_dropdown = xtc_draw_form('filter', xtc_href_link(basename($PHP_SELF), xtc_get_all_get_params(array('page', 'show', 'cat'))), 'get');
      if (isset($_GET['manufacturers_id']) && (int)$_GET['manufacturers_id'] > 0) {
        if (basename($PHP_SELF) != FILENAME_ADVANCED_SEARCH_RESULT) {
          $options = array (array ('id' => '', 'text' => TEXT_ALL_CATEGORIES));
          if (SEARCH_ENGINE_FRIENDLY_URLS != 'true') {
            $manufacturer_dropdown .= xtc_draw_hidden_field('manufacturers_id', (int)$_GET['manufacturers_id']).PHP_EOL;
          }
        } else {
          $manufacturer_dropdown .= xtc_draw_hidden_field('manufacturers_id', (int)$_GET['manufacturers_id']).PHP_EOL;
        }
      } else {
        $options = array (array ('id' => '', 'text' => TEXT_ALL_MANUFACTURERS));
      }
      if (isset($_GET['cPath']) && !empty($_GET['cPath']) && SEARCH_ENGINE_FRIENDLY_URLS != 'true') {
        $manufacturer_dropdown .= xtc_draw_hidden_field('cPath', preg_replace('/[^0-9_]/','',$_GET['cPath'])).PHP_EOL;
      }
      if (isset($_GET['categories_id']) && !empty($_GET['categories_id'])) {
        $manufacturer_dropdown .= xtc_draw_hidden_field('categories_id', (int)$_GET['categories_id']).PHP_EOL;
      }
      if (isset($_GET['inc_subcat']) && $_GET['inc_subcat'] == '1') {
        $manufacturer_dropdown .= xtc_draw_hidden_field('inc_subcat', '1').PHP_EOL;
      }
      if (isset($_GET['pfrom']) && !empty($_GET['pfrom'])) {
        $manufacturer_dropdown .= xtc_draw_hidden_field('pfrom', stripslashes($_GET['pfrom'])).PHP_EOL;
      }
      if (isset($_GET['pto']) && !empty($_GET['pto'])) {
        $manufacturer_dropdown .= xtc_draw_hidden_field('pto', stripslashes($_GET['pto'])).PHP_EOL;
      }
      if (isset($_GET['keywords']) && !empty($_GET['keywords'])) {
        $manufacturer_dropdown .= xtc_draw_hidden_field('keywords', $_GET['keywords']).PHP_EOL;
      }
      if (isset($_GET['filter']) && is_array($_GET['filter'])) {
        foreach ($_GET['filter'] as $key => $val) {
         $manufacturer_dropdown .= xtc_draw_hidden_field('filter['.$key.']', $val).PHP_EOL;
        }
      }
      while ($filterlist = xtc_db_fetch_array($filterlist_query, true)) {
        $options[] = array ('id' => $filterlist['id'], 'text' => $filterlist['name']);
        
        if (isset($_GET['filter_id']) && $_GET['filter_id'] == $filterlist['id']) {
          $filter_selected[] = array(
            'NAME' => $filterlist['name'],
            'LINK' => xtc_href_link(basename($PHP_SELF), xtc_get_all_get_params(array('filter_id'))),
          );
        }
      }
      $manufacturer_dropdown .= xtc_draw_pull_down_menu('filter_id', $options, isset($_GET['filter_id']) ? (int)$_GET['filter_id'] : '', 'aria-label="'.TEXT_FILTER_MANUFACTURERS_LABEL.'" onchange="this.form.submit()"').PHP_EOL;
      $manufacturer_dropdown .= '<noscript><input type="submit" value="'.SMALL_IMAGE_BUTTON_VIEW.'" id="filter_submit" /></noscript>'.PHP_EOL;
      $manufacturer_dropdown .= xtc_hide_session_id() .PHP_EOL;
      $manufacturer_dropdown .= '</form>'.PHP_EOL;

      $filter_smarty->assign('FILTER_MANUFACTURER', $manufacturer_dropdown);
    }


    // filter
    $join = '';  
    $where = '';
    $filterlist_sql = '';
    if ((isset($_GET['filter_id']) && (int)$_GET['filter_id'] > 0)
        || (isset($_GET['manufacturers_id']) && (int)$_GET['manufacturers_id'] > 0)
        )
    {
      if ((isset($_GET['filter_id']) && (int)$_GET['filter_id'] > 0)
          && (isset($_GET['manufacturers_id']) && (int)$_GET['manufacturers_id'] > 0)
          )
      {
        $join .= " JOIN ".TABLE_PRODUCTS_TO_CATEGORIES." p2c 
                        ON p2c.products_id = p.products_id
                           AND p2c.categories_id = '".(int)$_GET['filter_id']."' ";
        $where .= " AND p.manufacturers_id = '".(int)$_GET['manufacturers_id']."' ";
      } else {
        $where .= " AND p.manufacturers_id = '".((isset($_GET['filter_id']) && (int)$_GET['filter_id'] > 0) ? (int)$_GET['filter_id'] : (int)$_GET['manufacturers_id'])."' ";
      }
    }
    if (isset($current_category_id) && $current_category_id > 0) {
      $join .= " JOIN ".TABLE_PRODUCTS_TO_CATEGORIES." p2c 
                      ON p2c.products_id = p.products_id
                         AND p2c.categories_id ".((isset($subcategories_array) && count($subcategories_array) > 1) ? "IN (".implode(', ', $subcategories_array).") " : "= '".(int)$current_category_id."' ");
    }
    if (basename($PHP_SELF) == FILENAME_SPECIALS) {
      $join .= " JOIN ".TABLE_SPECIALS." s 
                      ON p.products_id = s.products_id
                         AND s.status = '1' ";
    } elseif (basename($PHP_SELF) == FILENAME_PRODUCTS_NEW) {
      if (MAX_DISPLAY_NEW_PRODUCTS_DAYS != '0' && isset($daysfound) && $daysfound == true) {
        $date_new_products = date("Y-m-d", mktime(1, 1, 1, date("m"), date("d") - MAX_DISPLAY_NEW_PRODUCTS_DAYS, date("Y")));
        $where .= " AND p.products_date_added > '".$date_new_products."' ";
      }
    } elseif (basename($PHP_SELF) == FILENAME_ADVANCED_SEARCH_RESULT) {
      $where .= " AND p.products_id IN ('".implode("', '", $products_search_array)."') ";
      if ($pfrom_check != '' || $pto_check != '') {
        $where .= $pfrom_check;
        $where .= $pto_check;
        $join .= " LEFT JOIN ".TABLE_SPECIALS." s 
                             ON p.products_id = s.products_id
                                AND s.status = '1' ";
      }
      if ($NeedTax === true) {
        if (!isset ($_SESSION['customer_country_id'])) {
          $_SESSION['customer_country_id'] = STORE_COUNTRY;
          $_SESSION['customer_zone_id'] = STORE_ZONE;
        }
        $join .= " LEFT OUTER JOIN ".TABLE_TAX_RATES." tr ON (p.products_tax_class_id = tr.tax_class_id) 
                   LEFT OUTER JOIN ".TABLE_ZONES_TO_GEO_ZONES." gz ON (tr.tax_zone_id = gz.geo_zone_id) ";
        $where .= " AND (gz.zone_country_id IS NULL OR gz.zone_country_id = '0' OR gz.zone_country_id = '".(int) $_SESSION['customer_country_id']."') 
                    AND (gz.zone_id is null OR gz.zone_id = '0' OR gz.zone_id = '".(int) $_SESSION['customer_zone_id']."')";
      }
    }
  
    $filterlist_sql = "SELECT DISTINCT pto.options_id,
                                       pto.options_name,
                                       ptv.values_id,
                                       ptv.values_name
                                  FROM ".TABLE_PRODUCTS." p
                                  JOIN ".TABLE_PRODUCTS_DESCRIPTION." pd
                                       ON p.products_id = pd.products_id
                                          AND pd.language_id = '".(int)$_SESSION['languages_id']."'
                                          AND trim(pd.products_name) != ''
                                  JOIN ".TABLE_PRODUCTS_TAGS." pt
                                       ON pt.products_id = p.products_id
                                  JOIN ".TABLE_PRODUCTS_TAGS_OPTIONS." pto
                                       ON pt.options_id = pto.options_id
                                          AND pto.languages_id = '".(int)$_SESSION['languages_id']."'
                                          AND pto.filter = '1'
                                  JOIN ".TABLE_PRODUCTS_TAGS_VALUES." ptv
                                       ON pto.options_id = ptv.options_id
                                          AND pt.values_id = ptv.values_id
                                          AND ptv.languages_id = '".(int)$_SESSION['languages_id']."'
                                          AND ptv.filter = '1'
                                       ".$join."
                                 WHERE p.products_status = '1'
                                       ".$where."
                                       ".$filter_where."
                                       ".PRODUCTS_CONDITIONS_P."
                              ORDER BY pto.sort_order, pto.options_name, ptv.sort_order, ptv.values_name";                           

    $filterlist_query = xtDBquery($filterlist_sql);
    if (xtc_db_num_rows($filterlist_query, true) > 0) {
      $options = array();
      while ($filterlist = xtc_db_fetch_array($filterlist_query, true)) {
        $options[$filterlist['options_id']]['NAME'] = $filterlist['options_name'];
        $options[$filterlist['options_id']][] = array ('id' => $filterlist['values_id'], 'text' => $filterlist['values_name']);

        if (isset($_GET['filter'][$filterlist['options_id']]) 
            && $_GET['filter'][$filterlist['options_id']] == $filterlist['values_id']
            )
        {
          $filter_selected[] = array(
            'NAME' =>  $filterlist['values_name'],
            'LINK' => xtc_href_link(basename($PHP_SELF), xtc_get_all_get_params(array(array('filter' => $filterlist['options_id'])))),
          );
        }
      }
        
      foreach ($options as $options_id => $values) {
        $options_name = $values['NAME'];
        unset($values['NAME']);

        if (isset($_GET['filter'][$options_id]) && $_GET['filter'][$options_id] != '') {
          $options_array = array (array ('id' => '', 'text' => $options_name . TEXT_SHOW_ALL));
        } else {
          $options_array = array (array ('id' => '', 'text' => $options_name));
        }
        $options_array = array_merge($options_array, $values);
            
        $filter_dropdown[$options_id] = xtc_draw_form('filter_'.$options_id, xtc_href_link(basename($PHP_SELF), xtc_get_all_get_params(array('page', 'show', 'cat'))), 'get');
        if (isset($_GET['manufacturers_id']) && $_GET['manufacturers_id'] > 0) {
          if (basename($PHP_SELF) == FILENAME_ADVANCED_SEARCH_RESULT || SEARCH_ENGINE_FRIENDLY_URLS != 'true') {
            $filter_dropdown[$options_id] .= xtc_draw_hidden_field('manufacturers_id', (int)$_GET['manufacturers_id']).PHP_EOL;
          }
        }
        if (isset($_GET['cPath']) && !empty($_GET['cPath']) && SEARCH_ENGINE_FRIENDLY_URLS != 'true') {
          $filter_dropdown[$options_id] .= xtc_draw_hidden_field('cPath', preg_replace('/[^0-9_]/','',$_GET['cPath'])).PHP_EOL;
        }
        if (isset($_GET['categories_id']) && !empty($_GET['categories_id'])) {
          $filter_dropdown[$options_id] .= xtc_draw_hidden_field('categories_id', (int)$_GET['categories_id']).PHP_EOL;
        }
        if (isset($_GET['inc_subcat']) && $_GET['inc_subcat'] == '1') {
          $filter_dropdown[$options_id] .= xtc_draw_hidden_field('inc_subcat', '1').PHP_EOL;
        }
        if (isset($_GET['pfrom']) && !empty($_GET['pfrom'])) {
          $filter_dropdown[$options_id] .= xtc_draw_hidden_field('pfrom', stripslashes($_GET['pfrom'])).PHP_EOL;
        }
        if (isset($_GET['pto']) && !empty($_GET['pto'])) {
          $filter_dropdown[$options_id] .= xtc_draw_hidden_field('pto', stripslashes($_GET['pto'])).PHP_EOL;
        }
        if (isset($_GET['keywords']) && !empty($_GET['keywords'])) {
          $filter_dropdown[$options_id] .= xtc_draw_hidden_field('keywords', $_GET['keywords']).PHP_EOL;
        }
        if (isset($_GET['filter_id']) && !empty($_GET['filter_id'])) {
          $filter_dropdown[$options_id] .= xtc_draw_hidden_field('filter_id', (int)$_GET['filter_id']).PHP_EOL;
        }
        if (isset($_GET['filter']) && is_array($_GET['filter'])) {
          foreach ($_GET['filter'] as $key => $val) {
            if ($key != $options_id) {
              $filter_dropdown[$options_id] .= xtc_draw_hidden_field('filter['.$key.']', $val).PHP_EOL;
            }
          }
        }
        $filter_dropdown[$options_id] .= xtc_draw_pull_down_menu('filter['.$options_id.']', $options_array, isset($_GET['filter'][$options_id]) ? (int)$_GET['filter'][$options_id] : '', 'aria-label="'.$options_name.'" onchange="this.form.submit()"').PHP_EOL;
        $filter_dropdown[$options_id] .= '<noscript><input type="submit" value="'.SMALL_IMAGE_BUTTON_VIEW.'" id="filter_'.$options_id.'_submit" /></noscript>'.PHP_EOL;
        $filter_dropdown[$options_id] .= xtc_hide_session_id() .PHP_EOL;
        $filter_dropdown[$options_id] .= '</form>'.PHP_EOL;
      }
    }

    $filter_smarty->assign('LINK_FILTER_RESET', xtc_href_link(basename($PHP_SELF), xtc_get_all_get_params(array('filter', 'show', 'filter_id')), 'NONSSL'));
  }

  $filter_smarty->assign('FILTER_TAG', $filter_dropdown);
  $filter_smarty->assign('FILTER_SELECTED', $filter_selected);
  $filter_smarty->assign('LINK_DISPLAY_LIST', xtc_href_link(basename($PHP_SELF), xtc_get_all_get_params(array('show')).'show=list', 'NONSSL'));
  $filter_smarty->assign('LINK_DISPLAY_BOX', xtc_href_link(basename($PHP_SELF), xtc_get_all_get_params(array('show')).'show=box', 'NONSSL'));
}

$module = $filter_smarty->fetch(CURRENT_TEMPLATE.'/module/listing_filter.html', $cache_id);

if (isset($smarty) && is_object($smarty)) {
  $smarty->assign('LISTING_FILTER', !empty($module) ? trim($module) : $module);
}

if (isset($module_smarty) && is_object($module_smarty)) {
  $module_smarty->assign('LISTING_FILTER', !empty($module) ? trim($module) : $module);
}
