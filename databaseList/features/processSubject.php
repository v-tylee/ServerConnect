<?php
  header("Access-Control-Allow-Headers: *");
  header("Access-Control-Allow-Credentials: true");
  header("Access-Control-Allow-Origin: http://gss.ebscohost.com/");
  header("Content-Security-Policy: upgrade-insecure-requests");
  header('Content-Type: application/json;charset=UTF-8');
  date_default_timezone_set('Asia/Taipei');

  include 'verifyToken.php';

  $jsonFile_direct = '../data/subject.json';

  $getSubjectData = file_get_contents('../data/subject.json');
  $subjectList = json_decode($getSubjectData, true);

  // parameters
  $type = $_POST["type"];
  $received_subject = '';
  if(isset($_POST['subject'])) {
    $received_subject = json_decode($_POST["subject"], true);
  }

  if ($type === 'add') {
    // gen UUID
    $received_subject['subjectID'] = gen_uuid();

    array_push($subjectList, $received_subject);

    // write back
    file_put_contents($jsonFile_direct, json_encode($subjectList, JSON_UNESCAPED_UNICODE));
    response('success', 'success');
  } else if($type === 'modify') {
    foreach($subjectList as $key => $row) {
      if(strcasecmp($row['subjectID'], $received_subject['subjectID']) == 0) {
        $subjectList[$key] = $received_subject;
        break;
      }
    }

    // write back
    file_put_contents($jsonFile_direct, json_encode($subjectList, JSON_UNESCAPED_UNICODE));
    response('success', 'success');
  } else if($type === 'delete') {
    foreach($subjectList as $key => $row) {
      if(strcasecmp($row['subjectID'], $received_subject['subjectID']) == 0) {
        array_splice($subjectList, $key, 1);
        break;
      }
    }

    // write back
    file_put_contents($jsonFile_direct, json_encode($subjectList, JSON_UNESCAPED_UNICODE));
    response('success', 'success');
  }

  function response($errorType, $message) {
    $res = array('status' => $errorType, 'type' => $message);
    echo json_encode($res, JSON_UNESCAPED_UNICODE);
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
?>
