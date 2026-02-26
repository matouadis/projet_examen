<?php
    $dossierpublic = "http://localhost/projet%20examen/public";
    include_once "includes/header.php";
    include_once "includes/navbar.php";
    include_once "includes/sidebar.php";
    require_once "traitement/requette.php";
    require_once "traitement/action.php";
$classes = getClasse();
// Récupérer les statistiques

    $page = isset($_GET['page']) ? $_GET['page'] : "acceuille";
    if (file_exists("pages/$page.php")) {
        include_once "pages/$page.php";
    }
    else{
        include_once "pages/erreur404.php";
    }

    include_once "includes/footer.php";
?>