<?php
    require_once 'connect.php';
    session_start();

    // On vérifie le cookie remember et on restaure la session si besoin
    if(isset($_COOKIE['RememberMe']) && !empty($_COOKIE['RememberMe'])){
        // On récupère et on nettoie le token
        $remember_token = strip_tags($_COOKIE['RememberMe']);

        // On se connect à la base : require_once 'connect.php'; (normalement pas dans le header mais dans chaque page avant de faire une requete => pas présent si pas besoin..)

        // On recherche l'utilisateur dans la bdd
        $sql = "SELECT * FROM `users` WHERE `remember_token`= :token"; 
        $query = $db->prepare($sql);
        // 3- On injecte les valeurs dans les paramètres
        $query->bindValue(':token', $remember_token, PDO::PARAM_STR);
        // 4- On exécute la requête
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if($user){
            $_SESSION['user'] = [
                'id'        => $user['id'],
                'nickname'  => $user['nickname'],
                'email'     => $user['email'],
                'roles'     => $user['roles']
            ];
        }else{
            // On supprime le cookie si pas utilisateur correspondant
            setcookie('RememberMe', '', 1);
        }
        
    }

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
    
