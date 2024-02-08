<?php 

namespace server;

use mysqli;

$servername = "localhost";
$username = "root";
$password = "gabriel1234d";
$dbname = "w3";

define ('ITENS', [
    'string' => 's',
    'integer' => 'i',
    'float' => 'd',
    'double' => 'd',
    'boolean' => 'b'
]);

class Server  {

    private object $conn;
    private string $database;
    private string $table = '';
    private array $column = [];


    public function __construct(string $servername, string $username, string $password, string $dbname) {
        $this->conn = new mysqli($servername, $username, $password, $dbname);
        $this->database = $dbname;
    }

    public function getConn() {
        var_dump($this->conn);
    }

    public function setTable(string $table) {
        $this->table = $table;
    }

    public function getTable() {
        return $this->table;
    }
    
    public function setColumn(array $columns, string $table = '') {
        if($table != '') {
            $this->setTable($table);
        }
        if($this->findTable($columns, $table)) {
            foreach ($columns as $key => $value) {
                $this->column[] = $value;
            }
            return true;
        }
        else {
            $this->closeConn();
            die ('Please Set Table or Other column, result of tables is: ' . count($columns));
            return false;
        }
    }


    public function getColumn () {
        return $this->column;
    }

    public function insert(array $itens, bool $activeSetColumns = false) {
        if($this->table != "") {
            $columnNames = array();
            if($activeSetColumns) {
                if(count($this->column) > 0 && count($this->column) == count($itens)) {
                    foreach ($this->column as $key => $value) {
                        $columnNames[] = $value;
                    }
                }
                else {
                    die ('Columns enabled, but dont have a single column saved, or you have passed more parameters');
                    return false; 
                }
                
            }
            else {
                $sql = "SHOW COLUMNS FROM " . $this->table;
                $result = $this->conn->query($sql);
                if($result->num_rows > 0) {
                    $row = $result->fetch_row();
                    while ($row = $result->fetch_row()) {
                        $columnNames[] = $row[0];
                    }
                }
                if(count($columnNames) != count($itens)) {
                    die ('Columns denabled, you have passed a number of differente parameters');
                    return false;
                }
            }

            $sql = "INSERT INTO " . $this->table . "(";
            foreach ($columnNames as $key => $value) {
                if(empty($columnNames[$key+1])) {
                    $sql .= "$value) ";
                }
                else {
                    $sql .= "$value,";
                }
            }
            $sql .= "VALUES(";
            for ($i=0; $i < count($columnNames); $i++) { 
                if($i+1 == count($columnNames)) {
                    $sql .= " ? )";
                }
                else {
                    $sql .= " ?,";
                }
            }
            echo $sql;
            $stmt = $this->conn->prepare($sql);
            $param = '';
            foreach ($itens as $item):
                $param .= ITENS[gettype($item)];
            endforeach;
            $stmt->bind_param($param, ...$itens);
            if($stmt->execute()) {
                echo 'Insert Success';
            }
        }
        else {
            die('Please set Table Before');
            return false;
        }
    }

    public function update(array $itensToUpdate, array $ItensToWhere = null) {
        $table = $this->table;
        if($table != "") {
            $sql = "UPDATE $table SET";
            if($ItensToWhere == null) {
                $aux = 0;
            }
            else {
                $aux = count($ItensToWhere);
            }
            if(count($itensToUpdate)+$aux == count($this->column)) {
                for($i = 0; $i < count($itensToUpdate); $i++) {
                    if($i+1 == count($itensToUpdate)) {
                        $sql .= " " . $this->column[$i] . " = ?";
                    }
                    else {
                        $sql .= " " . $this->column[$i] . " = ?,";
                    }
                }
                if($ItensToWhere != null) {
                    $sql .= " WHERE";
                    for ($j=0; $j < count($ItensToWhere); $j++) { 
                        if($j+1 == count($ItensToWhere)) {
                            $sql .= " " . $this->column[$i+$j] . " = ?";
                        }
                        else {
                            $sql .= " " . $this->column[$i+$j] . " = ?,";
                        }
                    }
                }
                $stmt = $this->conn->prepare($sql);
                $param = '';
                foreach ($itensToUpdate as $item):
                    $param .= ITENS[gettype($item)];
                endforeach;
                foreach ($ItensToWhere as $item):
                    $param .= ITENS[gettype($item)];
                endforeach;
                $stmt->bind_param($param, ...$itensToUpdate, ...$ItensToWhere);
                if($stmt->execute()) {
                    echo 'Uptade Success';
                }
            }
            else {
                $this->closeConn();
                die('Have more itens to update than columns set.');
            }
        }
        else {
            $this->closeConn();
            die('Table not set, please set before!');
        }
    }

    public function query(string $str) {
        if($this->conn->query($str)) {
            return true;
        }
        else {
            return false;
        }
    }

    public function closeConn() {
        if($this->checkConn($this->conn)) {
            if($this->conn->close()) {
                echo 'Close Success<br>';
            }
            else {
                echo 'Already Closed<br>';
            };
        }
    }

    private function checkConn(object $conn) {
        if(empty($conn)) {
            echo "Not connected";
            return false;
        }
        else {
            return true;
        }
    }

    private function findTable(array $columns, string $table = '') {
        $auxBoll = true;
        if($table == '') {
            $sql = "SHOW TABLES FROM " . $this->database;
            $result = $this->conn->query($sql);
            if($result->num_rows > 0) {
                while ($row = $result->fetch_row()) {
                    $sqlTwo = "SHOW COLUMNS FROM " . $row[0];
                    $resultTwo = $this->conn->query($sqlTwo);
                    if($resultTwo->num_rows > 0) {
                        if(isset($columnsName))
                            unset($columnsName);
                        $columnsName = array();
                        while ($rowTwo = $resultTwo->fetch_row()) {
                            $columnsName[] = $rowTwo[0];
                        }
                        $result1 = array_diff($columnsName, $columns);
                        $result2 = array_diff($columns, $columnsName);
                        if(count($result1) >= 0 && count($result2) == 0 && $auxBoll) {  
                            $this->setTable($row[0]);                
                            $auxBoll = false;
                        }
                        else if(count($result1) >= 0 && count($result2) == 0 && !$auxBoll) {
                            $this->closeConn();
                            die ("Plase set more columns or set the table before");
                            return false;
                        }
                    }
                }
            }
        }
        else {
            $sql = "SHOW COLUMNS FROM $table";
            $result = $this->conn->query($sql);
            $columnsName = array();
            if($result->num_rows > 0) {
                while($row = $result->fetch_row()) {
                    $columnsName[] = $row[0];
                }
                $result1 = array_diff($columnsName, $columns);
                $result2 = array_diff($columns, $columnsName);
                if(count($result1) >= 0 && count($result2) == 0 && $auxBoll) {
                    $this->setTable($table);
                    $auxBoll = false;
                }
                else {
                    $this->closeConn();
                    die("Does Not exist columns with this names on table: $table");
                    return false;
                }
            }
        }
        return !$auxBoll;
    }
}


$objTester = new Server($servername, $username, $password, $dbname);


?>