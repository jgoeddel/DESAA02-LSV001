<?php
/** (c) Joachim Göddel . RLMS */

$dtm = DATE('d.m.Y, H:i:s');
?>
<thead class="bg__blue-gray--12">
<tr>
    <th>Asset</th>
    <th class="text-center">Online seit</th>
    <th>IP Adresse</th>
    <th>Bezeichnung<span class="float-end font-size-10 text-warning"><?= $dtm ?></span></th>
</tr>
</thead>