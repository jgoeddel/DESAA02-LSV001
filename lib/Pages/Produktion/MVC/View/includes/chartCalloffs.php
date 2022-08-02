<?php
/** (c) Joachim Göddel . RLMS */
$l = ''; $d = '';
foreach ($query as $row):
    $Materialnumber = trim($row->Description1);
    $Materialnumber = str_replace("  ","",$Materialnumber);
    $Materialnumber = str_replace("...","",$Materialnumber);
    $l .= "'".$Materialnumber."',";
endforeach;
$l = substr($l,0,-1);
foreach ($query as $row):
    $d .= $row->summe.",";
endforeach;
$d = substr($d,0,-1);
?>
<canvas id="chart"></canvas>
<script type="text/javascript">

    var ctx = $('#chart');
    var options = {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
            display: false,
            position: "bottom",
            labels: {
                fontColor: "#333",
                fontSize: 12
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
    //bar chart data
    var data = {
        labels: [<?= $l ?>],
        <?php
        // Farben
        $clr = array("0,70,155","250,187,0","0,124,181");
        ?>
        datasets: [
            <?php $x = 0; ?>
            {
                label: '',
                data: [<?= $d ?>],
                backgroundColor: [ "rgba(<?= $clr[$x] ?>,0.3)" ],
                borderColor: [ "rgba(<?= $clr[$x] ?>,1)" ],
                borderWidth: 1
            },
            <?php $x++; ?>
        ]
    };
    //create Chart class object
    var chart = new Chart(ctx, {
        type: "bar",
        data: data,
        options: options
    });
</script>
