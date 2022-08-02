<?php
/** (c) Joachim Göddel . RLMS */

use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;
?>
<div class="row py-3 border__bottom--dotted-gray bg-light-lines mb-3">
    <div class="col-6 text-center border__right--dotted-gray">
        <small class="text-muted italic">
            <?= $_SESSION['text']['h_planDate'] ?>
        </small><br>
        <span class="font-size-20">
            <?php
            if ($edit === 1):
                Functions::invisibleInput("date", "", "text-center", "".ChangeManagementDatabase::dspImplementDate($id, 'base2pla')."","onblur=\"sendValue('implement_date',this.value,{$id},'base2pla')\"");
            else:
                Functions::invisibleInput("date", "", "text-center", "".ChangeManagementDatabase::dspImplementDate($id, 'base2pla')."","", "disabled");
            endif;
            ?>
        </span>
    </div><!-- col-6 -->
    <div class="col-6 text-center">
        <small class="text-muted italic">
            <?= $_SESSION['text']['h_actualyDate'] ?>
        </small><br>
        <span class="font-size-20">
            <?php
            $val = ChangeManagementDatabase::dspImplementDate($id, 'base2imp');
            $edit = ($row->name == $_SESSION['user']['dbname'] && $row->status <= 7 && $val == '-') ? 1 : 0;
            if ($edit === 1):
                Functions::invisibleInput("date", "", "text-center", "".ChangeManagementDatabase::dspImplementDate($id, 'base2imp')."","onblur=\"sendValue('implement_date',this.value,{$id},'base2imp')\"");
            else:
                Functions::invisibleInput("date", "", "text-center", "".ChangeManagementDatabase::dspImplementDate($id, 'base2imp')."","", "disabled");
            endif;
            ?>
        </span>
    </div><!-- col-6 -->
</div><!-- row -->
