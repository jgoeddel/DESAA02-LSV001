<?php
/** (c) Joachim Göddel . RLMS */

use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;

Functions::invisibleDataList("listLieferant", "lieferant"); ?>
<datalist id="listLieferant">
    <?php
    foreach($lieferanten AS $lf):
        $a = ChangeManagementDatabase::getLieferant($lf->lid);
        echo "<option value='$a->lieferant'>";
    endforeach;
    ?>
</datalist>
