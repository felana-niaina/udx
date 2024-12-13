<?php

// Classe SiteResultsProvider
class SiteResultsProvider {
    private $con;

    // Le constructeur attend une connexion PDO avec une base de données
    public function __construct($con) {
        // Vérifiez que la connexion est bien initialisée
        if (!$con) {
            throw new Exception("La connexion à la base de données a échoué.");
        }

        // Vérifiez si une base de données est bien sélectionnée
        $dbName = $con->query("SELECT DATABASE()")->fetchColumn();
        if (!$dbName) {
            throw new Exception("Aucune base de données sélectionnée.");
        }

        $this->con = $con;
    }

    // Méthode pour obtenir le nombre de résultats
    public function getNumResults($term) {
        try {
            // Préparer la requête pour obtenir le nombre de résultats correspondant au terme de recherche
            $query = $this->con->prepare("SELECT COUNT(*) as total 
                                          FROM sites 
                                          WHERE title LIKE :term
                                          OR url LIKE :term
                                          OR description LIKE :term
                                          OR keywords LIKE :term");

            // Préparer le terme de recherche en ajoutant des pourcentages pour la recherche partielle
            $searchTerm = "%" . $term . "%";
            $query->bindParam(":term", $searchTerm);
            $query->execute();

            // Récupérer la première ligne
            $row = $query->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                return $row["total"];
            } else {
                return 0; // Aucun résultat
            }

        } catch (PDOException $e) {
            // Gestion des erreurs PDO
            echo "Erreur dans la requête : " . $e->getMessage();
            return 0; // Retourner 0 en cas d'erreur
        }
    }

    // Méthode pour obtenir les résultats sous forme de HTML
    public function getResultsHtml($page, $pageSize, $term) {
        try {
            // Vérification si la connexion PDO fonctionne
            if (!$this->con) {
                throw new Exception("La connexion à la base de données a échoué.");
            }

            // Calcul de la limite de la pagination
            $fromLimit = ($page - 1) * $pageSize;

            // Préparer la requête pour récupérer les résultats de recherche
            $query = $this->con->prepare("SELECT * 
                                         FROM sites 
                                         WHERE title LIKE :term 
                                         OR url LIKE :term 
                                         OR keywords LIKE :term 
                                         OR description LIKE :term
                                         ORDER BY clicks DESC
                                         LIMIT :fromLimit, :pageSize");

            // Préparer le terme de recherche en ajoutant des pourcentages pour la recherche partielle
            $searchTerm = "%" . $term . "%";
            $query->bindParam(":term", $searchTerm);
            $query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
            $query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
            $query->execute();

            // Initialiser le code HTML des résultats
            $resultsHtml = "<div class='siteResults'>";

            // Parcourir les résultats et construire le HTML
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $id = $row["id"];
                $url = $row["url"];
                $title = $row["title"];
                $description = $row["description"];

                // Truncation des champs title et description si nécessaire
                $title = $this->trimField($title, 120);
                $description = $this->trimField($description, 230);

                // Ajouter le résultat au HTML
                $resultsHtml .= "<div class='resultContainer'>
                                    <h3 class='title'>
                                        <a class='result' href='$url' data-linkId='$id'>
                                            $title
                                        </a>
                                    </h3>
                                    <span class='url'>$url</span>
                                    <span class='description'>$description</span>
                                  </div>";
            }

            $resultsHtml .= "</div>"; // Fermer la div des résultats

            return $resultsHtml;

        } catch (PDOException $e) {
            // Affichage de l'erreur PDO
            echo "Erreur de requête : " . $e->getMessage();
            return "<p>Une erreur est survenue lors de l'affichage des résultats.</p>";
        } catch (Exception $e) {
            // Gestion d'autres erreurs génériques
            echo $e->getMessage();
            return "<p>Une erreur est survenue lors de l'affichage des résultats.</p>";
        }
    }

    // Méthode pour tronquer les champs de texte (par exemple, titre ou description)
    private function trimField($string, $characterLimit) {
        $dots = strlen($string) > $characterLimit ? "..." : "";
        return substr($string, 0, $characterLimit) . $dots;
    }
}
?>