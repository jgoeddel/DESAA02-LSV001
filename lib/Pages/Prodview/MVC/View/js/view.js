// Anzeige der Stationen einer Linie
function getStationen(id)
{
    $('#dspStationen').html('<div class="text-center"><i class="fa fa-cog fa-spin fa-2x"></i><br>Einen Augenblick bitte');
    $.post("/prodview/linien/stationen", { lineid: "" + id + "" }, function(resp){
        $('#dspStationen').html(resp);
        $('#dspTableStation').html('<div class="text-center"><i class="fa fa-cog fa-spin fa-2x"></i><br>Bitte wählen Sie zuerst eine Station aus');


    })
}
// Anzeige der Tabelle einer Station
function dspTableStation(id)
{
    $('#dspTableStation').html('<div class="text-center"><i class="fa fa-cog fa-spin fa-2x"></i><br>Einen Augenblick bitte');
    $.post("/prodview/linien/station", { sid: "" + id + "" }, function(resp){
        $('#dspTableStation').html(resp);
    })
}
// Anzeige der Linien im Dashboard
function dspLine(id)
{
    $('#dspLine'+id+'').html('<div class="text-center"><i class="fa fa-cog fa-spin fa-2x"></i><br>Einen Augenblick bitte');
    $.post("/prodview/linien/line", { id: "" + id + "" }, function(resp){
        $('#dspLine'+id+'').html(resp);
    })
}