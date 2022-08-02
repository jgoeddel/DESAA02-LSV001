<?php
/** (c) Joachim Göddel . RLMS */

use App\Pages\ChangeManagement\ChangeManagementDatabase;

?>
<div class="row address py-3 bg-light-lines border__top--dotted-gray" id="wrktime">
    <div class="col-12 text-center py-3 font-size-20">
        <?php
        $wrktime = ChangeManagementDatabase::getWrkTime($id);
        echo $wrktime[1]."<br>";
        echo $wrktime[0];
        ?>
    </div>
</div>
