<?php

session_start();

include_once '../classes/DatabaseConnector.php';
include_once '../classes/PostsResultsProvider.php';
include_once '../classes/UserRegistration.php';
$database = new DatabaseConnector();
$con = $database->getConnection();
$ResultsProvider = new PostsResultsProvider($con);

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
        $UserProvider = new UserRegistration($con);
        $UserInfo = $UserProvider->getUserInfo($userId);
        try {
            $to       = $UserInfo['email'];
            $username =  $UserInfo['username'];
            $subject  = 'Nouveau commentaire sur Underdex !';
            $message  = "
            <html>
            <head>
                <title>Nouveau commentaire sur Underdex !</title>
            </head>
            <p>Bonjour, </p>
            <p>L'utilisateur $username a commenté votre post dans le Feed !</p>
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

            mail($to, $subject, $message, $headers);
            
        } catch (Exception $e) {
            // Si l'envoi échoue, affichage de l'erreur
            $errorMessage = "Le message n'a pas pu être envoyé. Erreur: {$mail->ErrorInfo}";
        }
    }
    echo json_encode($result);
}elseif (isset($_POST['postLike'])){
    // like & unlike post
    $likerId = $_POST['userLiker'];
    $likedId = $_POST['userIdPost'];
    $postId = $_POST['postId'];
    echo json_encode($ResultsProvider->toggleLikePost($likerId, $likedId, $postId));

} elseif (isset($_POST['postPagination']) && isset($_POST['pageNumber']) ){ 
    echo $ResultsProvider->getPostsByUser($_SESSION['user_id'], $_POST['pageNumber']);
} else{
    echo "no info passed to page";
}

$con = null;
