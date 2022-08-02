// Höhe Container
function heightMainContainer(){ // Passt die Höhe des Main Container an
    "use striict";
    var parentHeight = $("#body").parent().height();
    var topHeight = $("#fixTop").height();
    var parHeight = $("#parallax").height();
    var footerHeight = $("#footer").height();
    var mainHeight = parentHeight-topHeight-parHeight-footerHeight+34;
    $("#main").css("min-height", ""+mainHeight+"px");
}
// Höhe Header
function heightHeader(){
    var topHeight = $("#fixTop").height();
    var navHeight = $("#mnav").height();
    var Height = topHeight+navHeight+10;
    $("#main").css("margin-top", ""+Height+"px");
}
// TOAST
Toast = Swal.mixin({
    toast: true,
    position: 'top-center',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
})
toastr.options = {
    "closeButton": false,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-bottom-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "3000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
}
// Sprache umstellen
function setLanguage(lang)
{
    "use strict";
    $.post("/i18n", { lang: "" + lang + "" }, function(){
        location.reload();
    })
}
// Umlaute ersetzen
umlautMap = {
    '\u00dc': 'UE',
    '\u00c4': 'AE',
    '\u00d6': 'OE',
    '\u00fc': 'ue',
    '\u00e4': 'ae',
    '\u00f6': 'oe',
    '\u00df': 'ss',
}
function replaceUmlaute(str) {
    return str
        .replace(/[\u00dc|\u00c4|\u00d6][a-z]/g, (a) => {
            const big = umlautMap[a.slice(0, 1)];
            return big.charAt(0) + big.charAt(1).toLowerCase() + a.slice(1);
        })
        .replace(new RegExp('['+Object.keys(umlautMap).join('|')+']',"g"),
            (a) => umlautMap[a]
        );
}
// Kalender
// Kalender weiterblättern
function dspKalenderIndex(k) {
    "use strict";
    $.post("/ajaxKalenderIndex", {k: "" + k + ""}, function (resp) {

    }).done(function (resp) {
        $('#relKalender').html(resp);
    }).fail(function () {

    })
}

// Zeigt Modal mit Hinweis an
function dspHinweis(){
    var modal_hinweis = document.getElementById('modalHinweis');
    console.log(jQuery('#modalHinweis').modal('show'));
}
// Blendet Modal mit Hinweis wieder aus
function remHinweis(){
    var modal_hinweis = document.getElementById('modalHinweis');
    console.log(jQuery('#modalHinweis').modal('hide'));
}
// Allgemein
// Einbinden von Elementen via AJAX
function getDiv(verzeichnis, div, var1 = 0, var2 = 0, var3 = 0, var4 = 0, var5 = 0, var6 = 0) {
    $.post('/' + verzeichnis + '', {
        var1: '' + var1 + '',
        var2: '' + var2 + '',
        var3: '' + var3 + '',
        var4: '' + var4 + '',
        var5: '' + var5 + '',
        var6: '' + var6 + '',
    }, function (resp) {
        $('#' + div + '').html(resp);
    })
}
// Kalender Startseite blättern
function getKalenderIndex(k){
    "use strict";
    $.post("/ajaxGetKalenderIndex", { k: "" + k + "" }, function (resp){
        $('#dspKalender').html(resp);
    });
}

// Mitarbeiterliste filtern
function filterList(letter) // Zeigt nur die Einträge mit dem jeweiligen Buchstaben an
{
    "use strict";
    $('.all').fadeOut(400);
    setTimeout(function () {
        $('.filter' + letter + '').fadeIn(400);
    }, 400);

}

// Mitarbeiter alle anzeigen
function filterListAll() // Zeigt nur die Einträge mit dem jeweiligen Buchstaben an
{
    "use strict";
    $('.all').fadeOut(400);
    setTimeout(function () {
        $('.all').fadeIn(400);
    }, 400);
}