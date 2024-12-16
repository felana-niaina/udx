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

}
?>