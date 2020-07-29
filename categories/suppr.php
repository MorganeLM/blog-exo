<?php
// On se connecte à la base de donnée
require_once '../include/connect.php';

// On écrit la requête, on prépare la requête, on injecte les valeurs dans les paramètres, on execute la requête, on redirige
    $sql = "DELETE FROM `categories` WHERE `id` = :id;";    
    $query = $db->prepare($sql);    
    $query->bindValue(':id', $_GET['id'], PDO::PARAM_INT);    $query->execute();
    header('Location: index.php');
?>