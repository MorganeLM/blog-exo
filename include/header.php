<?php
    require_once 'connect.php';
    session_start();

    define('URL', 'http://localhost/blog');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <link rel="stylesheet" href="<?=URL.'/css/styles.css';?>">
</head>
<body>

    <header>
        <h1><a href="<?=URL ;?>">Mon blog</a></h1>
      
        <div>
            <?php
            echo ("<a href='".URL."/categories/index.php'>Catégories</a>");

            if(isset($_SESSION['user']['nickname']) && !empty($_SESSION['user']['nickname'])){
                echo ("<a href='".URL."/users/deconnexion.php'>Déconnexion</a>");
            }else{
                echo ("<a href='".URL."/users/index.php'>Inscription</a>");
                echo ("<a href='".URL."/connexion/index.php'>Connexion</a>");
            }

            ?>

            
        </div>
    </header>
    
    <p>Bonjour 
            <?php 
            if(isset($_SESSION['user']['nickname']) && !empty($_SESSION['user']['nickname'])){echo$_SESSION['user']['nickname'];} 
            ?> 
            !
    </p>