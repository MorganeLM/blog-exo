<?php
session_start();
require_once '../include/connect.php';
// On supprime la partie 'user' de SESSION
unset($_SESSION['user']);    
   

$token_cookie = '';
setcookie('RememberMe', $token_cookie, [
    'expires' => 1,
    'path' => '/blog'
]);

header('Location: ../connexion/index.php');