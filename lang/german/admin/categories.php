<?php
/* --------------------------------------------------------------
   $Id: categories.php 16327 2025-02-19 13:41:24Z GTB $   

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(categories.php,v 1.22 2002/08/17); www.oscommerce.com 
   (c) 2003	 nextcommerce (categories.php,v 1.10 2003/08/14); www.nextcommerce.org
   (c) 2006 xt:Commerce; www.xt-commerce.com

   Released under the GNU General Public License 
   --------------------------------------------------------------*/
   
// BOF - Tomcraft - 2009-11-02 - Admin language tabs
define('TEXT_EDIT_STATUS', 'Status aktiv');
// BOF - Tomcraft - 2009-11-02 - Admin language tabs
define('HEADING_TITLE', 'Kategorien &amp; Artikel');
define('HEADING_TITLE_SEARCH', 'Suchergebnisse');
define('HEADING_TITLE_GOTO', 'Gehe zu:');
define('HEADING_TITLE_MANUFACTURERS', 'Hersteller:');
define('HEADING_TITLE_SHOW_CATEGORIES', 'Kategorien anzeigen:');

define('TABLE_HEADING_ID', 'ID');
define('TABLE_HEADING_CATEGORIES_PRODUCTS', 'Kategorien / Artikel');
define('TABLE_HEADING_ACTION', 'Aktion');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_STARTPAGE', 'TOP');
define('TABLE_HEADING_STOCK','Lagerbestand');
define('TABLE_HEADING_SORT','Sort.');
define('TABLE_HEADING_EDIT','Edit');
// BOF - Tomcraft - 2010-04-07 - Added definition for products model
define('TABLE_HEADING_PRODUCTS_MODEL','Artikelnummer');
// EOF - Tomcraft - 2010-04-07 - Added definition for products model

// BOF - Hendrik - 2010-08-11 - Thumbnails in admin products list
define('TABLE_HEADING_IMAGE','Bild');
// EOF - Hendrik - 2010-08-11 - Thumbnails in admin products list

define('TEXT_ACTIVE_ELEMENT','Aktives Element');
define('TEXT_MARKED_ELEMENTS','Markierte Elemente');
define('TEXT_INFORMATIONS','Informationen');
define('TEXT_INSERT_ELEMENT','Neues Element');
define('TEXT_SHOW_NO_MANUFACTURERS', 'Ohne Hersteller');
define('TEXT_SHOW_ALL_MANUFACTURERS', 'Alle Hersteller');
define('TEXT_SHOW_ALL_PRODUCTS', 'Alle Artikel');
define('TEXT_SHOW_ALL_ACTIVE', 'aktive');
define('TEXT_SHOW_ALL_INACTIVE', 'inaktive');

define('TEXT_WARN','Bestandswarnung:');
define('TEXT_WARN_MAIN','Hauptartikel');
define('TEXT_NEW_PRODUCT', 'Neuer Artikel in &quot;%s&quot;');
define('TEXT_EDIT_PRODUCT', 'Artikel bearbeiten in &quot;%s&quot;');
define('TEXT_CATEGORIES', 'Kategorien:');
define('TEXT_PRODUCTS', 'Produkte:');
define('TEXT_PRODUCTS_PRICE_INFO', 'Preis:');
define('TEXT_PRODUCTS_TAX_CLASS', 'Steuerklasse:');
define('TEXT_PRODUCTS_TAX_CLASS_FOR', 'Steuerklasse f&uuml;r ');
define('TEXT_PRODUCTS_AVERAGE_RATING', 'Durchschn. Bewertung:');
define('TEXT_PRODUCTS_QUANTITY_INFO', 'Lagerbestand:');
define('TEXT_PRODUCTS_DISCOUNT_ALLOWED_INFO', 'Maximal erlaubter Rabatt:');
define('TEXT_DATE_ADDED', 'Hinzugef&uuml;gt am:');
define('TEXT_DATE_AVAILABLE', 'Erscheinungsdatum:');
define('TEXT_LAST_MODIFIED', 'Letzte &Auml;nderung:');
define('TEXT_PRODUCTS_ORDERED', 'Verkaufte Artikel:');
define('TEXT_IMAGE_NONEXISTENT', 'Bild existiert nicht');
define('TEXT_NO_CHILD_CATEGORIES_OR_PRODUCTS', 'Bitte f&uuml;gen Sie eine neue Kategorie oder einen Artikel in <strong>%s</strong> ein.');
define('TEXT_PRODUCT_MORE_INFORMATION', 'F&uuml;r weitere Informationen, besuchen Sie bitte die <a href="http://%s" target="_blank"><u>Homepage</u></a> des Herstellers.');
define('TEXT_PRODUCT_DATE_ADDED', 'Diesen Artikel haben wir am %s in unseren Katalog aufgenommen.');
define('TEXT_PRODUCT_DATE_AVAILABLE', 'Dieser Artikel ist erh&auml;ltlich ab %s.');

