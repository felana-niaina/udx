<?php
    include_once 'classes/DatabaseConnector.php';
    include("classes/SiteResultsProvider.php");
    include("classes/MessageResultsProvider.php");
    include("classes/MarketplaceResultsProvider.php"); 
    include("classes/PostsResultsProvider.php"); 
    include("classes/ImageResultsProvider.php");

    // Créer une instance de la classe DatabaseConnector
    $database = new DatabaseConnector();
    $con = $database->getConnection();

    $postsResults = new PostsResultsProvider($con);
    $messageResult = new MessageResultsProvider($con);
    
    $isUserConnected = false;
    session_start();
    if(!empty($_SESSION)) {
        $isUserConnected = true;
    };

    $postId = $userId = $commentText = null;
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $formPost = isset($_POST["marketplace_id"]) ? $_POST["marketplace_id"] : false;
        if($formPost === "sendMessage")
        {
            $toUserId = $_POST['userId'];
            $fromUserId = $_POST["senderId"];
            $message = $_POST["message"];
            $subject = $_POST["subject"];

            $result = $messageResult->sendMessage($fromUserId,$toUserId, $message, $subject);

            if ($result) {
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
        
    }

    
    if(isset($_GET["term"])){
        $term = $_GET["term"];
    }else{
        exit("please enter a search term > 0");
    }

    $type = isset($_GET["type"]) ? $_GET["type"] : "sites";
    $page = isset($_GET["page"]) ? $_GET["page"] : 1;

?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Underdex</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/style.css?A">
    <style type="text/css">
        .profile-photo {
            width: 50px;
            height:50px;
            background-size: cover;
            background-position: center;
            border-radius: 100%;
            overflow: hidden;
        }

        .text {
            margin-left: 20px;
        }

        .comment-area {
            margin-top: 10px;
            display: none; /* Masque le champ initialement */
        }
       .badge-success{
            font-size: 0.7rem;
            background-color: green;
            margin-left: 10px;
        }
        .marketResult{
            padding-bottom: 15px;
        }
        .title{
            font-size: 1.4rem;
        }
        .description{
            font-size: 0.9rem;
        }
        .price{
            font-size: 0.8rem;
        }
        .btn{
            margin-top: -5px;
        }
        .comment-list{
            margin-bottom: 10px;
        }
        .connexionInfo{
            padding-top: 5px;
            padding-left: 5px;
        }
        button.liked i {
            color: #1E90FF; /* Change la couleur en rouge pour "bleu" */
        }

        button i {
            color: #555; /* Par défaut, couleur gris clair */
        }
        
        .liked {
            color: #1E90FF;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>

</head>
<body>
    <div class="wrapper">
        <div class="header">
            <div class="headerContent">
                <div class="logoContainer">
                    <a href="index.php">
                        <img src="assets/images/logo.png" alt="Underdex">
                    </a>
                </div>
                <div class="searchContainer">
                    <form action="search.php" method="get">
                        <div class="searchBarContainer">
                            <input type="hidden" name="type" value="<?php echo $type; ?>">
                            <input type="text" class="searchBox" name="term" value="<?php echo $term; ?>">
                        </div>
                    </form>
                </div>
            </div>
            <div class="tabsContainer">
                <ul class="tabList">
                    <li class="<?php echo $type == 'sites' ? 'active' : '' ?>">
                        <a href="<?php echo "search.php?term=$term&type=sites"; ?>">
                            Web
                        </a>
                    </li>
					<li class="<?php echo $type == 'posts' ? 'active' : '' ?>">
                        <a href="<?php echo "search.php?term=$term&type=posts"; ?>">
                            Posts
                        </a>
                    </li>
                    <li class="<?php echo $type == 'marketplace' ? 'active' : '' ?>">
                        <a href="<?php echo "search.php?term=$term&type=marketplace"; ?>">
                            Marketplace
                        </a>
                    </li>
                    <li class="<?php echo $type == 'images' ? 'active' : '' ?>">
                        <a href="<?php echo "search.php?term=$term&type=images"; ?>">
                            Actualités
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="mainResultsSection">
            <?php
                // Modifier cette partie pour utiliser la classe AnnoncesResultsProvider
                if ($type == "sites") {
                    $resultsProvider = new SiteResultsProvider($con);
                    $pageLimit = 20;
                } elseif ($type == "marketplace") {
                    // Utilisation d'AnnoncesResultsProvider pour le type 'marketplace'
                    $resultsProvider = new MarketplaceResultsProvider($con);
                    $pageLimit = 30;
                } else {
                    $resultsProvider = new PostsResultsProvider($con);
                    $pageLimit = 30;
                }

                $numResults = $resultsProvider->getNumResults($term);
                echo "<p class='resultsCount'>About $numResults results</p>";
                echo $resultsProvider->getResultsHtml($page, $pageLimit, $term);
            ?>
        </div>
        <div class="paginationContainer">
            <div class="pageButtons">
                <div class="pageNumberContainer">
                    <img src="assets/images/pageStart.png">
                </div>
                <?php
                    $pagesToShow = 10;
                    $numPages = ceil($numResults / $pageLimit); // pageLimit utilisé ici au lieu de $pageSize
                    $pageLefts = min($pagesToShow, $numPages);
                    $currentPage = $page - floor($pagesToShow / 2);

                    if ($currentPage < 1) {
                        $currentPage = 1;
                    }
                    if ($currentPage + $pageLefts > $numPages + 1) {
                        $currentPage = $numPages + 1 - $pageLefts;
                    }

                    while ($pageLefts != 0 && $currentPage <= $numPages) {
                        if ($currentPage == $page) {
                            echo "<div class='pageNumberContainer'>
                                <img src='assets/images/pageSelected.png'>
                                <span class='pageNumber'>$currentPage</span>
                              </div>";
                        } else {
                            echo "<div class='pageNumberContainer'>
                                      <a href='search.php?term=$term&type=$type&page=$currentPage'>
                                        <img src='assets/images/page.png'>
                                        <span class='pageNumber'>$currentPage</span>
                                      </a>
                                  </div>";
                        }
                        $currentPage++;
                        $pageLefts--;
                    }
                ?>
                <div class="pageNumberContainer">
                    <img src="assets/images/pageEnd.png">
                </div>
            </div>
        </div>

        <!-- Modal pour afficher les détails de l'article -->
        <div class="modal fade" id="productDetailModal" tabindex="-1" role="dialog" aria-labelledby="productDetailModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="productDetailModalLabel">Détails de l'article</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="productContent">
                            <img id="productDetailImage" src="" alt="Image de l'article" class="product-image">
                            <h5 id="productDetailTitle"></h5>
                            <p id="productDetailPrice"></p>
                            <p id="productDetailDescription"></p>
                        </div>

                        <!-- Formulaire pour répondre à l'article -->
                        <?php if($isUserConnected) : ?>
                        <div class="response-box">
                            <h6>Votre message:</h6>
                            <textarea id="responseText" class="form-control" rows="4" placeholder="Tapez votre message ici..."></textarea>
                        </div>
                        <?php endif ?>
                    </div>
                    <?php if($isUserConnected) : ?>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                        <button type="button" class="btn btn-primary" id="replyButton">Envoyer</button>
                    </div>
                    <?php endif ?>
                </div>
            </div>
        </div>

        <!-- Modal pour envoyer message -->
        <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="contactModalLabel">Contacter le Product Owner</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php if ($isUserConnected): ?>
                        <!-- Formulaire pour répondre à l'article -->
                        <form id="contactForm" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="marketplace_id" value="sendMessage">
                            <input type="hidden" name="userId" id="userId" value="" >
                            <input type="hidden" name="senderId" id="senderId" value="<?php echo $_SESSION['user_id'] ?>">
                            <div class="mb-3">
                                <label for="subject" class="form-label">Objet du message</label>
                                <input type="text" class="form-control" id="subject" name="subject" required/>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Votre message</label>
                                <textarea class="form-control" id="message" name="message" rows="4" placeholder="Écrivez votre message ici..." required></textarea>
                            </div>
                            <div>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                <button type="submit" class="btn btn-primary" id="sendMessage">Envoyer</button>
                            </div>
                        </form>
                        <?php else: ?>
                            <p>Veuillez vous connecter pour envoyer un message.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal pour afficher la photo du produit -->
        <div class="modal fade" id="seePictureModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="photoModalLabel">Photo du Produit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="productImage" src="" alt="Product Image" class="img-fluid">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
                </div>
            </div>
        </div>


    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.min.js"></script>
    <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
    <script type="text/javascript" src="assets/js/script.js?3"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function toggleCommentArea(button, id) {
            const elements = document.querySelectorAll('.comment-area');
            elements.forEach(element => {
                element.style.display = 'none';
            });
            // Recherche de la div comment-area associée
            const commentArea = document.getElementById('comment-area-'+id);
            
            // Alterne la visibilité
            if (commentArea.style.display === 'none' || commentArea.style.display === '') {
                commentArea.style.display = 'block'; // Affiche le champ de texte
            } else {
                commentArea.style.display = 'none'; // Masque à nouveau
            }
        }

        // Annuler et cacher le champ de commentaire
        function cancelComment(button) {
            const commentArea = button.closest('.comment-area');
            commentArea.style.display = 'none'; // Cache la div entière
        }

        //Ajouter commentaire
        function sendComment(button, id) {
            const commentArea = button.closest(".comment-area");
            const postId = id; // ID du post
            const userId = <?php echo $_SESSION['user_id'] ?? 0 ?>; // ID de l'utilisateur connecté (à récupérer dynamiquement)
            const commentText = commentArea.querySelector("textarea").value;

            if (!commentText.trim()) {
                alert("Le commentaire est vide !");
                return;
            }

            $.post("ajax/postInfo.php", {
                postId: postId,
                userId: userId,
                comment : commentText,
                postComment: true
            }).done(function(result){
                let data = JSON.parse(result);
                if(data.success) {
                    Swal.fire({
                        title: 'Succès',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = document.location.href;
                        }
                    });
                } else {
                    Swal.fire({
                        text: data.message,
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = document.location.href;
                        } 
                    });
                }
                document.getElementById('comment-area-'+id).style.display = 'none';
            })
        }

        
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.contact-product-owner').forEach((button) => {
                button.addEventListener('click', function () {
                    let element = document.querySelector('.contact-product-owner');
                    const receiverId = element.getAttribute('data-user-id'); // ID du propriétaire du produit                 
                    // Met à jour la valeur de l'input caché pour le champ userId
                    const hiddenInput = document.getElementById("userId");
                    if (hiddenInput) {
                        hiddenInput.setAttribute('value', receiverId); // Met à jour la valeur de l'input
                    } else {
                        console.error("L'input caché pour userId n'a pas été trouvé.");
                    }
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.see-market-image').forEach((button) => {
                button.addEventListener('click', function () {
                    let element = document.querySelector('.see-market-image');
                    const picture = this.getAttribute('data-picture'); // ID du propriétaire du produit  

                    // Met à jour la valeur de l'input caché pour le champ userId
                    const img = document.getElementById("productImage");
                    if (img) {
                        img.setAttribute('src', picture); // Met à jour la valeur de l'image
                    } else {
                        console.error("image non trouvé.");
                    }
                });
            });
        });

        
        function showLoginPopup() {
            alert("Vous devez vous connecter pour visiter ce profil.");
        }

        function handleLikeClick(button) {
            const postId = button.dataset.postId;
            const userIdPost = button.dataset.userId;
            const userLiker = <?php echo $_SESSION['user_id'] ?? 0 ?>;  // ID de l'utilisateur connecté
            
            if (!postId || !userIdPost || !userLiker) {
                alert("Erreur : Impossible de traiter le like.");
                return;
            }

            // Envoi de la requête AJAX pour ajouter ou retirer le like
            $.post("ajax/postInfo.php", {
                postId: postId,
                userLiker: userLiker,
                userIdPost: userIdPost,
                postLike: true
            }).done(function(result) {
                let data = JSON.parse(result);

                if (data.success) {
                    // Si le like a été ajouté ou supprimé, change la couleur du bouton
                    if (data.isLiked) {
                        button.classList.add('liked');  // Change la couleur pour montrer que c'est aimé
                    } else {
                        button.classList.remove('liked');  // Retire la couleur de "liked" si ce n'est plus aimé
                    }
                } else {
                    alert(data.message);
                }
            }).fail(function() {
                alert("Erreur lors du traitement de la requête.");
            });
        }


    </script>
</body>
</html>