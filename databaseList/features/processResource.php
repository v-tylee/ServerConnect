<?php
ini_set("display_errors", 1);
ini_set("track_errors", 1);
ini_set("html_errors", 1);
error_reporting(E_ALL);

  header("Access-Control-Allow-Headers: *");
  header("Access-Control-Allow-Credentials: true");
  header("Access-Control-Allow-Origin: http://gss.ebscohost.com/");
  header("Content-Security-Policy: upgrade-insecure-requests");
  header('Content-Type: application/json;charset=UTF-8');
  date_default_timezone_set('Asia/Taipei');

  include 'verifyToken.php';

  $jsonFile_direct = '../data/eResourceList.json';

  // parameters
  $type = $_POST["type"];
  $resource = json_decode($_POST["resource"], true);

  // get resource list
  $getResourceListJsonData = file_get_contents($jsonFile_direct);
  $resourceList = json_decode($getResourceListJsonData, true);

  if ($type === 'add') {
    // gen the uuid
    $resource['uuid'] = gen_uuid();

    // get stroke and zhuyin info

    $getStroke = getStrokeInfo($resource['local']['resourceName'], $resource['en']['resourceName']);


    $resource['en']['stroke'] = $getStroke['strokes'];
    $resource['en']['zhuyin'] = $getStroke['zhuyin'];
    $resource['en']['englishAlphabet'] = $getStroke['englishAlphabet'];
    $resource['local']['stroke'] = $getStroke['strokes'];
    $resource['local']['zhuyin'] = $getStroke['zhuyin'];
    $resource['local']['englishAlphabet'] = $getStroke['englishAlphabet'];

    // $getStroke = getStrokeInfo($resource['resourceName']);
    // $resource['stroke'] = $getStroke['strokes'];
    // $resource['zhuyin'] = $getStroke['zhuyin'];

    array_push($resourceList, $resource);

    // write back
    file_put_contents($jsonFile_direct, json_encode($resourceList, JSON_UNESCAPED_UNICODE));
    response('success', 'success');
  } else if($type === 'modify') {
    foreach($resourceList as $key => $row) {
      if(strcasecmp($row['uuid'], $resource['uuid']) == 0) {
        // get stroke and zhuyin info
        $getStroke = getStrokeInfo($resource['local']['resourceName'], $resource['en']['resourceName']);

        $resourceList[$key] = $resource;
        $resourceList[$key]['en']['stroke'] = $getStroke['strokes'];
        $resourceList[$key]['en']['zhuyin'] = $getStroke['zhuyin'];
        $resourceList[$key]['en']['englishAlphabet'] = $getStroke['englishAlphabet'];
        $resourceList[$key]['local']['stroke'] = $getStroke['strokes'];
        $resourceList[$key]['local']['zhuyin'] = $getStroke['zhuyin'];
        $resourceList[$key]['local']['englishAlphabet'] = $getStroke['englishAlphabet'];
        break;
      }
    }

    // write back
    file_put_contents($jsonFile_direct, json_encode($resourceList, JSON_UNESCAPED_UNICODE));
    response('success', 'success');
  } else if ($type === 'delete') {
    foreach($resourceList as $key => $row) {
      if(strcasecmp($row['uuid'], $resource['uuid']) == 0) {
        // unset($resourceList['rows'][$key]);
        array_splice($resourceList, $key, 1);
        break;
      }
    }

    // write back
    file_put_contents($jsonFile_direct, json_encode($resourceList, JSON_UNESCAPED_UNICODE));
    response('success', 'success');
  }

  function getStrokeInfo($str_resourceName_local, $str_resourceName_en) {
    $getStrokesJsonData = file_get_contents('../data/UniHanO.json');
    $strokes = json_decode($getStrokesJsonData, true);
    $resultExist = false;

    // get first char
    $chars = preg_split('/(?<!^)(?!$)/u', $str_resourceName_local);
    $firstChar = $chars[0];
    $result = [];

    foreach($strokes as $stroke) {
      if(strcasecmp($firstChar, $stroke['char']) == 0) {
        $result = $stroke;
        
        $zhuyin = preg_split('/(?<!^)(?!$)/u', $stroke['zhuyin']);
        $firstZhuyin = $zhuyin[0];
        $result['zhuyin'] = $firstZhuyin;

        if(strlen(strval($stroke['strokes'])) < 2) {
          $result['strokes'] = '0'.$stroke['strokes'];
        }
        
        $resultExist = true;
        break;
      }
    }

    if(!$resultExist) {
      $result['zhuyin'] = '';
      $result['strokes'] = '0';
      $result['englishAlphabet'] = $firstChar;
    } else {
      $chars = preg_split('/(?<!^)(?!$)/u', $str_resourceName_en);
      $firstChar = $chars[0];
      $result['englishAlphabet'] = $firstChar;
    }

    return $result;
  }

  // uuid V4
  function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
      // 32 bits for "time_low"
      mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

      // 16 bits for "time_mid"
      mt_rand( 0, 0xffff ),

      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 4
      mt_rand( 0, 0x0fff ) | 0x4000,

      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      mt_rand( 0, 0x3fff ) | 0x8000,

      // 48 bits for "node"
      mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
  }

  function response($errorType, $message) {
    $res = array('status' => $errorType, 'type' => $message);
    echo json_encode($res, JSON_UNESCAPED_UNICODE);
  }
?>
