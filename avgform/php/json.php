<?php

$XMLFilePath = '../javascript/form.xml';

function parse($url) {
  $getFile = file_get_contents($url);
  $xmlString = simplexml_load_string($getFile);

  $json = json_encode($xmlString, JSON_PRETTY_PRINT);

  return $json;
}

function parseXmlQuestions($url) {
  $xmlString = file_get_contents($url);
  $simpleXmlElement = simplexml_load_string($xmlString);

  return $simpleXmlElement;
}


header('Content-Type: application/json');

$simpleXmlElement = parseXmlQuestions($XMLFilePath);

$dataClean = array();

$dataClean['home']  = $simpleXmlElement->home;

$dataClean['results'] = array();
foreach( $simpleXmlElement->results->result as $element ) {
  $dataClean['results'][] = $element;
}

$dataClean['questions'] = array();
foreach( $simpleXmlElement->questions->question as $element ) {
  $dataClean['questions'][] = $element;
}



// var_dump($simpleXmlElement->results->result->count());;
// if( is_array($simpleXmlElement->results->result) ) {
//     $rv['results'] = $simpleXmlElement->results->result;
// } else {
//   $rv['results'][] = $simpleXmlElement->results->result;
// }


echo json_encode($dataClean, JSON_PRETTY_PRINT);

//echo json_encode($simpleXmlElement, JSON_PRETTY_PRINT);

?>
