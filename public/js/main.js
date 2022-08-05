$(document).ready(function() {
    $("#energieAjax").val("");
    $("#marqueAjax").val("");

    let energie = null;
    let marque = null;
    let prixMax = null;

    $(document).on("change", "#energieAjax",  function() {
        energie = $(this).val();

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
    })

    $(document).on("change", "#marqueAjax",  function() {
        marque = $(this).val();

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
    })
})