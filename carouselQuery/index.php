<?php
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json; charset=utf-8');
  header("Content-Security-Policy: upgrade-insecure-requests");
  date_default_timezone_set('Asia/Taipei');

  $randomBookQuantity = 20;
  $apiConnection = "https://eit.ebscohost.com/Services/SearchService.asmx/Search?prof=tylee.main.eit&&pwd=ebs3705&db=edsebk";

  $keyword = str_replace(' ', '+', $_GET['uquery']);
  $profile = $_GET['profile'];
  $custID = $_GET['custID'];

  // $keyword = "blockchain";

  $bookInfoList = getBookInfoFromServer($apiConnection, $keyword, $profile, $custID);

  echo json_encode($bookInfoList, JSON_NUMERIC_CHECK);

  function getBookInfoFromServer($apiUrl, $keyword, $profile, $custID) {
    $result = array();

    // get value from API
    $connectApiUrl = $apiUrl."&query=".$keyword;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $connectApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    $xml = curl_exec($ch);
    $parseXml = simplexml_load_string($xml);
    curl_close($ch);


    foreach($parseXml->SearchResults->records->children() as $rec) {
      // get url and parse
      $parseUrlParam = parse_url($rec->plink);
      parse_str($parseUrlParam['query'], $query);
      $parts = parse_url($url);
      $AN = $query['AN'];
      $imgUrl = 'http://rps2images.ebscohost.com/rpsweb/othumb?id=NL$'.$AN.'$PDF&s=l';
      $directionUrl = 'http://search.ebscohost.com/login.aspx?direct=true&db=nlebk&AN='.$AN.'&site=ehost-live&custid='.$custID.'&authtype=ip,uid&groupid=main&profileid='.$profile.'&scope=site';
      $onErrorImgUrl = 'http://rps2images.ebscohost.com/rpsweb/othumb?id=NL$'.$AN.'$EPUB&s=l';
      $title = $rec->header->controlInfo->bkinfo->btl;
      $tempItem = array('title' => strval($title), 'imgUrl' => strval($imgUrl), 'directionUrl' => strval($directionUrl), 'onErrorUrlImg' => strval($onErrorImgUrl));
      array_push($result, $tempItem);
    }

    return $result;
  }
?>
