<?php
// On vérifie si on a un id dans l'URL
if(isset($_GET['id']) && !empty($_GET['id'])){
    // On a un id, on va chercher la personne dans la base
    // On se connecte
    require_once '../include/connect.php';

    // 1- On écrit la requête
    $sql = 'SELECT * FROM `categories` WHERE `id` = :id';
    // 2- On prépare la requêtre
    $query = $db->prepare($sql);
    // 3- On injecte les valeurs dans les paramètres
    $query->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
    // 4- On exécute la requête
    $query->execute();

    // On récupère les données (que pour l'id et fetch assoc pour récupérer que les associations nom-valeur et pas avoir tout en double)
    $categories = $query->fetch(PDO::FETCH_ASSOC);
    
    if(!$categories){ 
        // Ici categories est vide (false) -> retour à page accueil
        header('Location: categories/index.php');
    }

// bloc UPDATE
// Ici $categories contient un enregistrementde la base de données (1 ligne)
    if(!empty($_POST)){
        // POST n'est pas vide, on vérifie tous les champs obligatoires
        if(
            isset($_POST['name']) && !empty($_POST['name']) 
        ){
            // Tous les champs sont valides
            // A) Protection contre le XSS (navigateur)
            $nom = strip_tags($_POST['name']);

            // B) Protection contre les injections SQL (bdd)
            // 1- On écrit la requête
            $sql = "UPDATE `categories` SET `name` = :nom WHERE `id`= {$categories['id']};"; 
            // 2- On prépare la requêtre
            $query = $db->prepare($sql);
            // 3- On injecte les valeurs dans les paramètres
            $query->bindValue(':nom', $_POST['name'], PDO::PARAM_STR);
            // 4- On exécute la requête
            $query->execute();
    
            header('Location: index.php');
        }
    }
// bloc UPDATE fin

}else{
    // Pas d'id ou vide => retour page d'accueil
    header('Location: index.php');
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h2>Modifier la catégorie :</h2>
    <form method="post">
        <div>
            <label for="name">Nouveau nom :</label>
            <div><input type="text" name="name" id="name" value="<?= $categories['name'] ?>" /></div>
        </div>
        <button>OK</button>
    </form>
</body>
</html>