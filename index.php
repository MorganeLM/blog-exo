<?php
    require_once 'include/connect.php';
    session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <a href="index.php"><h1>Mon blog</h1></a> 
        </div>
            <a href="categories/index.php">Catégories</a>
            <a href="users/index.php">Inscription</a>
            <a href="connexion/index.php">Connexion</a>
        <div>
    </header>

    <main>
        <p>Bienvenu <?php echo$_SESSION['pseudo'] ?> !</p>
        <h2>Mes articles :</h2>

    </main>
</body>
</html>