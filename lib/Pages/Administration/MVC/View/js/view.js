// Tabelle mit den Switches anzeigen
function dspTableNetwork(page,div,var1 = "",var2 = ""){
    $.post("" + page + "", function(resp){
        $('#'+div+'').html(resp);
    })
}