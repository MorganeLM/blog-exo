<?php 
include_once 'include/header.php' ;

$lastmodified = "2020-20-04 15:08:20" ;
list($date, $time) = explode(" ", $lastmodified);
list($year, $month, $day) = explode("-", $date);
list($hour, $min, $sec) = explode(":", $time);
$lastmodified = "le {$day}/{$month}/{$year} à {$time}";
echo "<p> {$lastmodified} <p>";

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
            // On remplit le select


            foreach ($articles_data as $article){
                echo "<div class='article'>
                <h3>{$article['title']}</h3>
                <p>écrit par {$article['nickname']}, le {$article['created_at']}, concernant {$article['name']}</p>
                <p>{$article['content']}</p>
                
                </div>";
            }

            ?>
        </div>

    </main>
</body>
</html>