<?php

session_start();

include_once '../classes/DatabaseConnector.php';
include_once '../classes/MessageResultsProvider.php';
$database = new DatabaseConnector();
$con = $database->getConnection();
$messageProvider = new MessageResultsProvider($con);

if($_SESSION['user_id']) {
    if(isset($_POST["parentId"])){
        echo json_encode($messageProvider->getMessagesByParent($_SESSION['user_id'] ,$_POST["parentId"]));
    } elseif(isset($_POST['listProduct'])) {
        echo json_encode($ResultsProvider->getProductList($_SESSION['user_id']));
    } else{
        echo "no info passed to page";
    }
}
$con = null;