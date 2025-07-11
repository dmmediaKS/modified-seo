<?php 
  /* --------------------------------------------------------------
   $Id: jquery.backup_restore.js.php 16430 2025-05-02 12:11:12Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   --------------------------------------------------------------
   based on:
   (c) 2011 (c) by  web28 - www.rpa-com.de

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );
?>

<script>
  var debug = false;
  var ajax_url = "backup_restore.php?ajax=1&action=restoredb&<?php echo xtc_session_name() . '=' . xtc_session_id(); ?>";
  var ajax_type = 'POST';
  var dataStr = '';
  
  var maxReloads = 100000000;
  
  ajaxCall(dataStr);

  function ajaxCall(dataStr) {
    if (debug) console.log('dataStr: ' + dataStr);
    if (debug) console.log('url:' + ajax_url);

    jQuery.ajax({
      url: ajax_url,
      type: ajax_type,
      timeout: 300000, //Set a timeout (in milliseconds) for the request. 
      dataType: 'json',
      data : dataStr,
      error: function(xhr, status, error) {
        alert(xhr.responseText);
      },
      success: function(data){
        JStoPHPResponse(data);
      }
    })
  }

  function JStoPHPResponse(data) {
    var response = data ;
    if (debug) console.log('response:' + $.param(response));
    
    var data_ok = '<div><b>' + 'Tabellen wiederhergestellt: ' + (response.table_ready) + '</b></div>';
    if (response.actual_table != '') {
      data_ok += '<div><b>' + '<br />Aktuell in Bearbeitung: ' + response.actual_table + '</b></div>';
    }
    data_ok += '<div><b>' + '<br />Seitenaufrufe: ' + response.aufruf + '</b></div>';
    data_ok += '<div><b>' + '<br />Anzahl Zeilen: ' + response.anzahl_zeilen + '</b></div>';
    data_ok += '<div><b>' + '<br />Scriptlaufzeit: ' +  response.time  + '</b></div>';
    
    $('#data_ok').html(data_ok);
        
    var maxReloadsText = '';
    if (response.aufruf > maxReloads) {
      response.nr = response.num_tables;
      maxReloadsText = '<span>' + "<?php echo (defined('TEXT_MAX_RELOADS') ? TEXT_MAX_RELOADS : 'Maximale Seitenreloads wurden erreicht: ');?>" + maxReloads + '</span>';
    }
    
    if (debug) console.log('fileEOF:' + response.fileEOF);
    if (response.fileEOF != 1) {
       var dataStrNew = $.param(response) //jquery build http query string
       if (debug) console.log('$.param:' + dataStrNew); 
       ajaxCall(dataStrNew);
    } else {
      $('#info_wait').html('&nbsp;');
      
      var infoText = '<?php echo TEXT_INFO_DO_RESTORE_OK;?>';
      if (maxReloadsText != '') infoText = maxReloadsText;
      $('#info_text').html(infoText);
    
      var button_back = '<a href="backup.php?<?php echo xtc_session_name() . "=" . xtc_session_id(); ?>" class="button"><?php echo BUTTON_BACK; ?></a>';
      $('#button_back').html(button_back);
      
    }
  }
  
  function updateProgressBar(total,counter,type) {
    precent = (counter *100/total).toFixed(0); //+ '%';
    $('#'+ type + '_process').css('width',precent + '%');
    $('#'+ type + '_precents').html(precent + '%');
   
    if (debug) console.log('precent:' + precent); 
    if (debug) console.log('type:' + type);
  }
</script>
