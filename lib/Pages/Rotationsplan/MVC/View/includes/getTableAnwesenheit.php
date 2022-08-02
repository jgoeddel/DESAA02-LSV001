<?php
/** (c) Joachim Göddel . RLMS */

use App\Pages\Administration\AdministrationDatabase;

$db = new \App\Pages\Rotationsplan\MVC\View\functions\Functions();

foreach ($maTable as $user):
    # Qualifikation
    $quali = $db->getAllQuali($user->id);
    $fn = ($user->funktion != 0) ? '' : 'ma';
    $bgf = ($user->funktion != 0) ? 'bg-light-lines' : '';
    $ssts = ($user->funktion != 0) ? 'selected' : '';
    # Training
    $tr = ($db->getTraining($user->id) > 0) ? '<span class="badge badge-info">T</span>' : '';
    # Handicap
    $hc = ($db->getHandicap($user->id) > 0) ? '<span class="badge badge-warning">H</span>' : '';
    # Abwesend
    $ah = ($db->getAbwesend($user->id) !== false) ? '<span class="badge badge-danger">A</span>' : '';
    $dis = ($db->getAbwesend($user->id) !== false) ? 'disabled' : '';
    # Checkbox
    $csts = ($db->getAbwesend($user->id) !== false) ? '' : 'checked';
    $user->vorname = mb_substr($user->vorname, 0, 1);
    ?>
    <tr class="<?= $bgf ?> <?= $dis ?>" id="tr<?= $user->id ?>">
        <td class="col-1 text-end"><?= $user->id ?></td>
        <td>
            <label class="form-check-label pointer w-100" for="<?= $user->id ?>">
                <b><?= $user->name ?></b>, <?= $user->vorname ?>
            </label>
        </td>
        <td class="col-1 text-end"><?= $tr ?> <?= $hc ?> <?= $ah ?></td>
        <td class="col-1 text-center">
            <div class="form-check form-switch p-0 m-0 pt-1 ps-2">
                <input type="checkbox" class="form-check-input m-0" name="anwesend[<?= $user->id ?>]"
                       onchange="zaehlen(this);checkValue(this.value,<?= $user->id ?>);" id="<?= $user->id ?>"
                       value="<?= $user->id ?>" <?= $csts ?>>
            </div>
        </td>
        <td>
            <?php
            if($dis != 'disabled'):
                ?>
                <select name="station[<?= $user->id ?>]" class="no-border w-100 bg__white--6" id="s<?= $user->id ?>"
                        onchange="checkAnwesenheit(<?= $user->id ?>);">
                    <option value="0"><?= $_SESSION['text']['s_station'] ?></option>
                    <option value="999"
                            class="text-warning font-weight-600" <?= $ssts ?>><?= $_SESSION['text']['t_flexibelEinsetzbar'] ?></option>

                    <?php
                    if ($quali !== false):
                        foreach ($quali as $q):
                            $db->getSelectOption($q->sid);
                        endforeach;
                    endif;
                    ?>
                </select>
            <?php
            endif;
            ?>
        </td>
    </tr>
<?php
endforeach;