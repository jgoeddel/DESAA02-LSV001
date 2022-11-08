// Formular versenden (Sicherung)
$('#insertSicherung').bind('submit', function () {
    $.post("/produktion/bandsicherung/insert", $("#insertSicherung").serialize(), function (responseText) {
        // Felder und Formular zurücksetzen
        $('#insertSicherung').trigger("reset");
        if (responseText == 'error') {
            swal.fire({
                icon: 'error',
                timer: 3000,
                showConfirmButton: false
            }).then(function () {
                //location.reload();
            });
        } else {
            /** Meldung ausgeben */
            swal.fire({
                icon: 'success',
                timer: 3000,
                showConfirmButton: false
            }).then(function () {
                //location.reload();
            });
        }
    });
    return false;
});