<?php
/* -----------------------------------------------------------------------------------------
   $Id: main.php 15723 2024-02-20 15:26:21Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(Coding Standards); www.oscommerce.com
   (c) 2006 XT-Commerce (main.php 1286 2005-10-07)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class main {

  var $SHIPPING;
  var $vpe_name;
  var $mainModules;

  /**
   * class constructor function
   */
  function __construct($language_id = '') {
    if ($language_id == '') {
      $language_id = (int)$_SESSION['languages_id'];
    }
    
    //new module support
    require_once (DIR_FS_CATALOG.'includes/classes/mainModules.class.php');
    $this->mainModules = new mainModules();
    
    $this->SHIPPING = array();

    // prefetch shipping status
    $status_query = xtDBquery("SELECT shipping_status_name,
                                      shipping_status_image,
                                      shipping_status_id
                               FROM ".TABLE_SHIPPING_STATUS."
                               WHERE language_id = '".(int)$language_id."'");

    while ($status_data=xtc_db_fetch_array($status_query,true)) {
      $this->SHIPPING[$status_data['shipping_status_id']] = array(
        'name'=>$status_data['shipping_status_name'],
        'image'=>$status_data['shipping_status_image']
      );
    }
  }

  /**
   * getShippingStatusName
   *
   * @param integer $id
   * @return  string
   */
  function getShippingStatusName($id, $link = false) {
    global $request_type;
    
    if (!defined('SHIPPING_STATUS_INFOS') || $link === false) {
      return (isset($this->SHIPPING[$id]['name']) ? $this->SHIPPING[$id]['name'] : '');
    }
    
    $popup_params = $this->getPopupParams();
    
    return '<a rel="nofollow" target="_blank" href="'.xtc_href_link(FILENAME_POPUP_CONTENT, 'coID='.SHIPPING_STATUS_INFOS.$popup_params['link_parameters'], $request_type).'" title="'.$popup_params['link_title'].'" class="'.$popup_params['link_class'].'">'.(isset($this->SHIPPING[$id]['name']) ? $this->SHIPPING[$id]['name'] : '').'</a>';
  }

  /**
   * getShippingStatusImage
   *
   * @param integer $id
   * @return  string
   */
  function getShippingStatusImage($id) {
    if (isset($this->SHIPPING[$id]['image']) 
        && $this->SHIPPING[$id]['image'] != ''
        && is_file(DIR_FS_CATALOG.DIR_WS_IMAGES.$this->SHIPPING[$id]['image'])
        )
    {
      return DIR_WS_CATALOG.DIR_WS_IMAGES.$this->SHIPPING[$id]['image'];
    }
  }

  /**
   * getShippingLink
   *
   * @return  string
   */
  function getShippingLink() {
    global $request_type;
        
    if (SHOW_SHIPPING == 'true') {
      $popup_params = $this->getPopupParams();
      return ' '.((SHOW_SHIPPING_EXCL == 'false') ? SHIPPING_INCL : SHIPPING_EXCL).' <a rel="nofollow" target="_blank" href="'.xtc_href_link(FILENAME_POPUP_CONTENT, 'coID='.SHIPPING_INFOS.$popup_params['link_parameters'], $request_type).'" title="'.$popup_params['link_title'].'" class="'.$popup_params['link_class'].'">'.SHIPPING_COSTS.'</a>';
    }
  }

  /**
   * getTaxNotice
   *
   * @return  string
   */
  function getTaxNotice() {
    // no prices
    if ($_SESSION['customers_status']['customers_status_show_price'] == 0) {
      return;
    }
    if ($_SESSION['customers_status']['customers_status_show_price_tax'] != 0) {
      return TAX_INFO_INCL_GLOBAL;
    }
    // excl tax + tax at checkout
    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 
        && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1
        )
    {
      return TAX_INFO_ADD_GLOBAL;
    }
    // excl tax
    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 
        && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0
        )
    {
      return TAX_INFO_EXCL_GLOBAL;
    }
  }

  /**
   * getTaxInfo
   *
   * @param string $tax_rate
   * @return string
   */
  function getTaxInfo($tax_rate) {
    $tax_info = '';
    
    if (defined('MODULE_ORDER_TOTAL_TAX_STATUS')
        && MODULE_ORDER_TOTAL_TAX_STATUS == 'true'
        )
    {
      // price incl tax
      if ($tax_rate > 0 && $_SESSION['customers_status']['customers_status_show_price_tax'] != 0) {
        $tax_info = sprintf(TAX_INFO_INCL, $tax_rate.' %');
      }
      // excl tax + tax at checkout
      if ($tax_rate > 0 
          && $_SESSION['customers_status']['customers_status_show_price_tax'] == 0 
          && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1
          )
      {
        $tax_info = sprintf(TAX_INFO_ADD, $tax_rate.' %');
      }
      // excl tax
      if ($tax_rate > 0 
          && $_SESSION['customers_status']['customers_status_show_price_tax'] == 0 
          && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0
          )
      {
        $tax_info = sprintf(TAX_INFO_EXCL, $tax_rate.' %');
      }
      // no tax
      if ($tax_rate == 0) {
        $tax_info = sprintf(TAX_INFO_EXCL, '');
      }
    }
    
    if (MODULE_SMALL_BUSINESS == 'true') {
      $tax_info = TAX_INFO_SMALL_BUSINESS;
    }
    
    //new module support
    $tax_info = $this->mainModules->getTaxInfo($tax_info,$tax_rate);
    
    return $tax_info;
  }

  /**
   * getShippingNotice
   *
   * @return string
   */
  function getShippingNotice() {
    $shippingNotice = '';
    if (SHOW_SHIPPING == 'true') {
      $shippingNotice = ' '.SHIPPING_EXCL.'<a href="'.xtc_href_link(FILENAME_CONTENT, xtc_content_link(SHIPPING_INFOS)).'">'.SHIPPING_COSTS.'</a>';
    }
    
    //new module support
    $shippingNotice = $this->mainModules->getShippingNotice($shippingNotice);
    
    return $shippingNotice;
  }
  
  /**
   * getPopupParams
   *
   * @return array
   */
  function getPopupParams() {
    if (!defined('POPUP_CONTENT_LINK_PARAMETERS')) {
      define('POPUP_CONTENT_LINK_PARAMETERS', '&KeepThis=true&TB_iframe=true&height=400&width=600');
    }
    if (!defined('POPUP_CONTENT_LINK_CLASS')) {
      define('POPUP_CONTENT_LINK_CLASS', 'thickbox');
    }
    $popup_params = array(
      'link_parameters' => defined('TPL_POPUP_CONTENT_LINK_PARAMETERS') ? TPL_POPUP_CONTENT_LINK_PARAMETERS : POPUP_CONTENT_LINK_PARAMETERS,
      'link_class' => defined('TPL_POPUP_CONTENT_LINK_CLASS') ? TPL_POPUP_CONTENT_LINK_CLASS : POPUP_CONTENT_LINK_CLASS,
      'link_title' => TEXT_LINK_TITLE_INFORMATION,
    );
    
    return $popup_params;
  }
  
  /**
   * getContentLink
   *
   * @param integer $coID
   * @param string $text, $ssl
   * @return string
   */
  function getContentLink($coID, $text, $ssl = 'NONSSL', $class_more = true) {
    $popup_params = $this->getPopupParams();

    $contentLink = '<a target="_blank" href="'.xtc_href_link(FILENAME_POPUP_CONTENT, 'coID='.$coID.$popup_params['link_parameters'], $ssl).'" title="'.$popup_params['link_title'].'" class="'.(($class_more === true) ? 'color_more ' : '').$popup_params['link_class'].'">'.$text.'</a>';
    
    //new module support
    $contentLink = $this->mainModules->getContentLink($contentLink, $coID, $text, $ssl, $class_more);
    
    return $contentLink;
  }
  
  /**
   * getContentData
   *
   * @param integer $coID
   * @return array
   */
  function getContentData($coID, $language_id = '', $customers_status = '', $get_inactive = true, $add_select= '') {
    global $_mod_captcha_class;
    static $content_data_array;
    
    if (!isset($content_data_array)) $content_data_array = array();
    
    $language_id = !empty($language_id) ? $language_id : $_SESSION['languages_id'];
    $status = (($get_inactive === true) ? '1' : '0');
    $customers_status = $customers_status != '' ? $customers_status : $_SESSION['customers_status']['customers_status_id'];
    $group_check = (GROUP_CHECK == 'true') ? "AND group_ids LIKE '%c_" . (int)$customers_status . "_group%'" : '';
    $where = (($get_inactive === true) ? '' : " AND content_active = '1'");
    
    if (!isset($content_data_array[$language_id][$customers_status][$status][(int)$coID])) {
      $content_data_array[$language_id][$customers_status][$status][(int)$coID] = array();

      $content_data_query = xtDBquery("SELECT ".$add_select."
                                              content_id,
                                              content_title,
                                              content_heading,
                                              content_text,
                                              content_file
                                         FROM " . TABLE_CONTENT_MANAGER . "
                                        WHERE content_group='". (int)$coID ."'
                                              " . $group_check . "
                                              " . $where . "
                                          AND trim(content_title) != ''
                                          AND languages_id='" . (int)$language_id . "'
                                        LIMIT 1");
      if (xtc_db_num_rows($content_data_query, true) > 0) {
        $content_data = xtc_db_fetch_array($content_data_query, true);
        $content_data['content_body'] = '';
        
        // check if content data is a file
        if ($content_data['content_file'] != '' 
            && is_file(DIR_FS_CATALOG.'media/content/'.$content_data['content_file'])
            )
        {
          $content_data['content_body'] = $content_data['content_text'];
          unset($content_data['content_text']);
          ob_start();      
          include (DIR_FS_DOCUMENT_ROOT.'media/content/'.$content_data['content_file']);      
          $content_data['content_text'] = @ob_get_contents();
          ob_end_clean();
          //check for txt file and format output
          if (strpos($content_data['content_file'], '.txt') !== false) {
            $content_data['content_text'] = '<pre>' . $content_data['content_text'] . '</pre>';
          }
        }
    
        //new module support
        $content_data_array[$language_id][$customers_status][$status][(int)$coID] = $this->mainModules->getContentData($content_data, $coID, $language_id, $customers_status, $get_inactive, $add_select);
      }
    }
    
    return $content_data_array[$language_id][$customers_status][$status][(int)$coID];    
  }

  /**
   * getVPEtext
   *
   * @param unknown_type $product
   * @param unknown_type $price
   * @return unknown
   */
  function getVPEtext($products, $price) {
    global $xtPrice, $product;
    
    if (!is_array($products)) {
      $products = $product->data;
    }
    
    $vpeText = '';
    $this->vpe_name = '';
    
    if (isset($products['products_vpe_status']) 
        && $products['products_vpe_status'] == 1 
        && $products['products_vpe_value'] != 0.0 
        && $price > 0
        )
    {
      // include needed function
      require_once (DIR_FS_INC.'xtc_get_vpe_name.inc.php');
      
      $this->vpe_name = xtc_get_vpe_name($products['products_vpe']);
      $vpeText = $xtPrice->xtcFormatCurrency(($price * (1 / $products['products_vpe_value'])), 0, true).TXT_PER.$this->vpe_name;
    }
    
    //new module support
    $vpeText = $this->mainModules->getVPEtext($vpeText, $products, $price, $this->vpe_name);
    
    return $vpeText;
  }

  /**
   * getProductPopupLink
   *
   * @param integer $pID
   * @param string $text, $class
   * @return string
   */
  function getProductPopupLink($pID, $text, $class = '', $add_params = '') {
    global $product, $request_type;
    
    $popup_params = $this->getPopupParams();    
    
    if ($class == 'image') {
      if ($text == '') {
        require_once (DIR_FS_INC . 'xtc_get_products_image.inc.php');
        $text = xtc_get_products_image($pID);
      }
      if (!isset($product) || !is_object($product)) {
        $product = new product();
      }
      $products_image = $product->productImage($text, 'thumbnail');      
      $productPopupLink = '<a target="_blank" title="'.$popup_params['link_title'].'" href="'.xtc_href_link('print_product_info.php', 'pID='.$pID.$popup_params['link_parameters'], $request_type).'" class="'.$popup_params['link_class'].'"><img class="'.$class.'" alt="" src="'.$products_image.'" /></a>';
    } else {
      $productPopupLink = '<a target="_blank" title="'.$popup_params['link_title'].'" href="'.xtc_href_link('print_product_info.php', 'pID='.$pID.$popup_params['link_parameters'].$add_params, $request_type).'" class="'.$popup_params['link_class'].' '.$class.'">'.$text.'</a>';
    }
    
    //new module support
    $productPopupLink = $this->mainModules->getProductPopupLink($productPopupLink, $pID, $text, $class, $add_params);
    
    return $productPopupLink;
  }

  /**
   * getDeliveryDutyInfo
   *
   * @param string $iso2code
   * @return boolean, string
   */
  function getDeliveryDutyInfo($iso2code) {
    $countries = array();
    $geo_zone_array = array();

    $geo_zone_query = xtDBquery("SELECT geo_zone_id 
                                   FROM ".TABLE_GEO_ZONES." 
                                  WHERE geo_zone_info = '1'");
    if (xtc_db_num_rows($geo_zone_query, true) > 0) {
      while ($geo_zone = xtc_db_fetch_array($geo_zone_query, true)) {
        $geo_zone_array[] = $geo_zone['geo_zone_id'];
      }
      $duty_countries_query = xtDBquery("SELECT c.countries_iso_code_2
                                           FROM ".TABLE_COUNTRIES." c
                                           JOIN " . TABLE_ZONES_TO_GEO_ZONES . " gz ON c.countries_id = gz.zone_country_id
                                          WHERE gz.geo_zone_id IN ('".implode("', '", $geo_zone_array)."')");
      if (xtc_db_num_rows($duty_countries_query, true)) {
        while ($duty_countries = xtc_db_fetch_array($duty_countries_query, true)) {
          $countries[] = $duty_countries['countries_iso_code_2'];
        }
      }
    }
    
    if (in_array($iso2code, $countries)) {
      return true;
    }
    
    return false;
  }
  
  /**
   * get all attributes information
   *
   * @param integer $products_id
   * @param integer $option_id
   * @param integer $value_id
   * @param string $add_select
   * @param string $left_join
   *
   * @return array
   */
  function getAttributes($products_id, $option_id, $value_id, $add_select = '', $left_join = '') {
    
    //new module support
    $paramsArr = $paramsArrOrigin = array($products_id, $option_id, $value_id, $add_select, $left_join);
    list($products_id, $option_id, $value_id, $add_select, $left_join) = $this->mainModules->getAttributes($paramsArr,$paramsArrOrigin);

    $attributes = xtc_db_query("SELECT ".$add_select."
                                       popt.products_options_name,
                                       poval.products_options_values_name,
                                       pa.*
                                  FROM ".TABLE_PRODUCTS_ATTRIBUTES." pa
                                  JOIN ".TABLE_PRODUCTS_OPTIONS." popt
                                       ON popt.products_options_id = pa.options_id
                                          AND popt.language_id = '".(int) $_SESSION['languages_id']."'
                                  JOIN ".TABLE_PRODUCTS_OPTIONS_VALUES." poval
                                       ON poval.products_options_values_id = pa.options_values_id
                                          AND poval.language_id = '".(int) $_SESSION['languages_id']."'
                                       ".$left_join."
                                 WHERE pa.products_id = '".(int)$products_id."'
                                   AND pa.options_id = '".(int)$option_id."'
                                   AND pa.options_values_id = '".(int)$value_id."'");
    
    $attributes = xtc_db_fetch_array($attributes);
    
    //new module support
    $attributes = $this->mainModules->getAttributesSelect($attributes,$paramsArr,$paramsArrOrigin);
    
    return $attributes;  
  }
  
  /**
   * get image
   *
   * @param string $image
   * @param string $dir
   * @param string $check
   * @param string $noImg
   *
   * @return string
   */
  function getImage($image, $dir = 'categories/', $check = CATEGORIES_IMAGE_SHOW_NO_IMAGE, $noImg = 'noimage.gif') {

    $imageOrigin = $image;
    
    if ($image != '') {
      $image = DIR_WS_IMAGES . $dir . $image;
    }   

    if (defined('IMAGE_TYPE_EXTENSION') && IMAGE_TYPE_EXTENSION != 'default') {
      $image_extension = substr($image, 0, strrpos($image, '.')).'.'.IMAGE_TYPE_EXTENSION;
      if (is_file($image_extension)) {
        $image = $image_extension;
      }
    }

    if (!is_file(DIR_FS_CATALOG.$image)) {
      $image = (($check == 'true') ? DIR_WS_IMAGES . $dir . $noImg : '');
    }
    
    //new module support
    $image = $this->mainModules->getImage($image, $dir, $check, $noImg, $imageOrigin);
    
    return $image;
  }
}
