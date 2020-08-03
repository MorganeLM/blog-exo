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
        <div id="categorie">
            <div>
                <h2>Liste des catégories :</h2>
                <ul>
                    <?php foreach ($categories as $categorie): ?>
                    <li>
                        <a href=<?= "index.php?id={$categorie['id']}"?>>
                            <?= $categorie['name'] ?> (id n°<?= $categorie['id'] ?>)
                        </a>
                        <a href="modif.php?id=<?= $categorie['id'] ?>"><i class="las la-edit"></i></a>
                        <a href="suppr.php?id=<?= $categorie['id'] ?>"><i class="las la-trash-alt"></i></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div>
                <h2>Ajouter une catégorie</h2>
                <form method="post">
                    <div>
                        <label for="name">Nom :</label>
                        <div><input type="text" name="name" id="name" /></div>
                    </div>
                    
                    <button>OK</button>
                </form>
            </div>
        </div>

        <div>
            <h2>Liste des articles par catégorie :</h2>
            
            <?php
            if(isset($_GET['id']) && !empty($_GET['id'])){
                // I) PARTIE POUR RéCUPERER LES DONNéES DE LA BASE
                // 1- On écrit la requête
                $sql = 'SELECT art.*, cat.`name`, u.`nickname` FROM `articles` art 
                LEFT JOIN `categories` cat ON art.`categories_id` = cat.`id`
                LEFT JOIN `users` u ON art.`users_id` = u.`id`
                WHERE cat.`id` = :id
                ORDER BY art.`created_at` DESC;';
                // 2- On prépare la requêtre
                $query = $db->prepare($sql);
                // 3- On injecte les valeurs dans les paramètres
                $query->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
                // 4- On exécute la requête
                $query->execute();

                // On récupère les données (que pour l'id et fetch assoc pour récupérer que les associations nom-valeur et pas avoir tout en double)
                $articles_data = $query->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($articles_data as $article){
                    $created_date = $article['created_at'];
                    $french_date = formatDate("{$created_date}");
 
                    echo "<div class='article'>
                    <h3><a href='".URL."/article.php?id={$article['id']}'>{$article['title']}</a></h3>
                    <em>écrit par {$article['nickname']}, le {$french_date}, concernant {$article['name']}</em>
                    <p>{$article['content']}</p>
                    
                    </div>";
                }
                
                }else{
                    echo '<p><em>Cliquez sur une des catégorie pour afficher les articles correspondants.</em></p>';
                }
            

            ?>

        </div>
    </main>
</body>