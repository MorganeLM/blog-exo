<?php
// Voir config : https://github.com/PHPMailer/PHPMailer

// PHPMailer est orienté objet, on appelle ses classes avec use
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// On importe les fichiers de PHPMailer
require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';


// On instancie PHPMailer
$sendmail = new PHPMailer(true);

// On configure le serveur SMTP
$sendmail->isSMTP();

// On configure l'encodage des caractères en UTF8
$sendmail->CharSet = "UTF-8";

// On définit l'hôte du serveur
$sendmail->Host       = 'localhost';                   

// On définit le port du serveur
$sendmail->Port       = 1025;                                  
