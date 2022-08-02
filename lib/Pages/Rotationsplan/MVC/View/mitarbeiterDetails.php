<?php
# Seitenparameter
use App\App\Container;
use App\Formular\Formular;
use App\Functions\Functions;
use App\Pages\Rotationsplan\RotationsplanDatabase;

$_SESSION['seite']['id'] = $ebene;
$subid = 10;
$n_suche = '';

# Seite lesen / schreiben / admin
$pb = Functions::getBerechtigung($ebene);

# Parameter setzen
$seitelesen = $pb[0];
$seiteschreiben = $pb[1];
$seiteadmin = $pb[2];

# Keine Berechtigung, dann zurück auf die Startseite
if (!isset($seitelesen) || $seitelesen != 1):
    header(header: 'Location: /');
endif;

# Möglichkeit zum Ändern setzen
$dspedit = ($seiteschreiben == 1) ? '' : 'dspnone';

# Datenbank
$db = new \App\Pages\Rotationsplan\MVC\View\functions\Functions();


# Datum
$rpldate = $db->getNewDate();
# Logfile schreiben
# Functions::logfile('Rotationsplan', '', '', 'Seite aufgerufen');
$rfid = ($ma->rfid > 0) ? '<i class="fa fa-check-circle text-success ms-2"></i>' : '';
$pw = ($ma->password > 0) ? '<i class="fa fa-check-circle text-success ms-2"></i>' : '';
# Funktionen
$funktion = RotationsplanDatabase::getFunktionPersonal($ma->funktion);
# Einsätze
RotationsplanDatabase::getSumEinsatzMa($ma->id);
$wrkabteilung = RotationsplanDatabase::showWrkParameter();
?>
<!doctype html>
<html lang="de" class="h-100">
<head>
    <head>
        <?php
        # Basis-Head Elemente einbinden
        Functions::getHeadBase();
        ?>
        <title><?= $_SESSION['page']['version'] ?></title>
    </head>
</head>
<body class="d-flex flex-column h-100 rotationsplan" id="body">
<div class="fixed-top z3">
    <?php
    # Basis Header einbinden
    Functions::getHeaderBasePage(1);
    Functions::dspNavigation($dspedit, 'index', '', 1);
    ?>
