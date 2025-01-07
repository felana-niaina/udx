<?php
session_start();

include_once 'classes/DatabaseConnector.php';
include_once 'classes/UserRegistration.php';
include_once 'classes/MarketplaceResultsProvider.php';
include_once 'classes/PostsResultsProvider.php';
include_once 'classes/MessageResultsProvider.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Créer une instance de la classe DatabaseConnector
$database = new DatabaseConnector();
$con = $database->getConnection();
$userRegistration = new UserRegistration($con);
$userIdParam = null;
if(isset($_GET['name']) && trim($_GET['name']) !== '' ) {
    $visitor = $userRegistration->getUserByName(trim($_GET['name']));
    $userIdParam= $visitor['id'];
}

$connectedUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($userIdParam) {
    $userId = $userIdParam;  // Si un userId est passé dans l'URL, on l'utilise
} else {
    $userId = $connectedUserId; // Sinon, on utilise l'ID de l'utilisateur connecté
}

$PostsResultsProvider = new PostsResultsProvider($con);
$messageResult = new MessageResultsProvider($con);
$MarketplaceResultsProvider = new MarketplaceResultsProvider($con);
$userInfo = $userRegistration->getUserInfo($userId);
$coverPhoto = is_null($userInfo['cover_photo']) ? 'uploads/default_cover.jpg' : $userInfo['cover_photo'];
$profilePhoto = is_null($userInfo['profile_photo']) ? 'https://via.placeholder.com/150' : $userInfo['profile_photo'];
$coverPhoto = 'http://'.$_SERVER['SERVER_NAME'] .'/'. $coverPhoto;
$profilePhoto = 'http://'.$_SERVER['SERVER_NAME'] .'/'. $profilePhoto;

$followersCount = $userRegistration->countFollowers($userId);
$points = $userRegistration->calculatePoints($userId);

// check if followed
// Récupérer l'ID du follower et de l'utilisateur suivi
$followedId = $userIdParam;  // ID de l'utilisateur suivi
$followerId = $connectedUserId;  // ID de l'utilisateur connecté (le follower)

$followedUser = $userRegistration->isFollowedUser($followerId, $followedId);

// Déterminer si l'utilisateur est suivi ou non
$isFollowed = ($followedUser !== null); 

