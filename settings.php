<?php
  session_start();

  // Inclusion des classes nécessaires
  include_once 'classes/DatabaseConnector.php';
  include_once 'classes/UserRegistration.php';

  // Créer une instance de la classe DatabaseConnector
  $database = new DatabaseConnector();
  $con = $database->getConnection();

  // Vérifier si l'utilisateur est connecté
  if (!isset($_SESSION['user_id'])) {
      // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
      header('Location: login.html');
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
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item"><a href="profil.html">Profil public</a></li>
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
				 <a href="#message" data-toggle="tab" class="nav-item nav-link has-icon nav-link-faded">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message mr-2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>Marketplace
                </a>
				 <a href="#message" data-toggle="tab" class="nav-item nav-link has-icon nav-link-faded">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message mr-2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>Mes posts
                </a>
                <a href="#message" data-toggle="tab" class="nav-item nav-link has-icon nav-link-faded">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message mr-2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>Messagerie <span class="badge badge-danger">10</span>
                </a>
                <a href="#security" data-toggle="tab" class="nav-item nav-link has-icon nav-link-faded">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shield mr-2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>Security
                </a>
                <a href="#notification" data-toggle="tab" class="nav-item nav-link has-icon nav-link-faded">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell mr-2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>Notification <span class="badge badge-danger">3</span>
                </a>
                <a href="#billing" data-toggle="tab" class="nav-item nav-link has-icon nav-link-faded">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-credit-card mr-2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>Moyen de paiement
                </a>
				<a href="#pub" data-toggle="tab" class="nav-item nav-link has-icon nav-link-faded">
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
                <form>
                  <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" aria-describedby="usernameHelp" placeholder="Enter your username" value="kennethvaldez">
                    <small id="usernameHelp" class="form-text text-muted">After changing your username, your old username becomes available for anyone else to claim.</small>
                  </div>
                  <hr>
                  <div class="form-group">
                    <label class="d-block text-danger">Delete Account</label>
                    <p class="text-muted font-size-sm">Once you delete your account, there is no going back. Please be certain.</p>
                  </div>
                  <button class="btn btn-danger" type="button">Delete Account</button>
                </form>
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
		<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Notifications</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Style pour les notifications en haut */
        .notification-header {
            background-color: #ffffff;
            border-bottom: 1px solid #ddd;
        }

        .notification-header .nav-pills .nav-link {
            font-size: 16px;
            font-weight: 600;
        }

        .notification-header .nav-pills .nav-link.active {
            background-color: #1c1c1c; /* Nouveau bleu */
            color: white;
        }

        .tab-pane {
            margin-top: 20px;
        }

        /* Style des notifications simulées */
        .notification-item {
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }

        .notification-item:hover {
            background-color: #f1f1f1;
        }

        .notification-item .user {
            font-weight: bold;
        }

        .notification-item .action {
            color: #1c1c1c; /* Nouveau bleu pour l'action */
        }

        .notification-item .date {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<!-- Notifications en haut -->
<div class="notification-header py-3">
    <div class="container">
        <div class="row">
            <div class="col">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link active" id="likes-tab" data-toggle="pill" href="#likes">Likes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="comments-tab" data-toggle="pill" href="#comments">Commentaires</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="messages-tab" data-toggle="pill" href="#messages">Messages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="followers-tab" data-toggle="pill" href="#followers">Followers</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Contenu des notifications -->
<div class="container mt-5">
    <div class="tab-content">
        <!-- Likes -->
        <div class="tab-pane" id="likes">
            <h6>Notifications de Likes</h6>
            <hr>
            <div class="notification-item">
                <span class="user">Alice</span> a aimé votre <span class="action">photo de profil</span>.
                <div class="date">12/12/2024 14:30</div>
            </div>
            <div class="notification-item">
                <span class="user">Bob</span> a aimé votre <span class="action">publication</span>.
                <div class="date">12/12/2024 13:45</div>
            </div>
            <div class="notification-item">
                <span class="user">Claire</span> a aimé votre <span class="action">commentaire</span>.
                <div class="date">12/12/2024 13:00</div>
            </div>
        </div>

        <!-- Commentaires -->
        <div class="tab-pane" id="comments">
            <h6>Notifications de Commentaires</h6>
            <hr>
            <div class="notification-item">
                <span class="user">David</span> a commenté votre <span class="action">post sur la technologie</span>.
                <div class="date">11/12/2024 16:10</div>
            </div>
            <div class="notification-item">
                <span class="user">Eva</span> a commenté votre <span class="action">photo de vacances</span>.
                <div class="date">11/12/2024 15:55</div>
            </div>
            <div class="notification-item">
                <span class="user">Franck</span> a commenté votre <span class="action">publication sur les livres</span>.
                <div class="date">11/12/2024 14:25</div>
            </div>
        </div>

        <!-- Messages -->
        <div class="tab-pane" id="messages">
            <h6>Notifications de Messages</h6>
            <hr>
            <div class="notification-item">
                <span class="user">Georges</span> vous a envoyé un message : <span class="action">"Salut, comment ça va ?"</span>.
                <div class="date">10/12/2024 18:40</div>
            </div>
            <div class="notification-item">
                <span class="user">Hélène</span> vous a envoyé un message : <span class="action">"Tu veux sortir ce soir ?"</span>.
                <div class="date">10/12/2024 17:30</div>
            </div>
            <div class="notification-item">
                <span class="user">Isabelle</span> vous a envoyé un message : <span class="action">"J'ai une question sur le projet."</span>.
                <div class="date">10/12/2024 16:20</div>
            </div>
        </div>

        <!-- Followers -->
        <div class="tab-pane" id="followers">
            <h6>Notifications de Followers</h6>
            <hr>
            <div class="notification-item">
                <span class="user">Jack</span> vous suit maintenant.
                <div class="date">09/12/2024 19:00</div>
            </div>
            <div class="notification-item">
                <span class="user">Kim</span> vous suit maintenant.
                <div class="date">09/12/2024 18:15</div>
            </div>
            <div class="notification-item">
                <span class="user">Liam</span> vous suit maintenant.
                <div class="date">09/12/2024 17:50</div>
            </div>
        </div>
    </div>
</div>

<!-- Script JavaScript pour Bootstrap -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

              <div class="tab-pane" id="notification">
                <h6>NOTIFICATION SETTINGS</h6>
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
                <h6>BILLING SETTINGS</h6>
                <hr>
                <form>
<div class="form-group">
    <label class="d-block mb-0">Payment Method</label>
    <div class="small text-muted mb-3">You have not added a payment method</div>
    <button class="btn btn-info" type="button" data-toggle="modal" data-target="#paymentModal">Ajouter une carte bancaire</button>
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

                  </div>
                  <div class="form-group mb-0">
                    <label class="d-block">Payment History</label>
                    <div class="border border-gray-500 bg-gray-200 p-3 text-center font-size-sm">You have not made any payment.</div>
                  </div>
                </form>
              </div>
			                <div class="tab-pane active" id="pub">
                <h6>Publicité</h6>
                <hr>
                <form>
                  <input type="hidden" name="form_id" value="update_pub">
                  <div class="form-group">
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
                  <div class="form-group">
                      <label for="dailyBudget">Durée</label>
                      <select class="form-control" id="dailyBudget">
                          <option value="10">1 jour</option>
                          <option value="20">2 jours</option>
                          <option value="50">3 jours</option>
                          <option value="100">4 jours</option>
                          <option value="10">5 jour</option>
                          <option value="20">6 jours</option>
                          <option value="50">7 jours</option>
                          <option value="100">8 jours</option>
                          <option value="10">9 jour</option>
                          <option value="20">10 jours</option>
                          <option value="50">11 jours</option>
                          <option value="100">12 jours</option>
                          <option value="10">13 jour</option>
                          <option value="20">14 jours</option>
                          <option value="50">15 jours</option>
                          <option value="100">16 jours</option>
                          <option value="10">17 jour</option>
                          <option value="20">18 jours</option>
                          <option value="50">19 jours</option>
                          <option value="100">20 jours</option>
                          <option value="10">21 jour</option>
                          <option value="20">22 jours</option>
                          <option value="50">23 jours</option>
                          <option value="100">24 jours</option>
                          <option value="10">25 jour</option>
                          <option value="20">26 jours</option>
                          <option value="50">27 jours</option>
                          <option value="100">28 jours</option>
                          <option value="50">29 jours</option>
                          <option value="100">30 jours</option>
                      </select>
                      <small id="dailyBudgetHelp" class="form-text text-muted">Sélectionnez votre budget quotidien ou choisissez "Autre" pour entrer un budget personnalisé.</small>
                  </div>
                  <div class="form-group small text-muted">
                    All of the fields on this page are optional and can be deleted at any time, and by filling them out, you're giving us consent to share this data wherever your user profile appears.
                  </div>
                  <button type="button" class="btn btn-primary">Valider</button>
                  <button type="reset" class="btn btn-light" name="reset_changes">Reset Changes</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<!-- Bootstrap JS & jQuery (necessary for Bootstrap components like modals) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">

</script>
</body>
</html>