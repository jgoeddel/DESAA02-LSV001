<?php
/** (c) Joachim Göddel . RLMS */

use App\Formular\Formular;use App\Functions\Functions;
use App\Pages\Home\IndexDatabase;

?>
<form id="icom" method="post" class="needs-validation mt-3">
    <input type="hidden" name="bid" id="bid" value="<?= $id ?>">
    <input type="hidden" name="part" id="part" value="<?= $part ?>">
    <input type="hidden" name="bereich" id="bereich" value="<?= $evaluation ?>">
    <?php Functions::alert($_SESSION['text']['f_frage']); ?>
    <div class="border__bottom--dotted-gray-50 pb-3 mb-3">
        <select name="frage" class="invisible-formfield me-3" onchange="$('#f_mitarbeiter').toggle(500);$('#button').attr('value','<?= $_SESSION['text']['h_frageStellen'] ?>'),$('#ma').attr('required','required')">
            <option value="1"><?=  $_SESSION['text']['h_anmerkungSchreiben'] ?></option>
            <option value="2"><?=  $_SESSION['text']['h_frageStellen'] ?></option>
        </select>
    </div>
    <div class="border__bottom--dotted-gray-50 pb-3 mb-3 dspnone" id="f_mitarbeiter">
        <select name="frage_an" class="invisible-formfield me-3" id="f_ma">
            <option value=""><?= $_SESSION['text']['i_selectMitarbeiter'] ?></option>
            <?php
            foreach(IndexDatabase::selectMaWork($ersteller->id) as $select_usr):
                ?>
                <option value="<?= $select_usr->vorname ?> <?= $select_usr->name ?>"><?= $select_usr->vorname ?>, <?= $select_usr->name ?></option>
            <?php
            endforeach;
            ?>
        </select>
    </div>
    <textarea name="log" class="summernote" required></textarea>
    <div class="text-end">
        <?php Formular::submit("submit","{$_SESSION['text']['b_kommentarSpeichern']}","btn btn-primary mt-3"); ?>
    </div>
</form>
