<?php 

    require_once("inc/init.php");

    // La fonction prédéfinie PHP mail() est inactive en localhost.

    if($_POST) {
        
        $to = "email@example.com"; // Votre adresse mail
        $from = $_POST['email']; // L'adresse email de destination
        $prenom = $_POST['prenom']; // Récupération du prénom
        $nom = $_POST['nom']; // Récupération du nom
        $subject = "Formulaire de contact";
        $subject2 = "Copie de votre formulaire de contact";
        $message = $prenom . " " . $nom . " a écrit le message suivant : " . "\n\n" . $_POST['message'];
        $message2 = "Ceci est une copie de votre message " . $prenom . "\n\n" . $_POST['message'];

        $headers = "From:" . $from;
        $headers2 = "From:" . $to;
        // Fonction prédéfinie PHP permettant d'envoyer un email
        mail($to,$subject,$message,$headers); // envoi du mail au propriétaire du site
        mail($from,$subject2,$message2,$headers2); // envoi d'une copie du message à l'envoyeur
        // Possibilité d'utiliser header('Location: thank_you.php'); pour rediriger vers une autre page.

        foreach($_POST as $indice => $valeur) {
            $_POST[$indice] = addslashes($valeur);
        }

        // Ajout de la demande de contact en BDD
        $count = $pdo->exec("INSERT INTO contact (prenom, nom, email, telephone, sujet, message)
        VALUES('$_POST[prenom]', '$_POST[nom]', '$_POST[email]', '$_POST[telephone]', '$_POST[sujet]', '$_POST[message]' )");

        if($count > 0) {
            // Message de confirmation affiché à l'écran
            $content .= "<div class=\"col-md-12 alert alert-success\" role=\"alert\"> 
                Votre message a bien été envoyé, notre équipe s'engage à vous répondre dans un délai de 48h.
            </div>";
        }

    }

    require_once("inc/header.php");
?>

<h1 class="text-center">Laissez-nous vos coordonnées et notre équipe reprendra contact avec vous.</h1>


<?php if(isset($count) && $count > 0) { ?>
    <?php echo $content; ?>
<?php } else { ?>
    <form class="row col-md-10" method="post">
        <div class="form-group col-md-6">
            <label for="prenom">Prénom :</label>
            <input type="text" name="prenom" class="form-control" id="prenom" aria-describedby="prenom" placeholder="Prénom">
        </div>
        <div class="form-group col-md-6">
            <label label for="name">Nom :</label>
            <input type="text" name="nom" class="form-control" id="name" placeholder="Nom">
        </div>
        <div class="form-group col-md-6">
            <label label for="name">Email :</label>
            <input type="email" name="email" class="form-control" id="email" placeholder="Email">
        </div>
        <div class="form-group col-md-6">
            <label label for="name">Téléphone :</label>
            <input type="telephone" name="telephone" class="form-control" id="telephone" placeholder="Téléphone">
        </div>
        <div class="form-group col-md-12">
            <label for="exampleFormControlSelect1">En quoi pouvons-nous vous aider ?</label>
            <select class="form-control" id="exampleFormControlSelect1" name="sujet">
                <option>J'ai une question sur une commande !</option>
                <option>J'ai une question sur un produit !</option>
                <option>Je souhaite contacter le service après vente.</option>
                <option>Je suis fournisseur.</option>
            </select>
        </div>
        <div class="form-group col-md-12">
            <label for="message">Votre message</label>
            <textarea class="form-control" id="message" name="message" rows="3"></textarea>
        </div>
        <div class="form-group col-md-12">
            <button type="submit" class="btn btn-primary">Envoyer votre message</button>
        </div>
    </form>
<?php } ?>

<?php

    require_once("inc/footer.php");

?>
