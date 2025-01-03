<?php

// Classe AnnoncesResultsProvider
class PostsResultsProvider {
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
                                          FROM posts
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

    public function isLikedPost($likerId, $postId) {
        try {
            $sql = "SELECT * FROM likers WHERE likerId = :likerId AND postId = :postId";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':likerId', $likerId);
            $stmt->bindParam(':postId', $postId);
            $stmt->execute();
            
            // Si une ligne existe, l'utilisateur a aimé le post
            return $stmt->fetchColumn() !== false;

        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des données : " . $e->getMessage();
            return false;
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

            $userIdConnected = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;


            $fromLimit = ($page - 1) * $pageSize;

            $query = $this->con->prepare("SELECT posts.*, users.profile_photo, users.username
                                        FROM posts
                                        LEFT JOIN users ON posts.userId = users.id
                                        WHERE posts.title LIKE :term 
                                        OR posts.description LIKE :term 
                                        OR posts.keywords LIKE :term
                                        ORDER BY isFeatured, posts.clicks DESC
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
                $title = $row["title"];
                $description = $row["description"];
                $username = $row["username"];
                $profilePicture = $row["profile_photo"] ?: "https://via.placeholder.com/150";
                $commentList = $this->getPostComments($id);

                // Truncation des champs title et description si nécessaire
                $title = $this->trimField($title, 120);
                $description = $this->trimField($description, 230);
                $isFeatured = $row["isFeatured"];

                $likedClass = '';
                if ($isUserConnected) {
                   
                    $likedPost = $this->isLikedPost($userIdConnected, $id); // Vérifiez si l'utilisateur a aimé ce post
                    if ($likedPost) {
                        $likedClass = 'liked'; // Ajoutez une classe "liked" si l'utilisateur a aimé
                    }
                }


                // Vérifiez si l'utilisateur est connecté
                $profileLink ="$this->siteUrl/profil.php?id=$userId&name=" . urlencode($username);


                // Ajouter le résultat au HTML
                $resultsHtml .= "<div class='d-flex mb-3'>
                                    <a href='$profileLink'><img src='$profilePicture' class='profile-photo' /></a>
                                    <div class='text'>
                                        <span>$username</span>
                                        <div class='d-flex'>
                                            <h4 class='title'>
                                                    $title ";
                if($isFeatured) {
                    $resultsHtml .= "<span class='badge badge-success' id='success-badge'>Pub</span>";
                }                                   
                $resultsHtml .= "
                                            </h4>
                                             <!-- Icone Like -->
                                            <button class='btn like-btn $likedClass' 
                                                data-post-id='$id' 
                                                data-user-id='$userId'
                                                onclick='handleLikeClick(this)'
                                            >
                                                <i class='bi bi-hand-thumbs-up'></i>
                                            </button>

                                            <!-- Icone Commentaire -->
                                            <button class='btn'  onclick='toggleCommentArea(this, $id)'>
                                                <i class='bi bi-chat-dots'></i>
                                            </button>

                                        </div>
                                        <div>
                                            <span class='description'>$description</span>
                                        </div>
                                        
                                        <!-- Zone pour le commentaire -->
                                        <div class='comment-area' id='comment-area-$id'>
                                            $commentList ";
                if($isUserConnected) {
                    $resultsHtml .= "<textarea class='form-control' rows='2' placeholder='Écrire un commentaire...'></textarea>
                    <div class='mt-2'>
                        <button class='btn btn-secondary' onclick='cancelComment(this)'>Annuler</button>
                        <button class='btn btn-primary' onclick='sendComment(this, $id)'>Envoyer</button>
                    </div>";
                }else{
                    $resultsHtml .= "<span class='description'><strong>Connectez-vous pour pouvoir commenter ce post.</strong></span>";
                }
                                            
                $resultsHtml .=         "</div>
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

    public function getPostsByUser($userId){
        try {
            $query = $this->con->prepare("SELECT * 
                                          FROM posts
                                          WHERE userId = :userId
                                        ");
            $query->execute([
                'userId' => $userId
            ]);

            $resultsHtml = "<div class='list-group'>";
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $id = $row["id"];
                $title = $row["title"];
                $description = $row["description"];
                $keywords = $row["keywords"];
                $date = $row["createdDate"];
                $category = $row['categoryId'];

                // Truncation des champs title et description si nécessaire
                $title = $this->trimField($title, 120);
                $description = $this->trimField($description, 230);

                // Ajouter le résultat au HTML
				$resultsHtml .= "
                <div class='list-group-item post-item' >
                    <h5 class='post-title'>$title</h5>
                    <p class='post-date'>$date</p>
                    <p class='post-body'>$description</p>
                    <div class='post-actions'>
                        <a href='#' class='updatePostButton' data-toggle='modal' data-target='#postModal' data-title='$title' data-body='$description' data-keyword='$keywords' data-date='$date' data-id='$id' data-category='$category'>Edit</a> | <a href='#' class='removePostButton' data-toggle='modal' data-target='#removePostModal' data-title='$title' data-id='$id'>Delete</a>
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

    // Méthode pour tronquer les champs de texte
    private function trimField($string, $characterLimit) {
        $dots = strlen($string) > $characterLimit ? "..." : "";
        return substr($string, 0, $characterLimit) . $dots;
    }

    public function createPost($userId, $postTitle, $postDescription, $postCategory){
        try {
            $sql = "INSERT INTO posts (title, description, userId, categoryId) VALUES (:title, :description, :userId, :categoryId)";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':title', $postTitle);
            $stmt->bindParam(':description', $postDescription);
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':categoryId', $postCategory);
            $stmt->execute();
            $postId = $this->con->lastInsertId();
            if ($postId) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            echo "Erreur lors de l'enregistrement : " . $e->getMessage();
            return false;
        }
    }

    public function getPostCategories(){
        try {
            $query = $this->con->prepare("SELECT * FROM category");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des catégories : " . $e->getMessage();
            return [];
        }
    }

    public function getPostList($userId) {
        try {
            $query = $this->con->prepare("SELECT * 
                                          FROM posts
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

    public function saveComment($postId, $userId, $commentText) {
        try {
            $query = $this->con->prepare("INSERT INTO comments (postId, userId, commentText, createdAt) 
                                          VALUES (:postId, :userId, :commentText, NOW())");
    
            $query->bindParam(":postId", $postId, PDO::PARAM_INT);
            $query->bindParam(":userId", $userId, PDO::PARAM_INT);
            $query->bindParam(":commentText", $commentText);
    
            $query->execute();
    
            echo json_encode(["success" => true, "message" => "Commentaire enregistré avec succès."]);
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "message" => "Erreur : " . $e->getMessage()]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
    }

    public function updatePostFeatured($userId, $postId, $isFeatured){
        try {
            $sql = "UPDATE posts 
                    SET isFeatured = :isFeatured 
                    WHERE id = :postId AND userId = :userId";
            $stmt = $this->con->prepare($sql);

            $stmt->bindParam(':isFeatured', $isFeatured);
            $stmt->bindParam(':postId', $postId);
            $stmt->bindParam(':userId', $userId);

            // Exécuter la requête d'update
            $stmt->execute();
            return true;

        } catch (PDOException $e) {
            echo "Erreur lors de la mise à jour du produit : " . $e->getMessage();
            return false;
        }
    }

    private function getPostComments($postId) {
        try {
            $query = $this->con->prepare("SELECT comments.*, users.profile_photo, users.username
                                          FROM comments 
                                          INNER JOIN users ON comments.userId = users.id
                                          WHERE postId = :postId
                                        ");
            $query->bindParam(":postId", $postId, PDO::PARAM_INT);
            $query->execute();
            $resultsHtml = "<div class='commentList'>";

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $id = $row["userId"];
                $comment = $row["commentText"];
                $profilePicture = $row["profile_photo"] ?: "https://via.placeholder.com/150";
                $username = $row['username'];

                $resultsHtml .= "<div class='d-flex mb-12 comment-list'>
                    <a href='$this->siteUrl/profil.php/$id'><img src='$profilePicture' class='profile-photo'></a>
                    <div class='text'>
                        <div>
                            <span class='price'>$username a écrit : </span><br/>
                            <span class='description'>$comment</span>
                        </div>
                    </div>
                </div>";
            }

            $resultsHtml .= "</div>";
            return $resultsHtml;

        } catch (PDOException $e) {
            echo "Erreur dans la requête : " . $e->getMessage();
            return 0;
        }
    }

    public function removePost($userId, $postId) {
        try {
            $sql = "DELETE from posts 
                    WHERE id = :postId AND userId = :userId ";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':postId', $postId);
            $stmt->bindParam(':userId', $userId);

            // Exécuter la requête de suppression
            $stmt->execute();
            // Vérifier si des lignes ont été mises à jour
            if ($stmt->rowCount() > 0) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            echo "Erreur lors de la suppression du post : " . $e->getMessage();
        }
    }

    public function updatePost($userId, $postId, $title, $description, $keywords, $categoryId){
        try {
            $sql = "UPDATE posts 
                    SET title = :title, description = :description, keywords = :keywords, categoryId = :categoryId
                    WHERE id = :postId AND userId = :userId";
            $stmt = $this->con->prepare($sql);

            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':keywords', $keywords);
            $stmt->bindParam(':categoryId', $categoryId);
            $stmt->bindParam(':postId', $postId);
            $stmt->bindParam(':userId', $userId);

            // Exécuter la requête d'update
            $stmt->execute();
            return true;

        } catch (PDOException $e) {
            echo "Erreur lors de la mise à jour du post : " . $e->getMessage();
            return false;
        }
    }

    public function toggleLikePost($likerId, $likedId, $postId) {
        try {

             // Vérification si un "like" existe déjà pour cet utilisateur et ce post
            $sqlCheck = "SELECT id FROM likers WHERE likerId = :likerId AND likedId = :likedId AND postId = :postId";
            $stmtCheck = $this->con->prepare($sqlCheck);

            $stmtCheck->bindParam(':likerId', $likerId);
            $stmtCheck->bindParam(':likedId', $likedId);
            $stmtCheck->bindParam(':postId', $postId);

            $stmtCheck->execute();
            $like = $stmtCheck->fetch();

            if ($like) {
                // Si like existe, like retiré
                $sqlDelete = "DELETE FROM likers WHERE id = :id";
                $stmtDelete = $this->con->prepare($sqlDelete);

                $stmtDelete->bindParam(':id', $like['id']);
                $stmtDelete->execute();

                return ['success' => true, 'isLiked' => false];

            } else {
        
                $sqlInsert = "INSERT INTO likers (likerId, likedId, postId, createdDate) 
                                VALUES (:likerId, :likedId, :postId, NOW())";
                $stmtInsert = $this->con->prepare($sqlInsert);

                $stmtInsert->bindParam(':likerId', $likerId);
                $stmtInsert->bindParam(':likedId', $likedId);
                $stmtInsert->bindParam(':postId', $postId);

                $stmtInsert->execute();

                return ['success' => true, 'isLiked' => true];

            }
          
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur : ' . $e->getMessage()];
        }
    }
    
    
}
?>