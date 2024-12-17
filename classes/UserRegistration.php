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
            $sql = "SELECT fullname, username ,profileTitle, bio, url, phone, location, cover_photo, profile_photo FROM users WHERE id = :userId";
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
            $sql = "UPDATE users SET profileTitle = '', bio = '', url = '', phone = '', location = '' WHERE id = :userId";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            header("Location: settings.php?reset=profile_success");
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
    
}
?>
