<?php 
    $isUserConnected = false;
    $notifNumber = 1;
    session_start();
    if(!empty($_SESSION)) {
        $isUserConnected = true;
        include_once 'classes/DatabaseConnector.php';
        include_once 'classes/UserRegistration.php';
        include_once 'classes/NotificationProvider.php';
        $database = new DatabaseConnector();
        $con = $database->getConnection();
        $UserProvider = new UserRegistration($con);
        $lastLogout = $UserProvider->getLastConnexionByUser($_SESSION['user_id']);
        $logoutDate = $lastLogout['createdDate'] ?? '2024-01-01';
        $NotifProvider = new NotificationProvider($con);
        $notifNumber = 2;
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

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

        .bottom-menu {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #1c1c1c;
            z-index: 999;
        }

        .bottom-menu ul {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .bottom-menu ul li {
            text-align: center;
            width: 20%;
        }

        .bottom-menu ul li a {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 10px 0;
            color: white;
            text-decoration: none;
            font-size: 14px;
            height: 60px;
            transition: background-color 0.3s, transform 0.2s;
        }

        .bottom-menu ul li a i {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .bottom-menu ul li a:hover {
            background-color: #575757;
            transform: translateY(-3px);
        }

        @media (max-width: 768px) {
            .bottom-menu {
                display: block;
            }
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

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            font-weight: bold;
        }

        .fas.fa-bell {
            position: relative;
        }

        .notification-badge-message {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            font-weight: bold;
        }

        .fas.fa-comment {
            position: relative;
        }

        .notification-badge-feed {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            font-weight: bold;
        }

        .fas.fa-stream {
            position: relative;
        }

        .notification-badge-marketplace {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            font-weight: bold;
        }

        .fas.fa-store {
            position: relative;
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
                            <span class="far fa-bell h3 text-white position-relative">
                                <span class="notification-badge"><? echo $notifNumber ?></span> <!-- Le nombre de notifications -->
                            </span>
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
                            <li><a href="settings.php" class="nav-link" style="font-size: 15px;">Notifications<span class="notification-badge">1</span></a></li>
                            <li><a href="profil.php" class="nav-link" style="font-size: 15px;">Mon Profil</a></li>
							<li><a href="settings.php" class="nav-link" style="font-size: 15px;">Paramètres</a></li>
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

    <div class="bottom-menu">
        <ul>
		<!-- TEMPLATE NOTIFICATIONS START   -->
		<!-- TEMPLATE NOTIFICATIONS END   -->
		    <li>
                <a href="settings.php#notifications"> <!-- L'utilisateut doit être redirigé automatiquement vers la page de notifications  -->
                    <i class="fas fa-stream">
                    <?php if($isUserConnected) : ?><span class="notification-badge-feed">10</span> <?php endif ?><!-- Le nombre de notifications doit s'afficher ici -->
                    </i>
                    <span>Feed</span>
                </a>
            </li>
		    <li>
                <a href="settings.php#notifications"> <!-- L'utilisateut doit être redirigé automatiquement vers la page de notifications  -->
                    <i class="fas fa-store">
                        <?php if($isUserConnected) : ?><span class="notification-badge-feed">10</span> <?php endif ?><!-- Le nombre de notifications doit s'afficher ici -->
                    </i>
                    <span>Marketplace</span>
                </a>
            </li>
            <li>
                <a href="settings.php#notifications"> <!-- L'utilisateut doit être redirigé automatiquement vers la page de notifications  -->
                    <i class="fas fa-comment">
                        <?php if($isUserConnected) : ?><span class="notification-badge-message">3</span> <?php endif ?><!-- Le nombre de notifications doit s'afficher ici -->
                    </i>
                    <span>Messages</span>
                </a>
            </li>
			<?php if(!$isUserConnected) : ?>
            <li><a href="register.php"><i class="fas fa-user"></i><span>S'inscrire</span></a></li>
            <li><a href="login.php"><i class="fas fa-sign-in-alt"></i><span>Connexion</span></a></li>
			<?php else : ?>
			<li><a href="profil.php"><i class="fas fa-user"></i><span>Profil</span></a></li>
            <li><a href="settings.php"><i class="fas fa-cogs"></i><span>Paramètres</span></a></li>
			<?php endif ?>
        </ul>
    </div>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.sticky.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
