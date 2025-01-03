<?php

session_start();

include_once '../classes/DatabaseConnector.php';
include_once '../classes/PostsResultsProvider.php';
$database = new DatabaseConnector();
$con = $database->getConnection();
$ResultsProvider = new PostsResultsProvider($con);

if(isset($_POST["productId"])){
    echo json_encode($ResultsProvider->getProductById());
} elseif(isset($_POST['listProduct'])) {
    echo json_encode($ResultsProvider->getPostList($_SESSION['user_id']));
} elseif (isset($_POST['postComment']) && $_SESSION['user_id'] == $_POST["userId"] ) {
    $postId = $_POST["postId"];
    $userId = $_POST["userId"];
    $commentText = $_POST["comment"];
    echo $ResultsProvider->saveComment($postId, $userId, $commentText);
}elseif (isset($_POST['postLike'])){
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
