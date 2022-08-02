<?php
/** (c) Joachim Göddel . RLMS */

use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;
use App\Pages\Home\IndexDatabase;

# Anzahl Kommentare
$k = ChangeManagementDatabase::countComments($part, $bid, $bereich);
if ($k > 0):
    foreach (ChangeManagementDatabase::getCom($bid, $bereich, $part) as $kom):
        $ersteller = IndexDatabase::getUserInfo($kom->mid);
        ?>
        <div class="kommentar row border__bottom--dotted-gray m-0 p-0 mb-2 pb-2">
            <div class="col-2">
                <img src="<?= Functions::getBaseUrl() ?>/lib/Pages/Administration/MVC/View/files/images/<?= $ersteller->bild ?>"
                     class="rund_small img-thumbnail img-fluid">
            </div>
            <div class="col-10 font-size-12 pt-2 pe-2">
                <?php if ($kom->frage_an != ''): ?>
                    <?= sprintf($_SESSION['text']['t_hatEineFrage'], $kom->name) ?><b><?= $kom->frage_an ?></b> <span
                            class="badge badge-warning pointer ms-2"
                            onclick="$('#ant<?= $kom->id ?>').toggle(500);"><?= $_SESSION['text']['h_antworten'] ?></span>
                <?php else: ?>
                    <b><?= $kom->name ?></b>
                <?php endif; ?>
                <br><small class="text-muted"><?= $kom->am ?></small><br>
                <?= $kom->kommentar ?>
            </div>
        </div>
        <?php
        if ($kom->frage_an != ''): ?>
            <div id="ant<?= $kom->id ?>" class="dspnone">
                <form method="post" class="needs-validation antkom">
                    <input type="hidden" name="fid" value="<?= $kom->id ?>">
                    <input type="hidden" name="bid" value="<?= $bid ?>">
                    <textarea name="kommentar" class="summernote"></textarea>
                    <div class="text-end mt-2">
                        <input type="submit" class="btn btn-primary btn-sm" value="Antwort speichern">
                    </div>
                </form>
            </div>
            <?php // Antwort(en) vorhanden
            if (ChangeManagementDatabase::countAntwort($kom->id) > 0):
                foreach (ChangeManagementDatabase::getAntwort($kom->id) as $an):
                    $ma = IndexDatabase::getUserInfo($an->mid);
                    ?>
                    <div class="kommentar row border__bottom--dotted-gray-50 m-0 p-0 bg-light-lines">
                        <div class="col-2">
                            <img src="<?= Functions::getBaseUrl() ?>/lib/Pages/Administration/MVC/View/files/images/<?= $ma->bild ?>"
                                 class="rund_small img-thumbnail img-fluid">
                        </div>
                        <div class="col-10 font-size-12 pt-2 pe-2">
                            <div class="">
                                <?= sprintf($_SESSION['text']['frage_beantwortet'], $an->name) ?>
                                <br><small class="text-muted"><?= $an->am ?></small><br>
                                <?= $an->kommentar ?></div>
                        </div>
                    </div>
                <?php
                endforeach;
            endif;
        endif;
    endforeach;
endif;