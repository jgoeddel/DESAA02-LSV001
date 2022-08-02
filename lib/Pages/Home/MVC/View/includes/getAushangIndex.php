<?php
/** (c) Joachim Göddel . RLMS */

use App\Pages\Administration\AdministrationDatabase;

?>
<h3 class="text-center font-weight-300 py-3 m-0 border__bottom--dotted-gray">
    <?= $_SESSION['text']['h_aushang'] ?>
</h3>
<div class="row py-2 border__bottom--dotted-gray">
    <?php
    # Zähler
    $i = 0;
    foreach(AdministrationDatabase::getAushangIndex() AS $row):
        $border = ($i == 6) ? '' : 'border__right--dotted-gray';
        ?>
        <div class="col <?= $border ?>">
            <div class="pointer text-center px-3" onclick="">
                <div class="font-size-10 text-muted">
                    <?= $row->bereich ?>
                </div>
                <h4 class="oswald font-size-18 text-primary font-weight-300 m-0 p-0">
                    <?= $row->titel ?>
                </h4>
                <div class="font-size-10 text-muted m-0 p-0">
                    <?= $row->anzeige ?>
                </div>
            </div><!-- pointer -->
        </div><!-- col -->
        <?php
        $i++;
    endforeach;
    ?>
</div><!-- row -->
