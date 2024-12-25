<?php

session_start();

include_once '../classes/DatabaseConnector.php';
include_once '../classes/MessageResultsProvider.php';
$database = new DatabaseConnector();
$con = $database->getConnection();
$messageProvider = new MessageResultsProvider($con);

if($_SESSION['user_id']) {
    if(isset($_POST["form_id"]) && $_POST["form_id"] === 'messageReply' ){
        // send message
        $parentId = isset($_POST["parentId"]) ? $_POST["parentId"] : 0;
        $messageProvider->sendMessage($userId, $receiverId, $message, $subject, $parentId);
    } elseif(isset($_POST["parentId"])) {
        echo json_encode($messageProvider->getMessagesByParent($_SESSION['user_id'] ,$_POST["parentId"]));
    } else{
        echo "no info passed to page";
    }
}
$con = null;