<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Security-Policy: upgrade-insecure-requests");
  header('Content-Type: application/json');

  $jsonFile_direct = '../data/eResourceList.json';

  // get resource list
  $getResourceListJsonData = file_get_contents('../data/eResourceList.json');
  $resourceList = json_decode($getResourceListJsonData, true);

  $allZhuYin = 'ㄅㄆㄇㄈㄉㄊㄋㄌㄍㄎㄏㄐㄑㄒㄓㄔㄕㄖㄗㄘㄙㄧㄨㄩㄚㄛㄜㄝㄞㄟㄠㄡㄢㄣㄤㄥㄦ';
  $allEnglishAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $zhuYins = preg_split('/(?<!^)(?!$)/u', $allZhuYin);
  $englishAlphabets = str_split($allEnglishAlphabet);
  $zhuyin_map = [];
  $englishAlphabet_map = [];

  foreach($zhuYins as $key => $row) {
    $zhuyin_map[$row] = false;
  }
  foreach($englishAlphabets as $key => $row) {
    $englishAlphabet_map[$row] = false;
  }

  foreach($resourceList['rows'] as $key => $row) {
    if($row['zhuyin'] !== '') {
      $zhuyin_map[$row['zhuyin']] = true;
    } else if(preg_match("/^[a-zA-Z]$/", $row['englishAlphabet'])) {
      $char_uppercase = strtoupper($row['englishAlphabet']);
      $englishAlphabet_map[$char_uppercase] = true;
    }
  //   }
  //   $chars = preg_split('/(?<!^)(?!$)/u', $row['resourceName']);
  //   $firstChar = $chars[0];
  //   $resultExist = false;

  //   foreach($strokes as $stroke) {
  //     if(strcasecmp($firstChar, $stroke['char']) == 0) {

  //       $zhuyin = preg_split('/(?<!^)(?!$)/u', $stroke['zhuyin']);
  //       $firstZhuyin = $zhuyin[0];
  //       $resourceList['rows'][$key]['zhuyin'] = $firstZhuyin;

  //       if(strlen(strval($stroke['strokes'])) < 2) {
  //         $resourceList['rows'][$key]['strokes'] = '0'.$stroke['strokes'];
  //       } else {
  //         $resourceList['rows'][$key]['strokes'] = $stroke['strokes'];
  //       }
  //       $resultExist = true;
  //       break;
  //     }
  //   }

  //   if(!$resultExist) {
  //     $resourceList['rows'][$key]['zhuyin'] = '';
  //     $resourceList['rows'][$key]['strokes'] = '0';
  //   }
  }
  print_r($zhuyin_map);
  print_r($englishAlphabet_map);
  // write back
  // file_put_contents($jsonFile_direct, json_encode($resourceList, JSON_UNESCAPED_UNICODE));

  // $res = array('type' => 'success', 'mesage' => 'success');
  // echo json_encode($res, JSON_UNESCAPED_UNICODE);
?>
