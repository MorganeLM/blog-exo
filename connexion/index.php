
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <a href="../index.php"><h1>Mon blog</h1></a> 
        </div>
            <a href="../categories/index.php">Catégories</a>
            <a href="../users/index.php">Inscription</a>
            <a href="index.php">Connexion</a>
        <div>
    </header>

    <main>
        <h2>Se connecter :</h2>
        <form method="post">
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

<?php
    require_once '../include/connect.php';

    if(!empty($_POST)){
        // POST n'est pas vide, on vérifie tous les champs obligatoires
        if(
            isset($_POST['email']) && !empty($_POST['email']) 
            && isset($_POST['mdp']) && !empty($_POST['mdp']) 
        ){
            // Tous les champs sont valides
            // A) Protection contre le XSS (navigateur)
            $email = strip_tags($_POST['email']);
            $mdp_entre = strip_tags($_POST['mdp']);

            // B) Protection contre les injections SQL (bdd)
            // 1- On écrit la requête
            $sql =  "SELECT * FROM `users` WHERE `email` = :email;";
            // 2- On prépare la requêtre
            $query = $db->prepare($sql);
            // 3- On injecte les valeurs dans les paramètres
            $query->bindValue(':email', $email, PDO::PARAM_STR);
            // 4- On exécute la requête
            $query->execute();
    
            // On récupére les données
            $user = $query->fetch(PDO::FETCH_ASSOC);
            // verifier le mot de pass ($pass => mdp haché), return true
            // var_dump($hashed_mdp);
            $valid = password_verify($mdp_entre, $user['password']);

            if ($valid) {
                echo '<p>---- Mot de passe valide! ----</p>';

                session_start();
                var_dump($_SESSION);
                $_SESSION['pseudo'] = $user['nickname'];
                header('Location: ../index.php');

            } else {
                echo '<p>---- Mot de passe et/ou adresse mail erroné.e.(s). -----</p>';
            }
        }
    }
?>
    </main>
</body>
</html>