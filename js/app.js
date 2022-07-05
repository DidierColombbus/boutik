$(document).ready(function() {

    // Si je choisis une quantité lors de l'ajout du produit dans le panier
    // alors j'active le boutton "Ajouter au panier"
    $("#selectQuantity").change(function(){
        $("#addCart").prop("disabled", false);
    });

    $("select").change(function(){
        $(this).parent().trigger("submit");
    });

});