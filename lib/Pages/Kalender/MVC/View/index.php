<?php
/** (c) Joachim Göddel . RLMS */
# Klassen
use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;
use App\Pages\Home\IndexDatabase;
use App\Pages\Kalender\KalenderDatabase;
use App\Pages\Kundenaenderungen\KundenaenderungenDatabase;
use App\Pages\Logbuch\LogbuchDatabase;
use App\Pages\Produktion\ProduktionDatabase;
use App\Pages\Servicedesk\ServicedeskDatabase;

$_SESSION['seite']['id'] = 48;
$_SESSION['seite']['name'] = 'index';
$subid = 0;
$n_suche = '';

# Seite lesen / schreiben / admin
$pb = Functions::getBerechtigung($_SESSION['seite']['id']);

# Parameter setzen
$seitelesen = $pb[0];
$seiteschreiben = $pb[1];
$seiteadmin = $pb[2];

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
<body class="d-flex flex-column h-100 kalender" id="body">
    <div class="fixed-top z3">
        <?php
        # Basis Header einbinden
        Functions::getHeaderBasePage(1);
        Functions::dspNavigation($dspedit,'index','',1);
        ?>
    </div>
    <?php
    $jahr = DATE('Y');
    Functions::dspParallaxSmall("INTRANET &bull; RHENUS AUTOMOTIVE", "{$_SESSION['text']['h_kalender']} $jahr");
    ?>
    <main class="w-100 bg__white--95 flex-shrink-0" id="main">
        <div class="p-5">
            <span class="bg__info text-primary font-size-10 p-1 px-2 me-1">Kalendereintrag</span>
            <span class="bg__warning font-size-10 p-1 me-1 px-2">Logbucheintrag</span>
            <span class="bg__primary text-white font-size-10 p-1 me-1 px-2">Job 1 (Kundenänderung)</span>
            <span class="bg__danger text-white font-size-10 p-1 me-1 px-2">Service Desk</span>
            <span class="bg__success text-white font-size-10 p-1 me-1 px-2">Schulung</span>
            <span class="bg__black text-white font-size-10 p-1 me-1 px-2">Aktueller Tag</span>
        <div class="row mt-3">
            <?php
            #$jahr = 2023;
            for($i = 1; $i <= 12; $i++):
                $border = ($i < 12) ? 'border__right--dotted-gray' : '';
                $tageMonat = cal_days_in_month(CAL_GREGORIAN, $i, $jahr);
                $mc = $i-1;
            ?>
            <div class="col-1 <?= $border ?>">
                <div class="p-1">
                    <p class="oswald"><?= $_SESSION['text'][''.$_SESSION['i18n']['monate'][$mc].''] ?> <?= $jahr ?></p>
                <?php
                $period = Functions::buildMonth(''.$jahr.'-'.$i.'-00',$tageMonat);
                foreach ($period as $dt):
                    # Datum für die Datenbankabfrage
                    $sdate = $dt->format("d.m");
                    $pdate = $dt->format("Y-m-d");
                    $tag = Functions::germanTag($dt->format('l'));
                    # Zähler
                    $z = $dt->format("j");
                    # Vorgabe Tag
                    $v[$z] = ProduktionDatabase::getVorgabeTag($pdate);
                    # Ergebnis Tag
                    $e[$z] = ProduktionDatabase::getErgebnisTag($pdate);
                    $d[$z] = Functions::germanNumberNoDez($e[$z] - $v[$z]);
                    $cls[$z] = ($d[$z] < 0) ? 'text-danger font-weight-600' : '';
                    # Vorgabe Tag
                    $v[$z] = Functions::germanNumberNoDez(ProduktionDatabase::getVorgabeTag($pdate));
                    # Ergebnis Tag
                    $e[$z] = Functions::germanNumberNoDez(ProduktionDatabase::getErgebnisTag($pdate));
                    # Hintergrund Wochenende
                    $bg = ($tag == 'Sonntag' || $tag == 'Samstag') ? 'bg__blue-gray text-white' : 'bg__blue-gray--25';
                    if($pdate == DATE('Y-m-d')) $bg = 'bg__black text-white';

                    Functions::htmlOpenDiv(12,"bottom","dotted","","mb-1");
                    echo "<div class='row $bg'>";
                    echo "<div class='font-size-10 italic col-6'>";
                    echo "<p class='m-0 p-1'><b class='font-size-10'>$tag</b></p>";
                    echo "</div>";
                    echo "<div class='font-size-12 italic col-6 text-end'>";
                    echo "<p class='m-0 p-1'><b class='font-size-12'>$sdate</b></p>";
                    echo "</div>";
                    echo "</div>";
                    Functions::htmlCloseDiv();
                    # Kalendereinträge
                    $kld[$i] = KalenderDatabase::getKalenderEintrag($pdate);
                    if (!empty($kld[$i])):
                        foreach ($kld[$i] as $row):
                            $clr = ($row->event == 'Werksurlaub') ? 'font-weight-600 text-center' : '';
                            echo "<div class=\"row mb-1 pb-1 font-size-10 border__bottom--dotted-gray\">";
                            echo "<div class=\"col-12 bg__info text-primary\">";
                            echo "<div class='p-1 $clr'>$row->event</div>";
                            echo "</div>";
                            echo "</div>";
                        endforeach;
                    endif;
                    # Produktion
                    if($v[$z] > 0):
                        echo "<div class=\"row border__bottom--solid-gray_25 pb-1 mb-1 font-size-10 bg__white\">";
                        echo "<div class=\"col-4 text-center border__right--dotted-gray\">";
                        echo "<div class='p-1'>$e[$z]</div>";
                        echo "</div>";
                        echo "<div class=\"col-4 text-center border__right--dotted-gray\">";
                        echo "<div class='p-1'>$v[$z]</div>";
                        echo "</div>";
                        echo "<div class=\"col-4 text-center\">";
                        echo "<div class='p-1 $cls[$z]'>$d[$z]</div>";
                        echo "</div>";
                        echo "</div>";
                    endif;
                    # Job 1
                    $ka[$i] = KundenaenderungenDatabase::getKalenderEintrag($pdate);
                    if (!empty($ka[$i])):
                        foreach ($ka[$i] as $row):
                            echo "<div class=\"row border__bottom--solid-gray_25 pb-1 mb-1 font-size-10\">";
                            echo "<div class=\"col-12 bg__primary text-white\">";
                            echo "<div class='p-1'>$row->teilname</div>";
                            echo "</div>";
                            echo "</div>";
                        endforeach;
                    endif;
                    # Logbucheinträge
                    $lb[$i] = LogbuchDatabase::getKalenderEintrag($pdate);
                    if (!empty($lb[$i])):
                        foreach ($lb[$i] as $row):
                            echo "<div class=\"row border__bottom--solid-gray_25 pb-1 mb-1 font-size-10\">";
                            echo "<div class=\"col-12 bg-warning\">";
                            echo "<div class='p-1'>$row->titel</div>";
                            echo "</div>";
                            echo "</div>";
                        endforeach;
                    endif;
                    # Servicedesk
                    $sd[$i] = ServicedeskDatabase::getKalenderEintrag($pdate);
                    if (!empty($sd[$i])):
                        foreach ($sd[$i] as $row):
                            echo "<div class=\"row border__bottom--solid-gray_25 pb-1 mb-1 font-size-10\">";
                            echo "<div class=\"col-12 bg-danger text-white\">";
                            echo "<div class='p-1'>$row->titel</div>";
                            echo "</div>";
                            echo "</div>";
                        endforeach;
                    endif;

                endforeach;
                ?>
                </div>
            </div><!-- col -->
            <?php endfor; ?>
        </div>
        </div>
    </main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<script type="text/javascript">
    $(document).ready(function () {
        heightMainContainer();
    });
</script>
</body>
</html>