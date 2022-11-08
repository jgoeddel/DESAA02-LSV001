// Neuen Serviceauftrag speichern
$('#insertEintrag').bind('submit', function () { // Eintrag speichern
    $.post("/servicedesk/insert", $("#insertEintrag").serialize(), function (responseText) {
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
// Kommentar speichern
$('#sendComment').bind('submit', function () {
    const meldung = $('#meldung').val();
    const markupStr = $('#summernote').summernote('code');
    $.post("/servicedesk/kommentar/insert", $('#sendComment').serialize(), function(){
        swal.fire({
            title: meldung,
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        }).then(function () {
            location.reload();
        });
    });
    return false;
});