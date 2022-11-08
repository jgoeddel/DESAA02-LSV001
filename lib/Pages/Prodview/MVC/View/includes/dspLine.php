<?php
/** (c) Joachim Göddel . RLMS */

use App\Pages\Prodview\ProdviewDatabase;

$a = ProdviewDatabase::getLastEntry($_SESSION['line'][$line->Id]);
if(!empty($a)):
    $bg = ProdviewDatabase::bgColorStatus($a->IdStatus);
    $icon = ProdviewDatabase::iconStatus($a->IdStatus);
    $dt = new DateTime($a->TimeStamp);
    $tag = $dt->format('d.m.Y');
    $zeit = $dt->format('H:i:s');
else:
    $bg = 'blue-gray--50';
    $icon = 'fa-ban';
endif;
?>
<div class="mb-3">
    <div class="pe-3">
        <div class="border__dotted--gray border__radius--10 text-center">
            <div class="row">
                <div class="col-10 border__right--dotted-gray">
                    <h3 class="oswald font-size-50 pt-5 mb-5"><?= $line->Line ?> <?= $line->Description ?></h3>
                    <?php if(!empty($a)): ?>
                        <div class="row pb-3 mt-3">
                            <div class="col-5"><span class="small font-size-16"><?= $tag ?></span><br><span class="oswald font-size-30"><?= $zeit ?></span></div>
                            <div class="col-4 border__right--dotted-gray border__left--dotted-gray"><span class="small font-size-16">VIN</span><br><span class="oswald font-size-30"><?= $a->Value1 ?></span></div>
                            <div class="col-3"><span class="small font-size-16">Seq</span><br><span class="oswald font-size-30"><?= $a->Value2 ?></span></div>
                        </div>
                    <?php else: ?>
                        <div class="mt-3">
                            <div class="pb-3 mt-3"><small class="font-size-16">&nbsp;</small><br><span class="oswald font-size-30">Keine aktuellen Daten vorhanden</span></div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-2 bg__<?= $bg ?> pt-5">
                    <i class="fa <?= $icon ?> text-white fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>