// Anzeige der Stationen im Dashboard
function dspStations()
{
    $('#dspStations').html('<div class="text-center"><i class="fa fa-cog fa-spin fa-2x"></i><br>Einen Augenblick bitte');
    $.post("/produktion/frontcorner/dashboard/show", function(resp){
        $('#dspStations').html(resp);
    })
}
function dspStation(id)
{
    $.post("/produktion/frontcorner/dashboard/showStation", { id: ""+ id + "" }, function(resp){
        const res = resp;
        const a = res.split("|");
        $('#rot'+id+'').html(a[2]);
        $('#name'+id+'').html(a[4]);
        $('#vin'+id+'').html(a[3]);
        $('#mode'+id+'').html(a[5]);
        $('#fault'+id+'').html('<i class="fa fa-circle '+a[1]+'"></i>');
        $('#title'+id+'').html('<h3 class="font-size-40 oswald text-center p-0 m-0 line-height-24 '+a[0]+'">'+a[4]+'</h3>');
        $('#opreq'+id+'').html('<i class="fa fa-circle '+a[6]+'"></i>');

    })
}