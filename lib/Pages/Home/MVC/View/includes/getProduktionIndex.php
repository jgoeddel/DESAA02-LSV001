<?php

use App\Functions\Functions;

$lastRotSQL = Functions::getSQLNumber(
    $_SESSION['mkspts']['server'],
    $_SESSION['mkspts']['database'],
    $_SESSION['mkspts']['uid'],
    $_SESSION['mkspts']['pwd'],
    'Aktuell'
);
?>
<div class="row border__bottom--dotted-gray">
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xxl-2 border__right--dotted-gray my-2">
        <div class="produktionsdaten text-center">
            <p>
                <small class="text-muted oswald"><?= $_SESSION['text']['h_aktuelles_jahr'] ?></small><br>
                <span class="oswald font-size-40"><?= Functions::germanNumberNoDez($summeJahr); ?></span><br>
                <small class="text-muted"><?= Functions::getDiffEV($summeJahr, $summeVorgabe); ?></small>
            </p>
        </div><!-- produktionsdaten -->
    </div><!-- col -->
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xxl-2 border__right--dotted-gray my-2">
        <div class="produktionsdaten text-center">
            <p>
                <small class="text-muted oswald"><?= $_SESSION['text']['h_ergebnis'] ?></small><br>
                <span class="oswald font-size-40"><?= $ergebnis ?></span><br>
                <small class="text-muted"><?= Functions::getDiffEV($ergebnis, $vorgabe); ?></small>
            </p>
        </div><!-- produktionsdaten -->
    </div><!-- col -->
    <div class="col-12 col-sm-6 col-md-6 col-lg-8 col-xxl-8">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xxl-2 border__right--dotted-gray my-2">
                <div class="produktionsdaten text-center">
                    <p>
                        <small class="oswald text-muted"><?= $_SESSION['text']['h_aktuelle_rot'] ?></small><br>
                        <span class="oswald font-size-40">
                                    <?= $lastRotSQL->ExtraSequence ?>
                                </span>
                    </p>
                </div><!-- produktionsdaten -->
            </div><!-- col -->
            <?php
            for ($a = 0; $a < count($_SESSION['page']['linien']); $a++) {
                // Umwandeln für i18n
                $linie = strtolower($_SESSION['page']['linien'][$a]);
                // Datenbank
                $srv = 'mkspts';
                if ($_SESSION['page']['linien'][$a] == 'Frontcorner') {
                    $srv = 'corner';
                }
                if ($_SESSION['page']['linien'][$a] == 'AKL') {
                    $srv = 'akl';
                }
                ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xxl-2 border__right--dotted-gray my-2">
                    <div class="produktionsdaten text-center">
                        <p>
                            <small class="oswald text-muted"><?= $_SESSION['text']['h_' . $linie . ''] ?></small><br>
                            <span class="oswald font-size-40">
                                    <?php
                                    $zahl[$a] = Functions::getSQLNumber(
                                        $_SESSION['' . $srv . '']['server'],
                                        $_SESSION['' . $srv . '']['database'],
                                        $_SESSION['' . $srv . '']['uid'],
                                        $_SESSION['' . $srv . '']['pwd'],
                                        '' . $_SESSION['page']['linien'][$a] . '');
                                    echo $zahl[$a]->ExtraSequence;
                                    ?>
                                </span><br>
                            <small class="badge badge-primary p-2 mt-2 font-size-12"><?= Functions::getProduktionDifNeu(
                                    $_SESSION['' . $srv . '']['server'],
                                    $_SESSION['' . $srv . '']['database'],
                                    $_SESSION['' . $srv . '']['uid'],
                                    $_SESSION['' . $srv . '']['pwd'],
                                    '' . $_SESSION['page']['linien'][$a] . '',
                                    $lastRotSQL,
                                    $ergebnis
                                ); ?></small>
                        </p>
                    </div><!-- produktionsdaten -->
                </div><!-- col-12 -->
                <?php
            }
            ?>
        </div><!-- row -->
    </div><!-- row -->