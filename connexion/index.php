<?php include_once '../include/header.php' ?>
<!-- Fichier connexion (ici connexion/index.php) a mettre de préférence à la racine, sinon penser à changer le path du cookie -->
<?php 
// AFFICHAGE DES MESSAGES DE SESSION D'ERREUR (RESET PWD)
    if(isset($_SESSION['message'])  && !empty($_SESSION['message'])):
        foreach($_SESSION['message'] as $message):
        ?>
            <p class="message_success"><?=$message?></p>
        <?php
        endforeach;
        unset($_SESSION['message']);
    endif;
?>
<?php 
// AFFICHAGE DES INFOS UTILISATEURS SI SE SOUVENIR DE MOI ACTIF

?>

    <main>
        <h2>Se connecter :</h2>
        <form method="post">
            <div>
                <label for="email">Email :</label>
                <div><input type="email" name="email" id="email" /></div>
            </div>
            <div>
                <label for="mdp">Mot de passe :</label>
                <div><input type="password" name="mdp" id="mdp" /></div>
            </div>
            <div>     
                <input type="checkbox" name="remember" id="remember" />
                <label for="remember">se souvenir de moi </label>
            </div>
           
            <button>Valider</button>
        </form>
        <p><a href="forgot-pwd.php">Mot de passe oublié ?</a></p>

<?php
    if(!empty($_POST)){
        // POST n'est pas vide, on vérifie tous les champs obligatoires
        if(
            isset($_POST['email']) && !empty($_POST['email']) 
            && isset($_POST['mdp']) && !empty($_POST['mdp']) 
        ){
            // Tous les champs sont valides
            // A) Protection contre le XSS (navigateur)
            $email = strip_tags($_POST['email']);
            $mdp_entre = strip_tags($_POST['mdp']);

            // B) Protection contre les injections SQL (bdd)
            // 1- On écrit la requête
            $sql =  "SELECT * FROM `users` WHERE `email` = :email;";
            // 2- On prépare la requêtre
            $query = $db->prepare($sql);
            // 3- On injecte les valeurs dans les paramètres
            $query->bindValue(':email', $email, PDO::PARAM_STR);
            // 4- On exécute la requête
            $query->execute();
    
            // On récupére les données
            $user = $query->fetch(PDO::FETCH_ASSOC);
            // verifier le mot de pass ($pass => mdp haché), return true
            // var_dump($hashed_mdp);
            $valid = password_verify($mdp_entre, $user['password']);

            if ($valid) {

                $_SESSION['user'] = [
                    'id'        => $user['id'],
                    'nickname'  => $user['nickname'],
                    'email'     => $user['email'],
                    'roles'     => $user['roles']
                ];

                if(isset($_POST['remember']) && $_POST['remember'] == true){
                    $token_cookie = md5(uniqid());
                    setcookie('RememberMe', $token_cookie, [
                        'samesite' => 'Strict',
                        'expires' => strtotime('+ 1 week'),
                        'path' => '/blog'
                    ]);

                    $sql = "UPDATE `users` SET `remember_token` = '$token_cookie' WHERE `id` = '{$user['id']}';"; 
                    // On exécute la requête
                    $query = $db->query($sql);
                }
                // echo '<pre>';
                // var_dump($_COOKIE);
                // echo '</pre>';
                // exit;

                header('Location: ../index.php');

            } else {
                echo '<p>---- Mot de passe et/ou adresse mail erroné.e.(s). -----</p>';
            }
        }
    }
?>
    </main>
</body>
</html>