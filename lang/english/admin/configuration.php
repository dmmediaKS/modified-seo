<?php
/* -----------------------------------------------------------------------------------------
   $Id: configuration.php 16364 2025-03-21 12:19:39Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(configuration.php,v 1.8 2002/01/04); www.oscommerce.com
   (c) 2003 nextcommerce (configuration.php,v 1.16 2003/08/25); www.nextcommerce.org
   (c) 2006 XT-Commerce

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


define('TABLE_HEADING_CONFIGURATION_TITLE', 'Title');
define('TABLE_HEADING_CONFIGURATION_VALUE', 'Value');
define('TABLE_HEADING_ACTION', 'Action');

define('TEXT_INFO_EDIT_INTRO', 'Please make any necessary changes');
define('TEXT_INFO_DATE_ADDED', 'Date Added:');
define('TEXT_INFO_LAST_MODIFIED', 'Last Modified:');

// language definitions for config
define('STORE_NAME_TITLE' , 'Store Name');
define('STORE_NAME_DESC' , 'The name of this store');
define('STORE_OWNER_TITLE' , 'Store Owner');
define('STORE_OWNER_DESC' , 'The name of the store owner');
define('STORE_OWNER_EMAIL_ADDRESS_TITLE' , 'E-Mail Address');
define('STORE_OWNER_EMAIL_ADDRESS_DESC' , 'The e-mail address of the store owner');

define('EMAIL_FROM_TITLE' , 'E-Mail From');
define('EMAIL_FROM_DESC' , 'The e-mail address used to send e-mails.');

define('STORE_COUNTRY_TITLE' , 'Country');
define('STORE_COUNTRY_DESC' , 'The country my store is located in <br /><br /><b>Note: Please remember to update the store state.</b>');
define('STORE_ZONE_TITLE' , 'State');
define('STORE_ZONE_DESC' , 'The state my store is located in.');

define('EXPECTED_PRODUCTS_SORT_TITLE' , 'Expected Products Sort Order');
define('EXPECTED_PRODUCTS_SORT_DESC' , 'This is the sort order used in the expected products box.');
define('EXPECTED_PRODUCTS_FIELD_TITLE' , 'Expexted Products Sort Field');
define('EXPECTED_PRODUCTS_FIELD_DESC' , 'The column to sort by in the expected products box.');

define('PRODUCTS_NEW_SORT_TITLE', 'New Products Sort Order');
define('PRODUCTS_NEW_SORT_DESC', 'This is the sort order used for new products.');
define('PRODUCTS_NEW_FIELD_TITLE', 'New Products Sort Field');
define('PRODUCTS_NEW_FIELD_DESC', 'The column to sort by for new products.');

define('SPECIALS_SORT_TITLE', 'Specials Sort Order');
define('SPECIALS_SORT_DESC', 'This is the sort order used for specials.');
define('SPECIALS_FIELD_TITLE', 'Specials Sort Field');
define('SPECIALS_FIELD_DESC', 'The column to sort by for specials.');

define('USE_DEFAULT_LANGUAGE_CURRENCY_TITLE' , 'Switch to language specific currency');
define('USE_DEFAULT_LANGUAGE_CURRENCY_DESC' , 'Automatically switch currency to customers country if currency is available.');

define('SEND_EXTRA_ORDER_EMAILS_TO_TITLE' , 'Send Copies of Order E-Mails to:');
define('SEND_EXTRA_ORDER_EMAILS_TO_DESC' , 'Send copies of order e-mails to the following e-mail addresses, like: Name1 &lt;e-mail@address1&gt;, Name2 &lt;e-mail@address2&gt;');

define('SEARCH_ENGINE_FRIENDLY_URLS_TITLE' , 'Use Search-Engine Safe URLs?');
define('SEARCH_ENGINE_FRIENDLY_URLS_DESC' , 'Use search-engine friendly URLs for all site links.<br /><br /><strong>Attention:</strong> For search-engine optimized URLs, the file _.htaccess in the root directory has to be activated, i.e. renamed to .htaccess! Moreover, your webserver has to support the <a href="http://www.modrewrite.com/" target="_blank">mod_rewrite</a> module! (Please ask your webhoster if you are unsure how to check that.)');

define('DISPLAY_CART_TITLE' , 'Display Cart After Adding a Product?');
define('DISPLAY_CART_DESC' , 'Display the shopping cart after adding a product or return back to product?');

define('ALLOW_GUEST_TO_TELL_A_FRIEND_TITLE' , 'Allow Guests To Tell a Friend?');
define('ALLOW_GUEST_TO_TELL_A_FRIEND_DESC' , 'Allow guests to tell a friend about a product?');

define('ADVANCED_SEARCH_DEFAULT_OPERATOR_TITLE' , 'Default Search Operator');
define('ADVANCED_SEARCH_DEFAULT_OPERATOR_DESC' , 'Default search operators.');

define('STORE_NAME_ADDRESS_TITLE' , 'Store Address and Phone');
define('STORE_NAME_ADDRESS_DESC' , 'Store details used for display and printing');

define('SHOW_COUNTS_TITLE' , 'Display Number of Products after Category Name');
define('SHOW_COUNTS_DESC' , 'Display number of products after each category name, counting products recursively');

define('DEFAULT_CUSTOMERS_STATUS_ID_ADMIN_TITLE' , 'Customer Status of Administrators for the frontend');
define('DEFAULT_CUSTOMERS_STATUS_ID_ADMIN_DESC' , 'Choose the default customer status for administrators for the frontend');
define('DEFAULT_CUSTOMERS_STATUS_ID_GUEST_TITLE' , 'Customer Status of Guests');
define('DEFAULT_CUSTOMERS_STATUS_ID_GUEST_DESC' , 'Choose the default customer status for guest accounts');
define('DEFAULT_CUSTOMERS_STATUS_ID_TITLE' , 'Customer Status of New Customers');
define('DEFAULT_CUSTOMERS_STATUS_ID_DESC' , 'Choose the default customer status for a new customer');

define('ALLOW_ADD_TO_CART_TITLE' , 'Allow add to cart');
define('ALLOW_ADD_TO_CART_DESC' , 'Allow customers to add products into cart even if group setting "show prices" is disabled');
define('ALLOW_DISCOUNT_ON_PRODUCTS_ATTRIBUTES_TITLE' , 'Allow discount on products attribute?');
define('ALLOW_DISCOUNT_ON_PRODUCTS_ATTRIBUTES_DESC' , 'Allow customers to get discount on attribute price (if main product is not a "specials" product)');
define('CURRENT_TEMPLATE_TITLE' , 'Template Set (Theme)');
define('CURRENT_TEMPLATE_DESC' , 'Choose a template set (theme). The theme must have been saved before in the following folder: www.Your-Domain.com/templates/');

define('ENTRY_FIRST_NAME_MIN_LENGTH_TITLE' , 'First Name');
define('ENTRY_FIRST_NAME_MIN_LENGTH_DESC' , 'Minimum length of first name');
define('ENTRY_LAST_NAME_MIN_LENGTH_TITLE' , 'Last Name');
define('ENTRY_LAST_NAME_MIN_LENGTH_DESC' , 'Minimum length of last name');
define('ENTRY_DOB_MIN_LENGTH_TITLE' , 'Date of Birth');
define('ENTRY_DOB_MIN_LENGTH_DESC' , 'Minimum length of date of birth');
define('ENTRY_EMAIL_ADDRESS_MIN_LENGTH_TITLE' , 'E-Mail Address');
define('ENTRY_EMAIL_ADDRESS_MIN_LENGTH_DESC' , 'Minimum length of e-mail address');
define('ENTRY_STREET_ADDRESS_MIN_LENGTH_TITLE' , 'Street Address');
define('ENTRY_STREET_ADDRESS_MIN_LENGTH_DESC' , 'Minimum length of street address');
define('ENTRY_COMPANY_MIN_LENGTH_TITLE' , 'Company');
define('ENTRY_COMPANY_MIN_LENGTH_DESC' , 'Minimum length of company name');
define('ENTRY_POSTCODE_MIN_LENGTH_TITLE' , 'Postcode');
define('ENTRY_POSTCODE_MIN_LENGTH_DESC' , 'Minimum length of postcode');
define('ENTRY_CITY_MIN_LENGTH_TITLE' , 'City');
define('ENTRY_CITY_MIN_LENGTH_DESC' , 'Minimum length of city');
define('ENTRY_STATE_MIN_LENGTH_TITLE' , 'State');
define('ENTRY_STATE_MIN_LENGTH_DESC' , 'Minimum length of state');
define('ENTRY_TELEPHONE_MIN_LENGTH_TITLE' , 'Telephone Number');
define('ENTRY_TELEPHONE_MIN_LENGTH_DESC' , 'Minimum length of telephone number');
define('ENTRY_PASSWORD_MIN_LENGTH_TITLE' , 'Password');
define('ENTRY_PASSWORD_MIN_LENGTH_DESC' , 'Minimum length of password');

define('REVIEW_TEXT_MIN_LENGTH_TITLE' , 'Reviews');
define('REVIEW_TEXT_MIN_LENGTH_DESC' , 'Minimum length of review text');

define('MIN_DISPLAY_BESTSELLERS_TITLE' , 'Best Sellers');
define('MIN_DISPLAY_BESTSELLERS_DESC' , 'Minimum number of best sellers to display');
define('MIN_DISPLAY_ALSO_PURCHASED_TITLE' , 'Also Purchased');
define('MIN_DISPLAY_ALSO_PURCHASED_DESC' , 'Minimum number of products to display in the "This Customer Also Purchased" box');

define('MAX_ADDRESS_BOOK_ENTRIES_TITLE' , 'Address Book Entries');
define('MAX_ADDRESS_BOOK_ENTRIES_DESC' , 'Maximum address book entries a customer is allowed to have');
define('MAX_DISPLAY_SEARCH_RESULTS_TITLE' , 'Number of products');
define('MAX_DISPLAY_SEARCH_RESULTS_DESC' , 'Maximum number of products to be displayed per page in the product listing (category pages).');
define('MAX_DISPLAY_PAGE_LINKS_TITLE' , 'Page Links');
define('MAX_DISPLAY_PAGE_LINKS_DESC' , 'Number of "number" links use for page-sets');
define('MAX_DISPLAY_SPECIAL_PRODUCTS_TITLE' , 'Specials');
define('MAX_DISPLAY_SPECIAL_PRODUCTS_DESC' , 'Maximum number of specials (specials.php) to be displayed per page.');
define('MAX_DISPLAY_NEW_PRODUCTS_TITLE' , 'New Products Module');
define('MAX_DISPLAY_NEW_PRODUCTS_DESC' , 'Maximum number of new products to display in a category and as "TOP-products" on startpage');
define('MAX_DISPLAY_UPCOMING_PRODUCTS_TITLE' , 'Upcoming Products');
define('MAX_DISPLAY_UPCOMING_PRODUCTS_DESC' , 'Maximum number of upcoming products to display on startpage');
define('MAX_DISPLAY_MANUFACTURERS_IN_A_LIST_TITLE' , 'Manufacturers List');
define('MAX_DISPLAY_MANUFACTURERS_IN_A_LIST_DESC' , 'Used in manufacturers box; when the number of manufacturers exceeds this number, a drop-down list or list-box will be displayed instead of the default link-list (depends on what you entered in "Manufacturers Select Size").');
define('MAX_MANUFACTURERS_LIST_TITLE' , 'Manufacturers Select Size');
define('MAX_MANUFACTURERS_LIST_DESC' , 'Used in manufacturers box; when this value is "1" the classic drop-down list will be used for the manufacturers box. Otherwise, a list-box with the specified number of rows will be displayed.');
define('MAX_DISPLAY_MANUFACTURER_NAME_LEN_TITLE' , 'Length of Manufacturers Name');
define('MAX_DISPLAY_MANUFACTURER_NAME_LEN_DESC' , 'Used in manufacturers box; maximum length of manufacturers name to display');
define('MAX_DISPLAY_NEW_REVIEWS_TITLE' , 'New Reviews');
define('MAX_DISPLAY_NEW_REVIEWS_DESC' , 'Maximum number of new reviews to display');
define('MAX_RANDOM_SELECT_REVIEWS_TITLE' , 'Selection of Random Reviews');
define('MAX_RANDOM_SELECT_REVIEWS_DESC' , 'How many records to select from to choose one random product review. For templates based on xtc5, it corresponds to the number of reviews displayed. Otherwise it is the selection pool from which one or more reviews are displayed.');
define('MAX_RANDOM_SELECT_NEW_TITLE' , 'Selection of Random New Products');
define('MAX_RANDOM_SELECT_NEW_DESC' , 'How many records to select from to choose one random new product to display. For templates based on xtc5, it corresponds to the number of articles displayed. Otherwise it is the selection pool from which one or more articles are displayed.');
define('MAX_RANDOM_SELECT_SPECIALS_TITLE' , 'Selection of Products on Special');
define('MAX_RANDOM_SELECT_SPECIALS_DESC' , 'How many records to select from to choose one random product special to display. For templates based on xtc5, it corresponds to the number of special offers displayed. Otherwise, it is the selection pool from which one or more special offers are displayed.');
define('MAX_DISPLAY_CATEGORIES_PER_ROW_TITLE' , 'Categories To List Per Row');
define('MAX_DISPLAY_CATEGORIES_PER_ROW_DESC' , 'Number of subcategories to be displayed per row in the category overview. Values above 1 only apply in table-based templates such as xtc5. For all other templates, the display of subcategories can be set to 1 (activated) and 0 (deactivated).');
define('MAX_DISPLAY_PRODUCTS_NEW_TITLE' , 'New Products Listing');
define('MAX_DISPLAY_PRODUCTS_NEW_DESC' , 'Maximum number of new products to be displayed per page of "New Products" (products_new.php).');
define('MAX_DISPLAY_BESTSELLERS_TITLE' , 'Best Sellers');
define('MAX_DISPLAY_BESTSELLERS_DESC' , 'Maximum number of best sellers to display');
define('MAX_DISPLAY_BESTSELLERS_DAYS_TITLE' , 'Maximum Age (days) for Best Sellers');
define('MAX_DISPLAY_BESTSELLERS_DAYS_DESC' , 'Maximum age (in days) for products to be displayed as "Best Sellers"');
define('MAX_DISPLAY_ALSO_PURCHASED_TITLE' , 'Also Purchased');
define('MAX_DISPLAY_ALSO_PURCHASED_DESC' , 'Maximum number of products to display in the "This Customer Also Purchased" box');
define('MAX_DISPLAY_ALSO_PURCHASED_ORDERS_TITLE' , 'Number of orders Also Purchased');
define('MAX_DISPLAY_ALSO_PURCHASED_ORDERS_DESC' , 'Maximum number of orders to search for also purchased products');
define('MAX_DISPLAY_CROSS_SELLS_TITLE' , 'Cross Selling');
define('MAX_DISPLAY_CROSS_SELLS_DESC' , 'Maximum number of cross selling to display in the "Recommendation" box<br><b>Note:</b> 0 will display all assigned products');
define('MAX_DISPLAY_REVERSE_CROSS_SELLS_TITLE' , 'Reverse Cross Selling');
define('MAX_DISPLAY_REVERSE_CROSS_SELLS_DESC' , 'Maximum number of cross selling to display in the "Recommendation" box<br><b>Note:</b> 0 will display all assigned products');
define('MAX_DISPLAY_PRODUCTS_IN_ORDER_HISTORY_BOX_TITLE' , 'Customer Order History Box');
define('MAX_DISPLAY_PRODUCTS_IN_ORDER_HISTORY_BOX_DESC' , 'Maximum number of products to display in the customer order history box');
define('MAX_DISPLAY_ORDER_HISTORY_TITLE' , 'Order History');
define('MAX_DISPLAY_ORDER_HISTORY_DESC' , 'Maximum number of orders to display in the order history page');
define('MAX_PRODUCTS_QTY_TITLE', 'Maximum Quantity');
define('MAX_PRODUCTS_QTY_DESC', 'Maximum quantity per product in cart');
define('MAX_DISPLAY_NEW_PRODUCTS_DAYS_TITLE' , 'Maximum Age (days) for New Products');
define('MAX_DISPLAY_NEW_PRODUCTS_DAYS_DESC' , 'Maximum age (in days) for products to be displayed as "new products"');

define('PRODUCT_IMAGE_MINI_WIDTH_TITLE' , 'Width of Product Mini Images');
define('PRODUCT_IMAGE_MINI_WIDTH_DESC' , 'Maximum width of product mini (in pixels) (Standard: 80). For larger values possibly "productPreviewImage" is adjusted in the templates stylesheet.css file.');
define('PRODUCT_IMAGE_MINI_HEIGHT_TITLE' , 'Height of Product Mini Images');
define('PRODUCT_IMAGE_MINI_HEIGHT_DESC' , 'Maximum height of product mini (in pixels) (Standard: 80).');

define('PRODUCT_IMAGE_THUMBNAIL_WIDTH_TITLE' , 'Width of Product Thumbnails');
define('PRODUCT_IMAGE_THUMBNAIL_WIDTH_DESC' , 'Maximum width of product thumbnails (in pixels) (Standard: 240). For larger values possibly "productPreviewImage" is adjusted in the templates stylesheet.css file.');
define('PRODUCT_IMAGE_THUMBNAIL_HEIGHT_TITLE' , 'Height of Product Thumbnails');
define('PRODUCT_IMAGE_THUMBNAIL_HEIGHT_DESC' , 'Maximum height of product thumbnails (in pixels) (Standard: 240).');

define('PRODUCT_IMAGE_MIDI_WIDTH_TITLE' , 'Width of Product Midi Images');
define('PRODUCT_IMAGE_MIDI_WIDTH_DESC' , 'Maximum width of product midi (in pixels) (Standard: 160). For larger values possibly "productPreviewImage" is adjusted in the templates stylesheet.css file.');
define('PRODUCT_IMAGE_MIDI_HEIGHT_TITLE' , 'Height of Product Midi Images');
define('PRODUCT_IMAGE_MIDI_HEIGHT_DESC' , 'Maximum height of product midi (in pixels) (Standard: 160).');

define('PRODUCT_IMAGE_INFO_WIDTH_TITLE' , 'Width of Product Info Images');
define('PRODUCT_IMAGE_INFO_WIDTH_DESC' , 'Maximum width of product info images (in pixels) (Standard: 520).');
define('PRODUCT_IMAGE_INFO_HEIGHT_TITLE' , 'Height of Product Info Images');
define('PRODUCT_IMAGE_INFO_HEIGHT_DESC' , 'Maximum height of product info images (in pixels) (Standard: 520).');

define('PRODUCT_IMAGE_POPUP_WIDTH_TITLE' , 'Width of Popup Images');
define('PRODUCT_IMAGE_POPUP_WIDTH_DESC' , 'Maximum width of popup images (in pixels) (Standard: 800).');
define('PRODUCT_IMAGE_POPUP_HEIGHT_TITLE' , 'Height of Popup Images');
define('PRODUCT_IMAGE_POPUP_HEIGHT_DESC' , 'Maximum height of popup images (in pixels) (Standard: 800).');

define('CATEGORIES_IMAGE_WIDTH_TITLE' , 'Width of Category Images');
define('CATEGORIES_IMAGE_WIDTH_DESC' , 'Maximum width of category images (in pixels) (Standard: 985).');
define('CATEGORIES_IMAGE_HEIGHT_TITLE' , 'Height of Category Images');
define('CATEGORIES_IMAGE_HEIGHT_DESC' , 'Maximum height of category images (in pixels) (Standard: 370).');

define('CATEGORIES_IMAGE_MOBILE_WIDTH_TITLE' , 'Width of Category Images Mobile');
define('CATEGORIES_IMAGE_MOBILE_WIDTH_DESC' , 'Maximum width of category images mobile (in pixels) (Standard: 600).');
define('CATEGORIES_IMAGE_MOBILE_HEIGHT_TITLE' , 'Height of Category Images Mobile');
define('CATEGORIES_IMAGE_MOBILE_HEIGHT_DESC' , 'Maximum height of category images mobile (in pixels) (Standard: 400).');

define('CATEGORIES_IMAGE_LIST_WIDTH_TITLE' , 'Width of Category Images Listing');
define('CATEGORIES_IMAGE_LIST_WIDTH_DESC' , 'Maximum width of category images listing (in pixels) (Standard: 225).');
define('CATEGORIES_IMAGE_LIST_HEIGHT_TITLE' , 'Height of Category Images Listing');
define('CATEGORIES_IMAGE_LIST_HEIGHT_DESC' , 'Maximum height of category images listing (in pixels) (Standard: 170).');

define('MANUFACTURER_IMAGE_WIDTH_TITLE' , 'Width of Manufacturer Images');
define('MANUFACTURER_IMAGE_WIDTH_DESC' , 'Maximum width of manufacturer images (in pixels) (Standard: 100).');
define('MANUFACTURER_IMAGE_HEIGHT_TITLE' , 'Height of Manufacturer Images');
define('MANUFACTURER_IMAGE_HEIGHT_DESC' , 'Maximum height of manufacturer images (in pixels) (Standard: 60).');

define('BANNERS_IMAGE_WIDTH_TITLE' , 'Width of Banner Images');
define('BANNERS_IMAGE_WIDTH_DESC' , 'Maximum width of banner images (in pixels) (Standard: 985).');
define('BANNERS_IMAGE_HEIGHT_TITLE' , 'Height of Banner Images');
define('BANNERS_IMAGE_HEIGHT_DESC' , 'Maximum height of banner images (in pixels) (Standard: 370).');

define('BANNERS_IMAGE_MOBILE_WIDTH_TITLE' , 'Width of Banner Images Mobile');
define('BANNERS_IMAGE_MOBILE_WIDTH_DESC' , 'Maximum width of banner images mobile (in pixels) (Standard: 600).');
define('BANNERS_IMAGE_MOBILE_HEIGHT_TITLE' , 'Height of Banner Images Mobile');
define('BANNERS_IMAGE_MOBILE_HEIGHT_DESC' , 'Maximum height of banner images mobile (in pixels) (Standard: 600).');

define('SMALL_IMAGE_WIDTH_TITLE' , 'Small Image Width');
define('SMALL_IMAGE_WIDTH_DESC' , 'Width of small images (in pixels)');
define('SMALL_IMAGE_HEIGHT_TITLE' , 'Small Image Height');
define('SMALL_IMAGE_HEIGHT_DESC' , 'Height of small images (in pixels)');

define('SUBCATEGORY_IMAGE_WIDTH_TITLE' , 'Subcategory Image Width');
define('SUBCATEGORY_IMAGE_WIDTH_DESC' , 'Width of subcategory images (in pixels)');
define('SUBCATEGORY_IMAGE_HEIGHT_TITLE' , 'Subcategory Image Height');
define('SUBCATEGORY_IMAGE_HEIGHT_DESC' , 'Height of subcategory images (in pixels)');

define('MO_PICS_TITLE','Number of product images');
define('MO_PICS_DESC','if this number is set > 0 , you will be able to upload/display more images per product');

define('PRODUCT_IMAGE_MINI_MERGE_TITLE' , 'Product Mini Images:Merge<br /><img src="images/config_merge.gif">');
define('PRODUCT_IMAGE_MINI_MERGE_DESC' , 'Product Mini Images:Merge<br /><br />Default-values: (overlay.gif,10,-50,60,FF0000)<br /><br />overlay merge image<br />Usage:<br />(merge image,x start [neg = from right],y start [neg = from base],opacity, transparent colour on merge image)');

define('PRODUCT_IMAGE_THUMBNAIL_MERGE_TITLE' , 'Product Thumbnails:Merge');
define('PRODUCT_IMAGE_THUMBNAIL_MERGE_DESC' , 'Product Thumbnails:Merge<br /><br />Default-values: (overlay.gif,10,-50,60,FF0000)<br /><br />overlay merge image<br />Usage:<br />(merge image,x start [neg = from right],y start [neg = from base],opacity, transparent colour on merge image)');

define('PRODUCT_IMAGE_MIDI_MERGE_TITLE' , 'Product Midi Images:Merge');
define('PRODUCT_IMAGE_MIDI_MERGE_DESC' , 'Product Midi Images:Merge<br /><br />Default-values: (overlay.gif,10,-50,60,FF0000)<br /><br />overlay merge image<br />Usage:<br />(merge image,x start [neg = from right],y start [neg = from base],opacity, transparent colour on merge image)');

define('PRODUCT_IMAGE_INFO_MERGE_TITLE' , 'Product Images:Merge');
define('PRODUCT_IMAGE_INFO_MERGE_DESC' , 'Product Images:Merge<br /><br />Default-values: (overlay.gif,10,-50,60,FF0000)<br /><br />overlay merge image<br />Usage:<br />(merge image,x start [neg = from right],y start [neg = from base],opacity,transparent colour on merge image)');

define('PRODUCT_IMAGE_POPUP_MERGE_TITLE' , 'Product Popup Images:Merge');
define('PRODUCT_IMAGE_POPUP_MERGE_DESC' , 'Product Popup Images:Merge<br /><br />Default-values: (overlay.gif,10,-50,60,FF0000)<br /><br />overlay merge image<br />Usage:<br />(merge image,x start [neg = from right],y start [neg = from base],opacity,transparent colour on merge image)');

define('IMAGE_MANIPULATOR_TITLE','GDlib processing');
define('IMAGE_MANIPULATOR_DESC','Image Manipulator for GD2 or GD1<br /><br /><b>NOTE:</b> image_manipulator_GD2_advanced.php support transparent PNG\s');

define('IMAGE_TYPE_EXTENSION_TITLE','Imagetype');
define('IMAGE_TYPE_EXTENSION_DESC','Which image type should be delivered?<br /><br /><b>NOTE:</b> After changing, <a href="'.xtc_href_link(FILENAME_MODULE_EXPORT, 'set=system&module=image_processing_step').'"><b>Imageprocessing - product images</b></a> must be started to generate the images.');

define('ACCOUNT_GENDER_TITLE' , 'Salutation');
define('ACCOUNT_GENDER_DESC' , 'Display salutation upon customer account creation/editing');
define('ACCOUNT_DOB_TITLE' , 'Date of Birth');
define('ACCOUNT_DOB_DESC' , 'Display date of birth upon customer account creation/editing');
define('ACCOUNT_COMPANY_TITLE' , 'Company');
define('ACCOUNT_COMPANY_DESC' , 'Display company upon customer account creation/editing');
define('ACCOUNT_SUBURB_TITLE' , 'Addition to address');
define('ACCOUNT_SUBURB_DESC' , 'Display addition to address upon customer account creation/editing');
define('ACCOUNT_STATE_TITLE' , 'State');
define('ACCOUNT_STATE_DESC' , 'Display state upon customer account creation/editing');
define('ACCOUNT_FAX_TITLE' , 'Fax');
define('ACCOUNT_FAX_DESC' , 'Display fax upon customer account creation/editing');

define('DEFAULT_CURRENCY_TITLE' , 'Default Currency');
define('DEFAULT_CURRENCY_DESC' , 'Currency to be used as default');
define('DEFAULT_LANGUAGE_TITLE' , 'Default Language');
define('DEFAULT_LANGUAGE_DESC' , 'Language to be used as default');
define('DEFAULT_ORDERS_STATUS_ID_TITLE' , 'Default Order Status');
define('DEFAULT_ORDERS_STATUS_ID_DESC' , 'Default order status when a new order is placed.');

define('SHIPPING_MAX_WEIGHT_TITLE' , 'Enter the Maximum Package Weight you will ship');
define('SHIPPING_MAX_WEIGHT_DESC' , 'Carriers have a max weight limit for a single package. This is a common one for all.');
define('SHIPPING_BOX_WEIGHT_TITLE' , 'Package Tare weight');
define('SHIPPING_BOX_WEIGHT_DESC' , 'What is the weight of typical packaging of small to medium packages?');
define('SHIPPING_BOX_PADDING_TITLE' , 'Larger packages - percentage increase');
define('SHIPPING_BOX_PADDING_DESC' , 'For 10% enter 10');
define('SHOW_SHIPPING_TITLE' , 'Display shipping costs');
define('SHOW_SHIPPING_DESC' , 'Show link to shipping costs');
define('SHIPPING_INFOS_TITLE' , 'Shipping costs');
define('SHIPPING_INFOS_DESC' , 'Select content to display shipping costs');
define('SHIPPING_DEFAULT_TAX_CLASS_METHOD_TITLE' , 'Calculation method of default tax class');
define('SHIPPING_DEFAULT_TAX_CLASS_METHOD_DESC' , 'none: do not show shipping tax<br />auto proportional: show shipping tax proportional to order<br />auto max: show shipping tax, use tax rate of biggest turnover group');

define('SHOW_SHIPPING_MODULE_TITLE_TITLE' , 'Title of shipping costs');
define('SHOW_SHIPPING_MODULE_TITLE_DESC' , 'Choose the title of the shipping costs in the order total');
define('CUSTOM_SHIPPING_TITLE_TITLE' , 'Individual title shipping costs');
define('CUSTOM_SHIPPING_TITLE_DESC' , 'Enter a name. This setting only applies if the title shipping costs is set to "Custom".');
define('CFG_TXT_SHIPPING_DEFAULT', 'Standard');
define('CFG_TXT_SHIPPING_TITLE', 'Title');
define('CFG_TXT_SHIPPING_CUSTOM', 'Custom');

define('CAPITALIZE_ADDRESS_FORMAT_TITLE' , 'Address in capital letters');
define('CAPITALIZE_ADDRESS_FORMAT_DESC' , 'Display country and city names in capital letters');

define('PRODUCT_LIST_FILTER_TITLE' , 'Display Category/Manufacturer Filter (false=disable; true=enable)');
define('PRODUCT_LIST_FILTER_DESC' , 'Do you want to display the Category/Manufacturer Filter?');

define('STOCK_CHECK_TITLE' , 'Check Stock Level');
define('STOCK_CHECK_DESC' , 'Check to see if sufficent stock is available');

define('ATTRIBUTE_STOCK_CHECK_TITLE' , 'Check Attribute Stock Level');
define('ATTRIBUTE_STOCK_CHECK_DESC' , 'Check to see if sufficent attribute stock is available');
define('STOCK_LIMITED_TITLE' , 'Subtract stock');
define('STOCK_LIMITED_DESC' , 'Subtract product quantity in order from quantity of products in stock');
define('STOCK_LIMITED_DOWNLOADS_TITLE' , 'Subtract stock for downloads');
define('STOCK_LIMITED_DOWNLOADS_DESC' , 'Should the amount of goods also be deducted for download items?');
define('STOCK_ALLOW_CHECKOUT_TITLE' , 'Allow Checkout');
define('STOCK_ALLOW_CHECKOUT_DESC' , 'Allow customer to checkout even if there is insufficient stock');
define('STOCK_MARK_PRODUCT_OUT_OF_STOCK_TITLE' , 'Mark product out of stock');
define('STOCK_MARK_PRODUCT_OUT_OF_STOCK_DESC' , 'Display on-screen message so customers can see which product has insufficient stock');
define('STOCK_REORDER_LEVEL_TITLE' , 'Stock re-order level');
define('STOCK_REORDER_LEVEL_DESC' , 'Define when stock needs to be re-ordered (planned function)');
define('STORE_PAGE_PARSE_TIME_TITLE' , 'Store page parse time');
define('STORE_PAGE_PARSE_TIME_DESC' , 'Store the time it takes to parse a page');
define('STORE_PARSE_DATE_TIME_FORMAT_TITLE' , 'Log file date format');
define('STORE_PARSE_DATE_TIME_FORMAT_DESC' , 'The date format (Default: Y-m-d H:i:s)');
define('STORE_DB_SLOW_QUERY_TITLE' , 'Slow Query Log');
define('STORE_DB_SLOW_QUERY_DESC' , 'Should only slow SQL Queries be saved?<br/><strong>Caution: Store Database Queries must be enabled!</strong>.<br/><strong>Caution: File can get very big in size in long logging sessions!</strong>.<br/><br/>The Logfile is saved in /log in the shoproot');
define('STORE_DB_SLOW_QUERY_TIME_TITLE' , 'Slow Query Log - Time');
define('STORE_DB_SLOW_QUERY_TIME_DESC' , 'Time for the slow querys wich should be logged.');

define('DISPLAY_PAGE_PARSE_TIME_TITLE' , 'Display The Page Parse Time');
define('DISPLAY_PAGE_PARSE_TIME_DESC' , 'Display the page parse time<br /><strong>none</strong>: deactivated<br /><strong>admin</strong>: Only the admin sees the page parse time<br /><strong>all</strong>: Everybody sees the page parse time');
define('STORE_DB_TRANSACTIONS_TITLE' , 'Store Database Queries');
define('STORE_DB_TRANSACTIONS_DESC' , 'Store the database queries in the page parse time log file<br/><strong>Caution: File can get very big in size in long logging sessions!</strong>.<br/><br/>The Logfile is saved in /log in the shoproot');

define('USE_CACHE_TITLE' , 'Use Template Cache');
define('USE_CACHE_DESC' , 'Use Template caching features');

define('DB_CACHE_TITLE','DB Cache');
define('DB_CACHE_DESC','Cache database query results to gain more speed for slow databases.');

define('DB_CACHE_EXPIRE_TITLE','DB Cache lifetime');
define('DB_CACHE_EXPIRE_DESC','Time in seconds to rebuild cached result.');

define('DIR_FS_CACHE_TITLE' , 'Cache Directory');
define('DIR_FS_CACHE_DESC' , 'The directory where cached files are saved');

define('ACCOUNT_OPTIONS_TITLE','Account Options');
define('ACCOUNT_OPTIONS_DESC','How do you want to configure the login procedure of your store?<br />You can choose between regular customer accounts and "One-Off Orders" without creating a customer account (an account will be created but the customer won\'t be informed about that)');

define('EMAIL_TRANSPORT_TITLE' , 'E-Mail Transport Method');
define('EMAIL_TRANSPORT_DESC' , 'Defines if this server uses a local connection to sendmail or uses an SMTP connection via TCP/IP. Servers running on Windows and MacOS should change this setting to SMTP.');

define('EMAIL_LINEFEED_TITLE' , 'E-Mail Linefeeds');
define('EMAIL_LINEFEED_DESC' , 'Defines the character sequence used to separate mail headers.');
define('EMAIL_USE_HTML_TITLE' , 'Use MIME HTML When Sending E-Mails');
define('EMAIL_USE_HTML_DESC' , 'Send E-Mails in HTML format');
define('ENTRY_EMAIL_ADDRESS_CHECK_TITLE' , 'Verify E-Mail Address Through DNS');
define('ENTRY_EMAIL_ADDRESS_CHECK_DESC' , 'Verify e-mail address through a DNS server');
define('SEND_EMAILS_TITLE' , 'Send E-Mails');
define('SEND_EMAILS_DESC' , 'Send out E-Mails');
define('SENDMAIL_PATH_TITLE' , 'The Path to sendmail');
define('SENDMAIL_PATH_DESC' , 'If you use sendmail, please give the right path (default: /usr/bin/sendmail):');
define('USE_SENDMAIL_OPTIONS_TITLE', 'Sendmail Options');
define('USE_SENDMAIL_OPTIONS_DESC', 'Whether mail() uses a fully sendmail-compatible MTA');
define('SMTP_MAIN_SERVER_TITLE' , 'Address of the SMTP Server');
define('SMTP_MAIN_SERVER_DESC' , 'Please enter the address of your main SMTP Server.');
define('SMTP_BACKUP_SERVER_TITLE' , 'Address of the SMTP Backup Server');
define('SMTP_BACKUP_SERVER_DESC' , 'Please enter the address of your Backup SMTP Server.');
define('SMTP_USERNAME_TITLE' , 'SMTP Username');
define('SMTP_USERNAME_DESC' , 'Please enter the username of your SMTP Account.');
define('SMTP_PASSWORD_TITLE' , 'SMTP Password');
define('SMTP_PASSWORD_DESC' , 'Please enter the password of your SMTP Account.');
define('SMTP_AUTH_TITLE' , 'SMTP Auth');
define('SMTP_AUTH_DESC' , 'Enable secure authentication for your SMTP Server');
define('SMTP_AUTH_TYPE_TITLE' , 'SMTP AuthType');
define('SMTP_AUTH_TYPE_DESC' , 'Set type for SMTP authentication.');
define('SMTP_PORT_TITLE' , 'SMTP Port');
define('SMTP_PORT_DESC' , 'Please enter the SMTP port of your SMTP server (default: 25)?');
define('SMTP_AUTO_TLS_TITLE' , 'SMTP Automatic TLS connection');
define('SMTP_AUTO_TLS_DESC' , 'Connection automatically with STARTTLS if SMTP SECURE is deactivated? <br> In case of transmission problems deactivate the automatic TLS connection!');
define('SMTP_DEBUG_TITLE' , 'SMTP Debug Log-Level');
define('SMTP_DEBUG_DESC' , '<b>0</b>: no output; <b>1</b>: commands; <b>2</b>: data and commands; <b>3</b>: as 2 plus connection status; <b>4</b>: as 3 plus low-level data output<br>In case of sending problems set to 2, a log is created in the log directory.');
define('CFG_TXT_DEFAULT', 'Standard');

//DokuMan - 2011-09-20 - E-Mail SQL errors
define('EMAIL_SQL_ERRORS_TITLE','Send SQL error messages to shop owner via email');
define('EMAIL_SQL_ERRORS_DESC','When "true" an email will be sent to the shop owner\'s email address containing the appropriate SQL error message. The SQL error message itself will be hidden from the customer.<br />When "false" the SQL error message will be displayed directly and visible for everybody (default).');

//Constants for contact_us
define('CONTACT_US_EMAIL_ADDRESS_TITLE' , 'Contact Us - E-Mail Address');
define('CONTACT_US_EMAIL_ADDRESS_DESC' , 'Please enter an e-mail address used for "Contact Us" messages');
define('CONTACT_US_NAME_TITLE' , 'Contact Us - E-Mail Name');
define('CONTACT_US_NAME_DESC' , 'Please enter a name to be used for "Contact Us" messages');
define('CONTACT_US_FORWARDING_STRING_TITLE' , 'Contact Us - Forwarding-To');
define('CONTACT_US_FORWARDING_STRING_DESC' , 'Please enter e-mail addresses (separated by ",") where "Contact Us" messages should be forwarded to.');
define('CONTACT_US_REPLY_ADDRESS_TITLE' , 'Contact Us - Reply-To');
define('CONTACT_US_REPLY_ADDRESS_DESC' , 'Please enter an e-mail address where customers can reply to.');
define('CONTACT_US_REPLY_ADDRESS_NAME_TITLE' , 'Contact Us - Reply-To Name');
define('CONTACT_US_REPLY_ADDRESS_NAME_DESC' , 'Please enter a name to be used in the reply-to field of "Contact Us" meesages.');
define('CONTACT_US_EMAIL_SUBJECT_TITLE' , 'Contact Us - E-Mail Subject');
define('CONTACT_US_EMAIL_SUBJECT_DESC' , 'Please enter an e-mail subject for "Contact Us" messages.');

//Constants for support system
define('EMAIL_SUPPORT_ADDRESS_TITLE' , 'Technical Support - E-Mail address');
define('EMAIL_SUPPORT_ADDRESS_DESC' , 'Please enter an e-mail address for sending e-mails over the <b>Support System</b> (account creation, lost password, newsletter system).');
define('EMAIL_SUPPORT_NAME_TITLE' , 'Technical Support - E-Mail Name');
define('EMAIL_SUPPORT_NAME_DESC' , 'Please enter a name for sending E-Mails over the <b>Support System</b>.');
define('EMAIL_SUPPORT_FORWARDING_STRING_TITLE' , 'Technical Support - Forwarding-To');
define('EMAIL_SUPPORT_FORWARDING_STRING_DESC' , 'Please enter forwarding addresses for mails of the <b>Support System</b> (seperated by , )');
define('EMAIL_SUPPORT_REPLY_ADDRESS_TITLE' , 'Technical Support - Reply-To');
define('EMAIL_SUPPORT_REPLY_ADDRESS_DESC' , 'Please enter an e-mail address for replies of your customers.');
define('EMAIL_SUPPORT_REPLY_ADDRESS_NAME_TITLE' , 'Technical Support - Reply-To Name');
define('EMAIL_SUPPORT_REPLY_ADDRESS_NAME_DESC' , 'Please enter a name to be used in the reply-to field of support e-mails.');
define('EMAIL_SUPPORT_SUBJECT_TITLE' , 'Technical Support - E-Mail Subject');
define('EMAIL_SUPPORT_SUBJECT_DESC' , 'Please enter an e-mail subject for <b>Support System</b> messages.');

//Constants for Billing system
define('EMAIL_BILLING_ADDRESS_TITLE' , 'Billing - E-Mail address');
define('EMAIL_BILLING_ADDRESS_DESC' , 'Please enter an E-Mail address for sending e-mails over the <b>Billing System</b> (order confirmations, status changes, ...).');
define('EMAIL_BILLING_NAME_TITLE' , 'Billing - E-Mail Name');
define('EMAIL_BILLING_NAME_DESC' , 'Please enter a name for sending e-mails over the <b>Billing System</b> (order confirmations, status changes, ...).');
define('EMAIL_BILLING_FORWARDING_STRING_TITLE' , 'Billing - Forwarding-To');
define('EMAIL_BILLING_FORWARDING_STRING_DESC' , 'Please enter forwarding addresses for mails of the <b>Billing System</b> (seperated by , )');
define('EMAIL_BILLING_REPLY_ADDRESS_TITLE' , 'Billing - Reply-To');
define('EMAIL_BILLING_REPLY_ADDRESS_DESC' , 'Please enter an e-mail address for replies of your customers.');
define('EMAIL_BILLING_REPLY_ADDRESS_NAME_TITLE' , 'Billing - Reply-To Name');
define('EMAIL_BILLING_REPLY_ADDRESS_NAME_DESC' , 'Please enter a name to be used in the reply-to field of billing e-mails.');
define('EMAIL_BILLING_SUBJECT_TITLE' , 'Billing - E-Mail Subject');
define('EMAIL_BILLING_SUBJECT_DESC' , 'Please enter an e-mail subject for <b>Billing</b> messages. (For example <b>Your order {$nr} from {$date}</b>)<br />You can use: {$nr},{$date},{$firstname},{$lastname}');
define('EMAIL_BILLING_SUBJECT_ORDER_TITLE','Billing - Order Mail Subject');
define('EMAIL_BILLING_SUBJECT_ORDER_DESC','Please enter a subject for order mails. (For example <b>Your order {$nr} from {$date}</b>)<br />You can use: {$nr},{$date},{$firstname},{$lastname}');
define('MODULE_ORDER_MAIL_STEP_SUBJECT_TITLE','Billing - Order Confirmation Mail Subject');
define('MODULE_ORDER_MAIL_STEP_SUBJECT_DESC','Please enter a subject for order confirmation mails. (For example <b>Your order {$nr} from {$date}</b>)<br />You can use: {$nr},{$date},{$firstname},{$lastname}');

define('DOWNLOAD_ENABLED_TITLE' , 'Enable Download');
define('DOWNLOAD_ENABLED_DESC' , 'Enable the products download functions.');
define('DOWNLOAD_BY_REDIRECT_TITLE' , 'Download by Redirect');
define('DOWNLOAD_BY_REDIRECT_DESC' , 'Use browser redirection for download. Disabled on non-Unix systems.');
define('DOWNLOAD_MAX_DAYS_TITLE' , 'Expiry Delay (Days)');
define('DOWNLOAD_MAX_DAYS_DESC' , 'Set number of days before the download link expires. 0 means no limit.');
define('DOWNLOAD_MAX_COUNT_TITLE' , 'Maximum Number of Downloads');
define('DOWNLOAD_MAX_COUNT_DESC' , 'Set the maximum number of downloads. 0 means no download authorized.');
define('DOWNLOAD_MULTIPLE_ATTRIBUTES_ALLOWED_TITLE' , 'Multiple Attribute for Downloads');
define('DOWNLOAD_MULTIPLE_ATTRIBUTES_ALLOWED_DESC' , 'Allow Multiple Attribute to skip Shipping.');

define('GZIP_COMPRESSION_TITLE' , 'Enable GZip Compression');
define('GZIP_COMPRESSION_DESC' , 'Enable HTTP gzip compression.');
define('GZIP_LEVEL_TITLE' , 'Compression Level');
define('GZIP_LEVEL_DESC' , 'Set a compression level from 0-9 (0 = minimum, 9 = maximum).');

define('SESSION_WARNING', '<br /><br /><span class="col-red"><strong>CAUTION:</strong></span> This feature might reduce the operability of the shop system. Change it only when you are aware of the following consequences and your webserver supports the corresponding feature.');

define('SESSION_WRITE_DIRECTORY_TITLE' , 'Session Directory');
define('SESSION_WRITE_DIRECTORY_DESC' , 'If sessions are file based, store them in this directory.');
define('SESSION_FORCE_COOKIE_USE_TITLE' , 'Force Cookie Use');
define('SESSION_FORCE_COOKIE_USE_DESC' , 'Force the use of sessions when cookies are only enabled (Default &quot;true&quot;)<br /><br /><span class="col-red"><strong>CAUTION:</strong></span> This feature prevents the session ID in the URL');
define('SESSION_CHECK_SSL_SESSION_ID_TITLE' , 'Check SSL Session ID');
define('SESSION_CHECK_SSL_SESSION_ID_DESC' , 'Validate the SSL_SESSION_ID on every secure HTTPS page request. (Default &quot;false&quot;)'.SESSION_WARNING);
define('SESSION_CHECK_USER_AGENT_TITLE' , 'Check User Agent');
define('SESSION_CHECK_USER_AGENT_DESC' , 'Validate the client\'s browser user agent on every page request. (Default &quot;false&quot;)'.SESSION_WARNING);
define('SESSION_CHECK_IP_ADDRESS_TITLE' , 'Check IP Address');
define('SESSION_CHECK_IP_ADDRESS_DESC' , 'Validate the client\'s IP address on every page request. (Default &quot;false&quot;)'.SESSION_WARNING);
define('SESSION_RECREATE_TITLE' , 'Recreate Session');
define('SESSION_RECREATE_DESC' , 'Recreate the session to generate a new session ID when a customer logs on or creates an account (PHP >=4.1 needed). (Default &quot;false&quot;)'.SESSION_WARNING);
define('SESSION_DELETE_OLD_COOKIES_TITLE' , 'Delete Session Cookie');
define('SESSION_DELETE_OLD_COOKIES_DESC' , 'Should old session cookies be deleted? After updating from shop version <= 2.0.1.0 it is recommended to enable this option. (Default &quot;false&quot;)'.SESSION_WARNING);

define('DISPLAY_CONDITIONS_ON_CHECKOUT_TITLE' , 'Display Conditions on Checkout');
define('DISPLAY_CONDITIONS_ON_CHECKOUT_DESC' , 'Display terms and conditions on checkout.');
define('SIGN_CONDITIONS_ON_CHECKOUT_TITLE' , 'Sign Conditions on Checkout');
define('SIGN_CONDITIONS_ON_CHECKOUT_DESC' , 'Request approval of terms and conditions and on checkout.<br/><b>Attention:</b> "Display Conditions on Checkout" must be enabled.');

define('META_MIN_KEYWORD_LENGTH_TITLE' , 'Min. Meta-Keyword Length');
define('META_MIN_KEYWORD_LENGTH_DESC' , 'min. length of a single keyword (generated from products description)');
define('META_KEYWORDS_NUMBER_TITLE' , 'Number of Meta-Keywords');
define('META_KEYWORDS_NUMBER_DESC' , 'number of keywords');
define('META_AUTHOR_TITLE' , 'Author');
define('META_AUTHOR_DESC' , '<meta name="author">');
define('META_PUBLISHER_TITLE' , 'Publisher');
define('META_PUBLISHER_DESC' , '<meta name="publisher">');
define('META_COMPANY_TITLE' , 'Company');
define('META_COMPANY_DESC' , '<meta name="company">');
define('META_TOPIC_TITLE' , 'page-topic');
define('META_TOPIC_DESC' , '<meta name="page-topic">');
define('META_REPLY_TO_TITLE' , 'Reply-To');
define('META_REPLY_TO_DESC' , '<meta name="reply-to">');
define('META_REVISIT_AFTER_TITLE' , 'Revisit-After');
define('META_REVISIT_AFTER_DESC' , '<meta name="revisit-after">');
define('META_ROBOTS_TITLE' , 'Robots');
define('META_ROBOTS_DESC' , '<meta name="robots">');
define('META_DESCRIPTION_TITLE' , 'Description');
define('META_DESCRIPTION_DESC' , '<meta name="description">');
define('META_KEYWORDS_TITLE' , 'Keywords');
define('META_KEYWORDS_DESC' , '<meta name="keywords">');

define('MODULE_PAYMENT_INSTALLED_TITLE' , 'Installed Payment Modules');
define('MODULE_PAYMENT_INSTALLED_DESC' , 'List of payment module filenames separated by semi-colon. The list is  updated automatically. No need to edit. (Example: cc.php;cod.php;paypal.php)');
define('MODULE_ORDER_TOTAL_INSTALLED_TITLE' , 'Installed Order Total Modules');
define('MODULE_ORDER_TOTAL_INSTALLED_DESC' , 'List of order_total module filenames separated by a semi-colon. The list is updated automatically. No need to edit. (Example: ot_subtotal.php;ot_tax.php;ot_shipping.php;ot_total.php)');
define('MODULE_SHIPPING_INSTALLED_TITLE' , 'Installed Shipping Modules');
define('MODULE_SHIPPING_INSTALLED_DESC' , 'List of shipping module filenames separated by a semi-colon. The list is updated automatically. No need to edit. (Example: ups.php;flat.php;item.php)');

define('CACHE_LIFETIME_TITLE','Template Cache Lifetime');
define('CACHE_LIFETIME_DESC','The number of seconds cached content will persist');
define('CACHE_CHECK_TITLE','Check Template Cache');
define('CACHE_CHECK_DESC','If active, then with cached content, If-Modified-Since headers are accounted for, and appropriate HTTP headers are sent. This way repeated hits to a cached page do not send the entire page to the client every time.');

define('PRODUCT_REVIEWS_VIEW_TITLE','Reviews in Product Details');
define('PRODUCT_REVIEWS_VIEW_DESC','Number of reviews displayed on the product details page');

define('DELETE_GUEST_ACCOUNT_TITLE','Delete Guest Accounts');
define('DELETE_GUEST_ACCOUNT_DESC','Shold guest accounts be deleted after placing orders? (Order data will be saved)');

define('USE_WYSIWYG_TITLE','Activate WYSIWYG Editor');
define('USE_WYSIWYG_DESC','Activate WYSIWYG editor for CMS and products');

define('PRICE_IS_BRUTTO_TITLE','Gross Admin');
define('PRICE_IS_BRUTTO_DESC','Usage of prices with tax in admin');

define('PRICE_PRECISION_TITLE','Gross/Net Precision');
define('PRICE_PRECISION_DESC','Gross/Net precision (Does not affect the display in the shop, which always displays 2 decimal places.)');

define('CHECK_CLIENT_AGENT_TITLE','Prevent Spider Sessions');
define('CHECK_CLIENT_AGENT_DESC','Prevent known spiders from starting a session.');
define('SHOW_IP_LOG_TITLE','IP-Log on Checkout?');
define('SHOW_IP_LOG_DESC','Show Text "Your IP will be saved", in checkout?');

define('ACTIVATE_GIFT_SYSTEM_TITLE','Activate Gift Voucher System');
define('ACTIVATE_GIFT_SYSTEM_DESC','Activate gift voucher system<br/><b>Attention:</b> You have to install the Modules ot_coupon <a href="'.xtc_href_link(FILENAME_MODULES, 'set=ordertotal&module=ot_coupon').'"><b>here</b></a> and ot_gv <a href="'.xtc_href_link(FILENAME_MODULES, 'set=ordertotal&module=ot_gv').'"><b>here</b></a>.');

define('SECURITY_CODE_LENGTH_TITLE', 'Length of Gift Voucher Code');
define('SECURITY_CODE_LENGTH_DESC', 'Enter here the length of the Gift Voucher Code (max. 16 characters)');

define('NEW_SIGNUP_GIFT_VOUCHER_AMOUNT_TITLE', 'Welcome Gift Voucher Amount');
define('NEW_SIGNUP_GIFT_VOUCHER_AMOUNT_DESC', 'Welcome Gift Voucher Amount: If you do not wish to send a Gift Voucher in your create account E-Mail, put 0 for no amount, else place the amount here, i.e. 10.00 or 50.00, no currency signs. The Gift Voucher Code is generated automatically by the store.<br /><b>Attention:</b> Under "Configuration" -> <a href="'.xtc_href_link(FILENAME_CONFIGURATION, 'gID=12').'"><strong>"E-Mail Options"</strong></a> the setting "E-Mail upon Create Account" must be set to "Yes".');
define('NEW_SIGNUP_DISCOUNT_COUPON_TITLE', 'Welcome Discount Coupon Code');
define('NEW_SIGNUP_DISCOUNT_COUPON_DESC', 'Welcome Discount Coupon Code: If you do not want to send a Discount Coupon in your create account E-Mail, leave this field blank, else place the coupon code here you wish to use.<br /><b>Attention:</b> Under "Configuration" -> <a href="'.xtc_href_link(FILENAME_CONFIGURATION, 'gID=12').'"><strong>"E-Mail Options"</strong></a> the setting "E-Mail upon Create Account" must be set to "Yes" and the coupon code must be created in the <a href="'.xtc_href_link(FILENAME_COUPON_ADMIN).'"><b>Coupon Admin</b></a> before.');

define('ACTIVATE_SHIPPING_STATUS_TITLE','Display Shipping Status');
define('ACTIVATE_SHIPPING_STATUS_DESC','Show shipping status? (Different dispatch times can be specified for individual products. If enabled, a new item <b>Delivery Status</b> is displayed on product input)');

define('IMAGE_QUALITY_TITLE','Image Quality');
define('IMAGE_QUALITY_DESC','Image quality (0= highest compression, 100=best quality)');

define('GROUP_CHECK_TITLE','Customer Status Check');
define('GROUP_CHECK_DESC','Restrict access to individual categories, products and content items to specified customer groups (after activation, input fields will appear in categories, products and in content manager');

define('ACTIVATE_REVERSE_CROSS_SELLING_TITLE', 'Reverse Cross-Selling');
define('ACTIVATE_REVERSE_CROSS_SELLING_DESC', 'Activate reverse Cross-selling?');
define('ACTIVATE_CROSS_SELLING_TITLE', 'Cross-Selling');
define('ACTIVATE_CROSS_SELLING_DESC', 'Activate Cross-selling?');

define('ACTIVATE_NAVIGATOR_TITLE','Activate Product Navigator?');
define('ACTIVATE_NAVIGATOR_DESC','Activate/deactivate product navigator in product_info, (deactivate for better performance if lots of articles are present in system)');

define('QUICKLINK_ACTIVATED_TITLE','Activate Multilink / Copy Function');
define('QUICKLINK_ACTIVATED_DESC','Allows selection of multiple categories when performing "copy product to".');

define('DOWNLOAD_UNALLOWED_PAYMENT_TITLE', 'Disallowed Download Payment Modules');
define('DOWNLOAD_UNALLOWED_PAYMENT_DESC', '<strong>DISALLOWED</strong> payment modules for download products.');
define('DOWNLOAD_MIN_ORDERS_STATUS_TITLE', 'Order Status');
define('DOWNLOAD_MIN_ORDERS_STATUS_DESC', 'Order status to allow download of files.');

// Vat Check
define('STORE_OWNER_VAT_ID_TITLE' , 'VAT Reg No of Shop Owner');
define('STORE_OWNER_VAT_ID_DESC' , 'The VAT Reg No of the Shop Owner');
define('DEFAULT_CUSTOMERS_VAT_STATUS_ID_TITLE' , 'Customers Group Approved VAT Reg No (Foreign Country)');
define('DEFAULT_CUSTOMERS_VAT_STATUS_ID_DESC' , 'Customers group for customers whose VAT Reg No has been checked and approved, shop country <> customer\'s country');
define('ACCOUNT_COMPANY_VAT_CHECK_TITLE' , 'Validate VAT Reg No');
define('ACCOUNT_COMPANY_VAT_CHECK_DESC' , 'Customers may enter a VAT Registration number. If false, the box disappears.<br /><b>Attention:</b> Under "Configuration" -> <a href="'.xtc_href_link(FILENAME_CONFIGURATION, 'gID=5').'"><strong>"Customer Details"</strong></a> the setting "Company" must be set to "Yes".');
define('ACCOUNT_COMPANY_VAT_LIVE_CHECK_TITLE' , 'Validate VAT Reg No online for plausability');
define('ACCOUNT_COMPANY_VAT_LIVE_CHECK_DESC' , 'Validate VAT Registration number online for plausability using the webservice of the taxation portal of the EU (<a href="http://ec.europa.eu/taxation_customs" style="font-style:italic">http://ec.europa.eu/taxation_customs</a>).<br/>Requires PHP5 with activated "SOAP" support!<br/><br/><span class="messageStackSuccess">The "PHP5 SOAP"-support is actually '.(in_array ('soap', get_loaded_extensions()) ? '' : '<span class="messageStackError">NOT</span>').' active!</span><br/><br/>');
define('ACCOUNT_COMPANY_VAT_GROUP_TITLE' , 'Automatic Pruning?');
define('ACCOUNT_COMPANY_VAT_GROUP_DESC' , 'Set to true, the customers group will be changed automatically if a valid VAT Reg No is used');
define('ACCOUNT_VAT_BLOCK_ERROR_TITLE' , 'Allow Invalid VAT Reg No?');
define('ACCOUNT_VAT_BLOCK_ERROR_DESC' , 'Set to true, only validated VAT Reg No are accepted');
define('DEFAULT_CUSTOMERS_VAT_STATUS_ID_LOCAL_TITLE','Customers Group - Approved VAT Reg No (Shop country)');
define('DEFAULT_CUSTOMERS_VAT_STATUS_ID_LOCAL_DESC','Customers group for customers whose VAT Reg No has been checked and approved, shop country = customers country');

// Google Conversion
define('GOOGLE_CONVERSION_TITLE','Google Conversion Tracking');
define('GOOGLE_CONVERSION_DESC','Track conversion keywords on orders');
define('GOOGLE_CONVERSION_ID_TITLE','Conversion ID');
define('GOOGLE_CONVERSION_ID_DESC','Your Google conversion ID');
define('GOOGLE_LANG_TITLE','Google Language');
define('GOOGLE_LANG_DESC','ISO code of used language');
define('GOOGLE_CONVERSION_LABEL_TITLE','Google conversion label');
define('GOOGLE_CONVERSION_LABEL_DESC','Your Google conversion label');

// Afterbuy
define('AFTERBUY_ACTIVATED_TITLE','Active');
define('AFTERBUY_ACTIVATED_DESC','Activate afterbuy module');
define('AFTERBUY_PARTNERID_TITLE','Partner ID');
define('AFTERBUY_PARTNERID_DESC','Your Afterbuy Partner ID');
define('AFTERBUY_PARTNERPASS_TITLE','Partner Password');
define('AFTERBUY_PARTNERPASS_DESC','Your partner password for Afterbuy XML module');
define('AFTERBUY_USERID_TITLE','User ID');
define('AFTERBUY_USERID_DESC','Your Afterbuy user ID');
define('AFTERBUY_ORDERSTATUS_TITLE','Order Status');
define('AFTERBUY_ORDERSTATUS_DESC','Order status for exported orders');
define('AFTERBUY_URL','You will find detailed Afterbuy info here: <a href="http://www.afterbuy.de" target="new">http://www.afterbuy.de</a>');
define('AFTERBUY_DEALERS_TITLE', 'mark as Dealer');
define('AFTERBUY_DEALERS_DESC', 'Example: <em>6,5,8</em>');
define('AFTERBUY_IGNORE_GROUPE_TITLE', 'Customer group ignor');
define('AFTERBUY_IGNORE_GROUPE_DESC', 'Example: <em>6,5,8</em>.');

// Search-Options
define('SEARCH_MIN_LENGTH_TITLE', 'Search number of characters');
define('SEARCH_MIN_LENGTH_DESC', 'Minimum number of characters for the search');
define('SEARCH_IN_DESC_TITLE','Search in products descriptions');
define('SEARCH_IN_DESC_DESC','Include products descriptions when searching');
define('SEARCH_IN_ATTR_TITLE','Search in products attributes');
define('SEARCH_IN_ATTR_DESC','Include products attributes when searching');
define('SEARCH_IN_MANU_TITLE','Search in products manufacturers');
define('SEARCH_IN_MANU_DESC','Include products manufacturers when searching');

define('ADMIN_SEARCH_IN_DESC_TITLE','Search in products descriptions');
define('ADMIN_SEARCH_IN_DESC_DESC','Include products descriptions when searching');
define('ADMIN_SEARCH_IN_ATTR_TITLE','Search in products attributes');
define('ADMIN_SEARCH_IN_ATTR_DESC','Include products attributes when searching');

// changes for 3.0.4 SP2
define('REVOCATION_ID_TITLE','Revocation');
define('REVOCATION_ID_DESC','Select content to display revocation');
define('DISPLAY_REVOCATION_ON_CHECKOUT_TITLE','Display right of revocation');
define('DISPLAY_REVOCATION_ON_CHECKOUT_DESC','Display right of revocation on checkout_confirmation.<br/><b>Attention:</b> "Display Conditions on Checkout" must be enabled.');

// BOF - Tomcraft - 2009-11-02 - New admin top menu
define('USE_ADMIN_TOP_MENU_TITLE' , 'Admin Top Navigation');
define('USE_ADMIN_TOP_MENU_DESC' , 'Activate Admin Top Navigation? Otherwise the menu will be displayed on the left (classic view)');
// EOF - Tomcraft - 2009-11-02 - New admin top menu

// BOF - Tomcraft - 2009-11-02 - Admin language tabs
define('USE_ADMIN_LANG_TABS_TITLE' , 'Language Tabs with Categories / Articles');
define('USE_ADMIN_LANG_TABS_DESC' , 'Use language tabs with categories / articles?');
// EOF - Tomcraft - 2009-11-02 - Admin language tabs

// BOF - Hendrik - 2010-08-11 - Thumbnails in admin products list
define('USE_ADMIN_THUMBS_IN_LIST_TITLE' , 'Admin products list images');
define('USE_ADMIN_THUMBS_IN_LIST_DESC' , 'Show an extra column in Admin products list with images of the categories / products?');
define('USE_ADMIN_THUMBS_IN_LIST_STYLE_TITLE', 'Admin products list images CSS-Style');
define('USE_ADMIN_THUMBS_IN_LIST_STYLE_DESC', 'Here, simple CSS style information to be entered - for example, the maximum width: max-width: 90px;');// EOF - Hendrik - 2010-08-11 - Thumbnails in admin products list
// EOF - Hendrik - 2010-08-11 - Thumbnails in admin products list

// BOF - Tomcraft - 2009-11-05 - Advanced contact form
//define('USE_CONTACT_EMAIL_ADDRESS_TITLE' , 'Contact Us - sending option'); // not needed anymore!
//define('USE_CONTACT_EMAIL_ADDRESS_DESC' , 'Use "Contact Us" e-mail address for sending contact form (important for some Hosters like Hosteurope)'); // not needed anymore!
// EOF - Tomcraft - 2009-11-05 - Advanced contact form

// BOF - Dokuman - 2010-02-04 - delete cache files in admin section
define('DELETE_CACHE_SUCCESSFUL', 'Cache deleted successfully.');
define('DELETE_TEMP_CACHE_SUCCESSFUL', 'Templatecache deleted successfully.');
// EOF - Dokuman - 2010-02-04 - delete cache files in admin section

// BOF - web28 - 2010-08-17 -  Bildgrößenberechnung kleinerer Bilder
define('PRODUCT_IMAGE_NO_ENLARGE_UNDER_DEFAULT_TITLE','Upscaling low-res images');
define('PRODUCT_IMAGE_NO_ENLARGE_UNDER_DEFAULT_DESC','If set to <strong>false</strong>, upscaling of low-res images to default settings for image size is disabled. Set to <strong>true</strong> to enable upscaling of low-res images. In this case those images will be shown blurry.');
// EOF - web28 - 2010-08-17 -  Bildgrößenberechnung kleinerer Bilder

//BOF - hendrik - 2011-05-14 - independent invoice number and date
//define('IBN_BILLNR_TITLE', 'Next invoice number');
//define('IBN_BILLNR_DESC', 'When assigning an invoice number, this number is given next.');
//define('IBN_BILLNR_FORMAT_TITLE', 'Invoice number format');
//define('IBN_BILLNR_FORMAT_DESC', 'Format invoice number.: {n}=number, {d}=day, {m}=month, {y}=year, <br>example. "100{n}-{d}-{m}-{y}" => "10099-28-02-2007"');
//EOF - hendrik - 2011-05-14 - independent invoice number and date

//BOC - h-h-h - 2011-12-23 - Button "Buy Now" optional - default off
define('SHOW_BUTTON_BUY_NOW_TITLE', 'Show "Cart"-Button in product lists');
define('SHOW_BUTTON_BUY_NOW_DESC', '<span class="col-red"><strong>CAUTION:</strong></span> This option is legally critical if customers can\'t see all chief product features directly in the product lists.');
//EOC - h-h-h - 2011-12-23 - Button "Buy Now" optional - default off

//split page results
define('MAX_DISPLAY_LIST_PRODUCTS_TITLE', 'Number of products per page');
define('MAX_DISPLAY_LIST_PRODUCTS_DESC', 'Maximum number of products that are to be displayed in the grid per page.');

// Whos online
define ('WHOS_ONLINE_TIME_LAST_CLICK_TITLE', 'Who\'s Online - Display period in seconds');
define ('WHOS_ONLINE_TIME_LAST_CLICK_DESC', 'Timing of online users in the "Who\'s Online" table, afterwhich time the entries are deleted (min value: 900).');

//Sessions
define ('SESSION_LIFE_ADMIN_TITLE', 'Session Lifetime Admin');
define ('SESSION_LIFE_ADMIN_DESC', 'Time in seconds before the session time for Admins expires (logging out) - Default 7200<br />The entered value is only applied if the session handling is db based (configure.php => define(\'STORE_SESSIONS\', \'mysql\');)<br />Maximum value: 14400');
define ('SESSION_LIFE_CUSTOMERS_TITLE', 'Session lifetime customer');
define ('SESSION_LIFE_CUSTOMERS_DESC', 'Time in seconds before the session time for customers expires (logging out) - Default 1440<br />The entered value is only applied if the session handling is db based (configure.php => define(\'STORE_SESSIONS\', \'mysql\');)<br />Maximum value: 14400');

//checkout confirmation options
define ('CHECKOUT_USE_PRODUCTS_SHORT_DESCRIPTION_TITLE', 'Use short description in order confirmation/acknowledgement mails and on the checkout confirmation page?');
define ('CHECKOUT_USE_PRODUCTS_SHORT_DESCRIPTION_DESC', 'If "Yes" the short description will be used in the mentioned circumstances in case the order description is empty.<br />If also the short description is empty the long description will be used, shortened to the number of characters indicated below with "<i>Length of the description when short description is empty</i>".');
define('CHECKOUT_USE_PRODUCTS_DESCRIPTION_FALLBACK_LENGTH_TITLE','Length of the description when short description is empty');
define('CHECKOUT_USE_PRODUCTS_DESCRIPTION_FALLBACK_LENGTH_DESC','At which length the long description shall be cut off in case no short description is available? (text will be augmented with [...])<br />Only applies if "<i>Use short description in order confirmation/acknowledgement mails and on the checkout confirmation page</i>" above is set to "Yes".');
define ('CHECKOUT_SHOW_PRODUCTS_IMAGES_TITLE', 'Order Confirmation page: Product images');
define ('CHECKOUT_SHOW_PRODUCTS_IMAGES_DESC', 'If on the order confirmation page, the product images are displayed?');
define ('CHECKOUT_SHOW_PRODUCTS_MODEL_TITLE', 'Order Confirmation Page: Item no.');
define ('CHECKOUT_SHOW_PRODUCTS_MODEL_DESC', 'on the order confirmation page you want the item number will be displayed.');

// Billing email attachments
define ('EMAIL_BILLING_ATTACHMENTS_TITLE', 'Billing - E-Mail attachments for orders');
define ('EMAIL_BILLING_ATTACHMENTS_DESC', 'Example of attachments - assumed that the files are in the shop directory <b>/media/content/</b>, separate multiple attachments with comma and no space:<br />media/content/agb.pdf,media/content/widerruf.pdf,media/content/datenschutz.pdf');

// email images
define ('SHOW_IMAGES_IN_EMAIL_TITLE', 'Product Images in Order - Insert email');
define ('SHOW_IMAGES_IN_EMAIL_DESC', 'Product images in the HTML order confirmation - Insert Email (increases risk, which is classified the e-mail as SPAM)');
define ('SHOW_IMAGES_IN_EMAIL_DIR_TITLE', 'Email pictures folder');
define ('SHOW_IMAGES_IN_EMAIL_DIR_DESC', 'Select email pictures folder');
define ('SHOW_IMAGES_IN_EMAIL_STYLE_TITLE', 'Email images CSS style');
define ('SHOW_IMAGES_IN_EMAIL_STYLE_DESC', 'Here, simple CSS style information to be entered - for example, the maximum width: max-width: 90px;');

// Popup window configuration
define ('POPUP_SHIPPING_LINK_PARAMETERS_TITLE', 'Returns popup window URL parameter');
define ('POPUP_SHIPPING_LINK_PARAMETERS_DESC', 'Here, the URL parameters are entered - Default: & Keep This = true & type = spare true & height = 400 & width = 600');
define ('POPUP_SHIPPING_LINK_CLASS_TITLE', 'Returns popup CSS class');
define ('POPUP_SHIPPING_LINK_CLASS_DESC', 'Here CSS classes to be entered - Default: thickbox');
define ('POPUP_CONTENT_LINK_PARAMETERS_TITLE', 'content pages, pop-up URL parameters');
define ('POPUP_CONTENT_LINK_PARAMETERS_DESC', 'Here, the URL parameters are entered - Default: & Keep This = true & type = spare true & height = 400 & width = 600');
define ('POPUP_CONTENT_LINK_CLASS_TITLE', 'content pages popup CSS class');
define ('POPUP_CONTENT_LINK_CLASS_DESC', 'Here CSS classes to be entered - Default: thickbox');
define ('POPUP_PRODUCT_LINK_PARAMETERS_TITLE', 'Product pages popup URL parameter');
define ('POPUP_PRODUCT_LINK_PARAMETERS_DESC', 'Here, the URL parameters are entered - Default: & Keep This = true & type = spare true & height = 450 & width = 750');
define ('POPUP_PRODUCT_LINK_CLASS_TITLE', 'Product pages popup CSS class');
define ('POPUP_PRODUCT_LINK_CLASS_DESC', 'Here CSS classes to be entered - Default: thickbox');
define ('POPUP_COUPON_HELP_LINK_PARAMETERS_TITLE', 'Coupon Help popup window URL parameter');
define ('POPUP_COUPON_HELP_LINK_PARAMETERS_DESC', 'Here, the URL parameters are entered - Default: & Keep This = true & type = spare true & height = 450 & width = 750');
define ('POPUP_COUPON_HELP_LINK_CLASS_TITLE', 'Coupon Help popup CSS class');
define ('POPUP_COUPON_HELP_LINK_CLASS_DESC', 'Here CSS classes to be entered - Default: thickbox');

define ('POPUP_PRODUCT_PRINT_SIZE_TITLE', 'product Print view window size');
define ('POPUP_PRODUCT_PRINT_SIZE_DESC', 'Sets the size of the popup window to be defined - default: width = 640, height = 600');
define ('POPUP_PRINT_ORDER_SIZE_TITLE', 'order window size Print view');
define ('POPUP_PRINT_ORDER_SIZE_DESC', 'Sets the size of the popup window to be defined - default: width = 640, height = 600');

define ('CONFIRM_SAVE_ENTRY_TITLE', 'Confirmation when saving articles/category');
define ('CONFIRM_SAVE_ENTRY_DESC', 'Should be made a confirmation message when saving products/categories? Default: true (yes)');

define('WHOS_ONLINE_IP_WHOIS_SERVICE_TITLE', 'Who\'s Online - Whois Lookup URL');
define('WHOS_ONLINE_IP_WHOIS_SERVICE_DESC', 'https://utrace.me/?query= or https://whois.domaintools.com/');

define('STOCK_CHECKOUT_UPDATE_PRODUCTS_STATUS_TITLE', 'Completion of order - disable Sold out?');
define('STOCK_CHECKOUT_UPDATE_PRODUCTS_STATUS_DESC', 'If a sold-out items (stock 0) be disabled at the end of the order automatically? The article is no longer visible in the shop! <br /> On Products are available again shortly, the option should be set to "false"');

define('SEND_EMAILS_DOUBLE_OPT_IN_TITLE','Double-Opt-In for Newsletter registration.');
define('SEND_EMAILS_DOUBLE_OPT_IN_DESC','If "true" an eMail will be send where the Registration have to be confirmed. This  only works if send eMails is activated.');

define('USE_ADMIN_FIXED_TOP_TITLE', 'Fixate admin page header?'); 
define('USE_ADMIN_FIXED_TOP_DESC', 'Shall the page header always be visable when scrolling?');
define('USE_ADMIN_FIXED_SEARCH_TITLE', 'Always display admin searchbar?'); 
define('USE_ADMIN_FIXED_SEARCH_DESC', 'Shall the admin searchbar always be visable?');

define('SMTP_SECURE_TITLE' , 'SMTP SECURE');
define('SMTP_SECURE_DESC' , 'Does the SMTP server require a secure connection? Contact your ISP for the appropriate settings.');

define('DISPLAY_ERROR_REPORTING_TITLE', 'Error reporting');
define('DISPLAY_ERROR_REPORTING_DESC', 'Display formatted error reporting in footer?');

define('DISPLAY_ERROR_REPORTING_LEVEL_TITLE', 'Error Reporting Level');
define('DISPLAY_ERROR_REPORTING_LEVEL_DESC', 'For which errors should a log be written?');

define('DISPLAY_BREADCRUMB_OPTION_TITLE', 'Breadcrumb navigation');
define('DISPLAY_BREADCRUMB_OPTION_DESC', '<strong>name:</strong> In the breadcrumb navigation, the article name is displayed.<br /><strong>model:</strong> In the breadcrumb navigation, the item number is displayed if it is available. Otherwise fallback to the article name.');

define('EMAIL_WORD_WRAP_TITLE', 'WordWrap for text e-mails');
define('EMAIL_WORD_WRAP_DESC', 'Indicate number of characters for one line in text e-mails before text will be wrapped (only whole numbers).<br /><strong>Attention:</strong> A character count greater than 76 may cause the shop mails to be categorized as SPAM by SpamAssassin.<br />More infos <a href="http://wiki.apache.org/spamassassin/Rules/MIME_QP_LONG_LINE" target="_blank">here</a>.');

//define('USE_PAGINATION_LIST_TITLE', 'Pagination List'); // Tomcraft - 2017-07-12 - Not used anymore since r10840, see: http://trac.modified-shop.org/ticket/1238
//define('USE_PAGINATION_LIST_DESC', 'Use a HTML list (ul / li Tag) for Pagination.<br/><b>Attention:</b> This only works with a shop version 2.0.0.0 compatible template!'); // Tomcraft - 2017-07-12 - Not used anymore since r10840, see: http://trac.modified-shop.org/ticket/1238

define('ORDER_STATUSES_FOR_SALES_STATISTICS_TITLE', 'Sales Report Filter');
define('ORDER_STATUSES_FOR_SALES_STATISTICS_DESC', 'Choose the order statuses which shall be considered on the admin startpage and in the sales report when choosing "Sales Report Filter" in the status dropdown.<br />(To show only the real sales volume, choose the order status for completed orders.)<br /><b>Note:</b> For the "Sales Report Filter" to be displayed in the status dropdown, you have to choose at least two statuses. Otherwise you can directly choose the desired status in the dropdown.');

define('SAVE_IP_LOG_TITLE', 'Save IP Address');
define('SAVE_IP_LOG_DESC', 'Save the IP Address to database?<br/>With Option xxx the IP will be anonymous.');

define('META_MAX_KEYWORD_LENGTH_TITLE', 'Maximum Length Meta-Keywords');
define('META_MAX_KEYWORD_LENGTH_DESC', 'Maximum Length automatic generated Meta-Keywords');
define('META_DESCRIPTION_LENGTH_TITLE', 'Length Meta-Description');
define('META_DESCRIPTION_LENGTH_DESC', 'Maximum Length of description (Letters)');
define('META_STOP_WORDS_TITLE', 'Stop Words');
define('META_STOP_WORDS_DESC', 'Please enter comma separated keywords that are not allowed.');
define('META_GO_WORDS_TITLE', 'Go Words');
define('META_GO_WORDS_DESC', 'Please enter comma separated keywords that are allowed.');

//BOC added text constants for group id 20, noRiddle
define('CSV_TEXTSIGN_TITLE','Text qualifier');
define('CSV_TEXTSIGN_DESC','eg. " &nbsp; | &nbsp; <span style="color:#c00;">With semicolon as delimiter the text qualifier should be set to "!</span>');
define('CSV_SEPERATOR_TITLE','Delimiter');
define('CSV_SEPERATOR_DESC','eg. ; &nbsp; | &nbsp;<span Style="color:#c00;">If the input field is left empty, \\t (= tab) is used by default during export/import!</span> ');
define('COMPRESS_EXPORT_TITLE','Compression');
define('COMPRESS_EXPORT_DESC','Compress export file');
define('CSV_CATEGORY_DEFAULT_TITLE','Category for Import');
define('CSV_CATEGORY_DEFAULT_DESC','All products in the csv-importfile that do <b>not</b> have a category defined will be imported into this category.<br/><b>Attention:</b> If you do not want to import products which have no category defined, then select category "Top" as it is not possible to import into this category.');
define('CSV_CAT_DEPTH_TITLE','Category depth');
define('CSV_CAT_DEPTH_DESC','How deep shall the category tree go? (e.g. with default 4: main category plus 3 sub-categories)<br />This indication is important to get the in the CSV integrated categories imported well. Same applies to the export function.<br /><span style="color:#c00;">More than 4 may result in performance loss and is probably not user friendly!');
//EOC added text constants for group id 20, noRiddle

define('MIN_GROUP_PRICE_STAFFEL_TITLE', 'Additional Graduated Price');
define('MIN_GROUP_PRICE_STAFFEL_DESC', 'Additional Graduated Price to show.');

define('MODULE_CAPTCHA_ACTIVE_TITLE', 'Activate Captcha');
define('MODULE_CAPTCHA_ACTIVE_DESC', 'For which shop sections shall the Captcha be activated?');
define('MODULE_CAPTCHA_LOGGED_IN_TITLE', 'Logged in Customers');
define('MODULE_CAPTCHA_LOGGED_IN_DESC', 'Show Captcha for logged in customers');
define('MODULE_CAPTCHA_LOGIN_NUM_TITLE', 'Login attempts');
define('MODULE_CAPTCHA_LOGIN_NUM_DESC', 'Number of failed login attempts before the captcha is displayed.');
define('CAPTCHA_MOD_CLASS_TITLE', 'Captcha Module');
define('CAPTCHA_MOD_CLASS_DESC', 'Choose the Captcha module to use. Make sure that the module is also installed.');
define('CFG_TXT_MODIFIED_CAPTCHA', 'Standard');
define('CFG_TXT_PHP_CAPTCHA', 'PhpCaptcha');

define('SHIPPING_STATUS_INFOS_TITLE', 'Shippingtime');
define('SHIPPING_STATUS_INFOS_DESC', 'Select content to display Information for Shippingtime');

define('USE_SHORT_DATE_FORMAT_TITLE', 'Show Date in short format');
define('USE_SHORT_DATE_FORMAT_DESC', 'Always show date in short format: <b> 01/03/2014 </b> instead <b> Saturday 01 March 2014 </b> <br /> Recommended for display errors with the long date format as incorrect language or special signs!');

define('MAX_DISPLAY_PRODUCTS_CATEGORY_TITLE', 'Maximum Products');
define('MAX_DISPLAY_PRODUCTS_CATEGORY_DESC', 'Maximum products of same category');
define('MAX_DISPLAY_ADVANCED_SEARCH_RESULTS_TITLE', 'Number of Search Results');
define('MAX_DISPLAY_ADVANCED_SEARCH_RESULTS_DESC', 'Maximum number of products to be displayed per page in the search results (advanced_search_result.php).');
define('MAX_DISPLAY_PRODUCTS_HISTORY_TITLE' , 'Maximum History');
define('MAX_DISPLAY_PRODUCTS_HISTORY_DESC' , 'Maximum visited products in account history');
define('MAX_DISPLAY_SEARCH_AC_RESULTS_TITLE' , 'Number of Autocomplete Search Results');
define('MAX_DISPLAY_SEARCH_AC_RESULTS_DESC' , 'Maximum number of products to be displayed in the autocomplete search results');

define('SEARCH_RESULTS_SORT_TITLE', 'Search Sort Order');
define('SEARCH_RESULTS_SORT_DESC', 'This is the sort order used for search.');
define('SEARCH_RESULTS_FIELD_TITLE', 'Search Sort Field');
define('SEARCH_RESULTS_FIELD_DESC', 'The column to sort by for search.');

define('PRODUCT_IMAGE_SHOW_NO_IMAGE_TITLE', 'Product noimage.gif');
define('PRODUCT_IMAGE_SHOW_NO_IMAGE_DESC', 'Show noimage.gif if there is no product image assigned');
define('CATEGORIES_IMAGE_SHOW_NO_IMAGE_TITLE', 'Category noimage.gif');
define('CATEGORIES_IMAGE_SHOW_NO_IMAGE_DESC', 'Show noimage.gif if there is no category image assigned');
define('MANUFACTURER_IMAGE_SHOW_NO_IMAGE_TITLE', 'Manufacturer noimage.gif');
define('MANUFACTURER_IMAGE_SHOW_NO_IMAGE_DESC', 'Show noimage.gif if there is no manufacturer image assigned');

define('MODULE_SMALL_BUSINESS_TITLE', 'Small Business');
define('MODULE_SMALL_BUSINESS_DESC', 'Shall the store be switched to small business according to &sect; 19 UStG.<br/><b>Important:</b> Under "Modules" -> "Order Total" the module "ot_tax" must be disabled or uninstalled <a href="'.xtc_href_link(FILENAME_MODULES, 'set=ordertotal&module=ot_tax').'"><b>here</b></a>. In addition you have to set "Prices incl. Tax" to "No" in the particular <a href="'.xtc_href_link(FILENAME_CUSTOMERS_STATUS, '').'"><b>customer groups</b></a>.');

define('COMPRESS_HTML_OUTPUT_TITLE', 'HTML Compression');
define('COMPRESS_HTML_OUTPUT_DESC', 'Compress HTML Output from the Template?');
define('COMPRESS_STYLESHEET_TITLE', 'CSS Compression');
define('COMPRESS_STYLESHEET_DESC', 'Compress Stylesheet?<br/><b>Attention:</b> This only works with a shop version 2.0.0.0 compatible template!');
define('COMPRESS_JAVASCRIPT_TITLE', 'JavaScript Compression');
define('COMPRESS_JAVASCRIPT_DESC', 'Compress JavaScript?<br/><b>Attention:</b> This only works with a shop version 2.0.1.0 compatible template!');

define('USE_ATTRIBUTES_IFRAME_TITLE', 'Attributes and Features in iframe');
define('USE_ATTRIBUTES_IFRAME_DESC', 'Open Attribute and Features Manager in the Category / Product view in an iframe');

define('ADMIN_HEADER_X_FRAME_OPTIONS_TITLE', 'Admin Clickjacking Protection');
define('ADMIN_HEADER_X_FRAME_OPTIONS_DESC', 'Protect Adminarea with Header "X-Frame-Options: SAMEORIGIN"<br>Supported Browsers: FF 3.6.9+ Chrome 4.1.249.1042+ IE 8+ Safari 4.0+ Opera 10.50+ ');

define('SEND_MAIL_ACCOUNT_CREATED_TITLE', 'E-Mail upon Create Account');
define('SEND_MAIL_ACCOUNT_CREATED_DESC', 'Send an E-Mail to customer upon account creation?');

define('STATUS_EMAIL_SENT_COPY_TO_ADMIN_TITLE', 'E-Mail upon status change');
define('STATUS_EMAIL_SENT_COPY_TO_ADMIN_DESC', 'Send an E-Mail to admin upon status change of order?');

define('ORDER_EMAIL_SEND_COPY_TO_ADMIN_TITLE', 'E-Mail order confirmation to admin');
define('ORDER_EMAIL_SEND_COPY_TO_ADMIN_DESC', 'Send a copy of the order confirmation E-Mail to admin?');

define('STOCK_CHECK_SPECIALS_TITLE', 'Check Specials Stock');
define('STOCK_CHECK_SPECIALS_DESC', 'Check to see if sufficent specials stock is available<br/><br/><b>ATTENTION:</b> If there is insufficient specials stock, the order can only be processed after a reduction of the quantity.');

define('DOWNLOAD_SHOW_LANG_DROPDOWN_TITLE', 'Countries dropdown in cart');
define('DOWNLOAD_SHOW_LANG_DROPDOWN_DESC', 'Show countries dropdown in cart if only download products are buyed?');

define('GUEST_ACCOUNT_EDIT_TITLE', 'Edit guest accounts');
define('GUEST_ACCOUNT_EDIT_DESC', 'enable guest accounts to see and edit avvount details?');

define('EMAIL_SIGNATURE_ID_TITLE', 'E-Mail signature');
define('EMAIL_SIGNATURE_ID_DESC', 'Select the content to be used for the signature in shop E-Mails.');

define('POLICY_MIN_LOWER_CHARS_TITLE', 'Password lower case');
define('POLICY_MIN_LOWER_CHARS_DESC', 'How many lower case signs should to the password at least have?');
define('POLICY_MIN_UPPER_CHARS_TITLE', 'Password upper case');
define('POLICY_MIN_UPPER_CHARS_DESC', 'How many upper case signs should to the password at least have?');
define('POLICY_MIN_NUMERIC_CHARS_TITLE', 'Password Numbers');
define('POLICY_MIN_NUMERIC_CHARS_DESC', 'How many numeric signs should to the password at least have?');
define('POLICY_MIN_SPECIAL_CHARS_TITLE', 'Password special chars');
define('POLICY_MIN_SPECIAL_CHARS_DESC', 'How many special chars signs should to the password at least have?');

define('SHOW_SHIPPING_EXCL_TITLE', 'Shippingcost excl.');
define('SHOW_SHIPPING_EXCL_DESC', 'Show excl. or incl. shippingcost');

define('ACCOUNT_TELEPHONE_OPTIONAL_TITLE', 'Telephone number optional');
define('ACCOUNT_TELEPHONE_OPTIONAL_DESC', 'Telephone number only optional on registration?');

define('NEW_ATTRIBUTES_STYLING_TITLE', 'Attribute Manager Styling');
define('NEW_ATTRIBUTES_STYLING_DESC', 'Enable styling of the checkboxes/dropdowns in the attribute manager? Set it to "No" if you experience problems with a huge number of attributes and performance problems.');

define('DB_CACHE_TYPE_TITLE', 'Cache Engine');
define('DB_CACHE_TYPE_DESC', 'Choose an available Engine for caching');

define('META_PRODUCTS_KEYWORDS_LENGTH_TITLE', 'Length of extra words for Search');
define('META_PRODUCTS_KEYWORDS_LENGTH_DESC', 'Maximum Length of extra words for Search (Letters)');
define('META_KEYWORDS_LENGTH_TITLE', 'Length Meta-Keywords');
define('META_KEYWORDS_LENGTH_DESC', 'Maximum Length of Keywords (Letters)');
define('META_TITLE_LENGTH_TITLE', 'Length Meta-Title');
define('META_TITLE_LENGTH_DESC', 'Maximum Length of Title (Letters)');
define('META_CAT_SHOP_TITLE_TITLE', 'Shop-Title Categories');
define('META_CAT_SHOP_TITLE_DESC', 'Add Shop-Title to Categories?');
define('META_PROD_SHOP_TITLE_TITLE', 'Shop-Title Products');
define('META_PROD_SHOP_TITLE_DESC', 'Add Shop-Title to Products?');
define('META_CONTENT_SHOP_TITLE_TITLE', 'Shop-Title Contents');
define('META_CONTENT_SHOP_TITLE_DESC', 'Add Shop-Title to Contents?');
define('META_SPECIALS_SHOP_TITLE_TITLE', 'Shop-Title Specials');
define('META_SPECIALS_SHOP_TITLE_DESC', 'Add Shop-Title to Specials?');
define('META_NEWS_SHOP_TITLE_TITLE', 'Shop-Title New Products');
define('META_NEWS_SHOP_TITLE_DESC', 'Add Shop-Title to New Products?');
define('META_SEARCH_SHOP_TITLE_TITLE', 'Shop-Title Search');
define('META_SEARCH_SHOP_TITLE_DESC', 'Add Shop-Title to search results?');
define('META_OTHER_SHOP_TITLE_TITLE', 'Shop-Title other pages');
define('META_OTHER_SHOP_TITLE_DESC', 'Add Shop-Title all other pages?');
define('META_GOOGLE_VERIFICATION_KEY_TITLE', 'Google Verification Key');
define('META_GOOGLE_VERIFICATION_KEY_DESC', '<meta name="google-site-verification">');
define('META_BING_VERIFICATION_KEY_TITLE', 'Bing Verification Key');
define('META_BING_VERIFICATION_KEY_DESC', '<meta name="msvalidate.01">');

define('NEW_SELECT_CHECKBOX_TITLE', 'Admin Styling');
define('NEW_SELECT_CHECKBOX_DESC', 'Use Styling for Checkboxes/Dropdowns?');
define('CSRF_TOKEN_SYSTEM_TITLE', 'Admin Token System');
define('CSRF_TOKEN_SYSTEM_DESC', 'Use Token System in admin area?<br/><b>Attention:</b> The Token System is used to secure the admin area.');

define('DISPLAY_FILTER_INDEX_TITLE', 'Filter display per page - Products');
define('DISPLAY_FILTER_INDEX_DESC', 'Please enter comma separated values for the selection. For all products enter all.<br/>E.g.: 3,12,27,all');
define('DISPLAY_FILTER_SPECIALS_TITLE', 'Filter display per page - Specials');
define('DISPLAY_FILTER_SPECIALS_DESC', 'Please enter comma separated values for the selection. For all products enter all.<br/>E.g.: 3,12,27,all');
define('DISPLAY_FILTER_PRODUCTS_NEW_TITLE', 'Filter display per page - New Products');
define('DISPLAY_FILTER_PRODUCTS_NEW_DESC', 'Please enter comma separated values for the selection. For all products enter all.<br/>E.g.: 3,12,27,all');
define('DISPLAY_FILTER_ADVANCED_SEARCH_RESULT_TITLE', 'Filter display per page - Search results');
define('DISPLAY_FILTER_ADVANCED_SEARCH_RESULT_DESC', 'Please enter comma separated values for the selection. For all products enter all.<br/>E.g.: 4,12,32,all');

define('USE_BROWSER_LANGUAGE_TITLE' , 'Switch to browser language');
define('USE_BROWSER_LANGUAGE_DESC' , 'Automatically switch language to customers browser language.');

define('WYSIWYG_SKIN_TITLE' , 'WYSIWYG Editor Skin');
define('WYSIWYG_SKIN_DESC' , 'Choose the skin for the WYSIWYG Editor.');

define('CHECK_CHEAPEST_SHIPPING_MODUL_TITLE', 'Preselect cheapest shipping method');
define('CHECK_CHEAPEST_SHIPPING_MODUL_DESC', 'Shall the cheapest shipping method be preselected in checkout for the customer?');

define('DISPLAY_PRIVACY_CHECK_TITLE', 'Sign privacy notice');
define('DISPLAY_PRIVACY_CHECK_DESC', 'Request approval of privacy notice during account creation, contact page, newsletter registration and checkout?)');

define('SHOW_SELFPICKUP_FREE_TITLE', 'Shipping module "Self Pickup" on "free shipping"');
define('SHOW_SELFPICKUP_FREE_DESC', 'Shall the shipping module "Self Pickup (selfpickup)" (if installed) be displayed upon reaching the amount for "free shipping" in module "Shipping (ot_shipping)"?');

define('CHECK_FIRST_PAYMENT_MODUL_TITLE', 'Preselect first payment method');
define('CHECK_FIRST_PAYMENT_MODUL_DESC', 'Shall the first payment method be preselected in checkout for the customer?');

define('ATTRIBUTES_VALID_CHECK_TITLE', 'Attribute validation');
define('ATTRIBUTES_VALID_CHECK_DESC', 'Checks products in customers cart for attributes, that are no longer valid.<br/>(This can occur, if a customer revisits the shop after a long time and purchases a product from a previous visit that remained in the cart.)<br/><b>Note:</b> For extensions that expand the attributes such as text field, this check must be disabled.');

define('ATTRIBUTE_MODEL_DELIMITER_TITLE', 'Product-/Attribute-No. delimiter');
define('ATTRIBUTE_MODEL_DELIMITER_DESC', 'Delimiter between product number &amp; attribute product number');

define('STORE_PAGE_PARSE_TIME_THRESHOLD_TITLE' , 'Threshold for storing the page parse time');
define('STORE_PAGE_PARSE_TIME_THRESHOLD_DESC' , 'Determines the threshold in seconds for storing the page parse time.');

define('SEARCH_IN_FILTER_TITLE', 'Search in products features');
define('SEARCH_IN_FILTER_DESC', 'Include products features when searching');
define('SEARCH_AC_STATUS_TITLE','Autocomplete search');
define('SEARCH_AC_STATUS_DESC','Activate for autocomplete search<br/><b>Attention:</b> This only works with a shop version 2.0.0.0 compatible template!');
define('SEARCH_AC_MIN_LENGTH_TITLE', 'Autocomplete number of characters');
define('SEARCH_AC_MIN_LENGTH_DESC', 'Number of characters to display first search results<br/><b>Attention:</b> This only works with a shop version 2.0.0.0 compatible template!');
define('SEARCH_AC_CATEGORIES_TITLE', 'Categories dropdown in search box');
define('SEARCH_AC_CATEGORIES_DESC', 'Activate for displaying a categories dropdown in front of the search field in search box.');

define('DISPLAY_PRIVACY_ON_CHECKOUT_TITLE', 'Display privacy notice on checkout');
define('DISPLAY_PRIVACY_ON_CHECKOUT_DESC', 'Display an additional privacy notice on checkout?<br/><b>Attention:</b> If an additional checkbox shall be displayed, you need to set "Sign privacy notice" to "Yes"');
define('DISPLAY_REVOCATION_VIRTUAL_ON_CHECKOUT_TITLE', 'Display right of withdrawal for Downloads');
define('DISPLAY_REVOCATION_VIRTUAL_ON_CHECKOUT_DESC', 'Display a checkbox to inform the customer, that the right of withdrawal expires for downloads?');
define('ORDER_STATUSES_DISPLAY_DEFAULT_TITLE', 'Display Orders');
define('ORDER_STATUSES_DISPLAY_DEFAULT_DESC', 'Orders with which status will be shown by default?');

define('INVOICE_INFOS_TITLE', 'Invoice data');
define('INVOICE_INFOS_DESC', 'Choose a content site. The content will be displayed on invoices.');

define('CATEGORIES_SHOW_PRODUCTS_SUBCATS_TITLE', 'Show products from subcategories');
define('CATEGORIES_SHOW_PRODUCTS_SUBCATS_DESC', 'Show all products from subcategories in products listing?');

define('SEO_URL_MOD_CLASS_TITLE', 'URL Module');
define('SEO_URL_MOD_CLASS_DESC', 'Select an URL Module.');

define('MODULE_BANNER_MANAGER_STATUS_TITLE', 'Banner Manager');
define('MODULE_BANNER_MANAGER_STATUS_DESC', 'Activate Banner Manager?');

define('MODULE_NEWSLETTER_STATUS_TITLE', 'Newsletter');
define('MODULE_NEWSLETTER_STATUS_DESC', 'Activate Newsletter System?');

define('EMAIL_ARCHIVE_ADDRESS_TITLE', 'Archive - E-Mail-Address');
define('EMAIL_ARCHIVE_ADDRESS_DESC', 'Please enter an e-mail address for archiving all outgoing e-mails. This is used to send the e-mails to an archive mailbox via BCC.');

define('DISPLAY_HEADQUARTER_ON_CHECKOUT_TITLE', 'Company Headquarter on Checkout');
define('DISPLAY_HEADQUARTER_ON_CHECKOUT_DESC', 'Display Company Headquarter during checkout?');

define('MODULE_NEWSLETTER_VOUCHER_AMOUNT_TITLE', 'Newsletter Voucher Amount');
define('MODULE_NEWSLETTER_VOUCHER_AMOUNT_DESC', 'For newsletter registration gift voucher place the amount, put 0 for no amount.');
define('MODULE_NEWSLETTER_DISCOUNT_COUPON_TITLE', 'Newsletter Coupon Code');
define('MODULE_NEWSLETTER_DISCOUNT_COUPON_DESC', 'For newsletter registration coupon place the coupon code you wish to use or leave this field blank to disable.');
define('MODULE_NEWSLETTER_INFOS_TITLE', 'Newsletter Info');
define('MODULE_NEWSLETTER_INFOS_DESC', 'Select content to display as newsletter info.');

define('ADMIN_START_TAB_SELECTED_TITLE', 'Tabs startpage');
define('ADMIN_START_TAB_SELECTED_DESC', 'Which tab on the start page should be selected by default?');

define('CACHE_LIFETIME_NOTE', '<b>Attention:</b> If the cache is activated, changes will only become visible in the shop after the cache lifetime expires!');

define('REVIEWS_PURCHASED_ONLY_TITLE', 'Check ratings');
define('REVIEWS_PURCHASED_ONLY_DESC', 'Should reviews only be possible if a customer has also bought the item?');
define('REVIEWS_PURCHASED_INFOS_TITLE', 'Reviews Info');
define('REVIEWS_PURCHASED_INFOS_DESC', 'Select the content to display as rating info on its authenticity.');

define('CAT_VIEW_DROPDOWN_TITLE' , 'Categories Select with Categories / Articles');
define('CAT_VIEW_DROPDOWN_DESC' , 'Use Categories Select as navigation with categories / articles?');

define('DISPLAY_PRODUCTS_ADDED_TITLE' , 'Display products date added');
define('DISPLAY_PRODUCTS_ADDED_DESC' , 'Display when the product was added to the catalog on product page?');
