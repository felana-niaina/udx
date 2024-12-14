<?php
include_once '../classes/DatabaseConnector.php';
$database = new DatabaseConnector();
$con = $database->getConnection();

if(isset($_POST["productId"])){
    $query = $con->prepare("SELECT * FROM marketplace WHERE id = :productId ");
    $query->execute([
        "productId" => $_POST["productId"]
    ]);
    echo json_encode($query->fetchObject());
} elseif(isset($_POST['newProduct'])) {

} else{
    echo "no info passed to page";
}

$con = null;