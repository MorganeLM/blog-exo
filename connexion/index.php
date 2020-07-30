<?php include_once '../include/header.php' ?>

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
            <button>Valider</button>
        </form>

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
                echo '<p>---- Mot de passe valide! ----</p>';

                var_dump($_SESSION);

                $_SESSION['user'] = [
                    'id'        => $user['id'],
                    'nickname'  => $user['nickname'],
                    'email'     => $user['email'],
                    'roles'     => $user['roles']
                ];
                
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