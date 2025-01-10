<?php

// Classe NotificationProvider
class NotificationProvider {
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

    public function getUserSetting($userId){
        try {
            $sql = "SELECT * FROM notificationsSetting WHERE userId = :userId";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            
            $setting = $stmt->fetchObject();
            
            if ($setting) {
                return $setting;
            } else {
                return null;  // L'utilisateur n'a pas de pub
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des données utilisateur : " . $e->getMessage();
            return null;
        }
    }

    public function setUserSetting($userId, $params) {
        try {
            $existingSetting = $this->getUserSetting($userId);
            $isComment = isset($params['setComment']) ? 1 : 0;
            $isLiked = isset($params['setLike']) ? 1 : 0;
            $isFollower = isset($params['setFollower']) ? 1 : 0;
            $isMessage = isset($params['setMessage']) ? 1 : 0;
            $isVulnerability = isset($params['setVulnerability']) ? 1 : 0;
            $isSummary = isset($params['setSummary']) ? 1 : 0;

            if(!is_null($existingSetting)) {
                // update
                $sql = "UPDATE notificationsSetting 
                    SET isComment = :isComment, isLiked = :isLiked, isFollower = :isFollower, isMessage = :isMessage, isVulnerability = :isVulnerability, isSummary = :isSummary
                    WHERE id = :settingId";
                $stmt = $this->con->prepare($sql);

                $stmt->bindParam(':isComment', $isComment);
                $stmt->bindParam(':isLiked', $isLiked);
                $stmt->bindParam(':isFollower', $isFollower);
                $stmt->bindParam(':isMessage', $isMessage);
                $stmt->bindParam(':isVulnerability', $isVulnerability);
                $stmt->bindParam(':isSummary', $isSummary);
                $stmt->bindParam(':settingId', $existingSetting->id);

                // Exécuter la requête d'update
                $stmt->execute();
                return true;
            }

            // insert
            $sql = "INSERT INTO notificationsSetting (isComment, isLiked, isFollower, isMessage, isVulnerability, isSummary, userId) VALUES (:isComment, :isLiked, :isFollower, :isMessage, :isVulnerability, :isSummary, :userId)";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':isComment', $isComment);
            $stmt->bindParam(':isLiked', $isLiked);
            $stmt->bindParam(':isFollower', $isFollower);
            $stmt->bindParam(':isMessage', $isMessage);
            $stmt->bindParam(':isVulnerability', $isVulnerability);
            $stmt->bindParam(':isSummary', $isSummary);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            $settingId = $this->con->lastInsertId();
            if ($settingId) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            echo "Erreur lors de l'enregistrement : " . $e->getMessage();
            return false;
        }
    }

    public function addNotification($type, $itemId, $fromUserId, $forUserId) {
        try {
            // insert
            $sql = "INSERT INTO notifications (type, itemId, fromUserId, forUserId) VALUES (:type, :itemId, :fromUserId, :forUserId)";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':itemId', $itemId);
            $stmt->bindParam(':fromUserId', $fromUserId);
            $stmt->bindParam(':forUserId', $forUserId);
            $stmt->execute();
            $notifId = $this->con->lastInsertId();
            if ($notifId) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            echo "Erreur lors de l'enregistrement : " . $e->getMessage();
            return false;
        }
    }

    public function getNotificationByType($type, $userId){
        try {
            $sql = "SELECT COUNT(*) AS notifNumber FROM notifications WHERE forUserId = :userId AND type = :type";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':type', $type);
            $stmt->execute();
            
            $notifs = $stmt->fetchColumn();
            
            if ($notifs) {
                return $notifs;
            } else {
                return 0;  // L'utilisateur n'a pas de pub
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des données utilisateur : " . $e->getMessage();
            return 0;
        }
    }

}
?>