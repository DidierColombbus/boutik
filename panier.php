<?php
require_once("inc/init.php");

    ////////////////////////////////////////////
    //////////// RETIRER UN PRODUIT DU PANIER ////////////////
    ////////////////////////////////////////////


    if(isset($_GET["action"]) && $_GET["action"] == "suppressionArticle") {
        $content .= "<div class='col-md-12 mb-5'> <span class='badge badge-dark'>
        Votre article a bien été retiré du panier !</span> </div>";
        retirerProduitDuPanier($_GET["id_produit"]);
        mettreAJourIndiceIndexProduitPanier();
    }

    ////////////////////////////////////////////
    //////////// VIDER LE PANIER ////////////////
    ////////////////////////////////////////////

    if(isset($_GET["action"]) && $_GET["action"] == "viderPanier") {
        $content .= "<div class='col-md-12 mb-5'> <span class='badge badge-dark'>Panier vidé !</span> </div>";
        unset($_SESSION["panier"]);
    }

    ////////////////////////////////////////////
    //////////// MODIFIER LA QUANTITÉ ////////////////
    ////////////////////////////////////////////
    if($_POST && isset($_POST["modifierQuantite"])) {
        modifierQuantitePanier($_POST["id_produit"], $_POST["quantite"]);
        $content .= "<div class=\"col-md-12 alert alert-success\" role=\"alert\">
            La quantité pour le produit " . $_POST["titre"]  . " a bien été modifiée !
        </div>";
    }

    ////////////////////////////////////////////
    //////////// PAYER ////////////////
    ////////////////////////////////////////////

    // L'idée générale
    // au moment du paiment je vérifie la disponibilité des produits dans le panier
    // Si le stock pour le produit séléctionné est >= à la quantité séléctionnée => pas d'erreur
    // Si le stock pour le produit séléctionné est < à la quantité séléctionnée => 2 scénarios possible :
    // Soit c'est une rupture de stock dans ce cas la j'enlève le produit du panier et msg d'info qui dit le produit tata est expiré nous allons retirer de votre panier
    // Le stock n'est pas vide du coup je mets à jour la quantité séléctionnée avec le stock dispo et msg d'info 'la quantité pour tel produit a été mise à jour'

    // Je récupère les produits en session
    // pour chaque produit en session je récupère le stock en BDD
    // je compare la quantité séléctionnée à ce stock
    // je génére ou non un msg d'erreur

    if($_POST && isset($_POST["payer"])) {

        // J'itère dans les produits du panier
        for($i = 0; $i < count($_SESSION["panier"]["id_produit"]); $i++) {
            // Pour chaque produit je récupère les infos en BDD
            $r = $pdo->query('SELECT * FROM produit WHERE id_produit = "' . $_SESSION["panier"]["id_produit"][$i] . '"');
            $produit = $r->fetch(PDO::FETCH_ASSOC);

            // Si le stock pour le produit séléctionné est < à la quantité séléctionnée => 2 scénarios possible :
            if($produit["stock"] < $_SESSION["panier"]["quantite"][$i]) {

                // Soit c'est une rupture de stock dans ce cas la j'enlève le produit du panier et msg d'info qui dit le produit tata est expiré nous allons retirer de votre panier

                if($produit["stock"] <= 0) {
                    
                    $content .= "<div class=\"col-md-12 alert alert-danger\" role=\"alert\">
                        Le stock est épuisé pour l'article " . $_SESSION["panier"]["titre"][$i] . "!
                    </div>";
                    retirerProduitDuPanier($_SESSION["panier"]["id_produit"][$i]);
                    $i--;

                } else {

                    // Le stock n'est pas vide du coup je mets à jour la quantité séléctionnée avec le stock dispo et msg d'info 'la quantité pour tel produit a été mise à jour'

                    $_SESSION["panier"]["quantite"][$i] = $produit["stock"];
                    $content .= "<div class=\"col-md-12 alert alert-warning\" role=\"alert\">
                        La quantité pour le produit " . $_SESSION["panier"]["titre"][$i] . "a été réduite à $produit[stock] car le stock était insufisant pour vos achats !
                    </div>";

                }

                $erreur = true;

            }

        }


        ////////////////////////////////////////////
        //////////// COMMANDE ////////////////
        ////////////////////////////////////////////

        // si j'ai pas d'erreur (si tous les stocks sont suffisant pour la quantité séléctionnée) alors je génére une commande

        if(!isset($erreur)) {

            // si pas de msg d'erreur insert de la commande en BDD

            // commande : id_membre, montant; date_enregistrement (NOW())
            $idMembre = $_SESSION["membre"]["id_membre"];
            $montant = montantTotal();
            $pdo->exec("INSERT INTO commande (id_membre, montant, date_enregistrement, etat) VALUES('$idMembre', '$montant', NOW(),'en cours de traitement')");

            // Récupérer l'id de la commande générée
            $idCommande = $pdo->lastInsertId();
            // et pour chaque produit dans le panier
            // alimenter la table details_commande avec l'id de la commande

            for($i = 0; $i < count($_SESSION["panier"]["id_produit"]); $i++) {
                
                $pdo->exec('INSERT INTO details_commande (id_produit, id_commande, quantite, prix)
                VALUES(
                    " ' . $_SESSION["panier"]["id_produit"][$i] . ' ",
                    " ' . $idCommande . ' ",
                    " ' . $_SESSION["panier"]["quantite"][$i] . ' ",
                    " ' . $_SESSION["panier"]["prix"][$i] . ' "
                )');

                // mettre à jour le stock des produits
                $pdo->exec('UPDATE produit SET stock = stock - " ' . $_SESSION['panier']['quantite'][$i] . ' " WHERE id_produit = "' . $_SESSION['panier']['id_produit'][$i] . '" ');

            }

            // vider le panier
            unset($_SESSION["panier"]);
            // msg de confirmation en affichant le numéro de la commande

            $content .= "<div class=\"col-md-12 alert alert-success\" role=\"alert\">
                Merci pour votre commande ! Votre numéro de suivi est le n° $idCommande 
            </div>";

        }

    }

    // L'idée générale c'est d'afficher dans le panier les produits sélectionnés
    // par l'internaute
    // possibilité de supprimer un produit du panier
    // possibilité de payer (de manière fictive)
    // avant le paiment revérifier le stock des produits séléctionnés

    ////////////////////////////////////////////
    //////////// AFFICHER LES PRODUITS DANS LE PANIER ////////////////
    ////////////////////////////////////////////

    if(isset($_POST["ajout_panier"])) {

        $r = $pdo->query("SELECT * FROM produit WHERE id_produit = '$_POST[id_produit]'");
        $produit = $r->fetch(PDO::FETCH_ASSOC);

        ////////////////////////////////////////////
        //////////// AJOUTER À LA SESSION LE PRODUIT SÉLECTIONNÉ ////////////////
        ////////////////////////////////////////////
        ajouter_produit_panier($_POST["id_produit"], $_POST["quantite"], $produit["prix"], $produit["titre"], $produit["photo"], $produit["stock"]);

        // Pour voir le panier 
        // echo '<pre>';
        // var_dump($_SESSION);
        // echo '</pre>';

    }

    // Lien pour vider le panier
    if(!empty($_SESSION["panier"]["id_produit"])) {
        $content .= "<div class='col-md-12 mb-5'>";
        $content .= "<a href='?action=viderPanier' class='badge badge-danger'> Vider le Panier </a>";
        $content .= "</div>";

    }

    // Début de la table panier
    $content .= "<table class='table mb-5'>
    <thead>
        <tr>
            <th scope='col'>Titre</th>
            <th scope='col'>Quantité</th>
            <th scope='col'>Prix</th>
            <th scope='col'>Miniature</th>
            <th scope='col'>Action</th>
        </tr>
    </thead>
    <tbody>";

    // Si j'ai pas de produit en session je n'affiche rien dans mon tableau "Panier vide"
    if(empty($_SESSION["panier"]["id_produit"])) {
        $content .= "<tr> <td colspan='3'> Votre panier est vide </td>  </tr>";

    // Fin de la table
    $content .= "</tbody> </table>";

    if(isset($_POST["ajout_panier"])) {
        $content .= "<div class='col-md-12'> <a href='index.php?categorie=$_POST[categorie]' class='badge badge-dark text-white'>Retourner à la catégorie $_POST[categorie]</a> </div>";
    }


    } else {

        // Si la session panier n'est pas vide, alors j'affiche les produits dans la table
        for($i = 0; $i < count($_SESSION["panier"]["id_produit"]); $i++) {
            $content .= "<tr>";
            // Trois façons de faire la même chose ci-dessous (double guillemets, et concaténation...)
            $content .= '<td> ' . $_SESSION["panier"]["titre"][$i] . '</td>';
            $content .= "<td>
            <form method='post' action='' name='quantite'>
                <input type='hidden' name='modifierQuantite' value=''>
                <input type='hidden' name='titre' value='". $_SESSION["panier"]["titre"][$i] ."'>
                <input type='hidden' name='id_produit' value='". $_SESSION["panier"]["id_produit"][$i] ."'>
                <select class='form-control' name='quantite'>";
                for($j = 1; $j <= $_SESSION["panier"]["stock"][$i]; $j++) {
                    if($j == $_SESSION["panier"]["quantite"][$i]) {
                        $content .=  "<option value='$j' selected>$j</option>";
                    }else {
                        $content .=  "<option value='$j'>$j</option>";
                    }
                }
             $content .=  "</select>
            </form>
            </td>";
            $content .= "<td>" . $_SESSION["panier"]["prix"][$i] . "</td>";
            $content .= "<td> <img style='width:50px' src='". $_SESSION["panier"]["photo"][$i]  ."' alt=''> </td>";
            $content .= "<td> <a href='?action=suppressionArticle&id_produit=" . 
            $_SESSION["panier"]["id_produit"][$i] . "'> Supprimer <a/></td>";
            $content .= "</tr>";
        }

            ////////////////////////////////////////////
            //////////// LE MONTANT TOTAL ////////////////
            ////////////////////////////////////////////

            $content .= "<tr> <td colspan='5' style='text-align:right'> <strong> Montant Total : </strong> " . montantTotal() . " € </td> </tr>";

            // Fin de la table
            $content .= "</tbody> </table>";
            if(isset($_POST["ajout_panier"])) {
                $content .= "<div class='col-md-12'> <a href='index.php?categorie=$_POST[categorie]' class='badge badge-dark text-white'>Retourner à la catégorie $_POST[categorie]</a> </div>";
            }
            ////////////////////////////////////////////
            //////////// AFFICHER LE BOUTTON POUR PAYER ////////////////
            ////////////////////////////////////////////

            if(internauteEstConnecte()) {
                $content .= "<div class='d-flex justify-content-end col-md-12'>";
                $content .= "<form method='post' action=''>";
                $content .= "<input type='submit' class='btn btn-outline-secondary' name='payer' value='Payer'>";
                $content .= "</form>";
                // J'afficher le bouton payer
            } else {
                // J'affiche le bouton se connecter
                $content .= "<div class='text-center'> <p> Veuillez-vous connecter pour payer. </p>";
                $content .= "<a href='connexion.php'> Se connecter </a> </div> ";
            }

    }





require_once("inc/header.php");
?>



<!-- BODY -->

<?php echo $content; ?>


<?php
require_once("inc/footer.php");
?>