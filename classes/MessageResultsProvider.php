<?php

// Classe MessageResultsProvider
class MessageResultsProvider {
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

    public function sendMessage($userId, $receiverId, $message, $subject) {
        try {
            $sql = "INSERT INTO message (subject, content, fromUserId, toUserId) VALUES (:subject, :fromUserId, :toUserId)";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':subject', $adType);
            $stmt->bindParam(':content', $adContent);
            $stmt->bindParam(':fromUserId', $budget);
            $stmt->bindParam(':toUserId', $userId);
            $stmt->execute();
            $messageId = $this->con->lastInsertId();
            if ($messageId) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            echo "Erreur lors de l'enregistrement : " . $e->getMessage();
            return false;
        }
    }

    public function markAsRead($messageId) {
        try {
            $sql = "UPDATE message 
                SET isRead = 1 
                WHERE id = :messageId";
            $stmt = $this->con->prepare($sql);

            $stmt->bindParam(':messageId', $messageId);
            // Exécuter la requête d'update
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "Erreur lors de l'enregistrement : " . $e->getMessage();
            return false;
        }
    }

    public function getMessagesByUser($userId) {
        
    }
}
?>