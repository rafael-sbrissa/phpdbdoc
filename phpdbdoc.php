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
        $constraints = $this->getConstraints($tables);
//        echo "<pre>";
//        print_r($constraints);
//        echo "</pre>";
        $this->render($tables, $fields, $constraints);
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
        return $fields_list;
    }

    private function getConstraints($tables) {
        $constraints_list = array();
        foreach ($tables as $table) {
            $cts = $this->db->prepare("SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME "
                    . "FROM KEY_COLUMN_USAGE  "
                    . "WHERE TABLE_SCHEMA = '{$this->getDataBase()}' AND TABLE_NAME = '{$table['TABLE_NAME']}'");
            $cts->execute();
            $cts->setFetchMode(PDO::FETCH_ASSOC);
            $constraints_list[$table['TABLE_NAME']] = $cts->fetchAll();
        }
        return $constraints_list;
    }

    private function render($tables, $fields, $constraints) {
        echo "<!DOCTYPE html>
          <html>
            <head>
                <title>DB Doc - {$this->getDataBase()}</title>
                <meta charset=\"UTF-8\">
                <meta name=\"viewport\" content=\"width = device-width, initial-scale = 1.0\">
            </head>
            <body>\n";
        echo "<table>
            <thead>
                <tr>
                    <th>Table Name</th>
                    <th>Comment</th>
                    <th>Collaction</th>
                    <th>Create Time</th>
                </tr>
            </thead>\n";
        echo "<tbody>\n";
        foreach ($tables as $table) {
            echo "<tr>\n";
            echo "<td>";
            print $table["TABLE_NAME"];
            echo "</td>\n";
            echo "<td>";
            print $table["TABLE_COMMENT"];
            echo "</td>\n";
            echo "<td>";
            print $table["TABLE_COLLATION"];
            echo "</td>\n";
            echo "<td>";
            print $table["CREATE_TIME"];
            echo "</td>\n";
            echo "</tr>\n";
        }
        echo "</tbody>\n";
        echo "</table>\n";
        foreach ($fields as $name => $field) {
            echo "<table>
            <thead>
                <tr>
                <th colspan=\"6\">{$name}<th>
                </tr>
                <tr>
                    <th>Column Name</th>
                    <th>Nullable</th>
                    <th>Type</th>
                    <th>Key</th>
                    <th>Extra</th>
                    <th>Comment</th>
                </tr>
            </thead>\n";
            echo "<tbody>\n";
            foreach ($field as $row) {
                echo "<tr>\n";
                echo "<td>";
                print $row["COLUMN_NAME"];
                echo "</td>\n";
                echo "<td>";
                print $row["IS_NULLABLE"];
                echo "</td>\n";
                echo "<td>";
                print $row["COLUMN_TYPE"];
                echo "</td>\n";
                echo "<td>";
                print $row["COLUMN_KEY"];
                echo "</td>\n";
                echo "<td>";
                print $row["EXTRA"];
                echo "</td>\n";
                echo "<td>";
                print $row["COLUMN_COMMENT"];
                echo "</td>\n";
                echo "</tr>\n";
            }
            echo "</tbody>\n";
            echo "</table>\n";
        }
        foreach ($constraints as $name => $constraint) {
            echo "<table>
            <thead>
                <tr>
                <th colspan=\"6\">{$name}<th>
                </tr>
                <tr>
                    <th>Constraint Name</th>
                    <th>Table</th>
                    <th>Column</th>
                    <th>Referenced Table</th>
                    <th>Referenced column</th>
                </tr>
            </thead>\n";
            echo "<tbody>\n";
            foreach ($constraint as $row) {
                echo "<tr>\n";
                echo "<td>";
                print $row["CONSTRAINT_NAME"];
                echo "</td>\n";
                echo "<td>";
                print $row["TABLE_NAME"];
                echo "</td>\n";
                echo "<td>";
                print $row["COLUMN_NAME"];
                echo "</td>\n";
                echo "<td>";
                print $row["REFERENCED_TABLE_NAME"];
                echo "</td>\n";
                echo "<td>";
                print $row["REFERENCED_COLUMN_NAME"];
                echo "</td>\n";
                echo "</tr>\n";
            }
            echo "</tbody>\n";
            echo "</table>\n";
        }
        echo "</body>"
        . "</html>";
    }

}

?>
