<?php

session_start();

include_once '../classes/DatabaseConnector.php';
include_once '../classes/PostsResultsProvider.php';
include_once '../classes/NotificationProvider.php';

$database = new DatabaseConnector();
$con = $database->getConnection();
$ResultsProvider = new PostsResultsProvider($con);
$NotifProvider = new NotificationProvider($con);

if(isset($_POST["productId"])){
    // get post by Id
    echo json_encode($ResultsProvider->getProductById());
} elseif(isset($_POST['listProduct'])) {
    // get list of post by user
    echo json_encode($ResultsProvider->getPostList($_SESSION['user_id']));
} elseif (isset($_POST['postComment']) && $_SESSION['user_id'] == $_POST["userId"] ) {
    // comment post
    $postId = $_POST["postId"];
    $userId = $_POST["userId"];
    $commentText = $_POST["comment"];

    $result = $ResultsProvider->saveComment($postId, $userId, $commentText);
    if($result['success']) {
        // Send email into post owner
        $postInfo = $ResultsProvider->getPostInfo($postId);
        if(intval($userId) !== intval($postInfo->userId)) {
            // insert notification
            $NotifProvider->addNotification('comments', $result['item'], $userId, $postInfo->userId);
            $userNotifSetting = $NotifProvider->getUserSetting($postInfo->userId);
            if(is_null($userNotifSetting) || $userNotifSetting->isComment == 1 ) {
                $username = $_SESSION['user_username'];
                $paragraphe = "L'utilisateur $username a commenté votre post dans le Feed !";
                sendMail($postInfo->email, "Nouveau commentaire sur Underdex !", $paragraphe);
            }
        }
    }
    echo json_encode($result);
}elseif (isset($_POST['postLike'])){
    // like & unlike post
    $likerId = $_POST['userLiker'];
    $likedId = $_POST['userIdPost'];
    $postId = $_POST['postId'];
    $result = $ResultsProvider->toggleLikePost($likerId, $likedId, $postId);
    if($result['success'] && $result['isLiked'] == 1) {
        if($likerId !== $likedId ) {
            // insert notification
            $NotifProvider->addNotification('likers', $postId, $likerId, $likedId);

            // Send email into post owner
            $postInfo = $ResultsProvider->getPostInfo($postId);
            $userNotifSetting = $NotifProvider->getUserSetting($postInfo->userId);
            if(is_null($userNotifSetting) || $userNotifSetting->isLiked == 1 ) {
                $username = $_SESSION['user_username'];
                $paragraphe = "L'utilisateur $username vous a donné un like dans le Feed !";
                sendMail($postInfo->email, "Nouveau like sur Underdex !", $paragraphe);
            }
        }
    }
    echo json_encode($result);

} elseif (isset($_POST['postPagination']) && isset($_POST['pageNumber']) ){ 
    echo $ResultsProvider->getPostsByUser($_SESSION['user_id'], $_POST['pageNumber']);
} else{
    echo "no info passed to page";
}

$con = null;
die();

function sendMail($to, $subject, $paragraphe) {
    try {
        $message  = "
        <html>
        <head>
            <title>$subject!</title>
        </head>
        <p>Bonjour, </p>
        <p>$paragraphe</p>
        <p>Allez jeter un coup d’Oeil sur ce qui s’est passé !</p>
        <p>Cordialement,<br> Underdex Team</p>
        </body>
        </html>
        ";
        // Set headers for HTML content
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";

        // Additional headers
        $headers .= "From: udx@underdex.com" . "\r\n";
        $headers .= "Reply-To: udx@underdex.com" . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        return mail($to, $subject, $message, $headers);
        
    } catch (Exception $e) {
        // Si l'envoi échoue, affichage de l'erreur
        return "Le message n'a pas pu être envoyé. Erreur: {$mail->ErrorInfo}";
    }
}
