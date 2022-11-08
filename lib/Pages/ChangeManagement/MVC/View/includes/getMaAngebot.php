<?php
/** (c) Joachim Göddel . RLMS */

use App\Pages\ChangeManagement\ChangeManagementDatabase;
use App\Pages\Home\IndexDatabase;

if($row->status < 6):
echo "<div class='m-0 row pb-3 mb-3 border__bottom--dotted-gray' id='ma{$id}'>";

$x = 0;
foreach($ma AS $u):
    $border = ($x < 3) ? 'border__right--dotted-gray' : '';
    $vorname = substr($u->vorname, 0, 1);
    # Prüfe die Berechtigung des Mitarbeiters
    $show = ChangeManagementDatabase::checkMaAngebot($u->id, $id);
    if(!$show):
    ?>
    <div class="col-3 mb-2 <?= $border ?> px-3 font-size-12 pointer" onclick="setMaAngebot(<?= $u->id ?>, <?= $id ?>, '<?= $location ?>')" id="ma<?= $u->id ?>">
       <i class="fa fa-plus-square text-success me-2"></i> <?= $vorname ?>. <?= $u->name ?>
    </div>
    <?php
    endif;
    $x++;
    if ($x == 4) $x = 0;
endforeach;
echo "</div>";
endif;
if($maa): ?>
    <div class="row border__bottom--dotted-gray font-size-12 mb-1 pb-1 italic bg__blue-gray--12 py-2">
        <div class="col-9 border__right--dotted-gray">
            <span class="ps-2">Mitarbeiter</span>
        </div>
        <div class="col-1 text-center border__right--dotted-gray">
            R
        </div>
        <div class="col-1 text-center border__right--dotted-gray">
            W
        </div>
        <?php if($row->status < 6): ?>
        <div class="col-1 text-center">
            D
        </div>
    <?php endif; ?>
    </div>
<?php
foreach($maa AS $t):
    $r = ($t->awrite == 0) ? '<i class="fa fa-dot-circle text-success"></i>' : '<i class="fa fa-dot-circle text-muted pointer" onclick="setAccess(\'aread\',\''.$row->location.'\','.$row->id.','.$t->mid.')"></i>';
    $w = ($t->awrite == 1) ? '<i class="fa fa-dot-circle text-success"></i>' : '<i class="fa fa-dot-circle text-muted pointer" onclick="setAccess(\'awrite\',\''.$row->location.'\','.$row->id.','.$t->mid.')"></i>';
    ?>
    <div class="row border__bottom--dotted-gray font-size-12 mb-1 pb-1">
        <div class="col-9 border__right--dotted-gray">
            <span class="ps-2"><?php IndexDatabase::getNameMa($t->mid); ?></span>
        </div>
        <div class="col-1 text-center border__right--dotted-gray">
            <?= $r ?>
        </div>
        <div class="col-1 text-center border__right--dotted-gray">
            <?= $w ?>
        </div>
        <?php if($row->status < 6): ?>
        <div class="col-1 text-center">
            <i class="fa fa-minus-square text-danger pointer fa-fw" onclick="setMaAngebot(<?= $t->mid ?>,<?= $id ?>,'<?= $location ?>');"></i>
        </div>
    <?php endif; ?>
    </div>
<?php
endforeach;
endif;