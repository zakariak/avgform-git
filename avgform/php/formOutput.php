<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);

$jsonData = file_get_contents('php://input');
$XMLFilePath = '../javascript/form.xml';

function parseXmlQuestions($url) {
  $xmlString = file_get_contents($url);
  $simpleXmlElement = simplexml_load_string($xmlString);
  return $simpleXmlElement;
}

$userData = json_decode($jsonData);
$xmlData = parseXmlQuestions($XMLFilePath);


$questionsById = array();
foreach ($xmlData->questions->question as $question) {
  $id = strval($question->id);
  $questionsById[$id] = $question;

  $optionsArray = array();
  foreach ($question->options as $opt ) {
    $optionsArray[] = $opt;
  }
}

$sum = 0;
$theQuestion = array();
$theAnswer = array();
foreach ($userData->choiceHistory as $userOutput ) {
  $question = intval($userOutput->q);
  $answer   = intval($userOutput->a);

  if (!$questionsById[$question]) {
    continue;
  }
  $theQuestion[] .= $questionsById[$question]->desc;
  $theAnswer[] .= $questionsById[$question]->options[$answer]->desc;

  $sum += $questionsById[$question]->options[$answer]->points;
}
  // var_dump($theQuestion, $theAnswer);
  // var_dump($theQuestion);

$finalResult = null;
foreach($xmlData->results as $results){
  foreach ($results as $key => $resultOutput) {
    if (($sum <= $resultOutput->maxtotalpoints) && ($sum >= $resultOutput->mintotalpoints)) {
      $resultCategorie = $resultOutput;
      break 2;
    }
  }
}
    // var_dump($theQuestion);
foreach($theQuestion as $question) {
  foreach($theAnswer as $answer) {

  }

}


$answer       = $theAnswer;

$total        = $sum;

$name         = $userData->submitHistory->name;
$companyName  = $userData->submitHistory->bname;
$email        = $userData->submitHistory->email;
$result       = $resultCategorie->name;

$id      =  uniqid('X');
$date    =  date('r');

$to      =  'zkzk@Live.nl';
$subject =  'Hello my frond this is Vladmir tjop secrut';
$message =  foreach($theQuestion as $object) { $object . ": ";} . ': ' . $answer . '</br>' . "\r\n" .
            'Eindresultaat: ' . $result . '</br>' . "\r\n" .
            'Totaalpunten: ' . $total . '</br>' . "\r\n" .
            'Naam: ' . $name . '</br>' . "\r\n" .
            'Bedrijfsnaam: ' . $companyName . '</br>' . "\r\n" .
            'Email-adress: ' . $email . '</br>' . "\r\n";

$body    =  '--' . $id . '-mixed' . "\r\n" .
            'Content-Type: multipart/alternative; boundary="' . $id . '-alt"' . "\r\n" . "\r\n" .

            '--' . $id . '-alt' . "\r\n" .
            'Content-Type: text/plain; charset="utf-8"' . "\r\n" .
            'Content-Transfer-Encoding: quoted-printable' . "\r\n" . "\r\n" .
            strip_tags($message) . "\r\n" .

            // '--' . $id . '-alt' . "\r\n" .
            // 'Content-Type: text/html; charset="utf-8"' . "\r\n" .
            // 'Content-Transfer-Encoding: quoted-printable' . "\r\n" .
            // $message . "\r\n"  .
            //
            // '--' . $id . '-alt--' . "\r\n" .
            // '--' . $id . '-mixed--'. "\r\n";

$headers =  'From: korendebug@korencrm.nl' . "\r\n" .
            // 'CC: joeri.noort@koren.nl' . "\r\n" .
            'Date:'. $date . "\r\n" .
            'Message-Id: <mail.' . $id . '@mail.korencrm>' . "\r\n" .
            'Content-Type: multipart/related; boundary="' . $id . '-mixed"' . "\r\n" .
            'X-MimeOLE: Produced by KorenCRM IT v4.0.13' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
            'MIME-Version: 1.0' . "\r\n";
//

var_dump($message);
// if(mail($to, $subject, $body, $headers))
// {
//   var_dump("Mail Sent Successfully");
// }else{
//   var_dump("Mail Not Sent");
// }
