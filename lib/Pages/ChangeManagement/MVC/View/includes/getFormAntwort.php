<?php
/** (c) Joachim Göddel . RLMS */

use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;

$base_apqp = ChangeManagementDatabase::idAPQP($apqp,$bid,$part);

        $a = sprintf($_SESSION['text']['t_grundAntwort'], $antwort);
        $a.= "<br><b>{$_SESSION['text']['i_optional']}</b>";
        Functions::alert($a); ?>
    <form class="ik" id="k<?= $apqp ?>" method="post">
        <input type="hidden" name="bid" value="<?= $bid ?>">
        <input type="hidden" name="apqp" value="<?= $apqp ?>">
        <input type="hidden" name="apqpid" value="<?= $base_apqp ?>">
        <textarea name="bemerkung" class="summernote"></textarea>
        <div class="text-end">
            <input type="reset" class="btn btn-warning mt-3 me-2" value="<?= $_SESSION['text']['b_abbrechen'] ?>" onclick="$('#kf<?= $apqp ?>').toggle(800);">
            <input type="submit" class="btn btn-primary mt-3" value="<?= $_SESSION['text']['b_kommentarSpeichern'] ?>">
        </div>
    </form>

<script type="text/javascript">
    // summernote
    $('.summernote').summernote({
        height: 160,
        lang: 'de-DE',
        toolbar: [
            ['style', ['bold', 'italic', 'underline']],
            ['color', ['color']],
            ['para', ['ul', 'ol']]
        ]
    });
</script>