</div>
<?php
Functions::dspParallaxSmall("RHENUS LMS GmbH &bull; $wrkabteilung", $_SESSION['text']['h_rotationsplan']);
?>
<main class="bg__white w-100" id="main">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <div class="container-fluid">
        <div class="p-3">
            <div class="row pb-5">
                <?php
                Functions::htmlOpenDiv(3, "right", "dotted", "", "", "", "pe-3");
                ?>
                <form class="needs-validation" method="post" id="pw" autocomplete="off">
                    <h3 class="oswald text-black font-weight-100 py-3 border__bottom--dotted-gray_50">
                        <b><?= $ma->name ?></b>, <?= $ma->vorname ?>
                        <span class="badge badge-black font-weight-300 float-end"><?= $ma->id ?></span>
                        <br>
                        <small class="text-warning font-size-14">
                            <?php
                            if ($funktion != 0): echo $funktion . " &bull; "; endif;
                            ?>
                            <?= RotationsplanDatabase::getAbteilungRotationsplan($ma->abteilung) ?> &bull;
                            Schicht <?= $ma->schicht ?>
                        </small>
                    </h3>
                    <?php
                    if ($ma->status != 0):
                    Functions::alert("Tragen Sie bitte das gewünschte Passwort in das dafür vorgesehene Feld ein. Um Ihre RFID zu
                        speichern klicken Sie bitte in das Feld mit der RFID und scannen Sie anschließend Ihren Chip.
                        Wenn Sie ihr Passwort und die RFID eingetragen haben klicken Sie bitte auf \"Daten speichern\".");
                    ?>
                    <div class="row pb-3 mb-3 border__bottom--dotted-gray_50 p-0 m-0">
                        <?php
                        Functions::htmlOpenDiv(6, "right", "dotted", "", "", "", "px-3");
                        Formular::input("hidden","uid","$ma->id","","");
                        Formular::labelInvisibleInput("RFID $rfid", "password", "rfid", "", "", "", "RFID?");
                        Functions::htmlCloseDiv();
                        Functions::htmlOpenDiv(6, "", "", "", "", "", "px-3");
                        Formular::labelInvisibleInput("Passwort $pw", "password", "password", "", "", "", "Ihr Passwort?");
                        Functions::htmlCloseDiv();
                        ?>
                    </div><!-- row -->
                    <div class="row pb-3 mb-3 border__bottom--dotted-gray_50 p-0 m-0">
                        <?php
                        Functions::htmlOpenDiv(4, "right", "dotted", "text-center", "", "", "px-3");
                        ?>
                        <span class="font-size-10 text-muted italic">Qualifikationen</span><br>
                        <span class="font-size-14 oswald"><?= $db->getAnzahlQualiMa($ma->id) ?></span>
                        <?php
                        Functions::htmlCloseDiv();
                        Functions::htmlOpenDiv(4, "right", "dotted", "text-center", "", "", "px-3");
                        ?>
                        <span class="font-size-10 text-muted italic">Anwesenheit</span><br>
                        <span class="font-size-14 oswald"><?= $db->getSummeAnwesend($ma->id) ?></span>
                        <?php
                        Functions::htmlCloseDiv();
                        Functions::htmlOpenDiv(4, "", "", "text-center", "", "", "px-3");
                        ?>
                        <span class="font-size-10 text-muted italic">Einsätze</span><br>
                        <span class="font-size-14 oswald"><?= $db->getAnzahlEinsatzGesamt($ma->id) ?></span>
                        <?php
                        Functions::htmlCloseDiv();
                        ?>
                    </div><!-- row -->
                    <div class="text-end">
                        <?php
                        Formular::submit("submit","Änderungen speichern","btn btn-primary oswald font-weight-300");
                        ?>
                    </div>
                </form>
            <?php
            if ($ma->status != 0):
                ?>
                <h3 class="oswald text-primary font-weight-100 py-3 border__bottom--dotted-gray_50 mt-3">
                    Mitarbeiter löschen
                    <span class="float-end">
                                <i class="fa fa-trash text-danger pointer"
                                   onclick="$('#m<?= $ma->id ?>').toggle(800)"></i>
                            </span>
                </h3>
                <div class="row p-0 m-0 bg-light-lines-danger border__bottom--dotted-gray_50 font-size-10 pointer dspnone"
                     id="m<?= $ma->id ?>" onclick="deleteMa(<?= $ma->id ?>)">
                    <div class="col-12 text-center text-white italic">
                        <i class="fa fa-caret-up me-3"></i>
                        Klicken Sie bitte hier um den Mitarbeiter zu löschen
                        <i class="fa fa-caret-up ms-3"></i>
                    </div>
                </div>
                <?php
                Functions::alert("Wenn Sie einen Mitarbeiter löschen bleibt dieser weiter in der Datenbank erhalten. Er wird nur deaktiviert und kann in weiteren Plänen nicht mehr eingesetzt werden. Jedoch ist es Ihnen weiterhin möglich die Historie des Mitarbeiters aufzurufen.");
                ?>
            <?php
            endif;
            endif;
            Functions::htmlCloseDiv();
            if ($ma->status != 0):
                Functions::htmlOpenDiv(3, "right", "dotted", "", "", "", "px-3");
                ?>
                <h3 class="oswald font-weight-100 py-3 border__bottom--dotted-gray_50">
                    Qualifikationen (Produktion)
                </h3>
                <?php
                Functions::alert("Über das nachfolgende Auswahlfeld können Sie eine neue Qualifikation eintragen. Darunter finden
                    Sie alle bereits erworbenen Qualifikationen. Durch einen Klick auf die entsprechende Qualifikation
                    haben Sie die Möglichkeit, diese wieder zu löschen.");
                ?>
                <?php
                Functions::htmlOpenDiv(12, "bottom", "dotted", "", "", "pb-3", "");
                ?>
                <select class="no-border font-size-12 w-100" name="stationen"
                        onchange="showFormularQualiMa(this.value,<?= $ma->id ?>)">
                    <option value="">Bitte wählen Sie eine Station</option>
                    <?php
                    $stnabt = $db->getStationAbteilung();
                    foreach ($stnabt as $stn):
                        if ($db->getQualiMaStation($ma->id, $stn->id) === false):
                            ?>
                            <option value="<?= $stn->id ?>"><?= $stn->station ?>
                                &bull; <?= $stn->bezeichnung ?></option>
                        <?php
                        endif;
                    endforeach;
                    ?>
                </select>
                <div id="formQuali">

                </div>
                <?php
                Functions::htmlCloseDiv();
                foreach ($stnabt as $stn):
                    $q = $db->getQualiMaStationDetails($ma->id, $stn->id);
                    $badge = ($stn->ergo == 'danger') ? 'danger' : 'black';
                    if ($q !== false && $q->status != 1 && $q->status != 9):
                        $lines = '';
                        $lines = match ($q->status) {
                            NULL => '',
                            1 => 'bg-light-lines-warning-2',
                            3 => 'bg-light-lines-info',
                            5 => 'bg-light-lines-danger-2',
                        }
                        ?>
                        <?php
                        if ($q->status == 3):
                            ?>
                            <div class="row p-0 m-0 bg-light-lines-primary border__bottom--dotted-gray_50 font-size-10 pointer"
                                 id="s<?= $stn->id ?>" onclick="setQualiMa(<?= $stn->id ?>, <?= $ma->id ?>)">
                                <div class="col-12 text-center text-white italic">
                                    <i class="fa fa-caret-down me-3"></i>
                                    Klicken Sie bitte hier um die Qualifikation zu erwerben
                                    <i class="fa fa-caret-down ms-3"></i>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="border__bottom--dotted-gray_50 py-2 font-size-12 divhover ps-2 <?= $lines ?>"
                             onclick="dspDeleteQualiMa(<?= $stn->id ?>)" id="q<?= $stn->id ?>">
                            <span class="badge badge-<?= $badge ?> me-2"><?= $stn->id ?></span><?= $stn->station ?>
                            &bull; <?= $stn->bezeichnung ?>
                            <?php if ($q->status != 3): ?>
                                <br>
                                <small class="text-warning italic font-size-10">Erworben am <?= $q->tag ?>
                                    &bull; <?= $q->user ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="row p-0 m-0 bg-light-lines-danger border__bottom--dotted-gray_50 font-size-10 pointer dspnone"
                             id="d<?= $stn->id ?>" onclick="deleteQualiMa(<?= $stn->id ?>, <?= $ma->id ?>)">
                            <div class="col-12 text-center text-white italic">
                                <i class="fa fa-caret-up me-3"></i>
                                Klicken Sie bitte hier umd die Qualifikation zu löschen
                                <i class="fa fa-caret-up ms-3"></i>
                            </div>
                        </div>
                    <?php
                    endif;
                endforeach;
                Functions::htmlCloseDiv();
                Functions::htmlOpenDiv(3, "right", "dotted", "", "", "", "px-3");
                ?>
                <form id="handicap" method="post" class="needs-validation">
                    <?php
                    formular::input("hidden", "id", "$ma->id", "", "");
                    formular::input("hidden", "uid", "$ma->id", "", "");
                    formular::input("hidden", "id", "$id", "", "");
                    ?>
                    <h3 class="oswald font-weight-100 py-3 border__bottom--dotted-gray_50">
                        Handicap eintragen
                    </h3>
                    <?php
                    Functions::alert("Bitte wählen Sie zuerst eine Station aus. Tragen Sie dann das Anfangs- und Enddatum der
                            Einschränkung ein. Bei einer Dauerhaften oder zeitlich erst einmal nicht befristeten
                            Einschränkung lassen Sie das Enddatum bitte leer.");
                    ?>
                    <?php
                    Functions::htmlOpenDiv(12, "bottom", "dotted", "", "", "pb-3", "");
                    ?>
                    <select class="no-border font-size-12 w-100" name="station" required>
                        <option value="">Bitte wählen Sie eine Station</option>
                        <?php
                        $stnabt = $db->getStationAbteilung();
                        foreach ($stnabt as $stn):
                            if ($db->getQualiMaStation($ma->id, $stn->id) === true):
                                ?>
                                <option value="<?= $stn->id ?>"><?= $stn->station ?>
                                    &bull; <?= $stn->bezeichnung ?></option>
                            <?php
                            endif;
                        endforeach;
                        ?>
                    </select>
                    <?php
                    Functions::htmlCloseDiv();
                    ?>
                    <div class="row p-0 m-0 my-2 pb-2 border__bottom--dotted-gray_50">
                        <?php
                        Functions::htmlOpenDiv(6, "right", "dotted", "", "", "pe-3", "px-3");
                        Formular::labelInvisibleInput("Start", "date", "start", "{$_SESSION['parameter']['heuteSQL']}", "", "required");
                        Functions::htmlCloseDiv();
                        Functions::htmlOpenDiv(6, "", "", "", "", "ps-3", "px-3");
                        Formular::labelInvisibleInput("Ende", "date", "ende", "", "", "");
                        Functions::htmlCloseDiv();
                        ?>
                    </div>
                    <div class="text-end">
                        <?php
                        Formular::submit("submit", "Handicap eintragen", "btn btn-primary btn-sm");
                        ?>
                    </div>
                </form>
                <div class="bg-light-lines my-3 font-size-7">
                    &nbsp;
                </div>
                <?php
                $hcap = $db->getHandicapDetails($ma->id);
                if ($hcap):
                    foreach ($hcap as $handicap):
                        $station = $db->getStation($handicap->sid);
                        ?>
                        <div class="border__bottom--dotted-gray_50 py-2 font-size-12 divhover ps-2">
                            <span class="badge badge-black me-2"><?= $station->id ?></span><?= $station->station ?>
                            &bull; <?= $station->bezeichnung ?><br>
                            <small class="text-warning italic font-size-10">Beginn am <?= $handicap->beginn ?>
                                &bull; Ende am <?= $handicap->stop ?></small>
                        </div>
                    <?php
                    endforeach;
                endif;
                Functions::htmlCloseDiv();
            endif;
            Functions::htmlOpenDiv(3, "right", "dotted", "", "", "", "px-3");
            if ($ma->status != 0):
                ?>
                <h3 class="oswald font-weight-100 py-3 border__bottom--dotted-gray_50">
                    Abwesenheit eintragen
                </h3>
                <?php
                Functions::alert("Bitte tragen Sie das Start <b>und</b> das Enddatum der Abwesenheit ein. Der Grund für die Abwesenheit wird hier nicht dokumentiert. Eingetragene aber bereits abgelaufene Abwesenheiten werden an dieser Stellen nicht mehr angezeigt. Eine Abwesenheit führt dazu, dass alle Qualifikationen des Mitarbeiters, für die Dauer der Abwesenheit, deaktiviert werden. Der Mitarbeiter ist auch bei der Erstellung des Rotationsplanes in diesem Zeitraum nicht auswählbar.");
                ?>
                <form class="needs-validation" method="post" id="abwesenheit">
                    <div class="row p-0 m-0 my-2 pb-2 border__bottom--dotted-gray_50">
                        <?php
                        Formular::input("hidden", "ida", "$ma->id", "", "");
                        Formular::input("hidden", "uid", "$ma->id", "", "");
                        formular::input("hidden", "id", "$id", "", "");
                        Functions::htmlOpenDiv(6, "right", "dotted", "", "", "pe-3", "px-3");
                        Formular::labelInvisibleInput("Beginn", "date", "start", "{$_SESSION['parameter']['heuteSQL']}", "", "required");
                        Functions::htmlCloseDiv();
                        Functions::htmlOpenDiv(6, "", "", "", "", "ps-3", "px-3");
                        Formular::labelInvisibleInput("Ende", "date", "ende", "", "", "required");
                        Functions::htmlCloseDiv();
                        ?>
                    </div>
                    <div class="text-end">
                        <?php
                        Formular::submit("submit", "Abwesenheit eintragen", "btn btn-primary btn-sm");
                        ?>
                    </div>
                </form>
                <div class="bg-light-lines my-3 font-size-7">
                    &nbsp;
                </div>
                <?php
                $abw = RotationsplanDatabase::getAbwesendDetails($ma->id);
                if ($abw !== false):
                    foreach ($abw as $abwesend):
                        # Daten splitten
                        $start = explode(" ", $abwesend->beginn);
                        $ende = explode(" ", $abwesend->stop);
                        ?>
                        <div class="border__bottom--dotted-gray_50 row pb-3 mb-3 oswald font-weight-300">
                            <?php
                            Functions::htmlOpenDiv(6, "right", "dotted", "text-center", "", "", "px-3");
                            echo "<div class='font-size-16 p-0 m-0 wota'>" . Functions::germanTag($start[0]) . "</div>";
                            echo "<div class='font-size-50 p-0 m-0 text-primary tag'>$start[1]</div>";
                            echo "<div class='font-size-16 p-0 m-0 monat'>" . Functions::germanMonat($start[2]) . "</div>";
                            echo "<div class='font-size-16 p-0 m-0 jahr'>$start[3]</div>";
                            Functions::htmlCloseDiv();
                            Functions::htmlOpenDiv(6, "", "", "text-center", "", "", "px-3");
                            echo "<div class='font-size-16 p-0 m-0 wota'>" . Functions::germanTag($ende[0]) . "</div>";
                            echo "<div class='font-size-50 p-0 m-0 text-primary tag'>$ende[1]</div>";
                            echo "<div class='font-size-16 p-0 m-0 monat'>" . Functions::germanMonat($ende[2]) . "</div>";
                            echo "<div class='font-size-16 p-0 m-0 jahr'>$ende[3]</div>";
                            Functions::htmlCloseDiv();
                            ?>
                        </div>
                        <div>
                            <p class="font-size-10 text-center">Klicken Sie bitte <a href="#" class="text-link"
                                                                                     onclick="deleteAbwesenheit(<?= $abwesend->id ?>,'<?= $ma->id ?>')">hier</a>
                                wenn Sie die Abwesenheit löschen möchten!</p>
                        </div>
                    <?php
                    endforeach;
                endif;
            endif;
            if (isset($_SESSION['einsatz']) && count($_SESSION['einsatz']) > 0):
                ?>
                <h3 class="oswald text-primary font-weight-100 py-3 border__bottom--dotted-gray_50">
                    Aufteilung Einsätze
                </h3>
                <canvas id="chart" style="height: 300px; width: 100%;"></canvas>
            <?php
            endif;
            Functions::htmlCloseDiv();
            ?>
            </div>
        </div><!-- p-3 -->
    </div>
</main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<!-- Zusätzliche Javascript Dateien -->
<script type="text/javascript" src="<?= Functions::getBaseURL() ?>skin/plugins/chart.min.js"></script>
<script type="text/javascript" src="<?= Functions::getBaseUrl() ?>lib/Pages/Rotationsplan/MVC/View/js/action.js"></script>
<script type="text/javascript" src="<?= Functions::getBaseUrl() ?>lib/Pages/Rotationsplan/MVC/View/js/view.js"></script>
<script type="text/javascript" src="<?= Functions::getBaseUrl() ?>lib/Pages/Rotationsplan/MVC/View/js/submit.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        const a = $('#rfid');
        a.val('');
        a.attr("placeholder","RFID?");
    })
    <?php
    if(count($_SESSION['einsatz']) > 0):
    # Station
    $stn = array();
    $anzahl = array();
    # Labels für die Anzeige
    foreach ($_SESSION['einsatz'] as $a):
        $s = $db->getStation($a[0]);
        $stn[] = $s->station;
        $anzahl[] = $a[1];
    endforeach;
    ?>
    const ctx = document.getElementById('chart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: [
                <?php
                $l = '';
                foreach ($stn as $station):
                    $l .= '"' . $station . '",';
                endforeach;
                $label = rtrim($l, ',');
                echo $label;
                ?>
            ],
            datasets: [{
                data: [
                    <?php
                    $d = '';
                    foreach ($anzahl as $anz):
                        $d .= $anz . ",";
                    endforeach;
                    $data = rtrim($d, ',');
                    echo $data;
                    ?>
                ],
                borderColor: [
                    "rgba(0,70,155,1)",
                    "rgba(0,70,155,0.75)",
                    "rgba(0,70,155,0.5)",
                    "rgba(0,70,155,0.25)",
                    "rgba(250,187,0,1)",
                    "rgba(250,187,0,0.75)",
                    "rgba(250,187,0,0.5)",
                    "rgba(250,187,0,0.25)",
                    "rgba(40,167,69,1)",
                    "rgba(40,167,69,0.75)",
                    "rgba(40,167,69,0.5)",
                    "rgba(40,167,69,0.25)",
                    "rgba(0,124,181,1)",
                    "rgba(0,124,181,0.75)",
                    "rgba(0,124,181,0.5)",
                    "rgba(0,124,181,0.25)",
                    "rgba(110,110,110,1)",
                    "rgba(110,110,110,0.75)",
                    "rgba(110,110,110,0.5)",
                    "rgba(110,110,110,0.25)",
                ],
                backgroundColor: [
                    "rgba(0,70,155,1)",
                    "rgba(0,70,155,0.75)",
                    "rgba(0,70,155,0.5)",
                    "rgba(0,70,155,0.25)",
                    "rgba(250,187,0,1)",
                    "rgba(250,187,0,0.75)",
                    "rgba(250,187,0,0.5)",
                    "rgba(250,187,0,0.25)",
                    "rgba(40,167,69,1)",
                    "rgba(40,167,69,0.75)",
                    "rgba(40,167,69,0.5)",
                    "rgba(40,167,69,0.25)",
                    "rgba(0,124,181,1)",
                    "rgba(0,124,181,0.75)",
                    "rgba(0,124,181,0.5)",
                    "rgba(0,124,181,0.25)",
                    "rgba(110,110,110,1)",
                    "rgba(110,110,110,0.75)",
                    "rgba(110,110,110,0.5)",
                    "rgba(110,110,110,0.25)",
                ],
                borderWidth: 2,
            }]
        },
        options: {},

    });
    <?php
    endif;
    ?>
</script>
</body>
</html>