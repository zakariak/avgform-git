<?php
class Plugin
{
  public $applicationName;
  public $applicationInstance;
  public $applicationVersion;
  private $level;
  private $descShort;
  private $descLong;
  private $ini;

  function __construct($applicationName, $applicationInstance, $applicationVersion)
  {
    $this->applicationName = $applicationName;
    $this->applicationInstance = $applicationInstance;
    $this->applicationVersion = $applicationVersion;
  }
// gets the config file for the plugin
  private function config() {
    $config = 'C:/config/plugin.ini';
    $this->ini = parse_ini_file($config);
    return $this->ini;
  }
// Converts the time to UTC
  private function utcNow() {
    $date = new DateTime('now');
    $date->setTimezone(new DateTimeZone('UTC'));
    $utcNow = $date->format('Y-m-d H:i:s');
    return $utcNow;
  }
// get the log directory
  private function getLogDirectory() {
    $ini = $this->config();
    return $ini['log_directory'];
  }
// creates the log directory if it doesn't exists
  private function createLogLocation() {
    if (!file_exists($this->getLogDirectory() ) ) {
      mkdir($this->getLogDirectory(), 0777, true);
    }
  }

// Generate the filename
  private static function GenerateLogId() {
    return uniqid('log_', true);
  }

// Generates a json file with a json string containing the parameters
  public function log($level, $descShort, $descLong) {
    $logId = self::GenerateLogId();
    $array = array(
      'id' => $logId,
      'applicatie_naam' => $this->applicationName,
      'applicatie_instantie' => $this->applicationInstance,
      'applicatie_versie' => $this->applicationVersion,
      'datum_gecreeerd' => $this->utcNow(),
      'niveau' => $level,
      'beschrijving_kort' => $descShort,
      'beschrijving_lang' => $descLong
    );
    $this->createLogLocation();
    $content = json_encode($array);
    $filePath = $this->getLogDirectory();
    $fileName = $filePath . '/' . $logId . '.json';
    $fileLocation = fopen($fileName, 'w+');
    fwrite($fileLocation, $content);
    fclose($fileLocation);
  }
}
?>
