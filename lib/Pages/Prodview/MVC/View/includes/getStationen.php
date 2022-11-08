<?php
/** (c) Joachim Göddel . RLMS */

use App\Pages\Prodview\ProdviewDatabase;

?>
    <h3 class="font-weight-300 border__bottom--dotted-gray mb-3 pb-3">
        Stationen der Linie <?= $linie->LineId ?>. <?= $linie->Line ?> <?= $linie->Description ?>
    </h3>
<?php
foreach (ProdviewDatabase::getStationLine($id) as $row):
    # Anzahl IO
    $io = ProdviewDatabase::getSumAuftrag($row->Id, '2022-06-24', 1);
    # Anzahl NIO
    $nio = ProdviewDatabase::getSumAuftrag($row->Id, '2022-06-24', 2);
    ?>
    <div class="border__bottom--dotted-gray pb-2 mb-2 font-size-12 row pointer" onclick="dspTableStation(<?= $row->Id ?>);">
        <div class="col-8 border__right--dotted-gray">
            <div class="pe-3">
                <?= $row->Id ?>. <?= $row->StationName ?><br><small
                        class="font-size-10 text-muted"><?= $row->Description ?></small>
            </div>
        </div>
        <div class="col-2 text-center border__right--dotted-gray">
            <?= $io ?>
        </div>
        <div class="col-2 text-center">
            <div class="ps-3 text-danger">
                <?= $nio ?>
            </div>
        </div>
    </div>

<?php
endforeach;