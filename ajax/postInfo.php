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
} else{
    echo "no info passed to page";
}

$con = null;