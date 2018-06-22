  <?php

require '../init.php';
class FrontEndMonitor
{
  // set up the database connection
  private function dbConn() {
    $db = new Connection();
    $con = $db->DatabaseConn();
    return $con;
  }
  // Gets all the data from the DATA table
  private function getData() {
    $pdo = $this->dbConn();
    $stmt = $pdo->prepare("SELECT * FROM data");
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $data;
  }

  private function filterInstance($instantie) {
    $pdo = $this->dbConn();
    $stmt = $pdo->prepare("SELECT * FROM data WHERE applicatie_instantie = $instantie");
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  // private function filterVersion($version) {
  //   $pdo = $this->dbConn();
  //   $stmt = $pdo->prepare("SELECT * FROM data WHERE applicatie_instantie = $version");
  //   $stmt->execute();
  //   $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
  // }
  // private function filterName($name) {
  //   $pdo = $this->dbConn();
  //   $stmt = $pdo->prepare("SELECT * FROM data WHERE applicatie_instantie = $name");
  //   $stmt->execute();
  //   $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
  // }
  // Creates the table and fills it with data from the database
  public function createTable(){
    ?> <link rel="stylesheet" href="../../monitor.css"> <?php
    $data = $this->getData();
    echo '<table>';
    echo '<tr>';
    foreach($data as $dataDump => $value) {
    echo '<th>'.$value['id'].'</th>';
    echo '<th>'.$value['Applicatie_naam'].'</th>';
    // echo '<th>'.$value['applicatie_instantie'].'</th>';
    // echo '<th>'.$value['applicatie_instantie'].'</th>';
    // echo '<th>'.$value['datum_gecreeerd'].'</th>';
    // echo '<th>'.$value['datum_aangekomen'].'</th>';
    // echo '<th>'.$value['niveau'].'</th>';
    // echo '<th>'.$value['beschrijving_kort'].'</th>';
    // echo '<th>'.$value['beschrijving_lang'].'</th>';
    }
    echo '</tr>';
    foreach ($data as $dataDump) {
      echo '<td>'.$dataDump.'</td>';
    }
      echo '</tr>';
      echo '<td>';
    // $data = $this->getData();
    // echo '<table>';
    // foreach ($data as $dataDump) {
    //   echo '<pre>';
    //   var_dump($dataDump['id'], $dataDump['id'], $dataDump['id'], $dataDump['id'], $dataDump['id']);
    //   echo '</pre>';
    //   echo '<tr>';
    //   foreach($dataDump as $singleData => $value) {
    //
    //     echo '<th>' . $singleData . '</th>';
    //   }
    //   echo '</tr>';
    //   break;
    // }
    // foreach ($data as $dataDump) {
    //   echo '<tr>';
    //   foreach($dataDump as $singleData => $value) {
    //     echo '<td>' . $value . '</td>';
    //   }
    //   echo '</tr>';
    // }
    // echo '</table>';
  }
}
$monitor = new FrontEndMonitor();
$monitor->createTable();

?>
