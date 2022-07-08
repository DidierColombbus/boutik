<?php
require_once("inc/init.php");


// Si je me déconnecte alors je vide la session membre
if(isset($_GET["action"]) && $_GET["action"] == "deconnexion") {
    unset($_SESSION["membre"]); // je supprime la session membre dans ma session
}

// Si je suis déjà connecté
// je redirige vers la page profil
if(internauteEstConnecte()) {
    // Redirection vers la page profil
    header("location:profil.php");
    exit();
}

//  Si je me connecte
if($_POST) {

    $r = $pdo->query("SELECT * FROM membre WHERE pseudo = '$_POST[pseudo]' ");

    // Si j'ai des données liées au pseudo renseigné dans le formulaire
    if($r->rowCount() > 0) {
        // je peux fetcher le membre
        $membre = $r->fetch(PDO::FETCH_ASSOC);

        // Je compare le mot de passe saisi dans le formulaire avec le mot de passe crypté en base (hachage)

        if(password_verify($_POST["password"], $membre["mdp"])) {
            // si password_verify renvoie true c'est que le mot de passe renseigné dans le formulaire correspond au mot de passe crypté en BDD

            // créer une session membre avec les coordonnées du membre
            $_SESSION["membre"]["id_membre"] = $membre["id_membre"];
            $_SESSION["membre"]["nom"] = $membre["nom"];
            $_SESSION["membre"]["prenom"] = $membre["prenom"];
            $_SESSION["membre"]["email"] = $membre["email"];
            $_SESSION["membre"]["civilite"] = $membre["civilite"];
            $_SESSION["membre"]["code_postal"] = $membre["code_postal"];
            $_SESSION["membre"]["adresse"] = $membre["adresse"];
            $_SESSION["membre"]["ville"] = $membre["ville"];
            $_SESSION["membre"]["statut"] = $membre["statut"];

            // Si je suis admin je suis redirigé directement vers le backoffice
            if($_SESSION["membre"]["statut"] == 1) {
                // Redirection vers la page profil
                header("location:admin/index.php");
                exit();
            } else {
                // Redirection vers la page profil
                header("location:profil.php");
                exit();
            }
            
            // echo '<pre>';
            // var_dump($_SESSION["membre"]);
            // echo '</pre>';

            // rediriger vers la page profil

        } else {
            // Si le mot de passe rentré dans le formulaire ne correspond pas au mdp en BDD
                $content .= "<div class='alert alert-danger' role='alert'>
                    Veuillez vérifier votre mot de passe!
                </div>";

        }
        // Si je n'ai pas ce pseudo en base
    } else {

        $content .= "<div class='alert alert-danger' role='alert'>
            Veuillez vérifier votre pseudo s'il vous plaît.
        </div>";

    }

} 




require_once("inc/header.php");
?>

<div class="col-md-12">
    <?php echo $content; ?>
</div>

<div class="col-md-12">
    <h3 class='text-center mb-5'>Connectez-vous pour accéder à votre profil.</h3>
</div>

<div class="col-md-5">
    <form method="post" action="">
    <div class="form-group">
        <label for="pseudo">Pseudo :</label>
        <input type="text" name="pseudo" class="form-control" id="pseudo" aria-describedby="pseudo" placeholder="Votre pseudo">
    </div>
    <div class="form-group">
        <label for="exampleInputPassword1">Mot de passe :</label>
        <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Votre mot de passe">
    </div>
    <button type="submit" class="btn btn-dark">Me connecter</button>
    </form>
</div>

<?php
require_once("inc/footer.php");
?>