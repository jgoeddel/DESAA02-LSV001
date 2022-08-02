<?php
/** (c) Joachim Göddel . RLMS */

use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;

$elemente = Functions::array_sort($elemente, 'abteilung', SORT_ASC);
$pid = ucfirst($part);
?>
<h4 class="oswald text-primary">
    <?= strtoupper($_SESSION['text']['h_' . $part . '']) ?>
</h4>
<?php if($$part > 0):
    Functions::alert($_SESSION['text']['i_neu'.$pid.'']);
?>
<div class="form-check form-switch pointer pb-2 mb-2 border__bottom--dotted-gray">
    <input class="form-check-input" type="checkbox" id="<?= $part ?>" name="<?= $part ?>_erforderlich" value="1"
           onchange="$('#pkt<?= $pid ?>').toggle(800);">
    <label class="form-check-label" for="<?= $part ?>"><?= $_SESSION['text']['e_' . $part . ''] ?></label>
</div>
<div class="dspnone" id="pkt<?= $pid ?>">
    <?php
    foreach ($elemente as $row):
        if (ChangeManagementDatabase::checkAPQPCitycode($row->id, $_SESSION['wrk']['citycode'])):
            ?>
            <div class="row p-0 m-0 border__bottom--dotted-gray pointer">
                <label class="form-check-label col-10 font-size-12 p-0 m-0 pt-1" for="apqp<?= $row->id ?>">
                    <?= $_SESSION['text']['apqp_' . $row->id . ''] ?><br><span
                            class="text-muted italic"><?= $row->abteilung ?></span>
                </label>
                <div class="col-2 form-check form-switch p-0 m-0">
                    <input class="form-check-input float-end mt-2" type="checkbox" id="apqp<?= $row->id ?>"
                           name="<?= $part ?>[]" value="<?= $row->id ?>" checked="checked">
                </div>
            </div>
        <?php
        endif;
    endforeach;
    ?>
</div>
<?php else:
    Functions::alert($_SESSION['text']['f_neu'.$pid.'']);
endif;
