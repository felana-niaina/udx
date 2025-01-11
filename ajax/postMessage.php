<?php

session_start();

include_once '../classes/DatabaseConnector.php';
include_once '../classes/MessageResultsProvider.php';
include_once '../classes/NotificationProvider.php';
$database = new DatabaseConnector();
$con = $database->getConnection();
$messageProvider = new MessageResultsProvider($con);

if($_SESSION['user_id']) {
    if(isset($_POST["form_id"]) && $_POST["form_id"] === 'messageReply' ){
        // send message
        $parentId = isset($_POST["parentId"]) ? $_POST["parentId"] : 0;
        $messageProvider->sendMessage($userId, $receiverId, $message, $subject, $parentId);
    } elseif(isset($_POST["parentId"])) {
        $listMessage  = $messageProvider->getMessagesByParent($_SESSION['user_id'] ,$_POST["parentId"]);
        $NotifProvider = new NotificationProvider($con);
        foreach ($listMessage as $key => $sms) {
            if($sms['toUserId'] == $_SESSION['user_id']) {
                // remove notification for each message
                $NotifProvider->removeMessageNotification($sms['toUserId'], $sms['id']);
            }
        }
        echo json_encode($listMessage);
    } else{
        echo "no info passed to page";
    }
}
$con = null;