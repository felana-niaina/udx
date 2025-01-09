<?php
class UserRegistration {
    private $con;
    
    public function __construct($db) {
        $this->con = $db;
    }

    // Méthode pour l'inscription d'un utilisateur
    public function registerUser($username, $email, $password) {
        try {
            // Hacher le mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->execute();
            
             // Récupérer l'ID directement après l'insertion
            $userId = $this->con->lastInsertId();
            if ($userId) {
                session_start();
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_username'] = $username;

                return true;
            }
            return false;

        } catch (PDOException $e) {
            echo "Erreur lors de l'enregistrement : " . $e->getMessage();
            return false;
        }
    }

    // Méthode pour se connecter
    public function loginUser($username, $password) {
        try {
            $sql = "SELECT id, username, password FROM users WHERE username = :username LIMIT 1";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Vérifier si l'utilisateur existe et si le mot de passe correspond
            if ($user && password_verify($password, $user['password'])) {
                // Initialisation de la session
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_username'] = $user['username'];

                return true;
            } else {
                return false; // Identifiants incorrects
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la connexion : " . $e->getMessage();
            return false;
        }
    }

    // Méthode pour mettre à jour la photo de couverture de l'utilisateur
    public function updateCoverPhoto($userId, $coverPhotoFile) {
        try {
            // Vérifier si le fichier est valide
            if ($coverPhotoFile['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Erreur lors du téléchargement du fichier.");
            }
            $newImageName = time() . '_' . uniqid() . '.' . pathinfo($coverPhotoFile['name'], PATHINFO_EXTENSION);

            // Déplacer le fichier téléchargé dans le répertoire voulu
            $uploadDir = 'uploads/';
            $targetFile = $uploadDir . $newImageName;
            
            if (!move_uploaded_file($coverPhotoFile['tmp_name'], $targetFile)) {
                throw new Exception("Impossible de déplacer le fichier téléchargé.");
            }

            // Mettre à jour la photo de couverture dans la base de données
            $sql = "UPDATE users SET cover_photo = :cover_photo WHERE id =:userId";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':cover_photo', $targetFile);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();

            return true;

        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
            return false;
        }
    }

    // Méthode pour mettre à jour la photo de couverture de l'utilisateur
    public function updateProfilePhoto($userId, $profilePhotoFile) {
        try {
            // Vérifier si le fichier est valide
            if ($profilePhotoFile['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Erreur lors du téléchargement du fichier.");
            }
            $newImageName = time() . '_' . uniqid() . '.' . pathinfo($profilePhotoFile['name'], PATHINFO_EXTENSION);

            // Déplacer le fichier téléchargé dans le répertoire voulu
            $uploadDir = 'uploads/';
            $targetFile = $uploadDir . $newImageName;
            
            if (!move_uploaded_file($profilePhotoFile['tmp_name'], $targetFile)) {
                throw new Exception("Impossible de déplacer le fichier téléchargé.");
            }

            // Mettre à jour la photo de couverture dans la base de données
            $sql = "UPDATE users SET profile_photo = :profile_photo WHERE id =:userId";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':profile_photo', $targetFile);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();

            return true;

        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
            return false;
        }
    }


    // Méthode pour récupérer les informations de l'utilisateur
    public function getUserInfo($userId) {
        try {
            $sql = "SELECT email, fullname, username ,profileTitle, bio, url, phone, location, cover_photo, profile_photo FROM users WHERE id = :userId";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                return $user;
            } else {
                return null;  // L'utilisateur n'a pas été trouvé
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des données utilisateur : " . $e->getMessage();
            return null;
        }
    }

    // Méthode pour mettre à jour les informations du profil utilisateur
    public function updateUserProfile($userId,$fullname, $profileTitle, $bio, $url, $phone, $location) {
        try {
            $sql = "UPDATE users 
                    SET fullname = :fullname, profileTitle = :profileTitle, bio = :bio, url = :url, phone = :phone, location = :location
                    WHERE id = :userId";
            $stmt = $this->con->prepare($sql);

            $stmt->bindParam(':fullname', $fullname);
            $stmt->bindParam(':profileTitle', $profileTitle);
            $stmt->bindParam(':bio', $bio);
            $stmt->bindParam(':url', $url);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':userId', $userId);

            // Exécuter la requête d'update
            $stmt->execute();

            // Vérifier si des lignes ont été mises à jour
            if ($stmt->rowCount() > 0) {
                return true;
            }
            return false;

        } catch (PDOException $e) {
            echo "Erreur lors de la mise à jour du profil : " . $e->getMessage();
            return false;
        }
    }

    // Méthode pour mettre à jour le userName
    public function updateUserName($userId, $userName){
        try {
            $sql = "UPDATE users 
                    SET username = :username 
                    WHERE id = :userId";
            $stmt = $this->con->prepare($sql);

            $stmt->bindParam(':username', $userName);
            $stmt->bindParam(':userId', $userId);

            // Exécuter la requête d'update
            $stmt->execute();
            // Vérifier si des lignes ont été mises à jour
            if ($stmt->rowCount() > 0) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            echo "Erreur lors de la mise à jour du nom d'utilisateur : " . $e->getMessage();
            return false;
        }
    }

