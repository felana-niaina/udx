<?php
// Inclusion des classes nécessaires
include_once 'classes/DatabaseConnector.php';
include_once 'classes/UserRegistration.php';
require_once 'constants.php';

// Créer une instance de la classe DatabaseConnector
$database = new DatabaseConnector();
$con = $database->getConnection();
$errorMessage = "";

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
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
        $username = htmlspecialchars(trim($_POST['username'] ?? ''));
        $email = htmlspecialchars(trim($_POST['email'] ?? ''));
        $password = htmlspecialchars(trim($_POST['password'] ?? ''));

        // Vérifier si tous les champs sont remplis
        if (empty($username) || empty($email) || empty($password)) {
            $errorMessage =  "Tous les champs sont obligatoires.";
            exit;
        }

        // Vérifier si l'adresse email est valide
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMessage =  "L'adresse email est invalide.";
            exit;
        }

        // Créer une instance de la classe UserRegistration
        $user = new UserRegistration($con);

        // Inscription de l'utilisateur
        if ($user->registerUser($username, $email, $password)) {
            // Envoi de l'email de confirmation avec PHPMailer
            try {
                $to      = $email;
                $subject = 'Bienvenue sur Underdex !';
                $message = "
                <html>
                <head>
                    <title>Bienvenue sur my site!</title>
                </head>
                <h2>Bienvenue sur Underdex !</h2>
                <p>Vous pouvez désormais interagir avec la communauté !</p>
                <p>
                    - Profiter d’un profil complet et enrichie.<br>
                    - Publier vos posts dans la rubrique Feed.<br>
                    - Vendre des articles sans frais dans la Marketplace.<br>
                    - Echanger par messages avec d’autres membres.
                </p>
                <p>Et plus encore...</p>
                <p>Nous ésperons que notre application vous soit utile et nous vous souhaitons de passer d’agréables moment sur Underdex !</p>
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

                mail($to, $subject, $message, $headers);
                
            } catch (Exception $e) {
                // Si l'envoi échoue, affichage de l'erreur
                $errorMessage = "Le message n'a pas pu être envoyé. Erreur: {$mail->ErrorInfo}";
            }
            
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Succès',
                        text: 'Bienvenue sur Underdex ! remplissez votre profil et faîtes connaissances avec la communauté !  !',
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
    } else {
        $errorMessage = "reCAPTCHA verification failed. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'Inscription</title>
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

        /* Carte du formulaire */
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

        /* Bloc à gauche (si nécessaire) */
        .left-block {
            background-color: #f5f5f5; /* Fond clair pour le bloc à gauche */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            color: #333;
            margin-bottom: 20px;
        }

        .left-block h5 {
            font-size: 18px;
            color: #333;
        }

        .left-block p {
            font-size: 14px;
            color: #6c757d; /* Texte légèrement plus clair pour les paragraphes */
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
    <div class="row">
        <!-- Bloc à gauche -->
        <div class="col-md-4">
            <div class="left-block">
                <h5>S'inscrire gratuitement</h5>
                <p>En vous inscrivant gratuitement sur Underdex, vous profiterez de toutes les fonctionnalités.</p>
				<h5>Recherchez en illimité</h5>
                <p>Contenu associé au titre 3. Vous pouvez y ajouter des informations ou des liens.</p>
				<h5>Publiez sans filtres</h5>
                <p>Avec la Marketplace vendez vos articles en quelques clics et négociez vos prix avec les acheteurs grâce à la messagerie.</p>
                <h5>Vendez sans frais</h5>
                <p>Avec la Marketplace vendez vos articles en quelques clics et négociez vos prix avec les acheteurs grâce à la messagerie.</p>
                <h5>Sécurité renforcée</h5>
                <p>Contenu associé au titre 3. Vous pouvez y ajouter des informations ou des liens.</p>
            </div>
        </div>

        <!-- Formulaire d'inscription -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>S'inscrire gratuitement</h5>
                </div>
                <div class="card-body">
                    <form action="register.php" method="POST">
                        <!-- Nom -->
                        <div class="form-group">
                            <label for="username">Nom d'utilisateur</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Entrez votre nom d'utilisateur" required>
                            <small class="form-text">Votre pseudonyme sera visible auprès des autres utilisateurs.</small>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email">Adresse E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Entrez votre adresse e-mail" required>
                            <small class="form-text">Nous ne partagerons jamais votre e-mail avec d'autres.</small>
                        </div>

                        <!-- Mot de passe -->
                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Créez un mot de passe" required>
                            <small class="form-text">Le mot de passe doit contenir au moins 8 caractères.</small>
                        </div>

                        <!-- Confirmation du mot de passe -->
                        <div class="form-group">
                            <label for="confirmPassword">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirmez votre mot de passe" required>
                            <small class="form-text">Les mots de passe doivent correspondre.</small>
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
                        <!-- Bouton d'inscription -->
                        <button type="submit" class="btn btn-primary btn-block">S'inscrire</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS & jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

</body>
</html>
