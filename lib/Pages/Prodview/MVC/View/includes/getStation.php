<?php
/** (c) Joachim Göddel . RLMS */

use App\Pages\Prodview\ProdviewDatabase;
# Type
ProdviewDatabase::setIdType(1);

?>
    <h3 class="font-weight-300 border__bottom--dotted-gray mb-3 pb-3">
        Station <?= $station->Id ?>. <?= $station->StationName ?> <?= $station->Description ?>
    </h3>
    <table class="table table-striped">
        <thead class="font-weight-300 font-size-11">
        <tr>
            <th>Id</th>
            <th>Citycode</th>
            <th><?= $_SESSION['parameter']['Value1'] ?></th>
            <th><?= $_SESSION['parameter']['Value2'] ?></th>
            <th>Station</th>
            <th>Status</th>
            <th>Timestamp</th>
        </tr>
        </thead>
        <tbody class="font-size-12">
        <?php
        foreach ($tb as $row):
            # Datum umschreiben
            $dt = new DateTime($row->TimeStamp);
            $datum = $dt->format('d.m.Y H:i:s');
            $class = ($row->IdStatus == 3 || $row->IdStatus == 2) ? 'text-danger font-weight-600' : '';
            ?>
            <tr>
                <td class="<?= $class ?>"><?= $row->Id ?></td>
                <td class="<?= $class ?>"><?= $row->CityCode ?></td>
                <td class="<?= $class ?>"><?= $row->Value1 ?></td>
                <td class="<?= $class ?>"><?= $row->Value2 ?></td>
                <td class="<?= $class ?>"><?= $row->sid ?>. <?= $row->Description ?></td>
                <td class="<?= $class ?>"><?= $row->IdStatus ?></td>
                <td class="<?= $class ?>"><?= $datum ?></td>
            </tr>
        <?php
        endforeach;
        ?>
        </tbody>
    </table>
<?php
