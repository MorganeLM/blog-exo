<?php
    require_once 'include/header.php';

    // GESTION DES DROITS -> Connecté + ADMIN
    // Connecté ?
    if(!isset($_SESSION['user']) && empty($_SESSION['user'])){
        // echo ("<a href='".URL."/ajoutarticle.php'>Ecrire un article</a>");
        header('Location: connexion/index.php');
        // Transforme une chaine de caractères "json" en tableau PHP
    }
    // ADMIN
    $roles =json_decode($_SESSION['user']['roles']);
    
    if(!in_array("ROLE_ADMIN", $roles)){
        header('Location: index.php');
    }


?>


<main id="ajoutarticle">
    <section>
        <h2>Ajouter un article</h2>
        <form method="post">
            <div >
                <label for="title">Titre :</label>
                <div><input type="text" name="title" id="title" /></div>
            </div>

            <div>
                <label for="cat">Catégorie :</label>
                <div>
                    <select name="cat" id="cat"  require>
                        <option value="">-- Choisir une catégorie --</option>

                        <?php
                        // I) PARTIE POUR RéCUPERER LES DONNéES DE LA BASE
                        $sql = 'SELECT * FROM `categories` ORDER BY `name` ASC;';
                        // On exécute la requête
                        $query = $db->query($sql);
                        // On récupére les données
                        $categories = $query->fetchAll(PDO::FETCH_ASSOC);
                        // On remplit le select
                        foreach ($categories as $cat){
                            echo "<option value='{$cat['id']}'>{$cat['name']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div>
                <label for="content">Contenu :</label>
                <div><textarea name="content" id="content"></textarea></div>
            </div>
            <button <?php
            if(!isset($_SESSION['user']) && empty($_SESSION['user'])){
                echo 'disabled';
            }
            ?>
            >Ajouter</button>
            <?php
            
            // II) PARTIE POUR AJOUTER LES DONNéES DE LA BASE 
            if(isset($_SESSION['user']) && !empty($_SESSION['user'])){
                // On vérifie que POST n'est pas vide
                if(!empty($_POST)){
                    // POST n'est pas vide, on vérifie tous les champs obligatoires
                    if(
                        isset($_POST['title']) && !empty($_POST['title'])
                        && isset($_POST['cat']) && !empty($_POST['cat'])
                        && isset($_POST['content']) && !empty($_POST['content'])  
                    ){
                        // Tous les champs sont valides
                        // A) Protection contre le XSS (navigateur)
                        $titre = strip_tags($_POST['title']);
                        $categorie = strip_tags($_POST['cat']);
                        // htmlspecialchars pour autoriser les balises ecrites mais desactivées
                        $contenu = htmlspecialchars($_POST['content']);

                        $user_id = $_SESSION['user']['id'];

                        // B) Protection contre les injections SQL (bdd)
                        // 1- On écrit la requête
                        $sql =  "INSERT INTO `articles`(`title`, `content`, `categories_id`, `users_id`) 
                        VALUES (:title, :content, :id, :user);";
                        // 2- On prépare la requêtre
                        $query = $db->prepare($sql);
                        // 3- On injecte les valeurs dans les paramètres
                        $query->bindValue(':title', $titre, PDO::PARAM_STR);
                        $query->bindValue(':content', $contenu, PDO::PARAM_STR);
                        $query->bindValue(':id', $categorie, PDO::PARAM_INT);
                        $query->bindValue(':user', $user_id, PDO::PARAM_INT);
                        // 4- On exécute la requête
                        $query->execute();

                        header('Location: '.URL);


                    }else{
                        
                        // Au moins un des champs est invalide
                        $erreur = "Le formulaire est incomplet";
                    }
                }
            } else {
                echo '<p> Vous devez être connecté pour poster un article. </p>';
                echo '<a href="connexion/index.php"> Se connecter </a>';
            }

            ?>
        </form>
    </section>

    <section>
        <h2>Articles précédents</h2>
        <div>
            <?php


            // I) PARTIE POUR RéCUPERER LES DONNéES DE LA BASE
            $sql = 'SELECT * FROM `articles` ORDER BY `created_at` DESC;';
            // On exécute la requête
            $query = $db->query($sql);
            // On récupére les données
            $articles = $query->fetchAll(PDO::FETCH_ASSOC);
            // On remplit le select

            foreach ($articles as $article){
                echo "<div class='article'>
                <h3>{$article['title']}</h3>
                <p>{$article['content']}</p>
                <p>{$article['created_at']}</p>
                </div>";
            }

            ?>
        </div>

    </section> 
    </main>
</body>