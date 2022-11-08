<?php
/** (c) Joachim Göddel . RLMS */

# Klassen


use App\Formular\Formular;
use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;
use App\Pages\Home\IndexDatabase;

$_SESSION['seite']['id'] = 36;
$_SESSION['seite']['name'] = 'details';
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

# Sind noch Änderungen möglich ?
$edit = ($seiteschreiben == 1 && $row->status < 6) ? 1 : 0;
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
<body class="d-flex flex-column h-100" id="body">
<div class="fixed-top z3">
    <?php
    # Basis Header einbinden
    Functions::getHeaderBasePage(0);
    Functions::dspNavigation($dspedit, 'details', $id, 0);
    ?>
</div>
<main class="w-100 bg__white--95 flex-shrink-0" id="main">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <div class="container-fluid">
        <div class="px-4 py-5"><!-- Inhalt -->
            <div class="row">
                <div class="col-4 border__right--dotted-gray m-0">
                    <div class="me-3">
                        <h3><?= $row->nr ?><span
                                    class="float-end"><?php ChangeManagementDatabase::dspArt($id); ?></span></h3>
                        <h3 class="border__bottom--dotted-gray border__top--dotted-gray py-3 my-3"><?= $info->part_description ?></h3>
                        <div class="mb-3"><?= $info->change_description ?></div>

                        <?php
                        include_once "includes/getBearbeitungsdauer.php";
                        include_once "includes/getStatusbox.php";
                        ?>
                    </div><!-- me-3 -->
                </div><!-- col-4 -->
                <div class="col-4 border__right--dotted-gray">
                    <div class="mx-3">
                        <h3 class="border__bottom--dotted-gray mb-2 pb-2">Aktionen</h3>
                        <?php
                        if(count($aktionen) > 0):
                            foreach($aktionen AS $ak):
                                $ersteller = IndexDatabase::getUserInfo($row->mid);
                            ?>
                                <div class="kommentar row border__bottom--dotted-gray m-0 p-0 mb-2 pb-2">
                                    <div class="col-2 text-center pt-2">
                                        <img src="<?= Functions::getBaseUrl() ?>/lib/Pages/Administration/MVC/View/files/images/<?= $ersteller->bild ?>"
                                             class="rund_small img-thumbnail img-fluid">
                                    </div>
                                    <div class="col-10 font-size-12 pt-2 pe-2">
                                        <b><?= $ak->name ?></b>
                                        <br><small class="text-muted"><?= $ak->eintrag ?></small><br>
                                        <?= $ak->aktion ?>
                                    </div>
                                </div>
                            <?php
                            endforeach;
                        else:
                        Functions::alert("Es wurden noch keine Aktionen in der Datenbank gespeichert!");
                        endif;
                        ?>
                    </div><!-- me-3 -->
                </div><!-- col-4 -->
                <div class="col-4">
                    <div class="ms-3">
                        <?php
                        # Anfrage ist beendet (Status = 7)
                        $old = 0;
                        # Frage nach alten Teilen
                        if($oldPart === false && $row->status === 7): ?>
                            <div class="border__bottom--dotted-gray mb-3 pb-3">
                                <h3 class="border__bottom--dotted-gray mb-3 pb-3"><?= $_SESSION['text']['h_alteTeile'] ?></h3>
                                <p>Wählen Sie bitte aus, ob das Verschrotten der alten Teile dokumentiert werden muss oder nicht. Wählen Sie im positiven Falle auch bitte das Datum, bis zu dem der Nachweis erbracht werden soll.</p>
                            </div><!-- mb-3 -->
                            <div class="">
                                <form id="alteTeile" class="" method="post">
                                    <input type="hidden" name="id" value="<?= $id ?>">
                                    <div class="row border__bottom--dotted-gray mb-3 pb-3">
                                        <div class="col-7 border__right--dotted-gray">
                                            <div class="me-3">
                                                <?php
                                                Formular::selectJaNein("aktion","required");
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="ms-3">
                                                <?php
                                                $zieldatum = date("Y-m-d", strtotime($_SESSION['parameter']['heuteSQL'] . '+ 14 days'));
                                                Functions::invisibleInput("date", "ziel", "", "$zieldatum", "", "", "", "", "");
                                                ?>
                                            </div>
                                        </div>
                                    </div><!-- row -->
                                    <div class="text-end mt-2">
                                        <input type="submit" class="btn btn-primary oswald text-uppercase" value="<?= $_SESSION['text']['b_speichern'] ?>">
                                    </div>
                                </form>
                            </div><!-- mb-3 -->
                        <?php
                        else:
                            # Alte Teile
                            $old = ChangeManagementDatabase::isOldPart($id);
                            if($old === 0 && $row->status == 7): ?>
                                <div class="border__bottom--dotted-gray mb-3 pb-3">
                                    <h3 class="border__bottom--dotted-gray mb-3 pb-3"><?= $_SESSION['text']['h_alteTeile'] ?></h3>
                                    <p>Laden Sie bitte den Nachweis der Verschrottung alter Teile im Bereich Dateien auf den Server. Anschliessend bestätigen Sie bitte durch ihr Passwort, dass diese Aktion durchgeführt wurde. Erst danach können Sie die Anfrage ins Archiv verschieben.</p>
                                </div><!-- mb-3 -->
                                <div class="">
                                    <form id="endeAlteTeile" class="" method="post">
                                        <input type="hidden" name="id" value="<?= $id ?>">
                                        <div class="row border__bottom--dotted-gray mb-3 pb-3">
                                            <div class="col-8 border__right--dotted-gray">
                                                <div class="me-3">
                                                    Tragen Sie bitte Ihr Passwort ein
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="ms-3">
                                                    <input type="password" id="password" name="password" class="invisible-formfield"
                                                           placeholder="<?= $_SESSION['text']['h_passwort'] ?>?" required>
                                                </div>
                                            </div>
                                        </div><!-- row -->
                                        <div class="text-end mt-2">
                                            <input type="submit" class="btn btn-primary oswald text-uppercase"
                                                   value="<?= $_SESSION['text']['b_bestaetigen'] ?>">
                                        </div>
                                    </form>
                                </div><!-- mb-3 -->
                            <?php
                            endif;
                        endif;
                        # Offene Teilenummern ?
                        $open = ChangeManagementDatabase::countPartNoOpen($id);
                        # Aktionen die nur angezeigt werden, wenn alle Aufgaben erledigt wurden
                        if ($row->status === 7 && $old === 1): # Alle Arbeiten wurden erledigt
                            if($open === 0):
                            ?>
                            <div class="border__bottom--dotted-gray mb-3 pb-3">
                                <h3 class="border__bottom--dotted-gray mb-3 pb-3"><?= $_SESSION['text']['h_archiv'] ?></h3>
                                <p>Tragen Sie bitte nachfolgend Ihr Passwort ein und klicken Sie auf die Schaltfläche "ARCHIVIEREN". Die Änderung wird danach automatisch ins Archiv verschoben.</p>
                            </div><!-- mb-3 -->
                            <div class="">
                                <form id="abschliessen" class="" method="post">
                                    <input type="hidden" name="id" value="<?= $id ?>">
                                    <div class="row border__bottom--dotted-gray mb-3 pb-3">
                                        <div class="col-8 border__right--dotted-gray">
                                            <div class="me-3">
                                                Tragen Sie bitte Ihr Passwort ein
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="ms-3">
                                                <input type="password" id="password" name="password" class="invisible-formfield"
                                                       placeholder="<?= $_SESSION['text']['h_passwort'] ?>?" required>
                                            </div>
                                        </div>
                                    </div><!-- row -->
                                    <div class="text-end mt-2">
                                        <input type="submit" class="btn btn-primary oswald text-uppercase"
                                               value="<?= $_SESSION['text']['b_archivieren'] ?>">
                                    </div>
                                </form>
                            </div><!-- mb-3 -->
                            <?php
                            else:
                            ?>
                                <div class="border__bottom--dotted-gray mb-3 pb-3">
                                    <h3 class="border__bottom--dotted-gray mb-3 pb-3"><?= $_SESSION['text']['h_archiv'] ?></h3>
                                    <p class="p-0 m-0">Diese Anfrage kann noch nicht ins Archiv verschoben werden, da noch nicht alle Teilenummern abgearbeitet wurden. </p>
                                </div><!-- mb-3 -->
                            <?php
                            endif;
                            ?>
                        <?php
                        endif;
                        if ($row->status === 3): # Evaluation ist beendet. Freigabe zur Umsetzung (Tracking)
                            $fehler = ChangeManagementDatabase::getNIO($id, 1)
                            ?>
                            <div class="border__bottom--dotted-gray mb-3 pb-3">
                                <h3 class="border__bottom--dotted-gray mb-3 pb-3">Freigeben</h3>
                                <p>Es wurden alle Punkte der Herstellbarkeitsbewertung durchgeführt. Hierbei wurden <b><?= $fehler ?></b> Einträge als <b>n.i.O.</b> gekennzeichnet. Treffen Sie bitte Ihre Auswahl, tragen Sie Ihr Passwort ein und klicken Sie anschließend auf den Button "AKTION SPEICHERN".</p>
                            </div><!-- mb-3 -->
                            <div class="">
                                <form id="freigabe" class="" method="post">
                                    <input type="hidden" name="id" value="<?= $id ?>">
                                    <div class="row border__bottom--dotted-gray mb-3 pb-3">
                                        <div class="col-8 border__right--dotted-gray">
                                            <div class="me-3">
                                                <select name="antwort" class="invisible-formfield" required>
                                                    <option value=""><?= $_SESSION['text']['i_selectOption'] ?></option>
                                                    <option value="4"><?= $_SESSION['text']['s_ablehnen'] ?></option>
                                                    <option value="5"><?= $_SESSION['text']['s_freigeben'] ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="ms-3">
                                                <input type="password" id="password" name="password" class="invisible-formfield"
                                                       placeholder="<?= $_SESSION['text']['h_passwort'] ?>?" required>
                                            </div>
                                        </div>
                                    </div><!-- row -->
                                    <div class="text-end mt-2">
                                        <input type="submit" class="btn btn-primary oswald text-uppercase"
                                               value="<?= $_SESSION['text']['b_aktionSpeichern'] ?>">
                                    </div>
                                </form>
                            </div><!-- mb-3 -->
                        <?php
                        endif;
                        if ($row->status < 6): # Dokument wurde noch nicht komplett bearbeitet
                            ?>
                            <div class="border__bottom--dotted-gray mb-3">
                                <h3 class="border__bottom--dotted-gray mb-3 pb-3 text-danger"><?= $_SESSION['text']['h_kaLoeschen'] ?></h3>
                                <?php FUnctions::alert($_SESSION['text']['t_kaLoeschen']); ?>
                            </div><!-- mb-3 -->
                            <div class="">
                                <form id="delete" class="" method="post">
                                    <input type="hidden" name="id" value="<?= $id ?>">
                                    <div class="row border__bottom--dotted-gray mb-3 pb-3">
                                        <div class="col-8 border__right--dotted-gray">
                                            <div class="me-3">
                                                Tragen Sie bitte Ihr Passwort ein
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="ms-3">
                                                <input type="password" id="password" name="password" class="invisible-formfield"
                                                       placeholder="<?= $_SESSION['text']['h_passwort'] ?>?" required>
                                            </div>
                                        </div>
                                    </div><!-- row -->
                                    <div class="text-end mt-2">
                                        <input type="submit" class="btn btn-danger oswald text-uppercase"
                                               value="<?= $_SESSION['text']['h_kaLoeschen'] ?>">
                                    </div>
                                </form>
                            </div><!-- mb-3 -->
                        <?php
                        endif;
                        # Beenden
                        if ($row->status === 6): # Tracking ist beendet.
                            $fehler = ChangeManagementDatabase::getNIO($id, 2)
                            ?>
                            <div class="border__bottom--dotted-gray mb-3 pb-3">
                                <h3 class="border__bottom--dotted-gray mb-3 pb-3">Beenden</h3>
                                <p>Es wurden alle Punkte des Trackings durchgeführt. Hierbei wurden <b><?= $fehler ?></b> Einträge als <b>n.i.O.</b> gekennzeichnet. Treffen Sie bitte Ihre Auswahl, tragen Sie Ihr Passwort ein und klicken Sie anschließend auf den Button "AKTION SPEICHERN".</p>
                            </div><!-- mb-3 -->
                            <div class="">
                                <form id="abschliessen" class="" method="post">
                                    <input type="hidden" name="id" value="<?= $id ?>">
                                    <div class="row border__bottom--dotted-gray mb-3 pb-3">
                                        <div class="col-8 border__right--dotted-gray">
                                            <div class="me-3">
                                                <select name="antwort" class="invisible-formfield" required>
                                                    <option value=""><?= $_SESSION['text']['i_selectOption'] ?></option>
                                                    <option value="4"><?= $_SESSION['text']['s_ablehnen'] ?></option>
                                                    <option value="5"><?= $_SESSION['text']['s_freigeben'] ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="ms-3">
                                                <input type="password" id="password" name="password" class="invisible-formfield"
                                                       placeholder="<?= $_SESSION['text']['h_passwort'] ?>?" required>
                                            </div>
                                        </div>
                                    </div><!-- row -->
                                    <div class="text-end mt-2">
                                        <input type="submit" class="btn btn-primary oswald text-uppercase"
                                               value="<?= $_SESSION['text']['b_aktionSpeichern'] ?>">
                                    </div>
                                </form>
                            </div><!-- mb-3 -->
                        <?php
                        endif;
                        # Aktionen die immer angezeigt werden
                        ?>
                    </div><!-- me-3 -->
                </div><!-- col-4 -->
            </div><!-- row -->
        </div><!-- px-4 -->
    </div><!-- container-fluid -->
</main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<script type="text/javascript"
        src="<?= Functions::getBaseURL() ?>/lib/Pages/ChangeManagement/MVC/View/js/view.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseURL() ?>/lib/Pages/ChangeManagement/MVC/View/js/action.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseUrl() ?>skin/plugins/node_modules/summernote/dist/summernote.min.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseUrl() ?>skin/plugins/node_modules/summernote/dist/lang/summernote-de-DE.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        heightMainContainer();
        heightHeader();
        // Anzeigen
        dspStatus(<?= $id ?>, 'over', '<?= $row->location ?>');
        dspStatus(<?= $id ?>, 'study', '<?= $row->location ?>');
        dspStatus(<?= $id ?>, 'introduce', '<?= $row->location ?>');
    });
</script>
</body>
</html>