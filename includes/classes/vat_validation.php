<?php
/* --------------------------------------------------------------
   $Id: vat_validation.php 15732 2024-02-20 16:28:00Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   --------------------------------------------------------------
   based on:
   (c) 2012 Gambio GmbH - vat_validation.php 2012-05-10 gm
   (c) 2005 xtc_validate_vatid_status.inc.php 899 2005-04-29
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: vat_validation.php 15732 2024-02-20 16:28:00Z GTB $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// include needed functions
include_once(DIR_FS_INC . 'xtc_get_countries.inc.php');

require_once(DIR_FS_EXTERNAL . 'nusoap/nusoap.php');

define ('VAT_LIVE_CHECK_URL', 'https://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl');

class vat_validation {
  
  var $vat_info;
  var $vat_errors;
  var $live_check;
  
  
  function __construct($vat_id = '', $customers_id = '', $customers_status = '', $country_id = '', $guest = false) {

    $vat_id = str_replace(' ', '', $vat_id);
    $this->vat_info = array ();
    $this->vat_errors = array(
      'MS_MAX_CONCURRENT_REQ' => '93',
      'INVALID_INPUT' => '94',
      'SERVICE_UNAVAILABLE' => '95',
      'MS_UNAVAILABLE' => '96',
      'TIMEOUT' => '97',
      'SERVER_BUSY' => '98',
      );
    $this->live_check = ACCOUNT_COMPANY_VAT_LIVE_CHECK;
    if (xtc_not_null($vat_id)) {
      $this->getInfo($vat_id, $customers_id, $customers_status, $country_id, $guest);
    } else {
      if ($guest === true) {
        $this->vat_info = array ('status' => DEFAULT_CUSTOMERS_STATUS_ID_GUEST);
      } else {
        $this->vat_info = array ('status' => DEFAULT_CUSTOMERS_STATUS_ID);
      }
    }
  }


  function getInfo($vat_id = '', $customers_id = '', $customers_status = '', $country_id = '', $guest = false) {
    
    $customers_status_id = DEFAULT_CUSTOMERS_STATUS_ID;
    $customers_vat_status_id = DEFAULT_CUSTOMERS_VAT_STATUS_ID;
    $customers_vat_status_id_local = DEFAULT_CUSTOMERS_VAT_STATUS_ID_LOCAL;
    
    $error = false;
    if ($vat_id != '') {
      $validate_vatid = $this->validate_vatid($vat_id, $country_id);
      $vat_id_status = $validate_vatid;

      switch ($validate_vatid) {
        case '0' :
          if (ACCOUNT_VAT_BLOCK_ERROR == 'true') {
            $error = true;
          }
          $status = $customers_status_id;
          break;
        case '1' :
          if ($country_id == STORE_COUNTRY) {
            if (ACCOUNT_COMPANY_VAT_GROUP == 'true') {
              $status = $customers_vat_status_id_local;
            } else {
              $status = $customers_status_id;
            }
          } else {
            if (ACCOUNT_COMPANY_VAT_GROUP == 'true') {
              $status = $customers_vat_status_id;
            } else {
              $status = $customers_status_id;
            }
          }
          break;
        case '8' :
          if (ACCOUNT_VAT_BLOCK_ERROR == 'true') {
            $error = true;
          }
          $status = $customers_status_id;
          break;
        case '9' :
          if (ACCOUNT_VAT_BLOCK_ERROR == 'true') {
            $error = true;
          }
          $status = $customers_status_id;
          break;
        case '99' :
        case '98' :
        case '97' :
        case '96' :
        case '95' :
        case '94' :
        case '93' :
          if (ACCOUNT_VAT_BLOCK_ERROR == 'true') {
            $error = true;
          }
          $status = $customers_status_id;
          break;
        default :
          $status = $customers_status_id;
          break;
      }
    }
    
    if ($guest === true) {
      $status = DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
    }

    // check if is admin
    if ($customers_id != '') {
      $customers_status_query = xtc_db_query("SELECT customers_status 
                                                FROM ".TABLE_CUSTOMERS." 
                                               WHERE customers_id = '".(int)$customers_id."'");
      $customers_status_value = xtc_db_fetch_array($customers_status_query);
      if ($customers_status_value['customers_status'] == '0') {
        $status = '0';
      }
    }

    $this->vat_info = array(
      'status' => $status, 
      'vat_id_status' => $vat_id_status, 
      'error' => $error, 
      'validate' => $validate_vatid,
    );
  }


  function validate_vatid($vat_id, $country_id) {

    // remove special chars
    $remove = array (' ', '-', '/', '\\', '.', ':', ',');
    $vat_id = trim(chop($vat_id));
    $vat_id = str_replace($remove, '', $vat_id);
    
    $vatNumber = substr($vat_id, 2);
    $country = strtolower(substr($vat_id, 0, 2));
        
    // 0 = 'invalid'
    // 1 = 'valid'
    // 8 = 'unknown country'
    // 9 = 'unknown algorithm'
    //94 = 'INVALID_INPUT'       => 'The provided CountryCode is invalid or the VAT number is empty',
    //95 = 'SERVICE_UNAVAILABLE' => 'The SOAP service is unavailable, try again later',
    //96 = 'MS_UNAVAILABLE'      => 'The Member State service is unavailable, try again later or with another Member State',
    //97 = 'TIMEOUT'             => 'The Member State service could not be reached in time, try again later or with another Member State',
    //98 = 'SERVER_BUSY'         => 'The service cannot process your request. Try again later.'
    //99 = 'no PHP5 SOAP support'
    $results = array(
      0 => '0',
      1 => '1',
      8 => '8',
      9 => '9',
      93 => '93',
      94 => '94',
      95 => '95',
      96 => '96',
      97 => '97',
      98 => '98',
      99 => '99',
    );
    
    // check country 
    $country_check = xtc_get_countriesList($country_id, true);

    // fix for Greece
    $search_array = array('gr');
    $replace_array = array('el');
    $country = str_replace($search_array, $replace_array, $country);
    $country_check['countries_iso_code_2'] = str_replace($search_array, $replace_array, strtolower($country_check['countries_iso_code_2']));

    if (strtoupper($country_check['countries_iso_code_2']) != strtoupper($country)) {
      return $results[0];
    }
    
    // check store vatid
    if (STORE_OWNER_VAT_ID != '') {
      $vat_id_store_owner = trim(chop(STORE_OWNER_VAT_ID));
      $vat_id_store_owner = str_replace($remove, '', $vat_id_store_owner);
      $vat_id_store_owner = substr($vat_id_store_owner, 2);
      if ($vat_id_store_owner == $vatNumber) {
        return $results[0];
      }
    }
        
    $country_iso_code = strtoupper($country);
    
    if ($this->live_check == 'true') {
      
      //Check VAT for EU countries only
      switch ($country_iso_code) {
        case 'AT':
        case 'BE':
        case 'BG':
        case 'CY':
        case 'CZ':
        case 'DE':
        case 'DK':
        case 'EE':
        case 'EL':
        case 'ES':
        case 'FI':
        case 'FR':
        case 'GB':
        case 'HU':
        case 'HR':
        case 'IE':
        case 'IT':
        case 'LT':
        case 'LU':
        case 'LV':
        case 'MT':
        case 'NL':
        case 'PL':
        case 'PT':
        case 'RO':
        case 'SE':
        case 'SI':
        case 'SK':
          $t_result = $this->checkVatID_EU($vatNumber, $country_iso_code);
          break;
        default:
          $t_result = 8; //unknown country
          break;
      }
    } else {
      switch ($country_iso_code) {
        case 'BE':
          // fix for old vat_id
          if (strlen($vatNumber) == 9) {
            $vatNumber = str_pad($vatNumber, 10, '0', STR_PAD_LEFT);
          }
          break;
      }
      $vat_id = $country_iso_code . $vatNumber;
      $t_result = $this->validate_vatid_offline($country, $vat_id);
    }

    return $results[$t_result];
  }
  
  
  function checkVatID_EU($vatNumber, $country_iso_code) {

    $params = array(
      'countryCode' => $country_iso_code, 
      'vatNumber' => $vatNumber,
    );

    $soap_client = new nusoap_client(VAT_LIVE_CHECK_URL, true);
    $soap_proxy = $soap_client->getProxy();

    // check connection
    if (!$soap_client->getError() && is_object($soap_proxy)) {
      $result = $soap_proxy->checkVat($params);

      if (is_array($result) && isset($result['valid']) && $result['valid'] == 'true') {
        return 1; // VAT-ID is valid
      } elseif (is_array($result) && isset($result['valid']) && $result['valid'] == 'false') {
        return $this->_checkVatID_EU($vatNumber, $country_iso_code);
        return 0; // VAT-ID is NOT valid
      } elseif (is_array($result) && isset($result['faultstring'])) {
        return $this->vat_errors[$result['faultstring']];
      }      
    }

    return $this->_checkVatID_EU($vatNumber, $country_iso_code);
  }


  function _checkVatID_EU($vatNumber, $country_iso_code) {

    $params = array('countryCode' => $country_iso_code, 
                    'vatNumber' => $vatNumber);

    try {
      $options = array(
        'soap_version' => SOAP_1_1,
        'exceptions' => true,
        'trace' => 1,
        'cache_wsdl' => WSDL_CACHE_NONE,
        'user_agent' => 'Mozilla',
      );
      $client = new SoapClient(VAT_LIVE_CHECK_URL, $options);
    } catch (Exception $e) {
      trigger_error('SOAP-Fehler: (Fehlernummer: '. $e->faultcode .', Fehlermeldung: '. $e->faultstring .')', E_USER_ERROR);
    }

    if ($client) {
      try {
        $result = $client->checkVat($params);
        if ($result->valid == true){
          return 1;  // VAT-ID is valid
        } else {
          return 0;   // VAT-ID is NOT valid
        }
      } catch (SoapFault $e) {
        return $this->vat_errors[$e->faultstring];
      }
    }
    
    return false;
  }
  
  
  function validate_vatid_offline($country, $vat_id) {
    switch ($country) {
      default:
        return 8;
      break;
      // oesterreich
      case 'at' :
        if (strlen($vat_id) != 11 && strtoupper($vat_id[2]) != 'U') {
          return 0;
        }

        $number = substr(str_replace($country, '', strtolower($vat_id)), 1);

        if (strlen($number) == 8 && is_numeric($number)) {
          return 1;
        } else {
          return 0;
        }
      break;

      // belgien
      case 'be' :
        if (strlen($vat_id) != 12) {
          return 0;
        }

        $number = str_replace($country, '', strtolower($vat_id));

        if (strlen($number) == 10 && is_numeric($number)) {
          return 1;
        } else {
          return 0;
        }
      break;

      // bulgarien
      case 'bg' :

        $number = str_replace($country, '', strtolower($vat_id));

        if (strlen($vat_id) == 11) {
          if (strlen($number) == 9 && is_numeric($number)) {
            return 1;
          } else {
            return 0;
          }
        } elseif (strlen($vat_id) == 12) {
          if (strlen($number) == 10 && is_numeric($number)) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }


      break;

      // zypern
      case 'cy' :
        if (strlen($vat_id) != 11) {
          return 0;
        }

        $number = str_replace($country, '', strtolower($vat_id));

        if (strlen($number) == 9) {
          return 1;
        } else {
          return 0;
        }
      break;

      // tschechische republik
      case 'cz' :
        $number = str_replace($country, '', strtolower($vat_id));
        if (strlen($vat_id) == 10) {
          if (strlen($number) == 8 && is_numeric($number)) {
            return 1;
          } else {
            return 0;
          }
        } elseif (strlen($vat_id) == 11) {
          if (strlen($number) == 9 && is_numeric($number)) {
            return 1;
          } else {
            return 0;
          }
        } elseif (strlen($vat_id) == 12) {
          if (strlen($number) == 10 && is_numeric($number)) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }

      break;

      // deutschland
      case 'de' :
        $number = str_replace($country, '', strtolower($vat_id));
        if (strlen($vat_id) == 11) {
          if (strlen($number) == 9 && is_numeric($number)) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }

      break;

      // d�nemark
      case 'dk' :
        $number = str_replace($country, '', strtolower($vat_id));
        if (strlen($vat_id) == 10) {
          if (strlen($number) == 8 && is_numeric($number)) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }
      break;

      // estland
      case 'ee' :
        $number = str_replace($country, '', strtolower($vat_id));
        if (strlen($vat_id) == 11) {
          if (strlen($number) == 9 && is_numeric($number)) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }
      break;

      // griechenland
      case 'el' :
        $number = str_replace($country, '', strtolower($vat_id));
        if (strlen($vat_id) == 11) {
          if (strlen($number) == 9 && is_numeric($number)) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }
      break;

      // spanien
      case 'es' :
        $number = str_replace($country, '', strtolower($vat_id));
        if (strlen($vat_id) == 11) {
          if (strlen($number) == 9) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }
      break;

      // finnland
      case 'fi' :
        $number = str_replace($country, '', strtolower($vat_id));
        if (strlen($vat_id) == 10) {
          if (strlen($number) == 8 && is_numeric($number)) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }
      break;

      // frankreich
      case 'fr' :
        $number = substr(str_replace($country, '', strtolower($vat_id)),2);
        if (strlen($vat_id) == 13) {
          if (strlen($number) == 9 && is_numeric($number)) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }
      break;

      // england
      case 'gb' :
        $number = str_replace($country, '', strtolower($vat_id));
        if (strlen($vat_id) == 11) {
          if (strlen($number) == 9) {
            return 1;
          } else {
            return 0;
          }
        } elseif (strlen($vat_id) == 14) {
          if (strlen($number) == 12) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }
      break;

      // ungarn
      case 'hu' :
        $number = str_replace($country, '', strtolower($vat_id));
        if (strlen($vat_id) == 10) {
          if (strlen($number) == 8 && is_numeric($number)) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }
      break;

      // irland
      case 'ie' :
        $number = str_replace($country, '', strtolower($vat_id));
        if (strlen($vat_id) == 10) {
          if (strlen($number) == 8) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }
      break;

      // italien
      case 'it' :
        $number = str_replace($country, '', strtolower($vat_id));
        if (strlen($vat_id) == 13) {
          if (strlen($number) == 11 && is_numeric($number)) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }
      break;

      // litauen
      case 'lt' :
        $number = str_replace($country, '', strtolower($vat_id));
        if (strlen($vat_id) == 11) {
          if (strlen($number) == 9 && is_numeric($number)) {
            return 1;
          } else {
            return 0;
          }
        } elseif (strlen($vat_id) == 14) {
          if (strlen($number) == 12 && is_numeric($number)) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }
      break;

      // luxemburg
      case 'lu' :
        $number = str_replace($country, '', strtolower($vat_id));
        if (strlen($vat_id) == 10) {
          if (strlen($number) == 8 && is_numeric($number)) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }
      break;

      // lettland
      case 'lv' :
        $number = str_replace($country, '', strtolower($vat_id));
        if (strlen($vat_id) == 13) {
          if (strlen($number) == 11 && is_numeric($number)) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }
      break;

      // malta
      case 'mt' :
        $number = str_replace($country, '', strtolower($vat_id));
        if (strlen($vat_id) == 10) {
          if (strlen($number) == 8 && is_numeric($number)) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }

      // niederlande
      case 'nl' :
        $number = str_replace($country, '', strtolower($vat_id));
        if (strlen($vat_id) == 14) {
          if (strlen($number) == 12) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }
      break;

      // polen
      case 'pl' :
        $number = str_replace($country, '', strtolower($vat_id));
        if (strlen($vat_id) == 12) {
          if (strlen($number) == 10 && is_numeric($number)) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }
      break;

      // portugal
      case 'pt' :
        $number = str_replace($country, '', strtolower($vat_id));
        if (strlen($vat_id) == 11) {
          if (strlen($number) == 9 && is_numeric($number)) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }
      break;

      // rum�nien
      case 'ro' :
        $number = str_replace($country, '', strtolower($vat_id));

        if (strlen($vat_id) > 1 && strlen($vat_id) < 11) {
          if (is_numeric($number)) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }
      break;

      // schweden
      case 'se' :
        $number = str_replace($country, '', strtolower($vat_id));
        if (strlen($vat_id) == 14) {
          if (strlen($number) == 12 && is_numeric($number)) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }
      break;

      // slowenien
      case 'si' :
        $number = str_replace($country, '', strtolower($vat_id));
        if (strlen($vat_id) == 10) {
          if (strlen($number) == 8 && is_numeric($number)) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }
      break;

      // slowakei
      case 'sk' :
        $number = str_replace($country, '', strtolower($vat_id));
        if (strlen($vat_id) == 12) {
          if (strlen($number) == 10 && is_numeric($number)) {
            return 1;
          } else {
            return 0;
          }
        } else {
          return 0;
        }
      break;

    }
  }

  /********************************************************************
  * landesabhaengige Hilfsfunktionen zur Berechnung                   *
  ********************************************************************/

  // Canada
  function checkVatID_c($vat_id) {
    if (strlen($vat_id) != 10)
      return 0;

    // LUHN-10 code http://www.ee.unb.ca/tervo/ee4253/luhn.html

    $id = substr($vat_id, 1);
    $checksum = 0;
    for ($i = 9; $i > 0; $i --) {
      $digit = $vat_id[$i];
      if ($i % 2 == 1)
        $digit *= 2;
      if ($digit >= 10) {
        $checksum += $digit -10 + 1;
      } else {
        $checksum += $digit;
      }
    }
    if ($this->modulo($checksum, 10) == 0)
      return 1;

    return 0;
  } // Canada

  // belgien
  function checkVatID_be($vat_id) {
    if (strlen($vat_id) != 11)
      return 0;

    $checkvals = (int) substr($vat_id, 2, -2);
    $checksum = (int) substr($vat_id, -2);

    if (97 - $this->modulo($checkvals, 97) != $checksum)
      return 0;

    return 1;
  } // end belgien

  // daenemark
  function checkVatID_dk($vat_id) {
    if (strlen($vat_id) != 10)
      return 0;

    $weights = array (2, 7, 6, 5, 4, 3, 2, 1);
    $checksum = 0;

    for ($i = 0; $i < 8; $i ++)
      $checksum += (int) $vat_id[$i +2] * $weights[$i];
    if ($this->modulo($checksum, 11) > 0)
      return 0;

    return 1;
  } // end daenemark

  // deutschland
  function checkVatID_de($vat_id) {
    if (strlen($vat_id) != 11)
      return 0;

    $prod = 10;
    $checkval = 0;
    $checksum = (int) substr($vat_id, -1);

    for ($i = 2; $i < 10; $i ++) {
      $checkval = $this->modulo((int) $vat_id[$i] + $prod, 10);
      if ($checkval == 0)
        $checkval = 10;
      $prod = $this->modulo($checkval * 2, 11);
    } // end for($i = 2; $i < 10; $i++)
    $prod = $prod == 1 ? 11 : $prod;
    if (11 - $prod != $checksum)
      return 0;

    return 1;
  } // end deutschland

  // estland
  function checkVatID_ee($vat_id) {

    if (strlen($vat_id) != 11)
      return 0;
    if (!is_numeric(substr($vat_id, 2)))
      return 0;

    if ($this->live_check == 'true') {

      return $this->live($vat_id);

    } else {
      return 9; // es gibt keinen algorithmus
    }
  } // end estland

  // finnland
  function checkVatID_fi($vat_id) {
    if (strlen($vat_id) != 10)
      return 0;

    $weights = array (7, 9, 10, 5, 8, 4, 2);
    $checkval = 0;
    $checksum = (int) substr($vat_id, -1);

    for ($i = 0; $i < 8; $i ++)
      $checkval += (int) $vat_id[$i +2] * $weights[$i];

    if (11 - $this->modulo($checkval, 11) != $checksum)
      return 0;

    return 1;
  } // end finnland

  // frankreich
  function checkVatID_fr($vat_id) {
    if (strlen($vat_id) != 13)
      return 0;
    if (!is_numeric(substr($vat_id), 4))
      return 0;

    if ($this->live_check == 'true') {

      return $this->live($vat_id);

    } else {
      return 9; // es gibt keinen algorithmus
    }

  } // end frankreich

  // griechenland
  function checkVatID_el($vat_id) {
    if (strlen($vat_id) != 11)
      return 0;

    $checksum = substr($vat_id, -1);
    $checkval = 0;

    for ($i = 1; $i <= 8; $i ++)
      $checkval += (int) $vat_id[10 - $i] * pow(2, $i);
    $checkval = $this->modulo($checkval, 11) > 9 ? 0 : $this->modulo($checkval, 11);
    if ($checkval != $checksum)
      return 0;

    return 1;
  } // end griechenland

  // grossbrittanien
  function checkVatID_gb($vat_id) {
    if (strlen($vat_id) != 11 && strlen($vat_id) != 14)
      return 0;
    if (!is_numeric(substr($vat_id, 2)))
      return 0;

    if ($this->live_check == 'true') {

      return $this->live($vat_id);

    } else {
      return 9; // es gibt keinen algorithmus
    }

  } // end grossbrittanien

  /********************************************
  * irland                                    *
  ********************************************/
  // irland switch
  function checkVatID_ie($vat_id) {
    if (strlen($vat_id) != 10)
      return 0;
    if (!checkVatID_ie_new($vat_id) && !checkVatID_ie_old($vat_id))
      return 0;

    return 1;
  } // end irland switch

  // irland alte methode
  function checkVatID_ie_old($vat_id) {
    // in neue form umwandeln
    $transform = array (substr($vat_id, 0, 2), '0', substr($vat_id, 4, 5), $vat_id[2], $vat_id[9]);
    $vat_id = join('', $transform);

    // nach neuer form pruefen
    return checkVatID_ie_new($vat_id);
  } // end irland alte methode

  // irland neue methode
  function checkVatID_ie_new($vat_id) {
    $checksum = strtoupper(substr($vat_id, -1));
    $checkval = 0;
    $checkchar = 'A';
    for ($i = 2; $i <= 8; $i ++)
      $checkval += (int) $vat_id[10 - $i] * $i;
    $checkval = $this->modulo($checkval, 23);
    if ($checkval == 0) {
      $checkchar = 'W';
    } else {
      for ($i = $checkval -1; $i > 0; $i --)
        $checkchar ++;
    }
    if ($checkchar != $checksum)
      return false;

    return true;
  } // end irland neue methode
  /* end irland
  ********************************************/

  // italien
  function checkVatID_it($vat_id) {
    if (strlen($vat_id) != 13)
      return 0;

    $checksum = (int) substr($vat_id, -1);
    $checkval = 0;
    for ($i = 0; $i <= 9; $i ++)
      //echo $vat_id[11-$i];
      $checkval += (int) $vat_id[11 - $i] * ($this->is_even($i) ? 2 : 1);
    if ($checksum != $this->modulo($checkval, 10))
      return 0;

    return 1;
  } // end italien

  // lettland
  function checkVatID_lv($vat_id) {

    if (strlen($vat_id) != 13)
      return 0;
    if (!is_numeric(substr($vat_id, 2)))
      return 0;

    if ($this->live_check == 'true') {

      return $this->live($vat_id);

    } else {
      return 9; // es gibt keinen algorithmus
    }
  } // end lettland

  // litauen
  function checkVatID_lt($vat_id) {

    if ((strlen($vat_id) != 13) || (strlen($vat_id) != 11))
      return 0;
    if (!is_numeric(substr($vat_id, 2)))
      return 0;

    if ($this->live_check == 'true') {

      return $this->live($vat_id);

    } else {
      return 9; // es gibt keinen algorithmus
    }
  } // end litauen

  // luxemburg
  function checkVatID_lu($vat_id) {
    if (strlen($vat_id) != 10)
      return 0;

    $checksum = (int) substr($vat_id, -2);
    $checkval = (int) substr($vat_id, 2, 6);
    if ($this->modulo($checkval, 89) != $checksum)
      return 0;

    return 1;
  } // luxemburg

  // malta
  function checkVatID_mt($vat_id) {

    if (strlen($vat_id) != 10)
      return 0;
    if (!is_numeric(substr($vat_id, 2)))
      return 0;

    if ($this->live_check == 'true') {

      return $this->live($vat_id);

    } else {
      return 9; // es gibt keinen algorithmus
    }
  } // end malta

  // niederlande
  function checkVatID_nl($vat_id) {
    if (strlen($vat_id) != 14)
      return 0;
    if (strtoupper($vat_id[11]) != 'B')
      return 0;
    if ((int) $vat_id[12] == 0 || (int) $vat_id[13] == 0)
      return 0;

    $checksum = (int) $vat_id[10];
    $checkval = 0;

    for ($i = 2; $i <= 9; $i ++)
      $checkval += (int) $vat_id[11 - $i] * $i;
    $checkval = $this->modulo($checkval, 11) > 9 ? 0 : $this->modulo($checkval, 11);

    if ($checkval != $checksum)
      return 0;

    return 1;
  } // end niederlande

  // oesterreich
  function checkVatID_at($vat_id) {
    if (strlen($vat_id) != 11)
      return 0;
    if (strtoupper($vat_id[2]) != 'U')
      return 0;

    $checksum = (int) $vat_id[10];
    $checkval = 0;

    for ($i = 3; $i < 10; $i ++)
      $checkval += $this->cross_summa((int) $vat_id[$i] * ($this->is_even($i) ? 2 : 1));
    $checkval = substr((string) (96 - $checkval), -1);

    if ($checksum != $checkval)
      return 0;

    return 1;
  } // end oesterreich

  // polen
  function checkVatID_pl($vat_id) {
    if (strlen($vat_id) != 12)
      return 0;

    $weights = array (6, 5, 7, 2, 3, 4, 5, 6, 7);
    $checksum = (int) $vat_id[11];
    $checkval = 0;
    for ($i = 0; $i < count($weights); $i ++)
      $checkval += (int) $vat_id[$i +2] * $weights[$i];
    $checkval = $this->modulo($checkval, 11);

    if ($checkval != $checksum)
      return 0;

    return 1;
  } // end polen

  // portugal
  function checkVatID_pt($vat_id) {
    if (strlen($vat_id) != 11)
      return 0;

    $checksum = (int) $vat_id[10];
    $checkval = 0;

    for ($i = 2; $i < 10; $i ++) {
      $checkval += (int) $vat_id[11 - $i] * $i;
    }
    $checkval = (11 - $this->modulo($checkval, 11)) > 9 ? 0 : (11 - $this->modulo($checkval, 11));
    if ($checksum != $checkval)
      return 0;

    return 1;
  } // end portugal

  // schweden
  function checkVatID_se($vat_id) {
    if (strlen($vat_id) != 14)
      return 0;
    if ((int) substr($vat_id, -2) < 1 || (int) substr($vat_id, -2) > 94)
      return 0;
    $checksum = (int) $vat_id[11];
    $checkval = 0;

    for ($i = 0; $i < 10; $i ++)
      $checkval += $this->cross_summa((int) $vat_id[10 - $i] * ($this->is_even($i) ? 2 : 1));
    if ($checksum != ($this->modulo($checkval, 10) == 0 ? 0 : 10 - $this->modulo($checkval, 10)))
      return 0;

    $checkval = 0;
    for ($i = 0; $i < 13; $i ++)
      $checkval += (int) $vat_id[13 - $i] * ($this->is_even($i) ? 2 : 1);
    if ($this->modulo($checkval, 10) > 0)
      return 0;

    return 1;
  } // end schweden

  // slowakische republik
  function checkVatID_sk($vat_id) {
    if (strlen($vat_id) != 12)
      return 0;
    if (!is_numeric(substr($vat_id, 2)))
      return 0;

    if ($this->live_check == 'true') {

      return $this->live($vat_id);

    } else {
      return 9; // es gibt keinen algorithmus
    }

  } // end slowakische republik

  // slowenien
  function checkVatID_si($vat_id) {
    if (strlen($vat_id) != 10)
      return 0;
    if ((int) $vat_id[2] == 0)
      return 0;

    $checksum = (int) $vat_id[9];
    $checkval = 0;

    for ($i = 2; $i <= 8; $i ++)
      $checkval += (int) $vat_id[10 - $i] * $i;
    $checkval = $this->modulo($checkval, 11) == 10 ? 0 : 11 - $this->modulo($checkval, 11);
    if ($checksum != $checkval)
      return 0;

    return 1;
  } // end slowenien

  // spanien
  function checkVatID_es($vat_id) {
    if (strlen($vat_id) != 11)
      return 0;

    $allowed = array ('A', 'B', 'C', 'D', 'E', 'F', 'G', 'Q');
    $checkval = false;

    for ($i = 0; $i < count($allowed); $i ++) {
      if (strtoupper($vat_id[2]) == $allowed[$i])
        $checkval = true;
    } // end for($i=0; $i<count($allowed); $i++)
    if (!$checkval)
      return 0;

    $checksum = (int) $vat_id[10];
    $checkval = 0;

    for ($i = 2; $i <= 8; $i ++)
      $checkval += $this->cross_summa((int) $vat_id[11 - $i] * ($this->is_even($i) ? 2 : 1));
    if ($checksum != 10 - $this->modulo($checkval, 10))
      return 0;

    return 1;
  } // end spanien

  // tschechien
  function checkVatID_cz($vat_id) {

    if ((strlen($vat_id) != 10) || (strlen($vat_id) != 11) || (strlen($vat_id) != 12))
      return 0;
    if (!is_numeric(substr($vat_id, 2)))
      return 0;

    if ($this->live_check == 'true') {

      return $this->live($vat_id);

    } else {
      return 9; // es gibt keinen algorithmus
    }
  } // end tschechien

  // ungarn
  function checkVatID_hu($vat_id) {

    if (strlen($vat_id) != 10)
      return 0;
    if (!is_numeric(substr($vat_id, 2)))
      return 0;

    if ($this->live_check == 'true') {

      return $this->live($vat_id);

    } else {
      return 9; // es gibt keinen algorithmus
    }
  } // end ungarn

  // zypern
  function checkVatID_cy($vat_id) {

    if (strlen($vat_id) != 11)
      return 0;

    if ($this->live_check == 'true') {

      return $this->live($vat_id);

    } else {
      return 9; // es gibt keinen algorithmus
    }
  } // end zypern

  /*******************************************************************/

  /********************************************************************
  * mathematische Hilfsfunktionen                                     *
  ********************************************************************/
  // modulo berechnet den rest einer division von $val durch $param
  function modulo($val, $param) {
    return $val - (floor($val / $param) * $param);
  } // end function modulo($val, $param)

  // stellt fest, ob eine zahl gerade ist
  function is_even($val) {
    return ($val / 2 == floor($val / 2)) ? true : false;
  } // end function is_even($val)

  // errechnet die quersumme von $val
  function cross_summa($val) {
    $val = (string) $val;
    $sum = 0;
    for ($i = 0; $i < strlen($val); $i ++)
      $sum += (int) $val[$i];
    return $sum;
  } // end function cross_summa((string) $val)
  /*******************************************************************/
}
?>