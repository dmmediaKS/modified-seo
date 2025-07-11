<?php
/* --------------------------------------------------------------
   $Id: german.php 16320 2025-02-11 17:00:32Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(german.php,v 1.99 2003/05/28); www.oscommerce.com
   (c) 2003 nextcommerce (german.php,v 1.24 2003/08/24); www.nextcommerce.org
   (c) 2006 XT-Commerce (german.php)

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contributions:
   Customers Status v3.x (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

// look in your $PATH_LOCALE/locale directory for available locales..
// on RedHat6.0 I used 'de_DE'
// on FreeBSD 4.0 I use 'de_DE.ISO_8859-1'
// this may not work under win32 environments..

@setlocale(LC_TIME, 'de_DE.UTF-8' ,'de_DE@euro', 'de_DE', 'de-DE', 'de', 'ge', 'de_DE.ISO_8859-1', 'German','de_DE.ISO_8859-15');

define('DATE_LOCALE', 'de_DE');
define('DATE_FORMAT_SHORT', 'd.m.Y');
define('DATE_FORMAT_LONG', 'l, d. F Y');
define('DATE_FORMAT', DATE_FORMAT_SHORT);
define('PHP_DATE_TIME_FORMAT',  DATE_FORMAT_SHORT . ' H:i:s');
define('DATE_TIME_FORMAT', DATE_FORMAT_SHORT . ' H:i:s');

// Return date in raw format
// $date should be in format mm/dd/yyyy
// raw date is in format YYYYMMDD, or DDMMYYYY
function xtc_date_raw($date, $reverse = false) {
  if ($reverse) {
    return substr($date, 0, 2) . substr($date, 3, 2) . substr($date, 6, 4);
  } else {
    return substr($date, 6, 4) . substr($date, 3, 2) . substr($date, 0, 2);
  }
}

require_once(DIR_FS_INC.'auto_include.inc.php');
foreach(auto_include(DIR_FS_LANGUAGES.'german/extra/admin/','php') as $file) require ($file);

// Global entries for the <html> tag
define('HTML_PARAMS','dir="ltr" lang="de"');

// page title
define('TITLE', defined('PROJECT_VERSION') ? PROJECT_VERSION : 'undefined');

// header text in includes/header.php
define('HEADER_TITLE_TOP', 'Administration');
define('HEADER_TITLE_SUPPORT_SITE', 'Supportseite');
define('HEADER_TITLE_ONLINE_CATALOG', 'Online Katalog');
define('HEADER_TITLE_ADMINISTRATION', 'Administration');

// text for gender
define('GENDER_NONE', 'keine');
define('GENDER_MALE', 'Herr');
define('GENDER_FEMALE', 'Frau');
define('GENDER_DIVERSE', 'Divers');

// text for date of birth example
define('DOB_FORMAT_STRING', 'tt.mm.jjjj');

// configuration box text in includes/boxes/configuration.php

define('BOX_HEADING_CONFIGURATION','Konfiguration');
define('BOX_HEADING_MODULES','Module');
define('BOX_HEADING_PARTNER_MODULES','Partner Module');
define('BOX_HEADING_ZONE','Land/Steuer');
define('BOX_HEADING_CUSTOMERS','Kunden');
define('BOX_HEADING_PRODUCTS','Katalog');
define('BOX_HEADING_STATISTICS','Statistiken');
define('BOX_HEADING_TOOLS','Hilfsprogramme');
define('BOX_HEADING_LOCALIZATION', 'Sprachen/W&auml;hrungen');
define('BOX_HEADING_TEMPLATES','Templates');
define('BOX_HEADING_LOCATION_AND_TAXES', 'Land/Steuer');
define('BOX_HEADING_CATALOG', 'Katalog');
define('BOX_MODULE_NEWSLETTER','Newsletter');

define('BOX_CONTENT','Content Manager');
define('TEXT_ALLOWED', 'Erlaubnis');
define('TEXT_ACCESS', 'Zugriffsbereich');
define('BOX_CONFIGURATION', 'Grundeinstellungen');
define('BOX_CONFIGURATION_1', 'Mein Shop');
define('BOX_CONFIGURATION_2', 'Minimum Werte');
define('BOX_CONFIGURATION_3', 'Maximum Werte');
define('BOX_CONFIGURATION_4', 'Bild Optionen');
define('BOX_CONFIGURATION_5', 'Kunden Details');
define('BOX_CONFIGURATION_6', 'Modul Optionen');
define('BOX_CONFIGURATION_7', 'Versand Optionen');
define('BOX_CONFIGURATION_8', 'Artikel Listen Optionen');
define('BOX_CONFIGURATION_9', 'Lagerverwaltungs Optionen');
define('BOX_CONFIGURATION_10', 'Logging Optionen');
define('BOX_CONFIGURATION_11', 'Cache Optionen');
define('BOX_CONFIGURATION_12', 'E-Mail Optionen');
define('BOX_CONFIGURATION_13', 'Download Optionen');
define('BOX_CONFIGURATION_14', 'Komprimierung');
define('BOX_CONFIGURATION_15', 'Sessions');
define('BOX_CONFIGURATION_16', 'Meta-Tags/Suchmaschinen');
define('BOX_CONFIGURATION_17', 'Zusatzmodule');
define('BOX_CONFIGURATION_18', 'USt-IdNr.');
define('BOX_CONFIGURATION_19', 'Partner');
define('BOX_CONFIGURATION_22', 'Such-Optionen');
define('BOX_CONFIGURATION_24', 'Google, Matomo &amp; Facebook');
define('BOX_CONFIGURATION_25', 'Captcha');
define('BOX_CONFIGURATION_31', 'Skrill');
define('BOX_CONFIGURATION_40', 'Popup Fenster Optionen');
define('BOX_CONFIGURATION_1000', 'Adminbereich Optionen');

define('BOX_MODULES', 'Zahlungs-/Versand-/Verrechnungs-Module');
define('BOX_PAYMENT', 'Zahlungsoptionen');
define('BOX_SHIPPING', 'Versandart');
define('BOX_ORDER_TOTAL', 'Zusammenfassung');
define('BOX_CATEGORIES', 'Kategorien/Artikel');
define('BOX_PRODUCTS_ATTRIBUTES', 'Artikelmerkmale');
define('BOX_MANUFACTURERS', 'Hersteller');
define('BOX_REVIEWS', 'Produktrezensionen');
define('BOX_CAMPAIGNS', 'Kampagnen');
define('BOX_XSELL_PRODUCTS', 'Cross Marketing');
define('BOX_SPECIALS', 'Sonderangebote');
define('BOX_PRODUCTS_EXPECTED', 'Erwartete Artikel');
define('BOX_CUSTOMERS', 'Kunden');
define('BOX_ACCOUNTING', 'Adminrechte Verwaltung');
define('BOX_CUSTOMERS_STATUS','Kundengruppen');
define('BOX_ORDERS', 'Bestellungen');
define('BOX_COUNTRIES', 'Land');
define('BOX_ZONES', 'Bundesl&auml;nder');
define('BOX_GEO_ZONES', 'Steuerzonen');
define('BOX_TAX_CLASSES', 'Steuerklassen');
define('BOX_TAX_RATES', 'Steuers&auml;tze');
define('BOX_HEADING_REPORTS', 'Berichte');
define('BOX_PRODUCTS_VIEWED', 'Besuchte Artikel');
define('BOX_STOCK_WARNING','Lager Bericht');
define('BOX_PRODUCTS_PURCHASED', 'Verkaufte Artikel');
define('BOX_STATS_CUSTOMERS', 'Kunden-Bestellstatistik');
define('BOX_BACKUP', 'Datenbank Manager');
define('BOX_BANNER_MANAGER', 'Banner Manager');
define('BOX_CACHE', 'Cache Steuerung');
define('BOX_DEFINE_LANGUAGE', 'Sprachen definieren');
define('BOX_FILE_MANAGER', 'Datei-Manager');
define('BOX_MAIL', 'E-Mail versenden');
define('BOX_NEWSLETTERS', 'Newsletter Manager');
define('BOX_SERVER_INFO', 'Server Info');
define('BOX_WHOS_ONLINE', 'Wer ist Online');
define('BOX_TPL_BOXES','Box Reihenfolge');
define('BOX_CURRENCIES', 'W&auml;hrungen');
define('BOX_LANGUAGES', 'Sprachen');
define('BOX_ORDERS_STATUS', 'Bestellstatus');
define('BOX_ATTRIBUTES_MANAGER','Attribut Verwaltung');
define('BOX_SHIPPING_STATUS','Lieferstatus');
define('BOX_SALES_REPORT','Umsatzstatistik');
define('BOX_MODULE_EXPORT','Export Module');
define('BOX_MODULE_SYSTEM','System Module');
define('BOX_HEADING_GV_ADMIN', 'Gutscheine/Coupons');
define('BOX_GV_ADMIN_QUEUE', 'Gutschein Warteschlange');
define('BOX_GV_ADMIN_MAIL', 'Gutschein E-Mail');
define('BOX_GV_ADMIN_SENT', 'Gutscheine versandt');
define('BOX_COUPON_ADMIN','Coupon Admin');
define('BOX_TOOLS_BLACKLIST','Kreditkarten-Blacklist');
define('BOX_IMPORT','Import/Export');
define('BOX_PRODUCTS_VPE','Verpackungseinheit');
define('BOX_CAMPAIGNS_REPORT','Kampagnen Report');
define('BOX_ORDERS_XSELL_GROUP','Cross-Marketing Gruppen');
define('BOX_REMOVEOLDPICS','Alte Bilder l&ouml;schen'); // Remove old pictures - franky_n - 20110105
define('BOX_JANOLAW','janolaw AGB Hosting'); // Tomcraft - 2011-06-17 - Added janolaw AGB hosting service
define('BOX_HAENDLERBUND','H&auml;ndlerbund AGB Service'); // Tomcraft - 2012-12-08 - Added haendlerbund AGB interface
define('BOX_SHOP','Shop');
define('BOX_LOGOUT','Abmelden');
define('BOX_CREDITS','Credits');
define('BOX_UPDATE','Versionscheck');
define('BOX_GV_CUSTOMERS','Kunden-Guthaben');
define('BOX_IT_RECHT_KANZLEI', 'IT Recht Kanzlei');
define('BOX_PROTECTEDSHOPS', 'Protected Shops - AGB Service');
define('BOX_CLEVERREACH', 'CleverReach');
define('BOX_SUPERMAILER', 'SuperMailer');
define('BOX_OFFLINE', 'Shop offline');
define('BOX_LOGS', 'Logfiles');
define('BOX_SHIPCLOUD', 'shipcloud');
define('BOX_SHIPCLOUD_PICKUP', 'shipcloud - Abholung');
define('BOX_PRODUCTS_TAGS', 'Artikeleigenschaften');
define('BOX_TRUSTEDSHOPS', 'Trusted Shops');
define('BOX_NEWSLETTER_RECIPIENTS', 'Newsletter Empf&auml;nger');
define('BOX_DSGVO_EXPORT', 'DSGVO Export');
define('BOX_SUPPORT', 'Support');
define('BOX_CACHING', 'Caching');
define('BOX_COOKIE_CONSENT', 'Cookie Consent');
define('BOX_SEMKNOX', 'Site Search 360 Produktsuche');
define('BOX_PAGES_CONTENT', 'Content Seiten');
define('BOX_PRODUCTS_CONTENT', 'Artikel Anh&auml;nge');
define('BOX_CONTENT_CONTENT', 'Content Anh&auml;nge');
define('BOX_EMAIL_CONTENT', 'E-Mail Anh&auml;nge');
define('BOX_DHL', 'DHL Versand &amp; Label-Erstellung');
define('BOX_SCHEDULED_TASKS', 'Geplante Aufgaben');
define('BOX_AVALEX', 'avalex Rechtstexte');
define('BOX_GOOGLE_ANALYTICS', 'Google Analytics');
define('BOX_MATOMO_ANALYTICS', 'Matomo Analytics');
define('BOX_FACEBOOK_PIXEL', 'Facebook Pixel');
define('BOX_MODULE_CATEGORIES', 'Kategorien');
define('BOX_MODULE_CHECKOUT', 'Checkout');
define('BOX_MODULE_MAIN', 'Main');
define('BOX_MODULE_ORDER', 'Bestellungen');
define('BOX_MODULE_PRODUCT', 'Artikel');
define('BOX_MODULE_SHOPPING_CART', 'Warenkorb');
define('BOX_MODULE_XTCPRICE', 'Preise');

define('TXT_GROUPS','<b>Gruppen</b>:');
define('TXT_SYSTEM','System');
define('TXT_CUSTOMERS','Kunden/Bestellungen');
define('TXT_PRODUCTS','Artikel/Kategorien');
define('TXT_STATISTICS','Statistiktools');
define('TXT_TOOLS','Zusatzprogramme');
define('TEXT_ACCOUNTING','Zugriffseinstellungen f&uuml;r:');

// javascript messages
define('JS_ERROR', 'W&auml;hrend der Eingabe sind Fehler aufgetreten!\nBitte korrigieren Sie folgendes:\n\n');

define('JS_OPTIONS_VALUE_PRICE', '* Sie m&uuml;ssen diesem Wert einen Preis zuordnen\n');
define('JS_OPTIONS_VALUE_PRICE_PREFIX', '* Sie m&uuml;ssen ein Vorzeichen f&uuml;r den Preis angeben (+/-)\n');

define('JS_PRODUCTS_NAME', '* Der neue Artikel muss einen Namen haben\n');
define('JS_PRODUCTS_DESCRIPTION', '* Der neue Artikel muss eine Beschreibung haben\n');
define('JS_PRODUCTS_PRICE', '* Der neue Artikel muss einen Preis haben\n');
define('JS_PRODUCTS_WEIGHT', '* Der neue Artikel muss eine Gewichtsangabe haben\n');
define('JS_PRODUCTS_QUANTITY', '* Sie m&uuml;ssen dem neuen Artikel eine verf&uuml;gbare Anzahl zuordnen\n');
define('JS_PRODUCTS_MODEL', '* Sie m&uuml;ssen dem neuen Artikel eine Artikel-Nr. zuordnen\n');
define('JS_PRODUCTS_IMAGE', '* Sie m&uuml;ssen dem Artikel ein Bild zuordnen\n');

define('JS_SPECIALS_PRODUCTS_PRICE', '* Es muss ein neuer Preis f&uuml;r diesen Artikel festgelegt werden\n');

define('JS_GENDER', '* Die \'Anrede\' muss ausgew&auml;hlt werden.\n');
define('JS_FIRST_NAME', '* Der \'Vorname\' muss mindestens aus ' . (defined('ENTRY_FIRST_NAME_MIN_LENGTH') ? ENTRY_FIRST_NAME_MIN_LENGTH : 0) . ' Zeichen bestehen.\n');
define('JS_LAST_NAME', '* Der \'Nachname\' muss mindestens aus ' . (defined('ENTRY_LAST_NAME_MIN_LENGTH') ? ENTRY_LAST_NAME_MIN_LENGTH : 0) . ' Zeichen bestehen.\n');
define('JS_DOB', '* Das \'Geburtsdatum\' muss folgendes Format haben: xx.xx.xxxx (Tag/Monat/Jahr).\n');
define('JS_EMAIL_ADDRESS', '* Die \'E-Mail-Adresse\' muss mindestens aus ' . (defined('ENTRY_EMAIL_ADDRESS_MIN_LENGTH') ? ENTRY_EMAIL_ADDRESS_MIN_LENGTH : 0) . ' Zeichen bestehen.\n');
define('JS_ADDRESS', '* Die \'Stra&szlig;e\' muss mindestens aus ' . (defined('ENTRY_STREET_ADDRESS_MIN_LENGTH') ? ENTRY_STREET_ADDRESS_MIN_LENGTH : 0) . ' Zeichen bestehen.\n');
define('JS_POST_CODE', '* Die \'Postleitzahl\' muss mindestens aus ' . (defined('ENTRY_POSTCODE_MIN_LENGTH') ? ENTRY_POSTCODE_MIN_LENGTH : 0) . ' Zeichen bestehen.\n');
define('JS_CITY', '* Die \'Stadt\' muss mindestens aus ' . (defined('ENTRY_CITY_MIN_LENGTH') ? ENTRY_CITY_MIN_LENGTH : 0) . ' Zeichen bestehen.\n');
define('JS_STATE', '* Das \'Bundesland\' muss ausgew&auml;hlt werden.\n');
define('JS_STATE_SELECT', '-- W&auml;hlen Sie oberhalb --');
define('JS_ZONE', '* Das \'Bundesland\' muss aus der Liste f&uuml;r dieses Land ausgew&auml;hlt werden.');
define('JS_COUNTRY', '* Das \'Land\' muss ausgew&auml;hlt werden.\n');
define('JS_TELEPHONE', '* Die \'Telefonnummer\' muss aus mindestens ' . (defined('ENTRY_TELEPHONE_MIN_LENGTH') ? ENTRY_TELEPHONE_MIN_LENGTH : 0) . ' Zeichen bestehen.\n');
define('JS_PASSWORD', '* Das \'Passwort\' sowie die \'Passwortbest&auml;tigung\' m&uuml;ssen &uuml;bereinstimmen und aus mindestens ' . (defined('ENTRY_PASSWORD_MIN_LENGTH') ? ENTRY_PASSWORD_MIN_LENGTH : 0) . ' Zeichen bestehen.\n');

define('JS_ORDER_DOES_NOT_EXIST', 'Auftragsnummer %s existiert nicht!');

define('CATEGORY_PERSONAL', 'Pers&ouml;nliche Daten');
define('CATEGORY_ADDRESS', 'Adresse');
define('CATEGORY_CONTACT', 'Kontakt');
define('CATEGORY_COMPANY', 'Firma');
define('CATEGORY_OPTIONS', 'Weitere Optionen');

define('ENTRY_GENDER', 'Anrede:');
define('ENTRY_GENDER_ERROR', '');
define('ENTRY_FIRST_NAME', 'Vorname:');
define('ENTRY_FIRST_NAME_ERROR', '&nbsp;<span class="errorText">mindestens ' . (defined('ENTRY_FIRST_NAME_MIN_LENGTH') ? ENTRY_FIRST_NAME_MIN_LENGTH : 0) . ' Buchstaben</span>');
define('ENTRY_LAST_NAME', 'Nachname:');
define('ENTRY_LAST_NAME_ERROR', '&nbsp;<span class="errorText">mindestens ' . (defined('ENTRY_LAST_NAME_MIN_LENGTH') ? ENTRY_LAST_NAME_MIN_LENGTH : 0) . ' Buchstaben</span>');
define('ENTRY_DATE_OF_BIRTH', 'Geburtsdatum:');
define('ENTRY_DATE_OF_BIRTH_ERROR', '&nbsp;<span class="errorText">(z.B. 21.05.1970)</span>');
define('ENTRY_EMAIL_ADDRESS', 'E-Mail-Adresse:');
define('ENTRY_EMAIL_ADDRESS_ERROR', '&nbsp;<span class="errorText">mindestens ' . (defined('ENTRY_EMAIL_ADDRESS_MIN_LENGTH') ? ENTRY_EMAIL_ADDRESS_MIN_LENGTH : 0) . ' Buchstaben</span>');
define('ENTRY_EMAIL_ADDRESS_CHECK_ERROR', '&nbsp;<span class="errorText">ung&uuml;ltige E-Mail-Adresse! (Umlaute in E-Mail-Adressen werden derzeit nicht unterst&uuml;tzt.)</span>');
define('ENTRY_EMAIL_ADDRESS_ERROR_EXISTS', '&nbsp;<span class="errorText">Diese E-Mail-Adresse existiert schon!</span>');
define('ENTRY_COMPANY', 'Firmenname:');
define('ENTRY_STREET_ADDRESS', 'Stra&szlig;e:');
define('ENTRY_STREET_ADDRESS_ERROR', '&nbsp;<span class="errorText">mindestens ' . (defined('ENTRY_STREET_ADDRESS_MIN_LENGTH') ? ENTRY_STREET_ADDRESS_MIN_LENGTH : 0) . ' Buchstaben</span>');
define('ENTRY_SUBURB', 'Weitere Anschrift:');
define('ENTRY_POST_CODE', 'Postleitzahl:');
define('ENTRY_POST_CODE_ERROR', '&nbsp;<span class="errorText">mindestens ' . (defined('ENTRY_POSTCODE_MIN_LENGTH') ? ENTRY_POSTCODE_MIN_LENGTH : 0) . ' Zahlen</span>');
define('ENTRY_CITY', 'Stadt:');
define('ENTRY_CITY_ERROR', '&nbsp;<span class="errorText">mindestens ' . (defined('ENTRY_CITY_MIN_LENGTH') ? ENTRY_CITY_MIN_LENGTH : 0) . ' Buchstaben</span>');
define('ENTRY_STATE', 'Bundesland:');
define('ENTRY_STATE_ERROR', '&nbsp;<span class="errorText">notwendige Eingabe</font></small>');
define('ENTRY_COUNTRY', 'Land:');
define('ENTRY_COUNTRY_ERROR', 'Bitte w&auml;hlen Sie Ihr Land aus der Liste aus.');
define('ENTRY_TELEPHONE_NUMBER', 'Telefonnummer:');
define('ENTRY_TELEPHONE_NUMBER_ERROR', '&nbsp;<span class="errorText">mindestens ' . (defined('ENTRY_TELEPHONE_MIN_LENGTH') ? ENTRY_TELEPHONE_MIN_LENGTH : 0) . ' Zahlen</span>');
define('ENTRY_FAX_NUMBER', 'Telefaxnummer:');
define('ENTRY_NEWSLETTER', 'Newsletter:');
define('ENTRY_CUSTOMERS_STATUS', 'Kundengruppe:');
define('ENTRY_NEWSLETTER_YES', 'abonniert');
define('ENTRY_NEWSLETTER_NO', 'nicht abonniert');
define('ENTRY_MAIL_ERROR','&nbsp;<span class="errorText">Bitte treffen Sie eine Auswahl</span>');
define('ENTRY_PASSWORD','Passwort (autom. erstellt)');
define('ENTRY_PASSWORD_ERROR','&nbsp;<span class="errorText">Ihr Passwort muss aus mindestens ' . (defined('ENTRY_PASSWORD_MIN_LENGTH') ? ENTRY_PASSWORD_MIN_LENGTH : 0) . ' Zeichen bestehen.</span>');
define('ENTRY_MAIL_COMMENTS','Zus&auml;tzlicher E-Mail-Text:');

define('ENTRY_MAIL','E-Mail mit Passwort an Kunden versenden?');
define('YES','ja');
define('NO','nein');
define('SAVE_ENTRY','&Auml;nderungen Speichern?');
define('TEXT_CHOOSE_INFO_TEMPLATE','Vorlage f&uuml;r Artikeldetails');
define('TEXT_CHOOSE_OPTIONS_TEMPLATE','Vorlage f&uuml;r Artikeloptionen');
define('TEXT_SELECT','-- Bitte w&auml;hlen --');
define('PULL_DOWN_DEFAULT', 'Bitte w&auml;hlen');

// BOF - Tomcraft - 2009-06-10 - added some missing alternative text on admin icons
// Icons
define('ICON_ARROW_RIGHT','markiert');
define('ICON_BIG_WARNING','Achtung!');
define('ICON_CROSS', 'Falsch');
define('ICON_CURRENT_FOLDER', 'Aktueller Ordner');
define('ICON_DELETE', 'L&ouml;schen');
define('ICON_EDIT','Bearbeiten');
define('ICON_ERROR', 'Fehler');
define('ICON_FILE', 'Datei');
define('ICON_FILE_DOWNLOAD', 'Herunterladen');
define('ICON_FOLDER', 'Ordner');
define('ICON_LOCKED', 'Gesperrt');
define('ICON_POPUP','Banner Vorschau');
define('ICON_PREVIOUS_LEVEL', 'Vorherige Ebene');
define('ICON_PREVIEW', 'Vorschau');
define('ICON_STATISTICS', 'Statistik');
define('ICON_SUCCESS', 'Erfolg');
define('ICON_TICK', 'Wahr');
define('ICON_UNLOCKED', 'Entsperrt');
define('ICON_WARNING', 'Warnung');
// EOF - Tomcraft - 2009-06-10 - added some missing alternative text on admin icons

// constants for use in tep_prev_next_display function
define('TEXT_RESULT_PAGE', 'Seite %s von %d');
define('TEXT_DISPLAY_NUMBER_OF_BANNERS', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Bannern)');
define('TEXT_DISPLAY_NUMBER_OF_COUNTRIES', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> L&auml;ndern)');
define('TEXT_DISPLAY_NUMBER_OF_CUSTOMERS', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Kunden)');
define('TEXT_DISPLAY_NUMBER_OF_CURRENCIES', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> W&auml;hrungen)');
define('TEXT_DISPLAY_NUMBER_OF_LANGUAGES', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Sprachen)');
define('TEXT_DISPLAY_NUMBER_OF_MANUFACTURERS', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Herstellern)');
define('TEXT_DISPLAY_NUMBER_OF_NEWSLETTERS', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Newslettern)');
define('TEXT_DISPLAY_NUMBER_OF_NEWSLETTERS_RECIPIENTS', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Newsletterempf&auml;ngern)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Bestellungen)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS_STATUS', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Bestellstatus)');
define('TEXT_DISPLAY_NUMBER_OF_XSELL_GROUP', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Cross-Marketing Gruppen)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS_VPE', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Verpackungseinheiten)');
define('TEXT_DISPLAY_NUMBER_OF_SHIPPING_STATUS', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Lieferstatus)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Artikeln)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS_EXPECTED', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> erwarteten Artikeln)');
define('TEXT_DISPLAY_NUMBER_OF_REVIEWS', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Rezensionen)');
define('TEXT_DISPLAY_NUMBER_OF_SPECIALS', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Sonderangeboten)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_CLASSES', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Steuerklassen)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_ZONES', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Steuerzonen)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_RATES', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Steuers&auml;tzen)');
define('TEXT_DISPLAY_NUMBER_OF_ZONES', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Bundesl&auml;ndern)');
define('TEXT_DISPLAY_NUMBER_OF_WHOS_ONLINE', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Kunden die online sind)');

define('PREVNEXT_BUTTON_PREV', '&laquo;');
define('PREVNEXT_BUTTON_NEXT', '&raquo;');

define('TEXT_DEFAULT', 'Standard');
define('TEXT_SET_DEFAULT', 'als Standard definieren');
define('TEXT_FIELD_REQUIRED', '&nbsp;<span class="fieldRequired">* Erforderlich</span>');

define('ERROR_NO_DEFAULT_CURRENCY_DEFINED', 'Fehler: Es wurde keine Standardw&auml;hrung definiert. Bitte definieren Sie unter Land/Steuer -> W&auml;hrungen eine Standardw&auml;hrung.');
define('ERROR_NO_DEFAULT_LANGUAGE_DEFINED', 'Fehler: Es wurde keine Standardsprache definiert. Bitte definieren Sie unter Land/Steuer -> Sprachen eine Standardsprache.');

define('TEXT_CACHE_CATEGORIES', 'Kategorien Box');
define('TEXT_CACHE_MANUFACTURERS', 'Hersteller Box');
define('TEXT_CACHE_ALSO_PURCHASED', 'Ebenfalls gekauft Modul');

define('TEXT_NONE', '-- keine --');
define('TEXT_AUTO_PROPORTIONAL', '-- auto proportional --');
define('TEXT_AUTO_MAX', '-- auto max --');
define('TEXT_TOP', 'Top');

define('ERROR_DESTINATION_DOES_NOT_EXIST', 'Fehler: Speicherort existiert nicht.');
define('ERROR_DESTINATION_NOT_WRITEABLE', 'Fehler: Speicherort ist nicht beschreibbar.');
define('ERROR_FILE_NOT_SAVED', 'Fehler: Datei wurde nicht gespeichert.');
define('ERROR_FILETYPE_NOT_ALLOWED', 'Fehler: Dateityp ist nicht erlaubt.');
define('SUCCESS_FILE_SAVED_SUCCESSFULLY', 'Erfolg: Hochgeladene Datei wurde erfolgreich gespeichert.');
define('WARNING_NO_FILE_UPLOADED', 'Warnung: Es wurde keine Datei hochgeladen.');
define('ERROR_FILE_NOT_REMOVEABLE', 'Fehler: Datei konnte nicht entfernt werden.');

define('DELETE_ENTRY','Eintrag l&ouml;schen?');
define('TEXT_PAYMENT_ERROR','<b>WARNUNG:</b> Bitte aktivieren Sie ein <a href="%s">Zahlungsmodul</a>!');
define('TEXT_SHIPPING_ERROR','<b>WARNUNG:</b> Bitte aktivieren Sie ein <a href="%s">Versandmodul</a>!');
define('TEXT_PAYPAL_CONFIG','<b>WARNUNG:</b> Bitte konfigurieren Sie die PayPal-Zahlungseinstellungen f&uuml;r den "Live Modus" unter: <a href="%s"><strong>Partner -> PayPal</strong></a>'); //DokuMan - 2012-05-31 - show warning if PayPal payment module activated, but not configured for live mode yet
define('TEXT_NETTO','Netto: ');
define('TEXT_DUPLICATE_CONFIG_ERROR','<b>WARNUNG:</b> Doppelter Konfigurationsschl&uuml;ssel: ');

define('ENTRY_CID','Kundennummer:');
define('IP','Bestell IP:');
define('CUSTOMERS_MEMO','Memos:');
define('DISPLAY_MEMOS','Anzeigen/Schreiben');
define('TITLE_MEMO','Kunden MEMO');
define('ENTRY_LANGUAGE','Sprache:');
define('CATEGORIE_NOT_FOUND','Kategorie nicht vorhanden');

// BOF - Tomcraft - 2009-06-10 - added some missing alternative text on admin icons
// Image Icons
define('IMAGE_RELEASE', 'Gutschein einl&ouml;sen');
define('IMAGE_ICON_STATUS_GREEN_STOCK','auf Lager');
define('IMAGE_ICON_STATUS_GREEN','aktiv');
define('IMAGE_ICON_STATUS_GREEN_LIGHT','aktivieren');
define('IMAGE_ICON_STATUS_RED','inaktiv');
define('IMAGE_ICON_STATUS_RED_LIGHT','deaktivieren');
define('IMAGE_ICON_INFO','ausw&auml;hlen');
// EOF - Tomcraft - 2009-06-10 - added some missing alternative text on admin icons

define('_JANUARY', 'Januar');
define('_FEBRUARY', 'Februar');
define('_MARCH', 'M&auml;rz');
define('_APRIL', 'April');
define('_MAY', 'Mai');
define('_JUNE', 'Juni');
define('_JULY', 'Juli');
define('_AUGUST', 'August');
define('_SEPTEMBER', 'September');
define('_OCTOBER', 'Oktober');
define('_NOVEMBER', 'November');
define('_DECEMBER', 'Dezember');

// Beschreibung f&uuml;r Abmeldelink im Newsletter
define('TEXT_NEWSLETTER_REMOVE', 'Um sich von unserem Newsletter abzumelden klicken Sie hier:');

define('TEXT_DISPLAY_NUMBER_OF_GIFT_VOUCHERS', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Gutscheinen)');
define('TEXT_DISPLAY_NUMBER_OF_COUPONS', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Coupons)');
define('TEXT_VALID_PRODUCTS_LIST', 'Artikelliste');
define('TEXT_VALID_PRODUCTS_ID', 'Artikel-ID');
define('TEXT_VALID_PRODUCTS_NAME', 'Artikelname');
define('TEXT_VALID_PRODUCTS_MODEL', 'Artikel-Nr.');

define('TEXT_VALID_CATEGORIES_LIST', 'Kategorieliste');
define('TEXT_VALID_CATEGORIES_ID', 'Kategorie-ID');
define('TEXT_VALID_CATEGORIES_NAME', 'Kategoriename');

define('TXT_ALL','Alle');

// UST ID
define('HEADING_TITLE_VAT','USt-IdNr.');
define('ENTRY_VAT_ID','USt-IdNr.:');
define('ENTRY_CUSTOMERS_VAT_ID', 'USt-IdNr.:');
define('TEXT_VAT_FALSE','<span class="messageStackError">Gepr&uuml;ft/USt-IdNr. ist ung&uuml;ltig!</span>');
define('TEXT_VAT_TRUE','<span class="messageStackSuccess">Gepr&uuml;ft/USt-IdNr. ist g&uuml;ltig</span>');
define('TEXT_VAT_UNKNOWN_COUNTRY','<span class="messageStackError">Nicht gepr&uuml;ft/Land unbekannt!</span>');
define('TEXT_VAT_INVALID_INPUT','<span class="messageStackError">Nicht gepr&uuml;ft/Der &uuml;bergebene L&auml;ndercode ist ung&uuml;ltig oder die USt-IdNr. ist leer!</span>');
define('TEXT_VAT_SERVICE_UNAVAILABLE','<span class="messageStackError">Nicht gepr&uuml;ft/Der SOAP Service ist nicht erreichbar, versuchen Sie es sp&auml;ter noch einmal!</span>');
define('TEXT_VAT_MS_UNAVAILABLE','<span class="messageStackError">Nicht gepr&uuml;ft/Der Service des Mitgliedsstaats ist nicht erreichbar, versuchen Sie es sp&auml;ter noch einmal oder mit einem anderen Mitgliedsstaat!</span>');
define('TEXT_VAT_TIMEOUT','<span class="messageStackError">Nicht gepr&uuml;ft/Der Service des Mitgliedsstaats konnte nicht erreicht werden (timeout), versuchen Sie es sp&auml;ter noch einmal oder mit einem anderen Mitgliedsstaat!</span>');
define('TEXT_VAT_SERVER_BUSY','<span class="messageStackError">Nicht gepr&uuml;ft/Der Service kann Ihre Anfrage nicht bearbeiten. Versuchen Sie es sp&auml;ter noch einmal!</span>');
define('TEXT_VAT_NO_PHP5_SOAP_SUPPORT','<span class="messageStackError">Nicht gepr&uuml;ft/Unterst&uumltzung f&uumlr PHP5 SOAP ist nicht vorhanden!</span>');
define('TEXT_VAT_CONNECTION_NOT_POSSIBLE','<span class="messageStackError">FEHLER: Verbindung zu Webservice nicht m&ouml;glich (SOAP-FEHLER)!</span>');

define('ERROR_GIF_MERGE','Fehlender GDlib Gif Support, kein Wasserzeichen (Merge) m&ouml;glich');
define('ERROR_GIF_UPLOAD','Fehlender GDlib Gif Support, kein Upload von GIF Bildern m&ouml;glich');

// BOF - Tomcraft - 2009-06-17 Google Sitemap
define('BOX_GOOGLE_SITEMAP', 'Google Sitemap');
// EOF - Tomcraft - 2009-06-17 Google Sitemap

// BOF - Tomcraft - 2009-10-03 - Paypal Express Modul
define('BOX_PAYPAL','PayPal');
// EOF - Tomcraft - 2009-10-03 - Paypal Express Modul

// BOF - Dokuman - 2009-10-02 - added moneybookers payment module version 2.4
define('_PAYMENT_MONEYBOOKERS_EMAILID_TITLE','Skrill E-Mail-Adresse');
define('_PAYMENT_MONEYBOOKERS_EMAILID_DESC','E-Mail-Adresse mit welcher Sie bei Skrill.com registriert sind.<br />Wenn Sie noch &uuml;ber kein Konto verf&uuml;gen, <b>melden Sie sich</b> jetzt bei <a href="https://account.skrill.com/signup/page1" target="_blank"><b>Skrill</b></a> <b>gratis</b> an.');
define('_PAYMENT_MONEYBOOKERS_MERCHANTID_TITLE','Skrill H&auml;ndler ID');
define('_PAYMENT_MONEYBOOKERS_MERCHANTID_DESC','Ihre Skrill.com H&auml;ndler ID');
define('_PAYMENT_MONEYBOOKERS_PWD_TITLE','Skrill Geheimwort');
define('_PAYMENT_MONEYBOOKERS_PWD_DESC','Mit der Eingabe des Geheimwortes wird die Verbindung beim Bezahlvorgang verschl&uuml;sselt. So wird h&ouml;chste Sicherheit gew&auml;hrleistet. Geben Sie Ihr Skrill Geheimwort ein (dies ist nicht Ihr Passwort!). Das Geheimwort darf nur aus Kleinbuchstaben und Zahlen bestehen. Sie k&ouml;nnen Ihr Geheimwort <b><span class="col-red">nach der Freischaltung</b></span> in Ihrem Skrill-Benutzerkonto definieren (H&auml;ndlereinstellungen).<br /><br /><span class="col-red">So schalten Sie Ihren Skrill.com Account f&uuml;r die Zahlungsabwicklung frei!</span><br /><br />Senden Sie eine E-Mail mit:<br/>- Ihrer Shopdomain<br/>- Ihrer Skrill E-Mail-Adresse<br /><br />An: <a href="mailto:ecommerce@skrill.com?subject=modified eCommerce Shopsoftware: Aktivierung fuer Skrill Quick Checkout">ecommerce@skrill.com</a>');
define('_PAYMENT_MONEYBOOKERS_TMP_STATUS_ID_TITLE','Bestellstatus - Zahlungsvorgang');
define('_PAYMENT_MONEYBOOKERS_TMP_STATUS_ID_DESC',' Sobald der Kunde im Shop auf "Bestellung absenden" dr&uuml;ckt, wird eine "tempor&auml;re Bestellung" angelegt. Dies hat den Vorteil, dass bei Kunden die den Zahlungsvorgang bei Moneybookes abbrechen eine Bestellung aufgezeichnet wurde.');
define('_PAYMENT_MONEYBOOKERS_PROCESSED_STATUS_ID_TITLE','Bestellstatus - Zahlung OK');
define('_PAYMENT_MONEYBOOKERS_PROCESSED_STATUS_ID_DESC','Erscheint, wenn die Zahlung von Skrill best&auml;tigt wurde.');
define('_PAYMENT_MONEYBOOKERS_PENDING_STATUS_ID_TITLE','Bestellstatus - Zahlung in Warteschleife');
define('_PAYMENT_MONEYBOOKERS_PENDING_STATUS_ID_DESC','Wenn der Kunde kein Guthaben auf seinem Konto hat wird die Zahlung solange schwebend gehalten bis das Konto Skrill ausgeglichen ist.');
define('_PAYMENT_MONEYBOOKERS_CANCELED_STATUS_ID_TITLE','Bestellstatus - Zahlung Storniert');
define('_PAYMENT_MONEYBOOKERS_CANCELED_STATUS_ID_DESC','Wird erscheinen, wenn z.B. eine Kreditkarte abgelehnt wurde');
define('MB_ERROR_NO_MERCHANT','Es Existiert kein Skrill.com Account mit dieser E-Mail-Adresse!');
define('MB_MERCHANT_OK','Skrill.com Account korrekt, H&auml;ndler ID %s von Skrill.com empfangen und gespeichert.');
define('MB_INFO','<img src="../images/icons/moneybookers/MBbanner.jpg" /><br /><br />Sie k&ouml;nnen jetzt Kreditkarten, Lastschrift, Sofort., Giropay sowie alle weiteren wichtigen lokalen Bezahloptionen direkt akzeptieren, mit einer simplen Aktivierung im Shop. Mit Skrill als All-in-One-L&ouml;sung brauchen Sie dabei keine Einzelvertr&auml;ge pro Zahlart abzuschlie&szlig;en. Sie brauchen lediglich einen <a href="https://account.skrill.com/signup/page1" target="_blank"><b>kostenlosen Skrill Account</b></a>, um alle wichtigen Bezahloptionen in Ihrem Shop zu akzeptieren. Zus&auml;tzliche Bezahlarten sind ohne Mehrkosten, das Modul beinhaltet <b>keine monatlichen Fixkosten oder Installationskosten</b>.<br /><br /><b>Ihre Vorteile:</b><br />-Die Akzeptanz der wichtigsten Bezahloptionen steigert Ihren Umsatz<br />-Ein Anbieter reduziert Ihre Aufw&auml;nde und Ihre Kosten<br />-Ihr Kunde bezahlt direkt und ohne Registrierungsprozedur<br />-Ein-Klick-Aktivierung und Integration<br />-Sehr attraktive <a href="https://www.skrill.com/de/fees/" target="_blank"><b>Konditionen</b></a> <br />-sofortige Zahlungsbest&auml;tigung und Pr&uuml;fung der Kundendaten<br />-Bezahlabwicklung auch im Ausland und ohne Mehrkosten<br />-6 Millionen Kunden weltweit vertrauen Skrill');
// EOF - Dokuman - 2009-10-02 - added moneybookers payment module version 2.4

// BOF - Tomcraft - 2009-11-02 - set global customers-group-permissions
define('BOX_CUSTOMERS_GROUP','KG-Berechtigungen');
// EOF - Tomcraft - 2009-11-02 - set global customers-group-permissions

// BOF - Tomcraft - 2009-11-02 - New admin top menu
define('TEXT_ADMIN_START', 'Start');
define('BOX_HEADING_CONFIGURATION2','Erw. Konfiguration');
// EOF - Tomcraft - 2009-11-02 - New admin top menu

//BOF - web28 - 2010-04-10 - ADMIN SEARCH BAR
define('ASB_QUICK_SEARCH_CUSTOMER','Suche Kunde...');
define('ASB_QUICK_SEARCH_ORDER','Suche Bestellung...');
define('ASB_QUICK_SEARCH_ORDER_ID','Suche Bestellnummer...');
define('ASB_QUICK_SEARCH_ARTICLE','Suche Artikel/Kategorie...');
define('ASB_QUICK_SEARCH_EMAIL', 'Suche E-Mail-Adresse...');
define('ASB_QUICK_SEARCH_ARTICLE_ID','Suche Artikel/Kategorie ID...');
define('ASB_QUICK_SEARCH_ORDER_OR_INVOICE','Suche Best./Rg.Nr....');
//EOF - web28 - 2010-04-10 - ADMIN SEARCH BAR

//BOF - web28 - 2010.05.30 - accounting - set all checkboxes , countries - set all flags
define('BUTTON_SET','Alle aktivieren');
define('BUTTON_UNSET','Alle deaktivieren');
//EOF - web28 - 2010.05.30 - accounting - set all checkboxes 

//BOF - DokuMan - 2010-08-12 - added possibility to reset admin statistics
define('TEXT_ROWS','Zeile');
define('TABLE_HEADING_RESET','Statistik zur&uuml;cksetzen');
//EOF - DokuMan - 2010-08-12 - added possibility to reset admin statistics

//BOF - web28 - 2010-11-13 - added BUTTON_CLOSE_WINDOW
define('BUTTON_CLOSE_WINDOW' , 'Fenster schlie&szlig;en');
//EOF - web28 - 2010-11-13 - added BUTTON_CLOSE_WINDOW

//BOF - hendrik - 2011-05-14 - independent invoice number and date
define('ENTRY_INVOICE_NUMBER',  'Rechnungsnummer:'); 
define('ENTRY_INVOICE_DATE',    'Rechnungsdatum:'); 
//EOF - hendrik - 2011-05-14 - independent invoice number and date  

//BOF - web28 - 2010-07-06 - added missing error text
define('ENTRY_VAT_ERROR', '&nbsp;<span class="errorText">Ung&uuml;ltige USt-IdNr.</span>');
//EOF - web28 - 2010-07-06 - added missing error text

define('CONFIG_INT_VALUE_ERROR', '"%s" FEHLER: Bitte nur Zahlen eingeben! Eingabe %s wurde ignoriert!');
define('CONFIG_MAX_VALUE_WARNING', '"%s" WARNUNG: Eingabe %s wurde ignoriert! [Maximum: %s]');
define('CONFIG_MIN_VALUE_WARNING', '"%s" WARNUNG: Eingabe %s wurde ignoriert! [Minimum: %s]');

define('WHOS_ONLINE_TIME_LAST_CLICK_INFO', 'Anzeigezeitraum in Sek.: %s. Nach dieser Zeit werden die Eintr&auml;ge gel&ouml;scht.');

define('TEXT_GLOBAL_PRODUCTS_MODEL', 'Art.-Nr.');

define('TEXT_INFO_MODULE_RESTORE', 'Wollen Sie die gesicherten Einstellungen wiederherstellen?<br /><br /><b>ACHTUNG</b>: Es werden alle aktuellen Einstellungen &uuml;berschrieben!');
define('TEXT_INFO_MODULE_REMOVE', 'Wollen Sie das Modul deinstallieren?<br /><br /><b>ACHTUNG</b>: Alle Moduleinstellungen werden ebenfalls gel&ouml;scht!');
define('TEXT_INFO_MODULE_BACKUP', 'Wollen Sie die Moduleinstellungen sichern?');
define('MODULE_BACKUP_CONFIRM', 'Die Moduleinstellungen wurden erfolgreich gesichert!');
define('MODULE_RESTORE_CONFIRM', 'Die Moduleinstellungen wurden erfolgreich wiederhergestellt!');
define('MODULE_UPDATE_CONFIRM', 'Die Moduleinstellungen wurden erfolgreich aktualisiert!');

/* magnalister v1.0.0 */
define('BOX_HEADING_MAGNALISTER', 'magnalister');
define('BOX_MAGNALISTER', 'magnalister Admin');
/* END magnalister */