define('TEXT_EDIT_INTRO', 'Bitte f&uuml;hren Sie alle notwendigen &Auml;nderungen durch.');
define('TEXT_EDIT_CATEGORIES_ID', 'Kategorie ID:');
define('TEXT_EDIT_CATEGORIES_NAME', 'Kategorie Name:');
define('TEXT_EDIT_CATEGORIES_HEADING_TITLE', 'Kategorie &Uuml;berschrift:');
define('TEXT_EDIT_CATEGORIES_DESCRIPTION', 'Kategorie Beschreibung:');
define('TEXT_EDIT_CATEGORIES_SHORT_DESCRIPTION', 'Kategorie Kurzbeschreibung:');
define('TEXT_EDIT_CATEGORIES_IMAGE', 'Kategorie Bild:');
define('TEXT_EDIT_CATEGORIES_IMAGE_LIST', 'Kategorie Bild Listing:');
define('TEXT_EDIT_CATEGORIES_IMAGE_MOBILE', 'Kategorie Bild Mobil:');

define('TEXT_EDIT_SORT_ORDER', 'Sortierreihenfolge:');

define('TEXT_INFO_COPY_TO_INTRO', 'Bitte w&auml;hlen Sie eine neue Kategorie aus, in die Sie den Artikel kopieren m&ouml;chten:');
define('TEXT_INFO_CURRENT_CATEGORIES', 'Aktuelle Kategorien:');

define('TEXT_INFO_HEADING_NEW_CATEGORY', 'Neue Kategorie in &quot;%s&quot;');
define('TEXT_INFO_HEADING_EDIT_CATEGORY', 'Kategorie bearbeiten in &quot;%s&quot;');
define('TEXT_INFO_HEADING_DELETE_CATEGORY', 'Kategorie l&ouml;schen');
define('TEXT_INFO_HEADING_MOVE_CATEGORY', 'Kategorie verschieben');
define('TEXT_INFO_HEADING_DELETE_PRODUCT', 'Artikel l&ouml;schen');
define('TEXT_INFO_HEADING_MOVE_PRODUCT', 'Artikel verschieben');
define('TEXT_INFO_HEADING_COPY_TO', 'Kopieren nach');
define('TEXT_INFO_HEADING_MOVE_ELEMENTS', 'Elemente verschieben');
define('TEXT_INFO_HEADING_DELETE_ELEMENTS', 'Elemente l&ouml;schen');

define('TEXT_DELETE_CATEGORY_INTRO', 'Sind Sie sicher, dass Sie diese Kategorie l&ouml;schen m&ouml;chten?');
define('TEXT_DELETE_PRODUCT_INTRO', 'Sind Sie sicher, dass Sie diesen Artikel l&ouml;schen m&ouml;chten?');

define('TEXT_DELETE_WARNING_CHILDS', '<b>WARNUNG:</b> Es existieren noch %s (Unter-)Kategorien, die mit dieser Kategorie verbunden sind!');
define('TEXT_DELETE_WARNING_PRODUCTS', '<b>WARNUNG:</b> Es existieren noch %s Artikel, die mit dieser Kategorie verbunden sind!');

define('TEXT_MOVE_WARNING_CHILDS', '<b>Info:</b> Es existieren noch %s (Unter-)Kategorien, die mit dieser Kategorie verbunden sind!');
define('TEXT_MOVE_WARNING_PRODUCTS', '<b>Info:</b> Es existieren noch %s Artikel, die mit dieser Kategorie verbunden sind!');

