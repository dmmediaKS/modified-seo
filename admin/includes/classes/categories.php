<?php
/* --------------------------------------------------------------
  $Id: categories.php 16179 2024-10-16 11:46:14Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(categories.php,v 1.140 2003/03/24); www.oscommerce.com
   (c) 2003  nextcommerce (categories.php,v 1.37 2003/08/18); www.nextcommerce.org
   (c) 2006 XT-Commerce (categories.php 1318 2005-10-21)

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contribution:
   Enable_Disable_Categories 1.3               Autor: Mikel Williams | mikel@ladykatcostumes.com
   New Attribute Manager v4b                   Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com
   Category Descriptions (Version: 1.5 MS2)    Original Author:   Brian Lowe <blowe@wpcusrgrp.org> | Editor: Lord Illicious <shaolin-venoms@illicious.net>
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   --------------------------------------------------------------*/

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

//Neue Zusatzfelder koennen hier definiert werden: admin/includes/add_db_fields.php
defined('ADD_PRODUCTS_FIELDS') or require_once (DIR_WS_INCLUDES.'add_db_fields.php');

// holds functions for manipulating products & categories
class categories {

  var $catModules;
  var $images_type_array;
  var $page_parameter;
  var $page_parameter_plain;
  var $dup_products_id;
  
  
  //new module support
  function __construct() {
    require_once (DIR_WS_CLASSES.'categoriesModules.class.php');
    $this->catModules = new categoriesModules();

    $this->images_type_array = array(
      '',
      '_list',
      '_mobile'
    );
  }