define('CHARS_LEFT', 'Zeichen &uuml;brig');
define('CHARS_MAX', 'von max.');

define('DISPLAY_PER_PAGE', 'Anzeige pro Seite: ');

define('SPECIALS_DATE_START_TT', 'Angebote starten um 00:00:00 Uhr');
define('SPECIALS_DATE_END_TT', 'Angebote enden um Mitternacht (23:59:59 Uhr)');

define('BOX_PARCEL_CARRIERS', 'Paketdienstleister');
define('TEXT_DISPLAY_NUMBER_OF_CARRIERS', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Paketdienstleister)');

define('RSS_FEED_TITLE', 'Aktuelle Information von modified eCommerce Shopsoftware Blog');
define('RSS_FEED_DESCRIPTION', 'Aktuelle Information von modified eCommerce Shopsoftware Support Forum');
define('RSS_FEED_LINK', 'http://www.modified-shop.org/blog');
define('RSS_FEED_ALTERNATIVE', 'Leider k&ouml;nnen die aktuellen Neuigkeiten nicht im RSS Feed dargestellt werden. Bitte besuchen Sie unseren Blog unter <a href="'.RSS_FEED_LINK.'">www.modified-shop.org/blog</a> um wichtige Informationen f&uuml;r Shopbetreiber zu diesen Themen zu erfahren: <ul><li>Wichtige Updates und Fixes</li><li>Funktionserweiterungen</li><li>Rechtsprechungen</li><li>Neuigkeiten</li><li>Klatsch und Tratsch</li></ul>');
define('RSS_FEED_NOT_REACHABLE', 'Der Newsfeed konnte seit einem l&auml;ngeren Zeitraum nicht aktualisiert werden.<br>Verpassen Sie keine wichtigen Nachrichten, Angebote, uvm. und besuchen uns unter <a target="_blank" href="https://www.modified-shop.org/blog">www.modified-shop.org/blog</a><br>');
define('TEXT_DISPLAY_NUMBER_OF_NEWSFEED', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> News)');

