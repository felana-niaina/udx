<?php

// Classe BillingResultsProvider
class BillingResultsProvider {
    private $con;

    // Le constructeur attend une connexion PDO avec une base de données
    public function __construct($con) {
        if (!$con) {
            throw new Exception("La connexion à la base de données a échoué.");
        }

        $dbName = $con->query("SELECT DATABASE()")->fetchColumn();
        if (!$dbName) {
            throw new Exception("Aucune base de données sélectionnée.");
        }

        $this->con = $con;
    }

    public function getBillingMethodByUser($userId){
        try {
            $query = $this->con->prepare("SELECT * 
                                          FROM billing
                                          WHERE userId = :userId
                                        ");
            $query->bindParam(':userId', $userId);                 
            $query->execute();
            return $query->fetchAll();

        } catch (PDOException $e) {
            echo "Erreur dans la requête : " . $e->getMessage();
            return 0;
        }
    }

    public function createBillingMethohd($userId, $cardHolder, $cardNumber, $expirationDate, $cryptoVisuel){
        try {
            $sql = "INSERT INTO billing (cardHolder, cardNumber, expirationDate, cryptoVisuel, userId) VALUES (:cardHolder, :cardNumber, :expirationDate, :cryptoVisuel, :userId)";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':cardHolder', $cardHolder);
            $stmt->bindParam(':cardNumber', $cardNumber);
            $stmt->bindParam(':expirationDate', $expirationDate);
            $stmt->bindParam(':cryptoVisuel', $cryptoVisuel);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            $billingId = $this->con->lastInsertId();
            if ($billingId) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            echo "Erreur lors de l'enregistrement : " . $e->getMessage();
            return false;
        }
    }

}
?>