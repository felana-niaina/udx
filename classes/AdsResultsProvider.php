<?php

// Classe AdsResultsProvider
class AdsResultsProvider {
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

    // get all ads type
    public function getAdsType(){
        try {
            $query = $this->con->prepare("SELECT * FROM adstype");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des catégories : " . $e->getMessage();
            return [];
        }
    }

    public function addAds($userId, $adType, $adContent, $budget, $adId){
        try {
            $ad = $this->getUserAd($userId);
            if(!is_null($ad) && $ad->id == $adId) {
                // update
                $sql = "UPDATE ads 
                    SET adsTypeId = :adsTypeId, contentId = :contentId, budget = :budget 
                    WHERE id = :adId";
                $stmt = $this->con->prepare($sql);

                $stmt->bindParam(':adsTypeId', $adType);
                $stmt->bindParam(':contentId', $adContent);
                $stmt->bindParam(':budget', $budget);
                $stmt->bindParam(':adId', $ad->id);

                // Exécuter la requête d'update
                $stmt->execute();
                return true;
            }
            $sql = "INSERT INTO ads (adsTypeId, contentId, budget, userId) VALUES (:adsTypeId, :contentId, :budget, :userId)";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':adsTypeId', $adType);
            $stmt->bindParam(':contentId', $adContent);
            $stmt->bindParam(':budget', $budget);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            $AdId = $this->con->lastInsertId();
            if ($AdId) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            echo "Erreur lors de l'enregistrement : " . $e->getMessage();
            return false;
        }
    }

    public function getUserAd($userId) {
        try {
            $sql = "SELECT * FROM ads WHERE userId = :userId";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            
            $ad = $stmt->fetchObject();
            
            if ($ad) {
                return $ad;
            } else {
                return null;  // L'utilisateur n'a pas de pub
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des données utilisateur : " . $e->getMessage();
            return null;
        }
    }

    public function getAdType($adType) {
        try {
            $query = $this->con->prepare("SELECT * FROM adstype WHERE id = :adType");
            $query->bindParam(':adType', $adType);
            $query->execute();
            return $query->fetchObject();
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des catégories : " . $e->getMessage();
            return [];
        }
    }

    function resetAdParam($userId, $AdId) {
        try {
            $sql = "DELETE from ads 
                    WHERE id = :AdId AND userId = :userId ";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':AdId', $AdId);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            header("Location: settings.php");
            exit();
        } catch (PDOException $e) {
            echo "Erreur lors de la réinitialisation du profil : " . $e->getMessage();
        }
    }

}
?>