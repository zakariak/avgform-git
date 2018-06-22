<?php
/**
*
*/

require '../init.php';

error_reporting(E_ALL);

class Relay
{
  private $ini;

  private function config() {
    $this->ini = parse_ini_file('C:/config/relay.ini');
    return $this->ini;
  }

  private function getLogDir() {
    $ini = $this->config();
    return $ini['log_directory'];
  }

  private function getDir() {
    $files = glob($this->getLogDir() . '/log_*.json');
    return $files;
  }

  private function deleteReturnedLogs($returnedLogs) {
    $logs = json_decode($returnedLogs);
    $logsFromDirectory = array();
    if($logs !== NULL) {
      foreach ($logs as $key => $value) {
        $logsFromMonitor[] = $value;
      }
      foreach ($this->getDir() as $file => $value) {
        $base = pathinfo($value, PATHINFO_FILENAME);
        $logsFromDirectory[] = $base;
      }
      $intersectedLogs = array_intersect($logsFromDirectory, $logsFromMonitor);
      if(count($intersectedLogs) >= 0) {
        foreach($intersectedLogs as $log => $value) {
          $path = glob($this->getLogDir() . '/'. $value.'.json');
          unlink($path[0]);
        }
      }
    }
  }

  private function curl($filename) {
    $ini = parse_ini_file('C:/config/relay.ini');
    $target_url = $ini['target_url'];

    $cFile = curl_file_create($filename);
    $post = array('file_contents'=> $cFile);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$target_url);
    curl_setopt($ch, CURLOPT_POST,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $result = curl_exec($ch);

    if(curl_error($ch)) {
      throw new Exception("Curl Error:" . curl_errno($ch) . ': '. curl_error($ch));
    } elseif(!$result) {
      echo 'Nothing To Insert!';
      die();
    }
    curl_close($ch);
    $this->deleteReturnedLogs($result);
  }

  public function zipLogs() {
    $files = $this->getDir();
    $filename = sys_get_temp_dir() . '/relayZip_'.uniqid(true). '.zip';
    // var_dump($filename);
    $zip = new ZipArchive;
    $zip->open($filename, ZipArchive::CREATE);

    foreach ($files as $file => $value) {
      $zip->addFile($value, pathinfo( $value, PATHINFO_BASENAME));
    }
    $zip->close();
    $this->curl($filename);
    unlink($filename);
    echo 'Succesfully Inserted!';
  }
}


$relay = new Relay();
$relay->zipLogs();
?>
