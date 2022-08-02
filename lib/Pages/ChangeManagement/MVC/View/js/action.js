// Values ändern
function sendValue(feld, value, bid, table, art, sid = '') {
    if (art == 'editor') {
        value = $('#cd').summernote('code');
    }
    $.post("/changeManagement/changeValue", {
        feld: "" + feld + "",
        value: "" + value + "",
        bid: "" + bid + "",
        table: "" + table + "",
        sid: "" + sid + "",
    }, function () {
        toastr.success('Success')
    })
}

// Values setzen
function setValue(feld, value, bid, table, art) {
    "use strict";
    if (art == 'editor') {
        value = $('#cd').summernote('code');
    }
    $.post("/changeManagement/setValue", {
        feld: "" + feld + "",
        value: "" + value + "",
        bid: "" + bid + "",
        table: "" + table + "",
    }, function () {
        toastr.success('Success')
    })
}

// Order Values senden
function sendOrder(feld, value, bid, part) {
    "use strict";
    $.post("/changeManagement/changeOrder", {
        feld: "" + feld + "",
        value: "" + value + "",
        bid: "" + bid + "",
        part: "" + part + ""
    }, function () {
        toastr.success('Success')
    })
}

// Verantwortlichen setzen
function setVerantwortung(uid, bid) {
    "use strict";
    $.post("/changeManagement/changeVerantwortung", {
        bid: "" + bid + "",
        uid: "" + uid + ""
    }, function () {
        location.reload();
    })
}

// SELECT MA
$('#ma').change(function () {
    const uid = $('#ma').val();
    const bid = $('#bid').val();
    setVerantwortung(uid, bid);
});

// Zieldatum ändern
$('#fziel').bind('submit', function () {
    var markupStr = $('#summernote').summernote('code');
    $.post("/changeManagement/changeZieldatum", $("#fziel").serialize(), function (responseText) {
        toastr.success('Success');
        setTimeout(function () {
            location.reload();
        }, 3500);
    });
    return false;
});

// APQP setzen
function setAPQP(bid, apqp, antwort, bereich, part) {
    "use strict";
    $.post("/changeManagement/setAPQP", {
        bid: "" + bid + "",
        apqp: "" + apqp + "",
        antwort: "" + antwort + "",
        part: "" + part + ""
    }, function (resp) {
        $.post("/changeManagement/checkAuftrag", { id: "" + bid + "" }, function(resp){
           // Beenden, wenn Status 0
           if(resp === 0){
               $.post("/changeManagement/finishID", { id: "" + bid + "" }, function(){

               })
           }
        });
        getAPQPElement(bid, part, apqp, bereich);
        dspStatus(bid, 'over', ''+bereich+'');
        dspStatus(bid, 'study', ''+bereich+'');
        dspStatus(bid, 'introduce', ''+bereich+'');
    })
}


// APQP zurücksetzen
function resetAPQP(base_apqp) {
    "use strict";
    $.post("/changeManagement/resetAPQP", {
        base_apqp: "" + base_apqp + ""
    }, function () {
        location.reload();
    })
}
// Vereinfachter Durchlauf
$('#vd').bind('submit', function () {
    $.post("/changeManagement/simpleChange", $("#vd").serialize(), function (responseText) {
        if(responseText == 1) {
            swal.fire({
                icon: 'success',
                timer: 3000,
                showConfirmButton: false
            }).then(function () {
                location.reload();
            });
        } else {
            swal.fire({
                icon: 'error',
                timer: 3000,
                showConfirmButton: false
            }).then(function(){
                $('#password').val('');
            })
        }
    });
    return false;
});

// NEU: Auswahl Citycode
function setThisCitycode(citycode){
    "use strict";
    $('#citycode').attr('value', citycode);
    // Klasse von den ausgewählten Elementen entfernen
    $('#'+citycode+'').removeClass("cc");
    $('#'+citycode+'_icon').fadeIn(1000);
    $.post("/changeManagement/setCitycode", { citycode: "" + citycode + "", part: "evaluation" }, function (resp) {
        $('#dspEvaluation').html(resp);
    });
    $.post("/changeManagement/setCitycode", { citycode: "" + citycode + "", part: "tracking" }, function (resp) {
        $('#dspTracking').html(resp);
    });
    $.post("/changeManagement/setLieferanten", { citycode: "" + citycode + "" }, function (resp) {
        $('#dspLieferanten').html(resp);
    });
    remCitycode();

}
// Versteckte Felder füllen
function setHiddenField()
{
    "use strict";
    // Changetype
    const ctype = $("#change_type option:selected").val();
    console.log("Changetype: "+ctype);
    $("#changetype").val(ctype);
    // Quelle
    const quelle = $("#squelle option:selected").val();
    console.log("Quelle: "+quelle);
    $("#quelle").val(quelle);
}
// NEU: Citycode ausblenden
function remCitycode(){
    "use strict";
    $('.cc').fadeOut(800);
    $('#step2,#step3,#step4,#step5').fadeIn(800);
    $('bewertung_feld').fadeIn(800);
    return false;
}
// Neue Anfrage eintragen
$('#neu').bind('submit', function () {
    var markupStr = $('#summernote').summernote('code');
    $.post("/changeManagement/neuSend", $("#neu").serialize(), function (responseText) {
        swal.fire({
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        }).then(function () {
            $.post("/email/neu/cm", { titel: ''+ responseText +'' }, function (responseText) {
                $.redirect("/changeManagement");
            });
        });

    });
    return false;
});
// Neue Part No eintragen
$('#insertPartNo').bind('submit', function () {
    const bid = $('#bid').val();
    $.post("/changeManagement/setPartno", $("#insertPartNo").serialize(), function (responseText) {
        swal.fire({
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        }).then(function () {
            location.reload();
        });
    });
    return false;
});

