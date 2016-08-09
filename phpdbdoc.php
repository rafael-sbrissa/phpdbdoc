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
    } catch (PDOException $ex) {
      echo 'Connection failed: ' . $e->getMessage();
    }
  }

  public function getDoc() {
    $tables = $this->getTables();
    $fields = $this->getFields($tables);
  }

  private function getTables() {
    $tables = $this->db->prepare("select TABLE_NAME,TABLE_COMMENT,TABLE_COLLATION, CREATE_TIME from TABLES where TABLE_SCHEMA = '{$this->getDataBase()}'");
    $tables->execute();
    $tables->setFetchMode(PDO::FETCH_ASSOC);
    $tables_list = $tables->fetchAll();
    return $tables_list;
  }

  private function getFields($tables) {
    $fields_list = array();
    foreach ($tables as $table) {
      $fields = $this->db->prepare("SELECT COLUMN_NAME, IS_NULLABLE, COLUMN_TYPE, COLUMN_KEY, EXTRA, COLUMN_COMMENT "
          . "FROM COLUMNS "
          . "WHERE TABLE_SCHEMA = '{$this->getDataBase()}' AND TABLE_NAME = '{$table['TABLE_NAME']}'");
      $fields->execute();
      $fields->setFetchMode(PDO::FETCH_ASSOC);
      $fields_list[$table['TABLE_NAME']] = $fields->fetchAll();
    }
//    var_dump($fields_list);
    return $fields_list;
  }

}

?>