define('TEXT_MOVE_PRODUCTS_INTRO', 'Bitte w&auml;hlen Sie die &uuml;bergeordnete Kategorie, in die Sie <b>%s</b> verschieben m&ouml;chten');
define('TEXT_MOVE_CATEGORIES_INTRO', 'Bitte w&auml;hlen Sie die &uuml;bergeordnete Kategorie, in die Sie <b>%s</b> verschieben m&ouml;chten');
define('TEXT_MOVE', 'Verschiebe <b>%s</b> nach:');
define('TEXT_MOVE_ALL', 'Verschiebe alle nach:');

define('TEXT_NEW_CATEGORY_INTRO', 'Bitte geben Sie die neue Kategorie mit allen relevanten Daten ein.');
define('TEXT_CATEGORIES_NAME', 'Kategorie Name:');
define('TEXT_CATEGORIES_IMAGE', 'Kategorie Bild:');

define('TEXT_META_TITLE', 'Meta Title:');
define('TEXT_META_DESCRIPTION', 'Meta Description:');
define('TEXT_META_KEYWORDS', 'Meta Keywords:');

define('TEXT_SORT_ORDER', 'Sortierreihenfolge:');

define('TEXT_PRODUCTS_STATUS', 'Artikelstatus:');
define('TEXT_PRODUCTS_STARTPAGE', 'Auf Startseite zeigen:');
define('TEXT_PRODUCTS_STARTPAGE_YES', 'Ja');
define('TEXT_PRODUCTS_STARTPAGE_NO', 'Nein');
define('TEXT_PRODUCTS_STARTPAGE_SORT', 'Sortierreihenfolge (Startseite):');
define('TEXT_PRODUCTS_DATE_AVAILABLE', 'Erscheinungsdatum:');
// BOF - Hetfield - 2010-01-28 - Changing product available in correctly names for status
define('TEXT_PRODUCT_AVAILABLE', 'Aktiviert');
define('TEXT_PRODUCT_NOT_AVAILABLE', 'Deaktiviert');
// EOF - Hetfield - 2010-01-28 - Changing product available in correctly names for status
define('TEXT_PRODUCTS_MANUFACTURER', 'Artikelhersteller:');
define('TEXT_PRODUCTS_MANUFACTURER_MODEL', 'Hersteller Art.-Nr. (HAN/MPN):');
define('TEXT_PRODUCTS_NAME', 'Artikelname:');
define('TEXT_PRODUCTS_HEADING_TITLE', 'Artikel &Uuml;berschrift:');
define('TEXT_PRODUCTS_DESCRIPTION', 'Artikelbeschreibung:');
define('TEXT_PRODUCTS_QUANTITY', 'Lagerbestand:');
define('TEXT_PRODUCTS_MODEL', 'Artikel-Nr.:');
define('TEXT_PRODUCTS_IMAGE', 'Artikelbild:');
define('TEXT_PRODUCTS_IMAGE_TITLE', 'Titel:');
define('TEXT_PRODUCTS_IMAGE_ALT', 'Beschreibung:');
define('TEXT_PRODUCTS_URL', 'Herstellerlink:');
define('TEXT_PRODUCTS_URL_WITHOUT_HTTP', '<small>(ohne f&uuml;hrendes http://)</small>');
define('TEXT_PRODUCTS_PRICE', 'Artikelpreis:');
define('TEXT_PRODUCTS_WEIGHT', 'Artikelgewicht:');
define('TEXT_PRODUCTS_EAN','GTIN/EAN');
define('TEXT_PRODUCT_LINKED_TO','Verlinkt in:');
define('TEXT_DELETE', 'L&ouml;schen');
define('EMPTY_CATEGORY', 'Leere Kategorie');

define('TEXT_HOW_TO_COPY', 'Kopiermethode f&uuml;r Artikel:');
define('TEXT_COPY_AS_LINK', 'Verlinken');
define('TEXT_COPY_AS_DUPLICATE', 'Duplizieren');

define('ERROR_CANNOT_LINK_TO_SAME_CATEGORY', 'Fehler: Artikel k&ouml;nnen nicht in der gleichen Kategorie verlinkt werden.');
define('ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE', 'Fehler: Das Verzeichnis \'images\' im Katalogverzeichnis ist schreibgesch&uuml;tzt: ' . DIR_FS_CATALOG_IMAGES);
define('ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST', 'Fehler: Das Verzeichnis \'images\' im Katalogverzeichnis ist nicht vorhanden: ' . DIR_FS_CATALOG_IMAGES);

