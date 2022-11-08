<?php
/** (c) Joachim Göddel . RLMS */

# Klassen
use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;
use App\Pages\Home\IndexDatabase;
use App\Pages\Prodview\ProdviewDatabase;

$_SESSION['seite']['id'] = 54;
$_SESSION['seite']['name'] = 'citycode';
$subid = 0;
$n_suche = '';
$dspKalender = true;
$dspTag = false;

# Seite lesen / schreiben / admin
$pb = Functions::getBerechtigung($_SESSION['seite']['id']);

# Parameter setzen
$seitelesen = $pb[0];
$seiteschreiben = $pb[1];
$seiteadmin = $pb[2];

# Keine Berechtigung, dann zurück auf die Startseite
if (!isset($seitelesen) || $seitelesen != 1):
    header(header: 'Location: /');
endif;

// Parameter für die Anzeige von Bearbeitungsfunktionen
($seiteschreiben == 1) ? $dspedit = '' : $dspedit = 'dspnone';
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
<body class="d-flex flex-column h-100 ford" id="body" data-spy="scroll" data-target=".navbar" data-offset="183">
<div class="fixed-top z3">
    <?php
    # Basis Header einbinden
    Functions::getHeaderBasePage(1);
    Functions::dspNavigation($dspedit, 'index', '', 1);
    ?>
</div>
<?php
Functions::dspParallaxSmall("INTRANET &bull; RHENUS AUTOMOTIVE", "{$_SESSION['text']['h_citycode']}");
?>
<main class="w-100 bg__white--95 flex-shrink-0" id="main">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <div class="container-fluid p-4">
        <div class="row">
        <?php
        // Daten einlesen
        for ($i = 1; $i < 5; $i++):
            $context = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
            $url = 'http://172.16.101.101:813' . $i . '/xml/stations';
            $xml = file_get_contents($url, false, $context);
            $xml = simplexml_load_string($xml);
            foreach ($xml->station as $x):
                $y[] = $x;
            endforeach;
        endfor;
        # Testausgabe
        # echo "<pre>"; print_r($y); echo "</pre>";
        $abc = array("", "aa", "ab", "ac", "ad", "ae", "af", "ag", "ah", "ai", "aj", "ak", "al", "am", "an", "ao", "ap", "aq", "ar", "as", "at", "au", "av", "aw", "ax", "ay", "az", "ba", "bb", "bc", "bd", "be", "bf", "bg", "bh", "bi", "bj", "bk", "bl", "bm", "bn", "bo", "bp", "bq", "br", "bs", "bt", "bu", "bv", "bw", "bx", "by", "bz");
        $x = 0;
        # Alle Ergebnisse durchgehen
        for($c = 0; $c < count($y)-1; $c++):
            foreach ($y[$c]->attributes() as $d => $e):
                $$d[$c] = $e;
            endforeach;
            switch ($mode[$c]):
                case('AUTO'):
                    $bg[$c] = '3';
                    $style[$c] = 'border-left: 3px solid rgb(40,167,69); color: #fff;"';
                    break;
                case('MAN'):
                    $bg[$c] = '2';
                    $style[$c] = 'color: #fff;border-left: 3px solid rgb(250,187,0);"';
                    break;
                case('N/A'):
                    $bg[$c] = '4';
                    $style[$c] = 'color: #fff;border-left: 3px solid rgb(220,53,69);"';
                    break;
                case(''):
                    $bg[$c] = '5';
                    $style[$c] = 'style="color: #ccc;border-left: 3px solid rgb(255,255,255);"';
                    break;
            endswitch;
            if($fault[$c] == 'true'): $bg[$c] = '4'; $style[$c] = 'color: #fff;border-left: 30px solid rgb(220,53,69);"'; endif;
            if($opreq[$c] == 'true'): $bg[$c] = '1'; endif;

            $name[$c] = str_replace("FC", "", $name[$c]);
            if(!isset($vin[$c]) || $vin[$c] == ''): $vin[$c] = '-'; endif;
            if(!isset($rot[$c]) || $rot[$c] == ''): $rot[$c] = '-'; endif;
            if($opreq[$c] == 'true'): $bg[$c] = '1'; endif;
            ?>
            <div class="col-2 mb-3">

                        <div class="font-size-12">
                            <h3 class="oswald font-size-16 font-weight-300">Station:<b><?= $name[$c] ?></b></h3>
                            ROT: <b><?= $rot[$c] ?></b><br>
                            VIN: <b><?= $vin[$c] ?></b>
                            <hr>
                        </div>
            </div>
            <?php
            $rot[$c] = intval($rot[$c]);
            if($rot[$c] == 0): unset($rot[$c]); endif;
            $x++;
            if($x > 5): echo '<div class="w-100"></div>'; $x = 0; endif;
        endfor;
        $min = str_pad(min($rot), 4, "0", STR_PAD_LEFT);
        $max = str_pad(max($rot), 4, "0", STR_PAD_LEFT);
        ?>
        </div>
    </div>
</main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<script type="text/javascript"
        src="<?= Functions::getBaseURL() ?>/lib/Pages/Prodview/MVC/View/js/view.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        heightMainContainer();

    });

</script>
</body>
</html>