define('CFG_TXT_YES', 'Ja');
define('CFG_TXT_NO', 'Nein');
define('CFG_TXT_OR', 'oder');
define('CFG_TXT_AND', 'und');
define('CFG_TXT_ASC', 'aufsteigend');
define('CFG_TXT_DESC', 'absteigend');
define('CFG_TXT_PRODUCTS_NAME', 'Artikelname');
define('CFG_TXT_PRODUCTS_MODEL', 'Artikelnummer');
define('CFG_TXT_DATE_EXPECTED', 'verf&uuml;gbar ab');
define('CFG_TXT_ACCOUNT', 'Kundenkonto');
define('CFG_TXT_GUEST', 'Gastkonto');
define('CFG_TXT_BOTH', 'beides');
define('CFG_TXT_NONE', 'deaktiviert');
define('CFG_TXT_ADMIN', 'Admin');
define('CFG_TXT_ALL', 'alle');
define('CFG_TXT_WEIGHT', 'Gewicht');
define('CFG_TXT_PRICE', 'Preis');
define('CFG_TXT_ITEM', 'St&uuml;ck');
define('CFG_TXT_WHOS_ONLINE', 'Online');
define('CFG_TXT_ORDERS', 'Bestellungen');
define('CFG_TXT_CUSTOMERS', 'Kunden');
define('CFG_TXT_SALES_REPORT', 'Statistik');
define('CFG_TXT_BLOG', 'Blog');
define('CFG_TXT_P.PRODUCTS_PRICE', 'Preis');
define('CFG_TXT_PD.PRODUCTS_NAME', 'Artikelname');
define('CFG_TXT_P.PRODUCTS_DATE_ADDED', 'Einstelldatum');
define('CFG_TXT_P.PRODUCTS_MODEL', 'Artikelnummer');
define('CFG_TXT_P.PRODUCTS_ORDERED', 'Bestellte Artikel');
define('CFG_TXT_P.PRODUCTS_SORT', 'Sortierreihenfolge');
define('CFG_TXT_P.PRODUCTS_WEIGHT', 'Gewicht');
define('CFG_TXT_P.PRODUCTS_QUANTITY', 'Auf Lager');
define('CFG_TXT_S.SPECIALS_DATE_ADDED', 'Einstelldatum');

