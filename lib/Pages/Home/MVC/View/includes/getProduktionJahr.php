<?php
/** (c) Joachim Göddel . RLMS */

?>
<div class="">
    <div class="row py-2">
        <?php
        $m = DATE('n');
        $y = DATE('Y');
        for ($i = 0; $i <= 11; $i++):
            # Border
            $border = ($i != 11) ? 'border__right--dotted-gray' : '';
            # Jahreszahl
            $yz = substr($y, -2);
            # Zeichen
            $sign = match ($e[$i]) {
                $e[$i] > 0 => '<i class="fas fa-caret-up text-success"></i>',
                $e[$i] < 0 => '<i class="fas fa-caret-down text-danger"></i>',
                default => ''
            };
            # Anzeige Monat
            $ma = $monat[$i] - 1;
            ?>
            <!-- Beginn Monatsblock -->
            <div class="col-3 col-md-2 col-xl-1 flex-wrap-reverse font-size-10 <?= $border ?>">
                <div class="p-2">
                    <b class="text-primary"><?= $_SESSION['text'][$_SESSION['i18n']['monate'][$ma]] ?> <?= $yz ?> <?= $sign ?></b>
                    <br>
                    <div class="row">
                        <div class="col-5"><?= $_SESSION['text']['h_tage'] ?>:</div>
                        <div class="col-7 text-end"><?= $prodTage[$i] ?></div>
                    </div><!-- /row -->
                    <div class="row">
                        <div class="col-5"><?= $_SESSION['text']['h_soll'] ?>:</div>
                        <div class="col-7 text-end"><?= $v[$i] ?></div>
                    </div><!-- /row -->
                    <div class="row">
                        <div class="col-5"><?= $_SESSION['text']['h_ist'] ?>:</div>
                        <div class="col-7 text-end"><?= $fzg[$i] ?></div>
                    </div><!-- /row -->
                    <div class="row">
                        <div class="col-5"><?= $_SESSION['text']['h_differenz'] ?>.:</div>
                        <div class="col-7 text-end font-weight-600">
                            <?php
                            $bdg = ($e[$i] > 0) ? 'success' : 'danger';
                            ?>
                            <span class="badge badge-<?= $bdg ?>"><?= $e[$i] ?></span>
                        </div>
                    </div><!-- /row -->
                </div>
            </div>
            <!-- Ende Monatsblock -->
            <?php
            # Vorheriger Monat
            --$m;
            # Jahresgrenze
            if ($m == 0):
                $y--;
                $m = 12;
            endif;
        endfor;
        ?>
    </div><!-- row -->
</div>
