<?php
    require_once '../include/header.php' ;

    $erreur1 ="";
    $erreur2 ="";
    $erreur3 ="";
// II) PARTIE POUR AJOUTER LES DONNéES DE LA BASE 
    // On vérifie que POST n'est pas vide
    if(!empty($_POST)){
        // POST n'est pas vide, on vérifie tous les champs obligatoires
        if(
            isset($_POST['name']) && !empty($_POST['name']) 
            && isset($_POST['mdp']) && !empty($_POST['mdp']) 
            && isset($_POST['mdp2']) && !empty($_POST['mdp2'])
            && isset($_POST['email']) && !empty($_POST['email'])
        ){
            if(filter_var(($_POST['email']), FILTER_VALIDATE_EMAIL)){
                if(($_POST['mdp']) === ($_POST['mdp2'])){
                    // Tous les champs sont valides
                    // A) Protection contre le XSS (navigateur)
                    $email = strip_tags($_POST['email']);
                    $nom = strip_tags($_POST['name']);
                    $hashed_mdp = password_hash($_POST['mdp'], PASSWORD_ARGON2I);
                    
        
                    // B) Protection contre les injections SQL (bdd)
                    // 1- On écrit la requête
                    $sql =  "INSERT INTO `users`(`email`, `password`, `nickname`) VALUES (:email, :mdp, :pseudo);";
                    // 2- On prépare la requêtre
                    $query = $db->prepare($sql);
                    // 3- On injecte les valeurs dans les paramètres
                    $query->bindValue(':email', $email, PDO::PARAM_STR);
                    $query->bindValue(':mdp', $hashed_mdp, PDO::PARAM_STR);
                    $query->bindValue(':pseudo', $nom, PDO::PARAM_STR);
                    // 4- On exécute la requête
                    $query->execute();
        
                }else{
                    $erreur1 = 'Les mots de passe ne sont pas identiques.';
                }

            }else{
                die('Email invalide');
                header('Location: index.php');
                $erreur2 = "L'email' est incorrect.";
            }
        }else{
            // Au moins un des champs est invalide
            $erreur3 = "Le formulaire est incomplet, remplissez tous les champs";
   
        }


    }

// I) PARTIE POUR RéCUPERER LES DONNéES DE LA BASE (remplissage tableau)
$sql = 'SELECT * FROM `users` ORDER BY `id`;';
// On exécute la requête
$query = $db->query($sql);

// On récupére les données
$users = $query->fetchAll(PDO::FETCH_ASSOC);
?>


    <main>
        <h2>S'inscrire</h2>
        <form method="post">
            <div>
                <label for="name">Pseudo :</label>
                <div><input type="text" name="name" id="name" /></div>
            </div>
            <div>
                <label for="email">Email :</label>
                <div><input type="email" name="email" id="email" /></div>
            </div>
            <div>
                <label for="mdp">Mot de passe :</label>
                <div><input type="password" name="mdp" id="mdp" /></div>
            </div>
            <div>
                <label for="mdp2">Confirmer le mot de passe :</label>
                <div><input type="password" name="mdp2" id="mdp2" /></div>
            </div>
            <button>Valider</button>
            <div>
                <?php echo "<p>{$erreur1}</p>"?> 
                <?php echo "<p>{$erreur2}</p>"?> 
                <?php echo "<p>{$erreur3}</p>"?> 
            </div>
        </form>
    </main>
</body>