define('CSRF_TOKEN_MANIPULATION', 'CSRFToken Manipulation (Aus Sicherheits-Aspekten ist es nicht mehr erlaubt im Adminbereich in verschiedenen Tabs zu arbeiten.)');
define('CSRF_TOKEN_NOT_DEFINED', 'CSRFToken nicht definiert (Aus Sicherheits-Aspekten ist es nicht mehr erlaubt im Adminbereich in verschiedenen Tabs zu arbeiten.)');

define('TEXT_ACCOUNTING_INFO','Dem Hauptadmin [1] k&ouml;nnen die Zugriffsrechte nicht entzogen werden!');

define('JAVASCRIPT_DISABLED_INFO', 'JavaScript ist in Ihrem Browser deaktiviert. Aktivieren Sie JavaScript, um alle Funktionen dieser Webseite nutzen und alle Inhalte sehen zu k&ouml;nnen.');

define('BOX_MODULE_TYPE', 'Klassenerweiterungen');

define('MULTIPLE_INSTALLATION', '<span style="color:red">[Mehrfachinstallation: %s]</span>');

define('FILEUPLOAD_INPUT_TXT', 'Keine Datei');
define('FILEUPLOAD_BTN_TXT', 'Durchsuchen');

define('CHECK_LABEL_PRICE', 'Preis abfragen');

