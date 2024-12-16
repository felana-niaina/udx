<?php

session_start();

include_once '../classes/DatabaseConnector.php';
include_once '../classes/MarketplaceResultsProvider.php';
$database = new DatabaseConnector();
$con = $database->getConnection();
$ResultsProvider = new MarketplaceResultsProvider($con);

if(isset($_POST["productId"])){
    echo json_encode($ResultsProvider->getProductById());
} elseif(isset($_POST['listProduct'])) {
    echo json_encode($ResultsProvider->getProductList($_SESSION['user_id']));
} else{
    echo "no info passed to page";
}

$con = null;