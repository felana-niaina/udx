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
                $resultsHtml .= "<div class='list-group-item message-item' data-parent='$parentId'>
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
}
?>