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