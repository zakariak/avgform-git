<?php

//@todo op false zetten om emailen te beginnen
$debugMode = true;

error_reporting(E_ALL);
ini_set('display_errors', 1);

function exception_error_handler($severity, $message, $file, $line) {
	if (!(error_reporting() & $severity)) {
		return;
	}
	throw new ErrorException($message, 0, $severity, $file, $line);
}
set_error_handler("exception_error_handler");


function exception_handler($ex) {
	$ex instanceof Exception;
	echo "Er heeft zich een fout voorgedaan: Foutcode {$ex->getLine()}";
	die();
}
set_exception_handler('exception_handler');


function debug_exception_handler($ex) {
	$ex instanceof Exception;
	echo $ex;
	die();
}

if( $debugMode ) {
	set_exception_handler('debug_exception_handler');

}

$jsonData = file_get_contents('php://input');
$XMLFilePath = '../javascript/form.xml';

function parseXmlQuestions($url) {
  $xmlString = file_get_contents($url);
  $simpleXmlElement = simplexml_load_string($xmlString);
  return $simpleXmlElement;
}

$userData = json_decode($jsonData);


//@todo test validity of email addresses
$toUserAddressIsInvalid = $userData->submitHistory->email;
var_dump($toUserAddressIsInvalid);
if(filter_var($toUserAddressIsInvalid, FILTER_VALIDATE_EMAIL)) {
	throw new Exception("toUserAddressIsInvalid");
}

 // empty($toUserAddressIsInvalid) ||

//special case voor lege choiceHistory
if( empty($userData->choiceHistory) ) {
	throw new Exception("Blank choice history");
}


//@todo hecken of choice history eigenlijk wel mogelijk is.


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

$finalResult = null;
foreach($xmlData->results as $results){
  foreach ($results as $key => $resultOutput) {
    if (($sum <= $resultOutput->maxtotalpoints) && ($sum >= $resultOutput->mintotalpoints)) {
      $resultCategorie = $resultOutput;
      break 2;
    }
  }
}

$combined = array_combine($theQuestion,$theAnswer);
$combinedQA = '';
foreach ($combined as $key => $value) {
  $combinedQA .= $key . ': ' . $value . "\r\n" . "\r\n";
}


// mail to erik

$total        = $sum . "\r\n";

$name         = $userData->submitHistory->name . "\r\n";
$companyName  = $userData->submitHistory->bname . "\r\n";
$email        = $userData->submitHistory->email . "\r\n";
$result       = $resultCategorie->name . "\r\n";

$id      =  uniqid('X');
$date    =  date('r');
$to      =  'zkzk@Live.nl';
$subject =  'AVG Tool gebruikt door ' . $companyName;

$message =  $combinedQA . "\r\n" .
'Naam: ' . $name . "\r\n" .
'Bedrijfsnaam: ' . $companyName . "\r\n" .
'Email-adres: ' . $email . "\r\n" . "\r\n"  .
'Eindresultaat: ' . $result . "\r\n" .
'Totaalpunten: ' . $total . "\r\n" . "\r\n" .
'Datum: ' . $date;

$body    =  '--' . $id . '-mixed' . "\r\n" .
'Content-Type: multipart/alternative; boundary="' . $id . '-alt"' . "\r\n" . "\r\n" .

'--' . $id . '-alt' . "\r\n" .
'Content-Type: text/plain; charset="utf-8"' . "\r\n" .
'Content-Transfer-Encoding: quoted-printable' . "\r\n" . "\r\n" .
strip_tags($message) . "\r\n";

$headers =  'From: korendebug@korencrm.nl' . "\r\n" .
'Date:'. $date . "\r\n" .
'Message-Id: <mail.' . $id . '@mail.korencrm>' . "\r\n" .
'Content-Type: multipart/related; boundary="' . $id . '-mixed"' . "\r\n" .
'X-MimeOLE: Produced by KorenCRM IT v4.0.13' . "\r\n" .
'X-Mailer: PHP/' . phpversion();
'MIME-Version: 1.0' . "\r\n";

// mail to user

$toUser      =  $email;
$subjectUser =  'Bekijk je AVG score';

$messageUser =  'Geachte ' . ucfirst($name) . "\r\n" . "\r\n" .
'U heeft gebruik gemaakt van de AVG Tool en hieronder ziet u het resultaat daarvan.' . "\r\n" . "\r\n" .
$result . "\r\n" . "\r\n" .
'Bekijk de technische en organisatorische maatregelen die wij voor onze software hebben genomen hier op https://www.koren.nl/inleiding-avg-gdpr en neem contact met ons op als je interesse hebt in onze producten of diensten.' . "\r\n" . "\r\n" .
'Met vriendelijke groet,' . "\r\n" . "\r\n" .
'Team Kor�n';

$bodyUser    =   '--' . $id . '-mixed' . "\r\n" .
'Content-Type: multipart/alternative; boundary="' . $id . '-alt"' . "\r\n" . "\r\n" .
'--' . $id . '-alt' . "\r\n" .
'Content-Type: text/plain; charset="utf-8"' . "\r\n" .
'Content-Transfer-Encoding: quoted-printable' . "\r\n" . "\r\n" .
strip_tags($messageUser) . "\r\n";




if($debugMode) {
	ob_start();

	echo "==[Mail to us]===\n";
	echo "To: {$to}\n";
	echo "subject: {$subject}\n";
	echo "body: {$body}\n";
	echo "\n\n\n\n";
	echo "==[Mail to them]===\n";
	echo "To: {$toUser}\n";
	echo "subject: {$subjectUser}\n";
	echo "body: {$bodyUser}\n";
	$outData = ob_get_clean();

	$outPath = '../log/'.microtime(true).'.txt';
	file_put_contents($outPath, $outData);
	echo "logged to {$outPath}";
	die();
}





if(mail($to, $subject, $body, $headers) && mail($toUser, $subjectUser, $bodyUser, $headers))
{
  var_dump("Mail Sent Successfully");
}else{
  var_dump("Mail Not Sent");
}

// if)
// {
//   var_dump("Mail Sent Successfully");
// }else{
//   var_dump("Mail Not Sent");
// }
