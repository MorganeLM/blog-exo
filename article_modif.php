<?php
// On vérifie si on a un id dans l'URL
if(isset($_GET['id']) && !empty($_GET['id'])){
    // On a un id, on va chercher l'article dans la base
    // On se connecte
    require_once 'include/header.php';

    // 1- On écrit la requête
    $sql = 'SELECT * FROM `articles` WHERE `id` = :id';
    // 2- On prépare la requêtre
    $query = $db->prepare($sql);
    // 3- On injecte les valeurs dans les paramètres
    $query->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
    // 4- On exécute la requête
    $query->execute();

    // On récupère les données (que pour l'id et fetch assoc pour récupérer que les associations nom-valeur et pas avoir tout en double)
    $articles = $query->fetch(PDO::FETCH_ASSOC);
    
    if(!$articles){ 
        // Ici categories est vide (false) -> retour à page accueil
        header('Location: index.php');
    }
?>


<main id="form_modif_article">
    <section>
        <h2>Modifier mon article : "<?= $articles['title'] ?>"</h2>
        <form method="post" enctype="multipart/form-data">
        <!-- Encodage : enctype="multipart/form-data" => active la super globale FILES !! -->
            <div >
                <label for="title">Titre :</label>
                <div><input type="text" name="title" id="title" value="<?= $articles['title'] ?>"  /></div>
            </div>

            <div>
                <label for="cat">Catégorie :</label>
                <div>
                <select name="cat" id="cat"  require>
                        <option>-- Choisir une catégorie --</option>

                        <?php
                        // I) PARTIE POUR RéCUPERER LES DONNéES DE LA BASE
                        $sql = 'SELECT * FROM `categories` ORDER BY `name` ASC;';
                        // On exécute la requête
                        $query = $db->query($sql);
                        // On récupére les données
                        $categories = $query->fetchAll(PDO::FETCH_ASSOC);
                        // On remplit le select
                        foreach($categories as $cat): 
                        ?>

                            <option value="<?= $cat['id']; ?>" 

                            <?php if($articles['categories_id'] == $cat['id']){
                                echo 'selected';
                            }
                            ?>
                        >
                            <?= $cat['name']; ?>
                        </option>";
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label for="content">Contenu :</label>
                <div><textarea name="content" id="content"><?= $articles['content'] ?></textarea></div>
            </div>

            <div>
                <label for="image">Image :</label>
                <div><input type="file" name="image" id="image" accept="image/png, image/jpeg"></input></div>
            </div>
            
            <button <?php
            if(!isset($_SESSION['user']) && empty($_SESSION['user'])){
                echo 'disabled';
            }
            ?>
            >Modifier</button>
            <p>
                <a href="index.php">Annuler</a>
            </p>
         

        </form>
    </section>
</main>

<?php
// bloc UPDATE
// Ici $categories contient un enregistrementde la base de données (1 ligne)
    if(!empty($_POST)){
        // POST n'est pas vide, on vérifie tous les champs obligatoires
        if(
            isset($_POST['title']) && !empty($_POST['title'])
            && isset($_POST['cat']) && !empty($_POST['cat']) 
            && isset($_POST['content']) && !empty($_POST['content'])  
        ){
            // Tous les champs sont valides
            // A) Protection contre le XSS (navigateur)
            $title = strip_tags($_POST['title']);
            $cat = strip_tags($_POST['cat']);
            $content = strip_tags($_POST['content']);

            // B) Protection contre les injections SQL (bdd)
            // 1- On écrit la requête
            $sql = "UPDATE `articles` SET `title` = :title , `categories_id` = :cat , `content` = :content  WHERE `id`= {$articles['categories_id']};"; 
            // 2- On prépare la requêtre
            $query = $db->prepare($sql);
            // 3- On injecte les valeurs dans les paramètres
            $query->bindValue(':title', $title, PDO::PARAM_STR);
            $query->bindValue(':cat', $cat, PDO::PARAM_STR);
            $query->bindValue(':content', $content, PDO::PARAM_STR);
            // 4- On exécute la requête
            $query->execute();
    
            header('Location: index.php');
        }
    }
// bloc UPDATE fin

}else{
    // Pas d'id ou vide => retour page d'accueil
    header('Location: index.php');
}

?>