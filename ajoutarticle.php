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

                // Initialiser le nom de l'image null pour que la requête INSERT ensuit fonctionne sans chargement d'image (optionnelle)
                $nomImage = NULL ;

                // On récupère et on stocke l'image si elle existe (après validation du if (post rempli) et avant la requête) + UPLOAD_ERR_NO_FILE (PHP7.4 rempli $_FILES['image'] même qd pas de fichier mais ajoute cette erreur !)
                if(
                    isset($_FILES['image']) && !empty($_FILES['image'])
                    && $_FILES['image']['error'] != UPLOAD_ERR_NO_FILE
                ){
                    // On vérifie qu'on n'a pas d'erreur
                    if($_FILES['image']['error'] != UPLOAD_ERR_OK){
                        header('Location: ajoutarticle.php');
                        exit;
                    }
                    // => On continue si pas d'erreur 

                    // On récupère l'extension du fichier envoyé
                    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

                    // On vérifie si le type d'image accepté est respecté (extension + type MIME)
                    $goodExtensions = ['png', 'jpg', 'jpeg', 'jfif', 'pjpeg', 'pjp'];
                    $goodMimeTypes = ['image/png', 'image/jpeg'];
                    $mimeType = $_FILES['image']['type'];

                    if (!in_array($extension, $goodExtensions) || !in_array($mimeType, $goodMimeTypes)){
                        header('Location: ajoutarticle.php');
                        exit;
                    }

           
                    // On vérifie que le poids max du fichier n'est pas dépassé (1024*1024)
                    $tailleMax = 1048576 ;
                    if ($_FILES['image']['size'] > $tailleMax ){
                        header('Location: ajoutarticle.php');
                    }

                    // On génère un nom de fichier (uniqid = chiffre unique basé sur le timestamp actuel -> milliseconde + on l'encode en md5)
                    $nomImage = md5(uniqid()).'.'.$extension;
                    // On transfère le fichier
                    if(!move_uploaded_file($_FILES['image']['tmp_name'], __DIR__.'/uploads/'.$nomImage)){
                            //  Transfert échoué
                            header('Location: ajout.php');
                            exit;
                    };
                  
                }

                // htmlspecialchars pour autoriser les balises ecrites mais desactivées
                $contenu = htmlspecialchars($_POST['content']);
                $user_id = $_SESSION['user']['id'];

                // B) Protection contre les injections SQL (bdd)
                // 1- On écrit la requête
                $sql =  "INSERT INTO `articles`(`title`, `content`, `categories_id`, `users_id`, `featured_image`) 
                VALUES (:title, :content, :id, :user, :image_name);";
                // 2- On prépare la requêtre
                $query = $db->prepare($sql);
                // 3- On injecte les valeurs dans les paramètres
                $query->bindValue(':title', $titre, PDO::PARAM_STR);
                $query->bindValue(':content', $contenu, PDO::PARAM_STR);
                $query->bindValue(':id', $categorie, PDO::PARAM_INT);
                $query->bindValue(':user', $user_id, PDO::PARAM_INT);
                $query->bindValue(':image_name', $nomImage, PDO::PARAM_STR);
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


<main id="ajoutarticle">
    <section>
        <h2>Ajouter un article</h2>
        <form method="post" enctype="multipart/form-data">
        <!-- Encodage : enctype="multipart/form-data" => active la super globale FILES !! -->
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

            <div>
                <label for="image">Image :</label>
                <div><input type="file" name="image" id="image" accept="image/png, image/jpeg"></input></div>
            </div>
            
            <button <?php
            if(!isset($_SESSION['user']) && empty($_SESSION['user'])){
                echo 'disabled';
            }
            ?>
            >Ajouter</button>

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
                $content = extrait($article['content'], 200);

                echo "<div class='article'>
                <h3>{$article['title']}</h3>
                <p>{$content}</p>
                <p>{$article['created_at']}</p>
                </div>";
            }
            ?>
        </div>

    </section> 
    </main>
</body>