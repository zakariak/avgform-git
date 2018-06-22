<?php
require 'connection.php';
class Monitor
{
  // set up the database connection
  private function dbConn() {
    $db = new Connection();
    $conn = $db->DatabaseConn();
    return $conn;
  }
  // converts the time to UTC
  private function utcNow() {
    $date = new DateTime('now');
    $date->setTimezone(new DateTimeZone('UTC'));
    $utcNow = $date->format('Y-m-d H:i:s');
    return $utcNow;
  }
  //  Checks data from the uploaded zipfile and inserts the valid array into the database
  public function insertDataFromZipfile($zipFilePath) {
    $zip = new ZipArchive;
    if( !$zip->open($zipFilePath) )  {
      throw new Exception("Failed to open zipfile");
    }
    $date = $this->utcNow();
    $successList = array();
    $pdo = $this->dbConn();
    for( $i = 0; $i < $zip->numFiles; $i++ ) {
      $name = $zip->getNameIndex($i);
      $extension = pathinfo($name);
      if($extension['extension'] == 'json') {
        $data = $zip->getFromIndex($i);
        $obj = json_decode($data);
      } else {
        echo "the file: $name is not a JSON file <br>";
        continue;
      }
      if($extension['extension'] != 'json') {
        echo "the file: $name is not a JSON file <br>";
        continue;
      }
      $data = $zip->getFromIndex($i);
      $obj = json_decode($data);
      if(!$zipFilePath = '') {
        $stmt2 = $pdo->prepare("SELECT * FROM data WHERE id='$obj->id'");
        $stmt2->execute();
        $num_rows = $stmt2->fetchAll();
        if ($num_rows) {
          $successList[] .= $obj->id;
          continue;
        }
        $stmt = $pdo->prepare('INSERT INTO data (id, applicatie_naam, applicatie_instantie, applicatie_versie, datum_gecreeerd, datum_aangekomen, niveau, beschrijving_kort, beschrijving_lang)
        VALUES (:id, :applicatie_naam, :applicatie_instantie, :applicatie_versie, :datum_gecreeerd, :datum_aangekomen, :niveau, :beschrijving_kort, :beschrijving_lang)');
        $stmt->bindParam(':id', $obj->id);
        $stmt->bindParam(':applicatie_naam', $obj->applicatie_naam);
        $stmt->bindParam(':applicatie_instantie', $obj->applicatie_versie);
        $stmt->bindParam(':applicatie_versie', $obj->applicatie_versie);
        $stmt->bindParam(':datum_gecreeerd', $obj->datum_gecreeerd);
        $stmt->bindParam(':datum_aangekomen', $date);
        $stmt->bindParam(':niveau', $obj->niveau);
        $stmt->bindParam(':beschrijving_kort', $obj->beschrijving_kort);
        $stmt->bindParam(':beschrijving_lang', $obj->beschrijving_lang);
        $stmt->execute();
        $successList[] .= $obj->id;
      }
    }
      $json = json_encode($successList);
      echo $json;
  }
}
?>
