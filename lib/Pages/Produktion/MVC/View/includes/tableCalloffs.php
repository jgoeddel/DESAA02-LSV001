<?php
/** (c) Joachim Göddel . RLMS */

?>
<div style="position: absolute; top: 142px; right: 20px;">
    <table class="table table-bordered table-lg table-striped mt-4 font-size-14 bg-white">
        <thead class="bg-light-lines-primary text-white">
        <tr>
            <th>Variante</th>
            <th class="text-end">%</th>
            <th class="text-end">Anzahl</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $bg = 'bg-light-lines-danger';
        $bg = '';
        foreach ($query as $row):
            $Description1 = str_replace(" ...", "", $row->Description1);
            $prznt = $row->summe * 100 / $i;
            $prznt = number_format($prznt, 1, ',', '.');
            $Materialnumber = trim($row->Materialnumber);
            //if($prznt < 10) $bg = 'bg_orange-15';
            //if($prznt < 3) $bg = 'bg_green-15';
            ?>
            <tr class="<?= $bg ?>">
                <td><?= $Description1 ?></td>
                <td class="text-end"><?= $prznt ?></td>
                <td class="text-end"><?= $row->summe ?></td>
            </tr>
        <?php
        endforeach;
        ?>
        </tbody>
    </table>
</div>