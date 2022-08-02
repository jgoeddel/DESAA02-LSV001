// Anzeige der Tabellem mit den anwesenden Mitarbeitern
function dspTableAnwesenheit(feld,tabelle) {
    "use strict";
    $.post("/rotationsplan/ajaxTableAnwesenheit", {
        tabelle: ""+tabelle+"",
    }, function (text) {
        $("#"+feld+"").html(text);
    });
}

// Anzeige der Tabellem it den Mitarbeitern einer Schicht
function dspTablePersonal(uid = "", sid = "") {
    $.post("/rotationsplan/ajaxTablePersonal", {
        uid: "" + uid + "",
        sid: "" + sid + ""
    }, function (text) {
        $("#dspPersonal").html(text);
        // Änderungen anzeigen

    });
}
// Anzeige der Tabellem it den Mitarbeitern einer Schicht
function dspTablePersonalArchiv(datum, uid = "", sid = "") {
    $.post("/rotationsplan/ajaxTablePersonalArchiv", {
        datum: "" + datum + "",
        uid: "" + uid + "",
        sid: "" + sid + ""
    }, function (text) {
        $("#dspPersonal").html(text);
        // Änderungen anzeigen

    });
}
// Anzeige der Tabellem it den Stationen in einer Zeitschiene
function dspTableZeitschiene(zeitschiene, datum = "", uid = "") {
    $.post("/rotationsplan/ajaxTableZeitschiene", {
        zeitschiene: "" + zeitschiene + "",
        datum: "" + datum + "",
        uid: "" + uid + ""
    }, function (text) {
        $("#dspTableZeitschiene"+zeitschiene).html(text);
    });
}
// Anzeige der Tabellem it den Stationen in einer Zeitschiene
function dspTableZeitschieneArchiv(zeitschiene, datum = "", uid = "") {
    $.post("/rotationsplan/ajaxTableZeitschieneArchiv", {
        zeitschiene: "" + zeitschiene + "",
        datum: "" + datum + "",
        uid: "" + uid + ""
    }, function (text) {
        $("#dspTableZeitschiene"+zeitschiene).html(text);
    });
}
// Tabellenzeile einblenden
function showTR(id){
    document.getElementById('' + id + '').style.display='table-row';
}
// Löschfunktion Qualifikation (Mitarbeiter Details) einblenden
function dspDeleteQualiMa(id){
    $('#d'+id).toggle(800);
}