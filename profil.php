<?php
require_once("inc/init.php");

// Si je ne suis pas connecté je suis redirigé vers la page de connexion

if(!internauteEstConnecte()) {
    header("location:connexion.php");
    exit();
}

////////////////////////////////////////////
//////////// Récupération des commandes ////////////////
////////////////////////////////////////////

$stmt = $pdo->query('SELECT * FROM commande WHERE id_membre ="' . $_SESSION["membre"]["id_membre"] . '"');
// echo '<pre>';
// var_dump($stmt->fetchAll());
// echo '</pre>';


require_once("inc/header.php");
?>  
    <div class="col-md-12">
        <!-- Message de bienvenu -->
        <?php if($_SESSION["membre"]["civilite"] == "m") { ?>
            <h2 class="text-center mb-5">Bonjour Mr <?= $_SESSION["membre"]["prenom"] . " " . $_SESSION["membre"]["nom"] ?>, bienvenue sur votre espace personnel !</h2>
        <?php } else { ?>
            <h2 class="text-center mb-5">Bonjour Mme <?= $_SESSION["membre"]["prenom"] . " " . $_SESSION["membre"]["nom"] ?>, bienvenue sur votre espace personnel !</h2>
        <?php } ?>
    </div>

    <div class="card col-md-4" style="width:18rem">

        <!-- Avatar -->
        <?php if($_SESSION["membre"]["civilite"] == "m") { ?>
            <img src="photo/avatar_male.png" alt="avatar male" class="card-img-top">
        <?php } else { ?>
            <img src="photo/avatar_female.png" alt="avatar female" class="card-img-top">
        <?php } ?>

        <div class="card-body">
            <h5 class="card-title text-center"> <?= $_SESSION["membre"]["prenom"] . " " . $_SESSION["membre"]["nom"] ?> </h5>
        </div>

        <ul class="list-group list-group-flush">
            <li class="list-group-item text-center"> <?= $_SESSION["membre"]["email"] ?> </li>
            <li class="list-group-item text-center"> <?= $_SESSION["membre"]["adresse"] ?> </li>
            <li class="list-group-item text-center"> <?= $_SESSION["membre"]["code_postal"] . " " . $_SESSION["membre"]["ville"] ?> </li>
        </ul>

    </div>

    <div class="col-md-4">
        <ul class="list-group list-group-flush">
            <li class="list-group-item text-center"> <h5> Mes commandes en cours </h5> </li>

            <?php while($commande = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                <li class="list-group-item text-center">
                    <p> Commande n° <?php echo $commande["id_commande"] . " du " . $commande["date_enregistrement"];  ?> </p>
                    <p class="badge bg-primary"> <?php echo $commande["etat"]; ?> </p>
                </li>
            <?php } ?>


        </ul>

        <ul class="mt-5 list-group list-group-flush">
            <li class="list-group-item text-center"><h5>Mon historique de commande</h5></li>
            <li class="list-group-item text-center">
                <p>Commande n° 1 du 22/01/2020</p>
                <p class="badge bg-primary">En cours de traitement</p>
            </li>
        </ul>
    </div>




<!-- BODY -->


<?php
require_once("inc/footer.php");
?>