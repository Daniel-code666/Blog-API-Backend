<?php

    class connection{
        private $host = "localhost";
        private $dbname = "blogapi";
        private $user = "root";
        private $password = "1234";
        private $conn;

        public function dbConnect(){
            $this->conn = null;

            try{
                $this->conn = new PDO('mysql:host='. $this->host.';dbname='. $this->dbname, 
                    $this->user, $this->password);
            }catch(Exception $ex){
                echo "Error: " . $ex->getMessage();
            }

            return $this->conn;
        }

    }