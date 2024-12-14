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

    // Méthode pour récupérer les informations de l'utilisateur
    public function getUserInfo($userId) {
        try {
            $sql = "SELECT fullname, profileTitle, bio, url, phone, location FROM users WHERE id = :userId";
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
    
}
?>
