<?php
/* --------------------------------------------------------------
   $Id: banner_manager.php 16409 2025-04-09 12:12:48Z GTB $   

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(banner_manager.php,v 1.25 2003/02/16); www.oscommerce.com 
   (c) 2003	 nextcommerce (banner_manager.php,v 1.4 2003/08/14); www.nextcommerce.org
   (c) 2006 xt:Commerce; www.xt-commerce.com

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

define('HEADING_TITLE', 'Banner Manager');

define('TABLE_HEADING_BANNERS', 'Banner');
define('TABLE_HEADING_GROUPS', 'Gruppe');
define('TABLE_HEADING_STATISTICS', 'Anzeigen / Klicks');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_ACTION', 'Aktion');
define('TABLE_HEADING_LANGUAGE', 'Sprache');
define('TABLE_HEADING_SORT', 'Reihenfolge');
define('TABLE_HEADING_IMAGE', 'Bild');

define('TEXT_BANNERS_TITLE', 'Titel des Banners:'); 
define('TEXT_BANNERS_URL', 'Banner-URL:'); 
define('TEXT_BANNERS_URL_NOTE', 'URL-Ziel bei Klick auf den Banner.'); 
define('TEXT_BANNERS_REDIRECT', 'Direkte Verlinkung:'); 
define('TEXT_BANNERS_REDIRECT_NOTE', 'Der Banner wird direkt mit der angegebenen Banner-URL und nicht unter Verwendung von "redirect.php?action=banner&amp;goto=xxx" verlinkt.<br /><strong>ACHTUNG:</strong> "Anzeigen / Klicks" k&ouml;nnen nicht mehr gez&auml;hlt werden, wenn "Direkte Verlinkung" aktiviert wird!'); 
define('TEXT_BANNERS_GROUP', 'Banner-Gruppe:'); 
define('TEXT_BANNERS_NEW_GROUP', 'W&auml;hlen Sie im Dropdown-Feld die gew&uuml;nschte Banner-Gruppe aus (falls vorhanden) oder geben Sie unten eine neue Banner-Gruppe ein.'); 
define('TEXT_BANNERS_NEW_GROUP_NOTE', 'Damit ein Banner im Template angezeigt wird, muss das Template erweitert werden.<br/>Beispiel: Banner Gruppe ist banner, dann kann im Template in der index.html mit {$BANNER} angezeigt werden'); 
define('TEXT_BANNERS_IMAGE', 'Bild (Datei):'); 
define('TEXT_BANNERS_IMAGE_MOBILE', 'Bild Mobil (Datei):'); 
define('TEXT_BANNERS_IMAGE_LOCAL', 'W&auml;hlen Sie das gew&uuml;nschte Bild mit Klick auf "Durchsuchen" oder w&auml;hlen Sie einen existierenden Banner aus.<br /><strong>Erlaubte Dateitypen:</strong> jpg, jpeg, jpe, gif, png, bmp, tiff, tif, bmp, swf, cab'); 
define('TEXT_BANNERS_IMAGE_TARGET', 'Bildziel (Speichern nach):'); 
define('TEXT_BANNERS_HTML_TEXT', 'HTML Text:');
define('TEXT_BANNERS_HTML_TEXT_NOTE', 'Hier kann direkt der HTML-Code eines Affiliate Dienstes zur Banner-Anzeige eingetragen werden.');
define('TEXT_BANNERS_EXPIRES_ON', 'G&uuml;ltigkeit bis:');
define('TEXT_BANNERS_OR_AT', ', oder bei');
define('TEXT_BANNERS_IMPRESSIONS', 'Impressionen/Anzeigen.');
define('TEXT_BANNERS_SCHEDULED_AT', 'G&uuml;ltigkeit ab:');
define('TEXT_BANNERS_BANNER_NOTE', '<b>Banner Bemerkung:</b><ul><li>Sie k&ouml;nnen Bild- oder HTML-Text-Banner verwenden, beides gleichzeitig ist nicht m&ouml;glich.</li><li>Wenn Sie beide Bannerarten gleichzeitig verwenden, wird nur der HTML-Text Banner angezeigt.</li></ul>');
define('TEXT_BANNERS_INSERT_NOTE', '<b>Bemerkung:</b><ul><li>Auf das Bildverzeichnis muss ein Schreibrecht bestehen!</li><li>F&uuml;llen Sie das Feld \'Bildziel (Speichern nach)\' nicht aus, wenn Sie kein Bild auf Ihren Server kopieren m&ouml;chten (z.B. wenn sich das Bild bereits auf dem Server befindet).</li><li>Das \'Bildziel (Speichern nach)\' Feld muss ein bereits existierendes Verzeichnis mit \'/\' am Ende sein (z.B. banners/).</li></ul>'); 
define('TEXT_BANNERS_EXPIRCY_NOTE', '<b>G&uuml;ltigkeit Bemerkung:</b><ul><li>Nur ein Feld ausf&uuml;llen!</li><li>Wenn der Banner unbegrenzt angezeigt werden soll, tragen Sie in diesen Feldern nichts ein.</li></ul>');
define('TEXT_BANNERS_SCHEDULE_NOTE', '<b>G&uuml;ltigkeit ab Bemerkung:</b><ul><li>Bei Verwendung dieser Funktion, wird der Banner erst ab dem angegeben Datum angezeigt.</li><li>Alle Banner mit dieser Funktion werden bis zu ihrer Aktivierung als deaktiviert angezeigt.</li></ul>');
define('TEXT_BANNERS_IMAGE_TITLE', 'Bild Titel');
define('TEXT_BANNERS_IMAGE_TITLE_NOTE', '');
define('TEXT_BANNERS_IMAGE_ALT', 'Bild Beschreibung');
define('TEXT_BANNERS_IMAGE_ALT_NOTE', '');

define('TEXT_BANNERS_DATE_ADDED', 'hinzugef&uuml;gt am:');
define('TEXT_BANNERS_SCHEDULED_AT_DATE', 'G&uuml;ltigkeit ab: <b>%s</b>');
define('TEXT_BANNERS_EXPIRES_AT_DATE', 'G&uuml;ltigkeit bis zum: <b>%s</b>');
define('TEXT_BANNERS_EXPIRES_AT_IMPRESSIONS', 'G&uuml;ltigkeit bis: <b>%s</b> impressionen/anzeigen');
define('TEXT_BANNERS_STATUS_CHANGE', 'Status ge&auml;ndert: %s');

define('TEXT_BANNERS_DATA', 'D<br />A<br />T<br />E<br />N');
define('TEXT_BANNERS_LAST_3_DAYS', 'letzten 3 Tage');
define('TEXT_BANNERS_BANNER_VIEWS', 'Banneranzeigen');
define('TEXT_BANNERS_BANNER_CLICKS', 'Bannerklicks');
define('TEXT_BANNERS_SORT', 'Reihenfolge:');
define('TEXT_BANNERS_SORT_NOTE', 'Die Reihenfolge hat nur Auswirkung auf dynamische Slider und nicht auf statische Banner.');

define('TEXT_INFO_DELETE_INTRO', 'Sind Sie sicher, dass Sie diesen Banner l&ouml;schen m&ouml;chten?');
define('TEXT_INFO_DELETE_IMAGE', 'Bannerbild l&ouml;schen');

define('SUCCESS_BANNER_INSERTED', 'Erfolg: Der Banner wurde eingef&uuml;gt.');
define('SUCCESS_BANNER_UPDATED', 'Erfolg: Der Banner wurde aktualisiert.');
define('SUCCESS_BANNER_REMOVED', 'Erfolg: Der Banner wurde gel&ouml;scht.');
define('SUCCESS_BANNER_STATUS_UPDATED', 'Erfolg: Der Status des Banners wurde aktualisiert.');

define('ERROR_BANNER_TITLE_REQUIRED', 'Fehler: Ein Bannertitel wird ben&ouml;tigt.');
define('ERROR_BANNER_GROUP_REQUIRED', 'Fehler: Eine Bannergruppe wird ben&ouml;tigt.');
define('ERROR_BANNER_IMAGE_HTML_REQUIRED', 'Fehler: Ein Bannerbild oder HTML Text wird ben&ouml;tigt.');
define('ERROR_IMAGE_DIRECTORY_DOES_NOT_EXIST', 'Fehler: Das Zielverzeichnis %s existiert nicht.');
define('ERROR_IMAGE_DIRECTORY_NOT_WRITEABLE', 'Fehler: Das Zielverzeichnis %s ist nicht beschreibbar.');
define('ERROR_IMAGE_DOES_NOT_EXIST', 'Fehler: Bild existiert nicht.');
define('ERROR_IMAGE_IS_NOT_WRITEABLE', 'Fehler: Bild kann nicht gel&ouml;scht werden.');
define('ERROR_UNKNOWN_STATUS_FLAG', 'Fehler: Unbekanntes Status Flag.');

define('ERROR_GRAPHS_DIRECTORY_DOES_NOT_EXIST', 'Fehler: Das Verzeichnis \'graphs\' ist nicht vorhanden! Bitte erstellen Sie ein Verzeichnis \'graphs\' im Verzeichnis \'images\'.');
define('ERROR_GRAPHS_DIRECTORY_NOT_WRITEABLE', 'Fehler: Das Verzeichnis \'graphs\' ist schreibgesch&uuml;tzt!');

// BOF - Tomcraft - 2009-11-06 - Use variable TEXT_BANNERS_DATE_FORMAT
define('TEXT_BANNERS_DATE_FORMAT', 'JJJJ-MM-TT');
// EOF - Tomcraft - 2009-11-06 - Use variable TEXT_BANNERS_DATE_FORMAT

define('TEXT_BANNERS_LANGUAGE', 'Sprache:');
define('TEXT_BANNERS_LANGUAGE_NOTE', 'F&uuml;r welche Sprache soll der Banner angezeigt werden?');
define('TEXT_NO_FILE', '-- keine Datei --');
define('TEXT_BANNERS_LATEST_STATISTICS', '%s Tage Statistik');
define('TEXT_ACTIVATE_BANNERS_HISTORY', 'Banner Statistik aktivieren:');
