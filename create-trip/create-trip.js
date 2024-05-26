$(document).ready(function() {
    // Sélectionner l'élément d'entrée de texte pour la date
    $('#datepicker').datepicker({
        // Options de positionnement
        position: {
            my: 'left top', // Position du coin supérieur gauche du calendrier
            at: 'left bottom', // Position du coin inférieur gauche de l'élément d'entrée de texte
            of: $('#datepicker') // Élément auquel le calendrier est aligné
        }
    });
});

// 2 fonctions pour echanger le input et le select afin de choisir si l'on part d'un campus ou de si l'on arrive à un campus 
document.getElementById('Campus').addEventListener('click', (event) => {
    var check = document.getElementById("Campus");
    var select = document.getElementById("depart1");
    var input = document.getElementById("depart2");

    if (check.checked == true) {
        select.style.display = "none";
        input.style.display = "block";
    }else{
        select.style.display = "block";
        input.style.display = "none";
    }
})

document.getElementById('Campus').addEventListener('click', (event) => {
    var check = document.getElementById("Campus");
    var select = document.getElementById("arriver1");
    var input = document.getElementById("arriver2")

    if (check.checked == false) {
        select.style.display = "block";
        input.style.display = "none";
    }else{
        select.style.display = "none";
        input.style.display = "block";
    }

})