define('TEXT_PRODUCTS_DISCOUNT_ALLOWED','Maximal erlaubter Rabatt:');
define('HEADING_PRICES_OPTIONS','<b>Preis-Optionen</b>');
define('HEADING_PRODUCT_IMAGES','<b>Artikel-Bilder</b>');
define('TEXT_PRODUCTS_WEIGHT_INFO','<small>(kg)</small>');
define('TEXT_PRODUCTS_SHORT_DESCRIPTION','Kurzbeschreibung:');
define('TEXT_PRODUCTS_KEYWORDS', 'Zusatz-Begriffe f&uuml;r Suche:');
define('TXT_STK','Stk: ');
define('TXT_PRICE','a :');
define('TXT_NETTO','Nettopreis: ');
define('TXT_STAFFELPREIS','Staffelpreise');

define('HEADING_PRODUCTS_MEDIA','<b>Artikelmedium</b>');
define('TABLE_HEADING_PRICE','Preis');

define('TEXT_FSK18','FSK 18:');
define('TEXT_CHOOSE_INFO_TEMPLATE_CATEGORIE','Vorlage f&uuml;r Kategorie&uuml;bersicht');
define('TEXT_CHOOSE_INFO_TEMPLATE_LISTING','Vorlage f&uuml;r Artikel&uuml;bersicht');
// BOF - Tomcraft - 2009-11-02 - Admin language tabs
//define('TEXT_PRODUCTS_SORT','Reihung:');
define('TEXT_PRODUCTS_SORT','Sortierreihenfolge:');
// EOF - Tomcraft - 2009-11-02 - Admin language tabs
define('TEXT_EDIT_PRODUCT_SORT_ORDER','Artikel-Sortierung');
define('TXT_PRICES','Preis');
define('TXT_NAME','Artikelname');
define('TXT_ORDERED','Bestellte Artikel');
// BOF - Tomcraft - 2009-11-02 - Admin language tabs
define('TXT_SORT','Sortierreihenfolge');
// EOF - Tomcraft - 2009-11-02 - Admin language tabs
define('TXT_WEIGHT','Gewicht');
define('TXT_QTY','Auf Lager');
// BOF - Tomcraft - 2009-09-12 - add option to sort by date and products model
define('TXT_DATE','Einstelldatum');
define('TXT_MODEL','Artikelnummer');
// EOF - Tomcraft - 2009-09-12 - add option to sort by date and products model

define('TEXT_MULTICOPY','Mehrfach');
define('TEXT_MULTICOPY_DESC','Elemente in folgende Kategorien kopieren:<br />(Falls ausgew&auml;hlt werden Einstellungen von "Einfach" ignoriert.)');
define('TEXT_SINGLECOPY','Einfach');
define('TEXT_SINGLECOPY_DESC','Elemente in folgende Kategorie kopieren:<br />(Daf&uuml;r darf unter "Mehrfach" keine Kategorie aktiviert sein.)');
define('TEXT_SINGLECOPY_CATEGORY','Kategorie:');

define('TEXT_PRODUCTS_VPE','VPE');
define('TEXT_PRODUCTS_VPE_VISIBLE','Anzeige VPE:');
define('TEXT_PRODUCTS_VPE_VALUE',' Wert:');

define('CROSS_SELLING_1','Cross Selling');
define('CROSS_SELLING_2','f&uuml;r Artikel');
define('CROSS_SELLING_SEARCH','Produktsuche:');
define('BUTTON_EDIT_CROSS_SELLING','Cross Selling');
define('HEADING_DEL','L&ouml;schen');
define('HEADING_ADD','Hinzuf&uuml;gen?');
define('HEADING_GROUP','Gruppe');
define('HEADING_SORTING','Reihung');
define('HEADING_MODEL','Artikelnummer');
define('HEADING_NAME','Artikel');
define('HEADING_CATEGORY','Kategorie');
define('HEADING_IMAGE','Bild');

// BOF - Tomcraft - 2009-11-06 - Use variable TEXT_PRODUCTS_DATE_FORMAT
define('TEXT_PRODUCTS_DATE_FORMAT', 'JJJJ-MM-TT');
// EOF - Tomcraft - 2009-11-06 - Use variable TEXT_PRODUCTS_DATE_FORMAT

