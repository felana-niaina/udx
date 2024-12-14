<?php
  session_start();

  // Inclusion des classes nécessaires
  include_once 'classes/DatabaseConnector.php';
  include_once 'classes/UserRegistration.php';
  include_once 'classes/MarketplaceResultsProvider.php';
  include_once 'classes/PostsResultsProvider.php'; 

  // Créer une instance de la classe DatabaseConnector
  $database = new DatabaseConnector();
  $con = $database->getConnection();

  // Vérifier si l'utilisateur est connecté
  if (!isset($_SESSION['user_id'])) {
      // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
      header('Location: login.php');
      exit;
  }

  // Créer une instance de la classe UserRegistration
  $userRegistration = new UserRegistration($con);

  // Récupérer l'ID utilisateur à partir de la session
  $userId = $_SESSION['user_id'];

  // Récupérer les informations de l'utilisateur
  $userInfo = $userRegistration->getUserInfo($userId);

  // Si l'utilisateur existe, on affiche les informations, sinon un message d'erreur
  if ($userInfo === null) {
      echo "Utilisateur introuvable.";
      exit;
  }

  // Si les données existent, elles seront stockées dans des variables
  $fullName = $userInfo['fullname'] ?? '';
  $profileTitle = $userInfo['profileTitle'] ?? '';
  $bio = $userInfo['bio'] ?? '';
  $url = $userInfo['url'] ?? '';
  $phone = $userInfo['phone'] ?? '';
  $location = $userInfo['location'] ?? '';

  //update information
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Vérifier quel formulaire a été soumis
    $formId = $_POST['form_id'] ?? '';

    if (isset($_POST['reset_changes'])) {
        // Si le bouton "Reset Changes" a été soumis, gérer la réinitialisation
        switch ($formId) {
            case 'update_profile':
                $userRegistration->resetProfileInfo($userId); // Appeler la fonction spécifique au formulaire de profil
                break;
            default:
                echo "Formulaire inconnu pour Reset.";
                break;
        }
    }

    // Traitement du formulaire pour la mise à jour du profil
    if ($formId === 'update_profile') {
      $fullname = $_POST['fullname'] ?? '';
      $profileTitle = $_POST['profileTitle'] ?? '';
      $bio = $_POST['bio'] ?? '';
      $url = $_POST['url'] ?? '';
      $phone = $_POST['phone'] ?? '';
      $location = $_POST['location'] ?? '';

      // Vérifier si toutes les informations nécessaires sont présentes
      if ($userId && $fullname && $profileTitle && $bio && $url && $phone && $location) {
          $userRegistration = new UserRegistration($con);

          // Appeler la méthode pour mettre à jour le profil de l'utilisateur
          if ($userRegistration->updateUserProfile($userId,$fullname ,$profileTitle, $bio, $url, $phone, $location)) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Succès',
                        text: 'Profil mis à jour !',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'settings.php';
                        }
                    });
                });
            </script>";
          } else {
              echo "Échec de la mise à jour du profil.";
          }
      }
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Paramètres</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
        /* Corps de la page */
        body {
            background-color: #f5f5f5; /* Fond clair pour toute la page */
            color: #333; /* Texte sombre pour un bon contraste */
            font-family: 'Arial', sans-serif;
            margin: 0;
        }

        /* Conteneur principal */
        .container {
            max-width: 1200px;
            margin-top: 50px;
            padding: 20px;
        }

        /* En-tête */
        .headerContent {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background-color: #1c1c1c;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .logoContainer img {
            width: 150px;
        }

        .searchContainer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        /* Carte du contenu */
        .card {
            background-color: #fff; /* Fond clair pour la carte */
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 20px;
            color: #333; /* Texte sombre dans la carte */
        }

        .card-header {
            background-color: #fff; /* Fond blanc pour l'en-tête */
            color: #000; /* Texte noir pour l'en-tête */
            font-size: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }

        .card-body h5 {
            color: #333;
            font-size: 22px;
            margin-bottom: 20px;
        }

        .card-body p {
            font-size: 16px;
            color: #6c757d; /* Texte légèrement plus clair pour les paragraphes */
            line-height: 1.6;
        }

        /* Style for market place */
        .product-item {
            cursor: pointer;
            padding: 15px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }

        .product-item:hover {
            background-color: #f1f1f1;
        }

        .product-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
            object-fit: cover;
        }

        .product-title, .modal-title {
            font-size: 18px !important;
            font-weight: bold;
            margin-top: 10px;
        }

        .product-price {
            font-size: 16px !important;
            color: #28a745 !important;
            font-weight: bold;
            margin-top: 5px;
        }

        .product-description {
            font-size: 14px !important;
            color: #333 !important;
            margin-top: 5px;
        }

        .product-actions {
            margin-top: 10px;
        }

        .product-actions a {
            color: #007bff;
            text-decoration: none;
            margin-right: 10px;
        }

        .product-actions a:hover {
            text-decoration: underline;
        }

        /* End Style for marketplace */

        /* Style for post */
        .post-item {
            cursor: pointer;
            padding: 15px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }

        .post-item:hover {
            background-color: #f1f1f1;
        }

        .post-title {
            font-size: 18px !important;
            font-weight: bold;
        }

        .post-date {
            font-size: 14px !important;
            color: #888;
        }

        .post-body {
            font-size: 14px !important;
            color: #333;
        }

        .post-actions {
            margin-top: 10px;
            font-size: 14px !important;
        }

        .post-actions a {
            color: #007bff;
            text-decoration: none;
        }

        .post-actions a:hover {
            text-decoration: underline;
        }
        /* End Style for post */

        /* style for message */
        .message-item {
            cursor: pointer;
        }

        .message-item:hover {
            background-color: #f1f1f1;
        }

        .message-details {
            display: none;
        }
        /* End Style for message */

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                margin-top: 20px;
            }
            .card {
                margin-bottom: 20px;
            }
        }
		
		

    </style>
