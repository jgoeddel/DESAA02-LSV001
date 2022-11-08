// Anzeige des Komplett Status in der Detailansicht
function dspStatus(bid,status,citycode){
    $.post("/changeManagement/ajaxStatus", {
        bid: "" + bid + "",
        status: "" + status + "",
        citycode: "" + citycode +""
    }, function(text){
        $('#'+status+'Status').html(text);
    })
}
// Anzeige des Formulares zum Ändern des Verantwortlichen
function dspFormChange()
{
    "use strict";
    const daddress = $('#daddress');
    const eaddress = $('#eaddress');
    if(daddress.is(":visible"))
    {
        daddress.fadeOut(800);
        setTimeout(function () {
            eaddress.fadeIn(800);
        }, 800);
    } else {
        eaddress.fadeOut(800);
        setTimeout(function () {
            daddress.fadeIn(800);
        }, 800);
    }
}
// Anzeigen des Formulares zum ändern des Zieldatums
function showZiel()
{
    "use strict";
    const fziel = $('#formularZiel');
    if(fziel.is(":visible"))
    {
        fziel.fadeOut(800);
    } else {
        fziel.fadeIn(800);
    }
}

// Anzeige weiterer Details
function dspFormField(bid,feld)
{
    "use strict";
}

// Anzeige Kommentare APQP
function dspKomAPQP(part,bid,bereich){
    $.post("/changeManagement/ajaxComAPQP", {
        bid: "" + bid + "",
        part: "" + part + "",
        bereich: "" + bereich +""
    }, function(text){
        $('#komAPQP').html(text);
        // summernote
        $('.summernote').summernote({
            height: 160,
            lang: 'de-DE',
            toolbar: [
                ['style', ['bold', 'italic', 'underline']],
                ['color', ['color']],
                ['para', ['ul', 'ol']]
            ],
            cleaner: {
                action: 'paste',
                keepHtml: false,
                keepClasses: false
            }
        });
        // KOMMENTAR SPEICHERN
        $('.antkom').bind('submit', function () {
            const markupStr = $('.summernote').summernote('code');
            $.post("/changeManagement/setAntwortCom", $(".antkom").serialize(), function (responseText) {
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
    })
}
// Anzeige Tabelle PartNo
function partnoTable(id){
    $.post("/changeManagement/partnoTable", {
        id: "" + id + ""
    }, function(text){
        $('#dspTbody').html(text);
    })
}
// Anzeige APQP
function getAPQPElement(bid, part, apqpid, loc){ // showAPQP($id, $evaluation, 1, $apqp, $loc);
    console.log("ID: "+bid+" . PART: "+part+" . APQP: "+apqpid+" . LOC: "+loc);
    $.post("/changeManagement/getAPQPElement", {
        bid: "" + bid + "",
        part: "" + part + "",
        apqpid: "" + apqpid + "",
        loc: "" + loc + ""
    },function(text){
        $('#apqp'+apqpid+'').html(text);
    })
}
// Anzeige Meetings
function dspMeetings(id){
    $.post("/changeManagement/getMeetings", {
        id: "" + id + ""
    }, function(text){
        $('#dspMeetings').html(text);
    })
}


// Anzeige Angebote
function dspAngebote(id){
    $.post("/changeManagement/getAngebote", {
        id: "" + id + ""
    }, function(text){
        $('#dspAngebote').html(text);
    })
}
// Anzeige Mitarbeiter Angebote
function dspMaAngebot(location,id){
    $.post("/changeManagement/getMaAngebot", {
        location: "" + location + "",
        id: "" + id + ""
    }, function(text){
        $('#dspMaAngebot').html(text);
    })
}

// Formular anzeigen (APQP)
function getFormAPQP(bid,apqpid,citycode,part)
{
    "use strict";
    // Formulare löschen
    document.querySelectorAll('.chapqp').forEach(e => e.remove());
    // Formular aufrufen
    $.post("/changeManagement/getFormAPQP", {
        bid: "" + bid + "",
        part: "" + part + "",
        apqpid: "" + apqpid + "",
        citycode: "" + citycode + ""
    }, function(text){
        $('#f'+apqpid+'').html(text);
        // summernote
        $('.summernote').summernote({
            height: 160,
            lang: 'de-DE',
            toolbar: [
                ['style', ['bold', 'italic', 'underline']],
                ['color', ['color']],
                ['para', ['ul', 'ol']]
            ],
            cleaner: {
                action: 'paste',
                keepHtml: false,
                keepClasses: false
            }
        });
        // APQP bearbeiten
        $('.chapqp').submit(function(evt) {
            evt.preventDefault();
            const form = $(this);
            const markupStr = $('.summernote').summernote('code');
            const data = form.serialize();
            $.post("/changeManagement/changeAPQP", data, function () {
                swal.fire({
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                }).then(function () {
                    getAPQPElement(bid, part, apqpid, citycode);
                });
            });
        })
    })
}

/**
 $.post("/changeManagement/getAPQPElement", {
        id: "" + id + "",
        part: "" + part + "",
        apqp: "" + apqp + "",
        loc: "" + loc + ""
    }, function(text){
        $('#apqp'+apqp+'').html(text);

        // APQP bearbeiten
        $('.chapqp').submit(function(evt) {
            evt.preventDefault();
            const form = $(this);
            const id = $(this).attr("data-id");
            const markupStr = $('.summernote' + id + '').summernote('code');
            const data = form.serialize();
            $.post("/changeManagement/changeAPQP", data, function (responseText) {
                swal.fire({
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                }).then(function () {
                    getAPQPElement(id, part, apqp, loc);
                });
            });
        })
    })
 */