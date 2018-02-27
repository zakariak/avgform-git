<?php

//$str_json = file_get_contents('../javascript/js.js');
//$response = json_decode($str_json, true);

//mail("zkzk@live.nl", $repsonse);

$jsonData = file_get_contents('php://input');

$dataObject = json_decode($jsonData); //JSON.Parse()

//var_Dump($dataObject);

// var_dump($dataObject->jesus);
//echo '['.strtoupper($data).']';
// var_dump($dataObject->choiceHistory[0 ]);
foreach ($dataObject as $key) {
  foreach ($key as $arraykey) {
    foreach ($arraykey as $keywhatever => $value) {
          var_dump($keywhatever);
    }

  }
}
// foreach ($dataObject as $key => $value) {
//     var_dump($key[$value]);
// }
// var_dump($dataObject->choiceHistory[0]->points);
// echo json_encode($dataObject);

// $dataobject->choicehistory->desc

// $dataobject->choicehistory->desc


die();


$results = array(
  'success' => false,
  'error' => 'Unknown error'
);


if( $dataObject->ikSnapWatErMisGaat ) {
  $results['error'] = 'Dit is wat er mis is: doe je je best wel';
}
if( $dataObject->allesIsGoedEnzo ) {
  $results['success'] = true;
  $results['error'] = null;
}


echo json_encode($results);

//echo date('r');
 ?>
