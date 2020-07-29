<?php
    require_once '../include/connect.php';

    // verifier le mot de pass ($pass => mdp haché), return true
    $valid = password_verify("Bonjour", $pass)


?>

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
        <h2>Me connecter :</h2>
        <form method="post">
            <div>
                <label for="name">Pseudo :</label>
                <div><input type="text" name="name" id="name" /></div>
            </div>
            <div>
                <label for="mdp">Mot de passe :</label>
                <div><input type="password" name="mdp" id="mdp" /></div>
            </div>
            <button>Valider</button>
        </form>

    </main>
</body>
</html>