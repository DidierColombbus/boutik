<?php

// Accueil du BACK OFFICE

require_once("../inc/init.php");

if($_POST) {

    $count = $pdo->exec("UPDATE contact SET sujet = '$_POST[sujet]' WHERE id_demande_contact = '$_POST[id_demande_contact]'");
}

// Récupérer toutes les commentaires de contact
$stmt = $pdo->query("SELECT * FROM contact");

////////////////////////////////////////////
    //////////// Suppression d'un commentaire ////////////////
    ////////////////////////////////////////////

    if(isset($_GET["action"]) && $_GET["action"] == "suppression") {
        $count = $pdo->exec("DELETE FROM contact WHERE id_demande_contact = '$_GET[id_demande_contact]' ");
        $content .= "<div class=\"col-md-12 alert alert-success\" role=\"alert\">
            Le commentaire a bien été supprimé.
        </div>";
    }

require_once("inc/header.php");

?>


<!-- BODY -->
<h1 class='mb-5 text-center'>Bienvenue dans la partie gestion des commentaires de votre back-office</h1>

<?php echo $content; ?>

<table class="table mb-5">
  <thead class="thead-dark">
    <tr>
        <?php for ($i = 0; $i < $stmt->columnCount(); $i++) {
            $col = $stmt->getColumnMeta($i); ?>
            <th scope="col"><?= $col['name']; ?></th>
        <?php } ?>
    </tr>
  </thead>
  <tbody>
      
        <?php foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $index => $contact) { ?>
            <tr>
                <?php foreach($contact as $index => $valeur) { ?>
                    
                        <td><?php echo $valeur; ?> </td>
                        
                        <?php } ?>
                        <td> <a href="?action=suppression&id_demande_contact=<?= $contact["id_demande_contact"]?>"> Suppresion </a> </td>

            </tr>
       <?php } ?>

  </tbody>
</table>

<?php
    require_once("inc/footer.php");
?>