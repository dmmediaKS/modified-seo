<?php
/* --------------------------------------------------------------
   $Id: stats_products_viewed.php 16411 2025-04-09 13:19:49Z GTB $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(stats_products_viewed.php,v 1.27 2003/01/29); www.oscommerce.com 
   (c) 2003	 nextcommerce (stats_products_viewed.php,v 1.9 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

require('includes/application_top.php');

// include needed functions
require_once (DIR_FS_INC.'xtc_get_category_path.inc.php');
require_once (DIR_FS_INC.'xtc_get_parent_categories.inc.php');

//display per page
$cfg_max_display_results_key = 'MAX_DISPLAY_STATS_PRODUCTS_VIEWED_RESULTS';
$page_max_display_results = xtc_cfg_save_max_display_results($cfg_max_display_results_key);

$products_history_status_array = array(
  array('id' => '1','text'=> CFG_TXT_YES),
  array('id' => '0','text'=> CFG_TXT_NO)
);

if (!defined('MODULE_PRODUCTS_HISTORY_STATUS')) {
  xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PRODUCTS_HISTORY_STATUS', 'true',  '6', '0', 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
  define('MODULE_PRODUCTS_HISTORY_STATUS', 'true');
}

if (isset($_GET['action']) && $_GET['action'] == 'save') {
  if (isset($_POST['reset_products_history']) && $_POST['reset_products_history'] == 'on') {
    xtc_db_query("UPDATE ".TABLE_PRODUCTS_DESCRIPTION." SET products_viewed = 0");
  }
  xtc_db_query("UPDATE ".TABLE_CONFIGURATION."
                   SET configuration_value = '".(($_POST['products_history'] == '1') ? 'true' : 'false')."'
                 WHERE configuration_key = 'MODULE_PRODUCTS_HISTORY_STATUS'");
  xtc_redirect(xtc_href_link(basename($PHP_SELF)));
}

require (DIR_WS_INCLUDES.'head.php');
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
      <div class="flt-l" style="min-width: 300px;">
        <div class="pageHeadingImage"><?php echo xtc_image(DIR_WS_ICONS.'heading/icon_statistic.png'); ?></div>
        <div class="pageHeading"><?php echo HEADING_TITLE; ?></div>              
        <div class="main pdg2">Statistics</div>
      </div>
      <div class="main pdg2 flt-l" style="margin:5px 0 0 128px;">
        <?php 
        echo xtc_draw_form('products_history', basename($PHP_SELF), 'action=save', 'post').PHP_EOL;
        echo '<div class="flt-l" style="margin: 10px 0 0">'.PHP_EOL;
        echo TEXT_RESET_PRODUCTS_HISTORY.PHP_EOL;
        echo '<div class="flt-l" style="margin: -5px 5px 0px 5px">'.PHP_EOL;
        echo xtc_draw_checkbox_field('reset_products_history', 'on', false);
        echo '</div>'.PHP_EOL;
        echo '</div>'.PHP_EOL;
        echo '<br>';
        echo '<div class="flt-l" style="margin: 10px 0 0">'.PHP_EOL;
        echo TEXT_ACTIVATE_PRODUCTS_HISTORY.PHP_EOL;
        echo '<div class="flt-r" style="margin: -6px 50px 0px 5px">'.PHP_EOL;
        echo draw_on_off_selection('products_history', $products_history_status_array, ((MODULE_PRODUCTS_HISTORY_STATUS == 'true') ? true : false), 'onchange="this.form.submit();"').PHP_EOL;
        echo '</div>'.PHP_EOL;
        echo '</div>'.PHP_EOL;
        echo '</form>';
        ?>
      </div>
      <table class="tableCenter">      
        <tr>
          <td class="boxCenterFull">
            <table class="tableBoxCenter collapse">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_NUMBER; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_MODEL; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
                <td class="dataTableHeadingContent txta-c"><?php echo TABLE_HEADING_VIEWED; ?>&nbsp;</td>
              </tr>
              <?php
              $rows = (isset($_GET['page']) && $_GET['page'] > 1) ? $_GET['page']*$page_max_display_results-$page_max_display_results : 0;   
              $products_query_raw = "SELECT p.products_id,
                                            p.products_model,                  
                                            pd.products_name, 
                                            pd.products_viewed,
                                            l.name 
                                       FROM " . TABLE_PRODUCTS . " p
                                       JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd
                                            ON p.products_id = pd.products_id
                                       JOIN " . TABLE_LANGUAGES . " l 
                                            ON l.languages_id = pd.language_id
                                   ORDER BY pd.products_viewed DESC, pd.products_name ASC";
              $products_split = new splitPageResults($_GET['page'], $page_max_display_results, $products_query_raw, $products_query_numrows, 'p.products_id');
              $products_query = xtc_db_query($products_query_raw);
              while ($products = xtc_db_fetch_array($products_query)) {
                $category_query = xtc_db_query("SELECT categories_id 
                                                  FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                                                 WHERE products_id = '" . (int)$products['products_id'] . "'
                                                   AND categories_id != '0'
                                                 LIMIT 1");
                $category = xtc_db_fetch_array($category_query);
                $rows++;
                $rows = str_pad($rows, strlen($page_max_display_results), '0', STR_PAD_LEFT);
              ?>                  
              <tr class="dataTableRow" onmouseover="this.className='dataTableRowOver';this.style.cursor='pointer'" onmouseout="this.className='dataTableRow'" onclick="document.location.href='<?php echo xtc_href_link(FILENAME_CATEGORIES, 'action=new_product&pID=' . $products['products_id'] . '&origin=' . FILENAME_STATS_PRODUCTS_VIEWED . '&page=' . $_GET['page'] . '&cPath='.xtc_get_category_path($category['categories_id']), 'NONSSL'); ?>'">
                <td class="dataTableContent"><?php echo $rows; ?>.</td>
                <td class="dataTableContent"><?php echo $products['products_model']; ?></td>
                <td class="dataTableContent"><?php echo  $products['products_name'] . ' (' . $products['name'] . ')'; ?></td>
                <td class="dataTableContent txta-c"><?php echo $products['products_viewed']; ?>&nbsp;</td>
              </tr>
            <?php
              }
            ?>
            </table>
          </td>
        </tr>
      </table>           
      <div class="smallText pdg2 flt-l"><?php echo $products_split->display_count($products_query_numrows, $page_max_display_results, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></div>
      <div class="smallText pdg2 flt-r"><?php echo $products_split->display_links($products_query_numrows, $page_max_display_results, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div>
      <?php echo draw_input_per_page($PHP_SELF,$cfg_max_display_results_key,$page_max_display_results); ?>
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