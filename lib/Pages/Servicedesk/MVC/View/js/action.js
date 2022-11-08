// Serviceauftrag übernehmen
function meinAuftrag(id,title)
{
    $.post('/servicedesk/meinAuftrag', { id: ''+id+'' }, function () {
        Swal.fire({
            title: title,
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        }).then(function () {
            location.reload();
        });
    });
}
// Serviceauftrag deligieren
function deinAuftrag(id,uid,title)
{
    $.post('/servicedesk/deinAuftrag', { id: ''+id+'', uid: ''+uid+'' }, function () {
        Swal.fire({
            title: title,
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        }).then(function () {
            location.reload();
        });
    });
}
// Serviceauftrag starten
function auftragStarten(id,title)
{
    $.post('/servicedesk/start', { id: ''+id+'' }, function () {
        Swal.fire({
            title: title,
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        }).then(function () {
            location.reload();
        });
    });
}
// Serviceauftrag anhalten
function auftragPause(id,title)
{
    $.post('/servicedesk/pause', { id: ''+id+'' }, function () {
        Swal.fire({
            title: title,
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        }).then(function () {
            location.reload();
        });
    });
}
// Serviceauftrag fortsetzen
function auftragWeiter(id,title)
{
    $.post('/servicedesk/weiter', { id: ''+id+'' }, function () {
        Swal.fire({
            title: title,
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        }).then(function () {
            location.reload();
        });
    });
}
// Serviceauftrag beenden
function auftragEnde(id,title)
{
    $.post('/servicedesk/beenden', { id: ''+id+'' }, function () {
        Swal.fire({
            title: title,
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        }).then(function () {
            location.reload();
        });
    });
}
// Serviceauftrag nio
function auftragNIO(id,title)
{
    $.post('/servicedesk/nio', { id: ''+id+'' }, function () {
        Swal.fire({
            title: title,
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        }).then(function () {
            location.reload();
        });
    });
}
// Serviceauftrag abschiessen
function auftragAbschluss(id,title)
{
    $.post('/servicedesk/abschluss', { id: ''+id+'' }, function () {
        Swal.fire({
            title: title,
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        }).then(function () {
            location.reload();
        });
    });
}

// Datei löschen
function deleteFile(id,datei,title)
{
    $.post('/servicedesk/deleteFile', { id: ''+id+'', datei: ''+datei+'' }, function () {
        Swal.fire({
            title: title,
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        }).then(function () {
            location.reload();
        });
    });
}