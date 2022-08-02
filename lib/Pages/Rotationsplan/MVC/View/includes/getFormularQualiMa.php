<?php
/** (c) Joachim Göddel . RLMS */
$db = new \App\Pages\Rotationsplan\MVC\View\functions\Functions();
# Stationdetails
$stn = $db->getStation($sid);
?>
<div class="pt-3 border__top--dotted-gray_50">
    <h4 class="font-size-14 border__bottom--dotted-gray_50 pb-2"><span class="oswald font-weight-300">Qualifikation</span><br><small class="font-size-11 italic font-weight-300 text-warning">für Station <?= $stn->station ?> &bull; <?= $stn->bezeichnung ?></small></h4>
    <div class="row bg-light-lines p-0 m-0">
        <div class="col-6 border__right--dotted-gray_50">
            <div class="p-3 text-center">
                <span class="oswald font-weight-300">Einarbeitung starten</span><br>
                <button class="btn btn-warning mt-2 btn-block oswald font-weight-300" onclick="setTrainingMa(<?= $sid ?>,<?= $uid ?>);">
                    <i class="fa fa-play"></i>
                </button>
            </div>
        </div>
        <div class="col-6">
            <div class="p-3 text-center">
                <span class="oswald font-weight-300">Qualifikation erworben</span><br>
                <button class="btn btn-success mt-2 btn-block oswald font-weight-300" onclick="setQualiMa(<?= $sid ?>, <?= $uid ?>);">
                    <i class="fa fa-check"></i>
                </button>
            </div>
        </div>
    </div>
</div>