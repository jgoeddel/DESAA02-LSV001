// Neuer User - Felder füllen
function generateValues() {
    // Vorname und Name ohne Umlaute
    const a = replaceUmlaute($('#vorname').val());
    const b = replaceUmlaute($('#name').val());
    const c = $('#citycode').val();
    // Alles klein schreiben
    const vorname = a.toLowerCase();
    const name = b.toLowerCase();
    const citycode = c.toLowerCase();
    // Land
    const land = citycode.substr(0, 2);
    // Feld Username füllen
    $('#username').val(vorname + '.' + name);
    // Feld E-Mail füllen
    $('#email').val(vorname + '.' + name + '@' + land + '.rhenus.com');
    // Badge erstellen
    $('.korrekt').html('<span class="badge badge-warning">Korrekt?</span>');
}

// Neuen Mitarbeiter speichern
$('#mitarbeiterNeu').bind('submit', function () {
    $.post("/administration/mitarbeiter/neu", $("#mitarbeiterNeu").serialize(), function (responseText) {
        if (responseText == 0) {
            swal.fire({
                icon: 'success',
                timer: 3000,
                showConfirmButton: false
            }).then(function () {
                //$.redirect("/administration/mitarbieter");
            });
        } else {
            swal.fire({
                icon: 'error',
                timer: 3000,
                showConfirmButton: false
            });
        }
    });
    return false;
});

// APQP
$cc = $('.cc');
$cc.click(function () {
    $cc.not($(this)).fadeOut(800);
    var citycode = $(this).attr('id');
    $('#citycode').attr('value', citycode);
    setTimeout(function () {
        $('#dspBereich').fadeIn(800);
    }, 800);
});
$dd = $('.dd');
$dd.click(function () {
    $dd.not($(this)).fadeOut(800);
    var bereich = $(this).attr('id');
    $('#bereich').attr('value', bereich);
    setTimeout(function () {
        $('#info').fadeIn(800);
        $('#source').fadeIn(800);
    }, 800);
    setTimeout(function () {
        var citycode = $('#citycode').val();
        var bereich = $('#bereich').val();
        $.post("/administration/getAPQP", {
            citycode: "" + citycode + "",
            bereich: "" + bereich + ""
        }, function (resp) {
            $('#zuordnung').html(resp);

            function dspAPQPZiel() {
                "use strict";
                $.post("assets/elements/dsp.apqp.ziel.el.php", {
                    citycode: "" + citycode + "",
                    bereich: "" + bereich + ""
                }, function (resp) {
                    $('#ziel').html(resp);
                });
            }

            $(function () {
                $(".quelle, #ziel").sortable({
                    connectWith: ".connectedSortable",
                    placeholder: "ui-state-highlight",
                    items: "li:not(.ui-state-disabled)",
                    receive: function (event, ui) {
                        var id = ui.item.attr("id");
                        var citycode = $('#citycode').val();
                        var bereich = $('#bereich').val();
                        $.post("assets/querys/set.apqp2citycode.qry.php", {
                            id: "" + id + "",
                            citycode: "" + citycode + "",
                            bereich: "" + bereich + "",
                            aktion: "1"
                        }, function (resp) {
                            dspAPQPZiel();
                        });
                    }
                }).disableSelection();
            });
        });
    }, 800);
});

// APQP Citycode ebtfernen oder hinzufügen
function editAPQP(id, bereich, citycode) {
    "use strict";
    $.post("/administration/getAPQP", {
        citycode: "" + citycode + "",
        bereich: "" + bereich + "",
        id: "" + id + ""
    }, function (resp) {
        $('#zuordnung').html(resp);
    });
}

// APQP bearbeiten
function remAPQP(id, bereich, citycode, aktion) {
    "use strict";
    $.post("/administration/apqp/delete/citycode", {
        id: "" + id + "",
        citycode: "" + citycode + "",
        bereich: "" + bereich + "",
        aktion: "" + aktion + ""
    }, function () {
        $('#' + id + '').fadeOut(800);
        setTimeout(function () {
            $.post("/administration/getAPQP", {
                citycode: "" + citycode + "",
                bereich: "" + bereich + ""
            }, function (resp) {
                $('#zuordnung').html(resp);
            });
        }, 800);
    });
}

// APQP Citycode ebtfernen
function setAPQP(id, bereich, citycode) {
    "use strict";
    $.post("/administration/apqp/delete/citycode", {
        id: "" + id + "",
        citycode: "" + citycode + "",
        bereich: "" + bereich + "",
        aktion: "0"
    }, function () {
        $('#' + id + '').fadeOut(800);
        setTimeout(function () {
            $.post("/administration/getAPQP", {
                citycode: "" + citycode + "",
                bereich: "" + bereich + ""
            }, function (resp) {
                $('#zuordnung').html(resp);
            });
        }, 800);
    });
}