<?php

    //
    // Fonction permettant la création d'un panier
    //
    function creation_panier() {

        if(!isset($_SESSION["panier"])) {
            $_SESSION["panier"] = array();
            $_SESSION["panier"]["id_produit"] = array();
            $_SESSION["panier"]["quantite"] = array();
            $_SESSION["panier"]["prix"] = array();
            $_SESSION["panier"]["titre"] = array();
            $_SESSION["panier"]["photo"] = array();
            $_SESSION["panier"]["stock"] = array();
        }

    }

    //
    // Fonction permettant d'ajouter le produit au panier
    //
    function ajouter_produit_panier($id_produit, $quantite, $prix, $titre, $photo, $stock) {

        creation_panier();

        // Le produit est-il déjà dans mon panier?
        $positionProduit = array_search($id_produit, $_SESSION["panier"]["id_produit"]);

        if($positionProduit !== false) {
            // Je mets à jour la quantité
            $_SESSION["panier"]["quantite"][$positionProduit] += $quantite;
        } else {
            // Si le produit n'est pas en session
            // Je rajoute le produit dans la sessions panier
            $_SESSION["panier"]["id_produit"][] = $id_produit;
            $_SESSION["panier"]["quantite"][] = $quantite;
            $_SESSION["panier"]["prix"][] = $prix;
            $_SESSION["panier"]["titre"][] = $titre;
            $_SESSION["panier"]["photo"][] = $photo;
            $_SESSION["panier"]["stock"][] = $stock;
        }

    }

    function modifierQuantitePanier($id_produit, $quantite) {

        // Je récupère la position de id_produit dans ma session
        $positionProduit = array_search($id_produit, $_SESSION["panier"]["id_produit"]);

        if($positionProduit !== false) {
            // Je mets à jour la quantité
            $_SESSION["panier"]["quantite"][$positionProduit] = $quantite;
        } 
    }

    //
    // Fonction permettant de calculer le montant total
    //
    function montantTotal() {

        $total = 0;

        // itérer dans le panier 
        // incrémenter le total pour chaque produit sa quantité * prix
        for($i = 0; $i < count($_SESSION["panier"]["id_produit"]); $i++) {
            $total += $_SESSION["panier"]["quantite"][$i] * $_SESSION["panier"]["prix"][$i];
        }

        return $total;

    }

    //
    // Fonction permettant de savoir si un internaute est connecté
    //
    function internauteEstConnecte() {

        if(isset($_SESSION["membre"])) {
            return true;
        } else {
            return false;
        }

    }

    //
    // Fonction permettant de savoir si un internaute est connecté et administrateur du site
    //
    function internauteEstConnecteEtAdmin() {

        if(internauteEstConnecte() && $_SESSION["membre"]["statut"] == 1) {
            return true;
        } else {
            return false;
        }

    }


    //
    // Fonction permettant de retirer un produit du panier
    //
    function retirerProduitDuPanier($id_produit) {

        // Trouver l'index de $idProduit
        $indexIdProduit = array_search($id_produit, $_SESSION["panier"]["id_produit"]);

        // une fois que je l'ai je peux supprimer
        if($indexIdProduit !== false) {

            // la valeur correspondant à l'index dans $_SESSION["panier"]["id_produit"][$indexIdProduit]
            // la valeur correspondant à l'index dans $_SESSION["panier"]["quantite"][$indexIdProduit]
            // la valeur correspondant à l'index dans $_SESSION["panier"]["prix"][$indexIdProduit]
            // la valeur correspondant à l'index dans $_SESSION["panier"]["titre"][$indexIdProduit]

            unset($_SESSION["panier"]["id_produit"][$indexIdProduit]);
            unset($_SESSION["panier"]["quantite"][$indexIdProduit]);
            unset($_SESSION["panier"]["prix"][$indexIdProduit]);
            unset($_SESSION["panier"]["titre"][$indexIdProduit]);
            unset($_SESSION["panier"]["stock"][$indexIdProduit]);
            unset($_SESSION["panier"]["photo"][$indexIdProduit]);
        }

    }
    //
    // Fonction permettant d'afficher le nombre de produit dans le panier au niveau de la navbar
    //
    function afficherNombreProduitsPanier() {
       $totalProduitDansPanier = 0;

        for($i = 0; $i < count($_SESSION["panier"]["id_produit"]); $i++) {
            $totalProduitDansPanier += $_SESSION["panier"]["quantite"][$i];
        }

        return $totalProduitDansPanier;
    }

    //
    // Fonction permettant de réindexer les produits dans le panier après suppresion d'un produit dans le panier
    //

    // array_values renvoit les valeurs indéxées correctement d'un tableau
    // on a besoin de réindéxer les valeurs du panier quand on supprime un produit
    // car si j'ai deux produits dans le panier et que je supprime de la session panier le premier
    // je me retrouve avec un array qui a un élément indéxé à 1 alors que je souhaiterais qu'il soit réindéxé à sa juste valeur
    // c'est à dire 0
    // car dans la page panier, pour afficher les produits dans le panier j'itère dans la session panier
    // et je commence avec $i = 0 pour récupérer le premier index jusqu'au dernier
    // donc essentiel de réindéxer les valeur par ordre croissant après suppression d'un produit

    function mettreAJourIndiceIndexProduitPanier() {
        $_SESSION["panier"]["id_produit"] = array_values($_SESSION["panier"]["id_produit"]);
        $_SESSION["panier"]["quantite"] = array_values($_SESSION["panier"]["quantite"]);
        $_SESSION["panier"]["prix"] = array_values($_SESSION["panier"]["prix"]);
        $_SESSION["panier"]["titre"] = array_values($_SESSION["panier"]["titre"]);
        $_SESSION["panier"]["stock"] = array_values($_SESSION["panier"]["stock"]);
        $_SESSION["panier"]["photo"] = array_values($_SESSION["panier"]["photo"]);
    }

    function pagination($pdo, $nombreArticlePagePage, $requeteSQL) {

        // Déterminer le nombre d'articles à afficher par page
        $articlesParPage = $nombreArticlePagePage;

        //Récupérer la page dans la quelle je suis
        if(isset($_GET["page"]) && !empty($_GET["page"])) {
            $pageEnCours = $_GET["page"];
        } else {
            $pageEnCours = 1;
        }

        // Déterminer combien de pages je vais avoir (je récupère le total des produits à afficher / $articleParPage)
        $nbrArticles = $pdo->query($requeteSQL)->fetchColumn();

        $pages = ceil($nbrArticles / $articlesParPage);

        // Déterminer le premier articles qu'on récupère
        // et le dernier article
        $premierArticle = ($pageEnCours * $articlesParPage)  - $articlesParPage;

        $elemForPagination = ["nbrArticles" => $nbrArticles, "pages" => $pages, "premierArticle" => $premierArticle, "pageEnCours" => $pageEnCours];

        return $elemForPagination;

    }

?>