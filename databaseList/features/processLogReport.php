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

  $getResourceData = file_get_contents('../data/eResourceList.json');
  $resourceData = json_decode($getResourceData, true);

  $getIdentity = file_get_contents('../data/authIdentity.json');
  $identityData = json_decode($getIdentity, true);

  $getLogJsonData = file_get_contents('../data/logUserCountClick.json');
  $logData = json_decode($getLogJsonData, true);

  $dateRange = json_decode($_GET["searchRange"], true);
  $startTime = new DateTime($dateRange["startDate"]);
  $endTime = new DateTime($dateRange["endDate"]);
  $endTime = date_modify($endTime, '+1 day');

  $generateType = $dateRange["type"];

  // filter the log data
  $clickedData_filtered_by_date = [];
  foreach($logData['log'] as $log) {
    $logDateTime = strtotime($log['clickedDateTime']);
    $processedStartTime = strtotime($startTime->format('Y-m-d'));
    $processedEndTime = strtotime($endTime->format('Y-m-d'));

    if($logDateTime >= $processedStartTime && $logDateTime <= $processedEndTime) {
      array_push($clickedData_filtered_by_date, $log);
    }
  }

  // create map and countable template
  $template = [];
  foreach($resourceData as $resource) {
    $template[$resource['uuid']] = array(
      "name" => $resource['local']['resourceName'],
      "clickTimes" => 0,
    );
    $template['total'] = 0;
  }

  // choose date formate
  if($generateType === 'month') {
    $interval= new DateInterval('P1M');
    $dateFormat = 'Y-m';
  } else if($generateType === 'day') {
    $interval= new DateInterval('P1D');
    $dateFormat = 'Y-m-d';
  }

  if($generateType === 'month' || $generateType === 'day') {
    // create an array which the key is period of date/month
    $report = array_period($interval, $dateFormat, $startTime, $endTime);
    foreach($report as $key => $field) {
      $report[$key] = new ArrayObject($template);
    }

    // create log counting array
    foreach($clickedData_filtered_by_date as $log) {
      $logDateTime = new DateTime($log['clickedDateTime']);
      $string_time = $logDateTime->format($dateFormat);
      // $ary_tempDate = explode(" ", $log['clickedDateTime']);
      // $date = $ary_tempDate[0];

      $report[$string_time][$log['uuid']]['clickTimes']++;
      $report[$string_time]['total']++;
      // if (array_key_exists($log['id'], $report[$string_time])) {
      //   $report[$string_time][$log['id']]['clickTimes']++;
      // } else {
      //   $report[$string_time][$log['id']]['name'] = $resourceIdArray[$log['id']];
      //   $report[$string_time][$log['id']]['clickTimes'] = 1;
      // }
    }
  } else if($generateType === 'userDepartment' || $generateType === 'userIdentity') {
    $report = array_identityAndDeparture($generateType, $identityData);
    foreach($report as $key => $field) {
      $report[$key] = new ArrayObject($template);
    }
    
    // create log counting array
    foreach($clickedData_filtered_by_date as $log) {
      $report[$log[$generateType]][$log['uuid']]['clickTimes']++;
      $report[$log[$generateType]]['total']++;
    }

    $report = array_replaceIdentityKeyName($generateType,$report, $identityData);
  }

  $response = [];
  $response['status'] = 'success';
  $response['report'] = $report;

  echo json_encode($response, JSON_UNESCAPED_UNICODE);

  function array_replaceIdentityKeyName($generateType, $array_original, $array_reference) {
    $array_result = [];
    
    foreach ($array_original as $key => $value) {
      // print_r($array_reference[$generateType]);
      foreach ($array_reference[$generateType] as $rkey => $rValue) {
        if($rValue['id'] === $key) {
          $array_result[$rValue['name']] = $value;
        }
      }
      // $array_result[$array_reference[$generateType][$key]['name']] = $value;
    }

    return $array_result;
  }

  function array_identityAndDeparture($type, $identityData) {
    $array_result = [];

    foreach ($identityData[$type] as $key => $value) {
      $array_result[$value["id"]] = [];
    }
    return $array_result;
  }

  function array_period($interval, $dateFormat, $startTime, $endTime) {
    $array_result = [];
    $period = new DatePeriod($startTime, $interval, $endTime);

    foreach ($period as $key => $value) {
      $array_result[$value->format($dateFormat)] = [];
    }
    return $array_result;
  }
?>