define('TEXT_PAYPAL_TAB_CONFIG', 'PayPal Konfiguration');
define('TEXT_PAYPAL_TAB_PROFILE', 'PayPal Profil');
define('TEXT_PAYPAL_TAB_WEBHOOK', 'PayPal Webhook');
define('TEXT_PAYPAL_TAB_MODULE', 'PayPal Produkte');
define('TEXT_PAYPAL_TAB_INFO', 'PayPal Informationen');

define('TEXT_DEFAULT_SORT_ORDER_TITLE', 'Sortierreihenfolge');
define('TEXT_DEFAULT_SORT_ORDER_DESC', 'Reihenfolge der Verarbeitung. Kleinste Ziffer wird zuerst ausgef&uuml;hrt.');
define('TEXT_DEFAULT_STATUS_TITLE', 'Modul aktivieren?');
define('TEXT_DEFAULT_STATUS_DESC', 'Modul Status');

define('TEXT_HOUR', 'Stunde');
define('TEXT_HOURS', 'Stunden');

define('DELETE_LOGS_SUCCESSFUL', 'Logfiles erfolgreich gel&ouml;scht.');

define('BOX_BLACKLIST_LOGS', 'Blacklist Logs');

define('CONTINUE_WITHOUT_SAVE', 'Nicht gespeicherte &Auml;nderungen gehen verloren.');

define('TEXT_SORT_ASC','aufsteigend');
define('TEXT_SORT_DESC','absteigend');

define('MSRP','UVP');
define('YOUR_PRICE','Ihr Preis ');
define('UNIT_PRICE','St&uuml;ckpreis ');
define('ONLY',' Jetzt nur ');
define('FROM','ab ');
define('YOU_SAVE','Sie sparen ');
define('INSTEAD','Unser bisheriger Preis ');
define('TXT_PER',' pro ');
define('TEXT_NO_PAYMENT', 'Keine Zahlart');

define('TEXT_ERROR_EMPTY_NOT_ALLOWED', '<span class="error_message" style="display:inline-block;">Pflichtfeld (bitte ausf&uuml;llen)</span>');
