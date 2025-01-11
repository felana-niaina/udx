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

    public function getMessageNotification($userId) {
        try {
            $type = 'message';
            $sql = "SELECT users.username, notif.createdDate, sms.content FROM notifications AS notif
                INNER JOIN message AS sms ON notif.itemId = sms.id
                LEFT JOIN users ON notif.fromUserId = users.id
                WHERE forUserId = :userId AND type = :type
            ";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':type', $type);
            $stmt->execute();
            
            $notifs = $stmt->fetchAll();
            
            if ($notifs) {
                return $notifs;
            } else {
                return null;  // L'utilisateur n'a pas de pub
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des données utilisateur : " . $e->getMessage();
            return 0;
        }
    }

    public function getLikeNotification($userId) {
        try {
            $type = 'likers';
            $sql = "SELECT users.username, notif.createdDate, post.title FROM notifications AS notif
                INNER JOIN posts AS post ON notif.itemId = post.id
                LEFT JOIN users ON notif.fromUserId = users.id
                WHERE notif.forUserId = :userId AND notif.type = :type
            ";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':type', $type);
            $stmt->execute();
            
            $notifs = $stmt->fetchAll();
            
            if ($notifs) {
                return $notifs;
            } else {
                return null;  // L'utilisateur n'a pas de pub
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des données utilisateur : " . $e->getMessage();
            return 0;
        }
    }

    public function getCommentNotification($userId) {
        try {
            $type = 'comments';
            $sql = "SELECT users.username, notif.createdDate, comment.commentText FROM notifications AS notif
                INNER JOIN comments AS comment ON notif.itemId = comment.id
                LEFT JOIN users ON notif.fromUserId = users.id
                WHERE notif.forUserId = :userId AND notif.type = :type
            ";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':type', $type);
            $stmt->execute();
            
            $notifs = $stmt->fetchAll();
            
            if ($notifs) {
                return $notifs;
            } else {
                return null;  // L'utilisateur n'a pas de pub
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des données utilisateur : " . $e->getMessage();
            return 0;
        }
    }

    public function getFollowerNotification($userId) {
        try {
            $type = 'followers';
            $sql = "SELECT users.username, notif.createdDate  FROM notifications AS notif
                LEFT JOIN users ON notif.fromUserId = users.id
                WHERE notif.forUserId = :userId AND notif.type = :type
            ";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':type', $type);
            $stmt->execute();
            
            $notifs = $stmt->fetchAll();
            
            if ($notifs) {
                return $notifs;
            } else {
                return null;  // L'utilisateur n'a pas de pub
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des données utilisateur : " . $e->getMessage();
            return 0;
        }
    }

    public function removeUserNotification($userId) {
        try {
            $liker = 'likers';
            $comment = 'comments';
            $follow = 'followers';
            $sql = "DELETE from notifications 
                    WHERE forUserId = :forUserId AND type IN ( :liker, :comment, :follow )";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':forUserId', $userId);
            $stmt->bindParam(':liker', $liker);
            $stmt->bindParam(':comment', $comment);
            $stmt->bindParam(':follow', $follow);

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

}
?>