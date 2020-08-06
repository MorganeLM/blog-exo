<?php

use PHPMailer\PHPMailer\Exception;

include_once '../include/header.php' ;
?>

<main>
    <h1>Demande de réinitialisation de mot de passe</h1>
    <p>Veuillez entrer votre email pour recevoir un lien de réintialisation.</p>
    <form method="post" >
        <label for="email">Email :</label>
        <input type="email" id="email" name="email">
        <button>Valider</button>
    </form>
</main>


<?php
    if(isset($_POST['email']) && !empty($_POST['email'])){
        // On vérifie que le format de l'email est valide
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            $_SESSION['message'][] = 'email invalide';
            header('Location: forgot-pwd.php');
            exit;
        } 
        // On récupère l'email (filter_var donc pas de striptags..)
        $email = $_POST['email'];
        
        // I) PARTIE POUR RéCUPERER LES DONNéES DE LA BASE
        // 1- On écrit la requête
        $sql = 'SELECT * FROM `users`  
        WHERE `email` = :email;';
        // 2- On prépare la requêtre
        $query = $db->prepare($sql);
        // 3- On injecte les valeurs dans les paramètres
        $query->bindValue(':email', $email, PDO::PARAM_STR);
        // 4- On exécute la requête
        $query->execute();

        // On récupère les données (que pour l'id et fetch assoc pour récupérer que les associations nom-valeur et pas avoir tout en double)
        $user = $query->fetch(PDO::FETCH_ASSOC);

        // On vérifie si l'utilisateur n'existe pas
        if(!$user['email']){
            $_SESSION['message'][] = 'La demande a bien été envoyée.';
        }
        // On envoie un mail à l'utilisateur ---------------------
            // On importe config-mail.php
        require_once '../include/config-mail.php';
        // On crée le mail
        try{
            // On gène un token et sa date d'expiration
            $token = md5(uniqid());
            $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // On écrit la requête
            $sql = "UPDATE `users` SET `reset_token` = '$token' , `expiration_date` = '$expiration' WHERE `email` = '{$user['email']}';"; 
            // On exécute la requête
            $query = $db->query($sql);


            // On définit l'expéditeur du mail
            $sendmail->setFrom('no-reply@abc.fr', 'MonBlog_expéditeur');

            // On définit le ou les destinataire(s)
            $sendmail->addAddress($user['email'], $user['nickname']);

            // On définit le sujet du mail
            $sendmail->Subject = 'Mon blog : mot de passe';

            // On active le HTML (true par défaut)
            $sendmail->isHTML();

            // On écrit le contenu du message 
            $lien = URL.'/connexion/reset-pwd.php?tk='.$token;
            // en HTML
            $sendmail->Body = "<h1>Réinitialisation du mot de passe</h1>
                            <p>Cliquez sur ce lien pour réinitialiser votre mot de passe : 
                            <a href='$lien'>$lien</a>.</p>
                            <p> Ce lien est actif durant 1 heure.";
            // en text brut
            $sendmail->AltBody = "Cliquez sur ce lien pour réinitialiser votre mot de passe : \n $lien.";

            // On envoie le mail
            $sendmail->send();
            $_SESSION['message'][] = 'La demande a bien été envoyée.';
            header('Location: '.URL.'/index.php');
            exit;

          

        }catch(Exception $e){
            // Le mail n'est pas parti
            echo 'Erreur : ' / $e->errorMessage();
        }
        // --------Fin bloc envoi de mail---------
        
    }
?>