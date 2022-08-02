<?php
/** (c) Joachim Göddel . RLMS */

use App\Formular\Formular;
use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;

# Antwort abrufen
$a = ChangeManagementDatabase::antwortAPQP($apqpid, $bid, $part);
?>
<form method="post"
      class="chapqp needs-validation border__bottom--dotted-gray-50 my-3 pb-3" data-id="<?= $apqpid ?>">
    <h3 class="font-weight-300 font-size-16"><?= $_SESSION['text']['t_aenderungVornehmen'] ?><span
            class="float-end"><i
                class="fa fa-caret-up"></i></span></h3>
    <?php
    # Antworten umschreiben
    if($part == 2){
        $tio = $_SESSION['text']['ja'];
        $tnio = $_SESSION['text']['nein'];
        $tnoimpact = $_SESSION['text']['t_nichtErforderlich'];
    } else {
        $tio = $_SESSION['text']['io'];
        $tnio = $_SESSION['text']['nio'];
        $tnoimpact = $_SESSION['text']['t_noImpact'];
    }
    Functions::alert($_SESSION['text']['t_changeApqp']);
    Formular::input("hidden", "bid", "$bid", "", "");
    Formular::input("hidden", "apqp", "$apqpid", "", "");
    Formular::input("hidden", "location", "$citycode", "", "");
    Formular::input("hidden", "part", "$part", "", "");
    $aktiv = '<i class="fa fa-dot-circle text-primary"></i>';
    $inaktiv = '<i class="far fa-circle text-muted"></i>';
    # ROW
    Functions::htmlOpenSingleDiv("row pb-2 font-size-13");
    Functions::htmlOpenDiv("2", "right", "dotted", "text-center pointer", "", "", "py-2");
    # ROW
    Functions::htmlOpenSingleDiv("row pb-2 font-size-12");
    Functions::htmlOpenDivAction("col-4", "onclick=\"setAPQP($bid,'$apqpid','io','$citycode', $part);\"", "d3$apqpid");
    $ausgabe = (isset($a->antwort) && $a->antwort == 'io') ? $aktiv : $inaktiv;
    echo $ausgabe;
    Functions::htmlCloseSingleDiv();
    Functions::htmlOpenDivAction("col-8", "onclick=\"setAPQP($bid,'$apqpid','io','$citycode', $part);\"", "d3$apqpid");
    echo $tio;
    Functions::htmlCloseSingleDiv();
    Functions::htmlCloseSingleDiv();
    # END ROW
    Functions::htmlCloseDiv();
    Functions::htmlOpenDiv("4", "right", "dotted", "text-center pointer", "", "", "py-2");
    # ROW
    Functions::htmlOpenSingleDiv("row pb-2 font-size-12");
    Functions::htmlOpenDivAction("col-4", "onclick=\"setAPQP($bid,'$apqpid','no-impact','$citycode', $part);\"", "d2$apqpid");
    $ausgabe = (isset($a->antwort) && $a->antwort == 'no-impact') ? $aktiv : $inaktiv;
    echo $ausgabe;
    Functions::htmlCloseSingleDiv();
    Functions::htmlOpenDivAction("col-8", "onclick=\"setAPQP($bid,'$apqpid','no-impact','$citycode', $part);\"", "d2$apqpid");
    echo $tnoimpact;
    Functions::htmlCloseSingleDiv();
    Functions::htmlCloseSingleDiv();
    # END ROW
    Functions::htmlCloseDiv();
    Functions::htmlOpenDiv("2", "right", "dotted", "text-center pointer", "", "", "py-2");
    # ROW
    Functions::htmlOpenSingleDiv("row pb-2 font-size-12");
    Functions::htmlOpenDivAction("col-4", "onclick=\"setAPQP($bid,'$apqpid','nio','$citycode', $part);\"", "d1$apqpid");
    $ausgabe = (isset($a->antwort) && $a->antwort == 'nio') ? $aktiv : $inaktiv;
    echo $ausgabe;
    Functions::htmlCloseSingleDiv();
    Functions::htmlOpenDivAction("col-8", "onclick=\"setAPQP($bid,'$apqpid','nio','$citycode', $part);\"", "d1$apqpid");
    echo $tnio;
    Functions::htmlCloseSingleDiv();
    Functions::htmlCloseSingleDiv();
    # END ROW
    Functions::htmlCloseDiv();
    Functions::htmlOpenDiv("2", "", "", "text-center pointer", "", "", "py-2");
    # ROW
    Functions::htmlOpenSingleDiv("row pb-2 font-size-12");
    Functions::htmlOpenDivAction("col-4", "onclick=\"resetAPQP($a->id);\"", "d4$apqpid");
    echo $inaktiv;
    Functions::htmlCloseSingleDiv();
    Functions::htmlOpenDivAction("col-8", "onclick=\"resetAPQP($a->id);\"", "d4$apqpid");
    echo $_SESSION['text']['b_zuruecksetzen'];
    Functions::htmlCloseSingleDiv();
    Functions::htmlCloseSingleDiv();
    # END ROW
    Functions::htmlCloseDiv();
    Functions::htmlCloseSingleDiv();
    # END ROW
    ?>

    <textarea class="summernote" name="bemerkung"><?= $a->bemerkung ?></textarea>

    <div class="row m-0 py-2 border__top--dotted-gray border__bottom--dotted-gray">
        <div class="col-3">
            <?php
            (!empty($k->kosten)) ? $kosten = $k->kosten : $kosten = '';
            ?>
            <input type="number" name="kosten" id="kosten" min="0" step=".01" class="invisible-formfield"
                   value="<?= $kosten ?>" placeholder="<?= $_SESSION['text']['ph_kosten'] ?>">
        </div>
        <div class="col-9">
            <?php
            if (!empty($k) && $k->anmerkung != ''):
                $anmerkung = str_replace("<p>", "", $k->anmerkung);
                $anmerkung = str_replace("</p>", "", $anmerkung);
                $anmerkung = str_replace("<br>", "", $anmerkung);
            else:
                $anmerkung = '';
            endif;
            ?>
            <input type="search" class="invisible-formfield" name="anmerkung" value="<?= $anmerkung ?>"
                   placeholder="<?= $_SESSION['text']['i_kostenPlaceholder'] ?>">
        </div>
    </div>
    <div class="text-end mt-2 pb-2 mb-4 border__bottom--dotted-gray">
        <input type="reset" class="btn btn-warning btn-sm me-1"
               value="<?= $_SESSION['text']['b_abbrechen'] ?>"
               onclick="$('.form<?= $a->id ?>').toggle(800)">
        <input type="submit" class="btn btn-primary btn-sm"
               value="<?= $_SESSION['text']['b_aenderungenSpeichern'] ?>">
    </div>
</form>
