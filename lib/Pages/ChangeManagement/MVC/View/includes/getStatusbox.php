<?php
/** (c) Joachim Göddel . RLMS */

?>
<div class="row py-3 border__top--dotted-gray border__bottom--dotted-gray">
    <div class="col-md-4 text-center border__right--dotted-gray">
        <small class="italic text-muted"><?= $_SESSION['text']['h_overStatus'] ?></small><br>
        <span class="font-size-50" id="overStatus"><!-- AJAX: dspStatus() --></span>
    </div>
    <div class="col-md-4 text-center border__right--dotted-gray">
        <small class="italic text-muted"><?= $_SESSION['text']['h_studyStatus'] ?></small><br>
        <span class="font-size-50" id="studyStatus"><!-- AJAX: dspStatus() --></span>
    </div>
    <div class="col-md-4 text-center">
        <small class="italic text-muted"><?= $_SESSION['text']['h_introduceStatus'] ?></small><br>
        <span class="font-size-50" id="introduceStatus"><!-- AJAX: dspStatus() --></span>
    </div>
</div>