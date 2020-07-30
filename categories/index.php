<?php
    include_once '../include/header.php';

// II) PARTIE POUR AJOUTER LES DONNéES DE LA BASE 
    // On vérifie que POST n'est pas vide
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
            $sql =  "INSERT INTO `categories`(`name`) 
            VALUES (:nom);";
            // 2- On prépare la requêtre
            $query = $db->prepare($sql);
            // 3- On injecte les valeurs dans les paramètres
            $query->bindValue(':nom', $nom, PDO::PARAM_STR);
            // 4- On exécute la requête
            $query->execute();


        }else{
            // Au moins un des champs est invalide
            $erreur = "Le formulaire est incomplet";
        }
    }

// I) PARTIE POUR RéCUPERER LES DONNéES DE LA BASE (remplissage tableau)
$sql = 'SELECT * FROM `categories` ORDER BY `id`;';
// On exécute la requête
$query = $db->query($sql);

// On récupére les données
$categories = $query->fetchAll(PDO::FETCH_ASSOC);


?>

    <main>

        <h2>Liste des catégories :</h2>
        <ul>
            <?php foreach ($categories as $categorie): ?>
            <li>
                <?= $categorie['name'] ?> (id n°<?= $categorie['id'] ?>)
                <a href="modif.php?id=<?= $categorie['id'] ?>"><i class="las la-edit"></i></a>
                <a href="suppr.php?id=<?= $categorie['id'] ?>"><i class="las la-trash-alt"></i></a>
            </li>
            <?php endforeach; ?>
        </ul>

        <h2>Ajouter une catégorie</h2>
        <form method="post">
            <div>
                <label for="name">Nom :</label>
                <div><input type="text" name="name" id="name" /></div>
            </div>
            <div>
            <button>OK</button>
        </form>
    </main>
</body>