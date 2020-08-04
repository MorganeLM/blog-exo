<?php 
/**
 * Fonction qui formate une date donnée
 *
 * @param string $origDate
 * @return string
 */
function formatPointDate(string $origDate): string
{
    // On type les paramètres d'entrée et de sortie en string
    //  On définit la langue du site
    setlocale(LC_TIME, 'FR_fr');
    //  ON formate la date dans la langue choisie
    $newDate = strftime('%e %B %Y - %H:%M:%S', strtotime($origDate));
    //  On encode en UTF-8 pour gérer les caractères spéciaux ('août')
    $newDate = utf8_encode($newDate);
    // On retourne la date formatée
    return $newDate;
}



/**
 * Fonction qui formate une date donnée avec liaison "à"
 *
 * @param string $origDate
 * @return string
 */
function formatDate(string $origDate): string
{
    setlocale(LC_TIME, 'FR_fr');
    $newDate = strftime('%e %B %Y', strtotime($origDate));
  
    $newTime = strftime('%T', strtotime($origDate));
    $newDate = utf8_encode($newDate);

    $fullDate = $newDate . ' à ' . $newTime;
    return $fullDate;
}





/**
 * Cette fonction renvoie un extrait du texte raccourci à la longueur demandée
 *
 * @param string $texte
 * @param integer $longueur
 * @return string
 */
function extrait(string $texte, int $longueur): string
{
    //  On décode les caractères HTML
    $texte = htmlspecialchars_decode($texte);
    //  On supprime le HTML (dans extraits pour éviter de ne pas refermer une balise)
    $texte = strip_tags($texte);
    // On raccourcit le texte (qui prend en compte les caractères spéciaux / multibites -> mb)
    $texteReduit = mb_strimwidth($texte, 0, $longueur, "[...]");

    return $texteReduit;
}








/**
 * Cette fonction génére une miniature d'une image (PNG ou JPG) dans la taille demandée (carré)
 * Le nom du fichier généré est issu du nom du fichier source, sous la forme "brouette-300x300.jpg" pour une taille de 300px
 *
 * @param string $fichier Chemin complet du fichier
 * @param integer $taille Taille en pixels
 * @return boolean
 */
function mini(string $fichier, int $taille) : bool
{
    // On récupère le chemin du dossier où se trouve l'image source
    $chemin = pathinfo($fichier, PATHINFO_DIRNAME);
    $nomFichier = pathinfo($fichier, PATHINFO_BASENAME);

    $dimensions = getimagesize($fichier);
    // On définit l'orientation et les décalages qui en découlent
    // On initialise les décalages
    $decalageX = $decalageY = 0;

    switch($dimensions[0] <=> $dimensions[1]){
        case -1 : // Portrait
            $tailleCarre = $dimensions[0];
            $decalageY = ($dimensions[1] - $tailleCarre) / 2;
            break;
        case 0 : // Carré
            $tailleCarre = $dimensions[0];
            break;
        case 1 : // Paysage
            $tailleCarre = $dimensions[1];
            $decalageX = ($dimensions[0] - $tailleCarre) / 2;
    }

    // On vérifie le type Mime de l'image
    switch($dimensions['mime']) {
        case "image/png" :
            $imageTemp = imagecreatefrompng($fichier);
            break;
        case "image/jpeg" :
            $imageTemp = imagecreatefromjpeg($fichier);
            break;
    }

    // On crée une nouvelle image temporaire en mémoire pour créer la copie
    $imageDest = imagecreatetruecolor($taille, $taille);

    // On copie la la partie de l'image source dans l'image de destination
    imagecopyresampled(
        $imageDest, // Image destination
        $imageTemp, // Image source
        0, // Point gauche de la zone de "collage"
        0, // Point supérieur de la zone de "collage"
        $decalageX, // Point gauche de la zone de "copie"
        $decalageY, // Point supérieur de la zone de "copie"
        $taille, // Largeur de la zone de "collage" dest
        $taille, // Hauteur de la zone de "collage" dest
        $tailleCarre, // Largeur de la zone de "copie" src
        $tailleCarre // Hauteur de la zone de "copie" src
    );

    // On enregistre l'image sur le disque
    switch($dimensions['mime']) {
        case "image/png" :
            imagepng($imageDest, $chemin.$nomFichier."-{$taille}x{$taille}.png");
            return true;
        case "image/jpeg" :
            imagejpeg($imageDest, $chemin.'/'.$nomFichier."-{$taille}x{$taille}.jpg");
            return true;
    }
    return false;
}