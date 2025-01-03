<?php
session_start(); // Utilisé pour gérer les sessions utilisateur
// Inclusion des classes nécessaires
include_once 'classes/DatabaseConnector.php';
include_once 'classes/UserRegistration.php';
require_once 'constants.php';

// Créer une instance de la classe DatabaseConnector
$database = new DatabaseConnector();
$con = $database->getConnection();

$userRegistration = new UserRegistration($con);
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Valider les entrées utilisateur
    if (empty($username) || empty($password)) {
        $errorMessage = "Nom d'utilisateur et mot de passe requis.";
    } else {

        $secretKey = SECRET_KEY;
        $responseKey = $_POST['g-recaptcha-response'];
        $userIP = $_SERVER['REMOTE_ADDR'];

        // Verify reCAPTCHA response
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $data = [
            'secret' => $secretKey,
            'response' => $responseKey,
            'remoteip' => $userIP
        ];

        // Make the POST request to Google's API
        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $response = json_decode($result);

        if ($response->success) {
            try {
                // Appeler la méthode de connexion
                if ($userRegistration->loginUser($username, $password)) {
                    // Redirection en cas de succès
                    header('Location: settings.php');
                    exit;
                } else {
                    // Erreur d'authentification
                    $errorMessage = "Nom d'utilisateur ou mot de passe incorrect.";
                }
            } catch (PDOException $e) {
                $errorMessage = "Erreur de base de données : " . $e->getMessage();
            }
        } else {
            $errorMessage = "reCAPTCHA verification failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style type="text/css">
        /* Corps de la page */
        body {
            background-color: #f5f5f5; /* Fond clair pour toute la page */
            color: #333; /* Texte sombre pour contraster sur fond clair */
            font-family: 'Arial', sans-serif;
            margin: 0;
        }

        /* Conteneur principal */
        .container {
            max-width: 600px;
            margin-top: 100px;
            padding: 20px;
        }

        /* En-tête */
        .headerContent {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background-color: #1c1c1c; /* Fond sombre pour l'entête */
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

        /* Carte du formulaire */
        .card {
            background-color: #fff; /* Fond clair pour la carte du formulaire */
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

        .form-group label {
            font-weight: bold;
            color: #333; /* Texte sombre pour les labels */
        }

        .btn-primary {
            background-color: #333333; /* Bouton sombre */
            border-color: #333333;
            color: #fff; /* Texte du bouton en blanc */
        }

        .btn-primary:hover {
            background-color: #555555; /* Changement de couleur lors du survol */
            border-color: #555555;
        }

        .form-text {
            font-size: 14px;
            color: #6c757d; /* Texte gris clair pour les descriptions */
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

        .cgu-text {
            font-size: 14px;
            color: #007bff;
        }

        .cgu-text:hover {
            text-decoration: underline;
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

<!-- Conteneur principal -->
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5>Se connecter</h5>
        </div>
        <div class="card-body">
            <form action="#" method="POST">
                <!-- Nom d'utilisateur -->
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" class="form-control" id="username" placeholder="Entrez votre nom d'utilisateur" name="username" required>
                </div>

                <!-- Mot de passe -->
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" class="form-control" id="password" placeholder="Entrez votre mot de passe" name="password" required>
                </div>

                <div class="form-group text-center">
                    <div class="g-recaptcha" data-sitekey="<?php echo SITE_KEY ?>"></div>
                </div>
                <?php if( trim($errorMessage) !== "" ) { ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $errorMessage; ?>
                </div>
                <?php } ?>
                <!-- Case à cocher pour accepter les CGU et CGV -->
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="acceptCGU" required>
                    <label class="form-check-label" for="acceptCGU">
                        En cochant cette case, j'accepte les <a href="#" class="cgu-text">CGU</a> et les <a href="#" class="cgu-text">CGV</a>.
                    </label>
                </div>
				<br>

                <!-- Bouton de connexion -->
                <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS & jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>