// BOF - web28 - 2010-08-03 - add metatags max charakters info
define('TEXT_CHARACTERS','Zeichen');
// EOF - web28 - 2010-08-03 - add metatags max charakters info

define('TEXT_ATTRIBUTE_COPY', 'Artikelattribute mitkopieren');
define('TEXT_ATTRIBUTE_COPY_INFO', 'Artikelattribute mitkopieren<br/ >Nur bei Einzelkopie (1 Artikel) empfohlen');

define('TEXT_PRODUCTS_ORDER_DESCRIPTION','Bestellbeschreibung - Anzeige am Ende der Bestellung, in Bestellemail, Bestellausdruck');

define('TEXT_HOW_TO_LINK', '<b>Seitenaufruf nach dem Kopieren/Verlinken</b>');
define('TEXT_HOW_TO_LINK_INFO', 'Artikeleingabemaske<br/ >(Bei mehreren Artikel zum Letzten in der Liste)');

define('TEXT_SET_GROUP_PERMISSIONS', 'Kundengruppen Berechtigungen f&uuml;r alle Unterordner und Artikel &uuml;bernehmen?');

define('HEADING_TITLE_ONLY_INACTIVE_PRODUCTS', 'Nur inaktive Artikel anzeigen');

// BOF - Timo Paul (mail[at]timopaul[dot]biz) - 2014-01-17 - duplicate products content and links
define('TEXT_CONTENT_COPY', 'Artikel-Content mitkopieren');
define('TEXT_CONTENT_COPY_INFO', 'Artikel-Content mitkopieren<br/ >Nur bei Einzelkopie (1 Artikel) empfohlen');
define('TEXT_LINKS_COPY', 'Artikel-Verlinkungen mitkopieren');
define('TEXT_LINKS_COPY_INFO', 'Artikel-Verlinkungen mitkopieren<br/ >Nur bei Einzelkopie (1 Artikel) empfohlen');
// EOF - Timo Paul (mail[at]timopaul[dot]biz) - 2014-01-17 - duplicate products content and links

define('TEXT_GRADUATED_PRICES_INFO', 'Die Anzahl der Eingabefelder f&uuml;r die Staffelpreise kann unter "<b>Konfiguration - Adminbereich Optionen - Anzahl Staffelpreise</b>" angepasst werden.');
define('TEXT_CATEGORY_SETTINGS', 'Kategorie Einstellungen:');

define('ERROR_QTY_SAVE_CHANGED', 'W&auml;hrend der Bearbeitung des Artikels wurde der Lagerbestand ver&auml;ndert und wurde nicht gespeichert.');

define('TEXT_NO_MOVE_POSSIBLE', 'Artikel kann nicht verschoben werden.');

define('TEXT_IN', 'in:');

define('TEXT_PRODUCTS_ATTRIBUTES_RECALCULATE', 'Attribute beim &Auml;ndern des Steuersatzes neu berechnen');

define('HEADING_TITLE_CAT_BREADCRUMB', ' in &quot;%s&quot;');

define('TEXT_PRODUCTS_TAGS', 'Artikeleigenschaften');

define('TEXT_GRADUATED_PRICES_GROUP_INFO', 'Die Kundengruppe hat derzeit keine Berechtigungen Staffelpreise zu sehen. Dies kann in den Kundengruppen-Einstellungen jederzeit ge&auml;ndert werden.');

define('TEXT_NO_FILE', 'Keine Vorlagen-Datei vorhanden!');

define('ERROR_COPY_METHOD_NOT_SPECIFIED', 'Kopiermethode nicht angegeben.');
define('ERROR_COPY_METHOD_NOT_ALLOWED', 'Kopiermethode "Verlinken" nicht erlaubt bei Kategorien.');

define('TEXT_TAGS_COPY', 'Artikeleigenschaften mitkopieren');
define('TEXT_TAGS_COPY_INFO', 'Artikeleigenschaften mitkopieren<br/ >Nur bei Einzelkopie (1 Artikel) empfohlen');

define('TEXT_PRODUCTS_LAST_MODIFIED', 'Zuletzt aktualisiert:');
define('TEXT_STOCK_UPDATE_SUCCESS', 'Lagerbestand aktualisiert');
define('TEXT_STOCK_UPDATE_ERROR', 'Lagerbestand nicht aktualisiert');
?>