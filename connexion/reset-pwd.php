<?php

use PHPMailer\PHPMailer\Exception;

include_once '../include/header.php' ;

    // var_dump($_GET);

    // On récupère le token pour retrouver l'utilisateur s'il y en a un et on le nettoie !
    if(!isset($_GET['tk']) && !empty($_GET['tk'])){
        $_SESSION['message'][] = 'Le lien n\'existe pas. Jeton absent';
        header('Location: '.URL.'/index.php');
        exit;
    }

    $token = strip_tags($_GET['tk']);

    // A partir d'ici se connecter à la base de donnée (ne pas inclure le connect dans le header comme fait ici...)
    $sql = "SELECT * FROM `users` WHERE `reset_token`= :token"; 
    $query = $db->prepare($sql);
    // 3- On injecte les valeurs dans les paramètres
    $query->bindValue(':token', $token, PDO::PARAM_STR);
    // 4- On exécute la requête
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);

    //    echo '<pre>';
    //    var_dump($user);
    //    echo '</pre>';


    if(!$user['id']){
        $_SESSION['message'][] = 'Il y a un problème. Jeton invalide';
        header('Location: '.URL.'/index.php');
        exit;
    }

    // Pour la date, autre méthode avec l'objet DateTime :
        // $maintenant = new DateTime();
        // $expiration = new DateTime($user['expiration_date']);

    if(strtotime($user['expiration_date']) < strtotime(date('Y-m-d H:i:s'))){
        $_SESSION['message'][] = 'Le lien a expiré, veuillez réitérer votre demande.';
        $sql = "UPDATE `users` SET `reset_token` = NULL , `expiration_date` = NULL WHERE `id` = '{$user['id']}';"; 
        // On exécute la requête
        $query = $db->query($sql);

        header('Location: '.URL.'/index.php');
        exit;
    }


    if(!empty($_POST)){
        // POST n'est pas vide, on vérifie tous les champs obligatoires
        if(
            isset($_POST['mdp']) && !empty($_POST['mdp']) 
            && isset($_POST['mdp2']) && !empty($_POST['mdp2'])
        ){
            if(($_POST['mdp']) === ($_POST['mdp2'])){
                // Tous les champs sont valides
            
                // A) Protection contre le XSS (navigateur)
                $hashed_mdp = password_hash($_POST['mdp'], PASSWORD_ARGON2I);
    
                // B) Protection contre les injections SQL (bdd)
                // 1- On écrit la requête
                $sql = "UPDATE `users` SET `password` = :mdp WHERE `reset_token`= :token"; 
                // 2- On prépare la requêtre
                $query = $db->prepare($sql);
                // 3- On injecte les valeurs dans les paramètres
                $query->bindValue(':mdp', $hashed_mdp, PDO::PARAM_STR);
                $query->bindValue(':token', $token, PDO::PARAM_STR);
                // 4- On exécute la requête
                $query->execute();


                // On supprime le token
                $sql = "UPDATE `users` SET `reset_token` = NULL , `expiration_date` = NULL WHERE `id` = '{$user['id']}';"; 
                // On exécute la requête
                $query = $db->query($sql);


                // On envoie un mail à l'utilisateur pour l'informer de la mise à jour du mot de passe -------------------
                // On importe config-mail.php
                require_once '../include/config-mail.php';
                // On crée le mail
                try{
                    // On définit l'expéditeur du mail
                    $sendmail->setFrom('no-reply@abc.fr', 'MonBlog_expéditeur');

                    // On définit le ou les destinataire(s)
                    $sendmail->addAddress($user['email'], $user['nickname']);

                    // On définit le sujet du mail
                    $sendmail->Subject = 'Mon blog : mot de passe modifié';

                    // On active le HTML (true par défaut)
                    $sendmail->isHTML();

                    // On écrit le contenu du message 
                    $lien = URL.'/connexion/index.php';
                    // en HTML
                    $sendmail->Body = "<h1>Succès de la                 réinitialisation du mot de passe</h1>
                                    <p>Suite à votre demande, votre mot de passe a bien été modifié. Vous pouvez à nouveau vous connecter à votre compte utilisateur via cette page : 
                                    <a href='$lien'>$lien</a>.</p>
                                    <p> A bientôt ! </p>";
                    // en text brut
                    $sendmail->AltBody = "Succès de la réinitialisation du mot de passe ! \n Suite à votre demande, votre mot de passe a bien été modifié. \n Vous pouvez à nouveau vous connecter à votre compte utilisateur via cette page : \n $lien. \n A bientôt !";

                    // On envoie le mail
                    $sendmail->send();

                }catch(Exception $e){
                    // Le mail n'est pas parti
                    echo 'Erreur : ' / $e->errorMessage();
                }
                // --------Fin bloc envoi de mail---------
                
                // Tout est bon, on renvoit à la page de connexion avec un message de succès
                $_SESSION['message'][] = 'Le mot de passe a été modifié avec succès. Veuillez vous connecter avec votre nouveau mot de mot de passe';

                header('Location: '.URL.'/connexion/index.php');
            }
            else{
                echo '<p class="message_erreur">Les mots de passe doivent être identiques.</p>';
            }
        }else{
            echo '<p class="message_erreur">Le formulaire est incomplet.</p>';
        }
    }
?>



    <main>
        <h1>Réinitialiser votre mot de passe</h1>
        <form method="post" >
            <div>
                <label for="mdp">Rentrez votre nouveau mot de passe :</label>
                <input type="password" id="mdp" name="mdp">
            </div>
            <div>
                <label for="mdp2">Confirmez le nouveau mot de passe :</label>
                <input type="password" id="mdp2" name="mdp2">
            </div>
            <button>Valider ? Il faudra s'en souvenir cette fois ! </button>
        </form>
    </main>

