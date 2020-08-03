<?php 
include_once 'include/header.php' ;

setlocale (LC_TIME, 'fr_FR.utf8','fra'); 
?>

    <main>
        <h2>Mes articles :</h2>

        <div>
            <?php
            // I) PARTIE POUR RéCUPERER LES DONNéES DE LA BASE
            $sql = 'SELECT art.*, cat.`name`, u.`nickname` FROM `articles` art
            LEFT JOIN `categories` cat ON art.`categories_id` = cat.`id`
            LEFT JOIN `users` u ON art.`users_id` = u.`id`
            ORDER BY art.`created_at` DESC;';
            // On exécute la requête
            $query = $db->query($sql);
            // On récupére les données
            $articles_data = $query->fetchAll(PDO::FETCH_ASSOC);


            foreach ($articles_data as $article){
                $created_date = $article['created_at'];
                $french_date = formatDate("{$created_date}");

                $content = extrait($article['content'], 200);


                echo "<div class='article'>
                <h3><a href='article.php?id={$article['id']}'>{$article['title']}</a></h3>
                <em>écrit par {$article['nickname']}, le {$french_date}, concernant {$article['name']}</em>
                <p>{$content}</p>
                
                </div>";
            }
            ?>

        <h2>Mes articles - deuxième écriture :</h2>
            <!-- Ecriture alternative -->
            <?php foreach($articles_data as $article): ?>
                <div class='article'>
                <h3>
                    <a href="article.php?id=<?=$article['id']?>">
                        <?= $article['title'] ?>
                    </a>
                </h3>
                <em>écrit par <?= $article['nickname'] ?>, le <?= formatDate($article['created_at']) ?>, dans la catégorie <?= $article['name'] ?></em>
                <p><?= extrait($article['content'], 200)?></p>
                </div>
            <?php endforeach ?>

        </div>

    </main>
</body>
</html>