    // Méthode pour supprimer un utilisateur
    public function removeUser($userId){
        try {
            $sql = "DELETE from users 
                    WHERE id = :userId";
            $stmt = $this->con->prepare($sql);
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
            return false;
        }
    }

    function resetProfileInfo($userId) {
        try {
            $sql = "UPDATE users SET fullname = '', profileTitle = '', bio = '', url = '', phone = '', location = '' WHERE id = :userId";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            header("Location: settings.php");
            exit();
        } catch (PDOException $e) {
            echo "Erreur lors de la réinitialisation du profil : " . $e->getMessage();
        }
    }

    public function updatePassword($userId, $username ,$currentPassword, $newPassword){
        try {
            $sql = "SELECT id, username, password FROM users WHERE id =:id LIMIT 1";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Vérifier si l'utilisateur existe et si le mot de passe correspond
            if ($user && password_verify($currentPassword, $user['password']) && $user['username'] == $username ) {
                $sql = "UPDATE users SET password = :password WHERE id = :userId";
                $stmt = $this->con->prepare($sql);

                $stmt->bindParam(':password', password_hash($newPassword, PASSWORD_DEFAULT));
                $stmt->bindParam(':userId', $userId);

                // Exécuter la requête d'update
                $stmt->execute();
                return true;
            } else {
                return false; // password incorrect
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la connexion : " . $e->getMessage();
            return false;
        }
    }

    public function isFollowedUser($followerId, $followedId) {
        try {
            $sql = "SELECT * FROM followers WHERE followerId = :followerId AND followedId = :followedId";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':followerId', $followerId);
            $stmt->bindParam(':followedId', $followedId);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                return $user;
            } else {
                return null;  // L'utilisateur n'a pas été trouvé
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des données : " . $e->getMessage();
            return null;
        }
    }

    public function isLiked($likerId, $likedId) {
        try {
            $sql = "SELECT * FROM likers WHERE likerId = :likerId AND likedId = :likedId";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':likerId', $likerId);
            $stmt->bindParam(':likedId', $likedId);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                return $user;
            } else {
                return null;  // L'utilisateur n'a pas été trouvé
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des données : " . $e->getMessage();
            return null;
        }
    }


    function updateFollowers($followedId, $followerId) {
        try {
            // Vérifier si l'utilisateur suit déjà cet utilisateur
            $sql = "SELECT COUNT(*) FROM followers WHERE followerId = :followerId AND followedId = :followedId";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':followerId', $followerId);
            $stmt->bindParam(':followedId', $followedId);
            $stmt->execute();

            $count = $stmt->fetchColumn();

            // Si l'utilisateur suit déjà le suivi
            if ($count > 0) {
                return ['success' => false, 'message' => 'Vous suivez déjà cet utilisateur.'];
            }


            $sql = "INSERT INTO followers (followerId, followedId) VALUES (:followerId, :followedId)";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':followedId', $followedId);
            $stmt->bindParam(':followerId', $followerId);
            $stmt->execute();
    
            return ['success' => true, 
            'message' => 'Félicitations ! Vous suivez désormais cet utilisateur.',
            'item' => $this->con->lastInsertId()
        ];
    
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur de base de données : ' . $e->getMessage()];
        }
    }

    public function countFollowers($userId) {
        try {
            // Vérifiez que la connexion à la base de données existe
            if (!$this->con) {
                throw new Exception("La connexion à la base de données a échoué.");
            }
    
            // Requête pour compter les followers
            $sql = "SELECT COUNT(*) AS totalFollowers FROM followers WHERE followedId = :userId";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();
    
            // Récupérer le nombre total de followers
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return $result['totalFollowers'];
    
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    public function calculatePoints($userId) {
        try {
            if (!$this->con) {
                throw new Exception("La connexion à la base de données a échoué.");
            }

            // Récupération du nombre de followers de l'utilisateur
            $totalFollowers = $this->countFollowers($userId);
    
            // Récupération du nombre de posts de l'utilisateur
            $postQuery = "SELECT COUNT(*) AS totalPosts FROM posts WHERE userId = :userId";
            $postStmt = $this->con->prepare($postQuery);
            $postStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $postStmt->execute();
            $postResult = $postStmt->fetch(PDO::FETCH_ASSOC);
            $totalPosts = $postResult['totalPosts'];
    
            // Calcul des points
            $points = ($totalPosts * 1) + ($totalFollowers * 2);
    
            return $points;
    
        } catch (PDOException $e) {
            return 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function getUserByName($userName) {
        try {
            $sql = "SELECT id, email, fullname, username ,profileTitle, bio, url, phone, location, cover_photo, profile_photo FROM users WHERE username = :username";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':username', $userName);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                return $user;
            } else {
                return null;  // L'utilisateur n'a pas été trouvé
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des données utilisateur : " . $e->getMessage();
            return null;
        }
    }
    
    
}
?>
