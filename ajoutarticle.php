<?php

use PHPMailer\PHPMailer\Exception;

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
            // On sauvegarde le contenu de $_POST (du formulaire) dans $_SESSION
            $_SESSION['form'] = $_POST;
            
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
                        // On ajoute un message de session
                        $_SESSION['message'][] = 'Le transfert du fichier a échoué.';
                    
                    }
                    // => On continue si pas d'erreur 

                    // On récupère l'extension du fichier envoyé
                    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

                    // On vérifie si le type d'image accepté est respecté (extension + type MIME)
                    $goodExtensions = ['png', 'jpg', 'jpeg', 'jfif', 'pjpeg', 'pjp'];
                    $goodMimeTypes = ['image/png', 'image/jpeg'];
                    $mimeType = $_FILES['image']['type'];

                    if (!in_array(strtolower($extension), $goodExtensions) || !in_array($mimeType, $goodMimeTypes)){
                        $_SESSION['message'][] = 'Le type de l\'image sélectionnée n\'est pas accepté (PNG et JPG uniquement).';
                    }
           
                    // On vérifie que le poids max du fichier n'est pas dépassé (1024*1024)
                    $tailleMax = 1048576 ;
                    if ($_FILES['image']['size'] > $tailleMax ){
                        $_SESSION['message'][] = 'Le fichier est trop volumineux (1Mo maximum).';
                    }

                    // On génère un nom de fichier (uniqid = chiffre unique basé sur le timestamp actuel -> milliseconde + on l'encode en md5)
                    $nomImage = md5(uniqid()).'.'.$extension;

                    if(isset($_SESSION['message'])  && !empty($_SESSION['message'])){
                        // Si, au moins une erreur, on redirige vers le formulaire
                        header('Location: ajoutarticle.php'); 
                        exit;
                    };

                    // On transfère le fichier
                    if(!move_uploaded_file($_FILES['image']['tmp_name'], __DIR__.'/uploads/'.$nomImage)){
                            //  Transfert échoué
                            header('Location: ajoutarticle.php'); 
                            exit;
                    };

                    // On crée la miniature
                    mini(__DIR__.'/uploads/'.$nomImage, 200);

                    
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
                
                // On envoie un mail à l'admin ---------------------
                // On importe config-mail.php
                require_once 'include/config-mail.php';
                // On crée le mail
                try{
                    // On définit l'expéditeur du mail
                    $sendmail->setFrom('no-reply@abc.fr', 'MonBlog_expéditeur');

                    // On définit le ou les destinataire(s)
                    $sendmail->addAddress('admin@monblog.fr', 'Admin');

                    // On définit le sujet du mail
                    $sendmail->Subject = 'Mon blog : article ajouté';

                    // On active le HTML (true par défaut)
                    $sendmail->isHTML();

                    // On écrit le contenu du message 
                    // en HTML
                    $sendmail->Body = "<h1>Message de blog</h1>
                                    <p>L'article \"$titre\" a été ajouté par {$_SESSION['user']['nickname']}.</p>";
                    // en text brut
                    $sendmail->AltBody = "L'article \"$titre\" a été ajouté par {$_SESSION['user']['nickname']}.";

                    // On envoie le mail
                    $sendmail->send();
                    

                }catch(Exception $e){
                    // Le mail n'est pas parti
                    echo 'Erreur : ' / $e->errorMessage();
                }
                // --------Fin bloc envoi de mail---------
                
                header('Location: '.URL);
                
                
            }else{             
                // Au moins un des champs est invalide
                $_SESSION['message'][] = 'Le formulaire est incomplet.';
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

        <?php 
        // AFFICHAGE DES MESSAGES DE SESSION D'ERREUR 
            if(isset($_SESSION['message'])  && !empty($_SESSION['message'])):
                foreach($_SESSION['message'] as $message):
                ?>
                    <p class="message_erreur"><?=$message?></p>
                <?php
                endforeach;
                unset($_SESSION['message']);
            endif;
        ?>

        <form method="post" enctype="multipart/form-data">
        <!-- Encodage : enctype="multipart/form-data" => active la super globale FILES !! -->
            <div >
                <label for="title">Titre :</label>
                <div>
                    <input type="text" name="title" id="title" 
                    value="<?= isset($_SESSION['form']['title']) ? $_SESSION['form']['title'] : "" ?>" />
                 
                </div>
            </div>

            <div>
                <label for="cat">Catégorie :</label>
                <div>
                    <select name="cat" id="cat"  require>
                        <option>-- Choisir une catégorie --</option>

                        <?php
                        // I) PARTIE POUR RéCUPERER LES DONNéES DE LA BASE
                        $sql = 'SELECT * FROM `categories` ORDER BY `name` ASC;';
                        // On exécute la requête
                        $query = $db->query($sql);
                        // On récupére les données
                        $categories = $query->fetchAll(PDO::FETCH_ASSOC);
                        // On remplit le select
                        foreach($categories as $cat): 
                        ?>

                            <option value="<?= $cat['id']; ?>" 

                            <?php if(isset($_SESSION['form']['cat']) && $_SESSION['form']['cat'] == $cat['id']){
                                echo 'selected';
                            }
                            ?>
                        >
                            <?= $cat['name']; ?>
                        </option>";
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label for="content">Contenu :</label>
                <div><textarea name="content" id="content"><?= isset($_SESSION['form']['content']) ? $_SESSION['form']['content'] : "" ?></textarea></div>
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
            
            <?php 
            // Supprime la partie form de la session pour libérer espace (j'affiche puis j'efface)
            unset($_SESSION['form']); 
            ?>

        </form>
    </section>











<!-- Bonus -->


    <section>
        <h2>Articles précédents</h2>
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
            $articles = $query->fetchAll(PDO::FETCH_ASSOC);

            ?>

            <?php foreach($articles as $article): ?>
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

    </section> 
    </main>
</body>