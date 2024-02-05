<?php

namespace SimpleTools;

class Database
{
    private $DatabaseName = "<DATABASE_NAME>";
    public $conn;

    function connect()
    {
        try {
            // Create (connect to) SQLite database in file
            $this->conn = new PDO('sqlite:' . __DIR__ . $this->DatabaseName);
            // Set errormode to exceptions
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            return "Error while connect to database: " . $e->getMessage();
        }
    }

    function disconnect()
    {
        $this->conn = null;
    }

    function login($arr, $table)
    {
        $counter = 1;
        $arrLen = count($arr);
        $sql = "SELECT * FROM $table WHERE";
        foreach ($arr as $key => $value) {
            $sql .= " $key=:$key ";
            if ($counter != $arrLen) {
                $sql .= "AND";
            }
            $counter++;
        }
        $this->connect();
        $stmt = $this->conn->prepare($sql);
        foreach ($arr as $key => $value) {
            $stmt->bindParam(":$key", $arr[$key]);
        }
        $stmt->execute();
        $this->disconnect();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user !== false) {
            return $user;
        }
        return false;
    }

    function delete($id, $table, $columnIdName)
    {
        $this->connect();
        $stmt = $this->conn->prepare("DELETE FROM {$table} WHERE {$columnIdName}=:id");
        $stmt->bindParam(":id", $id);
        if ($stmt->execute()) {
            $this->disconnect();
            return "deleted";
        } else {
            $this->disconnect();
            return "Cannot delete";
        }
    }

    function insert($table, $data)
    {
        $sql = "INSERT INTO " . $table . " (";
        $temp = 0;
        foreach ($data as $key => $value) {
            $temp++;
            $sql .= $key;
            if ($temp <= sizeof($data) - 1) {
                $sql .= ",";
            }
        }
        $sql .= ") VALUES(";
        $temp = 0;
        foreach ($data as $key => $value) {
            $temp++;
            $sql .= "'" . $value . "'";
            if ($temp <= sizeof($data) - 1) {
                $sql .= ",";
            }
        }
        $sql .= ")";

        $this->connect();
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $LastId = $this->conn->lastInsertId();
        $this->disconnect();

        return $LastId;
    }

    function update($table, $data, $conditionId, $columnIdName)
    {
        $sql = "UPDATE " . $table . " SET ";
        $temp = 0;
        foreach ($data as $key => $value) {
            $temp++;
            $sql .= $key . "= '" . $value . "'";
            if ($temp <= sizeof($data) - 1) {
                $sql .= ",";
            }
        }

        $sql .= " WHERE " . $columnIdName . "=" . $conditionId;

        $this->connect();
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $this->disconnect();
    }

    function getRow($table, $columnIdName, $id)
    {
        $this->connect();
        $stmt = $this->conn->prepare("SELECT * FROM $table WHERE $columnIdName=:id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $this->disconnect();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    function getAll($table)
    {
        $this->connect();
        $stmt = $this->conn->prepare("SELECT * FROM $table");
        $stmt->execute();
        $this->disconnect();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    function query($sql)
    {
        $this->connect();
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $this->disconnect();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    function usernameExist($table, $colunm, $username)
    {
        $this->connect();
        $stmt = $this->conn->prepare("SELECT * FROM $table WHERE $colunm=:username");
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $this->disconnect();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if (is_null($res)) {
            return false;
        } else {
            return true;
        }
    }
}
