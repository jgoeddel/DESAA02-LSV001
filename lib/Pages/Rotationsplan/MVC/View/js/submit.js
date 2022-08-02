// Neuen Logbucheintrag schreiben
$('#handicap').bind('submit', function () {
    const id = $('#id').val();
    $.post("/rotationsplan/ajaxSetMitarbeiterHandicap", $("#handicap").serialize(), function (responseText) {
        console.log(responseText);
        swal.fire({
            title: 'Handicap eingetragen',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false,
            backdrop: 'rgba(0,0,0,0.7)'
        }).then(function () {
            location.reload();
        });
    });
    return false;
});

// Neue Abwesenheit eintragen
$('#abwesenheit').bind('submit', function () {
    const id = $('#ida').val();
    $.post("/rotationsplan/ajaxSetMitarbeiterAbwesend", $("#abwesenheit").serialize(), function (responseText) {
        console.log(responseText);
        if(responseText === true) {
            swal.fire({
                title: 'Abwesenheit eingetragen',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                backdrop: 'rgba(0,0,0,0.7)'
            }).then(function () {
                location.reload();
            });
        } else {
            swal.fire({
                title: 'Abwesenheit nicht eingetragen!',
                icon: 'error',
                timer: 2000,
                showConfirmButton: false,
                backdrop: 'rgba(0,0,0,0.7)'
            })
        }
    });
    return false;
});
// Passwort speichern
$('#pw').bind('submit', function () {
    const id = $('#id').val();
    $.post("/rotationsplan/ajaxSetMitarbeiterPassword", $("#pw").serialize(), function (responseText) {
        console.log(responseText);
        swal.fire({
            title: 'Passwort eingetragen',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false,
            backdrop: 'rgba(0,0,0,0.7)'
        }).then(function () {
            //location.reload();
        });
    });
    return false;
});
// Mitarbeiter speichern
$('#mitarbeiterNeu').bind('submit', function () {
    const id = $('#id').val();
    $.post("/rotationsplan/neuerMitarbeiter", $("#mitarbeiterNeu").serialize(), function () {
        swal.fire({
            title: 'Mitarbeiter gespeichert',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false,
            backdrop: 'rgba(0,0,0,0.7)'
        }).then(function () {
            location.reload();
        });
    });
    return false;
});
// Station speichern
$('#stationNeu').bind('submit', function () {
    const id = $('#id').val();
    $.post("/rotationsplan/neueStation", $("#stationNeu").serialize(), function () {
        swal.fire({
            title: 'Station gespeichert',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false,
            backdrop: 'rgba(0,0,0,0.7)'
        }).then(function () {
            location.reload();
        });
    });
    return false;
});
// Passwort speichern
$('#changeStation').bind('submit', function () {
    const id = $('#id').val();
    $.post("/rotationsplan/ajaxChangeStation", $("#changeStation").serialize(), function (responseText) {
        console.log(responseText);
        swal.fire({
            title: 'Station geändert',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false,
            backdrop: 'rgba(0,0,0,0.7)'
        }).then(function () {

        });
    });
    return false;
});
// Mitarbeiter vergleichen
$('#vergleich').bind('submit', function () {
    $('#morris').html();
    $.post("/rotationsplan/mitarbeiter/getVergleich", $("#vergleich").serialize(), function (responseText) {
        $('#dspvergleich').html(responseText);
    });
    $.post("/rotationsplan/mitarbeiter/getVergleichChart", $("#vergleich").serialize(), function (responseText) {
        $('#morris').html(responseText);
    });
    return false;
});