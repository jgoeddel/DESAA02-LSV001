<?php
# Seitenparameter
use App\Functions\Functions;
?>
<!doctype html>
<html lang="de" class="h-100">
<head>
    <?php
    # Basis-Head Elemente einbinden
    Functions::getHeadBase();
    ?>
    <title><?= $_SESSION['page']['version'] ?></title>
</head>
<body class="d-flex flex-column h-100" id="body">
<div class="fixed-top z3">
    <?php
    # Basis Header einbinden
    Functions::getHeaderBasePage(0);
    ?>
</div>
<main class="bg__white w-100">
    <div class="container-fluid mt-85">
        <div class="row">
            <div class="col-10 border__right--dotted-gray" id="dspPlan">
                <div class="px-5 pt-5 mt-5">
                    <img src="<?= Functions::getBaseUrl() ?>skin/files/images/Rotationsplan.png" alt="Rotationsplan" class="img-fluid mt-5 text-center">
                </div>
            </div>
            <div class="col-2">
                <div class="px-3 pt-5 mt-5">
                    <h3 class="oswald font-weight-300 text-primary border__bottom--dotted-gray pb-3 mb-3">ROTATIONSPLAN<span id="Timer" class="float-end"></span></h3>
                    <p>Scannen Sie bitte ihren Chip an dem dafür vorgesehenen RFID Lesegerät. Sollte Ihnen kein Ergebnis angezeigt werden melden Sie sich bitte bei Ihrem Teamleiter.</p>

                    <input type="text" name="rfid" id="rfid" class="rfidform" autofocus placeholder="****" autocomplete="new-password" style="color: white">
                    <button class="btn btn-danger font-size-30 oswald font-weight-100 w-100 p-4 mt-2 qform dspnone" onclick="clrPlan()" id="delbtn">
                        AUSBLENDEN
                    </button>
                </div>
            </div>
        </div>
    </div><!-- fluid -->
</main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<!-- Zusätzliche Javascript Dateien -->
<script type="text/javascript" src="<?= Functions::getBaseURL() ?>skin/plugins/chart.min.js"></script>
<script type="text/javascript" src="<?= Functions::getBaseUrl() ?>lib/Pages/Rotationsplan/MVC/View/js/action.js"></script>
<script type="text/javascript" src="<?= Functions::getBaseUrl() ?>lib/Pages/Rotationsplan/MVC/View/js/view.js"></script>
<script type="text/javascript" src="<?= Functions::getBaseUrl() ?>lib/Pages/Rotationsplan/MVC/View/js/submit.js"></script>
<script type="text/javascript">
    $(document).ready(function () {

    });
    $('#rfid').change(function(e){
        e.preventDefault();
        let rfid = '';
        rfid = $('#rfid').val();
        $.post("/rotationsplan/checkRfid", { rfid: "" + rfid + ""}, function (resp) {
            $.post("/rotationsplan/rfid", { id: "" + resp + ""}, function (text) {
                $('#dspPlan').html(text);
                $('#rfid').val('');
                myCounter.start();
                $('#delbtn').toggle(500);
            });
        })
    })

    $("#rfid").blur(function(){
        setTimeout(function(){
            $("#rfid").focus();
        },125);
    })

    function clrPlan(){
        "use strict";
        $('#dspPlan').html('<img src="<?= Functions::getBaseUrl() ?>skin/files/images/Rotationsplan.png" alt="Rotationsplan" class="img-fluid mt-5">');
        document.getElementById("Timer").innerHTML = "";
        myCounter.stop();
        $('#delbtn').fadeOut(500);
    }
</script>
</body>
</html>
