// Prüfen, ob bereits ein Plan zu einem Datum eingetragen ist
function planExists(datum){
    $.post("/rotationsplan/ajaxPlanExists", { datum: "" + datum + "" }, function(resp){
        if(resp != 0) {
            $('#warning').css('display', 'block');
        } else {
            $('#warning').css('display', 'none');
        }
    })
}
// Prüft den Status der Anwesenheit (Checkbox) und setzt diese, wenn noch nicht aktiv
function checkAnwesenheit(id) {
    "use strict";
    const a = $('#' + id + '');
    if (a.prop('checked')) {

    } else {
        a.prop('checked', true);
    }
}
// Prüft den Status einer Checkbox und ändert entsprechend deren Aussehen
function checkValue(val, id) {
    "use strict";
    //console.log(val, id);
    if ($('#' + id + '').prop('checked')) {
        $('#tr' + id + '').removeClass("disabled");
        $('#s' + id + '').removeAttr('disabled');
    } else {
        $('#tr' + id + '').addClass("disabled");
        $('#s' + id + '').attr('disabled', 'disabled');
    }
}
// Zählt die Anwesenden Mitarbeiter und gibt diese aus
function zaehlen(el) {
    var summe = 0;
    var f = el.form;
    for (var i = 0; i < f.elements.length; i++) {
        var element = f.elements[i];
        console.log(element);
        if (element.onchange && /zaehlen/.exec(element.onchange)
            && !isNaN(parseFloat(element.value))) {
            if (element.type == 'checkbox' && element.checked && element.className == 'form-check-input ca ma') {
                summe += parseFloat(element.value)
            }
        }
    }
    $('#summe').html(summe + ' Mitarbeiter');
    $('#info').html(summe);
}
// Dump
function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}
// Mitarbeiter aus einer Zeitschiene im Rotationsplan löschen
function deleteMitarbeiterPlan(id,uid,zeitschiene,springer,z){
    $.post("/rotationsplan/ajaxDeleteMitarbeiterPlan", { id: "" + id + "", uid: "" + uid + "", zeitschiene: "" + zeitschiene + "", springer: "" + springer + "" }, function(resp){
        $('#td'+uid+''+zeitschiene+'').html('<i class="fa fa-user-circle text-muted"></i>');
        dspTablePersonal();
        dspTableZeitschiene(zeitschiene);
    })
}
// Mitarbeiter aus einer Zeitschiene der Anwesenheit löschen
function deleteMitarbeiterAnwesend(uid,z){
    $.post("/rotationsplan/ajaxDeleteMitarbeiterAnwesend", { uid: "" + uid + "", z: "" + z + "" }, function(resp){
        $('#td'+uid+''+z+'').html('<i class="fa fa-user-circle text-muted"></i>');
        dspTablePersonal();
    })
}
// Mitarbeiter aus einer Zeitschiene der Anwesenheit löschen
function setMitarbeiterAnwesend(uid,z){
    $.post("/rotationsplan/ajaxSetMitarbeiterAnwesend", { uid: "" + uid + "", z: "" + z + "" }, function(resp){
        $('#td'+uid+''+z+'').html('<i class="fa fa-user-circle text-success"></i>');
        dspTablePersonal();
    })
}
// Mögliche Stationen eines Mitarbeiters anzeigen
function showPossStation(zs, uid, datum) {
    "use strict";
    $('#spin'+uid).html('<i class="fa fa-cog fa-spin text-muted ms-2"></i>');
    const zs2 = zs + 1;
    const zs3 = zs + 2;
    dspTableZeitschiene(zs, datum, uid);
    dspTableZeitschiene(zs2, datum, uid);
    dspTableZeitschiene(zs3, datum, uid);
    dspTablePersonal(uid, 0);
}
// Mögliche Mitarbeiter / Station
function showPossMa(sid){
    "use strict";
    dspTablePersonal(0,sid);
}
// Plan eines bestimmten Tages anzeigen
function getVerwaltung(datum){
    $.redirect("/rotationsplan/verwaltung", { datum: "" + datum + "" },"POST");
}
// Plan eines bestimmten Tages anzeigen (Archiv)
function getArchiv(){
    const datum = $('#archivDatum').val();
    $.redirect("/rotationsplan/archiv", { datum: "" + datum + "" },"POST");
}
// Neuen Mitarbeiter eine Station zufügen
function setMaStation(sid,mitarbeiter,guid,zeitschiene,uid,springer){
    "use strict";
    //alert("SID: "+sid+" - MA: "+mitarbeiter+" - GUID: "+guid+" - ZS: "+zeitschiene+" - UID: "+uid+" - SP: "+springer);
    $.post("/rotationsplan/ajaxSetMitarbeiterStation", {
        sid: "" + sid + "",
        mitarbeiter: "" + mitarbeiter + "",
        guid: "" + guid + "",
        zeitschiene: "" + zeitschiene + "",
        uid: "" + uid + "",
        springer: "" + springer + ""
    }, function(resp) {
        dspTableZeitschiene(zeitschiene, '', guid);
        dspTablePersonal(guid, 0);
    })
}
// Abwesenheit löschen
function deleteAbwesenheit(uid, id){
    "use strict";
    $.post("/rotationsplan/ajaxDeleteAbwesend", { uid: "" + uid + "", id: "" + id + "" }, function(){
        location.reload();
    })
}
// Mitarbeiter Details
function mitarbeiterDetails(id){
    $.redirect("/rotationsplan/mitarbeiterDetails", { id: "" + id + "" }, "GET");
}
// Qualifikation MA löschen
function deleteQualiMa(sid,uid){
    $.post("/rotationsplan/ajaxDeleteQualiMa", { sid: "" + sid + "", uid: "" + uid + "" }, function(){
        $('#d'+sid).toggle(800);
        $('#q'+sid).toggle(800);
    })
}
// Formular Qualifikation anzeigen
function showFormularQualiMa(sid,uid){
    $.post("/rotationsplan/ajaxGetFormularQualiMa", { sid: "" + sid + "", uid: "" + uid + "" }, function(resp){
        $('#formQuali').html(resp);
    })
}

