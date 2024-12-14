<?php
session_start();

include_once '../classes/DatabaseConnector.php';
include_once '../classes/UserRegistration.php';
$database = new DatabaseConnector();
$con = $database->getConnection();
$resultsProvider = new UserRegistration($con);

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST["accountSetting"])){
        $isUpdated = $resultsProvider->updateUserName($_SESSION['user_id'], $_POST['usernameField'] );
        if($isUpdated) {
            $_SESSION['user_username'] = $_POST['usernameField'];
            echo 1;
        } else {
            echo 0;
        }
    } elseif(isset($_POST['removeAccount'])) {
        $isRemoved = $resultsProvider->removeUser($_SESSION['user_id']);
        if($isRemoved) {
            session_destroy();
            echo 1;
        } else {
            echo 0;
        }
    } else{
        echo "no info passed to page";
    }
}

$con = null;