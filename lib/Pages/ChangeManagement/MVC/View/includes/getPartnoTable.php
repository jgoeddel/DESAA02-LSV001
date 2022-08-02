<?php
/** (c) Joachim Göddel . RLMS */

use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;

$_SESSION['seite']['id'] = 36;
$_SESSION['seite']['name'] = 'details';
$subid = 0;
$n_suche = '';

# Seite lesen / schreiben / admin
$pb = Functions::getBerechtigung(36);

# Parameter setzen
$seitelesen = $pb[0];
$seiteschreiben = $pb[1];
$seiteadmin = $pb[2];
// Parameter für die Anzeige von Bearbeitungsfunktionen
($seiteschreiben == 1) ? $dspedit = '' : $dspedit = 'dspnone';
# Sind noch Änderungen möglich ?
$edit = ($seiteschreiben == 1 && $row->status < 6) ? 1 : 0;

foreach ($pn as $part):
    $l = ChangeManagementDatabase::getLieferant($part->lid);
    $ziel = ($part->ziel == '0000-00-00') ? 'offen' : $part->zieldatum;
    $ist = ($part->ist == '0000-00-00') ? 'offen' : $part->istdatum;
    $dsnr = ($part->doppelsnr == 1) ? $_SESSION['text']['ja'] : $_SESSION['text']['nein'];
    $checked = ($part->status == 0) ? '' : 'checked disabled';
    $status = $part->status;
    $lieferant = ($part->lid === 0) ? $lieferant = 'N.N.' : $lieferant = $l->lieferant;
    ?>
    <tr>
        <td><?= $part->anlage ?></td>
        <td><?= $part->bezeichnung ?></td>
        <td><?= $part->alt ?></td>
        <td><?= $part->neu ?></td>
        <td><?= $dsnr ?></td>
        <td><?= $lieferant ?></td>
        <td id="sts<?=$part->id?>"><?= $_SESSION['text']['' . ChangeManagementDatabase::getStatusPart($status) . ''] ?></td>
        <td id="ziel<?=$part->id?>"><?= $ziel ?></td>
        <td id="ist<?=$part->id?>"><?= $ist ?></td>
        <td class="text-center">
            <div class="form-check form-switch" style="margin-left: -12px;">
                <input class="form-check-input float-end mt-2" type="checkbox" id="partno<?= $part->id ?>" name="<?= $part->id ?>" value="<?= $part->status ?>" <?= $checked ?> onchange="setUmstellungPartNo(<?= $part->id ?>,'<?= $_SESSION['text']['s_umgestellt'] ?>',<?= $id ?>)">
            </div>
        </td>
        <?php if($edit === 1): ?>
        <td class="text-center pointer" ondblclick="deletePartno(<?= $part->id ?>,<?= $id ?>)">
        <i class="fa fa-trash text-danger"></i>
        </td>
        <?php endif; ?>
    </tr>
<?php
endforeach;