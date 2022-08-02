<?php
/** (c) Joachim Göddel . RLMS */

use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;
$edit = ($row->name == $_SESSION['user']['dbname'] && $row->status <= 7) ? 1 : 0;
?>
<div class="row pb-3 border__bottom--dotted-gray">
    <div class="col-4 border__right--dotted-gray">
        <small class="text-muted italic">
            <?= $_SESSION['text']['h_lastOrder'] ?>
        </small><br>
        <?php
        $val = ChangeManagementDatabase::dspOrderFeld($id, '2','nummer');
        if ($edit === 1 && $val == '-'):
            Functions::invisibleInput("text", "", "", "".ChangeManagementDatabase::dspOrderFeld($id, '2','nummer')."","onblur=\"sendOrder('nummer',this.value,$id,2)\"");
        else:
            Functions::invisibleInput("text", "", "", "".ChangeManagementDatabase::dspOrderFeld($id, '2','nummer')."","", "disabled");
        endif;
        ?>
    </div>
    <div class="col-4 text-center border__right--dotted-gray">
        <small class="text-muted italic">
            <?= $_SESSION['text']['h_datum'] ?>
        </small><br>
        <?php
        $val = ChangeManagementDatabase::dspOrderFeld($id, '2','datum');
        if ($edit === 1 && $val == '-'):
            Functions::invisibleInput("date", "", "text-center", "".ChangeManagementDatabase::dspOrderFeld($id, '2','datum')."","onblur=\"sendOrder('datum',this.value,$id,2)\"");
        else:
            Functions::invisibleInput("date", "", "text-center", "".ChangeManagementDatabase::dspOrderFeld($id, '2','datum')."","", "disabled");
        endif;
        ?>
    </div>
    <div class="col-4 text-center">
        <small class="text-muted italic">
            <?= $_SESSION['text']['h_zeit'] ?>
        </small><br>
        <?php
        $val = ChangeManagementDatabase::dspOrderFeld($id, '2','zeit');
        if ($edit === 1 && $val == '-'):
            Functions::invisibleInput("time", "", "text-center", "".ChangeManagementDatabase::dspOrderFeld($id, '2','zeit')."","onblur=\"sendOrder('zeit',this.value,$id,2)\"");
        else:
            Functions::invisibleInput("time", "", "text-center", "".ChangeManagementDatabase::dspOrderFeld($id, '2','zeit')."","", "disabled");
        endif;
        ?>
    </div>
</div>
<div class="row py-3 border__bottom--dotted-gray">
    <div class="col-4 border__right--dotted-gray">
        <small class="text-muted italic">
            <?= $_SESSION['text']['h_firstOrder'] ?>
        </small><br>
        <?php
        $val = ChangeManagementDatabase::dspOrderFeld($id, '1','nummer');
        if ($edit === 1 && $val == '-'):
            Functions::invisibleInput("text", "", "", "".ChangeManagementDatabase::dspOrderFeld($id, '1','nummer')."","onblur=\"sendOrder('nummer',this.value,$id,1)\"");
        else:
            Functions::invisibleInput("text", "", "", "".ChangeManagementDatabase::dspOrderFeld($id, '1','nummer')."","", "disabled");
        endif;
        ?>
    </div>
    <div class="col-4 text-center border__right--dotted-gray">
        <small class="text-muted italic">
            <?= $_SESSION['text']['h_datum'] ?>
        </small><br>
        <?php
        $val = ChangeManagementDatabase::dspOrderFeld($id, '1','datum');
        if ($edit === 1 && $val == '-'):
            Functions::invisibleInput("date", "", "text-center", "".ChangeManagementDatabase::dspOrderFeld($id, '1','datum')."","onblur=\"sendOrder('datum',this.value,$id,1)\"");
        else:
            Functions::invisibleInput("date", "", "text-center", "".ChangeManagementDatabase::dspOrderFeld($id, '1','datum')."","", "disabled");
        endif;
        ?>
    </div>
    <div class="col-4 text-center">
        <small class="text-muted italic">
            <?= $_SESSION['text']['h_zeit'] ?>
        </small><br>
        <?php
        $val = ChangeManagementDatabase::dspOrderFeld($id, '1','zeit');
        if ($edit === 1 && $val == '-'):
            Functions::invisibleInput("time", "", "text-center", "".ChangeManagementDatabase::dspOrderFeld($id, '1','zeit')."","onblur=\"sendOrder('zeit',this.value,$id,1)\"");
        else:
            Functions::invisibleInput("time", "", "text-center", "".ChangeManagementDatabase::dspOrderFeld($id, '1','zeit')."","", "disabled");
        endif;
        ?>
    </div>
</div>