// KOMMENTAR SPEICHERN
$('#icom').bind('submit', function () {
    var markupStr = $('#summernote').summernote('code');
    $.post("/changeManagement/setComAPQP", $("#icom").serialize(), function (responseText) {
        swal.fire({
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        }).then(function () {
            location.reload();
        });
    });
    return false;
});

// NACHRICHT SPEICHERN
$('#inachricht').bind('submit', function () {
    var markupStr = $('#summernote').summernote('code');
    $.post("/changeManagement/setNachricht", $("#inachricht").serialize(), function (responseText) {
        swal.fire({
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        }).then(function () {
            location.reload();
        });
    });
    return false;
});
// FREIGABE
$('#freigabe').bind('submit', function () {
    $.post("/changeManagement/setFreigabe", $("#freigabe").serialize(), function (responseText) {
        swal.fire({
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        }).then(function () {
            location.reload();
        });
    });
    return false;
});
// ABSCHLIESSEN
$('#abschliessen').bind('submit', function () {
    $.post("/changeManagement/abschliessen", $("#abschliessen").serialize(), function (responseText) {
        swal.fire({
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        }).then(function () {
            location.reload();
        });
    });
    return false;
});
// ALTE TEILE
$('#alteTeile').bind('submit', function () {
    $.post("/changeManagement/alteTeile", $("#alteTeile").serialize(), function (responseText) {
        swal.fire({
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        }).then(function () {
            location.reload();
        });
    });
    return false;
});
// ALTE TEILE (ENDE)
$('#endeAlteTeile').bind('submit', function () {
    $.post("/changeManagement/endeAlteTeile", $("#endeAlteTeile").serialize(), function (responseText) {
        swal.fire({
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        }).then(function () {
            location.reload();
        });
    });
    return false;
});
// ABSCHLIESSEN
$('#delete').bind('submit', function () {
    $.post("/changeManagement/delete", $("#delete").serialize(), function (responseText) {
        swal.fire({
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        }).then(function () {
            $.redirect("/changeManagement");
        });
    });
    return false;
});

// Partno umstellen
function setUmstellungPartNo(id,status,bid){
    const heute = new Date().toLocaleString();
    const tm = heute.split(", ");
    const ziel = $('#ziel'+id+'').html();
    if(ziel === 'offen'){ $('#ziel'+id+'').html(tm[0]); }
    $('#ist'+id+'').html(tm[0]);
    $('#sts'+id+'').html(status);
    $.post("/changeManagement/changePartno", { id: "" + id + "" }, function (responseText) {
        partnoTable(bid);
    });
}
// Partno löschen
function deletePartno(id,bid){
    $.post("/changeManagement/deletePartno", { id: "" + id + "" }, function (responseText) {
        partnoTable(bid);
    });
}
// Neuer List of open Points
$('#lopneu').bind('submit', function () {
    const id = $('#id').val();
    $.post("/changeManagement/setLop", $("#lopneu").serialize(), function (responseText) {
        swal.fire({
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        }).then(function () {
            location.reload();
        });

    });
    return false;
});
// Status LOP ändern
function changeLopStatus(id,status)
{
    $.post("/changeManagement/changeLop", { id: ""+id+"", status: ""+status+"" }, function() {
        location.reload();
    });
}
// KOMMENTAR MEETING SPEICHERN
$('#iprotokoll').bind('submit', function () {
    const markupStr = $('#summernote').summernote('code');
    const id = $('#id').val();
    $.post("/changeManagement/setMeeting", $("#iprotokoll").serialize(), function (responseText) {
        swal.fire({
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        }).then(function () {
            dspMeetings(id);
        });
    });
    return false;
});
// Mitarbeiter zu Meeting
function addUserMeeting(mid,bid,uid)
{
    "use strict";
    $.post("/changeManagement/setMaMeeting", { bid: ""+bid+"", mid: ""+mid+"", uid: ""+uid+"" }, function(resp) {
        dspMeetings(bid);
    });
}