// Vérifiez si un fichier est envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier quel formulaire a été soumis
    $formId = $_POST['form_id'] ?? '';

    //update photo de couverture
    if (isset($_FILES['cover_photo']) ) {
        // Appeler la méthode pour mettre à jour la photo
        $coverPhotoFile = $_FILES['cover_photo'];
        $result = $userRegistration->updateCoverPhoto($userId, $coverPhotoFile);

        if ($result) {
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: 'Succès',
                            text: 'Photo de couverture mise à jour !',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'profil.php';
                            }
                        });
                    });
                </script>";
        } else {
            echo "Échec de la mise à jour de la photo de couverture.";
        }
    }

    //update photo de profil
    if (isset($_FILES['profile_photo']) ) {
        // Appeler la méthode pour mettre à jour la photo
        $profilePhotoFile = $_FILES['profile_photo'];
        $result = $userRegistration->updateProfilePhoto($userId, $profilePhotoFile);

        if ($result) {
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: 'Succès',
                            text: 'Photo de profil mise à jour !',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'profil.php';
                            }
                        });
                    });
                </script>";
        } else {
            echo "Échec de la mise à jour de la photo de profil.";
        }
    }

    // Publication d'un nouveau post
    elseif ($formId === 'publishPost') {
        if($userId && $_POST['postTitle'] && $_POST['postDescription'] && $_POST['postCategory']) {
            if ($PostsResultsProvider->createPost($userId,$_POST['postTitle'], $_POST['postDescription'], $_POST['postCategory'])) {
              echo "<script>
                  document.addEventListener('DOMContentLoaded', function() {
                      Swal.fire({
                          title: 'Succès',
                          text: 'Votre post a bien été publié !',
                          icon: 'success',
                          confirmButtonText: 'OK'
                      }).then((result) => {
                          if (result.isConfirmed) {
                              window.location.href = 'profil.php';
                          }
                      });
                  });
              </script>";
            }
        }
    }

    // Publication d'un nouveau produit
    elseif ($formId === 'publishProduct') {
        if($userId && $_POST['productName'] && $_POST['productDescription'] && $_POST['productPrice'] && $_POST['productTags'] && $_POST['productCity'] && $_POST['productCaterogy']) {
            $productPicture = isset($_FILES['productPicture']) ? $_FILES['productPicture'] : NULL;
            if ($MarketplaceResultsProvider->createProduct($userId,$_POST['productName'], $_POST['productDescription'], $_POST['productPrice'], $_POST['productTags'], $_POST['productCity'], $_POST['productCaterogy'], $productPicture)) {
              echo "<script>
                  document.addEventListener('DOMContentLoaded', function() {
                      Swal.fire({
                          title: 'Succès',
                          text: 'Votre produit a bien été publié !',
                          icon: 'success',
                          confirmButtonText: 'OK'
                      }).then((result) => {
                          if (result.isConfirmed) {
                              window.location.href = 'settings.php';
                          }
                      });
                  });
              </script>";
            }
        }
    }

    elseif ($formId === "sendMessage" && $_POST["senderId"] > 0) {
        $toUserId = $_POST['userId'];
        $fromUserId = $_POST["senderId"];
        $message = $_POST["message"];
        $subject = $_POST["subject"];

        $result = $messageResult->sendMessage($fromUserId,$toUserId, $message, $subject);

        if ($result) {
            // Send email
            try {
                $username = $_SESSION['user_username'];
                $message  = "
                <html>
                <head>
                    <title>Nouveau message sur Underdex !</title>
                </head>
                <p>Bonjour, </p>
                <p>L'utilisateur $username vous a envoyé un message sur Underdex !</p>
                <p>Cordialement,<br> Underdex Team</p>
                </body>
                </html>
                ";
                // Set headers for HTML content
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
        
                // Additional headers
                $headers .= "From: udx@underdex.com" . "\r\n";
                $headers .= "Reply-To: udx@underdex.com" . "\r\n";
                $headers .= "X-Mailer: PHP/" . phpversion();
        
                mail($visitor['email'], "Nouveau message sur Underdex !", $message, $headers);
                
            } catch (Exception $e) {
                // Si l'envoi échoue, affichage de l'erreur
                return "Le message n'a pas pu être envoyé. Erreur: {$mail->ErrorInfo}";
            }
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: 'Succès',
                            text: 'Message envoyé !',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            
                        });
                    });
                </script>";
        } else {
            echo "Échec de l'envoie du message.";
        }
    } 

    elseif(isset($_POST['followedId']) && isset($_POST['followerId']) ){
        // Récupérer les données POST
        $followedId = $_POST['followedId'];
        $followerId = $_POST['followerId'];

        try {
            $result = $userRegistration->updateFollowers($followedId, $followerId);
            if ($result) {
                // Send email
                try {
                    $username = $_SESSION['user_username'];
                    $message  = "
                    <html>
                    <head>
                        <title>Nouveau follower sur Underdex !</title>
                    </head>
                    <p>Bonjour, </p>
                    <p>L’utilisateur $username vous suit désormais sur Underdex !</p>
                    <p>Vous avez du succès, félicitations !</p>
                    <p>Cordialement,<br> Underdex Team</p>
                    </body>
                    </html>
                    ";
                    // Set headers for HTML content
                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
            
                    // Additional headers
                    $headers .= "From: udx@underdex.com" . "\r\n";
                    $headers .= "Reply-To: udx@underdex.com" . "\r\n";
                    $headers .= "X-Mailer: PHP/" . phpversion();
            
                    mail($visitor['email'], "Nouveau follower sur Underdex !", $message, $headers);
                    
                } catch (Exception $e) {
                    // Si l'envoi échoue, affichage de l'erreur
                    echo "Le message n'a pas pu être envoyé. Erreur: {$mail->ErrorInfo}";
                }

                $profilName = trim($_GET['name']);
                echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Utilisateur suivi',
                                text: `Félicitations ! Vous commencez à la suivre`,
                                confirmButtonText: 'Fermer'
                            }).then(function() {
                                // Redirection vers le profil de l'utilisateur suivi
                                window.location.href = 'profil.php?name=$profilName';
                            });
                        });
                    </script>";
            } else {
                echo "Érreur survenu.";
            }

        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données : ' . $e->getMessage()]);
        }
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>User Profile - Public View</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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
            background-color: #1c1c1c; /* Fond sombre pour le header */
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
            background-color: #fff; /* Fond blanc pour la carte */
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

        /* Styles de la section profile-header */
        .profile-header {
            position: relative;
            background-color: #f8f9fa;
            padding-top: 60px; /* Ajoute de l'espace pour la photo de profil */
            text-align: center;
        }
        .profile-header .cover-photo {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 200px; /* Hauteur de la photo de couverture */
            background-image: url('https://via.placeholder.com/1200x400'); /* Photo de couverture */
            background-size: cover;
            background-position: center;
            border-radius: 10px 10px 0 0;
        }
        .profile-header img {
            border-radius: 50%;
            width: 250px;
            height: 250px;
            object-fit: cover;
            border: 3px solid #fff;
            position: relative;
            z-index: 1;
            margin-top: -75px; /* Remonte la photo de profil */
        }
        .profile-header h3 {
            margin-top: 15px;
            font-size: 28px;
        }
        .profile-header p {
            font-size: 16px;
            color: #6c757d;
        }
        
        /* Styles des boutons */
        .buttons-container {
            display: flex;
            justify-content: flex-start; /* Aligner les boutons à gauche */
            align-items: center;
            gap: 15px; /* Ajoute de l'espace entre les boutons */
            margin-top: 20px;
        }
        .buttons-container button {
            background-color: #007bff;
            color: white;
            font-size: 16px;
            padding: 10px 15px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .buttons-container button:hover {
            background-color: #0056b3;
        }
        .follow-btn {
            background-color: #28a745;
        }
        .follow-btn:hover {
            background-color: #218838;
        }

        /* Formulaire pour le champ de publication */
        .form-group textarea,
        .form-group input {
            width: 100%;
            border-radius: 5px;
            border: 1px solid #ccc;
            padding: 10px;
        }

        /* Différenciation entre les icônes */
        .icons-container {
            display: flex;
            justify-content: flex-start;
            gap: 15px;
            margin-top: 20px;
        }
        .icon {
            font-size: 24px;
            cursor: pointer;
        }
        .icon:hover {
            color: #007bff;
        }

        .cover-photo {
            width: 100%;
            height: 100px;
            background-size: cover;
            background-position: center;
            position: relative; /* Permet la position absolue des éléments enfants */
            border-radius: 8px;
            overflow: hidden;
        }

        .edit-cover-photo {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            border: none;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            transition: background-color 0.3s;
        }

        .edit-cover-photo i {
            font-size: 18px;
        }

        .edit-cover-photo:hover {
            background-color: rgba(255, 255, 255, 0.8);
            color: black;
        }

        .save-cover-photo-btn {
            position: absolute;
            bottom: 10px;
            right: 70px;
            background-color: rgba(0, 255, 0, 0.7);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            font-size:0.85rem;
        }

        .save-cover-photo-btn:hover {
            background-color: rgba(0, 200, 0, 1);
        }

        .profile-photo-container{
            width: 250px;
            margin-inline: auto;
            position: relative;
        }
        .profile-photo {
            width: 100%;
            background-size: cover;
            background-position: center;
            border-radius: 8px;
            overflow: hidden;
        }

        .edit-profile-photo {
            position: absolute;
            right: 0;
            top:100px;
            z-index:100;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            border: none;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            transition: background-color 0.3s;
        }

        .edit-profile-photo i {
            font-size: 18px;
        }

        .edit-profile-photo:hover {
            background-color: rgba(255, 255, 255, 0.8);
            color: black;
        }

        .save-profile-photo-btn {
            bottom: 10px;
            right: 70px;
            background-color: rgba(0, 255, 0, 0.7);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            margin-top:5px;
            font-size:0.85rem;
        }

        .save-profile-photo-btn:hover {
            background-color: rgba(0, 200, 0, 1);
        }

        /* Style pour les boutons non cliquables */
        .follow-btn:disabled {
            background-color: #ADD8E6; /* Couleur de fond grise pour un bouton désactivé */
            color: white;
            border: 1px solid #ccc;
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

<!-- Header -->
<div class="headerContent">
    <div class="logoContainer">
        <a href="index.php">
            <img src="assets/images/logo.png" alt="Underdex">
        </a>
    </div>
    <div class="searchContainer">
        <!-- Vous pouvez ajouter un champ de recherche ici si nécessaire -->
    </div>
</div>

<div class="container">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="settings.php">Paramètres</a></li>
            <li class="breadcrumb-item active" aria-current="page">Public Profile</li>
        </ol>
    </nav>
    <!-- /Breadcrumb -->

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header profile-header">
                    <!-- Cover photo -->
                    <div class="cover-photo" id="cover-photo" style='background-image: url(<?php echo $coverPhoto ?>);'>
                        <?php if (!$userIdParam || ($userIdParam && $connectedUserId == $userIdParam) ): ?>
                            <!-- Bouton de modification avec icône -->
                            <button class="edit-cover-photo" id="edit-cover-photo-btn" type="button">
                                <i class="fas fa-camera"></i>
                            </button>
                        <?php endif; ?>

                        <!-- Formulaire d'upload de la photo -->
                        <form id="cover-photo-form" action="profil.php" method="POST" enctype="multipart/form-data" style="display: none;">
                            <!-- Image de couverture -->
                            <input type="file" id="cover-photo-input" name="cover_photo" accept="image/*" style="display: none;">
                            <!-- Bouton Enregistrer -->
                            <button type="submit" id="save-cover-photo-btn" class="save-cover-photo-btn">Enregistrer</button>
                        </form>
                    </div>

                    <!-- Profile picture -->
                    <div class="profile-photo-container">
                        <img class="profile-photo" id="profile-photo" src="<?php echo $profilePhoto; ?>">
                        <?php if (!$userIdParam || ($userIdParam && $connectedUserId == $userIdParam) ): ?>
                            <!-- Bouton de modification avec icône -->
                            <button class="edit-profile-photo" id="edit-profile-photo-btn" type="button">
                                <i class="fas fa-camera"></i>
                            </button>
                        <?php endif; ?>

                        <!-- Formulaire d'upload de la photo -->
                        <form id="profile-photo-form" action="profil.php" method="POST" enctype="multipart/form-data" style="display: none;">
                            <!-- Image de profil -->
                            <input type="file" id="profile-photo-input" name="profile_photo" accept="image/*" style="display: none;">
                            <!-- Bouton Enregistrer -->
                            <button type="submit" id="save-profile-photo-btn" class="save-profile-photo-btn">Enregistrer</button>
                        </form>
                    </div>
                    <!-- <img src="https://via.placeholder.com/150"> -->
                    <h3><?php echo $userInfo['username']; ?></h3><br>
                    <p><?php echo $userInfo['profileTitle']; ?></p>

                    <!-- Buttons Container (Send Message & Follow) -->
                    <div class="buttons-container">
                        <?php if ($userIdParam && $connectedUserId != $userIdParam) { ?>
                            <?php if( isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0) { ?>
                                <button data-toggle="modal" data-target="#messageModal"><i class="fas fa-envelope"></i> Send Message</button>
                            <?php } ?>
                                <button 
                                class="follow-btn" 
                                onclick="handleFollowClick(this)" 
                                data-followed-id="<?php echo $userIdParam; ?>" 
                                data-follower-id="<?php echo $connectedUserId; ?>"
                                <?php echo $isFollowed ? 'disabled' : ''; ?>>
                                <i class="fas fa-user-plus"></i>
                                <?php echo $isFollowed ? 'Already Following' : 'Follow'; ?>
                            </button>                        
                        <?php } ?>
                        
                        <?php if (!$userIdParam || ($userIdParam && $connectedUserId == $userIdParam) ): ?>
                            <!-- New Buttons for Post and Sell -->
                            <button id="post-btn" class="btn btn-warning" onclick="showPostForm()"><i class="fas fa-comment"></i> Publier</button>
                            <button id="sell-btn" class="btn btn-success" onclick="showSaleForm()"><i class="fas fa-tags"></i> Vendre</button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Formulaires pour Poster un message et Vendre un article juste avant la Biographie -->
                <div id="post-form" class="form-group" style="display:none;">
                    <h4>Publier un post</h4>
                    <form action="profil.php" method="post">
                        <input type="hidden" name="form_id" value="publishPost">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Titre du post" id="post-title" name="postTitle" required>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" rows="4" placeholder="Description du post" name="postDescription" required></textarea>
                        </div>
                        <div class="form-group row">
                            <label for="postCategory" class="col-sm-2 col-form-label">Catégorie du post</label>
                            <div class="col-sm-10">
                                <select id="postCategory" class="form-control" name="postCategory" required>
                                    <?php foreach ($PostsResultsProvider->getPostCategories() as $key => $value) { ?>
                                        <option value="<?php echo $value['id'] ?>"><?php echo $value['title'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-2">Publier</button>
                    </form>
                </div>

                <div id="sale-form" class="form-group" style="display:none;">
                    <h4>Vendre un produit</h4>
                    <form action="profil.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="form_id" value="publishProduct">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Titre du produit" name="productName" required>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control mt-2" rows="4" placeholder="Description du produit" name="productDescription"  required></textarea>
                        </div>
                        <div class="form-group">
                            <input type="number" class="form-control mt-2" placeholder="Prix" name="productPrice"  required>
                        </div>
                        <!-- Ville -->
                        <div class="form-group">
                            <input type="text" class="form-control" id="ville" placeholder="Ville" name="productCity" required>
                        </div>
                        
                        <!-- Category -->
                        <select class="form-control" id="productCaterogy" name="productCaterogy" required>
                        <option value="" disabled selected>-- Select an option --</option>
                        <?php foreach ($MarketplaceResultsProvider->getCategoryList() as $key => $value) { ?>
                            <option value="<?php echo $value['id'] ?>"><?php echo $value['title'] ?></option>
                        <?php } ?>
                        </select>

                        <div class="form-group">
                            <input type="text" class="form-control mt-2" placeholder="Tags" name="productTags"  required>
                        </div>
                        <div class="form-group">
                            <input type="file" class="form-control-file mt-2" name="productPicture"  accept="image/*" required>
                        </div>
                        <button class="btn btn-success mt-2">Publier</button>
                    </form>
                </div>

                <div class="card-body profile-info">
                    <div class="info-item">
                        <h6>Biography</h6>
                        <p><?php echo $userInfo['bio']; ?></p>
                    </div>
                    <div class="info-item">
                        <h6>Location</h6>
                        <p><?php echo $userInfo['location']; ?></p>
                    </div>
                    <div class="info-item">
                        <h6>Website</h6>
                        <p><a href="<?php echo $userInfo['url']; ?>" target="_blank"><?php echo $userInfo['url']; ?></a></p>
                    </div>

                    <!-- Followers Section -->
                    <div class="followers">
                        <p><span>Followers:</span> <?php echo number_format($followersCount); ?></p>
                        <?php if (!$userIdParam): ?>
                            <p><span>Points:</span><?php echo number_format($points); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal for sending message -->
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="messageModalLabel">Send a Message</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post">
            <input type="hidden" name="form_id" value="sendMessage">
            <input type="hidden" name="userId" id="userId" value="<?php echo $userIdParam ?>" >
            <input type="hidden" name="senderId" id="senderId" value="<?php echo $connectedUserId ?>">
            <div class="form-group">
                <label for="subject" class="form-label">Objet du message</label>
                <input type="text" class="form-control" id="subject" name="subject" require/>
            </div>
            <div class="form-group">
                <label for="message-text" class="col-form-label">Your Message:</label>
                <textarea class="form-control" id="message-text" rows="4" name="message" placeholder="Type your message here..." required></textarea>
            </div>
            <div class="form-group text-right">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Send Message</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.min.js"></script>
    <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if( !isset($_SESSION['user_id']) || $_SESSION['user_id'] < 0) { ?>
    <script>
        $(document).ready(function() {
            Swal.fire({
                text: 'Inscrivez-vous pour avoir accès à son profil.',
                icon: 'warning',
                showCloseButton: false, // Ajouter un bouton
                showCancelButton: true,
                confirmButtonText: 'Se connecter',
                cancelButtonText: 'S\'inscrire',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'login.php';
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    window.location.href = 'register.php';
                }
            });
        });
    </script>
<?php } ?>

<script>
    function showPostForm() {
        document.getElementById('post-form').style.display = 'block';
        document.getElementById('sale-form').style.display = 'none';
    }

    function showSaleForm() {
        document.getElementById('post-form').style.display = 'none';
        document.getElementById('sale-form').style.display = 'block';
    }
    
    // Lorsque l'utilisateur clique sur le bouton d'édition de la photo de couverture
    document.getElementById("edit-cover-photo-btn").addEventListener("click", function () {
        // Affiche l'input de téléchargement de photo
        document.getElementById("cover-photo-input").click();
    });

    // Lorsque l'utilisateur clique sur le bouton d'édition de la photo de profil
    document.getElementById("edit-profile-photo-btn").addEventListener("click", function () {
        // Affiche l'input de téléchargement de photo
        document.getElementById("profile-photo-input").click();
    });

    // Lorsque l'utilisateur sélectionne une nouvelle image de couverture
    document.getElementById("cover-photo-input").addEventListener("change", function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById("cover-photo").style.backgroundImage = `url(${e.target.result})`;
                // Afficher le bouton Enregistrer
                document.getElementById("save-cover-photo-btn").style.display = "inline-block";
                document.getElementById("cover-photo-form").style.display = "block";
            };
            reader.readAsDataURL(file);
        }
        document.getElementById("cover-photo-form").style.display = "block";
    });

    // Lorsque l'utilisateur sélectionne une nouvelle image de profil
    document.getElementById("profile-photo-input").addEventListener("change", function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById("profile-photo").src = e.target.result;
                // Afficher le bouton Enregistrer
                document.getElementById("save-profile-photo-btn").style.display = "inline-block";
                document.getElementById("profile-photo-form").style.display = "block";
            };
            reader.readAsDataURL(file);
        }
        document.getElementById("profile-photo-form").style.display = "block";
    });

    function handleFollowClick(button) {
        // Récupérer les IDs des utilisateurs depuis les attributs data
        const followedId = button.dataset.followedId;
        const followerId = button.dataset.followerId;
        const profilName = '<?php echo $_GET["name"] ?? "" ?>';

        // Assurer que les données sont présentes
        if (!followedId || !followerId) {
            alert("Erreur : Impossible de suivre cet utilisateur.");
            return;
        }

        // Création d'un formulaire dynamique et soumettez les données via POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `profil.php?name=${profilName}`; // L'action vers laquelle les données seront envoyées
        // Ajout des champs à la requête
        const inputFollowedId = document.createElement('input');
        inputFollowedId.type = 'hidden';
        inputFollowedId.name = 'followedId';
        inputFollowedId.value = followedId;

        const inputFollowerId = document.createElement('input');
        inputFollowerId.type = 'hidden';
        inputFollowerId.name = 'followerId';
        inputFollowerId.value = followerId;

        form.appendChild(inputFollowedId);
        form.appendChild(inputFollowerId);

        // Ajout du formulaire au body et puis soumission
        document.body.appendChild(form);
        form.submit();

    }
    
</script>

</body>
</html>
