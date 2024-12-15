<?php

// Classe AnnoncesResultsProvider
class MarketplaceResultsProvider {
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

    // Méthode pour obtenir le nombre de résultats
    public function getNumResults($term) {
        try {
            $query = $this->con->prepare("SELECT COUNT(*) as total 
                                          FROM marketplace
                                          WHERE title LIKE :term
                                          OR description LIKE :term
                                          OR keywords LIKE :term");

            $searchTerm = "%" . $term . "%";
            $query->bindParam(":term", $searchTerm);
            $query->execute();

            $row = $query->fetch(PDO::FETCH_ASSOC);

            return $row ? $row["total"] : 0;

        } catch (PDOException $e) {
            echo "Erreur dans la requête : " . $e->getMessage();
            return 0;
        }
    }

    // Méthode pour obtenir les résultats sous forme de HTML
    public function getResultsHtml($page, $pageSize, $term) {
        try {
            if (!$this->con) {
                throw new Exception("La connexion à la base de données a échoué.");
            }

            $fromLimit = ($page - 1) * $pageSize;

            $query = $this->con->prepare("SELECT * 
                                         FROM marketplace 
                                         WHERE title LIKE :term 
                                         OR description LIKE :term 
                                         OR keywords LIKE :term
                                         ORDER BY clicks DESC
                                         LIMIT :fromLimit, :pageSize");

            $searchTerm = "%" . $term . "%";
            $query->bindParam(":term", $searchTerm);
            $query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
            $query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
            $query->execute();

            $resultsHtml = "<div class='siteResults'>";

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $id = $row["id"];
                $title = $row["title"];
                $description = $row["description"];
                $price = $row["price"];

                // Truncation des champs title et description si nécessaire
                $title = $this->trimField($title, 120);
                $description = $this->trimField($description, 230);

                // Ajouter le résultat au HTML
				$resultsHtml .= "<div class='resultContainer'>
                    <h3 class='title'>
                        $title &nbsp;<a href='#' class='see-market-image' onClick='getProductPictures($id)'>
                            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-image' viewBox='0 0 16 16'>
                            <path d='M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0'/>
                            <path d='M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1z'/>
                            </a>
                            &nbsp;
                            <a href='#' class='contact-product-owner' id='contactProductOwner($id)'>
                            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-envelope-at' viewBox='0 0 16 16'>
                            <path d='M2 2a2 2 0 0 0-2 2v8.01A2 2 0 0 0 2 14h5.5a.5.5 0 0 0 0-1H2a1 1 0 0 1-.966-.741l5.64-3.471L8 9.583l7-4.2V8.5a.5.5 0 0 0 1 0V4a2 2 0 0 0-2-2zm3.708 6.208L1 11.105V5.383zM1 4.217V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v.217l-7 4.2z'/>
                            <path d='M14.247 14.269c1.01 0 1.587-.857 1.587-2.025v-.21C15.834 10.43 14.64 9 12.52 9h-.035C10.42 9 9 10.36 9 12.432v.214C9 14.82 10.438 16 12.358 16h.044c.594 0 1.018-.074 1.237-.175v-.73c-.245.11-.673.18-1.18.18h-.044c-1.334 0-2.571-.788-2.571-2.655v-.157c0-1.657 1.058-2.724 2.64-2.724h.04c1.535 0 2.484 1.05 2.484 2.326v.118c0 .975-.324 1.39-.639 1.39-.232 0-.41-.148-.41-.42v-2.19h-.906v.569h-.03c-.084-.298-.368-.63-.954-.63-.778 0-1.259.555-1.259 1.4v.528c0 .892.49 1.434 1.26 1.434.471 0 .896-.227 1.014-.643h.043c.118.42.617.648 1.12.648m-2.453-1.588v-.227c0-.546.227-.791.573-.791.297 0 .572.192.572.708v.367c0 .573-.253.744-.564.744-.354 0-.581-.215-.581-.8Z'/>
                            </a>
                        </svg>
                    </h3>
                    <span class='description'>$description</span>
                    <span class='price'>$price €</span>
                  </div>";
            }

            $resultsHtml .= "</div>"; // Fermer la div des résultats

            return $resultsHtml;

        } catch (PDOException $e) {
            echo "Erreur de requête : " . $e->getMessage();
            return "<p>Une erreur est survenue lors de l'affichage des résultats.</p>";
        } catch (Exception $e) {
            echo $e->getMessage();
            return "<p>Une erreur est survenue lors de l'affichage des résultats.</p>";
        }
    }

    // Méthode pour tronquer les champs de texte
    private function trimField($string, $characterLimit) {
        $dots = strlen($string) > $characterLimit ? "..." : "";
        return substr($string, 0, $characterLimit) . $dots;
    }

    public function getProductsByUser($userId) {
        try {
            $query = $this->con->prepare("SELECT * 
                                          FROM marketplace
                                          WHERE userId = :userId
                                        ");
            $query->execute([
                'userId' => $userId
            ]);

            $resultsHtml = "<div class='row'>";
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $id = $row["id"];
                $title = $row["title"];
                $description = $row["description"];
                $price = $row["price"] . " €";
                $image = "https://via.placeholder.com/150";
                $date = $row["createdDate"];

                // Truncation des champs title et description si nécessaire
                $title = $this->trimField($title, 120);
                $description = $this->trimField($description, 230);

                // Ajouter le résultat au HTML
				$resultsHtml .= "<div class='col-md-6'>
                <div class='product-item' data-toggle='modal' data-target='#productModal' data-title='$title' data-description='$description.' data-price='$price' data-image='$image' data-date='$date' data-id='$id'>
                    <img src='$image' alt='$title' class='product-image'>
                    <h5 class='product-title'>$title</h5>
                    <p class='product-price'>$price</p>
                    <p class='product-description'>$description</p>
                    <div class='product-actions'>
                        <a href='#'>Modifier</a> | <a href='#'>Supprimer</a>
                    </div>
                </div>
              </div>";
            }
            $resultsHtml .= "</div>"; // Fermer la div des résultats

            return $resultsHtml;

        } catch (PDOException $e) {
            echo "Erreur dans la requête : " . $e->getMessage();
            return 0;
        }
    }

    public function createProduct($userId, $name, $description, $price, $tags){
        try {
            $sql = "INSERT INTO marketplace (title, description, price, keywords, userId) VALUES (:title, :description, :price, :keywords, :userId)";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':title', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':keywords', $tags);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            $productId = $this->con->lastInsertId();
            if ($productId) {
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