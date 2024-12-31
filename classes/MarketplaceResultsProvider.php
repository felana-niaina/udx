<?php

// Classe AnnoncesResultsProvider
class MarketplaceResultsProvider {
    private $con;
    private $siteUrl;

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
        $this->siteUrl = 'http://'.$_SERVER['SERVER_NAME'] ;
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
            // Vérifier si l'utilisateur est connecté via la session
            $isUserConnected = !empty($_SESSION);

            $fromLimit = ($page - 1) * $pageSize;

            $query = $this->con->prepare("SELECT marketplace.*, users.profile_photo, users.username
                                         FROM marketplace
                                         INNER JOIN users ON marketplace.userId = users.id
                                         WHERE marketplace.title LIKE :term 
                                         OR marketplace.description LIKE :term 
                                         OR marketplace.keywords LIKE :term
                                         ORDER BY marketplace.clicks, isFeatured DESC
                                         LIMIT :fromLimit, :pageSize");

            $searchTerm = "%" . $term . "%";
            $query->bindParam(":term", $searchTerm);
            $query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
            $query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
            $query->execute();

            $resultsHtml = "<div class='siteResults'>";

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $id = $row["id"];
                $userId = $row["userId"]; 
                $profilePicture = $row["profile_photo"] ?: "https://via.placeholder.com/150";
                $username = $row["username"];
                $title = $row["title"];
                $picture = $row["picture"];
                $description = $row["description"];
                $price = $row["price"];
                $isFeatured = $row["isFeatured"];
                $profilePicture = $row["profile_photo"] ?: "https://via.placeholder.com/150";

                // Truncation des champs title et description si nécessaire
                $title = $this->trimField($title, 120);
                $description = $this->trimField($description, 230);

                // Ajouter le résultat au HTML
				$resultsHtml .= " <div class='d-flex mb-12 marketResult'>
                    <a href='$this->siteUrl/udx/profil.php?userId=$userId'><img src='$profilePicture' class='profile-photo'></a>
                    <div class='text'>
                        <div>
                            <h6 class='price'>$username</h6>
                        </div>
                        <div class='d-flex'>
                            <h4 class='title'>$title</h4>";
                    if($isFeatured) {
                        $resultsHtml .= "<div><span class='badge badge-success'>Pub</span></div>";
                    }

                    if($isUserConnected) {
                        $resultsHtml .=             "<button class='btn see-market-image' data-bs-toggle='modal' data-bs-target='#seePictureModal' data-picture='$this->siteUrl/udx/$picture' data-id=`$id`>
                                <i class='bi bi-images'></i>
                            </button>
                            <button class='btn contact-product-owner' data-bs-toggle='modal' data-bs-target='#contactModal' data-user-id='$userId' data-id=`$id`>
                                <i class='bi bi-chat-dots'></i>
                            </button>";
                    } else {
                        $resultsHtml .= "<span class='price connexionInfo'>(Connectez-vous pour voir l' offre)</span>";
                    }

                
                    $resultsHtml .=     "</div>
                        <div>
                            <span class='description'>$description</span><br/>
                            <span class='price'>$price €</span>
                        </div>
                    </div>
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
            $siteUrl = 'http://'.$_SERVER['SERVER_NAME'] .'/udx/';

            $resultsHtml = "<div class='row'>";
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $id = $row["id"];
                $title = $row["title"];
                $description = $row["description"];
                $keywords = $row["keywords"];
                $price = $row["price"] . " €";
                $image = is_null($row['picture']) || $row['picture'] == '' ? "https://via.placeholder.com/150" : $siteUrl. $row['picture'];
                $date = $row["createdDate"];

                // Truncation des champs title et description si nécessaire
                $title = $this->trimField($title, 120);
                $description = $this->trimField($description, 230);

                // Ajouter le résultat au HTML
				$resultsHtml .= "<div class='col-md-6'>
                <div class='product-item'>
                    <img src='$image' alt='$title' class='product-image'>
                    <h5 class='product-title'>$title</h5>
                    <p class='product-price'>$price</p>
                    <p class='product-description'>$description</p>
                    <div class='product-actions' id='actionProduct-$id'>
                        <a href='#' class='editProduct' data-toggle='modal' data-target='#productModal' data-title='$title' data-description='$description.' data-price='$price' data-keywords='$keywords' data-image='$image' data-date='$date' data-id='$id'>Modifier</a> | <a href='#' class='removeProductButton' data-toggle='modal' data-target='#removeProductModal' data-title='$title' data-id='$id'>Supprimer</a>
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

    public function createProduct($userId, $name, $description, $price, $tags, $picture = NULL){
        try {
            $targetFile = '';
            if(!is_null($picture)) {
                if ($picture['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception("Erreur lors du téléchargement du fichier.");
                }
                $newImageName = time() . '_' . uniqid() . '.' . pathinfo($picture['name'], PATHINFO_EXTENSION);
    
                // Déplacer le fichier téléchargé dans le répertoire voulu
                $uploadDir = 'uploads/';
                $targetFile = $uploadDir . $newImageName;
                
                if (!move_uploaded_file($picture['tmp_name'], $targetFile)) {
                    throw new Exception("Impossible de déplacer le fichier téléchargé.");
                }
            }

            $sql = "INSERT INTO marketplace (title, description, price, keywords, picture ,userId) VALUES (:title, :description, :price, :keywords, :picture, :userId)";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':title', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':keywords', $tags);
            $stmt->bindParam(':picture', $targetFile);
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

    public function updateProduct($productId, $userId, $name, $description, $price, $tags, $picture) {
        try {
            $targetFile = '';
    
            // Gérer l'upload de la nouvelle image si elle est sous forme de tableau ($_FILES)
            if (is_array($picture) && isset($picture['tmp_name']) && !empty($picture['tmp_name'])) {
                if ($picture['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception("Erreur lors du téléchargement du fichier.");
                }
                $newImageName = time() . '_' . uniqid() . '.' . pathinfo($picture['name'], PATHINFO_EXTENSION);
    
                // Déplacer le fichier téléchargé dans le répertoire voulu
                $uploadDir = 'uploads/';
                $targetFile = $uploadDir . $newImageName;
    
                if (!move_uploaded_file($picture['tmp_name'], $targetFile)) {
                    throw new Exception("Impossible de déplacer le fichier téléchargé.");
                }
    
                // Supprimer l'ancienne image si elle existe
                $sql = "SELECT picture FROM marketplace WHERE id = :productId";
                $stmt = $this->con->prepare($sql);
                $stmt->bindParam(':productId', $productId);
                $stmt->execute();
                $oldPicture = $stmt->fetchColumn();
    
                if ($oldPicture && file_exists($oldPicture)) {
                    unlink($oldPicture);
                }
            } else {
                // Utiliser l'ancienne image si aucun fichier n'a été uploadé
                $targetFile = $picture;
            }
    
            // Construire la requête SQL
            $sql = "
                UPDATE marketplace 
                SET 
                    title = :title, 
                    description = :description, 
                    price = :price, 
                    keywords = :keywords,
                    userId = :userId" .
                    (!empty($targetFile) ? ", picture = :picture" : "") . 
                " WHERE id = :productId";
    
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':title', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':keywords', $tags);
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':productId', $productId);
    
            // Ajouter l'image à la requête seulement si elle a été modifiée
            if (!empty($targetFile)) {
                $stmt->bindParam(':picture', $targetFile);
            }
    
            $stmt->execute();
    
            return true;
    
        } catch (Exception $e) {
            echo "Erreur lors de la mise à jour : " . $e->getMessage();
            return false;
        }
    }
    
    
    public function removeProduct($userId, $productId) {
        try {
            $sql = "DELETE from marketplace 
                    WHERE id = :productId AND userId = :userId ";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':productId', $productId);
            $stmt->bindParam(':userId', $userId);

            // Exécuter la requête de suppression
            $stmt->execute();
            // Vérifier si des lignes ont été mises à jour
            if ($stmt->rowCount() > 0) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            echo "Erreur lors de la suppression du compte : " . $e->getMessage();
        }
    }

    public function getProductList($userId) {
        try {
            $query = $this->con->prepare("SELECT * 
                                          FROM marketplace
                                          WHERE userId = :userId
                                        ");
            $query->execute([
                'userId' => $userId
            ]);
            return $query->fetchAll();

        } catch (PDOException $e) {
            echo "Erreur dans la requête : " . $e->getMessage();
            return 0;
        }
    }

    public function getProductById($productId) {
        try {
            $query = $this->con->prepare("SELECT * 
                                          FROM marketplace
                                          WHERE id = :productId
                                        ");
            $query->execute([
                'productId' => $productId
            ]);
            return $query->fetchObject();

        } catch (PDOException $e) {
            echo "Erreur dans la requête : " . $e->getMessage();
            return 0;
        }
    }

    public function updateProductFeatured($userId, $productId, $isFeatured){
        try {
            $sql = "UPDATE marketplace 
                    SET isFeatured = :isFeatured
                    WHERE id = :productId AND userId = :userId";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':isFeatured', $isFeatured);
            $stmt->bindParam(':productId', $productId);
            $stmt->bindParam(':userId', $userId);

            // Exécuter la requête d'update
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "Erreur lors de la mise à jour du produit : " . $e->getMessage();
            return false;
        }
    }
}
?>