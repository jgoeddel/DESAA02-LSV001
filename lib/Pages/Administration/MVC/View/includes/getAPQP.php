<?php
/** (c) Joachim Göddel . RLMS */

use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;


?>
<div class="col-4 border__right--dotted-gray">
    <div class="pe-3">
        <h3 class="border__bottom--dotted-gray pb-3 pt-2">
            <span class="badge badge-primary me-3">3</span>
            <?= $_SESSION['text']['h_quelle'] ?>
        </h3>
        <ul id="quelle" class="quelle connectedSortable m-0 p-0">
            <?php
            $q = 0;
            foreach ($apqp as $li):
                $isin = ChangeManagementDatabase::checkAPQPCitycode($li->id, $citycode);
                if (empty($isin)):
                    if ($li->id < 10): $lid = "0" . $li->id;
                    else: $lid = $li->id; endif;
                    ?>
                    <li id="<?= $li->id ?>" onclick="editAPQP(<?= $li->id ?>, '<?= $bereich ?>', '<?= $citycode ?>')"
                        ondblclick="remAPQP(<?= $li->id ?>, '<?= $bereich ?>', '<?= $citycode ?>',1);">
                        <div class="border__bottom--dotted-gray py-2 pointer dhover">
                            <p class="font-size-14 m-0 p-0"><?= $_SESSION['text']['apqp_' . $lid . ''] ?></p>
                            <p class="font-size-10 text-muted m-0 p-0"><?= $li->abteilung ?></p>
                        </div>
                    </li>
                <?php
                endif;
                $q++;
            endforeach;
            ?>
        </ul>
    </div>
</div>
<div class="col-4 border__right--dotted-gray">
    <div class="px-3">
        <h3 class="border__bottom--dotted-gray pb-3 pt-2">
            <span class="badge badge-primary me-3">4</span>
            <?= $_SESSION['text']['h_ziel'] ?>
        </h3>
        <ul id="ziel" class="connectedSortable m-0 p-0">
            <li class="ui-state-disabled">
                <?php Functions::alert($_SESSION['text']['i_zielApqp']); ?>
            </li>
            <?php
            $z = 0;
            foreach ($apqp as $li):
                $isin = ChangeManagementDatabase::checkAPQPCitycode($li->id, $citycode);
                if (!empty($isin)):
                    if ($li->id < 10): $lid = "0" . $li->id;
                    else: $lid = $li->id; endif;
                    ?>
                    <li id="<?= $li->id ?>" onclick="editAPQP(<?= $li->id ?>, '<?= $bereich ?>', '<?= $citycode ?>')"
                        ondblclick="remAPQP(<?= $li->id ?>, '<?= $bereich ?>', '<?= $citycode ?>',0);">
                        <div class="border__bottom--dotted-gray py-2 pointer dhover">
                            <p class="font-size-14 m-0 p-0"><?= $_SESSION['text']['apqp_' . $lid . ''] ?></p>
                            <p class="font-size-10 text-muted m-0 p-0"><?= $li->abteilung ?></p>
                        </div>
                    </li>
                <?php
                endif;
                $z++;
            endforeach;
            ?>

        </ul>
    </div>
</div>
<div class="col-4">
    <div class="ps-3">
        <?php
        $area = '';
        $h3 = 'Neuer Eintrag';
        if (isset($apqpid) && $apqpid > 0):
            $ap = ChangeManagementDatabase::getOneAPQP($apqpid);
            $area = $ap->titel;
            $sbereich = ($ap->evaluation != '') ? $ap->evaluation : $ap->tracking;
        endif;
        ?>
        <h3 class="border__bottom--dotted-gray pb-2 mb-2">
            <?php if (isset($apqpid) && $apqpid > 0): ?>
                Eintrag bearbeiten
            <?php else: ?>
                Neuer Eintrag
            <?php endif; ?>
        </h3>
        <p class="border__bottom--dotted-gray pb-2 mb-2"><b>Standort: </b><?= $citycode ?>
            <br><b>Bereich:</b> <?= ucfirst($bereich) ?></p>

        <form id="apqpNeu" method="post">
            <input type="hidden" name="citycode" value="<?= $citycode ?>">
            <input type="hidden" name="bereich" value="<?= $bereich ?>">
            <input type="hidden" name="apqpid" value="<?= $apqpid ?>">
            <div class="border__bottom--dotted-gray pb-2 mb-2">
                <label class="font-size-12 text-muted italic" for="titel">Titel <span
                            class="text-warning">*</span></label><br>
                <textarea class="summernote m-0 p-0" name="titel" required><?= $area ?></textarea>
            </div><!-- border -->
            <div class="border__bottom--dotted-gray pb-2 mb-2">
                <label class="font-size-12 text-muted italic" for="start">
                    <?= $_SESSION['text']['h_abteilung'] ?> / Bereich <span class="text-warning">*</span>
                </label>
                <select name="abteilung" class="invisible-formfield" required>
                    <option value="21"><?= $_SESSION['text']['i_selectOption'] ?> ?</option>
                    <?php
                    foreach ($ab as $abt):
                        $ckd = ($abt->kurz == $sbereich) ? 'selected' : '';
                        echo "<option value='$abt->kurz' $ckd>" . $_SESSION['text']['abt_' . $abt->id . ''] . "</option>";
                    endforeach;
                    ?>
                </select>
            </div><!-- border -->
            <div class="border__bottom--dotted-gray pb-2 mb-2">
                <label class="font-size-12 text-muted italic" for="start">
                    Verantwortlich <span class="text-warning">*</span>
                </label>
                <select name="verantwortlich" class="invisible-formfield" required>
                    <option value="21"><?= $_SESSION['text']['i_selectOption'] ?> ?</option>
                    <?php
                    foreach ($va as $van):
                        $ckd = ($van->verantwortlich == $ap->abteilung) ? 'selected' : '';
                        echo "<option value='$van->verantwortlich' $ckd>$van->verantwortlich</option>";
                    endforeach;
                    ?>
                </select>
            </div><!-- border -->
            <div class="text-end">
                <?php if (isset($apqpid) && $apqpid > 0): ?>
                    <input type="reset" class="btn btn-danger oswald text-uppercase font-weight-300 me-2"
                           value="Eintrag löschen" onclick="deleteAPQP(<?= $apqpid ?>)">
                <?php endif; ?>
                    <input type="submit" class="btn btn-primary oswald text-uppercase font-weight-300"
                           value="Eintrag speichern">
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
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

    // Neuen Mitarbeiter speichern
    $('#apqpNeu').bind('submit', function () {
        $.post("/administration/apqp/neu", $("#apqpNeu").serialize(), function (responseText) {
            if (responseText == 0) {
                swal.fire({
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                }).then(function () {
                    $.post("/administration/getAPQP", {
                        citycode: "<?= $citycode ?>",
                        bereich: "<?= $bereich ?>"
                    }, function (resp) {
                        $('#zuordnung').html(resp);
                    });
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
</script>