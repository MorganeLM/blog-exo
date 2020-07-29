<?php
// On se connecte à la base de données
try{
    // On essaie de se connecter
    $dsn = 'mysql:dbname=blog;host=localhost';

    // DSN, Utilisateur, Mot de passe
    $db = new PDO($dsn, 'root', '');

    // echo 'La connextion a fonctionné.';

}catch(Exception $erreur){
    // On gère l'échec du "try"
    echo 'La connexion a échoué';
    die;
}

?>
