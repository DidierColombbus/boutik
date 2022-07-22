<?php

// Accueil du BACK OFFICE

require_once("../inc/init.php");


    ////////////////////////////////////////////
    //////////// Récupérer les informations liées au produit que l'on veut modifier ////////////////
    ////////////////////////////////////////////

    if(isset($_GET["action"]) && $_GET["action"] == "modification") {
        $r = $pdo->query("SELECT * FROM produit WHERE id_produit = '$_GET[id_produit]' ");
        $produit = $r->fetch(PDO::FETCH_ASSOC);
    }

    ////////////////////////////////////////////
    //////////// Suppression d'un produit ////////////////
    ////////////////////////////////////////////

    if(isset($_GET["action"]) && $_GET["action"] == "suppression") {
        $count = $pdo->exec("DELETE FROM produit WHERE id_produit = '$_GET[id_produit]' ");
        $content .= "<div class=\"col-md-12 alert alert-success\" role=\"alert\">
            Le produit a bien été supprimé !
        </div>";
    }

    ////////////////////////////////////////////
    //////////// Suppression d'un produit ////////////////
    ////////////////////////////////////////////
    $elementsPourLaPagination = pagination($pdo, 3, "SELECT COUNT(*) FROM produit");
    
    // echo '<pre>';
    // var_dump($elementsPourLaPagination);
    // echo '</pre>';

    
    // Afficher les données dans un tableau
    // Faudra rajouter deux colonnes (modification/suppresion)
    // 2 évènements post : modif et suppresion

    // En dessous du tableau on va avoir un formulaire qui va permettre deux choses : ajouter un produit / modifier

    // Si je suis dans le cadre d'un post modification
    // Je vais pré charger les infos du produit à modifier dans le formulaire


    if($_POST) {

        ////////////////////////////////////////////
        //////////// Ajout d'un produit ////////////////
        ////////////////////////////////////////////


        ////////////////////////////////////////////
        //////////// TRAITEMENT DE L'INPUT TYPE FILE ////////////////
        ////////////////////////////////////////////

        $fileLoaded = false;

        if(!empty($_FILES) && !empty($_FILES["maPhoto"]["name"])) {

            // Récupérer le nom de la photo
            $nomPhoto = $_POST["reference"] . "_" . $_FILES["maPhoto"]["name"];
            // echo '<pre>';
            // var_dump($maPhoto);
            // echo '</pre>';

            // Copier le lien vers la photo en BDD
            $chemin_vers_la_photo_en_terme_durl_pour_attribut_src = URL . "photo/" . $nomPhoto;

            // Fichier de départ à copier
            // il correspond au fichier temporaire uploadé au niveau de l'input type file
            // il faut récupérer le répertoire de ce fichier temporaire uploadé et le copier vers le répértoire de destination
            // tmp_name correspond au fichier chargé que l'on souhaite copier

            // Copier la photo sur le serveur (préciser le bon chemin du dossier)
            $dossier_sur_serveur_pour_enregistrer_photo = RACINE_SITE . "photo/" . $nomPhoto;
            copy($_FILES["maPhoto"]["tmp_name"], $dossier_sur_serveur_pour_enregistrer_photo);

            $fileLoaded = true;

        }

        ////////////////////////////////////////////
        //////////// TRAITEMENT DE L'INPUT TYPE FILE ////////////////
        ////////////////////////////////////////////

        // Permet d'échapper les caractères succeptibles de créer des erreurs sql
        foreach($_POST as $indice => $valeur) {
            $_POST[$indice] = addslashes($valeur);
        }

        if(isset($_POST["ajouterProduit"])) {

            $count = $pdo->exec("INSERT INTO produit (reference, categorie, titre, description, couleur, taille, public, photo, prix, stock) VALUES(
                '$_POST[reference]',
                '$_POST[categorie]',
                '$_POST[titre]',
                '$_POST[description]',
                '$_POST[couleur]',
                '$_POST[taille]',
                '$_POST[public]',
                '$chemin_vers_la_photo_en_terme_durl_pour_attribut_src',
                '$_POST[prix]',
                '$_POST[stock]'
            )");

            if($count > 0) {
                $content .= "<div class=\"col-md-12 alert alert-success\" role=\"alert\">
                    Le produit avec la référence " . $_POST['reference'] . " a bien été ajouté !
                </div>";
            }

        } else {

            // Si j'ai chargé une photo dans mon input type file
            // je récupère le chemin générer vers la photo
            // si je modifie le produit sans modifier la photo
            // je préserve en BDD la photo existente en BDD pour ce produit
            $cheminPhoto = ($fileLoaded) ? $chemin_vers_la_photo_en_terme_durl_pour_attribut_src : $_POST["prevPhoto"];

            ////////////////////////////////////////////
            //////////// Modification d'un produit ////////////////
            ////////////////////////////////////////////

            $count = $pdo->exec("UPDATE produit SET
            reference = '$_POST[reference]',
            categorie = '$_POST[categorie]',
            titre = '$_POST[titre]',
            description = '$_POST[description]',
            couleur = '$_POST[couleur]',
            taille = '$_POST[taille]',
            public = '$_POST[public]',
            photo = '$cheminPhoto',
            prix = '$_POST[prix]',
            stock = '$_POST[stock]' 
            WHERE id_produit = '$_POST[id_produit]'");

            if($count > 0) {
                $content .= "<div class=\"col-md-12 alert alert-success\" role=\"alert\">
                    Le produit avec la référence $_POST[reference] a bien été modifié !
                </div>";
            }

        }

    }

////////////////////////////////////////////
//////////// Récupérer en BDD les produits ////////////////
////////////////////////////////////////////

// le dernier chiffre de $stmt correspond au nombre d'éléments affichés pour la pagination, ici 3

$stmt = $pdo->query("SELECT * FROM produit LIMIT $elementsPourLaPagination[premierArticle], 3");
// echo '<pre>';
// var_dump($stmt->fetchAll(PDO::FETCH_ASSOC));
// echo '</pre>';


//     foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $index => $produit) {
//         foreach($produit as $index => $valeur) {
//             echo '<pre>';
//             var_dump($produit);
//             echo '</pre>';
//         }
//    }

// Initialisation des champs

$idProduit = (isset($produit)) ? $produit["id_produit"] : "";
$reference = (isset($produit)) ? $produit["reference"] : "";
$categorie = (isset($produit)) ? $produit["categorie"] : "";
$titre = (isset($produit)) ? $produit["titre"] : "";
$description = (isset($produit)) ? $produit["description"] : "";
$couleur = (isset($produit)) ? $produit["couleur"] : "";
$taille = (isset($produit)) ? $produit["taille"] : "";
$public = (isset($produit)) ? $produit["public"] : "";
$photo = (isset($produit)) ? $produit["photo"] : "";
$prix = (isset($produit)) ? $produit["prix"] : "";
$stock = (isset($produit)) ? $produit["stock"] : "";

require_once("inc/header.php");

?>


<!-- BODY -->

<h1 class='mb-5 text-center'>Bienvenue dans la partie gestion de produits de votre Back-office</h1>


<!-- TABLE -->

<p>Vos produits en BDD :</p>

<?php echo $content; ?>

<table class="table mb-5">
  <thead class="thead-dark">
    <tr>
        <?php for ($i = 0; $i < $stmt->columnCount(); $i++) {
            $col = $stmt->getColumnMeta($i); ?>
            <th scope="col"><?= $col['name']; ?></th>
        <?php } ?>
        <th scope="col"> Modification  </th>
        <th scope="col"> Suppression  </th>
    </tr>
  </thead>
  <tbody>
        <!-- J'itère dans le fetchAll qui m'indèxe dans un tableau multidimensionnel les arrays contenants mes produits
        Pour chaque array de produit récupéré, j'itère dans les index pour récupérer les valeurs et générer un td pour chaque valeur  -->
        <?php foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $index => $produit) { ?>
            <tr>
                <?php foreach($produit as $index => $valeur) { 
                    if($index == 'photo') { ?>
                        <td> <img style="width:50px" src="<?= $valeur; ?>" alt=""> </td>
                    <?php } else { ?>
                        <td> <?php echo $produit[$index];  ?> </td>
                    <?php } ?>
                <?php } ?>
                
                <!-- Lien de modification et de suppression -->

                <td> <a href="?action=modification&id_produit=<?= $produit["id_produit"]?>#ajout_modif"> Modification </a> </td>
                <td> <a href="?action=suppression&id_produit=<?= $produit["id_produit"]?>"> Suppresion </a> </td>
            </tr>
       <?php } ?>

  </tbody>
