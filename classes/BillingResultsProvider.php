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
            $query->execute([
                'userId' => $userId
            ]);

            /* $resultsHtml = "<div class='list-group'>";
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $id = $row["id"];
                $title = $row["title"];
                $description = $row["description"];
                $keywords = $row["keywords"];
                $date = $row["createdDate"];

                // Truncation des champs title et description si nécessaire
                $title = $this->trimField($title, 120);
                $description = $this->trimField($description, 230);

                // Ajouter le résultat au HTML
				$resultsHtml .= "
                <div class='list-group-item post-item' data-toggle='modal' data-target='#postModal' data-title='$title' data-body='$description' data-keyword='$keywords' data-date='$date' data-id='$id'>
                    <h5 class='post-title'>$title</h5>
                    <p class='post-date'>$date</p>
                    <p class='post-body'>$description</p>
                    <div class='post-actions'>
                        <a href='#'>Edit</a> | <a href='#'>Delete</a>
                    </div>
                </div>";
            }
            $resultsHtml .= "</div>"; // Fermer la div des résultats

            return $resultsHtml; */
            return false;

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