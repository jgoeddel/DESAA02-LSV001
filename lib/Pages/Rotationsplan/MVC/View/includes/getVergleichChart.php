<?php
/** (c) Joachim Göddel . RLMS */

use App\Pages\Rotationsplan\MVC\View\functions\Functions;
use App\Pages\Rotationsplan\RotationsplanDatabase;

# Datenbank
$db = new Functions();

# Anzahl der zu vergleichenden Mitarbeiter
$anz = count($u);
# Wenn kein Datum gesetzt ist die Werte setzen
if(empty($start)) $start = RotationsplanDatabase::getPlanDates('ASC');
if(empty($ende)) $ende = RotationsplanDatabase::getPlanDates('DESC');
?>
<script type="text/javascript">
    const ctx = $('#chart');
    const options = {
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
    // Chart Data
    const data = {
        <?php
        $l = '';
        // Stationen
        foreach ($q as $c): $l .= "'".$c->station."',"; endforeach;
        $l = substr($l,0,-1);
        // Farben
        $clr = array("0,70,155","250,187,0","0,124,181");
        ?>
        labels: [<?= $l ?>],
        datasets: [
            <?php
            $x = 0;
            foreach($u AS $usr):
                $d = '';
                foreach($q AS $c):
                    $d .= $db->getAnzahlEinsatzZeitraum($u[$x]->id, $c->id, $start, $ende).',';
                endforeach;
                $d = substr($d, 0, -1);
            ?>
            {
                label: "<?= $u[$x]->name ?>, <?= $u[$x]->vorname ?>",
                data: [<?= $d ?>],
                backgroundColor: "rgba(<?= $clr[$x] ?>,0.7)",
                borderColor: "rgba(<?= $clr[$x] ?>,1)",
                borderWidth: 1
            },
            <?php
            $x++;
            endforeach;
            ?>
        ]
    };
    //create Chart class object
    var chart = new Chart(ctx, {
        type: "bar",
        data: data,
        options: options
    });
</script>
