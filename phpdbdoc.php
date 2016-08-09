<?php

error_reporting(E_ALL);

/**
 * @author rafael
 *
 */
class phpdbdoc {

  protected $userdb = "";
  protected $linkdb = "";
  protected $database = "";
  protected $password = "";
  protected $db = "";
  protected $information = "information_schema";

  public function setUserdb($userdb) {
    $this->userdb = $userdb;
  }

  public function setLinkdb($linkdb) {
    $this->linkdb = $linkdb;
  }

  public function setDataBase($database) {
    $this->database = $database;
  }

  public function setPassword($password) {
    $this->password = $password;
  }

  public function getUserdb() {
    return $this->userdb;
  }

  public function getLinkdb() {
    return $this->linkdb;
  }

  public function getDataBase() {
    return $this->database;
  }

  public function getPassword() {
    return $this->password;
  }

  public function DBConnect() {
    $dsn = "mysql:dbname={$this->information};host={$this->getLinkdb()}";
    try {
      $this->db = new PDO($dsn, $this->getUserdb(), $this->getPassword());
//      $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $ex) {
      echo 'Connection failed: ' . $e->getMessage();
    }
  }

  public function getDoc() {
    $this->getTables();
  }

  private function getTables() {
    $mysql = $this->db;
    $tables = $mysql->query("select TABLE_NAME,TABLE_COMMENT,TABLE_COLLATION, CREATE_TIME from TABLES where TABLE_SCHEMA = '{$this->getDataBase()}'", PDO::FETCH_INTO, $tables_list);
//    while ($row = $tables->fetch_object()) {
//      $tables_list[] = $row;
//    }
    var_dump($tables_list);
  }

}

?>
