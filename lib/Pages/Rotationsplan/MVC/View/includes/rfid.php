<?php
/** (c) Joachim Göddel . RLMS */

# In welcher Zeitschiene bewege ich mich ?
use App\App\Container;
use App\Functions\Functions;
use App\Pages\Rotationsplan\RotationsplanDatabase;

# Aktuelle Zeitzone abrufen
$zzone = rotationsplanDatabase::getZeitschieneSchicht();

# Array löschen
$_SESSION['beginn'] = array();
$_SESSION['ende'] = array();

# Array setzen
rotationsplanDatabase::zs2array();

# Angezeigte Werte (array)
$dspzz = array();

# Ausgabe der Arbeitszeiten
if ($zzone == 1):
    $dspzz[0] = $_SESSION['beginn'][0] . " bis ". $_SESSION['ende'][0] . " Uhr";
    $dspzz[1] = $_SESSION['beginn'][1] . " bis ". $_SESSION['ende'][1] . " Uhr";
    $dspzz[2] = $_SESSION['beginn'][2] . " bis ". $_SESSION['ende'][3] . " Uhr";
    $zschiene = array(1, 2, 3);
else:
    $dspzz[0] = $_SESSION['beginn'][4] . " bis ". $_SESSION['ende'][4] . " Uhr";
    $dspzz[1] = $_SESSION['beginn'][5] . " bis ". $_SESSION['ende'][5] . " Uhr";
    $dspzz[2] = $_SESSION['beginn'][6] . " bis ". $_SESSION['ende'][7] . " Uhr";
    $zschiene = array(4, 5, 6);
endif;

# An welchen Stationen arbeitet der Mitarbeiter
foreach($zschiene AS $z):
    # Daten in Array schreiben
    $station[$z] = rotationsplanDatabase::getStationIdPerson($id, $z);
    # Details zur Station abrufen
    $stn[] = rotationsplanDatabase::getDetailsStation($station[$z]);
    # LEtzter Einsatz an dieser SStation
    $letzerEinsatz[] = rotationsplanDatabase::getLastWorkStation($id,$station[$z]);
endforeach;

# Wieviel Einsätze hat der Mitarbeiter an der Station
$anzahlEinsaetze[0] = rotationsplanDatabase::countAnzahlEinsatzStation($id,$stn[0]->id);
$anzahlEinsaetze[1] = rotationsplanDatabase::countAnzahlEinsatzStation($id,$stn[1]->id);
$anzahlEinsaetze[2] = rotationsplanDatabase::countAnzahlEinsatzStation($id,$stn[2]->id);
# Einsätze gesamt
$summeEinsatz = rotationsplanDatabase::countAnzahlEinsatz($id);

# Tabelle
$tabelle = array("","frontcorner","kuehler","motorband","akl");
$abteilung = $tabelle[$_SESSION['user']['wrk_abteilung']];

$rma = RotationsplanDatabase::getMitarbeiterDetailsStatic($id);
RotationsplanDatabase::chipLog($rma->rfid,$rma->id,$rma->vorname,htmlspecialchars($rma->name),$zzone);