// Training Mitarbeiter
function setTrainingMa(sid,uid){
    $.post("/rotationsplan/ajaxSetTrainingMa", { sid: "" + sid + "", uid: "" + uid + "" }, function(resp) {
        swal.fire({
            title: 'Einarbeitung gestartet',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false,
            backdrop: 'rgba(0,0,0,0.7)'
        }).then(function () {
            location.reload();
        });
    })
}
// Qualifikation Mitarbeiter
function setQualiMa(sid,uid){
    $.post("/rotationsplan/ajaxSetQualiMa", { sid: "" + sid + "", uid: "" + uid + "" }, function(resp) {
        location.reload();
    })
}
// Löschen Mitarbeiter
function deleteMa(id){
    $.post("/rotationsplan/ajaxDeleteMa", { id: "" + id + "" }, function(resp) {
        swal.fire({
            title: 'Mitarbeiter gelöscht',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false,
            backdrop: 'rgba(0,0,0,0.7)'
        }).then(function () {
            $.redirect("/rotationsplan/mitarbeiter");
        });
    })
}

// Station Details
function stationDetails(id){
    $.redirect("/rotationsplan/stationDetails", { id: "" + id + "" }, "POST");
}

// Countdown
var myCounter = new countdown({
    seconds:10,  // number of seconds to count down
    onUpdateStatus: function(sec){ $('#Timer').html(sec); }, // callback for each second
    onCounterEnd: function(){ $('#dspPlan').html('<div class="text-center mt-5 pt-5"><img src="../../skin/files/images/Rotationsplan.png"  alt="Rotationsplan" class="img-fluid"></div>'); $('#Timer').html(''); $('#delbtn').fadeOut(500); } // final action
});
function countdown(options) {
    var timer,
        instance = this,
        seconds = options.seconds || 10,
        updateStatus = options.onUpdateStatus || function () {},
        counterEnd = options.onCounterEnd || function () {};

    function decrementCounter() {
        updateStatus(seconds);
        if (seconds === 0) {
            counterEnd();
            instance.stop();
        }
        seconds--;
    }

    this.start = function () {
        clearInterval(timer);
        timer = 0;
        seconds = options.seconds;
        timer = setInterval(decrementCounter, 1000);
    };

    this.stop = function () {
        clearInterval(timer);
    };
}

// Abteilung wechseln
function setAbteilung(id){
    $.post("/rotationsplan/ajaxSetAbteilung", { id: "" + id + "" }, function() {
        location.reload();
    })
}
// Schicht wechseln
function setSchicht(id){
    $.post("/rotationsplan/ajaxSetSchicht", { id: "" + id + "" }, function() {
        location.reload();
    })
}