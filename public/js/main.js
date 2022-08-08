$(document).ready(function() {
    $("#energieAjax").val("");
    $("#marqueAjax").val("");
    $("#prixAjax").val("");

    /**
     * Filtres
     */
    let energie = null;
    let marque = null;
    let prixMax = null;

    let etat = null;
    let conso = null;

    let sortType = null;

    function filtrer() {
        $.ajax({
            url: "/voiture/search",
            type: "POST",
            data: {
                energie: energie,
                marque: marque,
                prixMax: prixMax
            },
            success: function(data) {
                $("#voiture_index_body").html(data);
            }
        });
    }

    $(document).on("change", "#energieAjax",  function() {
        energie = $(this).val();

        filtrer()
    })

    $(document).on("change", "#marqueAjax",  function() {
        marque = $(this).val();

        filtrer()
    })

    // $(document).on('input', "#prixAjax", function() {
    $(document).on('change', "#prixAjax", function() {
        $("#rangeValue").html($(this).val());
        prixMax = $(this).val();

        filtrer()
    })


    // Affichage de l'unité de l'énergie sélectionnée
    $(document).on('change', "#energie", function() {
        let unite = "L/100 km";
        let newEnergie = $(this).val();

        if (newEnergie == 4) unite = "kWh";

        $("#conso_unite").html("(" + unite + ")");
    })
})