</table>

<div class="row col-md-12">
    <nav aria-label="Page navigation example">
    <ul class="pagination">

        <li class="page-item <?php echo ($elementsPourLaPagination["pageEnCours"] == 1) ? "disabled" : ""; ?>">
            <a class="page-link" href="?page=<?php echo $elementsPourLaPagination["pageEnCours"] - 1; ?>">Précédent</a>
        </li>

        <?php for($page = 1; $page <= $elementsPourLaPagination["pages"]; $page++) { ?>
            <li class="page-item <?php echo ($elementsPourLaPagination["pageEnCours"] == $page) ? "active" : ""; ?>">
                <a class="page-link" href="?page=<?php echo $page; ?>"><?php echo $page; ?></a>
            </li>
        <?php } ?>

        <li class="page-item <?php echo ($elementsPourLaPagination["pageEnCours"] == $elementsPourLaPagination["pages"]) ? "disabled" : ""; ?>">
            <a class="page-link" href="?page=<?php echo $elementsPourLaPagination["pageEnCours"] + 1; ?>">Suivant</a>
        </li>
    </ul>
    </nav>
</div>
<!-- Formulaire de modification/ajout de produit -->

<p id="ajout_modif">Ajouter ou Modifier des produits :</p>

<form action="" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id_produit" value="<?php echo $idProduit; ?>">
    <input type="hidden" name="prevPhoto" value="<?php echo $photo; ?>">
    <div class="form-row">
        <div class="form-group col-md-3">
            <label for="reference">Référence</label>
            <input type="text" class="form-control" id="reference" name="reference" value="<?= $reference; ?>">
        </div>
        <div class="form-group col-md-3">
            <label for="categorie">Catégorie</label>
            <input type="text" class="form-control" id="categorie" name="categorie" value="<?= $categorie; ?>">
        </div>
        <div class="form-group col-md-3">
            <label for="titre">Titre</label>
            <input type="text" class="form-control" id="titre" name="titre" value="<?= $titre; ?>">
        </div>
        <div class="form-group col-md-3">
            <label for="couleur">Couleur</label>
            <input type="text" class="form-control" id="couleur" name="couleur" value="<?= $couleur; ?>">
        </div>
        <div class="form-group col-md-3">
            <label for="taille">Taille</label>
            <input type="text" class="form-control" id="taille" name="taille" value="<?= $taille; ?>">
        </div>
        <div class="form-group col-md-3">
            <label for="prix">Prix</label>
            <input type="text" class="form-control" id="prix" name="prix" value="<?= $prix; ?>">
        </div>
        <div class="form-group col-md-3">
            <label for="stock">Stock</label>
            <input type="text" class="form-control" id="stock" name="stock" value="<?= $stock; ?>">
        </div>
        <div class="w-100"></div>

        <!-- FAIRE VARIER LE SELECTED DES INPUTS -->

        <div class="form-group col-md-2">
            <label for="public_m">Public</label>
            <div class="custom-control custom-radio">
                <input type="radio" id="public_m" name="public" class="custom-control-input" value="m" checked>
                <label class="custom-control-label" for="public_m">Masculin</label>
            </div>
        </div>
        <div class="form-group col-md-2">
            <label for="public_f" style="color:transparent">Public</label>
            <div class="custom-control custom-radio">
                <input type="radio" id="public_f" name="public" class="custom-control-input" value="f">
                <label class="custom-control-label" for="public_f">Féminin</label>
            </div>
        </div>
        
        <div class="custom-file mb-5">
            <input type="file" class="custom-file-input" id="maPhoto" name="maPhoto">
            <label class="custom-file-label" for="maPhoto">Choisir une photo</label>

            <!-- Si je suis dans le cadre d'une modification j'affiche l'img actuelle -->
            <?php if(isset($_GET["action"]) && $_GET["action"] == "modification") { ?>
                <img class="mt-1" style="width:75px" src="<?= $photo; ?>" alt="<?= $titre; ?>" title="<?= $description; ?>">
            <?php } ?>

        </div>
        <div class="form-group col-md-12">
            <label for="description">Description</label>
            <input type="text" class="form-control" id="description" name="description" value="<?= $description; ?>">
        </div>

        <?php if(isset($_GET["action"]) && $_GET["action"] == "modification") { ?>
            <button type="submit" class="btn btn-secondary" name="modifierProduit">Modifier un produit</button>
        <?php } else { ?>
            <button type="submit" class="btn btn-secondary" name="ajouterProduit">Ajouter un produit</button>
        <?php } ?>
    </div>
                
</form>



<?php
    require_once("inc/footer.php");
?>