</head>
<body>
  <div class="headerContent">
    <div class="logoContainer">
        <a href="index.php">
            <img src="assets/images/logo.png" alt="Underdex">
        </a>
    </div>
    <div class="searchContainer">
    </div>
  </div>
  <div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="main-breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item"><a href="profil.php">Profil public</a></li>
        <li class="breadcrumb-item active" aria-current="page">Paramètres</li>
      </ol>
    </nav>
    <!-- /Breadcrumb -->

    <div class="row gutters-sm">
      <div class="col-md-4 d-none d-md-block">
        <div class="card">
          <div class="card-body">
            <nav class="nav flex-column nav-pills nav-gap-y-1">
              <a href="#profile" data-toggle="tab" class="nav-item nav-link has-icon nav-link-faded active">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user mr-2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>Profile Information
              </a>
              <a href="#account" data-toggle="tab" class="nav-item nav-link has-icon nav-link-faded">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings mr-2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>Account Settings
              </a>
              <a href="#marketplace" data-toggle="tab" class="nav-item nav-link has-icon nav-link-faded">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message mr-2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>Marketplace
              </a>
              <a href="#posts" data-toggle="tab" class="nav-item nav-link has-icon nav-link-faded">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message mr-2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>Mes posts
              </a>
              <a href="#messages" data-toggle="tab" class="nav-item nav-link has-icon nav-link-faded">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message mr-2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>Messagerie <span class="badge badge-danger">10</span>
              </a>
              <a href="#security" data-toggle="tab" class="nav-item nav-link has-icon nav-link-faded">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shield mr-2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>Security
              </a>
              <a href="#notifications" data-toggle="tab" class="nav-item nav-link has-icon nav-link-faded">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell mr-2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>Notification <span class="badge badge-danger">3</span>
              </a>
              <a href="#billing" data-toggle="tab" class="nav-item nav-link has-icon nav-link-faded">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-credit-card mr-2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>Moyen de paiement
              </a>
				      <a href="#ads" data-toggle="tab" class="nav-item nav-link has-icon nav-link-faded">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eur mr-2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>Publicité <span class="badge badge-danger">Bêta !</span>
              </a>
            </nav>
          </div>
        </div>
      </div>
      <div class="col-md-8">
        <div class="card">
          <div class="card-header border-bottom mb-3 d-flex d-md-none">
            <ul class="nav nav-tabs card-header-tabs nav-gap-x-1" role="tablist">
              <li class="nav-item">
                <a href="#profile" data-toggle="tab" class="nav-link has-icon active"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></a>
              </li>
              <li class="nav-item">
                <a href="#account" data-toggle="tab" class="nav-link has-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg></a>
              </li>
              <li class="nav-item">
                <a href="#security" data-toggle="tab" class="nav-link has-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shield"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg></a>
              </li>
              <li class="nav-item">
                <a href="#notification" data-toggle="tab" class="nav-link has-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg></a>
              </li>
              <li class="nav-item">
                <a href="#billing" data-toggle="tab" class="nav-link has-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-credit-card"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg></a>
              </li>
              <li class="nav-item">
                <a href="#pub" data-toggle="tab" class="nav-link has-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eur"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg></a>
              </li>
            </ul>
          </div>
    
          <div class="card-body tab-content">
            <div class="tab-pane active" id="profile">
              <h6>YOUR PROFILE INFORMATION</h6>
              <hr>
              <form action="settings.php" method="POST" >
                <input type="hidden" name="form_id" value="update_profile">
                <div class="form-group">
                  <label for="fullName">Nom Prénom</label>
                  <input type="text" class="form-control" id="fullName" name="fullname" aria-describedby="fullNameHelp" placeholder="Enter your fullname" value="<?php echo htmlspecialchars($fullName); ?>">
                  <small id="fullNameHelp" class="form-text text-muted">Seul votre pseudonyme sera visible publiquement, votre nom et prénom resterons privé.</small>
                </div>
                <div class="form-group">
                  <label for="fullName">Titre du profil</label>
                  <input type="text" class="form-control" id="profileTitle" aria-describedby="fullNameHelp" placeholder="Entrez le titre de votre profil" name="profileTitle" value="<?php echo htmlspecialchars($profileTitle); ?>">
                  <small id="fullNameHelp" class="form-text text-muted">Choisissez un titre pour définir votre profil et qui vous êtes.</small>
                </div>
                <div class="form-group">
                  <label for="bio">Your Bio</label>
                  <textarea class="form-control autosize" id="bio" name="bio" placeholder="Write something about you" style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 62px;"><?php echo htmlspecialchars($bio); ?></textarea>
                </div>
                <div class="form-group">
                  <label for="url">URL</label>
                  <input type="text" class="form-control" id="url" name="url" placeholder="Enter your website address" value="<?php echo htmlspecialchars($url); ?>">
                </div>
                <div class="form-group">
                  <label for="phone">Téléphone</label>
                  <input type="text" class="form-control" id="phone" name="phone" placeholder="Entrez votre numéro de téléphone" value="<?php echo htmlspecialchars($phone); ?>">
                  <small id="fullNameHelp" class="form-text text-muted">Votre numéro de téléphone ne sera pas visible publiquement.</small>
                </div>
                <div class="form-group">
                  <label for="location">Location</label>
                  <input type="text" class="form-control" id="location" name="location" placeholder="Enter your location" value="<?php echo htmlspecialchars($location); ?>">
                </div>
                <div class="form-group small text-muted">
                  All of the fields on this page are optional and can be deleted at any time, and by filling them out, you're giving us consent to share this data wherever your user profile appears.
                </div>
                <button type="submit" class="btn btn-primary">Update Profile</button>
                <button type="submit" class="btn btn-light" name="reset_changes">Reset Changes</button>
              </form>
            </div>

            <div class="tab-pane" id="account">
              <h6>ACCOUNT SETTINGS</h6>
              <hr>
              <form id="accountSetting">
                <input type="hidden" name="accountSetting">
                <div class="form-group">
                  <label for="username">Username</label>
                  <input type="text" class="form-control" id="usernameField" name="usernameField" aria-describedby="usernameHelp" placeholder="Enter your username" value="<?php echo $_SESSION['user_username'] ?>">
                  <small id="usernameHelp" class="form-text text-muted">After changing your username, your old username becomes available for anyone else to claim.</small>
                </div>
                <hr>
                <div class="form-group">
                  <label class="d-block text-danger">Delete Account</label>
                  <p class="text-muted font-size-sm">Once you delete your account, there is no going back. Please be certain.</p>
                </div>
                <button class="btn btn-danger" type="button" id="removeAccount">Delete Account</button>
              </form>
            </div>

            <div class="tab-pane" id="marketplace">
              <h6>MARKETPLACE</h6>
              <hr>
              <?php 
                $resultsProvider = new MarketplaceResultsProvider($con);
                echo $resultsProvider->getProductsByUser($_SESSION['user_id']);
              ?>
              <button class="btn btn-primary mt-4" data-toggle="modal" data-target="#postProductModal">Poster une annonce</button>
            </div>

            <div class="tab-pane" id="posts">
              <h6>POSTS</h6>
              <hr>
              <div class="list-group">
                <?php 
                  $resultsProvider = new PostsResultsProvider($con);
                  echo $resultsProvider->getPostsByUser($_SESSION['user_id']);
                ?>
              </div>
            </div>

            <div class="tab-pane" id="messages">
              <h6>YOUR INBOX</h6>
              <hr>
              <div class="list-group">
                <!-- Example of received message -->
                <div class="list-group-item message-item" data-toggle="modal" data-target="#messageModal" data-subject="Meeting Request" data-body="Hi, I wanted to schedule a meeting with you next week. Let me know your availability." data-sender="John Doe" data-date="2024-12-10">
                  <h5 class="mb-1">Meeting Request</h5>
                  <p class="mb-1">John Doe - 2024-12-10</p>
                  <small>Hi, I wanted to schedule a meeting with you next week...</small>
                </div>
                <!-- Add more messages here as needed -->
                <div class="list-group-item message-item" data-toggle="modal" data-target="#messageModal" data-subject="Project Update" data-body="The project status has been updated. Please check the dashboard." data-sender="Jane Smith" data-date="2024-12-09">
                  <h5 class="mb-1">Project Update</h5>
                  <p class="mb-1">Jane Smith - 2024-12-09</p>
                  <small>The project status has been updated...</small>
                </div>
              </div>
            </div>
            
            <div class="tab-pane" id="security">
              <h6>SECURITY SETTINGS</h6>
              <hr>
              <form>
                <div class="form-group">
                  <label class="d-block">Change Password</label>
                  <input type="text" class="form-control" placeholder="Enter your old password">
                  <input type="text" class="form-control mt-1" placeholder="New password">
                  <input type="text" class="form-control mt-1" placeholder="Confirm new password">
                </div>
              </form>
              <hr>
              <form>
                <div class="form-group">
                  <label class="d-block">Two Factor Authentication</label>
                  <button class="btn btn-info" type="button">Enable two-factor authentication</button>
                  <p class="small text-muted mt-2">Two-factor authentication adds an additional layer of security to your account by requiring more than just a password to log in.</p>
                </div>
              </form>
              <hr>
              <form>
                <div class="form-group mb-0">
                  <label class="d-block">Sessions</label>
                  <p class="font-size-sm text-secondary">This is a list of devices that have logged into your account. Revoke any sessions that you do not recognize.</p>
                  <ul class="list-group list-group-sm">
                    <li class="list-group-item has-icon">
                      <div>
                        <h6 class="mb-0">San Francisco City 190.24.335.55</h6>
                        <small class="text-muted">Your current session seen in United States</small>
                      </div>
                      <button class="btn btn-light btn-sm ml-auto" type="button">More info</button>
                    </li>
                  </ul>
                </div>
              </form>
            </div>

            <div class="tab-pane" id="notifications">
              <h6>NOTIFICATIONS</h6>
              <hr>
              <form>
                <div class="form-group">
                  <label class="d-block mb-0">Security Alerts</label>
                  <div class="small text-muted mb-3">Receive security alert notifications via email</div>
                  <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="customCheck1" checked="">
                    <label class="custom-control-label" for="customCheck1">Email each time a vulnerability is found</label>
                  </div>
                  <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="customCheck2" checked="">
                    <label class="custom-control-label" for="customCheck2">Email a digest summary of vulnerability</label>
                  </div>
                </div>
                <div class="form-group mb-0">
                  <label class="d-block">SMS Notifications</label>
                  <ul class="list-group list-group-sm">
                    <li class="list-group-item has-icon">
                      Commentaires
                      <div class="custom-control custom-control-nolabel custom-switch ml-auto">
                        <input type="checkbox" class="custom-control-input" id="customSwitch1" checked="">
                        <label class="custom-control-label" for="customSwitch1"></label>
                      </div>
                    </li>
                    <li class="list-group-item has-icon">
                      Likes
                      <div class="custom-control custom-control-nolabel custom-switch ml-auto">
                        <input type="checkbox" class="custom-control-input" id="customSwitch2">
                        <label class="custom-control-label" for="customSwitch2"></label>
                      </div>
                    </li>
                    <li class="list-group-item has-icon">
                      Messages
                      <div class="custom-control custom-control-nolabel custom-switch ml-auto">
                        <input type="checkbox" class="custom-control-input" id="customSwitch3" checked="">
                        <label class="custom-control-label" for="customSwitch3"></label>
                      </div>
                    </li>
                    <li class="list-group-item has-icon">
                      Followers
                      <div class="custom-control custom-control-nolabel custom-switch ml-auto">
                        <input type="checkbox" class="custom-control-input" id="customSwitch4" checked="">
                        <label class="custom-control-label" for="customSwitch4"></label>
                      </div>
                    </li>
                  </ul>
                </div>
              </form>
            </div>

            <div class="tab-pane" id="billing">
              <h6>BILLING</h6>
              <hr>
              <div class="form-group">
                <label class="d-block mb-0">Payment Method</label>
                <div class="small text-muted mb-3">You have not added a payment method</div>
                <button class="btn btn-info" type="button" data-toggle="modal" data-target="#paymentModal">Ajouter une carte bancaire</button>
              </div>
              <div class="form-group mb-0">
                <label class="d-block">Payment History</label>
                <div class="border border-gray-500 bg-gray-200 p-3 text-center font-size-sm">You have not made any payment.</div>
              </div>
            </div>

            <div class="tab-pane" id="ads">
              <h6>ADS</h6>
              <hr>
              <form>
                <div class="form-group">
                  <label for="adType">Type de publicité</label>
                  <select class="form-control" id="adType">
                      <option value="display">Publicité Search</option>
                      <option value="video">Publicité Marketplace</option>
                  </select>
                  <small id="adTypeHelp" class="form-text text-muted">Sélectionnez le type de publicité que vous souhaitez utiliser.</small>
                </div>
                <div class="form-group">
                  <label for="targeting">Contenue</label>
                  <select class="form-control" id="targeting">
                      <option value="location">Mon annonce marketplace</option>
                      <option value="age">Mon site internet</option>
                  </select>
                  <small id="targetingHelp" class="form-text text-muted">Sélectionnez votre méthode de ciblage.</small>
                </div>
                <div class="form-group">
                  <label for="dailyBudget">Budget quotidien</label>
                  <select class="form-control" id="dailyBudget">
                      <option value="10">10€</option>
                      <option value="20">20€</option>
                      <option value="50">50€</option>
                      <option value="100">100€</option>
                      <option value="custom">Autre...</option>
                  </select>
                  <small id="dailyBudgetHelp" class="form-text text-muted">Sélectionnez votre budget quotidien ou choisissez "Autre" pour entrer un budget personnalisé.</small>
                </div>
                <div class="form-group small text-muted">
                  All of the fields on this page are optional and can be deleted at any time, and by filling them out, you're giving us consent to share this data wherever your user profile appears.
                </div>
                <button type="button" class="btn btn-primary">Valider</button>
                <button type="reset" class="btn btn-light">Reset Changes</button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal pour afficher les détails de l'article -->
      <div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="productModalLabel">Détails de l'article</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                      <div id="productContent">
                          <img id="productImage" src="" alt="Image de l'article" class="product-image">
                          <h5 id="productTitle"></h5>
                          <p id="productPrice"></p>
                          <p id="productDescription"></p>
                          <p id="productDate"></p>
                      </div>

                      <!-- Formulaire pour répondre à l'article -->
                      <div class="response-box">
                          <h6>Votre message:</h6>
                          <textarea id="responseText" class="form-control" rows="4" placeholder="Tapez votre message ici..."></textarea>
                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                      <button type="button" class="btn btn-primary" id="replyButton">Envoyer</button>
                  </div>
              </div>
          </div>
      </div>

      <!-- Modal pour poster une nouvelle annonce -->
      <div class="modal fade" id="postProductModal" tabindex="-1" role="dialog" aria-labelledby="postProductModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="postProductModalLabel">Poster une annonce</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form id="postProductForm" method="post" enctype="multipart/form-data">
                <input type="hidden" name="newProduct">
                <!-- Titre -->
                <div class="form-group">
                    <label for="title">Titre</label>
                    <input type="text" class="form-control" id="title" placeholder="Titre de l'annonce" required>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" rows="3" placeholder="Description de l'article" required></textarea>
                </div>

                <!-- Prix -->
                <div class="form-group">
                    <label for="price">Prix</label>
                    <input type="text" class="form-control" id="price" placeholder="Prix de l'article" required>
                </div>

                <!-- Mots-clés -->
                <div class="form-group">
                    <label for="keywords">Mots-clés</label>
                    <input type="text" class="form-control" id="keywords" placeholder="Mots-clés pour l'article" required>
                </div>

                <button type="submit" class="btn btn-primary">Poster l'annonce</button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal to display post details -->
      <div class="modal fade" id="postModal" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="postModalLabel">Post Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="postContent">
                    <h5 id="postTitle" class="post-title"></h5>
                    <p id="postDate" class="post-date"></p>
                    <p id="postBody" class="post-body"></p>
                </div>

                <!-- Form for responding to the post -->
                <div class="response-box">
                    <h6>Your Reply:</h6>
                    <textarea id="responseText" class="form-control" rows="4" placeholder="Type your reply here..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="replyButton">Reply</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal for adding payment method -->
      <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="paymentModalLabel">Add Payment Method</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form>
                <div class="form-group">
                  <label for="cardHolder">Titulaire de la carte</label>
                  <input type="text" class="form-control" id="cardHolder" placeholder="Nom du titulaire" required>
                </div>
                <div class="form-group">
                  <label for="cardNumber">Numéro de la carte</label>
                  <input type="text" class="form-control" id="cardNumber" placeholder="Numéro de carte" maxlength="16" required>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="expirationDate">Date d'expiration</label>
                    <input type="month" class="form-control" id="expirationDate" required>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="cvv">Cryptogramme visuel</label>
                    <input type="text" class="form-control" id="cvv" maxlength="3" placeholder="CVV" required>
                  </div>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Save Payment Method</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal to display message details -->
      <div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="messageModalLabel">Message Details</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div id="messageContent">
                <h5 id="messageSubject"></h5>
                <p id="messageSender"></p>
                <p id="messageDate"></p>
                <p id="messageBody"></p>
              </div>

              <!-- Form for responding to the message -->
              <div class="response-box">
                <h6>Your Reply:</h6>
                <textarea id="responseText" class="form-control" rows="4" placeholder="Type your reply here..."></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" id="replyButton">Reply</button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
  <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
  <!-- Bootstrap JS & jQuery (necessary for Bootstrap components like modals) -->
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    $(function(){
      $(document).on('click', '.product-item', function(e){
        var title = $(this).data('title');
        var description = $(this).data('description');
        var price = $(this).data('price');
        var image = $(this).data('image');
        var date = $(this).data('date');

        $('#productTitle').text(title);
        $('#productPrice').text(price);
        $('#productDescription').text(description);
        $('#productDate').text(date);
        $('#productImage').attr('src', image);
      });

      $('#postProductForm').submit(function(e) {
        e.preventDefault(); // Empêcher la soumission réelle du formulaire
        var data = new FormData($("#postProductForm")[0]);
        console.log(data)
        // $('#postProductModal').modal('hide'); // Fermer le modal après soumission
      });

      $(document).on('click', '.post-item', function(e){
        var title = $(this).data('title');
        var body = $(this).data('body');
        var date = $(this).data('date');

        $('#postTitle').text(title);
        $('#postDate').text('Date: ' + date);
        $('#postBody').text(body);
      });

      $(document).on('blur', '#usernameField', function(e){
        let oldUsername = "<?php echo $_SESSION['user_username'] ?>";
        if (oldUsername != $(this).val().trim()) {
          $("#accountSetting").trigger("submit")
        }
      }); 

      $('#accountSetting').submit(function(e) {
        e.preventDefault(); // Empêcher la soumission réelle du formulaire
        $.post("ajax/userInfo.php", {
          accountSetting: "true", 
          usernameField: $("#usernameField").val()
        }).done(function(result){
          if(result == 1) {
            Swal.fire({
              title: 'Succès',
              text: "Votre nom d'utilisateur a été bien modifié !",
              icon: 'success',
              confirmButtonText: 'OK'
            })
          }
        });
      });
      
      $(document).on('click', '#removeAccount', function(e){
        e.preventDefault(); // Empêcher la soumission réelle du formulaire
        $.post("ajax/userInfo.php", {
          removeAccount: "true"
        }).done(function(result){
          if(result == 1) {
            Swal.fire({
              title: 'Succès',
              text: "Votre compte a été supprimé avec succès !",
              icon: 'success',
              confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'index.php';
                }
            });
          }
        });
      });
      
    })
  </script>
</body>
</html>