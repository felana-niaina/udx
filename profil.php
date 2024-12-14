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
                    <div class="cover-photo"></div>

                    <!-- Profile picture -->
                    <img src="https://via.placeholder.com/150" alt="User Profile Picture">
                    <h3>Pseudo</h3><br>
                    <p>A front-end developer focused on user interface design</p>

                    <!-- Buttons Container (Send Message & Follow) -->
                    <div class="buttons-container">
                        <button data-toggle="modal" data-target="#messageModal"><i class="fas fa-envelope"></i> Send Message</button>
                        <button class="follow-btn"><i class="fas fa-user-plus"></i> Follow</button>

                        <!-- New Buttons for Post and Sell -->
                        <button id="post-btn" class="btn btn-warning" onclick="showPostForm()"><i class="fas fa-comment"></i> Publier</button>
                        <button id="sell-btn" class="btn btn-success" onclick="showSaleForm()"><i class="fas fa-tags"></i> Vendre</button>
                    </div>
                </div>

                <!-- Formulaires pour Poster un message et Vendre un article juste avant la Biographie -->
                <div id="post-form" class="form-group" style="display:none;">
                    <h4>Publier un post</h4>
					 <input type="text" class="form-control" placeholder="Titre du post" id="post-title">
                    <textarea class="form-control" rows="4" placeholder="Description du post"></textarea>
                    <button class="btn btn-primary mt-2">Publier</button>
                </div>

                <div id="sale-form" class="form-group" style="display:none;">
                    <h4>Vendre un produit</h4>
                    <input type="text" class="form-control" placeholder="Titre du produit" id="item-title">
                    <textarea class="form-control mt-2" rows="4" placeholder="Description du produit" id="item-description"></textarea>
                    <input type="number" class="form-control mt-2" id="item-price" placeholder="Prix">
                    <input type="file" class="form-control-file mt-2" id="item-image">
                    <button class="btn btn-success mt-2">Publier</button>
                </div>

                <div class="card-body profile-info">
                    <div class="info-item">
                        <h6>Biography</h6>
                        <p>A front-end developer who specializes in creating engaging user interfaces and seamless user experiences. Passionate about building intuitive and accessible web applications.</p>
                    </div>
                    <div class="info-item">
                        <h6>Location</h6>
                        <p>Bay Area, San Francisco, CA</p>
                    </div>
                    <div class="info-item">
                        <h6>Website</h6>
                        <p><a href="http://benije.ke/pozzivkij" target="_blank">benije.ke/pozzivkij</a></p>
                    </div>

                    <!-- Followers Section -->
                    <div class="followers">
                        <p><span>Followers:</span> 1,024</p>
                        <p><span>Points:</span> 8,024</p>
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
        <form>
          <div class="form-group">
            <label for="message-text" class="col-form-label">Your Message:</label>
            <textarea class="form-control" id="message-text" rows="4" placeholder="Type your message here..."></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Send Message</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function showPostForm() {
        document.getElementById('post-form').style.display = 'block';
        document.getElementById('sale-form').style.display = 'none';
    }

    function showSaleForm() {
        document.getElementById('post-form').style.display = 'none';
        document.getElementById('sale-form').style.display = 'block';
    }
</script>

</body>
</html>