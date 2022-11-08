<?php
/** (c) Joachim Göddel . RLMS */

use App\Functions\Functions;
use App\Pages\Administration\AdministrationDatabase;
use App\Pages\Servicedesk\ServicedeskDatabase;

?>
<div class="row border__bottom--dotted-gray pb-3 mb-3">
    <?php
    $x = 1;
    foreach ($dsp as $row):
        # Abteilung
        $abteilung = AdministrationDatabase::getOneAbt('b_abteilung_rlms', $row->aid);
        # Status
        $status = AdministrationDatabase::getStatusBadge($row->status);
        # ROW
        if ($x == 4):
            $x = 1;
            echo '</div><div class="row border__bottom--dotted-gray pb-3 mb-3">';
        endif;
        # Border
        $border = ($x < 3) ? 'border__right--dotted-gray' : '';
        # Bearbeiter
        $bid = ServicedeskDatabase::getBearbeiter($row->id);
        if (!empty($bid)):
            $usr = AdministrationDatabase::getUserInfo($bid);
            $v = substr($usr->vorname, 0, 1);
            $n = substr($usr->name, 0, 1);
            $iv = "<p class=\"init init__primary oswald\">$v$n</p>";
        else:
            $iv = '<p class="init init__warning"><i class="fa fa-user"></i></p>';
        endif;
        # Datum umschreiben
        $row->eintrag = Functions::germanDate($row->eintrag);
        # ID Verschlüsseln
        $id = Functions::encrypt($row->id);
        ?>
        <div class="col-4 <?= $border ?> pointer"
             onclick="top.location.href='/servicedesk/details?id=<?= $id ?>'">
            <div class="row">
                <div class="col-9">
                    <div class="p-3">
                        <p class="text-warning font-size-11 m-0 p-0"><?= $_SESSION['text']['' . $abteilung . ''] ?></p>
                        <h5 class="text-primary font-size-16 p-0 m-0 oswald font-weight-600"><?= $row->id ?>
                            : <?= $row->titel ?></h5>
                        <p class="text-muted font-size-11 italic p-0 m-0">
                            <b><?= $row->user ?></b>, <?= $row->eintrag ?> Uhr</p>
                        <?= $status ?>
                    </div><!-- p-3 -->
                </div><!-- col -->
                <div class="col-3">
                    <div class="p-3">
                        <?= $iv ?>
                    </div>
                </div><!-- col-3 -->
            </div><!-- row -->
        </div><!-- col -->
        <?php
        $x++;
    endforeach;
    ?>
</div><!-- row -->
