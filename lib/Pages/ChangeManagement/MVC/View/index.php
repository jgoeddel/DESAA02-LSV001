<?php
/** (c) Joachim Göddel . RLMS */
# Klassen
use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;
use App\Pages\Home\IndexDatabase;

$_SESSION['seite']['id'] = 36;
$_SESSION['seite']['name'] = 'index';
$subid = 0;
$n_suche = '';

# Seite lesen / schreiben / admin
$pb = Functions::getBerechtigung(36);

# Parameter setzen
$seitelesen = $pb[0];
$seiteschreiben = $pb[1];
$seiteadmin = $pb[2];

// Parameter für die Anzeige von Bearbeitungsfunktionen
($seiteschreiben == 1) ? $dspedit = '' : $dspedit = 'dspnone';
?>
<!doctype html>
<html lang="de" class="h-100">
<head>
    <?php
    # Basis-Head Elemente einbinden
    Functions::getHeadBase();
    ?>
    <title><?= $_SESSION['page']['version'] ?></title>
</head>
<body class="d-flex flex-column h-100 kaenderung" id="body">
    <div class="fixed-top z3">
        <?php
        # Basis Header einbinden
        Functions::getHeaderBasePage(1);
        Functions::dspNavigation($dspedit,'index','',1);
        ?>
    </div>
    <?php
    Functions::dspParallaxMedium("INTRANET &bull; RHENUS AUTOMOTIVE", "{$_SESSION['text']['h_offeneAnforderungen']}");
    ?>
    <main class="w-100 bg__white--95 flex-shrink-0" id="main">
        <div class="container-fluid p-0">
            <?php include_once "includes/inc.sub.nav.php"; ?>
        </div><!-- fluid -->
        <div class="container-fluid px-5">
            <p class="border__bottom--dotted-gray py-3 mb-3"><?= $_SESSION['text']['t_offeneAnforderungen'] ?></p>
            <!-- Technische Änderungen -->
            <?php foreach($cm AS $row):
                # Detaillierte Informationen zu dem jeweiligen Eintrag
                $info = ChangeManagementDatabase::getInfo($row->id);
                # Detaillierte Informationen zu dem betroffenen Standort
                $loc = ChangeManagementDatabase::getLocationInfo($row->location);
                # Zugriff des Users
                $view = ChangeManagementDatabase::getCMViewDate($row->id);
                # Ausgabe Badge MEU wenn noch kein Eintrag in der Datenbank ist
                $neu = (!isset($view)) ? '<span class="badge badge-primary">'.$_SESSION['text']['neu'].'</span>' : '';
                # ID verschlüsseln
                $id = Functions::encrypt($row->id);
                # Status
                $ex = ChangeManagementDatabase::getCMParts($row->id,'evaluation',1,1);
                $tx = ChangeManagementDatabase::getCMParts($row->id,'tracking',1,2);
                $g = $ex[1] + $tx[1];
                $h = $ex[2] + $tx[2];
                $i = $ex[3] + $tx[3];

                # Berechtigung vorhanden ?
                if(IndexDatabase::checkRechteCitycode($loc->citycode) > 0) {
                    # Basisinformationen
                    echo "<div class='pointer filter$loc->citycode all' onclick='top.location.href=\"/changeManagement/details?id=$id&loc=$loc->citycode\"'>";
                    echo "<h3>";
                    ChangeManagementDatabase::dspCMArt($row->id);
                    echo "$row->nr . $info->part_description ";
                    $wrktime = ChangeManagementDatabase::getWrkTime($row->id);
                    echo $wrktime[1];
                    echo "<span class='float-end'>$neu</span>";
                    echo "</h3>";
                    echo "<p>$info->change_description</p>";
                    # Bereich mit den weiteren Informationen
                    echo "<div class='row m-0 border__top--dotted-gray border__bottom--dotted-gray py-2 my-2'>";
                    echo "<div class='col-2 col-lg-1 col-xxl-1 srow text-center border__right--dotted-gray'>";
                    echo "<div class='font-size-12 italic py-1 text-muted italic'>" . $_SESSION['text']['h_overStatus'] . "</div>";
                    echo "<div class=''>" . ChangeManagementDatabase::dspCMOverStatus($g, $h, $i) . "</div>";
                    echo "</div>"; # col-2
                    echo "<div class='col-5 col-lg-3 col-xxl-2 srow text-center border__right--dotted-gray'>";
                    echo "<div class='font-size-12 italic py-1 text-muted italic'>" . $_SESSION['text']['h_location'] . "</div>";
                    echo "<div class=''>$loc->citycode &bull; $loc->location</div>";
                    echo "</div>"; # col-5
                    echo "<div class='col-5 col-lg-3 col-xxl srow text-center border__right--dotted-gray'>";
                    echo "<div class='font-size-12 italic py-1 text-muted italic'>" . $_SESSION['text']['h_verantwortlich'] . "</div>";
                    echo "<div class=''>$row->name</div>";
                    echo "</div>"; # col-5
                    echo "<div class='col-6 col-lg-2 col-xxl-1 srow text-center border__right--dotted-gray'>";
                    echo "<div class='font-size-12 italic py-1 text-muted italic'>" . $_SESSION['text']['h_inputDate'] . "</div>";
                    echo "<div class=''>$row->datum</div>";
                    echo "</div>"; # col-6
                    echo "<div class='col-6 col-lg-2 col-xxl srow text-center border__right--dotted-gray'>";
                    echo "<div class='font-size-12 italic py-1 text-muted italic'>" . $_SESSION['text']['h_evaluation'] . "</div>";
                    echo "<div class='p-1'>";
                    echo "<div class='row'>";
                    if (ChangeManagementDatabase::checkEvaluation($row->id) == 1):
                        echo "<div class='col text-center'>";
                        echo "" . ChangeManagementDatabase::dspCMOverStatus($ex[1], $ex[2], $ex[3]) . "";
                        echo "</div>"; # col
                        $d = ChangeManagementDatabase::pla2evaluation($row->id);
                        foreach ($d as $eva):
                            echo "<div class='col text-center'>";
                            echo "" . ChangeManagementDatabase::dspEvaluationStatus($row->id, $eva->bereich, 1) . "";
                            echo "</div>";
                        endforeach;
                    endif;
                    echo "</div>";
                    echo "</div>"; # p-1
                    echo "</div>"; # col-6
                    echo "<div class='col-6 col-lg-2 col-xxl srow text-center'>";
                    echo "<div class='font-size-12 italic py-1 text-muted italic'>" . $_SESSION['text']['h_tracking'] . "</div>";
                    echo "<div class='p-1'>";
                    echo "<div class='row'>";
                    if (ChangeManagementDatabase::checkTracking($row->id) == 1):
                        echo "<div class='col text-center'>";
                        echo "" . ChangeManagementDatabase::dspCMOverStatus($tx[1], $tx[2], $tx[3]) . "";
                        echo "</div>"; # col
                        $d = ChangeManagementDatabase::imp2tracking($row->id);
                        foreach ($d as $eva):
                            echo "<div class='col text-center'>";
                            echo "" . ChangeManagementDatabase::dspEvaluationStatus($row->id, $eva->bereich, 2) . "";
                            echo "</div>";
                        endforeach;
                    endif;
                    echo "</div>";
                    echo "</div>"; # p-1
                    echo "</div>"; # col-6
                    echo "</div>"; # row
                    echo "<div class='text-center text-primary font-size-30'>&bull; &bull; &bull;</div>";
                    echo "</div>"; # Basisinformationen
                }

            endforeach;
            ?>
            <!-- Ende technische Änderungen -->
        </div>
    </main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<script type="text/javascript">
    $(document).ready(function () {
        heightMainContainer();
    });
</script>
</body>
</html>