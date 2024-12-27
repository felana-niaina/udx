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

    public function sendMessage($userId, $receiverId, $message, $subject, $parentId = 0) {
        try {
            if (is_null($receiverId) && $parentId > 0) {
                // get receiver Id and the subject
                $exchange = $this->getMessageDetailsByParendId($parentId);
                if($exchange) {
                    $subject = $exchange->subject;
                    $receiverId = $exchange->fromUserId == $userId ? $exchange->toUserId : $exchange->fromUserId;
                }
            }

            $sql = "INSERT INTO message (subject, content, fromUserId, toUserId, parentId) VALUES (:subject, :content, :fromUserId, :toUserId, :parentId)";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':content', $message);
            $stmt->bindParam(':fromUserId', $userId);
            $stmt->bindParam(':toUserId', $receiverId);
            $stmt->bindParam(':parentId', $parentId);
            $stmt->execute();
            $messageId = $this->con->lastInsertId();
            if ($messageId) {
                if($parentId == 0) {
                    // Update parentId by the last insert Id
                    $sql = "UPDATE message 
                    SET parentId = :parentId
                    WHERE id = :messageId";
                    $stmt = $this->con->prepare($sql);
                    $stmt->bindParam(':parentId', $messageId);
                    $stmt->bindParam(':messageId', $messageId);

                    // Exécuter la requête d'update
                    $stmt->execute();
                }
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

    // Récupération détails des messages de l'utilisateur
    public function getMessagesByUser($userId) {
        try {
            if (!$this->con) {
                throw new Exception("La connexion à la base de données a échoué.");
            }
            $sql = "SELECT m1.subject, m1.content, m1.parentId, m1.createdDate, 
                m1.fromUserId, m1.toUserId, m1.isRead, fus.username AS fromUser, tus.username AS toUser
                FROM message m1
                INNER JOIN (
                    SELECT 
                        parentId,
                        MAX(createdDate) AS latestDate
                    FROM message
                    WHERE fromUserId = :userId OR toUserId = :userId
                    GROUP BY parentId
                ) m2 ON m1.parentId = m2.parentId AND m1.createdDate = m2.latestDate 
                INNER JOIN users fus ON m1.fromUserId = fus.id
                INNER JOIN users tus ON m1.toUserId = tus.id 
                WHERE fromUserId = :userId OR toUserId = :userId
                ORDER BY m1.createdDate DESC;
                ";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();

            $resultsHtml = "<div class='list-group'>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $subject = $row['subject'];
                $date = date('Y-m-d', strtotime($row['createdDate']));
                $userName = $row['fromUser'];
                if($userId == $row['fromUserId']) {
                    $userName = $row['toUser'];
                }
                $content = $row['content'];
                $parentId = $row['parentId'];
                $resultsHtml .= "<div class='list-group-item message-item' data-parent='$parentId' data-subject='$subject'>
                    <h5 class='mb-1'>$subject</h5>
                    <p class='mb-1'>$userName - $date</p>
                    <small>$content...</small>
                    </div>
                ";
            }
            $resultsHtml .= '</div>';
            return $resultsHtml;
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des messages : " . $e->getMessage();
            return [];
        }
    }

    // Récupération détails des messages du même sujet
    public function getMessagesByParent($userId, $parentId){
        try {
            if (!$this->con) {
                throw new Exception("La connexion à la base de données a échoué.");
            }
            $sql = "SELECT m1.*, fus.profile_photo, fus.username, tus.profile_photo, tus.username 
                FROM message m1
                INNER JOIN users fus ON m1.fromUserId = fus.id
                INNER JOIN users tus ON m1.toUserId = tus.id
                WHERE m1.parentId = :parentId AND ( m1.fromUserId = :userId OR m1.toUserId = :userId )
                ORDER BY createdDate ASC;
                ";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':parentId', $parentId);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des messages : " . $e->getMessage();
            return [];
        }
    }

    public function getMessageDetailsByParendId($parentId) {
        try {
            $sql = "SELECT * FROM message WHERE parentId = :parentId LIMIT 0,1";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':parentId', $parentId);
            $stmt->execute();
            
            $message = $stmt->fetchObject();
            
            if ($message) {
                return $message;
            } else {
                return null;  // L'utilisateur n'a pas de pub
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des données utilisateur : " . $e->getMessage();
            return null;
        }
    }
}
?>