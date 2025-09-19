<?php

class Database {
    private $connection;

    public function __construct($host = 'localhost', $user = 'root', $password = '', $database = 'saracha') {
        $this->connect($host, $user, $password, $database);
    }

    private function connect($host, $user, $password, $database) {
        $this->connection = new mysqli($host, $user, $password, $database);

        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    public function executeQuery($query) {
        $result = $this->connection->query($query);

        if (!$result) {
            die("Query failed: " . $this->connection->error);
        }

        return $result;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function closeConnection() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}