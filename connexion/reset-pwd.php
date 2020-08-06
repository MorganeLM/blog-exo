<?php
    include_once '../include/header.php' ;

?>

    <main>
        <h1>Réinitialiser votre mot de passe</h1>
        <form method="post" >
            <div>
                <label for="mdp">Rentrez votre nouveau mot de passe :</label>
                <input type="password" id="mdp" name="mdp">
            </div>
            <div>
                <label for="mdp">Confirmez le nouveau mot de passe :</label>
                <input type="password" id="mdp" name="mdp">
            </div>
            <button>Valider ? Il faudra s'en souvenir cette fois ! </button>
        </form>
    </main>

<?php
    var_dump($_GET);

       // On récupère le token pour retrouver l'utilisateur
       $token = $_GET['tk'];

       $sql = "SELECT * FROM `users` WHERE `reset_token`= :token"; 
       $query = $db->prepare($sql);
       // 3- On injecte les valeurs dans les paramètres
       $query->bindValue(':token', $token, PDO::PARAM_STR);
       // 4- On exécute la requête
       $query->execute();
       $user = $query->fetch(PDO::FETCH_ASSOC);

       if($user['expiration_date'] < date('Y-m-d H:i:s')){
            $_SESSION['message'][] = 'Le lien a expiré, veuillez réitérer votre demande.';
            header('Location: '.URL.'/index.php');
            exit;
       }

       echo '<pre>';
       var_dump($user);
       echo '</pre>';
    
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
        
                header('Location: '.URL.'/connexion/index.php');
            }
        }
    }