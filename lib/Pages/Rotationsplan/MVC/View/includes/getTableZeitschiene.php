<?php
/** (c) Joachim Göddel . RLMS */

use App\Functions\Functions;

# Datenbank
$db = new \App\Pages\Rotationsplan\MVC\View\functions\Functions();
# Springer
$springer = $db->getSpringerID();
?>
<table class="table table-bordered table-striped table-sm bg-white font-size-12">
    <thead class="bg__blue-gray--50">
    <tr>
        <th></th>
        <th>Name</th>
        <th class="text-center">E</th>
        <th class="text-center">G</th>
        <th class="text-center" colspan="2">Station</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach($stn AS $station):
    for($m = 1; $m <= $station->mitarbeiter; $m++):
        # Die Inhalte der Position aus der Datenbank abrufen
        $row = $db->getRowRotationsplan($station->id, $zeitschiene, $m);
        # Ausgabe der Tabellenzeile
        $db->getTrZeitschiene($station->id, $row->uid, $zeitschiene, $m, $springer, $uid);
    endfor;
    endforeach;
    ?>
    </tbody>
</table>
<p class="font-size-10 italic">E = Anzahl der Einsätze heute (alle Zeitschienen)<br>G = Anzahl der Einsätze an dieser Station insgesamt</p>
<script type="text/javascript">
    setTimeout(
        function(){
            $('.neu').fadeOut(2000);
        }, 20000);
</script>
