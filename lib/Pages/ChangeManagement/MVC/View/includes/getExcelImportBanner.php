<?php

use App\Functions\Functions;

?>
<div class="border__solid--gray_25 border__radius--5 p-2 linear__top--gray pointer" onclick="window.open('<?= Functions::getBaseURL() ?>lib/Pages/ChangeManagement/MVC/View/files/import.xlsx')">
    <div class="row">
        <div class="col-2">
            <div class="p-1">
                <img src="<?= Functions::getBaseURL() ?>skin/files/images/excel1.png" class="img-fluid">
            </div>
        </div>
        <div class="col-10">
            <div class="px-2">
                <span class="font-weight-600 oswald">EXCEL VORLAGE</span><br>
                <p class="font-size-10 m-0 p-0">Klicken Sie bitte auf diese Grafik, um sich die Excel Vorlage für die Teilenummern auf Ihren Rechner zu laden.</p>
            </div>
        </div>
    </div>
</div>
