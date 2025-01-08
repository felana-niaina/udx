<?php

session_start();

include_once '../classes/DatabaseConnector.php';
include_once '../classes/NotificationProvider.php';

$database = new DatabaseConnector();
$con = $database->getConnection();
$NotifProvider = new NotificationProvider($con);

$post = [];
if(isset($_POST['data'])) {
    parse_str($_POST['data'], $post);
}

if(isset($post["form_id"]) && $post['form_id'] === "notificationSetting" && ($post['user_id'] == $_SESSION['user_id'] ) ){
    echo $NotifProvider->setUserSetting($post['user_id'], $post);
} 
$con = null;
die();
