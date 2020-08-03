<?php 
include_once 'include/header.php' ;

setlocale (LC_TIME, 'fr_FR.utf8','fra'); 
?>

<main>
        <h2>Détail de l'article :</h2>

        <div>
            <?php
            if(isset($_GET['id']) && !empty($_GET['id'])){
                // I) PARTIE POUR RéCUPERER LES DONNéES DE LA BASE
                // 1- On écrit la requête
                $sql = 'SELECT art.*, cat.`name`, u.`nickname` FROM `articles` art 
                LEFT JOIN `categories` cat ON art.`categories_id` = cat.`id`
                LEFT JOIN `users` u ON art.`users_id` = u.`id`
                WHERE art.`id` = :id;';
                // 2- On prépare la requêtre
                $query = $db->prepare($sql);
                // 3- On injecte les valeurs dans les paramètres
                $query->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
                // 4- On exécute la requête
                $query->execute();
    
                // On récupère les données (que pour l'id et fetch assoc pour récupérer que les associations nom-valeur et pas avoir tout en double)
                $article = $query->fetch(PDO::FETCH_ASSOC);

                // On vérifie si l'article n'existe pas
                if(!$article){
                    header('Location: '.URL);
                }

                // METHODE ECHO 
                $created_date = $article['created_at'];
                $french_date = formatDate("{$created_date}");
    
                echo "<div class='article'>
                <h3>{$article['title']}</h3>
                <em>écrit par {$article['nickname']}, le {$french_date}, concernant {$article['name']}</em>
                <p>{$article['content']}</p>
                
                </div>";
            } else {
                // pas d'ID ou vide, retour à l'accueil
                header('Location: '.URL);
            }
            ?>
        </div>

        <h2><em> Deuxième méthode d'écriture :</em></h2>
        <div class='article'>
                <h3>
                        <?= $article['title'] ?>
                </h3>
                <em>écrit par <?= $article['nickname'] ?>, le <?= formatDate($article['created_at']) ?>, dans la catégorie <?= $article['name'] ?></em>
                <p><?= htmlspecialchars($article['content'])?></p>
        </div>

    </main>
</body>
</html>