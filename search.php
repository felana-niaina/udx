<?php
include_once 'classes/DatabaseConnector.php';
include("classes/SiteResultsProvider.php");
include("classes/MarketplaceResultsProvider.php"); 
include("classes/PostsResultsProvider.php"); 
include("classes/ImageResultsProvider.php");

// Créer une instance de la classe DatabaseConnector
$database = new DatabaseConnector();
$con = $database->getConnection();
$isUserConnected = false;
session_start();
if(!empty($_SESSION)) {
    $isUserConnected = true;
};

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/style.css?A">
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

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.min.js"></script>
    <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
    <script type="text/javascript" src="assets/js/script.js?3"></script>
</body>
</html>