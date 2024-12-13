<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foire Aux Questions (FAQ)</title>
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

        /* Carte du contenu */
        .card {
            background-color: #fff; /* Fond clair pour la carte */
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 20px;
            color: #333; /* Texte sombre dans la carte */
        }

        .card-header {
            background-color: #ffffff; /* Fond blanc pour le header de la carte */
            color: #000000;  /* Texte en noir pour le header */
            font-size: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            padding: 10px 0;
        }

        .card-body h5 {
            color: #333;
            font-size: 22px;
            margin-bottom: 20px;
        }

        .card-body p {
            font-size: 16px;
            color: #292929; /* Texte légèrement plus clair pour les paragraphes */
            line-height: 1.6;
        }

        .btn-link {
            font-size: 18px;
            color: #000000;
            text-decoration: none;
        }

        .btn-link:hover {
            color: #5b5b5b;
            text-decoration: underline;
        }

        /* Espacement entre les blocs en haut et en bas */
        .spaced-blocks {
            margin-bottom: 40px; /* Ajoute de l'espace entre les blocs en haut et en bas */
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

<!-- Conteneur principal -->
<div class="container">
    <div class="row">
        <!-- Bloc 1: Informations générales -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Informations Générales</h5>
                </div>
                <div class="card-body">
                    <h5><a href="#faq1" class="btn btn-link" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="faq1">Qu'est-ce qu'Underdex ?</a></h5>
                    <div class="collapse" id="faq1">
                        <p>Underdex est une plateforme en ligne dédiée à l'achat et à la vente de produits d'occasion. Nous mettons en relation des utilisateurs à travers une interface simple et sécurisée, permettant de négocier des prix et de réaliser des transactions en toute confiance.</p>
                    </div>

                    <h5><a href="#faq2" class="btn btn-link" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="faq2">Comment puis-je m'inscrire sur Underdex ?</a></h5>
                    <div class="collapse" id="faq2">
                        <p>Pour vous inscrire, il vous suffit de cliquer sur le bouton "S'inscrire" en haut de la page. Ensuite, remplissez le formulaire avec vos informations personnelles, telles que votre nom, votre e-mail et un mot de passe sécurisé.</p>
                    </div>

                    <h5><a href="#faq3" class="btn btn-link" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="faq3">Est-ce que l'inscription est gratuite ?</a></h5>
                    <div class="collapse" id="faq3">
                        <p>Oui, l'inscription sur Underdex est entièrement gratuite. Vous pouvez créer un compte sans frais et accéder à toutes les fonctionnalités de la plateforme.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bloc 2: Vendre et acheter sur Underdex -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Vendre et Acheter</h5>
                </div>
                <div class="card-body">
                    <h5><a href="#faq4" class="btn btn-link" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="faq4">Comment puis-je vendre un produit sur Underdex ?</a></h5>
                    <div class="collapse" id="faq4">
                        <p>Pour vendre un produit, il vous suffit de vous connecter à votre compte, puis de cliquer sur l'option "Vendre". Remplissez les informations du produit (description, prix, photos), et votre annonce sera mise en ligne.</p>
                    </div>

                    <h5><a href="#faq5" class="btn btn-link" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="faq5">Puis-je négocier le prix avec l'acheteur ?</a></h5>
                    <div class="collapse" id="faq5">
                        <p>Oui, Underdex permet aux acheteurs et aux vendeurs de discuter du prix via une messagerie interne. Vous pouvez proposer une offre et négocier directement avec l'acheteur avant de finaliser la vente.</p>
                    </div>

                    <h5><a href="#faq6" class="btn btn-link" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="faq6">Comment effectuer un paiement sécurisé ?</a></h5>
                    <div class="collapse" id="faq6">
                        <p>Nous proposons des méthodes de paiement sécurisées, telles que PayPal, pour assurer que vos transactions sont protégées et sans risque.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Espacement entre les blocs en haut et en bas -->
    <div class="spaced-blocks"></div>

    <div class="row">
        <!-- Bloc 3: Sécurité et confidentialité -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Sécurité et Confidentialité</h5>
                </div>
                <div class="card-body">
                    <h5><a href="#faq7" class="btn btn-link" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="faq7">Est-ce que mes informations sont sécurisées sur Underdex ?</a></h5>
                    <div class="collapse" id="faq7">
                        <p>Oui, nous prenons la sécurité de vos informations très au sérieux. Nous utilisons des technologies de cryptage avancées pour protéger vos données personnelles et vos transactions.</p>
                    </div>

                    <h5><a href="#faq8" class="btn btn-link" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="faq8">Que faire si j'oublie mon mot de passe ?</a></h5>
                    <div class="collapse" id="faq8">
                        <p>Si vous oubliez votre mot de passe, il vous suffit de cliquer sur "Mot de passe oublié ?" lors de la connexion. Vous recevrez un lien pour réinitialiser votre mot de passe par email.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bloc 4: Support et contact -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Support et Contact</h5>
                </div>
                <div class="card-body">
                    <h5><a href="#faq9" class="btn btn-link" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="faq9">Comment contacter le support ?</a></h5>
                    <div class="collapse" id="faq9">
                        <p>Si vous avez besoin d'assistance, vous pouvez nous contacter par email à support@underdex.com. Nous nous efforcerons de répondre dans les plus brefs délais.</p>
                    </div>

                    <h5><a href="#faq10" class="btn btn-link" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="faq10">Puis-je modifier mes informations personnelles ?</a></h5>
                    <div class="collapse" id="faq10">
                        <p>Oui, vous pouvez modifier vos informations personnelles à tout moment en accédant à votre profil et en cliquant sur "Modifier mes informations".</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS & jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
