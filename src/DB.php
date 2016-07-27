<?php
namespace SlackBot;

class DB {
    private $db;

    public function __construct() {
        $this->db = new \mysqli(getenv('MYSQL_HOST'), getenv('MYSQL_USER'), getenv('MYSQL_PASS'), getenv('MYSQL_BASE'));

        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error . "\r\n");
        }
    }

    public function query($table, $where = array()) {
        $query = "SELECT * FROM " . $table;
        if (!empty($where)) {
            $i = 0;
            $query .= " WHERE ";
            foreach($where as $col => $input) {
                $split = explode(':', $input);
                $type = $split[0];
                $value = $split[1];

                $query .= $col . " = ";
                if ($type == "int" || $type == "timestamp") {
                    $query .= $value;
                } else {
                    $query .= "'" . $value . "'";
                }
                $i++;
                if ($i < count($where)) {
                    $query .= " AND ";
                }
            }
        }

        $results = [];
        $result = $this->db->query($query);
        if ($result->num_rows >= 1) {
            while ($row = $result->fetch_array()) {
                $results[] = $row;
            }

            return $results;
        } else {
            return null;
        }
    }

    public function insert($table, $cols = array(), $values = array()) {
        $query = "INSERT INTO " . $table;
        if (!empty($cols) && !empty($values) && count($cols) == count($values)) {
            $query .= " (";
            for($i = 0; $i < count($cols); $i++) {
                $query .= $cols[$i];
                if ($i != count($cols) - 1) {
                    $query .= ", ";
                }
            }
            $query .= ") VALUES (";
            $j = 0;
            foreach($values as $type => $value) {
                if ($type == "int" || $type == "timestamp") {
                    $query .= $value;
                } else {
                    $value = addslashes($this->db->real_escape_string($value));
                    $query .= "'" . $value . "'";
                }
                $j++;
                if ($j != count($values)) {
                    $query .= ", ";
                }
            }
            $query .= ")";

            return $this->db->query($query);
        } else {
            return null;
        }
    }

    public function update($table, $values = array(), $where = array()) {
        $query = "UPDATE " . $table . " SET ";
        if (!empty($values) && !empty($where)) {
            $i = 0;
            foreach($values as $col => $input) {
                $split = explode(':', $input);
                $type = $split[0];
                $value = $split[1];

                $query .= $col . " = ";
                if ($type == "int" || $type == "timestamp") {
                    $query .= $value;
                } else {
                    $query .= "'" . $value . "'";
                }
                $i++;
                if ($i < count($values)) {
                    $query .= ", ";
                }
            }

            $query .= " WHERE ";

            $j = 0;
            foreach($where as $col => $input) {
                $split = explode(':', $input);
                $type = $split[0];
                $value = $split[1];

                $query .= $col . " = ";
                if ($type == "int" || $type == "timestamp") {
                    $query .= $value;
                } else {
                    $query .= "'" . $value . "'";
                }
                $j++;
                if ($j < count($where)) {
                    $query .= " AND ";
                }
            }
            /*for($i = 0; $i < count($cols); $i++) {
                $query .= $cols[$i];
                if ($i != count($cols) - 1) {
                    $query .= ", ";
                }
            }
            $query .= "";
            $j = 0;
            foreach($values as $type => $value) {
                if ($type == "int" || $type == "timestamp") {
                    $query .= $value;
                } else {
                    $value = addslashes($this->db->real_escape_string($value));
                    $query .= "'" . $value . "'";
                }
                $j++;
                if ($j != count($values)) {
                    $query .= ", ";
                }
            }
            $query .= ")";*/

            return $this->db->query($query);
        } else {
            return null;
        }
    }

    public function error() {
        return $this->db->error;
    }
}