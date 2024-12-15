<?php 
    $isUserConnected = false;
    session_start();
    if(!empty($_SESSION)) {
        $isUserConnected = true;
    };
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Underdex</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <link rel="stylesheet" href="fonts/icomoon/style.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    
    <!-- Style -->
    <link rel="stylesheet" href="css/style.css">

    <style>
        /* Assurez-vous que le body et html couvrent toute la hauteur */
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        /* Correction de l'espace en bas de la page */
        .wrapper.indexPage {
            min-height: 100vh;  /* Assurez-vous que la wrapper couvre toute la hauteur de l'écran */
            display: flex;
            flex-direction: column;
            justify-content: space-between;  /* Équilibre l'espace entre les éléments */
        }

        /* Si la recherche ou d'autres éléments créent un débordement */
        .searchContainer {
            margin-bottom: 0;  /* Assurez-vous qu'il n'y a pas de marges indésirables */
        }

        /* Enlever l'espace sous la page si un footer est invisible ou mal géré */
        footer {
            display: none;  /* Masquez le footer si ce dernier est présent sans contenu */
        }

        /* Retirer les marges du header ou de toute autre section qui pourrait laisser un espace en bas */
        .site-navbar {
            margin-bottom: 0;
        }
    </style>
</head>

<body>
    <div class="site-mobile-menu site-navbar-target">
        <div class="site-mobile-menu-header">
            <div class="site-mobile-menu-close mt-3">
                <span class="icon-close2 js-menu-toggle"></span>
            </div>
        </div>
        <div class="site-mobile-menu-body"></div>
    </div>

    <header class="site-navbar site-navbar-target py-4" role="banner">
        <div class="container">
            <div class="row align-items-center position-relative">
                <div class="col-3">
                    <div class="site-logo">
                        <a href="index.php" class="font-weight-bold text-white"></a>
                    </div>
                </div>

                <div class="col-9 text-right">
                    <span class="d-inline-block d-lg-block">
                        <a href="#" class="text-black site-menu-toggle js-menu-toggle py-5">
                            <span class="icon-menu h3 text-white"></span>
                        </a>
                    </span>

                    <nav class="site-navigation text-right ml-auto d-none d-lg-none" role="navigation">
                        <ul class="site-menu main-menu js-clone-nav ml-auto">
                            <li class="active"><a href="index.html" class="nav-link">À propos</a></li>
                            <li><a href="about.html" class="nav-link" style="font-size: 15px;">À propos d'Underdex</a></li>
                            <li><a href="faq.php" class="nav-link" style="font-size: 15px;">Foire aux questions</a></li>			
                            <li class="active"><a href="about.html" class="nav-link">Démarrer</a></li>
                            <?php if(!$isUserConnected) : ?>
                            <li><a href="login.php" class="nav-link" style="font-size: 15px;">Se connecter</a></li>
                            <li><a href="register.php" class="nav-link" style="font-size: 15px;">S'inscrire gratuitement</a></li>
                            <?php else : ?>
                            <li><a href="settings.php" class="nav-link" style="font-size: 15px;">Profil</a></li>
                            <?php endif ?>
                            <li class="active"><a href="services.html" class="nav-link">Services</a></li>
                            <li><a href="register.php" class="nav-link" style="font-size: 16px;">Vendre sur la Marketplace</a></li>
                            <li><a href="register.php" class="nav-link" style="font-size: 16px;">Campagnes publicitaires</a></li>
                            <li class="active"><a href="contact.html" class="nav-link">Conditions</a></li>
							<li><a href="about.html" class="nav-link" style="font-size: 15px;">Mentions légales</a></li>
                            <li><a href="cgu.html" class="nav-link" style="font-size: 15px;">Conditions d'usages</a></li>
                            <?php if($isUserConnected) : ?>
                            <li><a href="logout.php" class="nav-link" style="font-size: 15px;">Se déconnecter</a></li>
                            <?php endif ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <div class="wrapper indexPage">
        <div class="mainSection">
		<br><br><br><br><br><br><br><br><br>
            <div class="logoContainer">
                <img src="assets/images/logo.png" alt="Google Title">
            </div>
            <div class="searchContainer">
                <form action="search.php" method="get">
                    <input type="text" class="searchBox" name="term">                 
                </form>
            </div>
        </div>
    </div>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.sticky.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