?>
<div class="row mt-5">
    <?php
    for($i = 0; $i < 3; $i++):
        # Prozentual an der Station
        $prozent[$i] = ($anzahlEinsaetze[$i] > 0) ? round(($anzahlEinsaetze[$i]/$summeEinsatz)*100) : 0;
        Functions::htmlOpenDiv(4, "right", "dotted", "", "", "", "px-3");
        ?>
        <h3 class="oswald text-primary font-weight-100 py-2 my-3 border__bottom--dotted-gray_25">
            Zeitschiene <?= $i+1 ?>: <small class="font-size-16 float-end pt-1">
                <?= $dspzz[$i] ?>
            </small>
        </h3>
        <div class="rplan text-center">
            <div class="oswald font-size-40 text-primary"><?= $stn[$i]->station ?></div>
            <div class="oswald font-size-20 font-weight-100 text-primary"><?= $stn[$i]->bezeichnung ?></div>
            <div class="row border__bottom--dotted-gray border__top--dotted-gray py-3 my-3">
                <?php
                Functions::htmlOpenDiv(3, "right", "dotted", "", "", "", "pe-3");
                echo "<span class=\"oswald font-size-30 font-weight-100 text-primary\">$summeEinsatz</span>";
                Functions::htmlCloseDiv();
                Functions::htmlOpenDiv(3, "right", "dotted", "", "", "", "px-3");
                echo "<span class=\"oswald font-size-30 font-weight-100 text-primary\">$anzahlEinsaetze[$i]</span>";
                Functions::htmlCloseDiv();
                Functions::htmlOpenDiv(3, "right", "dotted", "", "", "", "pe-3");
                echo "<span class=\"oswald font-size-30 font-weight-100 text-primary\">$prozent[$i]%</span>";
                Functions::htmlCloseDiv();
                Functions::htmlOpenDiv(3, "", "", "", "", "", "ps-3");
                if(!empty($stn[$i]->qps)): $qps = rotationsplanDatabase::getQPS($stn[$i]->qps);
                    echo "<span class=\"oswald font-size-30 font-weight-100 text-primary pointer\" onclick=\"window.open('". Functions::getBaseUrl() ."lib/pages/Rotationsplan/MVC/View/files/$abteilung/$qps->datei');\">";
                    echo "<i class=\"fa fa-file-pdf me-2\"></i>QPS";
                    echo "</span>";
                else:
                    echo "<span class=\"oswald font-size-30 font-weight-100 text-primary\">-</span>";
                endif;
                Functions::htmlCloseDiv();
                ?>
            </div><!-- row -->
            <div class="">
                <?php if($stn[$i]->id != 104): ?>
                    <span class="oswald fon-weight-100 text-primary">Ihr letzter Einsatz an dieser Station war am <b><?= $letzerEinsatz[$i] ?></b></span>
                <?php endif; ?>
            </div>
        </div><!-- rplan -->
        <?php
        Functions::htmlCloseDiv();
    endfor;
    ?>
</div><!-- row -->
<div class="chart-container p-1 pb-3 mt-5" style="position: relative; height:350px;">
    <canvas id="chart"></canvas>
</div>

<?php
if(empty($start)) $start = rotationsplanDatabase::getDatum('ASC');
if(empty($ende)) $ende = rotationsplanDatabase::getDatum('DESC');
?>
<script type="text/javascript">
    ctx = $('#chart');
    options = {
        responsive: true,
        maintainAspectRatio: false,
        title: {
            display: true,
            position: "top",
            text: "Bar Graph",
            fontSize: 18,
            fontColor: "#111"
        },
        legend: {
            display: true,
            position: "bottom",
            labels: {
                fontColor: "#333",
                fontSize: 16
            }
        },
        scales: {
            yAxes: [{
                ticks: {
                    min: 0
                }
            }]
        }
    };
    // Data
    data = {
        <?php
        $l = '';
        $q = rotationsplanDatabase::getStationAbteilungRand();
        # Farben
        $clr = array("0,70,155","250,187,0","0,124,181");
        # Labels zusammenstellen
        foreach($q AS $c): $l.= "'".$c->station."',"; endforeach;
        $labels = substr($l,0,-1);
        ?>
        labels: [<?= $labels ?>],
        datasets: [
            <?php
            $x = 0; $e = '';
            foreach($q AS $c):
                $e.= rotationsplanDatabase::countEinsatzStation($c->id,$id,$start,$ende).",";
            endforeach;
            $data = substr($e,0,-1);
            ?>
            {
                label: 'Einsätze',
                data: [<?= $data ?>],
                backgroundColor: ["rgba(<?= $clr[$x] ?>,0.3)"],
                borderColor: ["rgba(<?= $clr[$x] ?>,1)"],
                borderWidth: 1
            },
            <?php
            $x++;
            ?>
        ]
    };
    // Chart ausgeben
    var chart = new Chart(ctx, {
        type: "bar",
        data: data,
        options: options
    });
</script>
