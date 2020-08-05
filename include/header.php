<?php
    require_once 'connect.php';
    session_start();

    define('URL', 'http://localhost/blog');
    require_once 'functions.php';

    
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="<?=URL.'/css/styles.css';?>">
</head>
<body>

    <header>
        <h1><a href="<?=URL ;?>">Mon blog</a></h1>
      
        <div>
            <?php
            // var_dump($_SESSION['user']['roles'] );

            echo ("<a href='".URL."/categories/index.php'>Catégories</a>");

            if(isset($_SESSION['user']) && !empty($_SESSION['user'])){
                // echo ("<a href='".URL."/ajoutarticle.php'>Ecrire un article</a>");

                // Transforme une chaine de caractères "json" en tableau PHP
                $roles =json_decode($_SESSION['user']['roles']);

                if(in_array("ROLE_ADMIN", $roles)){
                    echo ("<a href='".URL."/ajoutarticle.php'>Ecrire un article</a>");
                }else{
                    echo ("<a href='".URL."/index.php'>Ecrire un article</a>");
                }

                echo ("<a href='".URL."/users/deconnexion.php'>Déconnexion</a>");
            }else{
                echo ("<a href='".URL."/connexion/index.php'>Ecrire un article</a>");
                echo ("<a href='".URL."/users/index.php'>Inscription</a>");
                echo ("<a href='".URL."/connexion/index.php'>Connexion</a>");
            }


            ?>

            
        </div>
    </header>
    
