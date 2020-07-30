<?php
session_start();
require_once '../include/connect.php';
// On supprime la partie 'user' de SESSION
unset($_SESSION['user']);    
header('Location: ../index.php');