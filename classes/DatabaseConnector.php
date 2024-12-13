<?php
class DatabaseConnector {
    private $dsn = "mysql:host=localhost;dbname=udx";
    private $username = "root";
    private $password = "";
    private $con;

    public function __construct() {
        try {
            $this->con = new PDO($this->dsn, $this->username, $this->password);
            $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Erreur de connexion : " . $e->getMessage();
            exit;
        }
    }

    public function getConnection() {
        return $this->con;
    }
}
?>
