<?php 
include_once 'include/header.php' ;

setlocale (LC_TIME, 'fr_FR.utf8','fra'); 
?>

    <main>
        <p id="bonjour">Bonjour 
                <?php 
                if(isset($_SESSION['user']['nickname']) && !empty($_SESSION['user']['nickname'])){echo$_SESSION['user']['nickname'];} 
                ?> 
                !
        </p>

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

            ?>

            <?php foreach($articles_data as $article): ?>
                <div class='article'>

                <?php if(!is_null($article['featured_image'])):     
                    // On fabrique le nom de l'image
                    $nomImage = pathinfo($article['featured_image'], PATHINFO_FILENAME);
                    $extension = pathinfo($article['featured_image'], PATHINFO_EXTENSION);
                    $minature = "$nomImage-200x200.$extension";
                ?>

                    <img src="<?= URL.'/uploads/'.$minature ?>" alt="<?= $article['title'] ?>">

                <?php endif; ?>


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