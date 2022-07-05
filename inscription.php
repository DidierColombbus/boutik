<?php
require_once("inc/init.php");

    if(internauteEstConnecte()) {
        header("location:profil.php");
        exit();
    }

    ////////////////////////////////////////////
    //////////// Inscription ////////////////
    ////////////////////////////////////////////

    $inscriptionDone = false;

    $erreur = '';

    if($_POST) {

        // Vérifier si le pseudo a entre 3 et 20 caractères(strlen)
        if(strlen($_POST["pseudo"]) < 3 || strlen($_POST["pseudo"]) > 20) {
            $erreur .= "<div class='alert alert-danger' role='alert'>
                Le pseudo doit faire entre 3 et 20 caractères!
            </div>";
        }

        // Vérifier si le pseudo a une valeur alphanumérique(preg_match)

        if(!ctype_alnum($_POST["pseudo"])) {
            $erreur .= "<div class='alert alert-danger' role='alert'>
                Veuillez renseigner une valeur alpha numérique !
            </div>";
        }

        // Si c'est pas le cas j'affiche les erreurs

        // L'insert ne se fera que si je n'ai pas d'erreurs

        // echo '<pre>';
        // var_dump($_POST);
        // echo '</pre>';

        if($erreur == '') {

            // Pour éviter les erreurs au niveau de l'insert
            // on va échapper pour chaque donnée du formulaire
            // les caractères succeptibles de provoquer des erreurs SQL *
            // comme l'apostrophe grâce à la fonction php addslashes()
            // pour chaque paramètre post je réaffecte la valeur actuelle du paramètre avec les caractèrs échapés

            // * single quote (')
            // double quote (")
            // backslash (\)
            // NULL

            foreach($_POST as $indice => $valeur) {
                $_POST[$indice] = addslashes($valeur);
            }

            // echo '<pre>';
            // var_dump($_POST);
            // echo '</pre>';

            // Pour des raisons de sécurité nous allons crypter le mot de passe
            $_POST["mdp"] = password_hash($_POST["mdp"], PASSWORD_DEFAULT);

            $count = $pdo->exec("INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, ville, code_postal, adresse, statut)
            VALUES('$_POST[pseudo]', '$_POST[mdp]', '$_POST[nom]', '$_POST[prenom]', '$_POST[email]', '$_POST[civilite]', '$_POST[ville]', '$_POST[code_postal]', '$_POST[adresse]', 2)");

            // Si l'insert a correctement fonctionné msg de confirmation
            if($count > 0 ) {
                $content .= "<div class='alert alert-success' role='alert'>
                    Votre inscription a bien été réalisée!
                </div>";

                $inscriptionDone = true;
            }

        }



    }

require_once("inc/header.php");
?>

<!-- BODY -->
    
    <?php if($erreur != "") { ?>
        <?php echo $erreur; ?>
    <?php } if($inscriptionDone) { ?>
        <?php echo $content; ?>
    <?php } else { ?>

        <form action="" method="POST">
            <div class="form-row">
                <!-- Pseudo -->
                <div class="form-group col-md-6">
                    <label for="pseudo">Pseudo :</label>
                    <input type="text" class="form-control" id="pseudo" placeholder="Pseudo" name="pseudo">
                </div>

                <!-- Password -->
                <div class="form-group col-md-6">
                    <label for="password">Mot de passe :</label>
                    <input type="password" class="form-control" id="password" placeholder="Password" name="mdp">
                </div>

                <!-- Name -->
                <div class="form-group col-md-3">
                    <label for="name">Nom :</label>
                    <input type="text" class="form-control" id="name" placeholder="Name" name="nom">
                </div>

                <!-- First Name -->
                <div class="form-group col-md-3">
                    <label for="firstName">Prénom :</label>
                    <input type="text" class="form-control" id="firstName" placeholder="First Name" name="prenom">
                </div>

                <!-- Email -->
                <div class="form-group col-md-3">
                    <label for="email">Email :</label>
                    <input type="text" class="form-control" id="email" placeholder="Email" name="email">
                </div>

                <!-- Civilité -->
                <div class="form-group col-md-3">
                    <label for="email">Civilité :</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="civilitem" value="m" name="civilite" checked>
                        <label class="form-check-label" for="civilitem">
                        Masculin
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="civilitef" value="f" name="civilite">
                        <label class="form-check-label" for="civilitef">
                        Féminin
                        </label>
                    </div>
                </div>

            </div>

            <!-- Address -->

            <div class="form-group">
                <label for="address">Adresse</label>
                <input type="text" class="form-control" id="address" name="adresse" placeholder="1234 Main St">
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputCity">Ville</label>
                    <input type="text" class="form-control" name="ville" id="inputCity" placeholder="Soissons">
                </div>

                <div class="form-group col-md-6">
                    <label for="inputZip">Code postal</label>
                    <input type="text" class="form-control" id="inputZip" name="code_postal" placeholder="02200">
                </div>
            </div>

            <button type="submit" class="btn btn-dark">Créer mon compte</button>

        </form>

    <?php } ?>


<?php
require_once("inc/footer.php");
?>