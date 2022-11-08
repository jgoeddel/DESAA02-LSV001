// Neue Schulung speichern
$('#insertSchulung').bind('submit', function () { // Eintrag speichern
    const markupStr = $('#summernote').summernote('code');
    $.post("/schulungen/insertSchulung", $("#insertSchulung").serialize(), function (resp) {
        const ret = resp.split('|');
        if(ret[0] == 1) {
            swal.fire({
                icon: 'success',
                timer: 3000,
                showConfirmButton: false,
                title: ret[1]
            }).then(function () {
                //location.reload();
            });
        } else {
            swal.fire({
                icon: 'error',
                timer: 5000,
                showConfirmButton: false,
                title: ret[1],
                text: ret[2]
            });
        }
    });
    return false;
});