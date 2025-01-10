<?php

    session_start();
    if(isset($_SESSION['user_id'])) {
        include_once 'classes/DatabaseConnector.php';
        include_once 'classes/UserRegistration.php';

        // Créer une instance de la classe DatabaseConnector
        $database = new DatabaseConnector();
        $con = $database->getConnection();
        $userRegistration = new UserRegistration($con);
        $userRegistration->addToHistory($_SESSION['user_id'], 'logout');
        session_destroy();
        header('Location: index.php');
        exit;
    }
?>