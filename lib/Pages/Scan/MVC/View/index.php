<?php
/** (c) Joachim Göddel . Rhenus Automotive Services GmbH & Co. KG */

# Klassen
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

<main class="w-100 bg__white--95 flex-shrink-0" id="main">
    <div class="container-fluid bg__white pt-1">
        <form id="scn" method="post" class="mt-1">
            <p><b>Wareneingan scannen</b></p>
            <input type="text" name="lbl" id="lbl" class="form-control font-size-30 oswald text-center" placeholder="Label scannen">
        </form>
        <div class="ergebnis mt-4">
                    <?php
                    # NX6G 6007 DA+6+FE1373004141
                    if($_POST):
                        $e[0] = 1;
                        $e[1] = 1;
                        $e[2] = 1;
                        $matnr = '<span class="text-danger">Keine Materialnummer erkannt</span>';
                        $anzahl = '<span class="text-danger"><i class="fa fa-exclamation-circle"></i></span>';
                        $racknr = '<span class="text-danger">Keine Racknummer erkannt</span>';
                        # var_dump($_POST);
                        $a = explode("+", $_POST['lbl']);
                        $a = array_filter($a);
                        # echo "<pre>"; print_r($a); echo "</pre>";
                        # Anzahl Elemente muss 3 sein
                        if(count($a) == 3):
                            # Schauen, was in welchem String steckt
                            if(strpos($a[0],'6007')): $matnr = $a[0]; unset($a[0]); $e[0] = 0; endif;
                            if(strpos($a[1],'6007')): $matnr = $a[1]; unset($a[1]); $e[0] = 0; endif;
                            if(strpos($a[2],'6007')): $matnr = $a[2]; unset($a[2]); $e[0] = 0; endif;
                            $a = array_values($a);
                            # Schauen, wo die Anzahl steht
                            if(strlen($a[0]) == 1): $anzahl = $a[0]; unset($a[0]); endif;
                            if(strlen($a[1]) == 1): $anzahl = $a[1]; unset($a[1]); endif;
                            $a = array_values($a);
                            $racknr = $a[0];
                        endif;
                        # Ausgabe
                        echo '<div class="row border__bottom--dotted-gray pb-3 mb-3">';
                        echo '<div class="col-9">';
                        echo '<div class="font-size-14">Materialnummer</div>';
                        echo "<div class=\"font-size-30 oswald font-weight-600\">$matnr</div>";
                        echo '</div>';
                        echo '<div class="col-3">';
                        echo '<div class="font-size-14">Anzahl</div>';
                        echo "<div class=\"font-size-30 oswald font-weight-600\">$anzahl</div>";
                        echo '</div>';
                        echo '</div>'; # ROW
                        echo '<div class="row border__bottom--dotted-gray pb-3 mb-3">';
                        echo '<div class="col-12">';
                        echo '<div class="font-size-14">Racknummer</div>';
                        echo "<div class=\"font-size-30 oswald font-weight-600\">$racknr</div>";
                        echo '</div>';
                        echo '</div>'; # ROW
                    endif;
                    ?>
            </div>
        </div>
    </div>
</main>
<?php
# Footer einbinden
Functions::getFooterJs();
?>
<!-- zusätzliche Javascript Bibliotheken -->
<script type="text/javascript" src="<?= Functions::getBaseURL() ?>/skin/plugins/other/progressbar.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#lbl').focus();
    });
</script>
</body>
</html>