<?php
# Seitenparameter
use App\App\Container;
use App\Formular\Formular;
use App\Functions\Functions;
use App\Pages\Rotationsplan\RotationsplanDatabase;

$_SESSION['seite']['id'] = $ebene;
$subid = 29;
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

# Logfile schreiben
# Functions::logfile('Rotationsplan Stationen', '', '', 'Seite aufgerufen');
$wrkabteilung = RotationsplanDatabase::showWrkParameter();
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
<main class="bg__white w-100">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <div class="container-fluid">
        <div class="p-3">
            <div class="row pb-5">
                <?php
                Functions::htmlOpenDiv(3, "right", "dotted", "", "", "", "pe-3");
                ?>
                <h3 class="oswald text-black font-weight-100 py-3 border__bottom--dotted-gray_50">
                    <?= $station->station ?> &bull; <?= $station->bezeichnung ?><br>
                    <small class="text-warning font-size-14"><?php echo RotationsplanDatabase::getAbteilungRotationsplan($station->abteilung) ?></small>
                </h3>
                <?php
                Functions::alert("Beachten Sie bitte, dass sich die hier getätigten Änderungen auch für die andere(n) Schicht(en) gilt");
                ?>
                <form class="needs-validation font-size-12" method="post" id="changeStation">
                    <div class="row border__bottom--dotted-gray_50 p-0 m-0 mb-3 pb-3">
                        <?php
                        Formular::input("hidden", "id", "$station->id", "", "");
                        Functions::htmlOpenDiv(4, "right", "dotted", "", "", "", "pe-3");
                        Formular::labelInvisibleInput("Station", "text", "station", "$station->station", "", "required", "", "", "font-size-16");
                        Functions::htmlCloseDiv();
                        Functions::htmlOpenDiv(8, "", "", "", "", "", "ps-3");
                        Formular::labelInvisibleInput("Bezeichnung", "text", "bezeichnung", "$station->bezeichnung", "", "required", "", "", "font-size-16");
                        Functions::htmlCloseDiv();
                        ?>
                    </div><!-- row -->
                    <div class="row border__bottom--dotted-gray_50 p-0 m-0 mb-3 pb-3">
                        <?php
                        Functions::htmlOpenDiv(2, "right", "dotted", "", "", "", "pe-3");
                        Formular::labelInvisibleInput("Mitarbeiter", "number", "mitarbeiter", "$station->mitarbeiter", "", "", "", "", "font-size-16");
                        Functions::htmlCloseDiv();
                        Functions::htmlOpenDiv(10, "", "", "", "", "", "ps-3");
                        ?>
                        <label class='form-label font-size-10 text-muted italic p-0 m-0'>Ergonomie</label>
                        <select name="ergo" class="no-border font-size-16 w-100">
                            <option value="white" <?php if ($station->ergo == 'white') echo 'selected'; ?>>
                                keine oder nicht relevante Arbeitsinhalte
                            </option>
                            <option value="success" <?php if ($station->ergo == 'success') echo 'selected'; ?>>
                                0 bis 24
                            </option>
                            <option value="warning" <?php if ($station->ergo == 'warning') echo 'selected'; ?>>
                                25
                                bis 49
                            </option>
                            <option value="danger" <?php if ($station->ergo == 'danger') echo 'selected'; ?>>
                                50 oder mehr
                            </option>
                        </select>
                        <?php
                        Functions::htmlCloseDiv();
                        ?>
                    </div>
                    <div class="row border__bottom--dotted-gray_50 p-0 m-0 pb-3">
                        <?php
                        Functions::htmlOpenDiv(2, "right", "dotted", "", "", "", "pe-3");
                        Formular::labelInvisibleInput("Status", "number", "status", "1", "", "", "", "", "font-size-16");
                        Functions::htmlCloseDiv();
                        Functions::htmlOpenDiv(10, "", "", "", "", "", "ps-3");
                        ?>
                        <label class='form-label font-size-10 text-muted italic p-0 m-0'>QPS Dokument</label>
                        <select name="qps" class="no-border font-size-16 w-100">
                            <option value="0">Bitte wählen Sie ein Dokument</option>
                            <?php
                            foreach(RotationsplanDatabase::getDokumente($_SESSION['user']['wrk_abteilung']) AS $dok):
                                $ckd = ($station->qps == $dok->id) ? 'selected' : '';
                                ?>
                                <option value="<?= $dok->id ?>" <?= $ckd ?>><?= $dok->titel ?> <?= $dok->nr ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php
                        Functions::htmlCloseDiv();
                        ?>
                    </div><!-- row -->
                    <p class="font-size-10 italic text-muted py-1 mb-3">Wenn Sie eine Station deaktivieren möchten,
                        setzen Sie den Status bitte auf 0 (Null)</p>
                    <div class="text-end">
                        <?php
                        Formular::submit("submit", "Änderungen speichern", "btn btn-primary oswald font-weight-300");
                        ?>
                    </div>
                </form>
                <?php
                Functions::htmlCloseDiv();
                Functions::htmlOpenDiv(3, "right", "dotted", "", "", "", "px-3");
                ?>
                <h3 class="oswald font-weight-100 py-3 border__bottom--dotted-gray_50">
                    Mitarbeiter <b>mit</b> Qualifikation
                </h3>
                <?php
                Functions::alert("<p>Um die Qaulifikation an dieser Station für einen User aufzuheben, gehen Sie bitte wie folgt vor:</p>
                        <ul>
                            <li>Fahren Sie mit der Maus über den Mitarbeiter</li>
                            <li>Klicken und halten Sie die linke Maustaste</li>
                            <li>Ziehen Sie die Maus nun in die rechte Liste (ein kleines Auswahlfeld erscheint)</li>
                            <li>Lassen Sie die Maustaste los</li>
                        </ul>");
                echo '<ul class="connectedSortable" id="ul1">';
                foreach ($ma as $d):
                    # Name
                    $in = substr($d->name, 0, 1);
                    if ($db->getQualiMaStation($d->id, $station->id) === true):
                        ?>
                        <li class="<?= $in ?> lisortable" id="<?= $d->id ?>" data-aktion="minus">
                            <h5 class="font-size-14">
                                <?= $d->name ?> <span class="font-size-12 font-weight-300"><?= $d->vorname ?></span>
                            </h5>
                        </li>
                    <?php
                    endif;
                endforeach;
                echo '</ul>';
                Functions::htmlCloseDiv();
                Functions::htmlOpenDiv(3, "right", "dotted", "", "", "", "px-3");
                ?>
                <h3 class="oswald font-weight-100 py-3 border__bottom--dotted-gray_50">
                    Mitarbeiter <b>ohne</b> Qualifikation
                </h3>
                <?php
                Functions::alert("<p>Um die Qaulifikation an dieser Station für einen User einzutragen, gehen Sie bitte wie folgt vor:</p>
                        <ul>
                            <li>Fahren Sie mit der Maus über den Mitarbeiter</li>
                            <li>Klicken und halten Sie die linke Maustaste</li>
                            <li>Ziehen Sie die Maus nun in die linke Liste (ein kleines Auswahlfeld erscheint)</li>
                            <li>Lassen Sie die Maustaste los</li>
                        </ul>");
                echo '<ul class="connectedSortable" id="ul2">';
                foreach ($ma as $d):
                    # Name
                    $in = substr($d->name, 0, 1);
                    if ($db->getQualiMaStation($d->id, $station->id) === false):
                        $bg = ($db->getTrainingStation($d->id, $station->id) > 0) ? 'bg-light-lines' : '';
                        $hc = $db->getHandicapStation($d->id,$station->id);
                        $dsphc = ($hc > 0) ? '<span class="badge badge-warning ms-2 oswald font-weight-300 text-black">HANDICAP</span>' : '';
                        ?>
                        <li class="<?= $in ?> lisortable <?= $bg ?>" id="<?= $d->id ?>" data-aktion="plus">
                            <h5 class="font-size-14">
                                <?= $d->name ?> <span class="font-size-12 font-weight-300"><?= $d->vorname ?><?= $dsphc ?></span>
                            </h5>
                        </li>
                    <?php
                    endif;
                endforeach;
                echo '</ul>';
                Functions::htmlCloseDiv();
                Functions::htmlOpenDiv(3, "", "", "", "", "", "ps-3");
                ?>
                <h3 class="oswald font-weight-100 py-3 border__bottom--dotted-gray_50">
                    Anzahl Einsätze
                </h3>
                <?php
                foreach ($ma as $d):
                    # Name
                    $in = substr($d->name, 0, 1);
                    # Anzahl Einsätze
                    $anz = $db->getAnzahlEinsatz($d->id, $station->id);
                    if ($db->getQualiMaStation($d->id, $station->id) === true):
                        echo '<div class="border__bottom--dotted-gray_50 pb-2 mb-2">';
                        echo '<h5 class="font-size-14">';
                        echo $d->name . " <span class='font-weight-300 font-size-12'>$d->vorname</span>";
                        echo '<span class="float-end"><span class="badge badge-primary">';
                        echo $anz;
                        echo '</span>';
                        echo '</h5>';
                        echo '</div>';
                    endif;
                endforeach;
                Functions::htmlCloseDiv();
                ?>
            </div><!-- row -->
        </div><!-- p-3 -->
    </div><!-- fluid -->
</main>

<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<!-- Zusätzliche Javascript Dateien -->
<script type="text/javascript" src="<?= Functions::getBaseUrl() ?>skin/js/base.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseUrl() ?>lib/Pages/Rotationsplan/MVC/View/js/action.js"></script>
<script type="text/javascript" src="<?= Functions::getBaseUrl() ?>lib/Pages/Rotationsplan/MVC/View/js/view.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseUrl() ?>lib/Pages/Rotationsplan/MVC/View/js/submit.js"></script>
<script type="text/javascript">
    $(document).ready(function () {

    })
    const l1 = $('ul#ul1 li').length;
    const l2 = $('ul#ul2 li').length;
    if (l1 >= 2) {
        $('.empty1').addClass('dspnone');
    }
    if (l2 >= 2) {
        $('.empty2').addClass('dspnone');
    }
    $(function () {
        $("ul").sortable({
            connectWith: "ul",
            placeholder: "ui-state-highlight",
            dropOnEmpty: true,
            update: function (event, ui) {
                if (!ui.sender) {
                    const itemOrder = $(this).sortable('toArray').toString();
                    const id = ui.item.attr("id");
                    const aktion = ui.item.attr("data-aktion");
                    const sid = <?= $station->id ?>;
                    console.log(itemOrder);
                    console.log(id);
                    console.log(sid);
                    $.post("/rotationsplan/ajaxSetQualiStation", {
                        id: "" + id + "",
                        sid: "" + sid + "",
                        aktion: "" + aktion + ""
                    }, function (text) {
                        const l1 = $('ul#ul1 li').length;
                        const l2 = $('ul#ul2 li').length;
                        if (l1 > 1) {
                            $('.empty1').addClass('dspnone');
                        }
                        if (l2 > 1) {
                            $('.empty2').addClass('dspnone');
                        }
                    })
                }
            }
        }).disableSelection();
    });
</script>
</body>
</html>