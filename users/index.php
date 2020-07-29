<?php
    require_once '../include/connect.php';

// II) PARTIE POUR AJOUTER LES DONNéES DE LA BASE 
    // On vérifie que POST n'est pas vide
    if(!empty($_POST)){
        // POST n'est pas vide, on vérifie tous les champs obligatoires
        if(
            isset($_POST['name']) && !empty($_POST['name']) 
            && isset($_POST['mdp']) && !empty($_POST['mdp']) 
            && isset($_POST['email']) && !empty($_POST['email']) 
        ){
            // Tous les champs sont valides
            // A) Protection contre le XSS (navigateur)
            $email = strip_tags($_POST['email']);
            $mdp = strip_tags($_POST['mdp']);
            $nom = strip_tags($_POST['name']);

            $hashed_mdp = password_hash($mdp, PASSWORD_DEFAULT);

            // B) Protection contre les injections SQL (bdd)
            // 1- On écrit la requête
            $sql =  "INSERT INTO `users`(`email`, `password`, `nickname`) VALUES (:email, :mdp, :pseudo);";
            // 2- On prépare la requêtre
            $query = $db->prepare($sql);
            // 3- On injecte les valeurs dans les paramètres
            $query->bindValue(':email', $email, PDO::PARAM_STR);
            $query->bindValue(':mdp', $hashed_mdp, PDO::PARAM_STR);
            $query->bindValue(':pseudo', $nom, PDO::PARAM_STR);
            // 4- On exécute la requête
            $query->execute();


        }else{
            // Au moins un des champs est invalide
            $erreur = "Le formulaire est incomplet";
        }
    }

// I) PARTIE POUR RéCUPERER LES DONNéES DE LA BASE (remplissage tableau)
$sql = 'SELECT * FROM `users` ORDER BY `id`;';
// On exécute la requête
$query = $db->query($sql);

// On récupére les données
$users = $query->fetchAll(PDO::FETCH_ASSOC);

?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des catégories</title>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <a href="../index.php"><h1>Mon blog</h1></a> 
        </div>
            <a href="../categories/index.php">Catégories</a>
            <a href="index.php">Inscription</a>
            <a href="../connexion/index.php">Connexion</a>
        <div>
    </header>

    <main>
        <h2>S'inscrire</h2>
        <form method="post">
            <div>
                <label for="name">Pseudo :</label>
                <div><input type="text" name="name" id="name" /></div>
            </div>
            <div>
                <label for="email">Email :</label>
                <div><input type="email" name="email" id="email" /></div>
            </div>
            <div>
                <label for="mdp">Mot de passe :</label>
                <div><input type="password" name="mdp" id="mdp" /></div>
            </div>
            <button>Valider</button>
        </form>
    </main>
</body>