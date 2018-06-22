<?php

class Connection
{
  private $conn;
  private $ini;

  private function config() {
    $this->ini = parse_ini_file('C:/config/monitor.ini');
    return $this->ini;
  }

  public function DatabaseConn() {
    // create db connection
    $ini = $this->config();
    $servername = $ini['db_host'];
    $username   = $ini['db_user'];
    $password   = $ini['db_password'];
    $dbname     = $ini['db_name'];

    try {
        $this->conn = new PDO("mysql:host=$servername;dbname={$dbname}",$username,$password);
        return $this->conn;
        }

    catch(PDOException $e)
        {
        echo "Connection failed: " . $e->getMessage();
        }

  }
}
?>