  // deletes an array of categories, with products
  // makes use of remove_category, remove_product
  function remove_categories($category_id) {
    $categories = xtc_get_category_tree($category_id, '', '0', '', true);
    $products = array();
    $products_delete = array();
    for ($i = 0, $n = sizeof($categories); $i < $n; $i ++) {
      $product_ids_query = xtc_db_query("SELECT products_id
                                           FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
                                          WHERE categories_id = '".$categories[$i]['id']."'");
      while ($product_ids = xtc_db_fetch_array($product_ids_query)) {
        $products[$product_ids['products_id']]['categories'][] = $categories[$i]['id'];
      }
    }
    foreach ($products as $key => $value) {
      $check_query = xtc_db_query("SELECT COUNT(*) AS total
                                     FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
                                    WHERE products_id = '".$key."'
                                      AND categories_id NOT IN ('".implode("', '", $value['categories'])."')");
      $check = xtc_db_fetch_array($check_query);
      if ($check['total'] < '1') {
        $products_delete[$key] = $key;
      }
    }
    // Removing categories can be a lengthy process
    xtc_set_time_limit(0);
    for ($i = 0, $n = sizeof($categories); $i < $n; $i ++) {
      $this->remove_category($categories[$i]['id']);
    }
    foreach ($products_delete as $key) {
      $this->remove_product($key);
    }
  }

  // deletes a single category, without products
  function remove_category($category_id) {
    $category_image_query = xtc_db_query("SELECT *
                                            FROM ".TABLE_CATEGORIES." 
                                           WHERE categories_id = '".xtc_db_input($category_id)."'");
    $category_image = xtc_db_fetch_array($category_image_query);

    foreach ($this->images_type_array as $image_type) {
      $duplicate_image_query = xtc_db_query("SELECT count(*) AS total 
                                               FROM ".TABLE_CATEGORIES." 
                                              WHERE categories_image".$image_type." = '".xtc_db_input($category_image['categories_image'.$image_type])."'");
      $duplicate_image = xtc_db_fetch_array($duplicate_image_query);

      if ($duplicate_image['total'] < 2) {
        if (is_file(DIR_FS_CATALOG_IMAGES.'categories/'.$category_image['categories_image'.$image_type])) {
          unlink(DIR_FS_CATALOG_IMAGES.'categories/'.$category_image['categories_image'.$image_type]);
        }
        if (is_file(DIR_FS_CATALOG_IMAGES.'categories/original_images/'.$category_image['categories_image'.$image_type])) {
          unlink(DIR_FS_CATALOG_IMAGES.'categories/original_images/'.$category_image['categories_image'.$image_type]);
        }
        
        $this->catModules->delete_category_image($category_image['categories_image'.$image_type]);
      }
    }

    //new module support
    $this->catModules->remove_category($category_id);

    xtc_db_query("DELETE FROM ".TABLE_CATEGORIES." WHERE categories_id = '".xtc_db_input($category_id)."'");
    xtc_db_query("DELETE FROM ".TABLE_CATEGORIES_DESCRIPTION." WHERE categories_id = '".xtc_db_input($category_id)."'");
    xtc_db_query("DELETE FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE categories_id = '".xtc_db_input($category_id)."'");
  }


  // inserts / updates a category from given $categories_data array
  // Needed fields: id, sort_order, status, array(groups), products_sorting, products_sorting2, category_template,
  // listing_template, previous_image, array[name][lang_id], array[heading_title][lang_id], array[description][lang_id],
  // array[meta_title][lang_id], array[meta_description][lang_id], array[meta_keywords][lang_id]
  function insert_category($categories_data, $dest_category_id, $action = 'insert') {
    $categories_id = (int)((isset($categories_data['categories_id'])) ? $categories_data['categories_id'] : 0);

    $permission = array();
    $customers_statuses_array = xtc_get_customers_statuses();
    foreach ($customers_statuses_array as $customers_status) {
      $permission[$customers_status['id']] = 0;
    }
    
    if (isset ($categories_data['groups'])) {
      foreach ($categories_data['groups'] as $index => $status_id) {
        $permission[$status_id] = 1;
      }
    }
    
    if (isset($permission['all']) && $permission['all'] == 1) {
      foreach ($permission as $status_id => $status) {
        $permission[$status_id] = 1;
      }
      unset($permission['all']);
    }

    $sql_data_array = array(
      'sort_order' => xtc_db_prepare_input($categories_data['sort_order']),
      'categories_status' => xtc_db_prepare_input($categories_data['status']),
      'products_sorting' => xtc_db_prepare_input($categories_data['products_sorting']),
      'products_sorting2' => xtc_db_prepare_input($categories_data['products_sorting2']),
      'categories_template' => xtc_db_prepare_input($categories_data['categories_template']),
      'listing_template' => xtc_db_prepare_input($categories_data['listing_template'])
    );

    if (trim(ADD_CATEGORIES_FIELDS) != '') {
      $sql_data_array = array_merge($sql_data_array, $this->add_data_fields(ADD_CATEGORIES_FIELDS,$categories_data));
    }
    
    $permission_array = array();
    foreach ($customers_statuses_array as $customers_status) {
      if (isset($permission[$customers_status['id']])) {
        $sql_data_array['group_permission_'.$customers_status['id']] = $permission[$customers_status['id']];
        $permission_array['group_permission_'.$customers_status['id']] = $permission[$customers_status['id']];
      }
    }

    //new module support
    $sql_data_array = $this->catModules->insert_category_before($sql_data_array,$categories_data);//Return parameter must be in first place
    
    if ($action == 'insert') {
      $insert_sql_data = array(
        'parent_id' => $dest_category_id, 
        'date_added' => 'now()'
      );
      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
      xtc_db_perform(TABLE_CATEGORIES, $sql_data_array);
      $categories_id = xtc_db_insert_id();
    } elseif ($action == 'update') {
      $update_sql_data = array('last_modified' => 'now()');
      $sql_data_array = array_merge($sql_data_array, $update_sql_data);
      xtc_db_perform(TABLE_CATEGORIES, $sql_data_array, 'update', "categories_id = '".$categories_id."'");
    }

    if (isset($categories_data['set_groups_permissions']) && $categories_data['set_groups_permissions'] != 0) {
      $this->xtc_set_groups($categories_id, $permission_array);
    }
    
    //new module support
    $this->catModules->insert_category_after($categories_data,$categories_id);
    
    $languages = xtc_get_languages();
    foreach ($languages AS $lang) {
      if (isset($categories_data['name'])) $categories_name_array = $categories_data['name'];
      $sql_data_array = array(
        'categories_name' => xtc_db_prepare_input($categories_data['categories_name'][$lang['id']]),
        'categories_heading_title' => xtc_db_prepare_input($categories_data['categories_heading_title'][$lang['id']]),
        'categories_description' => xtc_db_prepare_input($categories_data['categories_description'][$lang['id']]),
        'categories_short_description' => xtc_db_prepare_input($categories_data['categories_short_description'][$lang['id']]),
        'categories_meta_title' => xtc_db_prepare_input($categories_data['categories_meta_title'][$lang['id']]),
        'categories_meta_description' => xtc_db_prepare_input($categories_data['categories_meta_description'][$lang['id']]),
        'categories_meta_keywords' => xtc_db_prepare_input($categories_data['categories_meta_keywords'][$lang['id']])
      );

      if (trim(ADD_CATEGORIES_DESCRIPTION_FIELDS) != '') {
        $sql_data_array = array_merge($sql_data_array, $this->add_data_fields(ADD_CATEGORIES_DESCRIPTION_FIELDS,$categories_data,$lang['id']));
      }

      //new module support
      $sql_data_array = $this->catModules->insert_category_desc($sql_data_array,$categories_data,$categories_id,$lang['id']);
      
      if ($action == 'insert') {
        $insert_sql_data = array(
          'categories_id' => $categories_id, 
          'language_id' => $lang['id']
        );
        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
        xtc_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);
      } elseif ($action == 'update') {
        $category_query = xtc_db_query("SELECT * 
                                          FROM ".TABLE_CATEGORIES_DESCRIPTION." 
                                         WHERE language_id = '".$lang['id']."' 
                                           AND categories_id = '".$categories_id."'");
        if (xtc_db_num_rows($category_query) == 0) {
          xtc_db_perform(TABLE_CATEGORIES_DESCRIPTION, array('categories_id' => $categories_id, 'language_id' => $lang['id']));
        }
        xtc_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array, 'update', "categories_id = '".$categories_id."' AND language_id = '".$lang['id']."'");
      }
    }
    
    require(DIR_WS_INCLUDES.'upload_types.php');
    
    foreach ($this->images_type_array as $image_type) {
      //are we asked to delete some pics?
      if (isset($categories_data['del_cat_pic'.$image_type]) && $categories_data['del_cat_pic'.$image_type] == 'yes') {
        if (is_file(DIR_FS_CATALOG_IMAGES.'categories/'.$categories_data['categories_previous_image'.$image_type])) {
          unlink(DIR_FS_CATALOG_IMAGES.'categories/'.$categories_data['categories_previous_image'.$image_type]);
        }
        if (is_file(DIR_FS_CATALOG_IMAGES.'categories/original_images/'.$categories_data['categories_previous_image'.$image_type])) {
          unlink(DIR_FS_CATALOG_IMAGES.'categories/original_images/'.$categories_data['categories_previous_image'.$image_type]);
        }
      
        $this->catModules->delete_category_image($categories_data['categories_previous_image'.$image_type]);
      
        xtc_db_query("UPDATE ".TABLE_CATEGORIES."
                         SET categories_image".$image_type." = ''
                       WHERE categories_id = '".(int) $categories_id."'");
      }
    
      if ($categories_image = xtc_try_upload('categories_image'.$image_type, DIR_FS_CATALOG_IMAGES.'categories/original_images/', '777', $accepted_image_extensions, $accepted_image_mime_types)) {
        $cname_arr = explode('.', $categories_image->filename);
        $cnsuffix = array_pop($cname_arr);
        $categories_image_name = $categories_image_name_process = $this->image_name($categories_id, substr($image_type, 1), $cnsuffix, $cname_arr, false, $categories_data);
        rename(DIR_FS_CATALOG_IMAGES.'categories/original_images/'.$categories_image->filename, DIR_FS_CATALOG_IMAGES.'categories/original_images/'.$categories_image_name);
        xtc_db_query("UPDATE ".TABLE_CATEGORIES."
                         SET categories_image".$image_type." = '".xtc_db_input($categories_image_name)."'
                       WHERE categories_id = '".(int) $categories_id."'");
                     
        //image processing
        require(DIR_WS_INCLUDES.'categories_image'.$image_type.'.php');
        
        //image chmod
        chmod(DIR_FS_CATALOG_IMAGES.'categories/original_images/'.$categories_image_name, 0644);

        //categories image processing
        $this->catModules->categories_image_process($categories_image_name, $image_type);
      }
    }
   
    return $categories_id;
  }


  function set_category_recursive($categories_id, $status = "0") {
    // set status of category
    xtc_db_query("UPDATE ".TABLE_CATEGORIES." 
                     SET categories_status = '".$status."' 
                   WHERE categories_id = '".$categories_id."'");
                   
    // look for deeper categories and go rekursiv
    $categories_query = xtc_db_query("SELECT categories_id 
                                        FROM ".TABLE_CATEGORIES." 
                                       WHERE parent_id='".$categories_id."'");
    while ($categories = xtc_db_fetch_array($categories_query)) {
      $this->set_category_recursive($categories['categories_id'], $status);
    }
  }


  function xtc_set_groups($categories_id, $permission_array) {
    // get products in categorie
    $products_query = xtc_db_query("SELECT products_id
                                      FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
                                     WHERE categories_id='".(int)$categories_id."'");
    while ($products = xtc_db_fetch_array($products_query)) {
      xtc_db_perform(TABLE_PRODUCTS, $permission_array, 'update', "products_id = '".$products['products_id']."'");
    }
    
    // set status of categorie
    xtc_db_perform(TABLE_CATEGORIES, $permission_array, 'update', "categories_id = '".(int)$categories_id."'");
    
    // look for deeper categories and go rekursiv
    $categories_query = xtc_db_query("SELECT categories_id
                                        FROM ".TABLE_CATEGORIES."
                                       WHERE parent_id='".(int)$categories_id."'");
    while ($categories = xtc_db_fetch_array($categories_query)) {
      $this->xtc_set_groups($categories['categories_id'], $permission_array);
    }
  }

  // moves a category to new parent category
  function move_category($src_category_id, $dest_category_id) {
    $src_category_id = xtc_db_prepare_input($src_category_id);
    $dest_category_id = xtc_db_prepare_input($dest_category_id);
    $sql_data_array = array(
      'parent_id' => $dest_category_id,
      'last_modified' => 'now()'
    );
    xtc_db_perform(TABLE_CATEGORIES, $sql_data_array, 'update', "categories_id = '".(int)$src_category_id."'");         

    $this->catModules->move_category($src_category_id, $dest_category_id);
  }


  // copies a category to new parent category, takes argument to link or duplicate its products
  // arguments are "link" or "duplicate"
  // $copied is an array of ID's that were already newly created, and is used to prevent them from being
  // copied recursively again
  function copy_category($src_category_id, $dest_category_id, $ctype = "link") {
    //skip category if it is already a copied one
    if (!(in_array($src_category_id, $_SESSION['copied']))) {
      $src_category_id = (int)$src_category_id;
      $dest_category_id = (int)$dest_category_id;

      //get data
      $ccopy_query = xtc_db_query("SELECT * 
                                     FROM ".TABLE_CATEGORIES." 
                                    WHERE categories_id = '".$src_category_id."'");
      $ccopy_values = xtc_db_fetch_array($ccopy_query);

      //copy data (overrides)
      $sql_data_array = $ccopy_values;

      //new module support
      $sql_data_array = $this->catModules->copy_category($sql_data_array,$src_category_id,$dest_category_id,$ctype);
  
      //set new data
      unset($sql_data_array['categories_id']);
      $sql_data_array['parent_id'] = $dest_category_id;
      $sql_data_array['date_added'] = 'NOW()';
      $sql_data_array['last_modified'] = 'NOW()';
      //get customers statuses and set group_permissions
      //not needed, because group_permissions are in $sql_data_array

      //write data to DB
      xtc_db_perform(TABLE_CATEGORIES, $sql_data_array);

      //get new cat id
      $new_cat_id = xtc_db_insert_id();

      //store copied ids, because we don't want to go into an endless loop later
      $_SESSION['copied'][] = $new_cat_id;

      //copy / link products
      $get_prod_query = xtc_db_query("SELECT products_id FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE categories_id = '".$src_category_id."'");
      while ($product = xtc_db_fetch_array($get_prod_query)) {
        if ($ctype == 'link') {
          $this->link_product($product['products_id'], $new_cat_id);
        } elseif ($ctype == 'duplicate') {
          $this->duplicate_product($product['products_id'], $new_cat_id);
        } else {
          die('Undefined copy type!');
        }
      }

      foreach ($this->images_type_array as $image_type) {
        //copy+rename image
        $src_pic = DIR_FS_CATALOG_IMAGES.'categories/'.$ccopy_values['categories_image'.$image_type];
        $src_orig_pic = DIR_FS_CATALOG_IMAGES.'categories/original_images/'.$ccopy_values['categories_image'.$image_type];
        if (is_file($src_pic)) {
          $get_suffix = explode('.', $ccopy_values['categories_image'.$image_type]);
          $suffix = array_pop($get_suffix);
          $dest_pic = $this->image_name($new_cat_id, substr($image_type, 1), $suffix, $get_suffix, $src_category_id, $ccopy_values);

          copy($src_pic, DIR_FS_CATALOG_IMAGES.'categories/'.$dest_pic);
          chmod(DIR_FS_CATALOG_IMAGES.'categories/'.$dest_pic, 0644);
          
          if (is_file($src_orig_pic)) {
            copy($src_orig_pic, DIR_FS_CATALOG_IMAGES.'categories/original_images/'.$dest_pic);
            chmod(DIR_FS_CATALOG_IMAGES.'categories/original_images/'.$dest_pic, 0644);
          }

          $this->catModules->copy_category_image($src_pic, $dest_pic);

          //write to DB
          xtc_db_query("UPDATE ".TABLE_CATEGORIES." 
                           SET categories_image".$image_type." = '".xtc_db_input($dest_pic)."' 
                         WHERE categories_id = '".$new_cat_id."'");
        }
      }
    
      //get descriptions
      $languages = xtc_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {        
        $cdcopy_query = xtc_db_query("SELECT * 
                                        FROM ".TABLE_CATEGORIES_DESCRIPTION." 
                                       WHERE categories_id = '".$src_category_id."'
                                         AND language_id = '".$languages[$i]['id']."'");

        //copy descriptions
        while ($cdcopy_values = xtc_db_fetch_array($cdcopy_query)) {
          $sql_data_array = $cdcopy_values;
          //new module support
          $sql_data_array = $this->catModules->copy_category_desc($sql_data_array,$src_category_id,$dest_category_id,$ctype,$new_cat_id);
          //set new descriptions (overrides)
          $sql_data_array['categories_id'] = $new_cat_id;
          //write descriptions to DB
          xtc_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);
        }
      }
      
      //get child categories of current category
      $crcopy_query = xtc_db_query("SELECT categories_id 
                                      FROM ".TABLE_CATEGORIES." 
                                     WHERE parent_id = '".$src_category_id."'");

      //and go recursive
      while ($crcopy_values = xtc_db_fetch_array($crcopy_query)) {
        $this->copy_category($crcopy_values['categories_id'], $new_cat_id, $ctype);
      }
    }
  }


  // removes a product + images + more images + content
  function remove_product($product_id) {
    // get content of product
    $product_content_query = xtc_db_query("SELECT content_file 
                                             FROM ".TABLE_PRODUCTS_CONTENT." 
                                            WHERE products_id = '".(int)$product_id."'");
    // check if used elsewhere, delete db-entry + file if not
    while ($product_content = xtc_db_fetch_array($product_content_query)) {
       $duplicate_content_query = xtc_db_query("SELECT count(*) AS total 
                                                  FROM ".TABLE_PRODUCTS_CONTENT." 
                                                 WHERE content_file = '".xtc_db_input($product_content['content_file'])."' 
                                                   AND products_id != '".(int)$product_id."'");
       $duplicate_content = xtc_db_fetch_array($duplicate_content_query);
       if ($duplicate_content['total'] == 0) {
         if (is_file(DIR_FS_DOCUMENT_ROOT.'media/products/'.$product_content['content_file'])) {
           unlink(DIR_FS_DOCUMENT_ROOT.'media/products/'.$product_content['content_file']);
         }
       }
       //delete DB-Entry
       xtc_db_query("DELETE FROM ".TABLE_PRODUCTS_CONTENT." WHERE products_id = '".(int)$product_id."' AND (content_file = '".xtc_db_input($product_content['content_file'])."' OR content_file = '')");
    }

    //delete products images
    $product_image_query = xtc_db_query("SELECT products_image 
                                           FROM ".TABLE_PRODUCTS." 
                                          WHERE products_id = '".(int)$product_id."'
                                            AND products_image != ''");
    if (xtc_db_num_rows($product_image_query) > 0) {
      $product_image = xtc_db_fetch_array($product_image_query);

      $duplicate_image_query = xtc_db_query("SELECT count(*) AS total 
                                               FROM ".TABLE_PRODUCTS." 
                                              WHERE products_image = '".xtc_db_input($product_image['products_image'])."'");
      $duplicate_image = xtc_db_fetch_array($duplicate_image_query);
      if ($duplicate_image['total'] < 2) {
        xtc_del_image_file($product_image['products_image']);
      }
    }

    //delete more images
    $mo_images_query = xtc_db_query("SELECT * 
                                       FROM ".TABLE_PRODUCTS_IMAGES." 
                                      WHERE products_id = '".(int)$product_id."'");
    while ($mo_images_values = xtc_db_fetch_array($mo_images_query)) {
      $duplicate_more_image_query = xtc_db_query("SELECT count(*) AS total 
                                                    FROM ".TABLE_PRODUCTS_IMAGES." 
                                                   WHERE image_name = '".xtc_db_input($mo_images_values['image_name'])."'");
      $duplicate_more_image = xtc_db_fetch_array($duplicate_more_image_query);
      if ($duplicate_more_image['total'] < 2) {
        xtc_del_image_file($mo_images_values['image_name']);
      }

      xtc_db_query("DELETE FROM ".TABLE_PRODUCTS_IMAGES_DESCRIPTION." WHERE image_id = '".(int)$mo_images_values['image_id']."'");
    }

    //new module support
    $this->catModules->remove_product($product_id);

    xtc_db_query("DELETE FROM ".TABLE_PRODUCTS." WHERE products_id = '".(int)$product_id."'");
    xtc_db_query("DELETE FROM ".TABLE_PRODUCTS_XSELL." WHERE products_id = '".(int)$product_id."'");
    xtc_db_query("DELETE FROM ".TABLE_PRODUCTS_XSELL." WHERE xsell_id = '".(int)$product_id."'");
    xtc_db_query("DELETE FROM ".TABLE_PRODUCTS_IMAGES." WHERE products_id = '".(int)$product_id."'");
    xtc_db_query("DELETE FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE products_id = '".(int)$product_id."'");
    xtc_db_query("DELETE FROM ".TABLE_PRODUCTS_DESCRIPTION." WHERE products_id = '".(int)$product_id."'");
    xtc_db_query("DELETE FROM ".TABLE_SPECIALS." WHERE products_id = '".(int)$product_id."'");
    
    xtc_db_query("DELETE pad 
                    FROM ".TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD." pad
                    JOIN ".TABLE_PRODUCTS_ATTRIBUTES." pa 
                         ON pa.products_attributes_id = pad.products_attributes_id
                            AND pa.products_id = '".(int)$product_id."'");
    xtc_db_query("DELETE FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE products_id = '".(int)$product_id."'");
    xtc_db_query("DELETE FROM ".TABLE_PRODUCTS_TAGS." WHERE products_id = '".(int)$product_id."'");

    xtc_db_query("DELETE rd
                    FROM ".TABLE_REVIEWS_DESCRIPTION." rd
                    JOIN ".TABLE_REVIEWS." r
                         ON r.reviews_id = rd.reviews_id
                            AND r.products_id = '".(int)$product_id."'");
    xtc_db_query("DELETE FROM ".TABLE_REVIEWS." WHERE products_id = '".(int)$product_id."'");
    
    xtc_db_query("DELETE FROM ".TABLE_CUSTOMERS_BASKET." WHERE products_id = '" . (int)$product_id . "' OR products_id LIKE '" . (int)$product_id . "{%'");
    xtc_db_query("DELETE FROM ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES." WHERE products_id = '" . (int)$product_id . "' OR products_id LIKE '" . (int)$product_id . "{%'");

    if (defined('MODULE_WISHLIST_SYSTEM_STATUS') && MODULE_WISHLIST_SYSTEM_STATUS == 'true') {
      xtc_db_query("DELETE FROM ".TABLE_CUSTOMERS_WISHLIST." WHERE products_id = '" . (int)$product_id . "' OR products_id LIKE '" . (int)$product_id . "{%'");
      xtc_db_query("DELETE FROM ".TABLE_CUSTOMERS_WISHLIST_ATTRIBUTES." WHERE products_id = '" . (int)$product_id . "' OR products_id LIKE '" . (int)$product_id . "{%'");
    }

    $customers_statuses_array = xtc_get_customers_statuses();
    foreach ($customers_statuses_array as $customers_status) {
      xtc_db_query("DELETE FROM ".TABLE_PERSONAL_OFFERS_BY.$customers_status['id']." WHERE products_id = '".(int)$product_id."'");
    }
  }


  // deletes given product from categories, removes it completely if no category is left
  function delete_product($product_id, $product_categories) {
    for ($i = 0, $n = sizeof($product_categories); $i < $n; $i ++) {
      xtc_db_query("DELETE FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE products_id   = '".(int)$product_id."' AND categories_id = '".xtc_db_input($product_categories[$i])."'");
      if (($product_categories[$i]) == 0) {
        $this->set_product_startpage($product_id, 0);
      }
    }
    $product_categories_query = xtc_db_query("SELECT COUNT(*) AS total
                                                FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
                                               WHERE products_id = '".(int)$product_id."'");
    $product_categories = xtc_db_fetch_array($product_categories_query);

    if ($product_categories['total'] == '0') {
      $this->remove_product($product_id);
    }
    
    //new module support
    $this->catModules->delete_product($product_id, $product_categories);
  }


  function update_product($products_data) {
    global $messageStack;
    
    $products_id = xtc_db_prepare_input($products_data['products_id']);

    $prod_quantity_query = xtc_db_query("SELECT products_quantity 
                                           FROM ".TABLE_PRODUCTS." 
                                          WHERE products_id = '".$products_id."'");
    if (xtc_db_num_rows($prod_quantity_query) > 0) {
      $prod_quantity = xtc_db_fetch_array($prod_quantity_query);
      if ($prod_quantity['products_quantity'] != $products_data['products_quantity_before_edit']) {
        $messageStack->add_session(ERROR_QTY_SAVE_CHANGED, 'error');
      } else {
        xtc_db_query("UPDATE ".TABLE_PRODUCTS." 
                         SET products_quantity = '".xtc_db_input($products_data['products_quantity'])."' 
                       WHERE products_id = '".(int)$products_id."'");  
    
        if (xtc_db_affected_rows() > 0) {
          $messageStack->add_session(TEXT_STOCK_UPDATE_SUCCESS, 'success');
        } else {
          $messageStack->add_session(TEXT_STOCK_UPDATE_ERROR, 'error');
        }
      }

      //new module support
      $this->catModules->update_product($products_data, $products_id);
    }
    
    return array(
      'error' => true,
      'products_id' => $products_id
    );
  }


  // inserts / updates a product from given data
  function insert_product($products_data, $dest_category_id, $action = 'insert') {
    global $messageStack;
    
    $products_id = (int)((isset($products_data['products_id'])) ? $products_data['products_id'] : 0);
    $products_date_available = xtc_db_prepare_input($products_data['products_date_available']);
    $products_date_available = (date('Y-m-d H:i:00') < $products_date_available) ? $products_date_available : 'null';

    $products_tax_rate = xtc_get_tax_rate($products_data['products_tax_class_id']);
    $products_data['products_price'] = $this->priceCheck($products_data['products_price'], $products_tax_rate);
    
    $permission = array();
    $customers_statuses_array = xtc_get_customers_statuses();
    foreach ($customers_statuses_array as $customers_status) {
      $permission[$customers_status['id']] = 0;
    }
    
    if (isset ($products_data['groups'])) {
      foreach ($products_data['groups'] as $index => $status_id) {
        $permission[$status_id] = 1;
      }
    }
    
    if (isset($permission['all']) && $permission['all'] == 1) {
      foreach ($permission as $status_id => $status) {
        $permission[$status_id] = 1;
      }
      unset($permission['all']);
    }
        
    $sql_data_array = array(
      'products_quantity' => xtc_db_prepare_input($products_data['products_quantity']),
      'products_model' => xtc_db_prepare_input($products_data['products_model']),
      'products_ean' => xtc_db_prepare_input($products_data['products_ean']),
      'products_price' => xtc_db_prepare_input($products_data['products_price']),
      'products_sort' => xtc_db_prepare_input($products_data['products_sort']),
      'products_discount_allowed' => xtc_db_prepare_input($products_data['products_discount_allowed']),
      'products_date_available' => $products_date_available,
      'products_weight' => xtc_db_prepare_input($products_data['products_weight']),
      'products_status' => xtc_db_prepare_input($products_data['products_status']),
      'products_startpage' => xtc_db_prepare_input($products_data['products_startpage']),
      'products_startpage_sort' => xtc_db_prepare_input($products_data['products_startpage_sort']),
      'products_tax_class_id' => xtc_db_prepare_input($products_data['products_tax_class_id']),
      'product_template' => xtc_db_prepare_input($products_data['info_template']),
      'options_template' => xtc_db_prepare_input($products_data['options_template']),
      'manufacturers_id' => xtc_db_prepare_input($products_data['manufacturers_id']),
      'products_fsk18' => xtc_db_prepare_input($products_data['fsk18']),
      'products_vpe_value' => xtc_db_prepare_input($products_data['products_vpe_value']),
      'products_vpe_status' => xtc_db_prepare_input($products_data['products_vpe_status']),
      'products_vpe' => xtc_db_prepare_input($products_data['products_vpe']),
    );

    if (ACTIVATE_SHIPPING_STATUS == 'true') {
      $sql_data_array['products_shippingtime'] = xtc_db_prepare_input($products_data['shipping_status']);
    }
    
    if (trim(ADD_PRODUCTS_FIELDS) != '') {
      $sql_data_array = array_merge($sql_data_array, $this->add_data_fields(ADD_PRODUCTS_FIELDS,$products_data));
    }

    $error = false;
    $prod_quantity_query = xtc_db_query("SELECT products_quantity 
                                           FROM ".TABLE_PRODUCTS." 
                                          WHERE products_id = '".$products_id."'");
    if (xtc_db_num_rows($prod_quantity_query) > 0) {
      $prod_quantity = xtc_db_fetch_array($prod_quantity_query);
      if ($prod_quantity['products_quantity'] != $products_data['products_quantity_before_edit']) {
        unset($sql_data_array['products_quantity']);
        $error = true;
        $messageStack->add_session(ERROR_QTY_SAVE_CHANGED, 'error');
      }
    }
    
    foreach ($customers_statuses_array as $customers_status) {
      if (isset($permission[$customers_status['id']])) {
        $sql_data_array['group_permission_'.$customers_status['id']] = $permission[$customers_status['id']];
      }
    }

    //new module support
    $sql_data_array = $this->catModules->insert_product_before($sql_data_array,$products_data);
    
    if ($action == 'insert') {
      $insert_sql_data = array('products_date_added' => 'now()');
      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
      xtc_db_perform(TABLE_PRODUCTS, $sql_data_array);
      $products_id = xtc_db_insert_id();
      $products_data['products_id'] = $products_id;
      $sql_data_array = array(
        'products_id' => $products_id,
        'categories_id' => $dest_category_id
      );
      xtc_db_perform(TABLE_PRODUCTS_TO_CATEGORIES, $sql_data_array);                   
    } elseif ($action == 'update') {
      $update_sql_data = array('products_last_modified' => 'now()');
      $sql_data_array = array_merge($sql_data_array, $update_sql_data);
      xtc_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '".(int)$products_id."'");
    }
    
    if ($products_data['products_startpage'] == 1) {
      $this->link_product($products_id, 0);
    } else {
      $this->set_product_remove_startpage_sql($products_id, 0);
    }

    if (isset($products_data['products_geo_to_tax'])
        && is_array($products_data['products_geo_to_tax'])
        )
    {
      xtc_db_query("DELETE FROM ".TABLE_PRODUCTS_GEO_ZONES_TO_TAX_CLASS."
                          WHERE products_id = '".(int)$products_id."'");
                          
      foreach ($products_data['products_geo_to_tax'] as $geo_zone_id => $tax_class_id) {
        if ($tax_class_id != $products_data['products_tax_class_id']) {
          $sql_data_array = array(
            'products_id' => $products_id,
            'geo_zone_id' => $geo_zone_id,
            'tax_class_id' => $tax_class_id,
          );
          xtc_db_perform(TABLE_PRODUCTS_GEO_ZONES_TO_TAX_CLASS, $sql_data_array);
        }
      }
    }    
    
    //upload products image
    if ($products_image_name = $this->uploadImage($products_id,$products_data)) {
      if (xtc_not_null($products_image_name) && $products_image_name != 'none') {
        xtc_db_query("UPDATE ".TABLE_PRODUCTS." 
                         SET products_image = '".xtc_db_input($products_image_name)."' 
                       WHERE products_id = '".(int)$products_id."'");  
      }
    }

    //MO_PICS
    $this->uploadMoImages($products_id,$products_data,$action);

    //specials
    $this->saveSpecialsData($products_data);

    // calculate attribute_prices
    if (isset($products_data['products_attributes_recalculate']) 
        && $products_data['products_attributes_recalculate'] == 1 
        && $products_data['products_tax_class_id'] != $products_data['products_tax_class_id_old']
        ) 
    {
      $this->calculate_attribute_prices($products_data,$products_id);
    }

    // products tags
    if (isset($products_id) && $products_id > 0 && isset($products_data['products_tags_save'])) {
      $this->save_products_tags($products_data,$products_id);
    }

    //new module support 
    $this->catModules->insert_product_after($products_data,$products_id);
        
    // group and graduated prices
    foreach ($customers_statuses_array as $customers_status) {
      xtc_db_query("DELETE FROM ".TABLE_PERSONAL_OFFERS_BY.$customers_status['id']." WHERE products_id = '".$products_id."'");

      if (isset($products_data['products_price_'.$customers_status['id']])) {
        $personal_price = xtc_db_prepare_input($products_data['products_price_'.$customers_status['id']]);
        if ($personal_price == '' || $personal_price == '0.0000') {
          $personal_price = '0.00';
        }
        $personal_price = $this->priceCheck($personal_price, $products_tax_rate);
        
        
        if ($personal_price > 0) {
          $sql_data_array = array(
            'personal_offer' => $personal_price,
            'quantity' => '1',
            'products_id' => $products_id
          );
          xtc_db_perform(TABLE_PERSONAL_OFFERS_BY.$customers_status['id'], $sql_data_array);
        }
      }
      
      if (isset($products_data['products_staffel'][$customers_status['id']])) {
        foreach ($products_data['products_staffel'][$customers_status['id']] as $products_staffel) {
          if ($products_staffel['quantity'] > 1) {
            $staffelpreis = $products_staffel['personal_offer'];
            $staffelpreis = $this->priceCheck($staffelpreis, $products_tax_rate);
            
            if ($staffelpreis > 0
                && !isset($products_staffel['delete'])
                )
            {
              $sql_data_array = array(
                'personal_offer' => $staffelpreis,
                'quantity' => $products_staffel['quantity'],
                'products_id' => $products_id,
              );
              xtc_db_perform(TABLE_PERSONAL_OFFERS_BY.$customers_status['id'], $sql_data_array);            
            }
          }
        }        
      }
    }
       
    // description
    $languages = xtc_get_languages();
    foreach ($languages as $lang) {
      $language_id = $lang['id'];
      $sql_data_array = array(
        'products_name' => xtc_db_prepare_input($products_data['products_name'][$language_id]),
        'products_heading_title' => xtc_db_prepare_input($products_data['products_heading_title'][$language_id]),
        'products_description' => xtc_db_prepare_input($products_data['products_description'][$language_id]),
        'products_short_description' => xtc_db_prepare_input($products_data['products_short_description'][$language_id]),
        'products_keywords' => xtc_db_prepare_input($products_data['products_keywords'][$language_id]),
        'products_url' => xtc_db_prepare_input($products_data['products_url'][$language_id]),
        'products_meta_title' => xtc_db_prepare_input($products_data['products_meta_title'][$language_id]),
        'products_meta_description' => xtc_db_prepare_input($products_data['products_meta_description'][$language_id]),
        'products_meta_keywords' => xtc_db_prepare_input($products_data['products_meta_keywords'][$language_id])
      );
      if (trim(ADD_PRODUCTS_DESCRIPTION_FIELDS)) {
        $sql_data_array = array_merge($sql_data_array, $this->add_data_fields(ADD_PRODUCTS_DESCRIPTION_FIELDS,$products_data,$language_id));
      }
      
      //new module support
      $sql_data_array = $this->catModules->insert_product_desc($sql_data_array,$products_data,$products_id,$language_id);
   
      if ($action == 'insert') {
        $insert_sql_data = array(
          'products_id' => $products_id, 
          'language_id' => $language_id
        );
        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
        xtc_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
      } elseif ($action == 'update') {
        $product_query = xtc_db_query("SELECT * 
                                         FROM ".TABLE_PRODUCTS_DESCRIPTION."
                                        WHERE language_id = '".$lang['id']."'
                                          AND products_id = '".$products_id."'");
        if (xtc_db_num_rows($product_query) == 0) {
          xtc_db_perform(TABLE_PRODUCTS_DESCRIPTION, array('products_id' => $products_id, 'language_id' => $lang['id']));
        }
        xtc_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', "products_id = '".(int)$products_id."' and language_id = '".$language_id."'");
      }
    }
    
    //new module support 
    $this->catModules->insert_product_end($products_id);

    return array(
      'error' => $error,
      'products_id' => $products_id
    );
  }


  // duplicates a product by id into specified category by id
  function duplicate_product($src_products_id, $dest_categories_id) {
    $src_products_id = (int)$src_products_id;
    $dest_categories_id = (int)$dest_categories_id;

    //get data
    $product_query = xtc_db_query("SELECT * 
                                     FROM ".TABLE_PRODUCTS."
                                    WHERE products_id = '".$src_products_id."'");

    $product = xtc_db_fetch_array($product_query);
    
    //copy data
    $sql_data_array = $product;
    
    //new module support
    $sql_data_array = $this->catModules->duplicate_product_before($sql_data_array,$src_products_id,$dest_categories_id);
    
    //set new data (overrides)
    unset($sql_data_array['products_id']);
    $sql_data_array['products_date_added'] = 'now()';
    $sql_data_array['products_ordered'] = 0;

    //write data to DB
    xtc_db_perform(TABLE_PRODUCTS, $sql_data_array);

    //get duplicate id
    $this->dup_products_id = xtc_db_insert_id();

    //duplicate image if there is one
    if ($product['products_image'] != '') {
      //build new image_name for duplicate
      $pname_arr = explode('.', $product['products_image']);
      $nsuffix = array_pop($pname_arr);
      $dup_products_image_name = $this->image_name($this->dup_products_id, 0, $nsuffix, $pname_arr, $src_products_id, $product);
      
      //write to DB
      xtc_db_query("UPDATE ".TABLE_PRODUCTS." 
                       SET products_image = '".xtc_db_input($dup_products_image_name)."' 
                     WHERE products_id = '".$this->dup_products_id."'");
      
      //copy org images to duplicate
      copy(DIR_FS_CATALOG_ORIGINAL_IMAGES.$product['products_image'], DIR_FS_CATALOG_ORIGINAL_IMAGES.$dup_products_image_name);
      copy(DIR_FS_CATALOG_POPUP_IMAGES.$product['products_image'], DIR_FS_CATALOG_POPUP_IMAGES.$dup_products_image_name);
      copy(DIR_FS_CATALOG_INFO_IMAGES.$product['products_image'], DIR_FS_CATALOG_INFO_IMAGES.$dup_products_image_name);
      copy(DIR_FS_CATALOG_MIDI_IMAGES.$product['products_image'], DIR_FS_CATALOG_MIDI_IMAGES.$dup_products_image_name);
      copy(DIR_FS_CATALOG_THUMBNAIL_IMAGES.$product['products_image'], DIR_FS_CATALOG_THUMBNAIL_IMAGES.$dup_products_image_name);
      copy(DIR_FS_CATALOG_MINI_IMAGES.$product['products_image'], DIR_FS_CATALOG_MINI_IMAGES.$dup_products_image_name);
      $this->set_products_images_file_rights($dup_products_image_name);
    }

    //new module support
    $sql_data_array = $this->catModules->duplicate_product_after($sql_data_array,$src_products_id,$dest_categories_id,$this->dup_products_id);

    //get description data
    $languages = xtc_get_languages();
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {        
      $description_query = xtc_db_query("SELECT * 
                                           FROM ".TABLE_PRODUCTS_DESCRIPTION."
                                          WHERE products_id = '".$src_products_id."'
                                            AND language_id = '".$languages[$i]['id']."'");

      while ($description = xtc_db_fetch_array($description_query)) {
        //copy description data
        $sql_data_array = $description;
        //new module support
        $sql_data_array = $this->catModules->duplicate_product_desc($sql_data_array,$src_products_id,$dest_categories_id,$this->dup_products_id);
        //set description data (overrides)
        $sql_data_array['products_id'] = $this->dup_products_id;
        $sql_data_array['products_viewed'] = 0;
        //write description data to DB
        xtc_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
      }
    }
    
    $sql_data_array = array(
      'products_id' => $this->dup_products_id,
      'categories_id' => $dest_categories_id
    );
    xtc_db_perform(TABLE_PRODUCTS_TO_CATEGORIES, $sql_data_array);                   

    //mo_images
    $mo_images_query = xtc_db_query("SELECT *
                                       FROM ".TABLE_PRODUCTS_IMAGES."
                                      WHERE products_id = '".(int)$src_products_id."'");
    while ($mo_images = xtc_db_fetch_array($mo_images_query)) {     
      //build new image_name for duplicate
      $pname_arr = explode('.', $mo_images['image_name']);
      $nsuffix = array_pop($pname_arr);
      $dup_products_image_name = $this->image_name($this->dup_products_id, $mo_images['image_nr'], $nsuffix, $pname_arr, $src_products_id, $product);
      
      //copy org images to duplicate
      copy(DIR_FS_CATALOG_ORIGINAL_IMAGES.$mo_images['image_name'], DIR_FS_CATALOG_ORIGINAL_IMAGES.$dup_products_image_name);
      copy(DIR_FS_CATALOG_POPUP_IMAGES.$mo_images['image_name'], DIR_FS_CATALOG_POPUP_IMAGES.$dup_products_image_name);
      copy(DIR_FS_CATALOG_INFO_IMAGES.$mo_images['image_name'], DIR_FS_CATALOG_INFO_IMAGES.$dup_products_image_name);
      copy(DIR_FS_CATALOG_MIDI_IMAGES.$mo_images['image_name'], DIR_FS_CATALOG_MIDI_IMAGES.$dup_products_image_name);
      copy(DIR_FS_CATALOG_THUMBNAIL_IMAGES.$mo_images['image_name'], DIR_FS_CATALOG_THUMBNAIL_IMAGES.$dup_products_image_name);
      copy(DIR_FS_CATALOG_MINI_IMAGES.$mo_images['image_name'], DIR_FS_CATALOG_MINI_IMAGES.$dup_products_image_name);
      $this->set_products_images_file_rights($dup_products_image_name);
    
      //write to DB
      $sql_data_array = $mo_images;
      unset($sql_data_array['image_id']);
      $sql_data_array['products_id'] = $this->dup_products_id;
      $sql_data_array['image_name'] = $dup_products_image_name;

      xtc_db_perform(TABLE_PRODUCTS_IMAGES, $sql_data_array);
      $image_id = xtc_db_insert_id();

      $mo_images_desc_query = xtc_db_query("SELECT *
                                              FROM ".TABLE_PRODUCTS_IMAGES_DESCRIPTION."
                                             WHERE image_id = '".(int)$mo_images['image_id']."'");
      while ($mo_images_desc = xtc_db_fetch_array($mo_images_desc_query)) {
        $sql_data_array = $mo_images_desc;
        $sql_data_array['image_id'] = $image_id;
        $sql_data_array['products_id'] = $this->dup_products_id;
        
        xtc_db_perform(TABLE_PRODUCTS_IMAGES_DESCRIPTION, $sql_data_array);
      }
    }
    
    //dublicate group and graduated prices
    $customers_statuses_array = xtc_get_customers_statuses();
    foreach ($customers_statuses_array as $customers_status) {
      $copy_query = xtc_db_query("SELECT quantity,
                                         personal_offer
                                    FROM ".TABLE_PERSONAL_OFFERS_BY.$customers_status['id']."
                                   WHERE products_id = '".$src_products_id."'");
      while ($copy_data = xtc_db_fetch_array($copy_query)) {
        $sql_data_array = array(
          'products_id' => $this->dup_products_id,
          'quantity' => $copy_data['quantity'],
          'personal_offer' => $copy_data['personal_offer']
        );
        xtc_db_perform(TABLE_PERSONAL_OFFERS_BY.$customers_status['id'], $sql_data_array);                 
      }
    }
        
    //dublicate products attributes
    if (isset($_POST['attr_copy']) && $_POST['attr_copy'] == 'attr_copy') {
      $attribute_copy_query = xtc_db_query("SELECT *
                                              FROM ".TABLE_PRODUCTS_ATTRIBUTES."
                                             WHERE products_id = '".$src_products_id."'");
      while ($attribute_copy_data = xtc_db_fetch_array($attribute_copy_query)) {
        $sql_data_array = $attribute_copy_data;
        //set attributes data (overrides)
        unset($sql_data_array['products_attributes_id']);
        $sql_data_array['products_id'] = $this->dup_products_id;
        //write attributes data to DB
        xtc_db_perform(TABLE_PRODUCTS_ATTRIBUTES, $sql_data_array);
        $products_attributes_id = xtc_db_insert_id();
        
        $dl_copy_data_query = xtc_db_query("SELECT *
                                              FROM ".TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD."
                                             WHERE products_attributes_id = '".$attribute_copy_data['products_attributes_id']."'");
        while ($dl_copy_data = xtc_db_fetch_array($dl_copy_data_query)) {
          $sql_data_array = $dl_copy_data;

          unset($sql_data_array['products_attributes_id']);
          $sql_data_array['products_attributes_id'] = $products_attributes_id;
          xtc_db_perform(TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD, $sql_data_array);          
        }
      }
    }

    //dublicate products tags
    if (isset($_POST['tags_copy']) && $_POST['tags_copy'] == 'tags_copy') {
      $tags_copy_query = xtc_db_query("SELECT *
                                         FROM ".TABLE_PRODUCTS_TAGS."
                                        WHERE products_id = '".$src_products_id."'");
      while ($tags_copy_data = xtc_db_fetch_array($tags_copy_query)) {
        $sql_data_array = $tags_copy_data;
        unset($sql_data_array['products_tags_id']);
        $sql_data_array['products_id'] = $this->dup_products_id;
        //write tags data to DB
        xtc_db_perform(TABLE_PRODUCTS_TAGS, $sql_data_array);
      }
    }
    
    // duplicate products content and links, Timo Paul (mail[at]timopaul[dot]biz)
    if (isset($_POST['cnt_copy']) && $_POST['cnt_copy'] == 'cnt_copy') {
      $content_copy_query = xtc_db_query("SELECT * 
                                            FROM " . TABLE_PRODUCTS_CONTENT . "
                                           WHERE products_id = '".$src_products_id."'");
      while ($content_copy_data = xtc_db_fetch_array($content_copy_query)) {
        $sql_data_array = $content_copy_data;
        //set attributes data (overrides)
        unset($sql_data_array['content_id']);
        $sql_data_array['products_id'] = $this->dup_products_id;
        //write attributes data to DB
        xtc_db_perform(TABLE_PRODUCTS_CONTENT, $sql_data_array);
      }
    }
    
    if (isset($_POST['links_copy']) && $_POST['links_copy'] == 'links_copy') {
      $links_copy_query = xtc_db_query("SELECT * 
                                          FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
                                         WHERE products_id = '".$src_products_id."'
                                           AND categories_id != '".$dest_categories_id."'");
      while ($links_copy_data = xtc_db_fetch_array($links_copy_query)) {
        $sql_data_array = $links_copy_data;
        $sql_data_array['products_id'] = $this->dup_products_id;
        xtc_db_perform(TABLE_PRODUCTS_TO_CATEGORIES, $sql_data_array);
      }
    }

    //new module support 
    $this->catModules->duplicate_product_end($this->dup_products_id);
  }


  // links a product into specified category by id
  function link_product($src_products_id, $dest_categories_id) {
    global $messageStack;
    
    $src_products_id = (int)$src_products_id;
    $dest_categories_id = (int)$dest_categories_id;

    $check_query = xtc_db_query("SELECT COUNT(*) AS total
                                   FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
                                  WHERE products_id   = '".$src_products_id."'
                                    AND categories_id = '".$dest_categories_id."'");
    $check = xtc_db_fetch_array($check_query);
    if ($check['total'] < '1') {
      if ($dest_categories_id != 0) {
        $sql_data_array = array('products_id' => $src_products_id,
                                'categories_id' => $dest_categories_id);
        xtc_db_perform(TABLE_PRODUCTS_TO_CATEGORIES, $sql_data_array);                   
      }
      if ($dest_categories_id == 0) {
        $this->set_product_startpage($src_products_id, 1);
      }
    } elseif ($dest_categories_id != 0) {
      $messageStack->add_session(ERROR_CANNOT_LINK_TO_SAME_CATEGORY, 'error');
    }
  }


  // moves a product from category into specified category
  function move_product($src_products_id, $src_category_id, $dest_category_id) {
    $src_products_id = (int)$src_products_id;
    $dest_category_id = (int)$dest_category_id;
    $src_category_id = (int)$src_category_id;

    $duplicate_check_query = xtc_db_query("SELECT COUNT(*) AS total
                                             FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
                                            WHERE products_id = '".$src_products_id."'
                                              AND categories_id = '".$dest_category_id."'");
    $duplicate_check = xtc_db_fetch_array($duplicate_check_query);
    if ($duplicate_check['total'] < 1) {
      if ($dest_category_id != 0) {
        $sql_data_array = array(
          'products_id' => $src_products_id,
          'categories_id' => $dest_category_id
        );
        xtc_db_perform(TABLE_PRODUCTS_TO_CATEGORIES, $sql_data_array, 'update', "products_id = '".$src_products_id."' AND categories_id = '".$src_category_id."'");                   
      }
      if ($dest_category_id == 0) {
        $this->set_product_startpage($src_products_id, 1);
      }
    }
  }


  // Sets the status of a product
  function set_product_status($products_id, $status) {
    $sql_data_array = array(
      'products_status' => $status,
      'products_last_modified' => 'now()'
    );
    xtc_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '".$products_id."'");                       
  }


  // Sets a product active on startpage
  function set_product_startpage($products_id, $status) {
    $sql_data_array = array(
      'products_startpage' => $status,
      'products_last_modified' => 'now()'
    );
    xtc_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '".$products_id."'");                       
  }


  // Set a product remove on startpage
  function set_product_remove_startpage_sql($products_id, $status) {
    global $messageStack;

    if ($status == '0') {
      $check_query = xtc_db_query("SELECT COUNT(*) AS total
                                     FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
                                    WHERE products_id = '".$products_id."'
                                      AND categories_id != '0'");
      $check = xtc_db_fetch_array($check_query);
      if ($check['total'] >= '1') {
        xtc_db_query("DELETE FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE products_id = '".$products_id."' and categories_id = '0'");
      }
    }
  }


  // Counts how many products exist in a category
  function count_category_products($category_id, $include_deactivated = false) {
    $products_count = 0;
    
    $where = '';
    if ($include_deactivated) {
      $where = " WHERE p.products_status = '1'";
    }
    $products_query = xtc_db_query("SELECT count(*) as total 
                                      FROM ".TABLE_PRODUCTS." p
                                      JOIN ".TABLE_PRODUCTS_TO_CATEGORIES." p2c 
                                           ON p.products_id = p2c.products_id
                                              AND p2c.categories_id = '".(int)$category_id."'
                                           ".$where);
    $products = xtc_db_fetch_array($products_query);
    $products_count += $products['total'];
    $childs_query = xtc_db_query("SELECT categories_id 
                                    FROM ".TABLE_CATEGORIES." 
                                   WHERE parent_id = '".(int)$category_id."'");
    if (xtc_db_num_rows($childs_query)) {
      while ($childs = xtc_db_fetch_array($childs_query)) {
        $products_count += $this->count_category_products($childs['categories_id'], $include_deactivated);
      }
    }
    return $products_count;
  }


  // Counts how many subcategories exist in a category
  function count_category_childs($category_id) {
    $categories_count = 0;
    $categories_query = xtc_db_query("SELECT categories_id 
                                        FROM ".TABLE_CATEGORIES." 
                                       WHERE parent_id = '".(int)$category_id."'");
    while ($categories = xtc_db_fetch_array($categories_query)) {
      $categories_count ++;
      $categories_count += $this->count_category_childs($categories['categories_id']);
    }
    return $categories_count;
  }


  function edit_cross_sell($cross_data) {
    if (isset($cross_data['special']) && $cross_data['special'] == 'add_entries') {
      if (isset ($cross_data['ids'])) {
        foreach ($cross_data['ids'] AS $pID) {
          $sql_data_array = array(
            'products_id' => $cross_data['current_product_id'], 
            'xsell_id' => $pID,
            'products_xsell_grp_name_id' => $cross_data['group_name'][$pID]
          );
          // check if product is already linked
          $check_query = xtc_db_query("SELECT * 
                                         FROM ".TABLE_PRODUCTS_XSELL." 
                                        WHERE products_id = '".$cross_data['current_product_id']."' 
                                          AND xsell_id = '".$pID."'");
          if (xtc_db_num_rows($check_query) < 1) {
            xtc_db_perform(TABLE_PRODUCTS_XSELL, $sql_data_array);
          }
        }
      }
    }
    
    if (isset($cross_data['special']) && $cross_data['special'] == 'edit') {
      if (isset ($cross_data['ids'])) {
        // delete
        foreach ($cross_data['ids'] AS $pID) {
          xtc_db_query("DELETE FROM ".TABLE_PRODUCTS_XSELL." WHERE ID='".$pID."'");
        }
      }
      if (isset ($cross_data['sort'])) {
        // edit sorting
        foreach ($cross_data['sort'] AS $ID => $sort_order) {
          $sql_data_array = array(
            'sort_order' => $sort_order,
            'products_xsell_grp_name_id' => $cross_data['group_name'][$ID]
          );
          xtc_db_perform(TABLE_PRODUCTS_XSELL, $sql_data_array, 'update', "ID = '".$ID."'");                    
        }
      }
    }
  }


  function add_data_fields($add_data_string, $data, $language_id = '') {
    $add_data_array = explode(',',preg_replace("'[\r\n\s]+'",'',$add_data_string));
    $add_data_fields_array = array();
    for ($i = 0, $n = sizeof($add_data_array); $i < $n; $i ++) {
      if ($language_id != '') {
        $add_data_fields_array[$add_data_array[$i]] = xtc_db_prepare_input(implode(',', array_filter((array)$data[$add_data_array[$i]][$language_id])));
      } else {
        $add_data_fields_array[$add_data_array[$i]] = xtc_db_prepare_input(implode(',', array_filter((array)$data[$add_data_array[$i]])));
      }
    }
    return $add_data_fields_array;
  }


  function create_templates_dropdown_menu($template, $path, $default_value, $style = '') {
    $files = array();
    
    if (is_dir(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.$path)) {
      foreach(auto_include(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.$path, 'html') as $file) {
        $files[] = array(
          'id' => basename($file), 
          'text' => basename($file),
        );
      }
    }
    
    $default_array = array(array('id' => 'default', 'text' => (count($files) > 0) ? TEXT_SELECT : TEXT_NO_FILE));
    $files = array_merge($default_array, $files);
    return xtc_draw_pull_down_menu($template, $files, $default_value, $style);
  }


  //set products images file rights
  function set_products_images_file_rights($image_name) {
    if (is_file(DIR_FS_CATALOG_MINI_IMAGES.$image_name)) {
      chmod(DIR_FS_CATALOG_MINI_IMAGES.$image_name, 0644);
    }
    if (is_file(DIR_FS_CATALOG_THUMBNAIL_IMAGES.$image_name)) {
      chmod(DIR_FS_CATALOG_THUMBNAIL_IMAGES.$image_name, 0644);
    }
    if (is_file(DIR_FS_CATALOG_MIDI_IMAGES.$image_name)) {
      chmod(DIR_FS_CATALOG_MIDI_IMAGES.$image_name, 0644);
    }
    if (is_file(DIR_FS_CATALOG_INFO_IMAGES.$image_name)) {
      chmod(DIR_FS_CATALOG_INFO_IMAGES.$image_name, 0644);
    }
    if (is_file(DIR_FS_CATALOG_POPUP_IMAGES.$image_name)) {
      chmod(DIR_FS_CATALOG_POPUP_IMAGES.$image_name, 0644);
    }
  }


  function create_permission_checkboxes($object) {
    $customers_statuses_array = xtc_get_customers_statuses();
    
    $input = '<label>' . xtc_draw_checkbox_field('groups[]', 'all', ((!isset($_GET['pID']) && !isset($_GET['cID'])) ? ' checked' : ''), '', 'id="cgAll"').TXT_ALL.'</label><br />'. PHP_EOL;    
    foreach ($customers_statuses_array as $customers_status) {
      $checked = ((is_object($object) && isset($object->{'group_permission_'.$customers_status['id']}) && $object->{'group_permission_'.$customers_status['id']} == 1) ? ' checked' : '');
      $input .= '<label>'.  xtc_draw_checkbox_field('groups[]', $customers_status['id'], $checked,'', 'id="cg'.$customers_status['id'].'"') . $customers_status['text'].'</label><br />'. PHP_EOL;
    }

    return $input;
  }


  function get_categories_desc_fields($category_id, $language_id) {
    if (!empty($category_id)) {
      if (empty($language_id)) {
        $language_id = $_SESSION['languages_id'];
      }
      $category_query = xtc_db_query("SELECT *
                                        FROM ".TABLE_CATEGORIES_DESCRIPTION."
                                       WHERE categories_id = '".(int)$category_id."'
                                         AND language_id = '".(int)$language_id."'");
      return xtc_db_fetch_array($category_query);
    }
  }


  function get_products_desc_fields($product_id, $language_id) {
    if (!empty($product_id)) {
      if (empty($language_id)) {
        $language_id = $_SESSION['languages_id'];
      }
      $product_query = xtc_db_query("SELECT *
                                       FROM ".TABLE_PRODUCTS_DESCRIPTION."
                                      WHERE products_id = '".(int)$product_id."'
                                        AND language_id = '".(int)$language_id."'");
      return xtc_db_fetch_array($product_query);
    }
  }

  
  function set_page_parameter() {
    $this->page_parameter = isset($_GET['page']) ? '&page='.(int)$_GET['page'] : '';
    $this->page_parameter_plain = isset($_GET['page']) ? 'page='.(int)$_GET['page'] : '';
  }

  
  function uploadImage($products_id, $products_data) {
    require(DIR_WS_INCLUDES.'upload_types.php');
    
    //are we asked to delete some pics?
    if (isset($products_data['del_pic']) && $products_data['del_pic'] != '') {
      $dup_check_query = xtc_db_query("SELECT COUNT(*) AS total
                                         FROM ".TABLE_PRODUCTS."
                                        WHERE products_image = '".xtc_db_input($products_data['del_pic'])."'");
      $dup_check = xtc_db_fetch_array($dup_check_query);
      if ($dup_check['total'] < 2) {
        xtc_del_image_file($products_data['del_pic']);
      }
      xtc_db_query("UPDATE ".TABLE_PRODUCTS."
                       SET products_image = NULL
                     WHERE products_id    = '".(int)$products_id."'");
    }

    if ($products_image = xtc_try_upload('products_image', DIR_FS_CATALOG_ORIGINAL_IMAGES, '777', $accepted_image_extensions, $accepted_image_mime_types)) {
      $pname_arr = explode('.', $products_image->filename);
      $nsuffix = array_pop($pname_arr);
      $products_image_name = $products_image_name_process = $this->image_name($products_id, 0, $nsuffix, $pname_arr, false, $products_data);
      $dup_check_query = xtc_db_query("SELECT COUNT(*) AS total
                                        FROM ".TABLE_PRODUCTS."
                                       WHERE products_image = '".xtc_db_input($products_data['products_previous_image_0'])."'");
      $dup_check = xtc_db_fetch_array($dup_check_query);
      if ($dup_check['total'] < 2) {
        xtc_del_image_file($products_data['products_previous_image_0']);
      }
      //workaround if there are v2 images mixed with v3
      $dup_check_query = xtc_db_query("SELECT COUNT(*) AS total
                                         FROM ".TABLE_PRODUCTS."
                                        WHERE products_image = '".xtc_db_input($products_image->filename)."'");
      $dup_check = xtc_db_fetch_array($dup_check_query);
      if ($dup_check['total'] == 0) {
        rename(DIR_FS_CATALOG_ORIGINAL_IMAGES.$products_image->filename, DIR_FS_CATALOG_ORIGINAL_IMAGES.$products_image_name);
      } else {
        copy(DIR_FS_CATALOG_ORIGINAL_IMAGES.$products_image->filename, DIR_FS_CATALOG_ORIGINAL_IMAGES.$products_image_name);
      }

      //image processing
      $this->image_process($products_image_name, $products_image_name_process);

      //set file rights
      $this->set_products_images_file_rights($products_image_name);
      
      return $products_image_name;
    }
    return false;
  }

  
  function uploadMoImages($products_id, $products_data, $action) {
    require(DIR_WS_INCLUDES.'upload_types.php');
    
    //are we asked to delete some pics?
    if (isset($products_data['del_mo_pic']) && count($products_data['del_mo_pic']) > 0) {
      foreach ($products_data['del_mo_pic'] as $dummy => $val) {
        $dup_check_query = xtc_db_query("SELECT COUNT(*) AS total
                                           FROM ".TABLE_PRODUCTS_IMAGES."
                                          WHERE image_name = '".xtc_db_input($val)."'");
        $dup_check = xtc_db_fetch_array($dup_check_query);
        if ($dup_check['total'] < 2) {
          xtc_del_image_file($val);
        }
        xtc_db_query("DELETE FROM ".TABLE_PRODUCTS_IMAGES." WHERE products_id = '".(int)$products_id."' AND image_name  = '".xtc_db_input($val)."'");
      }
    }
    
    for ($img = 0; $img < MO_PICS; $img ++) {      
      if ($pIMG = xtc_try_upload('mo_pics_'.$img, DIR_FS_CATALOG_ORIGINAL_IMAGES, '777', $accepted_image_extensions, $accepted_image_mime_types)) {
        $pname_arr = explode('.', $pIMG->filename);
        $nsuffix = array_pop($pname_arr);
        $products_image_name = $products_image_name_process = $this->image_name($products_id, ($img +1), $nsuffix, $pname_arr, false, $products_data);
        $dup_check_query = xtc_db_query("SELECT COUNT(*) AS total
                                           FROM ".TABLE_PRODUCTS_IMAGES."
                                          WHERE image_name = '".xtc_db_input($products_data['products_previous_image_'. ($img +1)])."'");
        $dup_check = xtc_db_fetch_array($dup_check_query);
        if ($dup_check['total'] < 2) {
          xtc_del_image_file($products_data['products_previous_image_'. ($img +1)]);
        }
        rename(DIR_FS_CATALOG_ORIGINAL_IMAGES.'/'.$pIMG->filename, DIR_FS_CATALOG_ORIGINAL_IMAGES.'/'.$products_image_name);
        //get data & write to table
        $mo_img = array(
          'products_id' => xtc_db_prepare_input($products_id), 
          'image_nr' => xtc_db_prepare_input($img +1), 
          'image_name' => xtc_db_prepare_input($products_image_name)
        );
                         
        if ($action == 'insert' || !$dup_check['total']) {
          xtc_db_perform(TABLE_PRODUCTS_IMAGES, $mo_img);
        } elseif ($action == 'update' && $dup_check['total']) {
          xtc_db_perform(TABLE_PRODUCTS_IMAGES, $mo_img, 'update', "image_name = '".xtc_db_input($products_data['products_previous_image_'. ($img +1)])."'");
        }
        //image processing
        $this->image_process($products_image_name, $products_image_name_process);

        //set file rights
        $this->set_products_images_file_rights($products_image_name);
      }
      
      $mo_images_array = array();
      $mo_images_query = xtc_db_query("SELECT *
                                         FROM ".TABLE_PRODUCTS_IMAGES."
                                        WHERE products_id = '".(int)$products_id."'");
      while ($mo_images = xtc_db_fetch_array($mo_images_query)) {
        $mo_images_array[$mo_images['image_nr']] = $mo_images;
      }
      
      if (count($mo_images_array) > 0) {
        xtc_db_query("DELETE FROM ".TABLE_PRODUCTS_IMAGES_DESCRIPTION." WHERE products_id = '".(int)$products_id."'");

        $languages = xtc_get_languages();
        foreach ($mo_images_array as $mo_images) {        
          foreach ($languages as $language) {
            $sql_data_array = array(
              'image_id' => $mo_images['image_id'],
              'products_id' => $products_id,
              'image_title' => (isset($products_data['image_title'][$mo_images['image_nr']]) ? $products_data['image_title'][$mo_images['image_nr']][$language['id']] : ''),
              'image_alt' => (isset($products_data['image_alt'][$mo_images['image_nr']]) ? $products_data['image_alt'][$mo_images['image_nr']][$language['id']] : ''),
              'language_id' => $language['id'],
            );
            
            xtc_db_perform(TABLE_PRODUCTS_IMAGES_DESCRIPTION, $sql_data_array);
          }
        }
      }

      //new module support
      $this->catModules->insert_mo_images_after($products_data,$img+1,$products_id);
    } 
  } 


  function image_process($products_image_name, $products_image_name_process) {
    require(DIR_WS_INCLUDES . 'product_mini_images.php');
    require(DIR_WS_INCLUDES . 'product_thumbnail_images.php');
    require(DIR_WS_INCLUDES . 'product_midi_images.php');
    require(DIR_WS_INCLUDES . 'product_info_images.php');
    require(DIR_WS_INCLUDES . 'product_popup_images.php');
  }
  
  
  function image_name($data_id, $counter, $suffix, $name_arr = array(), $srcID = false, $data_arr = array()) {
    $separator = (((string)$counter != '') ? '_' : '');
    $image_name = $data_id.$separator.$counter.'.'.$suffix;
    //new module support
    $image_name = $this->catModules->image_name($image_name, $data_id, $counter, $suffix, $name_arr, $srcID, $data_arr);
    return $image_name;
  }
  
  
  function priceCheck($price, $products_tax_rate) {
    if (empty($price)) $price = 0;
    
    if (PRICE_IS_BRUTTO == 'true') {
      $price = xtc_round(($price / ($products_tax_rate + 100) * 100), PRICE_PRECISION);
    } else {
      $price = xtc_round($price, PRICE_PRECISION);
    }
    return $price;
  }


  function saveSpecialsData($products_data) {
    if(isset($products_data['specials_delete'])) {
      xtc_db_query("DELETE FROM " . TABLE_SPECIALS . " 
                          WHERE specials_id = '" . xtc_db_input($products_data['specials_id']) . "'");
    } elseif (isset($products_data['specials_price']) && !empty($products_data['specials_price'])) {
      if (!isset($products_data['specials_quantity']) || empty($products_data['specials_quantity'])) {
        $products_data['specials_quantity'] = 0;
      }
      
      if (!isset($products_data['products_tax_class_id']) 
          || (int)$products_data['products_tax_class_id'] <= 0
          || !isset($products_data['products_price']) 
          || (double)$products_data['products_price'] <= 0.00
          )
      {
        $price_query = xtc_db_query("SELECT products_tax_class_id,
                                            products_price
                                       FROM ".TABLE_PRODUCTS."
                                      WHERE products_id = '".(int)$products_data['products_id']."'");
        $price = xtc_db_fetch_array($price_query);
        
        if (!isset($products_data['products_tax_class_id']) || (int)$products_data['products_tax_class_id'] <= 0) {
          $products_data['products_tax_class_id'] = $price['products_tax_class_id'];
        }
        if (!isset($products_data['products_price']) || (double)$products_data['products_price'] <= 0.00) {
          $products_data['products_price'] = $price['products_price'];
        }
      }
      
      $tax_rate = xtc_get_tax_rate($products_data['products_tax_class_id']);

      if (substr($products_data['specials_price'], -1) != '%') {
        $products_data['specials_price'] = $this->priceCheck($products_data['specials_price'], $tax_rate);
      } else {
        $products_data['specials_price'] = trim(str_replace('%', '', $products_data['specials_price']));
        $products_data['specials_price'] = ($products_data['products_price'] - (($products_data['specials_price'] / 100) * $products_data['products_price']));
      }

      $products_data['specials_old_products_price'] = $this->priceCheck($products_data['specials_old_products_price'], $tax_rate);
      $start_date = isset($products_data['specials_start']) && !empty($products_data['specials_start']) ? date('Y-m-d H:i:00', strtotime($products_data['specials_start'])) : '';
      $expires_date = isset($products_data['specials_expires']) && !empty($products_data['specials_expires']) ? date('Y-m-d H:i:00', strtotime($products_data['specials_expires'])) : '';
    
      $sql_data_array = array(
        'products_id' => $products_data['products_id'],
        'specials_quantity' => (int)$products_data['specials_quantity'],
        'specials_old_products_price' => xtc_db_prepare_input($products_data['specials_old_products_price']),
        'specials_new_products_price' => xtc_db_prepare_input($products_data['specials_price']),
        'specials_date_added' => 'now()',
        'specials_last_modified' => 'now()',
        'start_date' => $start_date,
        'expires_date' => $expires_date,
        'status' => ((isset($products_data['specials_status'])) ? (int)$products_data['specials_status'] : '1')
      );
    
      //new module support
      $sql_data_array = $this->catModules->saveSpecialsData($sql_data_array,$products_data);
      
      if ($products_data['specials_action'] == 'insert') {
        unset($sql_data_array['specials_last_modified']);
        xtc_db_perform(TABLE_SPECIALS, $sql_data_array);
        $products_data['specials_id'] = xtc_db_insert_id();
      } else {
        unset($sql_data_array['specials_date_added']);
        xtc_db_perform(TABLE_SPECIALS, $sql_data_array, 'update', "specials_id = '" . (int)$products_data['specials_id']  . "'" );    
      }

      return $products_data['specials_id'];
    }
  }
  
  function save_products_tags($products_data,$products_id)
  {
      if (isset($products_data['options_id']) && is_array($products_data['options_id'])) {
        foreach ($products_data['options_id'] as $options_id) {
          $options_id = str_replace('oid-','',$options_id);
          xtc_db_query("DELETE FROM ".TABLE_PRODUCTS_TAGS." WHERE products_id = '".(int)$products_id."' AND options_id = '".(int)$options_id."'");
        }
      }
      
      if (isset($products_data['products_tags_save'])) {
        xtc_db_query("DELETE FROM ".TABLE_PRODUCTS_TAGS." WHERE products_id = '".(int)$products_id."'");
      }
      
      if (isset($products_data['product_tags']) && is_array($products_data['product_tags'])) {
        foreach ($products_data['product_tags'] as $options_id => $value) {
          foreach ($value as $values_id => $subvalue) {
            if ($subvalue == 'on') {
              $sql_data_array = array(
                'products_id' => (int)$products_id,
                'options_id' => (int)$options_id,
                'values_id' => (int)$values_id,
                'sort_order' => $products_data['product_tags_sort'][(int)$options_id][(int)$values_id],
              );
              xtc_db_perform(TABLE_PRODUCTS_TAGS, $sql_data_array);                    
            }
          }
        }
      }
      
  }
  
  function calculate_attribute_prices($products_data,$products_id) {
    
      $products_tax_rate_old = xtc_get_tax_rate($products_data['products_tax_class_id_old']);
      
      $attributes_query = xtc_db_query("SELECT *
                                          FROM ".TABLE_PRODUCTS_ATTRIBUTES."
                                         WHERE products_id = '".$products_id."'
                                           AND options_values_price > 0");
      if (xtc_db_num_rows($attributes_query) > 0) {
        while ($attributes = xtc_db_fetch_array($attributes_query)) {
          $values_price_brutto = $attributes['options_values_price'] * (1 + ($products_tax_rate_old / 100));
          
          $values_price_netto = $this->priceCheck($values_price_brutto, $products_tax_rate);
          xtc_db_query("UPDATE ".TABLE_PRODUCTS_ATTRIBUTES." 
                           SET options_values_price = '".xtc_db_input($values_price_netto)."' 
                         WHERE products_attributes_id = '".$attributes['products_attributes_id']."'");
        }
      }
  }
  
}
